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
		add_action( 'ats_admin_menu_sidebar', array( self::get_instance(), 'non_blueprint_notice' ) );
		add_action( 'ats_admin_menu_sidebar', array( self::get_instance(), 'super_admin_notice' ) );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_scripts' ) );
		add_action( 'ats_admin_menu_form_footer', array( self::get_instance(), 'form_footer' ) );

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

		$roles = wp_get_current_user()->roles;
		$roles = ! $roles || ! is_array( $roles ) ? array() : $roles;

		if ( ! in_array( $role, $roles, true ) ) {
			$this->user()->simulate_role( $role, true );
		}

		$ajax_handler->load_menu();
		wp_send_json_success( $ajax_handler->format_response( $role ) );

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
