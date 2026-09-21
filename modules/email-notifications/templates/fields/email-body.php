<?php
/**
 * Email body field.
 *
 * Rendered inside a popup that starts hidden, so TinyMCE is never
 * auto-initialized here (it sizes to zero inside a `display:none`
 * container). The "Text" mode textarea always prints normally; the JS
 * upgrades it to a full Visual/Text editor via `wp.editor.initialize()`
 * only once its popup is actually opened.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\EmailNotifications\Email_Notifications_Module;

return function ( $email_key ) {

	$module      = new Email_Notifications_Module();
	$types       = $module->get_email_types();
	$body_format = isset( $types[ $email_key ]['body_format'] ) ? $types[ $email_key ]['body_format'] : 'html';

	$settings = get_option( 'ats_email_notifications' );
	$body     = isset( $settings['emails'][ $email_key ]['body'] ) ? $settings['emails'][ $email_key ]['body'] : null;

	if ( null === $body ) {
		$body = isset( $types[ $email_key ]['defaults']['body'] ) ? $types[ $email_key ]['defaults']['body'] : '';
	}

	$field_name = 'ats_email_notifications[emails][' . $email_key . '][body]';

	if ( 'text' === $body_format ) {
		?>
		<textarea name="<?php echo esc_attr( $field_name ); ?>" rows="10" class="large-text code"><?php echo esc_textarea( $body ); ?></textarea>
		<?php
		return;
	}

	$editor_id = 'ats_email_notifications_' . $email_key . '_body';
	?>

	<div class="ats-email-notifications-editor-wrap" data-editor-id="<?php echo esc_attr( $editor_id ); ?>">
		<?php
		wp_editor(
			$body,
			$editor_id,
			array(
				'textarea_name' => $field_name,
				'media_buttons' => false,
				'editor_height' => 250,
				'tinymce'       => false,
				'quicktags'     => true,
			)
		);
		?>
	</div>

	<?php

};
