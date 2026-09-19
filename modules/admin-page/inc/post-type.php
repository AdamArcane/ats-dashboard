<?php
/**
 * Setup admin page post type.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	// Labels.
	$labels = array(
		'name'               => __( 'Admin Pages', 'ats-dashboard' ),
		'singular_name'      => __( 'Admin Page', 'ats-dashboard' ),
		'menu_name'          => __( 'Admin Pages', 'ats-dashboard' ),
		'name_admin_bar'     => __( 'Admin Page', 'ats-dashboard' ),
		'add_new'            => __( 'Add New', 'ats-dashboard' ),
		'add_new_item'       => __( 'Add Admin Page', 'ats-dashboard' ),
		'new_item'           => __( 'New Admin Page', 'ats-dashboard' ),
		'edit_item'          => __( 'Edit Admin Page', 'ats-dashboard' ),
		'view_item'          => __( 'View Admin Page', 'ats-dashboard' ),
		'all_items'          => __( 'Admin Pages', 'ats-dashboard' ),
		'search_items'       => __( 'Search Admin Pages', 'ats-dashboard' ),
		'not_found'          => __( 'No Admin Pages found.', 'ats-dashboard' ),
		'not_found_in_trash' => __( 'No Admin Pages in Trash.', 'ats-dashboard' ),
	);

	// Change capabilities so only users that can 'manage_options' are able to access the Admin Pages & settings.
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
		'labels'              => $labels,
		'menu_icon'           => 'dashicons-format-gallery',
		'public'              => true,
		'exclude_from_search' => true,
		'show_in_menu'        => false,
		'show_in_rest'        => false,
		'query_var'           => false,
		'rewrite'             => false,
		'map_meta_cap'        => false,
		'capabilities'        => $capabilities,
		'has_archive'         => false,
		'hierarchical'        => false,
		'supports'            => array( 'title', 'editor' ),
	);

	$args = apply_filters( 'ats_admin_page_post_type_args', $args );

	register_post_type( 'ats_admin_page', $args );

};
