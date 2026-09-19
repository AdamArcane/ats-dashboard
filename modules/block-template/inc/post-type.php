<?php
/**
 * Setup block editor template post type.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	// Labels.
	$labels = array(
		'name'               => __( 'Block Editor Templates', 'ats-dashboard' ),
		'singular_name'      => __( 'Block Editor Template', 'ats-dashboard' ),
		'menu_name'          => __( 'Block Editor Templates', 'ats-dashboard' ),
		'name_admin_bar'     => __( 'Block Editor Template', 'ats-dashboard' ),
		'add_new'            => __( 'Add New', 'ats-dashboard' ),
		'add_new_item'       => __( 'Add Block Editor Template', 'ats-dashboard' ),
		'new_item'           => __( 'New Block Editor Template', 'ats-dashboard' ),
		'edit_item'          => __( 'Edit Block Editor Template', 'ats-dashboard' ),
		'view_item'          => __( 'View Block Editor Template', 'ats-dashboard' ),
		'all_items'          => __( 'Block Editor Templates', 'ats-dashboard' ),
		'search_items'       => __( 'Search Block Editor Templates', 'ats-dashboard' ),
		'not_found'          => __( 'No Block Editor Templates found.', 'ats-dashboard' ),
		'not_found_in_trash' => __( 'No Block Editor Templates in Trash.', 'ats-dashboard' ),
	);

	// Change capabilities so only users that can 'manage_options' are able to access Block Editor Templates.
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
		'public'              => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => true,
		'show_in_menu'        => false,
		'show_in_rest'        => true,
		'query_var'           => false,
		'rewrite'             => array( 'slug' => 'ats-block-template' ),
		'map_meta_cap'        => false,
		'capabilities'        => $capabilities,
		'has_archive'         => false,
		'hierarchical'        => false,
		'supports'            => array( 'title', 'editor', 'custom-fields' ),
	);

	register_post_type( 'ats_block_template', $args );

};
