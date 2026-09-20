<?php
/**
 * CSS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_integrations() ) {

		// Heatbox.
		wp_enqueue_style( 'heatbox', ATS_DASHBOARD_CORE_URL . '/assets/css/heatbox.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		// Integrations page.
		wp_enqueue_style( 'ats-integrations', $module->url . '/assets/css/integrations.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

	}

};
