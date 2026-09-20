<?php
/**
 * CSS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_admin_menu() ) {

		wp_enqueue_style( 'select2', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/select2.min.css', array(), '4.1.0-rc.0' );
		wp_enqueue_style( 'dashicons-picker', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/dashicons-picker.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );
		wp_enqueue_style( 'heatbox', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/heatbox.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );
		wp_enqueue_style( 'ats-menu-builder', $module->url . '/assets/css/ats-menu-builder.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

	}

};
