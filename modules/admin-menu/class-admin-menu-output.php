<?php
/**
 * Admin menu output.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\AdminMenu;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Base\Base_Output;
use ATSDash\Helpers\Array_Helper;
use ATSDash\Helpers\Multisite_Helper;
use ATSDash\Helpers\Placeholder_Helper;
use WP_User;

/**
 * Class to setup admin menu output.
 */
class Admin_Menu_Output extends Base_Output {

	/**
	 * The class instance.
	 *
	 * @var Admin_Menu_Output
	 */
	public static $instance;

	/**
	 * The current module url.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * The placeholder helper.
	 *
	 * @var Placeholder_Helper
	 */
	public $placeholder_helper;

	/**
	 * Module constructor.
	 */
	public function __construct() {

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/admin-menu';

		/**
		 * This helper needs to be initialized here
		 * to prevent the condition where blog already switched to the blueprint site.
		 * Because in the placeholder class, we get the site_url & site_name.
		 */
		$this->placeholder_helper = new Placeholder_Helper();

	}

	/**
	 * Get instance of the class.
	 *
	 * @return Admin_Menu_Output
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
	 * Setup Admin Menu output.
	 */
	public function setup() {

		// Using 9999 as the prio here should be enough.
		add_action( 'admin_menu', array( self::get_instance(), 'menu_output' ), 9999 );

		add_action( 'ats_ajax_before_get_admin_menu', array( self::get_instance(), 'remove_output_actions' ) );

		// Patch for Dashboard menu item with SVG icon.
		$scripts = file_get_contents( __DIR__ . '/assets/js/admin-menu-output.js' );

		add_action(
			'admin_footer',
			function () use ( $scripts ) {
				echo '<script>' . $scripts . '</script>';
			}
		);

	}

	/**
	 * Remove output from the ajax process of getting admin menu.
	 * See modules/admin-menu/ajax/class-get-menu.php in the free version.
	 */
	public function remove_output_actions() {

		// We need to remove admin menu output to get the original $menu & $submenu.
		remove_action( 'admin_menu', array( self::get_instance(), 'menu_output' ), 9999 );

	}

	/**
	 * Preparing the admin menu output.
	 *
	 * @param string[] $roles User roles before switching blog.
	 */
	public function menu_output( $roles = array() ) {

		$ms_helper = new Multisite_Helper();

		$saved_menu = get_option( 'ats_admin_menu', array() );
		$saved_menu = is_array( $saved_menu ) ? $saved_menu : array();
		$user       = wp_get_current_user();

		if ( $ms_helper->multisite_supported() && is_super_admin() ) {
			/**
			 * Stop if:
			 * - multisite is supported
			 * - AND current user is a super admin
			 * - AND they don't have custom menu explicitly set in users tab.
			 */
			if ( ! isset( $saved_menu[ 'user_id_' . $user->ID ] ) ) {
				return;
			}
		}

		// Stop if $roles is empty but needs to switch blog.
		if ( ! $roles && $ms_helper->needs_to_switch_blog() ) {
			return;
		}

		global $menu, $submenu;

		if ( ! $roles ) {
			$roles = $user->roles;

			if ( empty( $roles ) ) {
				$user  = new WP_User( get_current_user_id(), '', get_main_site_id() );
				$roles = $user->roles;
			}
		}

		if ( empty( $roles ) ) {
			return;
		}

		$role = $roles[0];

		/**
		 * Saved menu based on user ID/role.
		 *
		 * @var array $role_menu
		 */
		$role_menu = array();

		// Prioritize user based menu over role based menu.
		if ( ! empty( $saved_menu[ 'user_id_' . $user->ID ] ) ) {
			$role_menu = $saved_menu[ 'user_id_' . $user->ID ];
		} else {
			$role_menu = ! empty( $saved_menu[ $role ] ) ? $saved_menu[ $role ] : array();
		}

		$role_menu = is_array( $role_menu ) ? $role_menu : array();

		if ( empty( $role_menu ) ) {
			return;
		}

		$new_menu       = array();
		$hidden_menu    = array();
		$new_submenu    = array();
		$hidden_submenu = array();

		foreach ( $role_menu as $menu_index => $menu_item ) {
			if ( ! is_int( $menu_index ) && ! is_float( $menu_index ) ) {
				continue;
			}

			if ( ! is_array( $menu_item ) || empty( $menu_item ) ) {
				continue;
			}

			$array_helper = new Array_Helper();

			$new_menu_item   = array();
			$menu_search_key = 'separator' === $menu_item['type'] ? 'url' : 'id';

			if ( 'separator' === $menu_item['type'] ) {
				$menu_finder_index = 2; // The separator url.
			} else {
				$menu_finder_index = 5; // The menu id attribute.
			}

			$default_menu_index = $array_helper->find_assoc_array_index_by_value( $menu, $menu_finder_index, $menu_item[ $menu_search_key . '_default' ] );

			$matched_default_menu = false !== $default_menu_index ? $menu[ $default_menu_index ] : false;
			$matched_default_menu = is_array( $matched_default_menu ) ? $matched_default_menu : false;

			if ( $menu_item['was_added'] ) {
				$matched_default_menu = array(
					$menu_item['title'],
					'read',
					( $menu_item['url_default'] ? $menu_item['url_default'] : '/wp-admin/' ),
					'',
					$menu_item['class_default'],
					$menu_item['id_default'],
					'',
				);

				if ( isset( $menu_item[ $menu_item['icon_type'] ] ) ) {
					$matched_default_menu[6] = $menu_item[ $menu_item['icon_type'] ];
				}
			}

			if ( empty( $matched_default_menu ) ) {
				continue;
			}

			if ( ! $menu_item['is_hidden'] ) {
				$menu_title = $menu_item['title'] ? $menu_item['title'] : ( isset( $matched_default_menu[0] ) ? $matched_default_menu[0] : '' );
				$menu_title = (string) $menu_title;

				$menu_cap = isset( $matched_default_menu[1] ) ? $matched_default_menu[1] : '';
				$menu_cap = (string) $menu_cap;

				$menu_url = $menu_item['url'] ? $menu_item['url'] : ( isset( $matched_default_menu[2] ) ? $matched_default_menu[2] : '' );
				$menu_url = $this->placeholder_helper->convert_admin_menu_placeholder_tags( $menu_url );

				$page_title = isset( $matched_default_menu[3] ) ? $matched_default_menu[3] : '';
				$page_title = (string) $page_title;

				$menu_class = $menu_item['class'] ? $menu_item['class'] : ( isset( $matched_default_menu[4] ) ? $matched_default_menu[4] : '' );
				$menu_class = (string) $menu_class;

				if ( ! empty( $menu_item['open_new_tab'] ) ) {
					$menu_class = trim( $menu_class . ' ats-open-new-tab' );
				}

				array_push( $new_menu_item, $this->placeholder_helper->convert_admin_menu_placeholder_tags( $menu_title ) );
				array_push( $new_menu_item, $menu_cap );
				array_push( $new_menu_item, $menu_url );
				array_push( $new_menu_item, $this->placeholder_helper->convert_admin_menu_placeholder_tags( $page_title ) );
				array_push( $new_menu_item, $menu_class );

				if ( 'menu' === $menu_item['type'] ) {
					$menu_id   = $menu_item['id'] ? $menu_item['id'] : ( isset( $matched_default_menu[5] ) ? $matched_default_menu[5] : '' );
					$menu_id   = (string) $menu_id;
					$menu_icon = isset( $matched_default_menu[6] ) ? $matched_default_menu[6] : '';

					if ( $menu_item['icon_type'] && $menu_item[ $menu_item['icon_type'] ] ) {
						$menu_icon = $menu_item[ $menu_item['icon_type'] ];
					}

					$menu_icon = (string) $menu_icon;

					array_push( $new_menu_item, $menu_id );
					array_push( $new_menu_item, $menu_icon );
				}

				/**
				 * The default submenu.
				 *
				 * @var array $default_submenu
				 */
				$default_submenu = ! empty( $submenu[ $menu_url ] ) && is_array( $submenu[ $menu_url ] ) ? $submenu[ $menu_url ] : array();

				/**
				 * Handle case where the default WordPress parent menu item URL is changed.
				 * Because when it's changed, its url doesn't match with it's submenu's array key.
				 */
				if ( empty( $default_submenu ) ) {
					/**
					 * The default submenu.
					 *
					 * @var array $default_submenu
					 */
					$default_submenu = ! empty( $submenu[ $matched_default_menu[2] ] ) && is_array( $submenu[ $matched_default_menu[2] ] ) ? $submenu[ $matched_default_menu[2] ] : array();
				}

				if ( ! empty( $menu_item['submenu'] ) && is_array( $menu_item['submenu'] ) ) {
					$custom_submenu       = array();
					$hidden_submenu_items = array();

					/**
					 * Looping $menu_item['submenu'].
					 *
					 * @var array $menu_item['submenu']
					 * @var int $submenu_index
					 * @var array $submenu_item
					 */
					foreach ( $menu_item['submenu'] as $submenu_index => $submenu_item ) {
						if ( empty( $submenu_item ) || ! is_array( $submenu_item ) ) {
							continue;
						}

						$submenu_finder_index = 2; // The submenu url.

						/**
						 * In the menu editor (builder), the submenu is taken via ajax.
						 * It makes the Customize submenu (under Appearance menu) url become like this:
						 * customize.php?return=%2Fwp-admin%2Fadmin-ajax.php.
						 *
						 * But in the output, the return url should not be admin-ajax.php.
						 * In the output, the return url should be the current url.
						 */
						if ( 'customize.php?return=%2Fwp-admin%2Fadmin-ajax.php' === $submenu_item['url_default'] ) {
							$current_path  = ! empty( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( $_SERVER['REQUEST_URI'] ) : '';
							$return_path   = rawurlencode( $current_path );
							$customize_url = 'customize.php?return=' . $return_path;

							$submenu_item['url_default'] = $customize_url;
						}

						$default_submenu_index = $array_helper->find_assoc_array_index_by_value( $default_submenu, $submenu_finder_index, $submenu_item['url_default'] );

						if ( false === $default_submenu_index ) {
							// If $default_submenu_index is false and the url_default is using & sign instead of &amp; code.
							if ( false !== stripos( $submenu_item['url_default'], '&' ) && false === stripos( $submenu_item['url_default'], '&amp;' ) ) {
								/**
								 * The submenu item's url_default.
								 *
								 * @var string $submenu_url_default
								 */
								$submenu_url_default = str_ireplace( '&', '&amp;', $submenu_item['url_default'] );

								// Try to look up using &amp; instead of &.
								$default_submenu_index = $array_helper->find_assoc_array_index_by_value( $default_submenu, $submenu_finder_index, $submenu_url_default );

								/**
								 * If $default_submenu_index is not false (is found),
								 * That means the url value of $default_submenu[$default_submenu_index] is using &amp; code instead of & sign.
								 * In this case, we should also replace $submenu_item['url_default'] to also using &amp; code.
								 */
								if ( false !== $default_submenu_index ) {
									$submenu_item['url_default'] = $submenu_url_default;
								}
							}
						}

						$matched_default_submenu = false !== $default_submenu_index ? $default_submenu[ $default_submenu_index ] : false;
						$matched_default_submenu = is_array( $matched_default_submenu ) ? $matched_default_submenu : false;

						/**
						 * If $matched_default_submenu is false, let's try to check in other submenus.
						 * Because we allow moving submenu items across parent menus.
						 */
						if ( false === $matched_default_submenu ) {
							/**
							 * The submenu item's url_default.
							 *
							 * @var string $submenu_url_default
							 */
							$submenu_url_default = ! empty( $submenu_item['url_default'] ) && is_string( $submenu_item['url_default'] ) ? $submenu_item['url_default'] : '';

							/**
							 * Looping global $submenu.
							 *
							 * @var array $submenu
							 * @var string $global_parent_menu_item_slug
							 * @var array $looped_global_submenu_items
							 */
							foreach ( $submenu as $global_parent_menu_item_slug => $looped_global_submenu_items ) {
								if ( ! is_array( $looped_global_submenu_items ) || empty( $looped_global_submenu_items ) ) {
									continue;
								}

								$matched_global_submenu_item_index_under_its_parent = -1;

								/**
								 * Looping $looped_global_submenu_items.
								 *
								 * @var array $looped_global_submenu_items
								 * @var int $looped_global_submenu_item_index
								 * @var array $looped_global_submenu_item
								 */
								foreach ( $looped_global_submenu_items as $looped_global_submenu_item_index => $looped_global_submenu_item ) {
									if ( ! is_array( $looped_global_submenu_item ) || empty( $looped_global_submenu_item ) ) {
										continue;
									}

									if ( $looped_global_submenu_item[ $submenu_finder_index ] === $submenu_url_default ) {
										$matched_default_submenu = $looped_global_submenu_item;

										$matched_global_submenu_item_index_under_its_parent = $looped_global_submenu_item_index;

										break;
									}

									// If condition above doesn't match and the $submenu_url_default is using & sign instead of &amp; code.
									if ( false !== stripos( $submenu_url_default, '&' ) && false === stripos( $submenu_url_default, '&amp;' ) ) {
										/**
										 * The submenu item's url_default.
										 *
										 * @var string $submenu_url_default
										 */
										$submenu_url_default = str_ireplace( '&', '&amp;', $submenu_url_default );

										// Try to look up using &amp; instead of &.
										if ( $looped_global_submenu_item[ $submenu_finder_index ] === $submenu_url_default ) {
											$matched_default_submenu = $looped_global_submenu_item;

											$matched_global_submenu_item_index_under_its_parent = $looped_global_submenu_item_index;

											/**
											 * If this block is reached, it means $submenu_url_default is using &amp; code instead of & sign.
											 * In this case, we should also replace $submenu_item['url_default'] to also using &amp; code.
											 */
											$submenu_item['url_default'] = $submenu_url_default;

											break;
										}
									}
								}

								/**
								 * If it matches submenu item from other parent menus,
								 * then remove that submenu item from the global submenu.
								 */
								if ( $matched_global_submenu_item_index_under_its_parent > -1 ) {
									unset( $submenu[ $global_parent_menu_item_slug ][ $matched_global_submenu_item_index_under_its_parent ] );
									break;
								}
							}
						}

						if ( $submenu_item['was_added'] ) {
							$matched_default_submenu = array(
								$submenu_item['title'],
								'read',
								( $submenu_item['url_default'] ? $submenu_item['url_default'] : '/wp-admin/' ),
								$submenu_item['title'],
								'',
							);
						}

						if ( ! $submenu_item['is_hidden'] ) {
							$new_submenu_item = array();

							$submenu_title = $submenu_item['title'] ? $submenu_item['title'] : ( isset( $matched_default_submenu[0] ) ? $matched_default_submenu[0] : '' );
							$submenu_title = (string) $submenu_title;
							array_push( $new_submenu_item, $this->placeholder_helper->convert_admin_menu_placeholder_tags( $submenu_title ) );

							$submenu_cap = isset( $matched_default_submenu[1] ) ? $matched_default_submenu[1] : '';
							$submenu_cap = (string) $submenu_cap;
							array_push( $new_submenu_item, $submenu_cap );

							$submenu_url = $submenu_item['url'] ? $submenu_item['url'] : ( isset( $matched_default_submenu[2] ) ? $matched_default_submenu[2] : '' );
							$submenu_url = (string) $submenu_url;
							array_push( $new_submenu_item, $this->placeholder_helper->convert_admin_menu_placeholder_tags( $submenu_url ) );

							$submenu_page_title = isset( $matched_default_submenu[3] ) ? $matched_default_submenu[3] : '';
							$submenu_page_title = (string) $submenu_page_title;
							array_push( $new_submenu_item, $this->placeholder_helper->convert_admin_menu_placeholder_tags( $submenu_page_title ) );

							$submenu_class = isset( $matched_default_submenu[4] ) ? $matched_default_submenu[4] : '';
							$submenu_class = (string) $submenu_class;

							if ( ! empty( $submenu_item['open_new_tab'] ) ) {
								$submenu_class = trim( $submenu_class . ' ats-open-new-tab' );
							}

							if ( ! empty( $submenu_class ) ) {
								array_push( $new_submenu_item, $submenu_class );
							}

							$new_submenu_item['url_default'] = $submenu_item['url_default'];

							if ( ! $submenu_item['was_added'] ) {
								if ( $matched_default_submenu ) {
									array_push( $custom_submenu, $new_submenu_item );
								}
							} else {
								array_push( $custom_submenu, $new_submenu_item );
							}
						} elseif ( $matched_default_submenu ) {
							$hidden_submenu_item = $matched_default_submenu;

							$hidden_submenu_item['url_default'] = $submenu_item['url_default'];

							array_push( $hidden_submenu_items, $hidden_submenu_item );
						}
					} // End of foreach $menu_item['submenu'].

					$new_submenu[ $menu_url ] = $custom_submenu;

					if ( ! empty( $hidden_submenu_items ) ) {
						$hidden_submenu[ $menu_url ] = $hidden_submenu_items;
					}
				} // End of checking $menu_item['submenu'].

				array_push( $new_menu, $new_menu_item );
			} else {
				array_push( $hidden_menu, $matched_default_menu );
			} // End of is_hidden checking.
		} // End of foreach $role_menu.

		$new_menu    = $this->get_new_menu_items( $role, $menu, $new_menu, $hidden_menu );
		$new_submenu = $this->get_new_submenu_items( $role, $submenu, $new_submenu, $hidden_submenu );

		// Update the global $menu & $submenu to use our parsing results.
		$menu    = $new_menu;
		$submenu = $new_submenu;

	}

	/**
	 * Get new items from menu
	 *
	 * @param string $role The specified role.
	 * @param array  $menu The old menu.
	 * @param array  $custom_menu The custom menu.
	 * @param array  $hidden_menu The hidden menu.
	 *
	 * @return array The modified custom menu.
	 */
	public function get_new_menu_items( $role, $menu, $custom_menu, $hidden_menu ) {
		ksort( $menu );

		$prev_custom_index = 0;

		$array_helper = new Array_Helper();

		foreach ( $menu as $menu_index => $menu_item ) {
			if ( ! is_array( $menu_item ) || empty( $menu_item ) ) {
				continue;
			}

			$menu_type = empty( $menu_item[0] ) && empty( $menu_item[3] ) ? 'separator' : 'menu';

			if ( 'separator' === $menu_type ) {
				$menu_finder_index = 2; // The separator url.
			} else {
				$menu_finder_index = 5; // The menu id attribute.
			}

			$custom_menu_index = $array_helper->find_assoc_array_index_by_value( $custom_menu, $menu_finder_index, $menu_item[ $menu_finder_index ] );

			$matched_custom_menu = false !== $custom_menu_index ? $custom_menu[ $custom_menu_index ] : false;
			$matched_custom_menu = is_array( $matched_custom_menu ) ? $matched_custom_menu : false;

			$current_custom_index = false !== $custom_menu_index ? $custom_menu_index : $prev_custom_index + 1;
			$prev_custom_index    = $current_custom_index;

			if ( false === $matched_custom_menu ) {
				$hidden_menu_index = $array_helper->find_assoc_array_index_by_value( $hidden_menu, $menu_finder_index, $menu_item[ $menu_finder_index ] );

				if ( false === $hidden_menu_index ) {
					array_splice( $custom_menu, $current_custom_index, 0, array( $menu_item ) );
				}
			}
		}

		return $custom_menu;
	}

	/**
	 * Get new items from submenu
	 *
	 * @param string $role The specified role.
	 * @param array  $submenu The old submenu.
	 * @param array  $custom_submenu The custom submenu.
	 * @param array  $hidden_submenu The hidden submenu.
	 *
	 * @return array The modified custom submenu.
	 */
	public function get_new_submenu_items( $role, $submenu, $custom_submenu, $hidden_submenu ) {
		$array_helper = new Array_Helper();

		foreach ( $submenu as $submenu_key => $submenu_item ) {
			if ( ! is_array( $submenu_item ) || empty( $submenu_item ) ) {
				continue;
			}

			$matched_custom_submenu = ! empty( $custom_submenu[ $submenu_key ] ) ? $custom_submenu[ $submenu_key ] : false;
			$matched_custom_submenu = is_array( $matched_custom_submenu ) ? $matched_custom_submenu : false;

			if ( ! $matched_custom_submenu ) {
				if ( ! isset( $hidden_submenu[ $submenu_key ] ) ) {
					$custom_submenu[ $submenu_key ] = $submenu_item;
				}

				continue;
			}

			ksort( $submenu_item );

			$prev_custom_index = -1;

			foreach ( $submenu_item as $submenu_order => $submenu_values ) {
				if ( ! is_array( $submenu_values ) || empty( $submenu_values ) ) {
					continue;
				}

				$submenu_finder_index = 2; // The submenu url.

				$custom_submenu_index = $array_helper->find_assoc_array_index_by_value( $matched_custom_submenu, 'url_default', $submenu_values[ $submenu_finder_index ] );
				$current_custom_index = false !== $custom_submenu_index ? $custom_submenu_index : $prev_custom_index + 1;
				$prev_custom_index    = $current_custom_index;

				if ( false === $custom_submenu_index ) {
					$is_hidden = false;

					if ( isset( $hidden_submenu[ $submenu_key ] ) ) {
						$hidden_submenu_index = $array_helper->find_assoc_array_index_by_value( $hidden_submenu[ $submenu_key ], $submenu_finder_index, $submenu_values[ $submenu_finder_index ] );

						if ( false !== $hidden_submenu_index ) {
							$is_hidden = true;
						}
					}

					if ( ! $is_hidden ) {
						array_splice( $custom_submenu[ $submenu_key ], $current_custom_index, 0, array( $submenu_values ) );
					}
				}
			}
		}

		return $custom_submenu;
	}

}
