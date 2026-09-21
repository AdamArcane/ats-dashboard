<?php
/**
 * CSS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_settings() ) {

		// Color pickers.
		wp_enqueue_style( 'wp-color-picker' );

		// Atsui.
		wp_enqueue_style( 'atsui', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/atsui.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		// Settings page.
		wp_enqueue_style( 'ats-settings', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/settings.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

	}

};
