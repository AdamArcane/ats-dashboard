<?php
/**
 * Placeholder tags metabox.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Widget\Widget_Base_Output;

return function () {

	$widget_output    = Widget_Base_Output::get_instance();
	$placeholder_tags = $widget_output->placeholder_tags;
	$placeholder_tags = apply_filters( 'ats_widgets_placeholder_tags', $placeholder_tags );
	?>

	<p>
		<?php esc_html_e( 'Use the placeholder tags below to display certain information dynamically.', 'ats-dashboard' ); ?>
		<br><strong><?php esc_html_e( '(Click to copy)', 'ats-dashboard' ); ?></strong>
	</p>

	<div class="tags-wrapper">
		<?php
		foreach ( $placeholder_tags as $tag_index => $placeholder_tag ) {
			?>
			<code><?php echo esc_attr( $placeholder_tag ); ?></code>
			<?php
		}
		?>
	</div>

	<?php

};
