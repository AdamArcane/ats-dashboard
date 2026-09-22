<?php
/**
 * Remove on uninstall field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings   = get_option( 'ats_settings' );
	$is_checked = isset( $settings['remove-on-uninstall'] ) ? 1 : 0;
	?>

	<label for="ats_settings[remove-on-uninstall]" class="label checkbox-label">
		&nbsp;
		<input type="hidden" name="ats_settings_checkboxes[]" value="remove-on-uninstall">
		<input type="checkbox" name="ats_settings[remove-on-uninstall]" id="ats_settings[remove-on-uninstall]" value="1" <?php checked( $is_checked, 1 ); ?>>
		<div class="indicator"></div>
	</label>

	<?php

};
