<?php
/**
 * Settings module.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Setting;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Base\Base_Module;
use ATSDash\Helpers\Multisite_Helper;

/**
 * Class to setup settings module.
 */
class Setting_Module extends Base_Module {
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

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/setting';

	}

	/**
	 * Setup settings module.
	 */
	public function setup() {

		$ms_helper = new Multisite_Helper();

		/**
		 * We can't simply use `$ms_helper->needs_to_switch_blog()` and remove the `$ms_helper->multisite_supported()`
		 * because it will also return false on non-multisite installation.
		 */
		if ( $ms_helper->multisite_supported() ) {
			global $blueprint;

			/**
			 * In this case, it's simpler to do manual checking instead of calling `$ms_helper->needs_to_switch_blog( false )`.
			 * And the `$blueprint` checking is necessary here (don't remove it).
			 * Because we're not only checking for non-blueprint sites, but also checking if blueprint is set.
			 */
			if ( $blueprint && get_current_blog_id() !== $blueprint ) {
				// Since multisite is supported and this is not a blueprint, then hide the data removal field.
				add_filter( 'ats_show_data_removal_field', '__return_false' );
			}
		}

		add_action( 'admin_init', array( $this, 'pro_setting_fields' ) );

		add_filter( 'ats_remove_3rd_party_widgets_field_path', array( $this, 'remove_3rd_party_widgets_field' ) );

		add_action( 'ats_after_widget_metabox', array( $this, 'dashboard_builder_metabox' ) );
		add_action( 'ats_after_page_builder_dashboard_metabox', array( $this, 'block_editor_template_metabox' ) );

	}

	/**
	 * Remove 3rd party widgets field.
	 *
	 * @param string $template The template path of the field.
	 * @return string The template path of the field.
	 */
	public function remove_3rd_party_widgets_field( $template ) {

		return __DIR__ . '/templates/fields/remove-3rd-party-widgets.php';

	}

	/**
	 * Add dashboard builder metabox after widget styling metabox.
	 */
	public function dashboard_builder_metabox() {
		add_settings_section( 'ats-builder-section', __( 'Page Builder Dashboard', 'ats-dashboard' ), '', 'ats-page-builder-dashboard-settings' );

		add_settings_field(
			'page-builder-template-headline',
			__( 'User Role', 'ats-dashboard' ),
			function () {
				echo '<strong>' . __( 'Saved Template', 'ats-dashboard' ) . '<strong> ';
			},
			'ats-page-builder-dashboard-settings',
			'ats-builder-section'
		);

		add_settings_field(
			'page-builder-template-all',
			__( 'All', 'ats-dashboard' ),
			function () {
				$this->page_builder_template_field( 'all' );
			},
			'ats-page-builder-dashboard-settings',
			'ats-builder-section'
		);

		$ms_helper = new Multisite_Helper();

		if ( $ms_helper->multisite_supported() ) {
			add_settings_field(
				'page-builder-template-super_admin',
				__( 'Super Admin', 'ats-dashboard' ),
				function () {
					$this->page_builder_template_field( 'super_admin' );
				},
				'ats-page-builder-dashboard-settings',
				'ats-builder-section'
			);
		}

		$wp_roles   = wp_roles();
		$role_names = $wp_roles->role_names;

		foreach ( $role_names as $role_key => $role_name ) {
			add_settings_field(
				'page-builder-template-' . $role_key,
				ucwords( $role_name ),
				function () use ( $role_key ) {
					$this->page_builder_template_field( $role_key );
				},
				'ats-page-builder-dashboard-settings',
				'ats-builder-section'
			);
		}
	}

	/**
	 * Page builder template field.
	 *
	 * @param string $role_key The role key.
	 */
	public function page_builder_template_field( $role_key ) {

		$field = require __DIR__ . '/templates/fields/page-builder.php';
		$field( $role_key );

	}

	/**
	 * Settings fields for pro version only.
	 */
	public function pro_setting_fields() {

		add_settings_field( 'column-settings', __( 'Dashboard Columns', 'ats-dashboard' ), array( $this, 'widget_columns_field' ), 'ats-general-settings', 'ats-general-section' );

		$ms_helper = new Multisite_Helper();

		// Widget order.
		if ( ! $ms_helper->is_network_active() ) {

			add_settings_field( 'widget-order', __( 'Order Widgets by', 'ats-dashboard' ), array( $this, 'widgets_order_field' ), 'ats-general-settings', 'ats-general-section' );

		}

	}

	/**
	 * Render the block editor template info metabox.
	 */
	public function block_editor_template_metabox() {

		require __DIR__ . '/templates/block-editor-template-metabox.php';

	}

	/**
	 * Widget columns field.
	 */
	public function widget_columns_field() {

		$field = require __DIR__ . '/templates/fields/widget-columns.php';
		$field();

	}

	/**
	 * Widgets order field.
	 */
	public function widgets_order_field() {

		$field = require __DIR__ . '/templates/fields/widgets-order.php';
		$field();

	}

}
