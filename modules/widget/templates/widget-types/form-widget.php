<?php
/**
 * Form widget.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	global $post;

	?>

	<div data-type="form">

		<div class="postbox">

			<div class="postbox-header">
				<h2><?php _e( 'Text', 'ats-dashboard' ); ?></h2>
			</div>

			<div class="inside">
				<?php $stored_meta = get_post_meta( $post->ID, 'ats_form_notes', true ); ?>
				<textarea style="height: 100px;" name="ats_form_notes"><?php echo esc_html( $stored_meta ? $stored_meta : '' ); ?></textarea>
			</div>

		</div>

		<div class="postbox">

			<div class="postbox-header">
				<h2><?php _e( 'Input Fields', 'ats-dashboard' ); ?></h2>
			</div>

			<div class="inside">

				<div class="ats-metabox-field">

					<?php $stored_meta = get_post_meta( $post->ID, 'ats_form_name', true ); ?>

					<label for="ats_form_name"><?php _e( 'Name', 'ats-dashboard' ); ?></label>
					<input value="<?php echo esc_attr( $stored_meta ? $stored_meta : '' ); ?>" id="ats_form_name" type="text" name="ats_form_name" placeholder="<?php _e( 'Your Name', 'ats-dashboard' ); ?>" class="form-field">

					<label style="margin-top: 5px;">
						<input type="checkbox" checked="checked" tabindex="-1" disabled>
						<?php _e( 'Enable this field', 'ats-dashboard' ); ?>
					</label>

				</div>

				<div class="ats-metabox-field">

					<?php $stored_meta = get_post_meta( $post->ID, 'ats_form_email', true ); ?>

					<label for="ats_form_email"><?php _e( 'Email', 'ats-dashboard' ); ?></label>
					<input value="<?php echo esc_attr( $stored_meta ? $stored_meta : '' ); ?>" id="ats_form_email" type="text" name="ats_form_email" placeholder="<?php _e( 'Your Email', 'ats-dashboard' ); ?>" class="form-field">

					<label style="margin-top: 5px;">
						<input type="checkbox" checked="checked" tabindex="-1" disabled>
						<?php _e( 'Enable this field', 'ats-dashboard' ); ?>
					</label>

				</div>

				<div class="ats-metabox-field">

					<?php $stored_meta = get_post_meta( $post->ID, 'ats_form_subject', true ); ?>

					<label for="ats_form_subject"><?php _e( 'Subject', 'ats-dashboard' ); ?></label>
					<input value="<?php echo esc_attr( $stored_meta ? $stored_meta : '' ); ?>" id="ats_form_subject" type="text" name="ats_form_subject" placeholder="<?php _e( 'Subject', 'ats-dashboard' ); ?>" class="form-field">

					<?php $stored_meta = get_post_meta( $post->ID, 'ats_form_subject_enable', true ); ?>

					<label style="margin-top: 5px;">
						<input id="ats_form_subject_enable" type="checkbox" name="ats_form_subject_enable" <?php checked( $stored_meta, true ); ?>>
						<?php _e( 'Enable this field', 'ats-dashboard' ); ?>
					</label>

				</div>

			</div>

		</div>

		<div class="postbox">

			<div class="postbox-header">
				<h2><?php _e( 'Messages', 'ats-dashboard' ); ?></h2>
			</div>

			<div class="inside">

				<div class="ats-metabox-field">

					<?php $stored_meta = get_post_meta( $post->ID, 'ats_form_success_message', true ); ?>

					<label for="ats_form_success_message"><?php _e( 'Success Message', 'ats-dashboard' ); ?></label>
					<input id="ats_form_success_message" type="text" placeholder="<?php _e( 'Your message has been sent.', 'ats-dashboard' ); ?>" name="ats_form_success_message" value="<?php echo esc_attr( $stored_meta ? $stored_meta : '' ); ?>">

				</div>

				<div class="ats-metabox-field">

					<?php $stored_meta = get_post_meta( $post->ID, 'ats_form_failed_message', true ); ?>

					<label for="ats_form_failed_message"><?php _e( 'Failed Message', 'ats-dashboard' ); ?></label>
					<input id="ats_form_failed_message" type="text" placeholder="<?php _e( 'There was an error trying to send your message. Please try again later.', 'ats-dashboard' ); ?>" name="ats_form_failed_message" value="<?php echo esc_attr( $stored_meta ? $stored_meta : '' ); ?>">

				</div>

			</div>

		</div>

		<div class="postbox">

			<div class="postbox-header">
				<h2><?php _e( 'Autoresponder', 'ats-dashboard' ); ?></h2>
			</div>

			<div class="inside">

				<?php $stored_meta = get_post_meta( $post->ID, 'ats_form_enable_autoresponder', true ); ?>

				<label style="margin-bottom: 15px; display: block;">
					<input id="ats_form_enable_autoresponder" type="checkbox" name="ats_form_enable_autoresponder" <?php checked( $stored_meta, true ); ?>>
					<?php _e( 'Enable Autoresponder', 'ats-dashboard' ); ?>
				</label>

				<div class="ats-metabox-field">

					<?php $stored_meta = get_post_meta( $post->ID, 'ats_form_autoresponder_subject', true ); ?>

					<label for="ats_form_autoresponder_subject"><?php _e( 'Subject', 'ats-dashboard' ); ?></label>
					<input value="<?php echo esc_attr( $stored_meta ? $stored_meta : '' ); ?>" id="ats_form_autoresponder_subject" type="text" name="ats_form_autoresponder_subject" placeholder="<?php _e( 'Your message has been sent', 'ats-dashboard' ); ?>" class="form-field">

				</div>

				<div class="ats-metabox-field">

					<?php $stored_meta = get_post_meta( $post->ID, 'ats_form_autoresponder', true ); ?>

					<label for="ats_form_autoresponder"><?php _e( 'Message', 'ats-dashboard' ); ?></label>
					<textarea style="height: 100px;" name="ats_form_autoresponder" id="ats_form_autoresponder"><?php echo esc_html( $stored_meta ? $stored_meta : '' ); ?></textarea>

				</div>

			</div>

		</div>

		<div class="postbox">

			<div class="postbox-header">
				<h2><?php _e( 'To', 'ats-dashboard' ); ?></h2>
			</div>

			<div class="inside ats-metabox-field">

				<?php $stored_meta = get_post_meta( $post->ID, 'ats_form_enable_custom_to_address', true ); ?>

				<label style="margin-top: 5px;">
					<input id="ats_form_enable_custom_to_address" type="checkbox" name="ats_form_enable_custom_to_address" <?php checked( $stored_meta, true ); ?>>
					<?php _e( 'Custom Recipient', 'ats-dashboard' ); ?>
				</label>

				<?php
				$stored_meta = get_post_meta( $post->ID, 'ats_form_custom_to_address', true );
				$admin_mail  = $stored_meta ? $stored_meta : get_option( 'admin_email' );

				$enabled                  = get_post_meta( $post->ID, 'ats_form_enable_custom_to_address', true );
				$custom_to_address_status = $enabled ? 'display: block;' : 'display: none;';
				?>

				<div id="ats-form-widget-custom-recipient" style="<?php echo $custom_to_address_status; ?>">
					<input value="<?php echo $admin_mail; ?>" id="ats_form_custom_to_address" type="text" name="ats_form_custom_to_address" placeholder="<?php _e( 'Enter your Email address', 'ats-dashboard' ); ?>" class="form-field">
				</div>

				<p class="description"><?php _e( 'By default, all emails are sent to the website administrator', 'ats-dashboard' ); ?> <code style="font-size:14px"> <?php echo get_option( 'admin_email' ); ?> </code></p>

			</div>

		</div>

		<div class="postbox">

			<div class="postbox-header">
				<h2><?php _e( 'Logs', 'ats-dashboard' ); ?></h2>
			</div>

			<div class="inside">

				<div id="clear_log_notice"></div>

				<?php $log_data = get_post_meta( $post->ID, 'ats_contact_form_logs', true ); ?>

				<?php if ( '' !== $log_data ) { ?>

				<div class="ats-metabox-field">
					<?php wp_nonce_field( 'ats_clear_contact_form_' . $post->ID, 'ats_clear_contact_form_nonce' ); ?>
					<div style="max-height: 400px; overflow-y: scroll;">
						<?php echo $log_data; ?>
					</div>
				</div>

				<?php } ?>

				<div class="ats-metabox-field">

					<?php $stored_meta = get_post_meta( $post->ID, 'ats_form_enable_logs', true ); ?>

					<label>
						<input id="ats_form_enable_logs" type="checkbox" name="ats_form_enable_logs" <?php checked( $stored_meta, true ); ?>>
						<?php _e( 'Enable Logs. This will store a copy of all messages being sent from this particular contact form.', 'ats-dashboard' ); ?>
					</label>

				</div>

				<?php if ( ! empty( $log_data ) ) : ?>
					<button id="ats-clear-log" type="button" class="button button-primary button-large" data-post-id="<?php echo esc_attr( $post->ID ); ?>">
						<?php _e( 'Clear Log', 'ats-dashboard' ); ?>
					</button>
				<?php endif; ?>

			</div>

		</div>

	</div>

	<?php
};
