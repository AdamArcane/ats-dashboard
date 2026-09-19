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
use ats\ats_Customize_Color_Control;
use ats\ats_Customize_Toggle_Switch_Control;

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

$layout_settings = array(
	'form_top_padding'        => array( 'label' => 'Top Padding', 'default' => '0px', 'min' => 0, 'max' => 100 ),
	'form_bottom_padding'     => array( 'label' => 'Bottom Padding', 'default' => '0px', 'min' => 0, 'max' => 100 ),
	'form_horizontal_padding' => array( 'label' => 'Side Padding', 'default' => '0px', 'min' => 0, 'max' => 80 ),
	'form_border_width'       => array( 'label' => 'Border Width', 'default' => '2px', 'min' => 0, 'max' => 30 ),
	'form_border_radius'      => array( 'label' => 'Border Radius', 'default' => '4px', 'min' => 0, 'max' => 50 ),
);

foreach ( $layout_settings as $setting_name => $setting ) {
	$wp_customize->add_setting(
		'ats_login[' . $setting_name . ']',
		array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'default'           => $setting['default'],
			'transport'         => 'postMessage',
			'sanitize_callback' => 'esc_attr',
		)
	);

	$wp_customize->add_control(
		new ats_Customize_Range_Control(
			$wp_customize,
			'ats_login[' . $setting_name . ']',
			array(
				'type'        => 'range',
				'section'     => 'ats_login_customizer_layout_section',
				'settings'    => 'ats_login[' . $setting_name . ']',
				'label'       => __( $setting['label'], 'ats-dashboard' ),
				'input_attrs' => array( 'min' => $setting['min'], 'max' => $setting['max'], 'step' => 1 ),
			)
		)
	);
}

$wp_customize->add_setting( 'ats_login[form_border_style]', array( 'type' => 'option', 'capability' => 'edit_theme_options', 'default' => 'solid', 'transport' => 'postMessage', 'sanitize_callback' => 'sanitize_text_field' ) );
$wp_customize->add_control( new ats_Customize_Control( $wp_customize, 'ats_login[form_border_style]', array( 'type' => 'select', 'section' => 'ats_login_customizer_layout_section', 'settings' => 'ats_login[form_border_style]', 'label' => __( 'Border Style', 'ats-dashboard' ), 'choices' => array( 'solid' => __( 'Solid', 'ats-dashboard' ), 'dashed' => __( 'Dashed', 'ats-dashboard' ), 'dotted' => __( 'Dotted', 'ats-dashboard' ), 'none' => __( 'None', 'ats-dashboard' ) ) ) ) );

foreach ( array( 'form_border_color' => array( 'Border Color', '#dddddd' ) ) as $setting_name => $setting ) {
	$wp_customize->add_setting( 'ats_login[' . $setting_name . ']', array( 'type' => 'option', 'capability' => 'edit_theme_options', 'default' => $setting[1], 'transport' => 'postMessage', 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new ats_Customize_Color_Control( $wp_customize, 'ats_login[' . $setting_name . ']', array( 'label' => __( $setting[0], 'ats-dashboard' ), 'section' => 'ats_login_customizer_layout_section', 'settings' => 'ats_login[' . $setting_name . ']' ) ) );
}

$wp_customize->add_setting( 'ats_login[enable_form_shadow]', array( 'type' => 'option', 'capability' => 'edit_theme_options', 'default' => 0, 'transport' => 'postMessage', 'sanitize_callback' => 'absint' ) );
$wp_customize->add_control( new ats_Customize_Toggle_Switch_Control( $wp_customize, 'ats_login[enable_form_shadow]', array( 'section' => 'ats_login_customizer_layout_section', 'settings' => 'ats_login[enable_form_shadow]', 'label' => __( 'Enable Form Shadow', 'ats-dashboard' ) ) ) );

$wp_customize->add_setting(
	'ats_login[form_shadow_blur]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '10px',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'esc_attr',
	)
);
$wp_customize->add_control(
	new ats_Customize_Range_Control(
		$wp_customize,
		'ats_login[form_shadow_blur]',
		array(
			'type'        => 'range',
			'section'     => 'ats_login_customizer_layout_section',
			'settings'    => 'ats_login[form_shadow_blur]',
			'label'       => __( 'Shadow Blur', 'ats-dashboard' ),
			'input_attrs' => array( 'min' => 0, 'max' => 50, 'step' => 1 ),
		)
	)
);

$wp_customize->add_setting( 'ats_login[form_shadow_color]', array( 'type' => 'option', 'capability' => 'edit_theme_options', 'default' => '#cccccc', 'transport' => 'postMessage', 'sanitize_callback' => 'sanitize_hex_color' ) );
$wp_customize->add_control( new ats_Customize_Color_Control( $wp_customize, 'ats_login[form_shadow_color]', array( 'label' => __( 'Shadow Color', 'ats-dashboard' ), 'section' => 'ats_login_customizer_layout_section', 'settings' => 'ats_login[form_shadow_color]' ) ) );

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
