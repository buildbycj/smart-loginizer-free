<?php
/**
 * Admin functionality.
 *
 * @package SmartLoginizer\Admin
 */

namespace SmartLoginizer\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin class.
 */
class Admin {

	/**
	 * Initialize admin.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'load-toplevel_page_smart-loginizer', array( $this, 'prevent_default_notices' ), 1 );
	}

	/**
	 * Get admin menu icon.
	 * Returns a data URI with base64-encoded SVG for WordPress.org compatibility.
	 *
	 * For WordPress.org plugins, you can use:
	 * 1. Dashicons (e.g., 'dashicons-admin-users') - Recommended, built into WordPress
	 * 2. Data URI with base64-encoded SVG - Custom icon, WordPress.org compatible
	 * 3. URL to image file - Requires hosting, less ideal
	 *
	 * @return string Icon data URI or dashicon class.
	 */
	private function get_admin_menu_icon(): string {
		// Option 1: Use a dashicon (recommended for WordPress.org).
		// return 'dashicons-admin-users';

		// Option 2: Custom SVG icon as data URI (WordPress.org compatible).
		// This creates a lock/user icon suitable for a login/authentication plugin.
		$svg_icon = '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
			<path fill="#a7aaad" d="M10 2C5.58 2 2 5.58 2 10s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 2c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6 2.69-6 6-6z"/>
			<path fill="#a7aaad" d="M10 5c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3z"/>
			<path fill="#a7aaad" d="M6 12.5c0-.83.67-1.5 1.5-1.5h5c.83 0 1.5.67 1.5 1.5v1.5H6v-1.5z"/>
		</svg>';

		// Encode SVG to base64 data URI.
		$encoded_svg = 'data:image/svg+xml;base64,' . base64_encode( $svg_icon ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Required for data URI.

		return $encoded_svg;
	}

	/**
	 * Add admin menu.
	 *
	 * @return void
	 */
	public function add_admin_menu(): void {
		add_menu_page(
			__( 'Smart Loginizer Settings', 'smart-loginizer' ),
			__( 'Smart Loginizer', 'smart-loginizer' ),
			'manage_options',
			'smart-loginizer',
			array( $this, 'render_settings_page' ),
			$this->get_admin_menu_icon(),
			30
		);
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register_settings(): void {
		register_setting(
			'smart_loginizer_settings',
			'smart_loginizer_settings',
			array( $this, 'sanitize_settings' )
		);

		// General Settings Section.
		add_settings_section(
			'smart_loginizer_general',
			__( 'General Settings', 'smart-loginizer' ),
			array( $this, 'render_section' ),
			'smart-loginizer'
		);

		// reCAPTCHA settings.
		add_settings_field(
			'enable_recaptcha',
			__( 'Enable reCAPTCHA', 'smart-loginizer' ),
			array( $this, 'render_checkbox_field' ),
			'smart-loginizer',
			'smart_loginizer_general',
			array(
				'label_for' => 'enable_recaptcha',
			)
		);

		add_settings_field(
			'recaptcha_version',
			__( 'reCAPTCHA Version', 'smart-loginizer' ),
			array( $this, 'render_recaptcha_version_field' ),
			'smart-loginizer',
			'smart_loginizer_general'
		);

		add_settings_field(
			'recaptcha_site_key',
			__( 'reCAPTCHA Site Key', 'smart-loginizer' ),
			array( $this, 'render_field' ),
			'smart-loginizer',
			'smart_loginizer_general',
			array(
				'label_for' => 'recaptcha_site_key',
				'type'      => 'text',
				'description' => sprintf(
					/* translators: %s: Link to create reCAPTCHA */
					__( 'Get your reCAPTCHA keys from <a href="%s" target="_blank">Google reCAPTCHA Admin</a>.', 'smart-loginizer' ),
					'https://www.google.com/recaptcha/admin/create'
				),
			)
		);

		add_settings_field(
			'recaptcha_secret_key',
			__( 'reCAPTCHA Secret Key', 'smart-loginizer' ),
			array( $this, 'render_field' ),
			'smart-loginizer',
			'smart_loginizer_general',
			array(
				'label_for' => 'recaptcha_secret_key',
				'type'      => 'password',
			)
		);

		// Custom Login Page settings (Free feature).
		add_settings_field(
			'enable_custom_login_page',
			__( 'Enable Custom Login Page', 'smart-loginizer' ),
			array( $this, 'render_custom_login_page_field' ),
			'smart-loginizer',
			'smart_loginizer_general'
		);

		// Widget Settings Section.
		add_settings_section(
			'smart_loginizer_widgets',
			__( 'Widget Settings', 'smart-loginizer' ),
			array( $this, 'render_widgets_section' ),
			'smart-loginizer'
		);

		// WooCommerce Settings Section (Pro feature - always show).
		add_settings_section(
			'smart_loginizer_woocommerce',
			__( 'WooCommerce Settings', 'smart-loginizer' ),
			array( $this, 'render_section' ),
			'smart-loginizer'
		);

		$is_pro = \SmartLoginizer\Helpers\Pro_Helper::is_pro_active();
		
		add_settings_field(
			'woocommerce_logged_out_action',
			__( 'Action for Logged-Out Users', 'smart-loginizer' ),
			array( $this, 'render_woocommerce_logged_out_action_field' ),
			'smart-loginizer',
			'smart_loginizer_woocommerce',
			array(
				'readonly'  => ! $is_pro,
			)
		);

		add_settings_field(
			'woocommerce_login_replacement_template',
			__( 'Elementor Template (for Template Replacement)', 'smart-loginizer' ),
			array( $this, 'render_woocommerce_login_replacement_field' ),
			'smart-loginizer',
			'smart_loginizer_woocommerce',
			array(
				'readonly'  => ! $is_pro,
			)
		);

		add_settings_field(
			'woocommerce_logged_out_redirect_page',
			__( 'Redirect Page', 'smart-loginizer' ),
			array( $this, 'render_woocommerce_redirect_page_field' ),
			'smart-loginizer',
			'smart_loginizer_woocommerce',
			array(
				'readonly'  => ! $is_pro,
			)
		);

		// IP Geolocation Section (Pro feature - always show).
		add_settings_section(
			'smart_loginizer_geolocation',
			__( 'IP Geolocation Settings', 'smart-loginizer' ),
			array( $this, 'render_section' ),
			'smart-loginizer'
		);

		add_settings_field(
			'ip_geolocation_service',
			__( 'IP Geolocation Service', 'smart-loginizer' ),
			array( $this, 'render_ip_geolocation_field' ),
			'smart-loginizer',
			'smart_loginizer_geolocation',
			array(
				'readonly'  => ! \SmartLoginizer\Helpers\Pro_Helper::is_pro_active(),
			)
		);

		add_settings_field(
			'ip_geolocation_api_key',
			__( 'IP Geolocation API Key (Optional)', 'smart-loginizer' ),
			array( $this, 'render_field' ),
			'smart-loginizer',
			'smart_loginizer_geolocation',
			array(
				'label_for' => 'ip_geolocation_api_key',
				'type'      => 'text',
				'description' => __( 'Optional: Only required for premium services. Leave empty to use free ip-api.com service.', 'smart-loginizer' ),
				'readonly'  => ! \SmartLoginizer\Helpers\Pro_Helper::is_pro_active(),
			)
		);

		// Page Restriction Section (Pro feature - always show).
		add_settings_section(
			'smart_loginizer_page_restriction',
			__( 'Page Restriction Settings', 'smart-loginizer' ),
			array( $this, 'render_page_restriction_section' ),
			'smart-loginizer'
		);

		add_settings_field(
			'page_restriction_login_template_id',
			__( 'Login Page Template', 'smart-loginizer' ),
			array( $this, 'render_page_restriction_login_template_field' ),
			'smart-loginizer',
			'smart_loginizer_page_restriction',
			array(
				'readonly'  => ! \SmartLoginizer\Helpers\Pro_Helper::is_pro_active(),
			)
		);

		// Elementor Widget Restriction Section (Pro feature - always show).
		add_settings_section(
			'smart_loginizer_elementor_widget_restriction',
			__( 'Elementor Widget Restriction Settings', 'smart-loginizer' ),
			array( $this, 'render_elementor_widget_restriction_section' ),
			'smart-loginizer'
		);

		add_settings_field(
			'elementor_widget_restriction_login_template_id',
			__( 'Login Template for Widget Restrictions', 'smart-loginizer' ),
			array( $this, 'render_elementor_widget_restriction_login_template_field' ),
			'smart-loginizer',
			'smart_loginizer_elementor_widget_restriction',
			array(
				'readonly'  => ! \SmartLoginizer\Helpers\Pro_Helper::is_pro_active(),
			)
		);

		// Social Login Section - Google (Free feature).
		add_settings_section(
			'smart_loginizer_google',
			__( 'Google OAuth Settings', 'smart-loginizer' ),
			array( $this, 'render_section' ),
			'smart-loginizer'
		);

		// Social Login fields (Free feature).
		add_settings_field(
			'google_client_id',
			__( 'Google Client ID', 'smart-loginizer' ),
			array( $this, 'render_field' ),
			'smart-loginizer',
			'smart_loginizer_google',
			array(
				'label_for' => 'google_client_id',
				'type'      => 'text',
			)
		);

		add_settings_field(
			'google_client_secret',
			__( 'Google Client Secret', 'smart-loginizer' ),
			array( $this, 'render_field' ),
			'smart-loginizer',
			'smart_loginizer_google',
			array(
				'label_for' => 'google_client_secret',
				'type'      => 'password',
			)
		);

		// Social Login Section - X (Twitter).
		add_settings_section(
			'smart_loginizer_x',
			__( 'X (Twitter) OAuth Settings', 'smart-loginizer' ),
			array( $this, 'render_section' ),
			'smart-loginizer'
		);

		add_settings_field(
			'x_client_id',
			__( 'X Client ID', 'smart-loginizer' ),
			array( $this, 'render_field' ),
			'smart-loginizer',
			'smart_loginizer_x',
			array(
				'label_for' => 'x_client_id',
				'type'      => 'text',
			)
		);

		add_settings_field(
			'x_client_secret',
			__( 'X Client Secret', 'smart-loginizer' ),
			array( $this, 'render_field' ),
			'smart-loginizer',
			'smart_loginizer_x',
			array(
				'label_for' => 'x_client_secret',
				'type'      => 'password',
			)
		);

		// Social Login Section - LinkedIn.
		add_settings_section(
			'smart_loginizer_linkedin',
			__( 'LinkedIn OAuth Settings', 'smart-loginizer' ),
			array( $this, 'render_section' ),
			'smart-loginizer'
		);

		add_settings_field(
			'linkedin_client_id',
			__( 'LinkedIn Client ID', 'smart-loginizer' ),
			array( $this, 'render_field' ),
			'smart-loginizer',
			'smart_loginizer_linkedin',
			array(
				'label_for' => 'linkedin_client_id',
				'type'      => 'text',
			)
		);

		add_settings_field(
			'linkedin_client_secret',
			__( 'LinkedIn Client Secret', 'smart-loginizer' ),
			array( $this, 'render_field' ),
			'smart-loginizer',
			'smart_loginizer_linkedin',
			array(
				'label_for' => 'linkedin_client_secret',
				'type'      => 'password',
			)
		);

		// Social Login Section - Facebook.
		add_settings_section(
			'smart_loginizer_facebook',
			__( 'Facebook OAuth Settings', 'smart-loginizer' ),
			array( $this, 'render_section' ),
			'smart-loginizer'
		);

		add_settings_field(
			'facebook_app_id',
			__( 'Facebook App ID', 'smart-loginizer' ),
			array( $this, 'render_field' ),
			'smart-loginizer',
			'smart_loginizer_facebook',
			array(
				'label_for' => 'facebook_app_id',
				'type'      => 'text',
			)
		);

		add_settings_field(
			'facebook_app_secret',
			__( 'Facebook App Secret', 'smart-loginizer' ),
			array( $this, 'render_field' ),
			'smart-loginizer',
			'smart_loginizer_facebook',
			array(
				'label_for' => 'facebook_app_secret',
				'type'      => 'password',
			)
		);

		// Security Settings Section - Wrong Password Limit.
		add_settings_section(
			'smart_loginizer_security_password_limit',
			__( 'Wrong Password Limit', 'smart-loginizer' ),
			array( $this, 'render_section' ),
			'smart-loginizer'
		);

		add_settings_field(
			'enable_password_limit',
			__( 'Enable Wrong Password Limit', 'smart-loginizer' ),
			array( $this, 'render_checkbox_field' ),
			'smart-loginizer',
			'smart_loginizer_security_password_limit',
			array(
				'label_for' => 'enable_password_limit',
			)
		);

		add_settings_field(
			'wrong_password_limit',
			__( 'Max Wrong Password Attempts', 'smart-loginizer' ),
			array( $this, 'render_field' ),
			'smart-loginizer',
			'smart_loginizer_security_password_limit',
			array(
				'label_for' => 'wrong_password_limit',
				'type'      => 'number',
				'min'       => 1,
				'max'       => 20,
				'default'   => 5,
			)
		);

		add_settings_field(
			'password_lockout_duration',
			__( 'Lockout Duration (minutes)', 'smart-loginizer' ),
			array( $this, 'render_field' ),
			'smart-loginizer',
			'smart_loginizer_security_password_limit',
			array(
				'label_for' => 'password_lockout_duration',
				'type'      => 'number',
				'min'       => 1,
				'max'       => 1440,
				'default'   => 15,
			)
		);

		// Security Settings Section - Location-Based Restriction (Pro feature - always show).
		add_settings_section(
			'smart_loginizer_security_location',
			__( 'Location-Based Restriction', 'smart-loginizer' ),
			array( $this, 'render_section' ),
			'smart-loginizer'
		);

		$is_pro = \SmartLoginizer\Helpers\Pro_Helper::is_pro_active();
		
		add_settings_field(
			'enable_location_restriction',
			__( 'Enable Location-Based Restriction', 'smart-loginizer' ),
			array( $this, 'render_checkbox_field' ),
			'smart-loginizer',
			'smart_loginizer_security_location',
			array(
				'label_for' => 'enable_location_restriction',
				'readonly'  => ! $is_pro,
			)
		);

		add_settings_field(
			'location_restriction_type',
			__( 'Restriction Type', 'smart-loginizer' ),
			array( $this, 'render_location_restriction_type_field' ),
			'smart-loginizer',
			'smart_loginizer_security_location',
			array(
				'readonly'  => ! $is_pro,
			)
		);

		add_settings_field(
			'location_countries',
			__( 'Countries (ISO codes, comma-separated)', 'smart-loginizer' ),
			array( $this, 'render_field' ),
			'smart-loginizer',
			'smart_loginizer_security_location',
			array(
				'label_for' => 'location_countries',
				'type'      => 'textarea',
				'description' => __( 'Enter country ISO codes (e.g., US,GB,IN) separated by commas. Leave empty to disable.', 'smart-loginizer' ),
				'readonly'  => ! $is_pro,
			)
		);

		// Security Settings Section - Registration Limit (Pro feature - always show).
		add_settings_section(
			'smart_loginizer_security_registration',
			__( 'Registration Limit', 'smart-loginizer' ),
			array( $this, 'render_section' ),
			'smart-loginizer'
		);

		add_settings_field(
			'enable_registration_limit',
			__( 'Enable Registration Limit', 'smart-loginizer' ),
			array( $this, 'render_checkbox_field' ),
			'smart-loginizer',
			'smart_loginizer_security_registration',
			array(
				'label_for' => 'enable_registration_limit',
				'readonly'  => ! $is_pro,
			)
		);

		add_settings_field(
			'max_registrations_per_ip',
			__( 'Max Registrations Per IP', 'smart-loginizer' ),
			array( $this, 'render_field' ),
			'smart-loginizer',
			'smart_loginizer_security_registration',
			array(
				'label_for' => 'max_registrations_per_ip',
				'type'      => 'number',
				'min'       => 1,
				'max'       => 100,
				'default'   => 3,
				'readonly'  => ! $is_pro,
			)
		);

		add_settings_field(
			'registration_limit_period',
			__( 'Registration Limit Period (hours)', 'smart-loginizer' ),
			array( $this, 'render_field' ),
			'smart-loginizer',
			'smart_loginizer_security_registration',
			array(
				'label_for' => 'registration_limit_period',
				'type'      => 'number',
				'min'       => 1,
				'max'       => 168,
				'default'   => 24,
				'readonly'  => ! $is_pro,
			)
		);

		// Security Settings Section - Banned Email Domains (Pro feature - always show).
		add_settings_section(
			'smart_loginizer_security_banned_domains',
			__( 'Banned Email Domains', 'smart-loginizer' ),
			array( $this, 'render_section' ),
			'smart-loginizer'
		);

		add_settings_field(
			'enable_banned_domains',
			__( 'Enable Banned Email Domains', 'smart-loginizer' ),
			array( $this, 'render_checkbox_field' ),
			'smart-loginizer',
			'smart_loginizer_security_banned_domains',
			array(
				'label_for' => 'enable_banned_domains',
				'readonly'  => ! $is_pro,
			)
		);

		add_settings_field(
			'banned_email_domains',
			__( 'Banned Email Domains (comma-separated)', 'smart-loginizer' ),
			array( $this, 'render_field' ),
			'smart-loginizer',
			'smart_loginizer_security_banned_domains',
			array(
				'label_for' => 'banned_email_domains',
				'type'      => 'textarea',
				'description' => __( 'Enter email domains to ban (e.g., example.com, spam.com). One domain per line or comma-separated.', 'smart-loginizer' ),
				'readonly'  => ! $is_pro,
			)
		);
	}

	/**
	 * Sanitize settings.
	 *
	 * @param array $input Input data.
	 * @return array
	 */
	public function sanitize_settings( array $input ): array {
		// Get existing settings to preserve values not in input.
		$existing = get_option( 'smart_loginizer_settings', array() );
		$sanitized = $existing; // Start with existing settings.
		$is_pro = \SmartLoginizer\Helpers\Pro_Helper::is_pro_active();

		if ( isset( $input['enable_recaptcha'] ) ) {
			$sanitized['enable_recaptcha'] = 'yes' === $input['enable_recaptcha'] ? 'yes' : 'no';
		}

		if ( isset( $input['recaptcha_version'] ) ) {
			// All reCAPTCHA versions are available in free version.
			$allowed_versions = array( 'v3', 'v2_checkbox', 'v2_invisible' );
			$sanitized['recaptcha_version'] = in_array( $input['recaptcha_version'], $allowed_versions, true ) ? $input['recaptcha_version'] : 'v3';
		}

		if ( isset( $input['recaptcha_site_key'] ) ) {
			$sanitized['recaptcha_site_key'] = sanitize_text_field( $input['recaptcha_site_key'] );
		}

		if ( isset( $input['recaptcha_secret_key'] ) ) {
			$sanitized['recaptcha_secret_key'] = sanitize_text_field( $input['recaptcha_secret_key'] );
		}

		// OAuth settings (Free feature).
		if ( isset( $input['google_client_id'] ) ) {
			$sanitized['google_client_id'] = sanitize_text_field( $input['google_client_id'] );
		}

		if ( isset( $input['google_client_secret'] ) ) {
			$sanitized['google_client_secret'] = sanitize_text_field( $input['google_client_secret'] );
		}

		// Legacy support for gmail_client_id.
		if ( isset( $input['gmail_client_id'] ) ) {
			$sanitized['google_client_id'] = sanitize_text_field( $input['gmail_client_id'] );
		}

		if ( isset( $input['gmail_client_secret'] ) ) {
			$sanitized['google_client_secret'] = sanitize_text_field( $input['gmail_client_secret'] );
		}

		if ( isset( $input['x_client_id'] ) ) {
			$sanitized['x_client_id'] = sanitize_text_field( $input['x_client_id'] );
		}

		if ( isset( $input['x_client_secret'] ) ) {
			$sanitized['x_client_secret'] = sanitize_text_field( $input['x_client_secret'] );
		}

		if ( isset( $input['linkedin_client_id'] ) ) {
			$sanitized['linkedin_client_id'] = sanitize_text_field( $input['linkedin_client_id'] );
		}

		if ( isset( $input['linkedin_client_secret'] ) ) {
			$sanitized['linkedin_client_secret'] = sanitize_text_field( $input['linkedin_client_secret'] );
		}

		if ( isset( $input['facebook_app_id'] ) ) {
			$sanitized['facebook_app_id'] = sanitize_text_field( $input['facebook_app_id'] );
		}

		if ( isset( $input['facebook_app_secret'] ) ) {
			$sanitized['facebook_app_secret'] = sanitize_text_field( $input['facebook_app_secret'] );
		}

		// IP Geolocation (Pro feature) - only save if pro is active.
		if ( $is_pro ) {
			if ( isset( $input['ip_geolocation_service'] ) ) {
				$sanitized['ip_geolocation_service'] = sanitize_text_field( $input['ip_geolocation_service'] );
			}

			if ( isset( $input['ip_geolocation_api_key'] ) ) {
				$sanitized['ip_geolocation_api_key'] = sanitize_text_field( $input['ip_geolocation_api_key'] );
			}
		}

		// Custom Login Page (Free feature).
		if ( isset( $input['enable_custom_login_page'] ) ) {
			$sanitized['enable_custom_login_page'] = sanitize_text_field( $input['enable_custom_login_page'] );
		}

		if ( isset( $input['custom_login_page_id'] ) ) {
			$sanitized['custom_login_page_id'] = absint( $input['custom_login_page_id'] );
		}

		// WooCommerce settings (Pro feature) - only save if pro is active.
		if ( $is_pro ) {
			if ( isset( $input['woocommerce_logged_out_action'] ) ) {
				$sanitized['woocommerce_logged_out_action'] = sanitize_text_field( $input['woocommerce_logged_out_action'] );
			}

			if ( isset( $input['woocommerce_login_replacement_template_id'] ) ) {
				$sanitized['woocommerce_login_replacement_template_id'] = absint( $input['woocommerce_login_replacement_template_id'] );
			}

			if ( isset( $input['woocommerce_logged_out_redirect_page_id'] ) ) {
				$sanitized['woocommerce_logged_out_redirect_page_id'] = absint( $input['woocommerce_logged_out_redirect_page_id'] );
			}
		}

		// Security settings - Wrong Password Limit.
		if ( isset( $input['enable_password_limit'] ) ) {
			$sanitized['enable_password_limit'] = 'yes' === $input['enable_password_limit'] ? 'yes' : 'no';
		}

		if ( isset( $input['wrong_password_limit'] ) ) {
			$sanitized['wrong_password_limit'] = absint( $input['wrong_password_limit'] );
			if ( $sanitized['wrong_password_limit'] < 1 ) {
				$sanitized['wrong_password_limit'] = 5;
			}
			if ( $sanitized['wrong_password_limit'] > 20 ) {
				$sanitized['wrong_password_limit'] = 20;
			}
		}

		if ( isset( $input['password_lockout_duration'] ) ) {
			$sanitized['password_lockout_duration'] = absint( $input['password_lockout_duration'] );
			if ( $sanitized['password_lockout_duration'] < 1 ) {
				$sanitized['password_lockout_duration'] = 15;
			}
			if ( $sanitized['password_lockout_duration'] > 1440 ) {
				$sanitized['password_lockout_duration'] = 1440;
			}
		}

		// Security settings - Location-Based Restriction (Pro feature) - only save if pro is active.
		if ( $is_pro ) {
			if ( isset( $input['enable_location_restriction'] ) ) {
				$sanitized['enable_location_restriction'] = 'yes' === $input['enable_location_restriction'] ? 'yes' : 'no';
			}

			if ( isset( $input['location_restriction_type'] ) ) {
				$sanitized['location_restriction_type'] = in_array( $input['location_restriction_type'], array( 'blocked', 'allowed' ), true ) ? $input['location_restriction_type'] : 'blocked';
			}

			if ( isset( $input['location_countries'] ) ) {
				$sanitized['location_countries'] = sanitize_textarea_field( $input['location_countries'] );
			}
		}

		// Security settings - Registration Limit (Pro feature) - only save if pro is active.
		if ( $is_pro ) {
			if ( isset( $input['enable_registration_limit'] ) ) {
				$sanitized['enable_registration_limit'] = 'yes' === $input['enable_registration_limit'] ? 'yes' : 'no';
			}

			if ( isset( $input['max_registrations_per_ip'] ) ) {
				$sanitized['max_registrations_per_ip'] = absint( $input['max_registrations_per_ip'] );
				if ( $sanitized['max_registrations_per_ip'] < 1 ) {
					$sanitized['max_registrations_per_ip'] = 3;
				}
				if ( $sanitized['max_registrations_per_ip'] > 100 ) {
					$sanitized['max_registrations_per_ip'] = 100;
				}
			}

			if ( isset( $input['registration_limit_period'] ) ) {
				$sanitized['registration_limit_period'] = absint( $input['registration_limit_period'] );
				if ( $sanitized['registration_limit_period'] < 1 ) {
					$sanitized['registration_limit_period'] = 24;
				}
				if ( $sanitized['registration_limit_period'] > 168 ) {
					$sanitized['registration_limit_period'] = 168;
				}
			}
		}

		// Security settings - Banned Email Domains (Pro feature) - only save if pro is active.
		if ( $is_pro ) {
			if ( isset( $input['enable_banned_domains'] ) ) {
				$sanitized['enable_banned_domains'] = 'yes' === $input['enable_banned_domains'] ? 'yes' : 'no';
			}

			if ( isset( $input['banned_email_domains'] ) ) {
				$sanitized['banned_email_domains'] = sanitize_textarea_field( $input['banned_email_domains'] );
			}
		}

		// Widget settings - always include pro widgets for read-only view.
		$widgets = array(
			'login_form',
			'registration_form',
			'lost_password',
			'logout_button',
			'go_home_button',
			'auth_modal',
			'auth_form',
		);

		// Process widget settings.
		// With hidden inputs, all widget keys will be in input.
		// When checkbox is checked: both hidden ('no') and checkbox ('yes') are sent, WordPress uses last value ('yes').
		// When checkbox is unchecked: only hidden input ('no') is sent.
		$is_pro = \SmartLoginizer\Helpers\Pro_Helper::is_pro_active();
		$pro_widgets = array( 'auth_modal', 'auth_form' );
		
		foreach ( $widgets as $widget_key ) {
			$key = 'enable_widget_' . $widget_key;
			
			// Don't save pro widget settings if pro is not active.
			if ( in_array( $widget_key, $pro_widgets, true ) && ! $is_pro ) {
				continue;
			}
			
			if ( isset( $input[ $key ] ) ) {
				// WordPress may convert multiple values to array, or use last value.
				$value = $input[ $key ];
				if ( is_array( $value ) ) {
					// If array, checkbox was checked (last value is 'yes').
					$sanitized[ $key ] = 'yes';
				} else {
					// Single value: 'yes' if checked, 'no' if unchecked.
					$sanitized[ $key ] = 'yes' === $value ? 'yes' : 'no';
				}
			}
			// If key not in input at all, preserve existing value (already in $sanitized from $existing).
		}

		// Page Restriction settings (Pro feature) - only save if pro is active.
		if ( $is_pro ) {
			if ( isset( $input['page_restriction_login_template_id'] ) ) {
				$sanitized['page_restriction_login_template_id'] = absint( $input['page_restriction_login_template_id'] );
			}

			// Elementor Widget Restriction settings.
			if ( isset( $input['elementor_widget_restriction_login_template_id'] ) ) {
				$sanitized['elementor_widget_restriction_login_template_id'] = absint( $input['elementor_widget_restriction_login_template_id'] );
			}
		}

		return $sanitized;
	}

	/**
	 * Stored admin notices callbacks.
	 *
	 * @var array
	 */
	private array $stored_notice_callbacks = array();

	/**
	 * Whether notices have been captured.
	 *
	 * @var bool
	 */
	private bool $notices_captured = false;

	/**
	 * Prevent default admin notices from appearing in the header.
	 *
	 * @return void
	 */
	public function prevent_default_notices(): void {
		// Only on our settings page.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Checking page parameter only.
		if ( ! isset( $_GET['page'] ) || 'smart-loginizer' !== $_GET['page'] ) {
			return;
		}

		// Store and remove admin_notices hooks early.
		global $wp_filter;
		if ( isset( $wp_filter['admin_notices'] ) && ! $this->notices_captured ) {
			$hook = $wp_filter['admin_notices'];
			// Handle both WP_Hook object (WordPress 4.7+) and array format.
			if ( is_object( $hook ) && isset( $hook->callbacks ) ) {
				$this->stored_notice_callbacks = $hook->callbacks;
				$hook->callbacks = array();
			} elseif ( is_array( $hook ) ) {
				$this->stored_notice_callbacks = $hook;
				$wp_filter['admin_notices'] = array();
			}
			$this->notices_captured = true;
		}
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading tab parameter for display purposes only, not processing form data.
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';
		$tabs       = array(
			'general'    => __( 'General', 'smart-loginizer' ),
			'security'   => __( 'Security', 'smart-loginizer' ),
			'geolocation' => __( 'IP Geolocation', 'smart-loginizer' ),
			'social'     => __( 'Social Login', 'smart-loginizer' ),
			'page_restriction' => __( 'Page Restriction', 'smart-loginizer' ),
		);

		// Add WooCommerce tab if WooCommerce is active.
		if ( class_exists( 'WooCommerce' ) ) {
			$tabs['woocommerce'] = __( 'WooCommerce', 'smart-loginizer' );
		}

		// Add Help tab.
		$tabs['help'] = __( 'Help', 'smart-loginizer' );

		?>
		<div class="smart-loginizer-wrapper">
			<div class="smart-loginizer-header">
				<div class="wrap">
					<h1><?php esc_html_e( 'Smart Loginizer Settings', 'smart-loginizer' ); ?></h1>
				</div>
			</div>

			<div class="wrap smart-loginizer-settings-page">
				<?php
				// Display stored admin notices after the header.
				if ( ! empty( $this->stored_notice_callbacks ) ) {
					global $wp_filter;
					// Temporarily restore callbacks to output notices.
					if ( isset( $wp_filter['admin_notices'] ) ) {
						$hook = $wp_filter['admin_notices'];
						// Handle both WP_Hook object (WordPress 4.7+) and array format.
						if ( is_object( $hook ) && isset( $hook->callbacks ) ) {
							$original_callbacks = $hook->callbacks;
							$hook->callbacks = $this->stored_notice_callbacks;
							// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core WordPress hook.
							do_action( 'admin_notices' );
							// Clear them again to prevent duplicate output.
							$hook->callbacks = array();
						} elseif ( is_array( $hook ) ) {
							$wp_filter['admin_notices'] = $this->stored_notice_callbacks;
							// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core WordPress hook.
							do_action( 'admin_notices' );
							// Clear them again to prevent duplicate output.
							$wp_filter['admin_notices'] = array();
						}
					}
				}
				// Display settings errors.
				settings_errors( 'smart_loginizer_settings' );
				?>
				<nav class="nav-tab-wrapper">
					<?php
					foreach ( $tabs as $tab_key => $tab_label ) {
						$active_class = ( $active_tab === $tab_key ) ? ' nav-tab-active' : '';
						printf(
							'<a href="?page=smart-loginizer&tab=%s" class="nav-tab%s">%s</a>',
							esc_attr( $tab_key ),
							esc_attr( $active_class ),
							esc_html( $tab_label )
						);
					}
					?>
				</nav>

				<form action="options.php" method="post" class="smart-loginizer-settings-form">
					<?php
					settings_fields( 'smart_loginizer_settings' );

					// Render sections based on active tab.
					if ( 'general' === $active_tab ) {
						$this->render_tab_sections( array( 'smart_loginizer_general', 'smart_loginizer_widgets' ) );
						submit_button();
					} elseif ( 'security' === $active_tab ) {
						if ( ! \SmartLoginizer\Helpers\Pro_Helper::is_pro_active() ) {
							echo '<div class="smart-loginizer-pro-features-notice">';
							echo '<p class="description">' . esc_html__( 'Advanced security features are available in Smart Loginizer Pro. Configure your settings below (read-only in free version).', 'smart-loginizer' ) . '</p>';
							echo wp_kses_post( \SmartLoginizer\Helpers\Pro_Helper::get_pro_notice( __( 'Location-Based Restriction, Registration Limits, and Banned Email Domains', 'smart-loginizer' ) ) );
							echo '</div>';
						}
						$this->render_tab_sections( array( 'smart_loginizer_security_password_limit', 'smart_loginizer_security_location', 'smart_loginizer_security_registration', 'smart_loginizer_security_banned_domains' ) );
						submit_button();
					} elseif ( 'geolocation' === $active_tab ) {
						if ( ! \SmartLoginizer\Helpers\Pro_Helper::is_pro_active() ) {
							echo '<div class="smart-loginizer-pro-features-notice">';
							echo '<p class="description">' . esc_html__( 'IP Geolocation features are available in Smart Loginizer Pro. Configure your settings below (read-only in free version).', 'smart-loginizer' ) . '</p>';
							echo wp_kses_post( \SmartLoginizer\Helpers\Pro_Helper::get_pro_notice( __( 'IP Geolocation', 'smart-loginizer' ) ) );
							echo '</div>';
						}
						$this->render_tab_sections( array( 'smart_loginizer_geolocation' ) );
						submit_button();
					} elseif ( 'social' === $active_tab ) {
						$this->render_tab_sections( array( 'smart_loginizer_google', 'smart_loginizer_x', 'smart_loginizer_linkedin', 'smart_loginizer_facebook' ) );
						submit_button();
					} elseif ( 'woocommerce' === $active_tab ) {
						if ( ! \SmartLoginizer\Helpers\Pro_Helper::is_pro_active() ) {
							echo '<div class="smart-loginizer-pro-features-notice">';
							echo '<p class="description">' . esc_html__( 'WooCommerce integration features are available in Smart Loginizer Pro. Configure your settings below (read-only in free version).', 'smart-loginizer' ) . '</p>';
							echo wp_kses_post( \SmartLoginizer\Helpers\Pro_Helper::get_pro_notice( __( 'WooCommerce Integration', 'smart-loginizer' ) ) );
							echo '</div>';
						}
						$this->render_tab_sections( array( 'smart_loginizer_woocommerce' ) );
						submit_button();
					} elseif ( 'page_restriction' === $active_tab ) {
						if ( ! \SmartLoginizer\Helpers\Pro_Helper::is_pro_active() ) {
							echo '<div class="smart-loginizer-pro-features-notice">';
							echo '<p class="description">' . esc_html__( 'Page restriction features are available in Smart Loginizer Pro. Configure your settings below (read-only in free version).', 'smart-loginizer' ) . '</p>';
							echo wp_kses_post( \SmartLoginizer\Helpers\Pro_Helper::get_pro_notice( __( 'Page Restrictions', 'smart-loginizer' ) ) );
							echo '</div>';
						}
						$this->render_tab_sections( array( 'smart_loginizer_page_restriction', 'smart_loginizer_elementor_widget_restriction' ) );
						submit_button();
					} elseif ( 'help' === $active_tab ) {
						$this->render_help_tab();
					}
					?>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Render section.
	 *
	 * @param array $args Section arguments.
	 * @return void
	 */
	public function render_section( array $args ): void {
		$section_id = $args['id'] ?? '';
		$descriptions = array(
			'smart_loginizer_general' => sprintf(
				/* translators: %s: Link to create reCAPTCHA */
				__( 'Configure general plugin settings including reCAPTCHA and custom login page. Get your reCAPTCHA keys from <a href="%s" target="_blank">Google reCAPTCHA Admin</a>.', 'smart-loginizer' ),
				'https://www.google.com/recaptcha/admin/create'
			),
			'smart_loginizer_woocommerce' => __( 'Configure what happens when logged-out users visit the WooCommerce account page. You can replace the login form with an Elementor template or redirect them to a custom page.', 'smart-loginizer' ),
			'smart_loginizer_geolocation' => __( 'Configure IP geolocation service for location-based restrictions.', 'smart-loginizer' ),
			'smart_loginizer_google' => sprintf(
				/* translators: %s: Redirect URI */
				__( 'Configure Google OAuth. Get your credentials from <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a>. Redirect URI: <code>%s</code>', 'smart-loginizer' ),
				admin_url( 'admin-ajax.php?action=smart_loginizer_oauth_callback&provider=google' )
			),
			'smart_loginizer_x' => sprintf(
				/* translators: %s: Redirect URI */
				__( 'Configure X (Twitter) OAuth. Get your credentials from <a href="https://developer.twitter.com/" target="_blank">Twitter Developer Portal</a>. Redirect URI: <code>%s</code>', 'smart-loginizer' ),
				admin_url( 'admin-ajax.php?action=smart_loginizer_oauth_callback&provider=x' )
			),
			'smart_loginizer_linkedin' => sprintf(
				/* translators: %s: Redirect URI */
				__( 'Configure LinkedIn OAuth. Get your credentials from <a href="https://www.linkedin.com/developers/" target="_blank">LinkedIn Developers</a>. Redirect URI: <code>%s</code>', 'smart-loginizer' ),
				admin_url( 'admin-ajax.php?action=smart_loginizer_oauth_callback&provider=linkedin' )
			),
			'smart_loginizer_facebook' => sprintf(
				/* translators: %s: Redirect URI */
				__( 'Configure Facebook OAuth. Get your credentials from <a href="https://developers.facebook.com/" target="_blank">Facebook Developers</a>. Redirect URI: <code>%s</code>', 'smart-loginizer' ),
				admin_url( 'admin-ajax.php?action=smart_loginizer_oauth_callback&provider=facebook' )
			),
			'smart_loginizer_security_password_limit' => __( 'Limit the number of wrong password attempts to prevent brute force attacks.', 'smart-loginizer' ),
			'smart_loginizer_security_location' => __( 'Restrict login and registration based on user location (country).', 'smart-loginizer' ),
			'smart_loginizer_security_registration' => __( 'Limit the number of registrations per IP address to prevent spam.', 'smart-loginizer' ),
			'smart_loginizer_security_banned_domains' => __( 'Block registrations from specific email domains.', 'smart-loginizer' ),
		);

		if ( isset( $descriptions[ $section_id ] ) ) {
			echo '<p class="description">' . wp_kses_post( $descriptions[ $section_id ] ) . '</p>';
		}
	}

	/**
	 * Render field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_field( array $args ): void {
		$options = get_option( 'smart_loginizer_settings', array() );
		$field_id = $args['label_for'];
		$value   = isset( $options[ $field_id ] ) ? $options[ $field_id ] : ( isset( $args['default'] ) ? $args['default'] : '' );
		$type    = isset( $args['type'] ) ? $args['type'] : 'text';
		$description = isset( $args['description'] ) ? $args['description'] : '';
		$readonly = isset( $args['readonly'] ) && $args['readonly'];
		$is_pro = \SmartLoginizer\Helpers\Pro_Helper::is_pro_active();
		$readonly_attr = $readonly ? ' readonly="readonly" disabled="disabled"' : '';

		if ( 'textarea' === $type ) {
			printf(
				'<textarea id="%s" name="smart_loginizer_settings[%s]" class="large-text" rows="4"%s>%s</textarea>',
				esc_attr( $field_id ),
				esc_attr( $field_id ),
				$readonly_attr, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML attribute.
				esc_textarea( $value )
			);
		} else {
			$min_attr = '';
			$max_attr = '';
			if ( isset( $args['min'] ) && '' !== $args['min'] ) {
				$min_attr = ' min="' . esc_attr( $args['min'] ) . '"';
			}
			if ( isset( $args['max'] ) && '' !== $args['max'] ) {
				$max_attr = ' max="' . esc_attr( $args['max'] ) . '"';
			}
			// $min_attr and $max_attr are already escaped HTML attribute strings, safe to output.
			printf(
				'<input type="%s" id="%s" name="smart_loginizer_settings[%s]" value="%s" class="regular-text"%s%s%s />',
				esc_attr( $type ),
				esc_attr( $field_id ),
				esc_attr( $field_id ),
				esc_attr( $value ),
				$min_attr, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped HTML attribute string.
				$max_attr, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped HTML attribute string.
				$readonly_attr // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML attribute.
			);
		}

		if ( $readonly && ! $is_pro ) {
			printf( '<p class="description smart-loginizer-pro-notice-inline">%s</p>', wp_kses_post( \SmartLoginizer\Helpers\Pro_Helper::get_pro_notice( '' ) ) );
		} elseif ( ! empty( $description ) ) {
			printf( '<p class="description">%s</p>', wp_kses_post( $description ) );
		}
	}

	/**
	 * Render checkbox field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_checkbox_field( array $args ): void {
		$options = get_option( 'smart_loginizer_settings', array() );
		$field_id = $args['label_for'];
		$value   = isset( $options[ $field_id ] ) ? $options[ $field_id ] : 'no';
		$checked = 'yes' === $value;
		$readonly = isset( $args['readonly'] ) && $args['readonly'];
		$is_pro = \SmartLoginizer\Helpers\Pro_Helper::is_pro_active();
		$readonly_attr = $readonly ? ' disabled="disabled"' : '';

		printf(
			'<label><input type="checkbox" id="%s" name="smart_loginizer_settings[%s]" value="yes" %s%s /> %s</label>',
			esc_attr( $field_id ),
			esc_attr( $field_id ),
			checked( $checked, true, false ),
			$readonly_attr, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML attribute.
			esc_html__( 'Enable this feature', 'smart-loginizer' )
		);

		if ( $readonly && ! $is_pro ) {
			printf( '<p class="description smart-loginizer-pro-notice-inline">%s</p>', wp_kses_post( \SmartLoginizer\Helpers\Pro_Helper::get_pro_notice( '' ) ) );
		}
	}

	/**
	 * Render location restriction type field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_location_restriction_type_field( array $args = array() ): void {
		$options = get_option( 'smart_loginizer_settings', array() );
		$value   = isset( $options['location_restriction_type'] ) ? $options['location_restriction_type'] : 'blocked';
		$readonly = isset( $args['readonly'] ) && $args['readonly'];
		$is_pro = \SmartLoginizer\Helpers\Pro_Helper::is_pro_active();
		$readonly_attr = $readonly ? ' disabled="disabled"' : '';

		?>
		<select id="location_restriction_type" name="smart_loginizer_settings[location_restriction_type]" class="regular-text"<?php echo $readonly_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML attribute. ?>>
			<option value="blocked" <?php selected( $value, 'blocked' ); ?>>
				<?php esc_html_e( 'Block Listed Countries', 'smart-loginizer' ); ?>
			</option>
			<option value="allowed" <?php selected( $value, 'allowed' ); ?>>
				<?php esc_html_e( 'Allow Only Listed Countries', 'smart-loginizer' ); ?>
			</option>
		</select>
		<?php
		if ( $readonly && ! $is_pro ) {
			printf( '<p class="description smart-loginizer-pro-notice-inline">%s</p>', wp_kses_post( \SmartLoginizer\Helpers\Pro_Helper::get_pro_notice( '' ) ) );
		}
	}

	/**
	 * Render reCAPTCHA version field.
	 *
	 * @return void
	 */
	public function render_recaptcha_version_field(): void {
		$options = get_option( 'smart_loginizer_settings', array() );
		$value   = isset( $options['recaptcha_version'] ) ? $options['recaptcha_version'] : 'v3';

		?>
		<select id="recaptcha_version" name="smart_loginizer_settings[recaptcha_version]" class="regular-text">
			<option value="v3" <?php selected( $value, 'v3' ); ?>>
				<?php esc_html_e( 'reCAPTCHA v3 (Invisible)', 'smart-loginizer' ); ?>
			</option>
			<option value="v2_checkbox" <?php selected( $value, 'v2_checkbox' ); ?>>
				<?php esc_html_e( 'reCAPTCHA v2 (Checkbox)', 'smart-loginizer' ); ?>
			</option>
			<option value="v2_invisible" <?php selected( $value, 'v2_invisible' ); ?>>
				<?php esc_html_e( 'reCAPTCHA v2 (Invisible)', 'smart-loginizer' ); ?>
			</option>
		</select>
		<p class="description">
			<?php
			printf(
				/* translators: %s: Link to create reCAPTCHA */
				esc_html__( 'Select the reCAPTCHA version. v3 is recommended as it provides better user experience (invisible). Get your keys from %s.', 'smart-loginizer' ),
				'<a href="https://www.google.com/recaptcha/admin/create" target="_blank">' . esc_html__( 'Google reCAPTCHA Admin', 'smart-loginizer' ) . '</a>'
			);
			?>
		</p>
		<?php
	}

	/**
	 * Render IP geolocation service field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_ip_geolocation_field( array $args = array() ): void {
		$options = get_option( 'smart_loginizer_settings', array() );
		$value   = isset( $options['ip_geolocation_service'] ) ? $options['ip_geolocation_service'] : 'ip-api-free';
		$readonly = isset( $args['readonly'] ) && $args['readonly'];
		$is_pro = \SmartLoginizer\Helpers\Pro_Helper::is_pro_active();
		$readonly_attr = $readonly ? ' disabled="disabled"' : '';

		?>
		<select id="ip_geolocation_service" name="smart_loginizer_settings[ip_geolocation_service]" class="regular-text"<?php echo $readonly_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML attribute. ?>>
			<option value="ip-api-free" <?php selected( $value, 'ip-api-free' ); ?>>
				<?php esc_html_e( 'ip-api.com (Free - No API Key Required)', 'smart-loginizer' ); ?>
			</option>
			<option value="ip-api-pro" <?php selected( $value, 'ip-api-pro' ); ?>>
				<?php esc_html_e( 'ip-api.com (Pro - Requires API Key)', 'smart-loginizer' ); ?>
			</option>
			<option value="ipapi-co" <?php selected( $value, 'ipapi-co' ); ?>>
				<?php esc_html_e( 'ipapi.co (Requires API Key)', 'smart-loginizer' ); ?>
			</option>
			<option value="ipgeolocation-io" <?php selected( $value, 'ipgeolocation-io' ); ?>>
				<?php esc_html_e( 'ipgeolocation.io (Requires API Key)', 'smart-loginizer' ); ?>
			</option>
		</select>
		<?php if ( $readonly && ! $is_pro ) : ?>
			<p class="description smart-loginizer-pro-notice-inline"><?php echo wp_kses_post( \SmartLoginizer\Helpers\Pro_Helper::get_pro_notice( '' ) ); ?></p>
		<?php else : ?>
			<p class="description">
				<?php esc_html_e( 'Default: ip-api.com (Free). No configuration needed. For premium services, select the service and enter your API key below.', 'smart-loginizer' ); ?>
			</p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render widgets section with grid layout.
	 *
	 * @param array $args Section arguments.
	 * @return void
	 */
	public function render_widgets_section( array $args ): void {
		?>
		<p class="description">
			<?php esc_html_e( 'Enable or disable individual widgets. Disabled widgets will not appear in Elementor.', 'smart-loginizer' ); ?>
		</p>
		<?php
		
		$options = get_option( 'smart_loginizer_settings', array() );

		$widgets = array(
			'login_form'            => __( 'Login Form', 'smart-loginizer' ),
			'registration_form'     => __( 'Registration Form', 'smart-loginizer' ),
			'lost_password'         => __( 'Lost Password', 'smart-loginizer' ),
			'logout_button'         => __( 'Logout Button', 'smart-loginizer' ),
			'go_home_button'        => __( 'Go Home Button', 'smart-loginizer' ),
			'auth_modal'            => __( 'Auth Modal', 'smart-loginizer' ),
			'auth_form'             => __( 'Auth Form (Inline)', 'smart-loginizer' ),
		);

		?>
		<div class="smart-loginizer-widgets-grid">
			<?php foreach ( $widgets as $widget_key => $widget_label ) : ?>
				<?php
				$field_key = 'enable_widget_' . $widget_key;
				// Default to 'yes' if not set (all widgets enabled by default).
				$value = isset( $options[ $field_key ] ) ? $options[ $field_key ] : 'yes';
				$checked = 'yes' === $value;
				$is_pro_widget = in_array( $widget_key, array( 'auth_modal', 'auth_form' ), true );
				?>
				<div class="smart-loginizer-widget-card <?php echo $is_pro_widget ? 'smart-loginizer-pro-widget' : ''; ?>">
					<div class="smart-loginizer-widget-card-header">
						<h3 class="smart-loginizer-widget-title">
							<?php echo esc_html( $widget_label ); ?>
							<?php if ( $is_pro_widget && ! \SmartLoginizer\Helpers\Pro_Helper::is_pro_active() ) : ?>
								<span class="smart-loginizer-pro-badge"><?php esc_html_e( 'Pro', 'smart-loginizer' ); ?></span>
							<?php endif; ?>
						</h3>
						<label class="smart-loginizer-toggle-switch">
							<!-- Hidden input to ensure value is always sent, even when unchecked -->
							<input type="hidden" name="smart_loginizer_settings[<?php echo esc_attr( $field_key ); ?>]" value="no" />
							<input type="checkbox" name="smart_loginizer_settings[<?php echo esc_attr( $field_key ); ?>]" value="yes" <?php checked( $checked, true ); ?> <?php echo ( $is_pro_widget && ! \SmartLoginizer\Helpers\Pro_Helper::is_pro_active() ) ? 'disabled' : ''; ?> />
							<span class="smart-loginizer-toggle-slider"></span>
						</label>
					</div>
					<p class="smart-loginizer-widget-description">
						<?php esc_html_e( 'Enable this widget in Elementor', 'smart-loginizer' ); ?>
						<?php if ( $is_pro_widget && ! \SmartLoginizer\Helpers\Pro_Helper::is_pro_active() ) : ?>
							<br><small><?php echo wp_kses_post( \SmartLoginizer\Helpers\Pro_Helper::get_pro_notice( '' ) ); ?></small>
						<?php endif; ?>
					</p>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Render WooCommerce logged-out action field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_woocommerce_logged_out_action_field( array $args = array() ): void {
		$options = get_option( 'smart_loginizer_settings', array() );
		$action = isset( $options['woocommerce_logged_out_action'] ) ? $options['woocommerce_logged_out_action'] : 'default';
		$readonly = isset( $args['readonly'] ) && $args['readonly'];
		$is_pro = \SmartLoginizer\Helpers\Pro_Helper::is_pro_active();
		$readonly_attr = $readonly ? ' disabled="disabled"' : '';

		// Check if WooCommerce is active.
		$woocommerce_active = class_exists( 'WooCommerce' );

		?>
		<?php if ( ! $woocommerce_active ) : ?>
			<p class="description smart-loginizer-error-message">
				<?php esc_html_e( 'WooCommerce is not active. This feature requires WooCommerce to be installed and activated.', 'smart-loginizer' ); ?>
			</p>
		<?php else : ?>
			<fieldset>
				<label>
					<input type="radio" name="smart_loginizer_settings[woocommerce_logged_out_action]" value="default" <?php checked( $action, 'default' ); ?><?php echo $readonly_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML attribute. ?> />
					<?php esc_html_e( 'Default (Show WooCommerce login form)', 'smart-loginizer' ); ?>
				</label>
				<br>
				<label>
					<input type="radio" name="smart_loginizer_settings[woocommerce_logged_out_action]" value="template" <?php checked( $action, 'template' ); ?><?php echo $readonly_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML attribute. ?> />
					<?php esc_html_e( 'Replace with Elementor template', 'smart-loginizer' ); ?>
				</label>
				<br>
				<label>
					<input type="radio" name="smart_loginizer_settings[woocommerce_logged_out_action]" value="redirect" <?php checked( $action, 'redirect' ); ?><?php echo $readonly_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML attribute. ?> />
					<?php esc_html_e( 'Redirect to custom page', 'smart-loginizer' ); ?>
				</label>
			</fieldset>
			<?php if ( $readonly && ! $is_pro ) : ?>
				<p class="description smart-loginizer-pro-notice-inline"><?php echo wp_kses_post( \SmartLoginizer\Helpers\Pro_Helper::get_pro_notice( '' ) ); ?></p>
			<?php else : ?>
				<p class="description smart-loginizer-field-description">
					<?php esc_html_e( 'Choose what happens when logged-out users visit the WooCommerce account page.', 'smart-loginizer' ); ?>
				</p>
			<?php endif; ?>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render WooCommerce login replacement field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_woocommerce_login_replacement_field( array $args = array() ): void {
		$options = get_option( 'smart_loginizer_settings', array() );
		$action = isset( $options['woocommerce_logged_out_action'] ) ? $options['woocommerce_logged_out_action'] : 'default';
		$template_id = isset( $options['woocommerce_login_replacement_template_id'] ) ? absint( $options['woocommerce_login_replacement_template_id'] ) : 0;
		$readonly = isset( $args['readonly'] ) && $args['readonly'];
		$is_pro = \SmartLoginizer\Helpers\Pro_Helper::is_pro_active();

		// Check if WooCommerce is active.
		$woocommerce_active = class_exists( 'WooCommerce' );
		$elementor_active = class_exists( '\Elementor\Plugin' );

		?>
		<?php if ( ! $woocommerce_active ) : ?>
			<p class="description smart-loginizer-error-message">
				<?php esc_html_e( 'WooCommerce is not active. This feature requires WooCommerce to be installed and activated.', 'smart-loginizer' ); ?>
			</p>
		<?php elseif ( ! $elementor_active ) : ?>
			<p class="description smart-loginizer-error-message">
				<?php esc_html_e( 'Elementor is not active. This feature requires Elementor to be installed and activated.', 'smart-loginizer' ); ?>
			</p>
		<?php else : ?>
			<?php
			$action = isset( $options['woocommerce_logged_out_action'] ) ? $options['woocommerce_logged_out_action'] : 'default';
			$show_template_field = 'template' === $action;
			?>
			<div class="smart-loginizer-field-wrapper">
				<label for="woocommerce_login_replacement_template_id" class="smart-loginizer-field-label">
					<?php esc_html_e( 'Elementor Template', 'smart-loginizer' ); ?>
				</label>
				<select id="woocommerce_login_replacement_template_id" name="smart_loginizer_settings[woocommerce_login_replacement_template_id]" class="regular-text" <?php echo ( ! $show_template_field || $readonly ) ? 'disabled' : ''; ?>>
					<option value="0"><?php esc_html_e( '-- Select a template --', 'smart-loginizer' ); ?></option>
					<?php
					// Get Elementor templates.
					$templates = get_posts(
						array(
							'post_type'      => 'elementor_library',
							'posts_per_page' => -1,
							'post_status'    => 'publish',
							'orderby'        => 'title',
							'order'          => 'ASC',
						)
					);
					foreach ( $templates as $template ) {
						printf(
							'<option value="%d" %s>%s</option>',
							esc_attr( $template->ID ),
							selected( $template_id, $template->ID, false ),
							esc_html( $template->post_title )
						);
					}
					?>
				</select>
				<?php if ( $readonly && ! $is_pro ) : ?>
					<p class="description smart-loginizer-pro-notice-inline"><?php echo wp_kses_post( \SmartLoginizer\Helpers\Pro_Helper::get_pro_notice( '' ) ); ?></p>
				<?php else : ?>
					<p class="description">
						<?php esc_html_e( 'Select an Elementor template to replace the WooCommerce login form. Create templates in Templates > Saved Templates.', 'smart-loginizer' ); ?>
					</p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render WooCommerce redirect page field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_woocommerce_redirect_page_field( array $args = array() ): void {
		$options = get_option( 'smart_loginizer_settings', array() );
		$action = isset( $options['woocommerce_logged_out_action'] ) ? $options['woocommerce_logged_out_action'] : 'default';
		$page_id = isset( $options['woocommerce_logged_out_redirect_page_id'] ) ? absint( $options['woocommerce_logged_out_redirect_page_id'] ) : 0;
		$readonly = isset( $args['readonly'] ) && $args['readonly'];
		$is_pro = \SmartLoginizer\Helpers\Pro_Helper::is_pro_active();

		// Check if WooCommerce is active.
		$woocommerce_active = class_exists( 'WooCommerce' );

		?>
		<?php if ( ! $woocommerce_active ) : ?>
			<p class="description smart-loginizer-error-message">
				<?php esc_html_e( 'WooCommerce is not active.', 'smart-loginizer' ); ?>
			</p>
		<?php else : ?>
			<select id="woocommerce_logged_out_redirect_page_id" name="smart_loginizer_settings[woocommerce_logged_out_redirect_page_id]" class="regular-text" <?php echo ( 'redirect' !== $action || $readonly ) ? 'disabled' : ''; ?>>
				<option value="0"><?php esc_html_e( '-- Select a page --', 'smart-loginizer' ); ?></option>
				<?php
				$pages = get_pages(
					array(
						'sort_column' => 'post_title',
						'sort_order'  => 'ASC',
					)
				);
				foreach ( $pages as $page ) {
					printf(
						'<option value="%d" %s>%s</option>',
						esc_attr( $page->ID ),
						selected( $page_id, $page->ID, false ),
						esc_html( $page->post_title )
					);
				}
				?>
			</select>
			<?php if ( $readonly && ! $is_pro ) : ?>
				<p class="description smart-loginizer-pro-notice-inline"><?php echo wp_kses_post( \SmartLoginizer\Helpers\Pro_Helper::get_pro_notice( '' ) ); ?></p>
			<?php else : ?>
				<p class="description">
					<?php esc_html_e( 'Select the page to redirect logged-out users to when they visit the WooCommerce account page.', 'smart-loginizer' ); ?>
				</p>
			<?php endif; ?>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render custom login page field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_custom_login_page_field( array $args = array() ): void {
		$options = get_option( 'smart_loginizer_settings', array() );
		$enabled = isset( $options['enable_custom_login_page'] ) && 'yes' === $options['enable_custom_login_page'];
		$page_id = isset( $options['custom_login_page_id'] ) ? absint( $options['custom_login_page_id'] ) : 0;

		// Get all pages.
		$pages = get_pages(
			array(
				'sort_column' => 'post_title',
				'sort_order'  => 'ASC',
			)
		);

		?>
		<label>
			<input type="checkbox" name="smart_loginizer_settings[enable_custom_login_page]" value="yes" <?php checked( $enabled, true ); ?> />
			<?php esc_html_e( 'Redirect wp-login.php to a custom page', 'smart-loginizer' ); ?>
		</label>
		<p class="description smart-loginizer-field-description">
			<?php esc_html_e( 'When enabled, all requests to wp-login.php will be redirected to the selected page below. Make sure the selected page contains a login form widget.', 'smart-loginizer' ); ?>
		</p>

		<div class="smart-loginizer-field-wrapper">
			<label for="custom_login_page_id" class="smart-loginizer-field-label">
				<?php esc_html_e( 'Custom Login Page', 'smart-loginizer' ); ?>
			</label>
			<select id="custom_login_page_id" name="smart_loginizer_settings[custom_login_page_id]" class="regular-text" <?php echo ! $enabled ? 'disabled' : ''; ?>>
				<option value="0"><?php esc_html_e( '-- Select a page --', 'smart-loginizer' ); ?></option>
				<?php
				foreach ( $pages as $page ) {
					printf(
						'<option value="%d" %s>%s</option>',
						esc_attr( $page->ID ),
						selected( $page_id, $page->ID, false ),
						esc_html( $page->post_title )
					);
				}
				?>
			</select>
			<p class="description">
				<?php esc_html_e( 'Select the page that contains your custom login form. This page will replace the default WordPress login page.', 'smart-loginizer' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Render page restriction section description.
	 *
	 * @return void
	 */
	public function render_page_restriction_section(): void {
		?>
		<p>
			<?php esc_html_e( 'Configure the login page template that will be used when users are redirected from restricted pages. You can also set individual restrictions on any page or post using the Page Restriction meta box.', 'smart-loginizer' ); ?>
		</p>
		<?php
	}

	/**
	 * Render elementor widget restriction section description.
	 *
	 * @return void
	 */
	public function render_elementor_widget_restriction_section(): void {
		?>
		<p>
			<?php esc_html_e( 'Configure the login template that will be used when restricted widgets need to show a login page. You can set restrictions on any Elementor widget in the Advanced tab.', 'smart-loginizer' ); ?>
		</p>
		<?php
	}

	/**
	 * Render page restriction login template field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_page_restriction_login_template_field( array $args = array() ): void {
		$options = get_option( 'smart_loginizer_settings', array() );
		$template_id = isset( $options['page_restriction_login_template_id'] ) ? absint( $options['page_restriction_login_template_id'] ) : 0;
		$readonly = isset( $args['readonly'] ) && $args['readonly'];
		$is_pro = \SmartLoginizer\Helpers\Pro_Helper::is_pro_active();
		$readonly_attr = $readonly ? ' disabled="disabled"' : '';

		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			?>
			<p class="description">
				<?php esc_html_e( 'Elementor is not active. This feature requires Elementor to be installed and activated.', 'smart-loginizer' ); ?>
			</p>
			<?php
		} else {
			?>
			<div class="smart-loginizer-field-wrapper">
				<label for="page_restriction_login_template_id" class="smart-loginizer-field-label">
					<?php esc_html_e( 'Elementor Template', 'smart-loginizer' ); ?>
				</label>
				<select id="page_restriction_login_template_id" name="smart_loginizer_settings[page_restriction_login_template_id]" class="regular-text"<?php echo $readonly_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML attribute. ?>>
					<option value="0"><?php esc_html_e( '-- Select a template --', 'smart-loginizer' ); ?></option>
					<?php
					// Get Elementor templates.
					$templates = get_posts(
						array(
							'post_type'      => 'elementor_library',
							'posts_per_page' => -1,
							'post_status'    => 'publish',
							'orderby'        => 'title',
							'order'          => 'ASC',
						)
					);
					foreach ( $templates as $template ) {
						printf(
							'<option value="%d" %s>%s</option>',
							esc_attr( $template->ID ),
							selected( $template_id, $template->ID, false ),
							esc_html( $template->post_title )
						);
					}
					?>
				</select>
				<?php if ( $readonly && ! $is_pro ) : ?>
					<p class="description smart-loginizer-pro-notice-inline"><?php echo wp_kses_post( \SmartLoginizer\Helpers\Pro_Helper::get_pro_notice( '' ) ); ?></p>
				<?php else : ?>
					<p class="description">
						<?php esc_html_e( 'Select an Elementor template to use as the login page when users are redirected from restricted pages. Create templates in Templates > Saved Templates.', 'smart-loginizer' ); ?>
					</p>
				<?php endif; ?>
			</div>
			<?php
		}
	}

	/**
	 * Render elementor widget restriction login template field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_elementor_widget_restriction_login_template_field( array $args = array() ): void {
		$options = get_option( 'smart_loginizer_settings', array() );
		$template_id = isset( $options['elementor_widget_restriction_login_template_id'] ) ? absint( $options['elementor_widget_restriction_login_template_id'] ) : 0;
		$readonly = isset( $args['readonly'] ) && $args['readonly'];
		$is_pro = \SmartLoginizer\Helpers\Pro_Helper::is_pro_active();
		$readonly_attr = $readonly ? ' disabled="disabled"' : '';

		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			?>
			<p class="description">
				<?php esc_html_e( 'Elementor is not active. This feature requires Elementor to be installed and activated.', 'smart-loginizer' ); ?>
			</p>
			<?php
		} else {
			?>
			<div class="smart-loginizer-field-wrapper">
				<label for="elementor_widget_restriction_login_template_id" class="smart-loginizer-field-label">
					<?php esc_html_e( 'Elementor Template', 'smart-loginizer' ); ?>
				</label>
				<select id="elementor_widget_restriction_login_template_id" name="smart_loginizer_settings[elementor_widget_restriction_login_template_id]" class="regular-text"<?php echo $readonly_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML attribute. ?>>
					<option value="0"><?php esc_html_e( '-- Select a template --', 'smart-loginizer' ); ?></option>
					<?php
					// Get Elementor templates.
					$templates = get_posts(
						array(
							'post_type'      => 'elementor_library',
							'posts_per_page' => -1,
							'post_status'    => 'publish',
							'orderby'        => 'title',
							'order'          => 'ASC',
						)
					);
					foreach ( $templates as $template ) {
						printf(
							'<option value="%d" %s>%s</option>',
							esc_attr( $template->ID ),
							selected( $template_id, $template->ID, false ),
							esc_html( $template->post_title )
						);
					}
					?>
				</select>
				<?php if ( $readonly && ! $is_pro ) : ?>
					<p class="description smart-loginizer-pro-notice-inline"><?php echo wp_kses_post( \SmartLoginizer\Helpers\Pro_Helper::get_pro_notice( '' ) ); ?></p>
				<?php else : ?>
					<p class="description">
						<?php esc_html_e( 'Select an Elementor template to use as the login page when restricted widgets need to show a login form. Create templates in Templates > Saved Templates.', 'smart-loginizer' ); ?>
					</p>
				<?php endif; ?>
			</div>
			<?php
		}
	}

	/**
	 * Render tab sections.
	 *
	 * @param array $section_ids Section IDs to render.
	 * @return void
	 */
	private function render_tab_sections( array $section_ids ): void {
		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections['smart-loginizer'] ) ) {
			return;
		}

		foreach ( $section_ids as $section_id ) {
			if ( ! isset( $wp_settings_sections['smart-loginizer'][ $section_id ] ) ) {
				continue;
			}

			$section = $wp_settings_sections['smart-loginizer'][ $section_id ];

			if ( $section['title'] ) {
				printf( '<h2>%s</h2>', esc_html( $section['title'] ) );
			}

			if ( $section['callback'] ) {
				call_user_func( $section['callback'], $section );
			}

			if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields['smart-loginizer'] ) || ! isset( $wp_settings_fields['smart-loginizer'][ $section_id ] ) ) {
				continue;
			}

			echo '<table class="form-table" role="presentation">';
			do_settings_fields( 'smart-loginizer', $section_id );
			echo '</table>';
		}
	}

	/**
	 * Render help tab content.
	 *
	 * @return void
	 */
	private function render_help_tab(): void {
		?>
		<div class="smart-loginizer-help-tab">
			<div class="smart-loginizer-help-section">
				<h2><?php esc_html_e( 'Getting Started', 'smart-loginizer' ); ?></h2>
				<p><?php esc_html_e( 'Smart Loginizer provides powerful login and registration forms with advanced security features, social login integration, and WooCommerce compatibility.', 'smart-loginizer' ); ?></p>
				
				<h3><?php esc_html_e( 'Quick Setup', 'smart-loginizer' ); ?></h3>
				<ol>
					<li><?php esc_html_e( 'Go to the General tab and configure your reCAPTCHA settings (optional but recommended).', 'smart-loginizer' ); ?></li>
					<li><?php esc_html_e( 'Enable or disable widgets as needed in the Widget Settings section.', 'smart-loginizer' ); ?></li>
					<li><?php esc_html_e( 'Configure security settings in the Security tab.', 'smart-loginizer' ); ?></li>
					<li><?php esc_html_e( 'Set up social login providers in the Social Login tab.', 'smart-loginizer' ); ?></li>
					<li><?php esc_html_e( 'Add the widgets to your pages using Elementor.', 'smart-loginizer' ); ?></li>
				</ol>
			</div>

			<div class="smart-loginizer-help-section">
				<h2><?php esc_html_e( 'Available Widgets', 'smart-loginizer' ); ?></h2>
				<ul>
					<li><strong><?php esc_html_e( 'Login Form', 'smart-loginizer' ); ?></strong> - <?php esc_html_e( 'A standalone login form with customizable styling.', 'smart-loginizer' ); ?></li>
					<li><strong><?php esc_html_e( 'Registration Form', 'smart-loginizer' ); ?></strong> - <?php esc_html_e( 'User registration form with password strength meter.', 'smart-loginizer' ); ?></li>
					<li><strong><?php esc_html_e( 'Auth Form', 'smart-loginizer' ); ?></strong> - <?php esc_html_e( 'Combined login and registration form with tabbed interface.', 'smart-loginizer' ); ?></li>
					<li><strong><?php esc_html_e( 'Auth Modal', 'smart-loginizer' ); ?></strong> - <?php esc_html_e( 'Modal popup with login, registration, and password reset forms.', 'smart-loginizer' ); ?></li>
					<li><strong><?php esc_html_e( 'Lost Password', 'smart-loginizer' ); ?></strong> - <?php esc_html_e( 'Password reset form for recovering lost passwords.', 'smart-loginizer' ); ?></li>
				</ul>
			</div>

			<div class="smart-loginizer-help-section">
				<h2><?php esc_html_e( 'Features', 'smart-loginizer' ); ?></h2>
				<ul>
					<li><?php esc_html_e( 'reCAPTCHA v2 (Checkbox & Invisible) and v3 support', 'smart-loginizer' ); ?></li>
					<li><?php esc_html_e( 'Social login integration (Google, X/Twitter, LinkedIn, Facebook)', 'smart-loginizer' ); ?></li>
					<li><?php esc_html_e( 'Advanced security features (password limits, location restrictions, registration limits)', 'smart-loginizer' ); ?></li>
					<li><?php esc_html_e( 'IP-based geolocation restrictions', 'smart-loginizer' ); ?></li>
					<li><?php esc_html_e( 'Banned email domain filtering', 'smart-loginizer' ); ?></li>
					<li><?php esc_html_e( 'WooCommerce integration', 'smart-loginizer' ); ?></li>
					<li><?php esc_html_e( 'Fully customizable with Elementor', 'smart-loginizer' ); ?></li>
					<li><?php esc_html_e( 'AJAX-powered forms for better user experience', 'smart-loginizer' ); ?></li>
				</ul>
			</div>

			<div class="smart-loginizer-help-section">
				<h2><?php esc_html_e( 'Support & Documentation', 'smart-loginizer' ); ?></h2>
				<p><?php esc_html_e( 'Need help? Here are some resources:', 'smart-loginizer' ); ?></p>
				<p>
					<a href="#" target="_blank" class="button button-primary">
						<?php esc_html_e( 'Get Support', 'smart-loginizer' ); ?>
					</a>
					<a href="#" target="_blank" class="button button-secondary">
						<?php esc_html_e( 'View Docs', 'smart-loginizer' ); ?>
					</a>
				</p>
				<ul>
					<li><?php esc_html_e( 'Check the plugin documentation for detailed guides.', 'smart-loginizer' ); ?></li>
					<li><?php esc_html_e( 'Review the settings in each tab for configuration options.', 'smart-loginizer' ); ?></li>
					<li><?php esc_html_e( 'Each widget has its own settings panel in Elementor for customization.', 'smart-loginizer' ); ?></li>
				</ul>
			</div>

			<div class="smart-loginizer-help-section">
				<h2><?php esc_html_e( 'Tips', 'smart-loginizer' ); ?></h2>
				<ul>
					<li><?php esc_html_e( 'Enable reCAPTCHA to protect your forms from spam and bots.', 'smart-loginizer' ); ?></li>
					<li><?php esc_html_e( 'Configure social login to allow users to sign in with their social accounts.', 'smart-loginizer' ); ?></li>
					<li><?php esc_html_e( 'Use security settings to limit failed login attempts and restrict registrations.', 'smart-loginizer' ); ?></li>
					<li><?php esc_html_e( 'You can enable or disable reCAPTCHA per widget in the Security section of each widget.', 'smart-loginizer' ); ?></li>
					<li><?php esc_html_e( 'Global security settings apply to all widgets, but can be overridden per widget.', 'smart-loginizer' ); ?></li>
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue_admin_assets( string $hook ): void {
		if ( 'toplevel_page_smart-loginizer' !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'smart-loginizer-admin',
			SMART_LOGINIZER_URL . 'assets/css/admin.css',
			array(),
			SMART_LOGINIZER_VERSION
		);

		// Add inline CSS to hide notices in header immediately (prevents flash).
		$inline_css = '.smart-loginizer-header .notice,
		.smart-loginizer-header .notice-error,
		.smart-loginizer-header .notice-warning,
		.smart-loginizer-header .notice-success,
		.smart-loginizer-header .notice-info {
			display: none !important;
		}';
		wp_add_inline_style( 'smart-loginizer-admin', $inline_css );

		wp_enqueue_script(
			'smart-loginizer-admin',
			SMART_LOGINIZER_URL . 'assets/js/admin.js',
			array(),
			SMART_LOGINIZER_VERSION,
			true
		);
	}
}

