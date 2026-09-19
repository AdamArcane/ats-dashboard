<?php
/**
 * Priority metabox.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $post ) {

	wp_nonce_field( 'ats_priority', 'ats_priority_nonce' );

	$saved_meta = get_post_meta( $post->ID, 'ats_priority_key', true );

	if ( ! $saved_meta ) {
		$saved_meta = 'default';
	}

	?>

	<ul>
		<li>
			<label>
				<input type="radio" name="ats_metabox_priority" value="default" <?php checked( $saved_meta, 'default' ); ?> />
				<?php esc_html_e( 'Default', 'ats-dashboard' ); ?>
			</label>
		</li>
		<li>
			<label>
				<input type="radio" name="ats_metabox_priority" value="low" <?php checked( $saved_meta, 'low' ); ?> />
				<?php esc_html_e( 'Low', 'ats-dashboard' ); ?>
			</label>
		</li>
		<li>
			<label>
				<input type="radio" name="ats_metabox_priority" value="high" <?php checked( $saved_meta, 'high' ); ?> />
				<?php esc_html_e( 'High', 'ats-dashboard' ); ?>
			</label>
		</li>
	</ul>

	<?php

};
