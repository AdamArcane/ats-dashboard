<?php
/**
 * Icon selector.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

$menu_icon   = get_post_meta( $post->ID, 'ats_menu_icon', true );
$menu_icon   = $menu_icon ? $menu_icon : 'dashicons dashicons-admin-post';
$dashicons   = file_get_contents( ATS_DASHBOARD_CORE_DIR . '/assets/json/dashicons.json' );
$dashicons   = json_decode( $dashicons, true );
$dashicons   = $dashicons ? $dashicons : array();
$fontawesome = file_get_contents( ATS_DASHBOARD_CORE_DIR . '/assets/json/fontawesome5.json' );
$fontawesome = json_decode( $fontawesome, true );
$fontawesome = $fontawesome ? $fontawesome : array();
$ats_icons   = array_merge( $dashicons, $fontawesome );

wp_localize_script(
	'ats-edit-admin-page',
	'iconPickerIcons',
	$ats_icons
);

?>

<div class="ats-metabox-field" data-show-if-field="ats_menu_type" data-show-if-value="parent">
	<label class="label" for="ats_menu_icon"><?php esc_html_e( 'Menu Icon', 'ats-dashboard' ); ?></label>
	<div class="icon-preview"></div>
</div>

<div class="ats-metabox-field" data-show-if-field="ats_menu_type" data-show-if-value="parent">
	<label class="label" for="ats_menu_icon"><?php esc_html_e( 'Select Icon', 'ats-dashboard' ); ?></label>
	<input type="text" class="icon-picker is-full" name="ats_menu_icon" id="ats_menu_icon" value="<?php echo esc_attr( $menu_icon ); ?>" placeholder="dashicons dashicons-admin-generic" />
</div>
