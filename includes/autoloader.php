<?php
/**
 * Simple autoloader for Smart Loginizer.
 *
 * @package SmartLoginizer
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Autoloader function.
 *
 * @param string $class Class name.
 * @return void
 */
function smart_loginizer_autoloader( string $class ): void {
	// Only handle our namespace.
	if ( strpos( $class, 'SmartLoginizer\\' ) !== 0 ) {
		return;
	}

	// Remove namespace prefix.
	$class = str_replace( 'SmartLoginizer\\', '', $class );

	// Convert namespace separators to directory separators.
	$class = str_replace( '\\', DIRECTORY_SEPARATOR, $class );

	// Build file path.
	$file = SMART_LOGINIZER_PATH . 'src' . DIRECTORY_SEPARATOR . $class . '.php';

	// Require file if it exists.
	if ( file_exists( $file ) ) {
		require_once $file;
	}
}

// Register autoloader.
spl_autoload_register( 'smart_loginizer_autoloader' );

