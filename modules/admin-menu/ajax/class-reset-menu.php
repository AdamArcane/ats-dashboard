<?php
/**
 * Reset menu.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\AdminMenu\Ajax;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Class to handle ajax request to reset admin menu.
 */
class Reset_Menu {

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
	 * Reset menu.
	 *
	 * Supports three scopes, from broadest to narrowest:
	 * - role=all: wipes every role/user/Default menu entirely.
	 * - role=<role or user_id_X>: reverts that whole role/user back to
	 *   inheriting Default (the pre-existing behavior - just deletes its delta).
	 * - role=<role or user_id_X> + item_type/item_key (+ submenu_key): reverts
	 *   just one item (or one submenu item within it) back to its inherited
	 *   value, leaving the rest of that role/user's overrides intact.
	 */
	public function reset() {
		$nonce       = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		$role        = isset( $_POST['role'] ) ? sanitize_text_field( wp_unslash( $_POST['role'] ) ) : '';
		$item_type   = isset( $_POST['item_type'] ) ? sanitize_text_field( wp_unslash( $_POST['item_type'] ) ) : '';
		$item_key    = isset( $_POST['item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['item_key'] ) ) : '';
		$submenu_key = isset( $_POST['submenu_key'] ) ? sanitize_text_field( wp_unslash( $_POST['submenu_key'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'ats_admin_menu_reset_menu' ) ) {
			wp_send_json_error( __( 'Invalid token', 'ats-dashboard' ) );
		}

		$capability = apply_filters( 'ats_settings_capability', 'manage_options' );

		if ( ! current_user_can( $capability ) ) {
			wp_send_json_error( __( 'You do not have permission to perform this action', 'ats-dashboard' ) );
		}

		if ( ! $role ) {
			wp_send_json_error( __( 'Role is not specified', 'ats-dashboard' ) );
		}

		if ( 'all' === $role ) {
			delete_option( 'ats_admin_menu' );
			wp_send_json_success( esc_attr( $role ) . ' ' . __( 'Menu reset successfully', 'ats-dashboard' ) );
		}

		$menu = get_option( 'ats_admin_menu', array() );
		$menu = is_array( $menu ) ? $menu : array();

		if ( '' === $item_key ) {
			// Whole role/user - revert to fully inheriting Default.
			if ( isset( $menu[ $role ] ) ) {
				unset( $menu[ $role ] );
				update_option( 'ats_admin_menu', $menu );
			}

			wp_send_json_success( esc_attr( $role ) . ' ' . __( 'Menu reset successfully', 'ats-dashboard' ) );
		}

		// Single item (or single submenu item) - revert just that one thing.
		$delta = ! empty( $menu[ $role ] ) && is_array( $menu[ $role ] ) ? $menu[ $role ] : array();
		$delta = $this->remove_from_delta( $delta, $item_type, $item_key, $submenu_key );

		if ( empty( $delta ) ) {
			unset( $menu[ $role ] );
		} else {
			$menu[ $role ] = $delta;
		}

		update_option( 'ats_admin_menu', $menu );

		wp_send_json_success( __( 'Reverted to Default', 'ats-dashboard' ) );
	}

	/**
	 * Remove one item's (or one of its submenu item's) override from a role/user delta.
	 *
	 * @param array  $delta The role/user's stored delta (see Menu_Inheritance_Helper).
	 * @param string $item_type The top level item's type ("menu" or "separator").
	 * @param string $item_key The top level item's identity (id_default for "menu", url_default for "separator").
	 * @param string $submenu_key Optional. A submenu item's identity (its url_default) to revert instead of the whole item.
	 *
	 * @return array The updated delta.
	 */
	private function remove_from_delta( $delta, $item_type, $item_key, $submenu_key ) {

		$result           = array();
		$identity_field   = 'separator' === $item_type ? 'url_default' : 'id_default';

		foreach ( (array) $delta as $entry ) {
			if ( ! is_array( $entry ) || empty( $entry ) ) {
				continue;
			}

			$entry_key = isset( $entry[ $identity_field ] ) ? (string) $entry[ $identity_field ] : '';

			if ( $entry_key !== (string) $item_key ) {
				$result[] = $entry;
				continue;
			}

			// Matched the target top level item.
			if ( '' === $submenu_key ) {
				// Revert the whole item - drop it from the delta entirely.
				continue;
			}

			// Revert only one submenu item within this entry, keep the rest.
			if ( empty( $entry['submenu'] ) || ! is_array( $entry['submenu'] ) ) {
				$result[] = $entry;
				continue;
			}

			$new_submenu = array();

			foreach ( $entry['submenu'] as $submenu_entry ) {
				if ( ! is_array( $submenu_entry ) || empty( $submenu_entry ) ) {
					continue;
				}

				if ( $this->submenu_key_matches( $submenu_entry, $submenu_key ) ) {
					continue;
				}

				$new_submenu[] = $submenu_entry;
			}

			if ( empty( $new_submenu ) ) {
				unset( $entry['submenu'] );
			} else {
				$entry['submenu'] = $new_submenu;
			}

			// If the parent entry has nothing left besides identity/type, drop it too.
			$meaningful_keys = array_diff( array_keys( $entry ), array( 'type', 'id_default', 'url_default', 'removed' ) );

			if ( empty( $meaningful_keys ) ) {
				continue;
			}

			$result[] = $entry;
		}

		return $result;

	}

	/**
	 * Whether a submenu delta entry matches the requested submenu identity,
	 * tolerating the & vs &amp; mismatch that real submenu URLs can have.
	 *
	 * @param array  $submenu_entry The submenu delta entry.
	 * @param string $submenu_key The requested submenu identity (url_default).
	 *
	 * @return bool
	 */
	private function submenu_key_matches( $submenu_entry, $submenu_key ) {

		$entry_key = isset( $submenu_entry['url_default'] ) ? (string) $submenu_entry['url_default'] : '';

		if ( $entry_key === (string) $submenu_key ) {
			return true;
		}

		if ( false !== stripos( $submenu_key, '&' ) && false === stripos( $submenu_key, '&amp;' ) ) {
			return $entry_key === str_ireplace( '&', '&amp;', $submenu_key );
		}

		return false;

	}

}
