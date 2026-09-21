<?php
/**
 * JS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_new_admin_page() || $module->screen()->is_edit_admin_page() ) {

		// Select2 (User Role Access field).
		wp_enqueue_script( 'select2', ATS_DASHBOARD_PLUGIN_URL . '/assets/js/select2.min.js', array( 'jquery' ), '4.1.0-rc.0', true );

		// Icon picker.
		wp_enqueue_script( 'icon-picker', ATS_DASHBOARD_PLUGIN_URL . '/assets/js/icon-picker.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		// Template tags (click-to-copy placeholder tags).
		wp_enqueue_script( 'ats-admin', ATS_DASHBOARD_PLUGIN_URL . '/assets/js/template-tags.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		// CodeMirror (HTML content editor).
		wp_enqueue_code_editor( array( 'type' => 'text/html' ) );

		// Edit admin page.
		wp_enqueue_script( 'ats-edit-admin-page', $module->url . '/assets/js/edit-admin-page.js', array( 'jquery', 'wp-escape-html' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		do_action( 'ats_edit_admin_page_scripts' );

	} elseif ( $module->screen()->is_admin_page_list() ) {

		do_action( 'ats_admin_page_list_scripts' );

	}

};
