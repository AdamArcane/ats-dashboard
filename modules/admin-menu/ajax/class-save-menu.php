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
		$_POST['menu'] = is_array( $_POST['menu'] ) ? $_POST['menu'] : array();

		$saved_menu = get_option( 'ats_admin_menu', array() );
		$saved_menu = is_array( $saved_menu ) ? $saved_menu : array();

		$inheritance = new \ATSDash\AdminMenu\Menu_Inheritance_Helper();

		/**
		 * "Default (Everyone)" is the base every role/user diffs against, so it's
		 * persisted first (as a full snapshot, same as before this feature) and
		 * any role/user included in the same save uses this fresh value as its base.
		 */
		if ( isset( $_POST['menu']['default'] ) && is_array( $_POST['menu']['default'] ) ) {
			$saved_menu['default'] = $_POST['menu']['default'];
		}

		$default_items = ! empty( $saved_menu['default'] ) && is_array( $saved_menu['default'] ) ? $saved_menu['default'] : array();

		/**
		 * Update the role & user based menu.
		 *
		 * Only the differences from the resolved parent layer are stored
		 * (Default for a role, Default+Role for a user). Anything left
		 * unchanged keeps inheriting from Default going forward.
		 *
		 * In the menu editor, the role based menu is only loaded if it's tab has been opened.
		 * Also, it's tab is not delete-able.
		 * That means, we only need to update the loaded menu.
		 */
		foreach ( $_POST['menu'] as $role_name => $menu_items ) {
			if ( 'default' === $role_name || ! is_array( $menu_items ) ) {
				continue;
			}

			if ( false !== stripos( $role_name, 'user_id_' ) ) {
				$user_id   = absint( str_ireplace( 'user_id_', '', $role_name ) );
				$user_data = get_userdata( $user_id );
				$user_role = $user_data && ! empty( $user_data->roles[0] ) ? $user_data->roles[0] : '';

				$role_delta    = ! empty( $saved_menu[ $user_role ] ) && is_array( $saved_menu[ $user_role ] ) ? $saved_menu[ $user_role ] : array();
				$resolved_role = $inheritance->apply_delta( $default_items, $role_delta );

				$saved_menu[ $role_name ] = $inheritance->diff_items( $resolved_role, $menu_items );
			} else {
				$saved_menu[ $role_name ] = $inheritance->diff_items( $default_items, $menu_items );
			}
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
