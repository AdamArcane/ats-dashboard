<?php
defined( 'ABSPATH' ) || exit;

/**
 * Minimal self-hosted plugin updater.
 *
 * Points WordPress's normal "Update available" flow at a small JSON
 * manifest we control (served from our own R2 bucket) instead of
 * WordPress.org or GitHub Releases. No third-party library — this is the
 * entire contract:
 *
 *   { "version": "1.2.0", "download_url": "https://.../plugin.zip", ... }
 *
 * If the manifest's version is newer than the installed version, the
 * plugin shows up with an update available on the Plugins screen exactly
 * like a wp.org plugin, and "Update Now" downloads download_url and
 * installs it in place. Nothing here talks to WordPress.org or GitHub.
 *
 * Same pattern as arcane-tech-core's updater — class renamed per-plugin
 * (ATS_Dashboard_Updater here) so multiple plugins using this same file
 * never collide if they ever end up active in the same PHP process.
 *
 * Usage (in the plugin's main file):
 *   require_once __DIR__ . '/includes/class-simple-updater.php';
 *   new ATS_Dashboard_Updater(
 *       'https://files.arcanetechct.com/ats-dashboard/info.json',
 *       __FILE__,
 *       ATS_DASHBOARD_PLUGIN_VERSION
 *   );
 */
class ATS_Dashboard_Updater {

	private $manifest_url;
	private $plugin_file;
	private $plugin_basename; // e.g. "ats-dashboard/ats-dashboard.php"
	private $slug;            // e.g. "ats-dashboard" — must match the installed plugin's folder name.
	private $version;
	private $cache_key;

	public function __construct( $manifest_url, $plugin_file, $installed_version ) {
		$this->manifest_url    = $manifest_url;
		$this->plugin_file     = $plugin_file;
		$this->plugin_basename = plugin_basename( $plugin_file );
		$this->slug             = dirname( $this->plugin_basename );
		$this->version           = $installed_version;
		$this->cache_key         = 'ats_dashboard_updater_' . md5( $this->manifest_url );

		if ( ! is_admin() ) {
			return;
		}

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_update' ) );
		add_filter( 'plugins_api', array( $this, 'plugin_info' ), 20, 3 );
		add_action( 'upgrader_process_complete', array( $this, 'clear_cache_after_update' ), 10, 2 );
		add_action( 'delete_site_transient_update_plugins', array( $this, 'clear_cache_if_requested' ) );
	}

	/**
	 * @param bool $force  Bypass the 6-hour cache (used right after an update
	 *                     and when the "Check again" link is used).
	 * @return object|false  Decoded manifest, or false on any failure —
	 *                       failures never block the Plugins screen, they
	 *                       just mean "no update info available right now."
	 */
	private function get_manifest( $force = false ) {
		if ( ! $force ) {
			$cached = get_transient( $this->cache_key );
			if ( false !== $cached ) {
				return $cached ?: false;
			}
		}

		$response = wp_remote_get( $this->manifest_url, array(
			'timeout' => 10,
			'headers' => array( 'Accept' => 'application/json' ),
		) );

		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			set_transient( $this->cache_key, false, 15 * MINUTE_IN_SECONDS ); // Short retry window on failure.
			return false;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ) );
		if ( ! is_object( $data ) || empty( $data->version ) || empty( $data->download_url ) ) {
			set_transient( $this->cache_key, false, 15 * MINUTE_IN_SECONDS );
			return false;
		}

		set_transient( $this->cache_key, $data, 6 * HOUR_IN_SECONDS );
		return $data;
	}

	/**
	 * Hooked to pre_set_site_transient_update_plugins — the same filter
	 * every plugin updater (including wp.org's own) uses to report an
	 * available update.
	 */
	public function check_for_update( $transient ) {
		// WP calls this filter before the transient has ever been populated
		// (e.g. very first admin load); nothing to compare against yet.
		if ( empty( $transient ) || ! is_object( $transient ) ) {
			return $transient;
		}

		$remote = $this->get_manifest();
		if ( ! $remote ) {
			return $transient;
		}

		if ( version_compare( $this->version, $remote->version, '<' ) ) {
			$transient->response[ $this->plugin_basename ] = (object) array(
				'id'           => $this->manifest_url,
				'slug'         => $this->slug,
				'plugin'       => $this->plugin_basename,
				'new_version'  => $remote->version,
				'url'          => $remote->homepage ?? '',
				'package'      => $remote->download_url,
				'tested'       => $remote->tested ?? '',
				'requires'     => $remote->requires ?? '',
				'requires_php' => $remote->requires_php ?? '',
			);
		} else {
			unset( $transient->response[ $this->plugin_basename ] );
		}

		return $transient;
	}

	/**
	 * Hooked to plugins_api — powers the "View version x.x.x details" popup
	 * on the Plugins screen. Optional in the sense that updates still work
	 * without it, but the popup would otherwise 404 against wp.org.
	 */
	public function plugin_info( $result, $action, $args ) {
		if ( 'plugin_information' !== $action || empty( $args->slug ) || $this->slug !== $args->slug ) {
			return $result;
		}

		$remote = $this->get_manifest();
		if ( ! $remote ) {
			return $result;
		}

		return (object) array(
			'name'          => $remote->name ?? $this->slug,
			'slug'          => $this->slug,
			'version'       => $remote->version,
			'author'        => $remote->author ?? '',
			'homepage'      => $remote->homepage ?? '',
			'requires'      => $remote->requires ?? '',
			'requires_php'  => $remote->requires_php ?? '',
			'tested'        => $remote->tested ?? '',
			'last_updated'  => $remote->last_updated ?? '',
			'download_link' => $remote->download_url,
			'sections'      => (array) ( $remote->sections ?? array( 'description' => '' ) ),
		);
	}

	/** Force a fresh manifest fetch right after an update completes. */
	public function clear_cache_after_update( $upgrader, $options ) {
		if ( isset( $options['action'], $options['type'] ) && 'update' === $options['action'] && 'plugin' === $options['type'] ) {
			delete_transient( $this->cache_key );
		}
	}

	/**
	 * WordPress core clears its own update_plugins site transient whenever
	 * something forces a fresh check -- the "Check Again" button on
	 * Dashboard > Updates, WP-CLI's `wp plugin update-check`, right after
	 * an update, etc. Hooking that action (instead of guessing at a
	 * force-check GET param on whichever admin page happens to be loaded)
	 * means our manifest cache always clears in lockstep with core's, so a
	 * forced check never serves a stale cached manifest.
	 */
	public function clear_cache_if_requested() {
		delete_transient( $this->cache_key );
	}
}
