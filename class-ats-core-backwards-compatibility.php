<?php
/**
 * Backwards compatibility.
 *
 * @package ATS_Dashboard
 */

namespace ats;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Class that handles backwards compatibility.
 */
class Backwards_Compatibility {

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
	 * Init the class setup.
	 */
	public static function init() {
		$instance = new Backwards_Compatibility();
		$instance->setup();
	}

	/**
	 * Setup the class.
	 */
	public function setup() {

		add_action( 'ats_compat_widget_type', array( $this, 'parse_widget_type' ) );
		add_action( 'admin_init', array( $this, 'meta_compatibility' ) );

	}

	/**
	 * Handle ats_widget_type.
	 *
	 * Can be used for "whole checking" or "partial checking".
	 *
	 * @param int $post_id The post ID.
	 */
	public function parse_widget_type( $post_id ) {

		$widget_type = get_post_meta( $post_id, 'ats_widget_type', true );

		if ( ! $widget_type ) {

			$html    = get_post_meta( $post_id, 'ats_html', true );
			$content = get_post_meta( $post_id, 'ats_content', true );

			if ( $html ) {
				$widget_type = 'html';
			} elseif ( $content ) {
				$widget_type = 'text';
			} else {
				$widget_type = 'icon';
			}

			$widget_type = apply_filters( 'ats_parse_widget_type', $widget_type, $post_id );

			update_post_meta( $post_id, 'ats_widget_type', $widget_type );

		}

	}

	/**
	 * Run compatibility checking on admin_init hook.
	 */
	public function meta_compatibility() {

		// Don't run checking on heartbeat request.
		if ( isset( $_POST['action'] ) && 'heartbeat' === $_POST['action'] ) {
			return;
		}

		$this->delete_old_options();
		$this->check_widget_type();
		$this->check_widget_status();
		$this->replace_submeta_keys();
		$this->delete_unused_page();

		do_action( 'ats_meta_compatibility' );

	}

	/**
	 * Delete old options and move their value to $ats_settings.
	 */
	public function delete_old_options() {

		// Make sure we don't check again.
		if ( get_option( 'ats_compat_old_option' ) ) {
			return;
		}

		$ats_settings = get_option( 'ats_settings', array() );

		if ( ! $ats_settings ) {
			update_option( 'ats_settings', array() );
		}

		if ( isset( $ats_settings['remove_admin_bar'] ) && ! is_array( $ats_settings['remove_admin_bar'] ) ) {
			/**
			 * The previous format was just checkbox,
			 * so we need to convert it to new format which is array (by roles).
			 */
			$ats_settings['remove_admin_bar'] = [ 'all' ];
			update_option( 'ats_settings', $ats_settings );
		}

		if ( get_option( 'removeallwidgets' ) ) {
			$ats_settings['remove-all'] = 1;
			update_option( 'ats_settings', $ats_settings );
			delete_option( 'removeallwidgets' );
		}

		if ( get_option( 'welcome' ) ) {
			$ats_settings['welcome_panel'] = 1;
			update_option( 'ats_settings', $ats_settings );
			delete_option( 'welcome' );
		}

		if ( get_option( 'primary' ) ) {
			$ats_settings['dashboard_primary'] = 1;
			update_option( 'ats_settings', $ats_settings );
			delete_option( 'primary' );
		}

		if ( get_option( 'quickpress' ) ) {
			$ats_settings['dashboard_quick_press'] = 1;
			update_option( 'ats_settings', $ats_settings );
			delete_option( 'quickpress' );
		}

		if ( get_option( 'rightnow' ) ) {
			$ats_settings['dashboard_right_now'] = 1;
			update_option( 'ats_settings', $ats_settings );
			delete_option( 'rightnow' );
		}

		if ( get_option( 'activity' ) ) {
			$ats_settings['dashboard_activity'] = 1;
			update_option( 'ats_settings', $ats_settings );
			delete_option( 'activity' );
		}

		if ( get_option( 'incominglinks' ) ) {
			$ats_settings['dashboard_incoming_links'] = 1;
			update_option( 'ats_settings', $ats_settings );
			delete_option( 'incominglinks' );
		}

		if ( get_option( 'plugins' ) ) {
			$ats_settings['dashboard_plugins'] = 1;
			update_option( 'ats_settings', $ats_settings );
			delete_option( 'plugins' );
		}

		if ( get_option( 'secondary' ) ) {
			$ats_settings['dashboard_secondary'] = 1;
			update_option( 'ats_settings', $ats_settings );
			delete_option( 'secondary' );
		}

		if ( get_option( 'drafts' ) ) {
			$ats_settings['dashboard_recent_drafts'] = 1;
			update_option( 'ats_settings', $ats_settings );
			delete_option( 'drafts' );
		}

		if ( get_option( 'comments' ) ) {
			$ats_settings['dashboard_recent_comments'] = 1;
			update_option( 'ats_settings', $ats_settings );
			delete_option( 'comments' );
		}

		do_action( 'ats_delete_old_options' );

		// Make sure we don't check again.
		update_option( 'ats_compat_old_option', 1 );

	}

	/**
	 * Whole checking ats_widget_type compatibility.
	 */
	public function check_widget_type() {

		// Make sure we don't check again.
		if ( get_option( 'ats_compat_widget_type' ) ) {
			return;
		}

		$widgets = get_posts(
			array(
				'post_type'   => 'ats_widgets',
				'numberposts' => -1,
				'post_status' => 'any',
			)
		);

		if ( ! $widgets ) {
			return;
		}

		foreach ( $widgets as $widget ) {
			do_action( 'ats_compat_widget_type', $widget->ID );
		}

		// Make sure we don't check again.
		update_option( 'ats_compat_widget_type', 1 );

	}

	/**
	 * Whole checking ats_widget_status compatibility.
	 */
	public function check_widget_status() {

		// Make sure we don't check again.
		if ( get_option( 'ats_compat_widget_status' ) ) {
			return;
		}

		$widgets = get_posts(
			array(
				'post_type'   => 'ats_widgets',
				'numberposts' => -1,
				'post_status' => 'any',
			)
		);

		if ( ! $widgets ) {
			// Make sure we don't check again.
			update_option( 'ats_compat_widget_status', 1 );

			return;
		}

		foreach ( $widgets as $widget ) {
			update_post_meta( $widget->ID, 'ats_is_active', 1 );
		}

		// Make sure we don't check again.
		update_option( 'ats_compat_widget_status', 1 );

	}

	/**
	 * Move ats_pro_settings to ats_settings.
	 */
	public function replace_submeta_keys() {

		// Make sure we don't check again.
		if ( get_option( 'ats_compat_settings_meta' ) ) {
			return;
		}

		$setting_opts = get_option( 'ats_settings', array() );
		$pro_opts     = get_option( 'ats_pro_settings', array() );

		$update_setting_opts = false;

		// Dashboard's custom css.
		if ( isset( $pro_opts['custom_css'] ) ) {
			$setting_opts['custom_css'] = $pro_opts['custom_css'];
			$update_setting_opts        = true;

			unset( $pro_opts['custom_css'] );
		}

		// Update the settings meta if necessary.
		if ( $update_setting_opts ) {
			update_option( 'ats_settings', $setting_opts );
		}

		do_action( 'ats_replace_submeta_keys' );

		// Delete ats_pro_settings, since we don't use it anymore.
		delete_option( 'ats_pro_settings' );

		// Make sure we don't check again.
		update_option( 'ats_compat_settings_meta', 1 );

	}

	/**
	 * Delete un-used auto-generated "Login Customizer" page (with 'ats-login-page' slug).
	 * That page existed in 2.7 of the FREE version.
	 */
	public function delete_unused_page() {

		// Make sure we don't check again.
		if ( get_option( 'ats_compat_delete_login_customizer_page' ) ) {
			return;
		}

		$page = get_page_by_path( 'ats-login-page' );

		if ( ! empty( $page ) && is_object( $page ) ) {
			wp_delete_post( $page->ID, true );
		}

		// Make sure we don't check again.
		update_option( 'ats_compat_delete_login_customizer_page', 1 );

	}
}
