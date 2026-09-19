<?php
/**
 * Welcome panel field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Widget\Widget_Base_Output;

return function () {

	$widget_output    = Widget_Base_Output::get_instance();
	$placeholder_tags = $widget_output->placeholder_tags;
	$placeholder_tags = apply_filters( 'ats_widgets_placeholder_tags', $placeholder_tags );
	$total_tags       = count( $placeholder_tags );
	?>

	<p><?php esc_html_e( 'Use the placeholder tags below to display certain information dynamically.', 'ats-dashboard' ); ?></p>

	<p>
		<?php
		foreach ( $placeholder_tags as $tag_index => $placeholder_tag ) {
			?>
			<code><?php echo esc_attr( $placeholder_tag ); ?></code><?php echo ( $total_tags - 1 === $tag_index ? '' : ',' ); ?>
			<?php
		}
		?>
	</p>

	<?php
	$settings   = get_option( 'ats_settings' );
	$editor_id  = 'ats_settings--welcome_panel_content';
	$content    = isset( $settings['welcome_panel_content'] ) ? $settings['welcome_panel_content'] : '';
	$is_default = false;

	if ( empty( $content ) ) {
		do_action( 'ats_ms_switch_blog' );

		ob_start();
		do_action( 'welcome_panel' );
		$content = ob_get_clean();

		do_action( 'ats_ms_restore_blog' );

		$is_default = true;
	}

	$content = trim( $content );

	/**
	 * Based on Keypress UI code.
	 *
	 * @see wp-content/plugins/keypress-ui/includes/modules/dashboard/class-kpui-widgets.php
	 */
	global $compress_css;

	$wp_styles = wp_styles();
	$dir       = $wp_styles->text_direction === 'ltr' ? '' : '-rtl';
	$ver       = $wp_styles->default_version;
	$min       = ! defined( 'SCRIPT_DEBUG' ) && $compress_css ? '.min' : '';

	$args = array(
		'textarea_name' => 'ats_settings[welcome_panel_content]',
		'media_buttons' => false,
		'editor_height' => 300,
		'tinymce'       => array(
			'body_class'    => 'wp-core-ui welcome-panel',
			'content_style' => '.welcome-panel-content .hide-if-customize {display: none;} .welcome-panel {border: none;}',
			'content_css'   => "/wp-includes/css/dashicons{$dir}{$min}.css?ver={$ver},/wp-includes/css/buttons{$dir}{$min}.css?ver={$ver},/wp-admin/css/common{$dir}{$min}.css?ver={$ver},/wp-admin/css/dashboard{$dir}{$min}.css?ver={$ver}",
		),
	);

	if ( $is_default ) {
		$args['tinymce']['setup'] = 'function(editor){editor.on("input keyup paste change",function(){var f=document.getElementById("ats-welcome-panel-is-default");if(f){f.value="0";}});}';
	}

	wp_editor( $content, $editor_id, $args );

	if ( $is_default ) {
		echo '<input type="hidden" id="ats-welcome-panel-is-default" name="ats_settings[welcome_panel_is_default]" value="1">';
	}

};
