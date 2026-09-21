<?php
/**
 * Accent color field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\EmailNotifications\Email_Notifications_Output;

return function () {

	$settings = get_option( 'ats_email_notifications' );
	$default  = Email_Notifications_Output::get_instance()->get_branding_color( 'accent_color' );
	$color    = isset( $settings['global']['accent_color'] ) && $settings['global']['accent_color'] ? $settings['global']['accent_color'] : $default;
	?>

	<input type="text" name="ats_email_notifications[global][accent_color]" value="<?php echo esc_attr( $color ); ?>" class="ats-email-notifications-color-field" data-default="<?php echo esc_attr( $default ); ?>" />

	<p class="description">
		<?php esc_html_e( 'Used for the call-to-action button and links. Defaults to your configured branding accent color.', 'ats-dashboard' ); ?>
	</p>

	<?php

};
