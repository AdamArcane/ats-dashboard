<?php
/**
 * Remove all widgets field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings   = get_option( 'ats_settings' );
	$is_checked = isset( $settings['remove-all'] ) ? 1 : 0;
	?>

	<label for="ats_settings[remove-all]" class="label checkbox-label">
		<?php esc_html_e( 'All', 'ats-dashboard' ); ?>
		<input type="hidden" name="ats_settings_checkboxes[]" value="remove-all">
		<input type="checkbox" name="ats_settings[remove-all]" id="ats_settings[remove-all]" value="1" <?php checked( $is_checked, 1 ); ?>>
		<div class="indicator"></div>
	</label>

	<?php

};
