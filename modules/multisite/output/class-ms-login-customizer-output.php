<?php
/**
 * Multisite's login customizer output.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Multisite\Output;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Base\Base_Output;
use ats\LoginCustomizer\Login_Customizer_Output as Free_Login_Customizer_Output;

use ATSDash\LoginCustomizer\Login_Customizer_Output;

/**
 * Class to setup the module output.
 */
class Ms_Login_Customizer_Output extends Base_Output {

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

		add_filter( 'login_headertext', array( self::get_instance(), 'login_headertext' ), 30 );
		add_filter( 'login_headerurl', array( self::get_instance(), 'login_logo_url' ), 30 );
		add_action( 'customize_register', array( self::get_instance(), 'customizer_sections' ) );

		remove_action( 'login_head', array( Login_Customizer_Output::get_instance(), 'print_login_styles' ), 20 );
		add_action( 'login_head', array( self::get_instance(), 'print_login_styles' ), 20 );

	}

	/**
	 * Change login page header text.
	 *
	 * @param string $text The existing header text.
	 * @return string The modified header text.
	 */
	public function login_headertext( $text ) {

		global $blueprint;

		// Stop here if we're on the blueprint or if it's not defined.
		if ( empty( $blueprint ) || get_current_blog_id() === $blueprint ) {
			return $text;
		}

		// Get login option from the current blog.
		$login = get_option( 'ats_login' );

		// Stop here if logo url is set on the current blog.
		if ( isset( $login['logo_title'] ) && ! empty( $login['logo_title'] ) ) {
			return $text;
		}

		$login_customizer_output = Free_Login_Customizer_Output::get_instance();

		switch_to_blog( $blueprint );
		$text = $login_customizer_output->login_headertext( $text );
		restore_current_blog();

		return $text;

	}

	/**
	 * Change login logo url.
	 *
	 * @param string $url The existing login logo url.
	 * @return string The modified login logo url.
	 */
	public function login_logo_url( $url ) {

		global $blueprint;

		// Stop here if we're on the blueprint or if it's not defined.
		if ( empty( $blueprint ) || get_current_blog_id() === $blueprint ) {
			return $url;
		}

		// Get login option from the current blog.
		$login = get_option( 'ats_login' );

		// Stop here if logo url is set on the current blog.
		if ( isset( $login['logo_url'] ) && ! empty( $login['logo_url'] ) ) {
			return $url;
		}

		switch_to_blog( $blueprint );

		$login = get_option( 'ats_login' );

		// Get login URL from blueprint.
		if ( isset( $login['logo_url'] ) && ! empty( $login['logo_url'] ) ) {
			$url = $login['logo_url'];
		}

		restore_current_blog();

		// If login URL on the blueprint is {home_url}, replace it with the actual home URL of the subsite.
		if ( '{home_url}' === $url ) {
			$url = home_url();
		}

		return $url;

	}

	/**
	 * Print login styles.
	 */
	public function print_login_styles() {

		global $blueprint;

		$switch_blog = $blueprint && get_current_blog_id() !== $blueprint ? true : false;

		$login_customizer_output = Login_Customizer_Output::get_instance();

		if ( $switch_blog ) {
			switch_to_blog( $blueprint );

			// Print blueprint styles (login.css.php).
			$login_customizer_output->print_login_styles();

			restore_current_blog();

			// Then print current site's stytles (login-subsite.css.php).
			$login_customizer_output->print_login_styles( true );
		} else {
			$login_customizer_output->print_login_styles();
		}

	}

	/**
	 * Register login customizer's sections in WP Customizer.
	 *
	 * @param WP_Customize $wp_customize The WP_Customize instance.
	 */
	public function customizer_sections( $wp_customize ) {

		// Stop here if we're on the main site.
		if ( is_main_site() ) {
			return;
		}

		$wp_customize->add_section(
			'ats_login_customizer_logo_section',
			array(
				'title' => __( 'Logo', 'ats-dashboard' ),
				'panel' => 'ats_login_customizer_panel',
			)
		);

	}

}
