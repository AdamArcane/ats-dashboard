<?php
/**
 * Support email field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings = get_option( 'ats_email_notifications' );
	$email    = isset( $settings['global']['support_email'] ) ? $settings['global']['support_email'] : '';
	?>

	<input type="email" name="ats_email_notifications[global][support_email]" class="all-options" value="<?php echo esc_attr( $email ); ?>" placeholder="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" />

	<?php

};
