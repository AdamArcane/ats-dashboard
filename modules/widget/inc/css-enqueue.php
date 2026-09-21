<?php
/**
 * CSS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {
	wp_enqueue_style( 'ats-widget-admin-menu', $module->url . '/assets/css/admin-menu.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

	if ( $module->screen()->is_new_widget() || $module->screen()->is_edit_widget() ) {

		if ( apply_filters( 'ats_font_awesome', true ) ) {
			// Font Awesome.
			wp_enqueue_style( 'font-awesome', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/font-awesome.min.css', array(), '5.14.0' );
			wp_enqueue_style( 'font-awesome-shims', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/v4-shims.min.css', array(), '5.14.0' );
		}

		// Select2.
		wp_enqueue_style( 'select2', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/select2.min.css', array(), '4.1.0-rc.0' );

		// Color picker (Icon widget's icon color field).
		wp_enqueue_style( 'wp-color-picker' );

		// Icon picker.
		wp_enqueue_style( 'icon-picker', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/icon-picker.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		// Atsui.
		wp_enqueue_style( 'atsui', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/atsui.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		// Edit widget.
		wp_enqueue_style( 'ats-edit-widget', $module->url . '/assets/css/edit-widget.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		do_action( 'ats_edit_widget_styles' );

	} elseif ( $module->screen()->is_widget_list() ) {

		if ( apply_filters( 'ats_font_awesome', true ) ) {
			// Font Awesome.
			wp_enqueue_style( 'font-awesome', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/font-awesome.min.css', array(), '5.14.0' );
			wp_enqueue_style( 'font-awesome-shims', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/v4-shims.min.css', array(), '5.14.0' );
		}

		// Atsui.
		wp_enqueue_style( 'atsui', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/atsui.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		do_action( 'ats_widget_list_styles' );

	} elseif ( $module->screen()->is_dashboard() ) {

		if ( apply_filters( 'ats_font_awesome', true ) ) {
			// Font Awesome.
			wp_enqueue_style( 'font-awesome', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/font-awesome.min.css', array(), '5.14.0' );
			wp_enqueue_style( 'font-awesome-shims', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/v4-shims.min.css', array(), '5.14.0' );
		}

		// Dashboard.
		wp_enqueue_style( 'ats-dashboard', $module->url . '/assets/css/dashboard.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		do_action( 'ats_dashboard_styles' );

	}
};
