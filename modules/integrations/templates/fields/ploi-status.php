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
	$fields = $status['fields'];

	$labels = array(
		'server_name' => __( 'Server Name', 'ats-dashboard' ),
		'server_ip'   => __( 'Server IP', 'ats-dashboard' ),
		'php_version' => __( 'PHP Version', 'ats-dashboard' ),
		'domain'      => __( 'Domain', 'ats-dashboard' ),
		'status'      => __( 'Status', 'ats-dashboard' ),
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
			<?php if ( ! empty( $status['pushed']['last_deploy_at'] ) ) : ?>
				<tr>
					<th><?php esc_html_e( 'Last Deploy', 'ats-dashboard' ); ?></th>
					<td><?php echo esc_html( $status['pushed']['last_deploy_at'] ); ?></td>
				</tr>
			<?php endif; ?>
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
		$overrides = isset( $overrides['ploi_status_override'] ) && is_array( $overrides['ploi_status_override'] ) ? $overrides['ploi_status_override'] : array();

		foreach ( $labels as $key => $label ) :
			$value = isset( $overrides[ $key ] ) ? $overrides[ $key ] : '';
			?>
			<div class="ats-integrations-override-row">
				<label for="ats-ploi-override-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
				<input type="text" id="ats-ploi-override-<?php echo esc_attr( $key ); ?>" name="ats_integrations[ploi_status_override][<?php echo esc_attr( $key ); ?>]" class="regular-text" value="<?php echo esc_attr( $value ); ?>" />
			</div>
		<?php endforeach; ?>
		<p class="description"><?php esc_html_e( 'Leave a field blank to use the value pushed by MainWP.', 'ats-dashboard' ); ?></p>
	</div>

	<?php

};
