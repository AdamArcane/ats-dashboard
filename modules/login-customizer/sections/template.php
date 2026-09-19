<?php
/**
 * Templates section of Login Customizer.
 *
 * @var $wp_customize This variable is brought from login-customizer.php file.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\ats_Customize_Login_Template_Control;

$wp_customize->add_setting(
	'ats_login[template]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	new ats_Customize_Login_Template_Control(
		$wp_customize,
		'ats_login[template]',
		array(
			'label'    => __( 'Layouts', 'ats-dashboard' ),
			'section'  => 'ats_login_customizer_template_section',
			'settings' => 'ats_login[template]',
		)
	)
);
