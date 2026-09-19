<?php
/**
 * Headline text field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings = get_option( 'ats_settings' );
	$headline = isset( $settings['dashboard_headline'] ) ? $settings['dashboard_headline'] : '';
	?>

	<input type="text" name="ats_settings[dashboard_headline]" class="all-options" value="<?php echo esc_attr( $headline ); ?>" placeholder="<?php esc_attr_e( 'Dashboard', 'ats-dashboard' ); ?>" />

	<?php

};
