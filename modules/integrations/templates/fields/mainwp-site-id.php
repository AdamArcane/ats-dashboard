<?php
/**
 * MainWP site ID field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Integrations\Integrations_Output;

return function () {

	$resolved = Integrations_Output::get_instance()->get_mainwp_site_id();
	$overrides = get_option( 'ats_integrations', array() );
	$override_value = isset( $overrides['mainwp_site_id_override'] ) ? $overrides['mainwp_site_id_override'] : '';
	?>

	<p>
		<?php if ( $resolved['is_override'] ) : ?>
			<span class="ats-integrations-badge ats-integrations-badge--override"><?php esc_html_e( 'Manually overridden', 'ats-dashboard' ); ?></span>
		<?php elseif ( '' !== $resolved['value'] ) : ?>
			<span class="ats-integrations-badge ats-integrations-badge--live"><?php esc_html_e( 'Pushed by MainWP', 'ats-dashboard' ); ?></span>
		<?php else : ?>
			<span class="ats-integrations-badge ats-integrations-badge--empty"><?php esc_html_e( 'Not set by MainWP yet', 'ats-dashboard' ); ?></span>
		<?php endif; ?>
		<br>
		<strong><?php echo esc_html( '' !== $resolved['value'] ? $resolved['value'] : __( '(none)', 'ats-dashboard' ) ); ?></strong>
	</p>

	<div class="ats-integrations-overrides">
		<h4><?php esc_html_e( 'Manual Override', 'ats-dashboard' ); ?></h4>
		<input type="text" name="ats_integrations[mainwp_site_id_override]" class="regular-text" value="<?php echo esc_attr( $override_value ); ?>" placeholder="<?php esc_attr_e( 'Leave blank to use the MainWP-pushed value', 'ats-dashboard' ); ?>" />
		<p class="description"><?php esc_html_e( 'Useful before MainWP has matched this site, or to correct a stale value.', 'ats-dashboard' ); ?></p>
	</div>

	<?php

};
