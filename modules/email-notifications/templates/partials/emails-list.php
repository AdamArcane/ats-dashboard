<?php
/**
 * The "Emails" tab's grouped table: every editable email, its enabled
 * toggle, and an "Edit" button that opens its popup (see email-modals.php).
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module, $types ) {

	$scopes  = $module->get_email_scopes();
	$grouped = array_fill_keys( array_keys( $scopes ), array() );

	foreach ( $types as $email_key => $email_type ) {
		$scope = isset( $email_type['scope'] ) && isset( $grouped[ $email_type['scope'] ] ) ? $email_type['scope'] : 'user';
		$grouped[ $scope ][ $email_key ] = $email_type;
	}
	?>

	<table class="widefat ats-email-notifications-table">

		<?php foreach ( $grouped as $scope => $scope_types ) : ?>

			<?php if ( empty( $scope_types ) ) continue; ?>

			<thead>
				<tr>
					<th colspan="3"><?php echo esc_html( $scopes[ $scope ] ); ?></th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ( $scope_types as $email_key => $email_type ) : ?>
					<tr>
						<td>
							<strong><?php echo esc_html( $email_type['label'] ); ?></strong>
							<p class="description"><?php echo esc_html( $email_type['description'] ); ?></p>
						</td>
						<td class="ats-email-notifications-status-cell">
							<?php $module->render_email_enabled_toggle( $email_key ); ?>
						</td>
						<td class="ats-email-notifications-actions-cell">
							<button type="button" class="button ats-email-notifications-edit" data-modal-target="ats-email-modal-<?php echo esc_attr( $email_key ); ?>">
								<?php esc_html_e( 'Edit', 'ats-dashboard' ); ?>
							</button>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>

		<?php endforeach; ?>

	</table>

	<?php

};
