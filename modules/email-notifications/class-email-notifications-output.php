<?php
/**
 * Email Notifications output.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\EmailNotifications;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Base\Base_Output;
use ATSDash\Helpers\Branding_Helper;

/**
 * Class to set up email notifications output.
 */
class Email_Notifications_Output extends Base_Output {

	/**
	 * The class instance.
	 *
	 * @var object
	 */
	public static $instance = null;

	/**
	 * Get instance of the class.
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Init the class setup.
	 */
	public static function init() {

		self::get_instance()->setup();

	}

	/**
	 * Setup email notifications output.
	 *
	 * One hook (or set of hooks) per email type in
	 * Email_Notifications_Module::get_email_types() — WordPress core
	 * doesn't expose a single uniform filter for "every outgoing email",
	 * so each core notification needs its own small adapter matching that
	 * email's own filter shape.
	 */
	public function setup() {

		// Combined {to,subject,message,headers} array filters.
		add_filter( 'wp_new_user_notification_email', array( $this, 'welcome_notification_email' ), 10, 3 );
		add_filter( 'wp_new_user_notification_email_admin', array( $this, 'admin_new_user_notification_email' ), 10, 3 );
		add_filter( 'retrieve_password_notification_email', array( $this, 'password_reset_notification_email' ), 10, 4 );
		add_filter( 'wp_password_change_notification_email', array( $this, 'password_changed_notification_email' ), 10, 3 );

		// Content-only filter, plain text, no header control.
		add_filter( 'new_user_email_content', array( $this, 'email_change_notification_content' ), 10, 2 );

		// Split subject/text/headers filters.
		add_filter( 'comment_notification_subject', array( $this, 'comment_notification_subject' ), 10, 2 );
		add_filter( 'comment_notification_text', array( $this, 'comment_notification_text' ), 10, 2 );
		add_filter( 'comment_notification_headers', array( $this, 'comment_notification_headers' ), 10, 2 );
		add_filter( 'comment_moderation_subject', array( $this, 'comment_moderation_subject' ), 10, 2 );
		add_filter( 'comment_moderation_text', array( $this, 'comment_moderation_text' ), 10, 2 );
		add_filter( 'comment_moderation_headers', array( $this, 'comment_moderation_headers' ), 10, 2 );

		// Renders a preview of an editable email in a new tab from the settings page.
		add_action( 'admin_post_ats_preview_notification_email', array( $this, 'render_preview' ) );

	}

	/**
	 * Get a configured branding color, falling back to the plugin's default
	 * for that color when white labeling hasn't set one.
	 *
	 * @param string $key The ats_branding option key (see Branding_Helper::default_colors()).
	 *
	 * @return string Color in hex format.
	 */
	public function get_branding_color( $key ) {

		$branding = get_option( 'ats_branding' );

		if ( ! empty( $branding[ $key ] ) ) {
			return $branding[ $key ];
		}

		return Branding_Helper::default_color( $key );

	}

	/**
	 * Get the saved email notifications settings, merged with defaults.
	 *
	 * @return array
	 */
	public function get_settings() {

		$saved  = get_option( 'ats_email_notifications', array() );
		$module = new Email_Notifications_Module();

		$defaults = array(
			'global' => array(
				'logo_url'      => '',
				'accent_color'  => $this->get_branding_color( 'accent_color' ),
				'header_color'  => $this->get_branding_color( 'admin_bar_bg_color' ),
				'support_email' => get_option( 'admin_email' ),
				'support_url'   => '',
				'footer_text'   => '',
			),
			'emails' => array(),
		);

		foreach ( $module->get_email_types() as $email_key => $email_type ) {
			$defaults['emails'][ $email_key ] = $email_type['defaults'];
		}

		$settings           = wp_parse_args( is_array( $saved ) ? $saved : array(), $defaults );
		$settings['global'] = wp_parse_args( isset( $settings['global'] ) && is_array( $settings['global'] ) ? $settings['global'] : array(), $defaults['global'] );

		foreach ( $defaults['emails'] as $email_key => $email_defaults ) {
			$saved_email                       = isset( $settings['emails'][ $email_key ] ) && is_array( $settings['emails'][ $email_key ] ) ? $settings['emails'][ $email_key ] : array();
			$settings['emails'][ $email_key ] = wp_parse_args( $saved_email, $email_defaults );
		}

		// Empty strings saved on top of a default (e.g. an emptied accent color) should still fall back.
		foreach ( array( 'accent_color', 'header_color' ) as $color_key ) {
			if ( empty( $settings['global'][ $color_key ] ) ) {
				$settings['global'][ $color_key ] = $defaults['global'][ $color_key ];
			}
		}

		return $settings;

	}

	/**
	 * Whether an editable email type is turned on.
	 *
	 * @param string $email_key The email type key.
	 *
	 * @return bool
	 */
	public function is_enabled( $email_key ) {

		$settings = $this->get_settings();

		return isset( $settings['emails'][ $email_key ]['enabled'] ) && '1' === $settings['emails'][ $email_key ]['enabled'];

	}

	/**
	 * Replace the merge tags used across email subjects/headings/bodies.
	 *
	 * @param string $content The raw content containing {tag} placeholders.
	 * @param array  $tags Associative array of tag => replacement value, without curly braces.
	 *
	 * @return string
	 */
	public function replace_tags( $content, $tags ) {

		$search  = array();
		$replace = array();

		foreach ( $tags as $tag => $value ) {
			$search[]  = '{' . $tag . '}';
			$replace[] = $value;
		}

		return str_replace( $search, $replace, $content );

	}

	/**
	 * Build the shared merge tag values for the current site.
	 *
	 * @param array $overrides Tag values specific to the email being sent (e.g. first_name, action_url).
	 *
	 * @return array
	 */
	public function get_tag_values( $overrides = array() ) {

		$settings = $this->get_settings();

		$defaults = array(
			'site_name'     => get_bloginfo( 'name' ),
			'site_url'      => home_url(),
			'support_email' => $settings['global']['support_email'],
			'support_url'   => $settings['global']['support_url'],
		);

		return array_merge( $defaults, $overrides );

	}

	/**
	 * Escape a literal "%" so a subject we return survives being run back
	 * through `sprintf( $subject, $blogname )` by WordPress core (several
	 * of the array-shaped notification filters build a `%s`-formatted
	 * subject string, then interpolate the site name into it *after* the
	 * filter runs). Without this, a custom subject containing a plain "%"
	 * — "50% off", a stray character, etc. — would throw a ValueError on
	 * PHP 8+ and break account creation entirely.
	 *
	 * @param string $subject The final subject text, tags already replaced.
	 *
	 * @return string
	 */
	public function escape_percent( $subject ) {

		return str_replace( '%', '%%', $subject );

	}

	/**
	 * Wrap an editable email's heading/body/button in the shared branded template.
	 *
	 * @param array $email The email type's settings (heading, body, button_text).
	 * @param array $tags Merge tag values to apply to the heading & body.
	 *
	 * @return string
	 */
	public function build_email_html( $email, $tags ) {

		$settings = $this->get_settings();

		$heading = ! empty( $email['heading'] ) ? $this->replace_tags( $email['heading'], $tags ) : '';
		$body    = $this->replace_tags( $email['body'], $tags );

		$logo_url = $settings['global']['logo_url'];

		if ( ! $logo_url ) {
			$logo_id  = get_theme_mod( 'custom_logo' );
			$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : admin_url( 'images/wordpress-logo.png' );
		}

		$footer_text = $settings['global']['footer_text'] ? $this->replace_tags( $settings['global']['footer_text'], $tags ) : sprintf(
			/* translators: %s: Site name */
			__( 'This email was sent by %s.', 'ats-dashboard' ),
			$tags['site_name']
		);

		$template = require __DIR__ . '/templates/email-wrapper.php';

		return $template(
			array(
				'logo_url'      => $logo_url,
				'accent_color'  => $settings['global']['accent_color'],
				'header_color'  => $settings['global']['header_color'],
				'heading'       => $heading,
				'body'          => $body,
				'button_text'   => ! empty( $email['button_text'] ) ? $this->replace_tags( $email['button_text'], $tags ) : '',
				'button_url'    => isset( $tags['action_url'] ) ? $tags['action_url'] : $tags['site_url'],
				'support_email' => $tags['support_email'],
				'support_url'   => $tags['support_url'],
				'footer_text'   => $footer_text,
			)
		);

	}

	/**
	 * Build the plain-text body used for the one email type WordPress core
	 * only ever sends as plain text (`email_change`), with no branded
	 * template wrapper.
	 *
	 * @param array $email The email type's settings (body).
	 * @param array $tags Merge tag values.
	 *
	 * @return string
	 */
	public function build_email_text( $email, $tags ) {

		return $this->replace_tags( $email['body'], $tags );

	}

	/**
	 * Filter the new user notification email (sent to the new user) to use
	 * the editable welcome email template & content, when enabled.
	 *
	 * @param array    $email User notification email arguments (to, subject, message, headers).
	 * @param \WP_User $user The new user object.
	 * @param string   $blogname The site title.
	 *
	 * @return array
	 */
	public function welcome_notification_email( $email, $user, $blogname ) {

		if ( ! $this->is_enabled( 'welcome' ) ) {
			return $email;
		}

		$welcome = $this->get_settings()['emails']['welcome'];

		// Extract the password reset URL WordPress already generated.
		preg_match( '/https?:\/\/\S+action=rp\S+/', $email['message'], $matches );
		$reset_url = isset( $matches[0] ) ? $matches[0] : network_site_url( 'wp-login.php' );

		$first_name = ! empty( $user->first_name ) ? $user->first_name : $user->display_name;

		$tags = $this->get_tag_values(
			array(
				'first_name'   => $first_name,
				'display_name' => $user->display_name,
				'action_url'   => $reset_url,
			)
		);

		$email['subject'] = $this->escape_percent( wp_strip_all_tags( $this->replace_tags( $welcome['subject'], $tags ) ) );
		$email['headers'] = array( 'Content-Type: text/html; charset=UTF-8' );
		$email['message'] = $this->build_email_html( $welcome, $tags );

		return $email;

	}

	/**
	 * Filter the new user notification email sent to the site admin.
	 *
	 * @param array    $email Notification email arguments (to, subject, message, headers).
	 * @param \WP_User $user The new user object.
	 * @param string   $blogname The site title.
	 *
	 * @return array
	 */
	public function admin_new_user_notification_email( $email, $user, $blogname ) {

		if ( ! $this->is_enabled( 'admin_new_user' ) ) {
			return $email;
		}

		$email_type = $this->get_settings()['emails']['admin_new_user'];

		$tags = $this->get_tag_values(
			array(
				'display_name' => $user->display_name,
				'user_login'   => $user->user_login,
				'user_email'   => $user->user_email,
				'action_url'   => admin_url( 'user-edit.php?user_id=' . $user->ID ),
			)
		);

		$email['subject'] = $this->escape_percent( wp_strip_all_tags( $this->replace_tags( $email_type['subject'], $tags ) ) );
		$email['headers'] = array( 'Content-Type: text/html; charset=UTF-8' );
		$email['message'] = $this->build_email_html( $email_type, $tags );

		return $email;

	}

	/**
	 * Filter the password reset request email sent to the user.
	 *
	 * @param array   $defaults {to,subject,message,headers} arguments.
	 * @param string  $key The activation key.
	 * @param string  $user_login The username for the user.
	 * @param \WP_User $user_data WP_User object.
	 *
	 * @return array
	 */
	public function password_reset_notification_email( $defaults, $key, $user_login, $user_data ) {

		if ( ! $this->is_enabled( 'password_reset' ) ) {
			return $defaults;
		}

		$email_type = $this->get_settings()['emails']['password_reset'];

		// Extract the reset URL WordPress already generated in the default message.
		preg_match( '/https?:\/\/\S+action=rp\S+/', $defaults['message'], $matches );
		$reset_url = isset( $matches[0] ) ? $matches[0] : network_site_url( 'wp-login.php' );

		$tags = $this->get_tag_values(
			array(
				'display_name' => $user_data->display_name,
				'action_url'   => $reset_url,
			)
		);

		// No later sprintf() re-processes this subject, so no percent escaping needed here.
		$defaults['subject'] = wp_strip_all_tags( $this->replace_tags( $email_type['subject'], $tags ) );
		$defaults['headers'] = array( 'Content-Type: text/html; charset=UTF-8' );
		$defaults['message'] = $this->build_email_html( $email_type, $tags );

		return $defaults;

	}

	/**
	 * Filter the password changed notification email sent to the site admin.
	 *
	 * @param array    $email Notification email arguments (to, subject, message, headers).
	 * @param \WP_User $user User object for the user whose password was changed.
	 * @param string   $blogname The site title.
	 *
	 * @return array
	 */
	public function password_changed_notification_email( $email, $user, $blogname ) {

		if ( ! $this->is_enabled( 'password_changed' ) ) {
			return $email;
		}

		$email_type = $this->get_settings()['emails']['password_changed'];

		$tags = $this->get_tag_values(
			array(
				'display_name' => $user->display_name,
				'user_login'   => $user->user_login,
				'action_url'   => admin_url( 'user-edit.php?user_id=' . $user->ID ),
			)
		);

		$email['subject'] = $this->escape_percent( wp_strip_all_tags( $this->replace_tags( $email_type['subject'], $tags ) ) );
		$email['headers'] = array( 'Content-Type: text/html; charset=UTF-8' );
		$email['message'] = $this->build_email_html( $email_type, $tags );

		return $email;

	}

	/**
	 * Filter the "confirm your new email address" content sent to a user's
	 * new email address from the Profile screen. WordPress core sends this
	 * as plain text with no header control, so the override stays plain
	 * text too — and the subject isn't filterable at all here.
	 *
	 * @param string $email_text The default plain-text email content (still has ###TOKEN### placeholders at this point).
	 * @param array  $new_user_email {hash, newemail}.
	 *
	 * @return string
	 */
	public function email_change_notification_content( $email_text, $new_user_email ) {

		if ( ! $this->is_enabled( 'email_change' ) ) {
			return $email_text;
		}

		$email_type   = $this->get_settings()['emails']['email_change'];
		$current_user = wp_get_current_user();

		$tags = $this->get_tag_values(
			array(
				'display_name' => $current_user->display_name,
				'new_email'    => $new_user_email['newemail'],
				'action_url'   => self_admin_url( 'profile.php?newuseremail=' . $new_user_email['hash'] ),
			)
		);

		// Returned before core's own ###TOKEN### replacements run, so those become no-ops.
		return $this->build_email_text( $email_type, $tags );

	}

	/**
	 * Get the merge tag values shared by the two comment-related email types.
	 *
	 * @param int $comment_id Comment ID.
	 *
	 * @return array|null Null when the comment/post can't be loaded.
	 */
	private function get_comment_tags( $comment_id ) {

		$comment = get_comment( $comment_id );

		if ( ! $comment ) {
			return null;
		}

		$post = get_post( $comment->comment_post_ID );

		return $this->get_tag_values(
			array(
				'comment_author' => esc_html( $comment->comment_author ),
				'post_title'     => $post ? esc_html( get_the_title( $post ) ) : '',
			)
		);

	}

	/**
	 * Filter the comment notification (to the post author) subject.
	 *
	 * @param string $subject Default subject.
	 * @param string $comment_id Comment ID.
	 *
	 * @return string
	 */
	public function comment_notification_subject( $subject, $comment_id ) {

		if ( ! $this->is_enabled( 'comment_notification' ) ) {
			return $subject;
		}

		$tags = $this->get_comment_tags( $comment_id );

		if ( ! $tags ) {
			return $subject;
		}

		$email_type = $this->get_settings()['emails']['comment_notification'];

		return wp_strip_all_tags( $this->replace_tags( $email_type['subject'], $tags ) );

	}

	/**
	 * Filter the comment notification (to the post author) body.
	 *
	 * @param string $message Default message.
	 * @param string $comment_id Comment ID.
	 *
	 * @return string
	 */
	public function comment_notification_text( $message, $comment_id ) {

		if ( ! $this->is_enabled( 'comment_notification' ) ) {
			return $message;
		}

		$tags = $this->get_comment_tags( $comment_id );

		if ( ! $tags ) {
			return $message;
		}

		$comment                = get_comment( $comment_id );
		$tags['action_url']     = get_comment_link( $comment );
		$email_type             = $this->get_settings()['emails']['comment_notification'];

		return $this->build_email_html( $email_type, $tags );

	}

	/**
	 * Force an HTML Content-Type on the comment notification email, only
	 * while the override is enabled (this filter exists specifically so we
	 * don't have to touch the global `wp_mail_content_type` filter, which
	 * would affect every other email on the site).
	 *
	 * @param string $headers Default headers.
	 * @param string $comment_id Comment ID.
	 *
	 * @return string|array
	 */
	public function comment_notification_headers( $headers, $comment_id ) {

		if ( ! $this->is_enabled( 'comment_notification' ) ) {
			return $headers;
		}

		return array( 'Content-Type: text/html; charset=UTF-8' );

	}

	/**
	 * Filter the comment moderation (to the site admin) subject.
	 *
	 * @param string $subject Default subject.
	 * @param int    $comment_id Comment ID.
	 *
	 * @return string
	 */
	public function comment_moderation_subject( $subject, $comment_id ) {

		if ( ! $this->is_enabled( 'comment_moderation' ) ) {
			return $subject;
		}

		$tags = $this->get_comment_tags( $comment_id );

		if ( ! $tags ) {
			return $subject;
		}

		$email_type = $this->get_settings()['emails']['comment_moderation'];

		return wp_strip_all_tags( $this->replace_tags( $email_type['subject'], $tags ) );

	}

	/**
	 * Filter the comment moderation (to the site admin) body.
	 *
	 * @param string $message Default message.
	 * @param int    $comment_id Comment ID.
	 *
	 * @return string
	 */
	public function comment_moderation_text( $message, $comment_id ) {

		if ( ! $this->is_enabled( 'comment_moderation' ) ) {
			return $message;
		}

		$tags = $this->get_comment_tags( $comment_id );

		if ( ! $tags ) {
			return $message;
		}

		$tags['action_url'] = admin_url( 'edit-comments.php?comment_status=moderated#wpbody-content' );
		$email_type          = $this->get_settings()['emails']['comment_moderation'];

		return $this->build_email_html( $email_type, $tags );

	}

	/**
	 * Force an HTML Content-Type on the comment moderation email, only
	 * while the override is enabled.
	 *
	 * @param string $headers Default headers.
	 * @param int    $comment_id Comment ID.
	 *
	 * @return string|array
	 */
	public function comment_moderation_headers( $headers, $comment_id ) {

		if ( ! $this->is_enabled( 'comment_moderation' ) ) {
			return $headers;
		}

		return array( 'Content-Type: text/html; charset=UTF-8' );

	}

	/**
	 * Render a preview of an editable email using sample data.
	 *
	 * Hooked to `admin_post_ats_preview_notification_email`, linked from the
	 * settings page so admins can see the rendered content in a new tab
	 * without sending a real email.
	 */
	public function render_preview() {

		$capability = apply_filters( 'ats_settings_capability', 'manage_options' );

		if ( ! current_user_can( $capability ) ) {
			wp_die( esc_html__( 'You do not have permission to preview this email.', 'ats-dashboard' ) );
		}

		check_admin_referer( 'ats_preview_notification_email' );

		$email_key = isset( $_GET['email'] ) ? sanitize_key( wp_unslash( $_GET['email'] ) ) : '';
		$module    = new Email_Notifications_Module();
		$types     = $module->get_email_types();

		if ( ! isset( $types[ $email_key ] ) ) {
			wp_die( esc_html__( 'Unknown email type.', 'ats-dashboard' ) );
		}

		$settings = $this->get_settings();
		$email    = $settings['emails'][ $email_key ];

		$tags = $this->get_tag_values(
			array(
				'first_name'     => __( 'Jamie', 'ats-dashboard' ),
				'display_name'   => __( 'Jamie Doe', 'ats-dashboard' ),
				'user_login'     => 'jamie',
				'user_email'     => 'jamie@example.com',
				'new_email'      => 'jamie.new@example.com',
				'comment_author' => __( 'A Site Visitor', 'ats-dashboard' ),
				'post_title'     => __( 'Sample Post Title', 'ats-dashboard' ),
				'action_url'     => '#',
			)
		);

		if ( 'text' === $types[ $email_key ]['body_format'] ) {
			header( 'Content-Type: text/plain; charset=utf-8' );
			echo $this->build_email_text( $email, $tags ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- text/plain response, not HTML; nothing to escape.
			exit;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rendering a full HTML email document for preview.
		echo $this->build_email_html( $email, $tags );

		exit;

	}

}
