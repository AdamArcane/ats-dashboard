<?php
/**
 * Email subject field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $email_key ) {

	$settings = get_option( 'ats_email_notifications' );
	$subject  = isset( $settings['emails'][ $email_key ]['subject'] ) ? $settings['emails'][ $email_key ]['subject'] : '';
	?>

	<input type="text" name="ats_email_notifications[emails][<?php echo esc_attr( $email_key ); ?>][subject]" class="all-options" value="<?php echo esc_attr( $subject ); ?>" />

	<?php

};
