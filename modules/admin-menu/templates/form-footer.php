<?php
/**
 * Admin menu's form footer template.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {
	?>

	<div class="atsui-left-footer">
		<button class="button button-large button-primary ats-menu-builder--button ats-menu-builder--submit-button">
			<i class="dashicons dashicons-yes"></i>
			<?php _e( 'Save Changes', 'ats-dashboard' ); ?>
		</button>
	</div>
	<div class="atsui-right-footer">
		<button type="button" class="button button-large button-danger ats-menu-builder--button ats-menu-builder--reset-button ats-menu-builder--reset-all" data-role="all">
			<?php _e( 'Reset All Menus', 'ats-dashboard' ); ?>
		</button>

		<button type="button" class="button button-large button-danger ats-menu-builder--button ats-menu-builder--reset-button ats-menu-builder--reset-role" data-role="administrator">
			<?php _e( 'Reset Administrator Menu', 'ats-dashboard' ); ?>
		</button>
	</div>

	<?php
};
