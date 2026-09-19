<?php
/**
 * Icon color field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings     = get_option( 'ats_settings' );
	$accent_color = isset( $settings['icon_color'] ) ? $settings['icon_color'] : '#555555';

	echo '<input type="text" name="ats_settings[icon_color]" value="' . esc_attr( $accent_color ) . '" class="ats-color-field ats-widget-color-settings-field" data-default="#555555" />';

};
