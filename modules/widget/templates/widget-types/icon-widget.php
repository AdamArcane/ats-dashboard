<?php
/**
 * Icon widget.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	global $post;

	?>

	<div data-type="icon">

		<div class="postbox">
			<div class="postbox-header">
				<h2><?php esc_html_e( 'Icon', 'ats-dashboard' ); ?></h2>
			</div>

			<?php

			$stored_meta = get_post_meta( $post->ID, 'ats_icon_key', true );
			$dashicons   = file_get_contents( ATS_DASHBOARD_PLUGIN_DIR . '/assets/json/dashicons.json' );
			$dashicons   = json_decode( $dashicons, true );
			$dashicons   = $dashicons ? $dashicons : array();
			$fontawesome = file_get_contents( ATS_DASHBOARD_PLUGIN_DIR . '/assets/json/fontawesome5.json' );
			$fontawesome = json_decode( $fontawesome, true );
			$fontawesome = $fontawesome ? $fontawesome : array();
			$ats_icons   = array_merge( $dashicons, $fontawesome );

			wp_localize_script(
				'ats-edit-widget',
				'iconPickerIcons',
				$ats_icons
			);

			?>

			<div class="inside">
				<div class="ats-metabox-field">
					<div class="icon-preview"></div>
				</div>
				<div class="ats-metabox-field">
					<label for="ats_icon"><?php esc_html_e( 'Select Icon', 'ats-dashboard' ); ?></label>
					<input type="text" class="icon-picker" data-width="100%" name="ats_icon" id="ats_icon" value="<?php echo esc_attr( $stored_meta ? $stored_meta : 'dashicons dashicons-menu' ); ?>" placeholder="dashicons dashicons-menu" />
				</div>
			</div>
		</div>

		<div class="postbox">
			<div class="postbox-header">
				<h2><?php esc_html_e( 'Tooltip', 'ats-dashboard' ); ?></h2>
			</div>
			<div class="inside">
				<?php $stored_meta = get_post_meta( $post->ID, 'ats_tooltip', true ); ?>
				<textarea style="width: 100%; height: 100px;" id="ats-tooltip" name="ats_tooltip"><?php echo esc_html( $stored_meta ? $stored_meta : '' ); ?></textarea>
			</div>
		</div>

		<div class="postbox">
			<div class="postbox-header">
				<h2><?php esc_html_e( 'Link', 'ats-dashboard' ); ?></h2>
			</div>
			<div class="inside">
				<div class="ats-metabox-field">
					<?php $stored_meta = get_post_meta( $post->ID, 'ats_link', true ); ?>
					<input id="ats_link" type="text" name="ats_link" value="<?php echo esc_attr( $stored_meta ? $stored_meta : '' ); ?>">
					<p class="description"><?php esc_html_e( "Absolute URL's (incl. http:// or https://) or relative URL's (./post-new.php) are allowed.", 'ats-dashboard' ); ?></p>
				</div>
				<?php $stored_meta = get_post_meta( $post->ID, 'ats_link_target', true ); ?>
				<label>
					<input id="ats_link_target" type="checkbox" name="ats_link_target" <?php checked( $stored_meta, '_blank' ); ?>>
					<?php esc_html_e( 'Open link in a new tab.', 'ats-dashboard' ); ?>
				</label>
			</div>
		</div>

	</div>

	<?php

};
