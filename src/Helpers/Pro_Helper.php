<?php
/**
 * Pro version helper.
 *
 * @package SmartLoginizer\Helpers
 */

namespace SmartLoginizer\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pro Helper class.
 */
class Pro_Helper {

	/**
	 * Check if pro version is active.
	 *
	 * @return bool
	 */
	public static function is_pro_active(): bool {
		return defined( 'SMART_LOGINIZER_PRO_VERSION' ) && class_exists( 'SmartLoginizerPro\Core\Core' );
	}

	/**
	 * Check if a pro feature is available.
	 *
	 * @param string $feature Feature name.
	 * @return bool
	 */
	public static function is_pro_feature_available( string $feature ): bool {
		if ( ! self::is_pro_active() ) {
			return false;
		}

		// Check if pro version supports this feature.
		return apply_filters( 'smart_loginizer_pro_feature_available', false, $feature );
	}

	/**
	 * Get pro upgrade URL.
	 *
	 * @return string
	 */
	public static function get_upgrade_url(): string {
		return apply_filters( 'smart_loginizer_upgrade_url', 'https://example.com/upgrade' );
	}

	/**
	 * Display pro upgrade notice.
	 *
	 * @param string $feature Feature name.
	 * @return string
	 */
	public static function get_pro_notice( string $feature = '' ): string {
		$message = __( 'This feature is available in Smart Loginizer Pro.', 'smart-loginizer' );
		if ( ! empty( $feature ) ) {
			$message = sprintf(
				/* translators: %s: Feature name */
				__( '%s is available in Smart Loginizer Pro.', 'smart-loginizer' ),
				$feature
			);
		}

		$upgrade_url = self::get_upgrade_url();
		$notice = sprintf(
			'<div class="smart-loginizer-pro-notice">
				<p><strong>%s</strong></p>
				<p><a href="%s" class="button button-primary" target="_blank">%s</a></p>
			</div>',
			esc_html( $message ),
			esc_url( $upgrade_url ),
			esc_html__( 'Upgrade to Pro', 'smart-loginizer' )
		);

		return $notice;
	}
}

