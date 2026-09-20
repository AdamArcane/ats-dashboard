<?php
/**
 * JS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {
	if ( $module->screen()->is_new_widget() || $module->screen()->is_edit_widget() ) {

		// Select2.
		wp_enqueue_script( 'select2', ATS_DASHBOARD_PLUGIN_URL . '/assets/js/select2.min.js', array( 'jquery' ), '4.1.0-rc.0', true );

		// Icon picker.
		wp_enqueue_script( 'icon-picker', ATS_DASHBOARD_PLUGIN_URL . '/assets/js/icon-picker.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		// Template tags.
		wp_enqueue_script( 'ats-admin', ATS_DASHBOARD_PLUGIN_URL . '/assets/js/template-tags.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		// CodeMirror.
		wp_enqueue_code_editor( array( 'type' => 'text/html' ) );

		// Edit widget.
		wp_enqueue_script( 'ats-edit-widget', $module->url . '/assets/js/edit-widget.js', array( 'jquery', 'wp-escape-html' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		do_action( 'ats_edit_widget_scripts' );

	} elseif ( $module->screen()->is_widget_list() ) {

		do_action( 'ats_widget_list_scripts' );

	} elseif ( $module->screen()->is_dashboard() ) {

		// Dashboard.
		wp_enqueue_script( 'ats-dashboard', $module->url . '/assets/js/dashboard.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		do_action( 'ats_dashboard_scripts' );

	}
};
