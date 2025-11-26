<?php
/**
 * Logout Button Widget.
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
 * Logout Button Widget class.
 */
class Logout_Button_Widget extends Base_Widget {

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
		return 'smart_loginizer_logout_button';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return __( 'Logout Button', 'smart-loginizer' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-sign-out';
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
			'button_text',
			array(
				'label'   => __( 'Button Text', 'smart-loginizer' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Logout', 'smart-loginizer' ),
			)
		);

		$this->add_control(
			'redirect_after_logout',
			array(
				'label'       => __( 'Redirect After Logout', 'smart-loginizer' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'smart-loginizer' ),
				'show_external' => false,
			)
		);

		$this->end_controls_section();

		// Style section.
		$this->start_controls_section(
			'style_section',
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
				'default' => 'left',
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-logout-button' => '{{VALUE}}',
				),
				'selectors_dictionary' => array(
					'left'    => 'margin-left: 0; margin-right: auto; width: auto; display: inline-block;',
					'center'  => 'margin-left: auto; margin-right: auto; width: auto; display: inline-block;',
					'right'   => 'margin-left: auto; margin-right: 0; width: auto; display: inline-block;',
					'stretch' => 'width: 100%; margin-left: 0; margin-right: 0; display: block;',
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
					'{{WRAPPER}} .smart-loginizer-logout-button' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .smart-loginizer-logout-button',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'button_background',
				'label'    => __( 'Background', 'smart-loginizer' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-logout-button',
			)
		);

		$this->add_control(
			'button_text_color',
			array(
				'label'     => __( 'Text Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-logout-button' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .smart-loginizer-logout-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'button_border',
				'selector' => '{{WRAPPER}} .smart-loginizer-logout-button',
			)
		);

		$this->add_control(
			'button_border_radius',
			array(
				'label'      => __( 'Border Radius', 'smart-loginizer' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart-loginizer-logout-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_box_shadow',
				'label'    => __( 'Box Shadow', 'smart-loginizer' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-logout-button',
			)
		);

		$this->add_control(
			'button_hover_heading',
			array(
				'label'     => __( 'Hover State', 'smart-loginizer' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'button_hover_background',
				'label'    => __( 'Hover Background', 'smart-loginizer' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-logout-button:hover',
			)
		);

		$this->add_control(
			'button_hover_text_color',
			array(
				'label'     => __( 'Hover Text Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-logout-button:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_hover_border_color',
			array(
				'label'     => __( 'Hover Border Color', 'smart-loginizer' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart-loginizer-logout-button:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_hover_box_shadow',
				'label'    => __( 'Hover Box Shadow', 'smart-loginizer' ),
				'selector' => '{{WRAPPER}} .smart-loginizer-logout-button:hover',
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
		if ( ! is_user_logged_in() ) {
			return;
		}

		$settings = $this->get_settings_for_display();
		$button_text = $settings['button_text'] ?? __( 'Logout', 'smart-loginizer' );
		$redirect    = $settings['redirect_after_logout']['url'] ?? '';

		if ( empty( $redirect ) ) {
			$redirect = $this->helpers->get_logout_redirect_url();
		}

		$logout_url = wp_logout_url( $redirect );
		$logout_url = add_query_arg( 'redirect_to', urlencode( $redirect ), $logout_url );

		?>
		<a href="<?php echo esc_url( $logout_url ); ?>" class="smart-loginizer-logout-button">
			<?php echo esc_html( $button_text ); ?>
		</a>
		<?php
	}
}

