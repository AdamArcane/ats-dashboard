<?php
/**
 * Remove screen option tab field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings   = get_option( 'ats_settings' );
	$is_checked = isset( $settings['remove_screen_options'] ) ? 1 : 0;
	?>

	<label for="ats_settings[remove_screen_options]" class="label checkbox-label">
		&nbsp;
		<input type="checkbox" name="ats_settings[remove_screen_options]" id="ats_settings[remove_screen_options]" value="1" <?php checked( $is_checked, 1 ); ?>>
		<div class="indicator"></div>
	</label>

	<?php

};
