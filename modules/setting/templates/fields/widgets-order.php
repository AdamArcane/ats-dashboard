<?php
/**
 * Widgets order field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings          = get_option( 'ats_settings' );
	$widget_order_user = isset( $settings['widget_order'] ) ? absint( $settings['widget_order'] ) : 0;

	$blogusers = get_users(
		array(
			'blog_id' => '1',
			'role'    => 'administrator',
		)
	);

	echo '<select name="ats_settings[widget_order]">';

	?>

	<option value="0" <?php selected( $widget_order_user, 0 ); ?>><?php _e( 'Custom Order', 'ats-dashboard' ); ?></option>

	<?php

	foreach ( $blogusers as $user ) {

		$user_id   = $user->ID;
		$user_name = ucfirst( $user->display_name );

		?>

		<option value="<?php echo esc_attr( $user_id ); ?>" <?php selected( $widget_order_user, $user_id ); ?>>
			<?php echo esc_html( $user_name ); ?>'s <?php _e( 'Order', 'ats-dashboard' ); ?>
		</option>

		<?php

	}

	echo '</select>';

	?>

	<p class="description">
		<?php _e( 'The order of the dashboard widgets is saved on a per user basis. Select a user whose widget order you want to apply to all users.', 'ats-dashboard' ); ?>
		<br>
		<strong>
			<?php _e( 'Note:', 'ats-dashboard' ); ?>
		</strong>
		<?php _e( 'The user must be an admin to appear in this list.', 'ats-dashboard' ); ?>
	</p>

	<?php

};
