<?php
/**
 * Text widget.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	global $post;

	$content        = get_post_meta( $post->ID, 'ats_content', true );
	$content_height = get_post_meta( $post->ID, 'ats_content_height', true );
	$editor         = 'ats_content';
	$args           = array(
		'media_buttons' => false,
		'editor_height' => 300,
		'teeny'         => true,
	);

	?>

	<div data-type="text">

		<div class="postbox">
			<div class="postbox-header">
				<h2><?php esc_html_e( 'Content', 'ats-dashboard' ); ?></h2>
			</div>
			<div class="inside">
				<?php wp_editor( $content, $editor, $args ); ?>
			</div>
		</div>

		<div class="postbox">
			<div class="postbox-header">
				<h2><?php esc_html_e( 'Fixed Height', 'ats-dashboard' ); ?></h2>
			</div>
			<div class="inside">
				<input type="text" name="ats_content_height" placeholder="200px" value="<?php echo esc_attr( $content_height ? $content_height : '' ); ?>">
			</div>
		</div>

	</div>

	<?php

};
