<?php
/**
 * Capability field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$ats_capability = get_site_option( 'ats_multisite_capability' );

	$capabilities = array(
		'manage_network' => 'Super Admin',
		'manage_options' => 'Administrators',
	);

	echo '<select name="ats_multisite_capability">';

	foreach ( $capabilities as $capability => $value ) {
		?>
		<option value="<?php echo esc_attr( $capability ); ?>" <?php selected( $capability, $ats_capability ); ?>><?php echo esc_html( $value ); ?></option>
		<?php
	}

	echo '</select>';

	?>

	<p>
		<?php _e( 'Users with the given User Role (and above) can access the ATS Dashboard settings & create widgets on sites on network.', 'ats-dashboard' ); ?>
	</p>

	<?php

};
