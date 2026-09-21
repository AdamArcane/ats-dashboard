<?php
/**
 * Notice Bell module.
 *
 * Collects the admin notices WordPress and other plugins would normally
 * print at the top of every wp-admin screen and surfaces them instead as a
 * notification bell in the WordPress toolbar
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\NoticeBell;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Base\Base_Module;

/**
 * Class to setup notice bell module.
 */
class Notice_Bell_Module extends Base_Module {

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
	 * The id of the hidden holder element the collected notices are printed into.
	 *
	 * @var string
	 */
	const HOLDER_ID = 'ats-notice-bell-holder';

	/**
	 * Concatenated markup of the notices collected so far on this page load.
	 *
	 * @var string
	 */
	private $collected_html = '';

	/**
	 * Number of notices collected so far on this page load.
	 *
	 * @var int
	 */
	private $collected_count = 0;

	/**
	 * Whether the hidden holder has already been printed on this page load.
	 *
	 * @var boolean
	 */
	private $holder_rendered = false;

	/**
	 * Module constructor.
	 */
	public function __construct() {

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/notice-bell';

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
	 * Setup notice bell module.
	 */
	public function setup() {

		add_action( 'admin_bar_menu', array( self::get_instance(), 'add_admin_bar_node' ), 5 );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_scripts' ) );
		add_action( 'admin_init', array( self::get_instance(), 'capture_notices' ) );

		/**
		 * Reposition the bell to the left of "Howdy, {user}" as late as
		 * possible — matching the technique the Admin Bar Editor module uses
		 * (modules/admin-bar/class-admin-bar-output.php, hooked at
		 * `wp_before_admin_bar_render` priority 1000000): running here at
		 * `admin_bar_menu` priority 5 wasn't reliable, because any later
		 * `admin_bar_menu` callback (core or another plugin) can still touch
		 * `my-account` afterwards and undo the reorder. Hooking just before
		 * the Admin Bar Editor's own rebuild means this is the default
		 * position, and the Admin Bar Editor (if the user customizes order
		 * there) still has the final say.
		 */
		add_action( 'wp_before_admin_bar_render', array( self::get_instance(), 'move_before_my_account' ), 999998 );

	}

	/**
	 * Whether the notice bell should run on the current request.
	 *
	 * @return boolean
	 */
	public function is_enabled() {

		if ( ! is_admin() || is_network_admin() ) {
			return false;
		}

		if ( ! is_admin_bar_showing() ) {
			return false;
		}

		/**
		 * Filter whether the notice bell is enabled for the current request.
		 *
		 * @param boolean $enabled Whether the notice bell is enabled.
		 */
		return apply_filters( 'ats_notice_bell_enabled', true );

	}

	/**
	 * Add the notification bell + its dropdown panel to the WordPress toolbar.
	 *
	 * The panel starts out empty; it is populated on the client once the
	 * notices collected via capture_notices() reach the browser (they are
	 * printed later in the page than the toolbar itself).
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar The WP_Admin_Bar instance.
	 */
	public function add_admin_bar_node( $wp_admin_bar ) {

		if ( ! $this->is_enabled() ) {
			return;
		}

		$icon = '<svg class="ats-notice-bell-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>';

		$wp_admin_bar->add_node(
			array(
				'id'     => 'ats-notice-bell',
				'parent' => 'top-secondary',
				'title'  => $icon . '<span id="ats-notice-bell-badge" class="ats-notice-bell-badge" hidden>0</span><span class="screen-reader-text">' . esc_html__( 'Notifications', 'ats-dashboard' ) . '</span>',
				'href'   => '#',
				'meta'   => array(
					'class' => 'ats-notice-bell-node',
					'title' => __( 'Notifications', 'ats-dashboard' ),
				),
			)
		);

		$wp_admin_bar->add_node(
			array(
				'parent' => 'ats-notice-bell',
				'id'     => 'ats-notice-bell-panel',
				'title'  => '<div class="ats-notice-bell-panel-header">' . esc_html__( 'Notifications', 'ats-dashboard' ) . '</div><div id="ats-notice-bell-list" class="ats-notice-bell-list"></div>',
				'meta'   => array(
					'class' => 'ats-notice-bell-panel-node',
				),
			)
		);

	}

	/**
	 * Reposition the bell to render to the left of "Howdy, {user}".
	 *
	 * Hooked on `wp_before_admin_bar_render` at priority 999998 (see setup())
	 * — as late as possible, just before the Admin Bar Editor module's own
	 * rebuild at priority 1000000 — because reordering this early, from
	 * within add_admin_bar_node() on `admin_bar_menu`, wasn't reliable: any
	 * later `admin_bar_menu` callback (core or another plugin) can still
	 * touch `my-account` afterwards and undo an early reorder. Removing and
	 * re-adding `my-account` moves it after our own node in WP_Admin_Bar's
	 * internal node order, which is what "further left" on screen means for
	 * the (non-reversed, despite the group itself floating right)
	 * `top-secondary` item order.
	 */
	public function move_before_my_account() {

		global $wp_admin_bar;

		if ( ! $wp_admin_bar instanceof \WP_Admin_Bar ) {
			return;
		}

		$my_account = $wp_admin_bar->get_node( 'my-account' );

		if ( ! $my_account ) {
			return;
		}

		$wp_admin_bar->remove_node( 'my-account' );
		$wp_admin_bar->add_node( $my_account );

	}

	/**
	 * Enqueue admin styles.
	 */
	public function admin_styles() {

		$enqueue = require __DIR__ . '/inc/css-enqueue.php';
		$enqueue( $this );

	}

	/**
	 * Enqueue admin scripts.
	 */
	public function admin_scripts() {

		$enqueue = require __DIR__ . '/inc/js-enqueue.php';
		$enqueue( $this );

	}

	/**
	 * Hook into the admin notices actions so their markup can be relocated
	 * into the notice bell instead of being printed on the page.
	 */
	public function capture_notices() {

		if ( ! $this->is_enabled() ) {
			return;
		}

		$hooks = array( 'admin_notices', 'all_admin_notices', 'user_admin_notices' );

		foreach ( $hooks as $hook ) {
			add_action( $hook, array( $this, 'start_capture' ), -PHP_INT_MAX );
			add_action( $hook, array( $this, 'end_capture' ), PHP_INT_MAX );
		}

	}

	/**
	 * Start buffering a notices hook's output.
	 */
	public function start_capture() {

		ob_start();

	}

	/**
	 * Stop buffering, sort the notices printed by this hook, and (on the last
	 * of the 3 notices hooks) print the holder with everything collected.
	 */
	public function end_capture() {

		$html = ob_get_clean();

		if ( is_string( $html ) && '' !== $html ) {
			echo $this->extract_notices( $html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Markup is WordPress/plugin-generated notice HTML, passed through unmodified.
		}

		// `all_admin_notices` always fires last, so it is the single reliable
		// point at which every notice collected across all 3 hooks has been seen.
		if ( 'all_admin_notices' === current_action() ) {
			$this->render_holder();
		}

	}

	/**
	 * Split a notices hook's rendered output into notices that should stay
	 * on the page (control notices, empty shells, and optionally errors) and
	 * notices that should be collected into the notice bell instead.
	 *
	 * Nested markup (e.g. a notice wrapping another <div>) is handled by
	 * walking forward from each notice's opening tag and counting nested
	 * <div>/</div> pairs until the matching closing tag is found, rather than
	 * relying on a non-greedy regex that would stop at the first inner </div>.
	 *
	 * @param string $html Raw hook output.
	 * @return string Markup that should stay in place on the page.
	 */
	private function extract_notices( $html ) {

		/**
		 * Filter whether error notices are pulled into the bell too. Off by
		 * default: red notices usually mean something is broken and should
		 * stay visible immediately.
		 *
		 * @param boolean $hide_errors Whether to collect error notices.
		 */
		$hide_errors = apply_filters( 'ats_notice_bell_hide_errors', false );

		$opener = '#<div\b[^>]*\bclass=(["\'])[^"\']*\b(?:notice|updated|error|update-nag)\b[^"\']*\1[^>]*>#is';
		$offset = 0;
		$out    = '';

		while ( preg_match( $opener, $html, $m, PREG_OFFSET_CAPTURE, $offset ) ) {
			$start = $m[0][1];

			// Emit everything before this notice untouched.
			$out .= substr( $html, $offset, $start - $offset );

			// Walk forward counting nested <div>/</div> pairs to find the
			// matching closing tag for this notice's opening tag.
			$pos   = $start + strlen( $m[0][0] );
			$depth = 1;

			while ( $depth > 0 && preg_match( '#<(/?)div\b[^>]*>#i', $html, $tag, PREG_OFFSET_CAPTURE, $pos ) ) {
				$pos = $tag[0][1] + strlen( $tag[0][0] );

				if ( '/' === $tag[1][0] ) {
					--$depth;
				} else {
					++$depth;
				}
			}

			$block    = substr( $html, $start, $pos - $start );
			$open_tag = $m[0][0];

			// WordPress core prints a couple of JS-controlled "control"
			// notices via the notices hooks that aren't real messages (the
			// autosave/heartbeat "Connection lost" banner and the
			// local-storage warning); they ship hidden and core reveals them
			// itself when relevant. Anything already carrying the `hidden`
			// class, or a first-party "Settings saved" style confirmation
			// (id="message"), is left in place for the same reason.
			$is_control_notice =
				preg_match( '#\bid=(["\'])(?:lost-connection-notice|local-storage-notice)\1#i', $open_tag )
				|| preg_match( '#\bclass=(["\'])[^"\']*\bhidden\b[^"\']*\1#i', $open_tag )
				|| preg_match( '#\bid=(["\'])message\1#i', $open_tag );

			$is_error_notice = false;

			if ( ! $hide_errors && preg_match( '#\bclass=(["\'])([^"\']*)\1#i', $open_tag, $cm ) ) {
				$classes         = preg_split( '#\s+#', trim( $cm[2] ) );
				$is_error_notice = in_array( 'notice-error', $classes, true ) || in_array( 'error', $classes, true );
			}

			// Empty notice shells (no text, no media/controls/links) have
			// nothing to show and are usually filled in client-side by their
			// own plugin's script, so leave them exactly where they are.
			$is_empty_notice = false;

			if ( ! $is_control_notice && ! $is_error_notice ) {
				$is_empty_notice = '' === trim( wp_strip_all_tags( $block ) )
					&& ! preg_match( '#<(?:img|svg|input|select|textarea|button)\b#i', $block )
					&& ! preg_match( '#<a\b[^>]*\shref=#i', $block );
			}

			if ( $is_control_notice || $is_error_notice || $is_empty_notice ) {
				$out .= $block;
			} else {
				$this->collected_html .= $block;
				++$this->collected_count;
			}

			$offset = $pos;
		}

		$out .= substr( $html, $offset );

		return $out;

	}

	/**
	 * Print the hidden holder containing every notice collected on this page
	 * load. Client-side script relocates its children into the toolbar panel.
	 */
	private function render_holder() {

		if ( $this->holder_rendered || $this->collected_count < 1 || '' === $this->collected_html ) {
			return;
		}

		$this->holder_rendered = true;

		echo '<div id="' . esc_attr( self::HOLDER_ID ) . '" style="display:none" data-count="' . (int) $this->collected_count . '">' . $this->collected_html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Markup is WordPress/plugin-generated notice HTML, passed through unmodified.

	}

}
