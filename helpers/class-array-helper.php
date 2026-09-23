<?php
/**
 * Array helper.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Helpers;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Class to setup array helper.
 */
class Array_Helper {
	/**
	 * Find associative array's index by its key's value.
	 *
	 * We don't use the array_search combined with array_column method
	 * because it doesn't work in ats admin menu module.
	 *
	 * @param array  $arr The haystack array.
	 * @param string $key The key to search in.
	 * @param mixed  $value The value to search for.
	 *
	 * @return false|int The index if found, false otherwise.
	 */
	public function find_assoc_array_index_by_value( $arr, $key, $value ) {
		foreach ( $arr as $index => $item ) {
			if ( isset( $item[ $key ] ) && $item[ $key ] === $value ) {
				return $index;
			}
		}

		return false;
	}

	/**
	 * Check if specific array key exists in multi-dimensional array.
	 *
	 * @param array  $arr The array.
	 * @param string $key The key.
	 *
	 * @return bool
	 */
	public function nested_key_exists( $arr, $key ) {

		// is in base array?
		if ( array_key_exists( $key, $arr ) ) {
			return true;
		}

		// Check arrays contained in this array.
		foreach ( $arr as $element ) {
			if ( is_array( $element ) ) {
				if ( $this->nested_key_exists( $element, $key ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Clean up a (multiple) serialized array.
	 *
	 * The returned $value after unserialization should be an array by default.
	 * If it's still a string, then we need to unserialize it again.
	 *
	 * This is related to a role import/export issue in earlier releases.
	 * Note that this can't be removed in the future to maintain full backwards compatibility.
	 *
	 * @param string $value The value to clean.
	 * @param int    $depth The depth of the checking.
	 *
	 * @return array The unserialized array.
	 */
	public function clean_unserialize( $value, $depth = 2 ) {
		for ( $i = 0; $i < $depth; $i++ ) {
			if ( is_serialized( $value ) ) {
				// Legacy role lists may be serialized more than once. Never instantiate objects.
				$value = @unserialize( $value, array( 'allowed_classes' => false, 'max_depth' => 32 ) );

				if ( ! is_serialized( $value ) ) {
					break;
				}
			} else {
				break;
			}
		}

		if ( ! is_array( $value ) ) {
			return array();
		}

		// All consumers expect a flat list of roles or user IDs.
		foreach ( $value as $item ) {
			if ( ! is_string( $item ) && ! is_int( $item ) ) {
				return array();
			}
		}

		return $value;
	}
}
