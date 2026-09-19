<?php
/**
 * Widgets order field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$widget_order_user = get_site_option( 'ats_multisite_widget_order' );

	$blogusers = get_users(
		array(
			'blog_id' => '1',
			'role'    => 'administrator',
		)
	);

	if ( ! empty( $blogusers ) ) {

		echo '<select name="ats_multisite_widget_order">';

		?>

		<option value="0" <?php selected( $widget_order_user, 0 ); ?>><?php _e( 'Custom Order', 'ats-dashboard' ); ?></option>

		<?php
		foreach ( $blogusers as $user ) {
			$user_id   = $user->ID;
			$user_name = ucfirst( $user->display_name );
			?>
			<option value="<?php echo esc_attr( $user_id ); ?>" <?php selected( $widget_order_user, $user_id ); ?>><?php echo esc_html( $user_name ); ?>'s <?php _e( 'Order', 'ats-dashboard' ); ?></option>
			<?php
		}

		echo '</select>';

		?>

		<p>
			<?php _e( 'The order of the dashboard widgets is saved on a per user basis. To display the widgets in the same order throughout all sites on your network, select the user here. <br><strong>Note:</strong> the user must be an admin (or super admin) on the main site of the network to show up in the list.', 'ats-dashboard' ); ?>
		</p>

		<?php

	}

};
