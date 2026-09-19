<?php
/**
 * The "Enable wp admin darkmode" field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Outputting "enable wp admin darkmode" field.
 */
return function () {

	$settings   = get_option( 'ats_branding' );
	$is_checked = isset( $settings['wp_admin_darkmode'] ) && $settings['wp_admin_darkmode'];

	$field_description = __(
		'Enable dark mode for the WordPress admin area.',
		'ats-dashboard'
	);
	?>

	<label for="ats_branding--wp_admin_darkmode" class="toggle-switch">
		<input
			type="checkbox"
			name="ats_branding[wp_admin_darkmode]"
			id="ats_branding--wp_admin_darkmode"
			value="1"
			<?php checked( $is_checked, true ); ?>
		/>
		<div class="switch-track">
			<div class="switch-thumb"></div>
		</div>
	</label>

	<p class="description">
		<?php echo esc_html( $field_description ); ?>
	</p>

	<?php

};
