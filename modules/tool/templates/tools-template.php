<?php
/**
 * Tools template.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {
	?>

	<div class="wrap atsui-wrap ats-tools-page">

		<div class="atsui-header atsui-margin-bottom">

			<div class="atsui-container atsui-container-center">

				<div class="logo-container">

					<div>
						<span class="title">
							<?php echo esc_html( get_admin_page_title() ); ?>
						</span>
						<p class="subtitle"><?php esc_html_e( 'Export & import the ATS Dashboard settings.', 'ats-dashboard' ); ?></p>
					</div>

					<div>
						<img src="<?php echo esc_url( ATS_DASHBOARD_PLUGIN_URL ); ?>/assets/img/logo.png">
					</div>

				</div>

			</div>

		</div>

		<div class="atsui-container atsui-container-center">

			<h1 style="display: none;"></h1>

			<?php settings_errors(); ?>

			<div class="ats-tools-container">

				<div class="atsui">
					<form method="post" action="options.php">
					<?php
					settings_fields( 'ats-export-group' );
					do_settings_sections( 'ats-dashboard-export' );
					submit_button( __( 'Export File', 'ats-dashboard' ) );
					?>
					</form>
				</div>

				<div class="atsui">
					<form method="post" action="options.php" enctype="multipart/form-data">
					<?php
					settings_fields( 'ats-import-group' );
					do_settings_sections( 'ats-dashboard-import' );
					submit_button( __( 'Import File', 'ats-dashboard' ) );
					?>
					</form>
				</div>

				<div class="atsui">
					<form method="post" action="options.php" onsubmit="return confirm('<?php echo esc_js( __( 'Are you sure you want to reset all ATS Dashboard settings? This cannot be undone.', 'ats-dashboard' ) ); ?>');">
					<?php
					settings_fields( 'ats-reset-group' );
					do_settings_sections( 'ats-dashboard-reset' );
					submit_button( __( 'Reset to Defaults', 'ats-dashboard' ), 'delete' );
					?>
					</form>
				</div>

			</div>

		</div>

	</div>

	<?php
};
