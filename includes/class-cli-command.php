<?php
/**
 * WP-CLI command for importing ATS Dashboard settings without the admin UI —
 * e.g. so a JSON export can be pushed to many sites at once via MainWP's
 * WP-CLI Terminal / Code Snippets extensions instead of logging into each site.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

require_once ATS_DASHBOARD_PLUGIN_DIR . '/modules/tool/class-import-validator.php';
require_once ATS_DASHBOARD_PLUGIN_DIR . '/modules/tool/class-import-handler.php';

/**
 * Manage ATS Dashboard data via WP-CLI.
 */
class ATS_Dashboard_CLI_Command {

	/**
	 * Import ATS Dashboard settings, widgets, and admin pages from a JSON export file.
	 *
	 * ## OPTIONS
	 *
	 * <file>
	 * : Path to the exported .json file.
	 *
	 * ## EXAMPLES
	 *
	 *     wp atsdash import defaults.json
	 *
	 * @param array $args Positional arguments.
	 */
	public function import( $args ) {
		list( $file ) = $args;

		if ( ! is_readable( $file ) ) {
			\WP_CLI::error( "Cannot read file: {$file}" );
		}

		if ( filesize( $file ) > 5 * MB_IN_BYTES ) {
			\WP_CLI::error( 'Import files must be no larger than 5 MB.' );
		}

		$imports = \ATSDash\Tool\Import_Validator::decode( file_get_contents( $file ) );

		if ( is_wp_error( $imports ) ) {
			\WP_CLI::error( $imports->get_error_message() );
		}

		foreach ( \ATSDash\Tool\Import_Handler::apply( $imports ) as $message ) {
			\WP_CLI::log( $message );
		}

		\WP_CLI::success( 'Import complete.' );
	}
}

\WP_CLI::add_command( 'atsdash', 'ATS_Dashboard_CLI_Command' );
