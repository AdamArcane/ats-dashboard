<?php
/**
 * Import processing.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Helpers\Array_Helper;

return function () {

	$array_helper = new Array_Helper();
	$import_file  = isset( $_FILES['ats_import_file'] ) ? $_FILES['ats_import_file'] : null;

	if ( is_null( $import_file ) ) {

		add_settings_error(
			'ats_export',
			esc_attr( 'ats-import' ),
			__( 'Please select a file to import', 'ats-dashboard' )
		);

		return;

	}

	$file_name = basename( sanitize_file_name( wp_unslash( $import_file['name'] ) ) );
	$explodes  = explode( '.', $file_name );
	$ext       = end( $explodes );

	// wp_check_filetype fails here, so let's check it manually.
	if ( 'json' !== $ext ) {

		add_settings_error(
			'ats_export',
			esc_attr( 'ats-import' ),
			__( 'Please upload a valid .json file', 'ats-dashboard' )
		);

		return;

	}

	$tmp_file = $import_file['tmp_name'];

	if ( empty( $tmp_file ) ) {

		add_settings_error(
			'ats_export',
			esc_attr( 'ats-import' ),
			__( 'Please upload a file to import', 'ats-dashboard' )
		);

		return;

	}

	$imports = file_get_contents( $tmp_file, true );
	$imports = (array) json_decode( $imports, true );

	// Retrieve settings & widgets.
	$modules_manager_settings  = isset( $imports['modules_manager_settings'] ) ? $imports['modules_manager_settings'] : array();
	$settings                  = isset( $imports['settings'] ) ? $imports['settings'] : array();
	$branding_settings         = isset( $imports['branding_settings'] ) ? $imports['branding_settings'] : array();
	$login_customizer_settings = isset( $imports['login_customizer_settings'] ) ? $imports['login_customizer_settings'] : array();
	$login_redirect_settings   = isset( $imports['login_redirect_settings'] ) ? $imports['login_redirect_settings'] : array();
	$widgets                   = isset( $imports['widgets'] ) ? $imports['widgets'] : array();
	$admin_pages               = isset( $imports['admin_pages'] ) ? $imports['admin_pages'] : array();

	// Backwards compatibility for "feature_settings".
	if ( empty( $modules_manager_settings ) ) {
		$modules_manager_settings = isset( $imports['feature_settings'] ) ? $imports['feature_settings'] : $modules_manager_settings;
	}

	// Backwards compatibility for "login_settings".
	if ( empty( $login_customizer_settings ) ) {
		$login_customizer_settings = isset( $imports['login_settings'] ) ? $imports['login_settings'] : $login_customizer_settings;
	}

	if ( ! $imports ) {

		add_settings_error(
			'ats_export',
			esc_attr( 'ats-import' ),
			__( 'Your import file is empty', 'ats-dashboard' )
		);

		return;

	}

	if ( $modules_manager_settings || $settings || $branding_settings || $login_customizer_settings || $login_redirect_settings ) {

		if ( $modules_manager_settings ) {
			update_option( 'ats_modules', $modules_manager_settings );
		}

		if ( $settings ) {
			update_option( 'ats_settings', $settings );
		}

		if ( $branding_settings ) {
			update_option( 'ats_branding', $branding_settings );
		}

		if ( $login_customizer_settings ) {
			update_option( 'ats_login', $login_customizer_settings );
		}

		if ( $login_redirect_settings ) {
			update_option( 'ats_login_redirect', $login_redirect_settings );
		}

		do_action( 'ats_import_settings', $imports );

		add_settings_error(
			'ats_export',
			esc_attr( 'ats-import' ),
			__( 'Settings imported', 'ats-dashboard' ),
			'updated'
		);

	}

	if ( $widgets ) {

		foreach ( $widgets as $widget ) {

			// For backwards compatibility: before version 3, post_type was unset in the export.
			if ( ! isset( $widget['post_type'] ) ) {
				$widget['post_type'] = 'ats_widgets';
			}

			$post = get_page_by_path( $widget['post_name'], OBJECT, 'ats_widgets' );
			$meta = $widget['meta'];

			unset( $widget['meta'] );

			if ( $post ) {

				$post_id      = $post->ID;
				$widget['ID'] = $post->ID;

				wp_update_post( $widget );

			} else {

				unset( $widget['ID'] );

				$post_id = wp_insert_post( $widget );

			}

			foreach ( $meta as $meta_key => $meta_value ) {
				if ( false !== stripos( $meta_key, '_roles' ) || false !== stripos( $meta_key, '_users' ) ) {
					$meta_value = $array_helper->clean_unserialize( $meta_value, 3 );
				}

				update_post_meta( $post_id, $meta_key, $meta_value );
			}
		}

		add_settings_error(
			'ats_export',
			esc_attr( 'ats-import' ),
			__( 'Widgets imported', 'ats-dashboard' ),
			'updated'
		);

	}

	if ( $admin_pages ) {

		foreach ( $admin_pages as $admin_page ) {
			$post = get_page_by_path( $admin_page['post_name'], OBJECT, 'ats_admin_page' );
			$meta = $admin_page['meta'];

			unset( $admin_page['meta'] );

			// if post exists.
			if ( $post ) {
				$post_id          = $post->ID;
				$admin_page['ID'] = $post->ID;

				wp_update_post( $admin_page );
			} else {
				unset( $admin_page['ID'] );

				$post_id = wp_insert_post( $admin_page );
			}

			foreach ( $meta as $meta_key => $meta_value ) {
				if ( false !== stripos( $meta_key, '_roles' ) || false !== stripos( $meta_key, '_users' ) ) {
					$meta_value = $array_helper->clean_unserialize( $meta_value, 3 );
				}

				update_post_meta( $post_id, $meta_key, $meta_value );
			}
		}

		add_settings_error(
			'ats_export',
			esc_attr( 'ats-import' ),
			__( 'Admin pages imported', 'ats-dashboard' ),
			'updated'
		);

	}

	do_action( 'ats_import', $imports );

};
