<?php
/**
 * Add separator button template.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {
	?>

	<button type="button" class="ats-menu-builder--add-item ats-menu-builder--add-new-separator">
		<i class="dashicons dashicons-plus"></i>
		<?php _e( 'Add Menu Separator', 'ats-dashboard' ); ?>
	</button>

	<?php
};
