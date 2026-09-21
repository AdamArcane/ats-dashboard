<?php
/**
 * Content type metabox.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module, $post ) {

	$content_type = get_post_meta( $post->ID, 'ats_content_type', true );
	$content_type = $content_type ? $content_type : 'builder';

	$builder_labels = array(
		'elementor'  => __( 'Elementor', 'ats-dashboard' ),
		'beaver'     => __( 'Beaver Builder', 'ats-dashboard' ),
		'brizy'      => __( 'Brizy', 'ats-dashboard' ),
		'divi'       => __( 'Divi Builder', 'ats-dashboard' ),
		'bricks'     => __( 'Bricks Builder', 'ats-dashboard' ),
		'oxygen'     => __( 'Oxygen Builder', 'ats-dashboard' ),
		'breakdance' => __( 'Breakdance', 'ats-dashboard' ),
	);

	$detected_editor = $module->content()->get_content_editor( $post->ID );
	$active_builder   = isset( $builder_labels[ $detected_editor ] ) ? $builder_labels[ $detected_editor ] : '';

	?>

	<select name="ats_content_type" id="ats_content_type">
		<option value="builder" <?php selected( $content_type, 'builder' ); ?>><?php esc_html_e( 'Default Editor', 'ats-dashboard' ); ?></option>
		<option value="html" <?php selected( $content_type, 'html' ); ?>><?php esc_html_e( 'HTML Editor', 'ats-dashboard' ); ?></option>
	</select>

	<?php if ( 'builder' === $content_type && $active_builder ) : ?>
		<p class="description">
			<?php
			echo esc_html(
				sprintf(
					/* translators: %s: the detected page builder name, e.g. "Elementor". */
					__( 'Built with: %s', 'ats-dashboard' ),
					$active_builder
				)
			);
			?>
		</p>
	<?php endif; ?>

	<?php
};
