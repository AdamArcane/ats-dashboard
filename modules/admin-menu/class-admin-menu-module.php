<?php
/**
 * Admin Menu module.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\AdminMenu;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Base\Base_Module;
use ATSDash\Helpers\Screen_Helper;
use ATSDash\Helpers\Multisite_Helper;

/**
 * Class to setup admin menu module.
 */
class Admin_Menu_Module extends Base_Module {

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

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/admin-menu';

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
	 * Setup admin menu module.
	 */
	public function setup() {

		add_action( 'admin_menu', array( self::get_instance(), 'submenu_page' ) );
		add_action( 'ats_ajax_get_admin_menu', array( self::get_instance(), 'get_admin_menu' ), 15, 2 );
		add_action( 'admin_menu', array( $this, 'save_recent_menu' ), 9000 );
		add_action( 'admin_init', array( self::get_instance(), 'maybe_migrate_to_default_layer' ) );
		add_action( 'ats_admin_menu_sidebar', array( self::get_instance(), 'non_blueprint_notice' ) );
		add_action( 'ats_admin_menu_sidebar', array( self::get_instance(), 'super_admin_notice' ) );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_scripts' ) );
		add_action( 'ats_admin_menu_form_footer', array( self::get_instance(), 'form_footer' ) );

		require __DIR__ . '/class-menu-inheritance-helper.php';

		require __DIR__ . '/class-admin-menu-output.php';
		Admin_Menu_Output::init();

		$this->setup_ajax();

	}

	/**
	 * Setup ajax.
	 */
	public function setup_ajax() {

		require_once __DIR__ . '/ajax/class-get-menu.php';
		require_once __DIR__ . '/ajax/class-get-users.php';
		require_once __DIR__ . '/ajax/class-reset-menu.php';
		require_once __DIR__ . '/ajax/class-save-menu.php';

		$get_menu  = new \ATSDash\AdminMenu\Ajax\Get_Menu();
		$get_users = new \ATSDash\AdminMenu\Ajax\Get_Users();

		add_action( 'wp_ajax_ats_admin_menu_get_menu', array( $get_menu, 'ajax' ) );
		add_action( 'wp_ajax_ats_admin_menu_get_users', array( $get_users, 'ajax' ) );
		add_action( 'wp_ajax_ats_admin_menu_reset_menu', array( Ajax\Reset_Menu::get_instance(), 'reset' ) );
		add_action( 'wp_ajax_ats_admin_menu_save_menu', array( Ajax\Save_Menu::get_instance(), 'save' ) );

		add_action( 'ats_admin_menu_add_menu_button', array( self::get_instance(), 'add_menu_button' ) );
		add_action( 'ats_admin_menu_add_submenu_button', array( self::get_instance(), 'add_submenu_button' ) );
		add_action( 'ats_admin_menu_add_separator_button', array( self::get_instance(), 'add_separator_button' ) );

	}

	/**
	 * Admin notice to give a warning about admin menu editor usage on non-blueprint site.
	 */
	public function non_blueprint_notice() {

		$ms_helper     = new Multisite_Helper();
		$screen_helper = new Screen_Helper();

		if ( ! $screen_helper->is_admin_menu() || ! $ms_helper->needs_to_switch_blog() ) {
			return;
		}
		?>

		<div class="atsui ats-notice-metabox is-warning">
			<h2><?php _e( 'Non-Blueprint Notice', 'welome-email-editor' ); ?></h2>
			<div class="atsui-content">
				<?php
				$description  = __( '<strong>Caution:</strong> If the Admin Menu Editor is configured on a subsite, the blueprint settings for this feature will no longer be inherited.', 'ats-dashboard' ) . '<br><br>';
				$description .= __( 'To inherit the blueprint configuration again for the Admin Menu Editor, please <strong>reset all menus</strong> (button below).', 'ats-dashboard' );
				?>

				<p><?php echo $description; ?></p>
			</div>
		</div>

		<?php

	}

	/**
	 * Admin notice to give an info about super admin not being affected by admin menu editor changes on multisite.
	 */
	public function super_admin_notice() {

		$ms_helper     = new Multisite_Helper();
		$screen_helper = new Screen_Helper();

		if ( ! $screen_helper->is_admin_menu() || ! $ms_helper->multisite_supported() || ! is_super_admin() ) {
			return;
		}
		?>

		<div class="atsui ats-notice-metabox is-info">
			<h2><?php _e( 'Super Admin Notice', 'welome-email-editor' ); ?></h2>
			<div class="atsui-content">
				<?php
				$description = '<strong>' . __( 'Info:', 'ats-dashboard' ) . '</strong>';
				$description = $description . ' ' . __( 'Changes made to the <strong>Admin Menu</strong> will not affect super admins. Super admins will always see the full admin bar for maximum control.', 'ats-dashboard' );
				?>

				<p><?php echo $description; ?></p>
			</div>
		</div>

		<?php

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
	 * Add the admin menu editor page.
	 */
	public function submenu_page() {

		add_submenu_page( 'ats_settings', __( 'Admin Menu Editor', 'ats-dashboard' ), __( 'Admin Menu Editor', 'ats-dashboard' ), apply_filters( 'ats_settings_capability', 'manage_options' ), 'ats_admin_menu', array( $this, 'submenu_page_content' ) );

	}

	/**
	 * Render the admin menu editor page.
	 */
	public function submenu_page_content() {

		require __DIR__ . '/templates/template.php';

	}

	/**
	 * Return the current admin menu for a simulated role.
	 *
	 * @param object $ajax_handler The menu AJAX handler.
	 * @param string $role The role to simulate.
	 */
	public function get_admin_menu( $ajax_handler, $role ) {

		/**
		 * "default" is a pseudo-role representing the "Default (Everyone)" menu.
		 * It's not a real WP role, so it can't (and doesn't need to) be simulated -
		 * it's built from the current (admin) user's own menu, same as before this
		 * menu was introduced.
		 */
		if ( 'default' !== $role ) {
			$roles = wp_get_current_user()->roles;
			$roles = ! $roles || ! is_array( $roles ) ? array() : $roles;

			if ( ! in_array( $role, $roles, true ) ) {
				$this->user()->simulate_role( $role, true );
			}
		}

		$ajax_handler->load_menu();
		wp_send_json_success( $ajax_handler->format_response( $role ) );

	}

	/**
	 * Self-healing migration for role/user menus that still hold a full,
	 * independent snapshot from before the "Default (Everyone)" layer existed
	 * (or from an import of such data - see modules/tool/class-tool-module.php).
	 *
	 * A full snapshot keeps overriding every field regardless of what Default
	 * says (see Menu_Inheritance_Helper::apply_delta()), so editing Default
	 * would silently do nothing for that role/user until it's converted into a
	 * delta. This runs on every admin_init, but only ever touches (and rewrites)
	 * keys that still look like a full snapshot - see looks_like_full_snapshot().
	 * Once converted, a key is a small delta and stops matching, so this is a
	 * no-op (and does zero extra writes) once everything has been converted.
	 */
	public function maybe_migrate_to_default_layer() {

		$saved_menu = get_option( 'ats_admin_menu', array() );

		if ( ! is_array( $saved_menu ) || empty( $saved_menu ) ) {
			return;
		}

		$changed = false;

		$baseline_items = ! empty( $saved_menu['default'] ) && is_array( $saved_menu['default'] ) ? $saved_menu['default'] : array();
		$baseline_key   = null;

		// No Default yet - adopt the most complete existing full snapshot as one (preferring "administrator").
		if ( empty( $baseline_items ) ) {
			if ( $this->looks_like_full_snapshot( isset( $saved_menu['administrator'] ) ? $saved_menu['administrator'] : null ) ) {
				$baseline_key   = 'administrator';
				$baseline_items = $saved_menu['administrator'];
			} else {
				foreach ( $saved_menu as $key => $items ) {
					if ( false !== stripos( $key, 'user_id_' ) || ! $this->looks_like_full_snapshot( $items ) ) {
						continue;
					}

					if ( count( $items ) > count( $baseline_items ) ) {
						$baseline_key   = $key;
						$baseline_items = $items;
					}
				}
			}

			if ( empty( $baseline_items ) ) {
				return;
			}

			$saved_menu['default'] = $baseline_items;
			$changed                = true;
		}

		$inheritance = new Menu_Inheritance_Helper();

		// Roles that still look like a full snapshot get converted into a delta against Default.
		foreach ( $saved_menu as $key => $items ) {
			if ( 'default' === $key || $key === $baseline_key || false !== stripos( $key, 'user_id_' ) ) {
				continue;
			}

			if ( ! $this->looks_like_full_snapshot( $items ) ) {
				continue;
			}

			$saved_menu[ $key ] = $inheritance->diff_items( $baseline_items, $items );
			$changed            = true;
		}

		// Users that still look like a full snapshot, diffed against their (possibly just converted) role.
		foreach ( $saved_menu as $key => $items ) {
			if ( false === stripos( $key, 'user_id_' ) || ! $this->looks_like_full_snapshot( $items ) ) {
				continue;
			}

			$user_id   = absint( str_ireplace( 'user_id_', '', $key ) );
			$user_data = get_userdata( $user_id );
			$user_role = $user_data && ! empty( $user_data->roles[0] ) ? $user_data->roles[0] : '';

			$role_delta    = ! empty( $saved_menu[ $user_role ] ) && is_array( $saved_menu[ $user_role ] ) ? $saved_menu[ $user_role ] : array();
			$resolved_role = $inheritance->apply_delta( $baseline_items, $role_delta );

			$saved_menu[ $key ] = $inheritance->diff_items( $resolved_role, $items );
			$changed            = true;
		}

		if ( $changed ) {
			update_option( 'ats_admin_menu', $saved_menu );
		}

	}

	/**
	 * Whether a stored role/user menu still looks like a full, independent
	 * snapshot rather than a proper delta (see Menu_Inheritance_Helper).
	 *
	 * A delta entry never carries a "was_added" key (Menu_Inheritance_Helper::diff_items()
	 * only ever writes identity + genuinely changed fields), while every item the
	 * builder posts as a full snapshot always includes it. This is checked against
	 * most/all items (rather than requiring 100%) since a delta can legitimately
	 * include a couple of newly added custom items, which do carry "was_added".
	 *
	 * @param mixed $items The stored value for a role/user key.
	 *
	 * @return bool
	 */
	private function looks_like_full_snapshot( $items ) {

		if ( ! is_array( $items ) || count( $items ) < 3 ) {
			return false;
		}

		$full_item_count = 0;

		foreach ( $items as $item ) {
			if ( is_array( $item ) && array_key_exists( 'was_added', $item ) ) {
				$full_item_count++;
			}
		}

		return ( $full_item_count / count( $items ) ) >= 0.7;

	}

	/**
	 * Save the latest admin menu for the editor's role simulation.
	 */
	public function save_recent_menu() {

		if ( wp_doing_ajax() || ( isset( $_POST['action'] ) && 'ats_admin_menu_get_menu' === $_POST['action'] ) ) {
			return;
		}

		$current_screen = get_current_screen();
		if ( is_null( $current_screen ) || 'edit-ats_widgets_page_ats_admin_menu' !== $current_screen->id ) {
			return;
		}

		global $menu, $submenu;
		$roles = wp_get_current_user()->roles;
		$role  = reset( $roles );
		$recent_menu = get_option( 'ats_recent_admin_menu', array() );
		$recent_menu[ $role ] = array(
			'menu'    => $menu,
			'submenu' => $submenu,
		);

		update_option( 'ats_recent_admin_menu', $recent_menu, false );

	}

	/**
	 * Add output to admin menu's form footer.
	 */
	public function form_footer() {

		$template = require __DIR__ . '/templates/form-footer.php';
		$template();

	}

	/**
	 * Add new menu button under the menu list.
	 */
	public function add_menu_button() {

		$template = require __DIR__ . '/templates/add-menu-button.php';
		$template();

	}

	/**
	 * Add new submenu button under the submenu list.
	 */
	public function add_submenu_button() {

		$template = require __DIR__ . '/templates/add-submenu-button.php';
		$template();

	}

	/**
	 * Add new separator button under menu / submenu list.
	 */
	public function add_separator_button() {

		$template = require __DIR__ . '/templates/add-separator-button.php';
		$template();

	}

}
