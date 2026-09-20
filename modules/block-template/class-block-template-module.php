<?php
/**
 * Block editor template.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\BlockTemplate;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Base\Base_Module;

/**
 * Class to set up block editor template CPT.
 */
class Block_Template_Module extends Base_Module {

	/**
	 * The class instance.
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Module constructor.
	 */
	public function __construct() {}

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
	 * Setup block editor template.
	 */
	public function setup() {

		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'flush_rewrite_rules' ), 20 );
		add_filter( 'template_include', array( $this, 'include_template' ), 1 );

	}

	/**
	 * Use a blank template for block editor template frontend pages.
	 *
	 * @param string $template The template path.
	 * @return string The template path.
	 */
	public function include_template( $template ) {

		if ( 'ats_block_template' !== get_post_type() ) {
			return $template;
		}

		return __DIR__ . '/templates/block-template-view.php';

	}

	/**
	 * Flush rewrite rules once after the CPT is registered.
	 */
	public function flush_rewrite_rules() {

		if ( ! get_option( 'ats_block_template_flush_rewrite_rules' ) ) {
			flush_rewrite_rules( false );
			update_option( 'ats_block_template_flush_rewrite_rules', 1 );
		}

	}

	/**
	 * Register the ats_block_template post type.
	 */
	public function register_post_type() {

		$register = require __DIR__ . '/inc/post-type.php';
		$register();

	}

}

