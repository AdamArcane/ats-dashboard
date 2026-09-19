<?php
/**
 * Widget module.
 *
 * @package ATS_Dashboard
 */

namespace ats\Widget;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Base\Base_Module;

/**
 * Class to setup widget module.
 */
class Widget_Base_Module extends Base_Module {

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

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/widget';

	}

	/**
	 * Setup widget module.
	 */
	public function setup() {

		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'admin_menu', array( $this, 'remove_add_widget_submenu' ), 999 );
		add_filter( 'post_updated_messages', array( $this, 'update_messages' ) );
		add_filter( 'manage_ats_widgets_posts_columns', array( $this, 'set_columns' ) );
		add_action( 'manage_ats_widgets_posts_custom_column', array( $this, 'column_content' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		add_action( 'ats_widget_metabox', array( $this, 'icon_widget' ) );
		add_action( 'ats_widget_metabox', array( $this, 'text_widget' ) );
		add_action( 'ats_widget_metabox', array( $this, 'html_widget' ) );

		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );

		add_action( 'wp_ajax_ats_widget_change_active_status', array( $this, 'change_active_status' ) );

		// The module output.
		require_once __DIR__ . '/class-widget-base-output.php';
		Widget_Base_Output::init();

	}

	/**
	 * Register post type.
	 */
	public function register_post_type() {

		$post_type = require __DIR__ . '/inc/post-type.php';
		$post_type();

	}

	/**
	 * Remove the duplicate Add Dashboard Widget submenu item.
	 */
	public function remove_add_widget_submenu() {
		remove_submenu_page( 'edit.php?post_type=ats_widgets', 'post-new.php?post_type=ats_widgets' );
	}

	/**
	 * Update messages.
	 *
	 * @param array $messages The messages.
	 */
	public function update_messages( $messages ) {

		$post = get_post();

		$messages['ats_widgets'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Widget updated.', 'ats-dashboard' ),
			2  => __( 'Custom field updated.', 'ats-dashboard' ),
			3  => __( 'Custom field deleted.', 'ats-dashboard' ),
			4  => __( 'Widget updated.', 'ats-dashboard' ),
			// translators: %s: Date and time of the revision.
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Widget restored to revision from %s', 'ats-dashboard' ), wp_post_revision_title( absint( $_GET['revision'] ), false ) ) : false,
			6  => __( 'Widget published.', 'ats-dashboard' ),
			7  => __( 'Widget saved.', 'ats-dashboard' ),
			8  => __( 'Widget submitted.', 'ats-dashboard' ),
			// translators: %1$s: Scheduled date and time
			9  => sprintf(
				/* translators: %1$s: Scheduled date and time */
				__( 'Widget scheduled for: <strong>%1$s</strong>.', 'ats-dashboard' ),
				// translators: Publish box date format, see http://php.net/date for more info.
				date_i18n( __( 'M j, Y @ G:i', 'ats-dashboard' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Widget draft updated.', 'ats-dashboard' ),
		);

		return $messages;

	}

	/**
	 * Setup widget list columns.
	 *
	 * @param array $columns The columns.
	 */
	public function set_columns( $columns ) {

		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'title'     => __( 'Widget Title', 'ats-dashboard' ),
			'type'      => __( 'Widget Type', 'ats-dashboard' ),
			'roles'     => __( 'User Roles', 'ats-dashboard' ),
			'is_active' => __( 'Active', 'ats-dashboard' ),
		);

		return $columns;

	}

	/**
	 * Widget list column content.
	 *
	 * @param string  $column The column name/key.
	 * @param integer $post_id The post ID.
	 */
	public function column_content( $column, $post_id ) {

		$column_content = require __DIR__ . '/inc/column-content.php';
		$column_content( $column, $post_id );

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
	 * Add the icon widget.
	 */
	public function icon_widget() {

		$widget = require __DIR__ . '/templates/widget-types/icon-widget.php';
		$widget();

	}

	/**
	 * Add the text widget.
	 */
	public function text_widget() {

		$widget = require __DIR__ . '/templates/widget-types/text-widget.php';
		$widget();

	}

	/**
	 * Add the html widget.
	 */
	public function html_widget() {

		$widget = require __DIR__ . '/templates/widget-types/html-widget.php';
		$widget();

	}

	/**
	 * Register metaboxes.
	 */
	public function register_meta_boxes() {

		add_meta_box( 'ats-main-metabox', __( 'ATS Dashboard', 'ats-dashboard' ), array( $this, 'main_metabox' ), 'ats_widgets', 'normal', 'high' );
		add_meta_box( 'ats-active-status-metabox', __( 'Active', 'ats-dashboard' ), array( $this, 'active_status_metabox' ), 'ats_widgets', 'side', 'high' );


		$tags_metabox_header  = __( 'Placeholder Tags', 'ats-dashboard' );
		$tags_metabox_header .= '<br><span class="action-status">📋 Copied</span>';

		add_meta_box( 'ats-tags-metabox', $tags_metabox_header, array( $this, 'placeholder_tags_metabox' ), 'ats_widgets', 'side' );
		add_meta_box( 'ats-position-metabox', __( 'Position', 'ats-dashboard' ), array( $this, 'position_metabox' ), 'ats_widgets', 'side' );
		add_meta_box( 'ats-priority-metabox', __( 'Priority', 'ats-dashboard' ), array( $this, 'priority_metabox' ), 'ats_widgets', 'side' );

	}

	/**
	 * Widget type metabox.
	 */
	public function main_metabox() {

		$metabox = require __DIR__ . '/templates/metaboxes/main.php';
		$metabox();

	}

	/**
	 * Active status metabox.
	 *
	 * @param WP_Post $post The WP_Post object.
	 */
	public function active_status_metabox( $post ) {

		$metabox = require __DIR__ . '/templates/metaboxes/active-status.php';
		$metabox( $post );

	}

	/**
	 * PRO link metabox.
	 */
	public function pro_link_metabox() {

		$metabox = require __DIR__ . '/templates/metaboxes/pro-link.php';
		$metabox();

	}

	/**
	 * Placeholder tags metabox.
	 */
	public function placeholder_tags_metabox() {

		$metabox = require __DIR__ . '/templates/metaboxes/placeholder-tags.php';
		$metabox();

	}

	/**
	 * Position metabox.
	 *
	 * @param WP_Post $post The WP_Post object.
	 */
	public function position_metabox( $post ) {

		$metabox = require __DIR__ . '/templates/metaboxes/position.php';
		$metabox( $post );

	}

	/**
	 * Priority metabox.
	 *
	 * @param WP_Post $post The WP_Post object.
	 */
	public function priority_metabox( $post ) {

		$metabox = require __DIR__ . '/templates/metaboxes/priority.php';
		$metabox( $post );

	}

	/**
	 * Save widget's postmeta data.
	 *
	 * @param int $post_id The post ID.
	 */
	public function save_post( $post_id ) {

		$save_widget = require __DIR__ . '/inc/save-post.php';
		$save_widget( $post_id );

	}

	/**
	 * Ajax handler of widget's active status change.
	 */
	public function change_active_status() {

		$ajax = require __DIR__ . '/ajax/change-active-status.php';
		$ajax();

	}

}
