<?php
/**
 * Block editor logo field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$branding           = get_option( 'ats_branding' );
	$block_editor_logo = isset( $branding['block_editor_logo_image'] ) ? $branding['block_editor_logo_image'] : false;

	?>

	<input type="text" name="ats_branding[block_editor_logo_image]" value="<?php echo esc_url( $block_editor_logo ); ?>" class="all-options ats-branding-upload-image">
	<button type="button" class="ats-branding-admin-bar-logo-upload button-secondary" data-media-library-title="Block Editor Logo">
		<?php _e( 'Add or Upload File', 'ats-dashboard' ); ?>
	</button>
	<a href="#" class="ats-branding-clear-upload button-secondary">x</a>

	<p class="description">
		<?php _e( 'Replace the logo on the top-left inside the WordPress block editor.', 'ats-dashboard' ); ?><br>
		<?php _e( '<strong>Recommended image size:</strong> 512px x 512px.', 'ats-dashboard' ); ?>
	</p>

	<?php

};
