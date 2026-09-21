<?php
/**
 * Widget link color field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings     = get_option( 'ats_settings' );
	$accent_color = isset( $settings['link_color'] ) ? $settings['link_color'] : '#2271b1';

	echo '<input type="text" name="ats_settings[link_color]" value="' . esc_attr( $accent_color ) . '" class="ats-color-field ats-link-color-settings-field" data-default="#2271b1" />';

};
