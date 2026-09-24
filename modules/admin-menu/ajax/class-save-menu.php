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
	 *
	 * There's a single saved menu list now (no more per-role/per-user copies),
	 * so this is just a straight save of whatever the builder posts.
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

		$menu_items = isset( $_POST['menu'] ) ? json_decode( stripslashes( $_POST['menu'] ), true ) : array();
		$menu_items = is_array( $menu_items ) ? $menu_items : array();

		update_option( 'ats_admin_menu', $menu_items );

		wp_send_json_success( __( 'Menu updated successfully', 'ats-dashboard' ) );
	}

}
