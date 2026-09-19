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
	 */
	public function reset() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		$role  = isset( $_POST['role'] ) ? sanitize_text_field( wp_unslash( $_POST['role'] ) ) : '';

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
		} else {
			$menu = get_option( 'ats_admin_menu', array() );

			if ( isset( $menu[ $role ] ) ) {
				unset( $menu[ $role ] );
				update_option( 'ats_admin_menu', $menu );
			}
		}

		wp_send_json_success( esc_attr( $role ) . ' ' . __( 'Menu reset successfully', 'ats-dashboard' ) );
	}

}
