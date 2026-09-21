<?php
/**
 * CSS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_tools() ) {

		// Atsui.
		wp_enqueue_style( 'atsui', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/atsui.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		// Tools page.
		wp_enqueue_style( 'ats-tools', $module->url . '/assets/css/tools.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

	}

};
