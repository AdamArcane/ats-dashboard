<?php
/**
 * Layout section of Login Customizer.
 *
 * @var $wp_customize This variable is brought from login-customizer.php file.
 * @var $branding This variable is brought from login-customizer.php file.
 * @var $branding_enabled This variable is brought from login-customizer.php file.
 * @var $accent_color This variable is brought from login-customizer.php file.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Helpers\Content_Helper;
use ats\ats_Customize_Control;
use ats\ats_Customize_Pro_Control;
use ats\ats_Customize_Image_Control;
use ats\ats_Customize_Color_Control;
use ats\ats_Customize_Range_Control;
use ats\ats_Customize_Toggle_Switch_Control;

if ( ! defined( 'ATS_DASHBOARD_PLUGIN_VERSION' ) ) {
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
				'label'    => __( 'Layout', 'ats-dashboard' ),
				// The PRO version contains more than "default".
				'choices'  => array(
					'default' => __( 'Default', 'ats-dashboard' ),
				),
			)
		)
	);

	$wp_customize->add_setting(
		'ats_login[pro_layout]',
		array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'default'           => '',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		new ats_Customize_Pro_Control(
			$wp_customize,
			'ats_login[pro_layout]',
			array(
				'label'       => '',
				'description' => __( 'More layouts (left & right) available in ATS Dashboard.', 'ats-dashboard' ),
				'section'     => 'ats_login_customizer_layout_section',
				'settings'    => 'ats_login[pro_layout]',
			)
		)
	);
}


$wp_customize->add_setting(
	'ats_login[form_bg_color]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '#ffffff',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	)
);

$wp_customize->add_control(
	new ats_Customize_Color_Control(
		$wp_customize,
		'ats_login[form_bg_color]',
		array(
			'label'    => __( 'Background Color', 'ats-dashboard' ),
			'section'  => 'ats_login_customizer_layout_section',
			'settings' => 'ats_login[form_bg_color]',
		)
	)
);

$content_helper = new Content_Helper();

$wp_customize->add_setting(
	'ats_login[form_bg_image]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '',
		'transport'         => 'postMessage',
		'sanitize_callback' => array( $content_helper, 'sanitize_image' ),
	)
);

$wp_customize->add_control(
	new ats_Customize_Image_Control(
		$wp_customize,
		'ats_login[form_bg_image]',
		array(
			'label'    => __( 'Background Image', 'ats-dashboard' ),
			'section'  => 'ats_login_customizer_layout_section',
			'settings' => 'ats_login[form_bg_image]',
		)
	)
);

$wp_customize->add_setting(
	'ats_login[form_bg_repeat]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => 'no-repeat',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	new ats_Customize_Control(
		$wp_customize,
		'ats_login[form_bg_repeat]',
		array(
			'type'     => 'select',
			'section'  => 'ats_login_customizer_layout_section',
			'settings' => 'ats_login[form_bg_repeat]',
			'label'    => __( 'Background Repeat', 'ats-dashboard' ),
			'choices'  => array(
				'no-repeat' => __( 'no-repeat', 'ats-dashboard' ),
				'repeat'    => __( 'repeat', 'ats-dashboard' ),
				'repeat-x'  => __( 'repeat-x', 'ats-dashboard' ),
				'repeat-y'  => __( 'repeat-y', 'ats-dashboard' ),
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[form_bg_position]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => 'center center',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	new ats_Customize_Control(
		$wp_customize,
		'ats_login[form_bg_position]',
		array(
			'type'     => 'select',
			'section'  => 'ats_login_customizer_layout_section',
			'settings' => 'ats_login[form_bg_position]',
			'label'    => __( 'Background Position', 'ats-dashboard' ),
			'choices'  => array(
				'left top'      => __( 'left top', 'ats-dashboard' ),
				'left center'   => __( 'left center', 'ats-dashboard' ),
				'left bottom'   => __( 'left bottom', 'ats-dashboard' ),
				'center top'    => __( 'center top', 'ats-dashboard' ),
				'center center' => __( 'center center', 'ats-dashboard' ),
				'center bottom' => __( 'center bottom', 'ats-dashboard' ),
				'right top'     => __( 'right top', 'ats-dashboard' ),
				'right center'  => __( 'right center', 'ats-dashboard' ),
				'right bottom'  => __( 'right bottom', 'ats-dashboard' ),
				'custom'        => __( 'custom', 'ats-dashboard' ),
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[form_bg_custom_position]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	new ats_Customize_Control(
		$wp_customize,
		'ats_login[form_bg_custom_position]',
		array(
			'type'        => 'text',
			'section'     => 'ats_login_customizer_layout_section',
			'settings'    => 'ats_login[form_bg_custom_position]',
			'label'       => __( 'Custom Background Position', 'ats-dashboard' ),
			'description' => '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/background-position" target="_blank">Click here</a> for more information.',
			'input_attrs' => array(
				'placeholder' => '0% 0%',
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[form_bg_size]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => 'cover',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	new ats_Customize_Control(
		$wp_customize,
		'ats_login[form_bg_size]',
		array(
			'type'     => 'select',
			'section'  => 'ats_login_customizer_layout_section',
			'settings' => 'ats_login[form_bg_size]',
			'label'    => __( 'Background Size', 'ats-dashboard' ),
			'choices'  => array(
				'auto'    => __( 'auto', 'ats-dashboard' ),
				'cover'   => __( 'cover', 'ats-dashboard' ),
				'contain' => __( 'contain', 'ats-dashboard' ),
				'custom'  => __( 'custom', 'ats-dashboard' ),
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[form_bg_custom_size]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	new ats_Customize_Control(
		$wp_customize,
		'ats_login[form_bg_custom_size]',
		array(
			'type'        => 'text',
			'section'     => 'ats_login_customizer_layout_section',
			'settings'    => 'ats_login[form_bg_custom_size]',
			'label'       => __( 'Custom Background Size', 'ats-dashboard' ),
			'description' => '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/background-size" target="_blank">Click here</a> for more information.',
			'input_attrs' => array(
				'placeholder' => 'auto auto',
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[form_width]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '320px',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'esc_attr',
	)
);

$wp_customize->add_control(
	new ats_Customize_Range_Control(
		$wp_customize,
		'ats_login[form_width]',
		array(
			'type'        => 'range',
			'section'     => 'ats_login_customizer_layout_section',
			'settings'    => 'ats_login[form_width]',
			'priority'    => 15, // We need to insert "Box Width" control before this one.
			'label'       => __( 'Width', 'ats-dashboard' ),
			'input_attrs' => array(
				'min'  => 200,
				'max'  => 1000,
				'step' => 1,
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[form_top_padding]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '26px',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'esc_attr',
	)
);

$wp_customize->add_control(
	new ats_Customize_Range_Control(
		$wp_customize,
		'ats_login[form_top_padding]',
		array(
			'type'        => 'range',
			'section'     => 'ats_login_customizer_layout_section',
			'settings'    => 'ats_login[form_top_padding]',
			'priority'    => 15,
			'label'       => __( 'Top Padding', 'ats-dashboard' ),
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[form_bottom_padding]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '46px',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'esc_attr',
	)
);

$wp_customize->add_control(
	new ats_Customize_Range_Control(
		$wp_customize,
		'ats_login[form_bottom_padding]',
		array(
			'type'        => 'range',
			'section'     => 'ats_login_customizer_layout_section',
			'settings'    => 'ats_login[form_bottom_padding]',
			'priority'    => 15,
			'label'       => __( 'Bottom Padding', 'ats-dashboard' ),
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[form_horizontal_padding]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '24px',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'esc_attr',
	)
);

$wp_customize->add_control(
	new ats_Customize_Range_Control(
		$wp_customize,
		'ats_login[form_horizontal_padding]',
		array(
			'type'        => 'range',
			'section'     => 'ats_login_customizer_layout_section',
			'settings'    => 'ats_login[form_horizontal_padding]',
			'priority'    => 15,
			'label'       => __( 'Side Padding', 'ats-dashboard' ),
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[form_border_width]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '2px',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'esc_attr',
	)
);

$wp_customize->add_control(
	new ats_Customize_Range_Control(
		$wp_customize,
		'ats_login[form_border_width]',
		array(
			'type'        => 'range',
			'section'     => 'ats_login_customizer_layout_section',
			'settings'    => 'ats_login[form_border_width]',
			'priority'    => 15,
			'label'       => __( 'Border Width', 'ats-dashboard' ),
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 50,
				'step' => 1,
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[form_border_style]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => 'solid',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	new ats_Customize_Control(
		$wp_customize,
		'ats_login[form_border_style]',
		array(
			'type'     => 'select',
			'section'  => 'ats_login_customizer_layout_section',
			'settings' => 'ats_login[form_border_style]',
			'priority' => 15,
			'label'    => __( 'Border Style', 'ats-dashboard' ),
			'choices'  => array(
				'solid'  => __( 'solid', 'ats-dashboard' ),
				'dotted' => __( 'dotted', 'ats-dashboard' ),
				'dashed' => __( 'dashed', 'ats-dashboard' ),
				'double' => __( 'double', 'ats-dashboard' ),
				'none'   => __( 'none', 'ats-dashboard' ),
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[form_border_color]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '#dddddd',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	)
);

$wp_customize->add_control(
	new ats_Customize_Color_Control(
		$wp_customize,
		'ats_login[form_border_color]',
		array(
			'label'    => __( 'Border Color', 'ats-dashboard' ),
			'section'  => 'ats_login_customizer_layout_section',
			'settings' => 'ats_login[form_border_color]',
			'priority' => 15,
		)
	)
);

$wp_customize->add_setting(
	'ats_login[form_border_radius]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '4px',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'esc_attr',
	)
);

$wp_customize->add_control(
	new ats_Customize_Range_Control(
		$wp_customize,
		'ats_login[form_border_radius]',
		array(
			'type'        => 'range',
			'section'     => 'ats_login_customizer_layout_section',
			'settings'    => 'ats_login[form_border_radius]',
			'priority'    => 15,
			'label'       => __( 'Border Radius', 'ats-dashboard' ),
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 80,
				'step' => 1,
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[enable_form_shadow]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => 0,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'absint',
	)
);

$wp_customize->add_control(
	new ats_Customize_Toggle_Switch_Control(
		$wp_customize,
		'ats_login[enable_form_shadow]',
		array(
			'section'  => 'ats_login_customizer_layout_section',
			'settings' => 'ats_login[enable_form_shadow]',
			'priority' => 15,
			'label'    => __( 'Box Shadow', 'ats-dashboard' ),
		)
	)
);

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
			'priority'    => 15,
			'label'       => __( 'Width', 'ats-dashboard' ),
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[form_shadow_color]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '#dddddd',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	)
);

$wp_customize->add_control(
	new ats_Customize_Color_Control(
		$wp_customize,
		'ats_login[form_shadow_color]',
		array(
			'label'    => __( 'Box Shadow Color', 'ats-dashboard' ),
			'section'  => 'ats_login_customizer_layout_section',
			'settings' => 'ats_login[form_shadow_color]',
			'priority' => 15,
		)
	)
);
