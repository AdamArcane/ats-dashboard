<?php
/**
 * SuiteDash status field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Integrations\Integrations_Output;

return function () {

	$status = Integrations_Output::get_instance()->get_suitedash_status();
	$fields = $status['fields'];

	$labels = array(
		'company_name'  => __( 'Company Name', 'ats-dashboard' ),
		'company_uuid'  => __( 'Company UUID', 'ats-dashboard' ),
		'contact_name'  => __( 'Contact Name', 'ats-dashboard' ),
		'contact_email' => __( 'Contact Email', 'ats-dashboard' ),
	);
	?>

	<?php if ( ! $status['has_data'] ) : ?>
		<span class="ats-integrations-badge ats-integrations-badge--empty"><?php esc_html_e( 'Not pushed by MainWP yet', 'ats-dashboard' ); ?></span>
	<?php endif; ?>

	<table class="ats-integrations-status-table">
		<tbody>
			<?php foreach ( $labels as $key => $label ) : ?>
				<tr>
					<th><?php echo esc_html( $label ); ?></th>
					<td>
						<?php if ( $fields[ $key ]['is_override'] ) : ?>
							<span class="ats-integrations-badge ats-integrations-badge--override"><?php esc_html_e( 'Override', 'ats-dashboard' ); ?></span>
						<?php endif; ?>
						<?php echo esc_html( '' !== $fields[ $key ]['value'] ? $fields[ $key ]['value'] : '—' ); ?>
					</td>
				</tr>
			<?php endforeach; ?>
			<?php if ( ! empty( $status['pushed']['pushed_at'] ) ) : ?>
				<tr>
					<th><?php esc_html_e( 'Last Pushed', 'ats-dashboard' ); ?></th>
					<td>
						<?php
						echo esc_html(
							/* translators: %s: human time diff */
							sprintf( __( '%s ago', 'ats-dashboard' ), human_time_diff( (int) $status['pushed']['pushed_at'] ) )
						);
						?>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<div class="ats-integrations-overrides">
		<h4><?php esc_html_e( 'Manual Overrides', 'ats-dashboard' ); ?></h4>
		<?php
		$overrides = get_option( 'ats_integrations', array() );
		$overrides = isset( $overrides['suitedash_status_override'] ) && is_array( $overrides['suitedash_status_override'] ) ? $overrides['suitedash_status_override'] : array();

		foreach ( $labels as $key => $label ) :
			$value = isset( $overrides[ $key ] ) ? $overrides[ $key ] : '';
			?>
			<div class="ats-integrations-override-row">
				<label for="ats-suitedash-override-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
				<input type="<?php echo 'contact_email' === $key ? 'email' : 'text'; ?>" id="ats-suitedash-override-<?php echo esc_attr( $key ); ?>" name="ats_integrations[suitedash_status_override][<?php echo esc_attr( $key ); ?>]" class="regular-text" value="<?php echo esc_attr( $value ); ?>" />
			</div>
		<?php endforeach; ?>
		<p class="description"><?php esc_html_e( 'Leave a field blank to use the value pushed by MainWP.', 'ats-dashboard' ); ?></p>
	</div>

	<?php

};
