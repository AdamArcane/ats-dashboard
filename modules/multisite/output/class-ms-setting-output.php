<?php
/**
 * Multisite's settings output.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Multisite\Output;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Helpers\Multisite_Helper;
use ATSDash\Base\Base_Output;
use ATSDash\Setting\Setting_Output;

/**
 * Class to setup the module output.
 */
class Ms_Setting_Output extends Base_Output {

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

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/multisite';

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
	 * Init the class setup.
	 */
	public static function init() {

		$class = new self();
		$class->setup();

	}

	/**
	 * Setup the module output.
	 */
	public function setup() {

		add_action( 'admin_init', array( self::get_instance(), 'custom_welcome_panel' ), 199 );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'dashboard_custom_css' ), 199 );
		add_action( 'admin_head', array( self::get_instance(), 'admin_custom_css' ), 199 );
		add_action( 'admin_head', array( self::get_instance(), 'change_dashboard_headline' ), 199 );

		// The priority here is set to 10001 because the free version has 'change_howdy_text' function hooked with priority 10000.
		add_action( 'admin_bar_menu', array( self::get_instance(), 'change_howdy_text' ), 10001 );

		add_action( 'admin_head', array( self::get_instance(), 'remove_help_tab' ) );
		add_filter( 'screen_options_show_screen', array( self::get_instance(), 'remove_screen_options_tab' ), 199 );
		add_action( 'init', array( self::get_instance(), 'remove_admin_bar' ), 199 );
		add_action( 'admin_init', array( self::get_instance(), 'remove_font_awesome' ), 15 );

	}

	/**
	 * Check if we have custom welcome panel.
	 */
	public function custom_welcome_panel() {

		if ( ! method_exists( Setting_Output::get_instance(), 'custom_welcome_panel' ) ) {
			return;
		}

		$ms_helper   = new Multisite_Helper();
		$switch_blog = $ms_helper->needs_to_switch_blog( false ) ? true : false;

		$settings    = get_option( 'ats_settings' );
		$has_content = ! isset( $settings['welcome_panel_content'] ) || empty( $settings['welcome_panel_content'] ) ? false : true;

		// Stop if it doesn't need to switch blog or if current site has welcome panel content.
		if ( ! $switch_blog || $has_content ) {
			return;
		}

		global $blueprint;
		switch_to_blog( $blueprint );

		Setting_Output::get_instance()->custom_welcome_panel();

		restore_current_blog();

	}

	/**
	 * Add dashboard custom CSS.
	 */
	public function dashboard_custom_css() {

		global $blueprint;

		// Stop here if we're on the blueprint or if it's not defined.
		if ( empty( $blueprint ) || get_current_blog_id() === $blueprint ) {
			return;
		}

		// Get setting option from current blog.
		$settings = get_option( 'ats_settings' );

		// Stop here if custom CSS is defined for the current blog.
		if ( isset( $settings['custom_css'] ) && ! empty( $settings['custom_css'] ) ) {
			return;
		}

		$settings_output = Setting_Output::get_instance();

		switch_to_blog( $blueprint );
		$settings_output->dashboard_custom_css();
		restore_current_blog();

	}

	/**
	 * Add admin custom CSS.
	 */
	public function admin_custom_css() {

		global $blueprint;

		// Stop here if we're on the blueprint or if it's not defined.
		if ( empty( $blueprint ) || get_current_blog_id() === $blueprint ) {
			return;
		}

		// Get setting option from current blog.
		$settings = get_option( 'ats_settings' );

		// Stop here if custom CSS is defined for the current blog.
		if ( isset( $settings['custom_admin_css'] ) && ! empty( $settings['custom_admin_css'] ) ) {
			return;
		}

		$settings_output = Setting_Output::get_instance();

		switch_to_blog( $blueprint );
		$settings_output->admin_custom_css();
		restore_current_blog();

	}

	/**
	 * Change Dashboard's headline.
	 */
	public function change_dashboard_headline() {

		global $blueprint;

		// Stop here if we're on the blueprint or if it's not defined.
		if ( empty( $blueprint ) || get_current_blog_id() === $blueprint ) {
			return;
		}

		$settings_output = Setting_Output::get_instance();

		switch_to_blog( $blueprint );
		$settings_output->change_dashboard_headline();
		restore_current_blog();

	}

	/**
	 * Change "Howdy" text.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance.
	 */
	public function change_howdy_text( $wp_admin_bar ) {

		if ( ! method_exists( Setting_Output::get_instance(), 'change_howdy_text' ) ) {
			return;
		}

		global $blueprint;

		// Stop here if we're on the blueprint or if it's not defined.
		if ( empty( $blueprint ) || get_current_blog_id() === $blueprint ) {
			return;
		}

		$settings_output = Setting_Output::get_instance();

		switch_to_blog( $blueprint );
		$settings_output->change_howdy_text( $wp_admin_bar );
		restore_current_blog();

	}

	/**
	 * Remove help tab on admin area.
	 */
	public function remove_help_tab() {

		global $blueprint;

		// Stop here if we're on the blueprint or if it's not defined.
		if ( empty( $blueprint ) || get_current_blog_id() === $blueprint ) {
			return;
		}

		$settings_output = Setting_Output::get_instance();

		switch_to_blog( $blueprint );
		$settings_output->remove_help_tab();
		restore_current_blog();

	}

	/**
	 * Remove screen options on admin area.
	 *
	 * @param bool $show_screen Whether or not to show the screen options tab.
	 * @return bool
	 */
	public function remove_screen_options_tab( $show_screen ) {

		global $blueprint;

		// Stop here if we're on the blueprint or if it's not defined.
		if ( empty( $blueprint ) || get_current_blog_id() === $blueprint ) {
			return $show_screen;
		}

		$settings_output = Setting_Output::get_instance();

		switch_to_blog( $blueprint );
		$show_screen = $settings_output->remove_screen_options_tab( $show_screen );
		restore_current_blog();

		return $show_screen;

	}

	/**
	 * Remove admin bar from frontend.
	 *
	 * @return void
	 */
	public function remove_admin_bar() {

		global $blueprint;

		// Stop here if we're on the blueprint or if it's not defined.
		if ( empty( $blueprint ) || get_current_blog_id() === $blueprint ) {
			return;
		}

		$settings_output = Setting_Output::get_instance();

		// Before switching blog, we need to get the original user roles.
		$current_user = wp_get_current_user();
		$user_roles   = $current_user->roles;
		$user_roles   = is_array( $user_roles ) ? $user_roles : array();

		switch_to_blog( $blueprint );
		$settings_output->remove_admin_bar( $user_roles );
		restore_current_blog();

	}

	/**
	 * Remove Font Awesome.
	 */
	public function remove_font_awesome() {

		global $blueprint;

		// Stop here if we're on the blueprint or if it's not defined.
		if ( empty( $blueprint ) || get_current_blog_id() === $blueprint ) {
			return;
		}

		$settings_output = Setting_Output::get_instance();

		switch_to_blog( $blueprint );
		$settings_output->remove_font_awesome();
		restore_current_blog();

	}

}
