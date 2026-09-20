<?php
/**
 * Setup ATS Dashboard plugin.
 *
 * @package ATS_Dashboard
 */

namespace ats;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Vars;
use ats\Helpers\Content_Helper;

/**
 * Class to setup ATS Dashboard plugin.
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

		$instance = new Setup();
		$instance->setup();

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

		return apply_filters( 'ats_saved_modules', get_option( 'ats_modules', $defaults ) );

	}

	/**
	 * Setup the class.
	 */
	public function setup() {

		/**
		 * We use 20 as the priority in the free version
		 * because the PRO version has to run first.
		 *
		 * The PRO version has to run before the free version
		 * because the PRO version hooks some action/filter to the free version.
		 * So in order to make them executed, the PRO version has to run first.
		 */

		register_activation_hook( ATS_DASHBOARD_PLUGIN_FILE, array( $this, 'on_plugin_activation' ), 20 );

		add_action( 'plugins_loaded', array( $this, 'load_modules' ), 20 );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_onboarding_module' ), 20 );
		add_action( 'init', array( self::get_instance(), 'check_activation_meta' ) );
		add_action( 'init', array( $this, 'register_action_links' ) );
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 20 );

		register_deactivation_hook( ATS_DASHBOARD_PLUGIN_FILE, array( $this, 'deactivation' ), 20 );

		$content_helper = new Content_Helper();
		add_filter( 'wp_kses_allowed_html', array( $content_helper, 'allow_iframes_in_html' ) );

	}

	/**
	 * Preload ats settings.
	 * This will reduce the repeated call of get_option across modules.
	 *
	 * @deprecated 3.7.15 Not good for performance. This method was called both in admin & front area. The benefits were not much, because there was only few places where the preloaded data was re-used.
	 */
	public function set_data() {}

	/**
	 * Check plugin activation meta.
	 */
	public function check_activation_meta() {

		if ( ! current_user_can( 'activate_plugins' ) || get_option( 'ats_plugin_activated' ) ) {
			return;
		}

		update_option( 'ats_install_date', current_time( 'mysql' ) );
		update_option( 'ats_plugin_activated', 1 );

	}

	/**
	 * Admin body class.
	 *
	 * @param string $classes The class names.
	 */
	public function admin_body_class( $classes ) {

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

		if ( ! in_array( $screen->id, $screens, true ) ) {
			return $classes;
		}

		$classes .= ' heatbox-admin has-header';

		return $classes;

	}

	/**
	 * Register plugin action links filter after init hook.
	 * This ensures translations are loaded before the callback is executed.
	 */
	public function register_action_links() {
		add_filter( 'plugin_action_links_' . ATS_DASHBOARD_PLUGIN_FILE, array( $this, 'action_links' ) );
	}

	/**
	 * Add action links displayed in plugins page.
	 *
	 * @param array $links The action links array.
	 * @return array The modified action links array.
	 */
	public function action_links( $links ) {

		$settings = array( '<a href="' . admin_url( 'edit.php?post_type=ats_widgets&page=ats_settings' ) . '">' . __( 'Settings', 'ats-dashboard' ) . '</a>' );

		return array_merge( $links, $settings );

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
	 * Load ATS Dashboard modules.
	 */
	public function load_modules() {
		$modules = array();

		if ( ! defined( 'ATS_DASHBOARD_PLUGIN_VERSION' ) || version_compare( ATS_DASHBOARD_PLUGIN_VERSION, '3.1', '>=' ) ) {
			$modules['ats\\Feature\\Feature_Module'] = __DIR__ . '/modules/feature/class-feature-module.php';
		}

		$modules['ats\\Setting\\Setting_Module'] = __DIR__ . '/modules/setting/class-setting-module.php';

		$saved_modules = $this->saved_modules();

		if ( isset( $saved_modules['white_label'] ) && 'true' === $saved_modules['white_label'] ) {
		}

		if ( isset( $saved_modules['admin_pages'] ) && 'true' === $saved_modules['admin_pages'] ) {
		}

		if ( isset( $saved_modules['login_customizer'] ) && 'true' === $saved_modules['login_customizer'] ) {
		}

		if ( isset( $saved_modules['login_redirect'] ) && 'true' === $saved_modules['login_redirect'] ) {
			$modules['ats\\LoginRedirect\\Login_Redirect_Module'] = __DIR__ . '/modules/login-redirect/class-login-redirect-module.php';
		}

		if ( isset( $saved_modules['integrations'] ) && 'true' === $saved_modules['integrations'] ) {
			$modules['ats\\Integrations\\Integrations_Module'] = __DIR__ . '/modules/integrations/class-integrations-module.php';
		}

		if ( isset( $saved_modules['admin_menu_editor'] ) && 'true' === $saved_modules['admin_menu_editor'] ) {
		}

		if ( isset( $saved_modules['admin_bar_editor'] ) && 'true' === $saved_modules['admin_bar_editor'] ) {
		}

		$modules['ats\\Tool\\Tool_Module'] = __DIR__ . '/modules/tool/class-tool-module.php';

		$modules = apply_filters( 'ats_modules', $modules );

		foreach ( $modules as $class => $file ) {
			$splits      = explode( '/', $file );
			$module_name = $splits[ count( $splits ) - 2 ];
			$filter_name = str_ireplace( '-', '_', $module_name );
			$filter_name = 'ats_' . $filter_name;

			// We have a filter here ats_$module_name to allow us to prevent loading modules under certain circumstances.
			// Not sure if currently in use.
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

		wp_enqueue_style( 'ats-admin', ATS_DASHBOARD_CORE_URL . '/assets/css/admin.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

	}

	/**
	 * Enqueue admin scripts.
	 */
	public function admin_scripts() {

		wp_enqueue_script( 'ats-notice-dismissal', ATS_DASHBOARD_CORE_URL . '/assets/js/notice-dismissal.js', array( 'jquery' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		wp_localize_script(
			'ats-notice-dismissal',
			'atsNoticeDismissal',
			array(
				'nonce' => wp_create_nonce( 'ats_dismiss_notice' ),
			)
		);

	}

	/**
	 * Plugin deactivation.
	 */
	public function deactivation() {

		if ( $this->multisite_supported() ) {
			$blueprint = get_site_option( 'ats_multisite_blueprint' );
			$blueprint = $blueprint ? (int) $blueprint : 0;

			$site_ids = get_sites(
				array(
					'fields' => 'ids',
				)
			);

			if ( $blueprint ) {
				// When blueprint is set, we get the data removal option from blueprint only.
				$settings    = get_blog_option( $blueprint, 'ats_settings', array() );
				$remove_data = isset( $settings['remove-on-uninstall'] ) ? true : false;

				if ( $remove_data ) {
					foreach ( $site_ids as $site_id ) {
						if ( $site_id !== $blueprint ) {
							// Don't restore the data removal option if $site_id is not the blueprint.
							$this->delete_ats_data( $site_id, false );
						} else {
							$this->delete_ats_data( $site_id );
						}
					}
				}
			} else {
				// When blueprint is not set, we check the data removal option per-site id.
				foreach ( $site_ids as $site_id ) {
					$settings    = get_blog_option( $site_id, 'ats_settings', array() );
					$remove_data = isset( $settings['remove-on-uninstall'] ) ? true : false;

					if ( $remove_data ) {
						$this->delete_ats_data( $site_id );
					}
				}
			}
		} else {
			$settings    = get_option( 'ats_settings' );
			$remove_data = isset( $settings['remove-on-uninstall'] ) ? true : false;

			if ( $remove_data ) {
				$this->delete_ats_data( null );
			}
		}

	}

	/**
	 * Delete free-related options on plugin deactivation.
	 *
	 * We still have `ats_multisite_blueprint` option and
	 * it won't be deleted on both free & pro versions deactivation.
	 * So that it wouldn't be a problem if user deactivate free version first or pro version first.
	 * Both versions will be able to get the blueprint value.
	 *
	 * So yea, `ats_multisite_blueprint` will stays in the database.
	 *
	 * @param int|null $site_id The site id or null.
	 * @param bool     $restore_removal_option Whether to restore the data removal option or not.
	 *                                         This is used to handle the case when the free version is de-activated first,
	 *                                         then the pro version is de-activated later.
	 */
	public function delete_ats_data( $site_id, $restore_removal_option = true ) {

		if ( $site_id ) {
			delete_blog_option( $site_id, 'ats_settings' );
			delete_blog_option( $site_id, 'ats_branding' );
			delete_blog_option( $site_id, 'ats_login' );
			delete_blog_option( $site_id, 'ats_login_redirect' );
			delete_blog_option( $site_id, 'ats_integrations' );
			delete_blog_option( $site_id, 'ats_import' );
			delete_blog_option( $site_id, 'ats_modules' );
			delete_blog_option( $site_id, 'ats_recent_admin_menu' );

			delete_blog_option( $site_id, 'ats_compat_widget_type' );
			delete_blog_option( $site_id, 'ats_compat_widget_status' );
			delete_blog_option( $site_id, 'ats_compat_delete_login_customizer_page' );
			delete_blog_option( $site_id, 'ats_compat_settings_meta' );
			delete_blog_option( $site_id, 'ats_compat_old_option' );

			delete_blog_option( $site_id, 'ats_migration_from_erident' );
			delete_blog_option( $site_id, 'ats_referred_by_kirki' );

			delete_blog_option( $site_id, 'ats_login_customizer_flush_url' );
			delete_blog_option( $site_id, 'review_notice_dismissed' );

			delete_blog_option( $site_id, 'ats_install_date' );
			delete_blog_option( $site_id, 'ats_plugin_activated' );

			if ( $restore_removal_option && defined( 'ATS_DASHBOARD_PLUGIN_VERSION' ) ) {
				update_blog_option( $site_id, 'ats_settings', array( 'remove-on-uninstall' => 1 ) );
			}
		} else {
			delete_option( 'ats_settings' );
			delete_option( 'ats_branding' );
			delete_option( 'ats_login' );
			delete_option( 'ats_login_redirect' );
			delete_option( 'ats_integrations' );
			delete_option( 'ats_import' );
			delete_option( 'ats_modules' );
			delete_option( 'ats_recent_admin_menu' );

			delete_option( 'ats_compat_widget_type' );
			delete_option( 'ats_compat_widget_status' );
			delete_option( 'ats_compat_delete_login_customizer_page' );
			delete_option( 'ats_compat_settings_meta' );
			delete_option( 'ats_compat_old_option' );

			delete_option( 'ats_migration_from_erident' );
			delete_option( 'ats_referred_by_kirki' );

			delete_option( 'ats_login_customizer_flush_url' );
			delete_option( 'review_notice_dismissed' );

			delete_option( 'ats_install_date' );
			delete_option( 'ats_plugin_activated' );

			if ( $restore_removal_option && defined( 'ATS_DASHBOARD_PLUGIN_VERSION' ) ) {
				update_option( $site_id, 'ats_settings', array( 'remove-on-uninstall' => 1 ) );
			}
		}

	}

	/**
	 * Check whether plugin is active on multisite or not.
	 *
	 * @return bool
	 */
	private function is_network_active() {
		// Load plugin.php if it doesn't already exist.
		if ( ! function_exists( 'is_plugin_active_for_network' ) || ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		return ( is_plugin_active_for_network( ATS_DASHBOARD_PLUGIN_FILE ) ? true : false );
	}

	/**
	 * Check whether multisite actions are supported or not.
	 *
	 * But, we don't check for `ats_pro_ms_support` filter here.
	 * That filter belongs to the pro version.
	 *
	 * @return bool
	 */
	private function multisite_supported() {
		return ( $this->is_network_active() ? true : false );
	}

}
