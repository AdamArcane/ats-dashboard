<?php
/**
 * JS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_features() ) {

		// Features.
		wp_enqueue_script( 'ats-features', ATS_DASHBOARD_PLUGIN_URL . '/modules/feature/assets/js/feature.js', array( 'jquery', 'wp-escape-html' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

	}

};
