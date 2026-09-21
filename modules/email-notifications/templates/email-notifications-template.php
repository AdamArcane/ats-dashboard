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
		array(
			'id'   => 'emails',
			'text' => __( 'Emails', 'ats-dashboard' ),
		),
	);
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

				<div class="heatbox-admin-panel ats-emails-panel">
					<div class="heatbox">
						<?php
						$emails_list = require __DIR__ . '/partials/emails-list.php';
						$emails_list( $module, $types );
						?>
					</div>
				</div>

				<?php
				// Rendered inside the form (not after it) so each popup's
				// subject/heading/body/button fields still POST with the
				// rest of the settings when "Save Changes" is clicked.
				$modals = require __DIR__ . '/partials/email-modals.php';
				$modals( $module, $types );
				?>

				<?php submit_button( '', 'button button-primary button-larger' ); ?>

			</form>

		</div>

	</div>

	<?php

};
