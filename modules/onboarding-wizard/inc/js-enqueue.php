<?php
/**
 * JS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_wizard() ) {

		wp_enqueue_script( 'tiny-slider', ATS_DASHBOARD_CORE_URL . '/modules/plugin-onboarding/assets/js/tiny-slider.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		// Select2 JS.
		wp_enqueue_script( 'select2', ATS_DASHBOARD_CORE_URL . '/assets/js/select2.min.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		// Onboarding Wizard JS.
		wp_enqueue_script( 'ats-onboarding-wizard', ATS_DASHBOARD_CORE_URL . '/modules/onboarding-wizard/assets/js/onboarding-wizard.js', array( 'tiny-slider', 'select2' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		wp_localize_script(
			'ats-onboarding-wizard',
			'atsOnboardingWizard',
			array(
				'adminUrl' => admin_url(),
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'nonces'   => [
					'saveModules'         => wp_create_nonce( 'ats_onboarding_wizard_save_modules_nonce' ),
					'saveWidgets'         => wp_create_nonce( 'ats_onboarding_wizard_save_widgets_nonce' ),
					'saveGeneralSettings' => wp_create_nonce( 'ats_onboarding_wizard_save_general_settings_nonce' ),
					'saveCustomLoginUrl'  => wp_create_nonce( 'ats_onboarding_wizard_save_custom_login_url_nonce' ),
					'subscribe'           => wp_create_nonce( 'ats_onboarding_wizard_subscribe_nonce' ),
					'skipDiscount'        => wp_create_nonce( 'ats_onboarding_wizard_skip_discount_nonce' ),
				],
			)
		);

	}

};
