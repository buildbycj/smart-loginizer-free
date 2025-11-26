<?php
/**
 * Custom WooCommerce login form template.
 * Replaces default login form with Elementor template.
 *
 * @package SmartLoginizer
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$smart_loginizer_options = get_option( 'smart_loginizer_settings', array() );
$smart_loginizer_template_id = isset( $smart_loginizer_options['woocommerce_login_replacement_template_id'] ) ? absint( $smart_loginizer_options['woocommerce_login_replacement_template_id'] ) : 0;

// Check if Elementor is active and template exists.
if ( 0 === $smart_loginizer_template_id || ! class_exists( '\Elementor\Plugin' ) ) {
	// Fallback to default WooCommerce template.
	$smart_loginizer_default_template = WC()->plugin_path() . '/templates/myaccount/form-login.php';
	if ( file_exists( $smart_loginizer_default_template ) ) {
		include $smart_loginizer_default_template; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.NotAbsolutePath
	}
	return;
}

$smart_loginizer_template = get_post( $smart_loginizer_template_id );
if ( ! $smart_loginizer_template || 'elementor_library' !== $smart_loginizer_template->post_type ) {
	// Fallback to default WooCommerce template.
	$smart_loginizer_default_template = WC()->plugin_path() . '/templates/myaccount/form-login.php';
	if ( file_exists( $smart_loginizer_default_template ) ) {
		include $smart_loginizer_default_template; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.NotAbsolutePath
	}
	return;
}

// Output Elementor template content.
$smart_loginizer_elementor_content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $smart_loginizer_template_id );

if ( ! empty( $smart_loginizer_elementor_content ) ) {
	?>
	<div class="smart-loginizer-woocommerce-replacement">
		<?php echo $smart_loginizer_elementor_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<?php
} else {
	// Fallback to default if Elementor content is empty.
	$smart_loginizer_default_template = WC()->plugin_path() . '/templates/myaccount/form-login.php';
	if ( file_exists( $smart_loginizer_default_template ) ) {
		include $smart_loginizer_default_template; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.NotAbsolutePath
	}
}

