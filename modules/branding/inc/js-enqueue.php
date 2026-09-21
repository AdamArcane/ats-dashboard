<?php
/**
 * JS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	$is_branding_page = $module->screen()->is_branding() || ( isset( $_GET['page'] ) && 'ats_branding' === $_GET['page'] );

	if ( $is_branding_page ) {

		wp_enqueue_script( 'wp-color-picker' );

		// CodeMirror, for the Custom CSS tab.
		wp_enqueue_code_editor( array( 'type' => 'text/html' ) );

		wp_enqueue_script( 'ats-settings', ATS_DASHBOARD_PLUGIN_URL . '/assets/js/settings.js', array( 'jquery', 'wp-color-picker' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		// Branding settings.
		wp_enqueue_script( 'ats-pro-branding-instant-preview', ATS_DASHBOARD_PLUGIN_URL . '/modules/branding/assets/js/instant-preview.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

	}

};
