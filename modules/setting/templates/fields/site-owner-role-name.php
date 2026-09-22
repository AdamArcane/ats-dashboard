<?php
/**
 * Site Owner role name field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Setting\Site_Owner_Role;

return function () {

	$settings = get_option( 'ats_settings', array() );
	$name     = isset( $settings['site_owner_role_name'] ) ? $settings['site_owner_role_name'] : '';
	?>

	<input type="text" name="ats_settings[site_owner_role_name]" class="all-options" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php echo esc_attr( Site_Owner_Role::DEFAULT_ROLE_NAME ); ?>" />

	<p class="description"><?php esc_html_e( 'The display name shown for this role, e.g. in the user profile role dropdown.', 'ats-dashboard' ); ?></p>

	<?php

};
