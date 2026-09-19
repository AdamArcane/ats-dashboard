<?php
/**
 * Widget styles.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

$settings       = get_option( 'ats_settings' );
$icon_color     = isset( $settings['icon_color'] ) ? $settings['icon_color'] : '#555555';
$headline_color = isset( $settings['headline_color'] ) ? $settings['headline_color'] : '#23282d';
?>

<?php if ( $icon_color && '#555555' !== $icon_color ) : ?>

	[id*='ms-ats'] .fa,
	[id*='ms-ats'] .fas,
	[id*='ms-ats'] .fab,
	[id*='ms-ats'] .far,
	[id*='ms-ats'] .dashicons,
	.ats-content-wrapper {
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

<?php if ( $headline_color && '#23282d' !== $headline_color ) : ?>

	[id*="ms-ats"] .hndle {
		color: <?php echo esc_attr( $headline_color ); ?>;
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
