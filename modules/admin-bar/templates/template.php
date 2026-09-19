<?php
/**
 * Admin menu page template.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\AdminBar\Admin_Bar_Module;
use ats\Vars;

/**
 * This function is being called in class-admin-bar-module.php.
 *
 * @param Admin_Bar_Module $module
 */
return function ( $module ) {

	$existing_menu = Vars::get( 'existing_admin_bar_menu' );
	$existing_menu = $module->nodes_to_array( $existing_menu );

	$saved_menu  = get_option( 'ats_admin_bar', array() );
	$saved_menu  = apply_filters( 'ats_ms_admin_bar_saved_menu', $saved_menu );
	$parsed_menu = ! $saved_menu ? $existing_menu : $module->parse_menu( $saved_menu, $existing_menu );
	$parsed_menu = $module->parse_frontend_items( $parsed_menu );

	// error_log( "existingMenu:\n" . print_r( $existing_menu, true ) );

	// error_log( "parsedMenu:\n" . print_r( $parsed_menu, true ) );

	// error_log( "builderItems:\n" . print_r( $module->to_builder_format( $parsed_menu ), true ) );

	wp_localize_script(
		'ats-admin-bar',
		'atsAdminBarBuilder',
		array(
			'existingMenu' => $existing_menu,
			'parsedMenu'   => $parsed_menu,
			'builderItems' => $module->to_builder_format( $parsed_menu ),
		)
	);

	?>

	<div class="wrap heatbox-wrap ats-admin-bar ats-menu-builder-editor-page">

		<div class="heatbox-header heatbox-margin-bottom">

			<div class="heatbox-container heatbox-container-center">

				<div class="logo-container">

					<div>
						<span class="title">
							<?php echo esc_html( get_admin_page_title() ); ?>
							<span class="version"><?php echo esc_html( ATS_DASHBOARD_PLUGIN_VERSION ); ?></span>
						</span>
						<p class="subtitle"><?php esc_html_e( 'Fully customize the WordPress admin bar.', 'ats-dashboard' ); ?></p>
					</div>

					<div>
						<img src="<?php echo esc_url( ATS_DASHBOARD_CORE_URL ); ?>/assets/img/logo.png">
					</div>

				</div>

			</div>

		</div>

		<div class="heatbox-container heatbox-container-center">
			<h1 style="display: none;"></h1>
		</div>

		<div class="heatbox-container heatbox-container-center heatbox-column-container">
			<div class="heatbox-main">



				<?php do_action( 'ats_admin_bar_before_form' ); ?>

				<form action="options.php" method="post" class="ats-menu-builder--edit-form">
	
					<div class="heatbox heatbox-admin-panel ats-menu-builder-box ats-menu-builder-box-panel">

						<div class="ats-menu-builder-box--header">
							<h2 class="ats-menu-builder-box--title">
								<?php esc_html_e( 'Admin Bar Editor', 'ats-dashboard' ); ?>
							</h2>

							<?php do_action( 'ats_admin_bar_header' ); ?>
						</div>

						<div class="ats-menu-builder--edit-area">
							<div id="ats-menu-builder--workspace" class="ats-menu-builder--workspace">
								<ul class="ats-menu-builder--menu-list" data-menu-type="parent">
									<!-- to be re-written via js -->
									<li class="loading"></li>
								</ul>

								<?php do_action( 'ats_admin_bar_add_menu_button' ); ?>
							</div>
						</div>

						<div class="heatbox-footer">


								<div class="ats-pro-settings-page-notice ats-pro-admin-bar-notice">
									<p><?php esc_html_e( 'This feature is available in ATS Dashboard.', 'ats-dashboard' ); ?></p>
									<a href="https://ats-dashboard.io/pro/?utm_source=plugin&utm_medium=admin_bar_link&utm_campaign=ats" class="button button-large button-primary" target="_blank">
										<?php esc_html_e( 'Get ATS Dashboard', 'ats-dashboard' ); ?>
									</a>
								</div>


							<?php do_action( 'ats_admin_bar_form_footer' ); ?>

						</div>
					</div>

				</form>
			</div>
			<div class="heatbox-sidebar">
				<?php
				require_once __DIR__ . '/metaboxes/remove-admin-bar-metabox.php';
				require_once __DIR__ . '/metaboxes/placeholder-tags-metabox.php';
				?>

				<?php do_action( 'ats_admin_bar_sidebar' ); ?>
			</div>
		</div>

	</div>

	<?php
};
