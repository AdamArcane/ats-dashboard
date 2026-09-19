<?php
/**
 * User tab content template to be rendered via JS.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

ob_start();
?>

<div id="ats-menu-builder--user-{user_id}-edit-area" class="ats-menu-builder--tab-content-item ats-menu-builder--workspace ats-menu-builder--user-workspace is-active" data-user-id="{user_id}">
	<ul class="ats-menu-builder--menu-list">
		<!-- to be re-written via js -->
		<li class="loading"></li>
	</ul>

	<div class="ats-menu-builder--inline-buttons">
		<?php
		do_action( 'ats_admin_menu_add_menu_button' );
		do_action( 'ats_admin_menu_add_separator_button' );
		?>
	</div>
</div>

<?php
return ob_get_clean();
