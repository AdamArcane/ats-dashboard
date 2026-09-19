<?php
/**
 * Advanced metabox.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $post ) {

	$custom_css = get_post_meta( $post->ID, 'ats_custom_css', true );
	?>

	<h4><?php esc_html_e( 'Custom CSS', 'ats-dashboard' ); ?></h4>
	<textarea id="ats_custom_css" class="widefat textarea ats-custom-css ats-codemirror" name="ats_custom_css" data-content-mode="css"><?php echo esc_textarea( wp_unslash( $custom_css ) ); ?></textarea>

	<?php

	do_action( 'ats_admin_page_advanced_fields', $post );

};
