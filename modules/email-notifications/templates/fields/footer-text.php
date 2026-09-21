<?php
/**
 * Footer text field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings = get_option( 'ats_email_notifications' );
	$text     = isset( $settings['global']['footer_text'] ) ? $settings['global']['footer_text'] : '';
	?>

	<input type="text" name="ats_email_notifications[global][footer_text]" class="all-options" value="<?php echo esc_attr( $text ); ?>" placeholder="<?php echo esc_attr( sprintf( __( 'This email was sent by %s.', 'ats-dashboard' ), get_bloginfo( 'name' ) ) ); ?>" />

	<p class="description">
		<?php esc_html_e( 'Shown at the bottom of every editable email. Supports the {site_name} tag.', 'ats-dashboard' ); ?>
	</p>

	<?php

};
