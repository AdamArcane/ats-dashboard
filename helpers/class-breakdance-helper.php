<?php
/**
 * Breakdance builder helper.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Helpers;

use ReflectionClass;
use WP_Post;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Class to setup Breakdance helper.
 */
class Breakdance_Helper {

	/**
	 * WP_Post instance.
	 *
	 * @var WP_Post
	 */
	private $post;

	/**
	 * Class constructor.
	 *
	 * @param WP_Post|null $post Instance of WP_Post.
	 */
	public function __construct( $post = null ) {

		$this->post = $post;

	}

	/**
	 * Check whether "Breakdance" builder is active.
	 *
	 * @return bool
	 */
	public function is_active() {

		if (
			function_exists( '\Breakdance\Render\render' )
			&& function_exists( '\Breakdance\Render\getWordPressHtmlOutput' )
			&& function_exists( '\Breakdance\Render\renderHtmlFromScriptAndStyleHolder' )
			&& function_exists( '\Breakdance\Themeless\outputHeadHtml' )
			&& function_exists( '\Breakdance\Themeless\get_header_for_theme_simulator_having_breakdance_template_for_request' )
			&& function_exists( '\Breakdance\Themeless\get_footer_for_theme_simulator_having_breakdance_template_for_request' )
			&& class_exists( '\Breakdance\Render\ScriptAndStyleHolder' )
			&& defined( '__BREAKDANCE_DIR__' )
			&& defined( 'BREAKDANCE_HEADER_ASSETS_PLACEHOLDER' )
			&& defined( 'BREAKDANCE_FOOTER_ASSETS_PLACEHOLDER' )
			&& defined( 'BREAKDANCE_ASSETS_PRIORITY' )
		) {
			return true;
		}

		return false;

	}

	/**
	 * Verify if a post was built with Breakdance Builder.
	 *
	 * @return bool
	 */
	public function built_with_breakdance() {

		if ( ! $this->is_active() ) {
			return false;
		}

		if ( ! get_post_meta( $this->post->ID, 'breakdance_dependency_cache', true ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Prepare Breakdance output.
	 */
	public function prepare_output() {

		if ( ! $this->is_active() ) {
			return;
		}

		/*
		 * Breakdance elements rely on the 'breakdance' class as a prefix for their styles.
		 * In Disable Theme mode, render() returns HTML without a wrapper div,
		 * expecting the class on <body> instead (see Breakdance's outputHeadHtml()).
		 */
		add_filter(
			'admin_body_class',
			function ( $classes ) {
				$classes .= ' breakdance';
				return $classes;
			}
		);

		/*
		 * Ensure ScriptAndStyleHolder has the correct blog's global settings.
		 * Breakdance populates this at 'init', but in multisite with blog switching
		 * (switch_to_blog), the singleton may hold the original blog's data instead of the target blog's.
		 */
		if ( function_exists( '\Breakdance\Render\getGlobalSettingsCache' ) && class_exists( '\Breakdance\Render\ScriptAndStyleHolder' ) ) {
			$global_cache = \Breakdance\Render\getGlobalSettingsCache();

			if ( isset( $global_cache['dependencyCache'] ) && $global_cache['dependencyCache'] ) {
				\Breakdance\Render\ScriptAndStyleHolder::getInstance()->append( $global_cache['dependencyCache'] );
			}

			if ( isset( $global_cache['cssCache'] ) && $global_cache['cssCache'] ) {
				\Breakdance\Render\ScriptAndStyleHolder::getInstance()->setGlobalGeneratedCssFilePaths( $global_cache['cssCache'] );
			}
		}

		/*
		 * Load normalize.css when Breakdance's theme is disabled or zero-theme is active.
		 * The render_content() method already handles all Breakdance global CSS (settings, presets,
		 * variables, element-specific styles) self-contained via ScriptAndStyleHolder — but
		 * normalize.css is only loaded by Breakdance's outputHeadHtml() which uses wp_head()
		 * and never fires in admin context.
		 */
		$is_theme_disabled    = function_exists( '\Breakdance\Themeless\ThemeDisabler\is_theme_disabled' ) && \Breakdance\Themeless\ThemeDisabler\is_theme_disabled();
		$is_zero_theme_active = function_exists( '\Breakdance\Themeless\ThemeDisabler\is_zero_theme_enabled' ) && \Breakdance\Themeless\ThemeDisabler\is_zero_theme_enabled();

		if ( $is_theme_disabled || $is_zero_theme_active ) {
			add_action(
				'admin_head',
				function () {
					if ( function_exists( '\Breakdance\Themeless\getNormalizeDotCssLinkTag' ) ) {
						echo \Breakdance\Themeless\getNormalizeDotCssLinkTag();
					}
				}
			);
		}

	}

	/**
	 * Render the content of a post.
	 */
	public function render_content() {

		if ( ! $this->post ) {
			return;
		}

		if ( ! $this->is_active() ) {
			echo apply_filters( 'the_content', $this->post->post_content );

			return;
		}

		// echo \Breakdance\Render\render( $post_id );

		ob_start();

		echo BREAKDANCE_HEADER_ASSETS_PLACEHOLDER;

		$renderedTemplateHtml = \Breakdance\Render\render( $this->post->ID );

		echo $renderedTemplateHtml;

		echo BREAKDANCE_FOOTER_ASSETS_PLACEHOLDER;

		$html = ob_get_clean();

		$headerAndFooterHtml = \Breakdance\Render\renderHtmlFromScriptAndStyleHolder(
			\Breakdance\Render\ScriptAndStyleHolder::getInstance()
		);

		echo str_replace(
			array( BREAKDANCE_HEADER_ASSETS_PLACEHOLDER, BREAKDANCE_FOOTER_ASSETS_PLACEHOLDER ),
			array( $headerAndFooterHtml['headerHtml'], $headerAndFooterHtml['footerHtml'] ),
			$html
		);

	}

}
