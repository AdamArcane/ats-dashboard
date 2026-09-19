<?php
/**
 * Setup ATS Dashboard plugin.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Helpers\Content_Helper;
use ATSDash\Helpers\Multisite_Helper;

/**
 * Class to set up ATS Dashboard plugin.
 */
class Setup {

	/**
	 * The class instanace
	 *
	 * @var object
	 */
	public static $instance = null;

	/**
	 * Get the class instance.
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
	 * Init the class setup.
	 */
	public static function init() {

		add_action( 'plugins_loaded', array( self::get_instance(), 'setup' ) );

	}

	/**
	 * Set up the class.
	 */
	public function setup() {

		$blueprint = get_site_option( 'ats_multisite_blueprint' );

		// Declare glolbal variables.
		$GLOBALS['blueprint'] = $blueprint ? (int) $blueprint : 0;

		// Enable multisite support.
		add_filter( 'ats_pro_ms_support', '__return_true' );

		require __DIR__ . '/helpers/class-multisite-helper.php';
		register_deactivation_hook( ATS_DASHBOARD_PLUGIN_FILE, array( $this, 'deactivation' ) );

		// Check whether ATS Dashboard free active status & version.
		if ( ! defined( 'ATS_DASHBOARD_PLUGIN_VERSION' ) || version_compare( ATS_DASHBOARD_PLUGIN_VERSION, '3.0', '<' ) ) {

			require __DIR__ . '/modules/instant-install/class-instant-install-module.php';
			InstantInstall\Instant_Install_Module::init();

			// Stop if ATS Dashboard free is not active, or it's version is lower than 3.0.
			return;

		}

		$this->load_helpers();
		Backwards_Compatibility::init();

		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'register_action_links' ) );
		add_action( 'init', array( self::get_instance(), 'check_activation_meta' ) );
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ), 30 );

		// Pro version filters.
		add_filter( 'ats_saved_modules', array( $this, 'saved_modules' ) );
		add_filter( 'ats_modules', array( $this, 'load_modules' ) );
		add_filter( 'ats_content_editor', array( $this, 'get_content_editor' ), 10, 2 );

	}

	/**
	 * Load ATS Dashboard helper classes.
	 *
	 * Note: the multisite helper has been loaded in the setup method above
	 * because it will be used as part of plugin de-activation process.
	 */
	public function load_helpers() {

		require __DIR__ . '/helpers/class-video-helper.php';
		require __DIR__ . '/helpers/class-content-helper.php';
		require __DIR__ . '/helpers/class-widget-helper.php';
		require __DIR__ . '/helpers/class-branding-helper.php';
		require __DIR__ . '/helpers/class-placeholder-helper.php';

		// Page builder helpers.
		require __DIR__ . '/helpers/class-divi-helper.php';
		require __DIR__ . '/helpers/class-bricks-helper.php';
		require __DIR__ . '/helpers/class-breakdance-helper.php';
		require __DIR__ . '/helpers/class-block-helper.php';

	}

	/**
	 * Load textdomain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'ats-dashboard', false, ATS_DASHBOARD_PLUGIN_DIR . '/languages' );
	}

	/**
	 * Register plugin action links filter after init hook.
	 * This ensures translations are loaded before the callback is executed.
	 */
	public function register_action_links() {
		$prefix = is_network_admin() ? 'network_admin_' : '';

		add_filter(
			$prefix . 'plugin_action_links_' . ATS_DASHBOARD_PLUGIN_FILE,
			array(
				$this,
				'action_links',
			)
		);
	}

	/**
	 * Add action links displayed in plugins page.
	 *
	 * @param array $links The action links array.
	 *
	 * @return array The modified action links array.
	 */
	public function action_links( $links ) {

		$multisite_settings = array();
		$settings           = array( '<a href="' . admin_url( 'edit.php?post_type=ats_widgets&page=ats_settings' ) . '">' . __( 'Settings', 'ats-dashboard' ) . '</a>' );

		if ( apply_filters( 'ats_pro_ms_support', false ) ) {
			$multisite_settings = is_multisite() ? array( '<a href="' . network_admin_url( 'settings.php?page=ats-dashboard-multisite' ) . '">' . __( 'Network Settings', 'ats-dashboard' ) . '</a>' ) : array();
		}

		return array_merge( $links, $settings, $multisite_settings );

	}

	/**
	 * Plugin deactivation.
	 */
	public function deactivation() {

		$ms_helper = new Multisite_Helper();

		if ( $ms_helper->multisite_supported() ) {
			global $blueprint;

			$site_ids = get_sites(
				array(
					'fields' => 'ids',
				)
			);

			if ( $blueprint ) {
				// When blueprint is set, we get the data removal option from the blueprint site only.
				$settings    = get_blog_option( $blueprint, 'ats_settings', array() );
				$remove_data = isset( $settings['remove-on-uninstall'] );

				if ( $remove_data ) {
					foreach ( $site_ids as $site_id ) {
						$this->delete_ats_pro_data( $site_id );
					}
				}
			} else {
				// When blueprint is not set, we check the data removal option per-site id.
				foreach ( $site_ids as $site_id ) {
					$settings    = get_blog_option( $site_id, 'ats_settings', array() );
					$remove_data = isset( $settings['remove-on-uninstall'] );

					if ( $remove_data ) {
						$this->delete_ats_pro_data( $site_id );
					}
				}
			}
		} else {
			$settings    = get_option( 'ats_settings' );
			$remove_data = isset( $settings['remove-on-uninstall'] );

			if ( $remove_data ) {
				$this->delete_ats_pro_data();
			}
		}

	}

	/**
	 * Delete pro-related options on plugin deactivation.
	 *
	 * But `ats_multisite_blueprint` won't be deleted on both free & pro versions deactivation.
	 * So that it wouldn't be a problem if user deactivate free version first or pro version first.
	 * Both versions will be able to get the blueprint value.
	 *
	 * So yea, `ats_multisite_blueprint` will stay in the database.
	 *
	 * @param int|null $site_id The site id or null.
	 */
	public function delete_ats_pro_data( $site_id = null ) {

		if ( $site_id ) {
			delete_blog_option( $site_id, 'ats_admin_menu' );
			delete_blog_option( $site_id, 'ats_admin_bar' );

			delete_blog_option( $site_id, 'ats_compat_branding_meta' );

			delete_blog_option( $site_id, 'ats_pro_widget_order' );
			delete_blog_option( $site_id, 'ats_multisite_exclude' );
			delete_blog_option( $site_id, 'ats_multisite_widget_order' );
			delete_blog_option( $site_id, 'ats_multisite_capability' );

			delete_blog_option( $site_id, 'ats_pro_site_url' );
			delete_blog_option( $site_id, 'ats_pro_plugin_activated' );

			delete_blog_option( $site_id, 'ats_block_template_flush_rewrite_rules' );

			// In case free version was deactivated first, we need to delete the restored settings option.
			if ( ! defined( 'ATS_DASHBOARD_PLUGIN_VERSION' ) ) {
				delete_blog_option( $site_id, 'ats_settings' );
			}
		} else {
			delete_option( 'ats_admin_menu' );
			delete_option( 'ats_admin_bar' );

			delete_option( 'ats_compat_branding_meta' );

			delete_option( 'ats_pro_widget_order' );
			delete_option( 'ats_multisite_exclude' );
			delete_option( 'ats_multisite_widget_order' );
			delete_option( 'ats_multisite_capability' );

			delete_option( 'ats_pro_site_url' );
			delete_option( 'ats_pro_plugin_activated' );

			delete_option( 'ats_block_template_flush_rewrite_rules' );

			// In case free version was deactivated first, we need to delete the restored settings option.
			if ( ! defined( 'ATS_DASHBOARD_PLUGIN_VERSION' ) ) {
				delete_option( 'ats_settings' );
			}
		}

	}

	/**
	 * Check plugin activation meta.
	 */
	public function check_activation_meta() {

		if ( ! current_user_can( 'activate_plugins' ) || get_option( 'ats_pro_plugin_activated' ) ) {
			return;
		}

		update_option( 'ats_pro_site_url', $_SERVER['SERVER_NAME'] );
		update_option( 'ats_pro_plugin_activated', 1 );

	}

	/**
	 * Admin body class.
	 *
	 * @param string $classes The class names.
	 */
	public function admin_body_class( $classes ) {

		$ms_helper = new Multisite_Helper();

		if ( ! $ms_helper->multisite_supported() ) {
			return $classes;
		}

		if ( is_network_admin() ) {
			$classes .= ' ats-is-network-admin';
		}

		if ( is_main_site() ) {
			$classes .= ' ats-is-main-site';
		} else {
			$classes .= ' ats-is-subsite';
		}

		$classes .= ' ats-site-' . get_current_blog_id();

		return $classes;

	}

	/**
	 * Filter the "get_content_editor" value of Content_Helper class in the free version.
	 *
	 * @param string $editor The editor name from free version.
	 * @param int    $post_id ID of the post being checked.
	 *
	 * @return string The content editor name from pro version.
	 */
	public function get_content_editor( $editor, $post_id ) {

		$content_helper = new Content_Helper();

		return $content_helper->get_content_editor( $post_id );

	}

	/**
	 * Get saved/default modules.
	 *
	 * Helper function, similar to what we have in the free version but with multisite support in mind.
	 * Also used to filter "ats_saved_modules" in the free version.
	 *
	 * @return array The saved/default modules.
	 */
	public function saved_modules() {

		$defaults = array(
			'white_label'       => 'true',
			'login_customizer'  => 'true',
			'login_redirect'    => 'true',
			'admin_pages'       => 'true',
			'admin_menu_editor' => 'true',
			'admin_bar_editor'  => 'true',
		);

		$saved_modules = get_option( 'ats_modules', $defaults );

		$ms_helper = new Helpers\Multisite_Helper();

		if ( $ms_helper->needs_to_switch_blog() ) {
			global $blueprint;

			// If we need to switch blog, let's grab ats_modules from the blueprint.
			$saved_modules = get_blog_option( $blueprint, 'ats_modules', $defaults );
		}

		return $saved_modules;

	}

	/**
	 * Load ATS Dashboard modules.
	 *
	 * @param array $modules The modules being loaded.
	 *
	 * @return array $modules The modules being loaded.
	 */
	public function load_modules( $modules ) {

		$modules['ATSDash\\Widget\\Widget_Module']   = __DIR__ . '/modules/widget/class-widget-module.php';
		$modules['ats\\Setting\\Setting_Module'] = __DIR__ . '/modules/setting/class-setting-module.php';

		$saved_modules = $this->saved_modules();

		if ( isset( $saved_modules['white_label'] ) && 'true' === $saved_modules['white_label'] ) {
			$modules['ATSDash\\Branding\\Branding_Module'] = __DIR__ . '/modules/branding/class-branding-module.php';
		}

		if ( isset( $saved_modules['login_customizer'] ) && 'true' === $saved_modules['login_customizer'] ) {
			$modules['ATSDash\\LoginCustomizer\\Login_Customizer_Module'] = __DIR__ . '/modules/login-customizer/class-login-customizer-module.php';
		}

		if ( isset( $saved_modules['login_redirect'] ) && 'true' === $saved_modules['login_redirect'] ) {
			$modules['ats\\LoginRedirect\\Login_Redirect_Module'] = __DIR__ . '/modules/login-redirect/class-login-redirect-module.php';
		}

		if ( isset( $saved_modules['admin_pages'] ) && 'true' === $saved_modules['admin_pages'] ) {
			$modules['ATSDash\\AdminPage\\Admin_Page_Module'] = __DIR__ . '/modules/admin-page/class-admin-page-module.php';
		}

		if ( isset( $saved_modules['admin_menu_editor'] ) && 'true' === $saved_modules['admin_menu_editor'] ) {
			$modules['ATSDash\\AdminMenu\\Admin_Menu_Module'] = __DIR__ . '/modules/admin-menu/class-admin-menu-module.php';
		}

		if ( version_compare( ATS_DASHBOARD_PLUGIN_VERSION, '3.2.1', '>' ) ) {
			if ( 'true' === $saved_modules['admin_bar_editor'] ) {
				$modules['ATSDash\\AdminBar\\Admin_Bar_Module'] = __DIR__ . '/modules/admin-bar/class-admin-bar-module.php';
			}
		}

		$modules['ATSDash\\BlockTemplate\\Block_Template_Module'] = __DIR__ . '/modules/block-template/class-block-template-module.php';

		$modules['ATSDash\\Tool\\Tool_Module']       = __DIR__ . '/modules/tool/class-tool-module.php';

		$ms_helper = new Helpers\Multisite_Helper();

		if ( $ms_helper->multisite_supported() ) {
			$modules['ATSDash\\Multisite\\Multisite_Module'] = __DIR__ . '/modules/multisite/class-multisite-module.php';
		}

		return $modules;

	}

}
