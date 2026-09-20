<?php
/**
 * Form footer section of Login Customizer.
 *
 * @var $wp_customize This variable is brought from login-customizer.php file.
 * @var $branding This variable is brought from login-customizer.php file.
 * @var $branding_enabled This variable is brought from login-customizer.php file.
 * @var $accent_color This variable is brought from login-customizer.php file.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Customize_Color_Control;
use ATSDash\Customize_Range_Control;

$wp_customize->add_setting(
	'ats_login[labels_font_size]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '14px',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'esc_attr',
	)
);

$wp_customize->add_control(
	new Customize_Range_Control(
		$wp_customize,
		'ats_login[labels_font_size]',
		array(
			'type'        => 'range',
			'section'     => 'ats_login_customizer_labels_section',
			'settings'    => 'ats_login[labels_font_size]',
			'label'       => __( 'Font Size', 'ats-dashboard' ),
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[labels_color]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '#444444',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	)
);

$wp_customize->add_control(
	new Customize_Color_Control(
		$wp_customize,
		'ats_login[labels_color]',
		array(
			'label'    => __( 'Text Color', 'ats-dashboard' ),
			'section'  => 'ats_login_customizer_labels_section',
			'settings' => 'ats_login[labels_color]',
		)
	)
);
