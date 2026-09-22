<?php
/**
 * Site Owner role enabled field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Setting\Site_Owner_Role;

return function () {

	$enabled = Site_Owner_Role::is_enabled();
	?>

	<label class="toggle-switch">
		<input type="hidden" name="ats_settings_checkboxes[]" value="site_owner_role_enabled">
		<input type="checkbox" name="ats_settings[site_owner_role_enabled]" value="1" <?php checked( $enabled ); ?> />
		<div class="switch-track">
			<div class="switch-thumb"></div>
		</div>
	</label>

	<p class="description"><?php esc_html_e( 'Adds a "Site Owner" role: a configurable, capability-restricted clone of Administrator. Useful for giving a client near-admin access without full control over plugins, themes, core updates, or user management.', 'ats-dashboard' ); ?></p>

	<?php

};
