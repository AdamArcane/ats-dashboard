<?php
/**
 * JS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_admin_bar() ) {

		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-mouse' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'select2', ATS_DASHBOARD_PLUGIN_URL . '/assets/js/select2.min.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );
		wp_enqueue_script( 'dashicons-picker', ATS_DASHBOARD_PLUGIN_URL . '/assets/js/dashicons-picker.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );
		wp_enqueue_script( 'ats-admin', ATS_DASHBOARD_PLUGIN_URL . '/assets/js/template-tags.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );
		wp_enqueue_script( 'ats-admin-bar-visibility', $module->url . '/assets/js/admin-bar-visibility.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		wp_dequeue_script( 'ats-admin-bar' );
		wp_deregister_script( 'ats-admin-bar' );

		// Admin menu.
		wp_enqueue_script( 'ats-admin-bar', $module->url . '/assets/js/admin-bar.js', array( 'jquery', 'dashicons-picker', 'jquery-ui-sortable' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		/**
		 * These codes are not being used currently.	
		 * But leave it here because in the future, if requested, it would be used for
		 * "hide menu item for specific role(s) / user(s)" functionality (inside dropdowns).
		 */
		// $wp_roles   = wp_roles();
		// $role_names = $wp_roles->role_names;
		// $roles      = array();

		// foreach ( $role_names as $role_key => $role_name ) {
		// 	array_push(
		// 		$roles,
		// 		array(
		// 			'id'   => $role_key,
		// 			'text' => $role_name,
		// 		)
		// 	);
		// }

		$admin_bar_data = array(
			'nonces'    => array(
				// 'getUsers'  => wp_create_nonce( 'ats_admin_bar_get_users' ),
				'resetMenu' => wp_create_nonce( 'ats_admin_bar_reset_menu' ),
				'saveMenu'  => wp_create_nonce( 'ats_admin_bar_save_menu' ),
			),
			'warningMessages' => array(
				'resetMenu' => __( 'Caution! Are you sure you want to reset the Admin Bar Editor?', 'ats-dashboard' ),
			),
			// 'roles'     => $roles,
			'templates' => array(
				'menuList'    => require __DIR__ . '/../templates/menu-list.php',
				'submenuList' => require __DIR__ . '/../templates/submenu-list.php',
			),
		);

		$admin_bar_data = apply_filters( 'ats_admin_bar_js_object', $admin_bar_data );

		wp_localize_script(
			'ats-admin-bar',
			'atsAdminBar',
			$admin_bar_data
		);

	}

};
