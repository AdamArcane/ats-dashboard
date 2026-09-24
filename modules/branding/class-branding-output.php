<?php
/**
 * Branding output.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Branding;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Base\Base_Output;
use ATSDash\Helpers\Branding_Helper;

/**
 * Class to setup branding output.
 */
class Branding_Output extends Base_Output {

	/**
	 * The class instance.
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * The current module url.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Module constructor.
	 */
	public function __construct() {

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/branding';

	}

	/**
	 * Get instance of the class.
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Init the class setup.
	 */
	public static function init() {

		$class = new self();
		$class->setup();

	}

	/**
	 * Setup branding output.
	 */
	public function setup() {

		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'dashboard_styles' ), 100 );
		add_action( 'wp_enqueue_scripts', array( self::get_instance(), 'frontend_styles' ), 100 );

		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'wp_admin_darkmode_styles' ), 100 );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'block_editor_darkmode_styles' ), 100 );

		add_action( 'admin_head', array( self::get_instance(), 'admin_styles' ), 100 );
		add_action( 'admin_head', array( self::get_instance(), 'admin_styles_preview' ), 120 );

		add_filter( 'ats_branding_dashboard_styles', array( self::get_instance(), 'minify_css' ), 20 );
		add_filter( 'ats_branding_admin_styles', array( self::get_instance(), 'minify_css' ), 20 );
		add_filter( 'ats_branding_frontend_styles', array( self::get_instance(), 'minify_css' ), 20 );
		add_filter( 'ats_branding_login_styles', array( self::get_instance(), 'minify_css' ), 20 );

		add_action( 'admin_bar_menu', array( self::get_instance(), 'replace_admin_bar_logo' ), 11 );
		add_filter( 'ats_admin_bar_logo_url', array( self::get_instance(), 'change_admin_bar_logo_url' ) );
		add_action( 'admin_bar_menu', array( self::get_instance(), 'remove_admin_bar_logo' ), 99 );

		add_action( 'admin_head', array( self::get_instance(), 'replace_block_editor_logo' ), 15 );

		add_action( 'adminmenu', array( self::get_instance(), 'modern_admin_bar_logo' ) );
		add_action( 'adminmenu', array( self::get_instance(), 'modern_admin_bar_logo_preview' ), 30 );

	}

	/**
	 * Enqueue dashboard styles.
	 */
	public function dashboard_styles() {

		$ats_dashboard_styles = $this->get_dashboard_styles();
		wp_add_inline_style( 'ats-dashboard', $ats_dashboard_styles );

	}

	/**
	 * Enqueue darkmode styles for admin area.
	 */
	public function wp_admin_darkmode_styles() {

		$screen_helper = $this->screen();

		// The `$screen_helper->is_block_editor_page()` was a new method added along when adding the dark mode feature.
		if ( ! method_exists( $screen_helper, 'is_block_editor_page' ) ) {
			return;
		}

		// The branding page has its own darkmode style tag.
		if ( $screen_helper->is_branding() ) {
			return;
		}

		$branding = get_option( 'ats_branding', array() );

		$darkmode_enabled = ! empty( $branding['wp_admin_darkmode'] );

		// Enqueue dark mode for admin area.
		if ( $darkmode_enabled && ! $screen_helper->is_block_editor_page() ) {
			wp_enqueue_style( 'ats-wp-admin-darkmode', $this->url . '/assets/css/wp-admin-darkmode.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );
		}

	}

	/**
	 * Enqueue darkmode styles for block editor screen.
	 */
	public function block_editor_darkmode_styles() {

		$screen_helper = $this->screen();

		// The `$screen_helper->is_block_editor_page()` was a new method added along when adding the dark mode feature.
		if ( ! method_exists( $screen_helper, 'is_block_editor_page' ) ) {
			return;
		}

		// The branding page has its own darkmode style tag.
		if ( $screen_helper->is_branding() ) {
			return;
		}

		$branding = get_option( 'ats_branding', array() );

		$darkmode_enabled = ! empty( $branding['block_editor_darkmode'] );

		// Enqueue dark mode for block editor screen.
		if ( $darkmode_enabled && $screen_helper->is_block_editor_page() ) {
			wp_enqueue_style( 'ats-block-editor-darkmode', $this->url . '/assets/css/block-editor-darkmode.css', array(), ATS_DASHBOARD_PLUGIN_VERSION );
		}

	}

	/**
	 * Get dashboard styles.
	 *
	 * @return string The dashboard CSS.
	 */
	public function get_dashboard_styles() {

		$css = '';

		ob_start();
		include_once __DIR__ . '/inc/widget-styles.css.php';
		$css = ob_get_clean();

		return apply_filters( 'ats_branding_dashboard_styles', $css );

	}

	/**
	 * Print admin styles.
	 *
	 * @param bool $inherit_blueprint Whether the admin styles source is inherited from blueprint.
	 */
	public function admin_styles( $inherit_blueprint = false ) {

		$branding         = get_option( 'ats_branding', array() );
		$branding_enabled = isset( $branding['enabled'] );
		$active_layout    = isset( $branding['layout'] ) && 'modern' === $branding['layout'] ? 'modern' : 'default';

		if ( ! $inherit_blueprint && $this->screen()->is_branding() ) {
			return;
		}

		if ( $branding_enabled ) {
			echo '<style class="ats-admin-colors-output ats-' . $active_layout . '-admin-colors-output ' . ( $inherit_blueprint ? 'ats-inherited-from-blueprint' : '' ) . '">' . $this->get_admin_styles( $active_layout ) . '</style>';
		}

	}

	/**
	 * Print admin styles for preview purpose.
	 */
	public function admin_styles_preview() {

		$branding         = get_option( 'ats_branding', array() );
		$branding_enabled = isset( $branding['enabled'] );
		$active_layout    = isset( $branding['layout'] ) && 'modern' === $branding['layout'] ? 'modern' : 'default';
		$darkmode_enabled = ! empty( $branding['wp_admin_darkmode'] );

		if ( ! $this->screen()->is_branding() ) {
			return;
		}

		echo '<style' . ( ! $darkmode_enabled ? ' type="text/ats"' : '' ) . ' class="ats-wp-admin-darkmode-preview ats-darkmode-output ats-wp-admin-darkmode-output">' . $this->get_darkmode_styles( 'wp-admin' ) . '</style>
		';

		echo '<style' . ( ! $branding_enabled || 'default' !== $active_layout ? ' type="text/ats"' : '' ) . ' class="ats-admin-colors-preview ats-admin-colors-output ats-default-admin-colors-output">' . $this->get_admin_styles( 'default' ) . '</style>
		';

		echo '<style' . ( ! $branding_enabled || 'modern' !== $active_layout ? ' type="text/ats"' : '' ) . ' class="ats-admin-colors-preview ats-admin-colors-output ats-modern-admin-colors-output">' . $this->get_admin_styles( 'modern' ) . '</style>';

	}

	/**
	 * Get darkmode styles.
	 *
	 * @param string $target The target to get the styles for. Accepts "wp-admin" or "block-editor".
	 * @return string The darkmode CSS.
	 */
	public function get_darkmode_styles( $target = 'wp-admin' ) {

		ob_start();

		require __DIR__ . '/assets/css/' . $target . '-darkmode.css';

		$css = ob_get_clean();

		return apply_filters( 'ats_branding_darkmode_styles', $css );

	}

	/**
	 * Get admin styles.
	 *
	 * @param string $layout The layout to get the styles for. Accepts "default" or "modern".
	 * @return string The admin CSS.
	 */
	public function get_admin_styles( $layout = 'default' ) {

		ob_start();

		require __DIR__ . '/inc/admin-styles-' . $layout . '.css.php';

		$css = ob_get_clean();

		return apply_filters( 'ats_branding_admin_styles', $css );

	}

	/**
	 * Enqueue frontend styles.
	 */
	public function frontend_styles() {

		$branding_helper = new Branding_Helper();

		if ( ! $branding_helper->is_enabled() ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			return;
		}

		$ats_frontend_styles = $this->get_frontend_styles();
		wp_add_inline_style( 'admin-bar', $ats_frontend_styles );

	}

	/**
	 * Get frontend styles.
	 *
	 * @return string The frontend CSS.
	 */
	public function get_frontend_styles() {

		$css = '';

		ob_start();
		include_once __DIR__ . '/inc/frontend-styles.css.php';
		$css = ob_get_clean();

		return apply_filters( 'ats_branding_frontend_styles', $css );

	}

	/**
	 * Minify CSS
	 *
	 * @param string $css The css.
	 *
	 * @return string the minified CSS.
	 */
	public function minify_css( $css ) {

		// Remove comments.
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );

		// Remove spaces.
		$css = str_replace( ': ', ':', $css );
		$css = str_replace( ' {', '{', $css );
		$css = str_replace( ', ', ',', $css );
		$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );

		return $css;

	}

	/**
	 * Replace admin bar logo.
	 *
	 * We do this to add a filter to the logo URL.
	 *
	 * @param object $wp_admin_bar The wp admin bar.
	 */
	public function replace_admin_bar_logo( $wp_admin_bar ) {

		$wp_admin_bar->remove_menu( 'wp-logo' );

		$args = array(
			'id'    => 'wp-logo',
			'title' => '<span class="ab-icon"></span>',
			'href'  => apply_filters( 'ats_admin_bar_logo_url', network_site_url() ),
			'meta'  => array(
				'class' => 'ats-wp-logo',
			),
		);

		if ( is_admin() && $this->screen()->is_branding() ) {
			$branding  = get_option( 'ats_branding' );
			$classname = 'ats-wp-logo';

			if ( isset( $branding['enabled'] ) ) {
				if ( isset( $branding['remove_admin_bar_logo'] ) ) {
					$classname = 'ats-wp-logo ats-is-hidden';
				} elseif ( 'modern' === $branding['layout'] ) {
						$classname = 'ats-wp-logo ats-is-hidden';
				}
			}

			$args['meta'] = array(
				'class' => $classname,
			);
		}

		$wp_admin_bar->add_menu( $args );

	}

	/**
	 * Change admin bar logo URL.
	 *
	 * Doesn't require separate multisite support!
	 *
	 * @param string $admin_bar_logo_url The admin bar logo URL.
	 *
	 * @return string The updated admin bar logo URL.
	 */
	public function change_admin_bar_logo_url( $admin_bar_logo_url ) {

		$branding = get_option( 'ats_branding' );

		if ( ! isset( $branding['enabled'] ) ) {
			return $admin_bar_logo_url;
		}

		if ( isset( $branding['remove_admin_bar_logo'] ) ) {
			return $admin_bar_logo_url;
		}

		if ( ! empty( $branding['admin_bar_logo_url'] ) ) {
			$admin_bar_logo_url = $branding['admin_bar_logo_url'];
		}

		return $admin_bar_logo_url;

	}

	/**
	 * Remove admin bar logo.
	 *
	 * @param object $wp_admin_bar The wp admin bar.
	 */
	public function remove_admin_bar_logo( $wp_admin_bar ) {

		$branding = get_option( 'ats_branding' );

		if ( ! is_admin() || ! $this->screen()->is_branding() ) {
			if ( isset( $branding['remove_admin_bar_logo'] ) ) {
				$wp_admin_bar->remove_node( 'wp-logo' );
			}
		}

	}

	/**
	 * Replace block editor logo.
	 */
	public function replace_block_editor_logo() {

		$current_screen = get_current_screen();

		if ( ! property_exists( $current_screen, 'is_block_editor' ) || ! $current_screen->is_block_editor ) {
			return;
		}

		$branding = get_option( 'ats_branding', [] );

		if ( ! isset( $branding['enabled'] ) ) {
			return;
		}

		$logo_url = isset( $branding['block_editor_logo_image'] ) && $branding['block_editor_logo_image'] ? $branding['block_editor_logo_image'] : '';

		if ( ! $logo_url ) {
			return;
		}
		?>

		<style type="text/css" class="ats-block-editor-logo-style">
			#editor .edit-post-header .edit-post-fullscreen-mode-close svg {
				display: none;
			}

			<?php
			/**
			 * We can't use "cover" or "contain" as the value for background-size.
			 * If a square logo is uploaded, "cover" or "contain" doesn't work.
			 * The background image seems cut off.
			 *
			 * The ::before dimension is 42px x 43px.
			 * But if we use 42px as the width, the background image seems cut off.
			 * Also we can't just use 100% for the width.
			 *
			 * That's why set set the background-size to "38px auto".
			 */
			?>
			#editor .edit-post-header .edit-post-fullscreen-mode-close::before {
				background-image: url( <?php echo esc_url( $logo_url ); ?> );
				background-repeat: no-repeat;
				background-position: center;	
				background-size: 38px auto;
			}
		</style>

		<?php
	}

	/**
	 * Modern layout: custom admin bar logo.
	 *
	 * @param bool $inherit_blueprint Whether the admin styles source is inherited from blueprint.
	 */
	public function modern_admin_bar_logo( $inherit_blueprint = false ) {

		$branding_helper     = new Branding_Helper();
		$is_branding_enabled = $branding_helper->is_enabled();
		$branding            = get_option( 'ats_branding' );

		// Stop here if branding is not enabled.
		if ( ! $is_branding_enabled ) {
			return;
		}

		// Stop here if modern layout is not selected.
		if ( ! isset( $branding['layout'] ) || 'modern' !== $branding['layout'] ) {
			return;
		}

		// If no logo is selected, use default.
		if ( ! empty( $branding['admin_bar_logo_image'] ) ) {
			$logo = $branding['admin_bar_logo_image'];
		} else {
			$logo = $this->url . '/assets/images/ats-dashboard-logo.png';
		}

		// If no logo url was set, use default.
		if ( ! empty( $branding['admin_bar_logo_url'] ) ) {
			$url = $branding['admin_bar_logo_url'];
		} else {
			$url = network_site_url();
		}

		// Let's add a filter, in case someone wants to dynamically change the logo.
		$logo = apply_filters( 'ats_admin_bar_logo_image', $logo );

		$classname = '';

		if ( isset( $branding['remove_admin_bar_logo'] ) ) {
			$classname = 'ats-is-hidden';
		}

		if ( $inherit_blueprint ) {
			$classname .= ' ats-inherited-from-blueprint';
		}
		?>

		<li class="ats-admin-logo-wrapper ats-admin-logo-wrapper-output <?php echo esc_attr( $classname ); ?>">
			<a href="<?php echo esc_url( $url ); ?>" class="ats-admin-logo-link">
				<img class="ats-admin-logo" src="<?php echo esc_url( $logo ); ?>" />
			</a>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ats-admin-logo-visit-site" target="_blank" rel="noopener noreferrer">
				<span class="dashicons dashicons-external"></span>
				<?php esc_html_e( 'Visit Site', 'ats-dashboard' ); ?>
			</a>
		</li>

		<?php

	}

	/**
	 * Modern layout: custom admin bar logo for preview purpose.
	 */
	public function modern_admin_bar_logo_preview() {

		// Only for branding's settings page.
		if ( ! $this->screen()->is_branding() ) {
			return;
		}

		$branding = get_option( 'ats_branding' );

		// If the saved layout is modern, then we already have the markup.
		if ( isset( $branding['layout'] ) && 'modern' === $branding['layout'] ) {
			return;
		}

		// If no logo is selected, use default.
		if ( ! empty( $branding['admin_bar_logo_image'] ) ) {
			$logo = $branding['admin_bar_logo_image'];
		} else {
			$logo = $this->url . '/assets/images/ats-dashboard-logo.png';
		}

		// If no logo url was set, use default.
		if ( ! empty( $branding['admin_bar_logo_url'] ) ) {
			$url = $branding['admin_bar_logo_url'];
		} else {
			$url = network_site_url();
		}

		// Let's add a filter, in case someone wants to dynamically change the logo.
		$logo = apply_filters( 'ats_admin_bar_logo_image', $logo );
		?>

		<li class="ats-admin-logo-wrapper ats-admin-logo-wrapper-preview ats-is-hidden">
			<a href="<?php echo esc_url( $url ); ?>" class="ats-admin-logo-link">
				<img class="ats-admin-logo" src="<?php echo esc_url( $logo ); ?>" />
			</a>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ats-admin-logo-visit-site" target="_blank" rel="noopener noreferrer">
				<span class="dashicons dashicons-external"></span>
				<?php esc_html_e( 'Visit Site', 'ats-dashboard' ); ?>
			</a>
		</li>

		<?php

	}

	/**
	 * Print color in rgba format from hex color.
	 *
	 * @param string     $hex_color Color in hex format.
	 * @param int|string $opacity The alpha opacity part of an rgba color.
	 */
	public function print_rgba_from_hex( $hex_color, $opacity ) {

		if ( ! class_exists( '\ATSDash\Helpers\Color_Helper' ) ) {
			echo esc_attr( $hex_color );
			return;
		}

		$color_helper = new \ATSDash\Helpers\Color_Helper();

		$rgb = $color_helper->hex_to_rgb( $hex_color );

		$rgba_string = 'rgba(' . $rgb[0] . ', ' . $rgb[1] . ', ' . $rgb[2] . ', ' . $opacity . ')';

		echo esc_attr( $rgba_string );

	}

}
