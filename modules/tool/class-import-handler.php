<?php
/** Applies decoded import data to the current site, independent of transport (admin upload or WP-CLI). */
namespace ATSDash\Tool;

defined( 'ABSPATH' ) || exit;

use ATSDash\Helpers\Array_Helper;

class Import_Handler {

	/**
	 * Apply decoded, validated import data (see Import_Validator::decode()) to the current site.
	 *
	 * @param array $imports Decoded import data.
	 * @return string[] Human-readable messages describing what was imported.
	 */
	public static function apply( array $imports ) {
		$array_helper = new Array_Helper();
		$messages     = array();

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

			$messages[] = __( 'Settings imported', 'ats-dashboard' );
		}

		// Network-only exports must also be handled when no site settings are present.
		do_action( 'ats_import_settings', $imports );

		if ( $widgets ) {
			self::apply_posts( $widgets, 'ats_widgets', $array_helper );
			$messages[] = __( 'Widgets imported', 'ats-dashboard' );
		}

		if ( $admin_pages ) {
			self::apply_posts( $admin_pages, 'ats_admin_page', $array_helper );
			$messages[] = __( 'Admin pages imported', 'ats-dashboard' );
		}

		do_action( 'ats_import', $imports );

		return $messages;
	}

	/** Insert or update ats_widgets / ats_admin_page posts and their meta. */
	private static function apply_posts( array $posts, $post_type, Array_Helper $array_helper ) {
		foreach ( $posts as $post ) {

			// For backwards compatibility: before version 3, post_type was unset in the export.
			if ( ! isset( $post['post_type'] ) ) {
				$post['post_type'] = $post_type;
			}

			$existing = get_page_by_path( $post['post_name'], OBJECT, $post_type );
			$meta     = Import_Validator::prepare_meta( $post['meta'] );

			unset( $post['meta'] );

			if ( $existing ) {
				$post_id    = $existing->ID;
				$post['ID'] = $existing->ID;

				wp_update_post( $post );
			} else {
				unset( $post['ID'] );

				$post_id = wp_insert_post( $post );
			}

			if ( ! $post_id || is_wp_error( $post_id ) ) {
				continue;
			}

			foreach ( $meta as $meta_key => $meta_value ) {
				if ( false !== stripos( $meta_key, '_roles' ) || false !== stripos( $meta_key, '_users' ) ) {
					$meta_value = $array_helper->clean_unserialize( $meta_value, 3 );
				}

				update_post_meta( $post_id, $meta_key, $meta_value );
			}
		}
	}
}
