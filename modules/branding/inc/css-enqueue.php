<?php
/**
 * CSS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	$is_branding_page = $module->screen()->is_branding() || ( isset( $_GET['page'] ) && 'ats_branding' === $_GET['page'] );

	if ( $is_branding_page ) {

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'atsui', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/atsui.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );
		wp_enqueue_style( 'ats-settings', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/settings.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

	}

};
