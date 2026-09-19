<?php
/**
 * Accent color field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$branding     = get_option( 'ats_branding' );
	$default      = '#0073AA';
	$accent_color = isset( $branding['accent_color'] ) ? $branding['accent_color'] : $default;
	?>

	<input type="text" name="ats_branding[accent_color]" value="<?php echo esc_attr( $accent_color ); ?>" class="ats-color-field ats-branding-color-field ats-instant-preview-trigger" data-default="<?php echo esc_attr( $default ); ?>" data-ats-trigger-name="accent-color" />

	<?php

};
