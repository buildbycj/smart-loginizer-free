# Plugin Development Details

## Overview

This document outlines the plugin development guidelines, architecture, coding standards (WordPress 2025), feature implementation methods, and best practices to develop the Elementor-based My Account & Authentication plugin.

---

## 1. Plugin Architecture

* **Core PHP Architecture**

  * Namespace-based file structure.
  * PSR-4 autoloading using composer.
  * Modular classes: `Core`, `Admin`, `Frontend`, `Widgets`, `Security`, `Helpers`.

* **Main Plugin File**

  * Define constants: `PLUGIN_VERSION`, `PLUGIN_PATH`, `PLUGIN_URL`.
  * Register autoloader.
  * Initialize plugin core on `plugins_loaded`.

* **Folder Structure**

```
plugin-name/
├── plugin-name.php
├── vendor/
├── src/
│   ├── Core/
│   ├── Admin/
│   ├── Frontend/
│   ├── Widgets/
│   ├── Security/
│   ├── Helpers/
├── templates/
├── assets/
│   ├── css/
│   └── js/
└── languages/
```

---

## 2. Elementor Widgets

Each Elementor widget is a separate PHP class under `src/Widgets/` and extends `\Elementor\Widget_Base`.

### Widgets List:

* My Account Nav Widget
* My Account Content Widget
* Login Form Widget
* Registration Form Widget
* Lost Password Widget
* Logout Button Widget
* Go Home Button Widget

### Widget Development Standards

* Register widget via `elementor/widgets/widgets_registered`.
* Use Elementor Controls API.
* Add responsive settings.
* Follow WCAG accessibility standards.
* Escape all output using `esc_html`, `esc_attr`, `esc_url`.

---

## 3. Feature Implementation

* **Gmail Authentication:** via OAuth (Google APIs).
* **Redirects:** Use `wp_login` and `template_redirect` hooks.
* **AJAX Forms:** Use `wp_ajax` and `wp_ajax_nopriv` endpoints.
* **reCAPTCHA Support:** Using Google v3 API.
* **Password Strength & Validation:** Use `zxcvbn.js`.
* **Icon Controls:** Use Elementor icon picker.
* **Granular CSS Loading:** Using conditional enqueue based on settings.

---

## 4. Coding Guidelines (WordPress 2025)

* Minimum PHP version **8.2**.
* Strict typing enabled.
* Use nonces for all form submissions.
* Validate and sanitize using: `sanitize_text_field`, `esc_url_raw`, `wp_kses_post`.
* Use `$wpdb->prepare()` for SQL queries.
* Follow PSR-12 formatting.
* Proper error logging using `error_log()` or WC logger.

---

## 5. Security Checklist

* [ ] Nonces applied to all form requests
* [ ] Sanitization & escaping
* [ ] Prevent direct file access
* [ ] Secure tokens for OAuth
* [ ] Role & capability validation
* [ ] GDPR compliance (delete user data on request)
* [ ] Use `wp_safe_redirect()`

---

## 6. Performance Optimization

* Conditional asset loading per widget
* Built-in caching using transients
* Avoid heavy DB queries in frontend
* Optimized images in templates
* Core Web Vitals safe (FID, CLS, LCP)

---

## 7. Uninstall & Cleanup

* Use `register_uninstall_hook()`
* Remove all plugin options and metadata
* Option to preserve user data

---

## 8. Developer Hooks & Filters

* `templines_account_nav_items`
* `templines_login_redirect_url`
* `templines_registration_success_message`
* `templines_enable_css_module`

---

## 9. Multilingual & Translation

* Use `__()` and `_e()` for strings
* Load text domain using `load_plugin_textdomain`
* .pot file stored in `/languages/`

---

## 10. Versioning & Update System

* Follow semantic versioning: `MAJOR.MINOR.PATCH`
* Include changelog in `readme.txt`
* Remote updater compatibility

---

## 11. Compatibility Testing

* Elementor 4.0+ and 2025 standards
* WooCommerce 9.0+
* PHP 8.2+
* WordPress 6.8+
* Tested with major cache and security plugins

---

## 12. Documentation & Support

* Developer documentation (API, hooks, widget development)
* End user documentation (setup wizard, usage guides)
* Support ticket system integration

---

## 13. Release Checklist

* [ ] Code validation via PHPCS (WordPress standard)
* [ ] Security scan
* [ ] Performance audit
* [ ] Elementor compatibility test
* [ ] WooCommerce test (if applicable)
* [ ] Translation test
* [ ] Final packaging

---

## 14. Future Enhancements

* Social login integrations
* User profile editor widget
* Avatar upload
* UI dashboard skin presets
* WooCommerce mini dashboard widgets

UI Wireframes & Control Structure
15.1 My Account Navigation Widget
+----------------------------------------------------+
| Orientation: [Horizontal | Vertical]              |
| Spacing Slider: 0px ------------------- 50px       |
| Icon Position: [Left | Right]                     |
| Alignment: [Left | Center | Right]                |
| Divider: [Toggle]                                 |
| Menu Items (Repeater):                            |
|   - Title                                         |
|   - Icon (Elementor Icon Control)                 |
|   - Endpoint / Custom URL                         |
|   - Visibility (Show/Hide)                        |
|   - Order (Sortable)                              |
+----------------------------------------------------+
15.2 My Account Content Widget
+----------------------------------------------------+
| Content Wrapper Style                             |
| Padding: [Slider]                                 |
| Background: [Color Picker]                        |
| Border Radius: [Slider]                           |
| Content Source: [WC Endpoint / Custom Template]   |
| Typography Controls                               |
+----------------------------------------------------+
15.3 Login Form Widget
+----------------------------------------------------+
| Form Fields:                                      |
|   - Username / Email                              |
|   - Password                                      |
| Submit Button Text                                |
| Redirect After Login: [URL Selector]              |
| Enable AJAX: [Toggle]                             |
| Enable reCAPTCHA: [Toggle]                        |
| Style Controls:                                   |
|   - Typography                                    |
|   - Padding / Margin                              |
|   - Button Style                                  |
+----------------------------------------------------+
15.4 Registration Form Widget
+----------------------------------------------------+
| Form Fields:                                      |
|   - Username                                      |
|   - Email                                         |
|   - Password                                      |
|   - Confirm Password                              |
| Enable Password Strength Meter: [Toggle]          |
| Auto-login After Registration: [Toggle]           |
| Redirect After Registration: [URL Selector]       |
| Enable reCAPTCHA: [Toggle]                        |
| Style Controls (similar to Login Widget)          |
+----------------------------------------------------+
15.5 Lost Password Widget
+----------------------------------------------------+
| Email Input                                       |
| Submit Button                                     |
| Success Message Customization                     |
| Style Controls                                    |
+----------------------------------------------------+
15.6 Logout Button Widget
+----------------------------------------------------+
| Redirect After Logout: [URL Selector]             |
| Button Text                                       |
| Style Controls                                    |
+----------------------------------------------------+
15.7 Go Home Button Widget
+----------------------------------------------------+
| Destination URL: [Home by default | Custom]       |
| Button Text                                       |
| Icon (Optional)                                   |
| Style Controls                                    |
+----------------------------------------------------+
16. Future Enhancements

Social login integrations

User profile editor widget

Avatar upload

UI dashboard skin presets

WooCommerce mini dashboard widgets

---

## Final Notes

The plugin must be fully modular, developer-friendly, optimized for performance, and 100% aligned with WordPress and Elementor 2025 coding standards.

> Keep the code future-proof, scalable, and extensible. Ensure no hardcoded strings or styles. All logic must follow OOP principles and hook-based architecture.
