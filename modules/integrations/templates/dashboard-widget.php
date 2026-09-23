<?php
/**
 * System Links dashboard widget.
 *
 * Ports the design and logic of the "System Links" widget in the ArcaneTech
 * core plugin (inc/dashboard.php), sourced from this plugin's own
 * Integrations_Output (MainWP-pushed data resolved against manual overrides)
 * instead of the core plugin's read-only option wrappers.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Integrations\Integrations_Output;

// SuiteDash's API doesn't expose the numeric record ID its portal URLs use,
// so a specific company record can't be linked to directly — see the note
// below. This is the best available fallback.
if ( ! defined( 'ATS_DASHBOARD_SUITEDASH_COMPANY_LIST_URL' ) ) {
	define( 'ATS_DASHBOARD_SUITEDASH_COMPANY_LIST_URL', 'https://portal.arcanetechct.com/crmCompany/admin' );
}

return function () {

	$output = Integrations_Output::get_instance();

	$mainwp_site_id    = $output->get_mainwp_site_id();
	$ploi_status       = $output->get_ploi_status();
	$suitedash_status  = $output->get_suitedash_status();
	$postmark          = $output->get_postmark_status();

	$mainwp_id = $mainwp_site_id['value'];
	// The raw MainWP-pushed data — includes fields (server_id, site_id,
	// disk_usage_human, security_updates, server_php_version, tags,
	// category, website) that aren't part of the manual-override set and so
	// aren't exposed via ['fields'].
	$ploi = $ploi_status['pushed'];
	$sd   = $suitedash_status['pushed'];

	$has_data = $mainwp_id || ! empty( $sd ) || ! empty( $ploi ) || $postmark;

	if ( ! $has_data ) {
		echo '<p class="ats-system-links-empty">' . esc_html__( 'Nothing pushed from MainWP yet. Match this site in the Arcane Tech extension on the MainWP dashboard, then push.', 'ats-dashboard' ) . '</p>';
		return;
	}

	// ── Customer card (top) ───────────────────────────────────────────────
	// SuiteDash's public API returns a UUID ('uid'), not the numeric record ID
	// its own portal URLs use (e.g. /crmContacts/7374096) — that numeric ID
	// isn't exposed anywhere in the API response, so a direct link to this
	// specific company record can't be constructed from pushed data. Fall
	// back to the company list in the portal instead.
	if ( ! empty( $sd ) ) {
		echo '<div class="ats-customer-card">';

		$co_name = $suitedash_status['fields']['company_name']['value'];
		$website = $sd['website'] ?? '';

		echo '<div class="ats-system-link-label" style="margin-bottom:4px;">' . esc_html__( 'Customer Profile', 'ats-dashboard' ) . '</div>';
		echo '<div class="ats-customer-name">';
		echo '<a href="' . esc_url( ATS_DASHBOARD_SUITEDASH_COMPANY_LIST_URL ) . '" target="_blank" rel="noopener">' . esc_html( $co_name ) . ' &nearr;</a>';
		echo '</div>';

		$contact_name  = $suitedash_status['fields']['contact_name']['value'];
		$contact_email = $suitedash_status['fields']['contact_email']['value'];

		if ( $contact_name ) {
			echo '<div class="ats-customer-contact">' . esc_html( $contact_name ) . '</div>';
		}
		if ( $contact_email ) {
			echo '<div class="ats-customer-contact"><a href="mailto:' . esc_attr( $contact_email ) . '">' . esc_html( $contact_email ) . '</a></div>';
		}

		$tags   = array_filter( array_map( 'trim', explode( ',', (string) ( $sd['tags'] ?? '' ) ) ) );
		$badges = array_filter( array_merge( ! empty( $sd['category'] ) ? array( $sd['category'] ) : array(), $tags ) );
		if ( $badges ) {
			echo '<div class="ats-customer-badges">';
			foreach ( $badges as $badge ) {
				echo '<span class="ats-customer-badge">' . esc_html( $badge ) . '</span>';
			}
			echo '</div>';
		}

		if ( $website ) {
			echo '<div class="ats-customer-website"><a href="' . esc_url( $website ) . '" target="_blank" rel="noopener">' . esc_html( preg_replace( '#^https?://#', '', rtrim( $website, '/' ) ) ) . '</a></div>';
		}

		echo '</div>';
		echo '<div class="ats-system-links-divider"></div>';
	}

	// ── Service links ─────────────────────────────────────────────────────
	$row = function ( $label, $name, $meta, $url ) {
		echo '<li class="ats-system-link-row">';
		echo '<div class="ats-system-link-main">';
		echo '<span class="ats-system-link-label">' . esc_html( $label ) . '</span>';
		if ( $url ) {
			echo '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener" class="ats-system-link-name">' . esc_html( $name ) . ' &nearr;</a>';
		} else {
			echo '<span class="ats-system-link-name">' . esc_html( $name ) . '</span>';
		}
		echo '</div>';
		if ( $meta ) {
			echo '<span class="ats-system-link-meta">' . esc_html( $meta ) . '</span>';
		}
		echo '</li>';
	};

	echo '<ul class="ats-system-links">';

	if ( ! empty( $ploi ) ) {
		$url        = ! empty( $ploi['server_id'] )
			? 'https://ploi.io/servers/' . rawurlencode( (string) $ploi['server_id'] )
			: null;
		$meta_parts = array_filter( array( $ploi['server_ip'] ?? null, ! empty( $ploi['server_php_version'] ) ? 'PHP ' . $ploi['server_php_version'] : null ) );
		$row( __( 'Ploi Server', 'ats-dashboard' ), $ploi_status['fields']['server_name']['value'] ?: __( 'View Server', 'ats-dashboard' ), implode( ' · ', $meta_parts ), $url );

		$site_url   = ( ! empty( $ploi['server_id'] ) && ! empty( $ploi['site_id'] ) )
			? 'https://ploi.io/servers/' . rawurlencode( (string) $ploi['server_id'] ) . '/sites/' . rawurlencode( (string) $ploi['site_id'] )
			: null;
		$meta_parts = array_filter( array( $ploi['disk_usage_human'] ?? null, ! empty( $ploi['security_updates'] ) ? $ploi['security_updates'] . ' ' . __( 'security update(s)', 'ats-dashboard' ) : null ) );
		$row( __( 'Ploi Site', 'ats-dashboard' ), $ploi_status['fields']['domain']['value'] ?: __( 'View Site', 'ats-dashboard' ), implode( ' · ', $meta_parts ), $site_url );
	} else {
		$row( __( 'Ploi', 'ats-dashboard' ), __( 'Not configured', 'ats-dashboard' ), '', null );
	}

	if ( $postmark && ! empty( $postmark['has_detail'] ) ) {
		$stream     = ! empty( $postmark['message_stream'] ) ? $postmark['message_stream'] : 'outbound';
		$url        = ! empty( $postmark['postmark_server_id'] )
			? 'https://account.postmarkapp.com/servers/' . rawurlencode( (string) $postmark['postmark_server_id'] ) . '/streams/' . rawurlencode( $stream ) . '/events'
			: null;
		$meta_parts = array_filter( array( $stream, __( 'via MainWP', 'ats-dashboard' ) ) );
		$row( __( 'Postmark', 'ats-dashboard' ), ! empty( $postmark['server_name'] ) ? $postmark['server_name'] : __( 'Mail Server', 'ats-dashboard' ), implode( ' · ', $meta_parts ), $url );
	} elseif ( $postmark ) {
		$row( __( 'Postmark', 'ats-dashboard' ), __( 'Managed via MainWP', 'ats-dashboard' ), __( 'Awaiting next config push for details', 'ats-dashboard' ), null );
	} else {
		$row( __( 'Postmark', 'ats-dashboard' ), __( 'Not configured', 'ats-dashboard' ), '', null );
	}

	if ( $mainwp_id ) {
		$url = 'https://siteman.arcanetech.cloud/wp-admin/admin.php?page=managesites&id=' . rawurlencode( $mainwp_id );
		$row( __( 'MainWP', 'ats-dashboard' ), __( 'Manage Site', 'ats-dashboard' ), '', $url );
	} else {
		$row( __( 'MainWP', 'ats-dashboard' ), __( 'Not configured', 'ats-dashboard' ), '', null );
	}

	echo '</ul>';

	echo '<p class="ats-system-links-footer">';
	echo '<a href="' . esc_url( admin_url( 'admin.php?page=ats_integrations' ) ) . '">' . esc_html__( 'View integration details', 'ats-dashboard' ) . ' &rarr;</a>';
	echo '</p>';

};
