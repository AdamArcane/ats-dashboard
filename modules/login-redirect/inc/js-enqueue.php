<?php
/**
 * JS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_login_redirect() ) {

		// Select2.
		wp_enqueue_script( 'select2', ATS_DASHBOARD_PLUGIN_URL . '/assets/js/select2.min.js', array( 'jquery' ), '4.1.0-rc.0', true );

		wp_enqueue_script( 'ats-login-redirect', $module->url . '/assets/js/login-redirect.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		$inline_script = '
			var atsLoginRedirect = {};
		';

		wp_add_inline_script( 'ats-login-redirect', $inline_script, 'before' );

	}

};
