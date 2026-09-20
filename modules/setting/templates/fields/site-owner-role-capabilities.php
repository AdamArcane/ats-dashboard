<?php
/**
 * Site Owner role capabilities field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Setting\Site_Owner_Role;

return function () {

	$granted = Site_Owner_Role::granted_caps();
	?>

	<p class="description">
		<?php esc_html_e( 'Choose which advanced capabilities the Site Owner role is granted. All standard content management capabilities (posts, pages, media, comments) are always granted. Checked = granted, unchecked = denied.', 'ats-dashboard' ); ?>
	</p>

	<?php foreach ( Site_Owner_Role::cap_groups() as $group_label => $caps ) : ?>
		<div class="ats-site-owner-cap-group">
			<h4><?php echo esc_html( $group_label ); ?></h4>
			<?php foreach ( $caps as $cap => $label ) : ?>
				<label class="ats-site-owner-cap-label">
					<input type="checkbox" name="ats_settings[site_owner_caps][]" value="<?php echo esc_attr( $cap ); ?>" <?php checked( in_array( $cap, $granted, true ) ); ?> />
					<?php echo esc_html( $label ); ?>
					<code><?php echo esc_html( $cap ); ?></code>
				</label>
			<?php endforeach; ?>
		</div>
	<?php endforeach; ?>

	<?php

};
