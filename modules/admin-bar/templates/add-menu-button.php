<?php
/**
 * Add menu button template.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {
	?>

	<button type="button" class="ats-menu-builder--add-item ats-menu-builder--add-new-menu">
		<i class="dashicons dashicons-plus"></i>
		<?php _e( 'Add Item', 'ats-dashboard' ); ?>
	</button>

	<?php
};
