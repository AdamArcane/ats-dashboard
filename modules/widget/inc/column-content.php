<?php
/**
 * Setup column content on widget list screen.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $column, $post_id ) {
	switch ( $column ) {

		case 'type':
			$widget_type = get_post_meta( $post_id, 'ats_widget_type', true );
			// Preventing edge case when widget_type is empty.
			if ( ! $widget_type ) {
				do_action( 'ats_compat_widget_type', $post_id );
			} else {
				$column_content = '';

				if ( 'html' === $widget_type ) {
					$column_content = __( 'HTML', 'ats-dashboard' );
				} elseif ( 'text' === $widget_type ) {
					$column_content = __( 'Text', 'ats-dashboard' );
				} elseif ( 'icon' === $widget_type ) {
					$column_content = '<i class="' . esc_attr( get_post_meta( $post_id, 'ats_icon_key', true ) ) . '"></i>';
				}

				$column_content = apply_filters( 'ats_widget_list_type_column_content', $column_content, $post_id, $widget_type );

				echo wp_kses_post( $column_content );
			}
			break;

		case 'roles':
			$allowed_roles = __( 'All', 'ats-dashboard' );
			$allowed_roles = apply_filters( 'ats_widget_list_roles_column_content', $allowed_roles, $post_id );

			echo esc_html( ucwords( $allowed_roles ) );
			break;

		case 'status':
			$status_object = get_post_status_object( get_post_status( $post_id ) );

			echo esc_html( $status_object ? $status_object->label : get_post_status( $post_id ) );
			break;

	}
};
