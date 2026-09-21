<?php
/**
 * Widget border radius field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings      = get_option( 'ats_settings' );
	$border_radius = isset( $settings['border_radius'] ) ? $settings['border_radius'] : '8';
	?>

	<input type="number" name="ats_settings[border_radius]" value="<?php echo esc_attr( $border_radius ); ?>" min="0" max="50" step="1" class="small-text" /> <?php esc_html_e( 'px', 'ats-dashboard' ); ?>

	<?php

};
