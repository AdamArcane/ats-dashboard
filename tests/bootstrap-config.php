<?php
/** Test the real plugin entry point without running WordPress or making requests. */
define( 'ABSPATH', __DIR__ . '/' );
$mode = $argv[1] ?? 'default';
if ( 'disabled' === $mode ) {
	define( 'ATS_DASHBOARD_UPDATES_ENABLED', false );
}
if ( 'custom' === $mode ) {
	define( 'ATS_DASHBOARD_UPDATE_MANIFEST_URL', 'https://example.invalid/info.json' );
	define( 'ATS_DASHBOARD_DEFAULT_LOGO_URL', 'https://example.invalid/logo.png' );
}
function plugin_dir_path( $file ) { return dirname( $file ) . '/'; }
function plugin_dir_url( $file ) { return 'https://example.invalid/plugin/'; }
function plugin_basename( $file ) { return 'ats-dashboard/' . basename( $file ); }
function is_admin() { return true; }
function wp_doing_ajax() { return false; }
function add_action( ...$args ) {}
function add_filter( $hook, $callback, ...$args ) { $GLOBALS['hooks'][ $hook ] = $callback; }
require dirname( __DIR__ ) . '/ats-dashboard.php';
$registered = isset( $GLOBALS['hooks']['pre_set_site_transient_update_plugins'] );
if ( $registered !== ( 'disabled' !== $mode ) ) { throw new RuntimeException( 'Update opt-out did not control registration' ); }
if ( $registered ) {
	$updater = $GLOBALS['hooks']['pre_set_site_transient_update_plugins'][0];
	$property = new ReflectionProperty( $updater, 'manifest_url' );
	$property->setAccessible( true );
	$expected = 'custom' === $mode ? 'https://example.invalid/info.json' : 'https://files.arcanetechct.com/ats-dashboard/info.json';
	if ( $expected !== $property->getValue( $updater ) ) { throw new RuntimeException( 'Manifest configuration not honored' ); }
}
if ( 'custom' === $mode && 'https://example.invalid/logo.png' !== ATS_DASHBOARD_DEFAULT_LOGO_URL ) { throw new RuntimeException( 'Logo override not honored' ); }
echo "Passed {$mode} bootstrap configuration.\n";
