<?php
/**
 * Admin menu page template.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
?>

<div class="wrap atsui-wrap ats-admin-menu ats-menu-builder-editor-page">

	<div class="atsui-header atsui-margin-bottom">

		<div class="atsui-container atsui-container-center">

			<div class="logo-container">

				<div>
					<span class="title">
						<?php echo esc_html( get_admin_page_title() ); ?>
						<span class="version"><?php echo esc_html( ATS_DASHBOARD_PLUGIN_VERSION ); ?></span>
					</span>
					<p class="subtitle"><?php esc_html_e( 'Fully customize the WordPress admin menu.', 'ats-dashboard' ); ?></p>
				</div>

				<div>
					<img src="<?php echo esc_url( ATS_DASHBOARD_PLUGIN_URL ); ?>/assets/img/logo.png">
				</div>

			</div>

		</div>

	</div>

	<div class="atsui-container atsui-container-center">
		<h1 style="display: none;"></h1>
	</div>

	<div class="atsui-container atsui-container-center atsui-column-container">

		<div class="atsui-main">

			

			<form action="options.php" method="post" class="ats-menu-builder--edit-form">

				<div class="atsui ats-menu-builder-box">

					<div class="ats-menu-builder-box--header">
						<h2 class="ats-menu-builder-box--title">
							<?php esc_html_e( 'Admin Menu Editor', 'ats-dashboard' ); ?>
						</h2>
					</div>

					<div class="ats-menu-builder--tabs ats-menu-builder--role-tabs">
						<div class="ats-menu-builder--tab-content ats-menu-builder--edit-area">
							<div id="ats-menu-builder--default-edit-area" class="ats-menu-builder--tab-content-item ats-menu-builder--workspace ats-menu-builder--role-workspace is-active" data-role="default">
								<ul class="ats-menu-builder--menu-list ats-menu-builder-sortable">
									<!-- to be re-written via js -->
									<li class="loading"></li>
								</ul>

								<div class="ats-menu-builder--inline-buttons">
									<?php
									do_action( 'ats_admin_menu_add_menu_button' );
									do_action( 'ats_admin_menu_add_separator_button' );
									?>
								</div>
							</div>
						</div><!-- .ats-menu-builder--tab-content -->
					</div><!-- .ats-menu-builder--role-tabs -->

					<div class="atsui-footer">
						<?php do_action( 'ats_admin_menu_form_footer' ); ?>
					</div>

				</div>

			</form>
		</div>

		<div class="atsui-sidebar">
			<div class="atsui tags-atsui">
				<h2>
					<?php esc_html_e( 'Placeholder Tags', 'ats-dashboard' ); ?>
					<span class="action-status">📋 Copied</span>
				</h2>

				<div class="atsui-content">
					<p>
						<?php esc_html_e( 'Use the placeholder tags below to display certain information dynamically.', 'ats-dashboard' ); ?>
						<br><strong><?php esc_html_e( '(Click to copy)', 'ats-dashboard' ); ?></strong>
					</p>
					<div class="tags-wrapper">
						<?php
						$placeholder_tags = [
							'{site_name}',
							'{site_url}',
						];

						$placeholder_tags = apply_filters( 'ats_admin_menu_placeholder_tags', $placeholder_tags );
						$total_tags       = count( $placeholder_tags );

						foreach ( $placeholder_tags as $tag_index => $placeholder_tag ) {
							?>
							<code><?php echo esc_attr( $placeholder_tag ); ?></code>
							<?php
						}
						?>
					</div>
				</div>
			</div>

			<?php do_action( 'ats_admin_menu_sidebar' ); ?>
		</div>

	</div>

</div>
