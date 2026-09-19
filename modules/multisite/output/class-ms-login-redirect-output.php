<?php
/**
 * Login url output.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Multisite\Output;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Helpers\Multisite_Helper;
use ats\Base\Base_Output;
use ats\LoginRedirect\Login_Redirect_Output as Free_Login_Redirect_Output;

/**
 * Class to setup login url output.
 */
class Ms_Login_Redirect_Output extends Base_Output {

	/**
	 * The class instance.
	 *
	 * @var object
	 */
	public static $instance = null;

	/**
	 * The current module url.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * The multisite helper object.
	 *
	 * @var Multisite_Helper Instance of Multisite_Helper class.
	 */
	public $ms_helper;

	/**
	 * Get instance of the class.
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Module constructor.
	 */
	public function __construct() {

		$this->url       = ATS_DASHBOARD_PLUGIN_URL . '/modules/login-url';
		$this->ms_helper = new Multisite_Helper();

	}

	/**
	 * Init the class setup.
	 */
	public static function init() {

		$class = new self();
		$class->setup();

	}

	/**
	 * Setup widgets output.
	 */
	public function setup() {

		add_filter( 'ats_login_slug', array( $this, 'new_login_slug' ) );
		add_filter( 'ats_wp_admin_redirect_slug', array( $this, 'wp_admin_redirect_slug' ) );

		// Let's remove free version's `login_redirect` filter.
		remove_filter( 'login_redirect', array( Free_Login_Redirect_Output::get_instance(), 'custom_login_redirect' ), 1000000000, 3 );

		// Then add multisite version of `login_redirect` filter.
		add_filter( 'login_redirect', array( $this, 'custom_login_redirect' ), 1000000010, 3 );

	}

	/**
	 * Filter ats login url slug.
	 *
	 * @param string $slug The existing slug.
	 * @return string
	 */
	public function new_login_slug( $slug ) {

		if ( ! $this->ms_helper->is_blueprint_site() ) {
			global $blueprint;

			$settings = get_blog_option( $blueprint, 'ats_login_redirect', array() );
			$slug     = isset( $settings['login_url_slug'] ) ? $settings['login_url_slug'] : '';
		}

		return $slug;

	}

	/**
	 * Filter ats wp-admin redirect url slug.
	 *
	 * @param string $slug The existing slug.
	 * @return string
	 */
	public function wp_admin_redirect_slug( $slug ) {

		if ( ! $this->ms_helper->is_blueprint_site() ) {
			global $blueprint;

			$settings = get_blog_option( $blueprint, 'ats_login_redirect', array() );
			$slug     = isset( $settings['wp_admin_redirect_slug'] ) ? $settings['wp_admin_redirect_slug'] : '';
		}

		return $slug;

	}

	/**
	 * Filters the login redirect URL.
	 *
	 * @param string           $redirect_to The redirect destination URL.
	 * @param string           $requested_redirect_to The requested redirect destination URL passed as a parameter.
	 * @param WP_User|WP_Error $user WP_User object if login was successful, WP_Error object otherwise.
	 *
	 * @return string
	 */
	public function custom_login_redirect( $redirect_to, $requested_redirect_to, $user ) {

		$roles        = property_exists( $user, 'roles' ) ? $user->roles : array();
		$is_blueprint = $this->ms_helper->is_blueprint_site();

		if ( empty( $roles ) ) {
			return $redirect_to;
		}

		// ! Don't omit the $user->ID check, omitting it will return false even if current user is actually a super admin.
		$is_super_admin = is_super_admin( $user->ID );

		$settings = get_option( 'ats_login_redirect', array() );
		$slugs    = isset( $settings['login_redirect_slugs'] ) ? $settings['login_redirect_slugs'] : array();

		if ( ! $is_blueprint ) {
			global $blueprint;

			switch_to_blog( $blueprint );

			$settings = get_option( 'ats_login_redirect', array() );
			$slugs    = isset( $settings['subsites_login_redirect_slugs'] ) ? $settings['subsites_login_redirect_slugs'] : array();

			restore_current_blog();
		}

		/**
		 * Before checking individual role, let's check for 'super_admin' role.
		 * The 'super_admin' role should have higher priority than individual role.
		 */
		if ( $is_super_admin && isset( $slugs['super_admin'] ) && ! empty( $slugs['super_admin'] ) ) {
			return site_url( $slugs['super_admin'] );
		}

		// If super admin checking doesn't match, then check for individual role.
		foreach ( $roles as $role ) {
			if ( isset( $slugs[ $role ] ) && ! empty( $slugs[ $role ] ) ) {
				return site_url( $slugs[ $role ] );
			}
		}

		// If current user role doesn't match with blueprint subsite's setting, then use the default / existing $redirect_to value.
		return $redirect_to;

	}

}
