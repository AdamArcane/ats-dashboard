<?php
/**
 * Admin Bar module.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\AdminBar;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Vars;
use ATSDash\Base\Base_Module;
use ATSDash\Helpers\Screen_Helper;
use ATSDash\Helpers\Multisite_Helper;

/**
 * Class to setup admin menu module.
 */
class Admin_Bar_Base_Module extends Base_Module {

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
	 * Frontend admin bar menu items.
	 *
	 * @var array
	 */
	public $frontend_items = array();

	/**
	 * Frontend admin bar menu in ats expected format.
	 *
	 * @var array
	 */
	public $frontend_menu = array();

	/**
	 * Module constructor.
	 */
	public function __construct() {

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/admin-bar';

	}

	/**
	 * Initialize frontend items.
	 * Called after init hook to ensure translations are available.
	 *
	 * This was created by looking at wp-toolbar-editor plugin's code.
	 * These items can be checked in wp-includes/admin-bar.php file.
	 */
	public function init_frontend_items() {

		if ( ! empty( $this->frontend_items ) ) {
			// Already initialized.
			return;
		}

		$this->frontend_items = array(
			array(
				'parent' => 'top-secondary',
				'id'     => 'search',
				'title'  => '',
				'meta'   => array(
					'class'    => 'admin-bar-search',
					'tabindex' => -1,
				),
			),

			array(
				'parent' => false,
				'after'  => 'site-name',
				'id'     => 'customize',
				'title'  => __( 'Customize', 'ats-dashboard' ),
				'href'   => '',
				'meta'   => array(
					'class' => 'hide-if-no-customize',
				),
			),

			array(
				'parent' => false,
				'after'  => 'new-content',
				'id'     => 'edit',
				'title'  => __( 'Edit', 'ats-dashboard' ) . ' {post_type}',
				// Used only when the current page has nothing more specific to edit
				// (e.g. an archive) - falls back to the general "All Posts" screen
				// so the item is always clickable.
				'href'   => admin_url( 'edit.php' ),
			),

			array(
				'parent' => 'site-name',
				'id'     => 'dashboard',
				'title'  => __( 'Dashboard', 'ats-dashboard' ),
				'href'   => admin_url(),
			),

			array(
				'parent' => 'site-name',
				'after'  => 'dashboard',
				'id'     => 'appearance',
				'title'  => '',
				'href'   => '',
				'group'  => true,
			),

			array(
				'parent' => 'appearance',
				'id'     => 'themes',
				'title'  => __( 'Themes', 'ats-dashboard' ),
				'href'   => admin_url( 'themes.php' ),
			),

			array(
				'parent' => 'appearance',
				'after'  => 'themes',
				'id'     => 'widgets',
				'title'  => __( 'Widgets', 'ats-dashboard' ),
				'href'   => admin_url( 'widgets.php' ),
			),

			array(
				'parent' => 'appearance',
				'after'  => 'widgets',
				'id'     => 'menus',
				'title'  => __( 'Menus', 'ats-dashboard' ),
				'href'   => admin_url( 'nav-menus.php' ),
			),
		);

		$this->frontend_items = apply_filters( 'ats_admin_bar_frontend_items', $this->frontend_items );

		$this->frontend_menu = $this->frontend_items_to_array();

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

		$get_users = new Ajax\Get_Users();
		add_action( 'wp_ajax_ats_admin_bar_get_users', array( $get_users, 'ajax' ) );
		new Ajax\Save_Remove_By_Roles();

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
	 * Add submenu page.
	 */
	public function submenu_page() {

		add_submenu_page( 'ats_settings', __( 'Admin Bar Editor', 'ats-dashboard' ), __( 'Admin Bar Editor', 'ats-dashboard' ), apply_filters( 'ats_settings_capability', 'manage_options' ), 'ats_admin_bar', array( $this, 'submenu_page_content' ) );

	}

	/**
	 * Submenu page content.
	 */
	public function submenu_page_content() {

		$template = require __DIR__ . '/templates/template.php';
		$template( $this );

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
	 * Get existing admin bar menu.
	 */
	public function get_existing_menu() {

		global $wp_admin_bar;

		Vars::set( 'existing_admin_bar_menu', $wp_admin_bar->get_nodes() );

	}

	/**
	 * Turn flat admin bar menu array to a nested format (parent -> submenu).
	 *
	 * @param array $nodes The existing admin bar menu.
	 * @return array Array in expected format.
	 */
	public function nodes_to_array( $nodes ) {
		$ats_array = array();

		foreach ( $nodes as $node_id => $node ) {
			$ats_array[ $node_id ] = array(
				'title'          => '',
				'title_default'  => $node->title,
				'id'             => $node->id,
				'id_default'     => $node->id,
				'parent'         => $node->parent,
				'parent_default' => $node->parent,
				'href'           => '',
				'href_default'   => $node->href,
				'group'          => $node->group,
				'group_default'  => $node->group,
				'meta'           => $node->meta,
				'meta_default'   => $node->meta,
				'was_added'      => 0,
				'is_hidden'      => 0,
				'open_new_tab'   => 0,
				/**
				 * These properties are not being used currently.
				 * But leave it here because in the future, if requested, it would be used for
				 * "hide menu item for specific role(s) & user(s)" functionality (inside dropdowns).
				 */
				// 'disallowed_roles' => array(),
				// 'disallowed_users' => array(),
			);
		}

		return $ats_array;
	}

	/**
	 * Convert frontend items to array in expected format.
	 *
	 * @return array Array in expected format.
	 */
	public function frontend_items_to_array() {
		$this->init_frontend_items(); // Ensure items are initialized.

		$ats_array = array();

		foreach ( $this->frontend_items as $item_data ) {
			$item_id = $item_data['id'];

			$ats_array[ $item_id ] = array(
				'title'          => '',
				'title_default'  => $item_data['title'],
				'id'             => $item_data['id'],
				'id_default'     => $item_data['id'],
				'parent'         => $item_data['parent'],
				'parent_default' => $item_data['parent'],
				'href'           => '',
				'href_default'   => isset( $item_data['href'] ) ? $item_data['href'] : '',
				'group'          => isset( $item_data['group'] ) ? $item_data['group'] : false,
				'group_default'  => isset( $item_data['group'] ) ? $item_data['group'] : false,
				'meta'           => isset( $item_data['meta'] ) ? $item_data['meta'] : array(),
				'meta_default'   => isset( $item_data['meta'] ) ? $item_data['meta'] : array(),
				'was_added'      => 0,
				'is_hidden'      => 0,
				'open_new_tab'   => 0,
				'frontend_only'  => 1,
				/**
				 * These properties are not being used currently.
				 * But leave it here because in the future, if requested, it would be used for
				 * "hide menu item for specific role(s) & user(s)" functionality (inside dropdowns).
				 */
				// 'disallowed_roles' => array(),
				// 'disallowed_users' => array(),
			);

			if ( isset( $item_data['after'] ) ) {
				$ats_array[ $item_id ]['after'] = $item_data['after'];
			}
		}

		return $ats_array;
	}

	/**
	 * Parse saved menu with existing menu.
	 *
	 * @param array $saved_menu The saved menu.
	 * @param array $existing_menu The nested-formatted existing menu.
	 * @param bool  $target This function is being called either for the builder or the output.
	 *              Possible vaule is 'builder' and 'output'.
	 *
	 * @return array The parsed menu.
	 */
	public function parse_menu( $saved_menu, $existing_menu, $target = 'builder' ) {
		$non_ats_items_id = $this->get_non_ats_items_id( $saved_menu );

		$prev_id = '';

		// Get new items from $existing_menu which are not inside $saved_menu.
		foreach ( $existing_menu as $menu_id => $menu ) {
			if ( ! in_array( $menu_id, $non_ats_items_id, true ) ) {
				$new_item = array(
					'id'             => $menu_id,
					'id_default'     => $menu_id,
					'title'          => $menu['title'],
					'title_default'  => $menu['title'],
					'parent'         => $menu['parent'],
					'parent_default' => $menu['parent'],
					'href'           => $menu['href'],
					'href_default'   => $menu['href'],
					'group'          => $menu['group'],
					'group_default'  => $menu['group'],
					'meta'           => $menu['meta'],
					'meta_default'   => $menu['meta'],
					'was_added'      => 0,
					'is_hidden'      => 0,
					'open_new_tab'   => 0,
					/**
					 * These properties are not being used currently.
					 * But leave it here because in the future, if requested, it would be used for
					 * "hide menu item for specific role(s) & user(s)" functionality (inside dropdowns).
					 */
					// 'disallowed_roles' => array(),
					// 'disallowed_users' => array(),
				);

				if ( empty( $prev_id ) ) {
					$saved_menu = array( $menu_id => $new_item ) + $saved_menu;
				} else {
					$pos = array_search( $prev_id, array_keys( $saved_menu ), true );

					$saved_menu = array_slice( $saved_menu, 0, $pos, true ) +
						array( $menu_id => $new_item ) +
						array_slice( $saved_menu, $pos, count( $saved_menu ) - 1, true );
				}
			}

			$prev_id = $menu_id;
		}

		// Exclude non-ats items from $saved_menu which are no longer exist in $existing_menu.
		foreach ( $non_ats_items_id as $menu_id ) {
			if ( 'output' === $target ) {
				if ( ! isset( $existing_menu[ $menu_id ] ) ) {
					unset( $saved_menu[ $menu_id ] );
				}
			} elseif ( ! isset( $existing_menu[ $menu_id ] ) && ! isset( $this->frontend_menu[ $menu_id ] ) ) {
					unset( $saved_menu[ $menu_id ] );
			}
		}

		// Reset some item's property's value (such as title and href) so that it will use the existing item's
		// value, since both are per-request (they point at whichever post is currently being viewed) and must
		// not be frozen to whatever they were when the admin bar customization was last saved. Only do this
		// when WordPress actually added its own "edit" node for the current request (e.g. a singular post) -
		// on pages with nothing to edit (e.g. an archive) core adds no node to pull fresh values from, so the
		// saved item's own generic title/href fallback must be left alone instead of being wiped out.
		if ( 'output' === $target && isset( $existing_menu['edit'] ) ) {
			if ( isset( $saved_menu['edit'] ) ) {
				$saved_menu['edit']['title']         = '';
				$saved_menu['edit']['title_default'] = '';
				$saved_menu['edit']['href']           = '';
				$saved_menu['edit']['href_default']   = '';
			}
		}

		// Bring some defaults from $existing_menu to $saved_menu.
		foreach ( $saved_menu as $menu_id => $menu ) {
			if ( isset( $existing_menu[ $menu_id ] ) ) {
				// Loop over matched $existing_menu item.
				foreach ( $existing_menu[ $menu_id ] as $field_key => $field_value ) {
					if ( ! isset( $menu[ $field_key ] ) ) {
						$saved_menu[ $menu_id ][ $field_key ] = $field_value;
					} elseif ( 'output' === $target ) {
						if ( empty( $menu[ $field_key ] ) && ! empty( $field_value ) ) {
							$saved_menu[ $menu_id ][ $field_key ] = $field_value;
						}
					}
				}
			}
		}

		// Compare saved menu's default values to existing menu's default values.
		foreach ( $saved_menu as $menu_id => $menu ) {
			if ( isset( $existing_menu[ $menu_id ] ) ) {
				// Loop over matched $saved_menu item.
				foreach ( $menu as $field_key => $field_value ) {
					if ( false !== stripos( $field_key, '_default' ) ) {
						if ( isset( $existing_menu[ $menu_id ][ $field_key ] ) && $field_value !== $existing_menu[ $menu_id ][ $field_key ] ) {
							$saved_menu[ $menu_id ][ $field_key ] = $existing_menu[ $menu_id ][ $field_key ];
						}
					}
				}
			}
		}

		/**
		 * The "menu-toggle" has been removed from the admin bar buider.
		 * Now after parsing it, its position is not at the beginning of the array.
		 * Let's bring it back to the correct position (as first item of the array).
		 */
		if ( isset( $saved_menu['menu-toggle'] ) ) {
			unset( $saved_menu['menu-toggle'] );
		}

		if ( isset( $existing_menu['menu-toggle'] ) ) {
			$saved_menu = array( 'menu-toggle' => $existing_menu['menu-toggle'] ) + $saved_menu;
		}

		return $saved_menu;
	}

	/**
	 * Loop over $saved_menu and collect the id of menu items which are not added by ats builder.
	 *
	 * @param array $saved_menu The saved menu.
	 * @return array The non ats menu items.
	 */
	public function get_non_ats_items_id( $saved_menu ) {
		// Id of menu items which are not added by ats.
		$non_ats_items_id = array();

		foreach ( $saved_menu as $menu_id => $menu_array ) {
			if ( ! $menu_array['was_added'] ) {
				array_push( $non_ats_items_id, $menu_id );
			}
		}

		return $non_ats_items_id;
	}

	/**
	 * Loop over $saved_menu and collect the id of menu items
	 * which are not added by ats builder and frontend only.
	 *
	 * @param array $saved_menu The saved menu.
	 * @return array The non ats menu items.
	 */
	public function get_non_ats_items_id_fontend_only( $saved_menu ) {
		// Id of menu items which are not added by ats & frontend only.
		$non_ats_items_id = array();

		foreach ( $saved_menu as $menu_id => $menu_array ) {
			if ( ! $menu_array['was_added'] && isset( $menu_array['frontend_only'] ) && $menu_array['frontend_only'] ) {
				array_push( $non_ats_items_id, $menu_id );
			}
		}

		return $non_ats_items_id;
	}

	/**
	 * Parse frontend items with saved menu.
	 *
	 * @param array $saved_menu The saved menu.
	 * @return array
	 */
	public function parse_frontend_items( $saved_menu ) {
		$this->init_frontend_items(); // Ensure items are initialized.

		$non_ats_items_id = $this->get_non_ats_items_id_fontend_only( $saved_menu );

		$prev_id = '';

		$uninserted_items = array();

		// Get new items from $this->frontend_menu which are not inside $saved_menu.
		foreach ( $this->frontend_menu as $menu_id => $menu ) {
			if ( ! isset( $saved_menu[ $menu_id ] ) && ! in_array( $menu_id, $non_ats_items_id, true ) ) {
				$new_item = $menu;

				if ( isset( $menu['after'] ) && $menu['after'] ) {
					if ( isset( $saved_menu[ $menu['after'] ] ) ) {
						unset( $new_item['after'] );

						$pos = array_search( $menu['after'], array_keys( $saved_menu ), true );
						++$pos;

						$saved_menu = array_slice( $saved_menu, 0, $pos, true ) +
							array( $menu_id => $new_item ) +
							array_slice( $saved_menu, $pos, count( $saved_menu ) - 1, true );
					} else {
						/**
						 * Keep the 'after' key here (unlike the immediate-insert branch
						 * above) — insert_uninserted_items() needs it to resolve the
						 * item's position once its target exists in $saved_menu, and
						 * strips it itself right before actually inserting.
						 */
						$uninserted_items[ $menu_id ] = $new_item;
					}
				} elseif ( empty( $prev_id ) ) {
					unset( $new_item['after'] );
					$saved_menu = array( $menu_id => $new_item ) + $saved_menu;
				} else {
					unset( $new_item['after'] );

					$pos = array_search( $prev_id, array_keys( $saved_menu ), true );

					$saved_menu = array_slice( $saved_menu, 0, $pos, true ) +
						array( $menu_id => $new_item ) +
						array_slice( $saved_menu, $pos, count( $saved_menu ) - 1, true );
				}
			}

			$prev_id = $menu_id;
		}

		$saved_menu = $this->insert_uninserted_items( $saved_menu, $uninserted_items, 10 );

		return $saved_menu;
	}

	/**
	 * Insert un-inserted items to saved menu.
	 *
	 * Each pass re-attempts only the items still left over from the previous
	 * pass (not the full original set), so an item resolved on an earlier
	 * pass is never re-processed. If a full pass inserts nothing, no further
	 * pass can make progress either (nothing in $saved_menu changed), so we
	 * stop immediately rather than burning through the remaining iterations —
	 * this also guarantees termination when an item's 'after' target is
	 * permanently missing (e.g. removed by another plugin), instead of
	 * looping (previously: recursing without a depth limit).
	 *
	 * @param array $saved_menu The saved menu.
	 * @param array $uninserted_items The uninserted items, each still carrying its 'after' key.
	 * @param int   $total_loop Max number of passes.
	 *
	 * @return array
	 */
	public function insert_uninserted_items( $saved_menu, $uninserted_items, $total_loop = 10 ) {

		for ( $i = 0; $i < $total_loop && ! empty( $uninserted_items ); $i++ ) {
			$remaining_items = array();
			$inserted_any    = false;

			// Get new items from $uninserted_items which are not inside $saved_menu.
			foreach ( $uninserted_items as $menu_id => $menu ) {
				if ( isset( $saved_menu[ $menu['after'] ] ) ) {
					$new_item = $menu;
					unset( $new_item['after'] );

					$pos = array_search( $menu['after'], array_keys( $saved_menu ), true );
					++$pos;

					$saved_menu = array_slice( $saved_menu, 0, $pos, true ) +
					array( $menu_id => $new_item ) +
					array_slice( $saved_menu, $pos, count( $saved_menu ) - 1, true );

					$inserted_any = true;
				} else {
					$remaining_items[ $menu_id ] = $menu;
				}
			}

			if ( empty( $remaining_items ) || ! $inserted_any ) {
				break;
			}

			$uninserted_items = $remaining_items;
		}

		return $saved_menu;
	}

	/**
	 * Turn flat admin bar array to a nested format as needed in menu builder.
	 *
	 * @param array $flat_array The flat array format of admin bar menu.
	 * @return array The nested format as needed in menu builder.
	 */
	public function to_builder_format( $flat_array ) {
		if ( ! $flat_array ) {
			return array();
		}

		if ( isset( $flat_array['menu-toggle'] ) ) {
			unset( $flat_array['menu-toggle'] );
		}

		// First, create new site-name item for frontend as "site-name-frontend".
		$site_name_frontend = array(
			'title'          => '',
			'title_default'  => $flat_array['site-name']['title_default'],
			'id'             => 'site-name-frontend',
			'id_default'     => 'site-name-frontend',
			'parent'         => false,
			'parent_default' => false,
			'href'           => '',
			'href_default'   => admin_url(),
			'group'          => false,
			'group_default'  => false,
			'meta'           => array(),
			'meta_default'   => array(),
			'was_added'      => 0,
			'is_hidden'      => 0,
			'open_new_tab'   => 0,
			'frontend_only'  => 1,
			/**
			 * These properties are not being used currently.
			 * But leave it here because in the future, if requested, it would be used for
			 * "hide menu item for specific role(s) & user(s)" functionality (inside dropdowns).
			 */
			// 'disallowed_roles' => array(),
			// 'disallowed_users' => array(),
		);

		$pos = array_search( 'site-name', array_keys( $flat_array ), true );
		++$pos;

		// Then place "site-name-frontend" after "site-name".
		$flat_array = array_slice( $flat_array, 0, $pos, true ) +
			array( 'site-name-frontend' => $site_name_frontend ) +
			array_slice( $flat_array, $pos, count( $flat_array ) - 1, true );

		$nested_array = array();

		/**
		 * Second, collect frontend only items which have "site-name" as the default parent,
		 * change their parent to "site-name-frontend".
		 */
		foreach ( $flat_array as $menu_id => $menu ) {
			if ( isset( $menu['frontend_only'] ) && $menu['frontend_only'] && isset( $menu['parent'] ) && $menu['parent'] && 'site-name' === $menu['parent_default'] ) {
				$flat_array[ $menu_id ]['parent'] = 'site-name-frontend';
			}
		}

		// Third, get the parent menu items.
		foreach ( $flat_array as $menu_id => $menu ) {
			if ( ! isset( $menu['parent'] ) || ! $menu['parent'] || ! isset( $flat_array[ $menu['parent'] ] ) ) {
				$nested_array[ $menu_id ] = $menu;

				$additional = array(
					'title_encoded'         => htmlentities2( $menu['title'] ),
					'title_clean'           => wp_strip_all_tags( $menu['title'] ),
					'title_default_encoded' => htmlentities2( $menu['title_default'] ),
					'title_default_clean'   => wp_strip_all_tags( $menu['title_default'] ),
					'submenu'               => array(),
				);

				$nested_array[ $menu_id ] = array_merge( $nested_array[ $menu_id ], $additional );
			}
		}

		// Fourth, remove collected parent array from $flat_array.
		foreach ( $nested_array as $key => $value ) {
			if ( isset( $flat_array[ $key ] ) ) {
				unset( $flat_array[ $key ] );
			}
		}

		// Fifth, get the 1st level submenu items.
		foreach ( $flat_array as $menu_id => $menu ) {
			if ( isset( $nested_array[ $menu['parent'] ] ) ) {
				$nested_array[ $menu['parent'] ]['submenu'][ $menu['id'] ] = $menu;

				$additional = array(
					'title_encoded'         => htmlentities2( $menu['title'] ),
					'title_clean'           => wp_strip_all_tags( $menu['title'] ),
					'title_default_encoded' => htmlentities2( $menu['title_default'] ),
					'title_default_clean'   => wp_strip_all_tags( $menu['title_default'] ),
					'submenu'               => array(),
				);

				$nested_array[ $menu['parent'] ]['submenu'][ $menu['id'] ] = array_merge(
					$nested_array[ $menu['parent'] ]['submenu'][ $menu['id'] ],
					$additional
				);

				unset( $flat_array[ $menu_id ] );
			}
		}

		// Sixth, get the 2nd level submenu items.
		if ( ! empty( $flat_array ) ) {
			// Loop over flat_array.
			foreach ( $flat_array as $menu_id => $menu ) {
				// Loop over nested_array.
				foreach ( $nested_array as $parent_id => $parent_array ) {
					$submenu_lv2_found = false;

					if ( ! empty( $parent_array['submenu'] ) ) {
						// Loop over parent array's submenu.
						foreach ( $parent_array['submenu'] as $submenu_lv1_id => $submenu_lv1_array ) {
							if ( $menu['parent'] === $submenu_lv1_id ) {
								if ( ! isset( $nested_array[ $parent_id ]['submenu'][ $submenu_lv1_id ]['submenu'] ) ) {
									$nested_array[ $parent_id ]['submenu'][ $submenu_lv1_id ]['submenu'] = array();
								}

								$nested_array[ $parent_id ]['submenu'][ $submenu_lv1_id ]['submenu'][ $menu_id ] = $menu;

								$additional = array(
									'title_encoded'       => htmlentities2( $menu['title'] ),
									'title_clean'         => wp_strip_all_tags( $menu['title'] ),
									'title_default_encoded' => htmlentities2( $menu['title_default'] ),
									'title_default_clean' => wp_strip_all_tags( $menu['title_default'] ),
									'submenu'             => array(),
								);

								$nested_array[ $parent_id ]['submenu'][ $submenu_lv1_id ]['submenu'][ $menu_id ] = array_merge(
									$nested_array[ $parent_id ]['submenu'][ $submenu_lv1_id ]['submenu'][ $menu_id ],
									$additional
								);

								unset( $flat_array[ $menu_id ] );
								$submenu_lv2_found = true;
								break;
							}
						}
					}

					if ( $submenu_lv2_found ) {
						break;
					}
				}
			}
		}

		// Seventh, get the 3rd level submenu items.
		if ( ! empty( $flat_array ) ) {
			// Loop over flat_array.
			foreach ( $flat_array as $menu_id => $menu ) {
				// Loop over nested_array.
				foreach ( $nested_array as $parent_id => $parent_array ) {
					$submenu_lv3_found = false;

					if ( ! empty( $parent_array['submenu'] ) ) {
						// Loop over parent array's submenu.
						foreach ( $parent_array['submenu'] as $submenu_lv1_id => $submenu_lv1_array ) {
							if ( ! empty( $submenu_lv1_array['submenu'] ) ) {
								// Loop over submenu level 1's submenu.
								foreach ( $submenu_lv1_array['submenu'] as $submenu_lv2_id => $submenu_lv2_array ) {
									if ( $menu['parent'] === $submenu_lv2_id ) {
										if ( ! isset( $nested_array[ $parent_id ]['submenu'][ $submenu_lv1_id ]['submenu'][ $submenu_lv2_id ]['submenu'] ) ) {
											$nested_array[ $parent_id ]['submenu'][ $submenu_lv1_id ]['submenu'][ $submenu_lv2_id ]['submenu'] = array();
										}

										$nested_array[ $parent_id ]['submenu'][ $submenu_lv1_id ]['submenu'][ $submenu_lv2_id ]['submenu'][ $menu_id ] = $menu;

										$additional = array(
											'title_encoded' => htmlentities2( $menu['title'] ),
											'title_clean' => wp_strip_all_tags( $menu['title'] ),
											'title_default_encoded' => htmlentities2( $menu['title_default'] ),
											'title_default_clean' => wp_strip_all_tags( $menu['title_default'] ),
											'submenu'     => array(),
										);

										$nested_array[ $parent_id ]['submenu'][ $submenu_lv1_id ]['submenu'][ $submenu_lv2_id ]['submenu'][ $menu_id ] = array_merge(
											$nested_array[ $parent_id ]['submenu'][ $submenu_lv1_id ]['submenu'][ $submenu_lv2_id ]['submenu'][ $menu_id ],
											$additional
										);

										unset( $flat_array[ $menu_id ] );
										$submenu_lv3_found = true;
										break;
									}
								}
							}

							if ( $submenu_lv3_found ) {
								break;
							}
						}
					}

					if ( $submenu_lv3_found ) {
						break;
					}
				}
			}
		}

		return $nested_array;
	}

	/**
	 * Remove by role tab field.
	 */
	public function remove_by_role_field_tab() {

		return require __DIR__ . '/templates/fields/remove-by-role-tab.php';

	}
}
