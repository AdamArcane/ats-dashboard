<?php
/**
 * Admin menu bg color field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$branding = get_option( 'ats_branding' );
	$default  = '#2E3640';
	$color    = isset( $branding['admin_menu_bg_color'] ) ? $branding['admin_menu_bg_color'] : $default;
	?>

	<input type="text" name="ats_branding[admin_menu_bg_color]" value="<?php echo esc_attr( $color ); ?>" class="ats-color-field ats-branding-color-field ats-instant-preview-trigger" data-default="<?php echo esc_attr( $default ); ?>" data-ats-trigger-name="admin-menu-bg-color" />

	<?php

};
