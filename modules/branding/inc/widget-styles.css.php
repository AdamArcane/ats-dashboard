<?php
/**
 * Widget styles.
 *
 * @package ATS_Dashboard
 *
 * @subpackage ATS Dashboard Branding
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

$settings       = get_option( 'ats_settings' );
$icon_color     = isset( $settings['icon_color'] ) ? $settings['icon_color'] : '#555555';
$headline_color = isset( $settings['headline_color'] ) ? $settings['headline_color'] : '#555555';

if ( $icon_color && '#555555' !== $icon_color ) { ?>

[id*='ms-ats'] .fa,
[id*='ms-ats'] .dashicons,
.ats-content-wrapper {
	color: <?php echo esc_attr( $icon_color ); ?>;
}

[id*='ms-ats'] .fa:hover,
[id*='ms-ats'] .dashicons:hover {
	color: <?php echo esc_attr( $icon_color ); ?>;
}

<?php } ?>

<?php if ( $headline_color && '#23282d' !== $headline_color ) { ?>

[id*="ms-ats"] .hndle {
	color: <?php echo esc_attr( $headline_color ); ?>;
}

<?php } ?>
