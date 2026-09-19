<?php
/**
 * JS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_tools() ) {

		wp_enqueue_script( 'ats-tools', $module->url . '/assets/js/tools.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

	}

};
