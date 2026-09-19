<?php
/**
 * CSS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_plugin_onboarding() ) {

		// Heatbox.
		wp_enqueue_style( 'heatbox', ATS_DASHBOARD_CORE_URL . '/assets/css/heatbox.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		// Tiny slider.
		wp_enqueue_style( 'tiny-slider', ATS_DASHBOARD_CORE_URL . '/assets/css/tiny-slider.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		// Plugin onboarding.
		wp_enqueue_style( 'ats-plugin-onboarding', ATS_DASHBOARD_CORE_URL . '/modules/plugin-onboarding/assets/css/plugin-onboarding.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

	}

};
