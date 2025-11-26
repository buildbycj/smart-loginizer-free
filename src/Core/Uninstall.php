<?php
/**
 * Uninstall handler.
 *
 * @package SmartLoginizer\Core
 */

namespace SmartLoginizer\Core;

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Uninstall class.
 */
class Uninstall {

	/**
	 * Uninstall plugin.
	 *
	 * @return void
	 */
	public static function uninstall(): void {
		// Check if user has permission.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		// Get option to check if user data should be preserved.
		$preserve_data = get_option( 'smart_loginizer_preserve_data', false );

		// Remove plugin options.
		$options = array(
			'smart_loginizer_settings',
			'smart_loginizer_oauth_tokens',
			'smart_loginizer_recaptcha_site_key',
			'smart_loginizer_recaptcha_secret_key',
			'smart_loginizer_preserve_data',
		);

		foreach ( $options as $option ) {
			delete_option( $option );
		}

		// Remove transients.
		// Direct database query is necessary here as there's no WordPress API to delete transients by pattern.
		// Caching is not applicable for uninstall operations.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Necessary for uninstall cleanup, no WordPress API available for pattern-based deletion.
		global $wpdb;
		$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Necessary for uninstall cleanup, no WordPress API available for pattern-based deletion.
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
				$wpdb->esc_like( '_transient_smart_loginizer_' ) . '%',
				$wpdb->esc_like( '_transient_timeout_smart_loginizer_' ) . '%'
			)
		);

		// Remove user meta if not preserving data.
		// Direct database query is necessary here as there's no WordPress API to delete user meta by pattern.
		// Caching is not applicable for uninstall operations.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Necessary for uninstall cleanup, no WordPress API available for pattern-based deletion.
		if ( ! $preserve_data ) {
			$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Necessary for uninstall cleanup, no WordPress API available for pattern-based deletion.
				$wpdb->prepare(
					"DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE %s",
					$wpdb->esc_like( 'smart_loginizer_' ) . '%'
				)
			);
		}
	}
}

