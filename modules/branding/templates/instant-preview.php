<?php
/**
 * Style tags for instant preview.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

$admin_submenu_bg_color = $colors['admin_submenu_bg_color'];
?>

<style type="text/ats" class="ats-instant-preview ats-style-remove-wp-icon">
	#wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {}
</style>

<style type="text/ats" class="ats-instant-preview ats-style-admin-bar-logo-image-url">
	#wpadminbar #wp-admin-bar-wp-logo > .ab-item {}
</style>

<style type="text/ats" class="ats-instant-preview ats-style-remove-wp-icon-submenu-wrapper">
	#wpadminbar #wp-admin-bar-wp-logo > .ab-sub-wrapper {}
</style>

<style type="text/ats" class="ats-instant-preview" data-ats-prop-admin-submenu-bg-color="background">
	#adminmenu .ats-admin-logo-wrapper a {
		background: <?php echo esc_attr( $admin_submenu_bg_color ); ?>;
	}
</style>
