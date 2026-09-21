<?php
/**
 * Widget settings page template.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$widget_settings_tab_menus = array(
		array(
			'id'   => 'general',
			'text' => __( 'Settings', 'ats-dashboard' ),
		),
		array(
			'id'   => 'styling',
			'text' => __( 'Styling', 'ats-dashboard' ),
		),
		array(
			'id'   => 'welcome-panel',
			'text' => __( 'Welcome Panel', 'ats-dashboard' ),
		),
	);

	$widget_settings_tab_menus = apply_filters( 'ats_widget_settings_tab_menus', $widget_settings_tab_menus );
	?>

	<div class="wrap atsui-wrap ats-widget-settings-page">

		<div class="atsui-header atsui-has-tab-nav atsui-margin-bottom">

			<div class="atsui-container atsui-container-center">

				<div class="logo-container">

					<div>
						<span class="title">
							<?php esc_html_e( 'Dashboard Widgets', 'ats-dashboard' ); ?>
						</span>
						<p class="subtitle"><?php esc_html_e( 'Configure the Dashboard Widgets module.', 'ats-dashboard' ); ?></p>
					</div>

					<div>
						<img src="<?php echo esc_url( ATS_DASHBOARD_PLUGIN_URL ); ?>/assets/img/logo.png">
					</div>
				</div>

				<nav>
					<ul class="atsui-tab-nav">
						<?php foreach ( $widget_settings_tab_menus as $tab ) : ?>

							<li class="atsui-tab-nav-item <?php echo esc_attr( $tab['id'] ); ?>-panel">
								<a href="#<?php echo esc_attr( $tab['id'] ); ?>"><?php echo esc_html( $tab['text'] ); ?></a>
							</li>
						<?php endforeach; ?>

						<li class="atsui-tab-nav-item">
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=ats_widgets' ) ); ?>"><?php esc_html_e( 'Custom Widgets', 'ats-dashboard' ); ?></a>
						</li>
					</ul>
				</nav>

			</div>

		</div>

		<div class="atsui-container atsui-container-center">

			<h1 style="display: none;"></h1>

			<form method="post" action="options.php" class="ats-widget-settings-form">

				<?php settings_fields( 'ats-settings-group' ); ?>

				<div>
					<div class="atsui-admin-panel ats-general-panel">
						<div class="atsui is-grouped">
							<?php do_settings_sections( 'ats-widget-settings' ); ?>
						</div>
					</div>

					<div class="atsui-admin-panel ats-styling-panel">
						<div class="atsui">
							<?php do_settings_sections( 'ats-widget-styling-settings' ); ?>
						</div>
					</div>

					<div class="atsui-admin-panel ats-welcome-panel-panel">
						<div class="atsui">
							<?php do_settings_sections( 'ats-welcome-panel-settings' ); ?>
						</div>
					</div>

					<?php do_action( 'ats_after_widgets_panel' ); ?>
				</div>

				<?php submit_button( '', 'button button-primary button-larger' ); ?>

			</form>

		</div>

	</div>

	<?php
};
