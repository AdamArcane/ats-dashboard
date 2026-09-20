<?php
/**
 * Frontend styles.
 *
 * @package ATS_Dashboard
 *
 * @subpackage ATS Dashboard Branding
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

$branding = get_option( 'ats_branding' );

$colors = \ATSDash\Helpers\Branding_Helper::default_colors();

$colors = apply_filters( 'ats_branding_colors', $colors );

$menu_item_color        = $colors['menu_item_color'];
$accent_color           = $colors['accent_color'];
$admin_bar_bg_color     = $colors['admin_bar_bg_color'];
$admin_menu_bg_color    = $colors['admin_menu_bg_color'];
$admin_submenu_bg_color = $colors['admin_submenu_bg_color'];

$admin_bar_logo = isset( $branding['admin_bar_logo_image'] ) ? $branding['admin_bar_logo_image'] : '';
$admin_bar_logo = apply_filters( 'ats_admin_bar_logo_image', $admin_bar_logo );

if ( $admin_bar_logo ) {

	?>

	#wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
		display: none;
	}

	#wpadminbar #wp-admin-bar-wp-logo > .ab-item {
		background-image: url(<?php echo esc_url( $admin_bar_logo ); ?>);
		background-size: 80% auto;
		background-repeat: no-repeat;
		background-position: center center;
	}

	#wpadminbar #wp-admin-bar-wp-logo > .ab-sub-wrapper {
		display: none;
	}

	<?php

}

?>

#wpadminbar {
	color: <?php echo esc_attr( $menu_item_color ); ?>;
	background: <?php echo esc_attr( $admin_bar_bg_color ); ?>;
}

#wpadminbar .ab-item,
#wpadminbar a.ab-item,
#wpadminbar > #wp-toolbar span.ab-label,
#wpadminbar > #wp-toolbar span.noticon {
	color: <?php echo esc_attr( $menu_item_color ); ?>;
}

#wpadminbar .menupop .ab-sub-wrapper,
#wpadminbar .quicklinks .menupop ul.ab-sub-secondary, #wpadminbar .quicklinks .menupop ul.ab-sub-secondary .ab-submenu {
	background: <?php echo esc_attr( $admin_submenu_bg_color ); ?>;
}

#wpadminbar:not(.mobile) .ab-top-menu > li:hover > .ab-item,
#wpadminbar:not(.mobile) .ab-top-menu > li > .ab-item:focus,
#wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus,
#wpadminbar.nojs .ab-top-menu > li.menupop:hover > .ab-item,
#wpadminbar .ab-top-menu > li.menupop.hover > .ab-item {
	color: <?php echo esc_attr( $menu_item_color ); ?>;
	background: <?php echo esc_attr( $admin_submenu_bg_color ); ?>;
}

#wpadminbar:not(.mobile) > #wp-toolbar li:hover span.ab-label,
#wpadminbar:not(.mobile) > #wp-toolbar li.hover span.ab-label,
#wpadminbar:not(.mobile) > #wp-toolbar a:focus span.ab-label {
	color: <?php echo esc_attr( $menu_item_color ); ?>;
}

#wpadminbar .quicklinks .menupop ul li a:hover,
#wpadminbar .quicklinks .menupop ul li a:focus,
#wpadminbar .quicklinks .menupop ul li a:hover strong,
#wpadminbar .quicklinks .menupop ul li a:focus strong,
#wpadminbar .quicklinks .ab-sub-wrapper .menupop.hover > a,
#wpadminbar .quicklinks .menupop.hover ul li a:hover,
#wpadminbar .quicklinks .menupop.hover ul li a:focus,
#wpadminbar.nojs .quicklinks .menupop:hover ul li a:hover,
#wpadminbar.nojs .quicklinks .menupop:hover ul li a:focus,
#wpadminbar li:hover .ab-icon:before,
#wpadminbar li:hover .ab-item:before,
#wpadminbar li a:focus .ab-icon:before,
#wpadminbar li .ab-item:focus:before,
#wpadminbar li .ab-item:focus .ab-icon:before,
#wpadminbar li.hover .ab-icon:before,
#wpadminbar li.hover .ab-item:before,
#wpadminbar li:hover #adminbarsearch:before,
#wpadminbar li #adminbarsearch.adminbar-focused:before {
	color: <?php echo esc_attr( $menu_item_color ); ?>;
}

#wpadminbar .quicklinks li a:hover .blavatar,
#wpadminbar .quicklinks li a:focus .blavatar,
#wpadminbar .quicklinks .ab-sub-wrapper .menupop.hover > a .blavatar,
#wpadminbar .menupop .menupop > .ab-item:hover:before,
#wpadminbar.mobile .quicklinks .ab-icon:before,
#wpadminbar.mobile .quicklinks .ab-item:before {
	color: <?php echo esc_attr( $menu_item_color ); ?>;
}

#wpadminbar .quicklinks li#wp-admin-bar-my-account.with-avatar > a img {
	border-radius: 100%;
	border: none;
	height: 20px;
}
