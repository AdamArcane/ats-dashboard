<?php
/**
 * JS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_admin_menu() ) {

		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-mouse' );
		wp_enqueue_script( 'jquery-ui-sortable' );

		wp_enqueue_script( 'select2', ATS_DASHBOARD_PLUGIN_URL . '/assets/js/select2.min.js', array( 'jquery' ), '4.1.0-rc.0', true );
		wp_enqueue_script( 'dashicons-picker', ATS_DASHBOARD_PLUGIN_URL . '/assets/js/dashicons-picker.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );
		wp_enqueue_script( 'ats-admin', ATS_DASHBOARD_PLUGIN_URL . '/assets/js/template-tags.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		wp_dequeue_script( 'ats-admin-menu' );
		wp_deregister_script( 'ats-admin-menu' );

		// Admin menu.
		$admin_menu_js_path    = ATS_DASHBOARD_PLUGIN_DIR . '/modules/admin-menu/assets/js/admin-menu.js';
		$admin_menu_js_version = file_exists( $admin_menu_js_path ) ? (string) filemtime( $admin_menu_js_path ) : ATS_DASHBOARD_PLUGIN_VERSION;

		wp_enqueue_script( 'ats-admin-menu', $module->url . '/assets/js/admin-menu.js', array( 'jquery', 'dashicons-picker', 'jquery-ui-sortable' ), $admin_menu_js_version, true );

		$wp_roles   = wp_roles();
		$role_names = $wp_roles->role_names;
		$roles      = array();

		foreach ( $role_names as $role_key => $role_name ) {
			array_push(
				$roles,
				array(
					'key'  => $role_key,
					'name' => $role_name,
				)
			);
		}

		$admin_menu_data = array(
			'nonces'          => array(
				'getMenu'    => wp_create_nonce( 'ats_admin_menu_get_menu' ),
				'getUsers'   => wp_create_nonce( 'ats_admin_menu_get_users' ),
				'resetMenu'  => wp_create_nonce( 'ats_admin_menu_reset_menu' ),
				'saveMenu'   => wp_create_nonce( 'ats_admin_menu_save_menu' ),
				'searchUrls' => wp_create_nonce( 'ats_admin_menu_search_urls' ),
			),
			'warningMessages' => array(
				'resetMenu' => __( 'Caution! Are you sure you want to reset the Admin Menu back to WordPress defaults?', 'ats-dashboard' ),
			),
			'roles'           => $roles,
			'templates'       => array(
				'menuList'      => require ATS_DASHBOARD_PLUGIN_DIR . '/modules/admin-menu/templates/menu-list.php',
				'submenuList'   => require ATS_DASHBOARD_PLUGIN_DIR . '/modules/admin-menu/templates/submenu-list.php',
				'menuSeparator' => require ATS_DASHBOARD_PLUGIN_DIR . '/modules/admin-menu/templates/menu-separator.php',
			),
		);

		$admin_menu_data = apply_filters( 'ats_admin_menu_js_object', $admin_menu_data );

		wp_localize_script(
			'ats-admin-menu',
			'atsAdminMenu',
			$admin_menu_data
		);

	}

};
