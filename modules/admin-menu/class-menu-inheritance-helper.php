<?php
/**
 * Menu inheritance helper.
 *
 * Provides the diff/merge primitives that let role & user menus store only
 * what they override, inheriting everything else from the "Default (Everyone)"
 * menu. This keeps edits to Default flowing through to every role/user that
 * hasn't explicitly overridden a given item.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\AdminMenu;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Class to compute & apply menu item deltas between inheritance layers.
 */
class Menu_Inheritance_Helper {

	/**
	 * Editable fields compared/overridden for top level menu & separator items.
	 *
	 * @var string[]
	 */
	public $menu_fields = array( 'title', 'url', 'class', 'dashicon', 'icon_svg', 'icon_type', 'open_new_tab', 'is_hidden' );

	/**
	 * Editable fields compared/overridden for submenu items.
	 *
	 * @var string[]
	 */
	public $submenu_fields = array( 'title', 'url', 'open_new_tab', 'is_hidden' );

	/**
	 * Build a stable identity key for a menu/submenu item.
	 *
	 * Items are matched across layers (Default -> Role -> User) using the same
	 * identity that already links a builder item back to its real WP menu entry
	 * (id_default for menu items, url_default for separators & submenu items).
	 *
	 * @param array $item The item.
	 * @param bool  $is_submenu Whether the item is a submenu item.
	 *
	 * @return string
	 */
	public function item_identity( $item, $is_submenu = false ) {

		if ( $is_submenu ) {
			return 'url:' . ( isset( $item['url_default'] ) ? (string) $item['url_default'] : '' );
		}

		if ( isset( $item['type'] ) && 'separator' === $item['type'] ) {
			return 'sep:' . ( isset( $item['url_default'] ) ? (string) $item['url_default'] : '' );
		}

		return 'id:' . ( isset( $item['id_default'] ) ? (string) $item['id_default'] : '' );

	}

	/**
	 * Compute the delta of $posted_items relative to $base_items.
	 *
	 * Only items (or fields) that actually differ from the base are returned.
	 * Items identical to the base are omitted entirely, which is what lets
	 * later edits to the base propagate to any layer that never overrode them.
	 *
	 * @param array $base_items The base (parent layer) items.
	 * @param array $posted_items The items posted from the builder for this layer.
	 * @param bool  $is_submenu Whether this is a submenu item list.
	 *
	 * @return array The delta.
	 */
	public function diff_items( $base_items, $posted_items, $is_submenu = false ) {

		$fields = $is_submenu ? $this->submenu_fields : $this->menu_fields;

		$base_by_key = array();

		foreach ( (array) $base_items as $base_item ) {
			if ( ! is_array( $base_item ) || empty( $base_item ) ) {
				continue;
			}

			$base_by_key[ $this->item_identity( $base_item, $is_submenu ) ] = $base_item;
		}

		$delta     = array();
		$seen_keys = array();

		foreach ( (array) $posted_items as $posted_item ) {
			if ( ! is_array( $posted_item ) || empty( $posted_item ) ) {
				continue;
			}

			$key                = $this->item_identity( $posted_item, $is_submenu );
			$seen_keys[ $key ]  = true;

			if ( ! isset( $base_by_key[ $key ] ) ) {
				// Item doesn't exist in the base layer (e.g. a custom item added at this layer). Store it whole.
				$delta[] = $posted_item;
				continue;
			}

			$base_item  = $base_by_key[ $key ];
			$changed    = array();
			$has_change = false;

			foreach ( $fields as $field ) {
				$base_value   = isset( $base_item[ $field ] ) ? $base_item[ $field ] : '';
				$posted_value = isset( $posted_item[ $field ] ) ? $posted_item[ $field ] : '';

				if ( (string) $base_value !== (string) $posted_value ) {
					$changed[ $field ] = $posted_value;
					$has_change        = true;
				}
			}

			if ( ! $is_submenu ) {
				$base_submenu   = ! empty( $base_item['submenu'] ) && is_array( $base_item['submenu'] ) ? $base_item['submenu'] : array();
				$posted_submenu = ! empty( $posted_item['submenu'] ) && is_array( $posted_item['submenu'] ) ? $posted_item['submenu'] : array();

				$submenu_delta = $this->diff_items( $base_submenu, $posted_submenu, true );

				if ( ! empty( $submenu_delta ) ) {
					$changed['submenu'] = $submenu_delta;
					$has_change         = true;
				}
			}

			if ( ! $has_change ) {
				continue;
			}

			/**
			 * Only identity fields go here - never "was_added", since this entry
			 * is for an item matched against the base (i.e. not new at this layer),
			 * and blindly writing was_added would clobber the base's real flag
			 * once applied in apply_delta().
			 */
			$entry = array(
				'type' => isset( $posted_item['type'] ) ? $posted_item['type'] : 'menu',
			);

			if ( $is_submenu || 'separator' === $entry['type'] ) {
				$entry['url_default'] = isset( $posted_item['url_default'] ) ? $posted_item['url_default'] : '';
			} else {
				$entry['id_default'] = isset( $posted_item['id_default'] ) ? $posted_item['id_default'] : '';
			}

			$delta[] = array_merge( $entry, $changed );
		}

		// Items that existed in the base but were removed entirely at this layer.
		// This can only happen for custom ("was_added") items - built-in WP menu
		// items can only be hidden (is_hidden), never truly removed.
		foreach ( $base_by_key as $key => $base_item ) {
			if ( isset( $seen_keys[ $key ] ) ) {
				continue;
			}

			if ( empty( $base_item['was_added'] ) ) {
				continue;
			}

			$entry = array(
				'type'    => isset( $base_item['type'] ) ? $base_item['type'] : 'menu',
				'removed' => 1,
			);

			if ( $is_submenu || 'separator' === $entry['type'] ) {
				$entry['url_default'] = isset( $base_item['url_default'] ) ? $base_item['url_default'] : '';
			} else {
				$entry['id_default'] = isset( $base_item['id_default'] ) ? $base_item['id_default'] : '';
			}

			$delta[] = $entry;
		}

		return $delta;

	}

	/**
	 * Apply a delta on top of a base item list, producing a fully resolved item list.
	 *
	 * Items not touched by the delta are inherited from the base as-is. Items
	 * present in the delta have only their overridden fields replaced. Items
	 * flagged "removed" are dropped, and items absent from the base are appended.
	 *
	 * @param array $base_items The base (parent layer) items.
	 * @param array $delta_items The delta to apply on top.
	 * @param bool  $is_submenu Whether this is a submenu item list.
	 *
	 * @return array The resolved item list.
	 */
	public function apply_delta( $base_items, $delta_items, $is_submenu = false ) {

		$result       = array();
		$index_by_key = array();

		foreach ( (array) $base_items as $base_item ) {
			if ( ! is_array( $base_item ) || empty( $base_item ) ) {
				continue;
			}

			$key                  = $this->item_identity( $base_item, $is_submenu );
			$index_by_key[ $key ] = count( $result );
			$result[]             = $base_item;
		}

		foreach ( (array) $delta_items as $delta_item ) {
			if ( ! is_array( $delta_item ) || empty( $delta_item ) ) {
				continue;
			}

			$key = $this->item_identity( $delta_item, $is_submenu );

			if ( ! empty( $delta_item['removed'] ) ) {
				if ( isset( $index_by_key[ $key ] ) ) {
					unset( $result[ $index_by_key[ $key ] ] );
					unset( $index_by_key[ $key ] );
				}

				continue;
			}

			if ( ! isset( $index_by_key[ $key ] ) ) {
				$index_by_key[ $key ] = count( $result );
				$result[]             = $delta_item;
				continue;
			}

			$merged_item = $result[ $index_by_key[ $key ] ];

			foreach ( $delta_item as $field => $value ) {
				if ( 'removed' === $field ) {
					continue;
				}

				if ( 'submenu' === $field && ! $is_submenu ) {
					$base_submenu             = ! empty( $merged_item['submenu'] ) && is_array( $merged_item['submenu'] ) ? $merged_item['submenu'] : array();
					$merged_item['submenu']   = $this->apply_delta( $base_submenu, $value, true );
					continue;
				}

				$merged_item[ $field ] = $value;
			}

			$result[ $index_by_key[ $key ] ] = $merged_item;
		}

		return array_values( $result );

	}

	/**
	 * Flag each item in a resolved (builder-format) response with whether it's
	 * overridden at this layer's own delta, or inherited from its parent layer.
	 *
	 * Adds an `is_overridden` boolean to every top level item, and to every
	 * submenu item whose parent has a submenu-level override.
	 *
	 * @param array $response The resolved response items (as built for the builder UI).
	 * @param array $delta The immediate delta for this layer (role's own delta for a
	 *                     role tab, user's own delta for a user tab).
	 *
	 * @return array The response, with `is_overridden` flags added.
	 */
	public function mark_overrides( $response, $delta ) {

		$delta_by_key = array();

		foreach ( (array) $delta as $entry ) {
			if ( ! is_array( $entry ) || empty( $entry ) || ! empty( $entry['removed'] ) ) {
				continue;
			}

			$delta_by_key[ $this->item_identity( $entry, false ) ] = $entry;
		}

		foreach ( $response as $index => $item ) {
			if ( ! is_array( $item ) || empty( $item ) ) {
				continue;
			}

			$key                             = $this->item_identity( $item, false );
			$matched_delta                   = isset( $delta_by_key[ $key ] ) ? $delta_by_key[ $key ] : false;
			$response[ $index ]['is_overridden'] = false !== $matched_delta;

			if ( empty( $item['submenu'] ) || ! is_array( $item['submenu'] ) ) {
				continue;
			}

			$submenu_delta_by_key = array();

			if ( $matched_delta && ! empty( $matched_delta['submenu'] ) && is_array( $matched_delta['submenu'] ) ) {
				foreach ( $matched_delta['submenu'] as $submenu_entry ) {
					if ( ! is_array( $submenu_entry ) || empty( $submenu_entry ) || ! empty( $submenu_entry['removed'] ) ) {
						continue;
					}

					$submenu_delta_by_key[ $this->item_identity( $submenu_entry, true ) ] = true;
				}
			}

			foreach ( $item['submenu'] as $submenu_index => $submenu_item ) {
				if ( ! is_array( $submenu_item ) || empty( $submenu_item ) ) {
					continue;
				}

				$submenu_key = $this->item_identity( $submenu_item, true );

				$response[ $index ]['submenu'][ $submenu_index ]['is_overridden'] = isset( $submenu_delta_by_key[ $submenu_key ] );
			}
		}

		return $response;

	}

}
