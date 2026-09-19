<?php
/**
 * Admin page module.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\AdminPage;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use Exception;
use WP_Post;

/**
 * Class to setup admin page module.
 */
class Admin_Page_Module extends \ats\AdminPage\Admin_Page_Base_Module {

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

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/admin-page';

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
	 * Setup admin page module.
	 */
	public function setup() {

		parent::setup();
		add_action( 'ats_admin_page_advanced_fields', array( self::get_instance(), 'custom_js_field' ) );
		add_action( 'ats_save_admin_page', array( self::get_instance(), 'save_post' ) );
		add_filter( 'ats_admin_page_post_type_args', array( self::get_instance(), 'modify_post_type_args' ) );

		add_filter(
			'ats_admin_page_list_roles_column_content',
			array(
				self::get_instance(),
				'roles_column_content',
			),
			10,
			2
		);

		add_action( 'wp', array( $this, 'admin_page_frontend_hooks' ), 99999 );

		// Page builder supports.
		// add_action( 'elementor/init', array( self::get_instance(), 'add_elementor_support' ) );
		// add_action( 'init', array( self::get_instance(), 'add_beaver_support' ) );
		// add_action( 'init', array( self::get_instance(), 'add_brizy_support' ) );
		// add_action( 'init', array( self::get_instance(), 'add_divi_support' ) );

		require __DIR__ . '/class-admin-page-output.php';
		Admin_Page_Output::init();

	}

	/**
	 * Modify the admin page post type arguments.
	 *
	 * @param array $args The post type arguments.
	 *
	 * @return array The modified post type arguments.
	 */
	public function modify_post_type_args( $args ) {

		$args['show_in_rest'] = true;

		return $args;

	}

	/**
	 * Register metaboxes.
	 */
	public function register_meta_boxes() {

		add_meta_box(
			'ats-roles-metabox',
			__( 'User Role Access', 'ats-dashboard' ),
			array(
				$this,
				'roles_metabox',
			),
			'ats_admin_page',
			'side'
		);

	}

	/**
	 * "User Role Access" metabox.
	 *
	 * @param WP_Post $post The WP_Post object.
	 */
	public function roles_metabox( $post ) {

		$metabox = require __DIR__ . '/templates/metaboxes/roles.php';
		$metabox( $post );

	}

	/**
	 * Custom JS field inside "Advanced" metabox.
	 *
	 * @param WP_Post $post The WP_Post object.
	 */
	public function custom_js_field( $post ) {

		$metabox = require __DIR__ . '/templates/metaboxes/custom-js.php';
		$metabox( $post );

	}

	/**
	 * Save admin page's postmeta data.
	 *
	 * @param int $post_id The post ID.
	 */
	public function save_post( $post_id ) {

		$save_widget = require __DIR__ . '/inc/save-post.php';
		$save_widget( $this, $post_id );

	}

	/**
	 * Auto add ats_admin_page post type to Elementor cpt support.
	 */
	public function add_elementor_support() {

		$post_types = get_option( 'elementor_cpt_support', array() );

		if ( ! in_array( 'ats_admin_page', $post_types, true ) ) {
			$post_types[] = 'ats_admin_page';
			update_option( 'elementor_cpt_support', $post_types, true );
		}

	}

	/**
	 * Auto add ats_admin_page post type to Beaver Builder cpt support.
	 */
	public function add_beaver_support() {

		if ( ! class_exists( 'FLBuilderModel' ) ) {
			return;
		}

		$post_types = \FLBuilderModel::get_post_types();

		if ( ! in_array( 'ats_admin_page', $post_types, true ) ) {
			$post_types[] = 'ats_admin_page';
			\FLBuilderModel::update_admin_settings_option( '_fl_builder_post_types', $post_types, true );
		}

	}

	/**
	 * Auto add ats_admin_page post type to Brizy Builder cpt support.
	 */
	public function add_brizy_support() {

		if ( ! class_exists( '\Brizy_Editor_Storage_Common' ) ) {
			return;
		}

		try {
			$post_types = \Brizy_Editor_Storage_Common::instance()->get( 'post-types' );
		} catch ( Exception $e ) {
			$post_types = array();
		}

		if ( ! in_array( 'ats_admin_page', $post_types, true ) ) {
			$post_types[] = 'ats_admin_page';
			\Brizy_Editor_Storage_Common::instance()->set( 'post-types', $post_types );
		}

	}

	/**
	 * Auto add ats_admin_page post type to Divi Builder cpt support.
	 */
	public function add_divi_support() {

		// Divi uses 2 option meta.
		$divi_integrations = array(
			'et_divi_builder_plugin' => 'et_pb_post_type_integration',
			'et_pb_builder_options'  => 'post_type_integration_main_et_pb_post_type_integration',
		);

		foreach ( $divi_integrations as $option_name => $integration_key ) {
			$options    = get_option( $option_name, array() );
			$post_types = isset( $options[ $integration_key ] ) ? $options[ $integration_key ] : array();

			if ( ! isset( $post_types['ats_admin_page'] ) || 'on' !== $post_types['ats_admin_page'] ) {
				$options[ $integration_key ]['ats_admin_page'] = 'on';

				update_option( $option_name, $options, true );
			}
		}

	}

	/**
	 * Modify the roles column content in admin page's post list screen.
	 *
	 * @param string $column_content The existing column content.
	 * @param int    $post_id The current admin page's post id.
	 *
	 * @return string The column content.
	 */
	public function roles_column_content( $column_content, $post_id ) {

		$roles = get_post_meta( $post_id, 'ats_allowed_roles', true );
		$roles = is_serialized( $roles ) ? unserialize( $roles ) : $roles;
		$roles = empty( $roles ) ? array( 'all' ) : $roles;

		return implode( ', ', $roles );

	}

	/**
	 * Hook necessary actions and filters on frontend.
	 * Despite being in admin page module, this is also being used in widget module (for the page builder dashboard).
	 */
	public function admin_page_frontend_hooks() {

		$divi_layout_post_type = defined( 'ET_BUILDER_LAYOUT_POST_TYPE' ) ? constant( 'ET_BUILDER_LAYOUT_POST_TYPE' ) : 'et_pb_layout';

		if ( ! is_singular( 'ats_admin_page' ) && ! is_singular( $divi_layout_post_type ) && ! is_singular( 'ats_block_template' ) ) {
			return;
		}

		// Force hide admin bar.
		add_filter( 'show_admin_bar', '__return_false', 99999 );

		if ( isset( $_GET['ats-inside-iframe'] ) ) {
			add_action( 'wp_head', array( $this, 'admin_page_frontend_inline_styles' ) );
			wp_enqueue_script( 'ats-admin-page-iframe', $this->url . '/assets/js/admin-page-iframe-content.js', array(), ATS_DASHBOARD_PLUGIN_VERSION, true );
		}

	}

	/**
	 * Inline styles for admin page frontend.
	 */
	public function admin_page_frontend_inline_styles() {
		?>

		<style class="ats-admin-page-frontend-inline-styles">
			html, body {
				overflow: hidden !important;
			}
		</style>

		<?php
	}

}
