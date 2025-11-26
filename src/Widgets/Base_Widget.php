<?php
/**
 * Base widget class.
 *
 * @package SmartLoginizer\Widgets
 */

namespace SmartLoginizer\Widgets;

use Elementor\Widget_Base;
use SmartLoginizer\Security\Security;
use SmartLoginizer\Helpers\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base widget class.
 */
abstract class Base_Widget extends Widget_Base {

	/**
	 * Security instance.
	 *
	 * @var Security
	 */
	protected Security $security;

	/**
	 * Constructor.
	 *
	 * @param array $data Widget data.
	 * @param array $args Widget arguments.
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
		$this->security = new Security();
	}

	/**
	 * Get widget categories.
	 *
	 * @return array
	 */
	public function get_categories(): array {
		return array( 'smart-loginizer' );
	}

	/**
	 * Get widget keywords.
	 *
	 * @return array
	 */
	public function get_keywords(): array {
		return array( 'smart', 'loginizer', 'login', 'account' );
	}

	/**
	 * Render social login buttons.
	 *
	 * @param bool   $show_social_login Whether to show social login.
	 * @param string $social_login_text Social login divider text.
	 * @param bool   $text_below Whether to show text below buttons (for top position).
	 * @param array  $icon_settings Icon settings array with provider keys.
	 * @param string $icon_position Icon position (left or right).
	 * @return void
	 */
	protected function render_social_login_buttons( bool $show_social_login = false, string $social_login_text = '', bool $text_below = false, array $icon_settings = array(), string $icon_position = 'left' ): void {
		if ( ! $show_social_login ) {
			return;
		}

		$helpers = new Helpers();
		$providers = $helpers->get_enabled_social_providers();

		if ( empty( $providers ) ) {
			return;
		}

		?>
		<div class="smart-loginizer-social-login">
			<?php if ( ! empty( $social_login_text ) && ! $text_below ) : ?>
				<div class="smart-loginizer-social-login-divider">
					<span class="smart-loginizer-social-login-text"><?php echo esc_html( $social_login_text ); ?></span>
				</div>
			<?php endif; ?>
			<div class="smart-loginizer-social-login-buttons">
				<?php foreach ( $providers as $provider_key => $provider_data ) : ?>
					<?php
					$icon = $icon_settings[ $provider_key . '_icon' ] ?? array();
					$this->render_social_button( $provider_key, $provider_data['name'], $icon, $icon_position );
					?>
				<?php endforeach; ?>
			</div>
			<?php if ( ! empty( $social_login_text ) && $text_below ) : ?>
				<div class="smart-loginizer-social-login-divider">
					<span class="smart-loginizer-social-login-text"><?php echo esc_html( $social_login_text ); ?></span>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render individual social login button.
	 *
	 * @param string $provider Provider key.
	 * @param string $label Button label.
	 * @param array  $icon Icon data from Elementor.
	 * @param string $icon_position Icon position (left or right).
	 * @return void
	 */
	private function render_social_button( string $provider, string $label, array $icon = array(), string $icon_position = 'left' ): void {
		$button_class = 'smart-loginizer-social-login-button smart-loginizer-social-btn smart-loginizer-' . esc_attr( $provider ) . '-btn';
		$icon_class = 'smart-loginizer-social-icon smart-loginizer-icon-' . esc_attr( $icon_position );
		$icon_html = '';
		$has_icon = false;
		
		if ( ! empty( $icon['value'] ) ) {
			ob_start();
			\Elementor\Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
			$icon_html = ob_get_clean();
			$has_icon = ! empty( $icon_html );
		} else {
			// Fallback to default SVG if no icon is set.
			$icon_svg = $this->get_social_icon_svg( $provider );
			if ( ! empty( $icon_svg ) ) {
				$icon_html = $icon_svg;
				$has_icon = true;
			}
		}
		
		?>
		<button type="button" class="<?php echo esc_attr( $button_class ); ?>" data-provider="<?php echo esc_attr( $provider ); ?>">
			<?php if ( 'left' === $icon_position && $has_icon ) : ?>
				<span class="<?php echo esc_attr( $icon_class ); ?>">
					<?php echo wp_kses( $icon_html, array( 'svg' => array( 'width' => array(), 'height' => array(), 'viewBox' => array(), 'xmlns' => array(), 'class' => array() ), 'g' => array( 'fill' => array(), 'fill-rule' => array() ), 'path' => array( 'd' => array(), 'fill' => array() ), 'i' => array( 'class' => array(), 'aria-hidden' => array() ) ) ); ?>
				</span>
			<?php endif; ?>
			<span class="smart-loginizer-social-text"><?php echo esc_html( $label ); ?></span>
			<?php if ( 'left' === $icon_position && $has_icon ) : ?>
				<span class="smart-loginizer-social-icon-spacer" data-icon-width></span>
			<?php endif; ?>
			<?php if ( 'right' === $icon_position && $has_icon ) : ?>
				<span class="<?php echo esc_attr( $icon_class ); ?>">
					<?php echo wp_kses( $icon_html, array( 'svg' => array( 'width' => array(), 'height' => array(), 'viewBox' => array(), 'xmlns' => array(), 'class' => array() ), 'g' => array( 'fill' => array(), 'fill-rule' => array() ), 'path' => array( 'd' => array(), 'fill' => array() ), 'i' => array( 'class' => array(), 'aria-hidden' => array() ) ) ); ?>
				</span>
			<?php endif; ?>
		</button>
		<?php
	}

	/**
	 * Get social icon SVG (fallback).
	 *
	 * @param string $provider Provider key.
	 * @return string SVG markup.
	 */
	private function get_social_icon_svg( string $provider ): string {
		$icons = array(
			'google' => '<svg width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg"><g fill="#000" fill-rule="evenodd"><path d="M17.64 9.2045c0-.6371-.0573-1.2516-.1636-1.8409H9v3.4814h4.8436c-.2086 1.125-.8427 2.0782-1.7955 2.7164v2.2581h2.9087c1.7023-1.5668 2.6836-3.874 2.6836-6.6149z" fill="#4285F4"/><path d="M9 18c2.43 0 4.4673-.795 5.9564-2.1805l-2.9087-2.2581c-.8059.54-1.8368.859-3.0477.859-2.344 0-4.3282-1.5831-5.036-3.7104H.9573v2.3318C2.4382 15.9832 5.4818 18 9 18z" fill="#34A853"/><path d="M3.9636 10.7104c-.18-.54-.2827-1.1168-.2827-1.7104s.1027-1.1704.2827-1.7104V4.9573H.9573C.3477 6.1745 0 7.5477 0 9s.3477 2.8255.9573 4.0427l3.0063-2.3323z" fill="#FBBC05"/><path d="M9 3.5795c1.3214 0 2.5077.4541 3.4405 1.3460l2.5813-2.5814C13.4632.8918 11.4264 0 9 0 5.4818 0 2.4382 2.0168.9573 4.9573l3.0063 2.3318C4.6718 5.1623 6.6559 3.5795 9 3.5795z" fill="#EA4335"/></g></svg>',
			'x' => '<svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" fill="#000"/></svg>',
			'linkedin' => '<svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" fill="#0A66C2"/></svg>',
			'facebook' => '<svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="#1877F2"/></svg>',
		);

		return $icons[ $provider ] ?? '';
	}
}

