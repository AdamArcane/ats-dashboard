<?php
/**
 * Disable site overview widget field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings   = get_option( 'ats_settings' );
	$is_checked = isset( $settings['disable_site_overview_widget'] ) ? 1 : 0;
	?>

	<label for="ats_settings[disable_site_overview_widget]" class="label checkbox-label">
		&nbsp;
		<input type="hidden" name="ats_settings_checkboxes[]" value="disable_site_overview_widget">
		<input type="checkbox" name="ats_settings[disable_site_overview_widget]" id="ats_settings[disable_site_overview_widget]" value="1" <?php checked( $is_checked, 1 ); ?>>
		<div class="indicator"></div>
	</label>

	<p class="description"><?php esc_html_e( 'By default, the "At a Glance" widget is replaced with a "Site Overview" widget geared towards managed clients (content counts, storage used, SSL status, last update) instead of WordPress/theme details. Check this to restore the original "At a Glance" widget instead.', 'ats-dashboard' ); ?></p>

	<?php

};
