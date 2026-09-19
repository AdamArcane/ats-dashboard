<?php
/**
 * "User Role Access" metabox.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Helpers\Multisite_Helper;

return function ( $post ) {

	$allowed_roles = get_post_meta( $post->ID, 'ats_allowed_roles', true );
	$allowed_roles = empty( $allowed_roles ) ? array( 'all' ) : $allowed_roles;
	$allowed_roles = is_serialized( $allowed_roles ) ? unserialize( $allowed_roles ) : $allowed_roles;

	$roles_obj = new \WP_Roles();
	$roles     = $roles_obj->role_names;

	$ms_helper = new Multisite_Helper();
	?>

	<div class="ats-metabox-field">

		<label class="label">
			<?php _e( 'Allow these roles to access the page', 'ats-dashboard' ); ?>
		</label>

		<select class="ats-widget-roles-field" name="ats_allowed_roles[]" multiple>

			<option value="all"  <?php echo esc_attr( in_array( 'all', $allowed_roles, true ) ? 'selected' : '' ); ?>>
				<?php _e( 'All', 'ats-dashboard' ); ?>
			</option>

			<?php if ( $ms_helper->multisite_supported() ) { ?>

				<option value="super_admin"  <?php echo esc_attr( in_array( 'super_admin', $allowed_roles, true ) ? 'selected' : '' ); ?>>
					<?php _e( 'Super Admin', 'ats-dashboard' ); ?>
				</option>

			<?php } ?>

			<?php foreach ( $roles as $role_key => $role_name ) {

				$selected_attr = '';
				$selected_attr = in_array( $role_key, $allowed_roles, true ) ? 'selected' : '';
				?>

				<option value="<?php echo esc_attr( $role_key ); ?>" <?php echo esc_attr( $selected_attr ); ?>>
					<?php echo esc_attr( $role_name ); ?>
				</option>

			<?php } ?>

		</select>

	</div>

	<?php

};
