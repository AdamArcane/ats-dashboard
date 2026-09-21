<?php
/**
 * Integrations module.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Integrations;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Base\Base_Module;

/**
 * Class to setup integrations module.
 */
class Integrations_Module extends Base_Module {

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

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/integrations';

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
	 * Setup integrations module.
	 */
	public function setup() {

		add_action( 'admin_menu', array( self::get_instance(), 'submenu_page' ) );
		add_action( 'admin_init', array( self::get_instance(), 'add_settings' ) );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_scripts' ) );

		// The module output.
		require_once __DIR__ . '/class-integrations-output.php';
		Integrations_Output::init();

	}

	/**
	 * Add submenu page.
	 */
	public function submenu_page() {
		add_submenu_page( 'ats_settings', __( 'Integrations', 'ats-dashboard' ), __( 'Integrations', 'ats-dashboard' ), apply_filters( 'ats_settings_capability', 'manage_options' ), 'ats_integrations', array( $this, 'submenu_page_content' ) );
	}

	/**
	 * Submenu page content.
	 */
	public function submenu_page_content() {

		$template = require __DIR__ . '/templates/integrations-template.php';
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
	 * Get the tab id => settings page slug map, so the module class and the
	 * template stay in sync on the exact page slug used for each tab.
	 *
	 * @return array
	 */
	public static function tab_pages() {

		return array(
			'mainwp'    => 'ats-integrations-mainwp-settings',
			'ploi'      => 'ats-integrations-ploi-settings',
			'suitedash' => 'ats-integrations-suitedash-settings',
			'postmark'  => 'ats-integrations-postmark-settings',
		);

	}

	/**
	 * Add settings.
	 */
	public function add_settings() {

		// Register setting.
		register_setting( 'ats-integrations-group', 'ats_integrations', array( 'sanitize_callback' => array( $this, 'sanitize_integrations_settings' ) ) );

		$pages = self::tab_pages();

		// Sections — one per tab, each on its own settings page.
		add_settings_section( 'ats-mainwp-section', '', '', $pages['mainwp'] );
		add_settings_section( 'ats-ploi-section', '', '', $pages['ploi'] );
		add_settings_section( 'ats-suitedash-section', '', '', $pages['suitedash'] );
		add_settings_section( 'ats-postmark-section', '', '', $pages['postmark'] );

		// Fields.
		add_settings_field( 'mainwp-site-id', __( 'MainWP Site ID', 'ats-dashboard' ), array( $this, 'mainwp_site_id_field' ), $pages['mainwp'], 'ats-mainwp-section' );
		add_settings_field( 'ploi-status', __( 'Server / Hosting Status', 'ats-dashboard' ), array( $this, 'ploi_status_field' ), $pages['ploi'], 'ats-ploi-section' );
		add_settings_field( 'suitedash-status', __( 'Company / CRM Status', 'ats-dashboard' ), array( $this, 'suitedash_status_field' ), $pages['suitedash'], 'ats-suitedash-section' );
		add_settings_field( 'postmark-status', __( 'Email Delivery Status', 'ats-dashboard' ), array( $this, 'postmark_status_field' ), $pages['postmark'], 'ats-postmark-section' );

	}

	/**
	 * Enqueue admin scripts.
	 */
	public function admin_scripts() {

		$enqueue = require __DIR__ . '/inc/js-enqueue.php';
		$enqueue( $this );

	}

	/**
	 * Sanitize integrations settings.
	 *
	 * @param mixed $input The input data to sanitize.
	 * @return array The sanitized settings array.
	 */
	public function sanitize_integrations_settings( $input ) {

		if ( ! is_array( $input ) ) {
			return array();
		}

		$sanitized = array();

		$sanitized['mainwp_site_id_override'] = isset( $input['mainwp_site_id_override'] ) ? sanitize_text_field( $input['mainwp_site_id_override'] ) : '';

		$ploi_fields                       = array( 'server_name', 'server_ip', 'php_version', 'domain', 'status' );
		$sanitized['ploi_status_override'] = array();

		foreach ( $ploi_fields as $field ) {
			$sanitized['ploi_status_override'][ $field ] = isset( $input['ploi_status_override'][ $field ] ) ? sanitize_text_field( $input['ploi_status_override'][ $field ] ) : '';
		}

		$suitedash_fields                       = array( 'company_name', 'company_uuid', 'contact_name', 'contact_email' );
		$sanitized['suitedash_status_override'] = array();

		foreach ( $suitedash_fields as $field ) {
			$value = isset( $input['suitedash_status_override'][ $field ] ) ? $input['suitedash_status_override'][ $field ] : '';

			$sanitized['suitedash_status_override'][ $field ] = 'contact_email' === $field ? sanitize_email( $value ) : sanitize_text_field( $value );
		}

		// Allow other extensions to add their own sanitization.
		$sanitized = apply_filters( 'ats_integrations_sanitize_settings', $sanitized, $input );

		return $sanitized;

	}

	/**
	 * MainWP site ID field.
	 */
	public function mainwp_site_id_field() {

		$field = require __DIR__ . '/templates/fields/mainwp-site-id.php';
		$field();

	}

	/**
	 * Ploi status field.
	 */
	public function ploi_status_field() {

		$field = require __DIR__ . '/templates/fields/ploi-status.php';
		$field();

	}

	/**
	 * SuiteDash status field.
	 */
	public function suitedash_status_field() {

		$field = require __DIR__ . '/templates/fields/suitedash-status.php';
		$field();

	}

	/**
	 * Postmark status field.
	 */
	public function postmark_status_field() {

		$field = require __DIR__ . '/templates/fields/postmark-status.php';
		$field();

	}

}
