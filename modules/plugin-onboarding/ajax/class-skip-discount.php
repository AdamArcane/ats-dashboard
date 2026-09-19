<?php
/**
 * Skip discount.
 *
 * @package ATS_Dashboard
 */

namespace ats\PluginOnboarding\Ajax;

/**
 * Class to manage ajax request of migration to ats.
 */
class SkipDiscount {

	/**
	 * The referrer where ats was installed from.
	 *
	 * @var string
	 */
	private $referrer;

	/**
	 * Class constructor.
	 */
	public function __construct() {

		add_action( 'wp_ajax_ats_plugin_onboarding_skip_discount', [ $this, 'handler' ] );

	}

	/**
	 * The request handler.
	 */
	public function handler() {

		$this->validate();
		$this->skip_discount();

	}

	/**
	 * Validate the data.
	 */
	private function validate() {

		$capability = apply_filters( 'ats_modules_capability', 'manage_options' );

		if ( ! current_user_can( $capability ) ) {
			wp_send_json_error( __( 'You do not have permission to perform this action', 'ats-dashboard' ), 401 );
		}

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		// Check if nonce is incorrect.
		if ( ! wp_verify_nonce( $nonce, 'ats_plugin_onboarding_skip_discount_nonce' ) ) {
			wp_send_json_error( __( 'Invalid token', 'ats-dashboard' ), 401 );
		}

		$this->referrer = isset( $_POST['referrer'] ) ? sanitize_text_field( wp_unslash( $_POST['referrer'] ) ) : '';

	}

	/**
	 * Save the data.
	 */
	private function skip_discount() {

		if ( 'erident' === $this->referrer ) {
			delete_option( 'ats_migration_from_erident' );
		} elseif ( 'kirki' === $this->referrer ) {
			delete_option( 'ats_referred_by_kirki' );
		}

		wp_send_json_success( __( 'Discount skipped', 'ats-dashboard' ) );

	}

}
