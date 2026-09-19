<?php
/**
 * Position metabox.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $post ) {

	wp_nonce_field( 'ats_position', 'ats_position_nonce' );

	$saved_meta = get_post_meta( $post->ID, 'ats_position_key', true );

	if ( ! $saved_meta ) {
		$saved_meta = 'normal';
	}

	?>

	<ul>
		<li>
			<label>
				<input type="radio" name="ats_metabox_position" value="normal" <?php checked( $saved_meta, 'normal' ); ?> />
				<?php esc_html_e( 'Left column', 'ats-dashboard' ); ?>
			</label>
		</li>
		<li>
			<label>
				<input type="radio" name="ats_metabox_position" value="side" <?php checked( $saved_meta, 'side' ); ?> />
				<?php esc_html_e( 'Right column', 'ats-dashboard' ); ?>
			</label>
		</li>
	</ul>

	<?php

};
