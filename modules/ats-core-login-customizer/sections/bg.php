<?php
/**
 * Background section of Login Customizer.
 *
 * @var $wp_customize This variable is brought from login-customizer.php file.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Helpers\Content_Helper;
use ats\ats_Customize_Control;
use ats\ats_Customize_Image_Control;
use ats\ats_Customize_Color_Control;
use ats\ats_Customize_Toggle_Switch_Control;
use ats\ats_Customize_Color_Picker_Control;

$wp_customize->add_setting(
	'ats_login[bg_color]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => '#f1f1f1',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	)
);

$wp_customize->add_control(
	new ats_Customize_Color_Control(
		$wp_customize,
		'ats_login[bg_color]',
		array(
			'label'    => __( 'Background Color', 'ats-dashboard' ),
			'section'  => 'ats_login_customizer_bg_section',
			'settings' => 'ats_login[bg_color]',
		)
	)
);

$content_helper = new Content_Helper();

$wp_customize->add_setting(
	'ats_login[bg_image]',
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
		'ats_login[bg_image]',
		array(
			'label'    => __( 'Upload Background', 'ats-dashboard' ),
			'section'  => 'ats_login_customizer_bg_section',
			'settings' => 'ats_login[bg_image]',
		)
	)
);

$wp_customize->add_setting(
	'ats_login[bg_repeat]',
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
		'ats_login[bg_repeat]',
		array(
			'type'     => 'select',
			'section'  => 'ats_login_customizer_bg_section',
			'settings' => 'ats_login[bg_repeat]',
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
	'ats_login[bg_position]',
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
		'ats_login[bg_position]',
		array(
			'type'     => 'select',
			'section'  => 'ats_login_customizer_bg_section',
			'settings' => 'ats_login[bg_position]',
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
	'ats_login[bg_custom_position]',
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
		'ats_login[bg_custom_position]',
		array(
			'type'        => 'text',
			'section'     => 'ats_login_customizer_bg_section',
			'settings'    => 'ats_login[bg_custom_position]',
			'label'       => __( 'Custom Background Position', 'ats-dashboard' ),
			'description' => '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/background-position" target="_blank">Click here</a> for more information.',
			'input_attrs' => array(
				'placeholder' => '0% 0%',
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[bg_size]',
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
		'ats_login[bg_size]',
		array(
			'type'     => 'select',
			'section'  => 'ats_login_customizer_bg_section',
			'settings' => 'ats_login[bg_size]',
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
	'ats_login[bg_custom_size]',
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
		'ats_login[bg_custom_size]',
		array(
			'type'        => 'text',
			'section'     => 'ats_login_customizer_bg_section',
			'settings'    => 'ats_login[bg_custom_size]',
			'label'       => __( 'Custom Background Size', 'ats-dashboard' ),
			'description' => '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/background-size" target="_blank">Click here</a> for more information.',
			'input_attrs' => array(
				'placeholder' => 'auto auto',
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[enable_bg_overlay_color]',
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
		'ats_login[enable_bg_overlay_color]',
		array(
			'settings' => 'ats_login[enable_bg_overlay_color]',
			'section'  => 'ats_login_customizer_bg_section',
			'label'    => __( 'Background Overlay', 'ats-dashboard' ),
		)
	)
);

$setting_args = array(
	'type'              => 'option',
	'capability'        => 'edit_theme_options',
	'transport'         => 'postMessage',
	// @todo Provide proper sanitize based on WPTT color alpha repo.
	'sanitize_callback' => 'sanitize_text_field', // Because sanitize_hex_color wouldn't work on rgba.
);

$opts = get_option( 'ats_login', array() );

if ( isset( $opts['bg_overlay_color'] ) ) {
	$setting_args['default'] = $opts['bg_overlay_color'];
}

$wp_customize->add_setting(
	'ats_login[bg_overlay_color]',
	$setting_args
);

$wp_customize->add_control(
	new ats_Customize_Color_Picker_Control(
		$wp_customize,
		'ats_login[bg_overlay_color]',
		array(
			'settings' => 'ats_login[bg_overlay_color]',
			'section'  => 'ats_login_customizer_bg_section',
			'label'    => __( 'Overlay Color', 'ats-dashboard' ),
			'alpha'    => true,
		)
	)
);
