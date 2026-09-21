<?php
/**
 * Admin page output.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\AdminPage;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Base\Base_Output;
use ATSDash\Widget\Widget_Base_Output;
use ATSDash\Helpers\Bricks_Helper;
use ATSDash\Helpers\Content_Helper;
use ATSDash\Helpers\Multisite_Helper;
use WP_Post;

/**
 * Class to setup admin page output.
 */
class Admin_Page_Output extends Base_Output {

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
	 * Init the class setup.
	 */
	public static function init() {

		$class = new self();
		$class->setup();

	}

	/**
	 * Setup admin page output.
	 */
	public function setup() {

		add_action( 'ats_admin_page_setup_menu', array( self::get_instance(), 'setup_menu' ) );
		add_action( 'ats_admin_page_prepare_output', array( self::get_instance(), 'prepare_output' ), 10, 2 );
		add_action( 'ats_admin_page_content_output', array( self::get_instance(), 'output_content' ), 10, 3 );
		add_action( 'ats_admin_page_scripts', array( self::get_instance(), 'print_admin_page_scripts' ) );

		add_filter( 'ats_admin_page_user_roles', array( self::get_instance(), 'add_super_admin' ) );
		add_filter( 'ats_admin_page_role_is_allowed', array( self::get_instance(), 'is_role_allowed' ), 10, 3 );

	}

	/**
	 * Setup menu.
	 *
	 * @param object $output_class The output utility class brought from the free version.
	 */
	public function setup_menu( $output_class ) {

		$ms_helper = new Multisite_Helper();

		$parent_pages  = $output_class->get_posts( 'parent' );
		$submenu_pages = $output_class->get_posts( 'submenu' );
		$hidden_pages  = $output_class->get_posts( 'none' );

		$ms_parent_pages  = array();
		$ms_submenu_pages = array();
		$ms_hidden_pages  = array();

		if ( $ms_helper->needs_to_switch_blog() ) {
			global $blueprint;

			switch_to_blog( $blueprint );

			$ms_parent_pages  = $output_class->get_posts( 'parent' );
			$ms_submenu_pages = $output_class->get_posts( 'submenu' );
			$ms_hidden_pages  = $output_class->get_posts( 'none' );

			restore_current_blog();
		}

		if ( ! empty( $parent_pages ) ) {
			$output_class->prepare_menu( $parent_pages );
		}

		if ( ! empty( $ms_parent_pages ) ) {
			$output_class->prepare_menu( $ms_parent_pages, true );
		}

		if ( ! empty( $submenu_pages ) ) {
			$output_class->prepare_menu( $submenu_pages );
		}

		if ( ! empty( $ms_submenu_pages ) ) {
			$output_class->prepare_menu( $ms_submenu_pages, true );
		}

		if ( ! empty( $hidden_pages ) ) {
			$output_class->prepare_menu( $hidden_pages );
		}

		if ( ! empty( $ms_hidden_pages ) ) {
			$output_class->prepare_menu( $ms_hidden_pages, true );
		}

	}

	/**
	 * Register menu / submenu page based on it's post.
	 *
	 * @param WP_Post $post Instance of WP_Post object.
	 * @param string  $screen_id The admin page screen id.
	 */
	public function prepare_output( $post, $screen_id ) {

		$ms_helper   = new Multisite_Helper();
		$switch_blog = $ms_helper->needs_to_switch_blog();

		if ( $switch_blog ) {
			global $blueprint;

			switch_to_blog( $blueprint );
		}

		$content_type = get_post_meta( $post->ID, 'ats_content_type', true );

		$content_helper = new Content_Helper();

		if ( 'html' !== $content_type ) {
			if ( $content_helper->is_built_with_brizy( $post->ID ) ) {
				$content_helper->prepare_brizy_output( $post->ID );
			} elseif ( $content_helper->is_built_with_beaver( $post->ID ) ) {
				\FLBuilder::register_layout_styles_scripts();
			} elseif ( $content_helper->is_built_with_divi( $post ) ) {
				$content_helper->prepare_divi_output( $post );
			} elseif ( $content_helper->is_built_with_bricks( $post->ID ) ) {
				( new Bricks_Helper( $post ) )->prepare_output();
			} elseif ( $content_helper->is_built_with_oxygen( $post->ID ) ) {
				$content_helper->prepare_oxygen_output( $post->ID );
			} elseif ( $content_helper->is_built_with_breakdance( $post ) ) {
				$content_helper->prepare_breakdance_output( $post );
			} elseif ( $content_helper->is_built_with_block( $post ) ) {
				$content_helper->prepare_block_output( $post );
			}
		}

		if ( $switch_blog ) {
			restore_current_blog();
		}

	}

	/**
	 * Output admin page content.
	 *
	 * @param WP_Post $post The admin page's post object.
	 * @param string  $editor The content editor type.
	 * @param bool    $from_multisite Whether the function is called from multisite function.
	 */
	public function output_content( $post, $editor, $from_multisite = false ) {

		$ms_helper       = new Multisite_Helper();
		$switch_blog     = $from_multisite && $ms_helper->needs_to_switch_blog();
		$current_site_id = get_current_blog_id();

		if ( $switch_blog ) {
			global $blueprint;
			switch_to_blog( $blueprint );
		}

		if ( 'html' === $post->content_type ) {

			$html_content = do_shortcode( $post->html_content );
			$html_content = Widget_Base_Output::get_instance()->convert_placeholder_tags( $html_content );

			echo $html_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- admin-authored HTML content, same as before placeholder support was added.

		} else {

			$content_helper = new Content_Helper();

			$content_helper->output_content_using_builder( $post, $editor, $switch_blog, $current_site_id );

		}

		if ( $switch_blog ) {
			restore_current_blog();
		}

	}

	/**
	 * Add super admin to existing roles in "class-admin-page-output.php" in the free version.
	 *
	 * @param array $roles The existing user roles.
	 *
	 * @return array The user roles.
	 */
	public function add_super_admin( $roles ) {

		$ms_helper = new Multisite_Helper();

		if ( $ms_helper->multisite_supported() ) {
			if ( is_super_admin() ) {
				$roles[] = 'super_admin';
			}
		}

		return $roles;

	}

	/**
	 * Print admin page's script.
	 *
	 * @param WP_Post $post Enriched WP_Post object.
	 */
	public function print_admin_page_scripts( $post ) {
		?>

		<script>
			<?php echo( property_exists( $post, 'custom_js' ) ? $post->custom_js : '' ); ?>
		</script>

		<?php
	}

	/**
	 * Check if current user is allowed to access the admin page.
	 *
	 * @param bool    $is_allowed The current condition.
	 * @param array   $user_roles The current user roles.
	 * @param WP_Post $post The enriched admin page's WP_Post objecct.
	 *
	 * @return bool Whether current user is allowed to access the admin page.
	 */
	public function is_role_allowed( $is_allowed, $user_roles, $post ) {

		$is_allowed = false;

		if ( in_array( 'all', $post->allowed_roles, true ) ) {
			$is_allowed = true;
		} else {
			foreach ( $user_roles as $user_role ) {
				if ( in_array( $user_role, $post->allowed_roles, true ) ) {
					$is_allowed = true;
					break;
				}
			}
		}

		return $is_allowed;

	}

}
