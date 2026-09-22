<?php
/**
 * Remove Howdy text field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings   = get_option( 'ats_settings' );
	$is_checked = isset( $settings['remove_howdy_text'] ) ? 1 : 0;
	?>

	<label for="ats_settings[remove_howdy_text]" class="label checkbox-label">
		&nbsp;
		<input type="hidden" name="ats_settings_checkboxes[]" value="remove_howdy_text">
		<input type="checkbox" name="ats_settings[remove_howdy_text]" id="ats_settings[remove_howdy_text]" value="1" <?php checked( $is_checked, 1 ); ?>>
		<div class="indicator"></div>
	</label>

	<p class="description"><?php esc_html_e( 'Removes the "Howdy" text entirely from the admin bar, leaving just your name. Overrides the custom Howdy text above when checked.', 'ats-dashboard' ); ?></p>

	<?php

};
