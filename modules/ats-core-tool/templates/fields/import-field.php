<?php
/**
 * Import field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	?>

	<p><?php esc_html_e( 'Select the .json file you would like to import.', 'ats-dashboard' ); ?></p>
	<br>
	<p>
		<label class="block-label" for="ats_import_file"><?php esc_html_e( 'Select File', 'ats-dashboard' ); ?></label>
		<input type="file" name="ats_import_file">
	</p>

	<?php

};
