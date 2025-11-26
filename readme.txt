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

* **9 Elementor Widgets:**
  * My Account Nav Widget - Horizontal and vertical navigation layouts, customizable menu items with icons, endpoint and custom URL support, icon positioning (left/right), spacing and alignment controls, divider options, sortable menu items, visibility controls per item
  * My Account Content Widget - Display WooCommerce endpoints, custom template support, content wrapper styling, padding/background/border radius controls, typography customization, responsive design
  * Login Form Widget - Username/Email and password fields, AJAX form submissions, custom redirect after login, reCAPTCHA integration, fully customizable styling, typography controls, button style customization, responsive settings
  * Registration Form Widget - Username, email, password, and confirm password fields, password strength meter (zxcvbn.js), auto-login after registration option, custom redirect after registration, reCAPTCHA integration, email verification support, customizable form styling, responsive design
  * Lost Password Widget - Email input field, password reset functionality, customizable success messages, AJAX form submission, reCAPTCHA support, styling controls
  * Logout Button Widget - Custom redirect after logout, customizable button text, icon support, full styling controls, responsive settings
  * Go Home Button Widget - Navigate to home or custom URL, customizable button text, optional icon, full styling controls
  * Auth Modal Widget - Modal popup for authentication, login and registration tabs, customizable modal styling, trigger button customization, responsive modal design
  * Auth Form Widget (Inline) - Inline authentication form, login and registration support, customizable layout, full styling controls

* **Social Login Integration (OAuth):**
  * Google OAuth - OAuth 2.0 authentication, user profile data retrieval, secure token handling
  * X (Twitter) OAuth - Twitter API v2 support, user authentication, profile data sync
  * LinkedIn OAuth - Professional network integration, profile information retrieval, secure authentication flow
  * Facebook OAuth - Facebook Login integration, user profile data, secure authentication

* **Security Features:**
  * reCAPTCHA Support - reCAPTCHA v2 (Checkbox), reCAPTCHA v2 (Invisible), reCAPTCHA v3, configurable per form
  * Password Protection - Wrong password attempt limits, account lockout after failed attempts, configurable lockout duration, password strength requirements
  * Location-Based Restrictions - IP geolocation support, country-based login restrictions, allow/block specific countries, IP whitelist/blacklist
  * Registration Security - Registration limits per IP, time-based rate limiting, banned email domain filtering, custom domain blacklist/whitelist
  * General Security - Nonce verification on all forms, input sanitization and output escaping, SQL injection prevention, XSS protection, CSRF protection, secure redirects

* **User Experience Features:**
  * AJAX Form Submissions - No page reload on form submission, real-time error handling, loading indicators, success/error messages
  * Password Strength Meter - Visual password strength indicator, real-time feedback, strength requirements, zxcvbn.js integration
  * Custom Redirects - Custom redirect after login, custom redirect after registration, custom redirect after logout, role-based redirects
  * Custom Login Page - Replace default WordPress login, custom login page URL, Elementor template support
  * Responsive Design - Mobile-first approach, tablet optimization, desktop layouts, Elementor responsive controls
  * Accessibility - WCAG 2.1 AA compliance, keyboard navigation support, screen reader compatibility, ARIA labels and roles, focus management

* **WooCommerce Integration:**
  * My Account Support - Replace WooCommerce My Account page, custom endpoint display, navigation integration, content widget support
  * Login Form Replacement - Replace WooCommerce login form, custom Elementor templates, redirect options for logged-out users, seamless integration
  * Account Endpoints - Orders endpoint, Downloads endpoint, Addresses endpoint, Account details endpoint, Payment methods endpoint, custom endpoints support

* **Additional Features:**
  * Page Restrictions - Restrict pages to logged-in users, custom redirect for unauthorized access, Elementor template restrictions, role-based access control
  * Admin Settings Panel - Comprehensive settings page, tabbed interface, help documentation, settings import/export
  * Multilingual Support - Translation-ready (.pot file), WPML compatible, Polylang compatible, RTL support
  * Performance Optimized - Conditional asset loading, transient caching, optimized database queries, Core Web Vitals optimized
  * Developer Friendly - PSR-4 autoloading, namespace-based architecture, hooks and filters, extensible widget system

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

