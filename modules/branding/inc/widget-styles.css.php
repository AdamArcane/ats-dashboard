<?php
/**
 * Widget styles.
 *
 * @package ATS_Dashboard
 *
 * @subpackage ATS Dashboard Branding
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

$settings                = get_option( 'ats_settings' );
$body_text_color         = isset( $settings['body_text_color'] ) ? $settings['body_text_color'] : '#3c434a';
$headline_color          = isset( $settings['headline_color'] ) ? $settings['headline_color'] : '#23282d';
$header_background_color = isset( $settings['header_background_color'] ) ? $settings['header_background_color'] : '#ffffff';
$border_color            = isset( $settings['border_color'] ) ? $settings['border_color'] : '#c3c4c7';
$border_radius           = isset( $settings['border_radius'] ) ? $settings['border_radius'] : '8';
$body_background_color   = isset( $settings['body_background_color'] ) ? $settings['body_background_color'] : '#ffffff';
$link_color              = isset( $settings['link_color'] ) ? $settings['link_color'] : '#2271b1';
$remove_widget_shadow    = isset( $settings['remove_widget_shadow'] );

/* Icon color is now a per-widget field on the Icon Widget's own edit screen
(see templates/widget-types/icon-widget.php), applied via inline style at
render time — it only ever applied to that one widget type anyway. */

if ( $body_text_color && '#3c434a' !== $body_text_color ) { ?>

.ats-content-wrapper,
.ats-html-wrapper,
.ats-rss-wrapper {
	color: <?php echo esc_attr( $body_text_color ); ?>;
}

<?php } ?>

<?php /* These three apply to every dashboard widget, not just custom ones. */ ?>

<?php if ( $headline_color && '#23282d' !== $headline_color ) { ?>

#dashboard-widgets .postbox-header .hndle {
	color: <?php echo esc_attr( $headline_color ); ?>;
}

<?php } ?>

<?php if ( $header_background_color && '#ffffff' !== $header_background_color ) { ?>

#dashboard-widgets .postbox-header {
	background-color: <?php echo esc_attr( $header_background_color ); ?>;
}

<?php } ?>

<?php if ( $border_color && '#c3c4c7' !== $border_color ) { ?>

#dashboard-widgets .postbox {
	border-color: <?php echo esc_attr( $border_color ); ?>;
}

<?php } ?>

<?php /* Clip the header/body to the box's rounded corners — without this, a
custom header background color shows square corners against the rounded box. */ ?>

#dashboard-widgets .postbox {
	overflow: hidden;
}

<?php if ( '' !== $border_radius && 8 !== (int) $border_radius ) { ?>

#dashboard-widgets .postbox {
	border-radius: <?php echo absint( $border_radius ); ?>px;
}

<?php } ?>

<?php if ( $body_background_color && '#ffffff' !== $body_background_color ) { ?>

<?php /* Set on .postbox too, not just .inside — some widgets (e.g. Quick
Draft) have core markup between the header and .inside, and without this
that gap shows the page's own background instead of ours. */ ?>
#dashboard-widgets .postbox,
#dashboard-widgets .postbox .inside {
	background-color: <?php echo esc_attr( $body_background_color ); ?>;
}

<?php } ?>

<?php if ( $remove_widget_shadow ) { ?>

#dashboard-widgets .postbox {
	box-shadow: none;
}

<?php } ?>

<?php /* Link color stays scoped to custom widget content — WordPress core's own widgets style their own links. */ ?>

<?php if ( $link_color && '#2271b1' !== $link_color ) { ?>

.ats-content-wrapper a,
.ats-html-wrapper a,
.ats-rss-wrapper a {
	color: <?php echo esc_attr( $link_color ); ?>;
}

<?php } ?>
