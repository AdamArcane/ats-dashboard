<?php
/**
 * Instant install module.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\InstantInstall;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Helpers\Multisite_Helper;

/**
 * Class to setup "instant install" module.
 */
class Instant_Install_Module {

	/**
	 * The class instance.
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * The current module url.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Module constructor.
	 */
	public function __construct() {

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/instant-install';

	}

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

		$instance = new self();
		$instance->setup();

	}

	/**
	 * Setup branding module.
	 */
	public function setup() {

		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		// Check whether ATS Dashboard free is active or not.
		if ( ! defined( 'ATS_DASHBOARD_PLUGIN_VERSION' ) ) {

			add_action( 'admin_notices', array( self::get_instance(), 'free_version_notice' ) );

		} else {
			if ( version_compare( ATS_DASHBOARD_PLUGIN_VERSION, '3.0', '<' ) ) {

				add_action( 'admin_notices', array( self::get_instance(), 'lower_free_version_notice' ) );

			}
		}

		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_assets' ) );

	}

	/**
	 * Admin notice to require free version of the plugin.
	 *
	 * @return void
	 */
	public function free_version_notice() {

		$notice_class = 'notice notice-warning';

		if ( file_exists( WP_PLUGIN_DIR . '/' . ATS_DASHBOARD_PLUGIN_FILE ) ) {
			$button_text  = __( 'Activate Now', 'ats-dashboard' );
			$action_class = 'ats-activate-plugin';
		} else {
			$button_text  = __( 'Install Now', 'ats-dashboard' );
			$action_class = 'ats-install-plugin';
		}

		$install_button = '<button type="button" class="button button-primary ats-button ' . $action_class . '">' . $button_text . '</button>';

		$description  = '<h2>' . __( 'ATS Dashboard', 'ats-dashboard' ) . '</h2>';
		$description .= __( '<strong>ATS Dashboard</strong> requires the <strong>ATS Dashboard</strong> plugin to run on your WordPress installation.', 'ats-dashboard' );
		$description .= '<br><br>';
		$description .= $install_button;

		printf( '<div class="%1s"><p>%2s</p></div>', $notice_class, $description );

	}

	/**
	 * Admin notice which shows that the free version is lower version 3.0.
	 *
	 * Example case:
	 *
	 * This could happen when they're still using version 2.x of the free version.
	 * Then without upgrading to version 3, they decide to buy the Pro one.
	 * And then they just install it without deactivating the old free version.
	 */
	public function lower_free_version_notice() {

		$notice_class  = 'notice notice-warning';
		$button_text   = __( 'Update Now', 'ats-dashboard' );
		$update_button = '<button type="button" class="button button-primary ats-button ats-update-plugin">' . $button_text . '</button>';

		$description  = '<h2>' . __( 'ATS Dashboard 3.0', 'ats-dashboard' ) . '</h2>';
		$description .= __( 'Since version 3.0, <strong>ATS Dashboard</strong> requires at minimum <strong>ATS Dashboard</strong> version 3 to run on your WordPress installation.', 'ats-dashboard' );
		$description .= '<br><br>';
		$description .= $update_button;

		printf( '<div class="%1s"><p>%2s</p></div>', $notice_class, $description );

	}

	/**
	 * Enqueue admin assets.
	 */
	public function admin_assets() {

		wp_enqueue_style( 'ats-install-plugin', $this->url . '/assets/css/install.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );
		wp_enqueue_script( 'ats-install-plugin', $this->url . '/assets/js/install.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		$slug   = 'ats-dashboard';
		$plugin = $slug . '/ats-dashboard.php';

		$activate_url = add_query_arg(
			array(
				'action'        => 'activate',
				'plugin'        => rawurlencode( $plugin ),
				'plugin_status' => 'all',
				'paged'         => '1',
				'_wpnonce'      => wp_create_nonce( 'activate-plugin_' . $plugin ),
			),
			esc_url( network_admin_url( 'plugins.php' ) )
		);

		wp_localize_script(
			'ats-install-plugin',
			'atsInstantInstall',
			array(
				'pluginPath'  => $plugin,
				'pluginSlug'  => $slug,
				'isActivated' => ( defined( 'ATS_DASHBOARD_PLUGIN_VERSION' ) ? true : false ),
				'redirectUrl' => admin_url( 'edit.php?post_type=ats_widgets' ),
				'activateUrl' => $activate_url,
				'updateNonce' => wp_create_nonce( 'upgrade-plugin_' . $plugin ),
				'texts'       => array(
					'update'     => __( 'Update', 'ats-dashboard' ),
					'updating'   => __( 'Updating...', 'ats-dashboard' ),
					'installing' => __( 'Installing...', 'ats-dashboard' ),
					'activate'   => __( 'Activate Plugin', 'ats-dashboard' ),
					'activating' => __( 'Activating...', 'ats-dashboard' ),
				),
			)
		);

	}

}
