<?php
/**
 * Setup widget post type.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {
	// Labels.
	$labels = array(
		'name'               => _x( 'Dashboard Widgets', 'Post type general name', 'ats-dashboard' ),
		'singular_name'      => _x( 'Dashboard Widget', 'Post type singular name', 'ats-dashboard' ),
		'menu_name'          => _x( 'Arcane Tech', 'Admin Menu text', 'ats-dashboard' ),
		'name_admin_bar'     => _x( 'Dashboard Widget', 'Add New on Toolbar', 'ats-dashboard' ),
		'add_new'            => __( 'Add New', 'ats-dashboard' ),
		'add_new_item'       => __( 'Add Dashboard Widget', 'ats-dashboard' ),
		'new_item'           => __( 'New Dashboard Widget', 'ats-dashboard' ),
		'edit_item'          => __( 'Edit Dashboard Widget', 'ats-dashboard' ),
		'view_item'          => __( 'View Dashboard Widget', 'ats-dashboard' ),
		'all_items'          => __( 'All Widgets', 'ats-dashboard' ),
		'search_items'       => __( 'Search Dashboard Widgets', 'ats-dashboard' ),
		'not_found'          => __( 'No Dashboard Widgets found.', 'ats-dashboard' ),
		'not_found_in_trash' => __( 'No Dashboard Widgets in Trash.', 'ats-dashboard' ),
	);

	// Change capabilities so only users that can 'manage_options' are able to access the dashboard widgets & settings.
	$capabilities = array(
		'edit_post'          => apply_filters( 'ats_settings_capability', 'manage_options' ),
		'read_post'          => apply_filters( 'ats_settings_capability', 'manage_options' ),
		'delete_post'        => apply_filters( 'ats_settings_capability', 'manage_options' ),
		'delete_posts'       => apply_filters( 'ats_settings_capability', 'manage_options' ),
		'edit_posts'         => apply_filters( 'ats_settings_capability', 'manage_options' ),
		'edit_others_posts'  => apply_filters( 'ats_settings_capability', 'manage_options' ),
		'publish_posts'      => apply_filters( 'ats_settings_capability', 'manage_options' ),
		'read_private_posts' => apply_filters( 'ats_settings_capability', 'manage_options' ),
		'create_posts'       => apply_filters( 'ats_settings_capability', 'manage_options' ),
	);

	// Arguments.
	$args = array(
		'labels'             => $labels,
		'menu_icon'          => 'dashicons-format-gallery',
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => false,
		'rewrite'            => false,
		'map_meta_cap'       => false,
		'capabilities'       => $capabilities,
		'has_archive'        => false,
		'hierarchical'       => false,
		'supports'           => array( 'title' ),
	);

	register_post_type( 'ats_widgets', $args );
};
