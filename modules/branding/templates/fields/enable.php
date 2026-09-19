<?php
/**
 * Enable branding field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$branding   = get_option( 'ats_branding' );
	$is_checked = isset( $branding['enabled'] ) ? 1 : 0;
	?>

	<div class="field setting-field">
		<label for="ats_branding[enabled]" class="label checkbox-label">
			&nbsp;
			<input type="checkbox" name="ats_branding[enabled]" id="ats_branding[enabled]" value="1" class="ats-enable-branding" <?php checked( $is_checked, 1 ); ?>>
			<div class="indicator"></div>
		</label>
	</div>

	<?php

};
