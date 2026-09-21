<?php
/**
 * JS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_email_notifications() ) {

		wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_script( 'ats-email-notifications', $module->url . '/assets/js/email-notifications.js', array( 'jquery', 'wp-color-picker' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

	}

};
