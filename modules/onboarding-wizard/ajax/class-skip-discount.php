<?php
/**
 * Skip discount.
 *
 * @package ATS_Dashboard
 */

namespace ats\OnboardingWizard\Ajax;

/**
 * Class to manage ajax request of migration to ats.
 */
class SkipDiscount {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		add_action( 'wp_ajax_ats_onboarding_wizard_skip_discount', [ $this, 'handler' ] );

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
		if ( ! wp_verify_nonce( $nonce, 'ats_onboarding_wizard_skip_discount_nonce' ) ) {
			wp_send_json_error( __( 'Invalid token', 'ats-dashboard' ), 401 );
		}

	}

	/**
	 * Save the data.
	 */
	private function skip_discount() {

		update_option( 'ats_onboarding_wizard_completed', 1 );
		wp_send_json_success( __( 'Discount skipped', 'ats-dashboard' ) );

	}

}
