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
		];

		$replacement = [
			$this->site_url,
			$this->site_name,
		];

		$str = str_replace( $find, $replacement, $str );
		$str = apply_filters( 'ats_admin_menu_convert_placeholder_tags', $str );

		return $str;

	}

}
