<?php
/**
 * Fields section of Login Customizer.
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
	'ats_login[fields_height]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '50px',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'esc_attr',
	)
);

$wp_customize->add_control(
	new Customize_Range_Control(
		$wp_customize,
		'ats_login[fields_height]',
		array(
			'type'        => 'range',
			'section'     => 'ats_login_customizer_fields_section',
			'settings'    => 'ats_login[fields_height]',
			'label'       => __( 'Height', 'ats-dashboard' ),
			'input_attrs' => array(
				'min'  => 20,
				'max'  => 80,
				'step' => 1,
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[fields_horizontal_padding]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '10px',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'esc_attr',
	)
);

$wp_customize->add_control(
	new Customize_Range_Control(
		$wp_customize,
		'ats_login[fields_horizontal_padding]',
		array(
			'type'        => 'range',
			'section'     => 'ats_login_customizer_fields_section',
			'settings'    => 'ats_login[fields_horizontal_padding]',
			'label'       => __( 'Side Padding', 'ats-dashboard' ),
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 80,
				'step' => 1,
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[fields_border_width]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '2px',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'esc_attr',
	)
);

$wp_customize->add_control(
	new Customize_Range_Control(
		$wp_customize,
		'ats_login[fields_border_width]',
		array(
			'type'        => 'range',
			'section'     => 'ats_login_customizer_fields_section',
			'settings'    => 'ats_login[fields_border_width]',
			'label'       => __( 'Border Width', 'ats-dashboard' ),
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 30,
				'step' => 1,
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[fields_border_radius]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '4px',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'esc_attr',
	)
);

$wp_customize->add_control(
	new Customize_Range_Control(
		$wp_customize,
		'ats_login[fields_border_radius]',
		array(
			'type'        => 'range',
			'section'     => 'ats_login_customizer_fields_section',
			'settings'    => 'ats_login[fields_border_radius]',
			'label'       => __( 'Border Radius', 'ats-dashboard' ),
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 50,
				'step' => 1,
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[fields_font_size]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '24px',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'esc_attr',
	)
);

$wp_customize->add_control(
	new Customize_Range_Control(
		$wp_customize,
		'ats_login[fields_font_size]',
		array(
			'type'        => 'range',
			'section'     => 'ats_login_customizer_fields_section',
			'settings'    => 'ats_login[fields_font_size]',
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
	'ats_login[fields_text_color]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '#32373c',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	)
);

$wp_customize->add_control(
	new Customize_Color_Control(
		$wp_customize,
		'ats_login[fields_text_color]',
		array(
			'label'    => __( 'Text Color', 'ats-dashboard' ),
			'section'  => 'ats_login_customizer_fields_section',
			'settings' => 'ats_login[fields_text_color]',
		)
	)
);

$wp_customize->add_setting(
	'ats_login[fields_text_color_focus]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '#32373c',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	)
);

$wp_customize->add_control(
	new Customize_Color_Control(
		$wp_customize,
		'ats_login[fields_text_color_focus]',
		array(
			'label'    => __( 'Text Color (Focus)', 'ats-dashboard' ),
			'section'  => 'ats_login_customizer_fields_section',
			'settings' => 'ats_login[fields_text_color_focus]',
		)
	)
);

$wp_customize->add_setting(
	'ats_login[fields_bg_color]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '#ffffff',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	)
);

$wp_customize->add_control(
	new Customize_Color_Control(
		$wp_customize,
		'ats_login[fields_bg_color]',
		array(
			'label'    => __( 'Background Color', 'ats-dashboard' ),
			'section'  => 'ats_login_customizer_fields_section',
			'settings' => 'ats_login[fields_bg_color]',
		)
	)
);

$wp_customize->add_setting(
	'ats_login[fields_bg_color_focus]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '#ffffff',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	)
);

$wp_customize->add_control(
	new Customize_Color_Control(
		$wp_customize,
		'ats_login[fields_bg_color_focus]',
		array(
			'label'    => __( 'Background Color (Focus)', 'ats-dashboard' ),
			'section'  => 'ats_login_customizer_fields_section',
			'settings' => 'ats_login[fields_bg_color_focus]',
		)
	)
);

$wp_customize->add_setting(
	'ats_login[fields_border_color]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '#dddddd',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	)
);

$wp_customize->add_control(
	new Customize_Color_Control(
		$wp_customize,
		'ats_login[fields_border_color]',
		array(
			'label'    => __( 'Border Color', 'ats-dashboard' ),
			'section'  => 'ats_login_customizer_fields_section',
			'settings' => 'ats_login[fields_border_color]',
		)
	)
);

$wp_customize->add_setting(
	'ats_login[fields_border_color_focus]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => $has_accent_color ? $accent_color : '#3858e9',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	)
);

$wp_customize->add_control(
	new Customize_Color_Control(
		$wp_customize,
		'ats_login[fields_border_color_focus]',
		array(
			'label'    => __( 'Border Color (Focus)', 'ats-dashboard' ),
			'section'  => 'ats_login_customizer_fields_section',
			'settings' => 'ats_login[fields_border_color_focus]',
		)
	)
);
