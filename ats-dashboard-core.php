<?php
/**
 * Embedded ATS Dashboard core.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

// Plugin constants.
define( 'ATS_DASHBOARD_CORE_DIR', ATS_DASHBOARD_PLUGIN_DIR );
define( 'ATS_DASHBOARD_CORE_URL', ATS_DASHBOARD_PLUGIN_URL );

// Admin menu specific support.
require_once __DIR__ . '/modules/admin-menu/inc/not-doing-ajax.php';
ats_admin_menu_not_doing_ajax();

// Helper classes.
require __DIR__ . '/helpers/class-screen-helper.php';
require __DIR__ . '/helpers/class-color-helper.php';
require __DIR__ . '/helpers/class-ats-core-widget-helper.php';
require __DIR__ . '/helpers/class-ats-core-content-helper.php';
require __DIR__ . '/helpers/class-user-helper.php';
require __DIR__ . '/helpers/class-array-helper.php';
require __DIR__ . '/helpers/class-admin-bar-helper.php';

// Base module.
require __DIR__ . '/modules/base/class-base-module.php';
require __DIR__ . '/modules/base/class-base-output.php';
require __DIR__ . '/modules/admin-bar/class-admin-bar-base.php';
require __DIR__ . '/modules/admin-page/class-admin-page-base.php';
require __DIR__ . '/modules/widget/class-widget-base.php';
require __DIR__ . '/modules/branding/class-branding-base-output.php';
require __DIR__ . '/modules/login-customizer/class-login-customizer-base.php';

// Core classes.
require __DIR__ . '/class-ats-core-backwards-compatibility.php';
require __DIR__ . '/class-ats-core-vars.php';
require __DIR__ . '/class-ats-core-setup.php';


ats\Backwards_Compatibility::init();
ats\Setup::init();
