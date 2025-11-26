<?php
/**
 * Elementor Widget Restriction functionality.
 *
 * @package SmartLoginizer\Widgets
 */

namespace SmartLoginizer\Widgets;

use Elementor\Controls_Manager;
use Elementor\Element_Base;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Elementor Widget Restriction class.
 */
class Elementor_Widget_Restriction {

	/**
	 * Initialize Elementor widget restriction.
	 *
	 * @return void
	 */
	public function __construct() {
		// Add controls to all widgets in Advanced tab.
		add_action( 'elementor/element/common/_section_style/after_section_end', array( $this, 'add_restriction_controls' ), 10, 2 );
		add_action( 'elementor/element/section/section_advanced/after_section_end', array( $this, 'add_restriction_controls' ), 10, 2 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', array( $this, 'add_restriction_controls' ), 10, 2 );

		// Check restrictions before rendering widgets.
		add_action( 'elementor/frontend/widget/before_render', array( $this, 'check_widget_restriction' ), 10, 1 );
		add_action( 'elementor/frontend/section/before_render', array( $this, 'check_widget_restriction' ), 10, 1 );
		add_action( 'elementor/frontend/column/before_render', array( $this, 'check_widget_restriction' ), 10, 1 );

		// Handle content replacement after render.
		add_action( 'elementor/frontend/widget/after_render', array( $this, 'handle_after_render' ), 10, 1 );
		add_action( 'elementor/frontend/section/after_render', array( $this, 'handle_after_render' ), 10, 1 );
		add_action( 'elementor/frontend/column/after_render', array( $this, 'handle_after_render' ), 10, 1 );

		// Enqueue scripts for client-side handling.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_restriction_scripts' ) );
	}

	/**
	 * Add restriction controls to Advanced tab.
	 *
	 * @param Element_Base $element Element instance.
	 * @param array         $args Section arguments.
	 * @return void
	 */
	public function add_restriction_controls( Element_Base $element, array $args ): void {
		// Get user roles.
		$user_roles = wp_roles()->get_names();

		$element->start_controls_section(
			'smart_loginizer_restriction_section',
			array(
				'label' => __( 'Smart Loginizer Restriction', 'smart-loginizer' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);

		$element->add_control(
			'smart_loginizer_enable_restriction',
			array(
				'label'        => __( 'Enable Restriction', 'smart-loginizer' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'smart-loginizer' ),
				'label_off'    => __( 'No', 'smart-loginizer' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$element->add_control(
			'smart_loginizer_restriction_type',
			array(
				'label'     => __( 'Restrict Access To', 'smart-loginizer' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'logged_out',
				'options'   => array(
					'logged_out'     => __( 'Logged Out Users', 'smart-loginizer' ),
					'logged_in'     => __( 'Logged In Users', 'smart-loginizer' ),
					'specific_roles' => __( 'Specific User Roles', 'smart-loginizer' ),
				),
				'condition' => array(
					'smart_loginizer_enable_restriction' => 'yes',
				),
			)
		);

		$element->add_control(
			'smart_loginizer_restriction_roles',
			array(
				'label'     => __( 'User Roles', 'smart-loginizer' ),
				'type'      => Controls_Manager::SELECT2,
				'multiple'  => true,
				'options'   => $user_roles,
				'condition' => array(
					'smart_loginizer_enable_restriction' => 'yes',
					'smart_loginizer_restriction_type'    => 'specific_roles',
				),
			)
		);

		$element->add_control(
			'smart_loginizer_restriction_action',
			array(
				'label'     => __( 'Action When Restricted', 'smart-loginizer' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'hide',
				'options'   => array(
					'hide'          => __( 'Hide Widget', 'smart-loginizer' ),
					'show_message'  => __( 'Show Message', 'smart-loginizer' ),
					'show_login'    => __( 'Show Login Page', 'smart-loginizer' ),
				),
				'condition' => array(
					'smart_loginizer_enable_restriction' => 'yes',
				),
			)
		);

		$element->add_control(
			'smart_loginizer_restriction_message',
			array(
				'label'       => __( 'Restriction Message', 'smart-loginizer' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'You do not have permission to view this content.', 'smart-loginizer' ),
				'placeholder' => __( 'Enter your restriction message', 'smart-loginizer' ),
				'condition'   => array(
					'smart_loginizer_enable_restriction' => 'yes',
					'smart_loginizer_restriction_action'   => 'show_message',
				),
			)
		);

		$element->end_controls_section();
	}

	/**
	 * Check widget restriction before rendering.
	 *
	 * @param Element_Base $element Element instance.
	 * @return void
	 */
	public function check_widget_restriction( Element_Base $element ): void {
		// Don't restrict in editor.
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			return;
		}

		$settings = $element->get_settings_for_display();

		// Check if restriction is enabled.
		if ( 'yes' !== ( $settings['smart_loginizer_enable_restriction'] ?? 'no' ) ) {
			return;
		}

		$restriction_type    = $settings['smart_loginizer_restriction_type'] ?? 'logged_out';
		$restriction_roles    = $settings['smart_loginizer_restriction_roles'] ?? array();
		$restriction_action  = $settings['smart_loginizer_restriction_action'] ?? 'hide';
		$restriction_message = $settings['smart_loginizer_restriction_message'] ?? __( 'You do not have permission to view this content.', 'smart-loginizer' );

		// Check if user should be restricted.
		$should_restrict = $this->should_restrict_access( $restriction_type, $restriction_roles );

		if ( ! $should_restrict ) {
			return;
		}

		// Handle restriction action.
		$this->handle_restriction_action( $element, $restriction_action, $restriction_message );
	}

	/**
	 * Check if access should be restricted.
	 *
	 * @param string $restriction_type Restriction type.
	 * @param array  $restriction_roles Allowed/restricted roles.
	 * @return bool True if access should be restricted, false otherwise.
	 */
	private function should_restrict_access( string $restriction_type, array $restriction_roles ): bool {
		$is_logged_in = is_user_logged_in();
		$current_user = wp_get_current_user();

		switch ( $restriction_type ) {
			case 'logged_out':
				// Restrict logged-out users.
				return ! $is_logged_in;

			case 'logged_in':
				// Restrict logged-in users.
				return $is_logged_in;

			case 'specific_roles':
				// Restrict if user doesn't have any of the specified roles.
				if ( ! $is_logged_in ) {
					return true;
				}

				if ( empty( $restriction_roles ) ) {
					return false;
				}

				$user_roles = $current_user->roles;
				$has_role   = false;

				foreach ( $restriction_roles as $role ) {
					if ( in_array( $role, $user_roles, true ) ) {
						$has_role = true;
						break;
					}
				}

				// Restrict if user doesn't have any of the specified roles.
				return ! $has_role;

			default:
				return false;
		}
	}

	/**
	 * Store restricted elements for after-render processing.
	 *
	 * @var array
	 */
	private array $restricted_elements = array();

	/**
	 * Handle restriction action.
	 *
	 * @param Element_Base $element Element instance.
	 * @param string       $action Action type.
	 * @param string       $message Restriction message.
	 * @return void
	 */
	private function handle_restriction_action( Element_Base $element, string $action, string $message ): void {
		$element_id = $element->get_id();

		switch ( $action ) {
			case 'hide':
				// Hide the widget completely.
				$element->add_render_attribute( '_wrapper', 'style', 'display: none !important;' );
				break;

			case 'show_message':
				// Store element data for after-render processing.
				$this->restricted_elements[ $element_id ] = array(
					'action'  => 'show_message',
					'message' => $message,
					'element' => $element,
				);
				$element->add_render_attribute( '_wrapper', 'class', 'smart-loginizer-restricted-widget' );
				$element->add_render_attribute( '_wrapper', 'data-restriction-action', 'show_message' );
				$element->add_render_attribute( '_wrapper', 'data-restriction-message', esc_attr( $message ) );
				break;

			case 'show_login':
				// Store element data for after-render processing.
				$this->restricted_elements[ $element_id ] = array(
					'action'  => 'show_login',
					'element' => $element,
				);
				$element->add_render_attribute( '_wrapper', 'class', 'smart-loginizer-restricted-widget-login' );
				$element->add_render_attribute( '_wrapper', 'data-restriction-action', 'show_login' );
				break;
		}
	}

	/**
	 * Handle after render to replace content.
	 *
	 * @param Element_Base $element Element instance.
	 * @return void
	 */
	public function handle_after_render( Element_Base $element ): void {
		$element_id = $element->get_id();

		if ( ! isset( $this->restricted_elements[ $element_id ] ) ) {
			return;
		}

		$restriction_data = $this->restricted_elements[ $element_id ];
		$action = $restriction_data['action'];

		if ( 'show_message' === $action ) {
			// Output message replacement script.
			$message = $restriction_data['message'];
			?>
			<script>
				(function() {
					var element = document.querySelector('[data-id="<?php echo esc_js( $element_id ); ?>"]');
					if (element) {
						var messageDiv = document.createElement('div');
						messageDiv.className = 'smart-loginizer-widget-restriction-message';
						messageDiv.style.cssText = 'padding: 20px; margin: 20px 0; background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; color: #856404;';
						messageDiv.innerHTML = <?php echo wp_json_encode( wp_kses_post( $message ) ); ?>;
						
						// Hide original content.
						var children = element.querySelectorAll(':scope > *');
						children.forEach(function(child) {
							child.style.display = 'none';
						});
						
						// Add message.
						element.insertBefore(messageDiv, element.firstChild);
					}
				})();
			</script>
			<?php
		} elseif ( 'show_login' === $action ) {
			// Output login template directly.
			$login_template = $this->get_login_template_content();
			if ( $login_template ) {
				// Hide original content with CSS.
				?>
				<style>
					[data-id="<?php echo esc_attr( $element_id ); ?>"] > *:not(.smart-loginizer-widget-login-template) {
						display: none !important;
					}
				</style>
				<div class="smart-loginizer-widget-login-template">
					<?php
					// Output the template content directly (not escaped, as it's from Elementor).
					echo $login_template; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor template output.
					?>
				</div>
				<?php
			} else {
				// Fallback message if template not found.
				?>
				<div class="smart-loginizer-widget-restriction-message" style="padding: 20px; margin: 20px 0; background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; color: #856404;">
					<?php esc_html_e( 'Login template not configured. Please set a login template in Smart Loginizer settings.', 'smart-loginizer' ); ?>
				</div>
				<?php
			}
		}
	}

	/**
	 * Enqueue restriction scripts.
	 *
	 * @return void
	 */
	public function enqueue_restriction_scripts(): void {
		// Scripts are inline in handle_after_render, but we can add CSS here.
		wp_add_inline_style(
			'elementor-frontend',
			'
			.smart-loginizer-restricted-widget,
			.smart-loginizer-restricted-widget-login {
				position: relative;
			}
			.smart-loginizer-widget-restriction-message {
				padding: 20px;
				margin: 20px 0;
				background-color: #fff3cd;
				border: 1px solid #ffc107;
				border-radius: 4px;
				color: #856404;
			}
			'
		);
	}

	/**
	 * Get login template content.
	 *
	 * @return string|false Template content or false.
	 */
	public function get_login_template_content() {
		$options = get_option( 'smart_loginizer_settings', array() );
		$login_template_id = isset( $options['elementor_widget_restriction_login_template_id'] ) ? absint( $options['elementor_widget_restriction_login_template_id'] ) : 0;

		if ( $login_template_id > 0 ) {
			$template = get_post( $login_template_id );
			if ( $template && 'elementor_library' === $template->post_type ) {
				if ( class_exists( '\Elementor\Plugin' ) ) {
					return \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $login_template_id );
				}
			}
		}

		return false;
	}
}

