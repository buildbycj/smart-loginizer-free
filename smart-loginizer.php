<?php
/**
 * Plugin Name: Smart Loginizer
 * Plugin URI: https://wordpress.org/plugins/smart-loginizer
 * Description: Elementor-based My Account & Authentication plugin with OAuth (Google, X/Twitter, LinkedIn, Facebook), reCAPTCHA, and advanced form widgets.
 * Version: 1.0.1
 * Author: Your Name
 * Author URI: https://profiles.wordpress.org/buildbycj/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: smart-loginizer
 * Domain Path: /languages
 * Requires at least: 6.4
 * Tested up to: 6.8
 * Requires PHP: 8.1
 * Elementor tested up to: 4.0
 * Elementor Pro: false
 *
 * @package SmartLoginizer
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
if ( ! defined( 'SMART_LOGINIZER_VERSION' ) ) {
	define( 'SMART_LOGINIZER_VERSION', '1.0.1' );
}
if ( ! defined( 'SMART_LOGINIZER_PATH' ) ) {
	define( 'SMART_LOGINIZER_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'SMART_LOGINIZER_URL' ) ) {
	define( 'SMART_LOGINIZER_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'SMART_LOGINIZER_BASENAME' ) ) {
	define( 'SMART_LOGINIZER_BASENAME', plugin_basename( __FILE__ ) );
}

// Autoloader - try Composer first, fallback to simple autoloader.
if ( file_exists( SMART_LOGINIZER_PATH . 'vendor/autoload.php' ) ) {
	require_once SMART_LOGINIZER_PATH . 'vendor/autoload.php';
} else {
	require_once SMART_LOGINIZER_PATH . 'includes/autoloader.php';
}

/**
 * Main plugin class.
 */
final class Smart_Loginizer {

	/**
	 * Plugin instance.
	 *
	 * @var Smart_Loginizer
	 */
	private static ?Smart_Loginizer $instance = null;

	/**
	 * Core instance.
	 *
	 * @var SmartLoginizer\Core\Core
	 */
	public SmartLoginizer\Core\Core $core;

	/**
	 * Get plugin instance.
	 *
	 * @return Smart_Loginizer
	 */
	public static function get_instance(): Smart_Loginizer {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Initialize plugin.
	 *
	 * @return void
	 */
	private function init(): void {
		// WordPress 4.6+ auto-loads translations from /languages folder.
		// For plugins hosted on WordPress.org, translations are automatically loaded.
		// No need to manually call load_plugin_textdomain().

		// Initialize core on plugins_loaded.
		add_action( 'plugins_loaded', array( $this, 'init_core' ), 20 );
	}

	/**
	 * Initialize core.
	 *
	 * @return void
	 */
	public function init_core(): void {
		// Check if Elementor is active.
		if ( ! $this->is_elementor_active() ) {
			add_action( 'admin_notices', array( $this, 'elementor_missing_notice' ) );
			return;
		}

		// Initialize core.
		$this->core = new SmartLoginizer\Core\Core();
		$this->core->init();
	}

	/**
	 * Check if Elementor is active.
	 *
	 * @return bool
	 */
	private function is_elementor_active(): bool {
		return did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Plugin' );
	}

	/**
	 * Display notice if Elementor is not active.
	 *
	 * @return void
	 */
	public function elementor_missing_notice(): void {
		$message = sprintf(
			/* translators: 1: Plugin name, 2: Elementor */
			esc_html__( '%1$s requires %2$s to be installed and active.', 'smart-loginizer' ),
			'<strong>' . esc_html__( 'Smart Loginizer', 'smart-loginizer' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'smart-loginizer' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', wp_kses_post( $message ) );
	}
}

/**
 * Initialize plugin.
 *
 * @return Smart_Loginizer
 */
function smart_loginizer(): Smart_Loginizer {
	return Smart_Loginizer::get_instance();
}

// Initialize plugin.
smart_loginizer();

// Register uninstall hook.
register_uninstall_hook( __FILE__, array( 'SmartLoginizer\Core\Uninstall', 'uninstall' ) );

