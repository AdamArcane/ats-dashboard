<?php
/**
 * Tool module.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Tool;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Base\Base_Module;
use ATSDash\Setup;

/**
 * Class to setup tool module.
 */
class Tool_Module extends Base_Module {

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

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/tool';

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
	 * Setup tool module.
	 */
	public function setup() {
		add_filter( 'option_page_capability_ats-import-group', array( $this, 'tools_capability' ) );
		add_filter( 'option_page_capability_ats-reset-group', array( $this, 'tools_capability' ) );
		add_filter( 'option_page_capability_ats-export-group', array( $this, 'tools_capability' ) );

		/**
		 * These 4 actions will be removed on multisite if current site is not a blueprint.
		 */
		add_action( 'admin_menu', array( self::get_instance(), 'submenu_page' ), 20 );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_scripts' ) );
		add_action( 'admin_init', array( self::get_instance(), 'add_settings' ) );
			add_action( 'ats_export_fields', array( self::get_instance(), 'add_export_fields' ) );
			add_filter( 'ats_export', array( self::get_instance(), 'add_export_data' ) );
			add_action( 'ats_import_settings', array( self::get_instance(), 'import_settings' ) );
			add_action( 'ats_import', array( self::get_instance(), 'import_admin_menu' ) );
			add_action( 'ats_import', array( self::get_instance(), 'import_admin_bar' ) );

	}

	/**
	 * Add submenu page.
	 */
	public function submenu_page() {

		add_submenu_page( 'ats_settings', __( 'Tools', 'ats-dashboard' ), __( 'Tools', 'ats-dashboard' ), $this->tools_capability(), 'ats_tools', array( $this, 'submenu_page_content' ) );

	}

	/**
	 * Submenu page content.
	 */
	public function submenu_page_content() {

		$template = require __DIR__ . '/templates/tools-template.php';
		$template();

	}

	/**
	 * Enqueue admin styles.
	 */
	public function admin_styles() {

		$enqueue = require __DIR__ . '/inc/css-enqueue.php';
		$enqueue( $this );

	}

	/**
	 * Enqueue admin scripts.
	 */
	public function admin_scripts() {

		$enqueue = require __DIR__ . '/inc/js-enqueue.php';
		$enqueue( $this );

	}

	/**
	 * Add settings.
	 */
	public function add_settings() {

		// Settings groups.
		register_setting( 'ats-export-group', 'ats_export', array( 'sanitize_callback' => array( $this, 'process_export' ) ) );
		register_setting( 'ats-import-group', 'ats_import', array( 'sanitize_callback' => array( $this, 'process_import' ) ) );
		register_setting( 'ats-reset-group', 'ats_reset', array( 'sanitize_callback' => array( $this, 'process_reset' ) ) );

		// Settings sections.
		add_settings_section( 'ats-export-section', __( 'Export', 'ats-dashboard' ), '', 'ats-dashboard-export' );
		add_settings_section( 'ats-import-section', __( 'Import', 'ats-dashboard' ), '', 'ats-dashboard-import' );
		add_settings_section( 'ats-reset-section', __( 'Reset', 'ats-dashboard' ), '', 'ats-dashboard-reset' );

		// Settings fields.
		add_settings_field( 'ats-export-field', '', array( $this, 'render_export_field' ), 'ats-dashboard-export', 'ats-export-section', array( 'class' => 'is-gapless has-small-text' ) );
		add_settings_field( 'ats-import-field', '', array( $this, 'render_import_field' ), 'ats-dashboard-import', 'ats-import-section', array( 'class' => 'is-gapless has-small-text' ) );
		add_settings_field( 'ats-reset-field', '', array( $this, 'render_reset_field' ), 'ats-dashboard-reset', 'ats-reset-section', array( 'class' => 'is-gapless has-small-text' ) );

	}

	/**
	 * Render export field.
	 *
	 * @param array $args The setting's arguments.
	 */
	public function render_export_field( $args ) {

		$field = require __DIR__ . '/templates/fields/export-field.php';
		$field();

	}

	/**
	 * Render import field.
	 *
	 * @param array $args The setting's arguments.
	 */
	public function render_import_field( $args ) {

		$field = require __DIR__ . '/templates/fields/import-field.php';
		$field();

	}

	/**
	 * Render reset field.
	 *
	 * @param array $args The setting's arguments.
	 */
	public function render_reset_field( $args ) {

		$field = require __DIR__ . '/templates/fields/reset-field.php';
		$field();

	}

	/**
	 * Process the export.
	 */
	public function process_export() {
		$this->authorize_transfer( 'ats-export-group' );

		$process = require __DIR__ . '/inc/process-export.php';
		$process();

	}

	/**
	 * Process the import.
	 */
	public function process_import() {
		$this->authorize_transfer( 'ats-import-group' );

		$process = require __DIR__ . '/inc/process-import.php';
		$process();

	}

	/**
	 * Reset all ATS Dashboard settings on this site back to their defaults.
	 */
	public function process_reset() {
		$this->authorize_transfer( 'ats-reset-group' );

		if ( empty( $_POST['ats_reset_confirm'] ) ) {
			add_settings_error( 'ats_export', esc_attr( 'ats-reset' ), __( 'Please confirm you want to reset all settings.', 'ats-dashboard' ) );
			return;
		}

		Setup::get_instance()->delete_ats_data();

		add_settings_error( 'ats_export', esc_attr( 'ats-reset' ), __( 'All ATS Dashboard settings have been reset to their defaults.', 'ats-dashboard' ), 'updated' );

	}

	/** The Settings API and handlers must use the same permission as Tools. */
	public function tools_capability() {
		return is_multisite() ? 'manage_network' : apply_filters( 'ats_tools_capability', 'manage_options' );
	}

	/** Authorize even when a transfer callback is invoked outside options.php. */
	private function authorize_transfer( $group ) {
		if ( ! current_user_can( $this->tools_capability() ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'ats-dashboard' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( $group . '-options' );
	}

		/**
		 * Add Admin Menu and Admin Bar export fields.
		 */
		public function add_export_fields() {
			?>
			<p>
				<label>
					<input type="checkbox" name="ats_export_modules[]" class="ats-module-checkbox" value="admin_menu" checked />
					<?php esc_html_e( 'Admin Menu Editor Settings', 'ats-dashboard' ); ?>
				</label>
			</p>
			<p>
				<label>
					<input type="checkbox" name="ats_export_modules[]" class="ats-module-checkbox" value="admin_bar" checked />
					<?php esc_html_e( 'Admin Bar Editor Settings', 'ats-dashboard' ); ?>
				</label>
			</p>
			<?php
		}

		/**
		 * Add legacy module data to the core export payload.
		 *
		 * @param array $data Existing export data.
		 * @return array Merged export data.
		 */
		public function add_export_data( $data ) {
			$process    = require __DIR__ . '/inc/process-export-legacy.php';
			$extra_data = $process( $this );

			return array_merge( $data, $extra_data );
		}

		/**
		 * Import multisite settings.
		 *
		 * @param array $data Imported data.
		 */
		public function import_settings( $data ) {
			$multisite_settings = isset( $data['multisite_settings'] ) ? $data['multisite_settings'] : array();

			if ( ! is_multisite() || ! current_user_can( 'manage_network_options' ) || ! is_array( $multisite_settings ) ) {
				return;
			}

			foreach ( array( 'ats_multisite_blueprint', 'ats_multisite_widget_order' ) as $key ) {
				if ( isset( $multisite_settings[ $key ] ) && is_scalar( $multisite_settings[ $key ] ) ) {
					update_site_option( $key, absint( $multisite_settings[ $key ] ) );
				}
			}
			if ( isset( $multisite_settings['ats_multisite_exclude'] ) && is_string( $multisite_settings['ats_multisite_exclude'] ) ) {
				$ids = array_filter( array_map( 'absint', explode( ',', $multisite_settings['ats_multisite_exclude'] ) ) );
				update_site_option( 'ats_multisite_exclude', implode( ',', array_unique( $ids ) ) );
			}
			if ( isset( $multisite_settings['ats_multisite_capability'] ) && in_array( $multisite_settings['ats_multisite_capability'], array( 'manage_network', 'manage_options' ), true ) ) {
				update_site_option( 'ats_multisite_capability', $multisite_settings['ats_multisite_capability'] );
			}
		}

		/**
		 * Import Admin Menu settings.
		 *
		 * @param array $data Imported data.
		 */
		public function import_admin_menu( $data ) {
			$admin_menu = isset( $data['admin_menu'] ) ? $data['admin_menu'] : array();

			if ( $admin_menu ) {
				update_option( 'ats_admin_menu', $this->replace_admin_menu_urls( $admin_menu, '{ats_site_url}', site_url() ) );
				add_settings_error( 'ats_export', esc_attr( 'ats-import' ), __( 'Admin menu imported', 'ats-dashboard' ), 'updated' );
			}
		}

		/**
		 * Import Admin Bar settings.
		 *
		 * @param array $data Imported data.
		 */
		public function import_admin_bar( $data ) {
			$admin_bar = isset( $data['admin_bar'] ) ? $data['admin_bar'] : array();

			if ( $admin_bar ) {
				update_option( 'ats_admin_bar', $this->replace_admin_bar_urls( $admin_bar, '{ats_site_url}', site_url() ) );
				add_settings_error( 'ats_export', esc_attr( 'ats-import' ), __( 'Admin bar imported', 'ats-dashboard' ), 'updated' );
			}
		}

		/**
		 * Replace site URL placeholders in Admin Menu data.
		 *
		 * @param array  $admin_menu Menu data.
		 * @param string $find Value to replace.
		 * @param string $replace Replacement value.
		 * @return array Updated menu data.
		 */
		public function replace_admin_menu_urls( $admin_menu, $find, $replace ) {
			/**
			 * There's a single flat menu list now (no more per-role/per-user copies -
			 * see Admin_Menu_Output::resolve_item_visibility()), so this operates
			 * directly on $admin_menu[$index] rather than $admin_menu[$role][$index].
			 */
			foreach ( (array) $admin_menu as $menu_item_index => $menu_item ) {
				if ( ! is_array( $menu_item ) ) {
					continue;
				}

				foreach ( array( 'url', 'url_default' ) as $url_key ) {
					if ( ! empty( $menu_item[ $url_key ] ) && 0 === stripos( $menu_item[ $url_key ], $find ) ) {
						$admin_menu[ $menu_item_index ][ $url_key ] = str_ireplace( $find, $replace, $menu_item[ $url_key ] );
					}
				}

				foreach ( (array) ( $menu_item['submenu'] ?? array() ) as $submenu_index => $submenu_item ) {
					if ( ! is_array( $submenu_item ) ) {
						continue;
					}

					foreach ( array( 'url', 'url_default' ) as $url_key ) {
						if ( ! empty( $submenu_item[ $url_key ] ) && 0 === stripos( $submenu_item[ $url_key ], $find ) ) {
							$admin_menu[ $menu_item_index ]['submenu'][ $submenu_index ][ $url_key ] = str_ireplace( $find, $replace, $submenu_item[ $url_key ] );
						}
					}
				}
			}

			return $admin_menu;
		}

		/**
		 * Replace site URL placeholders in Admin Bar data.
		 *
		 * @param array  $admin_bar Admin Bar data.
		 * @param string $find Value to replace.
		 * @param string $replace Replacement value.
		 * @return array Updated Admin Bar data.
		 */
		public function replace_admin_bar_urls( $admin_bar, $find, $replace ) {
			foreach ( $admin_bar as $menu_slug => $menu_data ) {
				foreach ( array( 'href', 'href_default' ) as $url_key ) {
					if ( ! empty( $menu_data[ $url_key ] ) && 0 === stripos( $menu_data[ $url_key ], $find ) ) {
						$admin_bar[ $menu_slug ][ $url_key ] = str_ireplace( $find, $replace, $menu_data[ $url_key ] );
					}
				}
			}

			return $admin_bar;
		}

}
