<?php
/**
 * JS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_integrations() ) {

		wp_enqueue_script( 'ats-integrations', $module->url . '/assets/js/integrations.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

	}

};
