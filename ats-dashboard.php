<?php
/**
 * Plugin Name: ATS Dashboard
 * Plugin URI: https://arcanetechct.com/
 * Description: ATS Dashboard gives you full control over your WordPress Dashboard. Remove the default Dashboard Widgets and create your own for a better user experience.
 * Version: 1.0.9
 * Author: Arcane Tech
 * Author URI: https://arcanetechct.com/
 * Text Domain: ats-dashboard
 * License: GPL-3.0-only
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * Derived from Ultimate Dashboard by David Vongries (GPL-2.0-or-later).
 * See NOTICE.md and THIRD-PARTY-NOTICES.md for upstream attribution.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

// Constants.
define( 'ATS_DASHBOARD_PLUGIN_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
define( 'ATS_DASHBOARD_PLUGIN_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );
define( 'ATS_DASHBOARD_PLUGIN_VERSION', '1.0.9' );
define( 'ATS_DASHBOARD_PLUGIN_FILE', plugin_basename( __FILE__ ) );
if ( ! defined( 'ATS_DASHBOARD_DEFAULT_LOGO_URL' ) ) {
	define( 'ATS_DASHBOARD_DEFAULT_LOGO_URL', 'https://cdn-r2.arcanetechct.com/LogoWideTransparent.png' );
}

// Site owners and forks can override these in wp-config.php before plugins load.
if ( ! defined( 'ATS_DASHBOARD_UPDATES_ENABLED' ) ) {
	define( 'ATS_DASHBOARD_UPDATES_ENABLED', true );
}
if ( ! defined( 'ATS_DASHBOARD_UPDATE_MANIFEST_URL' ) ) {
	define( 'ATS_DASHBOARD_UPDATE_MANIFEST_URL', 'https://files.arcanetechct.com/ats-dashboard/info.json' );
}

// ── Self-hosted Auto-Updates (R2) ───────────────────────────────────────────────

if ( ATS_DASHBOARD_UPDATES_ENABLED && ATS_DASHBOARD_UPDATE_MANIFEST_URL ) {
	require_once __DIR__ . '/includes/class-simple-updater.php';
	new ATS_Dashboard_Updater(
		ATS_DASHBOARD_UPDATE_MANIFEST_URL,
		__FILE__,
		ATS_DASHBOARD_PLUGIN_VERSION
	);
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once __DIR__ . '/includes/class-cli-command.php';
}

// Admin menu specific support — must run directly (unhooked) to avoid being overlapped by other plugins.
require_once __DIR__ . '/modules/admin-menu/inc/not-doing-ajax.php';
ats_admin_menu_not_doing_ajax();

require __DIR__ . '/class-setup.php';

ATSDash\Setup::init();
