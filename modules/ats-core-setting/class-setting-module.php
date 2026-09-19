<?php
/**
 * Setting module.
 *
 * @package ATS_Dashboard
 */

namespace ats\Setting;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Base\Base_Module;
use ats\Helpers\Content_Helper;

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

		$this->url = ATS_DASHBOARD_CORE_URL . '/modules/ats-core-setting';

	}

	/**
	 * Setup setting module.
	 */
	public function setup() {

		add_action( 'admin_menu', array( $this, 'submenu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		add_action( 'admin_init', array( $this, 'add_settings' ) );

		// The module output.
		require_once __DIR__ . '/class-setting-output.php';
		Setting_Output::init();

	}

	/**
	 * Add submenu page.
	 */
	public function submenu_page() {

		add_submenu_page(
			'edit.php?post_type=ats_widgets',
			__( 'Settings', 'ats-dashboard' ),
			__( 'Settings', 'ats-dashboard' ),
			apply_filters( 'ats_settings_capability', 'manage_options' ),
			'ats_settings',
			array( $this, 'submenu_page_content' )
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

		// We use this hook to place the Page Builder Dashboard feature here.
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
