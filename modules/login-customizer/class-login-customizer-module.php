<?php
/**
 * Login Customizer module.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\LoginCustomizer;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Helpers\Multisite_Helper;

/**
 * Class to setup login customizer module.
 */
class Login_Customizer_Module extends \ats\LoginCustomizer\Login_Customizer_Base_Module {

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

		parent::setup();
		add_action( 'customize_controls_enqueue_scripts', array( self::get_instance(), 'control_scripts' ) );
		add_action( 'login_enqueue_scripts', array( self::get_instance(), 'preview_styles' ), 99 );
		add_action( 'customize_preview_init', array( self::get_instance(), 'preview_scripts' ) );

		add_filter( 'ats_login_customizer_control_file_paths', array( self::get_instance(), 'modify_control_files' ) );
		add_filter( 'ats_login_customizer_default_logo_height', array( self::get_instance(), 'default_logo_height' ) );

		// The module output.
		require_once __DIR__ . '/class-login-customizer-output.php';
		Login_Customizer_Output::init();

	}

	/**
	 * Enqueue login customizer control scripts.
	 */
	public function control_scripts() {

		parent::control_scripts();
		$asset_version = ATS_DASHBOARD_PLUGIN_VERSION . '.10';
		wp_enqueue_script( 'ats-pro-login-customizer-control', $this->url . '/assets/js/controls.js', array( 'customize-controls' ), $asset_version, true );

		wp_add_inline_script(
			'ats-login-customizer-control',
			'!function(){var e=function(){if(window.atsLoginCustomizer&&atsLoginCustomizer.loginPageUrl&&wp.customize.previewer){wp.customize.previewer.previewUrl.set(atsLoginCustomizer.loginPageUrl);}};wp.customize.bind("ready",e);wp.customize.bind("preview-ready",e);}();',
			'after'
		);

	}

	/**
	 * Enqueue scripts to login customizer preview scripts.
	 */
	public function preview_scripts() {

		parent::preview_scripts();
		wp_enqueue_script( 'ats-pro-login-customizer-preview', $this->url . '/assets/js/preview.js', array( 'customize-preview' ), ATS_DASHBOARD_PLUGIN_VERSION . '.10', true );

	}

	/**
	 * Enqueue styles to login customizer preview styles.
	 */
	public function preview_styles() {

		if ( ! is_customize_preview() ) {
			return;
		}

		wp_enqueue_style( 'ats-pro-login-customizer-preview', $this->url . '/assets/css/preview.css', array( 'ats-login-customizer-preview' ), ATS_DASHBOARD_PLUGIN_VERSION, 'all' );

	}

	/**
	 * Change the default logo height value of login customizer
	 *
	 * @param string|int $height The logo height value.
	 * @return string|int The logo height value.
	 */
	public function default_logo_height( $height ) {

		$ms_helper       = new Multisite_Helper();
		$blueprint_login = array();

		if ( $ms_helper->needs_to_switch_blog() ) {
			global $blueprint;

			$blueprint_login = get_blog_option( $blueprint, 'ats_login', array() );
			$height          = ! empty( $blueprint_login ) && isset( $blueprint_login['logo_height'] ) ? $blueprint_login['logo_height'] : '100%';
		}

		return $height;

	}

	/**
	 * Modify existing ats control files.
	 *
	 * @param array $files Associative array containing "section -> file" pairs.
	 * @return array $files
	 */
	public function modify_control_files( $files ) {

		/**
		 * We use "*_pro" here because the basic name is already used by the free version.
		 * For instance, the "bg" and "layout" is already used in the free version.
		 *
		 * Previously (v <= 3.6.3 Free version and v <= 3.6.2 PRO version),
		 * the "bg" & "layout" in the free version were overriden by the pro version.
		 *
		 * Now, we change the behavior.
		 * The PRO version should just extend the free version instead of replacing it.
		 * This is due to our effort to migrate Erident Login users to ats.
		 */
		$files['layout_pro'] = __DIR__ . '/sections/layout.php';

		return $files;

	}

}
