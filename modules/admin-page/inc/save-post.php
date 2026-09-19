<?php
/**
 * Admin page saving process.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module, $post_id ) {

	// Custom js.
	if ( isset( $_POST['ats_custom_js'] ) ) {
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
