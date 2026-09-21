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

	<div class="wrap atsui-wrap ats-email-notifications-page">

		<div class="atsui-header atsui-has-tab-nav atsui-margin-bottom">

			<div class="atsui-container atsui-container-center">

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
					<ul class="atsui-tab-nav">
						<?php foreach ( $tab_menus as $tab ) : ?>
							<li class="atsui-tab-nav-item <?php echo esc_attr( $tab['id'] ); ?>-panel<?php echo ! empty( $tab['active'] ) ? ' active' : ''; ?>">
								<a href="#<?php echo esc_attr( $tab['id'] ); ?>"><?php echo esc_html( $tab['text'] ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>

			</div>

		</div>

		<div class="atsui-container atsui-container-center">

			<h1 style="display: none;"></h1>

			<form method="post" action="options.php" class="ats-email-notifications-form">

				<?php settings_fields( 'ats-email-notifications-group' ); ?>

				<div class="atsui-admin-panel ats-global-panel" style="display:block;">
					<div class="atsui">
						<?php do_settings_sections( 'ats-email-notifications-global-settings' ); ?>
					</div>
				</div>

				<div class="atsui-admin-panel ats-emails-panel">
					<div class="atsui">
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
