<?php
/**
 * Base module.
 *
 * @package ATS_Dashboard
 */

namespace ats\Base;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Vars;
use ats\Helpers\Array_Helper;
use ats\Helpers\Screen_Helper;
use ats\Helpers\Content_Helper;
use ats\Helpers\User_Helper;
use ats\Helpers\Widget_Helper;

/**
 * Class to setup base output.
 */
class Base_Output {

	/**
	 * Get ats option data.
	 *
	 * @deprecated 3.7.15 Use get_option() instead.
	 *
	 * @param string $option_name The option name without "ats_" prefix.
	 * @return mixed The value of ats_{$option_name}.
	 */
	public function option( $option_name ) {

		$value = Vars::get( 'ats_' . $option_name );

		if ( $value ) {
			return $value;
		}

		return get_option( 'ats_' . $option_name, array() );

	}

	/**
	 * Array helper.
	 *
	 * @return object Instance of array helper.
	 */
	public function array_helper() {

		return new Array_Helper();

	}

	/**
	 * Content helper.
	 *
	 * @return object Instance of content helper.
	 */
	public function content() {

		return new Content_Helper();

	}

	/**
	 * Screen helper.
	 *
	 * @return object Instance of screen helper.
	 */
	public function screen() {

		return new Screen_Helper();

	}

	/**
	 * User helper.
	 *
	 * @return object Instance of user helper.
	 */
	public function user() {

		return new User_Helper();

	}

	/**
	 * Widget helper.
	 *
	 * @return object Instance of widget helper.
	 */
	public function widget() {

		return new Widget_Helper();

	}
}
