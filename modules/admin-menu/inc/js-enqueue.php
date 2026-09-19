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
		wp_enqueue_script( 'ats-admin-menu', $module->url . '/assets/js/admin-menu.js', array( 'jquery', 'dashicons-picker', 'jquery-ui-sortable' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

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
				'getMenu'   => wp_create_nonce( 'ats_admin_menu_get_menu' ),
				'getUsers'  => wp_create_nonce( 'ats_admin_menu_get_users' ),
				'resetMenu' => wp_create_nonce( 'ats_admin_menu_reset_menu' ),
				'saveMenu'  => wp_create_nonce( 'ats_admin_menu_save_menu' ),
			),
			'warningMessages' => array(
				'resetMenu' => __( 'Caution! Are you sure you want to reset the Admin Menu for {role} role(s).', 'ats-dashboard' ),
			),
			'roles'           => $roles,
			'templates'       => array(
				'menuList'       => require ATS_DASHBOARD_PLUGIN_DIR . '/modules/admin-menu/templates/menu-list.php',
				'submenuList'    => require ATS_DASHBOARD_PLUGIN_DIR . '/modules/admin-menu/templates/submenu-list.php',
				'menuSeparator'  => require ATS_DASHBOARD_PLUGIN_DIR . '/modules/admin-menu/templates/menu-separator.php',

				'userTabMenu'    => '',
				'userTabContent' => '',
			),
		);

		// ATS Dashboard (free version) v3.1.3 and below doesn't have "user-tab-menu.php" and "user-tab-content.php".
		if ( version_compare( ATS_DASHBOARD_PLUGIN_VERSION, '3.1.3', '>' ) ) {
			$admin_menu_data['templates']['userTabMenu']    = require ATS_DASHBOARD_PLUGIN_DIR . '/modules/admin-menu/templates/user-tab-menu.php';
			$admin_menu_data['templates']['userTabContent'] = require ATS_DASHBOARD_PLUGIN_DIR . '/modules/admin-menu/templates/user-tab-content.php';
		}

		$admin_menu_data = apply_filters( 'ats_admin_menu_js_object', $admin_menu_data );

		wp_localize_script(
			'ats-admin-menu',
			'atsAdminMenu',
			$admin_menu_data
		);

	}

};
