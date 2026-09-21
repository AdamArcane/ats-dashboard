<?php
/**
 * RSS feed widget.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	global $post;

	$feed_url       = get_post_meta( $post->ID, 'ats_rss_feed_url', true );
	$max_items      = get_post_meta( $post->ID, 'ats_rss_max_items', true );
	$max_items      = $max_items ? $max_items : 5;
	$show_images    = get_post_meta( $post->ID, 'ats_rss_show_images', true );
	$show_excerpt   = get_post_meta( $post->ID, 'ats_rss_show_excerpt', true );
	$excerpt_length = get_post_meta( $post->ID, 'ats_rss_excerpt_length', true );
	$excerpt_length = $excerpt_length ? $excerpt_length : 20;
	$show_author    = get_post_meta( $post->ID, 'ats_rss_show_author', true );
	$show_date      = get_post_meta( $post->ID, 'ats_rss_show_date', true );
	$new_tab        = get_post_meta( $post->ID, 'ats_rss_new_tab', true );

	?>

	<div data-type="rss">

		<div class="postbox">
			<div class="postbox-header">
				<h2><?php esc_html_e( 'RSS Feed', 'ats-dashboard' ); ?></h2>
			</div>
			<div class="inside">

				<div class="ats-metabox-field">
					<label for="ats_rss_feed_url"><?php esc_html_e( 'Feed URL', 'ats-dashboard' ); ?></label>
					<input type="url" class="widefat" id="ats_rss_feed_url" name="ats_rss_feed_url" value="<?php echo esc_attr( $feed_url ); ?>" placeholder="https://example.com/feed" />
				</div>

				<div class="ats-metabox-field">
					<label for="ats_rss_max_items"><?php esc_html_e( 'Max Items', 'ats-dashboard' ); ?></label>
					<input type="number" id="ats_rss_max_items" name="ats_rss_max_items" value="<?php echo esc_attr( $max_items ); ?>" min="1" max="20" step="1" class="small-text" />
				</div>

				<div class="ats-metabox-field">
					<label>
						<input type="checkbox" name="ats_rss_show_images" <?php checked( $show_images, '1' ); ?>>
						<?php esc_html_e( 'Show item images', 'ats-dashboard' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'Uses the feed item\'s enclosure or media thumbnail, when the feed provides one.', 'ats-dashboard' ); ?></p>
				</div>

				<div class="ats-metabox-field">
					<label>
						<input type="checkbox" id="ats_rss_show_excerpt" name="ats_rss_show_excerpt" <?php checked( $show_excerpt, '1' ); ?>>
						<?php esc_html_e( 'Show excerpt', 'ats-dashboard' ); ?>
					</label>
				</div>

				<div class="ats-metabox-field">
					<label for="ats_rss_excerpt_length"><?php esc_html_e( 'Excerpt Length (words)', 'ats-dashboard' ); ?></label>
					<input type="number" id="ats_rss_excerpt_length" name="ats_rss_excerpt_length" value="<?php echo esc_attr( $excerpt_length ); ?>" min="5" max="100" step="1" class="small-text" />
				</div>

				<div class="ats-metabox-field">
					<label>
						<input type="checkbox" name="ats_rss_show_author" <?php checked( $show_author, '1' ); ?>>
						<?php esc_html_e( 'Show author', 'ats-dashboard' ); ?>
					</label>
				</div>

				<div class="ats-metabox-field">
					<label>
						<input type="checkbox" name="ats_rss_show_date" <?php checked( $show_date, '1' ); ?>>
						<?php esc_html_e( 'Show date', 'ats-dashboard' ); ?>
					</label>
				</div>

				<div class="ats-metabox-field">
					<label>
						<input type="checkbox" name="ats_rss_new_tab" <?php checked( $new_tab, '1' ); ?>>
						<?php esc_html_e( 'Open links in a new tab', 'ats-dashboard' ); ?>
					</label>
				</div>

			</div>
		</div>

	</div>

	<?php

};
