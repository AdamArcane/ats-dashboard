<?php
/**
 * Clear contact form logs.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$nonce   = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';
	$post_id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0;

	if ( ! wp_verify_nonce( $nonce, 'ats_clear_contact_form_' . $post_id ) ) {
		wp_send_json_error( __( 'Invalid token', 'ats-dashboard' ) );
	}

	$capability = apply_filters( 'ats_settings_capability', 'manage_options' );

	if ( ! current_user_can( $capability ) ) {
		wp_send_json_error( __( 'You do not have permission to perform this action', 'ats-dashboard' ) );
	}

	$page = get_post( $post_id );

	if ( ! $page ) {
		wp_send_json_error( __( 'Post not found', 'ats-dashboard' ) );
	}

	$is_deleted = delete_post_meta( $post_id, 'ats_contact_form_logs' );

	if ( $is_deleted ) {

		wp_send_json_success(
			array(
				'message' => '<div class="ats-form-widget-success-notice">' . __( 'Log deleted', 'ats-dashboard' ) . '</div>',
			)
		);

	} else {

		wp_send_json_error(
			array(
				'message' => '<div class="ats-form-widget-error-notice">' . __( 'Unable to delete log entries. Please try again later.', 'ats-dashboard' ) . '</div>',
			)
		);

	}

};
