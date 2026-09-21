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
	 */
	public function setup() {

		add_filter( 'wp_new_user_notification_email', array( $this, 'welcome_notification_email' ), 10, 3 );

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
			$saved_email                     = isset( $settings['emails'][ $email_key ] ) && is_array( $settings['emails'][ $email_key ] ) ? $settings['emails'][ $email_key ] : array();
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
	 * Filter the new user notification email to use the editable welcome
	 * email template & content, when enabled.
	 *
	 * @param array    $email User notification email arguments (subject, message, headers).
	 * @param \WP_User $user The new user object.
	 * @param string   $blogname The site title.
	 *
	 * @return array
	 */
	public function welcome_notification_email( $email, $user, $blogname ) {

		$settings = $this->get_settings();
		$welcome  = $settings['emails']['welcome'];

		if ( empty( $welcome['enabled'] ) || '1' !== $welcome['enabled'] ) {
			return $email;
		}

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

		$email['subject'] = wp_strip_all_tags( $this->replace_tags( $welcome['subject'], $tags ) );
		$email['headers'] = array( 'Content-Type: text/html; charset=UTF-8' );
		$email['message'] = $this->build_email_html( $welcome, $tags );

		return $email;

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

		$heading = $this->replace_tags( $email['heading'], $tags );
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
				'button_text'   => $this->replace_tags( $email['button_text'], $tags ),
				'button_url'    => isset( $tags['action_url'] ) ? $tags['action_url'] : $tags['site_url'],
				'support_email' => $tags['support_email'],
				'support_url'   => $tags['support_url'],
				'footer_text'   => $footer_text,
			)
		);

	}

	/**
	 * Render a preview of an editable email using sample data.
	 *
	 * Hooked to `admin_post_ats_preview_notification_email`, linked from the
	 * settings page so admins can see the rendered HTML in a new tab without
	 * sending a real email.
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
				'first_name'   => __( 'Jamie', 'ats-dashboard' ),
				'display_name' => __( 'Jamie Doe', 'ats-dashboard' ),
				'action_url'   => '#',
			)
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rendering a full HTML email document for preview.
		echo $this->build_email_html( $email, $tags );

		exit;

	}

}
