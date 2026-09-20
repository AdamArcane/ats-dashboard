<?php
/**
 * Login styles.
 *
 * @package ATS_Dashboard
 *
 * @subpackage ATS Dashboard Branding
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\LoginCustomizer\Login_Customizer_Base_Module;
use ATSDash\Helpers\Multisite_Helper;

$branding        = get_option( 'ats_branding', array() );
$login           = get_option( 'ats_login', array() );
$blueprint_login = array();

$ms_helper = new Multisite_Helper();

if ( $ms_helper->needs_to_switch_blog() ) {
	global $blueprint;
	$blueprint_login = get_blog_option( $blueprint, 'ats_login', array() );
}

$branding_enabled = isset( $branding['enabled'] ) ? true : false;
$accent_color     = isset( $branding['accent_color'] ) ? $branding['accent_color'] : '';
$has_accent_color = $branding_enabled && ! empty( $accent_color ) ? true : false;

$logo_image  = Login_Customizer_Base_Module::logo_image( $login );
$logo_height = isset( $login['logo_height'] ) ? $login['logo_height'] : 0;

if ( empty( $logo_height ) ) {
	if ( ! empty( $blueprint_login ) && isset( $blueprint_login['logo_height'] ) ) {
		$logo_height = $blueprint_login['logo_height'];
	} else {
		$logo_height = '90%';
	}
}

$fields_border_color_focus = $accent_color;
$button_bg_color           = $accent_color;
$button_bg_color_hover     = $accent_color;
$footer_link_color_hover   = $accent_color;
?>

.login h1 a,
.login .wp-login-logo a {
	background-size: auto <?php echo esc_attr( $logo_height ); ?>;
}

<?php if ( $logo_image ) : ?>
	.login h1 a,
	.login .wp-login-logo a {
		background-image: url(<?php echo esc_url( $logo_image ); ?>);
	}
<?php endif; ?>

<?php if ( $branding_enabled && $fields_border_color_focus ) : ?>
.login input[type=text]:focus,
.login input[type=password]:focus {
	border-color: <?php echo esc_attr( $fields_border_color_focus ); ?>;
}
<?php endif; ?>

<?php if ( $branding_enabled && $footer_link_color_hover ) : ?>
.login #nav a:hover,
.login #nav a:focus,
.login #backtoblog a:hover,
.login #backtoblog a:focus {
	color: <?php echo esc_attr( $footer_link_color_hover ); ?>;
}
<?php endif; ?>

<?php if ( $branding_enabled && $button_bg_color ) : ?>
.wp-core-ui .button.button-primary {
	background-color: <?php echo esc_attr( $button_bg_color ); ?>;
	border-color: <?php echo esc_attr( $button_bg_color ); ?>;
}
<?php endif; ?>

<?php if ( $branding_enabled && $button_bg_color_hover ) : ?>
.wp-core-ui .button.button-primary:hover,
.wp-core-ui .button.button-primary:focus {
	background-color: <?php echo esc_attr( $button_bg_color_hover ); ?>;
	border-color: <?php echo esc_attr( $button_bg_color_hover ); ?>;
}
<?php endif; ?>
