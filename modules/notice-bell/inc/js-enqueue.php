<?php
/**
 * JS Enqueue.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( ! is_admin_bar_showing() ) {
		return;
	}

	wp_enqueue_script( 'ats-notice-bell', $module->url . '/assets/js/notice-bell.js', array(), ATS_DASHBOARD_PLUGIN_VERSION, true );

	wp_localize_script(
		'ats-notice-bell',
		'atsNoticeBell',
		array(
			'holderId' => \ATSDash\NoticeBell\Notice_Bell_Module::HOLDER_ID,
			'i18n'     => array(
				'empty' => __( 'No notifications', 'ats-dashboard' ),
			),
		)
	);

};
