<?php
/**
 * Widget types metabox.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {
	global $post;

	$widget_types = array(
		'icon' => __( 'Icon Widget', 'ats-dashboard' ),
		'text' => __( 'Text Widget', 'ats-dashboard' ),
		'html' => __( 'HTML Widget', 'ats-dashboard' ),
		'rss'  => __( 'RSS Feed Widget', 'ats-dashboard' ),
	);

	$widget_types = apply_filters( 'ats_widget_types', $widget_types );
	$stored_meta  = get_post_meta( $post->ID, 'ats_widget_type', true );

	?>

	<div class="ats-main-metabox">
		<div class="postbox">
			<div class="postbox-header">
				<h2><?php esc_html_e( 'Widget Type', 'ats-dashboard' ); ?></h2>
			</div>
			<?php wp_nonce_field( 'ats_widget_type', 'ats_widget_type_nonce' ); ?>
			<div class="inside">
				<select name="ats_widget_type">
					<?php foreach ( $widget_types as $value => $text ) { ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $stored_meta ); ?>><?php echo esc_html( $text ); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>

		<div class="widget-fields">
			<?php do_action( 'ats_widget_metabox' ); ?>
		</div>

		<div class="postbox">
			<div class="postbox-header">
				<h2><?php esc_html_e( 'Custom CSS', 'ats-dashboard' ); ?></h2>
			</div>
			<div class="inside">
				<p class="description">
					<?php
					printf(
						/* translators: %s: the {{WRAPPER}} placeholder token. */
						esc_html__( 'Applies to this widget only. Use %s in a selector to scope it to this widget; anything else applies dashboard-wide.', 'ats-dashboard' ),
						'<code>{{WRAPPER}}</code>'
					);
					?>
				</p>
				<textarea id="ats_custom_css" class="widefat textarea ats-css-code-editor" name="ats_custom_css" rows="10"><?php echo esc_textarea( wp_unslash( get_post_meta( $post->ID, 'ats_custom_css', true ) ) ); ?></textarea>
			</div>
		</div>

	</div>

	<?php
};
