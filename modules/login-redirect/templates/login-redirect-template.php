<?php
/**
 * Login redirect page template.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {
	?>

	<div class="wrap atsui-wrap ats-branding-page">

		<div class="atsui-header atsui-margin-bottom">

			<div class="atsui-container atsui-container-center">

				<div class="logo-container">

					<div>
						<span class="title">
							<?php echo esc_html( get_admin_page_title() ); ?>
						</span>
						<p class="subtitle"><?php esc_html_e( 'Customize login redirect settings.', 'ats-dashboard' ); ?></p>
					</div>

					<div>
						<img src="<?php echo esc_url( ATS_DASHBOARD_PLUGIN_URL ); ?>/assets/img/logo.png">
					</div>

				</div>

			</div>

		</div>

		<form method="post" action="options.php">

			<div class="atsui-container atsui-container-center">

				<h1 style="display: none;"></h1>

				<?php settings_fields( 'ats-login-redirect-group' ); ?>

				<div class="atsui">
					<?php do_settings_sections( 'ats-login-url-settings' ); ?>
				</div>

				<div class="atsui ats-login-redirect-atsui">
					<?php do_settings_sections( 'ats-login-redirect-settings' ); ?>
				</div>

				<?php submit_button( '', 'button button-primary button-larger' ); ?>

			</div>

		</form>

	</div>

	<?php
};
