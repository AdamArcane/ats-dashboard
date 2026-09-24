<?php
/**
 * Menu separator template to be rendered via JS.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

ob_start();
?>

<li class="ats-menu-builder--menu-item ats-menu-builder--separator-item" data-hidden="{menu_is_hidden}" data-added="{menu_was_added}" data-default-id="{default_menu_id}" data-default-url="{default_menu_url}">
	<div class="ats-menu-builder--control-panel">
		<div class="ats-menu-builder--menu-drag">
			<span></span>
		</div>
		<div class="ats-menu-builder--menu-icon">
			<i class="dashicons dashicons-minus"></i>
		</div>
		<div class="ats-menu-builder--menu-name">{separator}</div>
		<span class="ats-menu-builder--menu-actions">
			{trash_icon}
			{override_indicator}
			<span class="dashicons dashicons-{hidden_icon} hide-menu"></span>
		</span>
	</div><!-- .ats-menu-builder--control-panel -->
</li>

<?php
return ob_get_clean();
