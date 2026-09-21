<?php
/**
 * Settings page template.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$setting_tab_menus = array(
		array(
			'id'     => 'general',
			'text'   => __( 'General', 'ats-dashboard' ),
			'active' => true,
		),
		array(
			'id'   => 'widgets',
			'text' => __( 'Dashboard Widgets', 'ats-dashboard' ),
		),
		array(
			'id'     => 'page-builder-dashboard',
			'text'   => __( 'Page Builder Dashboard', 'ats-dashboard' ),
			'is_pro' => true,
		),
		array(
			'id'   => 'custom-css',
			'text' => __( 'Custom CSS', 'ats-dashboard' ),
		),
	);

	$setting_tab_menus = apply_filters( 'ats_setting_tab_menus', $setting_tab_menus );
	?>

	<div class="wrap atsui-wrap ats-settings-page">

		<div class="atsui-header atsui-has-tab-nav atsui-margin-bottom">

			<div class="atsui-container atsui-container-center">

				<div class="logo-container">

					<div>
						<span class="title">
							<?php esc_html_e( 'Arcane Tech', 'ats-dashboard' ); ?>
						</span>
						<p class="subtitle"><?php esc_html_e( 'Dashboard Settings.', 'ats-dashboard' ); ?></p>
					</div>

					<div>
						<img src="<?php echo esc_url( ATS_DASHBOARD_PLUGIN_URL ); ?>/assets/img/logo.png">
					</div>

				</div>

				<nav>
					<ul class="atsui-tab-nav">
						<?php foreach ( $setting_tab_menus as $tab_index => $tab ) : ?>
						
							<li class="atsui-tab-nav-item <?php echo esc_attr( $tab['id'] ); ?>-panel">
								<a href="#<?php echo esc_attr( $tab['id'] ); ?>"><?php echo esc_html( $tab['text'] ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>

			</div>

		</div>

		<div class="atsui-container atsui-container-center">

			<h1 style="display: none;"></h1>

			<form method="post" action="options.php" class="ats-settings-form">

				<?php settings_fields( 'ats-settings-group' ); ?>

				<div>
					<div class="atsui-admin-panel ats-general-panel">
						<div class="atsui">
							<?php do_settings_sections( 'ats-general-settings' ); ?>
						</div>

						<div class="atsui">
							<?php do_settings_sections( 'ats-misc-settings' ); ?>
						</div>
					</div>

					<?php do_action( 'ats_after_general_panel' ); ?>

						<div class="atsui-admin-panel ats-widgets-panel">
							<div class="atsui is-grouped">
								<?php do_settings_sections( 'ats-widget-settings' ); ?>
							</div>

							<div class="atsui">
								<?php do_settings_sections( 'ats-widget-styling-settings' ); ?>
							</div>

							<div class="atsui">
								<?php do_settings_sections( 'ats-welcome-panel-settings' ); ?>
							</div>
						</div>

					<?php do_action( 'ats_after_widgets_panel' ); ?>

					<div class="atsui-admin-panel ats-page-builder-dashboard-panel">
						<div class="atsui">
							<?php do_settings_sections( 'ats-page-builder-dashboard-settings' ); ?>
						</div>

						<?php do_action( 'ats_after_page_builder_dashboard_metabox' ); ?>
					</div>

					<div class="atsui-admin-panel ats-custom-css-panel">
						<div class="atsui">
							<?php do_settings_sections( 'ats-custom-css-settings' ); ?>
						</div>
					</div>

					<?php do_action( 'ats_after_custom_panel' ); ?>
				</div>

				<?php submit_button( '', 'button button-primary button-larger' ); ?>

			</form>




		</div>


		

	</div>

	<?php
};
