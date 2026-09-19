<?php
/**
 * Branding output.
 *
 * @package ATS_Dashboard
 */

namespace ats\Feature;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Base\Base_Output;

/**
 * Class to setup dashboard output.
 */
class Feature_Output extends Base_Output {

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

		$this->url = ATS_DASHBOARD_CORE_URL . '/modules/feature';

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
	 * Setup dashboard output.
	 */
	public function setup() {

		// add_filter( 'admin_footer_text', array( self::get_instance(), 'footer_text' ) );
		// add_filter( 'update_footer', array( self::get_instance(), 'version_text' ), 20 );
	}

}
