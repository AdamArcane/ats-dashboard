<?php
/**
 * Postmark / email delivery status field.
 *
 * Read-only: this data comes from a MainWP-managed mu-plugin
 * (wp-content/mu-plugins/mainwp-postmark.php), not from this plugin.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Integrations\Integrations_Output;

return function () {

	$pm = Integrations_Output::get_instance()->get_postmark_status();
	?>

	<?php if ( ! $pm ) : ?>

		<span class="ats-integrations-badge ats-integrations-badge--empty"><?php esc_html_e( 'No Postmark configuration detected', 'ats-dashboard' ); ?></span>
		<p class="description"><?php esc_html_e( 'Email is configured centrally through MainWP. Assign this site to a Postmark server in the MainWP Postmark extension and push the config to enable it.', 'ats-dashboard' ); ?></p>

	<?php elseif ( empty( $pm['has_detail'] ) ) : ?>

		<span class="ats-integrations-badge ats-integrations-badge--override"><?php esc_html_e( 'Managed by MainWP (details pending)', 'ats-dashboard' ); ?></span>
		<p class="description">
			<?php
			echo esc_html(
				sprintf(
					/* translators: %s: human time diff */
					__( "This site's config was pushed before detail reporting was added (mu-plugin last written %s ago). Push the config again from the MainWP Postmark extension to populate this.", 'ats-dashboard' ),
					human_time_diff( (int) $pm['file_mtime'] )
				)
			);
			?>
		</p>

	<?php else : ?>

		<span class="ats-integrations-badge ats-integrations-badge--live"><?php esc_html_e( 'Managed by MainWP', 'ats-dashboard' ); ?></span>

		<table class="ats-integrations-status-table">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Postmark Server', 'ats-dashboard' ); ?></th>
					<td><?php echo esc_html( ! empty( $pm['server_name'] ) ? $pm['server_name'] : ( ! empty( $pm['postmark_server_id'] ) ? '#' . $pm['postmark_server_id'] : '—' ) ); ?></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Message Stream', 'ats-dashboard' ); ?></th>
					<td><?php echo esc_html( ! empty( $pm['message_stream'] ) ? $pm['message_stream'] : 'outbound' ); ?></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Sender', 'ats-dashboard' ); ?></th>
					<td>
						<?php echo esc_html( ! empty( $pm['sender_name'] ) ? $pm['sender_name'] : get_bloginfo( 'name' ) ); ?>
						&lt;<?php echo esc_html( ! empty( $pm['sender_email'] ) ? $pm['sender_email'] : get_option( 'admin_email' ) ); ?>&gt;
					</td>
				</tr>
				<?php if ( ! empty( $pm['pushed_at'] ) ) : ?>
					<tr>
						<th><?php esc_html_e( 'Config Pushed', 'ats-dashboard' ); ?></th>
						<td>
							<?php
							echo esc_html(
								sprintf(
									/* translators: %s: human time diff */
									__( '%s ago', 'ats-dashboard' ),
									human_time_diff( (int) $pm['pushed_at'] )
								)
							);
							?>
						</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>

		<p class="description"><?php esc_html_e( 'Managed centrally via the MainWP Postmark extension. To change the server, message stream, or sender, update the assignment in MainWP and push the config again — this page just reflects what is currently deployed.', 'ats-dashboard' ); ?></p>

	<?php endif; ?>

	<?php

};
