<?php
/**
 * Page builder template.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Helpers\Content_Helper;

/**
 * Inherited variables from anonymous function that is hooked into `admin_notices` hook.
 * That hooking process is done inside `render_dashboard_page` method in class-widget-output.php file.
 *
 * @var int    $template_id The template's post ID.
 * @var string $builder The page builder name.
 * @var bool   $switch_blog Whether or not to switch blog to $blueprint site.
 */

$content_helper = new Content_Helper();

/**
 * Render the welcome panel before the page builder content.
 * This respects ats settings for welcome panel visibility.
 *
 * Note: Multisite blueprint settings are handled by the multisite module
 * (Ms_Widget_Output::remove_dashboard_widgets) which removes the welcome_panel
 * action based on blueprint settings before this template runs.
 */
$ats_settings        = get_option( 'ats_settings' );
$should_show_welcome = true;

// Check if welcome panel should be hidden based on ats settings.
if ( isset( $ats_settings['remove-all'] ) || isset( $ats_settings['welcome_panel'] ) ) {
	$should_show_welcome = false;
}

// Check WordPress core conditions for welcome panel visibility.
if ( $should_show_welcome && has_action( 'welcome_panel' ) && current_user_can( 'edit_theme_options' ) ) {
	$welcome_option = (int) get_user_meta( get_current_user_id(), 'show_welcome_panel', true );
	// 0 = hide, 1 = toggled to show or single site creator, 2 = multisite site owner.
	$hide_welcome = ( 0 === $welcome_option || ( 2 === $welcome_option && wp_get_current_user()->user_email !== get_option( 'admin_email' ) ) );

	if ( ! $hide_welcome ) {
		$welcome_classes = 'welcome-panel';
		?>
		<div id="welcome-panel" class="<?php echo esc_attr( $welcome_classes ); ?>">
			<?php wp_nonce_field( 'welcome-panel-nonce', 'welcomepanelnonce', false ); ?>
			<a class="welcome-panel-close" href="<?php echo esc_url( admin_url( '?welcome=0' ) ); ?>" aria-label="<?php esc_attr_e( 'Dismiss the welcome panel', 'ats-dashboard' ); ?>"><?php esc_html_e( 'Dismiss', 'ats-dashboard' ); ?></a>
			<?php
			/**
			 * Render welcome panel content.
			 * This will use custom content if set, otherwise WordPress default.
			 */
			do_action( 'welcome_panel' );
			?>
		</div>
		<?php
	}
}
?>

<div class="notice ats-page-builder-template">

	<?php
	ob_start();

	$current_site_id = get_current_blog_id();

	if ( $switch_blog ) {
		global $blueprint;

		switch_to_blog( $blueprint );
	}

	$content_helper->output_content_using_builder( $template_id, $builder, $switch_blog, $current_site_id, 'dashboard' );

	$content = ob_get_clean();

	if ( $switch_blog ) {
		restore_current_blog();

		$blueprint_site_url = get_site_url( $blueprint );
		$blueprint_site_url = rtrim( $blueprint_site_url, '/' );

		$current_site_url = get_site_url( $current_site_id );
		$current_site_url = rtrim( $current_site_url, '/' );

		/**
		 * There was problem with Brizy's content.
		 * The image url of background images weren't rendered properly.
		 *
		 * Example case:
		 * Set main site as blueprint.
		 * Then set the dashboard page builder to use Brizy template.
		 * Then acccess the subsite's dashboard.
		 *
		 * Result:
		 * The brizy content will be parsed well.
		 * But if there's some part of the content that is using bg image,
		 * then that bg image wouldn't be rendered correctly.
		 * - Rendered bg image url on blueprint: http://site.local/wp-content/uploads/brizy/19/assets/images/iW=5000&iH=any/wop-7b769e31156e9546af6f5f629082a7bc.jpg
		 * - Rendered bg image url on subsite: http://mapsteps-ms.local/subsite1/?brizy_media=wop-7b769e31156e9546af6f5f629082a7bc.jpg&brizy_crop=iW%3D5000%26iH%3Dany&brizy_post=19
		 *
		 * Solution:
		 * Replacing the subsite's site url with blueprint's site url would fix it.
		 *
		 * However, this solution doesn't fix the issue.
		 * Because it seems like the image url will be rendered inside a generated css file inside upload folder (if I remember it well).
		 *
		 * @todo Do the string replacement to the css content that's being generated instead of the output content.
		 */
		if ( 'brizy' === $builder && false !== stripos( $content, $current_site_url . '/?brizy_media=' ) ) {
			$content = str_ireplace( $current_site_url . '/?brizy_media=', $blueprint_site_url . '/?brizy_media=', $content );
		}
	}

	echo $content;
	?>

</div>
