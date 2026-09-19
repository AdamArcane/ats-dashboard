<?php
/**
 * Branding page template.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
use ats\Setup;

return function () {

	$module        = new Setup();
	$saved_modules = $module->saved_modules();

	$features = array(
		array(
			'title'   => __( 'White Label', 'ats-dashboard' ),
			'img'     => ATS_DASHBOARD_CORE_URL . '/modules/feature/assets/img/white-label.png',
			'text'    => __( 'White label & rebrand the WordPress admin area with the White Label module.', 'ats-dashboard' ),
			'feature' => 'white_label',
		),
		array(
			'title'   => __( 'Login Customizer', 'ats-dashboard' ),
			'img'     => ATS_DASHBOARD_CORE_URL . '/modules/feature/assets/img/login-customizer.png',
			'text'    => __( 'Fully customize the login screen, directly within the WordPress customizer.', 'ats-dashboard' ),
			'feature' => 'login_customizer',
		),
		array(
			'title'   => __( 'Login Redirect', 'ats-dashboard' ),
			'img'     => ATS_DASHBOARD_CORE_URL . '/modules/feature/assets/img/login-redirect.png',
			'text'    => __( 'Change the WordPress login url, redirect users after login & set a <code>/wp-admin/</code> redirect for non logged-in users.', 'ats-dashboard' ),
			'feature' => 'login_redirect',
		),
		array(
			'title'   => __( 'Admin Pages', 'ats-dashboard' ),
			'img'     => ATS_DASHBOARD_CORE_URL . '/modules/feature/assets/img/admin-pages.png',
			'text'    => __( 'Create useful custom admin pages for your customers with the Admin Pages module.', 'ats-dashboard' ),
			'feature' => 'admin_pages',
		),
		array(
			'title'   => __( 'Admin Menu Editor', 'ats-dashboard' ),
			'img'     => ATS_DASHBOARD_CORE_URL . '/modules/feature/assets/img/admin-menu.png',
			'text'    => __( 'Rearrange, hide & add new admin menu items for specific users & user roles with the Admin Menu Editor module.', 'ats-dashboard' ),
			'feature' => 'admin_menu_editor',
		),
		array(
			'title'   => __( 'Admin Bar Editor', 'ats-dashboard' ),
			'img'     => ATS_DASHBOARD_CORE_URL . '/modules/feature/assets/img/admin-bar.png',
			'text'    => __( 'Rearrange, hide & add new items to the WordPress toolbar with the Admin Bar Editor module.', 'ats-dashboard' ),
			'feature' => 'admin_bar_editor',
		),
	)

	?>

	<div class="wrap heatbox-wrap ats-features-page">

		<div class="heatbox-header heatbox-margin-bottom">

			<div class="heatbox-container heatbox-container-center">

				<div class="logo-container">

					<div>
						<span class="title">
							<?php echo esc_html( get_admin_page_title() ); ?>
							<span class="version"><?php echo esc_html( ATS_DASHBOARD_PLUGIN_VERSION ); ?></span>
						</span>
						<p class="subtitle"><?php esc_html_e( 'Enable/disable ATS Dashboard features.', 'ats-dashboard' ); ?></p>
					</div>

					<div>
						<img src="<?php echo esc_url( ATS_DASHBOARD_CORE_URL ); ?>/assets/img/logo.png">
					</div>

				</div>

			</div>

		</div>

		<form method="post" action="options.php">

			<div class="heatbox-container heatbox-container-center">

				<h1 style="display: none;"></h1>

				<div class="ats-features-container">

				<?php foreach ( $features as $feature ) { ?>

					<div class="heatbox">

						<h2>
							<img src="<?php echo esc_url( $feature['img'] ); ?>" alt="<?php echo esc_attr( $feature['title'] ); ?>">
							<?php echo esc_html( $feature['title'] ); ?>
						</h2>

							<div class="heatbox-content">
								<p>
									<?php echo wp_kses_post( $feature['text'] ); ?>
								</p>
							</div>

							<div class="feature-status">
								<div class="status">
									<span><?php esc_html_e( 'Status: ', 'ats-dashboard' ); ?></span>
									<span class="status-code" data-active-text="<?php esc_attr_e( 'Active', 'ats-dashboard' ); ?>" data-inactive-text="<?php esc_attr_e( 'Inactive', 'ats-dashboard' ); ?>">
									<?php echo empty( $saved_modules ) || 'true' === $saved_modules[ $feature['feature'] ] ? '<span class="active">' . esc_html__( 'Active', 'ats-dashboard' ) . '</span>' : '<span class="inactive">' . esc_html__( 'Inactive', 'ats-dashboard' ) . '</span>'; ?>
								</span>
								</div>
								<div class="status-switch">
									<label for="ats_is_active_<?php echo esc_attr( $feature['feature'] ); ?>" class="toggle-switch">
										<input
											type="checkbox"
											name="<?php echo esc_attr( $feature['feature'] ); ?>"
											id="ats_is_active_<?php echo esc_attr( $feature['feature'] ); ?>"
											<?php checked( empty( $saved_modules ) || 'true' === $saved_modules[ $feature['feature'] ] ); ?>
										/>
										<div class="switch-track">
											<div class="switch-thumb"></div>
										</div>
									</label>
								</div>
							</div>

						<input type="hidden" name="ats_module_nonce" id="ats_module_nonce" value="<?php echo esc_attr( wp_create_nonce( 'ats_module_nonce_action' ) ); ?>" />

					</div>

				<?php } ?>

				</div>

			</div>

		</form>

	</div>

	<?php

};
