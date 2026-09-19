<?php
/**
 * Display options metabox.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $post ) {

	$remove_page_title    = (int) get_post_meta( $post->ID, 'ats_remove_page_title', true );
	$remove_page_margin   = (int) get_post_meta( $post->ID, 'ats_remove_page_margin', true );
	$remove_admin_notices = (int) get_post_meta( $post->ID, 'ats_remove_admin_notices', true );

	?>

	<div class="ats-metabox-field">
		<label for="ats_remove_page_title" class="label checkbox-label">
			<input type="checkbox" name="ats_remove_page_title" id="ats_remove_page_title" value="1" <?php checked( $remove_page_title, 1 ); ?>>
			<strong><?php esc_html_e( 'Remove Page Title', 'ats-dashboard' ); ?></strong>
		</label>
		<p class="description">
			<?php esc_html_e( 'Remove the page title from the Custom Admin Page.', 'ats-dashboard' ); ?>
		</p>
	</div>

	<div class="ats-metabox-field">
		<label for="ats_remove_page_margin" class="label checkbox-label">
			<input type="checkbox" name="ats_remove_page_margin" id="ats_remove_page_margin" value="1" <?php checked( $remove_page_margin, 1 ); ?>>
			<strong><?php esc_html_e( 'Remove Page Margin', 'ats-dashboard' ); ?></strong>
		</label>
		<p class="description">
			<?php esc_html_e( 'Remove the default margins from the Custom Admin Page.', 'ats-dashboard' ); ?>
		</p>
	</div>

	<div class="ats-metabox-field">
		<label for="ats_remove_admin_notices" class="label checkbox-label">
			<input type="checkbox" name="ats_remove_admin_notices" id="ats_remove_admin_notices" value="1" <?php checked( $remove_admin_notices, 1 ); ?>>
			<strong><?php esc_html_e( 'Remove Admin Notices', 'ats-dashboard' ); ?></strong>
		</label>
		<p class="description">
			<?php esc_html_e( 'Remove the admin notices (if any) from the Custom Admin Page.', 'ats-dashboard' ); ?>
		</p>
	</div>

	<?php

};
