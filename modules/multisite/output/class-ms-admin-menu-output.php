<?php
/**
 * Multisite's admin menu output.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Multisite\Output;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Base\Base_Output;
use ats\Helpers\User_Helper;
use ats\AdminMenu\Admin_Menu_Module as Free_Admin_Menu_Module;

use ATSDash\Helpers\Multisite_Helper;
use ATSDash\AdminMenu\Admin_Menu_Output;

/**
 * Class to setup the module output.
 */
class Ms_Admin_Menu_Output extends Base_Output {

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
	 * The multisite helper.
	 *
	 * @var Multisite_Helper
	 */
	public $ms_helper;

	/**
	 * The blueprint site name.
	 *
	 * @var string
	 */
	public $blueprint_name;

	/**
	 * The blueprint site url.
	 *
	 * @var string
	 */
	public $blueprint_url;

	/**
	 * Module constructor.
	 */
	public function __construct() {

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/multisite';

		$this->ms_helper = new Multisite_Helper();

		$this->blueprint_name = get_bloginfo('name');
		$this->blueprint_url  = get_site_url( null );

		if ( $this->ms_helper->needs_to_switch_blog( false ) ) {
			global $blueprint;

			switch_to_blog( $blueprint );

			// These vars needs to be defined here to prevent multiple repeating process of getting the site name and url.
			$this->blueprint_name = get_bloginfo('name');
			$this->blueprint_url  = get_site_url( $blueprint );

			restore_current_blog();
		}

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

		add_action( 'admin_menu', array( self::get_instance(), 'menu_output' ), 10000 );
		add_action( 'ats_ajax_before_get_admin_menu', array( self::get_instance(), 'remove_output_actions' ) );

		remove_action( 'ats_ajax_get_admin_menu', array( Free_Admin_Menu_Module::get_instance(), 'get_admin_menu' ), 15, 2 );
		add_action( 'ats_ajax_get_admin_menu', array( self::get_instance(), 'get_admin_menu' ), 20, 2 );

		add_filter( 'ats_admin_menu_convert_placeholder_tags', array( self::get_instance(), 'convert_placeholder_tags' ) );

	}

	/**
	 * Remove multisite output from the ajax process of getting admin menu.
	 * See modules/admin-menu/ajax/class-get-menu.php in the free version.
	 */
	public function remove_output_actions() {

		// We need to remove admin menu's multisite output to get the original $menu & $submenu.
		remove_action( 'admin_menu', array( self::get_instance(), 'menu_output' ), 10000 );

	}

	/**
	 * Preparing the admin menu output.
	 */
	public function menu_output() {

		$ms_helper = new Multisite_Helper();

		if ( ! $ms_helper->needs_to_switch_blog( false ) ) {
			return;
		}

		global $blueprint;

		// Get data from current blog.
		$admin_menu = get_option( 'ats_admin_menu', array() );
		$roles      = wp_get_current_user()->roles;

		$admin_menu_output = Admin_Menu_Output::get_instance();

		// If current blog has the data, use it.
		if ( ! empty( $admin_menu ) ) {
			$admin_menu_output->menu_output( $roles );
			return;
		}

		// Otherwise, switch blog.
		switch_to_blog( $blueprint );
		$admin_menu_output->menu_output( $roles );
		restore_current_blog();

	}

	/**
	 * Get admin menu via ajax.
	 * This action will be called in "ajax" method in "class-get-menu.php" in the free version.
	 *
	 * @param object $ajax_handler The ajax handler class from the free version.
	 * @param string $role The role target to simulate.
	 */
	public function get_admin_menu( $ajax_handler, $role ) {

		$ms_helper   = new Multisite_Helper();
		$switch_blog = $ms_helper->needs_to_switch_blog( false );
		$admin_menu  = get_option( 'ats_admin_menu', array() );

		if ( $switch_blog && empty( $admin_menu ) ) {
			global $blueprint;
			switch_to_blog( $blueprint );
		}

		$roles = wp_get_current_user()->roles;
		$roles = ! $roles || ! is_array( $roles ) ? array() : $roles;

		$is_super_admin = is_super_admin();
		$simulate_role  = in_array( $role, $roles, true ) ? false : true;

		$user_helper = new User_Helper();

		// If current user role is different with the targetted role.
		if ( $simulate_role ) {
			$user_helper->simulate_role( $role );

			/**
			 * If the original access before simulating role is super admin,
			 * then we need to make it to be not super admin.
			 * Because otherwise, any role (including subscriber) will have a complete menu.
			 */
			if ( $is_super_admin ) {
				$user_helper->change_current_username();
			}
		}

		$ajax_handler->load_menu();

		if ( $simulate_role && $is_super_admin ) {
			$user_helper->restore_current_username();
		}

		$response = $ajax_handler->format_response( $role );

		if ( $switch_blog && empty( $admin_menu ) ) {
			restore_current_blog();
		}

		wp_send_json_success( $response );

	}

	/**
	 * Convert placeholder tags with their values.
	 *
	 * @param string $str The string to replace the tags in.
	 * @return string The modified string.
	 */
	public function convert_placeholder_tags( $str )
	{

		$find = [
			'{blueprint_url}',
			'{blueprint_name}',
		];

		$replacement = [
			$this->blueprint_url,
			$this->blueprint_name,
		];

		$str = str_replace($find, $replacement, $str);

		return $str;

	}

}
