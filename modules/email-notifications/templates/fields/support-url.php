<?php
/**
 * Support URL field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings = get_option( 'ats_email_notifications' );
	$url      = isset( $settings['global']['support_url'] ) ? $settings['global']['support_url'] : '';
	?>

	<input type="text" name="ats_email_notifications[global][support_url]" class="all-options" value="<?php echo esc_url( $url ); ?>" placeholder="https://" />

	<p class="description">
		<?php esc_html_e( 'Optional link to a support portal or help center.', 'ats-dashboard' ); ?>
	</p>

	<?php

};
