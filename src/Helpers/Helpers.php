<?php
/**
 * Helper functions.
 *
 * @package SmartLoginizer\Helpers
 */

namespace SmartLoginizer\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helpers class.
 */
class Helpers {

	/**
	 * Get plugin option.
	 *
	 * @param string $key Option key.
	 * @param mixed  $default Default value.
	 * @return mixed
	 */
	public function get_option( string $key, $default = false ) {
		$options = get_option( 'smart_loginizer_settings', array() );
		return isset( $options[ $key ] ) ? $options[ $key ] : $default;
	}

	/**
	 * Update plugin option.
	 *
	 * @param string $key Option key.
	 * @param mixed  $value Option value.
	 * @return bool
	 */
	public function update_option( string $key, $value ): bool {
		$options = get_option( 'smart_loginizer_settings', array() );
		$options[ $key ] = $value;
		return update_option( 'smart_loginizer_settings', $options );
	}

	/**
	 * Check if user is logged in.
	 *
	 * @return bool
	 */
	public function is_user_logged_in(): bool {
		return is_user_logged_in();
	}

	/**
	 * Get current user ID.
	 *
	 * @return int
	 */
	public function get_current_user_id(): int {
		return get_current_user_id();
	}

	/**
	 * Sanitize text field.
	 *
	 * @param string $value Value to sanitize.
	 * @return string
	 */
	public function sanitize_text( string $value ): string {
		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize URL.
	 *
	 * @param string $url URL to sanitize.
	 * @return string
	 */
	public function sanitize_url( string $url ): string {
		return esc_url_raw( $url );
	}

	/**
	 * Get redirect URL after login.
	 *
	 * @param string $default Default URL.
	 * @return string
	 */
	public function get_login_redirect_url( string $default = '' ): string {
		if ( empty( $default ) ) {
			$default = home_url();
		}

		/**
		 * Filter login redirect URL.
		 *
		 * @param string $url Redirect URL.
		 */
		$redirect_url = apply_filters( 'smart_loginizer_login_redirect_url', $default );

		return esc_url_raw( $redirect_url );
	}

	/**
	 * Get redirect URL after registration.
	 *
	 * @param string $default Default URL.
	 * @return string
	 */
	public function get_registration_redirect_url( string $default = '' ): string {
		if ( empty( $default ) ) {
			$default = home_url();
		}

		return esc_url_raw( $default );
	}

	/**
	 * Get redirect URL after logout.
	 *
	 * @param string $default Default URL.
	 * @return string
	 */
	public function get_logout_redirect_url( string $default = '' ): string {
		if ( empty( $default ) ) {
			$default = home_url();
		}

		return esc_url_raw( $default );
	}

	/**
	 * Get enabled social login providers.
	 *
	 * @return array Array of provider keys that are configured.
	 */
	public function get_enabled_social_providers(): array {
		$providers = array();
		
		// Check Google (support legacy gmail_client_id).
		$google_client_id = $this->get_option( 'google_client_id' );
		if ( empty( $google_client_id ) ) {
			$google_client_id = $this->get_option( 'gmail_client_id' );
		}
		if ( ! empty( $google_client_id ) ) {
			$providers['google'] = array(
				'name' => 'Google',
				'icon' => 'google',
			);
		}
		
		// Check X (Twitter).
		if ( ! empty( $this->get_option( 'x_client_id' ) ) ) {
			$providers['x'] = array(
				'name' => 'X',
				'icon' => 'x',
			);
		}
		
		// Check LinkedIn.
		if ( ! empty( $this->get_option( 'linkedin_client_id' ) ) ) {
			$providers['linkedin'] = array(
				'name' => 'LinkedIn',
				'icon' => 'linkedin',
			);
		}
		
		// Check Facebook.
		if ( ! empty( $this->get_option( 'facebook_app_id' ) ) ) {
			$providers['facebook'] = array(
				'name' => 'Facebook',
				'icon' => 'facebook',
			);
		}
		
		return $providers;
	}
}

