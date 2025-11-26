# Smart Loginizer - Installation Guide

## Prerequisites

- WordPress 6.8 or higher
- PHP 8.2 or higher
- Elementor 4.0 or higher
- Composer (for dependency management)

## Installation Steps

1. **Install Composer Dependencies**
   ```bash
   cd /path/to/smart-loginizer
   composer install
   ```

2. **Activate the Plugin**
   - Go to WordPress Admin → Plugins
   - Find "Smart Loginizer" and click "Activate"

3. **Configure Settings**
   - Go to Settings → Smart Loginizer
   - Configure the following:
     - **reCAPTCHA**: Add your Site Key and Secret Key (optional)
     - **Gmail OAuth**: Add your Client ID and Client Secret (optional)

4. **Use in Elementor**
   - Edit any page with Elementor
   - Look for the "Smart Loginizer" category in the widget panel
   - Drag and drop any of the 7 widgets to your page

## Widgets Available

1. **My Account Nav** - Navigation menu for account pages
2. **My Account Content** - Display WooCommerce endpoints or custom content
3. **Login Form** - User login form with AJAX support
4. **Registration Form** - User registration with password strength meter
5. **Lost Password** - Password recovery form
6. **Logout Button** - Logout button with redirect option
7. **Go Home Button** - Button to navigate home or custom URL

## Configuration

### reCAPTCHA Setup
1. Visit [Google reCAPTCHA Admin Console](https://www.google.com/recaptcha/admin)
2. Create a new site (v3)
3. Copy the Site Key and Secret Key
4. Paste them in Settings → Smart Loginizer

### Gmail OAuth Setup
1. Visit [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing
3. Enable Google+ API
4. Create OAuth 2.0 credentials
5. Add authorized redirect URI: `your-site.com/wp-admin/admin-ajax.php?action=smart_loginizer_oauth_callback`
6. Copy Client ID and Client Secret
7. Paste them in Settings → Smart Loginizer

## Developer Notes

- All code follows PSR-4 autoloading
- Namespace: `SmartLoginizer\`
- Minimum PHP: 8.2
- Uses WordPress coding standards
- All output is escaped
- All inputs are sanitized
- Nonces are used for all form submissions

## Support

For issues or questions, please refer to the plugin documentation or contact support.

