<?php
/**
 * Integrations output.
 *
 * @package ATS_Dashboard
 */

namespace ats\Integrations;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Base\Base_Output;

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
	 */
	public function setup() {}

	/**
	 * Get the MainWP site ID, resolved against any manual override.
	 *
	 * @return array {
	 *     @type string $value       The resolved value.
	 *     @type bool   $is_override Whether the value came from a manual override.
	 * }
	 */
	public function get_mainwp_site_id() {

		$overrides      = get_option( 'ats_integrations', array() );
		$override_value = isset( $overrides['mainwp_site_id_override'] ) ? trim( $overrides['mainwp_site_id_override'] ) : '';

		if ( '' !== $override_value ) {
			return array(
				'value'       => $override_value,
				'is_override' => true,
			);
		}

		return array(
			'value'       => (string) get_option( 'arcane_atc_mainwp_site_id', '' ),
			'is_override' => false,
		);

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
