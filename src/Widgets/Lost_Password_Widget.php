<?php
/**
 * Lost Password Widget.
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
 * Lost Password Widget class.
 */
class Lost_Password_Widget extends Base_Widget {

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
		return 'smart_loginizer_lost_password';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return __( 'Lost Password', 'smart-loginizer' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-password';
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
				'default' => __( 'Reset Password', 'smart-loginizer' ),
			)
		);

		$this->add_control(
			'success_message',
			array(
				'label'   => __( 'Success Message', 'smart-loginizer' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Password reset link has been sent to your email.', 'smart-loginizer' ),
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

		$this->end_controls_section();

		// Form Style section - similar to Login Form.
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
					'{{WRAPPER}} .smart-loginizer-lost-password-form' => 'width: {{SIZE}}{{UNIT}}; max-width: 100%;',
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
					'{{WRAPPER}} .smart-loginizer-lost-password-form' => 'height: {{SIZE}}{{UNIT}} !important; overflow-y: auto;',
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
					'{{WRAPPER}} .smart-loginizer-lost-password-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'form_border',
				'selector' => '{{WRAPPER}} .smart-loginizer-lost-password-form',
			)
		);

		$this->add_control(
			'form_border_radius',
			array(
				'label'      => __( 'Border Radius', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-lost-password-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'form_box_shadow',
				'selector' => '{{WRAPPER}} .smart-loginizer-lost-password-form',
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
					'{{WRAPPER}} .smart-loginizer-lost-password-form' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .smart-loginizer-form-field' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		// Field and Button Style sections - similar to Login Form.
		$this->start_controls_section(
			'field_style_section',
			array(
				'label' => __( 'Field Style', 'smart-loginizer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'input_background',
				'label'    => __( 'Input Background', 'smart-loginizer' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-form-field input',
			)
		);

		$this->add_control(
			'input_color',
			array(
				'label'     => __( 'Input Text Color', 'smart-loginizer' ),
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
				'label'      => __( 'Input Padding', 'smart-loginizer' ),
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
				'label'      => __( 'Input Border Radius', 'smart-loginizer' ),
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

		// Button Style section.
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
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$submit_text        = $settings['submit_button_text'] ?? __( 'Reset Password', 'smart-loginizer' );
		$success_msg         = $settings['success_message'] ?? __( 'Password reset link has been sent to your email.', 'smart-loginizer' );
		$email_label         = $settings['email_label'] ?? __( 'Email', 'smart-loginizer' );
		$email_placeholder   = $settings['email_placeholder'] ?? __( 'Enter your email address', 'smart-loginizer' );
		$form_style          = $settings['form_style'] ?? 'default';
		$form_style_tablet   = $settings['form_style_tablet'] ?? 'default';
		$form_style_mobile   = $settings['form_style_mobile'] ?? 'default';
		$show_field_icons     = 'yes' === ( $settings['show_field_icons'] ?? 'no' );
		$email_icon           = $show_field_icons ? ( $settings['email_icon'] ?? array() ) : array();
		$email_icon_position  = $settings['email_icon_position'] ?? 'left';

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

		$wrapper_class = 'smart-loginizer-form-wrapper smart-loginizer-form-style-' . esc_attr( $form_style );
		?>
		<div class="<?php echo esc_attr( $wrapper_class ); ?>" 
			data-form-style-desktop="<?php echo esc_attr( $form_style ); ?>"
			data-form-style-tablet="<?php echo esc_attr( $form_style_tablet ); ?>"
			data-form-style-mobile="<?php echo esc_attr( $form_style_mobile ); ?>">
		<form class="smart-loginizer-lost-password-form smart-loginizer-ajax-form" method="post">
			<?php wp_nonce_field( 'smart_loginizer_nonce', 'smart_loginizer_nonce' ); ?>
			<input type="hidden" name="action" value="smart_loginizer_lost_password" />

			<div class="smart-loginizer-form-field">
				<label for="lost_password_email"><?php echo esc_html( $email_label ); ?></label>
				<div class="smart-loginizer-input-wrapper smart-loginizer-icon-<?php echo esc_attr( $email_icon_position ); ?>">
					<?php if ( ! empty( $email_icon['value'] ) && 'left' === $email_icon_position ) : ?>
						<span class="smart-loginizer-input-icon">
							<?php \Elementor\Icons_Manager::render_icon( $email_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
					<input type="email" id="lost_password_email" name="email" placeholder="<?php echo esc_attr( $email_placeholder ); ?>" required />
					<?php if ( ! empty( $email_icon['value'] ) && 'right' === $email_icon_position ) : ?>
						<span class="smart-loginizer-input-icon">
							<?php \Elementor\Icons_Manager::render_icon( $email_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $recaptcha_enabled && ! empty( $recaptcha_site_key ) ) : ?>
				<input type="hidden" name="recaptcha_token" id="recaptcha_token_lost_password" />
				<?php if ( 'v2_checkbox' === $recaptcha_version ) : ?>
					<div class="smart-loginizer-form-field">
						<div id="recaptcha_v2_lost_password" class="smart-loginizer-recaptcha-v2"></div>
					</div>
				<?php elseif ( 'v2_invisible' === $recaptcha_version ) : ?>
					<div id="recaptcha_v2_invisible_lost_password" class="smart-loginizer-recaptcha-v2-invisible"></div>
				<?php endif; ?>
			<?php endif; ?>

			<div class="smart-loginizer-form-field">
				<button type="submit" class="smart-loginizer-submit-btn">
					<?php echo esc_html( $submit_text ); ?>
				</button>
			</div>

			<div class="smart-loginizer-form-message"></div>
		</form>
		</div>
		<?php
	}
}

