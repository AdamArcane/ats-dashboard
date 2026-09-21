<?php
/**
 * CSS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( ! is_admin_bar_showing() ) {
		return;
	}

	wp_enqueue_style( 'ats-notice-bell', $module->url . '/assets/css/notice-bell.css', array( 'admin-bar' ), ATS_DASHBOARD_PLUGIN_VERSION );

};
