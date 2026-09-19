<?php
/**
 * Admin bar logo url field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$branding           = get_option( 'ats_branding' );
	$admin_bar_logo_url = isset( $branding['admin_bar_logo_url'] ) ? $branding['admin_bar_logo_url'] : false;

	echo '<input type="url" name="ats_branding[admin_bar_logo_url]" class="regular-text ats-admin-bar-logo-url" value="' . esc_attr( $admin_bar_logo_url ) . '" />';

};
