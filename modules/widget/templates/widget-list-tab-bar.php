<?php
/**
 * Tab bar injected above the native Dashboard Widgets list table.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {
	?>

	<div class="atsui-wrap ats-widget-list-tab-bar">

		<div class="atsui-header atsui-has-tab-nav atsui-margin-bottom">

			<div class="atsui-container atsui-container-center">

				<div class="logo-container">

					<div>
						<span class="title">
							<?php esc_html_e( 'Dashboard Widgets', 'ats-dashboard' ); ?>
						</span>
						<p class="subtitle"><?php esc_html_e( 'Manage your custom dashboard widgets.', 'ats-dashboard' ); ?></p>
					</div>

					<div>
						<img src="<?php echo esc_url( ATS_DASHBOARD_PLUGIN_URL ); ?>/assets/img/logo.png">
					</div>

				</div>

				<nav>
					<ul class="atsui-tab-nav">
						<li class="atsui-tab-nav-item">
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=ats_widget_settings' ) ); ?>"><?php esc_html_e( 'Settings', 'ats-dashboard' ); ?></a>
						</li>
						<li class="atsui-tab-nav-item">
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=ats_widget_settings#styling' ) ); ?>"><?php esc_html_e( 'Styling', 'ats-dashboard' ); ?></a>
						</li>
						<li class="atsui-tab-nav-item">
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=ats_widget_settings#welcome-panel' ) ); ?>"><?php esc_html_e( 'Welcome Panel', 'ats-dashboard' ); ?></a>
						</li>
						<li class="atsui-tab-nav-item active">
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=ats_widgets' ) ); ?>"><?php esc_html_e( 'Custom Widgets', 'ats-dashboard' ); ?></a>
						</li>
					</ul>
				</nav>

			</div>

		</div>

		<div class="atsui-container atsui-container-center atsui-list-actions">
			<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=ats_widgets' ) ); ?>" class="button button-primary button-larger">
				<?php esc_html_e( 'Add Dashboard Widget', 'ats-dashboard' ); ?>
			</a>
		</div>

	</div>

	<?php
};
