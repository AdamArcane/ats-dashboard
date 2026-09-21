<?php
/**
 * Logo URL field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings = get_option( 'ats_email_notifications' );
	$logo_url = isset( $settings['global']['logo_url'] ) ? $settings['global']['logo_url'] : '';
	?>

	<div class="ats-email-notifications-logo-field">

		<img src="<?php echo esc_url( $logo_url ); ?>" class="ats-email-notifications-logo-preview" style="<?php echo $logo_url ? '' : 'display:none;'; ?>" alt="" />

		<input type="text" name="ats_email_notifications[global][logo_url]" class="all-options ats-email-notifications-logo-upload" value="<?php echo esc_url( $logo_url ); ?>" placeholder="https://" />

		<button type="button" class="ats-email-notifications-logo-upload-button button-secondary" data-media-library-title="<?php esc_attr_e( 'Select Email Logo', 'ats-dashboard' ); ?>">
			<?php esc_html_e( 'Select From Media Library', 'ats-dashboard' ); ?>
		</button>

		<a href="#" class="ats-email-notifications-logo-clear button-secondary">
			<?php esc_html_e( 'Clear', 'ats-dashboard' ); ?>
		</a>

	</div>

	<p class="description">
		<?php esc_html_e( "Leave blank to use the site's Customizer logo, or the default WordPress logo if none is set.", 'ats-dashboard' ); ?>
	</p>

	<?php

};
