<?php
/**
 * Admin Menu module.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\AdminBar;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Helpers\Screen_Helper;
use ATSDash\Helpers\Multisite_Helper;

/**
 * Class to setup admin menu module.
 */
class Admin_Bar_Module extends \ATSDash\AdminBar\Admin_Bar_Base_Module {

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

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/admin-bar';

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
		if ( is_admin() ) {
			add_action( 'wp_before_admin_bar_render', array( self::get_instance(), 'get_existing_menu' ), 999999 );
		}
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_styles' ) );
		add_action( 'ats_admin_bar_sidebar', array( self::get_instance(), 'non_blueprint_notice' ) );
		add_action( 'ats_admin_bar_sidebar', array( self::get_instance(), 'super_admin_notice' ) );

		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_scripts' ) );
		add_action( 'ats_admin_bar_form_footer', array( self::get_instance(), 'form_footer' ) );

		add_action( 'ats_admin_bar_add_menu_button', array( self::get_instance(), 'add_menu_button' ) );
		add_action( 'ats_admin_bar_add_submenu_button', array( self::get_instance(), 'add_submenu_button' ) );

		require __DIR__ . '/class-admin-bar-output.php';
		Admin_Bar_Output::init();

		$this->setup_ajax();

	}

	/**
	 * Setup ajax.
	 */
	public function setup_ajax() {

		require_once __DIR__ . '/ajax/class-get-users.php';
		require_once __DIR__ . '/ajax/class-save-remove-by-roles.php';
		require_once __DIR__ . '/ajax/class-reset-menu.php';
		require_once __DIR__ . '/ajax/class-save-menu.php';

		$get_users = new \ATSDash\AdminBar\Ajax\Get_Users();
		add_action( 'wp_ajax_ats_admin_bar_get_users', array( $get_users, 'ajax' ) );
		new \ATSDash\AdminBar\Ajax\Save_Remove_By_Roles();

		add_action( 'wp_ajax_ats_admin_bar_reset_menu', array( Ajax\Reset_Menu::get_instance(), 'reset' ) );
		add_action( 'wp_ajax_ats_admin_bar_save_menu', array( Ajax\Save_Menu::get_instance(), 'save' ) );

	}

	/**
	 * Admin notice to give a warning about admin bar editor usage on non-blueprint site.
	 */
	public function non_blueprint_notice() {

		$ms_helper     = new Multisite_Helper();
		$screen_helper = new Screen_Helper();

		if ( ! $screen_helper->is_admin_bar() || ! $ms_helper->needs_to_switch_blog() ) {
			return;
		}
		?>

		<div class="atsui ats-notice-metabox is-warning">
			<h2><?php _e( 'Non-Blueprint Notice', 'welome-email-editor' ); ?></h2>
			<div class="atsui-content">
				<?php
				$description  = __( '<strong>Caution:</strong> If the Admin Bar Editor is configured on a subsite, the blueprint settings for this feature will no longer be inherited.', 'ats-dashboard' ) . '<br><br>';
				$description .= __( 'To inherit the blueprint configuration again for the Admin Bar Editor, please <strong>reset admin bar editor</strong> (button below).', 'ats-dashboard' );
				?>

				<p><?php echo $description; ?></p>
			</div>
		</div>

		<?php

	}

	/**
	 * Admin notice to give an info about super admin not being affected by admin bar editor changes on multisite.
	 */
	public function super_admin_notice() {

		$ms_helper     = new Multisite_Helper();
		$screen_helper = new Screen_Helper();

		if ( ! $screen_helper->is_admin_bar() || ! $ms_helper->multisite_supported() || ! is_super_admin() ) {
			return;
		}
		?>

		<div class="atsui ats-notice-metabox is-info">
			<h2><?php _e( 'Super Admin Notice', 'welome-email-editor' ); ?></h2>
			<div class="atsui-content">
				<?php
				$description = '<strong>' . __( 'Info:', 'ats-dashboard' ) . '</strong>';
				$description = $description . ' ' . __( 'Changes made to the <strong>Admin Bar</strong> will not affect super admins. Super admins will always see the full admin bar for maximum control.', 'ats-dashboard' );
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

}
