<?php
/**
 * Choose layout field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$branding = get_option( 'ats_branding' );
	$layout   = isset( $branding['layout'] ) ? $branding['layout'] : 'Modern';

	echo '<select name="ats_branding[layout]">';

	?>

	<option value="default" <?php selected( $layout, 'default' ); ?>>Default</option>

	<option value="modern" <?php selected( $layout, 'modern' ); ?>>Modern</option>

	<?php

	echo '</select>';

};
