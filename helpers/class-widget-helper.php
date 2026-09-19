<?php
/**
 * Widget helper.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Helpers;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Helpers\Widget_Helper as Free_Widget_Helper;

/**
 * Class to setup widget helper.
 */
class Widget_Helper extends Free_Widget_Helper {

	/**
	 * Get saved 3rd party widgets.
	 *
	 * @return array The saved 3rd party widgets.
	 */
	public function get_saved_3rd_party_widgets() {

		$widgets = $this->get_all();

		if ( get_option( 'ats_settings' ) ) {
			$settings = get_option( 'ats_settings' );
		} else {
			$settings = array();
		}

		$widgets = array_intersect_key( $widgets, $settings );

		return $widgets;

	}

	/**
	 * Get widget order user.
	 *
	 * @param bool $is_multisite Whether to get the user from the network- or regular settings.
	 * @return int user ID.
	 */
	public function get_widget_order_user( $is_multisite = false ) {

		if ( ! $is_multisite ) {

			$settings          = get_option( 'ats_settings' );
			$widget_order_user = isset( $settings['widget_order'] ) ? absint( $settings['widget_order'] ) : 0;

		} else {

			$widget_order_user = absint( get_site_option( 'ats_multisite_widget_order' ) );

		}

		return $widget_order_user;

	}

}
