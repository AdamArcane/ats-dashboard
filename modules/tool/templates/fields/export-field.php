<?php
/**
 * Export field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	?>

	<p><?php esc_html_e( 'Select the settings you would like to export and use the button below to generate & export a .json file.', 'ats-dashboard' ); ?></p>
	<br>
	<p>
		<label>
			<input type="checkbox" name="ats_export_modules[]" class="ats-module-checkbox" value="modules_manager" checked />
			<?php esc_html_e( 'Module Manager', 'ats-dashboard' ); ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="ats_export_modules[]" class="ats-module-checkbox" value="settings" checked />
			<?php esc_html_e( 'Settings', 'ats-dashboard' ); ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="ats_export_modules[]" class="ats-module-checkbox" value="widgets" checked />
			<?php esc_html_e( 'Dashboard Widgets', 'ats-dashboard' ); ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="ats_export_modules[]" class="ats-module-checkbox" value="branding" checked />
			<?php esc_html_e( 'White Label Settings', 'ats-dashboard' ); ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="ats_export_modules[]" class="ats-module-checkbox" value="login_customizer" checked />
			<?php esc_html_e( 'Login Customizer Settings', 'ats-dashboard' ); ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="ats_export_modules[]" class="ats-module-checkbox" value="login_redirect" checked />
			<?php esc_html_e( 'Login Redirect Settings', 'ats-dashboard' ); ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="ats_export_modules[]" class="ats-module-checkbox" value="admin_pages" checked />
			<?php esc_html_e( 'Admin Pages', 'ats-dashboard' ); ?>
		</label>
	</p>

	<?php do_action( 'ats_export_fields' ); ?>

	<br>
	<p>
		<a href="#" class="ats-select-all-modules">Select All</a>
	</p>

	<?php

};
