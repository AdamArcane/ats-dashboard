<?php
/**
 * JS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_plugin_onboarding() ) {

		wp_enqueue_script( 'tiny-slider', ATS_DASHBOARD_PLUGIN_URL . '/modules/plugin-onboarding/assets/js/tiny-slider.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		// Plugin onboarding.
		wp_enqueue_script( 'ats-plugin-onboarding', ATS_DASHBOARD_PLUGIN_URL . '/modules/plugin-onboarding/assets/js/plugin-onboarding.js', array( 'tiny-slider' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		wp_localize_script(
			'ats-plugin-onboarding',
			'atsPluginOnboarding',
			array(
				'adminUrl' => admin_url(),
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'nonces'   => [
					'saveModules'  => wp_create_nonce( 'ats_plugin_onboarding_save_modules_nonce' ),
					'subscribe'    => wp_create_nonce( 'ats_plugin_onboarding_subscribe_nonce' ),
					'skipDiscount' => wp_create_nonce( 'ats_plugin_onboarding_skip_discount_nonce' ),
				],
			)
		);

	}

};
