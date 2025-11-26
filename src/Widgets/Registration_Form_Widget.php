<?php
/**
 * Registration Form Widget.
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
 * Registration Form Widget class.
 */
class Registration_Form_Widget extends Base_Widget {

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
		return 'smart_loginizer_registration_form';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return __( 'Registration Form', 'smart-loginizer' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-user-circle-o';
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
			'submit_button_text',
			array(
				'label'   => __( 'Submit Button Text', 'smart-loginizer' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Register', 'smart-loginizer' ),
			)
		);

		$this->add_control(
			'enable_password_strength',
			array(
				'label'        => __( 'Enable Password Strength Meter', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'auto_login_after_registration',
			array(
				'label'        => __( 'Auto-login After Registration', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$this->add_control(
			'redirect_after_registration',
			array(
				'label'       => __( 'Redirect After Registration', 'smart-loginizer' ),
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
			'username_label',
			array(
				'label'   => __( 'Username Label', 'smart-loginizer' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Username', 'smart-loginizer' ),
			)
		);

		$this->add_control(
			'username_placeholder',
			array(
				'label'   => __( 'Username Placeholder', 'smart-loginizer' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Choose a username', 'smart-loginizer' ),
			)
		);

		$this->add_control(
			'username_icon',
			array(
				'label'     => __( 'Username Icon', 'smart-loginizer' ),
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
				'label'     => __( 'Username Icon Position', 'smart-loginizer' ),
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
			'email_label',
			array(
				'label'   => __( 'Email Label', 'smart-loginizer' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Email', 'smart-loginizer' ),
			)
		);

		$this->add_control(
			'email_placeholder',
			array(
				'label'   => __( 'Email Placeholder', 'smart-loginizer' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Enter your email address', 'smart-loginizer' ),
			)
		);

		$this->add_control(
			'email_icon',
			array(
				'label'     => __( 'Email Icon', 'smart-loginizer' ),
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

		$this->add_control(
			'password_label',
			array(
				'label'   => __( 'Password Label', 'smart-loginizer' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Password', 'smart-loginizer' ),
			)
		);

		$this->add_control(
			'password_placeholder',
			array(
				'label'   => __( 'Password Placeholder', 'smart-loginizer' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Choose a password', 'smart-loginizer' ),
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
			'confirm_password_label',
			array(
				'label'   => __( 'Confirm Password Label', 'smart-loginizer' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Confirm Password', 'smart-loginizer' ),
			)
		);

		$this->add_control(
			'confirm_password_placeholder',
			array(
				'label'   => __( 'Confirm Password Placeholder', 'smart-loginizer' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Confirm your password', 'smart-loginizer' ),
			)
		);

		$this->add_control(
			'confirm_password_icon',
			array(
				'label'     => __( 'Confirm Password Icon', 'smart-loginizer' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(),
				'condition' => array(
					'show_field_icons' => 'yes',
				),
			)
		);

		$this->add_control(
			'confirm_password_icon_position',
			array(
				'label'     => __( 'Confirm Password Icon Position', 'smart-loginizer' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'left',
				'options'   => array(
					'left'  => __( 'Left', 'smart-loginizer' ),
					'right' => __( 'Right', 'smart-loginizer' ),
				),
				'condition' => array(
					'show_field_icons' => 'yes',
					'confirm_password_icon[value]!' => '',
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
			'show_confirm_password',
			array(
				'label'        => __( 'Show Confirm Password Field', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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
					'top'          => __( 'Top (Before Username)', 'smart-loginizer' ),
					'after_username' => __( 'After Username', 'smart-loginizer' ),
					'after_email' => __( 'After Email', 'smart-loginizer' ),
					'after_password' => __( 'After Password', 'smart-loginizer' ),
					'after_confirm_password' => __( 'After Confirm Password', 'smart-loginizer' ),
					'after_recaptcha' => __( 'After reCAPTCHA', 'smart-loginizer' ),
					'after_submit'  => __( 'After Submit Button (Default)', 'smart-loginizer' ),
				),
				'condition' => array(
					'show_social_login' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_reset_link',
			array(
				'label'        => __( 'Show Reset Password Link', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'separator'    => 'before',
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
				),
			)
		);

		$this->add_control(
			'reset_link_url',
			array(
				'label'       => __( 'Reset Password Link URL', 'smart-loginizer' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'Leave empty to use default WordPress password reset page', 'smart-loginizer' ),
				'show_external' => true,
				'default'     => array(
					'url' => '',
					'is_external' => false,
					'nofollow' => false,
				),
				'condition' => array(
					'show_reset_link' => 'yes',
				),
				'description' => __( 'Leave empty to use the default WordPress password reset page. You can also set a custom URL.', 'smart-loginizer' ),
			)
		);

		$this->end_controls_section();

		// Form Style section.
		$this->start_controls_section(
			'form_style_section',
			array(
				'label' => __( 'Form Style', 'smart-loginizer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'form_width',
			array(
				'label'      => __( 'Form Width', 'smart-loginizer' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 200,
						'max' => 1000,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-registration-form' => 'width: {{SIZE}}{{UNIT}}; max-width: 100%;',
				),
			)
		);

		$this->add_control(
			'form_height_auto',
			array(
				'label'        => __( 'Auto Height', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'Enable to let form height adjust automatically based on content.', 'smart-loginizer' ),
			)
		);

		$this->add_responsive_control(
			'form_height',
			array(
				'label'      => __( 'Form Height', 'smart-loginizer' ),
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
					'form_height_auto!' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-registration-form' => 'height: {{SIZE}}{{UNIT}} !important; overflow-y: auto;',
				),
			)
		);

		$this->add_responsive_control(
			'form_padding',
			array(
				'label'      => __( 'Form Padding', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-registration-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'form_border',
				'selector' => '{{WRAPPER}} .smart-loginizer-registration-form',
			)
		);

		$this->add_control(
			'form_border_radius',
			array(
				'label'      => __( 'Border Radius', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-registration-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'form_box_shadow',
				'selector' => '{{WRAPPER}} .smart-loginizer-registration-form',
			)
		);

		$this->add_responsive_control(
			'form_alignment',
			array(
				'label'   => __( 'Form Alignment', 'smart-loginizer' ),
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
					'{{WRAPPER}} .smart-loginizer-registration-form' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-form-field' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		// Field Style section - same as Login Form.
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
				'selector' => '{{WRAPPER}} .smart-loginizer-form-field input',
			)
		);

		$this->add_control(
			'input_color',
			array(
				'label'     => __( 'Text Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-form-field input' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'input_typography',
				'selector' => '{{WRAPPER}} .smart-loginizer-form-field input',
			)
		);

		$this->add_responsive_control(
			'input_padding',
			array(
				'label'      => __( 'Padding', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-form-field input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'input_border',
				'selector' => '{{WRAPPER}} .smart-loginizer-form-field input',
			)
		);

		$this->add_control(
			'input_border_radius',
			array(
				'label'      => __( 'Border Radius', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-form-field input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$this->add_control(
			'password_strength_style',
			array(
				'label'     => __( 'Password Strength Meter', 'smart-loginizer' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'password_strength_height',
			array(
				'label'      => __( 'Meter Height', 'smart-loginizer' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 2,
						'max' => 10,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-password-strength-meter' => 'height: {{SIZE}}{{UNIT}};',
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

		// Button Style section - same as Login Form.
		$this->start_controls_section(
			'button_style_section',
			array(
				'label' => __( 'Button Style', 'smart-loginizer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'button_position',
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
			'button_alignment',
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
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .smart-loginizer-submit-btn',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'button_background',
				'label'    => __( 'Background', 'smart-loginizer' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-submit-btn',
			)
		);

		$this->add_control(
			'button_text_color',
			array(
				'label'     => __( 'Text Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-submit-btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding',
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
				'name'     => 'button_border',
				'selector' => '{{WRAPPER}} .smart-loginizer-submit-btn',
			)
		);

		$this->add_control(
			'button_border_radius',
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
			Group_Control_Background::get_type(),
			array(
				'name'     => 'button_hover_background',
				'label'    => __( 'Hover Background', 'smart-loginizer' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-submit-btn:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_box_shadow',
				'label'    => __( 'Box Shadow', 'smart-loginizer' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-submit-btn',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_hover_box_shadow',
				'label'    => __( 'Hover Box Shadow', 'smart-loginizer' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-submit-btn:hover',
			)
		);

		$this->end_controls_section();

		// Reset Link Style section.
		$this->start_controls_section(
			'reset_link_style_section',
			array(
				'label' => __( 'Reset Password Link Style', 'smart-loginizer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'reset_link_typography',
				'label'    => __( 'Typography', 'smart-loginizer' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-form-link',
			)
		);

		$this->add_control(
			'reset_link_color',
			array(
				'label'     => __( 'Text Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0073aa',
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-form-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'reset_link_hover_color',
			array(
				'label'     => __( 'Hover Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-form-link:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'reset_link_text_decoration',
			array(
				'label'   => __( 'Text Decoration', 'smart-loginizer' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none'         => __( 'None', 'smart-loginizer' ),
					'underline'    => __( 'Underline', 'smart-loginizer' ),
					'overline'     => __( 'Overline', 'smart-loginizer' ),
					'line-through' => __( 'Line Through', 'smart-loginizer' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-form-link' => 'text-decoration: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'reset_link_hover_text_decoration',
			array(
				'label'   => __( 'Hover Text Decoration', 'smart-loginizer' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'underline',
				'options' => array(
					'none'         => __( 'None', 'smart-loginizer' ),
					'underline'    => __( 'Underline', 'smart-loginizer' ),
					'overline'     => __( 'Overline', 'smart-loginizer' ),
					'line-through' => __( 'Line Through', 'smart-loginizer' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-form-link:hover' => 'text-decoration: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'reset_link_alignment',
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
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-form-field:has(.smart-loginizer-form-link)' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'reset_link_margin',
			array(
				'label'      => __( 'Margin', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-form-field:has(.smart-loginizer-form-link)' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'reset_link_padding',
			array(
				'label'      => __( 'Padding', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-form-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Social Login Style section - using same controls as Login_Form_Widget.
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
		$show_when_logged_in = 'yes' === ( $settings['show_when_logged_in'] ?? 'no' );

		// Check if user is logged in and form should not be shown.
		if ( is_user_logged_in() && ! $show_when_logged_in ) {
			$current_user      = wp_get_current_user();
			$logged_in_message = $settings['logged_in_message'] ?? /* translators: %s: User display name */
				__( 'You are already logged in as %s.', 'smart-loginizer' );
			?>
			<div class="smart-loginizer-registration-form">
				<p><?php echo esc_html( sprintf( $logged_in_message, $current_user->display_name ) ); ?></p>
			</div>
			<?php
			return;
		}

		$submit_text            = $settings['submit_button_text'] ?? __( 'Register', 'smart-loginizer' );
		$redirect               = $settings['redirect_after_registration']['url'] ?? '';
		$auto_login             = 'yes' === ( $settings['auto_login_after_registration'] ?? 'no' );
		$password_strength      = 'yes' === ( $settings['enable_password_strength'] ?? 'yes' );
		$username_label         = $settings['username_label'] ?? __( 'Username', 'smart-loginizer' );
		// Get reCAPTCHA settings - check widget setting first, then fall back to global.
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
		$enable_registration_limit = 'yes' === ( $global_settings['enable_registration_limit'] ?? 'no' );
		$max_registrations_per_ip = isset( $global_settings['max_registrations_per_ip'] ) ? absint( $global_settings['max_registrations_per_ip'] ) : 3;
		$registration_limit_period = isset( $global_settings['registration_limit_period'] ) ? absint( $global_settings['registration_limit_period'] ) : 24;
		$enable_banned_domains = 'yes' === ( $global_settings['enable_banned_domains'] ?? 'no' );
		$banned_email_domains = $global_settings['banned_email_domains'] ?? '';
		$enable_location_restriction = 'yes' === ( $global_settings['enable_location_restriction'] ?? 'no' );
		$location_restriction_type = $global_settings['location_restriction_type'] ?? 'blocked';
		$location_countries = $global_settings['location_countries'] ?? '';
		$username_placeholder   = $settings['username_placeholder'] ?? __( 'Choose a username', 'smart-loginizer' );
		$email_label            = $settings['email_label'] ?? __( 'Email', 'smart-loginizer' );
		$email_placeholder      = $settings['email_placeholder'] ?? __( 'Enter your email address', 'smart-loginizer' );
		$password_label         = $settings['password_label'] ?? __( 'Password', 'smart-loginizer' );
		$password_placeholder   = $settings['password_placeholder'] ?? __( 'Choose a password', 'smart-loginizer' );
		$confirm_password_label = $settings['confirm_password_label'] ?? __( 'Confirm Password', 'smart-loginizer' );
		$confirm_password_placeholder = $settings['confirm_password_placeholder'] ?? __( 'Confirm your password', 'smart-loginizer' );
		$show_confirm_password  = 'yes' === ( $settings['show_confirm_password'] ?? 'yes' );
		$form_style              = $settings['form_style'] ?? 'default';
		$form_style_tablet       = $settings['form_style_tablet'] ?? 'default';
		$form_style_mobile       = $settings['form_style_mobile'] ?? 'default';
		$show_field_icons        = 'yes' === ( $settings['show_field_icons'] ?? 'no' );
		$username_icon            = $show_field_icons ? ( $settings['username_icon'] ?? array() ) : array();
		$username_icon_position   = $settings['username_icon_position'] ?? 'left';
		$email_icon               = $show_field_icons ? ( $settings['email_icon'] ?? array() ) : array();
		$email_icon_position      = $settings['email_icon_position'] ?? 'left';
		$password_icon            = $show_field_icons ? ( $settings['password_icon'] ?? array() ) : array();
		$password_icon_position   = $settings['password_icon_position'] ?? 'left';
		$confirm_password_icon     = $show_field_icons ? ( $settings['confirm_password_icon'] ?? array() ) : array();
		$confirm_password_icon_position = $settings['confirm_password_icon_position'] ?? 'left';
		$show_social_login         = 'yes' === ( $settings['show_social_login'] ?? 'no' );
		$social_login_text         = $settings['social_login_text'] ?? __( 'Or continue with', 'smart-loginizer' );
		$social_login_position     = $settings['social_login_position'] ?? 'after_submit';
		$show_password_toggle      = 'yes' === ( $settings['show_password_toggle'] ?? 'yes' );
		$password_toggle_eye_icon = $show_password_toggle ? ( $settings['password_toggle_eye_icon'] ?? array() ) : array();
		$password_toggle_eye_slash_icon = $show_password_toggle ? ( $settings['password_toggle_eye_slash_icon'] ?? array() ) : array();
		$social_icon_settings      = array(
			'google_icon'   => $settings['google_icon'] ?? array(),
			'x_icon'        => $settings['x_icon'] ?? array(),
			'linkedin_icon' => $settings['linkedin_icon'] ?? array(),
			'facebook_icon' => $settings['facebook_icon'] ?? array(),
		);
		$social_icon_position      = $settings['social_login_icon_position'] ?? 'left';
		$show_reset_link            = 'yes' === ( $settings['show_reset_link'] ?? 'no' );
		$reset_link_text            = $settings['reset_link_text'] ?? __( 'Forgot your password?', 'smart-loginizer' );
		$reset_link_url             = ! empty( $settings['reset_link_url']['url'] ) ? $settings['reset_link_url']['url'] : wp_lostpassword_url();

		$form_class = 'smart-loginizer-registration-form smart-loginizer-ajax-form';
		if ( $password_strength ) {
			$form_class .= ' smart-loginizer-password-strength';
		}

		$wrapper_class = 'smart-loginizer-form-wrapper smart-loginizer-form-style-' . esc_attr( $form_style );
		?>
		<div class="<?php echo esc_attr( $wrapper_class ); ?>" 
			data-form-style-desktop="<?php echo esc_attr( $form_style ); ?>"
			data-form-style-tablet="<?php echo esc_attr( $form_style_tablet ); ?>"
			data-form-style-mobile="<?php echo esc_attr( $form_style_mobile ); ?>">
		<form class="<?php echo esc_attr( $form_class ); ?>" method="post">
			<?php wp_nonce_field( 'smart_loginizer_nonce', 'smart_loginizer_nonce' ); ?>
			<input type="hidden" name="action" value="smart_loginizer_register" />
			<?php if ( ! empty( $redirect ) ) : ?>
				<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ); ?>" />
			<?php endif; ?>
			<?php if ( $auto_login ) : ?>
				<input type="hidden" name="auto_login" value="true" />
			<?php endif; ?>
			<input type="hidden" name="enable_registration_limit" value="<?php echo $enable_registration_limit ? 'yes' : 'no'; ?>" />
			<input type="hidden" name="max_registrations_per_ip" value="<?php echo esc_attr( $max_registrations_per_ip ); ?>" />
			<input type="hidden" name="registration_limit_period" value="<?php echo esc_attr( $registration_limit_period ); ?>" />
			<input type="hidden" name="enable_banned_domains" value="<?php echo $enable_banned_domains ? 'yes' : 'no'; ?>" />
			<input type="hidden" name="banned_email_domains" value="<?php echo esc_attr( $banned_email_domains ); ?>" />
			<input type="hidden" name="enable_location_restriction" value="<?php echo $enable_location_restriction ? 'yes' : 'no'; ?>" />
			<input type="hidden" name="location_restriction_type" value="<?php echo esc_attr( $location_restriction_type ); ?>" />
			<input type="hidden" name="location_countries" value="<?php echo esc_attr( $location_countries ); ?>" />

			<?php if ( $show_social_login && 'top' === $social_login_position ) : ?>
				<?php $this->render_social_login_buttons( $show_social_login, $social_login_text, true, $social_icon_settings, $social_icon_position ); ?>
			<?php endif; ?>

			<div class="smart-loginizer-form-field">
				<label for="reg_username"><?php echo esc_html( $username_label ); ?></label>
				<div class="smart-loginizer-input-wrapper smart-loginizer-icon-<?php echo esc_attr( $username_icon_position ); ?>">
					<?php if ( ! empty( $username_icon['value'] ) && 'left' === $username_icon_position ) : ?>
						<span class="smart-loginizer-input-icon">
							<?php \Elementor\Icons_Manager::render_icon( $username_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
					<input type="text" id="reg_username" name="username" placeholder="<?php echo esc_attr( $username_placeholder ); ?>" required />
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
				<label for="reg_email"><?php echo esc_html( $email_label ); ?></label>
				<div class="smart-loginizer-input-wrapper smart-loginizer-icon-<?php echo esc_attr( $email_icon_position ); ?>">
					<?php if ( ! empty( $email_icon['value'] ) && 'left' === $email_icon_position ) : ?>
						<span class="smart-loginizer-input-icon">
							<?php \Elementor\Icons_Manager::render_icon( $email_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
					<input type="email" id="reg_email" name="email" placeholder="<?php echo esc_attr( $email_placeholder ); ?>" required />
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
				<label for="reg_password"><?php echo esc_html( $password_label ); ?></label>
				<div class="smart-loginizer-input-wrapper smart-loginizer-icon-<?php echo esc_attr( $password_icon_position ); ?>">
					<?php if ( ! empty( $password_icon['value'] ) && 'left' === $password_icon_position ) : ?>
						<span class="smart-loginizer-input-icon">
							<?php \Elementor\Icons_Manager::render_icon( $password_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
					<input type="password" id="reg_password" name="password" placeholder="<?php echo esc_attr( $password_placeholder ); ?>" required />
					<?php if ( $show_password_toggle ) : ?>
						<button type="button" class="smart-loginizer-password-toggle" aria-label="<?php esc_attr_e( 'Toggle password visibility', 'smart-loginizer' ); ?>" data-target="reg_password">
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
				<?php if ( $password_strength ) : ?>
					<div class="smart-loginizer-password-strength-meter"></div>
				<?php endif; ?>
			</div>

			<?php if ( $show_social_login && 'after_password' === $social_login_position ) : ?>
				<?php $this->render_social_login_buttons( $show_social_login, $social_login_text, false, $social_icon_settings, $social_icon_position ); ?>
			<?php endif; ?>

			<?php if ( $show_confirm_password ) : ?>
				<div class="smart-loginizer-form-field">
					<label for="reg_confirm_password"><?php echo esc_html( $confirm_password_label ); ?></label>
					<div class="smart-loginizer-input-wrapper smart-loginizer-icon-<?php echo esc_attr( $confirm_password_icon_position ); ?>">
						<?php if ( ! empty( $confirm_password_icon['value'] ) && 'left' === $confirm_password_icon_position ) : ?>
							<span class="smart-loginizer-input-icon">
								<?php \Elementor\Icons_Manager::render_icon( $confirm_password_icon, array( 'aria-hidden' => 'true' ) ); ?>
							</span>
						<?php endif; ?>
						<input type="password" id="reg_confirm_password" name="confirm_password" placeholder="<?php echo esc_attr( $confirm_password_placeholder ); ?>" required />
						<?php if ( $show_password_toggle ) : ?>
							<button type="button" class="smart-loginizer-password-toggle" aria-label="<?php esc_attr_e( 'Toggle password visibility', 'smart-loginizer' ); ?>" data-target="reg_confirm_password">
								<span class="smart-loginizer-eye-icon">
									<?php \Elementor\Icons_Manager::render_icon( $password_toggle_eye_icon, array( 'aria-hidden' => 'true' ) ); ?>
								</span>
								<span class="smart-loginizer-eye-slash-icon" style="display: none;">
									<?php \Elementor\Icons_Manager::render_icon( $password_toggle_eye_slash_icon, array( 'aria-hidden' => 'true' ) ); ?>
								</span>
							</button>
						<?php endif; ?>
						<?php if ( ! empty( $confirm_password_icon['value'] ) && 'right' === $confirm_password_icon_position ) : ?>
							<span class="smart-loginizer-input-icon">
								<?php \Elementor\Icons_Manager::render_icon( $confirm_password_icon, array( 'aria-hidden' => 'true' ) ); ?>
							</span>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $show_social_login && 'after_confirm_password' === $social_login_position ) : ?>
				<?php $this->render_social_login_buttons( $show_social_login, $social_login_text, false, $social_icon_settings, $social_icon_position ); ?>
			<?php endif; ?>

			<?php if ( $recaptcha_enabled && ! empty( $recaptcha_site_key ) ) : ?>
				<input type="hidden" name="recaptcha_token" id="recaptcha_token_register" />
				<?php if ( 'v2_checkbox' === $recaptcha_version ) : ?>
					<div class="smart-loginizer-form-field">
						<div id="recaptcha_v2_register" class="smart-loginizer-recaptcha-v2"></div>
					</div>
				<?php elseif ( 'v2_invisible' === $recaptcha_version ) : ?>
					<div id="recaptcha_v2_invisible_register" class="smart-loginizer-recaptcha-v2-invisible"></div>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( $show_social_login && 'after_recaptcha' === $social_login_position ) : ?>
				<?php $this->render_social_login_buttons( $show_social_login, $social_login_text, false, $social_icon_settings, $social_icon_position ); ?>
			<?php endif; ?>

			<div class="smart-loginizer-form-field">
				<button type="submit" class="smart-loginizer-submit-btn">
					<?php echo esc_html( $submit_text ); ?>
				</button>
			</div>

			<?php if ( $show_reset_link ) : ?>
				<div class="smart-loginizer-form-field">
					<a href="<?php echo esc_url( $reset_link_url ); ?>" class="smart-loginizer-form-link">
						<?php echo esc_html( $reset_link_text ); ?>
					</a>
				</div>
			<?php endif; ?>

			<?php if ( $show_social_login && 'after_submit' === $social_login_position ) : ?>
				<?php $this->render_social_login_buttons( $show_social_login, $social_login_text, false, $social_icon_settings, $social_icon_position ); ?>
			<?php endif; ?>

			<div class="smart-loginizer-form-message"></div>
		</form>
		</div>
		<?php
	}
}

