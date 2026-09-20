<?php
/**
 * Feature module.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Feature;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Setup;
use ATSDash\Base\Base_Module;

/**
 * Class to setup dashboard module.
 */
class Feature_Module extends Base_Module {

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

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/feature';

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
	 * Setup dashboard module.
	 */
	public function setup() {

		/**
		 * These 4 actions will be removed on multisite if current site is not a blueprint.
		 */
		add_action( 'admin_menu', array( self::get_instance(), 'submenu_page' ) );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_scripts' ) );
		add_action( 'wp_ajax_ats_handle_module_actions', array( self::get_instance(), 'handle_module_actions' ) );

		// The module output.
		require_once __DIR__ . '/class-feature-output.php';
		$output = new Feature_Output();
		$output->setup();

	}

	/**
	 * Add submenu page.
	 */
	public function submenu_page() {

		add_submenu_page( 'edit.php?post_type=ats_widgets', __( 'Modules', 'ats-dashboard' ), __( 'Modules', 'ats-dashboard' ), apply_filters( 'ats_modules_capability', 'manage_options' ), 'ats_features', array( $this, 'submenu_page_content' ) );

	}

	/**
	 * Submenu page content.
	 */
	public function submenu_page_content() {

		$template = require __DIR__ . '/templates/feature-template.php';
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
	 * Activation/deactivation action.
	 */
	public function handle_module_actions() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'ats_module_nonce_action' ) ) {
			wp_send_json_error( __( 'Invalid token', 'ats-dashboard' ) );
		}

		$capability = apply_filters( 'ats_modules_capability', 'manage_options' );

		if ( ! current_user_can( $capability ) ) {
			wp_send_json_error( __( 'You do not have permission to perform this action', 'ats-dashboard' ) );
		}

		$module        = new Setup();
		$saved_modules = $module->saved_modules();

		// Batch mode: multiple modules in one request.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized per-key below.
		if ( isset( $_POST['modules'] ) && is_array( $_POST['modules'] ) ) {

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized per-key below.
			foreach ( $_POST['modules'] as $mod_name => $mod_status ) {
				$saved_modules[ sanitize_key( $mod_name ) ] = sanitize_key( $mod_status );
			}
		} else {

			// Legacy single-module mode (backwards compatible).
			$name   = isset( $_POST['name'] ) ? sanitize_key( $_POST['name'] ) : null;
			$status = isset( $_POST['status'] ) ? sanitize_key( $_POST['status'] ) : null;

			if ( is_null( $name ) || is_null( $status ) ) {
				wp_send_json_error( __( 'Invalid data', 'ats-dashboard' ) );
			}

			$saved_modules[ $name ] = $status;

		}

		update_option( 'ats_modules', $saved_modules );

		wp_send_json_success( array( 'message' => __( 'Saved', 'ats-dashboard' ) ) );

	}

}
