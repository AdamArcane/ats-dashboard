<?php
/**
 * Headline text field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings = get_option( 'ats_settings' );
	$headline = isset( $settings['dashboard_headline'] ) ? $settings['dashboard_headline'] : '';
	?>

	<input type="text" name="ats_settings[dashboard_headline]" class="all-options" value="<?php echo esc_attr( $headline ); ?>" placeholder="<?php esc_attr_e( 'Dashboard', 'ats-dashboard' ); ?>" />

	<p class="description">
		<?php
		printf(
			/* translators: %s: comma-separated list of placeholder tokens. */
			esc_html__( 'You can use the following placeholders: %s', 'ats-dashboard' ),
			'<code>{site_name}</code>, <code>{display_name}</code>, <code>{first_name}</code>, <code>{username}</code>'
		);
		?>
	</p>

	<?php

};
