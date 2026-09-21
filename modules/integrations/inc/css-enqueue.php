<?php
/**
 * CSS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_integrations() ) {

		// Atsui.
		wp_enqueue_style( 'atsui', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/atsui.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		// Integrations page.
		wp_enqueue_style( 'ats-integrations', $module->url . '/assets/css/integrations.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

	}

};
