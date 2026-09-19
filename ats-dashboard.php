<?php
/**
 * Plugin Name: ATS Dashboard
 * Plugin URI: https://arcanetechct.com/
 * Description: ATS Dashboard gives you full control over your WordPress Dashboard. Remove the default Dashboard Widgets and create your own for a better user experience.
 * Version: 3.11.2
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
define( 'ATS_DASHBOARD_PLUGIN_VERSION', '3.11.2' );
define( 'ATS_DASHBOARD_PLUGIN_FILE', plugin_basename( __FILE__ ) );

require __DIR__ . '/ats-dashboard-core.php';
require __DIR__ . '/class-backwards-compatibility.php';
require __DIR__ . '/class-setup.php';

ATSDash\Setup::init();
