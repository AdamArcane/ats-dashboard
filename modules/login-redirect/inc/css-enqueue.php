<?php
/**
 * CSS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_login_redirect() ) {

		// Select2.
		wp_enqueue_style( 'select2', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/select2.min.css', array(), '4.1.0-rc.0' );

		// Atsui.
		wp_enqueue_style( 'atsui', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/atsui.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		// Login redirect page.
		wp_enqueue_style( 'ats-login-redirect', $module->url . '/assets/css/login-redirect.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

	}

};
