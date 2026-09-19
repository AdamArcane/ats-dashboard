<?php
/**
 * Admin bar logo field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$branding       = get_option( 'ats_branding' );
	$is_checked     = isset( $branding['remove_admin_bar_logo'] ) ? $branding['remove_admin_bar_logo'] : 0;
	$admin_bar_logo = isset( $branding['admin_bar_logo_image'] ) ? $branding['admin_bar_logo_image'] : false;

	?>

	<div class="field admin-bar-logo-image-field">
		<input type="text" name="ats_branding[admin_bar_logo_image]" value="<?php echo esc_url( $admin_bar_logo ); ?>" class="all-options ats-branding-upload-image">
		<button type="button" class="ats-branding-admin-bar-logo-upload button-secondary" data-media-library-title="Admin Bar Logo">
			<?php _e( 'Add or Upload File', 'ats-dashboard' ); ?>
		</button>
		<a href="#" class="ats-branding-clear-upload button-secondary">x</a>
	</div>


	<div class="field setting-field" style="margin-top: 10px;">
		<label for="ats_branding[remove_admin_bar_logo]" class="label checkbox-label">
			<?php _e( 'Remove Admin Bar Logo', 'ats-dashboard' ); ?>
			<input type="checkbox" name="ats_branding[remove_admin_bar_logo]" id="ats_branding[remove_admin_bar_logo]" value="1" class="ats-remove-wp-logo" <?php checked( $is_checked, 1 ); ?>>
			<div class="indicator"></div>
		</label>
	</div>

	<?php

};
