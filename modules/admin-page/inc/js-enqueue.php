<?php
/**
 * JS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_new_admin_page() || $module->screen()->is_edit_admin_page() ) {

		// Template tags (click-to-copy placeholder tags).
		wp_enqueue_script( 'ats-admin', ATS_DASHBOARD_PLUGIN_URL . '/assets/js/template-tags.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

	} elseif ( $module->screen()->is_admin_page_list() ) {

		//

	}

};
