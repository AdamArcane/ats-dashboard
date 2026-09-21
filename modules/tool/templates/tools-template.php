<?php
/**
 * Tools template.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {
	?>

	<div class="wrap heatbox-wrap ats-tools-page">

		<div class="heatbox-header heatbox-margin-bottom">

			<div class="heatbox-container heatbox-container-center">

				<div class="logo-container">

					<div>
						<span class="title">
							<?php echo esc_html( get_admin_page_title() ); ?>
ats
						</span>
						<p class="subtitle"><?php esc_html_e( 'Export & import the ATS Dashboard settings.', 'ats-dashboard' ); ?></p>
					</div>

					<div>
						<img src="<?php echo esc_url( ATS_DASHBOARD_PLUGIN_URL ); ?>/assets/img/logo.png">
					</div>

				</div>

			</div>

		</div>

		<div class="heatbox-container heatbox-container-center">

			<h1 style="display: none;"></h1>

			<?php settings_errors(); ?>

			<div class="ats-tools-container">

				<div class="heatbox">
					<form method="post" action="options.php">
					<?php
					settings_fields( 'ats-export-group' );
					do_settings_sections( 'ats-dashboard-export' );
					submit_button( __( 'Export File', 'ats-dashboard' ) );
					?>
					</form>
				</div>

				<div class="heatbox">
					<form method="post" action="options.php" enctype="multipart/form-data">
					<?php
					settings_fields( 'ats-import-group' );
					do_settings_sections( 'ats-dashboard-import' );
					submit_button( __( 'Import File', 'ats-dashboard' ) );
					?>
					</form>
				</div>

			</div>

		</div>

	</div>

	<?php
};
