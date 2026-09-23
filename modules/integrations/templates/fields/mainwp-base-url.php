<?php
/**
 * MainWP dashboard base URL field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Integrations\Integrations_Output;

return function () {

	$output    = Integrations_Output::get_instance();
	$auto      = $output->get_mainwp_base_url_detected();
	$overrides = get_option( 'ats_integrations', array() );
	$override  = isset( $overrides['mainwp_base_url'] ) ? $overrides['mainwp_base_url'] : '';

	$render_row = require __DIR__ . '/../../inc/override-field-row.php';
	?>

	<table class="ats-override-table">
		<tbody>
			<?php
			$render_row(
				array(
					'label'          => __( 'Dashboard URL', 'ats-dashboard' ),
					'auto_value'     => $auto,
					'input_name'     => 'ats_integrations[mainwp_base_url]',
					'override_value' => $override,
					'placeholder'    => 'https://siteman.example.com',
					'type'           => 'url',
				)
			);
			$render_row(
				array(
					'note' => __( 'Auto-detected from the MainWP Child plugin\'s own connection record. Only override this if MainWP Child isn\'t active on this site, or the detected value is wrong.', 'ats-dashboard' ),
				)
			);
			?>
		</tbody>
	</table>

	<?php

};
