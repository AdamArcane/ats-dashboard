<?php
/**
 * Remove 3rd party widgets field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Helpers\Widget_Helper;

return function () {

	$widget_helper = new Widget_Helper();
	$widgets       = $widget_helper->get_3rd_party();
	$settings      = get_option( 'ats_settings' );

	if ( empty( $widgets ) ) {
		_e( 'No 3rd Party Widgets available.', 'ats-dashboard' );
	}
	?>

	<div class="setting-fields is-gapless">

		<?php
		foreach ( $widgets as $id => $widget ) {

			$is_checked = isset( $settings[ $id ] ) ? 1 : 0;
			?>

			<div class="field setting-field">
				<label for="ats_settings[<?php echo esc_attr( $id ); ?>]" class="label checkbox-label">
					<?php echo esc_attr( isset( $widget['title_stripped'] ) ? $widget['title_stripped'] : '' ); ?> (<code><?php echo esc_attr( $id ); ?></code>)
					<input type="checkbox" name="ats_settings[<?php echo esc_attr( $id ); ?>]" id="ats_settings[<?php echo esc_attr( $id ); ?>]" value="1" <?php checked( $is_checked, 1 ); ?>>
					<div class="indicator"></div>
				</label>
			</div>

			<?php
		}
		?>

	</div>

	<?php

};
