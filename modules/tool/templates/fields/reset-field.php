<?php
/**
 * Reset field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	?>

	<p><?php esc_html_e( 'Permanently delete all ATS Dashboard settings on this site and revert to the plugin defaults. This cannot be undone.', 'ats-dashboard' ); ?></p>
	<br>
	<p>
		<label>
			<input type="checkbox" name="ats_reset_confirm" value="1" required>
			<?php esc_html_e( 'I understand this will delete all ATS Dashboard settings on this site.', 'ats-dashboard' ); ?>
		</label>
	</p>

	<?php

};
