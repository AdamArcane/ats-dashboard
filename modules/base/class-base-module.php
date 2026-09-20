<?php
/**
 * Base module.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Base;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Vars;
use ATSDash\Helpers\Array_Helper;
use ATSDash\Helpers\Screen_Helper;
use ATSDash\Helpers\Content_Base_Helper;
use ATSDash\Helpers\User_Helper;
use ATSDash\Helpers\Widget_Base_Helper;

/**
 * Class to setup base module.
 */
class Base_Module {

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
	public function array() {

		return new Array_Helper();

	}

	/**
	 * Content helper.
	 *
	 * @return Content_Base_Helper Instance of content helper.
	 */
	public function content() {

		return new Content_Base_Helper();

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

		return new Widget_Base_Helper();

	}
}
