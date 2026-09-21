<?php
/**
 * Header injected above the native Admin Pages list table.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {
	?>

	<div class="atsui-wrap ats-admin-page-list-header">

		<div class="atsui-header atsui-margin-bottom">

			<div class="atsui-container atsui-container-center">

				<div class="logo-container">

					<div>
						<span class="title">
							<?php esc_html_e( 'Admin Pages', 'ats-dashboard' ); ?>
						</span>
						<p class="subtitle"><?php esc_html_e( 'Build custom admin pages for your site.', 'ats-dashboard' ); ?></p>
					</div>

					<div>
						<img src="<?php echo esc_url( ATS_DASHBOARD_PLUGIN_URL ); ?>/assets/img/logo.png">
					</div>

				</div>

			</div>

		</div>

		<div class="atsui-container atsui-container-center atsui-list-actions">
			<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=ats_admin_page' ) ); ?>" class="button button-primary button-larger">
				<?php esc_html_e( 'Add Admin Page', 'ats-dashboard' ); ?>
			</a>
		</div>

	</div>

	<?php
};
