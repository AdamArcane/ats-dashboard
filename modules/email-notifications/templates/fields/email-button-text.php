<?php
/**
 * Email button text field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $email_key ) {

	$settings    = get_option( 'ats_email_notifications' );
	$button_text = isset( $settings['emails'][ $email_key ]['button_text'] ) ? $settings['emails'][ $email_key ]['button_text'] : '';
	?>

	<input type="text" name="ats_email_notifications[emails][<?php echo esc_attr( $email_key ); ?>][button_text]" class="all-options" value="<?php echo esc_attr( $button_text ); ?>" />

	<p class="description">
		<?php esc_html_e( 'Leave blank to hide the call-to-action button entirely.', 'ats-dashboard' ); ?>
	</p>

	<?php

};
