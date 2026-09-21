<?php
/**
 * CSS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_email_notifications() ) {

		wp_enqueue_style( 'wp-color-picker' );

		// Heatbox.
		wp_enqueue_style( 'heatbox', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/heatbox.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		// Email notifications page.
		wp_enqueue_style( 'ats-email-notifications', $module->url . '/assets/css/email-notifications.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

	}

};
