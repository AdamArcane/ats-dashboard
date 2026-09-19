<?php
/**
 * Tool module.
 *
 * @package ATS_Dashboard
 */

namespace ats\Tool;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Base\Base_Module;

/**
 * Class to setup tool module.
 */
class Tool_Module extends Base_Module {

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

		$this->url = ATS_DASHBOARD_CORE_URL . '/modules/ats-core-tool';

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
	 * Setup tool module.
	 */
	public function setup() {

		/**
		 * These 4 actions will be removed on multisite if current site is not a blueprint.
		 */
		add_action( 'admin_menu', array( self::get_instance(), 'submenu_page' ), 20 );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_scripts' ) );
		add_action( 'admin_init', array( self::get_instance(), 'add_settings' ) );

	}

	/**
	 * Add submenu page.
	 */
	public function submenu_page() {

		add_submenu_page( 'edit.php?post_type=ats_widgets', __( 'Tools', 'ats-dashboard' ), __( 'Tools', 'ats-dashboard' ), apply_filters( 'ats_tools_capability', 'manage_options' ), 'ats_tools', array( $this, 'submenu_page_content' ) );

	}

	/**
	 * Submenu page content.
	 */
	public function submenu_page_content() {

		$template = require __DIR__ . '/templates/tools-template.php';
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

		// Settings groups.
		register_setting( 'ats-export-group', 'ats_export', array( 'sanitize_callback' => array( $this, 'process_export' ) ) );
		register_setting( 'ats-import-group', 'ats_import', array( 'sanitize_callback' => array( $this, 'process_import' ) ) );

		// Settings sections.
		add_settings_section( 'ats-export-section', __( 'Export', 'ats-dashboard' ), '', 'ats-dashboard-export' );
		add_settings_section( 'ats-import-section', __( 'Import', 'ats-dashboard' ), '', 'ats-dashboard-import' );

		// Settings fields.
		add_settings_field( 'ats-export-field', '', array( $this, 'render_export_field' ), 'ats-dashboard-export', 'ats-export-section', array( 'class' => 'is-gapless has-small-text' ) );
		add_settings_field( 'ats-import-field', '', array( $this, 'render_import_field' ), 'ats-dashboard-import', 'ats-import-section', array( 'class' => 'is-gapless has-small-text' ) );

	}

	/**
	 * Render export field.
	 *
	 * @param array $args The setting's arguments.
	 */
	public function render_export_field( $args ) {

		$field = require __DIR__ . '/templates/fields/export-field.php';
		$field();

	}

	/**
	 * Render import field.
	 *
	 * @param array $args The setting's arguments.
	 */
	public function render_import_field( $args ) {

		$field = require __DIR__ . '/templates/fields/import-field.php';
		$field();

	}

	/**
	 * Process the export.
	 */
	public function process_export() {

		$process = require __DIR__ . '/inc/process-export.php';
		$process();

	}

	/**
	 * Process the import.
	 */
	public function process_import() {

		$process = require __DIR__ . '/inc/process-import.php';
		$process();

	}

}
