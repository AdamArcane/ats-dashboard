<?php
/**
 * Multisite's widgets output.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Multisite\Output;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Base\Base_Output;
use ATSDash\Helpers\Branding_Helper;
use ATSDash\Helpers\Multisite_Helper;

use ats\Widget\Widget_Base_Output as Free_Widget_Output;
use ATSDash\Widget\Widget_Output;

/**
 * Class to setup the module output.
 */
class Ms_Widget_Output extends Base_Output {

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
	 * The network url.
	 *
	 * @var string
	 */
	public $network_url;

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

		$this->network_url    = network_site_url();
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

		add_action( 'wp_dashboard_setup', array( self::get_instance(), 'remove_dashboard_widgets' ), 100 );
		add_action( 'wp_dashboard_setup', array( self::get_instance(), 'add_dashboard_widgets' ) );
		add_filter( 'ats_pro_dashboard_columns', array( self::get_instance(), 'dashboard_columns' ) );
		add_filter( 'ats_pro_dashboard_columns_layout', array( self::get_instance(), 'dashboard_columns_layout' ) );
		add_action( 'admin_init', array( self::get_instance(), 'update_widget_order' ), 15 );
		add_action( 'user_register', array( self::get_instance(), 'update_widget_order' ) );

		add_filter(
			'ats_tools_capability',
			function () {
				'manage_network'; // Only allow network admins to access the tools page.
			}
		);

		add_filter(
			'ats_modules_capability',
			function () {
				'manage_network'; // Only allow network admins to access the modules page.
			}
		);

		add_filter( 'ats_settings_capability', array( self::get_instance(), 'multisite_capability' ) );
		add_filter( 'ats_widgets_convert_placeholder_tags', array( self::get_instance(), 'convert_placeholder_tags' ) );

	}

	/**
	 * Remove dashboard widgets.
	 */
	public function remove_dashboard_widgets() {

		global $blueprint;

		// Stop here if blueprint is not defined.
		if ( empty( $blueprint ) ) {
			return;
		}

		$blog_id   = get_current_blog_id();

		if ( ! in_array( $blog_id, $this->ms_helper->get_excluded_sites(), true ) ) {

			switch_to_blog( $blueprint );

			$widgets_output     = Free_Widget_Output::get_instance();
			$pro_widgets_output = Widget_Output::get_instance();

			$widgets_output->remove_default_dashboard_widgets();
			$pro_widgets_output->remove_3rd_party_widgets();

			restore_current_blog();

		}

	}

	/**
	 * Add dashboard widgets.
	 */
	public function add_dashboard_widgets() {

		global $blueprint;

		// Stop here if blueprint is not defined.
		if ( empty( $blueprint ) ) {
			return;
		}

		$blog_id    = get_current_blog_id();
		$user_roles = wp_get_current_user()->roles;

		if ( ! in_array( $blog_id, $this->ms_helper->get_excluded_sites(), true ) ) {

			$widgets_output = Free_Widget_Output::get_instance();

			switch_to_blog( $blueprint );
			$widgets_output->add_dashboard_widgets( $user_roles );
			restore_current_blog();

		}

	}

	/**
	 * Dashboard columns.
	 *
	 * @param array $columns The dashboard columns.
	 *
	 * @return array The updated dashboard columns.
	 */
	public function dashboard_columns( $columns ) {

		global $blueprint;

		// Stop here if we're on the blueprint or if it's not defined.
		if ( empty( $blueprint ) || get_current_blog_id() === $blueprint ) {
			return $columns;
		}

		switch_to_blog( $blueprint );

		$settings = get_option( 'ats_settings' );

		if ( ! isset( $settings['dashboard_columns'] ) ) {
			$columns['dashboard'] = 4;
		} else {
			$columns['dashboard'] = $settings['dashboard_columns'];
		}

		restore_current_blog();

		return $columns;

	}

	/**
	 * Dashboard columns layout.
	 *
	 * @param array $columns The dashboard columns.
	 *
	 * @return array The updated dashboard columns.
	 */
	public function dashboard_columns_layout( $columns ) {

		global $blueprint;

		// Stop here if we're on the blueprint or if it's not defined.
		if ( empty( $blueprint ) || get_current_blog_id() === $blueprint ) {
			return $columns;
		}

		switch_to_blog( $blueprint );

		$settings = get_option( 'ats_settings' );

		if ( ! isset( $settings['dashboard_columns'] ) ) {
			$columns = 4;
		} else {
			$columns = $settings['dashboard_columns'];
		}

		restore_current_blog();

		return $columns;

	}

	/**
	 * Update widget order.
	 *
	 * @param int $user_id The user ID (when new user registered).
	 */
	public function update_widget_order( $user_id = 0 ) {

		$widget_helper     = new \ATSDash\Helpers\Widget_Helper();
		$widget_order_user = $widget_helper->get_widget_order_user( true );
		$order_option_meta = get_option( 'ats_pro_widget_order' );

		// Stop here if global widget order is not defined.
		if ( ! $widget_order_user ) {
			return;
		}

		$order_key       = 'meta-box-order_dashboard';
		$order_user_meta = get_user_meta( $widget_order_user, $order_key, true );

		if ( ! empty( $user_id ) ) {
			// If executed in "user_register" hook, update the new user's meta.
			update_user_meta( $user_id, $order_key, $order_user_meta );
		}

		// Stop if order hasn't changed.
		if ( $order_user_meta === $order_option_meta ) {
			return;
		}

		$blogusers = get_users();

		foreach ( $blogusers as $user ) {

			// Check if they're not the selected user.
			if ( $user->ID !== $widget_order_user ) {
				update_user_meta( $user->ID, $order_key, $order_user_meta );
			}
		}

		update_option( 'ats_pro_widget_order', $order_user_meta );

	}

	/**
	 * Multisite capability.
	 */
	public function multisite_capability() {

		$ats_capability = get_site_option( 'ats_multisite_capability' ) ? get_site_option( 'ats_multisite_capability' ) : 'manage_network';

		return $ats_capability;

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
			'{network_url}',
			'{blueprint_url}',
			'{blueprint_name}',
		];

		$replacement = [
			$this->network_url,
			$this->blueprint_url,
			$this->blueprint_name,
		];

		$str = str_replace($find, $replacement, $str);

		return $str;

	}

}
