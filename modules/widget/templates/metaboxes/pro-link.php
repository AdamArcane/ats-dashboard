<?php
/**
 * Pro link metabox.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {
	?>

	<ul class="ats-pro-metabox-content">
		<li><?php esc_html_e( 'Video Widgets', 'ats-dashboard' ); ?></li>
		<li><?php esc_html_e( 'Contact Form Widgets', 'ats-dashboard' ); ?></li>
		<li><?php esc_html_e( 'Restrict Widgets to specific Users or User Roles', 'ats-dashboard' ); ?></li>
		<li>
		<?php
		printf(
			/* translators: %1$s, %2$s, %3$s: page builder names */
			esc_html__( 'Create a Custom Dashboard with %1$s, %2$s or %3$s', 'ats-dashboard' ),
			'<strong>Elementor</strong>',
			'<strong>Beaver Builder</strong>',
			'<strong>Brizy</strong>'
		);
		?>
	</li>
	</ul>

	<a style="width: 100%; text-align: center;" href="https://ats-dashboard.io/docs-category/widgets/?utm_source=plugin&utm_medium=edit_widget_page&utm_campaign=ats" target="_blank" class="button button-primary button-large">
		<?php esc_html_e( 'Get ATS Dashboard', 'ats-dashboard' ); ?>
	</a>

	<?php
};
