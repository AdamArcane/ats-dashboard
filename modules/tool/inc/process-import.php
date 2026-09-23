<?php
/**
 * Import processing.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Tool\Import_Handler;
use ATSDash\Tool\Import_Validator;

require_once dirname( __DIR__ ) . '/class-import-validator.php';
require_once dirname( __DIR__ ) . '/class-import-handler.php';

return function () {

	$import_file = isset( $_FILES['ats_import_file'] ) ? $_FILES['ats_import_file'] : null;

	if ( ! is_array( $import_file ) || ! isset( $import_file['error'], $import_file['name'], $import_file['tmp_name'] ) || UPLOAD_ERR_OK !== $import_file['error'] || ! is_string( $import_file['name'] ) || ! is_string( $import_file['tmp_name'] ) || ! is_uploaded_file( $import_file['tmp_name'] ) ) {

		add_settings_error(
			'ats_export',
			esc_attr( 'ats-import' ),
			__( 'Please select a file to import', 'ats-dashboard' )
		);

		return;

	}

	$file_name = basename( sanitize_file_name( wp_unslash( $import_file['name'] ) ) );
	$explodes  = explode( '.', $file_name );
	$ext       = end( $explodes );

	// wp_check_filetype fails here, so let's check it manually.
	if ( 'json' !== $ext ) {

		add_settings_error(
			'ats_export',
			esc_attr( 'ats-import' ),
			__( 'Please upload a valid .json file', 'ats-dashboard' )
		);

		return;

	}

	$tmp_file = $import_file['tmp_name'];

	if ( empty( $tmp_file ) ) {

		add_settings_error(
			'ats_export',
			esc_attr( 'ats-import' ),
			__( 'Please upload a file to import', 'ats-dashboard' )
		);

		return;

	}

	if ( filesize( $tmp_file ) > 5 * MB_IN_BYTES ) {
		add_settings_error( 'ats_export', 'ats-import', __( 'Import files must be no larger than 5 MB.', 'ats-dashboard' ) );
		return;
	}

	$imports = Import_Validator::decode( file_get_contents( $tmp_file ) );
	if ( is_wp_error( $imports ) ) {
		add_settings_error( 'ats_export', 'ats-import', $imports->get_error_message() );
		return;
	}

	foreach ( Import_Handler::apply( $imports ) as $message ) {
		add_settings_error( 'ats_export', esc_attr( 'ats-import' ), $message, 'updated' );
	}

};
