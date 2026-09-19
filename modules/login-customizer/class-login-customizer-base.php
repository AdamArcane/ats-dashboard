<?php
/**
 * Login Customizer module.
 *
 * @package ATS_Dashboard
 */

namespace ats\LoginCustomizer;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Base\Base_Module;

/**
 * Class to setup login customizer module.
 */
class Login_Customizer_Base_Module extends Base_Module {

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

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/login-customizer';

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
	 * Setup login customizer module.
	 */
	public function setup() {

		add_action( 'admin_menu', array( $this, 'submenu_page' ) );

		// Create custom page (custom rewrite, not a real page).
		add_action( 'init', array( $this, 'rewrite_tags' ) );
		add_action( 'init', array( $this, 'rewrite_rules' ) );
		add_action( 'wp', array( $this, 'set_custom_page' ) );

		// Setup redirect.
		add_action( 'init', array( $this, 'redirect_frontend_page' ) );
		add_action( 'admin_init', array( $this, 'redirect_edit_page' ) );

		// Setup customizer.
		add_action( 'customize_register', array( $this, 'register_panels' ) );
		add_action( 'customize_register', array( $this, 'register_sections' ) );
		add_action( 'customize_register', array( $this, 'register_controls' ) );

		// Enqueue assets.
		add_action( 'customize_controls_print_styles', array( $this, 'control_styles' ), 99 );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'control_scripts' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'preview_styles' ), 99 );
		add_action( 'customize_preview_init', array( $this, 'preview_scripts' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'login_scripts' ), 99 );

		// The module output.
		require_once __DIR__ . '/class-login-customizer-base-output.php';
		Login_Customizer_Base_Output::init();

	}

	/**
	 * Add "Login Customizer" submenu under "ATS Dashboard" menu item.
	 * We're not usind add_submenu_page here because we're actually sending people to the customizer.
	 */
	public function submenu_page() {

		global $submenu;

		$ats_slug = 'edit.php?post_type=ats_widgets';

		// It's not set for users like subscribers with lower capabilities so this will throw an error if we don't check.
		if ( ! isset( $submenu[ $ats_slug ] ) ) {
			return;
		}

		array_push(
			$submenu[ $ats_slug ],
			array(
				__( 'Login Customizer', 'ats-dashboard' ),
				apply_filters( 'ats_settings_capability', 'manage_options' ),
				esc_url( admin_url( 'customize.php?autofocus%5Bpanel%5D=ats_login_customizer_panel' ) ),
			)
		);

	}

	/**
	 * Register rewrite tags.
	 *
	 * @return void
	 */
	public function rewrite_tags() {

		add_rewrite_tag( '%ats-login-customizer%', '([^&]+)' );

	}

	/**
	 * Register rewrite rules.
	 *
	 * @return void
	 */
	public function rewrite_rules() {

		// Rewrite rule for "ats-login-customizer" page.
		add_rewrite_rule(
			'^ats-login-customizer/?',
			'index.php?pagename=ats-login-customizer',
			'top'
		);

		// Flush the rewrite rules if it hasn't been flushed.
		if ( ! get_option( 'ats_login_customizer_flush_url' ) ) {
			flush_rewrite_rules( false );
			update_option( 'ats_login_customizer_flush_url', 1 );
		}

	}

	/**
	 * Set page manually by modifying 404 page.
	 *
	 * @return void
	 */
	public function set_custom_page() {

		// Only modify 404 page.
		if ( ! is_404() ) {
			return;
		}

		$pagename = sanitize_text_field( get_query_var( 'pagename' ) );

		// Only set for intended page.
		if ( 'ats-login-customizer' !== $pagename ) {
			return;
		}

		// If user is not logged-in, then redirect.
		if ( ! is_user_logged_in() ) {
			wp_safe_redirect( home_url() );
			exit;
		}

		// Only allow user with 'manage_options' capability.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_safe_redirect( home_url() );
			exit;
		}

		status_header( 200 );
		load_template( __DIR__ . '/templates/ats-login-page.php', true );
		exit;

	}

	/**
	 * Redirect "Login Customizer" frontend page to WordPress customizer page.
	 */
	public function redirect_frontend_page() {

		if ( ! isset( $_GET['page'] ) || 'ats-login-page' !== $_GET['page'] ) {
			return;
		}

		// Pull the Login Customizer page from options.
		$page = get_page_by_path( 'ats-login-page' );

		// Generate the redirect url.
		$redirect_url = add_query_arg(
			array(
				'autofocus[panel]' => 'ats_login_customizer_panel',
				'url'              => rawurlencode( get_permalink( $page ) ),
			),
			admin_url( 'customize.php' )
		);

		wp_safe_redirect( $redirect_url );

	}

	/**
	 * Redirect "Login Customizer" edit page to WordPress customizer page.
	 */
	public function redirect_edit_page() {

		global $pagenow;

		// Pull the Login Customizer page from options.
		$page = get_page_by_path( 'ats-login-page' );

		if ( ! $page ) {
			return;
		}

		// Generate the redirect url.
		$redirect_url = add_query_arg(
			array(
				'autofocus[panel]' => 'ats_login_customizer_panel',
				'url'              => rawurlencode( get_permalink( $page ) ),
			),
			admin_url( 'customize.php' )
		);

		if ( 'post.php' === $pagenow && ( isset( $_GET['post'] ) && absint( $page->ID ) === absint( $_GET['post'] ) ) ) {
			wp_safe_redirect( $redirect_url );
		}

	}

	/**
	 * Register "Login Customizer" panel in WP Customizer.
	 *
	 * @param WP_Customize $wp_customize The WP_Customize instance.
	 */
	public function register_panels( $wp_customize ) {

		$wp_customize->add_panel(
			'ats_login_customizer_panel',
			array(
				'title'      => __( 'Login Customizer', 'ats-dashboard' ),
				'capability' => apply_filters( 'ats_settings_capability', 'manage_options' ),
				'priority'   => 30,
			)
		);

	}

	/**
	 * Register login customizer's sections in WP Customizer.
	 *
	 * @param WP_Customize $wp_customize The WP_Customize instance.
	 */
	public function register_sections( $wp_customize ) {

		require_once __DIR__ . '/settings/class-custom-css-setting.php';

		$add_sections = require_once __DIR__ . '/inc/add-sections.php';
		$add_sections( $wp_customize );

	}

	/**
	 * Register customizer controls.
	 *
	 * @param WP_Customize $wp_customize The WP_Customize instance.
	 */
	public function register_controls( $wp_customize ) {

		// Customize control classes.
		require __DIR__ . '/controls/class-udb-customize-control.php';
		require __DIR__ . '/controls/class-udb-customize-pro-control.php';
		require __DIR__ . '/controls/class-udb-customize-range-control.php';
		require __DIR__ . '/controls/class-udb-customize-image-control.php';
		require __DIR__ . '/controls/class-udb-customize-color-control.php';
		require __DIR__ . '/controls/class-udb-customize-color-picker-control.php';
		require __DIR__ . '/controls/class-udb-customize-login-template-control.php';
		require __DIR__ . '/controls/class-udb-customize-toggle-switch-control.php';

		$branding         = get_option( 'ats_branding', array() );
		$branding_enabled = isset( $branding['enabled'] ) ? true : false;
		$accent_color     = isset( $branding['accent_color'] ) ? $branding['accent_color'] : '';
		$has_accent_color = $branding_enabled && ! empty( $accent_color ) ? true : false;

		$control_files = array(
			'template'    => __DIR__ . '/sections/template.php',
			'logo'        => __DIR__ . '/sections/logo.php',
			'bg'          => __DIR__ . '/sections/bg.php',
			'layout'      => __DIR__ . '/sections/layout.php',
			'fields'      => __DIR__ . '/sections/fields.php',
			'labels'      => __DIR__ . '/sections/labels.php',
			'button'      => __DIR__ . '/sections/button.php',
			'form-footer' => __DIR__ . '/sections/form-footer.php',
			'custom-css'  => __DIR__ . '/sections/custom-css.php',
		);

		$control_files = apply_filters( 'ats_login_customizer_control_file_paths', $control_files );

		// Register login customizer's settings & controls in WP Customizer.
		foreach ( $control_files as $section => $file ) {
			require $file;
		}

	}

	/**
	 * Enqueue the login customizer control styles.
	 */
	public function control_styles() {

		wp_enqueue_style( 'heatbox', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/heatbox.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );
		wp_enqueue_style( 'ats-login-customizer', $this->url . '/assets/css/controls.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

	}

	/**
	 * Enqueue login customizer control scripts.
	 */
	public function control_scripts() {

		$asset_version = ATS_DASHBOARD_PLUGIN_VERSION . '.10';
		wp_enqueue_script( 'ats-login-customizer-control', $this->url . '/assets/js/controls.js', array( 'customize-controls' ), $asset_version, true );


		wp_localize_script(
			'customize-controls',
			'atsLoginCustomizer',
			$this->create_js_object()
		);

	}

	/**
	 * Login customizer's localized JS object.
	 *
	 * @return array The login customizer's localized JS object.
	 */
	public function create_js_object() {

		return array(
			'homeUrl'      => home_url(),
			'loginPageUrl' => home_url( 'ats-login-customizer' ),
			'pluginUrl'    => rtrim( ATS_DASHBOARD_PLUGIN_URL, '/' ),
			'moduleUrl'    => ATS_DASHBOARD_PLUGIN_URL . '/modules/login-customizer',
			'assetUrl'     => $this->url . '/assets',
			'wpLogoUrl'    => admin_url( 'images/wordpress-logo.svg?ver=' . ATS_DASHBOARD_PLUGIN_VERSION ),
			'isProActive'  => ats_is_pro_active(),
		);

	}

	/**
	 * Enqueue styles to login customizer preview styles.
	 */
	public function preview_styles() {

		if ( ! is_customize_preview() ) {
			return;
		}

		wp_enqueue_style( 'ats-login-customizer-hint', $this->url . '/assets/css/hint.css', array(), ATS_DASHBOARD_PLUGIN_VERSION, 'all' );

		wp_enqueue_style( 'ats-login-customizer-preview', $this->url . '/assets/css/preview.css', array(), ATS_DASHBOARD_PLUGIN_VERSION, 'all' );

	}

	/**
	 * Enqueue scripts to login customizer preview scripts.
	 */
	public function preview_scripts() {

		wp_enqueue_script( 'ats-login-customizer-preview', $this->url . '/assets/js/preview.js', array( 'customize-preview' ), ATS_DASHBOARD_PLUGIN_VERSION . '.10', true );

		wp_localize_script(
			'customize-preview',
			'atsLoginCustomizer',
			$this->create_js_object()
		);

	}

	/**
	 * Enqueue scripts to the actual login page.
	 */
	public function login_scripts() {

		wp_enqueue_script( 'ats-login-page', $this->url . '/assets/js/login-page.js', array(), ATS_DASHBOARD_PLUGIN_VERSION, true );

	}

}
