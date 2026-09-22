<?php
/**
 * Remove font awesome field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings   = get_option( 'ats_settings' );
	$is_checked = isset( $settings['remove_font_awesome'] ) ? 1 : 0;
	?>

	<label for="ats_settings[remove_font_awesome]" class="label checkbox-label">
		&nbsp;
		<input type="hidden" name="ats_settings_checkboxes[]" value="remove_font_awesome">
		<input type="checkbox" name="ats_settings[remove_font_awesome]" id="ats_settings[remove_font_awesome]" value="1" <?php checked( $is_checked, 1 ); ?>>
		<div class="indicator"></div>
	</label>

	<?php

};
