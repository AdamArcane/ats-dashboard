<?php
/**
 * Email body field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\EmailNotifications\Email_Notifications_Module;

return function ( $email_key ) {

	$settings = get_option( 'ats_email_notifications' );
	$body     = isset( $settings['emails'][ $email_key ]['body'] ) ? $settings['emails'][ $email_key ]['body'] : null;

	if ( null === $body ) {
		$module   = new Email_Notifications_Module();
		$types    = $module->get_email_types();
		$body     = isset( $types[ $email_key ]['defaults']['body'] ) ? $types[ $email_key ]['defaults']['body'] : '';
	}

	$editor_id = 'ats_email_notifications_' . $email_key . '_body';

	wp_editor(
		$body,
		$editor_id,
		array(
			'textarea_name' => 'ats_email_notifications[emails][' . $email_key . '][body]',
			'media_buttons' => false,
			'editor_height' => 250,
			'quicktags'     => true,
			'tinymce'       => array(
				'toolbar1' => 'bold,italic,link,unlink,bullist,numlist,blockquote,undo,redo',
				'toolbar2' => '',
			),
		)
	);

};
