<?php
/**
 * Widget body text color field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings     = get_option( 'ats_settings' );
	$accent_color = isset( $settings['body_text_color'] ) ? $settings['body_text_color'] : '#3c434a';

	echo '<input type="text" name="ats_settings[body_text_color]" value="' . esc_attr( $accent_color ) . '" class="ats-color-field ats-body-text-color-settings-field" data-default="#3c434a" />';

};
