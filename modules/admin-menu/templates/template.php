<?php
/**
 * Admin menu page template.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

$wp_roles   = wp_roles();
$role_names = $wp_roles->role_names;

$saved_menu      = get_option( 'ats_admin_menu', array() );
$saved_user_data = array();

foreach ( $saved_menu as $identifier => $menu_item ) {
	if ( false !== stripos( $identifier, 'user_id_' ) ) {
		$user_id   = absint( str_ireplace( 'user_id_', '', $identifier ) );
		$user_data = get_userdata( $user_id );

		array_push(
			$saved_user_data,
			array(
				'ID'           => $user_id,
				'display_name' => $user_data->display_name,
			)
		);
	}
}
?>

<div class="wrap heatbox-wrap ats-admin-menu ats-menu-builder-editor-page">

	<div class="heatbox-header heatbox-margin-bottom">

		<div class="heatbox-container heatbox-container-center">

			<div class="logo-container">

				<div>
					<span class="title">
						<?php echo esc_html( get_admin_page_title() ); ?>
						<span class="version"><?php echo esc_html( ATS_DASHBOARD_PLUGIN_VERSION ); ?></span>
					</span>
					<p class="subtitle"><?php esc_html_e( 'Fully customize the WordPress admin menu.', 'ats-dashboard' ); ?></p>
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
			<?php if ( ! ats_is_pro_active() ) : ?>

				<div class="ats-pro-upgrade-nag">
					<p><?php esc_html_e( 'This feature is available in ATS Dashboard.', 'ats-dashboard' ); ?></p>
					<a href="https://ats-dashboard.io/pro/?utm_source=plugin&utm_medium=admin_menu_link&utm_campaign=ats" class="button button-large button-primary" target="_blank">
						<?php esc_html_e( 'Get ATS Dashboard', 'ats-dashboard' ); ?>
					</a>
				</div>

			<?php endif; ?>

			<form action="options.php" method="post" class="ats-menu-builder--edit-form">

				<div class="heatbox ats-menu-builder-box">

					<div class="ats-menu-builder-box--header">
						<h2 class="ats-menu-builder-box--title">
							<?php esc_html_e( 'Admin Menu Editor', 'ats-dashboard' ); ?>
						</h2>
						<div class="ats-menu-builder-box--search-box is-hidden">
							<select name="ats_admin_menu_user_selector" id="ats_admin_menu_user_selector" class="ats-menu-builder--search-user" data-loading-msg="<?php esc_attr_e( 'Loading Users...', 'ats-dashboard' ); ?>" data-placeholder="<?php esc_attr_e( 'Select a User', 'ats-dashboard' ); ?>" disabled>
								<option value="">
									<?php esc_html_e( 'Loading Users...', 'ats-dashboard' ); ?>
								</option>
							</select>
						</div>
						<ul class="ats-menu-builder-box--header-tabs">
							<li class="ats-menu-builder-box--header-tab is-active" data-header-tab="roles">
								<a href="#roles-menu">
									<?php esc_html_e( 'Roles', 'ats-dashboard' ); ?>
								</a>
							</li>
							<li class="ats-menu-builder-box--header-tab" data-header-tab="users">
								<a href="#users-menu">
									<?php esc_html_e( 'Users', 'ats-dashboard' ); ?>
								</a>
							</li>
						</ul>
					</div>

					<div class="ats-menu-builder--tabs ats-menu-builder--role-tabs">
						<ul class="ats-menu-builder--tab-menu ats-menu-builder--role-menu">
							<?php foreach ( $role_names as $role_key => $role_name ) : ?>

								<li class="ats-menu-builder--tab-menu-item<?php echo ( 'administrator' === $role_key ? ' is-active' : '' ); ?>" data-ats-tab-content="ats-menu-builder--<?php echo esc_html( $role_key ); ?>-edit-area" data-role="<?php echo esc_attr( $role_key ); ?>">
									<button type="button">
										<?php echo esc_html( ucwords( $role_name ) ); ?>
									</button>
								</li>

							<?php endforeach; ?>
						</ul>

						<div class="ats-menu-builder--tab-content ats-menu-builder--edit-area">
							<?php foreach ( $role_names as $role_key => $role_name ) : ?>

								<div id="ats-menu-builder--<?php echo esc_attr( $role_key ); ?>-edit-area" class="ats-menu-builder--tab-content-item ats-menu-builder--workspace ats-menu-builder--role-workspace<?php echo ( 'administrator' === $role_key ? ' is-active' : '' ); ?>" data-role="<?php echo esc_attr( $role_key ); ?>">
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

							<?php endforeach; ?>
						</div><!-- .ats-menu-builder--tab-content -->
					</div><!-- .ats-menu-builder--role-tabs -->

					<div class="ats-menu-builder--tabs ats-menu-builder--user-tabs is-hidden">
						<ul class="ats-menu-builder--tab-menu ats-menu-builder--user-menu">
							<?php foreach ( $saved_user_data as $index => $user_data ) : ?>

								<li class="ats-menu-builder--tab-menu-item <?php echo ( 0 === $index ? ' is-active' : '' ); ?>" data-ats-tab-content="ats-menu-builder--user-<?php echo esc_html( $user_data['ID'] ); ?>-edit-area" data-user-id="<?php echo esc_html( $user_data['ID'] ); ?>">
									<button type="button">
										<?php echo esc_html( $user_data['display_name'] ); ?>
									</button>
									<i class="dashicons dashicons-no-alt delete-icon ats-menu-builder--remove-tab"></i>
								</li>

							<?php endforeach; ?>

							<!-- to be managed more via JS -->
						</ul>

						<div class="ats-menu-builder--tab-content ats-menu-builder--edit-area">
							<div id="ats-menu-builder--user-empty-edit-area" class="ats-menu-builder--tab-content-item ats-menu-builder--workspace ats-menu-builder--user-workspace <?php echo ( empty( $saved_user_data ) ? ' is-active' : '' ); ?>">
								<?php esc_html_e( 'No user selected.', 'ats-dashboard' ); ?>
							</div>

							<?php foreach ( $saved_user_data as $index => $user_data ) : ?>

								<div id="ats-menu-builder--user-<?php echo esc_html( $user_data['ID'] ); ?>-edit-area" class="ats-menu-builder--tab-content-item ats-menu-builder--workspace ats-menu-builder--user-workspace <?php echo ( 0 === $index ? ' is-active' : '' ); ?>" data-user-id="<?php echo esc_html( $user_data['ID'] ); ?>">
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

							<?php endforeach; ?>

							<!-- to be managed more via JS -->
						</div><!-- .ats-menu-builder--tab-content -->
					</div><!-- .ats-menu-builder--user-tabs -->

					<div class="heatbox-footer">

						<?php if ( ! ats_is_pro_active() ) : ?>

							<div class="ats-pro-settings-page-notice ats-pro-admin-menu-notice">
								<p><?php esc_html_e( 'This feature is available in ATS Dashboard.', 'ats-dashboard' ); ?></p>
								<a href="https://ats-dashboard.io/pro/?utm_source=plugin&utm_medium=admin_menu_link&utm_campaign=ats" class="button button-large button-primary" target="_blank">
									<?php esc_html_e( 'Get ATS Dashboard', 'ats-dashboard' ); ?>
								</a>
							</div>

						<?php endif; ?>

						<?php do_action( 'ats_admin_menu_form_footer' ); ?>

					</div>
				</div>

			</form>
		</div>

		<div class="heatbox-sidebar">
			<div class="heatbox tags-heatbox">
				<h2>
					<?php esc_html_e( 'Placeholder Tags', 'ats-dashboard' ); ?>
					<span class="action-status">📋 Copied</span>
				</h2>

				<div class="heatbox-content">
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
