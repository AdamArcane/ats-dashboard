<?php
/**
 * CSS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_wizard() ) {

		// Select2 CSS.
		wp_enqueue_style( 'select2', ATS_DASHBOARD_CORE_URL . '/assets/css/select2.min.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		// Heatbox.
		wp_enqueue_style( 'heatbox', ATS_DASHBOARD_CORE_URL . '/assets/css/heatbox.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		// Tiny slider.
		wp_enqueue_style( 'tiny-slider', ATS_DASHBOARD_CORE_URL . '/assets/css/tiny-slider.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		// Onboarding Wizard.
		wp_enqueue_style( 'ats-wizard', ATS_DASHBOARD_CORE_URL . '/modules/onboarding-wizard/assets/css/onboarding-wizard.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

	}

};
