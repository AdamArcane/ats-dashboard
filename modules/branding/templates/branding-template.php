<?php
/**
 * Branding page template.
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
						<p class="subtitle"><?php esc_html_e( 'Customization options admin area.', 'ats-dashboard' ); ?></p>
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

				<?php settings_fields( 'ats-branding-group' ); ?>

				<div class="atsui">
					<?php do_settings_sections( 'ats-branding-settings' ); ?>
				</div>

				<?php do_action( 'ats_after_branding_layout_metabox' ); ?>

				<div class="atsui">
					<?php do_settings_sections( 'ats-darkmode-settings' ); ?>
				</div>

				<?php do_action( 'ats_after_darkmode_metabox' ); ?>

				<div class="atsui">
					<?php
					do_settings_sections( 'ats-admin-colors-settings' );

				
						?>

						<div class="atsui-overlay"></div>

					
					<p>
						<button type="button" class="button button-secondary" id="ats-reset-branding-colors">
							<?php esc_html_e( 'Reset to defaults', 'ats-dashboard' ); ?>
						</button>
					</p>
				</div>

				<?php do_action( 'ats_after_admin_colors_metabox' ); ?>

				<div class="atsui">
					<?php
					do_settings_sections( 'ats-admin-logo-settings' );

				
						?>

						<div class="atsui-overlay"></div>

					
				</div>

				<?php do_action( 'ats_after_admin_logo_metabox' ); ?>

				<div class="atsui">
					<?php do_settings_sections( 'ats-branding-misc-settings' ); ?>
				</div>

				<?php do_action( 'ats_after_branding_misc_metabox' ); ?>

				<?php submit_button( '', 'button button-primary button-larger' ); ?>

			</div>

		</form>

	</div>

	<?php
};
