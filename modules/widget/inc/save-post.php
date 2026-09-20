<?php
/**
 * Widget saving process.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $post_id ) {

	$post_type = get_post_type( $post_id );

	if ( 'ats_widgets' !== $post_type ) {
		return;
	}

	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	$is_valid_widget_roles_nonce   = isset( $_POST['ats_widget_roles_nonce'] ) && wp_verify_nonce( $_POST['ats_widget_roles_nonce'], 'ats_widget_roles' ) ? true : false;
	$is_valid_restrict_users_nonce = isset( $_POST['ats_restrict_users_nonce'] ) && wp_verify_nonce( $_POST['ats_restrict_users_nonce'], 'ats_restrict_users' ) ? true : false;

	if ( ! $is_valid_widget_roles_nonce || ! $is_valid_restrict_users_nonce ) {
		return;
	}

	// Widget type.
	$is_valid_widget_type_nonce = isset( $_POST['ats_widget_type_nonce'] ) && wp_verify_nonce( $_POST['ats_widget_type_nonce'], 'ats_widget_type' ) ? true : false;

	if ( $is_valid_widget_type_nonce && isset( $_POST['ats_widget_type'] ) ) {
		update_post_meta( $post_id, 'ats_widget_type', sanitize_text_field( $_POST['ats_widget_type'] ) );
	}

	// Icon, text & HTML widget fields all live inside the same metabox as the widget type field.
	if ( $is_valid_widget_type_nonce ) {

		// Icon widget.
		if ( isset( $_POST['ats_icon'] ) ) {
			update_post_meta( $post_id, 'ats_icon_key', sanitize_text_field( $_POST['ats_icon'] ) );
		}

		if ( isset( $_POST['ats_tooltip'] ) ) {
			update_post_meta( $post_id, 'ats_tooltip', sanitize_textarea_field( $_POST['ats_tooltip'] ) );
		}

		if ( isset( $_POST['ats_link'] ) ) {
			update_post_meta( $post_id, 'ats_link', esc_url_raw( $_POST['ats_link'] ) );
		}

		$check = isset( $_POST['ats_link_target'] ) && $_POST['ats_link_target'] ? '_blank' : '';
		update_post_meta( $post_id, 'ats_link_target', $check );

		// Text widget.
		if ( isset( $_POST['ats_content'] ) ) {
			update_post_meta( $post_id, 'ats_content', wp_kses_post( wp_unslash( $_POST['ats_content'] ) ) );
		}

		if ( isset( $_POST['ats_content_height'] ) ) {
			update_post_meta( $post_id, 'ats_content_height', sanitize_text_field( $_POST['ats_content_height'] ) );
		}

		// HTML widget.
		if ( isset( $_POST['ats_html'] ) ) {
			update_post_meta( $post_id, 'ats_html', wp_kses_post( wp_unslash( $_POST['ats_html'] ) ) );
		}
	}

	// Position.
	$is_valid_position_nonce = isset( $_POST['ats_position_nonce'] ) && wp_verify_nonce( $_POST['ats_position_nonce'], 'ats_position' ) ? true : false;

	if ( $is_valid_position_nonce && isset( $_POST['ats_metabox_position'] ) ) {
		update_post_meta( $post_id, 'ats_position_key', sanitize_text_field( $_POST['ats_metabox_position'] ) );
	}

	// Priority.
	$is_valid_priority_nonce = isset( $_POST['ats_priority_nonce'] ) && wp_verify_nonce( $_POST['ats_priority_nonce'], 'ats_priority' ) ? true : false;

	if ( $is_valid_priority_nonce && isset( $_POST['ats_metabox_priority'] ) ) {
		update_post_meta( $post_id, 'ats_priority_key', sanitize_text_field( $_POST['ats_metabox_priority'] ) );
	}

	// Video widget.
	if ( isset( $_POST['ats_video_thumbnail'] ) ) {
		update_post_meta( $post_id, 'ats_video_thumbnail', esc_url_raw( $_POST['ats_video_thumbnail'] ) );
	}

	if ( isset( $_POST['ats_video_platform'] ) ) {
		update_post_meta( $post_id, 'ats_video_platform', $_POST['ats_video_platform'] );
	}

	if ( isset( $_POST['ats_video_id'] ) ) {
		update_post_meta( $post_id, 'ats_video_id', esc_url_raw( $_POST['ats_video_id'] ) );
	}

	// Contact form widget.
	if ( isset( $_POST['ats_form_notes'] ) ) {
		update_post_meta( $post_id, 'ats_form_notes', sanitize_text_field( $_POST['ats_form_notes'] ) );
	}

	if ( isset( $_POST['ats_form_name'] ) ) {
		update_post_meta( $post_id, 'ats_form_name', sanitize_text_field( $_POST['ats_form_name'] ) );
	}

	if ( isset( $_POST['ats_form_email'] ) ) {
		update_post_meta( $post_id, 'ats_form_email', sanitize_text_field( $_POST['ats_form_email'] ) );
	}

	if ( isset( $_POST['ats_form_subject'] ) ) {
		update_post_meta( $post_id, 'ats_form_subject', sanitize_text_field( $_POST['ats_form_subject'] ) );
	}

	if ( isset( $_POST['ats_form_message'] ) ) {
		update_post_meta( $post_id, 'ats_form_message', sanitize_text_field( $_POST['ats_form_message'] ) );
	}

	$check = isset( $_POST['ats_form_subject_enable'] ) && $_POST['ats_form_subject_enable'] ? true : false;
	update_post_meta( $post_id, 'ats_form_subject_enable', $check );

	$check = isset( $_POST['ats_form_enable_logs'] ) && $_POST['ats_form_enable_logs'] ? true : false;
	update_post_meta( $post_id, 'ats_form_enable_logs', $check );

	if ( isset( $_POST['ats_form_success_message'] ) ) {
		update_post_meta( $post_id, 'ats_form_success_message', sanitize_text_field( $_POST['ats_form_success_message'] ) );
	}

	if ( isset( $_POST['ats_form_failed_message'] ) ) {
		update_post_meta( $post_id, 'ats_form_failed_message', sanitize_text_field( $_POST['ats_form_failed_message'] ) );
	}
	$check = isset( $_POST['ats_form_enable_autoresponder'] ) && $_POST['ats_form_enable_autoresponder'] ? true : false;
	update_post_meta( $post_id, 'ats_form_enable_autoresponder', $check );

	if ( isset( $_POST['ats_form_autoresponder_subject'] ) ) {
		update_post_meta( $post_id, 'ats_form_autoresponder_subject', sanitize_text_field( $_POST['ats_form_autoresponder_subject'] ) );
	}

	if ( isset( $_POST['ats_form_autoresponder'] ) ) {
		update_post_meta( $post_id, 'ats_form_autoresponder', sanitize_text_field( $_POST['ats_form_autoresponder'] ) );
	}

	$check = isset( $_POST['ats_form_enable_custom_to_address'] ) && $_POST['ats_form_enable_custom_to_address'] ? true : false;
	update_post_meta( $post_id, 'ats_form_enable_custom_to_address', $check );

	if ( isset( $_POST['ats_form_custom_to_address'] ) ) {
		update_post_meta( $post_id, 'ats_form_custom_to_address', sanitize_email( $_POST['ats_form_custom_to_address'] ) );
	}

	// Widget roles.
	if ( isset( $_POST['ats_widget_roles'] ) ) {
		$_POST['ats_widget_roles'] = empty( $_POST['ats_widget_roles'] ) ? array() : $_POST['ats_widget_roles'];

		foreach ( $_POST['ats_widget_roles'] as $index => $widget_role ) {
			$_POST['ats_widget_roles'][ $index ] = sanitize_text_field( $widget_role );
		}

		update_post_meta( $post_id, 'ats_widget_roles', $_POST['ats_widget_roles'] );
	}

	// Restrict users.
	if ( isset( $_POST['ats_restrict_users'] ) ) {
		$_POST['ats_restrict_users'] = empty( $_POST['ats_restrict_users'] ) ? array() : $_POST['ats_restrict_users'];

		foreach ( $_POST['ats_restrict_users'] as $index => $user_id ) {
			$_POST['ats_restrict_users'][ $index ] = 'all' === $user_id ? 'all' : absint( $user_id );
		}

		update_post_meta( $post_id, 'ats_restrict_users', $_POST['ats_restrict_users'] );
	}

};
