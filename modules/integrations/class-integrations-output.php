<?php
/**
 * Integrations output.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Integrations;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Base\Base_Output;

/**
 * Class to set up integrations output.
 *
 * Resolves MainWP-pushed integration data (site id, Ploi hosting status,
 * SuiteDash CRM status) against the manual overrides stored in the
 * `ats_integrations` option, so this data can be reused elsewhere in the
 * dashboard (e.g. a future dashboard widget) without duplicating the
 * override-resolution logic in every consumer.
 */
class Integrations_Output extends Base_Output {

	/**
	 * The class instance.
	 *
	 * @var object
	 */
	public static $instance = null;

	/**
	 * Get instance of the class.
	 *
	 * @return self
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Init the class setup.
	 */
	public static function init() {

		self::get_instance()->setup();

	}

	/**
	 * Setup integrations output.
	 *
	 * Wires this module's data into the widget placeholder tag system so it
	 * can be used in custom dashboard widgets and custom admin pages.
	 */
	public function setup() {

		add_filter( 'ats_widgets_placeholder_tags', array( $this, 'filter_placeholder_tags' ) );
		add_filter( 'ats_widgets_convert_placeholder_tags', array( $this, 'filter_convert_placeholder_tags' ) );

	}

	/**
	 * Append the integrations placeholder tags that currently have data to the
	 * displayed placeholder tag list, so a tag only shows up as available once
	 * MainWP (or another integration) has actually pushed a value for it.
	 *
	 * @param array $tags The existing placeholder tags.
	 * @return array The placeholder tags with the available integrations tags appended.
	 */
	public function filter_placeholder_tags( $tags ) {

		return array_merge( $tags, $this->get_available_placeholder_tags() );

	}

	/**
	 * Replace integrations placeholder tags with their resolved values.
	 *
	 * @param string $str The string to replace the tags in.
	 * @return string The modified string.
	 */
	public function filter_convert_placeholder_tags( $str ) {

		$map = $this->get_placeholder_map();

		return str_replace( array_keys( $map ), array_values( $map ), $str );

	}

	/**
	 * Get every integrations placeholder tag mapped to its current resolved
	 * value (manual override, if set, otherwise the MainWP-pushed value).
	 * A tag with no data available yet resolves to an empty string.
	 *
	 * @return array Tag (e.g. "{mainwp_site_id}") => string value.
	 */
	public function get_placeholder_map() {

		$map = array();

		$mainwp_site_id           = $this->get_mainwp_site_id();
		$map['{mainwp_site_id}'] = (string) $mainwp_site_id['value'];

		$ploi = $this->get_ploi_status();

		foreach ( $ploi['fields'] as $key => $field ) {
			$map[ '{ploi_' . $key . '}' ] = (string) $field['value'];
		}

		$suitedash = $this->get_suitedash_status();

		foreach ( $suitedash['fields'] as $key => $field ) {
			$map[ '{suitedash_' . $key . '}' ] = (string) $field['value'];
		}

		$postmark = $this->get_postmark_status();

		if ( $postmark && ! empty( $postmark['has_detail'] ) ) {
			$map['{postmark_server_name}']    = ! empty( $postmark['server_name'] ) ? $postmark['server_name'] : '';
			$map['{postmark_message_stream}'] = ! empty( $postmark['message_stream'] ) ? $postmark['message_stream'] : 'outbound';
			$map['{postmark_sender_email}']   = ! empty( $postmark['sender_email'] ) ? $postmark['sender_email'] : get_option( 'admin_email' );
		} else {
			$map['{postmark_server_name}']    = '';
			$map['{postmark_message_stream}'] = '';
			$map['{postmark_sender_email}']   = '';
		}

		return $map;

	}

	/**
	 * Get only the integrations placeholder tags that currently resolve to a
	 * non-empty value, so the tag picker UI doesn't advertise tags that would
	 * just render blank.
	 *
	 * @return string[] Placeholder tags, e.g. array( "{mainwp_site_id}", "{ploi_domain}" ).
	 */
	public function get_available_placeholder_tags() {

		$available = array();

		foreach ( $this->get_placeholder_map() as $tag => $value ) {
			if ( '' !== trim( $value ) ) {
				$available[] = $tag;
			}
		}

		return $available;

	}

	/**
	 * Get the MainWP site ID, resolved against any manual override.
	 *
	 * @return array {
	 *     @type string $value       The resolved value.
	 *     @type bool   $is_override Whether the value came from a manual override.
	 *     @type string $pushed      The raw MainWP-pushed value, ignoring any override.
	 * }
	 */
	public function get_mainwp_site_id() {

		$overrides      = get_option( 'ats_integrations', array() );
		$override_value = isset( $overrides['mainwp_site_id_override'] ) ? trim( $overrides['mainwp_site_id_override'] ) : '';
		$pushed_value   = (string) get_option( 'arcane_atc_mainwp_site_id', '' );

		if ( '' !== $override_value ) {
			return array(
				'value'       => $override_value,
				'is_override' => true,
				'pushed'      => $pushed_value,
			);
		}

		return array(
			'value'       => $pushed_value,
			'is_override' => false,
			'pushed'      => $pushed_value,
		);

	}

	/**
	 * Get the URL to manage this site on the MainWP dashboard, if MainWP has
	 * been matched to this site (site ID pushed or manually overridden) and
	 * a dashboard base URL is available (auto-detected or overridden).
	 *
	 * @return string The manage URL, or an empty string if either piece is missing.
	 */
	public function get_mainwp_manage_url() {

		$mainwp_site_id = $this->get_mainwp_site_id();
		$base_url       = $this->get_mainwp_base_url();

		if ( empty( $mainwp_site_id['value'] ) || '' === $base_url ) {
			return '';
		}

		return $base_url . '/wp-admin/admin.php?page=managesites&dashboard=' . rawurlencode( $mainwp_site_id['value'] );

	}

	/**
	 * Get the MainWP dashboard's base URL (scheme + host, no trailing slash),
	 * resolved against a manual override.
	 *
	 * Auto-detected from the MainWP Child plugin's own connection record
	 * (`mainwp_child_server` — the dashboard this site is connected to) when
	 * available, so this doesn't need to be entered by hand on sites that are
	 * already connected to MainWP. The manual override exists for sites
	 * where MainWP Child isn't installed/active, or to correct a value.
	 *
	 * @return string The base URL, or an empty string if neither an override
	 *                nor an auto-detected value is available.
	 */
	public function get_mainwp_base_url() {

		$overrides      = get_option( 'ats_integrations', array() );
		$override_value = isset( $overrides['mainwp_base_url'] ) ? trim( $overrides['mainwp_base_url'] ) : '';

		if ( '' !== $override_value ) {
			return untrailingslashit( $override_value );
		}

		return $this->detect_mainwp_base_url();

	}

	/**
	 * Get the auto-detected MainWP dashboard base URL, ignoring any manual
	 * override — for showing alongside the override field in settings.
	 *
	 * @return string
	 */
	public function get_mainwp_base_url_detected() {

		return $this->detect_mainwp_base_url();

	}

	/**
	 * Read the MainWP dashboard URL the MainWP Child plugin has this site
	 * connected to (`mainwp_child_server`, encrypted at rest), and reduce it
	 * to just scheme + host (+ port) — no admin path, query, user, etc.
	 *
	 * @return string
	 */
	private function detect_mainwp_base_url() {

		if ( ! class_exists( '\MainWP\Child\MainWP_Child_Keys_Manager' ) ) {
			return '';
		}

		$server = \MainWP\Child\MainWP_Child_Keys_Manager::get_encrypted_option( 'mainwp_child_server', '' );

		if ( ! is_string( $server ) || '' === $server ) {
			return '';
		}

		$parts = wp_parse_url( $server );

		if ( empty( $parts['scheme'] ) || empty( $parts['host'] ) || ! in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true ) ) {
			return '';
		}

		$port = isset( $parts['port'] ) ? ':' . $parts['port'] : '';

		return $parts['scheme'] . '://' . $parts['host'] . $port;

	}

	/**
	 * Get the MainWP-pushed Ploi hosting status, resolved against manual overrides.
	 *
	 * @return array {
	 *     @type bool  $has_data Whether MainWP has ever pushed data for this site.
	 *     @type array $fields   Field key => array( 'value' => ..., 'is_override' => bool ).
	 * }
	 */
	public function get_ploi_status() {

		$pushed    = get_option( 'arcane_atc_ploi_status', array() );
		$pushed    = is_array( $pushed ) ? $pushed : array();
		$overrides = get_option( 'ats_integrations', array() );
		$overrides = isset( $overrides['ploi_status_override'] ) && is_array( $overrides['ploi_status_override'] ) ? $overrides['ploi_status_override'] : array();

		$fields = array( 'server_name', 'server_ip', 'php_version', 'domain', 'status' );
		$result = array();

		foreach ( $fields as $field ) {
			$override_value = isset( $overrides[ $field ] ) ? trim( $overrides[ $field ] ) : '';

			if ( '' !== $override_value ) {
				$result[ $field ] = array(
					'value'       => $override_value,
					'is_override' => true,
				);
			} else {
				$result[ $field ] = array(
					'value'       => isset( $pushed[ $field ] ) ? $pushed[ $field ] : '',
					'is_override' => false,
				);
			}
		}

		return array(
			'has_data' => ! empty( $pushed ),
			'fields'   => $result,
			'pushed'   => $pushed,
		);

	}

	/**
	 * Get the MainWP-pushed SuiteDash status, resolved against manual overrides.
	 *
	 * @return array {
	 *     @type bool  $has_data Whether MainWP has ever pushed data for this site.
	 *     @type array $fields   Field key => array( 'value' => ..., 'is_override' => bool ).
	 * }
	 */
	public function get_suitedash_status() {

		$pushed    = get_option( 'arcane_atc_suitedash_status', array() );
		$pushed    = is_array( $pushed ) ? $pushed : array();
		$overrides = get_option( 'ats_integrations', array() );
		$overrides = isset( $overrides['suitedash_status_override'] ) && is_array( $overrides['suitedash_status_override'] ) ? $overrides['suitedash_status_override'] : array();

		$fields = array( 'company_name', 'company_uuid', 'contact_name', 'contact_email' );
		$result = array();

		foreach ( $fields as $field ) {
			$override_value = isset( $overrides[ $field ] ) ? trim( $overrides[ $field ] ) : '';

			if ( '' !== $override_value ) {
				$result[ $field ] = array(
					'value'       => $override_value,
					'is_override' => true,
				);
			} else {
				$result[ $field ] = array(
					'value'       => isset( $pushed[ $field ] ) ? $pushed[ $field ] : '',
					'is_override' => false,
				);
			}
		}

		return array(
			'has_data' => ! empty( $pushed ),
			'fields'   => $result,
			'pushed'   => $pushed,
		);

	}

	/**
	 * Detect the MainWP-managed Postmark mu-plugin and return what we know about it.
	 *
	 * This never talks to Postmark directly — a separate MainWP-side extension
	 * owns the credentials and pushes a self-contained mu-plugin
	 * (wp-content/mu-plugins/mainwp-postmark.php) that defines the
	 * ARCANE_POSTMARK_CONFIG constant this method reads.
	 *
	 * @return array|false False if no mu-plugin is present, otherwise the config array
	 *                      (with `has_detail` indicating whether display metadata is available).
	 */
	public function get_postmark_status() {

		$mu_file = WP_CONTENT_DIR . '/mu-plugins/mainwp-postmark.php';

		if ( ! file_exists( $mu_file ) ) {
			return false;
		}

		if ( defined( 'ARCANE_POSTMARK_CONFIG' ) && is_array( ARCANE_POSTMARK_CONFIG ) ) {
			return array_merge( array( 'has_detail' => true ), ARCANE_POSTMARK_CONFIG );
		}

		return array(
			'has_detail' => false,
			'file_mtime' => filemtime( $mu_file ),
		);

	}

}
