<?php
/**
 * CSS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_features() ) {

		// Heatbox.
		wp_enqueue_style( 'heatbox', ATS_DASHBOARD_CORE_URL . '/assets/css/heatbox.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		// Features.
		wp_enqueue_style( 'ats-features', ATS_DASHBOARD_CORE_URL . '/modules/feature/assets/css/features.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

	}

};
