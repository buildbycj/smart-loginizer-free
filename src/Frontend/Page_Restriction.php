<?php
/**
 * Page Restriction Frontend functionality.
 *
 * @package SmartLoginizer\Frontend
 */

namespace SmartLoginizer\Frontend;

use SmartLoginizer\Helpers\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Page Restriction Frontend class.
 */
class Page_Restriction {

	/**
	 * Helpers instance.
	 *
	 * @var Helpers
	 */
	private Helpers $helpers;

	/**
	 * Current restriction message.
	 *
	 * @var string
	 */
	private string $current_message = '';

	/**
	 * Initialize page restriction frontend.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->helpers = new Helpers();
		add_action( 'template_redirect', array( $this, 'check_page_restrictions' ), 1 );
	}

	/**
	 * Check page restrictions and enforce them.
	 *
	 * @return void
	 */
	public function check_page_restrictions(): void {
		// Don't restrict admin pages.
		if ( is_admin() ) {
			return;
		}

		// Don't restrict if user is editing.
		if ( isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		// Get current post.
		global $post;
		if ( ! $post || ! is_singular() ) {
			return;
		}

		// Check if restriction is enabled.
		$restriction_enabled = get_post_meta( $post->ID, '_smart_loginizer_restriction_enabled', true );
		if ( 'yes' !== $restriction_enabled ) {
			return;
		}

		// Get restriction settings.
		$restriction_type    = get_post_meta( $post->ID, '_smart_loginizer_restriction_type', true );
		$restriction_roles   = get_post_meta( $post->ID, '_smart_loginizer_restriction_roles', true );
		$restriction_message = get_post_meta( $post->ID, '_smart_loginizer_restriction_message', true );
		$restriction_action  = get_post_meta( $post->ID, '_smart_loginizer_restriction_action', true );
		$restriction_redirect = get_post_meta( $post->ID, '_smart_loginizer_restriction_redirect', true );
		$restriction_redirect_url = get_post_meta( $post->ID, '_smart_loginizer_restriction_redirect_url', true );

		// Default values.
		$restriction_type    = '' === $restriction_type ? 'logged_in' : $restriction_type;
		$restriction_roles   = is_array( $restriction_roles ) ? $restriction_roles : array();
		$restriction_message = '' === $restriction_message ? __( 'You do not have permission to access this page.', 'smart-loginizer' ) : $restriction_message;
		$restriction_action  = '' === $restriction_action ? 'message' : $restriction_action;
		$restriction_redirect = '' === $restriction_redirect ? 0 : absint( $restriction_redirect );
		// Ensure redirect URL is properly formatted.
		$restriction_redirect_url = '' === $restriction_redirect_url ? '' : esc_url_raw( trim( $restriction_redirect_url ) );

		// Check if user should be restricted.
		$should_restrict = $this->should_restrict_access( $restriction_type, $restriction_roles );

		if ( ! $should_restrict ) {
			return;
		}

		// Handle restriction action.
		$this->handle_restriction_action( $restriction_action, $restriction_message, $restriction_redirect, $restriction_redirect_url );
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
	 * Handle restriction action.
	 *
	 * @param string $action Action type.
	 * @param string $message Restriction message.
	 * @param int    $redirect_page_id Redirect page ID.
	 * @param string $redirect_url Redirect URL.
	 * @return void
	 */
	private function handle_restriction_action( string $action, string $message, int $redirect_page_id, string $redirect_url ): void {
		switch ( $action ) {
			case 'message':
				// Show message.
				$this->display_restriction_message( $message );
				break;

			case 'redirect_login':
				// Redirect to login page.
				$this->redirect_to_login_page();
				break;

			case 'redirect_page':
				// Redirect to specific page.
				if ( $redirect_page_id > 0 ) {
					$redirect_url = get_permalink( $redirect_page_id );
					if ( $redirect_url ) {
						wp_safe_redirect( $redirect_url );
						exit;
					}
				}
				// Fallback to message if redirect fails.
				$this->display_restriction_message( $message );
				break;

			case 'redirect_url':
				// Redirect to custom URL.
				if ( ! empty( $redirect_url ) ) {
					// Validate and sanitize the URL.
					$redirect_url = esc_url_raw( $redirect_url );
					
					// Check if URL is valid.
					if ( filter_var( $redirect_url, FILTER_VALIDATE_URL ) ) {
						// Check if it's an external URL.
						$site_url = home_url();
						$redirect_host = wp_parse_url( $redirect_url, PHP_URL_HOST );
						$site_host = wp_parse_url( $site_url, PHP_URL_HOST );
						
						// Use wp_redirect for external URLs, wp_safe_redirect for same domain.
						if ( $redirect_host && $redirect_host !== $site_host ) {
							// External URL - use wp_redirect.
							wp_redirect( $redirect_url ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
							exit;
						} else {
							// Same domain - use wp_safe_redirect.
							wp_safe_redirect( $redirect_url );
							exit;
						}
					}
				}
				// Fallback to message if redirect fails.
				$this->display_restriction_message( $message );
				break;

			default:
				// Default to message.
				$this->display_restriction_message( $message );
				break;
		}
	}

	/**
	 * Display restriction message.
	 *
	 * @param string $message Message to display.
	 * @return void
	 */
	private function display_restriction_message( string $message ): void {
		// Store message in class property for same request.
		$this->current_message = $message;

		// Add filter to display message at the top of content with high priority.
		add_filter( 'the_content', array( $this, 'prepend_restriction_message' ), 999 );
		
		// Add CSS for the message.
		add_action( 'wp_head', array( $this, 'display_restriction_message_css' ) );
		
		// Also add action to display notice in footer.
		add_action( 'wp_footer', array( $this, 'display_restriction_notice' ) );
	}

	/**
	 * Display restriction message CSS.
	 *
	 * @return void
	 */
	public function display_restriction_message_css(): void {
		if ( ! empty( $this->current_message ) ) {
			?>
			<style>
				.smart-loginizer-restriction-message {
					padding: 20px;
					margin: 20px 0;
					background-color: #fff3cd;
					border: 1px solid #ffc107;
					border-radius: 4px;
					color: #856404;
				}
			</style>
			<?php
		}
	}

	/**
	 * Prepend restriction message to content.
	 *
	 * @param string $content Post content.
	 * @return string
	 */
	public function prepend_restriction_message( string $content ): string {
		if ( ! empty( $this->current_message ) ) {
			$message_html = '<div class="smart-loginizer-restriction-message" style="padding: 20px; margin: 20px 0; background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; color: #856404;">' . wp_kses_post( $this->current_message ) . '</div>';
			$content = $message_html . $content;
		}
		return $content;
	}

	/**
	 * Display restriction notice in footer.
	 *
	 * @return void
	 */
	public function display_restriction_notice(): void {
		if ( ! empty( $this->current_message ) ) {
			?>
			<div class="smart-loginizer-restriction-notice" style="position: fixed; top: 20px; right: 20px; padding: 15px 20px; background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; color: #856404; box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 999999; max-width: 400px;">
				<?php echo wp_kses_post( $this->current_message ); ?>
			</div>
			<script>
				setTimeout(function() {
					var notice = document.querySelector('.smart-loginizer-restriction-notice');
					if (notice) {
						notice.style.opacity = '0';
						notice.style.transition = 'opacity 0.5s';
						setTimeout(function() {
							notice.remove();
						}, 500);
					}
				}, 5000);
			</script>
			<?php
		}
	}

	/**
	 * Redirect to login page.
	 *
	 * @return void
	 */
	private function redirect_to_login_page(): void {
		$options = get_option( 'smart_loginizer_settings', array() );
		$login_template_id = isset( $options['page_restriction_login_template_id'] ) ? absint( $options['page_restriction_login_template_id'] ) : 0;

		// If custom login template is set, create a temporary page or use a special endpoint.
		if ( $login_template_id > 0 ) {
			// Check if Elementor template exists.
			$template = get_post( $login_template_id );
			if ( $template && 'elementor_library' === $template->post_type ) {
				// Use a query parameter to identify this as a login redirect.
				$redirect_url = add_query_arg(
					array(
						'smart_loginizer_login' => '1',
						'redirect_to'           => urlencode( get_permalink() ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode
					),
					home_url()
				);
				wp_safe_redirect( $redirect_url );
				exit;
			}
		}

		// Fallback to default login page or home.
		$login_url = wp_login_url( get_permalink() );
		wp_safe_redirect( $login_url );
		exit;
	}

	/**
	 * Get login template content.
	 *
	 * @return string|false Template content or false.
	 */
	public function get_login_template_content() {
		$options = get_option( 'smart_loginizer_settings', array() );
		$login_template_id = isset( $options['page_restriction_login_template_id'] ) ? absint( $options['page_restriction_login_template_id'] ) : 0;

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

