<?php
/**
 * Plugin onboarding page template.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Setup;

return function ( $referrer = '' ) {

	$ats_core      = new Setup();
	$saved_modules = $ats_core->saved_modules();

	$modules = array(
		array(
			'module' => 'login_customizer',
			'title'  => __( 'Login Customizer', 'ats-dashboard' ),
		),
		array(
			'module' => 'white_label',
			'title'  => __( 'White Label', 'ats-dashboard' ),
		),
		array(
			'module' => 'login_redirect',
			'title'  => __( 'Login Redirect', 'ats-dashboard' ),
		),
		array(
			'module' => 'admin_pages',
			'title'  => __( 'Admin Pages', 'ats-dashboard' ),
		),
		array(
			'module' => 'admin_menu_editor',
			'title'  => __( 'Admin Menu Editor', 'ats-dashboard' ),
		),
		array(
			'module' => 'admin_bar_editor',
			'title'  => __( 'Admin Bar Editor', 'ats-dashboard' ),
		),
	)

	?>

	<div class="wrap heatbox-wrap ats-onboarding-page" data-ats-referrer="<?php echo esc_attr( $referrer ); ?>">

		<div class="heatbox-header heatbox-margin-bottom">

			<div class="heatbox-container heatbox-container-center">

				<div class="logo-container">

					<div style="width: 80%">
						<span class="title">
							<?php esc_html_e( 'Welcome to ATS Dashboard', 'ats-dashboard' ); ?>
						</span>
						<p class="subtitle">
							<?php echo wp_kses_post( __( 'Complete the 1-Click Setup & get an <strong style="font-weight: 700; color: tomato;">exclusive Discount</strong> on <strong>ATS Dashboard!</strong>', 'ats-dashboard' ) ); ?>							
						</p>
					</div>

					<div style="width: 20%">
						<img src="<?php echo esc_url( ATS_DASHBOARD_CORE_URL ); ?>/assets/img/logo.png">
					</div>

				</div>

			</div>

		</div>

		<div class="heatbox-container heatbox-container-center">
			<h1 style="display: none;"></h1>

			<div class="heatbox onboarding-heatbox">

				<div class="ats-onboarding-slides">
					<div class="ats-onboarding-slide ats-modules-slide">

						<header>
							<img src="<?php echo esc_url( ATS_DASHBOARD_CORE_URL ); ?>/modules/plugin-onboarding/assets/images/undraw_reviewed_docs_re_9lmr.svg" alt="ATS Dashboard Features" class="ats-illustration module-illustration">

							<h2>
								<?php esc_html_e( '1 Click Setup', 'ats-dashboard' ); ?>
							</h2>

							<p>
								<?php esc_html_e( 'Choose what features you would like to enable/disable. You can always manage this later from the Modules page.', 'ats-dashboard' ); ?>
							</p>
						</header>


						<ul class="ats-modules">
							<?php foreach ( $modules as $module ) : ?>
								<?php
								$slug          = $module['module'];
								$title         = $module['title'];
								$disabled_attr = '';
								$is_checked    = true;

								if ( 'erident' === $referrer ) {
									$disabled_attr = 'login_customizer' === $slug ? 'disabled' : $disabled_attr;
								}

								if ( isset( $saved_modules[ $slug ] ) && 'false' === $saved_modules[ $slug ] ) {
									$is_checked = false;

									if ( 'login_customizer' === $slug ) {
										$disabled_attr = '';
									}
								}
								?>

								<li>
									<div class="module-text">
										<h3>
											<label for="ats_modules__<?php echo esc_attr( $slug ); ?>">
												<?php echo esc_html( $title ); ?>
											</label>
										</h3>
									</div>
									<div class="module-toggle">
										<label for="ats_modules__<?php echo esc_attr( $slug ); ?>" class="label checkbox-label">
											<input
												type="checkbox"
												name="ats_modules[<?php echo esc_attr( $slug ); ?>]"
												id="ats_modules__<?php echo esc_attr( $slug ); ?>"
												value="1"
												<?php checked( $is_checked, 1 ); ?>
												<?php echo esc_attr( $disabled_attr ); ?>
											>

											<div class="indicator"></div>
										</label>
									</div>
								</li>

							<?php endforeach; ?>
						</ul>

					</div>
					<div class="ats-onboarding-slide ats-subscription-slide">

						<header>
							<img src="<?php echo esc_url( ATS_DASHBOARD_CORE_URL ); ?>/modules/plugin-onboarding/assets/images/undraw_discount_d-4-bd.svg" alt="ATS Dashboard Features" class="ats-illustration subscription-illustration">

							<h2>
								<?php esc_html_e( 'Exclusive Discount 🥳', 'ats-dashboard' ); ?>
							</h2>

							<p>
							<?php
							/* translators: %1$s: Referrer name */
							echo wp_kses_post( sprintf( __( 'We are offering all <strong>%1$s users an exclusive Discount</strong> on ATS Dashboard. Subscribe to our Newsletter & get your discount.', 'ats-dashboard' ), esc_attr( ucwords( $referrer ) ) ) );
							?>
						</p>
						</header>

						<div class="ats-subscription-form">
							<div class="ats-form-row">
								<input type="text" name="ats_subscription_name" id="ats-subscription-name" class="ats-input" placeholder="Name">
							</div>
							<div class="ats-form-row">
								<input type="text" name="ats_subscription_email" id="ats-subscription-email" class="ats-input" placeholder="Email">
							</div>
							<div class="ats-form-row">
								<button type="button" class="button button-primary button-large ats-button subscribe-button">
									<?php esc_html_e( 'Subscribe', 'ats-dashboard' ); ?>
								</button>
							</div>
							<div class="ats-form-row ats-skip-discount">
								<a href="">
									<?php esc_html_e( 'No, I don\'t want any Discount :/', 'ats-dashboard' ); ?>
								</a>
							</div>
						</div>

					</div>

					<div class="ats-onboarding-slide ats-finished-slide">

						<header>
							<h2>
								<?php esc_html_e( 'Setup Complete! 🎉', 'ats-dashboard' ); ?>
							</h2>

							<p data-ats-show-on="subscribe"> 
								<?php echo wp_kses_post( __( 'We\'ll send you an email with a <strong> discount code for ATS Dashboard </strong> shortly.', 'ats-dashboard' ) ); ?>
							</p>

							<p>
								<?php echo wp_kses_post( __( 'What\'s next? Explore all features from the <strong>"Arcane Tech"</strong> admin menu.', 'ats-dashboard' ) ); ?>
							</p>

							<p data-ats-show-on="skip-discount">
								<strong><?php esc_html_e( 'This is your last chance to get an exclusive discount on ATS Dashboard at the link below! 👇👇👇', 'ats-dashboard' ); ?></strong>
							</p>
						</header>

						<div class="finish-button-wrapper">
							<a target="_blank" href="https://ats-dashboard.io/special-discount/" class="button button-primary finish-button">
								<?php esc_html_e( 'Grab your Discount', 'ats-dashboard' ); ?>
							</a>
						</div>

					</div>
				</div>

				<footer class="heatbox-footer">
					<div class="heatbox-footer-item">
						<button type="button" class="button button-large ats-button skip-button">
							<?php esc_html_e( 'Skip', 'ats-dashboard' ); ?>
						</button>
					</div>
					<div class="heatbox-footer-item">
						<div class="ats-dots"></div>
					</div>
					<div class="heatbox-footer-item">
						<button type="button" class="button button-large button-primary ats-button save-button">
							<?php esc_html_e( 'Done', 'ats-dashboard' ); ?>
						</button>
					</div>
				</footer>

				<div class="ats-discount-notif is-hidden">
				<?php
				/* translators: %1$s: Referrer name */
				echo wp_kses_post( sprintf( __( 'This is an exclusive discount for %1$s users.<br> <strong>This discount will not come back!</strong>', 'ats-dashboard' ), esc_attr( ucwords( $referrer ) ) ) );
				?>
			</div>

			</div>

		</div>

	</div>

	<?php

};
