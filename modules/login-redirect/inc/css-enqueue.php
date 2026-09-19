<?php
/**
 * CSS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_login_redirect() ) {

		wp_enqueue_style( 'ats-pro-login-redirect', $module->url . '/assets/css/login-redirect.css', array( 'ats-login-redirect' ), ATS_DASHBOARD_PLUGIN_VERSION );

	}

};
