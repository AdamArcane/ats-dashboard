<?php
/**
 * Submit contact form.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Helpers\Multisite_Helper;

return function ( $module ) {

	// Vars.
	$nonce     = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
	$post_id   = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	$ms_helper = new Multisite_Helper();

	if ( ! wp_verify_nonce( $nonce, 'ats_submit_contact_form_' . $post_id ) ) {
		wp_send_json_error( __( 'Invalid token', 'ats-dashboard' ) );
	}

	$name            = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
	$email           = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
	$subject         = isset( $_POST['subject'] ) ? sanitize_text_field( $_POST['subject'] ) : '--';
	$message         = isset( $_POST['message'] ) ? sanitize_text_field( $_POST['message'] ) : '';
	$local_timestamp = date( 'M d, Y H:i:s', time() );

	$title                  = get_the_title( $post_id );
	$site_title             = get_bloginfo( 'name' );
	$site_url               = get_bloginfo( 'url' );
	$error_notice           = get_post_meta( $post_id, 'ats_form_failed_message', true );
	$error_notice           = $error_notice ? esc_html( $error_notice ) : __( 'There was an error trying to send your message. Please try again later.', 'ats-dashboard' );
	$delivery_notice        = get_post_meta( $post_id, 'ats_form_success_message', true );
	$delivery_notice        = $delivery_notice ? $delivery_notice : __( 'Message submitted succesfully.', 'ats-dashboard' );
	$recipient              = get_post_meta( $post_id, 'ats_form_custom_to_address', true );
	$recipient              = $recipient ? $recipient : get_option( 'admin_email' );
	$enable_auto_responder  = get_post_meta( $post_id, 'ats_form_enable_autoresponder', true );
	$auto_responder         = get_post_meta( $post_id, 'ats_form_autoresponder', true );
	$auto_responder_subject = get_post_meta( $post_id, 'ats_form_autoresponder_subject', true );
	$mail_subject           = __( 'New Contact Form Submission', 'ats-dashboard' ) . ' - ' . $site_title;

	if ( $ms_helper->needs_to_switch_blog() ) {

		global $blueprint;

		switch_to_blog( $blueprint );

		// Blueprint vars.
		$blueprint_title                 = get_the_title( $post_id );
		$blueprint_error_notice          = get_post_meta( $post_id, 'ats_form_failed_message', true );
		$blueprint_delivery_notice       = get_post_meta( $post_id, 'ats_form_success_message', true );
		$blueprint_recipient             = get_post_meta( $post_id, 'ats_form_custom_to_address', true );
		$blueprint_enable_auto_responder  = get_post_meta( $post_id, 'ats_form_enable_autoresponder', true );
		$blueprint_auto_responder         = get_post_meta( $post_id, 'ats_form_autoresponder', true );
		$blueprint_auto_responder_subject = get_post_meta( $post_id, 'ats_form_autoresponder_subject', true );

		// Update vars.
		$title                 = $blueprint_title ? $blueprint_title : $title;
		$error_notice          = $blueprint_error_notice ? $blueprint_error_notice : $error_notice;
		$delivery_notice       = $blueprint_delivery_notice ? $blueprint_delivery_notice : $delivery_notice;
		$recipient             = $blueprint_recipient ? $blueprint_recipient : $recipient;
		$enable_auto_responder  = $blueprint_enable_auto_responder ? $blueprint_enable_auto_responder : $enable_auto_responder;
		$auto_responder         = $blueprint_auto_responder ? $blueprint_auto_responder : $auto_responder;
		$auto_responder_subject = $blueprint_auto_responder_subject ? $blueprint_auto_responder_subject : $auto_responder_subject;

		restore_current_blog();

	}

	// Construct email.
	$mail_body  = '<strong>' . __( 'From:', 'ats-dashboard' ) . '</strong> ' . $name;
	$mail_body .= '<br/>';
	$mail_body .= '<strong>' . __( 'Email:', 'ats-dashboard' ) . '</strong> ' . $email;
	$mail_body .= '<br/>';
	$mail_body .= '<strong>' . __( 'Subject:', 'ats-dashboard' ) . '</strong> ' . $subject;
	$mail_body .= '<br/><br/>';
	$mail_body .= '<strong>' . __( 'Message Body:', 'ats-dashboard' ) . '</strong>';
	$mail_body .= '<br/>';
	$mail_body .= $message;
	$mail_body .= '<br/><br/>';
	$mail_body .= '--';
	$mail_body .= '<br/>';
	$mail_body .= __( 'This email was sent from the dashboard of your website', 'ats-dashboard' ) . ' - ' . $site_url . ' (' . $site_title . ' - ' . $title . ')';

	$mail_headers = array( 'Content-Type: text/html; charset=UTF-8' );

	$is_sent = wp_mail( $recipient, $mail_subject, $mail_body, $mail_headers );

	if ( $is_sent ) {

		$module->contact_form_logger( $post_id, $message, $local_timestamp, $email, $subject, $name, $status = true );

		if ( $enable_auto_responder && $auto_responder ) {

			$mail_subject = $auto_responder_subject ? $auto_responder_subject : __( 'Your message has been sent', 'ats-dashboard' );

			$mail_body = $auto_responder;

			$send_mail = wp_mail( $email, $mail_subject, $mail_body, $mail_headers );

		}

		wp_send_json_success(
			array(
				'message' => '<div class="ats-form-widget-success-notice">' . $delivery_notice . '</div>',
			)
		);

	} else {

		$module->contact_form_logger( $post_id, $message, $local_timestamp, $email, $subject, $name, $status = false );

		wp_send_json_error(
			array(
				'message' => '<div class="ats-form-widget-error-notice">' . $error_notice . '</div>',
			)
		);

	}

};
