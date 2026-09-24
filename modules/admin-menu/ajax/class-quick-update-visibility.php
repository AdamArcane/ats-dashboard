<?php
/**
 * Quick-update a single item's visibility, for the eyeball icon's quick-select
 * popover (bypasses the main "Save Changes" flow entirely).
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\AdminMenu\Ajax;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Class to handle ajax request to quick-update one item's visibility.
 */
class Quick_Update_Visibility {

	/**
	 * The class instance.
	 *
	 * @var object
	 */
	public static $instance;

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
	 * Patch one menu/submenu item's is_hidden field in the saved menu, without
	 * touching anything else - including fields the user may be mid-editing
	 * elsewhere in the form right now.
	 */
	public function update() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'ats_admin_menu_quick_update_visibility' ) ) {
			wp_send_json_error( __( 'Invalid token', 'ats-dashboard' ) );
		}

		$capability = apply_filters( 'ats_settings_capability', 'manage_options' );

		if ( ! current_user_can( $capability ) ) {
			wp_send_json_error( __( 'You do not have permission to perform this action', 'ats-dashboard' ) );
		}

		$item_key    = isset( $_POST['item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['item_key'] ) ) : '';
		$submenu_key = isset( $_POST['submenu_key'] ) ? sanitize_text_field( wp_unslash( $_POST['submenu_key'] ) ) : '';
		$is_hidden   = isset( $_POST['is_hidden'] ) ? sanitize_text_field( wp_unslash( $_POST['is_hidden'] ) ) : '0';

		if ( ! in_array( $is_hidden, array( '0', '1', '2' ), true ) ) {
			$is_hidden = '0';
		}

		if ( '' === $item_key ) {
			wp_send_json_error( __( 'Missing menu item identity', 'ats-dashboard' ) );
		}

		require_once __DIR__ . '/class-get-menu.php';

		/**
		 * @see \ATSDash\AdminMenu\Admin_Menu_Output::remove_output_actions()
		 */
		do_action( 'ats_ajax_before_get_admin_menu' );

		$get_menu = new Get_Menu();
		$get_menu->load_menu();

		global $menu, $submenu;

		$merged_default_menu    = $get_menu->merge_default_menu_submenu( $menu, $submenu );
		$formatted_default_menu = $get_menu->format_merged_default_menu( $merged_default_menu );

		$saved_menu = get_option( 'ats_admin_menu', array() );
		$saved_menu = is_array( $saved_menu ) ? $saved_menu : array();

		/**
		 * Make sure every live default item (and submenu item) has a
		 * corresponding saved entry to patch - a not-yet-customized item has
		 * no entry yet, same as the builder itself resolves on load.
		 *
		 * @see Get_Menu::parse_response_with_custom_menu()
		 */
		$saved_menu = $get_menu->get_new_default_menu_items( $formatted_default_menu, $saved_menu );

		$updated = false;

		foreach ( $saved_menu as $index => $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$identity_field = 'separator' === ( $item['type'] ?? 'menu' ) ? 'url_default' : 'id_default';
			$value          = isset( $item[ $identity_field ] ) ? (string) $item[ $identity_field ] : '';

			if ( $value !== $item_key ) {
				continue;
			}

			if ( '' !== $submenu_key ) {
				if ( empty( $item['submenu'] ) || ! is_array( $item['submenu'] ) ) {
					break;
				}

				foreach ( $item['submenu'] as $submenu_index => $submenu_item ) {
					if ( ! is_array( $submenu_item ) ) {
						continue;
					}

					$submenu_value = isset( $submenu_item['url_default'] ) ? (string) $submenu_item['url_default'] : '';

					if ( $submenu_value === $submenu_key ) {
						$saved_menu[ $index ]['submenu'][ $submenu_index ]['is_hidden'] = $is_hidden;
						$updated = true;
						break 2;
					}
				}
			} else {
				$saved_menu[ $index ]['is_hidden'] = $is_hidden;
				$updated = true;
			}

			break;
		}

		if ( ! $updated ) {
			wp_send_json_error( __( 'Could not find that menu item to update', 'ats-dashboard' ) );
		}

		update_option( 'ats_admin_menu', $saved_menu );

		wp_send_json_success( __( 'Visibility updated', 'ats-dashboard' ) );

	}

}
