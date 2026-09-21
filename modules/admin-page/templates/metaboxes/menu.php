<?php
/**
 * Menu metabox.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module, $post ) {

	$menu_type   = get_post_meta( $post->ID, 'ats_menu_type', true );
	$menu_parent = get_post_meta( $post->ID, 'ats_menu_parent', true );
	$menu_order  = get_post_meta( $post->ID, 'ats_menu_order', true );
	$menu_order  = $menu_order ? absint( $menu_order ) : 10;

	$admin_menu = $GLOBALS['menu'];

	?>

	<div class="ats-metabox-field">
		<label class="label" for="ats_menu_type"><?php esc_html_e( 'Menu Type', 'ats-dashboard' ); ?></label>
		<select name="ats_menu_type" id="ats_menu_type" class="is-full">
			<option value="parent" <?php selected( $menu_type, 'parent' ); ?>>
				<?php esc_html_e( 'Top-level Menu', 'ats-dashboard' ); ?>
			</option>
			<option value="submenu" <?php selected( $menu_type, 'submenu' ); ?>>
				<?php esc_html_e( 'Submenu', 'ats-dashboard' ); ?>
			</option>
			<option value="none" <?php selected( $menu_type, 'none' ); ?>>
				<?php esc_html_e( 'Not Shown in Menu', 'ats-dashboard' ); ?>
			</option>
		</select>
	</div>

	<div class="ats-metabox-field" data-show-if-field="ats_menu_type" data-show-if-value="submenu">
		<label class="label" for="ats_menu_parent"><?php esc_html_e( 'Parent Menu', 'ats-dashboard' ); ?></label>
		<select name="ats_menu_parent" id="ats_menu_parent" class="is-full">
			<?php foreach ( $admin_menu as $menu ) : ?>
				<?php if ( ! empty( $menu[0] ) ) : ?>
					<option value="<?php echo esc_attr( $menu[2] ); ?>" <?php selected( $menu_parent, $menu[2] ); ?>>
						<?php echo esc_html( $module->content()->strip_tags_content( $menu[0] ) ); ?>
					</option>
				<?php endif; ?>
			<?php endforeach; ?>
		</select>
	</div>

	<div class="ats-metabox-field">
		<label class="label" for="ats_menu_order"><?php esc_html_e( 'Menu Order', 'ats-dashboard' ); ?></label>
		<input type="number" name="ats_menu_order" id="ats_menu_order" class="is-full" value="<?php echo esc_attr( $menu_order ); ?>" min="0" step="1">
	</div>

	<?php require __DIR__ . '/../icon-selector.php'; ?>

	<?php

};
