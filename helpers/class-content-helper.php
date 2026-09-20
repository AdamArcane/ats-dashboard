<?php
/**
 * Content helper.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Helpers;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use Exception;
use ATSDash\Helpers\Content_Base_Helper;
use WP_Post;

/**
 * Class to set up content helper.
 */
class Content_Helper extends Content_Base_Helper {

	/**
	 * Check whether post is built with Elementor.
	 *
	 * @param int $post_id ID of the post being checked.
	 *
	 * @return bool
	 */
	public function is_built_with_elementor( $post_id ) {
		return class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->documents->get( $post_id )->is_built_with_elementor();
	}

	/**
	 * Check whether post is built with Beaver Builder.
	 *
	 * @param int $post_id ID of the post being checked.
	 *
	 * @return bool
	 */
	public function is_built_with_beaver( $post_id ) {
		return class_exists( '\FLBuilderModel' ) && \FLBuilderModel::is_builder_enabled( $post_id );
	}

	/**
	 * Check whether the post type of the given post id is checked in Brizy settings.
	 *
	 * @param int $post_id The post ID to check.
	 *
	 * @see wp-content/plugins/brizy/editor.php
	 */
	public function supported_in_brizy_post_types( $post_id ) {

		$post = get_post( $post_id );

		$brizy_editor         = \Brizy_Editor::get();
		$supported_post_types = $brizy_editor->supported_post_types();

		if ( in_array( $post->post_type, $supported_post_types, true ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Check whether post is built with Brizy Builder.
	 *
	 * @param int $post_id ID of the post being checked.
	 *
	 * @return bool
	 */
	public function is_built_with_brizy( $post_id ) {

		if ( class_exists( '\Brizy_Editor_Post' ) ) {

			if ( ! $this->supported_in_brizy_post_types( $post_id ) ) {
				return false;
			}

			try {
				$post = \Brizy_Editor_Post::get( $post_id );

				if ( is_object( $post ) && method_exists( $post, 'uses_editor' ) && $post->uses_editor() ) {
					return true;
				}
			} catch ( Exception $e ) {
				return false;
			}
		}

		return false;

	}

	/**
	 * Check whether post is built with Divi Builder.
	 *
	 * @param WP_Post|int $post The post being checked.
	 *
	 * @return bool
	 */
	public function is_built_with_divi( $post ) {

		$post_id = is_object( $post ) && property_exists( $post, 'ID' ) ? $post->ID : $post;
		$post    = is_object( $post ) && property_exists( $post, 'ID' ) ? $post : get_post( $post );

		if ( ! $post ) {
			return false;
		}

		return ( new Divi_Helper( $post ) )->built_with_divi();

	}

	/**
	 * Check whether post is built with block editor.
	 *
	 * @param WP_Post|int $post The post being checked.
	 *
	 * @return bool
	 */
	public function is_built_with_block( $post ) {

		$post_id = is_object( $post ) && property_exists( $post, 'ID' ) ? $post->ID : $post;
		$post    = is_object( $post ) && property_exists( $post, 'ID' ) ? $post : get_post( $post );

		if ( ! $post ) {
			return false;
		}

		return ( new Block_Helper( $post ) )->built_with_block();

	}

	/**
	 * Check whether post is built with Oxygen Builder.
	 *
	 * @param int $post_id ID of the post being checked.
	 *
	 * @return bool
	 */
	public function is_built_with_oxygen( $post_id ) {

		if ( ! function_exists( 'oxygen_vsb_current_user_can_access' ) || ! defined( 'CT_VERSION' ) ) {
			return false;
		}

		if ( ! get_post_meta( $post_id, 'ct_builder_shortcodes', true ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Check whether post is built with Bricks Builder.
	 *
	 * @param int $post_id ID of the post being checked.
	 *
	 * @return bool
	 */
	public function is_built_with_bricks( $post_id ) {

		$post = get_post( $post_id );

		if ( ! $post ) {
			return false;
		}

		return ( new Bricks_Helper( $post ) )->built_with_bricks();

	}

	/**
	 * Check whether post is built with Breakdance Builder.
	 *
	 * @param WP_Post|int $post The post being checked.
	 *
	 * @return bool
	 */
	public function is_built_with_breakdance( $post ) {

		$post_id = is_object( $post ) && property_exists( $post, 'ID' ) ? $post->ID : $post;
		$post    = is_object( $post ) && property_exists( $post, 'ID' ) ? $post : get_post( $post );

		if ( ! $post ) {
			return false;
		}

		return ( new Breakdance_Helper( $post ) )->built_with_breakdance();

	}

	/**
	 * Get the editor/ builder of the given post.
	 *
	 * @param int $post_id ID of the post being checked.
	 *
	 * @return string The content editor name.
	 */
	public function get_content_editor( $post_id ) {

		if ( $this->is_built_with_elementor( $post_id ) ) {
			return 'elementor';
		} elseif ( $this->is_built_with_beaver( $post_id ) ) {
			return 'beaver';
		} elseif ( $this->is_built_with_brizy( $post_id ) ) {
			return 'brizy';
		} elseif ( $this->is_built_with_divi( $post_id ) ) {
			return 'divi';
		} elseif ( $this->is_built_with_bricks( $post_id ) ) {
			return 'bricks';
		} elseif ( $this->is_built_with_oxygen( $post_id ) ) {
			return 'oxygen';
		} elseif ( $this->is_built_with_breakdance( $post_id ) ) {
			return 'breakdance';
		} elseif ( $this->is_built_with_block( $post_id ) ) {
			return 'block';
		}

		return 'default';

	}

	/**
	 * Get active page builders.
	 *
	 * @return array The list of builder names.
	 */
	public function get_active_page_builders() {

		$names = array();

		if ( defined( 'ELEMENTOR_VERSION' ) || defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			$names[] = 'elementor';
		}

		if ( defined( 'ET_BUILDER_VERSION' ) ) {
			$names[] = 'divi';
		}

		if ( defined( 'FL_BUILDER_VERSION' ) ) {
			$names[] = 'beaver';
		}

		if ( defined( 'BRIZY_VERSION' ) ) {
			$names[] = 'brizy';
		}

		if ( defined( 'CT_VERSION' ) ) {
			$names[] = 'oxygen';
		}

		if ( defined( 'BRICKS_VERSION' ) ) {
			$names[] = 'bricks';
		}

		if ( defined( '__BREAKDANCE_VERSION' ) && defined( '__BREAKDANCE_DIR__' ) ) {
			$names[] = 'breakdance';
		}

		if ( ( new Block_Helper() )->is_active() ) {
			$names[] = 'block';
		}

		return $names;

	}

	/**
	 * Parse content with the specified builder in admin area.
	 *
	 * Note: If a page builder identifier matches with `$builder_name`,
	 * then the related constant & function are available.
	 * That means, we don't need to check `if ( defined( 'THEIR_CONSTANT' ) )` here.
	 *
	 * @param WP_Post|int $post Either the admin page's post object or post ID.
	 * @param string      $builder_name The content builder name.
	 * @param bool        $blog_switched Whether the blog has been switched.
	 * @param int         $original_blog_id The original blog ID.
	 * @param string      $location The output location. Accepts "admin_page" or "dashboard".
	 */
	public function output_content_using_builder( $post, $builder_name, $blog_switched = false, $original_blog_id = 1, $location = 'admin_page' ) {

		if ( is_int( $post ) ) {
			$post_id = $post;
			$post    = get_post( $post_id );
		} elseif ( is_object( $post ) && property_exists( $post, 'ID' ) ) {
			$post_id = $post->ID;
		} else {
			return;
		}

		do_action( 'ats_pro_output_builder_content', $post, $builder_name );

		if ( 'elementor' === $builder_name ) {

			$elementor = \Elementor\Plugin::$instance;

			$elementor->frontend->register_styles();
			$elementor->frontend->enqueue_styles();

			echo $elementor->frontend->get_builder_content( $post_id, true );

			$elementor->frontend->register_scripts();
			$elementor->frontend->enqueue_scripts();

		} elseif ( 'beaver' === $builder_name ) {

			echo do_shortcode( '[fl_builder_insert_layout id="' . $post_id . '"]' );

		} elseif ( 'divi' === $builder_name ) {

			$divi_helper = new Divi_Helper( $post );

			$divi_helper->render_content( $location );

		} elseif ( 'brizy' === $builder_name ) {

			$this->render_brizy_content( $post_id );

		} elseif ( 'bricks' === $builder_name ) {

			( new Bricks_Helper( $post ) )->render_content();

		} elseif ( 'oxygen' === $builder_name ) {

			if ( version_compare( CT_VERSION, '4.0', '>=' ) ) {
				if ( version_compare( CT_VERSION, '4.8.3', '>=' ) ) {
					$json = get_post_meta( $post_id, '_ct_builder_json', true );
				} else {
					$json = get_post_meta( $post_id, 'ct_builder_json', true );
				}

				if ( $json ) {
					$json = json_decode( $json, true );

					global $oxygen_doing_oxygen_elements;
					$oxygen_doing_oxygen_elements = true;

					echo do_oxygen_elements( $json );
				} else {
					$shortcodes = get_post_meta( $post_id, 'ct_builder_shortcodes', true );

					echo ct_do_shortcode( $shortcodes );
				}
			} else {
				$shortcodes = get_post_meta( $post_id, 'ct_builder_shortcodes', true );

				echo ct_do_shortcode( $shortcodes );
			}

		} elseif ( 'breakdance' === $builder_name ) {

			$breakdance_helper = new Breakdance_Helper( $post );
			$breakdance_helper->render_content();

		} elseif ( 'block' === $builder_name && class_exists( '\ATSDash\Helpers\Block_Helper' ) ) {

			$block_helper = new Block_Helper( $post );
			$block_helper->render_content();

		} else {

			/**
			 * Handle a case in multisite where a page was built with Bricks in the blueprint site
			 * and that page is inherited to subsite but Bricks is not installed there.
			 *
			 * In this case, we can only recognize it by hard-checking the `_bricks_page_content_2` post meta.
			 * If we have custom meta for ats (for instance: `ats_page_builder`), then we can use that instead.
			 * But we don't have that.
			 *
			 * This will only run if current user is a super admin.
			 */
			if ( $blog_switched && is_super_admin() ) {
				$might_was_built_with_bricks = get_post_meta( $post_id, '_bricks_page_content_2', true );

				if ( $might_was_built_with_bricks ) {
					( new Bricks_Helper( $post ) )->no_bricks_notice( $original_blog_id );
				}
			}

			echo apply_filters( 'the_content', $post->post_content );

		}

	}

	/**
	 * Prepare admin page output that was built with Divi Builder.
	 *
	 * @param WP_Post|int $post The post object or post ID.
	 */
	public function prepare_divi_output( $post ) {

		$post = is_object( $post ) && property_exists( $post, 'ID' ) ? $post : get_post( $post );

		$divi_helper = new Divi_Helper( $post );

		$divi_helper->prepare_hooks();

	}

	/**
	 * Prepare Brizy output.
	 * This function is being called in the admin page module output and widget module output.
	 *
	 * This source can be found at `is_view_page` condition inside `initialize_front_end` function
	 * in brizy/public/main.php file.
	 *
	 * What we don't use from that function:
	 * - preparePost private function
	 * - template_include hook
	 * - `wpautop` filter removal from `the_content` (moved to our `render_brizy_content` function)
	 *
	 * @param int    $post_id The post id.
	 * @param string $location The output location.
	 *                  Accepts "frontend", and other values (such as "admin_page", "dashboard").
	 *
	 * @see wp-content/plugins/brizy/public/main.php
	 * @see wp-content/plugins/brizy/editor/post.php
	 */
	public function prepare_brizy_output( $post_id, $location = 'admin_page' ) {

		$post = get_post( $post_id );

		if ( ! $post ) {
			return;
		}

		try {
			$brizy_post   = \Brizy_Editor_Post::get( $post_id );
			$brizy_public = \Brizy_Public_Main::get( $brizy_post );
		} catch ( Exception $e ) {
			return;
		}

		if ( 'admin_page' === $location || 'dashboard' === $location ) {

			/**
			 * Check if the post needs to be compiled.
			 *
			 * Let's compile it if it hasn't been compiled.
			 * However, when compiling it, it takes sometime.
			 *
			 * That's why it takes time / very slow when
			 * first time visiting the dashboard / admin page
			 * or first time visiting it after the post being updated with Brizy.
			 *
			 * However, in the next visit (since it has been compiled), it will be much faster.
			 *
			 * @see wp-content/plugins/brizy/public/main.php
			 * @see wp-content/plugins/brizy/editor/post.php
			 */
			$needs_compile = ! $brizy_post->isCompiledWithCurrentVersion() || $brizy_post->get_needs_compile();

			if ( $needs_compile ) {
				try {
					$brizy_post->compile_page();
				} catch ( Exception $e ) {
					return;
				}

				$brizy_post->saveStorage();
				$brizy_post->savePost();
			}

			// The value of $body_class is array, let's convert it to string.
			$body_classes = $brizy_public->body_class_frontend( array() );
			$body_classes = implode( ' ', $body_classes );

			add_filter(
				'admin_body_class',
				function ( $classes ) use ( $body_classes ) {
					return $classes . ' ' . $body_classes;
				}
			);

			// insert the compiled head and content.
			add_action( 'admin_head', array( $brizy_public, 'insert_page_head' ) );
			add_action( 'admin_bar_menu', array( $brizy_public, 'toolbar_link' ), 999 );
			add_action( 'admin_enqueue_scripts', array( $brizy_public, '_action_enqueue_preview_assets' ), 9999 );
			add_filter( 'the_content', array( $brizy_public, 'insert_page_content' ), -12000 );
			add_action( 'brizy_template_content', array( $brizy_public, 'brizy_the_content' ) );

			$this->handle_brizy_assets();

		} else {

			// Insert the compiled head and content.
			add_filter( 'body_class', array( $brizy_public, 'body_class_frontend' ) );
			add_action( 'wp_head', array( $brizy_public, 'insert_page_head' ) );
			add_action( 'admin_bar_menu', array( $brizy_public, 'toolbar_link' ), 999 );
			add_action( 'wp_enqueue_scripts', array( $brizy_public, '_action_enqueue_preview_assets' ), 9999 );
			add_filter( 'the_content', array( $brizy_public, 'insert_page_content' ), -12000 );
			add_action( 'brizy_template_content', array( $brizy_public, 'brizy_the_content' ) );

			$this->handle_brizy_assets();

		}

	}

	/**
	 * Handle Brizy assets.
	 */
	public function handle_brizy_assets() {

		if ( ! class_exists( '\Brizy_Public_AssetEnqueueManager' ) ) {
			return;
		}

		$brizy_enqueue_manager = \Brizy_Public_AssetEnqueueManager::_init();

		add_action( 'admin_enqueue_scripts', array( $brizy_enqueue_manager, 'enqueueStyles' ), 10002 );
		add_action( 'admin_enqueue_scripts', array( $brizy_enqueue_manager, 'enqueueScripts' ), 10002 );
		add_filter( 'admin_enqueue_scripts', array( $brizy_enqueue_manager, 'addEditorConfigVar' ), 10002 );
		add_filter( 'script_loader_tag', array( $brizy_enqueue_manager, 'addScriptAttributes' ), 10, 2 );
		add_filter( 'style_loader_tag', array( $brizy_enqueue_manager, 'addStyleAttributes' ), 10, 2 );
		add_action( 'admin_head', array( $brizy_enqueue_manager, 'insertHeadCodeAssets' ) );
		add_action( 'admin_footer', array( $brizy_enqueue_manager, 'insertBodyCodeAssets' ) );

	}

	/**
	 * Render Brizy content.
	 * This function is being called from `output_content_using_builder` method in this file.
	 *
	 * @param int $post_id The post id.
	 *
	 * @see wp-content/plugins/brizy/public/main.php
	 */
	public function render_brizy_content( $post_id ) {

		$post = get_post( $post_id );

		if ( ! $post ) {
			return;
		}

		// @see wp-content/plugins/brizy/public/main.php
		remove_filter( 'the_content', 'wpautop' );

		try {
			$brizy_post   = \Brizy_Editor_Post::get( $post_id );
			$brizy_public = \Brizy_Public_Main::get( $brizy_post );
		} catch ( Exception $e ) {
			return;
		}

		$brizy_public->brizy_the_content();

		// Let's bring back the filter after rendering the content.
		add_filter( 'the_content', 'wpautop' );

	}

	/**
	 * Prepare Beaver output.
	 * This function is being called from `render_dashboard_page` method in `class-widget-output.php` file.
	 *
	 * @param int    $post_id The post id.
	 * @param string $location The output location.
	 *                  Accepts "frontend", and other values (such as "admin_page", "dashboard").
	 *
	 * @see wp-content/plugins/bb-plugin/classes/class-fl-builder-icons.php in `enqueue_styles_for_module` inside the `elseif` block.
	 *
	 * @see wp-content/plugins/bb-plugin/classes/class-fl-builder.php
	 * @see wp-content/plugins/bb-plugin/classes/class-fl-builder-icons.php
	 */
	public function prepare_beaver_output( $post_id, $location = 'admin_page' ) {

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_beaver_assets' ) );

	}

	/**
	 * Enqueue Beaver assets.
	 */
	public function enqueue_beaver_assets() {

		/**
		 * Patch for Foundation Icons to work in Page Builder Dashboard.
		 *
		 * @see wp-content/plugins/bb-plugin/classes/class-fl-builder-icons.php in `enqueue_styles_for_icon`.
		 */
		wp_register_style( 'foundation-icons', 'https://cdnjs.cloudflare.com/ajax/libs/foundicons/3.0.0/foundation-icons.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );

	}

	/**
	 * Prepare admin page output that was built with Oxygen Builder.
	 *
	 * This function is being called in the admin page module output.
	 * Might also be called in the widget module output.
	 *
	 * @param int    $post_id The post id.
	 * @param string $location The output location.
	 *                  Accepts "frontend", and other values (such as "admin_page", "dashboard").
	 *
	 * @see wp-content/plugins/oxygen/component-framework/components/classes/gallery.class.php as a component example.
	 * @see wp-content/plugins/wp-admin-pages-pro/inc/class-wu-oxygen-builder-support.php to look at "wp admin pages pro" implementation of Oxygen support.
	 *
	 * @see wp-content/plugins/oxygen/component-framework/component-init.php
	 * @see wp-content/plugins/oxygen/component-framework/includes/cache.php
	 */
	public function prepare_oxygen_output( $post_id, $location = 'admin_page' ) {

		add_filter( 'admin_body_class', array( $this, 'oxygen_body_class' ) );
		add_action( 'admin_enqueue_scripts', 'ct_enqueue_scripts' );
		add_action(
			'admin_enqueue_scripts',
			function () use ( $post_id ) {
				$this->oxygen_enqueue_scripts( $post_id );
			}
		);

		$shortcodes = get_post_meta( $post_id, 'ct_builder_shortcodes', true );

		global $oxygen_vsb_components;

		if ( ! empty( $shortcodes ) && ! empty( $oxygen_vsb_components ) ) {
			foreach ( $oxygen_vsb_components as $component_name => $component ) {
				$contains_oxy_prefix = false !== stripos( $shortcodes, '[/oxy_' . $component_name . ']' );
				$contains_ct_prefix  = false !== stripos( $shortcodes, '[/ct_' . $component_name . ']' );

				if ( $contains_oxy_prefix || $contains_ct_prefix ) {
					if ( method_exists( $component, 'output_js' ) ) {
						add_action( 'admin_enqueue_scripts', array( $component, 'output_js' ) );
					}

					if ( method_exists( $component, 'js_css_output' ) ) {
						add_action( 'admin_enqueue_scripts', array( $component, 'js_css_output' ) );
					}
				}
			}
		}

	}

	/**
	 * Prepare admin page output that was built with Breakdance Builder.
	 *
	 * @param WP_Post|int $post The post object or post ID.
	 * @param string      $location The output location.
	 */
	public function prepare_breakdance_output( $post, $location = 'admin_page' ) {

		$post = is_object( $post ) && property_exists( $post, 'ID' ) ? $post : get_post( $post );

		$breakdance_helper = new Breakdance_Helper( $post );
		$breakdance_helper->prepare_output();

	}

	/**
	 * Prepare admin page output that was built with WordPress block editor.
	 *
	 * @param WP_Post|int $post The post object or post ID.
	 * @param string      $location The output location.
	 */
	public function prepare_block_output( $post, $location = 'admin_page' ) {

		$post = is_object( $post ) && property_exists( $post, 'ID' ) ? $post : get_post( $post );

		$block_helper = new Block_Helper( $post );
		$block_helper->prepare_hooks();

	}

	/**
	 * Add oxygen body classes.
	 * This function is being hooked in the prepare_oxygen_output() function.
	 *
	 * @param string $classes The existing body class names.
	 *
	 * @return string
	 */
	public function oxygen_body_class( $classes ) {

		$oxygen_classes = ct_body_class( array() );
		$oxygen_classes = implode( ' ', $oxygen_classes );

		return $classes . ' ' . $oxygen_classes;

	}

	/**
	 * Enqueue necessary assets to support oxygen builder.
	 * This function is being called in the prepare_oxygen_output() function.
	 *
	 * @param int $post_id The post ID.
	 */
	public function oxygen_enqueue_scripts( $post_id = 0 ) {

		$xlink = 'css';

		$load_cached_universal = false;

		// Check whether to load universal css or not.
		if ( get_option( 'oxygen_vsb_universal_css_cache' ) && get_option( 'oxygen_vsb_universal_css_cache_success' ) ) {
			$xlink = 'css&nouniversal=true';

			$load_cached_universal = true;
		}

		// Check whether to load dynamic xlink or cached CSS files.
		if ( ! oxygen_vsb_load_cached_css_files() ) {
			wp_enqueue_style( 'oxygen-styles', ct_get_current_url( 'xlink=' . $xlink ) );
		}

		if ( $load_cached_universal ) {
			$universal_css_url = get_option( 'oxygen_vsb_universal_css_url' );
			$universal_css_url = add_query_arg( 'cache', get_option( 'oxygen_vsb_last_save_time' ), $universal_css_url );

			wp_enqueue_style( 'oxygen-universal-styles', $universal_css_url );

			if ( $post_id ) {
				$files_meta = get_option( 'oxygen_vsb_css_files_state', array() );

				if ( isset( $files_meta[ $post_id ]['success'] ) ) {
					$individual_css_url = $files_meta[ $post_id ]['url'];
					$individual_css_url = add_query_arg( 'cache', $files_meta[ $post_id ]['last_save_time'], $individual_css_url );

					wp_enqueue_style( 'oxygen-cache-' . $post_id, $individual_css_url );
				}
			}
		}

	}

	/**
	 * Get saved templates for specified page builder.
	 *
	 * @param string $builder The page builder name.
	 *
	 * @return array The saved templates.
	 */
	public function get_page_builder_templates( $builder ) {

		$templates = array();

		if ( 'elementor' === $builder ) {
			$builder_posts = get_posts(
				array(
					'post_type'   => 'elementor_library',
					'post_status' => 'publish',
					'numberposts' => -1,
				)
			);

			foreach ( $builder_posts as $builder_post ) {
				$templates[] = array(
					'id'      => $builder_post->ID,
					'title'   => $builder_post->post_title,
					'builder' => 'elementor',
				);
			}
		} elseif ( 'divi' === $builder ) {
			$divi_layout_post_type = defined( 'ET_BUILDER_LAYOUT_POST_TYPE' ) ? ET_BUILDER_LAYOUT_POST_TYPE : 'et_pb_layout';

			$builder_posts = get_posts(
				array(
					'post_type'   => $divi_layout_post_type,
					'post_status' => 'publish',
					'numberposts' => -1,
				)
			);

			foreach ( $builder_posts as $builder_post ) {
				$templates[] = array(
					'id'      => $builder_post->ID,
					'title'   => $builder_post->post_title,
					'builder' => 'divi',
				);
			}
		} elseif ( 'beaver' === $builder ) {
			if ( class_exists( '\FLBuilderModel' ) ) {
				$builder_posts = get_posts(
					array(
						'post_type'   => 'fl-builder-template',
						'post_status' => 'publish',
						'numberposts' => -1,
					)
				);

				foreach ( $builder_posts as $builder_post ) {
					$templates[] = array(
						'id'      => $builder_post->ID,
						'title'   => $builder_post->post_title,
						'builder' => 'beaver',
					);
				}
			}
		} elseif ( 'brizy' === $builder ) {
			$builder_posts = get_posts( array(
				'post_type'   => 'editor-template',
				'post_status' => 'publish',
				'numberposts' => -1,
			) );

			// If brizy templates are not found, try to find them using their old post type.
			if ( empty( $builder_posts ) ) {
				$builder_posts = get_posts( array(
					'post_type'   => 'brizy_template',
					'post_status' => 'publish',
					'numberposts' => -1,
				) );
			}

			foreach ( $builder_posts as $builder_post ) {
				$templates[] = array(
					'id'      => $builder_post->ID,
					'title'   => $builder_post->post_title,
					'builder' => 'brizy',
				);
			}
		} elseif ( 'bricks' === $builder ) {
			$bricks_helper = new Bricks_Helper();
			$builder_posts = $bricks_helper->get_templates();

			foreach ( $builder_posts as $builder_post ) {
				$templates[] = array(
					'id'      => $builder_post->ID,
					'title'   => $builder_post->post_title,
					'builder' => 'bricks',
				);
			}
		} elseif ( 'oxygen' === $builder ) {
			$builder_posts = get_posts(
				array(
					'post_type'   => 'ct_template',
					'post_status' => 'publish',
					'numberposts' => -1,
				)
			);

			foreach ( $builder_posts as $builder_post ) {
				$templates[] = array(
					'id'      => $builder_post->ID,
					'title'   => $builder_post->post_title,
					'builder' => 'oxygen',
				);
			}
		} elseif ( 'breakdance' === $builder ) {
			if ( function_exists( '\Breakdance\Themeless\getTemplatesAsWPPosts' ) ) {
				$builder_posts = \Breakdance\Themeless\getTemplatesAsWPPosts();

				foreach ( $builder_posts as $builder_post ) {
					$templates[] = array(
						'id'      => $builder_post->ID,
						'title'   => $builder_post->post_title,
						'builder' => 'breakdance',
					);
				}
			}
		} elseif ( 'block' === $builder ) {
			$builder_posts = get_posts(
				array(
					'post_type'   => 'ats_block_template',
					'post_status' => 'publish',
					'numberposts' => -1,
				)
			);

			foreach ( $builder_posts as $builder_post ) {
				$templates[] = array(
					'id'      => $builder_post->ID,
					'title'   => $builder_post->post_title,
					'builder' => 'block',
				);
			}
		}

		return $templates;

	}

}
