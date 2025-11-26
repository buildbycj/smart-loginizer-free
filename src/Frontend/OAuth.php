<?php
/**
 * OAuth handler for multiple providers.
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
 * OAuth class.
 */
class OAuth {

	/**
	 * Helpers instance.
	 *
	 * @var Helpers
	 */
	private Helpers $helpers;

	/**
	 * OAuth redirect URI.
	 *
	 * @var string
	 */
	private string $redirect_uri;

	/**
	 * Supported providers.
	 *
	 * @var array
	 */
	private array $providers = array(
		'google'   => 'Google',
		'x'        => 'X (Twitter)',
		'linkedin' => 'LinkedIn',
		'facebook' => 'Facebook',
	);

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->helpers     = new Helpers();
		$this->redirect_uri = admin_url( 'admin-ajax.php?action=smart_loginizer_oauth_callback' );

		add_action( 'wp_ajax_smart_loginizer_oauth_callback', array( $this, 'handle_oauth_callback' ) );
		add_action( 'wp_ajax_nopriv_smart_loginizer_oauth_callback', array( $this, 'handle_oauth_callback' ) );
		add_action( 'wp_ajax_smart_loginizer_oauth_init', array( $this, 'init_oauth' ) );
		add_action( 'wp_ajax_nopriv_smart_loginizer_oauth_init', array( $this, 'init_oauth' ) );
	}

	/**
	 * Get OAuth authorization URL for a provider.
	 *
	 * @param string $provider Provider name.
	 * @return string
	 */
	public function get_authorization_url( string $provider = 'google' ): string {
		switch ( $provider ) {
			case 'google':
				return $this->get_google_auth_url();
			case 'x':
				return $this->get_x_auth_url();
			case 'linkedin':
				return $this->get_linkedin_auth_url();
			case 'facebook':
				return $this->get_facebook_auth_url();
			default:
				return '';
		}
	}

	/**
	 * Get Google authorization URL.
	 *
	 * @return string
	 */
	private function get_google_auth_url(): string {
		$client_id = $this->helpers->get_option( 'google_client_id' );
		// Support legacy gmail_client_id.
		if ( empty( $client_id ) ) {
			$client_id = $this->helpers->get_option( 'gmail_client_id' );
		}

		if ( empty( $client_id ) ) {
			return '';
		}

		$params = array(
			'client_id'     => $client_id,
			'redirect_uri'  => $this->redirect_uri . '&provider=google',
			'response_type' => 'code',
			'scope'         => 'openid email profile',
			'access_type'   => 'offline',
			'prompt'        => 'consent',
		);

		return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query( $params );
	}

	/**
	 * Get X (Twitter) authorization URL.
	 *
	 * @return string
	 */
	private function get_x_auth_url(): string {
		$client_id = $this->helpers->get_option( 'x_client_id' );

		if ( empty( $client_id ) ) {
			return '';
		}

		// Generate state for CSRF protection.
		$state = wp_generate_password( 32, false );
		set_transient( 'smart_loginizer_oauth_state_' . $state, 'x', 600 );

		$params = array(
			'response_type' => 'code',
			'client_id'     => $client_id,
			'redirect_uri'  => $this->redirect_uri . '&provider=x',
			'scope'         => 'tweet.read users.read offline.access',
			'state'         => $state,
			'code_challenge' => $this->generate_code_challenge(),
			'code_challenge_method' => 'plain',
		);

		return 'https://twitter.com/i/oauth2/authorize?' . http_build_query( $params );
	}

	/**
	 * Get LinkedIn authorization URL.
	 *
	 * @return string
	 */
	private function get_linkedin_auth_url(): string {
		$client_id = $this->helpers->get_option( 'linkedin_client_id' );

		if ( empty( $client_id ) ) {
			return '';
		}

		$state = wp_generate_password( 32, false );
		set_transient( 'smart_loginizer_oauth_state_' . $state, 'linkedin', 600 );

		$params = array(
			'response_type' => 'code',
			'client_id'     => $client_id,
			'redirect_uri'  => $this->redirect_uri . '&provider=linkedin',
			'scope'         => 'openid profile email',
			'state'         => $state,
		);

		return 'https://www.linkedin.com/oauth/v2/authorization?' . http_build_query( $params );
	}

	/**
	 * Get Facebook authorization URL.
	 *
	 * @return string
	 */
	private function get_facebook_auth_url(): string {
		$client_id = $this->helpers->get_option( 'facebook_app_id' );

		if ( empty( $client_id ) ) {
			return '';
		}

		$state = wp_generate_password( 32, false );
		set_transient( 'smart_loginizer_oauth_state_' . $state, 'facebook', 600 );

		$params = array(
			'client_id'     => $client_id,
			'redirect_uri'  => $this->redirect_uri . '&provider=facebook',
			'scope'         => 'email public_profile',
			'response_type' => 'code',
			'state'         => $state,
		);

		return 'https://www.facebook.com/v18.0/dialog/oauth?' . http_build_query( $params );
	}

	/**
	 * Generate code challenge for PKCE.
	 *
	 * @return string
	 */
	private function generate_code_challenge(): string {
		$verifier = wp_generate_password( 43, false, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-._~' );
		set_transient( 'smart_loginizer_code_verifier', $verifier, 600 );
		return $verifier;
	}

	/**
	 * Initialize OAuth.
	 *
	 * @return void
	 */
	public function init_oauth(): void {
		check_ajax_referer( 'smart_loginizer_nonce', 'nonce' );

		$provider = isset( $_POST['provider'] ) ? sanitize_text_field( wp_unslash( $_POST['provider'] ) ) : 'google';

		if ( ! array_key_exists( $provider, $this->providers ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid provider.', 'smart-loginizer' ) ) );
		}

		$auth_url = $this->get_authorization_url( $provider );

		if ( empty( $auth_url ) ) {
			wp_send_json_error(
				array(
					'message' => sprintf(
						/* translators: %s: OAuth provider name */
						__( '%s OAuth is not configured.', 'smart-loginizer' ),
						$this->providers[ $provider ]
					),
				)
			);
		}

		wp_send_json_success( array( 'auth_url' => $auth_url ) );
	}

	/**
	 * Handle OAuth callback.
	 *
	 * Note: OAuth callbacks from external providers (Google, X, LinkedIn, Facebook)
	 * come via GET parameters and cannot use WordPress nonces. Security is handled
	 * via state parameter verification which acts as a CSRF token.
	 *
	 * @return void
	 */
	public function handle_oauth_callback(): void {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- OAuth callbacks from external providers cannot use WordPress nonces. Security handled via state parameter.
		$code     = isset( $_GET['code'] ) ? sanitize_text_field( wp_unslash( $_GET['code'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- OAuth callbacks from external providers cannot use WordPress nonces. Security handled via state parameter.
		$provider = isset( $_GET['provider'] ) ? sanitize_text_field( wp_unslash( $_GET['provider'] ) ) : 'google';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- OAuth callbacks from external providers cannot use WordPress nonces. Security handled via state parameter.
		$state    = isset( $_GET['state'] ) ? sanitize_text_field( wp_unslash( $_GET['state'] ) ) : '';

		if ( empty( $code ) ) {
			wp_die( esc_html__( 'OAuth callback failed.', 'smart-loginizer' ) );
		}

		// Verify state for providers that use it.
		if ( ! empty( $state ) && in_array( $provider, array( 'x', 'linkedin', 'facebook' ), true ) ) {
			$stored_provider = get_transient( 'smart_loginizer_oauth_state_' . $state );
			if ( $stored_provider !== $provider ) {
				wp_die( esc_html__( 'Invalid state parameter.', 'smart-loginizer' ) );
			}
			delete_transient( 'smart_loginizer_oauth_state_' . $state );
		}

		// Exchange code for token.
		$token_data = $this->exchange_code_for_token( $code, $provider );

		if ( is_wp_error( $token_data ) ) {
			wp_die( esc_html( $token_data->get_error_message() ) );
		}

		// Get user info from provider.
		$user_info = $this->get_user_info( $token_data['access_token'], $provider );

		if ( is_wp_error( $user_info ) ) {
			wp_die( esc_html( $user_info->get_error_message() ) );
		}

		// Create or login user.
		$user = $this->create_or_login_user( $user_info, $provider );

		if ( is_wp_error( $user ) ) {
			wp_die( esc_html( $user->get_error_message() ) );
		}

		// Set auth cookie.
		wp_set_current_user( $user->ID );
		wp_set_auth_cookie( $user->ID );

		// Redirect.
		$redirect = $this->helpers->get_login_redirect_url();
		wp_safe_redirect( $redirect );
		exit;
	}

	/**
	 * Exchange authorization code for access token.
	 *
	 * @param string $code Authorization code.
	 * @param string $provider Provider name.
	 * @return array|\WP_Error
	 */
	private function exchange_code_for_token( string $code, string $provider = 'google' ) {
		switch ( $provider ) {
			case 'google':
				return $this->exchange_google_token( $code );
			case 'x':
				return $this->exchange_x_token( $code );
			case 'linkedin':
				return $this->exchange_linkedin_token( $code );
			case 'facebook':
				return $this->exchange_facebook_token( $code );
			default:
				return new \WP_Error( 'invalid_provider', __( 'Invalid provider.', 'smart-loginizer' ) );
		}
	}

	/**
	 * Exchange Google token.
	 *
	 * @param string $code Authorization code.
	 * @return array|\WP_Error
	 */
	private function exchange_google_token( string $code ) {
		$client_id     = $this->helpers->get_option( 'google_client_id' );
		$client_secret = $this->helpers->get_option( 'google_client_secret' );
		// Support legacy gmail_client_id and gmail_client_secret.
		if ( empty( $client_id ) ) {
			$client_id = $this->helpers->get_option( 'gmail_client_id' );
		}
		if ( empty( $client_secret ) ) {
			$client_secret = $this->helpers->get_option( 'gmail_client_secret' );
		}

		if ( empty( $client_id ) || empty( $client_secret ) ) {
			return new \WP_Error( 'oauth_not_configured', __( 'Google OAuth is not configured.', 'smart-loginizer' ) );
		}

		$response = wp_remote_post(
			'https://oauth2.googleapis.com/token',
			array(
				'body' => array(
					'code'          => $code,
					'client_id'     => $client_id,
					'client_secret' => $client_secret,
					'redirect_uri'  => $this->redirect_uri . '&provider=google',
					'grant_type'    => 'authorization_code',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $body['error'] ) ) {
			return new \WP_Error( 'oauth_error', $body['error_description'] ?? $body['error'] );
		}

		return $body;
	}

	/**
	 * Exchange X (Twitter) token.
	 *
	 * @param string $code Authorization code.
	 * @return array|\WP_Error
	 */
	private function exchange_x_token( string $code ) {
		$client_id     = $this->helpers->get_option( 'x_client_id' );
		$client_secret = $this->helpers->get_option( 'x_client_secret' );
		$code_verifier = get_transient( 'smart_loginizer_code_verifier' );

		if ( empty( $client_id ) || empty( $client_secret ) ) {
			return new \WP_Error( 'oauth_not_configured', __( 'X OAuth is not configured.', 'smart-loginizer' ) );
		}

		$credentials = base64_encode( $client_id . ':' . $client_secret );

		$response = wp_remote_post(
			'https://api.twitter.com/2/oauth2/token',
			array(
				'headers' => array(
					'Content-Type'  => 'application/x-www-form-urlencoded',
					'Authorization' => 'Basic ' . $credentials,
				),
				'body'    => array(
					'code'          => $code,
					'grant_type'   => 'authorization_code',
					'client_id'     => $client_id,
					'redirect_uri'  => $this->redirect_uri . '&provider=x',
					'code_verifier' => $code_verifier,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $body['error'] ) ) {
			return new \WP_Error( 'oauth_error', $body['error_description'] ?? $body['error'] );
		}

		return $body;
	}

	/**
	 * Exchange LinkedIn token.
	 *
	 * @param string $code Authorization code.
	 * @return array|\WP_Error
	 */
	private function exchange_linkedin_token( string $code ) {
		$client_id     = $this->helpers->get_option( 'linkedin_client_id' );
		$client_secret = $this->helpers->get_option( 'linkedin_client_secret' );

		if ( empty( $client_id ) || empty( $client_secret ) ) {
			return new \WP_Error( 'oauth_not_configured', __( 'LinkedIn OAuth is not configured.', 'smart-loginizer' ) );
		}

		$response = wp_remote_post(
			'https://www.linkedin.com/oauth/v2/accessToken',
			array(
				'body' => array(
					'grant_type'    => 'authorization_code',
					'code'          => $code,
					'redirect_uri'  => $this->redirect_uri . '&provider=linkedin',
					'client_id'     => $client_id,
					'client_secret' => $client_secret,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $body['error'] ) ) {
			return new \WP_Error( 'oauth_error', $body['error_description'] ?? $body['error'] );
		}

		return $body;
	}

	/**
	 * Exchange Facebook token.
	 *
	 * @param string $code Authorization code.
	 * @return array|\WP_Error
	 */
	private function exchange_facebook_token( string $code ) {
		$client_id     = $this->helpers->get_option( 'facebook_app_id' );
		$client_secret = $this->helpers->get_option( 'facebook_app_secret' );

		if ( empty( $client_id ) || empty( $client_secret ) ) {
			return new \WP_Error( 'oauth_not_configured', __( 'Facebook OAuth is not configured.', 'smart-loginizer' ) );
		}

		$response = wp_remote_get(
			'https://graph.facebook.com/v18.0/oauth/access_token?' . http_build_query(
				array(
					'client_id'     => $client_id,
					'client_secret' => $client_secret,
					'redirect_uri'  => $this->redirect_uri . '&provider=facebook',
					'code'          => $code,
				)
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $body['error'] ) ) {
			return new \WP_Error( 'oauth_error', $body['error']['message'] ?? 'Unknown error' );
		}

		return $body;
	}

	/**
	 * Get user info from provider.
	 *
	 * @param string $access_token Access token.
	 * @param string $provider Provider name.
	 * @return array|\WP_Error
	 */
	private function get_user_info( string $access_token, string $provider = 'google' ) {
		switch ( $provider ) {
			case 'google':
				return $this->get_google_user_info( $access_token );
			case 'x':
				return $this->get_x_user_info( $access_token );
			case 'linkedin':
				return $this->get_linkedin_user_info( $access_token );
			case 'facebook':
				return $this->get_facebook_user_info( $access_token );
			default:
				return new \WP_Error( 'invalid_provider', __( 'Invalid provider.', 'smart-loginizer' ) );
		}
	}

	/**
	 * Get user info from Google.
	 *
	 * @param string $access_token Access token.
	 * @return array|\WP_Error
	 */
	private function get_google_user_info( string $access_token ) {
		$response = wp_remote_get(
			'https://www.googleapis.com/oauth2/v2/userinfo',
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $access_token,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $body['error'] ) ) {
			return new \WP_Error( 'api_error', $body['error']['message'] ?? 'Unknown error' );
		}

		return array(
			'email'      => $body['email'] ?? '',
			'name'       => $body['name'] ?? '',
			'given_name' => $body['given_name'] ?? '',
			'family_name' => $body['family_name'] ?? '',
		);
	}

	/**
	 * Get user info from X (Twitter).
	 *
	 * @param string $access_token Access token.
	 * @return array|\WP_Error
	 */
	private function get_x_user_info( string $access_token ) {
		$response = wp_remote_get(
			'https://api.twitter.com/2/users/me?user.fields=profile_image_url',
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $access_token,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $body['errors'] ) ) {
			return new \WP_Error( 'api_error', $body['errors'][0]['message'] ?? 'Unknown error' );
		}

		$user_data = $body['data'] ?? array();

		// X/Twitter doesn't provide email by default, use username as email.
		$username = $user_data['username'] ?? '';
		$name     = $user_data['name'] ?? '';

		return array(
			'email'      => $username . '@x.com',
			'name'       => $name,
			'given_name' => $name,
			'family_name' => '',
		);
	}

	/**
	 * Get user info from LinkedIn.
	 *
	 * @param string $access_token Access token.
	 * @return array|\WP_Error
	 */
	private function get_linkedin_user_info( string $access_token ) {
		$response = wp_remote_get(
			'https://api.linkedin.com/v2/userinfo',
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $access_token,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $body['error'] ) ) {
			return new \WP_Error( 'api_error', $body['error_description'] ?? 'Unknown error' );
		}

		$name_parts = explode( ' ', $body['name'] ?? '', 2 );

		return array(
			'email'      => $body['email'] ?? '',
			'name'       => $body['name'] ?? '',
			'given_name' => $name_parts[0] ?? '',
			'family_name' => $name_parts[1] ?? '',
		);
	}

	/**
	 * Get user info from Facebook.
	 *
	 * @param string $access_token Access token.
	 * @return array|\WP_Error
	 */
	private function get_facebook_user_info( string $access_token ) {
		$response = wp_remote_get(
			'https://graph.facebook.com/v18.0/me?fields=id,name,email,first_name,last_name',
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $access_token,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $body['error'] ) ) {
			return new \WP_Error( 'api_error', $body['error']['message'] ?? 'Unknown error' );
		}

		return array(
			'email'      => $body['email'] ?? '',
			'name'       => $body['name'] ?? '',
			'given_name' => $body['first_name'] ?? '',
			'family_name' => $body['last_name'] ?? '',
		);
	}

	/**
	 * Create or login user.
	 *
	 * @param array  $user_info User info from provider.
	 * @param string $provider Provider name.
	 * @return \WP_User|\WP_Error
	 */
	private function create_or_login_user( array $user_info, string $provider = 'google' ) {
		$email = $user_info['email'] ?? '';

		if ( empty( $email ) ) {
			return new \WP_Error( 'no_email', __( 'No email provided.', 'smart-loginizer' ) );
		}

		// Check if user exists.
		$user = get_user_by( 'email', $email );

		if ( $user ) {
			// Update provider if different.
			update_user_meta( $user->ID, 'smart_loginizer_oauth_provider', $provider );
			return $user;
		}

		// Check banned email domains for new registrations.
		$global_settings = get_option( 'smart_loginizer_settings', array() );
		$enable_banned_domains = 'yes' === ( $global_settings['enable_banned_domains'] ?? 'no' );
		$banned_email_domains = $global_settings['banned_email_domains'] ?? '';

		if ( $enable_banned_domains && ! empty( $banned_email_domains ) ) {
			$security = new \SmartLoginizer\Security\Security();
			if ( $security->is_email_domain_banned( $email, $banned_email_domains ) ) {
				return new \WP_Error( 'banned_domain', __( 'Registration from this email domain is not allowed.', 'smart-loginizer' ) );
			}
		}

		// Create new user.
		$username = $user_info['email'] ?? '';
		$name     = $user_info['name'] ?? '';
		$parts    = explode( '@', $username );
		$username = $parts[0];

		// Ensure username is unique.
		$original_username = $username;
		$counter           = 1;
		while ( username_exists( $username ) ) {
			$username = $original_username . $counter;
			++$counter;
		}

		// Generate random password.
		$password = wp_generate_password( 20, false );

		$user_id = wp_create_user( $username, $password, $email );

		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		// Update user meta.
		if ( ! empty( $name ) ) {
			wp_update_user(
				array(
					'ID'           => $user_id,
					'display_name' => $name,
					'first_name'   => $user_info['given_name'] ?? '',
					'last_name'    => $user_info['family_name'] ?? '',
				)
			);
		}

		// Store OAuth provider.
		update_user_meta( $user_id, 'smart_loginizer_oauth_provider', $provider );

		return get_user_by( 'id', $user_id );
	}
}
