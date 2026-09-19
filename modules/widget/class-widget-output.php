<?php
/**
 * Branding output.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Widget;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Base\Base_Output;
use ats\Helpers\Array_Helper;
use ATSDash\Helpers\Content_Helper;
use ATSDash\Helpers\Widget_Helper;
use ATSDash\Helpers\Video_Helper;
use ATSDash\Helpers\Multisite_Helper;
use ats\Widget\Widget_Base_Output as Free_Widget_Output;
use ATSDash\Helpers\Bricks_Helper;

/**
 * Class to set up branding output.
 */
class Widget_Output extends Base_Output {

	/**
	 * The class instance.
	 *
	 * @var object
	 */
	public static $instance = null;

	/**
	 * The current module url.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Get instance of the class.
	 *
	 * @return object
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Module constructor.
	 */
	public function __construct() {

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/widget';

	}

	/**
	 * Init the class setup.
	 */
	public static function init() {

		$class = new self();
		$class->setup();

	}

	/**
	 * Setup widgets output.
	 */
	public function setup() {

		add_action( 'wp_dashboard_setup', array( self::get_instance(), 'remove_3rd_party_widgets' ), 100 );
		add_filter( 'ats_widget_user_roles', array( self::get_instance(), 'dashboard_user_roles' ) );
		add_filter( 'ats_allow_widget_access', array( self::get_instance(), 'check_widget_access' ), 10, 3 );
		add_filter( 'ats_widget_output', array( self::get_instance(), 'widget_output' ), 10, 2 );

		/**
		 * ! https://developer.wordpress.org/reference/hooks/screen_layout_columns/
		 * ! Why not just using add_screen_option(); ?
		 */
		add_action( 'screen_layout_columns', array( self::get_instance(), 'dashboard_columns' ) );
		add_filter(
			'get_user_option_screen_layout_dashboard',
			array(
				self::get_instance(),
				'dashboard_columns_layout',
			)
		);

		add_action( 'admin_init', array( self::get_instance(), 'update_widget_order' ), 15 );
		add_action( 'user_register', array( self::get_instance(), 'update_widget_order' ) );

		add_action( 'admin_init', array( self::get_instance(), 'choose_dashboard_page' ) );
		add_action( 'init', array( self::get_instance(), 'strictly_modify_divi_layout_post_type' ) );

	}

	/**
	 * Remove 3rd party widgets.
	 */
	public function remove_3rd_party_widgets() {

		$widget_helper = new Widget_Helper();

		$saved_widgets = $widget_helper->get_saved_3rd_party_widgets();

		foreach ( $saved_widgets as $id => $widget ) {

			if ( false !== $widget ) {
				remove_meta_box( $id, 'dashboard', $widget['context'] );
			}
		}

	}

	/**
	 * Add "super_admin" to the current user roles on multisite.
	 * This filter will be called in the free version's "class-widget-output.php" inside "add_dashboard_widgets" method.
	 *
	 * @param array $roles Array of user roles.
	 *
	 * @return array
	 */
	public function dashboard_user_roles( $roles ) {

		$ms_helper = new Multisite_Helper();

		if ( $ms_helper->multisite_supported() ) {
			if ( is_super_admin() ) {
				$roles[] = 'super_admin';
			}
		}

		return $roles;

	}

	/**
	 * Add extra checking whether to allow widget access.
	 * This filter will be called in "class-widget-output.php" in the free version.
	 *
	 * @param bool  $allow_access The existing condition.
	 * @param int   $post_id The widget's post id.
	 * @param array $user_roles Current user roles.
	 *
	 * @return bool Whether access is allowed or not.
	 */
	public function check_widget_access( $allow_access, $post_id, $user_roles ) {

		$array_helper = new Array_Helper();

		$current_user = wp_get_current_user();
		$widget_roles = get_post_meta( $post_id, 'ats_widget_roles', true );
		$widget_roles = empty( $widget_roles ) ? array( 'all' ) : $widget_roles;
		$role_allowed = false;

		if ( property_exists( $array_helper, 'clean_unserialize' ) ) {
			$widget_roles = $array_helper->clean_unserialize( $widget_roles, 3 );
		}

		// Check widget roles.
		foreach ( $widget_roles as $widget_role ) {
			if ( 'all' === $widget_role ) {
				$role_allowed = true;
				break;
			} elseif ( in_array( $widget_role, $user_roles, true ) ) {
				$role_allowed = true;
				break;
			}
		}

		if ( ! $role_allowed ) {
			return false;
		}

		$allowed_user_ids = get_post_meta( $post_id, 'ats_restrict_users', true );
		$allowed_user_ids = empty( $allowed_user_ids ) ? array( 'all' ) : $allowed_user_ids;
		$user_allowed     = false;

		if ( property_exists( $array_helper, 'clean_unserialize' ) ) {
			$allowed_user_ids = $array_helper->clean_unserialize( $allowed_user_ids, 3 );
		}

		// Check user restriction.
		if ( in_array( 'all', $allowed_user_ids, true ) ) {
			$user_allowed = true;
		} elseif ( in_array( $current_user->ID, $allowed_user_ids ) ) {
			$user_allowed = true;
		}

		if ( ! $user_allowed ) {
			return false;
		}

		return true;

	}

	/**
	 * Display pro widgets.
	 * This action will be called in "class-widget-output.php" in the free version.
	 *
	 * @param string $output The existing output.
	 * @param array  $args The widget arguments (id, title, position, priority, widget_type).
	 *
	 * @return string $output The widget output.
	 */
	public function widget_output( $output, $args ) {

		$post_id     = $args['id'];
		$widget_type = $args['widget_type'];

		$widget_output = Free_Widget_Output::get_instance();

		if ( 'form' === $widget_type ) {

			$form_note           = get_post_meta( $post_id, 'ats_form_notes', true );
			$form_name           = get_post_meta( $post_id, 'ats_form_name', true ) ? get_post_meta( $post_id, 'ats_form_name', true ) : __( 'Your Name', 'ats-dashboard' );
			$form_email          = get_post_meta( $post_id, 'ats_form_email', true ) ? get_post_meta( $post_id, 'ats_form_email', true ) : __( 'Your Email', 'ats-dashboard' );
			$form_enable_subject = get_post_meta( $post_id, 'ats_form_subject_enable', true );
			$form_subject        = get_post_meta( $post_id, 'ats_form_subject', true ) ? get_post_meta( $post_id, 'ats_form_subject', true ) : __( 'Subject', 'ats-dashboard' );
			$form_message        = get_post_meta( $post_id, 'ats_form_message', true ) ? get_post_meta( $post_id, 'ats_form_message', true ) : __( 'Message', 'ats-dashboard' );

			$subject_field = '<div class="ats-form-widget-group"><label for="subject">' . $form_subject . '</label><input type="text" name="subject" class="ats-form-widget-input" required></div>';
			$subject_field = $form_enable_subject ? $subject_field : false;

			$output = do_shortcode(
				'<form class="ats-form-widget" method="post">'
				. '<input type="hidden" name="post_id" value="' . $post_id . '">'
				. '<input type="hidden" name="nonce" value="' . wp_create_nonce( 'ats_submit_contact_form_' . $post_id ) . '">'
				. '<p class="ats-form-widget-text">' . $form_note . '</p>'
				. '<div class="ats-form-widget-group">'
				. '<label for="name">' . $form_name . '</label>'
				. '<input type="text" name="name" class="ats-form-widget-input" required>'
				. '</div>'
				. '<div class="ats-form-widget-group">'
				. '<label for="email">' . $form_email . '</label>'
				. '<input type="email" name="email" class="ats-form-widget-input" required>'
				. '</div>'
				. $subject_field
				. '<div class="ats-form-widget-group">'
				. '<label for="message">' . $form_message . '</label>'
				. '<textarea type="text" name="message" class="ats-form-widget-input" required></textarea>'
				. '</div>'
				. '<div class="ats-form-widget-group">'
				. '<button type="submit" class="button button-primary button-large submit-button">' . __( 'Submit', 'ats-dashboard' ) . '</button>'
				. '<div class="ats-form-notice"></div>'
				. '</form>'
				. '</div>'
			);

			$output = $widget_output->convert_placeholder_tags( $output );

		} elseif ( 'video' === $widget_type ) {

			$video_id       = get_post_meta( $post_id, 'ats_video_id', true );
			$video_id       = Video_Helper::get_url_id( $video_id );
			$video_platform = get_post_meta( $post_id, 'ats_video_platform', true );
			$video_src      = 'vimeo' === $video_platform ? 'https://player.vimeo.com/video/' . $video_id . '?autoplay=1' : 'https://www.youtube.com/embed/' . $video_id . '?autoplay=1';

			$video_thumbnail = get_post_meta( $post_id, 'ats_video_thumbnail', true );

			if ( ! $video_thumbnail && 'youtube' === $video_platform ) {
				$video_thumbnail = 'https://img.youtube.com/vi/' . $video_id . '/maxresdefault.jpg';
			}

			$output  = '<div class="ats-video-preview-image-wrapper">';
			$output .= '<img class="ats-video-preview-image" src="' . $video_thumbnail . '">';
			$output .= '<div class="ats-video-preview-image-overlay"><div class="ats-video-preview-play-button-outer"><div class="ats-video-preview-play-button"></div></div></div>';
			$output .= '</div>';

			$output .= '<div class="ats-video-overlay" data-ats-video-src="' . esc_url( $video_src ) . '">';
			$output .= '<iframe class="ats-video" src="" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
			$output .= '<div class="ats-video-close"></div>';
			$output .= '</div>';

		}

		return $output;

	}

	/**
	 * Dashboard columns.
	 *
	 * @param array $columns The dashboard columns.
	 *
	 * @return array The updated dashboard columns.
	 */
	public function dashboard_columns( $columns ) {

		$settings = get_option( 'ats_settings' );

		if ( ! isset( $settings['dashboard_columns'] ) ) {
			$columns['dashboard'] = 4;
		} else {
			$columns['dashboard'] = $settings['dashboard_columns'];
		}

		return apply_filters( 'ats_pro_dashboard_columns', $columns );

	}

	/**
	 * Dashboard columns layout.
	 *
	 * @param int $columns The dashboard columns.
	 *
	 * @return integer The dashboard columns.
	 */
	public function dashboard_columns_layout( $columns ) {

		$settings = get_option( 'ats_settings' );

		if ( ! isset( $settings['dashboard_columns'] ) ) {
			$columns = 4;
		} else {
			$columns = $settings['dashboard_columns'];
		}

		return apply_filters( 'ats_pro_dashboard_columns_layout', $columns );

	}

	/**
	 * Update widget order.
	 *
	 * @param int $user_id The user ID (when new user registered).
	 */
	public function update_widget_order( $user_id = 0 ) {

		$multisite_helper = new \ATSDash\Helpers\Multisite_Helper();
		$widget_helper    = new \ATSDash\Helpers\Widget_Helper();

		// Stop here if plugin is activated network wide.
		if ( $multisite_helper->is_network_active() ) {
			return;
		}

		$widget_order_user = $widget_helper->get_widget_order_user();
		$order_option_meta = get_option( 'ats_pro_widget_order' );

		// Stop here if no user is selected.
		if ( ! $widget_order_user ) {
			return;
		}

		$order_key       = 'meta-box-order_dashboard';
		$order_user_meta = get_user_meta( $widget_order_user, $order_key, true );

		if ( ! empty( $user_id ) ) {
			// If executed in "user_register" hook, update the new user's meta.
			update_user_meta( $user_id, $order_key, $order_user_meta );
		}

		// If no change, return.
		if ( $order_user_meta === $order_option_meta ) {
			return;
		}

		$blogusers = get_users();

		foreach ( $blogusers as $user ) {

			// Check if they're not the selected user.
			if ( $user->ID !== $widget_order_user ) {
				update_user_meta( $user->ID, $order_key, $order_user_meta );
			}
		}

		update_option( 'ats_pro_widget_order', $order_user_meta );

	}

	/**
	 * Choose templates to be displayed.
	 */
	public function choose_dashboard_page() {

		global $pagenow;

		if ( 'index.php' !== $pagenow ) {
			return;
		}

		$ms_helper      = new Multisite_Helper();
		$switch_blog    = $ms_helper->needs_to_switch_blog();
		$settings       = get_option( 'ats_settings' );
		$user_roles     = wp_get_current_user()->roles;
		$template_roles = isset( $settings['page_builder_template'] ) ? $settings['page_builder_template'] : array();

		$user_roles[] = 'all';

		if ( $ms_helper->multisite_supported() ) {
			if ( is_super_admin() ) {
				$user_roles[] = 'super_admin';
			}
		}

		$blueprint_template_roles = array();

		if ( $switch_blog ) {
			global $blueprint;
			switch_to_blog( $blueprint );

			$multisite_settings       = get_option( 'ats_settings' );
			$blueprint_template_roles = isset( $multisite_settings['page_builder_template'] ) ? $multisite_settings['page_builder_template'] : array();

			restore_current_blog();
		}

		foreach ( $user_roles as $user_role ) {
			if ( ! empty( $template_roles ) && ! empty( $template_roles[ $user_role ] ) ) {
				$this->render_dashboard_page( $template_roles[ $user_role ] );
			} elseif ( ! empty( $blueprint_template_roles ) && ! empty( $blueprint_template_roles[ $user_role ] ) ) {
				$this->render_dashboard_page( $blueprint_template_roles[ $user_role ], true );
			}
		}

	}

	/**
	 * Prepare dashboard page.
	 *
	 * @param string $template The template value.
	 * @param bool   $switch_blog Whether to switch blog to $blueprint site.
	 */
	public function render_dashboard_page( $template, $switch_blog = false ) {
		$content_helper  = new Content_Helper();
		$active_builders = $content_helper->get_active_page_builders();
		$explode         = explode( '_', $template );
		$builder         = $explode[0];
		$template_id     = absint( $explode[1] );

		if ( in_array( $builder, $active_builders, true ) ) {
			if ( 'brizy' === $builder ) {
				if ( $switch_blog ) {
					global $blueprint;
					switch_to_blog( $blueprint );
				}

				$content_helper->prepare_brizy_output( $template_id, 'dashboard' );

				if ( $switch_blog ) {
					restore_current_blog();
				}
			} elseif ( 'oxygen' === $builder ) {
				$content_helper->prepare_oxygen_output( $template_id, 'dashboard' );
			} elseif ( 'beaver' === $builder ) {
				$content_helper->prepare_beaver_output( $template_id, 'dashboard' );
			} elseif ( 'divi' === $builder ) {
				$content_helper->prepare_divi_output( $template_id );
			} elseif ( 'bricks' === $builder ) {
				$template_post = get_post( $template_id );

				if ( $template_post ) {
					( new Bricks_Helper( $template_post ) )->prepare_output();
				}
			} elseif ( 'breakdance' === $builder ) {
				$content_helper->prepare_breakdance_output( $template_id, 'dashboard' );
			} elseif ( 'block' === $builder ) {
				$template_post = get_post( $template_id );

				if ( $template_post ) {
					$content_helper->prepare_block_output( $template_post, 'dashboard' );
				}
			}

			add_action(
				'admin_notices',
				function () use ( $template_id, $builder, $switch_blog ) {
					require __DIR__ . '/templates/page-builder-template.php';
				}
			);

			/**
			 * Suppress the original welcome panel rendering in WordPress core (wp-admin/index.php).
			 * We render the welcome panel inside page-builder-template.php before the page builder content.
			 *
			 * This runs at priority 999 to execute after our page-builder-template.php has rendered
			 * but before WordPress core checks has_action('welcome_panel') in wp-admin/index.php.
			 */
			add_action(
				'admin_notices',
				array( self::get_instance(), 'suppress_core_welcome_panel' ),
				999
			);
		}
	}

	/**
	 * Suppress the original welcome panel rendering in WordPress core.
	 *
	 * This removes all welcome_panel actions after we've rendered it in page-builder-template.php,
	 * preventing WordPress core from rendering it again in wp-admin/index.php.
	 */
	public function suppress_core_welcome_panel() {
		remove_all_actions( 'welcome_panel' );
	}

	/**
	 * Strictly modify Divi's layout post type.
	 */
	public function strictly_modify_divi_layout_post_type() {

		global $wp_post_types;

		if ( is_admin() ) {
			return;
		}

		if ( ! isset( $_GET['ats-inside-iframe'] ) ) {
			return;
		}

		$divi_layout_post_type = defined( 'ET_BUILDER_LAYOUT_POST_TYPE' ) ? ET_BUILDER_LAYOUT_POST_TYPE : 'et_pb_layout';

		if ( ! isset( $wp_post_types[ $divi_layout_post_type ] ) ) {
			return;
		}

		if ( ! is_object( $wp_post_types[ $divi_layout_post_type ] ) ) {
			return;
		}

		if ( ! isset( $_GET['ats-nonce'] ) || ! isset( $_GET['layout-id'] ) ) {
			return;
		}

		$ats_nonce = sanitize_text_field( $_GET['ats-nonce'] );
		$layout_id = absint( $_GET['layout-id'] );

		if ( ! wp_verify_nonce( $ats_nonce, ATS_DASHBOARD_PLUGIN_DIR . $layout_id . 'ats-divi-layout-iframe' ) ) {
			return;
		}

		$settings = get_option( 'ats_settings' );

		$template_roles = isset( $settings['page_builder_template'] ) ? $settings['page_builder_template'] : array();

		$layout_id_match = false;

		foreach ( $template_roles as $template_role ) {
			if ( false !== strpos( $template_role, 'divi_' . $layout_id ) ) {
				$layout_id_match = true;
			}
		}

		if ( ! $layout_id_match ) {
			return;
		}

		$wp_post_types[ $divi_layout_post_type ]->publicly_queryable = true;

		add_filter( 'template_include', array( $this, 'modify_divi_layout_template' ), 1 );

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
	public function modify_divi_layout_template( $template_path ) {

		return ATS_DASHBOARD_PLUGIN_DIR . '/modules/admin-page/templates/edit-page.php';

	}

}
