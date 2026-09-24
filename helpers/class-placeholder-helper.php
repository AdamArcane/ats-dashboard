<?php
/**
 * Placeholder helper.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Helpers;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Class to setup placeholder helper.
 */
class Placeholder_Helper {

	/**
	 * The current site/blog name.
	 *
	 * @var string
	 */
	public $site_name;

	/**
	 * The current site/blog url.
	 *
	 * @var string
	 */
	public $site_url;

	/**
	 * Module constructor.
	 */
	public function __construct() {

		/**
		 * These vars needs to be defined here because:
		 * - to prevent multiple repeating process of getting the site name and url.
		 * - to prevent the condition where blog already switched to the blueprint site.
		 */
		$this->site_name = get_bloginfo( 'name' );
		$this->site_url  = get_site_url( null );

	}

	/**
	 * Convert admin menu & admin bar placeholder tags with their respective values.
	 *
	 * @param string $str The string to replace the tags in.
	 * @return string The modified string.
	 */
	public function convert_admin_menu_placeholder_tags( $str ) {

		$find = [
			'{site_url}',
			'{site_name}',
			'{post_type}',
		];

		$replacement = [
			$this->site_url,
			$this->site_name,
			$this->get_current_post_type_label(),
		];

		$str = str_replace( $find, $replacement, $str );
		$str = apply_filters( 'ats_admin_menu_convert_placeholder_tags', $str );

		return $str;

	}

	/**
	 * Get the singular label of the post type being viewed on the front end,
	 * falling back to a generic label (e.g. for archives or when the queried
	 * object isn't a post) so front-end admin bar items always read naturally.
	 *
	 * @return string
	 */
	public function get_current_post_type_label() {

		$generic_label = __( 'Content', 'ats-dashboard' );

		$queried_object = get_queried_object();

		if ( $queried_object instanceof \WP_Post ) {
			$post_type_object = get_post_type_object( $queried_object->post_type );

			if ( $post_type_object && ! empty( $post_type_object->labels->singular_name ) ) {
				return $post_type_object->labels->singular_name;
			}
		}

		return $generic_label;

	}

}
