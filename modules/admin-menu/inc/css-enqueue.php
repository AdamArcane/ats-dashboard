<?php
/**
 * CSS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_admin_menu() ) {

		wp_enqueue_style( 'select2', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/select2.min.css', array(), '4.1.0-rc.0' );
		wp_enqueue_style( 'dashicons-picker', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/dashicons-picker.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );
		wp_enqueue_style( 'atsui', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/atsui.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );
		$menu_builder_css_path    = ATS_DASHBOARD_PLUGIN_DIR . '/modules/admin-menu/assets/css/ats-menu-builder.css';
		$menu_builder_css_version = file_exists( $menu_builder_css_path ) ? (string) filemtime( $menu_builder_css_path ) : ATS_DASHBOARD_PLUGIN_VERSION;

		wp_enqueue_style( 'ats-menu-builder', $module->url . '/assets/css/ats-menu-builder.css', array(), $menu_builder_css_version );

	}

};
