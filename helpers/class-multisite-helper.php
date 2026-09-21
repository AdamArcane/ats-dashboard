<?php
/**
 * Multisite helper.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Helpers;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Class to setup multisite helper.
 */
class Multisite_Helper {
	/**
	 * Check whether plugin is active on multisite or not.
	 *
	 * @return bool
	 */
	public function is_network_active() {
		// Load plugin.php if it doesn't already exist.
		if ( ! function_exists( 'is_plugin_active_for_network' ) || ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		return ( is_plugin_active_for_network( ATS_DASHBOARD_PLUGIN_FILE ) ? true : false );
	}

	/**
	 * Check whether multisite actions are supported or not.
	 *
	 * @return bool
	 */
	public function multisite_supported() {
		return ( $this->is_network_active() && apply_filters( 'ats_ms_support', false ) ? true : false );
	}

	/**
	 * Check whether we need to switch blog.
	 *
	 * @param bool $check_multisite_support Whether to check for multisite support or not.
	 *
	 *                                      If $check_multisite_support is true, `get_network_option` would be called during the process.
	 *                                      It's default to true because many of `needs_to_switch_blog` calls come from outside multisite module.
	 *
	 *                                      When working inside multisite module, we can set it to false, because multisite module is only being called
	 *                                      after multisite support check.
	 * @return bool
	 */
	public function needs_to_switch_blog( $check_multisite_support = true ) {

		if ( $check_multisite_support ) {
			if ( ! $this->multisite_supported() ) {
				return false;
			}
		}

		global $blueprint;

		if ( empty( $blueprint ) || get_current_blog_id() === $blueprint ) {
			return false;
		}

		return true;

	}

	/**
	 * Check if a site is an excluded site.
	 *
	 * @param int $site_id The site id. If omitted, it will use the current site id.
	 * @return bool
	 */
	public function is_blueprint_site( $site_id = 0 ) {

		global $blueprint;

		$site_id = $site_id ? $site_id : get_current_blog_id();

		if ( ! empty( $blueprint ) && $site_id === $blueprint ) {
			return true;
		}

		return false;

	}

	/**
	 * Construct array of excluded sites
	 *
	 * @return array The array of excluded sites.
	 */
	public function get_excluded_sites() {

		global $blueprint;

		$array = array();

		// Include blueprint site if it is defined.
		if ( ! empty( $blueprint ) ) {
			$array[] = $blueprint;
		}

		if ( get_site_option( 'ats_multisite_exclude' ) ) {
			$excluded_sites = get_site_option( 'ats_multisite_exclude' );
			$excluded_sites = str_replace( ' ', '', $excluded_sites );
			$excluded_sites = explode( ',', $excluded_sites );
		} else {
			$excluded_sites = array();
		}

		$excluded_sites = array_merge( $array, $excluded_sites );

		foreach ( $excluded_sites as $index => $excluded_site ) {
			$excluded_sites[ $index ] = absint( $excluded_site );
		}

		return $excluded_sites;

	}

	/**
	 * Check if a site is an excluded site.
	 *
	 * @param int $site_id The site id. If omitted, it will use the current site id.
	 * @return bool
	 */
	public function is_site_excluded( $site_id = 0 ) {

		global $blueprint;

		if ( empty( $blueprint ) ) {
			true;
		}

		if ( ! $site_id ) {
			$site_id = get_current_blog_id();
		}

		$excluded_sites = $this->get_excluded_sites();

		if ( in_array( $site_id, $excluded_sites, true ) ) {
			return true;
		}

		return false;

	}

}
