<?php
/**
 * Plugin Name: ATS Dashboard
 * Plugin URI: https://arcanetechct.com/
 * Description: ATS Dashboard gives you full control over your WordPress Dashboard. Remove the default Dashboard Widgets and create your own for a better user experience.
 * Version: 1.0.1
 * Author: Arcane Tech
 * Author URI: https://arcanetechct.com/
 * Text Domain: ats-dashboard
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

// Constants.
define( 'ATS_DASHBOARD_PLUGIN_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
define( 'ATS_DASHBOARD_PLUGIN_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );
define( 'ATS_DASHBOARD_PLUGIN_VERSION', '1.0.1' );
define( 'ATS_DASHBOARD_PLUGIN_FILE', plugin_basename( __FILE__ ) );
define( 'ATS_DASHBOARD_DEFAULT_LOGO_URL', 'https://cdn-r2.arcanetechct.com/LogoWideTransparent.png' );

// Admin menu specific support — must run directly (unhooked) to avoid being overlapped by other plugins.
require_once __DIR__ . '/modules/admin-menu/inc/not-doing-ajax.php';
ats_admin_menu_not_doing_ajax();

require __DIR__ . '/class-setup.php';

ATSDash\Setup::init();
