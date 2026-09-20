<?php
/**
 * Widget module.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Widget;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use WP_Post;

/**
 * Class to set up widgets module.
 */
class Widget_Module extends \ATSDash\Widget\Widget_Base_Module {

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

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/widget';

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
	 * Setup widgets module.
	 */
	public function setup() {

		parent::setup();
		add_filter( 'ats_widget_types', array( self::get_instance(), 'register_widget_types' ) );

		add_filter(
			'ats_widget_list_type_column_content',
			array(
				self::get_instance(),
				'type_column_content',
			),
			10,
			3
		);

		add_filter(
			'ats_widget_list_roles_column_content',
			array(
				self::get_instance(),
				'roles_column_content',
			),
			10,
			2
		);

		add_action( 'ats_widget_metabox', array( self::get_instance(), 'video_widget' ) );
		add_action( 'ats_widget_metabox', array( self::get_instance(), 'form_widget' ) );

		add_filter( 'ats_compat_widget_type', array( self::get_instance(), 'compat_widget_type' ), 20, 2 );

		add_action( 'ats_dashboard_styles', array( self::get_instance(), 'dashboard_styles' ) );
		add_action( 'ats_edit_widget_scripts', array( self::get_instance(), 'edit_widget_scripts' ) );
		add_action( 'ats_dashboard_scripts', array( self::get_instance(), 'dashboard_scripts' ) );

		add_action( 'wp_ajax_ats_contact_form_clear_logs', array( self::get_instance(), 'clear_contact_form_logs' ) );
		add_action( 'wp_ajax_ats_submit_contact_form', array( self::get_instance(), 'submit_contact_form' ) );

		// The module output.
		require_once __DIR__ . '/class-widget-output.php';
		Widget_Output::init();

		add_action(
			'ats_before_wp_dashboard_setup',
			array(
				self::get_instance(),
				'original_widgets_before_dashboard_setup',
			)
		);

		add_action(
			'ats_after_wp_dashboard_setup',
			array(
				self::get_instance(),
				'original_widgets_after_dashboard_setup',
			)
		);

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
	 * Enqueue styles on dashboard page.
	 */
	public function dashboard_styles() {

		// Dashboard.
		wp_enqueue_style( 'ats-pro-dashboard', $this->url . '/assets/css/dashboard.css', array( 'ats-dashboard' ), ATS_DASHBOARD_PLUGIN_VERSION );

	}

	/**
	 * Scripts to enqueue on new widget & edit widget screen.
	 */
	public function edit_widget_scripts() {

		// Edit widget.
		wp_enqueue_script( 'ats-pro-edit-widget', $this->url . '/assets/js/edit-widget.js', array( 'ats-edit-widget' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		// Edit widget object.
		wp_localize_script(
			'ats-pro-edit-widget',
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
	 * Scripts to enqueue on dashboard screen.
	 */
	public function dashboard_scripts() {

		// Dashboard.
		wp_enqueue_script( 'ats-pro-dashboard', $this->url . '/assets/js/dashboard.js', array( 'ats-dashboard' ), ATS_DASHBOARD_PLUGIN_VERSION, true );

		// General dashboard object.
		wp_localize_script(
			'ats-pro-dashboard',
			'ATSDashDashboard',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			)
		);

		// Contact form object.
		wp_localize_script(
			'ats-pro-dashboard',
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
	 * Backfill widget_type with the pro-only types, based on which fields the widget has data in.
	 *
	 * @param string $widget_type The current widget type.
	 * @param int    $post_id The widget's post id.
	 *
	 * @return string The detected widget type.
	 */
	public function compat_widget_type( $widget_type, $post_id ) {

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
	 * Register metaboxes.
	 */
	public function register_meta_boxes() {

		parent::register_meta_boxes();

		add_meta_box(
			'ats-widget-roles-metabox',
			__( 'User Role Access', 'ats-dashboard' ),
			array(
				$this,
				'widget_roles_metabox',
			),
			'ats_widgets',
			'side'
		);

		add_meta_box(
			'ats-restrict-users-metabox',
			__( 'User Access', 'ats-dashboard' ),
			array(
				$this,
				'restrict_users_metabox',
			),
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
	 * Save widget's postmeta data.
	 *
	 * @param int $post_id The post ID.
	 */
	public function save_post( $post_id ) {

		$save_widget = require __DIR__ . '/inc/save-post.php';
		$save_widget( $post_id );

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
