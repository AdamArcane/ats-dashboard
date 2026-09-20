<?php
/**
 * CSS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_admin_bar() ) {

		wp_enqueue_style( 'select2', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/select2.min.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );
		wp_enqueue_style( 'dashicons-picker', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/dashicons-picker.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );
		wp_enqueue_style( 'heatbox', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/heatbox.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );
		wp_enqueue_style( 'ats-menu-builder', ATS_DASHBOARD_PLUGIN_URL . '/modules/admin-menu/assets/css/ats-menu-builder.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );
		wp_enqueue_style( 'ats-admin-bar-settings', $module->url . '/assets/css/admin-bar-settings.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

	}

};
