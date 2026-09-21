<?php
/**
 * Email notifications page template.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\EmailNotifications\Email_Notifications_Module;

return function () {

	$module = new Email_Notifications_Module();
	$types  = $module->get_email_types();

	$tab_menus = array(
		array(
			'id'     => 'global',
			'text'   => __( 'General Template', 'ats-dashboard' ),
			'active' => true,
		),
	);

	foreach ( $types as $email_key => $email_type ) {
		$tab_menus[] = array(
			'id'   => $email_key,
			'text' => $email_type['label'],
		);
	}
	?>

	<div class="wrap heatbox-wrap ats-email-notifications-page">

		<div class="heatbox-header heatbox-has-tab-nav heatbox-margin-bottom">

			<div class="heatbox-container heatbox-container-center">

				<div class="logo-container">

					<div>
						<span class="title">
							<?php echo esc_html( get_admin_page_title() ); ?>
						</span>
						<p class="subtitle"><?php esc_html_e( 'Customize the branded template & content used for automated emails.', 'ats-dashboard' ); ?></p>
					</div>

					<div>
						<img src="<?php echo esc_url( ATS_DASHBOARD_PLUGIN_URL ); ?>/assets/img/logo.png">
					</div>

				</div>

				<nav>
					<ul class="heatbox-tab-nav">
						<?php foreach ( $tab_menus as $tab ) : ?>
							<li class="heatbox-tab-nav-item <?php echo esc_attr( $tab['id'] ); ?>-panel<?php echo ! empty( $tab['active'] ) ? ' active' : ''; ?>">
								<a href="#<?php echo esc_attr( $tab['id'] ); ?>"><?php echo esc_html( $tab['text'] ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>

			</div>

		</div>

		<div class="heatbox-container heatbox-container-center">

			<h1 style="display: none;"></h1>

			<form method="post" action="options.php" class="ats-email-notifications-form">

				<?php settings_fields( 'ats-email-notifications-group' ); ?>

				<div class="heatbox-admin-panel ats-global-panel" style="display:block;">
					<div class="heatbox">
						<?php do_settings_sections( 'ats-email-notifications-global-settings' ); ?>
					</div>
				</div>

				<?php foreach ( $types as $email_key => $email_type ) : ?>
					<div class="heatbox-admin-panel ats-<?php echo esc_attr( $email_key ); ?>-panel">
						<div class="heatbox">

							<?php
							$preview_url = wp_nonce_url(
								add_query_arg(
									array(
										'action' => 'ats_preview_notification_email',
										'email'  => $email_key,
									),
									admin_url( 'admin-post.php' )
								),
								'ats_preview_notification_email'
							);
							?>

							<p>
								<a href="<?php echo esc_url( $preview_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-secondary">
									<?php esc_html_e( 'Preview With Sample Data', 'ats-dashboard' ); ?>
								</a>
							</p>

							<?php do_settings_sections( 'ats-email-notifications-' . $email_key . '-settings' ); ?>
						</div>
					</div>
				<?php endforeach; ?>

				<?php submit_button( '', 'button button-primary button-larger' ); ?>

			</form>

		</div>

	</div>

	<?php

};
