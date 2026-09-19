<?php
/**
 * Tool module.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Tool;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Base\Base_Module;

/**
 * Class to setup tools module.
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
	 * Setup tools module.
	 */
	public function setup() {

		add_action( 'ats_export_fields', array( self::get_instance(), 'add_export_fields' ) );
		add_filter( 'ats_export', array( self::get_instance(), 'add_export_data' ) );

		add_action( 'ats_import_settings', array( self::get_instance(), 'import_settings' ) );
		add_action( 'ats_import', array( self::get_instance(), 'import_admin_menu' ) );
		add_action( 'ats_import', array( self::get_instance(), 'import_admin_bar' ) );

	}

	/**
	 * Add PRO only export fields.
	 */
	public function add_export_fields() {

		?>

		<p>
			<label>
				<input type="checkbox" name="ats_export_modules[]" class="ats-module-checkbox" value="admin_menu" checked />
				<?php _e( 'Admin Menu Editor Settings', 'ats-dashboard' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input type="checkbox" name="ats_export_modules[]" class="ats-module-checkbox" value="admin_bar" checked />
				<?php _e( 'Admin Bar Editor Settings', 'ats-dashboard' ); ?>
			</label>
		</p>

		<?php

	}

	/**
	 * Add extra export data.
	 *
	 * @param array $data The existing export data.
	 * @return array The merged export data.
	 */
	public function add_export_data( $data ) {

		$process    = require __DIR__ . '/inc/process-export.php';
		$extra_data = $process( $this );

		return array_merge( $data, $extra_data );

	}

	/**
	 * Import extra settings.
	 *
	 * @param array $data The existing import data.
	 */
	public function import_settings( $data ) {

		$multisite_settings = isset( $data['multisite_settings'] ) ? $data['multisite_settings'] : array();

		if ( $multisite_settings ) {

			// Check if multisite is enabled regardless is_plugin_active_for_network status.
			if ( is_multisite() && ! empty( $multisite_settings ) ) {
				foreach ( $multisite_settings as $key => $value ) {
					update_site_option( $key, $value );
				}
			}
		}

	}

	/**
	 * Import admin menu.
	 *
	 * @param array $data The existing import data.
	 */
	public function import_admin_menu( $data ) {

		$admin_menu = isset( $data['admin_menu'] ) ? $data['admin_menu'] : array();

		if ( $admin_menu ) {
			$admin_menu = $this->replace_admin_menu_urls( $admin_menu, '{ats_site_url}', site_url() );

			update_option( 'ats_admin_menu', $admin_menu );

			add_settings_error(
				'ats_export',
				esc_attr( 'ats-import' ),
				__( 'Admin menu imported', 'ats-dashboard' ),
				'updated'
			);
		}

	}

	/**
	 * Replace admin menu's placeholders with actual values or vice-versa.
	 *
	 * @param array  $admin_menu The admin menu array.
	 * @param string $find The string to replace.
	 * @param string $replace The replacement string.
	 *
	 * @return array The admin menu array with manipulated urls.
	 */
	public function replace_admin_menu_urls( $admin_menu, $find, $replace ) {

		foreach ( $admin_menu as $role => $menu_items ) {
			if ( ! empty( $menu_items ) && is_array( $menu_items ) ) {
				foreach ( $menu_items as $menu_item_index => $menu_item ) {
					$menu_item_url = isset( $menu_item['url'] ) ? $menu_item['url'] : '';

					if ( ! empty( $menu_item_url ) ) {
						if ( 0 === stripos( $menu_item_url, $find ) ) {
							$menu_item_url = str_ireplace( $find, $replace, $menu_item_url );

							$admin_menu[ $role ][ $menu_item_index ]['url'] = $menu_item_url;
						}
					}

					$menu_item_url_default = isset( $menu_item['url_default'] ) ? $menu_item['url_default'] : '';

					if ( ! empty( $menu_item_url_default ) ) {
						if ( 0 === stripos( $menu_item_url_default, $find ) ) {
							$menu_item_url_default = str_ireplace( $find, $replace, $menu_item_url_default );

							$admin_menu[ $role ][ $menu_item_index ]['url_default'] = $menu_item_url_default;
						}
					}

					$submenu = isset( $menu_item['submenu'] ) ? $menu_item['submenu'] : array();

					if ( ! empty( $submenu ) ) {
						foreach ( $submenu as $submenu_index => $submenu_item ) {
							$submenu_item_url = isset( $submenu_item['url'] ) ? $submenu_item['url'] : '';

							if ( ! empty( $submenu_item_url ) ) {
								if ( 0 === stripos( $submenu_item_url, $find ) ) {
									$submenu_item_url = str_ireplace( $find, $replace, $submenu_item_url );

									$admin_menu[ $role ][ $menu_item_index ]['submenu'][ $submenu_index ]['url'] = $submenu_item_url;
								}
							}

							$submenu_item_url_default = isset( $submenu_item['url_default'] ) ? $submenu_item['url_default'] : '';

							if ( ! empty( $submenu_item_url_default ) ) {
								if ( 0 === stripos( $submenu_item_url_default, $find ) ) {
									$submenu_item_url_default = str_ireplace( $find, $replace, $submenu_item_url_default );

									$admin_menu[ $role ][ $menu_item_index ]['submenu'][ $submenu_index ]['url_default'] = $submenu_item_url_default;
								}
							}
						}
					}
				}
			}
		}

		return $admin_menu;

	}

	/**
	 * Import admin bar.
	 *
	 * @param array $data The existing import data.
	 */
	public function import_admin_bar( $data ) {

		$admin_bar = isset( $data['admin_bar'] ) ? $data['admin_bar'] : array();

		if ( $admin_bar ) {
			$admin_bar = $this->replace_admin_bar_urls( $admin_bar, '{ats_site_url}', site_url() );

			update_option( 'ats_admin_bar', $admin_bar );

			add_settings_error(
				'ats_export',
				esc_attr( 'ats-import' ),
				__( 'Admin bar imported', 'ats-dashboard' ),
				'updated'
			);
		}

	}

	/**
	 * Replace admin bar's placeholders with actual values or vice-versa.
	 *
	 * @param array  $admin_bar The admin menu array.
	 * @param string $find The string to replace.
	 * @param string $replace The replacement string.
	 *
	 * @return array The admin bar array with manipulated urls.
	 */
	public function replace_admin_bar_urls( $admin_bar, $find, $replace ) {

		foreach ( $admin_bar as $menu_slug => $menu_data ) {
			$href = isset( $menu_data['href'] ) ? $menu_data['href'] : '';

			if ( ! empty( $href ) ) {
				if ( 0 === stripos( $href, $find ) ) {
					$href = str_ireplace( $find, $replace, $href );

					$admin_bar[ $menu_slug ]['href'] = $href;
				}
			}

			$href_default = isset( $menu_data['href_default'] ) ? $menu_data['href_default'] : '';

			if ( ! empty( $href_default ) ) {
				if ( 0 === stripos( $href_default, $find ) ) {
					$href_default = str_ireplace( $find, $replace, $href_default );

					$admin_bar[ $menu_slug ]['href_default'] = $href_default;
				}
			}
		}

		return $admin_bar;

	}

}
