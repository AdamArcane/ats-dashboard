<?php
/**
 * Branding module.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Branding;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Base\Base_Module;

/**
 * Class to setup branding module.
 */
class Branding_Module extends Base_Module {

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

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/branding';

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
	 * Setup branding module.
	 */
	public function setup() {

		add_action( 'admin_menu', array( $this, 'submenu_page' ) );
		add_action( 'admin_init', array( $this, 'add_settings' ) );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'replace_dashicons_style' ), 1000 );
		add_filter( 'style_loader_tag', array( $this, 'bust_admin_style_cache' ), 20, 4 );
		add_action( 'ats_instant_preview', array( self::get_instance(), 'instant_preview' ) );

		add_filter( 'ats_branding_colors', array( self::get_instance(), 'branding_colors' ) );

		add_filter( 'ats_branding_enable_feature_field_path', array( self::get_instance(), 'enable_field' ) );
		add_filter( 'ats_branding_choose_layout_field_path', array( self::get_instance(), 'choose_layout_field' ) );

		add_filter( 'ats_branding_wp_admin_darkmode_field_path', array( self::get_instance(), 'wp_admin_darkmode_field' ) );
		add_filter( 'ats_branding_block_editor_darkmode_field_path', array( self::get_instance(), 'block_editor_darkmode_field' ) );

		add_filter( 'ats_branding_accent_color_field_path', array( self::get_instance(), 'accent_color_field' ) );
		add_filter( 'ats_branding_admin_bar_bg_color_field_path', array( self::get_instance(), 'admin_bar_color_field' ) );
		add_filter( 'ats_branding_admin_menu_bg_color_field_path', array( self::get_instance(), 'admin_menu_bg_color_field' ) );
		add_filter( 'ats_branding_admin_submenu_bg_color_field_path', array( self::get_instance(), 'admin_submenu_bg_color_field' ) );
		add_filter( 'ats_branding_menu_item_color_field_path', array( self::get_instance(), 'menu_item_color_field' ) );
		add_filter( 'ats_branding_menu_item_active_color_field_path', array( self::get_instance(), 'menu_item_active_color_field' ) );

		add_filter( 'ats_branding_admin_bar_logo_field_path', array( self::get_instance(), 'admin_bar_logo_field' ) );
		add_filter( 'ats_branding_admin_bar_logo_url_field_path', array( self::get_instance(), 'admin_bar_logo_url_field' ) );
		add_filter( 'ats_branding_block_editor_logo_field_path', array( self::get_instance(), 'block_editor_logo_field' ) );

		// Sanitization.
		add_filter( 'ats_branding_sanitize_settings', array( self::get_instance(), 'sanitize_pro_fields' ), 10, 2 );

		// The module output.
		require_once __DIR__ . '/class-branding-output.php';
		Branding_Output::init();

	}

	/**
	 * Refresh concatenated admin CSS after repairing the Dashicons font asset.
	 *
	 * @param string $html Stylesheet tag.
	 * @param string $handle Stylesheet handle.
	 * @param string $href Stylesheet URL.
	 * @param string $media Media attribute.
	 * @return string
	 */
	public function bust_admin_style_cache( $html, $handle, $href, $media ) {

		if ( false !== strpos( $href, 'load-styles.php' ) && false !== strpos( $href, 'dashicons' ) ) {
			$fixed_href = add_query_arg( 'ats_dashicons_fix', '2', $href );
			$html       = str_replace( $href, $fixed_href, $html );
		}

		return $html;

	}

	/**
	 * Add the branding submenu page.
	 */
	public function submenu_page() {

		add_submenu_page( 'edit.php?post_type=ats_widgets', __( 'Admin Customization', 'ats-dashboard' ), __( 'Customization', 'ats-dashboard' ), apply_filters( 'ats_settings_capability', 'manage_options' ), 'ats_branding', array( $this, 'submenu_page_content' ) );

	}

	/**
	 * Render the branding settings page.
	 */
	public function submenu_page_content() {

		$template = require __DIR__ . '/templates/branding-template.php';
		$template();

	}

	/**
	 * Register branding settings and fields.
	 */
	public function add_settings() {

		register_setting( 'ats-branding-group', 'ats_branding', array( 'sanitize_callback' => array( $this, 'sanitize_branding_settings' ) ) );

		add_settings_section( 'ats-branding-section', __( 'Admin Branding', 'ats-dashboard' ), '', 'ats-branding-settings' );
		add_settings_section( 'ats-darkmode-section', __( 'Dark Mode (Experimental)', 'ats-dashboard' ), '', 'ats-darkmode-settings' );
		add_settings_section( 'ats-admin-colors-section', __( 'Admin Colors', 'ats-dashboard' ), '', 'ats-admin-colors-settings' );
		add_settings_section( 'ats-admin-logo-section', __( 'Admin Logo', 'ats-dashboard' ), '', 'ats-admin-logo-settings' );
		add_settings_section( 'ats-branding-misc-section', __( 'Misc', 'ats-dashboard' ), '', 'ats-branding-misc-settings' );

		$this->add_branding_field( 'ats-branding-enable-field', 'Enable', 'enable_field', 'ats-branding-settings', 'ats-branding-section' );
		$this->add_branding_field( 'ats-branding-layout-field', 'Layout', 'choose_layout_field', 'ats-branding-settings', 'ats-branding-section' );
		$this->add_branding_field( 'wp-admin-darkmode', 'WP Admin', 'wp_admin_darkmode_field', 'ats-darkmode-settings', 'ats-darkmode-section' );
		$this->add_branding_field( 'block-editor-darkmode', 'Block Editor', 'block_editor_darkmode_field', 'ats-darkmode-settings', 'ats-darkmode-section' );
		$this->add_branding_field( 'ats-accent-color-field', 'Accent Color', 'accent_color_field', 'ats-admin-colors-settings', 'ats-admin-colors-section' );
		$this->add_branding_field( 'ats-menu-item-color-field', 'Menu Item Color', 'menu_item_color_field', 'ats-admin-colors-settings', 'ats-admin-colors-section' );
		$this->add_branding_field( 'ats-admin-bar-bg-color-field', 'Admin Bar Bg Color', 'admin_bar_color_field', 'ats-admin-colors-settings', 'ats-admin-colors-section' );
		$this->add_branding_field( 'ats-admin-menu-bg-color-field', 'Admin Menu Bg Color', 'admin_menu_bg_color_field', 'ats-admin-colors-settings', 'ats-admin-colors-section' );
		$this->add_branding_field( 'ats-admin-submenu-bg-color-field', 'Admin Submenu Bg Color', 'admin_submenu_bg_color_field', 'ats-admin-colors-settings', 'ats-admin-colors-section' );
		$this->add_branding_field( 'ats-branding-admin-bar-logo-image-field', 'Admin Bar Logo', 'admin_bar_logo_field', 'ats-admin-logo-settings', 'ats-admin-logo-section' );
		$this->add_branding_field( 'ats-branding-admin-bar-logo-url-field', 'Admin Bar Logo URL', 'admin_bar_logo_url_field', 'ats-admin-logo-settings', 'ats-admin-logo-section' );
		$this->add_branding_field( 'ats-branding-block-editor-logo-image-field', 'Block-Editor Logo', 'block_editor_logo_field', 'ats-admin-logo-settings', 'ats-admin-logo-section' );
		$this->add_branding_field( 'ats-branding-footer-text-field', 'Footer Text', 'footer_text_field', 'ats-branding-misc-settings', 'ats-branding-misc-section' );
		$this->add_branding_field( 'ats-branding-version-text-field', 'Version Text', 'version_text_field', 'ats-branding-misc-settings', 'ats-branding-misc-section' );

		do_action( 'ats_branding_setting_fields' );

	}

	/**
	 * Sanitize branding settings and allow extension fields to participate.
	 *
	 * @param mixed $input Submitted branding settings.
	 * @return array
	 */
	public function sanitize_branding_settings( $input ) {

		if ( ! is_array( $input ) ) {
			return array();
		}

		$sanitized = $input;

		foreach ( array( 'footer_text', 'version_text' ) as $field ) {
			if ( isset( $input[ $field ] ) ) {
				$sanitized[ $field ] = wp_kses_post( $input[ $field ] );
			}
		}

		return apply_filters( 'ats_branding_sanitize_settings', $sanitized, $input );

	}

	/**
	 * Register one branding field and resolve its extension template.
	 */
	private function add_branding_field( $id, $label, $method, $page, $section ) {

		add_settings_field(
			$id,
			__( $label, 'ats-dashboard' ),
			function () use ( $method ) {
				if ( in_array( $method, array( 'footer_text_field', 'version_text_field' ), true ) ) {
					$this->{$method}();
					return;
				}

				$template = $this->{$method}( '' );
				$field    = require $template;
				$field();
			},
			$page,
			$section
		);

	}

	/**
	 * Footer text field.
	 */
	public function footer_text_field() {

		$field = require __DIR__ . '/templates/fields/footer-text.php';
		$field();

	}

	/**
	 * Version text field.
	 */
	public function version_text_field() {

		$field = require __DIR__ . '/templates/fields/version-text.php';
		$field();

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

		if ( function_exists( 'wp_enqueue_media' ) ) {
			global $wp_scripts;
			if ( isset( $wp_scripts->registered['underscore'] ) ) {
				$underscore = $wp_scripts->registered['underscore'];
				wp_deregister_script( 'underscore' );
				wp_register_script( 'underscore', $underscore->src, $underscore->deps, '1.13.8-ats-fix', false );
			}

			wp_enqueue_script( 'underscore' );
			wp_enqueue_script( 'backbone' );
			wp_enqueue_script( 'wp-util' );
			wp_enqueue_script( 'wp-backbone' );
			wp_enqueue_media();

			if ( isset( $wp_scripts->registered['wp-shortcode'] ) ) {
				$wp_scripts->registered['wp-shortcode']->deps[] = 'underscore';
			}
		}

		$enqueue = require __DIR__ . '/inc/js-enqueue.php';
		$enqueue( $this );

	}

	/**
	 * Serve the repaired Dashicons stylesheet outside WordPress's concatenated bundle.
	 */
	public function replace_dashicons_style() {

		global $wp_styles;
		$dashicons = $wp_styles->registered['dashicons'] ?? null;

		if ( $dashicons ) {
			wp_deregister_style( 'dashicons' );
			wp_register_style( 'dashicons', includes_url( 'css/dashicons.css' ), $dashicons->deps, 'ats-dashicons-fix' );
			wp_style_add_data( 'dashicons', 'concat', false );
			wp_enqueue_style( 'dashicons' );
		}

	}

	/**
	 * Instant preview style tags.
	 *
	 * @param array $colors The parsed branding colors.
	 */
	public function instant_preview( $colors = array() ) {

		if ( ! $this->screen()->is_branding() ) {
			return;
		}

		require __DIR__ . '/templates/instant-preview.php';

	}

	/**
	 * Apply branding colors.
	 *
	 * @param array $colors Existing array of color string.
	 * @return array
	 */
	public function branding_colors( $colors ) {

		$branding = get_option( 'ats_branding', array() );

		if ( ! $branding ) {
			return $colors;
		}

		if ( isset( $branding['menu_item_color'] ) && ! empty( $branding['menu_item_color'] ) ) {
			$colors['menu_item_color'] = $branding['menu_item_color'];
		}

		if ( isset( $branding['accent_color'] ) && ! empty( $branding['accent_color'] ) ) {
			$colors['accent_color'] = $branding['accent_color'];
		}

		if ( isset( $branding['admin_bar_bg_color'] ) && ! empty( $branding['admin_bar_bg_color'] ) ) {
			$colors['admin_bar_bg_color'] = $branding['admin_bar_bg_color'];
		}

		if ( isset( $branding['admin_menu_bg_color'] ) && ! empty( $branding['admin_menu_bg_color'] ) ) {
			$colors['admin_menu_bg_color'] = $branding['admin_menu_bg_color'];
		}

		if ( isset( $branding['admin_submenu_bg_color'] ) && ! empty( $branding['admin_submenu_bg_color'] ) ) {
			$colors['admin_submenu_bg_color'] = $branding['admin_submenu_bg_color'];
		}

		return $colors;

	}

	/**
	 * Sanitize PRO branding fields.
	 *
	 * @param array $sanitized The sanitized settings from free version.
	 * @param array $input The raw input data.
	 * @return array The sanitized settings with PRO fields added.
	 */
	public function sanitize_pro_fields( $sanitized, $input ) {

		// Sanitize checkbox fields.
		if ( isset( $input['enabled'] ) ) {
			$sanitized['enabled'] = 1;
		}

		if ( isset( $input['wp_admin_darkmode'] ) ) {
			$sanitized['wp_admin_darkmode'] = 1;
		}

		if ( isset( $input['block_editor_darkmode'] ) ) {
			$sanitized['block_editor_darkmode'] = 1;
		}

		if ( isset( $input['remove_admin_bar_logo'] ) ) {
			$sanitized['remove_admin_bar_logo'] = 1;
		}

		if ( isset( $input['layout'] ) ) {
			$sanitized['layout'] = sanitize_text_field( $input['layout'] );
		}

		// Sanitize color fields.
		if ( isset( $input['accent_color'] ) ) {
			$sanitized['accent_color'] = sanitize_hex_color( $input['accent_color'] );
		}

		if ( isset( $input['menu_item_color'] ) ) {
			$sanitized['menu_item_color'] = sanitize_hex_color( $input['menu_item_color'] );
		}

		if ( isset( $input['admin_bar_bg_color'] ) ) {
			$sanitized['admin_bar_bg_color'] = sanitize_hex_color( $input['admin_bar_bg_color'] );
		}

		if ( isset( $input['admin_menu_bg_color'] ) ) {
			$sanitized['admin_menu_bg_color'] = sanitize_hex_color( $input['admin_menu_bg_color'] );
		}

		if ( isset( $input['admin_submenu_bg_color'] ) ) {
			$sanitized['admin_submenu_bg_color'] = sanitize_hex_color( $input['admin_submenu_bg_color'] );
		}

		// Sanitize URL fields.
		if ( isset( $input['admin_bar_logo_image'] ) ) {
			$sanitized['admin_bar_logo_image'] = esc_url_raw( $input['admin_bar_logo_image'] );
		}

		if ( isset( $input['admin_bar_logo_url'] ) ) {
			$sanitized['admin_bar_logo_url'] = esc_url_raw( $input['admin_bar_logo_url'] );
		}

		if ( isset( $input['block_editor_logo_image'] ) ) {
			$sanitized['block_editor_logo_image'] = esc_url_raw( $input['block_editor_logo_image'] );
		}

		return $sanitized;

	}

	/**
	 * Enable branding field.
	 *
	 * @param string $template The existing template path.
	 * @return string The template path.
	 */
	public function enable_field( $template ) {

		return __DIR__ . '/templates/fields/enable.php';

	}

	/**
	 * Choose layout field.
	 *
	 * @param string $template The existing template path.
	 * @return string The template path.
	 */
	public function choose_layout_field( $template ) {

		return __DIR__ . '/templates/fields/choose-layout.php';

	}

	/**
	 * WP Admin darkmode field.
	 *
	 * @param string $template The existing template path.
	 * @return string The template path.
	 */
	public function wp_admin_darkmode_field( $template ) {

		return __DIR__ . '/templates/fields/wp-admin-darkmode.php';

	}

	/**
	 * Block editor (Gutenberg editor) darkmode field.
	 *
	 * @param string $template The existing template path.
	 * @return string The template path.
	 */
	public function block_editor_darkmode_field( $template ) {

		return __DIR__ . '/templates/fields/block-editor-darkmode.php';

	}

	/**
	 * Accent color field.
	 *
	 * @param string $template The existing template path.
	 * @return string The template path.
	 */
	public function accent_color_field( $template ) {

		return __DIR__ . '/templates/fields/accent-color.php';

	}

	/**
	 * Admin bar bg color field.
	 *
	 * @param string $template The existing template path.
	 * @return string The template path.
	 */
	public function admin_bar_color_field( $template ) {

		return __DIR__ . '/templates/fields/admin-bar-bg-color.php';

	}

	/**
	 * Admin menu bg color field.
	 *
	 * @param string $template The existing template path.
	 * @return string The template path.
	 */
	public function admin_menu_bg_color_field( $template ) {

		return __DIR__ . '/templates/fields/admin-menu-bg-color.php';

	}

	/**
	 * Admin submenu bg color field.
	 *
	 * @param string $template The existing template path.
	 * @return string The template path.
	 */
	public function admin_submenu_bg_color_field( $template ) {

		return __DIR__ . '/templates/fields/admin-submenu-bg-color.php';

	}

	/**
	 * Menu item color field.
	 *
	 * @param string $template The existing template path.
	 * @return string The template path.
	 */
	public function menu_item_color_field( $template ) {

		return __DIR__ . '/templates/fields/menu-item-color.php';

	}

	/**
	 * Menu item active color field.
	 *
	 * @param string $template The existing template path.
	 * @return string The template path.
	 */
	public function menu_item_active_color_field( $template ) {

		return __DIR__ . '/templates/fields/menu-item-active-color.php';

	}

	/**
	 * Admin bar logo field.
	 *
	 * @param string $template The existing template path.
	 * @return string The template path.
	 */
	public function admin_bar_logo_field( $template ) {

		return __DIR__ . '/templates/fields/admin-bar-logo.php';

	}

	/**
	 * Admin bar logo url field.
	 *
	 * @param string $template The existing template path.
	 * @return string The template path.
	 */
	public function admin_bar_logo_url_field( $template ) {

		return __DIR__ . '/templates/fields/admin-bar-logo-url.php';

	}

	/**
	 * Gutenberg block editor logo field.
	 *
	 * @param string $template The existing template path.
	 * @return string The template path.
	 */
	public function block_editor_logo_field( $template ) {

		return __DIR__ . '/templates/fields/block-editor-logo.php';

	}

}
