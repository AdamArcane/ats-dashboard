<?php
/**
 * Admin bar bg color field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$branding = get_option( 'ats_branding' );
	$default  = '#232931';
	$color    = isset( $branding['admin_bar_bg_color'] ) ? $branding['admin_bar_bg_color'] : $default;
	?>

	<input type="text" name="ats_branding[admin_bar_bg_color]" value="<?php echo esc_attr( $color ); ?>" class="ats-color-field ats-branding-color-field ats-instant-preview-trigger" data-default="<?php echo esc_attr( $default ); ?>" data-ats-trigger-name="admin-bar-bg-color" />

	<?php

};
