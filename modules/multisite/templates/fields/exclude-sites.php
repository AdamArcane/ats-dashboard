<?php
/**
 * Exclude sites field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$exclude = get_site_option( 'ats_multisite_exclude' );

	echo '<input type="text" placeholder="3, 14, 291" name="ats_multisite_exclude" value="' . esc_attr( $exclude ) . '" />';

	echo '<p>' . __( 'Comma separated list of subsite ID\'s. Exclude certain websites from the Blueprint.', 'ats-dashboard' ) . '</p>';

};
