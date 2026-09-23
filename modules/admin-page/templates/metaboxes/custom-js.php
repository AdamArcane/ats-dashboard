<?php
/**
 * Custom JS field inside "Advanced" metabox.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $post ) {
	if ( ! current_user_can( 'unfiltered_html' ) ) {
		return;
	}

	$custom_js = get_post_meta( $post->ID, 'ats_custom_js', true );
	?>

	<h4><?php _e( 'Custom JS', 'ats-dashboard' ); ?></h4>
	<textarea id="ats_custom_js" class="widefat textarea ats-custom-js ats-codemirror" name="ats_custom_js"
				data-content-mode="js"><?php echo esc_textarea( wp_unslash( $custom_js ) ); ?></textarea>

	<?php

};
