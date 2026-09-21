<?php
/**
 * Widget border color field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings     = get_option( 'ats_settings' );
	$accent_color = isset( $settings['border_color'] ) ? $settings['border_color'] : '#c3c4c7';

	echo '<input type="text" name="ats_settings[border_color]" value="' . esc_attr( $accent_color ) . '" class="ats-color-field ats-border-color-settings-field" data-default="#c3c4c7" />';

};
