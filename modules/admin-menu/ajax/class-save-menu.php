<?php
/**
 * Save menu.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\AdminMenu\Ajax;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Class to handle ajax request to save admin menu.
 */
class Save_Menu {

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
	 * Save menu.
	 */
	public function save() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'ats_admin_menu_save_menu' ) ) {
			wp_send_json_error( __( 'Invalid token', 'ats-dashboard' ) );
		}

		$capability = apply_filters( 'ats_settings_capability', 'manage_options' );

		if ( ! current_user_can( $capability ) ) {
			wp_send_json_error( __( 'You do not have permission to perform this action', 'ats-dashboard' ) );
		}

		$_POST['menu'] = json_decode( stripslashes( $_POST['menu'] ), true );

		$saved_menu = get_option( 'ats_admin_menu', array() );

		/**
		 * Update the role based menu.
		 *
		 * In the menu editor, the role based menu is only loaded if it's tab has been opened.
		 * Also, it's tab is not delete-able.
		 * That means, we only need to update the loaded menu.
		 */
		foreach ( $_POST['menu'] as $role_name => $menu_items ) {
			$saved_menu[ $role_name ] = $menu_items;
		}

		/**
		 * Update the user based menu.
		 *
		 * Because user based menu is always loaded by default in the menu editor,
		 * and it's tab is delete-able,
		 * then we need to loop it and delete the menu that's not sent via ajax.
		 */
		foreach ( $saved_menu as $role_name => $menu_items ) {
			if ( false !== stripos( $role_name, 'user_id_' ) && ! isset( $_POST['menu'][ $role_name ] ) ) {
				unset( $saved_menu[ $role_name ] );
			}
		}

		update_option( 'ats_admin_menu', $saved_menu );

		wp_send_json_success( __( 'Menu updated successfully', 'ats-dashboard' ) );
	}

}
