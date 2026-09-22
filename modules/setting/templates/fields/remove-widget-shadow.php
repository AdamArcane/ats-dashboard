<?php
/**
 * Remove widget box shadow field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings   = get_option( 'ats_settings' );
	$is_checked = isset( $settings['remove_widget_shadow'] ) ? 1 : 0;
	?>

	<label for="ats_settings[remove_widget_shadow]" class="label checkbox-label">
		&nbsp;
		<input type="hidden" name="ats_settings_checkboxes[]" value="remove_widget_shadow">
		<input type="checkbox" name="ats_settings[remove_widget_shadow]" id="ats_settings[remove_widget_shadow]" value="1" <?php checked( $is_checked, 1 ); ?>>
		<div class="indicator"></div>
	</label>

	<?php

};
