<?php
/**
 * Admin page module.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\AdminPage;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Base\Base_Module;
use Exception;
use WP_Post;

/**
 * Class to setup admin page module.
 */
class Admin_Page_Base_Module extends Base_Module {

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

		add_action( 'init', array( $this, 'register_post_type' ) );
		add_filter( 'post_updated_messages', array( $this, 'update_messages' ) );
		add_filter( 'manage_ats_admin_page_posts_columns', array( $this, 'set_columns' ) );
		add_action( 'manage_ats_admin_page_posts_custom_column', array( $this, 'column_content' ), 10, 2 );
		add_action( 'do_meta_boxes', array( $this, 'remove_metaboxes' ) );

		add_filter( 'template_include', array( $this, 'include_template' ), 1 );

		add_action( 'admin_menu', array( $this, 'submenu_page' ) );
		add_filter( 'submenu_file', array( $this, 'highlight_submenu' ), 10, 2 );
		add_action( 'admin_notices', array( $this, 'admin_page_list_header' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );

		add_action( 'ats_admin_page_advanced_fields', array( $this, 'custom_js_field' ) );
		add_action( 'ats_save_admin_page', array( $this, 'save_post' ) );
		add_filter( 'ats_admin_page_post_type_args', array( $this, 'modify_post_type_args' ) );

		add_filter(
			'ats_admin_page_list_roles_column_content',
			array( $this, 'roles_column_content' ),
			10,
			2
		);

		add_action( 'wp', array( $this, 'admin_page_frontend_hooks' ), 99999 );

		// Page builder supports.
		// add_action( 'elementor/init', array( $this, 'add_elementor_support' ) );
		// add_action( 'init', array( $this, 'add_beaver_support' ) );
		// add_action( 'init', array( $this, 'add_brizy_support' ) );
		// add_action( 'init', array( $this, 'add_divi_support' ) );

		// The module output.
		require_once __DIR__ . '/class-admin-page-base-output.php';
		Admin_Page_Base_Output::init();

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
	 * Register post type.
	 */
	public function register_post_type() {

		$post_type = require __DIR__ . '/inc/post-type.php';
		$post_type();

	}

	/**
	 * Update messages.
	 *
	 * @param array $messages The messages.
	 */
	public function update_messages( $messages ) {

		$post = get_post();

		$messages['ats_admin_page'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Admin Page updated.', 'ats-dashboard' ),
			2  => __( 'Custom field updated.', 'ats-dashboard' ),
			3  => __( 'Custom field deleted.', 'ats-dashboard' ),
			4  => __( 'Admin Page updated.', 'ats-dashboard' ),
			// translators: %s: Date and time of the revision.
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Admin Page restored to revision from %s', 'ats-dashboard' ), wp_post_revision_title( absint( $_GET['revision'] ), false ) ) : false,
			6  => __( 'Admin Page published.', 'ats-dashboard' ),
			7  => __( 'Admin Page saved.', 'ats-dashboard' ),
			8  => __( 'Admin Page submitted.', 'ats-dashboard' ),
			9  => sprintf(
			// translators: Publish box date format, see http://php.net/date for more info.
				__( 'Admin Page scheduled for: <strong>%1$s</strong>.', 'ats-dashboard' ),
				date_i18n( __( 'M j, Y @ G:i', 'ats-dashboard' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Admin Page draft updated.', 'ats-dashboard' ),
		);

		return $messages;

	}

	/**
	 * Setup widget list columns.
	 *
	 * @param array $columns The columns.
	 */
	public function set_columns( $columns ) {

		return array(
			'cb'          => '<input type="checkbox" />',
			'title'       => __( 'Page Name', 'ats-dashboard' ),
			'icon'        => __( 'Menu Icon', 'ats-dashboard' ),
			'type'        => __( 'Content Type', 'ats-dashboard' ),
			'parent_menu' => __( 'Parent Menu', 'ats-dashboard' ),
			'roles'       => __( 'User Roles', 'ats-dashboard' ),
			'status'      => __( 'Status', 'ats-dashboard' ),
		);

	}

	/**
	 * Widget list column content.
	 *
	 * @param string  $column The column name/key.
	 * @param integer $post_id The post ID.
	 */
	public function column_content( $column, $post_id ) {

		$column_content = require __DIR__ . '/inc/column-content.php';
		$column_content( $this, $column, $post_id );

	}

	/**
	 * Remove some known metaboxes from admin page editing.
	 */
	public function remove_metaboxes() {

		remove_meta_box( 'wpbf', 'ats_admin_page', 'side' );
		remove_meta_box( 'wpbf_sidebar', 'ats_admin_page', 'side' );
		remove_meta_box( 'revisionsdiv', 'ats_admin_page', 'normal' );
		remove_meta_box( 'slugdiv', 'ats_admin_page', 'normal' );
		remove_meta_box( 'pageparentdiv', 'ats_admin_page', 'side' );
		remove_meta_box( 'wpbf_header', 'ats_admin_page', 'side' );
		remove_meta_box( 'postcustom', 'ats_admin_page', 'normal' );

	}

	/**
	 * Force default template for admin page.
	 * This could live in the PRO add-on as frontend-editing is only available via page builders.
	 * Though, moving it is not worth the effort and this might come in handy at some point.
	 *
	 * @param string $template_path The template path.
	 *
	 * @return string The template path.
	 */
	public function include_template( $template_path ) {

		if ( 'ats_admin_page' !== get_post_type() ) {
			return $template_path;
		}

		return __DIR__ . '/templates/edit-page.php';

	}

	/**
	 * Render a styled header above the native Admin Pages list table, with a
	 * custom "Add New" button in place of the one WordPress core would
	 * normally render (hidden via CSS), matching the Dashboard Widgets list.
	 */
	public function admin_page_list_header() {

		if ( ! $this->screen()->is_admin_page_list() ) {
			return;
		}

		$template = require __DIR__ . '/templates/admin-page-list-header.php';
		$template();

	}

	/**
	 * Add "Admin Page" submenu under "ATS Dashboard" menu item.
	 */
	public function submenu_page() {

		add_submenu_page( 'ats_settings', __( 'Admin Pages', 'ats-dashboard' ), __( 'Admin Pages', 'ats-dashboard' ), apply_filters( 'ats_settings_capability', 'manage_options' ), 'edit.php?post_type=ats_admin_page' );

	}

	/**
	 * Hightlight submenu page.
	 *
	 * @param string $submenu_file The submenu file.
	 * @param string $parent_file The parent menu file.
	 *
	 * @return string The submenu file.
	 */
	public function highlight_submenu( $submenu_file, $parent_file ) {

		global $current_screen;
		global $parent_file;

		if (
			in_array(
				$current_screen->base,
				array(
					'post',
					'edit',
				),
				true
			) && 'ats_admin_page' === $current_screen->post_type
		) {

			$parent_file  = 'ats_settings';
			$submenu_file = 'edit.php?post_type=ats_admin_page';

		}

		return $submenu_file;

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
	 * Register metaboxes.
	 */
	public function register_meta_boxes() {

		add_meta_box( 'ats-content-type-metabox', __( 'Content Type', 'ats-dashboard' ), array( $this, 'content_type_metabox' ), 'ats_admin_page', 'side', 'high' );
		add_meta_box( 'ats-menu-metabox', __( 'Menu Attributes', 'ats-dashboard' ), array( $this, 'menu_metabox' ), 'ats_admin_page', 'side' );

		$tags_metabox_header  = __( 'Placeholder Tags', 'ats-dashboard' );
		$tags_metabox_header .= '<br><span class="action-status">📋 Copied</span>';

		add_meta_box( 'ats-tags-metabox', $tags_metabox_header, array( $this, 'placeholder_tags_metabox' ), 'ats_admin_page', 'side' );
		add_meta_box( 'ats-html-metabox', __( 'HTML', 'ats-dashboard' ), array( $this, 'html_metabox' ), 'ats_admin_page', 'normal', 'high' );
		add_meta_box( 'ats-display-metabox', __( 'Display Options', 'ats-dashboard' ), array( $this, 'display_metabox' ), 'ats_admin_page', 'normal' );
		add_meta_box( 'ats-advanced-metabox', __( 'Advanced', 'ats-dashboard' ), array( $this, 'advanced_metabox' ), 'ats_admin_page', 'normal' );

		add_meta_box(
			'ats-roles-metabox',
			__( 'User Role Access', 'ats-dashboard' ),
			array( $this, 'roles_metabox' ),
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
	 * Content type metabox.
	 *
	 * @param WP_Post $post The WP_Post object.
	 */
	public function content_type_metabox( $post ) {

		$metabox = require __DIR__ . '/templates/metaboxes/content-type.php';
		$metabox( $this, $post );

	}

	/**
	 * Menu metabox.
	 *
	 * @param WP_Post $post The WP_Post object.
	 */
	public function menu_metabox( $post ) {

		$metabox = require __DIR__ . '/templates/metaboxes/menu.php';
		$metabox( $this, $post );

	}

	/**
	 * Placeholder tags metabox.
	 *
	 * Reuses the widget module's placeholder tags template — the tags
	 * (including any pushed by the Integrations module) are converted the
	 * same way for both custom widgets and custom (HTML) admin pages via
	 * `ats\Widget\Widget_Base_Output::convert_placeholder_tags()`.
	 */
	public function placeholder_tags_metabox() {

		$metabox = require ATS_DASHBOARD_PLUGIN_DIR . '/modules/widget/templates/metaboxes/placeholder-tags.php';
		$metabox();

	}

	/**
	 * HTML content metabox.
	 *
	 * @param WP_Post $post The WP_Post object.
	 */
	public function html_metabox( $post ) {

		$metabox = require __DIR__ . '/templates/metaboxes/html.php';
		$metabox( $post );

	}

	/**
	 * Display options metabox.
	 *
	 * @param WP_Post $post The WP_Post object.
	 */
	public function display_metabox( $post ) {

		$metabox = require __DIR__ . '/templates/metaboxes/display.php';
		$metabox( $post );

	}

	/**
	 * Advanced metabox.
	 *
	 * @param WP_Post $post The WP_Post object.
	 */
	public function advanced_metabox( $post ) {

		$metabox = require __DIR__ . '/templates/metaboxes/advanced.php';
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
				background: transparent !important;
			}
		</style>

		<?php
	}

}
