<?php
/**
 * Ploi hosting status field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Integrations\Integrations_Output;

return function () {

	$status = Integrations_Output::get_instance()->get_ploi_status();
	$pushed = $status['pushed'];

	$overrides = get_option( 'ats_integrations', array() );
	$overrides = isset( $overrides['ploi_status_override'] ) && is_array( $overrides['ploi_status_override'] ) ? $overrides['ploi_status_override'] : array();

	$labels = array(
		'server_name' => __( 'Server Name', 'ats-dashboard' ),
		'server_ip'   => __( 'Server IP', 'ats-dashboard' ),
		'php_version' => __( 'PHP Version', 'ats-dashboard' ),
		'domain'      => __( 'Domain', 'ats-dashboard' ),
		'status'      => __( 'Status', 'ats-dashboard' ),
	);

	$render_row = require __DIR__ . '/../../inc/override-field-row.php';
	?>

	<?php if ( ! $status['has_data'] ) : ?>
		<span class="ats-integrations-badge ats-integrations-badge--empty"><?php esc_html_e( 'Not pushed by MainWP yet', 'ats-dashboard' ); ?></span>
	<?php endif; ?>

	<table class="ats-override-table">
		<tbody>
			<?php foreach ( $labels as $key => $label ) : ?>
				<?php
				$render_row(
					array(
						'label'          => $label,
						'auto_value'     => isset( $pushed[ $key ] ) ? $pushed[ $key ] : '',
						'input_name'     => 'ats_integrations[ploi_status_override][' . $key . ']',
						'override_value' => isset( $overrides[ $key ] ) ? $overrides[ $key ] : '',
						'placeholder'    => __( 'Leave blank to use the MainWP-pushed value', 'ats-dashboard' ),
					)
				);
				?>
			<?php endforeach; ?>
			<?php if ( ! empty( $pushed['last_deploy_at'] ) ) : ?>
				<?php
				$render_row(
					array(
						'label'      => __( 'Last Deploy', 'ats-dashboard' ),
						'auto_value' => $pushed['last_deploy_at'],
					)
				);
				?>
			<?php endif; ?>
			<?php if ( ! empty( $pushed['pushed_at'] ) ) : ?>
				<?php
				$render_row(
					array(
						'label'      => __( 'Last Pushed', 'ats-dashboard' ),
						/* translators: %s: human time diff */
						'auto_value' => sprintf( __( '%s ago', 'ats-dashboard' ), human_time_diff( (int) $pushed['pushed_at'] ) ),
					)
				);
				?>
			<?php endif; ?>
			<?php
			$render_row(
				array(
					'note' => __( 'Leave a field blank to use the value pushed by MainWP.', 'ats-dashboard' ),
				)
			);
			?>
		</tbody>
	</table>

	<?php

};
