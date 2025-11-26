<?php
/**
 * Security handler.
 *
 * @package SmartLoginizer\Security
 */

namespace SmartLoginizer\Security;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Security class.
 */
class Security {

	/**
	 * Verify nonce.
	 *
	 * @param string $nonce Nonce value.
	 * @param string $action Nonce action.
	 * @return bool
	 */
	public function verify_nonce( string $nonce, string $action ): bool {
		return wp_verify_nonce( $nonce, $action );
	}

	/**
	 * Create nonce.
	 *
	 * @param string $action Nonce action.
	 * @return string
	 */
	public function create_nonce( string $action ): string {
		return wp_create_nonce( $action );
	}

	/**
	 * Sanitize text field.
	 *
	 * @param string $value Value to sanitize.
	 * @return string
	 */
	public function sanitize_text_field( string $value ): string {
		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize email.
	 *
	 * @param string $email Email to sanitize.
	 * @return string
	 */
	public function sanitize_email( string $email ): string {
		return sanitize_email( $email );
	}

	/**
	 * Validate email.
	 *
	 * @param string $email Email to validate.
	 * @return bool
	 */
	public function is_email( string $email ): bool {
		return is_email( $email );
	}

	/**
	 * Escape HTML.
	 *
	 * @param string $text Text to escape.
	 * @return string
	 */
	public function esc_html( string $text ): string {
		return esc_html( $text );
	}

	/**
	 * Escape attribute.
	 *
	 * @param string $text Text to escape.
	 * @return string
	 */
	public function esc_attr( string $text ): string {
		return esc_attr( $text );
	}

	/**
	 * Escape URL.
	 *
	 * @param string $url URL to escape.
	 * @return string
	 */
	public function esc_url( string $url ): string {
		return esc_url( $url );
	}

	/**
	 * Check if IP is locked out due to wrong password attempts.
	 *
	 * @param string $ip IP address.
	 * @param int    $max_attempts Maximum attempts allowed.
	 * @param int    $lockout_duration Lockout duration in minutes.
	 * @return array Array with 'locked' bool and 'message' string.
	 */
	public function check_password_lockout( string $ip, int $max_attempts = 5, int $lockout_duration = 15 ): array {
		$transient_key = 'smart_loginizer_failed_login_' . md5( $ip );
		$failed_attempts = get_transient( $transient_key );

		if ( false === $failed_attempts ) {
			$failed_attempts = 0;
		}

		if ( $failed_attempts >= $max_attempts ) {
			$lockout_key = 'smart_loginizer_lockout_' . md5( $ip );
			$lockout_until = get_transient( $lockout_key );

			if ( false !== $lockout_until ) {
				$remaining = $lockout_until - time();
				if ( $remaining > 0 ) {
					$minutes = ceil( $remaining / 60 );
					return array(
						'locked'  => true,
						'message' => sprintf(
							/* translators: %d: minutes */
							__( 'Too many failed login attempts. Please try again in %d minute(s).', 'smart-loginizer' ),
							$minutes
						),
					);
				} else {
					// Lockout expired, reset attempts.
					delete_transient( $transient_key );
					delete_transient( $lockout_key );
				}
			}
		}

		return array(
			'locked'  => false,
			'message' => '',
		);
	}

	/**
	 * Record failed login attempt.
	 *
	 * @param string $ip IP address.
	 * @param int    $max_attempts Maximum attempts allowed.
	 * @param int    $lockout_duration Lockout duration in minutes.
	 * @return void
	 */
	public function record_failed_login( string $ip, int $max_attempts = 5, int $lockout_duration = 15 ): void {
		$transient_key = 'smart_loginizer_failed_login_' . md5( $ip );
		$failed_attempts = get_transient( $transient_key );

		if ( false === $failed_attempts ) {
			$failed_attempts = 0;
		}

		$failed_attempts++;
		set_transient( $transient_key, $failed_attempts, HOUR_IN_SECONDS );

		if ( $failed_attempts >= $max_attempts ) {
			$lockout_key = 'smart_loginizer_lockout_' . md5( $ip );
			set_transient( $lockout_key, time() + ( $lockout_duration * MINUTE_IN_SECONDS ), $lockout_duration * MINUTE_IN_SECONDS );
		}
	}

	/**
	 * Clear failed login attempts for IP.
	 *
	 * @param string $ip IP address.
	 * @return void
	 */
	public function clear_failed_login( string $ip ): void {
		$transient_key = 'smart_loginizer_failed_login_' . md5( $ip );
		$lockout_key = 'smart_loginizer_lockout_' . md5( $ip );
		delete_transient( $transient_key );
		delete_transient( $lockout_key );
	}

	/**
	 * Check if IP has reached registration limit.
	 *
	 * @param string $ip IP address.
	 * @param int    $max_registrations Maximum registrations allowed.
	 * @param int    $period_hours Period in hours.
	 * @return array Array with 'limited' bool and 'message' string.
	 */
	public function check_registration_limit( string $ip, int $max_registrations = 3, int $period_hours = 24 ): array {
		$transient_key = 'smart_loginizer_registrations_' . md5( $ip );
		$registrations = get_transient( $transient_key );

		if ( false === $registrations ) {
			$registrations = 0;
		}

		if ( $registrations >= $max_registrations ) {
			return array(
				'limited' => true,
				'message' => sprintf(
					/* translators: %d: hours */
					__( 'Registration limit reached. Please try again after %d hour(s).', 'smart-loginizer' ),
					$period_hours
				),
			);
		}

		return array(
			'limited' => false,
			'message' => '',
		);
	}

	/**
	 * Record registration attempt.
	 *
	 * @param string $ip IP address.
	 * @param int    $period_hours Period in hours.
	 * @return void
	 */
	public function record_registration( string $ip, int $period_hours = 24 ): void {
		$transient_key = 'smart_loginizer_registrations_' . md5( $ip );
		$registrations = get_transient( $transient_key );

		if ( false === $registrations ) {
			$registrations = 0;
		}

		$registrations++;
		set_transient( $transient_key, $registrations, $period_hours * HOUR_IN_SECONDS );
	}

	/**
	 * Check if email domain is banned.
	 *
	 * @param string $email Email address.
	 * @param string $banned_domains Banned domains (comma or newline separated).
	 * @return bool
	 */
	public function is_email_domain_banned( string $email, string $banned_domains = '' ): bool {
		if ( empty( $banned_domains ) || empty( $email ) ) {
			return false;
		}

		// Extract domain from email.
		$at_pos = strrpos( $email, '@' );
		if ( false === $at_pos ) {
			return false;
		}

		$email_domain = substr( $email, $at_pos + 1 );
		if ( empty( $email_domain ) ) {
			return false;
		}

		$email_domain = strtolower( trim( $email_domain ) );

		// Normalize banned domains list - handle both comma and newline separators.
		$banned_domains = str_replace( array( "\r\n", "\r", "\n" ), ',', $banned_domains );
		$banned_list = preg_split( '/[,\s]+/', $banned_domains );
		$banned_list = array_map( 'trim', $banned_list );
		$banned_list = array_map( 'strtolower', $banned_list );
		$banned_list = array_filter( $banned_list );

		if ( empty( $banned_list ) ) {
			return false;
		}

		return in_array( $email_domain, $banned_list, true );
	}

	/**
	 * Get user's country code from IP.
	 *
	 * @param string $ip IP address.
	 * @return string Country code or empty string.
	 */
	public function get_country_from_ip( string $ip ): string {
		// Check cache first.
		$transient_key = 'smart_loginizer_country_' . md5( $ip );
		$country = get_transient( $transient_key );

		if ( false !== $country ) {
			return $country;
		}

		// Get service and API key from settings.
		$helpers = new \SmartLoginizer\Helpers\Helpers();
		$service = $helpers->get_option( 'ip_geolocation_service', 'ip-api-free' );
		$api_key = $helpers->get_option( 'ip_geolocation_api_key', '' );

		$country = '';

		switch ( $service ) {
			case 'ip-api-pro':
				// ip-api.com Pro (requires API key).
				if ( ! empty( $api_key ) ) {
					$response = wp_remote_get(
						'http://pro.ip-api.com/json/' . $ip . '?key=' . urlencode( $api_key ) . '&fields=countryCode',
						array(
							'timeout' => 5,
						)
					);

					if ( ! is_wp_error( $response ) ) {
						$body = wp_remote_retrieve_body( $response );
						$data = json_decode( $body, true );
						if ( isset( $data['countryCode'] ) ) {
							$country = strtoupper( $data['countryCode'] );
						}
					}
				}
				break;

			case 'ipapi-co':
				// ipapi.co (requires API key).
				if ( ! empty( $api_key ) ) {
					$response = wp_remote_get(
						'https://ipapi.co/' . $ip . '/country_code/',
						array(
							'timeout' => 5,
							'headers' => array(
								'Authorization' => 'Token ' . $api_key,
							),
						)
					);

					if ( ! is_wp_error( $response ) ) {
						$country = trim( wp_remote_retrieve_body( $response ) );
						$country = strtoupper( $country );
					}
				}
				break;

			case 'ipgeolocation-io':
				// ipgeolocation.io (requires API key).
				if ( ! empty( $api_key ) ) {
					$response = wp_remote_get(
						'https://api.ipgeolocation.io/ipgeo?apiKey=' . urlencode( $api_key ) . '&ip=' . urlencode( $ip ),
						array(
							'timeout' => 5,
						)
					);

					if ( ! is_wp_error( $response ) ) {
						$body = wp_remote_retrieve_body( $response );
						$data = json_decode( $body, true );
						if ( isset( $data['country_code2'] ) ) {
							$country = strtoupper( $data['country_code2'] );
						}
					}
				}
				break;

			case 'ip-api-free':
			default:
				// ip-api.com Free (no API key required) - Default.
				$response = wp_remote_get(
					'http://ip-api.com/json/' . $ip . '?fields=countryCode',
					array(
						'timeout' => 5,
						'sslverify' => false, // Some servers may have SSL issues.
					)
				);

				if ( ! is_wp_error( $response ) ) {
					$response_code = wp_remote_retrieve_response_code( $response );
					if ( 200 === $response_code ) {
						$body = wp_remote_retrieve_body( $response );
						$data = json_decode( $body, true );
						if ( isset( $data['countryCode'] ) && ! empty( $data['countryCode'] ) ) {
							$country = strtoupper( trim( $data['countryCode'] ) );
						}
					}
				}
				break;
		}

		// Cache the result for 24 hours.
		if ( ! empty( $country ) ) {
			set_transient( $transient_key, $country, DAY_IN_SECONDS );
		}

		return $country;
	}

	/**
	 * Check if location is allowed/blocked.
	 *
	 * @param string $ip IP address.
	 * @param string $restriction_type 'blocked' or 'allowed'.
	 * @param string $countries Comma or newline-separated country codes.
	 * @return array Array with 'blocked' bool and 'message' string.
	 */
	public function check_location_restriction( string $ip, string $restriction_type = 'blocked', string $countries = '' ): array {
		if ( empty( $countries ) ) {
			return array(
				'blocked' => false,
				'message' => '',
			);
		}

		// Normalize country list - handle both comma and newline separators.
		$countries = str_replace( array( "\r\n", "\r", "\n" ), ',', $countries );
		$country_list = array_map( 'trim', explode( ',', $countries ) );
		$country_list = array_map( 'strtoupper', $country_list );
		$country_list = array_filter( $country_list );

		if ( empty( $country_list ) ) {
			return array(
				'blocked' => false,
				'message' => '',
			);
		}

		$user_country = $this->get_country_from_ip( $ip );
		if ( empty( $user_country ) ) {
			// If we can't determine country, allow by default for security (to avoid blocking legitimate users).
			// You can change this behavior if needed.
			return array(
				'blocked' => false,
				'message' => '',
			);
		}

		$is_in_list = in_array( $user_country, $country_list, true );

		if ( 'blocked' === $restriction_type && $is_in_list ) {
			return array(
				'blocked' => true,
				'message' => __( 'Access from your location is not allowed.', 'smart-loginizer' ),
			);
		}

		if ( 'allowed' === $restriction_type && ! $is_in_list ) {
			return array(
				'blocked' => true,
				'message' => __( 'Access from your location is not allowed.', 'smart-loginizer' ),
			);
		}

		return array(
			'blocked' => false,
			'message' => '',
		);
	}

	/**
	 * Get client IP address.
	 *
	 * @return string
	 */
	public function get_client_ip(): string {
		$ip_keys = array(
			'HTTP_CF_CONNECTING_IP', // Cloudflare.
			'HTTP_X_REAL_IP',
			'HTTP_X_FORWARDED_FOR',
			'REMOTE_ADDR',
		);

		foreach ( $ip_keys as $key ) {
			if ( ! empty( $_SERVER[ $key ] ) ) {
				$ip = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );
				// Handle comma-separated IPs (from proxies).
				if ( strpos( $ip, ',' ) !== false ) {
					$ip = trim( explode( ',', $ip )[0] );
				}
				if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
					return $ip;
				}
			}
		}

		return '0.0.0.0';
	}
}

