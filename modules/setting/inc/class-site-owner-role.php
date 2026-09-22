<?php
/**
 * Site Owner role: an Administrator clone with configurable restricted capabilities.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Setting;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Class to register/sync the "Site Owner" role from the `ats_settings` option.
 */
class Site_Owner_Role {

	const ROLE_KEY = 'site_owner';

	const DEFAULT_ROLE_NAME = 'Site Owner';

	/**
	 * Curated capability groups shown in the settings UI.
	 *
	 * @return array Group label => array( cap slug => label ).
	 */
	public static function cap_groups() {

		return array(
			__( 'Plugins', 'ats-dashboard' )           => array(
				'activate_plugins' => __( 'Activate & deactivate plugins', 'ats-dashboard' ),
				'install_plugins'  => __( 'Install new plugins', 'ats-dashboard' ),
				'update_plugins'   => __( 'Update plugins', 'ats-dashboard' ),
				'delete_plugins'   => __( 'Delete plugins', 'ats-dashboard' ),
				'edit_plugins'     => __( 'Edit plugin source files', 'ats-dashboard' ),
			),
			__( 'Themes', 'ats-dashboard' )            => array(
				'switch_themes'  => __( 'Switch the active theme', 'ats-dashboard' ),
				'install_themes' => __( 'Install new themes', 'ats-dashboard' ),
				'update_themes'  => __( 'Update themes', 'ats-dashboard' ),
				'delete_themes'  => __( 'Delete themes', 'ats-dashboard' ),
				'edit_themes'    => __( 'Edit theme source files', 'ats-dashboard' ),
			),
			__( 'Settings & Tools', 'ats-dashboard' )  => array(
				'manage_options' => __( 'Access WordPress Settings pages', 'ats-dashboard' ),
				'export'         => __( 'Export site data', 'ats-dashboard' ),
				'import'         => __( 'Import site data', 'ats-dashboard' ),
			),
			__( 'Core & Files', 'ats-dashboard' )      => array(
				'update_core' => __( 'Run WordPress core updates', 'ats-dashboard' ),
				'edit_files'  => __( 'Edit theme & plugin files', 'ats-dashboard' ),
			),
			__( 'Users', 'ats-dashboard' )             => array(
				'create_users'  => __( 'Create new users', 'ats-dashboard' ),
				'edit_users'    => __( 'Edit other users', 'ats-dashboard' ),
				'delete_users'  => __( 'Delete users', 'ats-dashboard' ),
				'list_users'    => __( 'View user list', 'ats-dashboard' ),
				'promote_users' => __( 'Promote users to higher roles', 'ats-dashboard' ),
				'remove_users'  => __( 'Remove users from this site', 'ats-dashboard' ),
			),
		);

	}

	/**
	 * Flat list of every configurable capability slug.
	 *
	 * @return string[]
	 */
	public static function all_configurable_caps() {

		$caps = array();

		foreach ( self::cap_groups() as $group_caps ) {
			$caps = array_merge( $caps, array_keys( $group_caps ) );
		}

		return $caps;

	}

	/**
	 * Capability slugs granted by default when no configuration has been saved yet.
	 *
	 * @return string[]
	 */
	public static function default_granted_caps() {

		return array( 'list_users' );

	}

	/**
	 * Get the capability slugs currently granted to the Site Owner role.
	 *
	 * @return string[]
	 */
	public static function granted_caps() {

		$settings = get_option( 'ats_settings', array() );

		if ( ! isset( $settings['site_owner_caps'] ) ) {
			return self::default_granted_caps();
		}

		$submitted = (array) $settings['site_owner_caps'];

		// Whitelist against the configurable caps so a stale/tampered option value can never grant an unknown capability.
		return array_values( array_intersect( $submitted, self::all_configurable_caps() ) );

	}

	/**
	 * Get the configured display name for the Site Owner role.
	 *
	 * @return string
	 */
	public static function role_name() {

		$settings = get_option( 'ats_settings', array() );
		$name     = isset( $settings['site_owner_role_name'] ) ? trim( (string) $settings['site_owner_role_name'] ) : '';

		return '' !== $name ? $name : __( 'Site Owner', 'ats-dashboard' );

	}

	/**
	 * Whether the Site Owner role feature is enabled.
	 *
	 * @return bool
	 */
	public static function is_enabled() {

		$settings = get_option( 'ats_settings', array() );

		return ! empty( $settings['site_owner_role_enabled'] );

	}

	/**
	 * Build the capability set for the Site Owner role: clone Administrator,
	 * then strip every configurable capability that isn't currently granted.
	 *
	 * @return array
	 */
	public static function build_caps() {

		$admin_role = get_role( 'administrator' );
		$caps       = $admin_role ? $admin_role->capabilities : array();

		$granted = self::granted_caps();
		$denied  = array_diff( self::all_configurable_caps(), $granted );

		foreach ( $denied as $cap ) {
			unset( $caps[ $cap ] );
		}

		return $caps;

	}

	/**
	 * Register (if enabled and missing) or remove (if disabled) the Site Owner
	 * role, and keep its capabilities in sync with the saved settings.
	 *
	 * Runs on every `init`, mirroring the pattern used by the legacy
	 * arcanetech-wp-core-plugin's site owner role, so a settings save is
	 * reflected immediately without needing a dedicated save handler.
	 */
	public static function sync() {

		$role = get_role( self::ROLE_KEY );

		if ( ! self::is_enabled() ) {
			if ( $role ) {
				remove_role( self::ROLE_KEY );
			}

			return;
		}

		$caps = self::build_caps();
		$name = self::role_name();

		if ( ! $role ) {
			add_role( self::ROLE_KEY, $name, $caps );

			return;
		}

		self::sync_role_name( $name );

		foreach ( self::all_configurable_caps() as $cap ) {
			if ( isset( $caps[ $cap ] ) ) {
				$role->add_cap( $cap );
			} else {
				$role->remove_cap( $cap );
			}
		}

	}

	/**
	 * Update the display name of the already-registered Site Owner role, if it
	 * has changed. WordPress core has no `rename_role()`, so this updates the
	 * `WP_Roles` in-memory/db state directly, mirroring what `add_role()` does.
	 *
	 * @param string $name The desired display name.
	 */
	private static function sync_role_name( $name ) {

		global $wp_roles;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = wp_roles(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}

		if ( ! isset( $wp_roles->roles[ self::ROLE_KEY ] ) || $wp_roles->roles[ self::ROLE_KEY ]['name'] === $name ) {
			return;
		}

		$wp_roles->roles[ self::ROLE_KEY ]['name'] = $name;
		$wp_roles->role_names[ self::ROLE_KEY ]    = $name;

		if ( $wp_roles->use_db ) {
			update_option( $wp_roles->role_key, $wp_roles->roles );
		}

	}

	/**
	 * Remove the Site Owner role on plugin deactivation.
	 *
	 * Users assigned this role fall back to "no role" — WordPress default behavior.
	 * User role assignment itself is left untouched; re-assigning users who had
	 * this role is a manual admin action.
	 */
	public static function remove_on_deactivation() {

		remove_role( self::ROLE_KEY );

	}

}
