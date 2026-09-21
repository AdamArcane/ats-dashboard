<?php
/**
 * Setup column content on admin page list screen.
 *
 * @package ATS_Dashboard
 */

use ATSDash\AdminPage\Admin_Page_Module;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Setup column content on admin page list screen.
 *
 * @param Admin_Page_Module $module The module class instance.
 * @param string            $column The column name.
 * @param int               $post_id The post ID.
 */
return function ( $module, $column, $post_id ) {

	$menu_type = get_post_meta( $post_id, 'ats_menu_type', true );

	switch ( $column ) {

		case 'status':
			$post_status        = get_post_status( $post_id );
			$post_status_object = get_post_status_object( $post_status );
			$label              = $post_status_object ? $post_status_object->label : ucfirst( $post_status );

			echo esc_html( $label );

			if ( 'publish' === $post_status && ! $menu_type ) {
				echo '<br><span class="description">' . esc_html__( 'Menu Type not set — won\'t appear in the menu.', 'ats-dashboard' ) . '</span>';
			}

			break;

		case 'parent_menu':
			$parent_menu = __( 'None', 'ats-dashboard' );

			if ( 'submenu' === $menu_type ) {
				$parent_slug = get_post_meta( $post_id, 'ats_menu_parent', true );

				foreach ( $GLOBALS['menu'] as $menu ) {
					if ( $menu[2] === $parent_slug ) {
						$parent_menu = $menu[0];
						break;
					}
				}
			}

			echo esc_html( $parent_menu );
			break;

		case 'type':
			$type = get_post_meta( $post_id, 'ats_content_type', true );

			if ( 'html' === $type ) {
				$text = __( 'HTML', 'ats-dashboard' );
			} else {
				$editor = $module->content()->get_content_editor( $post_id );
				$editor = 'block' === $editor || 'default' === $editor ? 'default' : $editor;
				$suffix = 'default' === $editor ? __( 'Editor', 'ats-dashboard' ) : __( 'Builder', 'ats-dashboard' );
				$suffix = 'elementor' === $editor ? '' : $suffix;
				$text   = wp_sprintf(
				// translators: %1$s: is the text prefix, %2$s: is the editor or builder name, %3$s: is the text suffix.
					__( '%1$s %2$s %3$s', 'ats-dashboard' ),
					$editor,
					$suffix
				);
			}

			echo esc_html( ucwords( $text ) );
			break;

		case 'roles':
			$allowed_roles = __( 'All', 'ats-dashboard' );
			$allowed_roles = apply_filters( 'ats_admin_page_list_roles_column_content', $allowed_roles, $post_id );

			echo esc_html( ucwords( $allowed_roles ) );
			break;

		case 'icon':
			$icon_class = get_post_meta( $post_id, 'ats_menu_icon', true );
			$icon_class = $icon_class ? $icon_class : 'dashicons dashicons-no is-empty';

			echo ( 'submenu' === $menu_type ? esc_html__( 'None', 'ats-dashboard' ) : wp_kses_post( '<i class="' . esc_attr( $icon_class ) . '"></i>' ) );
			break;

	}

};
