<?php
/**
 * Search for a URL to link a menu/submenu item to.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\AdminMenu\Ajax;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Class to handle ajax request to search for pages/posts & admin pages to link to.
 */
class Search_Urls {

	/**
	 * The class instance.
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Get instance of the class.
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Search for a URL, grouped by source (Admin Pages, then one group per public post type),
	 * in the same shape select2 expects for grouped results.
	 */
	public function ajax() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'ats_admin_menu_search_urls' ) ) {
			wp_send_json_error( __( 'Invalid token', 'ats-dashboard' ) );
		}

		$capability = apply_filters( 'ats_settings_capability', 'manage_options' );

		if ( ! current_user_can( $capability ) ) {
			wp_send_json_error( __( 'You do not have permission to perform this action', 'ats-dashboard' ) );
		}

		$term = isset( $_POST['term'] ) ? sanitize_text_field( wp_unslash( $_POST['term'] ) ) : '';

		if ( strlen( $term ) < 2 ) {
			wp_send_json_success( array() );
		}

		$results = array();

		$admin_pages = $this->search_admin_pages( $term );

		if ( ! empty( $admin_pages ) ) {
			$results[] = array(
				'text'     => __( 'Admin Pages', 'ats-dashboard' ),
				'children' => $admin_pages,
			);
		}

		$results = array_merge( $results, $this->search_content( $term ) );

		wp_send_json_success( $results );

	}

	/**
	 * Search the current user's admin menu - built-in WP pages, this plugin's own
	 * pages, and any other admin pages registered by other plugins - by title.
	 *
	 * Reuses Get_Menu the same way the builder itself loads the raw WP menu, so
	 * results reflect the true default menu rather than any saved customization.
	 *
	 * @param string $term The search term.
	 * @return array Select2-shaped {id, text} matches.
	 */
	private function search_admin_pages( $term ) {

		require_once __DIR__ . '/class-get-menu.php';

		/**
		 * @see \ATSDash\AdminMenu\Admin_Menu_Output::remove_output_actions()
		 */
		do_action( 'ats_ajax_before_get_admin_menu' );

		$get_menu = new Get_Menu();
		$get_menu->load_menu();

		global $menu, $submenu;

		$merged_menu = $get_menu->merge_default_menu_submenu( $menu, $submenu );

		$matches = array();
		$seen    = array();

		foreach ( $merged_menu as $menu_item ) {
			if ( empty( $menu_item[0] ) ) {
				continue; // Separator.
			}

			$this->maybe_add_admin_page_match( $matches, $seen, $menu_item[0], $menu_item[2], $term );

			if ( empty( $menu_item['submenu'] ) || ! is_array( $menu_item['submenu'] ) ) {
				continue;
			}

			foreach ( $menu_item['submenu'] as $submenu_item ) {
				if ( empty( $submenu_item[0] ) ) {
					continue;
				}

				$this->maybe_add_admin_page_match( $matches, $seen, $submenu_item[0], $submenu_item[2], $term );
			}
		}

		/**
		 * Pages built with this plugin's own "Admin Pages" feature that are set to
		 * "Not shown in menu" are registered via add_submenu_page( null, ... ), so
		 * WordPress deliberately never puts them in $menu/$submenu (see
		 * Admin_Page_Base_Output::add_menu()). They're still real, reachable admin
		 * URLs though, and a very likely thing to want to link to here, so search
		 * for them directly rather than relying on $menu/$submenu alone.
		 *
		 * @see \ATSDash\AdminPage\Admin_Page_Base_Output::add_menu()
		 */
		$this->search_custom_admin_pages( $term, $matches, $seen );

		return array_slice( $matches, 0, 20 );

	}

	/**
	 * Search this plugin's own "Admin Pages" custom post type by title, adding
	 * any not already found via $menu/$submenu (see search_admin_pages()).
	 *
	 * @param string $term The search term.
	 * @param array  $matches Matches gathered so far, passed by reference.
	 * @param array  $seen Admin URLs already added, passed by reference.
	 */
	private function search_custom_admin_pages( $term, &$matches, &$seen ) {

		if ( ! post_type_exists( 'ats_admin_page' ) ) {
			return;
		}

		$posts = get_posts(
			array(
				's'                   => $term,
				'post_type'           => 'ats_admin_page',
				'post_status'         => 'publish',
				'posts_per_page'      => 20,
				'orderby'             => 'title',
				'order'               => 'ASC',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);

		foreach ( $posts as $post ) {
			$url = 'admin.php?page=ats_page_' . $post->post_name;

			$this->maybe_add_admin_page_match( $matches, $seen, $post->post_title, $url, $term );
		}

	}

	/**
	 * Add an admin page to $matches if its title matches $term and it hasn't
	 * already been added (some pages appear as both a top level & submenu item).
	 *
	 * @param array  $matches Matches gathered so far, passed by reference.
	 * @param array  $seen Admin URLs already added, passed by reference.
	 * @param string $title The page's title (may contain a bubble count/HTML).
	 * @param string $url The page's relative admin URL.
	 * @param string $term The search term.
	 */
	private function maybe_add_admin_page_match( &$matches, &$seen, $title, $url, $term ) {

		$title = trim( wp_strip_all_tags( $title ) );

		if ( '' === $title || false === stripos( $title, $term ) ) {
			return;
		}

		// Most menu items store a relative slug ("options-general.php"), but some
		// plugins register an already-absolute URL - admin_url() would otherwise
		// double-prefix those with another "wp-admin/".
		$full_url = false !== strpos( $url, '://' ) ? $url : admin_url( $url );

		if ( isset( $seen[ $full_url ] ) ) {
			return;
		}

		$seen[ $full_url ] = true;

		$matches[] = array(
			'id'   => $full_url,
			'text' => $title,
		);

	}

	/**
	 * Search published content (pages, posts, and any other public post type) by title.
	 *
	 * @param string $term The search term.
	 * @return array One select2 group per post type that has matches.
	 */
	private function search_content( $term ) {

		$post_types = get_post_types( array( 'public' => true ), 'names' );
		unset( $post_types['attachment'] );

		if ( empty( $post_types ) ) {
			return array();
		}

		$groups = array();

		foreach ( $post_types as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );

			if ( ! $post_type_object ) {
				continue;
			}

			$posts = get_posts(
				array(
					's'                   => $term,
					'post_type'           => $post_type,
					'post_status'         => 'publish',
					'posts_per_page'      => 10,
					'orderby'             => 'title',
					'order'               => 'ASC',
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			);

			if ( empty( $posts ) ) {
				continue;
			}

			$children = array();

			foreach ( $posts as $post ) {
				$children[] = array(
					'id'   => get_permalink( $post ),
					'text' => get_the_title( $post ),
				);
			}

			$groups[] = array(
				'text'     => $post_type_object->labels->name,
				'children' => $children,
			);
		}

		return $groups;

	}

}
