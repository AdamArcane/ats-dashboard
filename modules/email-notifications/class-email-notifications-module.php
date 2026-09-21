<?php
/**
 * Email Notifications module.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\EmailNotifications;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Base\Base_Module;

/**
 * Class to setup email notifications module.
 */
class Email_Notifications_Module extends Base_Module {

	/**
	 * The class instance.
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * The current module url.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Module constructor.
	 */
	public function __construct() {

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/email-notifications';

	}

	/**
	 * Get instance of the class.
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Setup email notifications module.
	 */
	public function setup() {

		/**
		 * These 4 actions will be removed on multisite if current site is not a blueprint.
		 */
		add_action( 'admin_menu', array( self::get_instance(), 'submenu_page' ) );
		add_action( 'admin_init', array( self::get_instance(), 'add_settings' ) );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_scripts' ) );

		// The module output.
		require_once __DIR__ . '/class-email-notifications-output.php';
		Email_Notifications_Output::init();

	}

	/**
	 * Add submenu page.
	 */
	public function submenu_page() {
		add_submenu_page( 'edit.php?post_type=ats_widgets', __( 'Email Notifications', 'ats-dashboard' ), __( 'Email Notifications', 'ats-dashboard' ), apply_filters( 'ats_settings_capability', 'manage_options' ), 'ats_email_notifications', array( $this, 'submenu_page_content' ) );
	}

	/**
	 * Submenu page content.
	 */
	public function submenu_page_content() {

		$template = require __DIR__ . '/templates/email-notifications-template.php';
		$template();

	}

	/**
	 * Enqueue admin styles.
	 */
	public function admin_styles() {

		$enqueue = require __DIR__ . '/inc/css-enqueue.php';
		$enqueue( $this );

	}

	/**
	 * Enqueue admin scripts.
	 */
	public function admin_scripts() {

		if ( $this->screen()->is_email_notifications() && function_exists( 'wp_enqueue_media' ) ) {

			/**
			 * Some plugins/themes register an older "underscore" build that's
			 * missing methods wp.media's Backbone views rely on. Re-register
			 * core's own copy first so the media modal doesn't fatal.
			 *
			 * @see Branding_Module::admin_scripts() for the same workaround.
			 */
			global $wp_scripts;

			if ( isset( $wp_scripts->registered['underscore'] ) ) {
				$underscore = $wp_scripts->registered['underscore'];
				wp_deregister_script( 'underscore' );
				wp_register_script( 'underscore', $underscore->src, $underscore->deps, '1.13.8-ats-fix', false );
			}

			wp_enqueue_script( 'underscore' );
			wp_enqueue_script( 'backbone' );
			wp_enqueue_script( 'wp-util' );
			wp_enqueue_script( 'wp-backbone' );
			wp_enqueue_media();

		}

		$enqueue = require __DIR__ . '/inc/js-enqueue.php';
		$enqueue( $this );

	}

	/**
	 * Get the notification emails this module ships with.
	 *
	 * Each entry describes one editable email so the settings page, the
	 * sanitizer, and the output class can all loop over the same list
	 * instead of hardcoding "welcome" in three places. Add a new array
	 * entry here (plus a matching `add_filter()` in
	 * Email_Notifications_Output::setup()) to make another email editable.
	 *
	 * @return array
	 */
	public function get_email_types() {

		$types = array(
			'welcome' => array(
				'label'       => __( 'Welcome Email', 'ats-dashboard' ),
				'description' => __( 'Sent when a new user is added to the site (wp_new_user_notification_email).', 'ats-dashboard' ),
				'tags'        => array( '{first_name}', '{display_name}', '{site_name}', '{site_url}', '{action_url}', '{support_email}', '{support_url}' ),
				'defaults'    => array(
					'enabled'     => '1',
					'subject'     => __( "You've been added to {site_name}", 'ats-dashboard' ),
					'heading'     => __( 'Hi {first_name},', 'ats-dashboard' ),
					'body'        => '<p>' . __( "You've been given access to <a href=\"{site_url}\">{site_name}</a>. You can use your account to log in and manage the site's content.", 'ats-dashboard' ) . '</p><p>' . __( 'Click the button below to set your password and get started. This link expires in 24 hours.', 'ats-dashboard' ) . '</p>',
					'button_text' => __( 'Set Your Password', 'ats-dashboard' ),
				),
			),
		);

		return apply_filters( 'ats_email_notification_types', $types );

	}

	/**
	 * Add settings.
	 */
	public function add_settings() {

		// Register setting.
		register_setting( 'ats-email-notifications-group', 'ats_email_notifications', array( 'sanitize_callback' => array( $this, 'sanitize_email_notifications_settings' ) ) );

		// Global template section.
		add_settings_section( 'ats-email-notifications-global-section', '', '', 'ats-email-notifications-global-settings' );

		add_settings_field( 'logo-url', __( 'Logo URL', 'ats-dashboard' ), array( $this, 'logo_url_field' ), 'ats-email-notifications-global-settings', 'ats-email-notifications-global-section' );
		add_settings_field( 'accent-color', __( 'Accent Color', 'ats-dashboard' ), array( $this, 'accent_color_field' ), 'ats-email-notifications-global-settings', 'ats-email-notifications-global-section' );
		add_settings_field( 'header-color', __( 'Header Background', 'ats-dashboard' ), array( $this, 'header_color_field' ), 'ats-email-notifications-global-settings', 'ats-email-notifications-global-section' );
		add_settings_field( 'support-email', __( 'Support Email', 'ats-dashboard' ), array( $this, 'support_email_field' ), 'ats-email-notifications-global-settings', 'ats-email-notifications-global-section' );
		add_settings_field( 'support-url', __( 'Support URL', 'ats-dashboard' ), array( $this, 'support_url_field' ), 'ats-email-notifications-global-settings', 'ats-email-notifications-global-section' );
		add_settings_field( 'footer-text', __( 'Footer Text', 'ats-dashboard' ), array( $this, 'footer_text_field' ), 'ats-email-notifications-global-settings', 'ats-email-notifications-global-section' );

		// One section + set of fields per editable email type.
		foreach ( $this->get_email_types() as $email_key => $email_type ) {

			$page = 'ats-email-notifications-' . $email_key . '-settings';

			add_settings_section( 'ats-email-notifications-' . $email_key . '-section', '', array( $this, 'email_type_tags_notice' ), $page );

			add_settings_field( $email_key . '-enabled', __( 'Enabled', 'ats-dashboard' ), array( $this, 'email_enabled_field' ), $page, 'ats-email-notifications-' . $email_key . '-section', array( 'email_key' => $email_key ) );
			add_settings_field( $email_key . '-subject', __( 'Subject', 'ats-dashboard' ), array( $this, 'email_subject_field' ), $page, 'ats-email-notifications-' . $email_key . '-section', array( 'email_key' => $email_key ) );
			add_settings_field( $email_key . '-heading', __( 'Heading', 'ats-dashboard' ), array( $this, 'email_heading_field' ), $page, 'ats-email-notifications-' . $email_key . '-section', array( 'email_key' => $email_key ) );
			add_settings_field( $email_key . '-body', __( 'Body', 'ats-dashboard' ), array( $this, 'email_body_field' ), $page, 'ats-email-notifications-' . $email_key . '-section', array( 'email_key' => $email_key ) );
			add_settings_field( $email_key . '-button-text', __( 'Button Text', 'ats-dashboard' ), array( $this, 'email_button_text_field' ), $page, 'ats-email-notifications-' . $email_key . '-section', array( 'email_key' => $email_key ) );

		}

	}

	/**
	 * Sanitize email notifications settings.
	 *
	 * @param mixed $input The input data to sanitize.
	 * @return array The sanitized settings array.
	 */
	public function sanitize_email_notifications_settings( $input ) {

		if ( ! is_array( $input ) ) {
			return array();
		}

		$sanitized = array(
			'global' => array(),
			'emails' => array(),
		);

		$global = isset( $input['global'] ) && is_array( $input['global'] ) ? $input['global'] : array();

		$sanitized['global']['logo_url']      = isset( $global['logo_url'] ) ? esc_url_raw( $global['logo_url'] ) : '';
		$sanitized['global']['accent_color']  = isset( $global['accent_color'] ) ? sanitize_hex_color( $global['accent_color'] ) : '';
		$sanitized['global']['header_color']  = isset( $global['header_color'] ) ? sanitize_hex_color( $global['header_color'] ) : '';
		$sanitized['global']['support_email'] = isset( $global['support_email'] ) ? sanitize_email( $global['support_email'] ) : '';
		$sanitized['global']['support_url']   = isset( $global['support_url'] ) ? esc_url_raw( $global['support_url'] ) : '';
		$sanitized['global']['footer_text']   = isset( $global['footer_text'] ) ? sanitize_text_field( $global['footer_text'] ) : '';

		$emails = isset( $input['emails'] ) && is_array( $input['emails'] ) ? $input['emails'] : array();

		foreach ( $this->get_email_types() as $email_key => $email_type ) {

			$email = isset( $emails[ $email_key ] ) && is_array( $emails[ $email_key ] ) ? $emails[ $email_key ] : array();

			$sanitized['emails'][ $email_key ] = array(
				'enabled'     => ! empty( $email['enabled'] ) ? '1' : '0',
				'subject'     => isset( $email['subject'] ) ? sanitize_text_field( $email['subject'] ) : '',
				'heading'     => isset( $email['heading'] ) ? sanitize_text_field( $email['heading'] ) : '',
				'body'        => isset( $email['body'] ) ? wp_kses_post( $email['body'] ) : '',
				'button_text' => isset( $email['button_text'] ) ? sanitize_text_field( $email['button_text'] ) : '',
			);

		}

		// Allow PRO version or other extensions to add their own sanitization.
		$sanitized = apply_filters( 'ats_email_notifications_sanitize_settings', $sanitized, $input );

		return $sanitized;

	}

	/**
	 * Print the available merge tags above an email type's fields.
	 *
	 * @param array $args Settings section args, includes the section id.
	 */
	public function email_type_tags_notice( $args ) {

		$email_key  = str_replace( array( 'ats-email-notifications-', '-section' ), '', $args['id'] );
		$email_type = isset( $this->get_email_types()[ $email_key ] ) ? $this->get_email_types()[ $email_key ] : null;

		if ( ! $email_type ) {
			return;
		}
		?>

		<p><?php echo wp_kses_post( $email_type['description'] ); ?></p>

		<p>
			<?php esc_html_e( 'Use the placeholder tags below in the subject, heading, and body to display information dynamically:', 'ats-dashboard' ); ?>
		</p>

		<p>
			<?php foreach ( $email_type['tags'] as $tag_index => $tag ) : ?>
				<code><?php echo esc_html( $tag ); ?></code><?php echo ( count( $email_type['tags'] ) - 1 === $tag_index ? '' : ', ' ); ?>
			<?php endforeach; ?>
		</p>

		<?php

	}

	/**
	 * Logo URL field.
	 */
	public function logo_url_field() {

		$field = require __DIR__ . '/templates/fields/logo-url.php';
		$field();

	}

	/**
	 * Accent color field.
	 */
	public function accent_color_field() {

		$field = require __DIR__ . '/templates/fields/accent-color.php';
		$field();

	}

	/**
	 * Header background color field.
	 */
	public function header_color_field() {

		$field = require __DIR__ . '/templates/fields/header-color.php';
		$field();

	}

	/**
	 * Support email field.
	 */
	public function support_email_field() {

		$field = require __DIR__ . '/templates/fields/support-email.php';
		$field();

	}

	/**
	 * Support URL field.
	 */
	public function support_url_field() {

		$field = require __DIR__ . '/templates/fields/support-url.php';
		$field();

	}

	/**
	 * Footer text field.
	 */
	public function footer_text_field() {

		$field = require __DIR__ . '/templates/fields/footer-text.php';
		$field();

	}

	/**
	 * Email enabled field.
	 *
	 * @param array $args Field args, includes the email_key.
	 */
	public function email_enabled_field( $args ) {

		$field = require __DIR__ . '/templates/fields/email-enabled.php';
		$field( $args['email_key'] );

	}

	/**
	 * Email subject field.
	 *
	 * @param array $args Field args, includes the email_key.
	 */
	public function email_subject_field( $args ) {

		$field = require __DIR__ . '/templates/fields/email-subject.php';
		$field( $args['email_key'] );

	}

	/**
	 * Email heading field.
	 *
	 * @param array $args Field args, includes the email_key.
	 */
	public function email_heading_field( $args ) {

		$field = require __DIR__ . '/templates/fields/email-heading.php';
		$field( $args['email_key'] );

	}

	/**
	 * Email body field.
	 *
	 * @param array $args Field args, includes the email_key.
	 */
	public function email_body_field( $args ) {

		$field = require __DIR__ . '/templates/fields/email-body.php';
		$field( $args['email_key'] );

	}

	/**
	 * Email button text field.
	 *
	 * @param array $args Field args, includes the email_key.
	 */
	public function email_button_text_field( $args ) {

		$field = require __DIR__ . '/templates/fields/email-button-text.php';
		$field( $args['email_key'] );

	}

}
