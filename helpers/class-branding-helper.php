<?php
/**
 * Branding helper.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Helpers;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Class to setup branding helper.
 */
class Branding_Helper {

	/**
	 * The colors the plugin ships with, used until the user picks their own.
	 *
	 * @return array Color values in hex format, keyed by ats_branding option key.
	 */
	public static function default_colors() {

		return apply_filters(
			'ats_branding_default_colors',
			array(
				'accent_color'           => '#681E1E',
				'menu_item_color'        => '#f7f7f7',
				'admin_bar_bg_color'     => '#260000',
				'admin_menu_bg_color'    => '#2D0B0B',
				'admin_submenu_bg_color' => '#442d2d',
			)
		);

	}

	/**
	 * Get a single default color.
	 *
	 * @param string $key The ats_branding option key.
	 * @return string Color in hex format.
	 */
	public static function default_color( $key ) {

		$colors = self::default_colors();

		return isset( $colors[ $key ] ) ? $colors[ $key ] : '';

	}

	/**
	 * Check whether or not branding option is enabled.
	 *
	 * @return bool
	 */
	public function is_enabled() {

		$branding = get_option( 'ats_branding' );

		if ( isset( $branding['enabled'] ) ) {
			return true;
		}

		return false;

	}

}
