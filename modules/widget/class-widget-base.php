<?php
/**
 * Widget module.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Widget;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Base\Base_Module;
use WP_Post;

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
		add_action( 'admin_menu', array( $this, 'remove_widget_submenus' ), 999 );
		add_action( 'admin_notices', array( $this, 'widget_list_tab_bar' ) );
		add_filter( 'submenu_file', array( $this, 'highlight_submenu' ), 10, 2 );
		add_filter( 'post_updated_messages', array( $this, 'update_messages' ) );
		add_filter( 'manage_ats_widgets_posts_columns', array( $this, 'set_columns' ) );
		add_action( 'manage_ats_widgets_posts_custom_column', array( $this, 'column_content' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		add_action( 'ats_widget_metabox', array( $this, 'icon_widget' ) );
		add_action( 'ats_widget_metabox', array( $this, 'text_widget' ) );
		add_action( 'ats_widget_metabox', array( $this, 'html_widget' ) );
		add_action( 'ats_widget_metabox', array( $this, 'video_widget' ) );
		add_action( 'ats_widget_metabox', array( $this, 'form_widget' ) );

		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );

		add_filter( 'ats_compat_widget_type', array( $this, 'compat_widget_type' ), 10, 2 );
		add_filter( 'ats_compat_widget_type', array( $this, 'compat_widget_type_extended' ), 20, 2 );

		add_filter( 'ats_widget_types', array( $this, 'register_widget_types' ) );

		add_filter(
			'ats_widget_list_type_column_content',
			array( $this, 'type_column_content' ),
			10,
			3
		);

		add_filter(
			'ats_widget_list_roles_column_content',
			array( $this, 'roles_column_content' ),
			10,
			2
		);

		add_action( 'ats_edit_widget_scripts', array( $this, 'localize_edit_widget_scripts' ) );
		add_action( 'ats_dashboard_scripts', array( $this, 'localize_dashboard_scripts' ) );

		add_action( 'wp_ajax_ats_contact_form_clear_logs', array( $this, 'clear_contact_form_logs' ) );
		add_action( 'wp_ajax_ats_submit_contact_form', array( $this, 'submit_contact_form' ) );

		// The module output.
		require_once __DIR__ . '/class-widget-base-output.php';
		Widget_Base_Output::init();

		require_once __DIR__ . '/class-widget-output.php';
		Widget_Output::init();

		add_action(
			'ats_before_wp_dashboard_setup',
			array( $this, 'original_widgets_before_dashboard_setup' )
		);

		add_action(
			'ats_after_wp_dashboard_setup',
			array( $this, 'original_widgets_after_dashboard_setup' )
		);

	}

	/**
	 * Register post type.
	 */
	public function register_post_type() {

		$post_type = require __DIR__ . '/inc/post-type.php';
		$post_type();

	}

	/**
	 * Remove the "Add New" and "Dashboard Widgets" list submenu items.
	 *
	 * The widgets list is only reachable via the tab bar shared with the
	 * Widget Settings page, not its own sidebar entry.
	 */
	public function remove_widget_submenus() {
		remove_submenu_page( 'ats_settings', 'post-new.php?post_type=ats_widgets' );
		remove_submenu_page( 'ats_settings', 'edit.php?post_type=ats_widgets' );
	}

	/**
	 * Render the shared "Widget Settings" / "Dashboard Widgets" tab bar above
	 * the native widgets list table, with a custom "Add New" button in place
	 * of the one WordPress core would normally render (hidden via CSS).
	 */
	public function widget_list_tab_bar() {

		if ( ! $this->screen()->is_widget_list() ) {
			return;
		}

		$template = require __DIR__ . '/templates/widget-list-tab-bar.php';
		$template();

	}

	/**
	 * Fix sidebar highlighting for the widgets list screen.
	 *
	 * wp-admin/edit.php hardcodes $parent_file to its own post-type slug and
	 * never checks the CPT's show_in_menu string, unlike post.php/post-new.php
	 * which resolve it correctly. That mismatch means the "Arcane Tech" submenu
	 * doesn't render expanded while on the widgets list. Same fix pattern as
	 * Admin_Page_Base::highlight_submenu().
	 *
	 * @param string $submenu_file The submenu file.
	 * @param string $parent_file  The parent menu file.
	 *
	 * @return string The submenu file.
	 */
	public function highlight_submenu( $submenu_file, $parent_file ) {

		global $current_screen;
		global $parent_file;

		if (
			in_array( $current_screen->base, array( 'post', 'edit' ), true )
			&& 'ats_widgets' === $current_screen->post_type
		) {

			$parent_file  = 'ats_settings';
			$submenu_file = 'edit.php?post_type=ats_widgets';

		}

		return $submenu_file;

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
			'cb'     => '<input type="checkbox" />',
			'title'  => __( 'Widget Title', 'ats-dashboard' ),
			'type'   => __( 'Widget Type', 'ats-dashboard' ),
			'roles'  => __( 'User Roles', 'ats-dashboard' ),
			'status' => __( 'Status', 'ats-dashboard' ),
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
	 * Define the video widget.
	 */
	public function video_widget() {

		$widget = require __DIR__ . '/templates/widget-types/video-widget.php';
		$widget();

	}

	/**
	 * Define the form widget.
	 */
	public function form_widget() {

		$widget = require __DIR__ . '/templates/widget-types/form-widget.php';
		$widget();

	}

	/**
	 * Register metaboxes.
	 */
	public function register_meta_boxes() {

		add_meta_box( 'ats-main-metabox', __( 'ATS Dashboard', 'ats-dashboard' ), array( $this, 'main_metabox' ), 'ats_widgets', 'normal', 'high' );

		$tags_metabox_header  = __( 'Placeholder Tags', 'ats-dashboard' );
		$tags_metabox_header .= '<br><span class="action-status">📋 Copied</span>';

		add_meta_box( 'ats-tags-metabox', $tags_metabox_header, array( $this, 'placeholder_tags_metabox' ), 'ats_widgets', 'side' );
		add_meta_box( 'ats-position-metabox', __( 'Position', 'ats-dashboard' ), array( $this, 'position_metabox' ), 'ats_widgets', 'side' );
		add_meta_box( 'ats-priority-metabox', __( 'Priority', 'ats-dashboard' ), array( $this, 'priority_metabox' ), 'ats_widgets', 'side' );

		add_meta_box(
			'ats-widget-roles-metabox',
			__( 'User Role Access', 'ats-dashboard' ),
			array( $this, 'widget_roles_metabox' ),
			'ats_widgets',
			'side'
		);

		add_meta_box(
			'ats-restrict-users-metabox',
			__( 'User Access', 'ats-dashboard' ),
			array( $this, 'restrict_users_metabox' ),
			'ats_widgets',
			'side'
		);

	}

	/**
	 * Widget roles metabox.
	 *
	 * @param WP_Post $post The WP_Post object.
	 */
	public function widget_roles_metabox( $post ) {

		$metabox = require __DIR__ . '/templates/metaboxes/widget-roles.php';
		$metabox( $post );

	}

	/**
	 * Restrict user metabox.
	 *
	 * @param WP_Post $post The WP_Post object.
	 */
	public function restrict_users_metabox( $post ) {

		$metabox = require __DIR__ . '/templates/metaboxes/restrict-users.php';
		$metabox( $post );

	}

	/**
	 * Widget type metabox.
	 */
	public function main_metabox() {

		$metabox = require __DIR__ . '/templates/metaboxes/main.php';
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
	 * Backfill widget_type for widgets saved before that meta existed, based on which fields they have data in.
	 *
	 * @param string $widget_type The current (empty) widget type.
	 * @param int    $post_id The widget's post id.
	 *
	 * @return string The detected widget type.
	 */
	public function compat_widget_type( $widget_type, $post_id ) {

		if ( get_post_meta( $post_id, 'ats_html', true ) ) {
			$widget_type = 'html';
		} elseif ( get_post_meta( $post_id, 'ats_content', true ) ) {
			$widget_type = 'text';
		} elseif ( get_post_meta( $post_id, 'ats_icon_key', true ) || get_post_meta( $post_id, 'ats_link', true ) || get_post_meta( $post_id, 'ats_tooltip', true ) ) {
			$widget_type = 'icon';
		}

		if ( $widget_type ) {
			update_post_meta( $post_id, 'ats_widget_type', $widget_type );
		}

		return $widget_type;

	}

	/**
	 * Backfill widget_type with the video/form types, based on which fields the widget has data in.
	 *
	 * Runs after compat_widget_type() (priority 20 vs 10) — only fires if that
	 * didn't already find a type.
	 *
	 * @param string $widget_type The current widget type.
	 * @param int    $post_id The widget's post id.
	 *
	 * @return string The detected widget type.
	 */
	public function compat_widget_type_extended( $widget_type, $post_id ) {

		if ( $widget_type ) {
			return $widget_type;
		}

		if ( get_post_meta( $post_id, 'ats_video_id', true ) || get_post_meta( $post_id, 'ats_video_platform', true ) ) {
			$widget_type = 'video';
		} elseif ( get_post_meta( $post_id, 'ats_form_name', true ) || get_post_meta( $post_id, 'ats_form_email', true ) ) {
			$widget_type = 'form';
		}

		if ( $widget_type ) {
			update_post_meta( $post_id, 'ats_widget_type', $widget_type );
		}

		return $widget_type;

	}

	/**
	 * Register widget types.
	 *
	 * @param array $widget_types The existing widget types.
	 *
	 * @return array The modified widget types.
	 */
	public function register_widget_types( $widget_types ) {

		$widget_types['video'] = __( 'Video Widget', 'ats-dashboard' );
		$widget_types['form']  = __( 'Contact Form Widget', 'ats-dashboard' );

		return $widget_types;

	}

	/**
	 * Set "type" column's content on widget list screen.
	 *
	 * @param string $content The existing type column's content.
	 * @param int    $post_id The current post id.
	 * @param string $widget_type The current widget type.
	 *
	 * @return string The type column's content.
	 */
	public function type_column_content( $content, $post_id, $widget_type ) {

		if ( 'form' === $widget_type ) {
			$content = __( 'Contact Form', 'ats-dashboard' );
		} elseif ( 'video' === $widget_type ) {
			$content = __( 'Video', 'ats-dashboard' );
		}

		return $content;

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

		$roles = get_post_meta( $post_id, 'ats_widget_roles', true );
		$roles = is_serialized( $roles ) ? unserialize( $roles ) : $roles;
		$roles = empty( $roles ) ? array( 'all' ) : $roles;
		$roles = implode( ', ', $roles );

		return $roles;

	}

	/**
	 * Localize data needed by edit-widget.js for the Contact Form's
	 * log-clearing button. Attached to the 'ats-edit-widget' handle that
	 * admin_scripts() already enqueues — not a separate script.
	 */
	public function localize_edit_widget_scripts() {

		wp_localize_script(
			'ats-edit-widget',
			'ATSDashEditWidget',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'labels'  => array(
					'clearLog'    => __( 'Clear Log', 'ats-dashboard' ),
					'clearingLog' => __( 'Clearing Log Data...', 'ats-dashboard' ),
				),
			)
		);

	}

	/**
	 * Localize data needed by dashboard.js for the AJAX contact form.
	 * Attached to the 'ats-dashboard' handle that admin_scripts() already
	 * enqueues — not a separate script.
	 */
	public function localize_dashboard_scripts() {

		wp_localize_script(
			'ats-dashboard',
			'ATSDashDashboard',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			)
		);

		wp_localize_script(
			'ats-dashboard',
			'ATSDashContactForm',
			array(
				'labels' => array(
					'submit'     => __( 'Submit', 'ats-dashboard' ),
					'submitting' => __( 'Submitting...', 'ats-dashboard' ),
				),
			)
		);

	}

	/**
	 * Action to run on "get_original" method on "class-widget-helper.php" in the free version.
	 */
	public function original_widgets_before_dashboard_setup() {

		remove_action( 'wp_dashboard_setup', array( Widget_Output::get_instance(), 'remove_3rd_party_widgets' ), 100 );

	}

	/**
	 * Action to run on "get_original" method on "class-widget-helper.php" in the free version.
	 */
	public function original_widgets_after_dashboard_setup() {

		add_action( 'wp_dashboard_setup', array( Widget_Output::get_instance(), 'remove_3rd_party_widgets' ), 100 );

	}

	/**
	 * Ajax handler of clear contact form logs inside edit widget screen.
	 */
	public function clear_contact_form_logs() {

		$ajax = require __DIR__ . '/ajax/clear-contact-form-logs.php';
		$ajax();

	}

	/**
	 * Ajax handler of contact form submission.
	 */
	public function submit_contact_form() {

		$ajax = require __DIR__ . '/ajax/submit-contact-form.php';
		$ajax( $this );

	}

	/**
	 * Contact form logger.
	 *
	 * @param int    $post_id The form's post id.
	 * @param string $message The log message.
	 * @param string $local_timestamp The timestamp.
	 * @param string $email The sender email.
	 * @param string $subject The submission subject.
	 * @param string $name The sender name.
	 * @param string $status The email sending status.
	 */
	public function contact_form_logger( $post_id, $message, $local_timestamp, $email, $subject, $name, $status ) {

		$clean_message = sanitize_text_field( $message );
		$clean_email   = sanitize_email( $email );
		$clean_subject = sanitize_text_field( $subject );
		$clean_name    = sanitize_text_field( $name );

		$current_site_id   = 0;
		$blueprint_site_id = 0;

		if ( is_multisite() ) {

			$current_site_id   = get_current_blog_id();
			$blueprint_site_id = get_site_option( 'ats_multisite_blueprint' ) ? (int) get_site_option( 'ats_multisite_blueprint' ) : 0;

			if ( ! empty( $blueprint_site_id ) ) {
				switch_to_blog( $blueprint_site_id );
			}
		}

		$log_enabled = get_post_meta( $post_id, 'ats_form_enable_logs', true );
		$log         = get_post_meta( $post_id, 'ats_contact_form_logs', true );

		// Stop here if logs are disabled.
		if ( ! $log_enabled ) {
			return;
		}

		if ( $log ) {

			// Extend existing log if it exists.
			$log .= $this->create_contact_form_log_message( $local_timestamp, $clean_message, $clean_name, $clean_subject, $clean_email, $status );
			update_post_meta( $post_id, 'ats_contact_form_logs', $log );

		} else {

			// Create log entry if it doesn't exist.
			$log = $this->create_contact_form_log_message( $local_timestamp, $clean_message, $clean_name, $clean_subject, $clean_email, $status );
			update_post_meta( $post_id, 'ats_contact_form_logs', $log );

		}

		if ( is_multisite() && ! empty( $blueprint_site_id ) ) {
			switch_to_blog( $current_site_id );
		}

	}

	/**
	 * Construct contact form log entry.
	 *
	 * @param string $local_timestamp The timestamp.
	 * @param string $clean_message The sanitized submission message.
	 * @param string $clean_name The sanitized sender name.
	 * @param string $clean_subject The sanitized submission subject.
	 * @param string $clean_email The sanitized sender email.
	 * @param string $status The email sending status.
	 *
	 * @return string The log message.
	 */
	public function create_contact_form_log_message( $local_timestamp, $clean_message, $clean_name, $clean_subject, $clean_email, $status ) {

		$log_message  = '<div class="ats-form-widget-log-entry">';
		$log_message .= '<strong>' . __( 'Message:', 'ats-dashboard' ) . '</strong>';
		$log_message .= '<br/>';
		$log_message .= $clean_message;
		$log_message .= '<br/>';
		$log_message .= '<hr>';
		$log_message .= '<strong>' . __( 'From:', 'ats-dashboard' ) . ' </strong>';
		$log_message .= $clean_name;
		$log_message .= '<br/>';
		$log_message .= '<strong>' . __( 'Subject:', 'ats-dashboard' ) . ' </strong>';
		$log_message .= $clean_subject;
		$log_message .= '<br/>';
		$log_message .= '<strong>' . __( 'Email:', 'ats-dashboard' ) . ' </strong>';
		$log_message .= $clean_email;
		$log_message .= '<br/>';
		$log_message .= '<hr>';
		$log_message .= $status ? '<span class="ats-form-widget-log-entry-indicator success"></span>' . __( 'Sent', 'ats-dashboard' ) : '<span class="ats-form-widget-log-entry-indicator error"></span>' . __( 'Error', 'ats-dashboard' );
		$log_message .= ' - ';
		$log_message .= $local_timestamp;
		$log_message .= '<br/>';

		$log_message .= '</div>';

		return $log_message;

	}

}
