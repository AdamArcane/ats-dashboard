<?php
/**
 * HTML widget.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	global $post;

	$content = get_post_meta( $post->ID, 'ats_html', true );

	?>

	<div data-type="html">

		<div class="postbox">
			<div class="postbox-header">
				<h2><?php esc_html_e( 'HTML', 'ats-dashboard' ); ?></h2>
			</div>
			<div class="inside">
				<textarea class="widefat textarea" name="ats_html"><?php echo esc_textarea( wp_unslash( $content ) ); ?></textarea>
			</div>
		</div>

	</div>

	<?php

};
