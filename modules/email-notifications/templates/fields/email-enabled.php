<?php
/**
 * Email enabled field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $email_key ) {

	$settings   = get_option( 'ats_email_notifications' );
	$is_enabled = isset( $settings['emails'][ $email_key ]['enabled'] ) ? $settings['emails'][ $email_key ]['enabled'] : '1';
	?>

	<label for="ats_email_notifications_<?php echo esc_attr( $email_key ); ?>_enabled" class="label checkbox-label">
		<?php esc_html_e( 'Use this editable template for this email', 'ats-dashboard' ); ?>
		<input type="checkbox" name="ats_email_notifications[emails][<?php echo esc_attr( $email_key ); ?>][enabled]" id="ats_email_notifications_<?php echo esc_attr( $email_key ); ?>_enabled" value="1" <?php checked( $is_enabled, '1' ); ?>>
		<div class="indicator"></div>
	</label>

	<p class="description">
		<?php esc_html_e( 'When off, WordPress sends its default email for this notification instead.', 'ats-dashboard' ); ?>
	</p>

	<?php

};
