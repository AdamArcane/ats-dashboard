<?php
/**
 * Email heading field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $email_key ) {

	$settings = get_option( 'ats_email_notifications' );
	$heading  = isset( $settings['emails'][ $email_key ]['heading'] ) ? $settings['emails'][ $email_key ]['heading'] : '';
	?>

	<input type="text" name="ats_email_notifications[emails][<?php echo esc_attr( $email_key ); ?>][heading]" class="all-options" value="<?php echo esc_attr( $heading ); ?>" />

	<p class="description">
		<?php esc_html_e( 'The bold line shown at the top of the email body.', 'ats-dashboard' ); ?>
	</p>

	<?php

};
