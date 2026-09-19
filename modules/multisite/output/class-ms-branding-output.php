<?php
/**
 * Multisite's branding output.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Multisite\Output;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Helpers\Branding_Helper;
use ATSDash\Helpers\Multisite_Helper;

use ats\Base\Base_Output;
use ats\Branding\Branding_Output as Free_Branding_Output;
use ATSDash\Branding\Branding_Output;

/**
 * Class to setup the module output.
 */
class Ms_Branding_Output extends Base_Output {

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

		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'dashboard_styles' ), 90 );

		remove_action( 'admin_head', array( Branding_Output::get_instance(), 'admin_styles' ), 100 );
		add_action( 'admin_head', array( self::get_instance(), 'admin_styles' ), 110 );

		remove_action( 'admin_head', array( Branding_Output::get_instance(), 'wp_admin_darkmode_styles' ), 100 );
		add_action( 'admin_head', array( self::get_instance(), 'wp_admin_darkmode_styles' ), 110 );

		remove_action( 'admin_head', array( Branding_Output::get_instance(), 'block_editor_darkmode_styles' ), 100 );
		add_action( 'admin_head', array( self::get_instance(), 'block_editor_darkmode_styles' ), 110 );

		add_action( 'wp_enqueue_scripts', array( self::get_instance(), 'frontend_styles' ), 90 );
		add_action( 'admin_bar_menu', array( self::get_instance(), 'replace_admin_bar_logo' ), 11 );
		add_action( 'admin_bar_menu', array( self::get_instance(), 'remove_admin_bar_logo' ), 99 );
		add_action( 'admin_head', array( self::get_instance(), 'replace_block_editor_logo' ), 20 );
		add_filter( 'admin_footer_text', array( self::get_instance(), 'footer_text' ) );
		add_filter( 'update_footer', array( self::get_instance(), 'version_text' ), 11 );

		remove_action( 'adminmenu', array( Branding_Output::get_instance(), 'modern_admin_bar_logo' ) );
		add_action( 'adminmenu', array( self::get_instance(), 'modern_admin_bar_logo' ), 20 );

	}

	/**
	 * Enqueue dashboard styles.
	 */
	public function dashboard_styles() {
		global $blueprint;

		// Stop here if we're on the blueprint or if it's not defined.
		if ( empty( $blueprint ) || get_current_blog_id() === $blueprint ) {
			return;
		}

		$branding_helper = new Branding_Helper();

		// Stop here if branding is enabled for the current blog.
		if ( $branding_helper->is_enabled() ) {
			return;
		}

		$branding_output = Branding_Output::get_instance();

		switch_to_blog( $blueprint );
		$branding_output->dashboard_styles();
		restore_current_blog();
	}

	/**
	 * Branding admin styles.
	 */
	public function admin_styles() {

		$ms_helper       = new Multisite_Helper();
		$branding        = get_option( 'ats_branding' );
		$switch_blog     = ! isset( $branding['enabled'] ) && $ms_helper->needs_to_switch_blog( false ) ? true : false;
		$branding_output = Branding_Output::get_instance();

		if ( $switch_blog ) {
			global $blueprint;
			switch_to_blog( $blueprint );
		}

		$branding_output->admin_styles( $switch_blog );

		if ( $switch_blog ) {
			restore_current_blog();
		}

	}

	/**
	 * Enqueue darkmode styles for admin area.
	 */
	public function wp_admin_darkmode_styles() {

		$ms_helper   = new Multisite_Helper();
		$branding    = get_option( 'ats_branding', array() );
		$switch_blog = ! isset( $branding['wp_admin_darkmode'] ) && $ms_helper->needs_to_switch_blog( false ) ? true : false;

		if ( $switch_blog ) {
			global $blueprint;
			switch_to_blog( $blueprint );
		}

		Branding_Output::get_instance()->wp_admin_darkmode_styles( $switch_blog );

		if ( $switch_blog ) {
			restore_current_blog();
		}

	}

	/**
	 * Enqueue darkmode styles for block editor screen.
	 */
	public function block_editor_darkmode_styles() {

		$ms_helper   = new Multisite_Helper();
		$branding    = get_option( 'ats_branding', array() );
		$switch_blog = ! isset( $branding['block_editor_darkmode'] ) && $ms_helper->needs_to_switch_blog( false ) ? true : false;

		if ( $switch_blog ) {
			global $blueprint;
			switch_to_blog( $blueprint );
		}

		Branding_Output::get_instance()->block_editor_darkmode_styles( $switch_blog );

		if ( $switch_blog ) {
			restore_current_blog();
		}

	}

	/**
	 * Frontend styles.
	 */
	public function frontend_styles() {

		global $blueprint;

		$branding_helper = new Branding_Helper();

		// Stop here if we're on the blueprint or if it's not defined.
		if ( empty( $blueprint ) || get_current_blog_id() === $blueprint ) {
			return;
		}

		// Stop here if branding is enabled for the current blog.
		if ( $branding_helper->is_enabled() ) {
			return;
		}

		$branding_output = Branding_Output::get_instance();

		switch_to_blog( $blueprint );
		$branding_output->frontend_styles();
		restore_current_blog();

	}

	/**
	 * Replace admin bar logo.
	 *
	 * @param object $wp_admin_bar WP admin bar.
	 */
	public function replace_admin_bar_logo( $wp_admin_bar ) {

		global $blueprint;

		$branding_helper = new Branding_Helper();

		// Stop here if we're on the blueprint or if it's not defined.
		if ( empty( $blueprint ) || get_current_blog_id() === $blueprint ) {
			return;
		}

		// Stop here if branding is enabled for the current blog.
		if ( $branding_helper->is_enabled() ) {
			return;
		}

		$branding_output = Branding_Output::get_instance();

		switch_to_blog( $blueprint );
		$branding_output->replace_admin_bar_logo( $wp_admin_bar );
		restore_current_blog();

	}

	/**
	 * Remove admin bar logo.
	 *
	 * @param object $wp_admin_bar WP admin bar.
	 */
	public function remove_admin_bar_logo( $wp_admin_bar ) {

		global $blueprint;

		// Stop here if we're on the blueprint or if it's not defined.
		if ( empty( $blueprint ) || get_current_blog_id() === $blueprint ) {
			return;
		}

		$branding_output = Branding_Output::get_instance();

		switch_to_blog( $blueprint );
		$branding_output->remove_admin_bar_logo( $wp_admin_bar );
		restore_current_blog();

	}

	/**
	 * Replace block editor logo.
	 */
	public function replace_block_editor_logo() {

		global $blueprint;

		$branding_helper = new Branding_Helper();

		// Stop here if we're on the blueprint or if it's not defined.
		if ( empty( $blueprint ) || get_current_blog_id() === $blueprint ) {
			return;
		}

		// Stop here if branding is enabled for the current blog.
		if ( $branding_helper->is_enabled() ) {
			return;
		}

		$branding_output = Branding_Output::get_instance();

		switch_to_blog( $blueprint );
		$branding_output->replace_block_editor_logo();
		restore_current_blog();

	}

	/**
	 * Footer text.
	 *
	 * @param string $footer_text The footer text.
	 *
	 * @return string The updated footer text.
	 */
	public function footer_text( $footer_text ) {

		global $blueprint;

		// Stop here if we're on the blueprint or if it's not defined.
		if ( empty( $blueprint ) || get_current_blog_id() === $blueprint ) {
			return $footer_text;
		}

		// Get branding option from current blog.
		$branding = get_option( 'ats_branding' );

		// Stop here if footer text is defined for the current blog.
		if ( ! empty( $branding['footer_text'] ) ) {
			return $footer_text;
		}

		$branding_output = Free_Branding_Output::get_instance();

		switch_to_blog( $blueprint );
		$footer_text = $branding_output->footer_text( $footer_text );
		restore_current_blog();

		return $footer_text;

	}

	/**
	 * Version text.
	 *
	 * @param string $version_text The version text.
	 *
	 * @return string The updated version text.
	 */
	public function version_text( $version_text ) {

		global $blueprint;

		// Stop here if we're on the blueprint or if it's not defined.
		if ( empty( $blueprint ) || get_current_blog_id() === $blueprint ) {
			return $version_text;
		}

		// Get branding option from current blog.
		$branding = get_option( 'ats_branding' );

		// Stop here if version text is defined for the current blog.
		if ( ! empty( $branding['version_text'] ) ) {
			return $version_text;
		}

		$branding_output = Free_Branding_Output::get_instance();

		switch_to_blog( $blueprint );
		$version_text = $branding_output->version_text( $version_text );
		restore_current_blog();

		return $version_text;

	}

	/**
	 * Modern layout: custom admin bar logo.
	 */
	public function modern_admin_bar_logo() {

		$ms_helper       = new Multisite_Helper();
		$branding        = get_option( 'ats_branding' );
		$switch_blog     = ! isset( $branding['enabled'] ) && $ms_helper->needs_to_switch_blog( false ) ? true : false;
		$branding_output = Branding_Output::get_instance();

		if ( $switch_blog ) {
			global $blueprint;
			switch_to_blog( $blueprint );
		}

		$branding_output->modern_admin_bar_logo( $switch_blog );

		if ( $switch_blog ) {
			restore_current_blog();
		}

	}

}
