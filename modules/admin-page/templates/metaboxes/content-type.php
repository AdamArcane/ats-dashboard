<?php
/**
 * Content type metabox.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $post ) {

	$content_type = get_post_meta( $post->ID, 'ats_content_type', true );
	$content_type = $content_type ? $content_type : 'builder';

	?>

	<select name="ats_content_type" id="ats_content_type">
		<option value="builder" <?php selected( $content_type, 'builder' ); ?>><?php esc_html_e( 'Default Editor', 'ats-dashboard' ); ?></option>
		<option value="html" <?php selected( $content_type, 'html' ); ?>><?php esc_html_e( 'HTML Editor', 'ats-dashboard' ); ?></option>
	</select>

	<?php
};
