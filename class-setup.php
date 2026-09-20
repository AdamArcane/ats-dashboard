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

		$this->load_helpers();

		register_activation_hook( ATS_DASHBOARD_PLUGIN_FILE, array( $this, 'on_plugin_activation' ) );
		register_deactivation_hook( ATS_DASHBOARD_PLUGIN_FILE, array( $this, 'deactivation' ) );

		add_action( 'plugins_loaded', array( $this, 'load_modules' ), 20 );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_onboarding_module' ), 20 );

		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'register_action_links' ) );
		add_action( 'init', array( self::get_instance(), 'check_activation_meta' ) );

		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
		add_filter( 'ats_content_editor', array( $this, 'get_content_editor' ), 10, 2 );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		$content_helper = new Content_Helper();
		add_filter( 'wp_kses_allowed_html', array( $content_helper, 'allow_iframes_in_html' ) );

	}

	/**
	 * Load ATS Dashboard helper & base module/output classes.
	 */
	public function load_helpers() {

		// Helper classes.
		require __DIR__ . '/helpers/class-screen-helper.php';
		require __DIR__ . '/helpers/class-color-helper.php';
		require __DIR__ . '/helpers/class-widget-base-helper.php';
		require __DIR__ . '/helpers/class-content-base-helper.php';
		require __DIR__ . '/helpers/class-user-helper.php';
		require __DIR__ . '/helpers/class-array-helper.php';
		require __DIR__ . '/helpers/class-admin-bar-helper.php';
		require __DIR__ . '/helpers/class-vars.php';

		// Base module/output classes.
		require __DIR__ . '/modules/base/class-base-module.php';
		require __DIR__ . '/modules/base/class-base-output.php';
		require __DIR__ . '/modules/admin-bar/class-admin-bar-base.php';
		require __DIR__ . '/modules/admin-page/class-admin-page-base.php';
		require __DIR__ . '/modules/widget/class-widget-base.php';
		require __DIR__ . '/modules/branding/class-branding-base-output.php';
		require __DIR__ . '/modules/login-customizer/class-login-customizer-base.php';

		// Pro helper classes.
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
	 * Store an option that tracks the plugin activation.
	 */
	public function on_plugin_activation() {

		// Stop if this is activation from Erident's migration to ATS.
		if ( get_option( 'ats_migration_from_erident' ) ) {
			return;
		}

		// We bail out early in multisite because this function will still be called in the main site.
		if ( is_multisite() ) {
			return;
		}

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
						$this->delete_ats_data( $site_id );
					}
				}
			} else {
				// When blueprint is not set, we check the data removal option per-site id.
				foreach ( $site_ids as $site_id ) {
					$settings    = get_blog_option( $site_id, 'ats_settings', array() );
					$remove_data = isset( $settings['remove-on-uninstall'] );

					if ( $remove_data ) {
						$this->delete_ats_data( $site_id );
					}
				}
			}
		} else {
			$settings    = get_option( 'ats_settings' );
			$remove_data = isset( $settings['remove-on-uninstall'] );

			if ( $remove_data ) {
				$this->delete_ats_data();
			}
		}

	}

	/**
	 * Delete ATS Dashboard options on plugin deactivation.
	 *
	 * `ats_multisite_blueprint` is intentionally left alone so the blueprint
	 * value survives a deactivation/reactivation cycle.
	 *
	 * @param int|null $site_id The site id or null.
	 */
	public function delete_ats_data( $site_id = null ) {

		$options = array(
			'ats_settings',
			'ats_branding',
			'ats_login',
			'ats_login_redirect',
			'ats_integrations',
			'ats_import',
			'ats_modules',
			'ats_recent_admin_menu',
			'ats_admin_menu',
			'ats_admin_bar',
			'ats_pro_widget_order',
			'ats_multisite_exclude',
			'ats_multisite_widget_order',
			'ats_multisite_capability',
			'ats_pro_site_url',
			'ats_pro_plugin_activated',
			'ats_block_template_flush_rewrite_rules',
			'ats_compat_widget_type',
			'ats_compat_widget_status',
			'ats_compat_branding_meta',
			'ats_compat_delete_login_customizer_page',
			'ats_compat_settings_meta',
			'ats_compat_old_option',
			'ats_migration_from_erident',
			'ats_referred_by_kirki',
			'ats_login_customizer_flush_url',
			'review_notice_dismissed',
			'ats_install_date',
			'ats_plugin_activated',
		);

		foreach ( $options as $option_name ) {
			if ( $site_id ) {
				delete_blog_option( $site_id, $option_name );
			} else {
				delete_option( $option_name );
			}
		}

	}

	/**
	 * Check plugin activation meta.
	 */
	public function check_activation_meta() {

		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		if ( ! get_option( 'ats_plugin_activated' ) ) {
			update_option( 'ats_install_date', current_time( 'mysql' ) );
			update_option( 'ats_plugin_activated', 1 );
		}

		if ( ! get_option( 'ats_pro_plugin_activated' ) ) {
			update_option( 'ats_pro_site_url', $_SERVER['SERVER_NAME'] );
			update_option( 'ats_pro_plugin_activated', 1 );
		}

	}

	/**
	 * Admin body class.
	 *
	 * @param string $classes The class names.
	 */
	public function admin_body_class( $classes ) {

		$ms_helper = new Multisite_Helper();

		if ( $ms_helper->multisite_supported() ) {
			if ( is_network_admin() ) {
				$classes .= ' ats-is-network-admin';
			}

			if ( is_main_site() ) {
				$classes .= ' ats-is-main-site';
			} else {
				$classes .= ' ats-is-subsite';
			}

			$classes .= ' ats-site-' . get_current_blog_id();
		}

		$current_user = wp_get_current_user();
		$classes     .= ' ats-user-' . $current_user->user_nicename;

		$roles = $current_user->roles;
		$roles = $roles ? $roles : array();

		foreach ( $roles as $role ) {
			$classes .= ' ats-role-' . $role;
		}

		$screens = array(
			'ats_widgets_page_ats_features',
			'ats_widgets_page_ats-license',
			'ats_widgets_page_ats_tools',
			'ats_widgets_page_ats_branding',
			'ats_widgets_page_ats_settings',
			'ats_widgets_page_ats_login_redirect',
			'ats_widgets_page_ats_admin_menu',
			'ats_widgets_page_ats_admin_bar',
			'ats_widgets_page_ats_plugin_onboarding',
		);

		$screen = get_current_screen();

		if ( ! $screen || ! in_array( $screen->id, $screens, true ) ) {
			return $classes;
		}

		$classes .= ' heatbox-admin has-header';

		return $classes;

	}

	/**
	 * Filter the "get_content_editor" value of Content_Helper class.
	 *
	 * @param string $editor The editor name.
	 * @param int    $post_id ID of the post being checked.
	 *
	 * @return string The content editor name.
	 */
	public function get_content_editor( $editor, $post_id ) {

		$content_helper = new Content_Helper();

		return $content_helper->get_content_editor( $post_id );

	}

	/**
	 * Get saved/default modules.
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
			'integrations'      => 'true',
		);

		$saved_modules = get_option( 'ats_modules', $defaults );
		$new_modules   = array_diff_key( $defaults, $saved_modules );

		if ( ! empty( $new_modules ) ) {
			$updated_modules = array_merge( $saved_modules, $new_modules );
			update_option( 'ats_modules', $updated_modules );
		}

		$saved_modules = get_option( 'ats_modules', $defaults );

		$ms_helper = new Multisite_Helper();

		if ( $ms_helper->needs_to_switch_blog() ) {
			global $blueprint;

			// If we need to switch blog, let's grab ats_modules from the blueprint.
			$saved_modules = get_blog_option( $blueprint, 'ats_modules', $defaults );
		}

		return $saved_modules;

	}

	/**
	 * Load ATS Dashboard modules.
	 */
	public function load_modules() {

		$modules = array();

		$modules['ATSDash\\Feature\\Feature_Module']              = __DIR__ . '/modules/feature/class-feature-module.php';
		$modules['ATSDash\\Setting\\Setting_Module']              = __DIR__ . '/modules/setting/class-setting-module.php';
		$modules['ATSDash\\Widget\\Widget_Module']                = __DIR__ . '/modules/widget/class-widget-module.php';
		$modules['ATSDash\\BlockTemplate\\Block_Template_Module'] = __DIR__ . '/modules/block-template/class-block-template-module.php';
		$modules['ATSDash\\Tool\\Tool_Module']                    = __DIR__ . '/modules/tool/class-tool-module.php';

		$saved_modules = $this->saved_modules();

		if ( isset( $saved_modules['white_label'] ) && 'true' === $saved_modules['white_label'] ) {
			$modules['ATSDash\\Branding\\Branding_Module'] = __DIR__ . '/modules/branding/class-branding-module.php';
		}

		if ( isset( $saved_modules['login_customizer'] ) && 'true' === $saved_modules['login_customizer'] ) {
			$modules['ATSDash\\LoginCustomizer\\Login_Customizer_Module'] = __DIR__ . '/modules/login-customizer/class-login-customizer-module.php';
		}

		if ( isset( $saved_modules['login_redirect'] ) && 'true' === $saved_modules['login_redirect'] ) {
			$modules['ATSDash\\LoginRedirect\\Login_Redirect_Module'] = __DIR__ . '/modules/login-redirect/class-login-redirect-module.php';
		}

		if ( isset( $saved_modules['integrations'] ) && 'true' === $saved_modules['integrations'] ) {
			$modules['ATSDash\\Integrations\\Integrations_Module'] = __DIR__ . '/modules/integrations/class-integrations-module.php';
		}

		if ( isset( $saved_modules['admin_pages'] ) && 'true' === $saved_modules['admin_pages'] ) {
			$modules['ATSDash\\AdminPage\\Admin_Page_Module'] = __DIR__ . '/modules/admin-page/class-admin-page-module.php';
		}

		if ( isset( $saved_modules['admin_menu_editor'] ) && 'true' === $saved_modules['admin_menu_editor'] ) {
			$modules['ATSDash\\AdminMenu\\Admin_Menu_Module'] = __DIR__ . '/modules/admin-menu/class-admin-menu-module.php';
		}

		if ( isset( $saved_modules['admin_bar_editor'] ) && 'true' === $saved_modules['admin_bar_editor'] ) {
			$modules['ATSDash\\AdminBar\\Admin_Bar_Module'] = __DIR__ . '/modules/admin-bar/class-admin-bar-module.php';
		}

		$ms_helper = new Multisite_Helper();

		if ( $ms_helper->multisite_supported() ) {
			$modules['ATSDash\\Multisite\\Multisite_Module'] = __DIR__ . '/modules/multisite/class-multisite-module.php';
		}

		foreach ( $modules as $class => $file ) {
			$splits      = explode( '/', $file );
			$module_name = $splits[ count( $splits ) - 2 ];
			$filter_name = str_ireplace( '-', '_', $module_name );
			$filter_name = 'ats_' . $filter_name;

			// We have a filter here ats_$module_name to allow us to prevent loading modules under certain circumstances.
			if ( apply_filters( $filter_name, true ) ) {

				require_once $file;
				$module = new $class();
				$module->setup();

			}
		}

	}

	/**
	 * Load plugin onboarding module.
	 */
	public function load_plugin_onboarding_module() {

		$need_setup = false;
		$referrer   = '';

		// Erident's migration takes the highest priority.
		if ( get_option( 'ats_migration_from_erident' ) ) {
			$need_setup = true;
			$referrer   = 'erident';
		} elseif ( get_option( 'ats_referred_by_kirki' ) ) {
			$need_setup = true;
			$referrer   = 'kirki';
		}
		// In the future, we might allow ats to be installed from other plugins as well.

		if ( ! $need_setup ) {
			return;
		}

		require_once __DIR__ . '/modules/plugin-onboarding/class-plugin-onboarding-module.php';
		$module = new PluginOnboarding\Plugin_Onboarding_Module();
		$module->setup( $referrer );

	}

	/**
	 * Enqueue admin styles.
	 */
	public function admin_styles() {

		wp_enqueue_style( 'ats-admin', ATS_DASHBOARD_PLUGIN_URL . '/assets/css/admin.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

	}

	/**
	 * Enqueue admin scripts.
	 */
	public function admin_scripts() {

		wp_enqueue_script( 'ats-notice-dismissal', ATS_DASHBOARD_PLUGIN_URL . '/assets/js/notice-dismissal.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		wp_localize_script(
			'ats-notice-dismissal',
			'atsNoticeDismissal',
			array(
				'nonce' => wp_create_nonce( 'ats_dismiss_notice' ),
			)
		);

	}

}
