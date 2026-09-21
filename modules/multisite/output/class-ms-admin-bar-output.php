<?php
/**
 * Multisite's admin bar output.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Multisite\Output;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Base\Base_Output;
use ATSDash\Helpers\User_Helper;
use ATSDash\AdminBar\Admin_Bar_Base_Module as Free_Admin_Bar_Module;

use ATSDash\Helpers\Multisite_Helper;
use ATSDash\AdminBar\Admin_Bar_Output;

/**
 * Class to setup the module output.
 */
class Ms_Admin_Bar_Output extends Base_Output {

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

		add_filter( 'ats_ms_admin_bar_saved_menu', array( self::get_instance(), 'ms_get_saved_menu' ) );
		remove_action( 'wp_before_admin_bar_render', array( Admin_Bar_Output::get_instance(), 'menu_output' ), 1000000 );
		add_action( 'wp_before_admin_bar_render', array( self::get_instance(), 'menu_output' ), 1000005 );

	}

	/**
	 * Add switch blog checking to the saved menu when it's on multisite.
	 *
	 * This filter is applied in the free version under:
	 * modules\admin-bar\templates\template.php file.
	 *
	 * @param array $saved_menu The existing saved menu.
	 * @return array
	 */
	public function ms_get_saved_menu( $saved_menu ) {
		$ms_helper       = new Multisite_Helper();
		$switch_blog     = $ms_helper->needs_to_switch_blog( false ) ? true : false;
		$saved_admin_bar = get_option( 'ats_admin_bar', array() );

		if ( $switch_blog && empty( $saved_admin_bar ) ) {
			global $blueprint;
			switch_to_blog( $blueprint );

			$saved_menu = get_option( 'ats_admin_bar', array() );

			restore_current_blog();
		}

		return $saved_menu;
	}

	/**
	 * Prepare admin bar nodes.
	 */
	public function menu_output() {

		global $wp_admin_bar;

		$ms_helper = new Multisite_Helper();

		// Stop if multisite enabled & current user is a super admin.
		if ( $ms_helper->multisite_supported() && is_super_admin() ) {
			return;
		}

		$output_module = new Admin_Bar_Output();
		$parsed_menu   = $output_module->get_parsed_menu( true );
		$existing_menu = $wp_admin_bar->get_nodes();

		// Remove all nodes.
		foreach ( $existing_menu as $node_id => $node ) {
			$wp_admin_bar->remove_node( $node_id );
		}

		// Convert $parsed_menu to array of nodes.
		$output_module->generate_nodes( $parsed_menu );

	}

}
