<?php
/**
 * Position metabox.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Helpers\Multisite_Helper;

return function ( $post ) {

	wp_nonce_field( 'ats_widget_roles', 'ats_widget_roles_nonce' );

	$widget_roles = get_post_meta( $post->ID, 'ats_widget_roles', true );
	$widget_roles = empty( $widget_roles ) ? array( 'all' ) : $widget_roles;
	$widget_roles = ( new \ATSDash\Helpers\Array_Helper() )->clean_unserialize( $widget_roles, 3 );

	$roles_obj = new \WP_Roles();
	$roles     = $roles_obj->role_names;

	$ms_helper = new Multisite_Helper();
	?>

	<p>
		<?php _e( 'Show Widget to:', 'ats-dashboard' ); ?>
	</p>
	<select class="ats-widget-roles-field" name="ats_widget_roles[]" multiple>

		<option value="all"  <?php echo esc_attr( in_array( 'all', $widget_roles, true ) ? 'selected' : '' ); ?>>
			<?php _e( 'All', 'ats-dashboard' ); ?>
		</option>

		<?php if ( $ms_helper->multisite_supported() ) : ?>
			<option value="super_admin"  <?php echo esc_attr( in_array( 'super_admin', $widget_roles, true ) ? 'selected' : '' ); ?>>
				<?php _e( 'Super Admin', 'ats-dashboard' ); ?>
			</option>
		<?php endif; ?>

		<?php foreach ( $roles as $role_key => $role_name ) : ?>
			<?php
			$selected_attr = '';
			$selected_attr = in_array( $role_key, $widget_roles, true ) ? 'selected' : '';
			?>
			<option value="<?php echo esc_attr( $role_key ); ?>" <?php echo esc_attr( $selected_attr ); ?>>
				<?php echo esc_attr( $role_name ); ?>
			</option>
		<?php endforeach; ?>

	</select>

	<?php

};
