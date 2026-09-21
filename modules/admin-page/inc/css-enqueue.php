<?php
/**
 * CSS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_new_admin_page() || $module->screen()->is_edit_admin_page() ) {

		if ( apply_filters( 'ats_font_awesome', true ) ) {
			// Font Awesome (menu icon picker).
			wp_enqueue_style( 'font-awesome', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/font-awesome.min.css', array(), '5.14.0' );
			wp_enqueue_style( 'font-awesome-shims', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/v4-shims.min.css', array(), '5.14.0' );
		}

		// Select2 (User Role Access field).
		wp_enqueue_style( 'select2', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/select2.min.css', array(), '4.1.0-rc.0' );

		// Icon picker.
		wp_enqueue_style( 'icon-picker', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/icon-picker.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		// Atsui (metabox card styling).
		wp_enqueue_style( 'atsui', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/atsui.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		// Edit admin page.
		wp_enqueue_style( 'ats-edit-admin-page', $module->url . '/assets/css/edit-admin-page.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		do_action( 'ats_edit_admin_page_styles' );

	} elseif ( $module->screen()->is_admin_page_list() ) {

		if ( apply_filters( 'ats_font_awesome', true ) ) {
			// Font Awesome.
			wp_enqueue_style( 'font-awesome', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/font-awesome.min.css', array(), '5.14.0' );
			wp_enqueue_style( 'font-awesome-shims', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/v4-shims.min.css', array(), '5.14.0' );
		}

		// Atsui.
		wp_enqueue_style( 'atsui', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/atsui.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

		do_action( 'ats_admin_page_list_styles' );

	}

};
