=== Smart Loginizer ===
Contributors: yourname
Tags: elementor, login, registration, authentication, oauth
Requires at least: 6.4
Tested up to: 6.8
Requires PHP: 8.1
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Elementor-based My Account & Authentication plugin with OAuth (Google, X/Twitter, LinkedIn, Facebook), reCAPTCHA, and advanced form widgets.

== Description ==

Smart Loginizer is a comprehensive WordPress plugin that provides Elementor widgets for user authentication and account management. It includes login forms, registration forms, password recovery, and my account navigation widgets.

== Features ==

This section describes the **free version** of Smart Loginizer. See the **Free vs Pro** section below for a detailed comparison.

* **Elementor Widgets (Free):**
  * Login Form Widget
  * Registration Form Widget
  * Lost Password Widget
  * Logout Button Widget
  * Go Home Button Widget

* **Social Login (Free):**
  * Google, X (Twitter), LinkedIn, Facebook OAuth

* **reCAPTCHA (Free):**
  * reCAPTCHA v3, v2 (Checkbox), and v2 (Invisible) support

* **Custom Login Page (Free):**
  * Replace the default WordPress login with a custom page built in Elementor

* **Page Restriction (Free):**
  * “Page Restriction” meta box on posts and pages
  * Restrict access to logged-in, logged-out, or specific user roles

* **Core Security & UX (Free):**
  * AJAX form submissions
  * Nonce verification, sanitization, and escaping
  * Secure redirects and basic protection hardening
  * Responsive and accessibility-friendly design

== Free vs Pro ==

= Free Version (this plugin) =

* Elementor widgets:
  * Login Form, Registration Form, Lost Password, Logout Button, Go Home Button
* Authentication core:
  * AJAX login, registration, and lost password
  * Custom redirects after login, registration, and logout
  * Custom Login Page (replace wp-login.php with a selected page)
* reCAPTCHA:
  * reCAPTCHA v3, v2 (Checkbox), v2 (Invisible)
* Social Login (OAuth):
  * Google, X (Twitter), LinkedIn, Facebook
* Page Restriction:
  * "Page Restriction" meta box on posts and pages
  * Restrict to logged-in, logged-out, or specific roles
  * Show message or redirect to another page/URL
* Core security:
  * Nonce verification, sanitization/escaping, secure redirects

= Pro Version (separate add-on) =

* Extra Elementor widgets:
  * Auth Modal widget
  * Auth Form (Inline) widget with extended options
* WooCommerce enhancements:
  * Replace WooCommerce login with Elementor templates
  * Advanced "Action for Logged-Out Users" (redirect or template replacement)
* Elementor Widget Restriction:
  * Control where specific widgets render based on login/restriction rules
* Advanced security:
  * Location-based restrictions (country/IP)
  * Registration limits (rate limiting per IP)
  * Banned email domains (blacklist/whitelist)
* IP Geolocation:
  * Provider selection and configuration
* Page restriction enhancements:
  * Global Elementor login template for restricted content

== Pro Version ==

Upgrade to **Smart Loginizer Pro** to unlock advanced features such as WooCommerce login replacement, Elementor widget restriction, Auth Modal & Auth Form (Inline) widgets, advanced security (location-based restrictions, registration limits, banned domains), IP geolocation, and more.

Purchase Pro here: `https://smart-loginizer.buildbycj.com/pro`

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/smart-loginizer` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Ensure Elementor is installed and activated (required).
4. Configure the plugin settings in Smart Loginizer > Settings.
5. Add widgets to your Elementor pages using the Elementor editor.

== Screenshots ==

1. Smart Loginizer Settings Page
2. Login Form Widget
3. Registration Form Widget
4. Social Login Options
5. Security Settings

== Frequently Asked Questions ==

= Does this plugin require Elementor? =

Yes, this plugin requires Elementor 4.0 or higher to be installed and active. The plugin will display a notice if Elementor is not active.

= Does this plugin work with WooCommerce? =

Yes, the plugin has full WooCommerce integration. The My Account Content widget can display WooCommerce endpoints, and you can replace the WooCommerce login form with custom Elementor templates.

= How do I set up OAuth providers? =

For Google OAuth:
1. Go to Google Cloud Console (https://console.cloud.google.com/)
2. Create OAuth 2.0 credentials
3. Add the redirect URI shown in the settings page
4. Add the Client ID and Secret in Smart Loginizer > Settings > Social Login

Similar process applies for X (Twitter), LinkedIn, and Facebook. See the Help tab in settings for detailed instructions.

= How do I set up reCAPTCHA? =

1. Go to Google reCAPTCHA Admin Console (https://www.google.com/recaptcha/admin/create)
2. Create a site (v2 or v3)
3. Add the Site Key and Secret Key in Smart Loginizer > Settings > General

= Can I customize the login forms? =

Yes, all forms are fully customizable through Elementor. Each widget has extensive styling options including colors, typography, spacing, and layout controls.

= Does this plugin support password strength requirements? =

Yes, the Registration Form widget includes a built-in password strength meter that helps users create strong passwords.

= Can I restrict logins by location? =

Yes, the plugin includes location-based restriction features. You can block or allow specific countries based on IP geolocation.

= What happens to user data when I uninstall the plugin? =

By default, the plugin will remove all plugin-related data. However, you can choose to preserve user data during uninstallation.

== Changelog ==

= 1.0.1 =
* Bug fixes and improvements
* Enhanced security and performance optimizations
* Code quality improvements

= 1.0.0 =
* Initial release
* 9 Elementor widgets (Login Form, Registration Form, Lost Password, My Account Nav, My Account Content, Logout Button, Go Home Button, Auth Modal, Auth Form)
* Social login integration (Google, X/Twitter, LinkedIn, Facebook)
* reCAPTCHA v2 (Checkbox & Invisible) and v3 support
* Advanced security features (password limits, location restrictions, registration limits, banned domains)
* IP geolocation support
* WooCommerce integration
* AJAX form submissions
* Password strength meter
* Custom login page support
* Fully customizable with Elementor
* WordPress.org 2025 standards compliant

== Upgrade Notice ==

= 1.0.0 =
Initial release of Smart Loginizer.

