<?php
/**
 * Admin page saving process.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module, $post_id ) {

	if ( 'ats_admin_page' !== get_post_type( $post_id ) ) {
		return;
	}

	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'update-post_' . $post_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Content type.
	if ( isset( $_POST['ats_content_type'] ) ) {
		update_post_meta( $post_id, 'ats_content_type', sanitize_key( $_POST['ats_content_type'] ) );
	}

	// Menu attributes.
	if ( isset( $_POST['ats_menu_type'] ) ) {
		update_post_meta( $post_id, 'ats_menu_type', sanitize_key( $_POST['ats_menu_type'] ) );
	}

	if ( isset( $_POST['ats_menu_parent'] ) ) {
		update_post_meta( $post_id, 'ats_menu_parent', sanitize_text_field( wp_unslash( $_POST['ats_menu_parent'] ) ) );
	}

	if ( isset( $_POST['ats_menu_order'] ) ) {
		update_post_meta( $post_id, 'ats_menu_order', absint( $_POST['ats_menu_order'] ) );
	}

	if ( isset( $_POST['ats_menu_icon'] ) ) {
		update_post_meta( $post_id, 'ats_menu_icon', sanitize_text_field( wp_unslash( $_POST['ats_menu_icon'] ) ) );
	}

	// HTML content.
	if ( isset( $_POST['ats_html_content'] ) ) {
		update_post_meta( $post_id, 'ats_html_content', wp_kses( wp_unslash( $_POST['ats_html_content'] ), $module->content()->get_admin_page_html_allowed_tags() ) );
	}

	// Display options.
	update_post_meta( $post_id, 'ats_remove_page_title', isset( $_POST['ats_remove_page_title'] ) ? 1 : 0 );
	update_post_meta( $post_id, 'ats_remove_page_margin', isset( $_POST['ats_remove_page_margin'] ) ? 1 : 0 );
	update_post_meta( $post_id, 'ats_remove_admin_notices', isset( $_POST['ats_remove_admin_notices'] ) ? 1 : 0 );

	// Custom CSS.
	if ( isset( $_POST['ats_custom_css'] ) ) {
		update_post_meta( $post_id, 'ats_custom_css', $module->content()->sanitize_css( $_POST['ats_custom_css'] ) );
	}

	// Custom js.
	if ( current_user_can( 'unfiltered_html' ) && isset( $_POST['ats_custom_js'] ) && is_string( $_POST['ats_custom_js'] ) ) {
		update_post_meta( $post_id, 'ats_custom_js', $_POST['ats_custom_js'] );
	}

	// Allowed roles.
	if ( isset( $_POST['ats_allowed_roles'] ) ) {
		$allowed_roles = is_array( $_POST['ats_allowed_roles'] ) ? $_POST['ats_allowed_roles'] : array();

		foreach ( $allowed_roles as &$allowed_role ) {
			$allowed_role = sanitize_text_field( $allowed_role );
		}

		update_post_meta( $post_id, 'ats_allowed_roles', $allowed_roles );
	}

};
