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
		<li>
			<?php
			printf(
				/* translators: %1$s, %2$s, %3$s: page builder names */
				esc_html__( 'Use %1$s, %2$s or %3$s to create custom Admin Pages', 'ats-dashboard' ),
				'<strong>Elementor</strong>',
				'<strong>Beaver Builder</strong>',
				'<strong>Brizy</strong>'
			);
			?>
		</li>
		<li><?php esc_html_e( 'Restrict Admin Pages to specific Users or User Roles', 'ats-dashboard' ); ?></li>
	</ul>

	<a href="https://ats-dashboard.io/docs/admin-pages/?utm_source=plugin&utm_medium=edit_admin_page&utm_campaign=ats" target="_blank" class="button button-primary button-large">
		<?php esc_html_e( 'Get ATS Dashboard', 'ats-dashboard' ); ?>
	</a>

	<?php
};
