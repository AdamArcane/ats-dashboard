<?php
/**
 * Email enabled toggle. Rendered inline in the "Emails" tab's table row.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $email_key ) {

	$settings   = get_option( 'ats_email_notifications' );
	$is_enabled = isset( $settings['emails'][ $email_key ]['enabled'] ) ? $settings['emails'][ $email_key ]['enabled'] : '0';
	?>

	<label for="ats_email_notifications_<?php echo esc_attr( $email_key ); ?>_enabled" class="toggle-switch" title="<?php esc_attr_e( "When off, WordPress sends its own default email instead of this override.", 'ats-dashboard' ); ?>">
		<input
			type="checkbox"
			name="ats_email_notifications[emails][<?php echo esc_attr( $email_key ); ?>][enabled]"
			id="ats_email_notifications_<?php echo esc_attr( $email_key ); ?>_enabled"
			value="1"
			<?php checked( $is_enabled, '1' ); ?>
		/>
		<div class="switch-track">
			<div class="switch-thumb"></div>
		</div>
	</label>

	<?php

};
