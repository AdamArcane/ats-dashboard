<?php
/**
 * HTML content metabox.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $post ) {

	$html_content = get_post_meta( $post->ID, 'ats_html_content', true );
	?>

	<textarea class="widefat textarea ats-html-editor ats-codemirror" name="ats_html_content" data-content-mode="html"><?php echo esc_textarea( wp_unslash( $html_content ) ); ?></textarea>

	<?php

};
