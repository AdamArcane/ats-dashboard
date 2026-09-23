<?php
/**
 * MainWP site ID field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Integrations\Integrations_Output;

return function () {

	$resolved  = Integrations_Output::get_instance()->get_mainwp_site_id();
	$overrides = get_option( 'ats_integrations', array() );
	$override  = isset( $overrides['mainwp_site_id_override'] ) ? $overrides['mainwp_site_id_override'] : '';

	$render_row = require __DIR__ . '/../../inc/override-field-row.php';
	?>

	<table class="ats-override-table">
		<tbody>
			<?php
			$render_row(
				array(
					'label'          => __( 'Site ID', 'ats-dashboard' ),
					'auto_value'     => $resolved['pushed'],
					'input_name'     => 'ats_integrations[mainwp_site_id_override]',
					'override_value' => $override,
					'placeholder'    => __( 'Leave blank to use the MainWP-pushed value', 'ats-dashboard' ),
				)
			);
			$render_row(
				array(
					'note' => __( 'Useful before MainWP has matched this site, or to correct a stale value.', 'ats-dashboard' ),
				)
			);
			?>
		</tbody>
	</table>

	<?php

};
