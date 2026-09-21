<?php
/**
 * Setting module.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Setting;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Base\Base_Module;
use ATSDash\Helpers\Content_Helper;
use ATSDash\Helpers\Multisite_Helper;

require_once __DIR__ . '/inc/class-site-owner-role.php';

/**
 * Class to setup setting module.
 */
class Setting_Module extends Base_Module {

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

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/setting';

	}

	/**
	 * Setup setting module.
	 */
	public function setup() {

		// Priority 1: must register the top-level "ats_settings" page before any
		// other module's add_submenu_page( 'ats_settings', ... ) call, otherwise
		// WordPress computes a mismatched internal hookname for that submenu
		// (see get_plugin_page_hookname()) and the page 404s.
		add_action( 'admin_menu', array( $this, 'submenu_page' ), 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'top_level_menu_icon_style' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		add_action( 'admin_init', array( $this, 'add_settings' ) );
			add_action( 'admin_init', array( $this, 'pro_setting_fields' ) );
			add_action( 'ats_after_page_builder_dashboard_metabox', array( $this, 'block_editor_template_metabox' ) );

		// Site Owner role: settings tab wiring + role sync.
		add_filter( 'ats_setting_tab_menus', array( $this, 'add_site_owner_role_tab' ) );
		add_action( 'admin_init', array( $this, 'add_site_owner_role_settings' ) );
		add_action( 'ats_after_custom_panel', array( $this, 'site_owner_role_panel' ) );
		add_action( 'init', array( 'ATSDash\\Setting\\Site_Owner_Role', 'sync' ) );
		register_deactivation_hook( ATS_DASHBOARD_PLUGIN_FILE, array( 'ATSDash\\Setting\\Site_Owner_Role', 'remove_on_deactivation' ) );

		// The module output.
		require_once __DIR__ . '/class-setting-output.php';
		Setting_Output::init();

	}

		/**
		 * Add the "Site Owner Role" tab to the Settings page tab nav.
		 *
		 * @param array $setting_tab_menus The existing tab menu entries.
		 * @return array The tab menu entries with the Site Owner Role tab appended.
		 */
		public function add_site_owner_role_tab( $setting_tab_menus ) {

			$setting_tab_menus[] = array(
				'id'   => 'site-owner-role',
				'text' => __( 'Site Owner Role', 'ats-dashboard' ),
			);

			return $setting_tab_menus;

		}

		/**
		 * Register the "Site Owner Role" settings section & fields.
		 *
		 * Saved into the same `ats_settings` option as the rest of this module.
		 */
		public function add_site_owner_role_settings() {

			add_settings_section( 'ats-site-owner-role-section', '', '', 'ats-site-owner-role-settings' );

			add_settings_field(
				'site-owner-role-enabled',
				__( 'Enable Site Owner Role', 'ats-dashboard' ),
				array( $this, 'site_owner_role_enabled_field' ),
				'ats-site-owner-role-settings',
				'ats-site-owner-role-section'
			);

			add_settings_field(
				'site-owner-role-capabilities',
				__( 'Restricted Capabilities', 'ats-dashboard' ),
				array( $this, 'site_owner_role_capabilities_field' ),
				'ats-site-owner-role-settings',
				'ats-site-owner-role-section'
			);

		}

		/**
		 * Site Owner role enabled field.
		 */
		public function site_owner_role_enabled_field() {

			$field = require __DIR__ . '/templates/fields/site-owner-role-enabled.php';
			$field();

		}

		/**
		 * Site Owner role capabilities field.
		 */
		public function site_owner_role_capabilities_field() {

			$field = require __DIR__ . '/templates/fields/site-owner-role-capabilities.php';
			$field();

		}

		/**
		 * Render the "Site Owner Role" settings panel.
		 *
		 * Hooked into `ats_after_custom_panel` so the free-tier settings
		 * template doesn't need to be modified directly.
		 */
		public function site_owner_role_panel() {
			?>

			<div class="atsui-admin-panel ats-site-owner-role-panel">
				<div class="atsui">
					<?php do_settings_sections( 'ats-site-owner-role-settings' ); ?>
				</div>
			</div>

			<?php
		}

		/**
		 * Add dashboard builder settings after the widget settings.
		 */
		public function dashboard_builder_metabox() {
			add_settings_section( 'ats-builder-section', __( 'Page Builder Dashboard', 'ats-dashboard' ), '', 'ats-page-builder-dashboard-settings' );

			add_settings_field(
				'page-builder-template-headline',
				__( 'User Role', 'ats-dashboard' ),
				function () {
					echo '<strong>' . esc_html__( 'Saved Template', 'ats-dashboard' ) . '</strong>';
				},
				'ats-page-builder-dashboard-settings',
				'ats-builder-section'
			);

			add_settings_field(
				'page-builder-template-all',
				__( 'All', 'ats-dashboard' ),
				function () {
					$this->page_builder_template_field( 'all' );
				},
				'ats-page-builder-dashboard-settings',
				'ats-builder-section'
			);

			$ms_helper = new Multisite_Helper();
			if ( $ms_helper->multisite_supported() ) {
				add_settings_field(
					'page-builder-template-super_admin',
					__( 'Super Admin', 'ats-dashboard' ),
					function () {
						$this->page_builder_template_field( 'super_admin' );
					},
					'ats-page-builder-dashboard-settings',
					'ats-builder-section'
				);
			}

			foreach ( wp_roles()->role_names as $role_key => $role_name ) {
				add_settings_field(
					'page-builder-template-' . $role_key,
					ucwords( $role_name ),
					function () use ( $role_key ) {
						$this->page_builder_template_field( $role_key );
					},
					'ats-page-builder-dashboard-settings',
					'ats-builder-section'
				);
			}
		}

		/**
		 * Render a page builder template field.
		 *
		 * @param string $role_key The role key.
		 */
		public function page_builder_template_field( $role_key ) {
			$field = require __DIR__ . '/templates/fields/page-builder.php';
			$field( $role_key );
		}

		/**
		 * Add dashboard columns and widget order fields.
		 */
		public function pro_setting_fields() {
			add_settings_field( 'column-settings', __( 'Dashboard Columns', 'ats-dashboard' ), array( $this, 'widget_columns_field' ), 'ats-general-settings', 'ats-general-section' );

			$ms_helper = new Multisite_Helper();
			if ( ! $ms_helper->is_network_active() ) {
				add_settings_field( 'widget-order', __( 'Order Widgets by', 'ats-dashboard' ), array( $this, 'widgets_order_field' ), 'ats-general-settings', 'ats-general-section' );
			}
		}

		/**
		 * Render the block editor template info metabox.
		 */
		public function block_editor_template_metabox() {
			require __DIR__ . '/templates/block-editor-template-metabox.php';
		}

		/**
		 * Render the widget columns field.
		 */
		public function widget_columns_field() {
			$field = require __DIR__ . '/templates/fields/widget-columns.php';
			$field();
		}

		/**
		 * Render the widget order field.
		 */
		public function widgets_order_field() {
			$field = require __DIR__ . '/templates/fields/widgets-order.php';
			$field();
		}

	/**
	 * Add the top-level "Arcane Tech" menu page, landing on Settings.
	 *
	 * Also register a "Settings" submenu entry pointing at the same slug,
	 * so it's listed under the top-level item (matches how WordPress core
	 * does it for Settings > General).
	 */
	public function submenu_page() {

		add_menu_page(
			__( 'Settings', 'ats-dashboard' ),
			_x( 'Arcane Tech', 'Admin Menu text', 'ats-dashboard' ),
			apply_filters( 'ats_settings_capability', 'manage_options' ),
			'ats_settings',
			array( $this, 'submenu_page_content' ),
			ATS_DASHBOARD_PLUGIN_URL . '/assets/img/logo-icon.png'
		);

		add_submenu_page(
			'ats_settings',
			__( 'Settings', 'ats-dashboard' ),
			__( 'Settings', 'ats-dashboard' ),
			apply_filters( 'ats_settings_capability', 'manage_options' ),
			'ats_settings',
			array( $this, 'submenu_page_content' )
		);

	}

	/**
	 * Force the top-level menu icon to the standard WP admin menu icon size.
	 *
	 * WordPress core only auto-scales SVG background-image menu icons; a raster
	 * <img> icon (like ours) renders at its native pixel size, so it needs an
	 * explicit size rule. Attached to the always-loaded 'admin-menu' handle so
	 * it applies on every admin screen, not just this plugin's own pages.
	 */
	public function top_level_menu_icon_style() {

		wp_add_inline_style(
			'admin-menu',
			'#adminmenu .wp-menu-image img[src*="logo-icon"] { width: 20px; height: 20px; padding: 7px 0 0; }'
		);

	}

	/**
	 * Submenu page content.
	 */
	public function submenu_page_content() {

		$template = require __DIR__ . '/templates/settings-template.php';
		$template();

	}

	/**
	 * Enqueue admin styles.
	 */
	public function admin_styles() {

		$enqueue = require __DIR__ . '/inc/css-enqueue.php';
		$enqueue( $this );

	}

	/**
	 * Enqueue admin scripts.
	 */
	public function admin_scripts() {

		$enqueue = require __DIR__ . '/inc/js-enqueue.php';
		$enqueue( $this );

	}

	/**
	 * Add settings.
	 */
	public function add_settings() {

		// Register setting.
		register_setting(
			'ats-settings-group',
			'ats_settings',
			array(
				'sanitize_callback' => array( $this, 'sanitize_input' ),
			)
		);

		// Widget sections.
		add_settings_section( 'ats-widgets-section', __( 'WordPress Dashboard Widgets', 'ats-dashboard' ), '', 'ats-widget-settings' );
		add_settings_section( 'ats-3rd-party-widgets-section', __( '3rd Party Widgets', 'ats-dashboard' ), '', 'ats-widget-settings' );

		// Widget styling section.
		add_settings_section( 'ats-styling-section', __( 'Dashboard Widget Styling', 'ats-dashboard' ), '', 'ats-widget-styling-settings' );

		// Welcome panel section.
		add_settings_section( 'ats-welcome-panel-section', __( 'Welcome Panel', 'ats-dashboard' ), '', 'ats-welcome-panel-settings' );

			// Register the built-in Page Builder Dashboard section before extensions add fields.
			$this->dashboard_builder_metabox();
			do_action( 'ats_after_widget_metabox' );

		// General section.
		add_settings_section( 'ats-general-section', __( 'General', 'ats-dashboard' ), '', 'ats-general-settings' );

		// We use this hook to place the change login url feature here.
		do_action( 'ats_after_general_metabox' );

		// Custom CSS section.
		add_settings_section( 'ats-custom-css-section', __( 'Custom CSS', 'ats-dashboard' ), '', 'ats-custom-css-settings' );

		// Misc section.
		add_settings_section( 'ats-misc-section', __( 'Misc', 'ats-dashboard' ), '', 'ats-misc-settings' );

		// Widget fields.
		add_settings_field(
			'remove-all-widgets',
			__( 'Remove All Widgets', 'ats-dashboard' ),
			array( $this, 'remove_all_widgets_field' ),
			'ats-widget-settings',
			'ats-widgets-section'
		);

		add_settings_field(
			'remove-individual-widgets',
			__( 'Remove Individual Widgets', 'ats-dashboard' ),
			array( $this, 'remove_individual_widgets_field' ),
			'ats-widget-settings',
			'ats-widgets-section'
		);

		add_settings_field(
			'remove-3rd-party-widgets',
			__( 'Remove 3rd Party Widgets', 'ats-dashboard' ),
			array( $this, 'remove_3rd_party_widgets_field' ),
			'ats-widget-settings',
			'ats-3rd-party-widgets-section'
		);

		// Widget styling fields.
		add_settings_field(
			'ats-icon-color-field',
			__( 'Icon/Text Color', 'ats-dashboard' ),
			array( $this, 'icon_color_field' ),
			'ats-widget-styling-settings',
			'ats-styling-section'
		);

		add_settings_field(
			'ats-headline-color-field',
			__( 'Headline Color', 'ats-dashboard' ),
			array( $this, 'headline_color_field' ),
			'ats-widget-styling-settings',
			'ats-styling-section'
		);

		// Welcome panel fields.
		add_settings_field(
			'ats-welcome-panel-field',
			'',
			array( $this, 'welcome_panel_field' ),
			'ats-welcome-panel-settings',
			'ats-welcome-panel-section',
			array( 'class' => 'ats-no-label' )
		);

		// General fields.
		add_settings_field(
			'remove-help-tab-settings',
			__( 'Remove Help Tab', 'ats-dashboard' ),
			array( $this, 'remove_help_tab_field' ),
			'ats-general-settings',
			'ats-general-section'
		);

		add_settings_field(
			'remove-screen-options-settings',
			__( 'Remove Screen Options Tab', 'ats-dashboard' ),
			array( $this, 'remove_screen_option_tab_field' ),
			'ats-general-settings',
			'ats-general-section'
		);

		add_settings_field(
			'headline-settings',
			__( 'Custom Dashboard Headline', 'ats-dashboard' ),
			array( $this, 'headline_text_field' ),
			'ats-general-settings',
			'ats-general-section'
		);

		add_settings_field(
			'howdy-settings',
			__( 'Custom Howdy Text', 'ats-dashboard' ),
			array( $this, 'howdy_text_field' ),
			'ats-general-settings',
			'ats-general-section'
		);

		$remove_fa_description = '<p class="description">' . __( 'Use only if your icons are not displayed correctly.', 'ats-dashboard' ) . '</p>';

		// Misc fields.
		add_settings_field(
			'remove_font_awesome',
			__( 'Remove Font Awesome', 'ats-dashboard' ) . $remove_fa_description,
			array( $this, 'remove_fontawesome_field' ),
			'ats-misc-settings',
			'ats-misc-section'
		);

		$show_data_removal_field = apply_filters( 'ats_show_data_removal_field', true );

		if ( $show_data_removal_field ) {
			add_settings_field(
				'remove-all-settings',
				__( 'Remove Data on Uninstall', 'ats-dashboard' ),
				array( $this, 'remove_on_uninstall_field' ),
				'ats-misc-settings',
				'ats-misc-section'
			);
		}

		// Custom CSS fields.
		add_settings_field(
			'custom-dashboard-css',
			__( 'Custom Dashboard CSS', 'ats-dashboard' ),
			array( $this, 'custom_dashboard_css_field' ),
			'ats-custom-css-settings',
			'ats-custom-css-section'
		);

		add_settings_field(
			'custom-admin-css',
			__( 'Custom Admin CSS', 'ats-dashboard' ),
			array( $this, 'custom_admin_css_field' ),
			'ats-custom-css-settings',
			'ats-custom-css-section'
		);

		add_settings_field(
			'custom-login-css',
			__( 'Custom Login CSS', 'ats-dashboard' ),
			array( $this, 'custom_login_css_field' ),
			'ats-custom-css-settings',
			'ats-custom-css-section'
		);

	}

	/**
	 * Sanitize input.
	 *
	 * @param array $input Array of input data to sanitize.
	 *
	 * @return array Sanitized input.
	 */
	public function sanitize_input( $input ) {

		$output = $input;

		$content_helper = new Content_Helper();

		if ( isset( $output['custom_admin_css'] ) ) {
			$output['custom_admin_css'] = $content_helper->sanitize_css( $output['custom_admin_css'] );
		}

		if ( isset( $output['custom_css'] ) ) {
			$output['custom_css'] = $content_helper->sanitize_css( $output['custom_css'] );
		}

		if ( ! empty( $output['welcome_panel_is_default'] ) ) {
			unset( $output['welcome_panel_content'] );
			unset( $output['welcome_panel_is_default'] );
		}

		return $output;

	}

	/**
	 * Remove all widgets field.
	 */
	public function remove_all_widgets_field() {

		$field = require __DIR__ . '/templates/fields/remove-all-widgets.php';
		$field();

	}

	/**
	 * Remove individual widgets field.
	 */
	public function remove_individual_widgets_field() {

		$field = require __DIR__ . '/templates/fields/remove-individual-widgets.php';
		$field();

	}

	/**
	 * Remove 3rd party widgets field.
	 */
	public function remove_3rd_party_widgets_field() {

		$template = __DIR__ . '/templates/fields/remove-3rd-party-widgets.php';
		$template = apply_filters( 'ats_remove_3rd_party_widgets_field_path', $template );
		$field    = require $template;

		$field();

	}

	/**
	 * Welcome panel field.
	 */
	public function welcome_panel_field() {

		$field = require __DIR__ . '/templates/fields/welcome-panel.php';
		$field();

	}

	/**
	 * Icon color field.
	 */
	public function icon_color_field() {

		$field = require __DIR__ . '/templates/fields/icon-color.php';
		$field();

	}

	/**
	 * Headline color field.
	 */
	public function headline_color_field() {

		$field = require __DIR__ . '/templates/fields/headline-color.php';
		$field();

	}

	/**
	 * Remove help tab field.
	 */
	public function remove_help_tab_field() {

		$field = require __DIR__ . '/templates/fields/remove-help-tab.php';
		$field();

	}

	/**
	 * Remove screen option tab field.
	 */
	public function remove_screen_option_tab_field() {

		$field = require __DIR__ . '/templates/fields/remove-screen-option-tab.php';
		$field();

	}

	/**
	 * Headline text field.
	 */
	public function headline_text_field() {

		$field = require __DIR__ . '/templates/fields/headline-text.php';
		$field();

	}

	/**
	 * Howdy text field.
	 */
	public function howdy_text_field() {

		$field = require __DIR__ . '/templates/fields/howdy-text.php';
		$field();

	}

	/**
	 * Custom dashboard css field.
	 */
	public function custom_dashboard_css_field() {

		$field = require __DIR__ . '/templates/fields/custom-dashboard-css.php';
		$field();

	}

	/**
	 * Custom admin css field.
	 */
	public function custom_admin_css_field() {

		$field = require __DIR__ . '/templates/fields/custom-admin-css.php';
		$field();

	}

	/**
	 * Custom login css field.
	 */
	public function custom_login_css_field() {
		?>

		<p class="description" style="margin: 0;">
			<?php esc_html_e( 'To add custom CSS to the WordPress login screen, please click the button below to launch the WordPress customizer.', 'ats-dashboard' ); ?>
		</p>
		<br>
		<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus%5Bsection%5D=ats_login_customizer_custom_css_js_section' ) ); ?>"
			class="button button-primary button-larger">
			<?php esc_html_e( 'Add Custom Login CSS', 'ats-dashboard' ); ?>
		</a>

		<?php

	}

	/**
	 * Remove Font Awesome field.
	 */
	public function remove_fontawesome_field() {

		$field = require __DIR__ . '/templates/fields/remove-font-awesome.php';
		$field();

	}

	/**
	 * Remove settings on uninstall field.
	 */
	public function remove_on_uninstall_field() {

		$field = require __DIR__ . '/templates/fields/remove-on-uninstall.php';
		$field();

	}

}
