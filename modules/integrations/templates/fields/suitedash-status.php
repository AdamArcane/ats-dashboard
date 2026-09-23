<?php
/**
 * SuiteDash status field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Integrations\Integrations_Output;

return function () {

	$status = Integrations_Output::get_instance()->get_suitedash_status();
	$pushed = $status['pushed'];

	$overrides = get_option( 'ats_integrations', array() );
	$overrides = isset( $overrides['suitedash_status_override'] ) && is_array( $overrides['suitedash_status_override'] ) ? $overrides['suitedash_status_override'] : array();

	$labels = array(
		'company_name'  => __( 'Company Name', 'ats-dashboard' ),
		'company_uuid'  => __( 'Company UUID', 'ats-dashboard' ),
		'contact_name'  => __( 'Contact Name', 'ats-dashboard' ),
		'contact_email' => __( 'Contact Email', 'ats-dashboard' ),
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
						'input_name'     => 'ats_integrations[suitedash_status_override][' . $key . ']',
						'override_value' => isset( $overrides[ $key ] ) ? $overrides[ $key ] : '',
						'placeholder'    => __( 'Leave blank to use the MainWP-pushed value', 'ats-dashboard' ),
						'type'           => 'contact_email' === $key ? 'email' : 'text',
					)
				);
				?>
			<?php endforeach; ?>
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
