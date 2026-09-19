<?php
/**
 * JS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_settings() ) {

		// Color pickers.
		wp_enqueue_script( 'wp-color-picker' );

		// CodeMirror.
		wp_enqueue_code_editor( array( 'type' => 'text/html' ) );

		// Settings page.
		wp_enqueue_script( 'ats-settings', ATS_DASHBOARD_CORE_URL . '/assets/js/settings.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

	}

};
