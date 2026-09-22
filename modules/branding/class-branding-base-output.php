<?php
/**
 * Branding output.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Branding;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Base\Base_Output;

/**
 * Class to setup branding output.
 */
class Branding_Base_Output extends Base_Output {

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

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/branding';

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
	 * Setup branding output.
	 */
	public function setup() {

		add_filter( 'admin_footer_text', array( self::get_instance(), 'footer_text' ) );
		add_filter( 'update_footer', array( self::get_instance(), 'version_text' ), 20 );

	}

	/**
	 * Footer text.
	 *
	 * @param string $footer_text The footer text.
	 *
	 * @return string The updated footer text.
	 */
	public function footer_text( $footer_text ) {

		$branding = get_option( 'ats_branding' );

		if ( ! empty( $branding['footer_text'] ) ) {
			$footer_text = $branding['footer_text'];
		}

		return $footer_text;

	}

	/**
	 * Version text.
	 *
	 * Defaults to the plugin's own version instead of WordPress core's,
	 * since the branded dashboard shouldn't be surfacing the underlying
	 * WordPress version to its users.
	 *
	 * @param string $version_text The version text.
	 *
	 * @return string The updated version text.
	 */
	public function version_text( $version_text ) {

		$branding = get_option( 'ats_branding' );

		if ( ! empty( $branding['version_text'] ) ) {
			$version_text = $branding['version_text'];
		} else {
			$version_text = 'ATS Dashboard ' . ATS_DASHBOARD_PLUGIN_VERSION;
		}

		return $version_text;

	}

}
