<?php
/**
 * Force admin theme field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$branding   = get_option( 'ats_branding' );
	$is_checked = isset( $branding['force_admin_theme'] ) ? 1 : 0;
	?>

	<div class="field setting-field">
		<label for="ats_branding[force_admin_theme]" class="label checkbox-label">
			&nbsp;
			<input type="checkbox" name="ats_branding[force_admin_theme]" id="ats_branding[force_admin_theme]" value="1" <?php checked( $is_checked, 1 ); ?>>
			<div class="indicator"></div>
		</label>
	</div>

	<p class="description"><?php esc_html_e( 'Locks every user onto this admin theme instead of their own personal WordPress Admin Color Scheme, and removes the color scheme picker from each user\'s profile page. When unchecked, users may pick their own WordPress admin color scheme, which can override some of the branding colors below.', 'ats-dashboard' ); ?></p>

	<?php

};
