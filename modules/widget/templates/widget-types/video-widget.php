<?php
/**
 * Video widget.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	global $post;

	if ( function_exists( 'wp_enqueue_media' ) ) {
		wp_enqueue_media();
	} else {
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
	}

	?>

	<div data-type="video">

		<div class="postbox">
			<div class="postbox-header">
				<h2><?php _e( 'Video URL', 'ats-dashboard' ); ?></h2>
			</div>
			<div class="inside">
				<?php $stored_meta = get_post_meta( $post->ID, 'ats_video_id', true ); ?>
				<input id="ats-video-id" type="url" placeholder="https://www.youtube.com/watch?v=YlUKcNNmywk" name="ats_video_id" value="<?php echo esc_attr( $stored_meta ? $stored_meta : '' ); ?>" />
			</div>
		</div>

		<div class="postbox">
			<div class="postbox-header">
				<h2><?php _e( 'Platform', 'ats-dashboard' ); ?></h2>
			</div>
			<div class="inside">
				<?php
				$stored_meta = get_post_meta( $post->ID, 'ats_video_platform', true );
				$stored_meta = $stored_meta ? $stored_meta : 'youtube';
				?>
				<select id="ats-video-platform" name="ats_video_platform">
					<option value="youtube" <?php selected( $stored_meta, 'youtube' ); ?>><?php _e( 'YouTube', 'ats-dashboard' ); ?></option>
					<option value="vimeo" <?php selected( $stored_meta, 'vimeo' ); ?>><?php _e( 'Vimeo', 'ats-dashboard' ); ?></option>
				</select>
			</div>
		</div>

		<div class="postbox">
			<div class="postbox-header">
				<h2><?php _e( 'Thumbnail', 'ats-dashboard' ); ?></h2>
			</div>
			<div class="inside ats-metabox-field">
				<label for="ats-video-thumbnail">
					<?php _e( 'Required for Vimeo videos, optional for YouTube videos.', 'ats-dashboard' ); ?>
				</label>
				<br>
				<?php $stored_meta = get_post_meta( $post->ID, 'ats_video_thumbnail', true ); ?>
				<input style="max-width: 400px" id="ats-video-thumbnail" class="ats-video-thumbnail-url" type="text" name="ats_video_thumbnail" value="<?php echo esc_attr( $stored_meta ? $stored_meta : '' ); ?>">
				<a href="#" class="ats-video-thumbnail-upload button-secondary"><?php _e( 'Add or Upload File', 'ats-dashboard' ); ?></a>
				<a href="#" class="ats-video-thumbnail-remove button-secondary">x</a>
			</div>
		</div>

	</div>

	<?php

};
