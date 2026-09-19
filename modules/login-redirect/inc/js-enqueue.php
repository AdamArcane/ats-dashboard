<?php
/**
 * JS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_login_redirect() ) {

		wp_enqueue_script( 'ats-pro-login-redirect', $module->url . '/assets/js/login-redirect.js', array( 'ats-login-redirect' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

	}

};
