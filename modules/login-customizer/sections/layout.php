<?php
/**
 * Layout section of Login Customizer.
 *
 * @var $wp_customize This variable is brought from login-customizer.php file.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\ats_Customize_Control;
use ats\ats_Customize_Range_Control;

$wp_customize->add_setting(
	'ats_login[form_position]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => 'default',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	new ats_Customize_Control(
		$wp_customize,
		'ats_login[form_position]',
		array(
			'type'     => 'select',
			'section'  => 'ats_login_customizer_layout_section',
			'settings' => 'ats_login[form_position]',
			'priority' => 7, // Default is 10, but we need to place this on top of section.
			'label'    => __( 'Layout', 'ats-dashboard' ),
			'choices'  => array(
				'left'    => __( 'Left', 'ats-dashboard' ),
				'default' => __( 'Default', 'ats-dashboard' ),
				'right'   => __( 'Right', 'ats-dashboard' ),
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[box_width]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '40%',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'esc_attr',
	)
);

$wp_customize->add_control(
	new ats_Customize_Range_Control(
		$wp_customize,
		'ats_login[box_width]',
		array(
			'type'        => 'range',
			'section'     => 'ats_login_customizer_layout_section',
			'settings'    => 'ats_login[box_width]',
			'label'       => __( 'Box Width', 'ats-dashboard' ),
			'input_attrs' => array(
				'min'  => 30,
				'max'  => 100,
				'step' => 1,
			),
		)
	)
);
