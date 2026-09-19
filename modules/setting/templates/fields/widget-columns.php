<?php
/**
 * Widget columns field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings          = get_option( 'ats_settings' );
	$dashboard_columns = isset( $settings['dashboard_columns'] ) ? absint( $settings['dashboard_columns'] ) : 4;

	?>

	<select name="ats_settings[dashboard_columns]">
		<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
			<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $dashboard_columns, $i ); ?>><?php echo esc_attr( $i ); ?></option>
		<?php endfor; ?>
	</select>

	<p class="description">
		<?php _e( 'Change the default column layout.', 'ats-dashboard' ); ?>
		<br>
		<strong>
			<?php _e( 'Note:', 'ats-dashboard' ); ?>
		</strong>
		<?php _e( 'The Dashboard remains responsive & less columns may appear on smaller devices.', 'ats-dashboard' ); ?>
	</p>

	<?php

};
