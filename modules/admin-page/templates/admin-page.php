<?php
/**
 * ATS Dashboard admin page.
 *
 * Variables brought from "render_admin_page($post, $multisite)" function.
 * - $post
 * - $from_multisite
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Helpers\Content_Helper;

$content_helper = new Content_Helper();

do_action( 'ats_ms_switch_blog' );

$editor = $content_helper->get_content_editor( $post->ID );

do_action( 'ats_ms_restore_blog' );

$remove_page_title  = $post->remove_page_title;
$remove_page_margin = $post->remove_page_margin;

$custom_css = $post->custom_css;
?>

<style>
	<?php if ( $remove_page_margin ) : ?>
	#wpcontent {
		padding-left: 0;
	}

	.wrap {
		margin: 0;
	}

	<?php endif; ?>

	<?php
	if ( $custom_css ) {
		echo $content_helper->sanitize_css( $custom_css ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	?>
</style>

<div class="wrap">
	<?php if ( ! $remove_page_title ) : ?>
		<h1><?php echo esc_html( $post->post_title ); ?></h1>
	<?php else : ?>
		<h1 style="display: none;"></h1>
	<?php endif; ?>

	<?php
	/**
	 * Renders the page content (HTML content, or the active page builder's
	 * output — Elementor, Divi, Beaver, Brizy, Bricks, Oxygen, Breakdance,
	 * blocks, or the plain `the_content` fallback).
	 *
	 * @see \ATSDash\AdminPage\Admin_Page_Output::output_content()
	 */
	do_action( 'ats_admin_page_content_output', $post, $editor, $from_multisite );
	?>
</div>
