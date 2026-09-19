<?php
/**
 * Custom dashboard css field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings   = get_option( 'ats_settings' );
	$custom_css = isset( $settings['custom_css'] ) ? $settings['custom_css'] : false;

	?>

	<textarea id="ats-custom-dashboard-css"
			  class="widefat textarea ats-custom-css"
			  name="ats_settings[custom_css]"><?php echo esc_textarea( $custom_css ); ?></textarea>

	<?php
};
