<?php
/**
 * User tab menu template to be rendered via JS.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

ob_start();
?>

<li class="ats-menu-builder--tab-menu-item is-active" data-ats-tab-content="ats-menu-builder--user-{user_id}-edit-area" data-user-id="{user_id}">
	<button type="button">
		{display_name}
	</button>
	<i class="dashicons dashicons-no-alt delete-icon ats-menu-builder--remove-tab"></i>
</li>

<?php
return ob_get_clean();
