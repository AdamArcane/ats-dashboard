<?php
/**
 * Save custom login url.
 *
 * @package ATS_Dashboard
 */

namespace ats\OnboardingWizard\Ajax;

/**
 * Class to manage ajax request of migration to ats.
 */
class Save_Custom_Login_Url {

	/**
	 * The filled custom login url.
	 *
	 * @var array
	 */
	private $custom_login_url;

	/**
	 * Class constructor.
	 */
	public function __construct() {

		add_action( 'wp_ajax_ats_onboarding_wizard_save_custom_login_url', [ $this, 'handler' ] );

	}

	/**
	 * The request handler.
	 */
	public function handler() {

		$this->validate();
		$this->save();

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
		if ( ! wp_verify_nonce( $nonce, 'ats_onboarding_wizard_save_custom_login_url_nonce' ) ) {
			wp_send_json_error( __( 'Invalid token', 'ats-dashboard' ), 401 );
		}

		$this->custom_login_url = isset( $_POST['custom_login_url'] ) ? sanitize_text_field( wp_unslash( $_POST['custom_login_url'] ) ) : '';

	}

	/**
	 * Save the data.
	 */
	private function save() {

		$settings = get_option( 'ats_login_redirect', array() );

		$settings['login_url_slug'] = $this->custom_login_url;

		update_option( 'ats_login_redirect', $settings );

		wp_send_json_success( __( 'Login redirect saved', 'ats-dashboard' ) );

	}


}
