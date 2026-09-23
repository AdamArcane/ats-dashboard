<?php
/** Isolated security regressions. Run: php tests/security.php (no database or mail). */
define( 'ABSPATH', __DIR__ . '/' );
define( 'ATS_DASHBOARD_PLUGIN_URL', 'https://example.invalid/plugin' );

$caps = array();
$network = false;
$writes = array();
$checks = 0;
function __( $text, $domain = '' ) { return $text; }
function esc_html__( $text, $domain = '' ) { return htmlspecialchars( $text, ENT_QUOTES ); }
function _e( $text, $domain = '' ) { echo $text; }
function current_user_can( $cap, ...$args ) { return ! empty( $GLOBALS['caps'][ $cap ] ); }
function is_multisite() { return $GLOBALS['network']; }
function apply_filters( $name, $value, ...$args ) { return $value; }
function absint( $value ) { return abs( (int) $value ); }
function update_site_option( $key, $value ) { $GLOBALS['writes'][ $key ] = $value; }
function update_post_meta( $id, $key, $value ) { $GLOBALS['writes'][ $key ] = $value; }
function get_post_meta( $id, $key, $single = false ) { return $GLOBALS['saved_meta'][ $key ] ?? ''; }
function get_post_type( $id ) { return 'ats_admin_page'; }
function wp_is_post_autosave( $id ) { return false; }
function wp_is_post_revision( $id ) { return false; }
function wp_verify_nonce( $value, $action ) { return 'valid' === $value; }
function wp_unslash( $value ) { return stripslashes( $value ); }
function sanitize_text_field( $value ) { return strip_tags( $value ); }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_-]/', '', strtolower( $value ) ); }
function wp_strip_all_tags( $value ) { return strip_tags( $value ); }
function esc_textarea( $value ) { return htmlspecialchars( $value, ENT_QUOTES ); }
function wp_kses_allowed_html( $context ) { return array( 'p' => array(), 'strong' => array() ); }
function wp_kses( $value, $allowed ) { return strip_tags( $value, '<' . implode( '><', array_keys( $allowed ) ) . '>' ); }
function wp_kses_post( $value ) { return wp_kses( $value, wp_kses_allowed_html( 'post' ) ); }
function wp_die( ...$args ) { throw new RuntimeException( 'permission denied' ); }
function check_admin_referer( $action ) {
	if ( ! wp_verify_nonce( $_POST['_wpnonce'] ?? '', $action ) ) { throw new RuntimeException( 'nonce denied' ); }
}
// WordPress-like detection only; native PHP performs the actual deserialization under test.
function is_serialized( $value ) {
	return is_string( $value ) && ( 'N;' === $value || preg_match( '/^[aObisdC]:.*[;}s]$/s', trim( $value ) ) );
}
class WP_Error {
	private $message;
	public function __construct( $code, $message ) { $this->message = $message; }
	public function get_error_message() { return $this->message; }
}
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function check( $condition, $message ) {
	if ( ! $condition ) { throw new RuntimeException( $message ); }
	$GLOBALS['checks']++;
}
function denied( $callback, $message ) {
	try { $callback(); } catch ( RuntimeException $e ) { check( true, $message ); return; }
	throw new RuntimeException( $message );
}

require dirname( __DIR__ ) . '/helpers/class-array-helper.php';
require dirname( __DIR__ ) . '/helpers/class-content-base-helper.php';
require dirname( __DIR__ ) . '/modules/base/class-base-module.php';
require dirname( __DIR__ ) . '/modules/tool/class-tool-module.php';
require dirname( __DIR__ ) . '/modules/tool/class-import-validator.php';

use ATSDash\Helpers\Array_Helper;
use ATSDash\Helpers\Content_Base_Helper;
use ATSDash\Tool\Import_Validator;
use ATSDash\Tool\Tool_Module;

class HarmlessObjectProbe {
	public function __wakeup() { $GLOBALS['object_callback_ran'] = true; }
}
$helper = new Array_Helper();
$roles = array( 'administrator', 'editor', 12 );
check( $helper->clean_unserialize( $roles ) === $roles, 'Plain lists survive' );
check( $helper->clean_unserialize( serialize( serialize( serialize( $roles ) ) ), 3 ) === $roles, 'Legacy nested serialization survives' );
check( array() === $helper->clean_unserialize( serialize( new HarmlessObjectProbe() ) ), 'Objects are rejected' );
check( array() === $helper->clean_unserialize( serialize( array( new HarmlessObjectProbe() ) ) ), 'Nested objects are rejected' );
check( empty( $GLOBALS['object_callback_ran'] ), 'No object callbacks execute' );
check( array() === $helper->clean_unserialize( 'not an array' ), 'Scalars cannot become role lists' );
check( array() === $helper->clean_unserialize( 'a:1:{broken}' ), 'Malformed serialization is rejected' );

foreach ( array( '{bad', '[]', 'null', '{"settings":true}', '{"widgets":[{}]}' ) as $json ) {
	check( is_wp_error( Import_Validator::decode( $json ) ), 'Malformed import rejected: ' . $json );
}
$post = array( 'post_name' => 'welcome', 'post_title' => 'Welcome', 'meta' => array( 'ats_widget_roles' => serialize( serialize( $roles ) ) ) );
$decoded = Import_Validator::decode( json_encode( array( 'widgets' => array( $post ) ) ) );
check( ! is_wp_error( $decoded ) && $decoded['widgets'][0]['meta']['ats_widget_roles'] === $roles, 'Legacy JSON import accepted' );
$post['ID'] = 99;
$post['post_type'] = 'post';
$post['meta_input'] = array( 'injected' => 'yes' );
$decoded = Import_Validator::decode( json_encode( array( 'widgets' => array( $post ) ) ) );
check( 'ats_widgets' === $decoded['widgets'][0]['post_type'] && ! isset( $decoded['widgets'][0]['ID'] ) && ! isset( $decoded['widgets'][0]['meta_input'] ), 'Imported posts cannot override type or ID/meta input' );
$post['meta']['ats_widget_roles'] = serialize( array( new HarmlessObjectProbe() ) );
check( is_wp_error( Import_Validator::decode( json_encode( array( 'widgets' => array( $post ) ) ) ) ), 'Object-bearing import rejected before writes' );
check( empty( $GLOBALS['object_callback_ran'] ), 'Import never invokes object callback' );
check( ! is_wp_error( Import_Validator::decode( '{"multisite_settings":{"ats_multisite_blueprint":1}}' ) ), 'Network-only imports accepted' );

$tool = new Tool_Module();
$network = true;
$payload = array( 'multisite_settings' => array( 'site_admins' => array( 'attacker' ), 'ats_multisite_blueprint' => '12', 'ats_multisite_exclude' => '2,3,2', 'ats_multisite_widget_order' => '9', 'ats_multisite_capability' => 'manage_options' ) );
$tool->import_settings( $payload );
check( array() === $writes, 'Site users cannot write network options' );
$caps['manage_network_options'] = true;
$tool->import_settings( $payload );
check( ! isset( $writes['site_admins'] ) && 12 === $writes['ats_multisite_blueprint'], 'Only approved network options are written' );
check( '2,3' === $writes['ats_multisite_exclude'] && 9 === $writes['ats_multisite_widget_order'], 'Network values normalized' );
$writes = array();
$tool->import_settings( array( 'multisite_settings' => array( 'ats_multisite_capability' => 'read', 'ats_multisite_blueprint' => array( 5 ) ) ) );
check( array() === $writes, 'Invalid network values ignored' );
denied( function () use ( $tool ) { $tool->process_import(); }, 'Transfer needs network capability' );
$caps['manage_network'] = true;
denied( function () use ( $tool ) { $tool->process_import(); }, 'Transfer needs nonce' );
check( 'manage_network' === $tool->tools_capability(), 'Multisite Tools restricted' );
$network = false;
check( 'manage_options' === $tool->tools_capability(), 'Single-site Tools capability retained' );

$content = new Content_Base_Helper();
check( ! isset( $content->get_admin_page_html_allowed_tags()['script'] ), 'Restricted authors cannot embed scripts' );
$meta = array( 'ats_custom_js' => 'window.example = true;', 'ats_html_content' => '<p>Safe</p><script>alert(1)</script>', 'ats_custom_css' => '</style><script>alert(1)</script>', 'builder_data' => array( 'preserved' => true ) );
$prepared = Import_Validator::prepare_meta( $meta );
check( ! isset( $prepared['ats_custom_js'] ) && false === strpos( $prepared['ats_html_content'], '<script' ), 'Import enforces JS and HTML permissions' );
check( false === strpos( $prepared['ats_custom_css'], '<' ) && $prepared['builder_data'] === $meta['builder_data'], 'CSS cannot break out; builder metadata preserved' );
$caps['unfiltered_html'] = true;
check( isset( $content->get_admin_page_html_allowed_tags()['script'] ), 'Trusted authors retain script embeds' );
check( Import_Validator::prepare_meta( $meta )['ats_custom_js'] === $meta['ats_custom_js'], 'Trusted JS import retained' );

$save = require dirname( __DIR__ ) . '/modules/admin-page/inc/save-post.php';
$caps = array( 'edit_post' => true );
$_POST = array( '_wpnonce' => 'valid', 'ats_custom_js' => 'window.example = true;' );
$writes = array();
$save( $tool, 123 );
check( ! isset( $writes['ats_custom_js'] ), 'Saving page cannot bypass JS permission' );
$caps['unfiltered_html'] = true;
$save( $tool, 123 );
check( 'window.example = true;' === $writes['ats_custom_js'], 'Trusted page JS can be saved' );
$writes = array();
$_POST['_wpnonce'] = 'invalid';
$save( $tool, 123 );
check( array() === $writes, 'Page save requires nonce' );

$render = require dirname( __DIR__ ) . '/modules/admin-page/templates/metaboxes/custom-js.php';
$GLOBALS['saved_meta'] = array( 'ats_custom_js' => '</textarea><script>alert(1)</script>' );
ob_start(); $render( (object) array( 'ID' => 123 ) ); $html = ob_get_clean();
check( false === strpos( $html, '<script>' ) && false !== strpos( $html, '&lt;/textarea&gt;' ), 'JS editor escapes textarea breakout' );
$caps = array();
ob_start(); $render( (object) array( 'ID' => 123 ) ); $html = ob_get_clean();
check( '' === $html, 'Restricted authors do not see JS editor' );

echo "Passed {$checks} isolated security checks.\n";
