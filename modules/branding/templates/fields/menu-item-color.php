<?php
/**
 * Menu item color field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$branding = get_option( 'ats_branding' );
	$default  = '#ffffff';
	$color    = isset( $branding['menu_item_color'] ) ? $branding['menu_item_color'] : $default;
	?>

	<input type="text" name="ats_branding[menu_item_color]" value="<?php echo esc_attr( $color ); ?>" class="ats-color-field ats-branding-color-field ats-instant-preview-trigger" data-default="<?php echo esc_attr( $default ); ?>" data-ats-trigger-name="menu-item-color" />

	<?php

};
