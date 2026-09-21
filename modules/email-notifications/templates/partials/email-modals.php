<?php
/**
 * One popup per editable email, each holding that email's Settings API
 * section (subject/heading/body/button). Starts hidden; assets/js/
 * email-notifications.js shows/hides them and lazily initializes the
 * body's TinyMCE editor only while its popup is open.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module, $types ) {

	foreach ( $types as $email_key => $email_type ) :

		$preview_url = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'ats_preview_notification_email',
					'email'  => $email_key,
				),
				admin_url( 'admin-post.php' )
			),
			'ats_preview_notification_email'
		);
		?>

		<div class="ats-email-notifications-modal" id="ats-email-modal-<?php echo esc_attr( $email_key ); ?>">

			<div class="ats-email-notifications-modal-backdrop"></div>

			<div class="ats-email-notifications-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="ats-email-modal-<?php echo esc_attr( $email_key ); ?>-title">

				<div class="ats-email-notifications-modal-header">
					<h2 id="ats-email-modal-<?php echo esc_attr( $email_key ); ?>-title"><?php echo esc_html( $email_type['label'] ); ?></h2>
					<button type="button" class="ats-email-notifications-modal-close" aria-label="<?php esc_attr_e( 'Close', 'ats-dashboard' ); ?>">&times;</button>
				</div>

				<div class="ats-email-notifications-modal-body">
					<?php do_settings_sections( 'ats-email-notifications-' . $email_key . '-settings' ); ?>

					<p>
						<a href="<?php echo esc_url( $preview_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-secondary">
							<?php esc_html_e( 'Preview With Sample Data', 'ats-dashboard' ); ?>
						</a>
					</p>
				</div>

				<div class="ats-email-notifications-modal-footer">
					<button type="button" class="button button-primary ats-email-notifications-modal-close">
						<?php esc_html_e( 'Done', 'ats-dashboard' ); ?>
					</button>
				</div>

			</div>

		</div>

	<?php endforeach;

};
