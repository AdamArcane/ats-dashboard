<?php
/**
 * Login url module.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\LoginRedirect;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Base\Base_Module;

/**
 * Class to setup login url module.
 */
class Login_Redirect_Module extends Base_Module {
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

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/login-redirect';

	}

	/**
	 * Setup login url module.
	 */
	public function setup() {

		// Stop if free version hasn't been updated to have the login url module.
		if ( ! class_exists( '\ats\LoginRedirect\Login_Redirect_Module' ) ) {
			return;
		}

		add_action( 'init', array( $this, 'setup_hooks' ) );

	}

	/**
	 * Setup functions hooking on init.
	 *
	 * Currently this module will run for multisite only, so we need to hook it to `init` action hook.
	 * And we can't use `admin_init` hook here because `ats_login_redirect_title` will run on `admin_init`
	 * on the free version in 'ats-dashboard/modules/setting/class-setting-module.php' file.
	 */
	public function setup_hooks() {

		if ( ! apply_filters( 'ats_ms_is_blueprint', false ) ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_filter( 'ats_login_redirect_title', array( $this, 'add_tab_menu' ) );
		add_action( 'admin_init', array( $this, 'add_settings' ), 20 );

		// Sanitization.
		add_filter( 'ats_login_redirect_sanitize_settings', array( $this, 'sanitize_pro_fields' ), 10, 2 );

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

		$enqueue = require __DIR__ . '/inc/js-enqueue.php';
		$enqueue( $this );

	}

	/**
	 * Add settings.
	 */
	public function add_settings() {

		// Login redirect fields.
		add_settings_field( 'subsites-login-redirect-url', __( 'Select Role(s)', 'ats-dashboard' ), array( $this, 'login_redirect_url_field' ), 'ats-login-redirect-settings', 'ats-login-redirect-section' );

	}

	/**
	 * Sanitize PRO fields for login redirect settings.
	 *
	 * @param array $sanitized The sanitized settings from the free version.
	 * @param array $input The raw input data.
	 * @return array The sanitized settings with PRO fields added.
	 */
	public function sanitize_pro_fields( $sanitized, $input ) {

		if ( isset( $input['subsites_login_redirect_slugs'] ) && is_array( $input['subsites_login_redirect_slugs'] ) ) {
			$sanitized['subsites_login_redirect_slugs'] = array();

			foreach ( $input['subsites_login_redirect_slugs'] as $role_key => $redirect_slug ) {
				$sanitized_role_key = sanitize_key( $role_key );
				$sanitized['subsites_login_redirect_slugs'][ $sanitized_role_key ] = sanitize_text_field( $redirect_slug );
			}
		}

		return $sanitized;

	}

	/**
	 * Add tabs nav to "Redirect After Login" metabox header.
	 *
	 * @param string $title The metabox title.
	 */
	public function add_tab_menu( $title ) {

		$tabs_nav = '
		<span class="ats-login-redirect--tab-menu">
			<span class="ats-login-redirect--tab-menu-item is-active" data-ats-tab="blueprint">
				' . __( 'Blueprint', 'ats-dashboard' ) . '
			</span>
			<span class="ats-login-redirect--tab-menu-item" data-ats-tab="subsites">
				' . __( 'Subsites', 'ats-dashboard' ) . '
			</span>
		</span>
		';

		$tabs_nav = $title . $tabs_nav;

		return $tabs_nav;

	}

	/**
	 * Subsites login redirect url field.
	 */
	public function login_redirect_url_field() {

		$field = require ATS_DASHBOARD_PLUGIN_DIR . '/modules/login-redirect/templates/fields/login-redirect-url.php';
		$field( 'subsites' );

	}

}
