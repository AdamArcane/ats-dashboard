<?php
/**
 * Bricks builder helper.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Helpers;

use ReflectionClass;
use ReflectionProperty;
use WP_Post;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Class to setup branding helper.
 */
class Bricks_Helper {

	/**
	 * WP_Post instance.
	 *
	 * @var WP_Post
	 */
	private $post;

	/**
	 * Bricks data.
	 *
	 * @var array
	 */
	private $bricks_data;

	/**
	 * Class constructor.
	 *
	 * @param WP_Post|null $post Instance of WP_Post.
	 */
	public function __construct( $post = null ) {

		$this->post = $post;

		$this->bricks_data = $post ? $this->get_data() : [];

	}

	/**
	 * Check whether or not "Bricks" theme is active.
	 *
	 * @return bool
	 */
	public function is_active() {

		if (
			! class_exists( '\Bricks\Helpers' )
			|| ! class_exists( '\Bricks\Database' )
			|| ! class_exists( '\Bricks\Settings' )
			|| ! class_exists( '\Bricks\Setup' )
			|| ! class_exists( '\Bricks\Frontend' )
			|| ! class_exists( '\Bricks\Templates' )
			|| ! defined( 'BRICKS_DB_EDITOR_MODE' )
			|| ! defined( 'BRICKS_DB_PAGE_CONTENT' )
			|| ! defined( 'BRICKS_URL_ASSETS' )
			|| ! defined( 'BRICKS_PATH_ASSETS' )
		) {
			return false;
		}

		return true;

	}

	/**
	 * Verify if a post was built with Bricks Builder.
	 *
	 * @return bool
	 */
	public function built_with_bricks() {

		if ( ! $this->is_active() ) {
			return false;
		}

		if ( ! get_post_meta( $this->post->ID, BRICKS_DB_EDITOR_MODE, true ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Prepare Bricks output.
	 */
	public function prepare_output() {

		$this->parse_dynamic_data();

		$theme_style_reflection = new ReflectionClass( '\Bricks\Theme_Styles' );
		$theme_style_instance   = $theme_style_reflection->newInstanceWithoutConstructor();

		if ( method_exists( $theme_style_instance, 'load_styles' ) && method_exists( $theme_style_instance, 'set_active_style' ) ) {
			/**
			 * Load Bricks theme style.
			 *
			 * @see wp-content/themes/bricks/includes/theme-styles.php
			 * @see \Bricks\Theme_Styles\load_set_styles()
			 */
			$theme_style_instance::load_styles();
			$theme_style_instance::set_active_style( $this->post->ID );
		}

		$setup_reflection = new ReflectionClass( '\Bricks\Setup' );
		$setup_instance   = $setup_reflection->newInstanceWithoutConstructor();

		add_action( 'admin_enqueue_scripts', [ $setup_instance, 'enqueue_scripts' ] );

		if ( class_exists( '\Bricks\Database' ) ) {
			if ( property_exists( '\Bricks\Database', 'active_templates' ) && is_array( \Bricks\Database::$active_templates ) ) {
				\Bricks\Database::$active_templates['content'] = $this->post->ID;
			}
		}

		if ( class_exists( '\Bricks\Settings' ) && method_exists( '\Bricks\Settings', 'set_controls' ) ) {
			\Bricks\Settings::set_controls();
		}

		if ( class_exists( '\Bricks\Frontend' ) ) {
			$frontend_reflection = new ReflectionClass( '\Bricks\Frontend' );
			$frontend_instance   = $frontend_reflection->newInstanceWithoutConstructor();

			add_action( 'admin_enqueue_scripts', [ $frontend_instance, 'enqueue_scripts' ] );
			add_action( 'admin_enqueue_scripts', [ $frontend_instance, 'enqueue_inline_css' ], 11 );
			add_action( 'admin_footer', [ $frontend_instance, 'enqueue_footer_inline_css' ] );

			add_action( 'bricks_after_site_wrapper', [ $frontend_instance, 'one_page_navigation_wrapper' ] );

			// Load custom header body script (for analytics) only on the frontend.
			add_action( 'admin_head', [ $frontend_instance, 'add_header_scripts' ] );
			add_action( 'bricks_body', [ $frontend_instance, 'add_body_header_scripts' ] );

			// Change the priority to 21 to load the custom scripts after the default Bricks scripts in the footer (@since 1.5)
			// @see core: add_action( 'wp_footer', 'wp_print_footer_scripts', 20 );
			add_action( 'admin_footer', [ $frontend_instance, 'add_body_footer_scripts' ], 21 );

			add_action( 'render_header', [ $frontend_instance, 'render_header' ] );
			add_action( 'render_footer', [ $frontend_instance, 'render_footer' ] );
		}

	}

	/**
	 * Parse dynamic data tags.
	 *
	 * We can't use $providers_instance->register_providers() because:
	 * - The condition checking in that method doesn't meet our needs.
	 * - The `$providers` property is private, so we can't modify it.
	 *
	 * @see wp-content/themes/bricks/includes/init.php -> Theme::init() -> 'bricks/dynamic_data/register_providers'
	 * @see wp-content/themes/bricks/includes/integrations/dynamic-data/providers.php -> Providers::register_providers()
	 */
	public function parse_dynamic_data() {

		if ( ! class_exists( '\Bricks\Integrations\Dynamic_Data\Providers' ) ) {
			return;
		}

		/**
		 * Dynamic Data
		 *
		 * Order matters: 'cmb2' before 'wp' so it can filter the custom fields correctly.
		 *
		 * NOTE: bricks/dynamic_data/register_providers Undocumented (@since 1.6.2)
		 */
		$dynamic_data_providers = apply_filters( 'bricks/dynamic_data/register_providers', array(
			'cmb2',
			'wp',
			'woo',
			'acf',
			'pods',
			'metabox',
			'toolset',
			'jetengine',
		) );

		$providers_instance = new \Bricks\Integrations\Dynamic_Data\Providers( $dynamic_data_providers );

		$providers_prop_reflection = new ReflectionProperty( $providers_instance, 'providers' );
		$providers_prop_reflection->setAccessible( true );

		$current_providers = $providers_prop_reflection->getValue( null );

		foreach ( $dynamic_data_providers as $provider ) {
			$classname = 'Bricks\Integrations\Dynamic_Data\Providers\Provider_' . str_replace( ' ', '_', ucwords( str_replace( '-', ' ', $provider ) ) );

			// Check if required_hooks() method exists (@since Bricks 1.11).
			if ( class_exists( $classname ) && method_exists( $classname, 'required_hooks' ) ) {
				$classname::required_hooks();
			}

			if ( $classname::load_me() ) {
				$current_providers[ $provider ] = new $classname( str_replace( '-', '_', $provider ) );
			}
		}

		$providers_prop_reflection->setValue( null, $current_providers );

		global $wp_filter;

		/**
		 * We need to disable some filters added by Bricks to prevent conflict/fatal error.
		 *
		 * But we can't use remove_filter() because it's not possible to get the instance of the filter.
		 * Using lower priority is not the answer either because we need to avoid duplicated logics.
		 *
		 * So we need to unset the filter instead.
		 * While this is not ideal, it's the simplest & safest way to avoid fatal error.
		 *
		 * These are the filters we need to unset:
		 * - bricks/dynamic_data/render_content
		 * - bricks/dynamic_data/render_tag
		 */

		if ( isset( $wp_filter['bricks/dynamic_data/render_content'] ) ) {
			unset( $wp_filter['bricks/dynamic_data/render_content'] );
		}

		if ( isset( $wp_filter['bricks/dynamic_data/render_tag'] ) ) {
			unset( $wp_filter['bricks/dynamic_data/render_tag'] );
		}

		if ( method_exists( $providers_instance, 'register_tags' ) ) {
			$providers_instance->register_tags();
		}

		/**
		 * We don't need to add filter into these hooks:
		 * - bricks/frontend/render_data
		 * - bricks/dynamic_tags_list
		 *
		 * @see wp-content/themes/bricks/includes/integrations/dynamic-data/providers.php -> register()
		 */
		add_filter( 'bricks/dynamic_data/render_content', [ $providers_instance, 'render' ], 10, 3 );
		add_filter( 'bricks/dynamic_data/render_tag', [ $providers_instance, 'get_tag_value' ], 10, 3 );
	}

	/**
	 * Render the content of a post.
	 */
	public function render_content() {

		if ( ! $this->is_active() ) {
			echo apply_filters( 'the_content', $this->post->post_content );
			return;
		}

		if ( class_exists( '\Bricks\Frontend' ) && method_exists( '\Bricks\Frontend', 'render_content' ) && $this->bricks_data ) {
			\Bricks\Frontend::render_content( $this->bricks_data );
			return;
		}

		echo apply_filters( 'the_content', $this->post->post_content );

	}

	/**
	 * Get bricks data.
	 *
	 * @return array
	 */
	public function get_data() {

		if ( ! defined( 'BRICKS_DB_PAGE_CONTENT' ) ) {
			return [];
		}

		/**
		 * We can't use \Bricks\Helpers::get_bricks_data( $this->post->ID, 'content' ) here
		 * because the checking there doesn't cover our need.
		 */
		$bricks_data = get_post_meta( $this->post->ID, BRICKS_DB_PAGE_CONTENT, true );

		if ( $bricks_data ) {
			return $bricks_data;
		}

		return [];

	}

	/**
	 * Get bricks templates.
	 *
	 * @return array
	 */
	public function get_templates() {

		if ( ! class_exists( '\Bricks\Templates' ) || ! method_exists( '\Bricks\Templates', 'get_templates_query' ) ) {
			return [];
		}

		$query = \Bricks\Templates::get_templates_query();

		return ( $query->found_posts ? $query->posts : [] );

	}

	/**
	 * Notice for super admin on multisite if a page in blueprint was created in Bricks
	 * but Bricks is not active in subsite.
	 *
	 * @param int $blog_id The blog ID.
	 */
	public function no_bricks_notice( $blog_id = 1 ) {

		if ( ! is_super_admin() ) {
			return;
		}

		$blog_details = get_blog_details( $blog_id );
		$site_name    = $blog_details ? $blog_details->blogname : '';
		?>

		<div class="notice notice-warning ats-builder-inactive-notice is-dismissible">
			<p>
				<?php _e( 'This <strong>Custom Admin Page</strong> uses Bricks and can only be viewed on sites with <strong>Bricks enabled</strong>. <br> <strong>Note:</strong> Only super admin will see this notice.', 'ats-dashboard' ); ?>
			</p>
		</div>

		<?php
	}

}
