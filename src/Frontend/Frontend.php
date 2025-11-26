<?php
/**
 * Frontend functionality.
 *
 * @package SmartLoginizer\Frontend
 */

namespace SmartLoginizer\Frontend;

use SmartLoginizer\Helpers\Helpers;
use SmartLoginizer\Security\Security;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend class.
 */
class Frontend {

	/**
	 * Helpers instance.
	 *
	 * @var Helpers
	 */
	private Helpers $helpers;

	/**
	 * Security instance.
	 *
	 * @var Security
	 */
	private Security $security;

	/**
	 * OAuth instance.
	 *
	 * @var OAuth|null
	 */
	public ?OAuth $oauth = null;

	/**
	 * Initialize frontend.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->helpers = new Helpers();
		$this->security = new Security();
		
		// Load OAuth - check if pro version has it first, otherwise use free version.
		if ( class_exists( 'SmartLoginizerPro\Frontend\OAuth' ) && function_exists( 'smart_loginizer_pro' ) && isset( smart_loginizer_pro()->core->oauth ) ) {
			// Use pro version OAuth if available.
			$this->oauth = smart_loginizer_pro()->core->oauth;
		} else {
			// Use free version OAuth.
			$this->oauth = new OAuth();
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_ajax_smart_loginizer_login', array( $this, 'handle_ajax_login' ) );
		add_action( 'wp_ajax_nopriv_smart_loginizer_login', array( $this, 'handle_ajax_login' ) );
		add_action( 'wp_ajax_smart_loginizer_register', array( $this, 'handle_ajax_register' ) );
		add_action( 'wp_ajax_nopriv_smart_loginizer_register', array( $this, 'handle_ajax_register' ) );
		add_action( 'wp_ajax_smart_loginizer_lost_password', array( $this, 'handle_ajax_lost_password' ) );
		add_action( 'wp_ajax_nopriv_smart_loginizer_lost_password', array( $this, 'handle_ajax_lost_password' ) );
		add_action( 'template_redirect', array( $this, 'handle_redirects' ) );
		add_action( 'login_init', array( $this, 'redirect_wp_login' ) );

		// Handle login template display for page restrictions.
		add_action( 'template_redirect', array( $this, 'handle_login_template_display' ), 5 );

		// WooCommerce login replacement (Pro feature).
		if ( class_exists( 'WooCommerce' ) && \SmartLoginizer\Helpers\Pro_Helper::is_pro_feature_available( 'woocommerce' ) ) {
			// Remove default WooCommerce account content and replace with custom content.
			add_action( 'template_redirect', array( $this, 'init_woocommerce_replacement' ), 5 );
			// Filter the template to replace login form.
			add_filter( 'wc_get_template', array( $this, 'replace_woocommerce_login_template' ), 10, 5 );
		}
	}

	/**
	 * Enqueue frontend assets.
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		// Enqueue reCAPTCHA script if enabled globally.
		$global_settings = get_option( 'smart_loginizer_settings', array() );
		$enable_recaptcha = 'yes' === ( $global_settings['enable_recaptcha'] ?? 'no' );
		$recaptcha_version = $global_settings['recaptcha_version'] ?? 'v3';
		$recaptcha_site_key = $this->helpers->get_option( 'recaptcha_site_key' );

		if ( $enable_recaptcha && ! empty( $recaptcha_site_key ) ) {
			// Load appropriate reCAPTCHA script based on version.
			// Note: Google reCAPTCHA scripts are external and don't have version numbers we control.
			// Using plugin version for cache busting when settings change.
			if ( 'v3' === $recaptcha_version ) {
				wp_enqueue_script(
					'google-recaptcha-v3',
					'https://www.google.com/recaptcha/api.js?render=' . esc_attr( $recaptcha_site_key ),
					array(),
					SMART_LOGINIZER_VERSION,
					true
				);
			} elseif ( 'v2_checkbox' === $recaptcha_version ) {
				wp_enqueue_script(
					'google-recaptcha-v2',
					'https://www.google.com/recaptcha/api.js',
					array(),
					SMART_LOGINIZER_VERSION,
					true
				);
			} elseif ( 'v2_invisible' === $recaptcha_version ) {
				wp_enqueue_script(
					'google-recaptcha-v2-invisible',
					'https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit',
					array(),
					SMART_LOGINIZER_VERSION,
					true
				);
			}
		}

		// Enqueue password strength meter if needed.
		if ( ! wp_script_is( 'zxcvbn-async', 'enqueued' ) ) {
			wp_enqueue_script( 'zxcvbn-async' );
		}

		wp_enqueue_script(
			'smart-loginizer-frontend',
			SMART_LOGINIZER_URL . 'assets/js/frontend.js',
			array( 'jquery' ),
			SMART_LOGINIZER_VERSION,
			true
		);

		wp_localize_script(
			'smart-loginizer-frontend',
			'smartLoginizer',
			array(
				'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
				'nonce'            => wp_create_nonce( 'smart_loginizer_nonce' ),
				'recaptchaSiteKey' => $enable_recaptcha ? $recaptcha_site_key : '',
				'recaptchaVersion' => $recaptcha_version,
				'enableRecaptcha'  => $enable_recaptcha,
			)
		);

		wp_enqueue_style(
			'smart-loginizer-frontend',
			SMART_LOGINIZER_URL . 'assets/css/frontend.css',
			array(),
			SMART_LOGINIZER_VERSION
		);
	}

	/**
	 * Handle AJAX login.
	 *
	 * @return void
	 */
	public function handle_ajax_login(): void {
		check_ajax_referer( 'smart_loginizer_nonce', 'nonce' );

		$username = isset( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '';
		// Password should not be sanitized as it may contain special characters. Only unslash.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Password should not be sanitized to preserve special characters.
		$password = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : '';
		$remember = isset( $_POST['remember'] ) && ( '1' === $_POST['remember'] || 'true' === $_POST['remember'] );
		$redirect = isset( $_POST['redirect'] ) ? esc_url_raw( wp_unslash( $_POST['redirect'] ) ) : '';

		// Get global security settings.
		$global_settings = get_option( 'smart_loginizer_settings', array() );
		$enable_password_limit = 'yes' === ( $global_settings['enable_password_limit'] ?? 'no' );
		$wrong_password_limit = isset( $global_settings['wrong_password_limit'] ) ? absint( $global_settings['wrong_password_limit'] ) : 5;
		$password_lockout_duration = isset( $global_settings['password_lockout_duration'] ) ? absint( $global_settings['password_lockout_duration'] ) : 15;
		
		// Location restriction is a Pro feature.
		$enable_location_restriction = false;
		if ( \SmartLoginizer\Helpers\Pro_Helper::is_pro_feature_available( 'location_restriction' ) ) {
			$enable_location_restriction = 'yes' === ( $global_settings['enable_location_restriction'] ?? 'no' );
		}
		$location_restriction_type = $global_settings['location_restriction_type'] ?? 'blocked';
		$location_countries = $global_settings['location_countries'] ?? '';

		// Get client IP.
		$client_ip = $this->security->get_client_ip();

		// Check location restriction (Pro feature).
		if ( $enable_location_restriction && \SmartLoginizer\Helpers\Pro_Helper::is_pro_feature_available( 'location_restriction' ) ) {
			$location_check = $this->security->check_location_restriction( $client_ip, $location_restriction_type, $location_countries );
			if ( $location_check['blocked'] ) {
				wp_send_json_error( array( 'message' => wp_kses_post( $location_check['message'] ) ) );
			}
		}

		// Check password lockout.
		if ( $enable_password_limit ) {
			$lockout_check = $this->security->check_password_lockout( $client_ip, $wrong_password_limit, $password_lockout_duration );
			if ( $lockout_check['locked'] ) {
				wp_send_json_error( array( 'message' => wp_kses_post( $lockout_check['message'] ) ) );
			}
		}

		// Verify reCAPTCHA if enabled globally.
		$global_settings = get_option( 'smart_loginizer_settings', array() );
		$enable_recaptcha = 'yes' === ( $global_settings['enable_recaptcha'] ?? 'no' );
		if ( $enable_recaptcha ) {
			$recaptcha_token = isset( $_POST['recaptcha_token'] ) ? sanitize_text_field( wp_unslash( $_POST['recaptcha_token'] ) ) : '';
			if ( empty( $recaptcha_token ) || ! $this->verify_recaptcha( $recaptcha_token ) ) {
				wp_send_json_error( array( 'message' => __( 'reCAPTCHA verification failed.', 'smart-loginizer' ) ) );
			}
		}

		// Attempt login.
		$user = wp_authenticate( $username, $password );

		if ( is_wp_error( $user ) ) {
			// Record failed login attempt.
			if ( $enable_password_limit ) {
				$this->security->record_failed_login( $client_ip, $wrong_password_limit, $password_lockout_duration );
			}
			wp_send_json_error( array( 'message' => wp_kses_post( $user->get_error_message() ) ) );
		}

		// Clear failed login attempts on successful login.
		if ( $enable_password_limit ) {
			$this->security->clear_failed_login( $client_ip );
		}

		wp_set_current_user( $user->ID );
		wp_set_auth_cookie( $user->ID, $remember );

		if ( empty( $redirect ) ) {
			$redirect = $this->helpers->get_login_redirect_url();
		}

		wp_send_json_success(
			array(
				'message'  => __( 'Login successful!', 'smart-loginizer' ),
				'redirect' => $redirect,
			)
		);
	}

	/**
	 * Handle AJAX registration.
	 *
	 * @return void
	 */
	public function handle_ajax_register(): void {
		check_ajax_referer( 'smart_loginizer_nonce', 'nonce' );

		$username         = isset( $_POST['username'] ) ? sanitize_user( wp_unslash( $_POST['username'] ) ) : '';
		$email            = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		// Password should not be sanitized as it may contain special characters. Only unslash.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Password should not be sanitized to preserve special characters.
		$password         = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : '';
		// Confirm password should not be sanitized as it may contain special characters. Only unslash.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Password should not be sanitized to preserve special characters.
		$confirm_password = isset( $_POST['confirm_password'] ) ? wp_unslash( $_POST['confirm_password'] ) : '';
		$redirect         = isset( $_POST['redirect'] ) ? esc_url_raw( wp_unslash( $_POST['redirect'] ) ) : '';
		$auto_login       = isset( $_POST['auto_login'] ) && 'true' === $_POST['auto_login'];

		// Get global security settings.
		$global_settings = get_option( 'smart_loginizer_settings', array() );
		
		// Registration limit is a Pro feature.
		$enable_registration_limit = false;
		if ( \SmartLoginizer\Helpers\Pro_Helper::is_pro_feature_available( 'registration_limit' ) ) {
			$enable_registration_limit = 'yes' === ( $global_settings['enable_registration_limit'] ?? 'no' );
		}
		$max_registrations_per_ip = isset( $global_settings['max_registrations_per_ip'] ) ? absint( $global_settings['max_registrations_per_ip'] ) : 3;
		$registration_limit_period = isset( $global_settings['registration_limit_period'] ) ? absint( $global_settings['registration_limit_period'] ) : 24;
		
		// Banned domains is a Pro feature.
		$enable_banned_domains = false;
		if ( \SmartLoginizer\Helpers\Pro_Helper::is_pro_feature_available( 'banned_domains' ) ) {
			$enable_banned_domains = 'yes' === ( $global_settings['enable_banned_domains'] ?? 'no' );
		}
		$banned_email_domains = $global_settings['banned_email_domains'] ?? '';
		
		// Location restriction is a Pro feature.
		$enable_location_restriction = false;
		if ( \SmartLoginizer\Helpers\Pro_Helper::is_pro_feature_available( 'location_restriction' ) ) {
			$enable_location_restriction = 'yes' === ( $global_settings['enable_location_restriction'] ?? 'no' );
		}
		$location_restriction_type = $global_settings['location_restriction_type'] ?? 'blocked';
		$location_countries = $global_settings['location_countries'] ?? '';

		// Validate inputs.
		if ( empty( $username ) || empty( $email ) || empty( $password ) ) {
			wp_send_json_error( array( 'message' => __( 'All fields are required.', 'smart-loginizer' ) ) );
		}

		// Validate email format.
		if ( ! is_email( $email ) ) {
			wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'smart-loginizer' ) ) );
		}

		// Only validate confirm password if it was provided.
		if ( ! empty( $confirm_password ) && $password !== $confirm_password ) {
			wp_send_json_error( array( 'message' => __( 'Passwords do not match.', 'smart-loginizer' ) ) );
		}

		// Get client IP.
		$client_ip = $this->security->get_client_ip();

		// Check location restriction.
		if ( $enable_location_restriction ) {
			$location_check = $this->security->check_location_restriction( $client_ip, $location_restriction_type, $location_countries );
			if ( $location_check['blocked'] ) {
				wp_send_json_error( array( 'message' => wp_kses_post( $location_check['message'] ) ) );
			}
		}

		// Check banned email domains (check before registration limit to fail fast).
		if ( $enable_banned_domains && ! empty( $banned_email_domains ) ) {
			if ( $this->security->is_email_domain_banned( $email, $banned_email_domains ) ) {
				wp_send_json_error( array( 'message' => __( 'Registration from this email domain is not allowed.', 'smart-loginizer' ) ) );
			}
		}

		// Check registration limit.
		if ( $enable_registration_limit ) {
			$limit_check = $this->security->check_registration_limit( $client_ip, $max_registrations_per_ip, $registration_limit_period );
			if ( $limit_check['limited'] ) {
				wp_send_json_error( array( 'message' => wp_kses_post( $limit_check['message'] ) ) );
			}
		}

		// Verify reCAPTCHA if enabled globally.
		$global_settings = get_option( 'smart_loginizer_settings', array() );
		$enable_recaptcha = 'yes' === ( $global_settings['enable_recaptcha'] ?? 'no' );
		if ( $enable_recaptcha ) {
			$recaptcha_token = isset( $_POST['recaptcha_token'] ) ? sanitize_text_field( wp_unslash( $_POST['recaptcha_token'] ) ) : '';
			if ( empty( $recaptcha_token ) || ! $this->verify_recaptcha( $recaptcha_token ) ) {
				wp_send_json_error( array( 'message' => __( 'reCAPTCHA verification failed.', 'smart-loginizer' ) ) );
			}
		}

		// Register user.
		$user_id = wp_create_user( $username, $password, $email );

		if ( is_wp_error( $user_id ) ) {
			wp_send_json_error( array( 'message' => wp_kses_post( $user_id->get_error_message() ) ) );
		}

		// Record registration.
		if ( $enable_registration_limit ) {
			$this->security->record_registration( $client_ip, $registration_limit_period );
		}

		// Auto-login if enabled.
		if ( $auto_login ) {
			$user = get_user_by( 'id', $user_id );
			wp_set_current_user( $user_id );
			wp_set_auth_cookie( $user_id );
		}

		if ( empty( $redirect ) ) {
			$redirect = $this->helpers->get_registration_redirect_url();
		}

		wp_send_json_success(
			array(
				'message'  => __( 'Registration successful!', 'smart-loginizer' ),
				'redirect' => $redirect,
			)
		);
	}

	/**
	 * Handle AJAX lost password.
	 *
	 * @return void
	 */
	public function handle_ajax_lost_password(): void {
		check_ajax_referer( 'smart_loginizer_nonce', 'nonce' );

		$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

		if ( empty( $email ) || ! is_email( $email ) ) {
			wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'smart-loginizer' ) ) );
		}

		// Get global security settings.
		$global_settings = get_option( 'smart_loginizer_settings', array() );
		
		// Location restriction is a Pro feature.
		$enable_location_restriction = false;
		if ( \SmartLoginizer\Helpers\Pro_Helper::is_pro_feature_available( 'location_restriction' ) ) {
			$enable_location_restriction = 'yes' === ( $global_settings['enable_location_restriction'] ?? 'no' );
		}
		$location_restriction_type = $global_settings['location_restriction_type'] ?? 'blocked';
		$location_countries = $global_settings['location_countries'] ?? '';

		// Get client IP.
		$client_ip = $this->security->get_client_ip();

		// Check location restriction (Pro feature).
		if ( $enable_location_restriction && \SmartLoginizer\Helpers\Pro_Helper::is_pro_feature_available( 'location_restriction' ) ) {
			$location_check = $this->security->check_location_restriction( $client_ip, $location_restriction_type, $location_countries );
			if ( $location_check['blocked'] ) {
				wp_send_json_error( array( 'message' => wp_kses_post( $location_check['message'] ) ) );
			}
		}

		// Verify reCAPTCHA if enabled globally.
		$global_settings = get_option( 'smart_loginizer_settings', array() );
		$enable_recaptcha = 'yes' === ( $global_settings['enable_recaptcha'] ?? 'no' );
		if ( $enable_recaptcha ) {
			$recaptcha_token = isset( $_POST['recaptcha_token'] ) ? sanitize_text_field( wp_unslash( $_POST['recaptcha_token'] ) ) : '';
			if ( empty( $recaptcha_token ) || ! $this->verify_recaptcha( $recaptcha_token ) ) {
				wp_send_json_error( array( 'message' => __( 'reCAPTCHA verification failed.', 'smart-loginizer' ) ) );
			}
		}

		// Use WordPress lost password function.
		$result = retrieve_password( $email );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => wp_kses_post( $result->get_error_message() ) ) );
		}

		wp_send_json_success(
			array(
				'message' => __( 'Password reset link has been sent to your email.', 'smart-loginizer' ),
			)
		);
	}

	/**
	 * Verify reCAPTCHA token.
	 *
	 * @param string $token reCAPTCHA token.
	 * @return bool
	 */
	private function verify_recaptcha( string $token ): bool {
		$secret_key = $this->helpers->get_option( 'recaptcha_secret_key' );

		if ( empty( $secret_key ) ) {
			return true; // Skip verification if not configured.
		}

		// Get client IP safely.
		$client_ip = $this->security->get_client_ip();
		
		$response = wp_remote_post(
			'https://www.google.com/recaptcha/api/siteverify',
			array(
				'body' => array(
					'secret'   => $secret_key,
					'response' => $token,
					'remoteip' => $client_ip,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		return isset( $body['success'] ) && true === $body['success'];
	}

	/**
	 * Handle redirects.
	 *
	 * @return void
	 */
	public function handle_redirects(): void {
		// Handle logout redirect.
		if ( isset( $_GET['action'] ) && 'logout' === $_GET['action'] ) {
			check_admin_referer( 'log-out' );
			wp_logout();
			$redirect = $this->helpers->get_logout_redirect_url();
			wp_safe_redirect( $redirect );
			exit;
		}
	}

	/**
	 * Redirect wp-login.php to custom page.
	 *
	 * @return void
	 */
	public function redirect_wp_login(): void {
		$options = get_option( 'smart_loginizer_settings', array() );
		$enabled = isset( $options['enable_custom_login_page'] ) && 'yes' === $options['enable_custom_login_page'];
		$page_id = isset( $options['custom_login_page_id'] ) ? absint( $options['custom_login_page_id'] ) : 0;

		// If feature is not enabled or no page selected, do nothing.
		if ( ! $enabled || 0 === $page_id ) {
			return;
		}

		// Get the action parameter.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading action parameter for wp-login.php redirect, not processing form data.
		$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

		// Allow certain actions to work normally (logout, password reset, etc.).
		$allowed_actions = array( 'logout', 'rp', 'resetpass', 'postpass' );

		// If it's an allowed action, don't redirect.
		if ( ! empty( $action ) && in_array( $action, $allowed_actions, true ) ) {
			return;
		}

		// Get the custom login page URL.
		$custom_page_url = get_permalink( $page_id );

		if ( ! $custom_page_url ) {
			return;
		}

		// Preserve query parameters (like redirect_to).
		$query_string = isset( $_SERVER['QUERY_STRING'] ) ? sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ) : '';
		if ( ! empty( $query_string ) ) {
			parse_str( $query_string, $query_params );
			// Remove action if it's login (default action).
			if ( isset( $query_params['action'] ) && 'login' === $query_params['action'] ) {
				unset( $query_params['action'] );
			}
			// Only add query string if there are parameters left.
			if ( ! empty( $query_params ) ) {
				$custom_page_url = add_query_arg( $query_params, $custom_page_url );
			}
		}

		// Redirect to custom login page.
		wp_safe_redirect( $custom_page_url );
		exit;
	}

	/**
	 * Initialize WooCommerce login replacement.
	 *
	 * @return void
	 */
	public function init_woocommerce_replacement(): void {
		// Only on WooCommerce account page when user is not logged in.
		if ( ! function_exists( 'is_account_page' ) || ! is_account_page() || is_user_logged_in() ) {
			return;
		}

		$options = get_option( 'smart_loginizer_settings', array() );
		$action = isset( $options['woocommerce_logged_out_action'] ) ? $options['woocommerce_logged_out_action'] : 'default';

		// Handle redirect option.
		if ( 'redirect' === $action ) {
			$redirect_page_id = isset( $options['woocommerce_logged_out_redirect_page_id'] ) ? absint( $options['woocommerce_logged_out_redirect_page_id'] ) : 0;
			
			if ( 0 !== $redirect_page_id ) {
				$redirect_url = get_permalink( $redirect_page_id );
				if ( $redirect_url ) {
					wp_safe_redirect( $redirect_url );
					exit;
				}
			}
			return;
		}

		// Handle template replacement option.
		if ( 'template' === $action ) {
			$template_id = isset( $options['woocommerce_login_replacement_template_id'] ) ? absint( $options['woocommerce_login_replacement_template_id'] ) : 0;

			// If no template selected, do nothing.
			if ( 0 === $template_id ) {
				return;
			}

			// Also hook into account content as backup (in case template filter doesn't work).
			add_action( 'woocommerce_account_content', array( $this, 'output_elementor_template_backup' ), 1 );
			
			// Hide default form elements using CSS (template filter handles the replacement).
			add_action( 'wp_head', array( $this, 'hide_woocommerce_login_forms' ), 999 );
		}
	}

	/**
	 * Output Elementor template as backup method.
	 *
	 * @return void
	 */
	public function output_elementor_template_backup(): void {
		$options = get_option( 'smart_loginizer_settings', array() );
		$action = isset( $options['woocommerce_logged_out_action'] ) ? $options['woocommerce_logged_out_action'] : 'default';
		$template_id = isset( $options['woocommerce_login_replacement_template_id'] ) ? absint( $options['woocommerce_login_replacement_template_id'] ) : 0;

		// Only output if template action is selected.
		if ( 'template' !== $action || 0 === $template_id || ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}

		$template = get_post( $template_id );
		if ( ! $template || 'elementor_library' !== $template->post_type ) {
			return;
		}

		// Output Elementor template content as backup.
		echo '<div class="smart-loginizer-woocommerce-replacement-backup">';
		echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
	}

	/**
	 * Replace WooCommerce login template with our custom template.
	 *
	 * @param string $template Template path.
	 * @param string $template_name Template name.
	 * @param array  $args Template arguments.
	 * @param string $template_path Template path.
	 * @param string $default_path Default path.
	 * @return string
	 */
	public function replace_woocommerce_login_template( string $template, string $template_name, array $args, string $template_path, string $default_path ): string {
		// Only replace the login form template.
		if ( 'myaccount/form-login.php' !== $template_name ) {
			return $template;
		}

		// Only on account page when user is not logged in.
		if ( ! function_exists( 'is_account_page' ) || ! is_account_page() || is_user_logged_in() ) {
			return $template;
		}

		$options = get_option( 'smart_loginizer_settings', array() );
		$action = isset( $options['woocommerce_logged_out_action'] ) ? $options['woocommerce_logged_out_action'] : 'default';

		// Only replace template if action is 'template'.
		if ( 'template' !== $action ) {
			return $template;
		}

		$template_id = isset( $options['woocommerce_login_replacement_template_id'] ) ? absint( $options['woocommerce_login_replacement_template_id'] ) : 0;

		// If no template selected, use default.
		if ( 0 === $template_id ) {
			return $template;
		}

		// Check if Elementor is active and template exists.
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return $template;
		}

		$elementor_template = get_post( $template_id );
		if ( ! $elementor_template || 'elementor_library' !== $elementor_template->post_type ) {
			return $template;
		}

		// Return path to our custom template file.
		$custom_template = SMART_LOGINIZER_PATH . 'templates/woocommerce/myaccount/form-login.php';
		
		if ( file_exists( $custom_template ) ) {
			return $custom_template;
		}

		// Fallback to default if custom template doesn't exist.
		return $template;
	}


	/**
	 * Hide default WooCommerce login forms with CSS.
	 *
	 * @return void
	 */
	public function hide_woocommerce_login_forms(): void {
		// Only on account page when user is not logged in.
		if ( ! function_exists( 'is_account_page' ) || ! is_account_page() || is_user_logged_in() ) {
			return;
		}

		$options = get_option( 'smart_loginizer_settings', array() );
		$action = isset( $options['woocommerce_logged_out_action'] ) ? $options['woocommerce_logged_out_action'] : 'default';
		$template_id = isset( $options['woocommerce_login_replacement_template_id'] ) ? absint( $options['woocommerce_login_replacement_template_id'] ) : 0;

		// Only hide forms if template replacement is enabled.
		if ( 'template' !== $action || 0 === $template_id ) {
			return;
		}

		// Hide default WooCommerce login form elements.
		?>
		<style>
			.woocommerce-account .woocommerce-form-login,
			.woocommerce-account .woocommerce-form-register,
			.woocommerce-account form.login,
			.woocommerce-account form.woocommerce-form,
			.woocommerce-account form.woocommerce-form-login,
			.woocommerce-account .u-columns,
			.woocommerce-account .u-column1,
			.woocommerce-account .u-column2 {
				display: none !important;
			}
		</style>
		<?php
	}

	/**
	 * Handle login template display for page restrictions.
	 *
	 * @return void
	 */
	public function handle_login_template_display(): void {
		// Page restriction is a Pro feature.
		if ( ! \SmartLoginizer\Helpers\Pro_Helper::is_pro_feature_available( 'page_restriction' ) ) {
			return;
		}

		// Check if this is a login redirect from page restriction.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Checking query parameter only.
		if ( ! isset( $_GET['smart_loginizer_login'] ) || '1' !== $_GET['smart_loginizer_login'] ) {
			return;
		}

		// Get login template from page restriction settings.
		$options = get_option( 'smart_loginizer_settings', array() );
		$login_template_id = isset( $options['page_restriction_login_template_id'] ) ? absint( $options['page_restriction_login_template_id'] ) : 0;

		if ( $login_template_id > 0 && class_exists( '\Elementor\Plugin' ) ) {
			$template = get_post( $login_template_id );
			if ( $template && 'elementor_library' === $template->post_type ) {
				// Replace page content with template.
				add_filter( 'the_content', array( $this, 'replace_content_with_login_template' ), 999 );
				add_action( 'wp_head', array( $this, 'hide_page_elements' ) );
			}
		}
	}

	/**
	 * Replace content with login template.
	 *
	 * @param string $content Original content.
	 * @return string
	 */
	public function replace_content_with_login_template( string $content ): string {
		$options = get_option( 'smart_loginizer_settings', array() );
		$login_template_id = isset( $options['page_restriction_login_template_id'] ) ? absint( $options['page_restriction_login_template_id'] ) : 0;

		if ( $login_template_id > 0 && class_exists( '\Elementor\Plugin' ) ) {
			$template = get_post( $login_template_id );
			if ( $template && 'elementor_library' === $template->post_type ) {
				$template_content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $login_template_id );
				if ( $template_content ) {
					return $template_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor template output.
				}
			}
		}

		return $content;
	}

	/**
	 * Hide page elements when displaying login template.
	 *
	 * @return void
	 */
	public function hide_page_elements(): void {
		?>
		<style>
			.smart-loginizer-login-template-page .entry-header,
			.smart-loginizer-login-template-page .entry-content > *:not(.elementor) {
				display: none;
			}
			.smart-loginizer-login-template-page .entry-content .elementor {
				display: block;
			}
		</style>
		<?php
	}
}

