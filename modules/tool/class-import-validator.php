<?php
/** Import structure validation, performed before any database writes. */
namespace ATSDash\Tool;

defined( 'ABSPATH' ) || exit;

class Import_Validator {
	/** Return normalized import data or a WP_Error. */
	public static function decode( $json ) {
		$data = json_decode( $json, true, 32 );
		if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $data ) || '{' !== substr( ltrim( $json ), 0, 1 ) || empty( $data ) ) {
			return new \WP_Error( 'ats_invalid_import', __( 'Please upload a valid ATS Dashboard JSON object.', 'ats-dashboard' ) );
		}

		$groups = array( 'modules_manager_settings', 'feature_settings', 'settings', 'branding_settings', 'login_customizer_settings', 'login_settings', 'login_redirect_settings', 'widgets', 'admin_pages', 'admin_menu', 'admin_bar', 'multisite_settings' );
		foreach ( $groups as $group ) {
			if ( array_key_exists( $group, $data ) && ! is_array( $data[ $group ] ) ) {
				return new \WP_Error( 'ats_invalid_import', __( 'Import sections must contain arrays or objects.', 'ats-dashboard' ) );
			}
		}

		foreach ( array( 'widgets' => 'ats_widgets', 'admin_pages' => 'ats_admin_page' ) as $group => $post_type ) {
			if ( ! isset( $data[ $group ] ) ) {
				continue;
			}
			foreach ( $data[ $group ] as &$post ) {
				if ( ! is_array( $post ) || empty( $post['post_name'] ) || ! is_string( $post['post_name'] ) || ! isset( $post['meta'] ) || ! is_array( $post['meta'] ) ) {
					return new \WP_Error( 'ats_invalid_import', __( 'Each imported widget or admin page needs a slug and metadata.', 'ats-dashboard' ) );
				}
				// Do not pass arbitrary wp_insert_post arguments (ID, meta_input, etc.) through.
				$post = array_intersect_key( $post, array_flip( array( 'post_name', 'post_title', 'post_content', 'post_excerpt', 'post_status', 'menu_order', 'meta' ) ) );
				foreach ( $post as $field => $value ) {
					if ( 'meta' !== $field && ! is_scalar( $value ) ) {
						return new \WP_Error( 'ats_invalid_import', __( 'Imported post fields must contain scalar values.', 'ats-dashboard' ) );
					}
				}
				$post['post_type'] = $post_type;
				if ( isset( $post['post_status'] ) && ! in_array( $post['post_status'], array( 'publish', 'draft', 'pending', 'private' ), true ) ) {
					return new \WP_Error( 'ats_invalid_import', __( 'Invalid imported post status.', 'ats-dashboard' ) );
				}
				foreach ( $post['meta'] as $key => &$value ) {
					if ( ! is_string( $key ) ) {
						return new \WP_Error( 'ats_invalid_import', __( 'Invalid metadata key.', 'ats-dashboard' ) );
					}
					if ( in_array( $key, array( 'ats_custom_js', 'ats_html_content', 'ats_custom_css', 'ats_html', 'ats_content' ), true ) && ! is_string( $value ) ) {
						return new \WP_Error( 'ats_invalid_import', __( 'Imported code fields must contain text.', 'ats-dashboard' ) );
					}
					if ( false !== stripos( $key, '_roles' ) || false !== stripos( $key, '_users' ) ) {
						// Decode legacy lists without allowing object callbacks or nested structures.
						for ( $i = 0; $i < 3 && is_serialized( $value ); $i++ ) {
							$value = @unserialize( $value, array( 'allowed_classes' => false, 'max_depth' => 32 ) );
						}
						if ( ! is_array( $value ) ) {
							return new \WP_Error( 'ats_invalid_import', __( 'Imported roles and users must be lists.', 'ats-dashboard' ) );
						}
						foreach ( $value as $item ) {
							if ( ! is_string( $item ) && ! is_int( $item ) ) {
								return new \WP_Error( 'ats_invalid_import', __( 'Invalid imported role or user.', 'ats-dashboard' ) );
							}
						}
					}
				}
				unset( $value );
			}
			unset( $post );
		}

		return $data;
	}

	/** Apply code-field permissions and sanitizers to imported metadata too. */
	public static function prepare_meta( $meta ) {
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			unset( $meta['ats_custom_js'] );
		}
		if ( isset( $meta['ats_html_content'] ) ) {
			$helper = new \ATSDash\Helpers\Content_Base_Helper();
			$meta['ats_html_content'] = wp_kses( $meta['ats_html_content'], $helper->get_admin_page_html_allowed_tags() );
		}
		foreach ( array( 'ats_html', 'ats_content' ) as $key ) {
			if ( isset( $meta[ $key ] ) ) {
				$meta[ $key ] = wp_kses_post( $meta[ $key ] );
			}
		}
		if ( isset( $meta['ats_custom_css'] ) ) {
			$meta['ats_custom_css'] = wp_strip_all_tags( $meta['ats_custom_css'] );
		}
		return $meta;
	}
}
