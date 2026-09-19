<?php
/**
 * Headline color field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings     = get_option( 'ats_settings' );
	$accent_color = isset( $settings['headline_color'] ) ? $settings['headline_color'] : '#23282d';

	echo '<input type="text" name="ats_settings[headline_color]" value="' . esc_attr( $accent_color ) . '" class="ats-color-field ats-headline-color-settings-field" data-default="#23282d" />';

};
