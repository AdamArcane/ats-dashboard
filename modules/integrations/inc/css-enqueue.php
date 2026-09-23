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

	}

	if ( $module->screen()->is_integrations() || $module->screen()->is_dashboard() ) {

		// Integrations page and the System Info dashboard widget.
		wp_enqueue_style( 'ats-integrations', $module->url . '/assets/css/integrations.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

	}

};
