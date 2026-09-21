<?php
/**
 * Widget header background color field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings     = get_option( 'ats_settings' );
	$accent_color = isset( $settings['header_background_color'] ) ? $settings['header_background_color'] : '#ffffff';

	echo '<input type="text" name="ats_settings[header_background_color]" value="' . esc_attr( $accent_color ) . '" class="ats-color-field ats-header-background-color-settings-field" data-default="#ffffff" />';

};
