<?php
/**
 * Add customizer sections.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $wp_customize ) {

	$wp_customize->add_section(
		'ats_login_customizer_template_section',
		array(
			'title' => __( 'Layouts', 'ats-dashboard' ),
			'panel' => 'ats_login_customizer_panel',
		)
	);

	$wp_customize->add_section(
		'ats_login_customizer_logo_section',
		array(
			'title' => __( 'Logo', 'ats-dashboard' ),
			'panel' => 'ats_login_customizer_panel',
		)
	);

	$wp_customize->add_section(
		'ats_login_customizer_bg_section',
		array(
			'title' => __( 'Background', 'ats-dashboard' ),
			'panel' => 'ats_login_customizer_panel',
		)
	);

	$wp_customize->add_section(
		'ats_login_customizer_layout_section',
		array(
			'title' => __( 'Login Form', 'ats-dashboard' ),
			'panel' => 'ats_login_customizer_panel',
		)
	);

	$wp_customize->add_section(
		'ats_login_customizer_fields_section',
		array(
			'title' => __( 'Input Fields', 'ats-dashboard' ),
			'panel' => 'ats_login_customizer_panel',
		)
	);

	$wp_customize->add_section(
		'ats_login_customizer_labels_section',
		array(
			'title' => __( 'Labels', 'ats-dashboard' ),
			'panel' => 'ats_login_customizer_panel',
		)
	);

	$wp_customize->add_section(
		'ats_login_customizer_button_section',
		array(
			'title' => __( 'Log In Button', 'ats-dashboard' ),
			'panel' => 'ats_login_customizer_panel',
		)
	);

	$wp_customize->add_section(
		'ats_login_customizer_form_footer_section',
		array(
			'title' => __( 'Footer', 'ats-dashboard' ),
			'panel' => 'ats_login_customizer_panel',
		)
	);

	$wp_customize->add_section(
		'ats_login_customizer_welcome_messages_section',
		array(
			'title' => __( 'Welcome Messages', 'ats-dashboard' ),
			'panel' => 'ats_login_customizer_panel',
		)
	);

	$wp_customize->add_section(
		'ats_login_customizer_error_messages_section',
		array(
			'title' => __( 'Error Messages', 'ats-dashboard' ),
			'panel' => 'ats_login_customizer_panel',
		)
	);

	$wp_customize->add_section(
		'ats_login_customizer_redirect_section',
		array(
			'title' => __( 'Redirect', 'ats-dashboard' ),
			'panel' => 'ats_login_customizer_panel',
		)
	);

	$wp_customize->add_section(
		'ats_login_customizer_custom_css_js_section',
		array(
			'title' => __( 'Custom CSS', 'ats-dashboard' ),
			'panel' => 'ats_login_customizer_panel',
		)
	);

};
