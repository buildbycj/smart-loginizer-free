<?php
/**
 * Auth Form Widget (Inline).
 *
 * @package SmartLoginizer\Widgets
 */

namespace SmartLoginizer\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use SmartLoginizer\Helpers\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Auth Form Widget class (Inline version).
 */
class Auth_Form_Widget extends Base_Widget {

	/**
	 * Helpers instance.
	 *
	 * @var Helpers
	 */
	private Helpers $helpers;

	/**
	 * Constructor.
	 *
	 * @param array $data Widget data.
	 * @param array $args Widget arguments.
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
		$this->helpers = new Helpers();
	}

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'smart_loginizer_auth_form';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return __( 'Auth Form (Inline)', 'smart-loginizer' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-form-horizontal';
	}

	/**
	 * Register widget controls.
	 *
	 * @return void
	 */
	protected function register_controls(): void {
		// Content section.
		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Content', 'smart-loginizer' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'default_form',
			array(
				'label'   => __( 'Default Form', 'smart-loginizer' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'login',
				'options' => array(
					'login'    => __( 'Login', 'smart-loginizer' ),
					'register' => __( 'Register', 'smart-loginizer' ),
					'reset'    => __( 'Reset Password', 'smart-loginizer' ),
				),
			)
		);

		$this->add_control(
			'enable_login',
			array(
				'label'        => __( 'Enable Login Form', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'enable_register',
			array(
				'label'        => __( 'Enable Register Form', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'enable_reset',
			array(
				'label'        => __( 'Enable Reset Password Form', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_register_link',
			array(
				'label'        => __( 'Show Register Link in Login', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'register_link_text',
			array(
				'label'     => __( 'Register Link Text', 'smart-loginizer' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( "Don't have an account? Register", 'smart-loginizer' ),
				'condition' => array(
					'show_register_link' => 'yes',
					'enable_register'     => 'yes',
				),
			)
		);

		$this->add_control(
			'show_reset_link',
			array(
				'label'        => __( 'Show Reset Password Link in Login', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'reset_link_text',
			array(
				'label'     => __( 'Reset Password Link Text', 'smart-loginizer' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Forgot your password?', 'smart-loginizer' ),
				'condition' => array(
					'show_reset_link' => 'yes',
					'enable_reset'    => 'yes',
				),
			)
		);

		$this->add_control(
			'show_login_link_register',
			array(
				'label'        => __( 'Show Login Link in Register', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'login_link_text_register',
			array(
				'label'     => __( 'Login Link Text (in Register)', 'smart-loginizer' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Already have an account? Login', 'smart-loginizer' ),
				'condition' => array(
					'show_login_link_register' => 'yes',
					'enable_login'             => 'yes',
				),
			)
		);

		$this->add_control(
			'show_login_link_reset',
			array(
				'label'        => __( 'Show Login Link in Reset', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'login_link_text_reset',
			array(
				'label'     => __( 'Login Link Text (in Reset)', 'smart-loginizer' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Back to Login', 'smart-loginizer' ),
				'condition' => array(
					'show_login_link_reset' => 'yes',
					'enable_login'           => 'yes',
				),
			)
		);

		$this->add_control(
			'redirect_after_login',
			array(
				'label'       => __( 'Redirect After Login', 'smart-loginizer' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'smart-loginizer' ),
				'show_external' => false,
			)
		);

		$this->add_control(
			'show_when_logged_in',
			array(
				'label'        => __( 'Show Form When Logged In', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'description'  => __( 'Enable to show the form even when user is logged in (useful for preview in editor).', 'smart-loginizer' ),
			)
		);

		$this->add_control(
			'logged_in_message',
			array(
				'label'     => __( 'Logged In Message', 'smart-loginizer' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => /* translators: %s: User display name */
					__( 'You are already logged in as %s.', 'smart-loginizer' ),
				'condition' => array(
					'show_when_logged_in' => '',
				),
			)
		);

		$this->add_control(
			'form_header_separator',
			array(
				'type'      => Controls_Manager::DIVIDER,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'show_site_logo',
			array(
				'label'        => __( 'Show Site Logo', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$this->add_control(
			'site_logo',
			array(
				'label'     => __( 'Site Logo', 'smart-loginizer' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array(
					'show_site_logo' => 'yes',
				),
			)
		);

		$this->add_control(
			'use_separate_headlines',
			array(
				'label'        => __( 'Use Separate Headlines/Descriptions for Each Form', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		// Default Headline & Description
		$this->add_control(
			'default_headline',
			array(
				'label'     => __( 'Default Headline', 'smart-loginizer' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Welcome Back', 'smart-loginizer' ),
				'condition' => array(
					'use_separate_headlines' => '',
				),
			)
		);

		$this->add_control(
			'default_description',
			array(
				'label'     => __( 'Default Description', 'smart-loginizer' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 3,
				'condition' => array(
					'use_separate_headlines' => '',
				),
			)
		);

		// Login Form Headline & Description
		$this->add_control(
			'login_headline',
			array(
				'label'     => __( 'Login Headline', 'smart-loginizer' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Welcome Back', 'smart-loginizer' ),
				'condition' => array(
					'use_separate_headlines' => 'yes',
					'enable_login'            => 'yes',
				),
			)
		);

		$this->add_control(
			'login_description',
			array(
				'label'     => __( 'Login Description', 'smart-loginizer' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 3,
				'condition' => array(
					'use_separate_headlines' => 'yes',
					'enable_login'            => 'yes',
				),
			)
		);

		// Register Form Headline & Description
		$this->add_control(
			'register_headline',
			array(
				'label'     => __( 'Register Headline', 'smart-loginizer' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Create Account', 'smart-loginizer' ),
				'condition' => array(
					'use_separate_headlines' => 'yes',
					'enable_register'         => 'yes',
				),
			)
		);

		$this->add_control(
			'register_description',
			array(
				'label'     => __( 'Register Description', 'smart-loginizer' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 3,
				'condition' => array(
					'use_separate_headlines' => 'yes',
					'enable_register'         => 'yes',
				),
			)
		);

		// Reset Form Headline & Description
		$this->add_control(
			'reset_headline',
			array(
				'label'     => __( 'Reset Password Headline', 'smart-loginizer' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Reset Password', 'smart-loginizer' ),
				'condition' => array(
					'use_separate_headlines' => 'yes',
					'enable_reset'            => 'yes',
				),
			)
		);

		$this->add_control(
			'reset_description',
			array(
				'label'     => __( 'Reset Password Description', 'smart-loginizer' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 3,
				'condition' => array(
					'use_separate_headlines' => 'yes',
					'enable_reset'            => 'yes',
				),
			)
		);

		$this->add_control(
			'show_social_login',
			array(
				'label'        => __( 'Show Social Login', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'social_login_text',
			array(
				'label'     => __( 'Social Login Text', 'smart-loginizer' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Or continue with', 'smart-loginizer' ),
				'condition' => array(
					'show_social_login' => 'yes',
				),
			)
		);

		$this->add_control(
			'social_login_icons_heading',
			array(
				'label'     => __( 'Social Login Icons', 'smart-loginizer' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'show_social_login' => 'yes',
				),
			)
		);

		$this->add_control(
			'google_icon',
			array(
				'label'     => __( 'Google Icon', 'smart-loginizer' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fab fa-google',
					'library' => 'fa-brands',
				),
				'condition' => array(
					'show_social_login' => 'yes',
				),
			)
		);

		$this->add_control(
			'x_icon',
			array(
				'label'     => __( 'X (Twitter) Icon', 'smart-loginizer' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fab fa-x-twitter',
					'library' => 'fa-brands',
				),
				'condition' => array(
					'show_social_login' => 'yes',
				),
			)
		);

		$this->add_control(
			'linkedin_icon',
			array(
				'label'     => __( 'LinkedIn Icon', 'smart-loginizer' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fab fa-linkedin',
					'library' => 'fa-brands',
				),
				'condition' => array(
					'show_social_login' => 'yes',
				),
			)
		);

		$this->add_control(
			'facebook_icon',
			array(
				'label'     => __( 'Facebook Icon', 'smart-loginizer' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fab fa-facebook',
					'library' => 'fa-brands',
				),
				'condition' => array(
					'show_social_login' => 'yes',
				),
			)
		);

		$this->add_control(
			'form_element_order_heading',
			array(
				'label'     => __( 'Form Element Order', 'smart-loginizer' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'social_login_position',
			array(
				'label'   => __( 'Social Login Position', 'smart-loginizer' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'after_submit',
				'options' => array(
					'top'                => __( 'Top (Before Username)', 'smart-loginizer' ),
					'after_username'     => __( 'After Username', 'smart-loginizer' ),
					'after_email'        => __( 'After Email (Register only)', 'smart-loginizer' ),
					'after_password'    => __( 'After Password', 'smart-loginizer' ),
					'after_confirm_password' => __( 'After Confirm Password (Register only)', 'smart-loginizer' ),
					'after_remember'    => __( 'After Remember Me (Login only)', 'smart-loginizer' ),
					'after_recaptcha'   => __( 'After reCAPTCHA', 'smart-loginizer' ),
					'after_submit'      => __( 'After Submit Button (Default)', 'smart-loginizer' ),
				),
				'condition' => array(
					'show_social_login' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		// Form Layout Style section.
		$this->start_controls_section(
			'form_layout_style_section',
			array(
				'label' => __( 'Form Style', 'smart-loginizer' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'form_style',
			array(
				'label'   => __( 'Form Style (Desktop)', 'smart-loginizer' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default'  => __( 'Default', 'smart-loginizer' ),
					'inline'   => __( 'Inline', 'smart-loginizer' ),
					'two_rows' => __( 'Two Rows', 'smart-loginizer' ),
				),
			)
		);

		$this->add_control(
			'form_style_tablet',
			array(
				'label'     => __( 'Form Style (Tablet)', 'smart-loginizer' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => array(
					'default'  => __( 'Default', 'smart-loginizer' ),
					'inline'   => __( 'Inline', 'smart-loginizer' ),
					'two_rows' => __( 'Two Rows', 'smart-loginizer' ),
				),
				'condition' => array(
					'form_style!' => '',
				),
			)
		);

		$this->add_control(
			'form_style_mobile',
			array(
				'label'     => __( 'Form Style (Mobile)', 'smart-loginizer' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => array(
					'default'  => __( 'Default', 'smart-loginizer' ),
					'inline'   => __( 'Inline', 'smart-loginizer' ),
					'two_rows' => __( 'Two Rows', 'smart-loginizer' ),
				),
				'condition' => array(
					'form_style!' => '',
				),
			)
		);

		$this->end_controls_section();

		// Form Fields section.
		$this->start_controls_section(
			'form_fields_section',
			array(
				'label' => __( 'Form Fields', 'smart-loginizer' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_field_icons',
			array(
				'label'        => __( 'Show Field Icons', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$this->add_control(
			'username_icon',
			array(
				'label'     => __( 'Username/Email Icon', 'smart-loginizer' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(),
				'condition' => array(
					'show_field_icons' => 'yes',
				),
			)
		);

		$this->add_control(
			'username_icon_position',
			array(
				'label'     => __( 'Username/Email Icon Position', 'smart-loginizer' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'left',
				'options'   => array(
					'left'  => __( 'Left', 'smart-loginizer' ),
					'right' => __( 'Right', 'smart-loginizer' ),
				),
				'condition' => array(
					'show_field_icons' => 'yes',
					'username_icon[value]!' => '',
				),
			)
		);

		$this->add_control(
			'password_icon',
			array(
				'label'     => __( 'Password Icon', 'smart-loginizer' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(),
				'condition' => array(
					'show_field_icons' => 'yes',
				),
			)
		);

		$this->add_control(
			'password_icon_position',
			array(
				'label'     => __( 'Password Icon Position', 'smart-loginizer' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'left',
				'options'   => array(
					'left'  => __( 'Left', 'smart-loginizer' ),
					'right' => __( 'Right', 'smart-loginizer' ),
				),
				'condition' => array(
					'show_field_icons' => 'yes',
					'password_icon[value]!' => '',
				),
			)
		);

		$this->add_control(
			'show_password_toggle',
			array(
				'label'        => __( 'Enable Password Eye Icon', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
				'description'  => __( 'Show eye icon in password fields to toggle password visibility.', 'smart-loginizer' ),
			)
		);

		$this->add_control(
			'password_toggle_eye_icon',
			array(
				'label'     => __( 'Eye Icon (Show Password)', 'smart-loginizer' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'far fa-eye',
					'library' => 'fa-regular',
				),
				'condition' => array(
					'show_password_toggle' => 'yes',
				),
			)
		);

		$this->add_control(
			'password_toggle_eye_slash_icon',
			array(
				'label'     => __( 'Eye Slash Icon (Hide Password)', 'smart-loginizer' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'far fa-eye-slash',
					'library' => 'fa-regular',
				),
				'condition' => array(
					'show_password_toggle' => 'yes',
				),
			)
		);

		$this->add_control(
			'email_icon',
			array(
				'label'     => __( 'Email Icon (Registration)', 'smart-loginizer' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(),
				'condition' => array(
					'show_field_icons' => 'yes',
				),
			)
		);

		$this->add_control(
			'email_icon_position',
			array(
				'label'     => __( 'Email Icon Position', 'smart-loginizer' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'left',
				'options'   => array(
					'left'  => __( 'Left', 'smart-loginizer' ),
					'right' => __( 'Right', 'smart-loginizer' ),
				),
				'condition' => array(
					'show_field_icons' => 'yes',
					'email_icon[value]!' => '',
				),
			)
		);

		$this->end_controls_section();

		// Security section.
		$this->start_controls_section(
			'security_section',
			array(
				'label' => __( 'Security', 'smart-loginizer' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		// Get global reCAPTCHA setting for default value.
		$global_settings = get_option( 'smart_loginizer_settings', array() );
		$global_recaptcha_enabled = 'yes' === ( $global_settings['enable_recaptcha'] ?? 'no' );

		$this->add_control(
			'enable_recaptcha',
			array(
				'label'        => __( 'Enable reCAPTCHA', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => $global_recaptcha_enabled ? 'yes' : 'no',
				'description'  => __( 'Uses global reCAPTCHA settings by default. You can override it here.', 'smart-loginizer' ),
			)
		);

		$this->end_controls_section();

		// Form Container Style section.
		$this->start_controls_section(
			'container_style_section',
			array(
				'label' => __( 'Form Container Style', 'smart-loginizer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'container_width',
			array(
				'label'      => __( 'Container Width', 'smart-loginizer' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 300,
						'max' => 1200,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 500,
				),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-auth-form-container' => 'width: {{SIZE}}{{UNIT}}; max-width: 100%;',
				),
			)
		);

		$this->add_control(
			'container_height_auto',
			array(
				'label'        => __( 'Auto Height', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'Enable to let container height adjust automatically based on content.', 'smart-loginizer' ),
			)
		);

		$this->add_responsive_control(
			'container_height',
			array(
				'label'      => __( 'Container Height', 'smart-loginizer' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vh', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 200,
						'max' => 1000,
					),
					'%'  => array(
						'min' => 30,
						'max' => 100,
					),
					'vh' => array(
						'min' => 30,
						'max' => 100,
					),
					'em' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'condition'  => array(
					'container_height_auto!' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-auth-form-container' => 'height: {{SIZE}}{{UNIT}} !important; overflow-y: auto;',
				),
			)
		);

		$this->add_responsive_control(
			'container_padding',
			array(
				'label'      => __( 'Padding', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-auth-form-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'container_background',
				'selector' => '{{WRAPPER}} .smart-loginizer-auth-form-container',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'container_border',
				'selector' => '{{WRAPPER}} .smart-loginizer-auth-form-container',
			)
		);

		$this->add_control(
			'container_border_radius',
			array(
				'label'      => __( 'Border Radius', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-auth-form-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'container_box_shadow',
				'selector' => '{{WRAPPER}} .smart-loginizer-auth-form-container',
			)
		);

		$this->add_responsive_control(
			'container_margin',
			array(
				'label'      => __( 'Margin', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-auth-form-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'container_alignment',
			array(
				'label'   => __( 'Alignment', 'smart-loginizer' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left'   => array(
						'title' => __( 'Left', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-auth-form-wrapper' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-form-link-wrapper' => 'text-align: {{VALUE}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'form_alignment',
			array(
				'label'   => __( 'Form Content Alignment', 'smart-loginizer' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left'   => array(
						'title' => __( 'Left', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-auth-form-panel' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-auth-form-panel .smart-loginizer-form-field' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-form-link-wrapper' => 'text-align: {{VALUE}} !important;',
				),
			)
		);

		$this->end_controls_section();

		// Field Style section.
		$this->start_controls_section(
			'field_style_section',
			array(
				'label' => __( 'Field Style', 'smart-loginizer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'field_spacing',
			array(
				'label'      => __( 'Field Spacing', 'smart-loginizer' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-form-field' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'label_style',
			array(
				'label'     => __( 'Label', 'smart-loginizer' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => __( 'Label Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-form-field label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'label_background',
				'label'    => __( 'Label Background', 'smart-loginizer' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-form-field label',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .smart-loginizer-form-field label',
			)
		);

		$this->add_responsive_control(
			'label_margin',
			array(
				'label'      => __( 'Label Margin', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-form-field label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'input_style',
			array(
				'label'     => __( 'Input Field', 'smart-loginizer' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'input_background',
				'label'    => __( 'Background', 'smart-loginizer' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-form-field input[type="text"],
					{{WRAPPER}} .smart-loginizer-form-field input[type="email"],
					{{WRAPPER}} .smart-loginizer-form-field input[type="password"]',
			)
		);

		$this->add_control(
			'input_color',
			array(
				'label'     => __( 'Text Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-form-field input[type="text"],
					{{WRAPPER}} .smart-loginizer-form-field input[type="email"],
					{{WRAPPER}} .smart-loginizer-form-field input[type="password"]' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'input_typography',
				'selector' => '{{WRAPPER}} .smart-loginizer-form-field input[type="text"],
					{{WRAPPER}} .smart-loginizer-form-field input[type="email"],
					{{WRAPPER}} .smart-loginizer-form-field input[type="password"]',
			)
		);

		$this->add_responsive_control(
			'input_padding',
			array(
				'label'      => __( 'Padding', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-form-field input[type="text"],
					{{WRAPPER}} .smart-loginizer-form-field input[type="email"],
					{{WRAPPER}} .smart-loginizer-form-field input[type="password"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'input_border',
				'selector' => '{{WRAPPER}} .smart-loginizer-form-field input[type="text"],
					{{WRAPPER}} .smart-loginizer-form-field input[type="email"],
					{{WRAPPER}} .smart-loginizer-form-field input[type="password"]',
			)
		);

		$this->add_control(
			'input_border_radius',
			array(
				'label'      => __( 'Border Radius', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-form-field input[type="text"],
					{{WRAPPER}} .smart-loginizer-form-field input[type="email"],
					{{WRAPPER}} .smart-loginizer-form-field input[type="password"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'input_focus_heading',
			array(
				'label'     => __( 'Focus State', 'smart-loginizer' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'input_focus_border_color',
			array(
				'label'     => __( 'Focus Border Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-form-field input:focus' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'input_focus_background',
				'label'    => __( 'Focus Background', 'smart-loginizer' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-form-field input:focus',
			)
		);

		$this->add_control(
			'input_focus_text_color',
			array(
				'label'     => __( 'Focus Text Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-form-field input:focus' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'placeholder_style',
			array(
				'label'     => __( 'Placeholder', 'smart-loginizer' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'placeholder_color',
			array(
				'label'     => __( 'Placeholder Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-form-field input::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-form-field input::-webkit-input-placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-form-field input::-moz-placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-form-field input:-ms-input-placeholder' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'placeholder_typography',
				'label'    => __( 'Placeholder Typography', 'smart-loginizer' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-form-field input::placeholder,
					{{WRAPPER}} .smart-loginizer-form-field input::-webkit-input-placeholder,
					{{WRAPPER}} .smart-loginizer-form-field input::-moz-placeholder,
					{{WRAPPER}} .smart-loginizer-form-field input:-ms-input-placeholder',
			)
		);

		$this->add_control(
			'placeholder_opacity',
			array(
				'label'      => __( 'Placeholder Opacity', 'smart-loginizer' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0.6,
				),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-form-field input::placeholder' => 'opacity: {{SIZE}};',
					'{{WRAPPER}} .smart-loginizer-form-field input::-webkit-input-placeholder' => 'opacity: {{SIZE}};',
					'{{WRAPPER}} .smart-loginizer-form-field input::-moz-placeholder' => 'opacity: {{SIZE}};',
					'{{WRAPPER}} .smart-loginizer-form-field input:-ms-input-placeholder' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->end_controls_section();

		// Links Style section.
		$this->start_controls_section(
			'links_style_section',
			array(
				'label' => __( 'Form Links Style', 'smart-loginizer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label'     => __( 'Link Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-form-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'link_background',
				'label'    => __( 'Link Background', 'smart-loginizer' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-form-link',
			)
		);

		$this->add_control(
			'link_hover_color',
			array(
				'label'     => __( 'Link Hover Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-form-link:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'link_hover_background',
				'label'    => __( 'Link Hover Background', 'smart-loginizer' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-form-link:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'link_typography',
				'selector' => '{{WRAPPER}} .smart-loginizer-form-link',
			)
		);

		$this->add_responsive_control(
			'link_margin',
			array(
				'label'      => __( 'Link Margin', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-form-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Remember Me Style section.
		$this->start_controls_section(
			'remember_me_style_section',
			array(
				'label' => __( 'Remember Me Style', 'smart-loginizer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'remember_me_alignment',
			array(
				'label'   => __( 'Alignment', 'smart-loginizer' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left'   => array(
						'title' => __( 'Left', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default' => 'left',
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-auth-form-panel .smart-loginizer-form-field label:has(input[type="checkbox"][name="remember"])' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'remember_me_typography',
				'label'    => __( 'Typography', 'smart-loginizer' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-auth-form-panel .smart-loginizer-form-field label:has(input[type="checkbox"][name="remember"])',
			)
		);

		$this->add_control(
			'remember_me_text_color',
			array(
				'label'     => __( 'Text Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-auth-form-panel .smart-loginizer-form-field label:has(input[type="checkbox"][name="remember"])' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'remember_me_spacing',
			array(
				'label'      => __( 'Spacing', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-auth-form-panel .smart-loginizer-form-field:has(input[type="checkbox"][name="remember"])' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'remember_me_checkbox_heading',
			array(
				'label'     => __( 'Checkbox', 'smart-loginizer' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'remember_me_checkbox_size',
			array(
				'label'      => __( 'Size', 'smart-loginizer' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 30,
					),
					'em' => array(
						'min' => 0.5,
						'max' => 2,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-auth-form-panel .smart-loginizer-form-field input[type="checkbox"][name="remember"]' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'remember_me_checkbox_color',
			array(
				'label'     => __( 'Checkbox Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-auth-form-panel .smart-loginizer-form-field input[type="checkbox"][name="remember"]' => 'accent-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'remember_me_checkbox_border',
				'label'    => __( 'Border', 'smart-loginizer' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-auth-form-panel .smart-loginizer-form-field input[type="checkbox"][name="remember"]',
			)
		);

		$this->add_control(
			'remember_me_checkbox_border_radius',
			array(
				'label'      => __( 'Border Radius', 'smart-loginizer' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
					'%'  => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-auth-form-panel .smart-loginizer-form-field input[type="checkbox"][name="remember"]' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'remember_me_checkbox_margin',
			array(
				'label'      => __( 'Checkbox Margin', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-auth-form-panel .smart-loginizer-form-field input[type="checkbox"][name="remember"]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'remember_me_hover_heading',
			array(
				'label'     => __( 'Hover State', 'smart-loginizer' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'remember_me_hover_text_color',
			array(
				'label'     => __( 'Hover Text Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-auth-form-panel .smart-loginizer-form-field label:has(input[type="checkbox"][name="remember"]):hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'remember_me_hover_checkbox_border_color',
			array(
				'label'     => __( 'Hover Border Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-auth-form-panel .smart-loginizer-form-field input[type="checkbox"][name="remember"]:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		// Form Header Style section.
		$this->start_controls_section(
			'form_header_style_section',
			array(
				'label' => __( 'Form Header Style', 'smart-loginizer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'logo_heading',
			array(
				'label' => __( 'Logo', 'smart-loginizer' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'logo_width',
			array(
				'label'      => __( 'Logo Width', 'smart-loginizer' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 50,
						'max' => 300,
					),
					'%'  => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-form-header-logo img' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
				),
			)
		);

		$this->add_responsive_control(
			'logo_alignment',
			array(
				'label'   => __( 'Alignment', 'smart-loginizer' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left'   => array(
						'title' => __( 'Left', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-form-header-logo' => 'text-align: {{VALUE}} !important;',
					'{{WRAPPER}} .smart-loginizer-form-header-logo img' => 'display: inline-block; margin: 0 !important;',
				),
			)
		);

		$this->add_responsive_control(
			'logo_margin',
			array(
				'label'      => __( 'Margin', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-form-header-logo' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'headline_heading',
			array(
				'label'     => __( 'Headline', 'smart-loginizer' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'headline_alignment',
			array(
				'label'   => __( 'Alignment', 'smart-loginizer' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left'    => array(
						'title' => __( 'Left', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => __( 'Justify', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-form-headline' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'headline_typography',
				'label'    => __( 'Typography', 'smart-loginizer' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-form-headline',
			)
		);

		$this->add_control(
			'headline_color',
			array(
				'label'     => __( 'Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-form-headline' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'headline_background',
				'label'    => __( 'Background', 'smart-loginizer' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-form-headline',
			)
		);

		$this->add_responsive_control(
			'headline_padding',
			array(
				'label'      => __( 'Padding', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-form-headline' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'headline_margin',
			array(
				'label'      => __( 'Margin', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-form-headline' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'headline_border',
				'selector' => '{{WRAPPER}} .smart-loginizer-form-headline',
			)
		);

		$this->add_control(
			'headline_border_radius',
			array(
				'label'      => __( 'Border Radius', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-form-headline' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'headline_box_shadow',
				'selector' => '{{WRAPPER}} .smart-loginizer-form-headline',
			)
		);

		$this->add_control(
			'description_heading',
			array(
				'label'     => __( 'Description', 'smart-loginizer' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'description_alignment',
			array(
				'label'   => __( 'Alignment', 'smart-loginizer' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left'    => array(
						'title' => __( 'Left', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => __( 'Justify', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-form-description' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'label'    => __( 'Typography', 'smart-loginizer' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-form-description',
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label'     => __( 'Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-form-description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'description_background',
				'label'    => __( 'Background', 'smart-loginizer' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-form-description',
			)
		);

		$this->add_responsive_control(
			'description_padding',
			array(
				'label'      => __( 'Padding', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-form-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'description_margin',
			array(
				'label'      => __( 'Margin', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-form-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'description_border',
				'selector' => '{{WRAPPER}} .smart-loginizer-form-description',
			)
		);

		$this->add_control(
			'description_border_radius',
			array(
				'label'      => __( 'Border Radius', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-form-description' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'description_box_shadow',
				'selector' => '{{WRAPPER}} .smart-loginizer-form-description',
			)
		);

		$this->add_responsive_control(
			'header_margin',
			array(
				'label'      => __( 'Header Container Margin', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-form-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Form Field Icons Style section.
		$this->start_controls_section(
			'form_field_icons_style_section',
			array(
				'label' => __( 'Form Field Icons', 'smart-loginizer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Icon Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#666',
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-input-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-input-icon svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Icon Size', 'smart-loginizer' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
					'em' => array(
						'min' => 0.5,
						'max' => 3,
					),
					'rem' => array(
						'min' => 0.5,
						'max' => 3,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 18,
				),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-input-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .smart-loginizer-input-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'icon_background',
				'label'    => __( 'Icon Background', 'smart-loginizer' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-input-icon',
			)
		);

		$this->add_responsive_control(
			'icon_padding',
			array(
				'label'      => __( 'Icon Padding', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-input-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_margin',
			array(
				'label'      => __( 'Icon Margin', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-input-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'icon_border',
				'label'    => __( 'Icon Border', 'smart-loginizer' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-input-icon',
			)
		);

		$this->add_control(
			'icon_border_radius',
			array(
				'label'      => __( 'Border Radius', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-input-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'icon_box_shadow',
				'label'    => __( 'Icon Box Shadow', 'smart-loginizer' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-input-icon',
			)
		);

		$this->add_control(
			'icon_hover_heading',
			array(
				'label'     => __( 'Hover State', 'smart-loginizer' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'icon_hover_color',
			array(
				'label'     => __( 'Hover Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-input-wrapper:hover .smart-loginizer-input-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-input-wrapper:hover .smart-loginizer-input-icon svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'icon_hover_background',
				'label'    => __( 'Hover Background', 'smart-loginizer' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-input-wrapper:hover .smart-loginizer-input-icon',
			)
		);

		$this->add_control(
			'icon_hover_border_color',
			array(
				'label'     => __( 'Hover Border Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-input-wrapper:hover .smart-loginizer-input-icon' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'icon_hover_box_shadow',
				'label'    => __( 'Hover Box Shadow', 'smart-loginizer' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-input-wrapper:hover .smart-loginizer-input-icon',
			)
		);

		$this->add_control(
			'icon_focus_heading',
			array(
				'label'     => __( 'Focus State', 'smart-loginizer' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'icon_focus_color',
			array(
				'label'     => __( 'Focus Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-form-field:focus-within .smart-loginizer-input-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-form-field:focus-within .smart-loginizer-input-icon svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'icon_focus_background',
				'label'    => __( 'Focus Background', 'smart-loginizer' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-form-field:focus-within .smart-loginizer-input-icon',
			)
		);

		$this->add_control(
			'icon_focus_border_color',
			array(
				'label'     => __( 'Focus Border Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-form-field:focus-within .smart-loginizer-input-icon' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		// Password Toggle Style section.
		$this->start_controls_section(
			'password_toggle_style_section',
			array(
				'label' => __( 'Password Toggle Style', 'smart-loginizer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'password_toggle_size',
			array(
				'label'      => __( 'Icon Size', 'smart-loginizer' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 30,
					),
					'em' => array(
						'min' => 0.5,
						'max' => 2,
					),
					'rem' => array(
						'min' => 0.5,
						'max' => 2,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 18,
				),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-password-toggle' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .smart-loginizer-password-toggle i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .smart-loginizer-password-toggle svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .smart-loginizer-password-toggle .smart-loginizer-eye-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .smart-loginizer-password-toggle .smart-loginizer-eye-slash-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .smart-loginizer-password-toggle .smart-loginizer-eye-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .smart-loginizer-password-toggle .smart-loginizer-eye-slash-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'password_toggle_color',
			array(
				'label'     => __( 'Icon Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#666',
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-password-toggle' => 'color: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-password-toggle i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-password-toggle svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-password-toggle svg path' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-password-toggle .smart-loginizer-eye-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-password-toggle .smart-loginizer-eye-slash-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-password-toggle .smart-loginizer-eye-icon svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-password-toggle .smart-loginizer-eye-slash-icon svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'password_toggle_hover_color',
			array(
				'label'     => __( 'Hover Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-password-toggle:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-password-toggle:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-password-toggle:hover svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-password-toggle:hover svg path' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-password-toggle:hover .smart-loginizer-eye-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-password-toggle:hover .smart-loginizer-eye-slash-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-password-toggle:hover .smart-loginizer-eye-icon svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-password-toggle:hover .smart-loginizer-eye-slash-icon svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'password_toggle_padding',
			array(
				'label'      => __( 'Padding', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-password-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'password_toggle_margin',
			array(
				'label'      => __( 'Margin', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-password-toggle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'password_toggle_cursor',
			array(
				'label'   => __( 'Cursor', 'smart-loginizer' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'pointer',
				'options' => array(
					'pointer' => __( 'Pointer', 'smart-loginizer' ),
					'default' => __( 'Default', 'smart-loginizer' ),
					'not-allowed' => __( 'Not Allowed', 'smart-loginizer' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-password-toggle' => 'cursor: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		// Form Button Style section (for buttons inside inline forms).
		$this->start_controls_section(
			'form_button_style_section',
			array(
				'label' => __( 'Form Button Style', 'smart-loginizer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'form_button_position',
			array(
				'label'   => __( 'Position', 'smart-loginizer' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left'    => array(
						'title' => __( 'Left', 'smart-loginizer' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'smart-loginizer' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'smart-loginizer' ),
						'icon'  => 'eicon-h-align-right',
					),
					'stretch' => array(
						'title' => __( 'Stretch', 'smart-loginizer' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'default' => 'stretch',
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-submit-btn' => '{{VALUE}}',
				),
				'selectors_dictionary' => array(
					'left'    => 'margin-left: 0; margin-right: auto; width: auto;',
					'center'  => 'margin-left: auto; margin-right: auto; width: auto;',
					'right'   => 'margin-left: auto; margin-right: 0; width: auto;',
					'stretch' => 'width: 100%; margin-left: 0; margin-right: 0;',
				),
			)
		);

		$this->add_responsive_control(
			'form_button_alignment',
			array(
				'label'   => __( 'Alignment', 'smart-loginizer' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left'    => array(
						'title' => __( 'Left', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => __( 'Justify', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-submit-btn' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'form_button_typography',
				'selector' => '{{WRAPPER}} .smart-loginizer-submit-btn',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'form_button_background',
				'label'    => __( 'Background', 'smart-loginizer' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-submit-btn',
			)
		);

		$this->add_control(
			'form_button_text_color',
			array(
				'label'     => __( 'Text Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-submit-btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'form_button_padding',
			array(
				'label'      => __( 'Padding', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-submit-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'form_button_border',
				'selector' => '{{WRAPPER}} .smart-loginizer-submit-btn',
			)
		);

		$this->add_control(
			'form_button_border_radius',
			array(
				'label'      => __( 'Border Radius', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-submit-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'form_button_box_shadow',
				'label'    => __( 'Box Shadow', 'smart-loginizer' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-submit-btn',
			)
		);

		$this->add_control(
			'form_button_hover_heading',
			array(
				'label'     => __( 'Hover State', 'smart-loginizer' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'form_button_hover_background',
				'label'    => __( 'Hover Background', 'smart-loginizer' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-submit-btn:hover',
			)
		);

		$this->add_control(
			'form_button_hover_text_color',
			array(
				'label'     => __( 'Hover Text Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-submit-btn:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'form_button_hover_border_color',
			array(
				'label'     => __( 'Hover Border Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-submit-btn:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'form_button_hover_box_shadow',
				'label'    => __( 'Hover Box Shadow', 'smart-loginizer' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-submit-btn:hover',
			)
		);

		$this->end_controls_section();

		// Social Login Style section.
		$this->start_controls_section(
			'social_login_style_section',
			array(
				'label' => __( 'Social Login Style', 'smart-loginizer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'social_login_divider_heading',
			array(
				'label' => __( 'Divider', 'smart-loginizer' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'social_login_divider_color',
			array(
				'label'     => __( 'Divider Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-social-login-divider' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'social_login_text_color',
			array(
				'label'     => __( 'Text Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-social-login-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'social_login_text_typography',
				'selector' => '{{WRAPPER}} .smart-loginizer-social-login-text',
			)
		);

		$this->add_responsive_control(
			'social_login_divider_spacing',
			array(
				'label'      => __( 'Spacing', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-social-login-divider' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'social_login_buttons_heading',
			array(
				'label'     => __( 'Buttons', 'smart-loginizer' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'social_login_icon_position',
			array(
				'label'   => __( 'Icon Position', 'smart-loginizer' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left'  => array(
						'title' => __( 'Left', 'smart-loginizer' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'smart-loginizer' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default' => 'left',
				'toggle'  => false,
			)
		);

		$this->add_control(
			'social_login_text_align',
			array(
				'label'   => __( 'Text Alignment', 'smart-loginizer' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left'   => array(
						'title' => __( 'Left', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'smart-loginizer' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default' => 'center',
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-social-text' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'social_login_buttons_gap',
			array(
				'label'      => __( 'Gap Between Buttons', 'smart-loginizer' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'em' => array(
						'min' => 0,
						'max' => 3,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-social-login-buttons' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'social_login_button_padding',
			array(
				'label'      => __( 'Button Padding', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-social-login-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'social_login_button_bg',
			array(
				'label'     => __( 'Background Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-social-login-button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'social_login_button_text_color',
			array(
				'label'     => __( 'Text Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-social-login-button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'social_login_button_typography',
				'selector' => '{{WRAPPER}} .smart-loginizer-social-login-button',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'social_login_button_border',
				'selector' => '{{WRAPPER}} .smart-loginizer-social-login-button',
			)
		);

		$this->add_responsive_control(
			'social_login_button_border_radius',
			array(
				'label'      => __( 'Border Radius', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-social-login-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'social_login_button_box_shadow',
				'selector' => '{{WRAPPER}} .smart-loginizer-social-login-button',
			)
		);

		$this->add_control(
			'social_login_button_hover_heading',
			array(
				'label'     => __( 'Hover', 'smart-loginizer' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'social_login_button_hover_bg',
			array(
				'label'     => __( 'Background Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-social-login-button:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'social_login_button_hover_text_color',
			array(
				'label'     => __( 'Text Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-social-login-button:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'social_login_button_hover_border_color',
			array(
				'label'     => __( 'Border Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-social-login-button:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'social_login_button_hover_box_shadow',
				'selector' => '{{WRAPPER}} .smart-loginizer-social-login-button:hover',
			)
		);

		$this->add_responsive_control(
			'social_login_icon_size',
			array(
				'label'      => __( 'Icon Size', 'smart-loginizer' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
					'em' => array(
						'min' => 0.5,
						'max' => 3,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-social-icon' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .smart-loginizer-social-icon i' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .smart-loginizer-social-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'social_login_icon_spacing',
			array(
				'label'      => __( 'Icon Spacing', 'smart-loginizer' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
					'em' => array(
						'min' => 0,
						'max' => 2,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 8,
				),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .smart-loginizer-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'social_login_icon_color_heading',
			array(
				'label'     => __( 'Icon Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'social_login_icon_color',
			array(
				'label'     => __( 'Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-social-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-social-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-social-icon svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-social-icon svg path' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-social-icon svg g' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'social_login_icon_hover_color',
			array(
				'label'     => __( 'Hover Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-social-login-button:hover .smart-loginizer-social-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-social-login-button:hover .smart-loginizer-social-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-social-login-button:hover .smart-loginizer-social-icon svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-social-login-button:hover .smart-loginizer-social-icon svg path' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-social-login-button:hover .smart-loginizer-social-icon svg g' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$default_form              = $settings['default_form'] ?? 'login';
		$enable_login              = 'yes' === ( $settings['enable_login'] ?? 'yes' );
		$enable_register           = 'yes' === ( $settings['enable_register'] ?? 'yes' );
		$enable_reset              = 'yes' === ( $settings['enable_reset'] ?? 'yes' );
		$show_register_link        = 'yes' === ( $settings['show_register_link'] ?? 'yes' );
		$register_link_text        = $settings['register_link_text'] ?? __( "Don't have an account? Register", 'smart-loginizer' );
		$show_reset_link           = 'yes' === ( $settings['show_reset_link'] ?? 'yes' );
		$reset_link_text           = $settings['reset_link_text'] ?? __( 'Forgot your password?', 'smart-loginizer' );
		$show_login_link_register  = 'yes' === ( $settings['show_login_link_register'] ?? 'yes' );
		$login_link_text_register  = $settings['login_link_text_register'] ?? __( 'Already have an account? Login', 'smart-loginizer' );
		$show_login_link_reset     = 'yes' === ( $settings['show_login_link_reset'] ?? 'yes' );
		$login_link_text_reset     = $settings['login_link_text_reset'] ?? __( 'Back to Login', 'smart-loginizer' );
		$redirect_after_login      = $settings['redirect_after_login']['url'] ?? '';
		$show_when_logged_in       = 'yes' === ( $settings['show_when_logged_in'] ?? 'no' );
		$logged_in_message        = $settings['logged_in_message'] ?? /* translators: %s: User display name */
			__( 'You are already logged in as %s.', 'smart-loginizer' );
		$show_site_logo            = 'yes' === ( $settings['show_site_logo'] ?? 'no' );
		$site_logo                 = $settings['site_logo'] ?? array();
		$use_separate_headlines    = 'yes' === ( $settings['use_separate_headlines'] ?? 'no' );
		$default_headline          = $settings['default_headline'] ?? '';
		$default_description       = $settings['default_description'] ?? '';
		$login_headline            = $settings['login_headline'] ?? '';
		$login_description         = $settings['login_description'] ?? '';
		$register_headline         = $settings['register_headline'] ?? '';
		$register_description      = $settings['register_description'] ?? '';
		$reset_headline            = $settings['reset_headline'] ?? '';
		$reset_description         = $settings['reset_description'] ?? '';
		$form_style                = $settings['form_style'] ?? 'default';
		$form_style_tablet         = $settings['form_style_tablet'] ?? 'default';
		$form_style_mobile         = $settings['form_style_mobile'] ?? 'default';
		$show_field_icons          = 'yes' === ( $settings['show_field_icons'] ?? 'no' );
		$username_icon             = $show_field_icons ? ( $settings['username_icon'] ?? array() ) : array();
		$username_icon_position    = $settings['username_icon_position'] ?? 'left';
		$password_icon             = $show_field_icons ? ( $settings['password_icon'] ?? array() ) : array();
		$password_icon_position    = $settings['password_icon_position'] ?? 'left';
		$email_icon                = $show_field_icons ? ( $settings['email_icon'] ?? array() ) : array();
		$email_icon_position       = $settings['email_icon_position'] ?? 'left';
		$show_social_login         = 'yes' === ( $settings['show_social_login'] ?? 'no' );
		$social_login_text         = $settings['social_login_text'] ?? __( 'Or continue with', 'smart-loginizer' );
		$social_icon_settings      = array(
			'google_icon'   => $settings['google_icon'] ?? array(),
			'x_icon'        => $settings['x_icon'] ?? array(),
			'linkedin_icon' => $settings['linkedin_icon'] ?? array(),
			'facebook_icon' => $settings['facebook_icon'] ?? array(),
		);
		$social_icon_position      = $settings['social_login_icon_position'] ?? 'left';
		$social_login_position     = $settings['social_login_position'] ?? 'after_submit';
		$show_password_toggle      = 'yes' === ( $settings['show_password_toggle'] ?? 'yes' );
		$password_toggle_eye_icon = $show_password_toggle ? ( $settings['password_toggle_eye_icon'] ?? array() ) : array();
		$password_toggle_eye_slash_icon = $show_password_toggle ? ( $settings['password_toggle_eye_slash_icon'] ?? array() ) : array();

		$is_logged_in = is_user_logged_in();

		// Show logged in message if user is logged in and option is disabled.
		if ( $is_logged_in && ! $show_when_logged_in ) {
			$current_user = wp_get_current_user();
			$message = str_replace( '%s', esc_html( $current_user->display_name ), $logged_in_message );
			?>
			<div class="smart-loginizer-auth-form-wrapper">
				<div class="smart-loginizer-logged-in-message">
					<?php echo wp_kses_post( $message ); ?>
				</div>
			</div>
			<?php
			return;
		}

		$form_id = 'smart-loginizer-auth-form-' . $this->get_id();

		$wrapper_class = 'smart-loginizer-auth-form-wrapper smart-loginizer-form-wrapper smart-loginizer-form-style-' . esc_attr( $form_style );
		?>
		<div class="<?php echo esc_attr( $wrapper_class ); ?>" 
			data-form-style-desktop="<?php echo esc_attr( $form_style ); ?>"
			data-form-style-tablet="<?php echo esc_attr( $form_style_tablet ); ?>"
			data-form-style-mobile="<?php echo esc_attr( $form_style_mobile ); ?>">
			<div class="smart-loginizer-auth-form-container" id="<?php echo esc_attr( $form_id ); ?>">
				<?php if ( $enable_login ) : ?>
					<div class="smart-loginizer-auth-form-panel <?php echo 'login' === $default_form ? 'active' : ''; ?>" data-panel="login">
						<?php
						$headline   = $use_separate_headlines ? $login_headline : $default_headline;
						$description = $use_separate_headlines ? $login_description : $default_description;
						$this->render_login_form( $show_register_link, $register_link_text, $show_reset_link, $reset_link_text, $enable_register, $enable_reset, $redirect_after_login, $show_site_logo, $site_logo, $headline, $description, $username_icon, $username_icon_position, $password_icon, $password_icon_position, $show_social_login, $social_login_text, $social_icon_settings, $social_icon_position, $social_login_position, $show_password_toggle, $password_toggle_eye_icon, $password_toggle_eye_slash_icon );
						?>
					</div>
				<?php endif; ?>

				<?php if ( $enable_register ) : ?>
					<div class="smart-loginizer-auth-form-panel <?php echo 'register' === $default_form ? 'active' : ''; ?>" data-panel="register">
						<?php
						$headline   = $use_separate_headlines ? $register_headline : $default_headline;
						$description = $use_separate_headlines ? $register_description : $default_description;
						$this->render_register_form( $show_login_link_register, $login_link_text_register, $enable_login, $show_site_logo, $site_logo, $headline, $description, $username_icon, $username_icon_position, $email_icon, $email_icon_position, $password_icon, $password_icon_position, $show_social_login, $social_login_text, $social_icon_settings, $social_icon_position, $social_login_position, $show_password_toggle, $password_toggle_eye_icon, $password_toggle_eye_slash_icon );
						?>
					</div>
				<?php endif; ?>

				<?php if ( $enable_reset ) : ?>
					<div class="smart-loginizer-auth-form-panel <?php echo 'reset' === $default_form ? 'active' : ''; ?>" data-panel="reset">
						<?php
						$headline   = $use_separate_headlines ? $reset_headline : $default_headline;
						$description = $use_separate_headlines ? $reset_description : $default_description;
						$this->render_reset_form( $show_login_link_reset, $login_link_text_reset, $enable_login, $show_site_logo, $site_logo, $headline, $description, $email_icon, $email_icon_position );
						?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render login form.
	 *
	 * @param bool   $show_register_link Show register link.
	 * @param string $register_link_text Register link text.
	 * @param bool   $show_reset_link Show reset link.
	 * @param string $reset_link_text Reset link text.
	 * @param bool   $enable_register Enable register form.
	 * @param bool   $enable_reset Enable reset form.
	 * @param string $redirect_after_login Redirect URL after login.
	 * @param bool   $show_site_logo Show site logo.
	 * @param array  $site_logo Site logo data.
	 * @param string $headline Form headline.
	 * @param string $description Form description.
	 * @param array  $username_icon Username icon data.
	 * @param string $username_icon_position Username icon position.
	 * @param array  $password_icon Password icon data.
	 * @param string $password_icon_position Password icon position.
	 * @return void
	 */
	private function render_login_form( bool $show_register_link = true, string $register_link_text = '', bool $show_reset_link = true, string $reset_link_text = '', bool $enable_register = true, bool $enable_reset = true, string $redirect_after_login = '', bool $show_site_logo = false, array $site_logo = array(), string $headline = '', string $description = '', array $username_icon = array(), string $username_icon_position = 'left', array $password_icon = array(), string $password_icon_position = 'left', bool $show_social_login = false, string $social_login_text = '', array $social_icon_settings = array(), string $social_icon_position = 'left', string $social_login_position = 'after_submit', bool $show_password_toggle = true, array $password_toggle_eye_icon = array(), array $password_toggle_eye_slash_icon = array() ): void {
		// Get widget settings for reCAPTCHA.
		$settings = $this->get_settings_for_display();
		$global_settings = get_option( 'smart_loginizer_settings', array() );
		$widget_recaptcha = isset( $settings['enable_recaptcha'] ) ? $settings['enable_recaptcha'] : null;
		if ( null === $widget_recaptcha ) {
			// Use global setting if widget setting is not set.
			$recaptcha_enabled = 'yes' === ( $global_settings['enable_recaptcha'] ?? 'no' );
		} else {
			// Use widget setting (user override).
			$recaptcha_enabled = 'yes' === $widget_recaptcha;
		}
		$recaptcha_version = $global_settings['recaptcha_version'] ?? 'v3';
		$recaptcha_site_key = $this->helpers->get_option( 'recaptcha_site_key' );
		
		// Get redirect URL.
		if ( empty( $redirect_after_login ) ) {
			$redirect_after_login = $this->helpers->get_login_redirect_url();
		}
		
		?>
		<?php if ( $show_site_logo && ! empty( $site_logo['url'] ) ) : ?>
			<div class="smart-loginizer-form-header-logo">
				<img src="<?php echo esc_url( $site_logo['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $headline ) || ! empty( $description ) ) : ?>
			<div class="smart-loginizer-form-header">
				<?php if ( ! empty( $headline ) ) : ?>
					<h3 class="smart-loginizer-form-headline"><?php echo esc_html( $headline ); ?></h3>
				<?php endif; ?>
				<?php if ( ! empty( $description ) ) : ?>
					<p class="smart-loginizer-form-description"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<form class="smart-loginizer-login-form smart-loginizer-ajax-form" method="post">
			<?php wp_nonce_field( 'smart_loginizer_nonce', 'smart_loginizer_nonce' ); ?>
			<input type="hidden" name="action" value="smart_loginizer_login" />
			<?php if ( ! empty( $redirect_after_login ) ) : ?>
				<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect_after_login ); ?>" />
			<?php endif; ?>

			<?php if ( $show_social_login && 'top' === $social_login_position ) : ?>
				<?php $this->render_social_login_buttons( $show_social_login, $social_login_text, true, $social_icon_settings, $social_icon_position ); ?>
			<?php endif; ?>

			<div class="smart-loginizer-form-field">
				<label for="inline_login_username"><?php esc_html_e( 'Username / Email', 'smart-loginizer' ); ?></label>
				<div class="smart-loginizer-input-wrapper smart-loginizer-icon-<?php echo esc_attr( $username_icon_position ); ?>">
					<?php if ( ! empty( $username_icon['value'] ) && 'left' === $username_icon_position ) : ?>
						<span class="smart-loginizer-input-icon">
							<?php \Elementor\Icons_Manager::render_icon( $username_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
					<input type="text" id="inline_login_username" name="username" required />
					<?php if ( ! empty( $username_icon['value'] ) && 'right' === $username_icon_position ) : ?>
						<span class="smart-loginizer-input-icon">
							<?php \Elementor\Icons_Manager::render_icon( $username_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $show_social_login && 'after_username' === $social_login_position ) : ?>
				<?php $this->render_social_login_buttons( $show_social_login, $social_login_text, false, $social_icon_settings, $social_icon_position ); ?>
			<?php endif; ?>

			<div class="smart-loginizer-form-field">
				<label for="inline_login_password"><?php esc_html_e( 'Password', 'smart-loginizer' ); ?></label>
				<div class="smart-loginizer-input-wrapper smart-loginizer-icon-<?php echo esc_attr( $password_icon_position ); ?>">
					<?php if ( ! empty( $password_icon['value'] ) && 'left' === $password_icon_position ) : ?>
						<span class="smart-loginizer-input-icon">
							<?php \Elementor\Icons_Manager::render_icon( $password_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
					<input type="password" id="inline_login_password" name="password" required />
					<?php if ( $show_password_toggle ) : ?>
						<button type="button" class="smart-loginizer-password-toggle" aria-label="<?php esc_attr_e( 'Toggle password visibility', 'smart-loginizer' ); ?>" data-target="inline_login_password">
							<span class="smart-loginizer-eye-icon">
								<?php \Elementor\Icons_Manager::render_icon( $password_toggle_eye_icon, array( 'aria-hidden' => 'true' ) ); ?>
							</span>
							<span class="smart-loginizer-eye-slash-icon" style="display: none;">
								<?php \Elementor\Icons_Manager::render_icon( $password_toggle_eye_slash_icon, array( 'aria-hidden' => 'true' ) ); ?>
							</span>
						</button>
					<?php endif; ?>
					<?php if ( ! empty( $password_icon['value'] ) && 'right' === $password_icon_position ) : ?>
						<span class="smart-loginizer-input-icon">
							<?php \Elementor\Icons_Manager::render_icon( $password_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $show_social_login && 'after_password' === $social_login_position ) : ?>
				<?php $this->render_social_login_buttons( $show_social_login, $social_login_text, false, $social_icon_settings, $social_icon_position ); ?>
			<?php endif; ?>

			<div class="smart-loginizer-form-field">
				<label>
					<input type="checkbox" name="remember" value="1" />
					<?php esc_html_e( 'Remember me', 'smart-loginizer' ); ?>
				</label>
			</div>

			<?php if ( $show_social_login && 'after_remember' === $social_login_position ) : ?>
				<?php $this->render_social_login_buttons( $show_social_login, $social_login_text, false, $social_icon_settings, $social_icon_position ); ?>
			<?php endif; ?>

			<?php if ( $show_reset_link && $enable_reset ) : ?>
				<div class="smart-loginizer-form-field">
					<a href="#" class="smart-loginizer-form-link" data-switch-to="reset">
						<?php echo esc_html( $reset_link_text ); ?>
					</a>
				</div>
			<?php endif; ?>

			<?php if ( $recaptcha_enabled && ! empty( $recaptcha_site_key ) ) : ?>
				<input type="hidden" name="recaptcha_token" id="inline_recaptcha_token_login" />
				<?php if ( 'v2_checkbox' === $recaptcha_version ) : ?>
					<div class="smart-loginizer-form-field">
						<div id="inline_recaptcha_v2_login" class="smart-loginizer-recaptcha-v2"></div>
					</div>
				<?php elseif ( 'v2_invisible' === $recaptcha_version ) : ?>
					<div id="inline_recaptcha_v2_invisible_login" class="smart-loginizer-recaptcha-v2-invisible"></div>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( $show_social_login && 'after_recaptcha' === $social_login_position ) : ?>
				<?php $this->render_social_login_buttons( $show_social_login, $social_login_text, false, $social_icon_settings, $social_icon_position ); ?>
			<?php endif; ?>

			<div class="smart-loginizer-form-field">
				<button type="submit" class="smart-loginizer-submit-btn">
					<?php esc_html_e( 'Log In', 'smart-loginizer' ); ?>
				</button>
			</div>

			<?php if ( $show_social_login && 'after_submit' === $social_login_position ) : ?>
				<?php $this->render_social_login_buttons( $show_social_login, $social_login_text, false, $social_icon_settings, $social_icon_position ); ?>
			<?php endif; ?>

			<?php if ( $show_register_link && $enable_register ) : ?>
				<div class="smart-loginizer-form-field smart-loginizer-form-link-wrapper" style="margin-top: 15px;">
					<a href="#" class="smart-loginizer-form-link" data-switch-to="register">
						<?php echo esc_html( $register_link_text ); ?>
					</a>
				</div>
			<?php endif; ?>

			<div class="smart-loginizer-form-message"></div>
		</form>
		<?php
	}

	/**
	 * Render register form.
	 *
	 * @param bool   $show_login_link Show login link.
	 * @param string $login_link_text Login link text.
	 * @param bool   $enable_login Enable login form.
	 * @param bool   $show_site_logo Show site logo.
	 * @param array  $site_logo Site logo data.
	 * @param string $headline Form headline.
	 * @param string $description Form description.
	 * @param array  $username_icon Username icon data.
	 * @param string $username_icon_position Username icon position.
	 * @param array  $email_icon Email icon data.
	 * @param string $email_icon_position Email icon position.
	 * @param array  $password_icon Password icon data.
	 * @param string $password_icon_position Password icon position.
	 * @return void
	 */
	private function render_register_form( bool $show_login_link = true, string $login_link_text = '', bool $enable_login = true, bool $show_site_logo = false, array $site_logo = array(), string $headline = '', string $description = '', array $username_icon = array(), string $username_icon_position = 'left', array $email_icon = array(), string $email_icon_position = 'left', array $password_icon = array(), string $password_icon_position = 'left', bool $show_social_login = false, string $social_login_text = '', array $social_icon_settings = array(), string $social_icon_position = 'left', string $social_login_position = 'after_submit', bool $show_password_toggle = true, array $password_toggle_eye_icon = array(), array $password_toggle_eye_slash_icon = array() ): void {
		// Get widget settings for reCAPTCHA.
		$settings = $this->get_settings_for_display();
		$global_settings = get_option( 'smart_loginizer_settings', array() );
		$widget_recaptcha = isset( $settings['enable_recaptcha'] ) ? $settings['enable_recaptcha'] : null;
		if ( null === $widget_recaptcha ) {
			// Use global setting if widget setting is not set.
			$recaptcha_enabled = 'yes' === ( $global_settings['enable_recaptcha'] ?? 'no' );
		} else {
			// Use widget setting (user override).
			$recaptcha_enabled = 'yes' === $widget_recaptcha;
		}
		$recaptcha_version = $global_settings['recaptcha_version'] ?? 'v3';
		$recaptcha_site_key = $this->helpers->get_option( 'recaptcha_site_key' );
		?>
		<?php if ( $show_site_logo && ! empty( $site_logo['url'] ) ) : ?>
			<div class="smart-loginizer-form-header-logo">
				<img src="<?php echo esc_url( $site_logo['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $headline ) || ! empty( $description ) ) : ?>
			<div class="smart-loginizer-form-header">
				<?php if ( ! empty( $headline ) ) : ?>
					<h3 class="smart-loginizer-form-headline"><?php echo esc_html( $headline ); ?></h3>
				<?php endif; ?>
				<?php if ( ! empty( $description ) ) : ?>
					<p class="smart-loginizer-form-description"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<form class="smart-loginizer-registration-form smart-loginizer-ajax-form smart-loginizer-password-strength" method="post">
			<?php wp_nonce_field( 'smart_loginizer_nonce', 'smart_loginizer_nonce' ); ?>
			<input type="hidden" name="action" value="smart_loginizer_register" />

			<?php if ( $show_social_login && 'top' === $social_login_position ) : ?>
				<?php $this->render_social_login_buttons( $show_social_login, $social_login_text, true, $social_icon_settings, $social_icon_position ); ?>
			<?php endif; ?>

			<div class="smart-loginizer-form-field">
				<label for="inline_reg_username"><?php esc_html_e( 'Username', 'smart-loginizer' ); ?></label>
				<div class="smart-loginizer-input-wrapper smart-loginizer-icon-<?php echo esc_attr( $username_icon_position ); ?>">
					<?php if ( ! empty( $username_icon['value'] ) && 'left' === $username_icon_position ) : ?>
						<span class="smart-loginizer-input-icon">
							<?php \Elementor\Icons_Manager::render_icon( $username_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
					<input type="text" id="inline_reg_username" name="username" required />
					<?php if ( ! empty( $username_icon['value'] ) && 'right' === $username_icon_position ) : ?>
						<span class="smart-loginizer-input-icon">
							<?php \Elementor\Icons_Manager::render_icon( $username_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $show_social_login && 'after_username' === $social_login_position ) : ?>
				<?php $this->render_social_login_buttons( $show_social_login, $social_login_text, false, $social_icon_settings, $social_icon_position ); ?>
			<?php endif; ?>

			<div class="smart-loginizer-form-field">
				<label for="inline_reg_email"><?php esc_html_e( 'Email', 'smart-loginizer' ); ?></label>
				<div class="smart-loginizer-input-wrapper smart-loginizer-icon-<?php echo esc_attr( $email_icon_position ); ?>">
					<?php if ( ! empty( $email_icon['value'] ) && 'left' === $email_icon_position ) : ?>
						<span class="smart-loginizer-input-icon">
							<?php \Elementor\Icons_Manager::render_icon( $email_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
					<input type="email" id="inline_reg_email" name="email" required />
					<?php if ( ! empty( $email_icon['value'] ) && 'right' === $email_icon_position ) : ?>
						<span class="smart-loginizer-input-icon">
							<?php \Elementor\Icons_Manager::render_icon( $email_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $show_social_login && 'after_email' === $social_login_position ) : ?>
				<?php $this->render_social_login_buttons( $show_social_login, $social_login_text, false, $social_icon_settings, $social_icon_position ); ?>
			<?php endif; ?>

			<div class="smart-loginizer-form-field">
				<label for="inline_reg_password"><?php esc_html_e( 'Password', 'smart-loginizer' ); ?></label>
				<div class="smart-loginizer-input-wrapper smart-loginizer-icon-<?php echo esc_attr( $password_icon_position ); ?>">
					<?php if ( ! empty( $password_icon['value'] ) && 'left' === $password_icon_position ) : ?>
						<span class="smart-loginizer-input-icon">
							<?php \Elementor\Icons_Manager::render_icon( $password_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
					<input type="password" id="inline_reg_password" name="password" required />
					<?php if ( $show_password_toggle ) : ?>
						<button type="button" class="smart-loginizer-password-toggle" aria-label="<?php esc_attr_e( 'Toggle password visibility', 'smart-loginizer' ); ?>" data-target="inline_reg_password">
							<span class="smart-loginizer-eye-icon">
								<?php \Elementor\Icons_Manager::render_icon( $password_toggle_eye_icon, array( 'aria-hidden' => 'true' ) ); ?>
							</span>
							<span class="smart-loginizer-eye-slash-icon" style="display: none;">
								<?php \Elementor\Icons_Manager::render_icon( $password_toggle_eye_slash_icon, array( 'aria-hidden' => 'true' ) ); ?>
							</span>
						</button>
					<?php endif; ?>
					<?php if ( ! empty( $password_icon['value'] ) && 'right' === $password_icon_position ) : ?>
						<span class="smart-loginizer-input-icon">
							<?php \Elementor\Icons_Manager::render_icon( $password_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
				</div>
				<div class="smart-loginizer-password-strength-meter"></div>
			</div>

			<?php if ( $show_social_login && 'after_password' === $social_login_position ) : ?>
				<?php $this->render_social_login_buttons( $show_social_login, $social_login_text, false, $social_icon_settings, $social_icon_position ); ?>
			<?php endif; ?>

			<div class="smart-loginizer-form-field">
				<label for="inline_reg_confirm_password"><?php esc_html_e( 'Confirm Password', 'smart-loginizer' ); ?></label>
				<div class="smart-loginizer-input-wrapper smart-loginizer-icon-<?php echo esc_attr( $password_icon_position ); ?>">
					<?php if ( ! empty( $password_icon['value'] ) && 'left' === $password_icon_position ) : ?>
						<span class="smart-loginizer-input-icon">
							<?php \Elementor\Icons_Manager::render_icon( $password_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
					<input type="password" id="inline_reg_confirm_password" name="confirm_password" required />
					<?php if ( $show_password_toggle ) : ?>
						<button type="button" class="smart-loginizer-password-toggle" aria-label="<?php esc_attr_e( 'Toggle password visibility', 'smart-loginizer' ); ?>" data-target="inline_reg_confirm_password">
							<span class="smart-loginizer-eye-icon">
								<?php \Elementor\Icons_Manager::render_icon( $password_toggle_eye_icon, array( 'aria-hidden' => 'true' ) ); ?>
							</span>
							<span class="smart-loginizer-eye-slash-icon" style="display: none;">
								<?php \Elementor\Icons_Manager::render_icon( $password_toggle_eye_slash_icon, array( 'aria-hidden' => 'true' ) ); ?>
							</span>
						</button>
					<?php endif; ?>
					<?php if ( ! empty( $password_icon['value'] ) && 'right' === $password_icon_position ) : ?>
						<span class="smart-loginizer-input-icon">
							<?php \Elementor\Icons_Manager::render_icon( $password_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $recaptcha_enabled && ! empty( $recaptcha_site_key ) ) : ?>
				<input type="hidden" name="recaptcha_token" id="inline_recaptcha_token_register" />
				<?php if ( 'v2_checkbox' === $recaptcha_version ) : ?>
					<div class="smart-loginizer-form-field">
						<div id="inline_recaptcha_v2_register" class="smart-loginizer-recaptcha-v2"></div>
					</div>
				<?php elseif ( 'v2_invisible' === $recaptcha_version ) : ?>
					<div id="inline_recaptcha_v2_invisible_register" class="smart-loginizer-recaptcha-v2-invisible"></div>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( $show_social_login && 'after_recaptcha' === $social_login_position ) : ?>
				<?php $this->render_social_login_buttons( $show_social_login, $social_login_text, false, $social_icon_settings, $social_icon_position ); ?>
			<?php endif; ?>

			<div class="smart-loginizer-form-field">
				<button type="submit" class="smart-loginizer-submit-btn">
					<?php esc_html_e( 'Register', 'smart-loginizer' ); ?>
				</button>
			</div>

			<?php if ( $show_social_login && 'after_submit' === $social_login_position ) : ?>
				<?php $this->render_social_login_buttons( $show_social_login, $social_login_text, false, $social_icon_settings, $social_icon_position ); ?>
			<?php endif; ?>

			<?php if ( $show_login_link && $enable_login ) : ?>
				<div class="smart-loginizer-form-field smart-loginizer-form-link-wrapper" style="margin-top: 15px;">
					<a href="#" class="smart-loginizer-form-link" data-switch-to="login">
						<?php echo esc_html( $login_link_text ); ?>
					</a>
				</div>
			<?php endif; ?>

			<div class="smart-loginizer-form-message"></div>
		</form>
		<?php
	}

	/**
	 * Render reset form.
	 *
	 * @param bool   $show_login_link Show login link.
	 * @param string $login_link_text Login link text.
	 * @param bool   $enable_login Enable login form.
	 * @param bool   $show_site_logo Show site logo.
	 * @param array  $site_logo Site logo data.
	 * @param string $headline Form headline.
	 * @param string $description Form description.
	 * @param array  $email_icon Email icon data.
	 * @param string $email_icon_position Email icon position.
	 * @return void
	 */
	private function render_reset_form( bool $show_login_link = true, string $login_link_text = '', bool $enable_login = true, bool $show_site_logo = false, array $site_logo = array(), string $headline = '', string $description = '', array $email_icon = array(), string $email_icon_position = 'left' ): void {
		// Get widget settings for reCAPTCHA.
		$settings = $this->get_settings_for_display();
		$global_settings = get_option( 'smart_loginizer_settings', array() );
		$widget_recaptcha = isset( $settings['enable_recaptcha'] ) ? $settings['enable_recaptcha'] : null;
		if ( null === $widget_recaptcha ) {
			// Use global setting if widget setting is not set.
			$recaptcha_enabled = 'yes' === ( $global_settings['enable_recaptcha'] ?? 'no' );
		} else {
			// Use widget setting (user override).
			$recaptcha_enabled = 'yes' === $widget_recaptcha;
		}
		$recaptcha_version = $global_settings['recaptcha_version'] ?? 'v3';
		$recaptcha_site_key = $this->helpers->get_option( 'recaptcha_site_key' );
		?>
		<?php if ( $show_site_logo && ! empty( $site_logo['url'] ) ) : ?>
			<div class="smart-loginizer-form-header-logo">
				<img src="<?php echo esc_url( $site_logo['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $headline ) || ! empty( $description ) ) : ?>
			<div class="smart-loginizer-form-header">
				<?php if ( ! empty( $headline ) ) : ?>
					<h3 class="smart-loginizer-form-headline"><?php echo esc_html( $headline ); ?></h3>
				<?php endif; ?>
				<?php if ( ! empty( $description ) ) : ?>
					<p class="smart-loginizer-form-description"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<form class="smart-loginizer-lost-password-form smart-loginizer-ajax-form" method="post">
			<?php wp_nonce_field( 'smart_loginizer_nonce', 'smart_loginizer_nonce' ); ?>
			<input type="hidden" name="action" value="smart_loginizer_lost_password" />

			<div class="smart-loginizer-form-field">
				<label for="inline_lost_password_email"><?php esc_html_e( 'Email', 'smart-loginizer' ); ?></label>
				<div class="smart-loginizer-input-wrapper smart-loginizer-icon-<?php echo esc_attr( $email_icon_position ); ?>">
					<?php if ( ! empty( $email_icon['value'] ) && 'left' === $email_icon_position ) : ?>
						<span class="smart-loginizer-input-icon">
							<?php \Elementor\Icons_Manager::render_icon( $email_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
					<input type="email" id="inline_lost_password_email" name="email" required />
					<?php if ( ! empty( $email_icon['value'] ) && 'right' === $email_icon_position ) : ?>
						<span class="smart-loginizer-input-icon">
							<?php \Elementor\Icons_Manager::render_icon( $email_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $recaptcha_enabled && ! empty( $recaptcha_site_key ) ) : ?>
				<input type="hidden" name="recaptcha_token" id="inline_recaptcha_token_reset" />
				<?php if ( 'v2_checkbox' === $recaptcha_version ) : ?>
					<div class="smart-loginizer-form-field">
						<div id="inline_recaptcha_v2_reset" class="smart-loginizer-recaptcha-v2"></div>
					</div>
				<?php elseif ( 'v2_invisible' === $recaptcha_version ) : ?>
					<div id="inline_recaptcha_v2_invisible_reset" class="smart-loginizer-recaptcha-v2-invisible"></div>
				<?php endif; ?>
			<?php endif; ?>

			<div class="smart-loginizer-form-field">
				<button type="submit" class="smart-loginizer-submit-btn">
					<?php esc_html_e( 'Reset Password', 'smart-loginizer' ); ?>
				</button>
			</div>

			<?php if ( $show_login_link && $enable_login ) : ?>
				<div class="smart-loginizer-form-field smart-loginizer-form-link-wrapper" style="margin-top: 15px;">
					<a href="#" class="smart-loginizer-form-link" data-switch-to="login">
						<?php echo esc_html( $login_link_text ); ?>
					</a>
				</div>
			<?php endif; ?>

			<div class="smart-loginizer-form-message"></div>
		</form>
		<?php
	}
}

