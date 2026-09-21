<?php
/**
 * Screen helper.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Helpers;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Class to setup screen helper.
 */
class Screen_Helper {
	/**
	 * Check if current screen is new widget screen.
	 *
	 * @return boolean
	 */
	public function is_new_widget() {

		global $pagenow, $typenow;
		return ( 'post-new.php' === $pagenow && 'ats_widgets' === $typenow ? true : false );

	}

	/**
	 * Check if current screen is edit widget screen.
	 *
	 * @return boolean
	 */
	public function is_edit_widget() {

		global $pagenow, $typenow;
		return ( 'post.php' === $pagenow && 'ats_widgets' === $typenow ? true : false );

	}

	/**
	 * Check if current screen is widget list screen.
	 *
	 * @return boolean
	 */
	public function is_widget_list() {

		$current_screen = get_current_screen();
		return is_null( $current_screen ) ? false : ( 'edit-ats_widgets' === $current_screen->id ? true : false );

	}

	/**
	 * Check if current screen is dashboard page.
	 *
	 * @return boolean
	 */
	public function is_dashboard() {

		global $pagenow;
		return ( 'index.php' === $pagenow ? true : false );

	}

	/**
	 * Check if current screen is settings page.
	 *
	 * @return boolean
	 */
	public function is_features() {

		$current_screen = get_current_screen();
		return is_null( $current_screen ) ? false : ( 'ats_widgets_page_ats_features' === $current_screen->id ? true : false );

	}

	/**
	 * Check if current screen is settings page.
	 *
	 * @return boolean
	 */
	public function is_settings() {

		$current_screen = get_current_screen();
		return is_null( $current_screen ) ? false : ( 'ats_widgets_page_ats_settings' === $current_screen->id ? true : false );

	}

	/**
	 * Check if current screen is branding page.
	 *
	 * @return boolean
	 */
	public function is_branding() {

		$current_screen = get_current_screen();
		return is_null( $current_screen ) ? false : ( 'ats_widgets_page_ats_branding' === $current_screen->id ? true : false );

	}

	/**
	 * Check if current screen is login redirect page.
	 *
	 * @return boolean
	 */
	public function is_login_redirect() {

		$current_screen = get_current_screen();
		return is_null( $current_screen ) ? false : ( 'ats_widgets_page_ats_login_redirect' === $current_screen->id ? true : false );

	}

	/**
	 * Check if current screen is integrations page.
	 *
	 * @return boolean
	 */
	public function is_integrations() {

		$current_screen = get_current_screen();
		return is_null( $current_screen ) ? false : ( 'ats_widgets_page_ats_integrations' === $current_screen->id ? true : false );

	}

	/**
	 * Check if current screen is tools page.
	 *
	 * @return boolean
	 */
	public function is_tools() {

		$current_screen = get_current_screen();
		return is_null( $current_screen ) ? false : ( 'ats_widgets_page_ats_tools' === $current_screen->id ? true : false );

	}

	/**
	 * Check if current screen is new admin page screen.
	 *
	 * @return boolean
	 */
	public function is_new_admin_page() {

		global $pagenow, $typenow;
		return ( 'post-new.php' === $pagenow && 'ats_admin_page' === $typenow ? true : false );

	}

	/**
	 * Check if current screen is edit admin page screen.
	 *
	 * @return boolean
	 */
	public function is_edit_admin_page() {

		global $pagenow, $typenow;
		return ( 'post.php' === $pagenow && 'ats_admin_page' === $typenow ? true : false );

	}

	/**
	 * Check if current screen is admin page list screen.
	 *
	 * @return boolean
	 */
	public function is_admin_page_list() {

		$current_screen = get_current_screen();
		return is_null( $current_screen ) ? false : ( 'edit-ats_admin_page' === $current_screen->id ? true : false );

	}

	/**
	 * Check if current screen is admin menu page.
	 *
	 * @return boolean
	 */
	public function is_admin_menu() {

		$current_screen = get_current_screen();
		return is_null( $current_screen ) ? false : ( 'ats_widgets_page_ats_admin_menu' === $current_screen->id ? true : false );

	}

	/**
	 * Check if current screen is admin bar page.
	 *
	 * @return boolean
	 */
	public function is_admin_bar() {

		$current_screen = get_current_screen();
		return is_null( $current_screen ) ? false : ( 'ats_widgets_page_ats_admin_bar' === $current_screen->id ? true : false );

	}

	/**
	 * Check if current screen is block editor page.
	 *
	 * @return boolean
	 */
	public function is_block_editor_page() {

		$current_screen = get_current_screen();

		if ( is_null( $current_screen ) ) {
			return false;
		}

		if ( property_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor ) {
			return true;
		}

		if ( 'block-editor-page' === $current_screen->id ) {
			return true;
		}

		return false;

	}
}
