<?php
/**
 * Widget styles.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

$settings                 = get_option( 'ats_settings' );
$icon_color               = isset( $settings['icon_color'] ) ? $settings['icon_color'] : '#555555';
$headline_color           = isset( $settings['headline_color'] ) ? $settings['headline_color'] : '#23282d';
$header_background_color  = isset( $settings['header_background_color'] ) ? $settings['header_background_color'] : '#ffffff';
$border_color             = isset( $settings['border_color'] ) ? $settings['border_color'] : '#c3c4c7';
$border_radius            = isset( $settings['border_radius'] ) ? $settings['border_radius'] : '8';
$body_background_color    = isset( $settings['body_background_color'] ) ? $settings['body_background_color'] : '#ffffff';
$link_color               = isset( $settings['link_color'] ) ? $settings['link_color'] : '#2271b1';
$remove_widget_shadow     = isset( $settings['remove_widget_shadow'] );
?>

<?php if ( $icon_color && '#555555' !== $icon_color ) : ?>

	[id*='ms-ats'] .fa,
	[id*='ms-ats'] .fas,
	[id*='ms-ats'] .fab,
	[id*='ms-ats'] .far,
	[id*='ms-ats'] .dashicons,
	.ats-content-wrapper,
	.ats-html-wrapper {
		color: <?php echo esc_attr( $icon_color ); ?>;
	}

	[id*='ms-ats'] .fa:hover,
	[id*='ms-ats'] .fas:hover,
	[id*='ms-ats'] .fab:hover,
	[id*='ms-ats'] .far:hover,
	[id*='ms-ats'] .dashicons:hover {
		color: <?php echo esc_attr( $icon_color ); ?>;
	}

<?php endif; ?>

<?php /* These three apply to every dashboard widget, not just custom ones. */ ?>

<?php if ( $headline_color && '#23282d' !== $headline_color ) : ?>

	#dashboard-widgets .postbox-header .hndle {
		color: <?php echo esc_attr( $headline_color ); ?>;
	}

<?php endif; ?>

<?php if ( $header_background_color && '#ffffff' !== $header_background_color ) : ?>

	#dashboard-widgets .postbox-header {
		background-color: <?php echo esc_attr( $header_background_color ); ?>;
	}

<?php endif; ?>

<?php if ( $border_color && '#c3c4c7' !== $border_color ) : ?>

	#dashboard-widgets .postbox {
		border-color: <?php echo esc_attr( $border_color ); ?>;
	}

<?php endif; ?>

<?php /* Clip the header/body to the box's rounded corners — without this, a
custom header background color shows square corners against the rounded box. */ ?>

	#dashboard-widgets .postbox {
		overflow: hidden;
	}

<?php if ( '' !== $border_radius && 8 !== (int) $border_radius ) : ?>

	#dashboard-widgets .postbox {
		border-radius: <?php echo absint( $border_radius ); ?>px;
	}

<?php endif; ?>

<?php if ( $body_background_color && '#ffffff' !== $body_background_color ) : ?>

	<?php /* Set on .postbox too, not just .inside — some widgets (e.g. Quick
	Draft) have core markup between the header and .inside, and without this
	that gap shows the page's own background instead of ours. */ ?>
	#dashboard-widgets .postbox,
	#dashboard-widgets .postbox .inside {
		background-color: <?php echo esc_attr( $body_background_color ); ?>;
	}

<?php endif; ?>

<?php if ( $remove_widget_shadow ) : ?>

	#dashboard-widgets .postbox {
		box-shadow: none;
	}

<?php endif; ?>

<?php /* Link color stays scoped to custom widget content — WordPress core's own widgets style their own links. */ ?>

<?php if ( $link_color && '#2271b1' !== $link_color ) : ?>

	.ats-content-wrapper a,
	.ats-html-wrapper a {
		color: <?php echo esc_attr( $link_color ); ?>;
	}

<?php endif; ?>

/*
 * List styling for widgets.
 * Ensures bullets (disc) and numbers (decimal) are visible by adding padding,
 * which is often removed by default WordPress dashboard styles.
 */
.ats-content-wrapper ul,
.ats-html-wrapper ul {
	list-style-type: disc;
	padding-left: 20px;
	margin: 1em 0;
}

.ats-content-wrapper ol,
.ats-html-wrapper ol {
	list-style-type: decimal;
	padding-left: 20px;
	margin: 1em 0;
}

/* 
 * Support for nested lists.
 */
.ats-content-wrapper ul ul,
.ats-content-wrapper ol ul,
.ats-html-wrapper ul ul,
.ats-html-wrapper ol ul {
	list-style-type: circle;
}

/* 
 * Fix for centered lists.
 * Prevents list indicators (bullets/numbers) from being misaligned when 
 * the parent container uses text-align: center. 
 */
[style*="text-align: center"] ul,
[style*="text-align: center"] ol,
[style*="text-align:center"] ul,
[style*="text-align:center"] ol {
	display: inline-block;
	text-align: left;
}

/* 
 * Basic form styling for HTML widgets.
 * Ensures labels and inputs are clearly separated and responsive.
 */
.ats-html-wrapper form {
	margin: 10px 0;
}

.ats-html-wrapper label {
	display: inline-block;
	margin-bottom: 5px;
	font-weight: 600;
}

.ats-html-wrapper input[type="text"],
.ats-html-wrapper input[type="email"],
.ats-html-wrapper textarea {
	width: 100%;
	box-sizing: border-box;
}
