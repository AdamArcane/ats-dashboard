<?php
/**
 * Logo section of Login Customizer.
 *
 * @var $wp_customize This variable is brought from login-customizer.php file.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\ats_Customize_Control;
use ats\ats_Customize_Range_Control;
use ats\ats_Customize_Image_Control;

use ats\Helpers\Content_Helper;

$content_helper = new Content_Helper();

$wp_customize->add_setting(
	'ats_login[logo_image]',
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
		'ats_login[logo_image]',
		array(
			'label'    => __( 'Logo', 'ats-dashboard' ),
			'section'  => 'ats_login_customizer_logo_section',
			'settings' => 'ats_login[logo_image]',
		)
	)
);

$default_logo_height = '100%';

// Used to provide multisite support in the PRO version.
$default_logo_height = apply_filters( 'ats_login_customizer_default_logo_height', $default_logo_height );

$wp_customize->add_setting(
	'ats_login[logo_height]',
	array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'default'           => $default_logo_height,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'esc_attr',
	)
);

$wp_customize->add_control(
	new ats_Customize_Range_Control(
		$wp_customize,
		'ats_login[logo_height]',
		array(
			'type'        => 'range',
			'section'     => 'ats_login_customizer_logo_section',
			'settings'    => 'ats_login[logo_height]',
			'label'       => __( 'Logo Height', 'ats-dashboard' ),
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[logo_url]',
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
		'ats_login[logo_url]',
		array(
			'type'        => 'text',
			'section'     => 'ats_login_customizer_logo_section',
			'settings'    => 'ats_login[logo_url]',
			'label'       => __( 'Logo URL', 'ats-dashboard' ),
			'description' => __( 'Available template tags: {home_url}', 'ats-dashboard' ),
			'input_attrs' => array(
				'placeholder' => 'https://wordpress.org/',
			),
		)
	)
);

$wp_customize->add_setting(
	'ats_login[logo_title]',
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
		'ats_login[logo_title]',
		array(
			'type'        => 'text',
			'section'     => 'ats_login_customizer_logo_section',
			'settings'    => 'ats_login[logo_title]',
			'label'       => __( 'Logo Title', 'ats-dashboard' ),
			'input_attrs' => array(
				'placeholder' => 'Powered by WordPress',
			),
		)
	)
);
