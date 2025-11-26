<?php
/**
 * Widgets manager.
 *
 * @package SmartLoginizer\Widgets
 */

namespace SmartLoginizer\Widgets;

use SmartLoginizer\Widgets\Login_Form_Widget;
use SmartLoginizer\Widgets\Registration_Form_Widget;
use SmartLoginizer\Widgets\Lost_Password_Widget;
use SmartLoginizer\Widgets\Logout_Button_Widget;
use SmartLoginizer\Widgets\Go_Home_Button_Widget;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widgets manager class.
 */
class Widgets_Manager {

	/**
	 * Initialize widgets.
	 *
	 * @return void
	 */
	public function init(): void {
		// Register category with priority to ensure it runs after default categories.
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ), 20 );
		// Reorder categories after registration.
		add_action( 'elementor/elements/categories_registered', array( $this, 'reorder_categories' ), 25 );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
	}

	/**
	 * Register Elementor category.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elements manager.
	 * @return void
	 */
	public function register_category( \Elementor\Elements_Manager $elements_manager ): void {
		$elements_manager->add_category(
			'smart-loginizer',
			array(
				'title' => __( 'Smart Loginizer', 'smart-loginizer' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	/**
	 * Reorder categories to place Smart Loginizer after Layout.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elements manager.
	 * @return void
	 */
	public function reorder_categories( \Elementor\Elements_Manager $elements_manager ): void {
		// Use reflection to access and modify categories.
		$reflection = new \ReflectionClass( $elements_manager );
		$property   = $reflection->getProperty( 'categories' );
		$property->setAccessible( true );
		$categories = $property->getValue( $elements_manager );

		// Check if Layout and Smart Loginizer categories exist.
		if ( ! isset( $categories['layout'] ) || ! isset( $categories['smart-loginizer'] ) ) {
			return;
		}

		// Store our category.
		$our_category = $categories['smart-loginizer'];

		// Remove our category from the array.
		unset( $categories['smart-loginizer'] );

		// Create new ordered array.
		$ordered_categories = array();
		$found_layout       = false;

		foreach ( $categories as $key => $category ) {
			$ordered_categories[ $key ] = $category;

			// Insert our category right after Layout.
			if ( 'layout' === $key && ! $found_layout ) {
				$ordered_categories['smart-loginizer'] = $our_category;
				$found_layout                          = true;
			}
		}

		// If Layout wasn't found, just append our category.
		if ( ! $found_layout ) {
			$ordered_categories['smart-loginizer'] = $our_category;
		}

		// Update the categories.
		$property->setValue( $elements_manager, $ordered_categories );
	}

	/**
	 * Register widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 * @return void
	 */
	public function register_widgets( \Elementor\Widgets_Manager $widgets_manager ): void {
		$options = get_option( 'smart_loginizer_settings', array() );

		// Widget mapping: widget_key => widget_class.
		$widgets = array(
			'login_form'            => Login_Form_Widget::class,
			'registration_form'     => Registration_Form_Widget::class,
			'lost_password'         => Lost_Password_Widget::class,
			'logout_button'         => Logout_Button_Widget::class,
			'go_home_button'        => Go_Home_Button_Widget::class,
		);

		// Pro widgets.
		if ( \SmartLoginizer\Helpers\Pro_Helper::is_pro_active() ) {
			// Check if pro widgets are available.
			if ( class_exists( 'SmartLoginizerPro\Widgets\Auth_Modal_Widget' ) ) {
				$widgets['auth_modal'] = 'SmartLoginizerPro\Widgets\Auth_Modal_Widget';
			}
			if ( class_exists( 'SmartLoginizerPro\Widgets\Auth_Form_Widget' ) ) {
				$widgets['auth_form'] = 'SmartLoginizerPro\Widgets\Auth_Form_Widget';
			}
		}

		foreach ( $widgets as $widget_key => $widget_class ) {
			$field_key = 'enable_widget_' . $widget_key;
			// Default to 'yes' if not set (all widgets enabled by default).
			$enabled = isset( $options[ $field_key ] ) ? $options[ $field_key ] : 'yes';

			if ( 'yes' === $enabled ) {
				// Handle string class names (for pro widgets).
				if ( is_string( $widget_class ) && class_exists( $widget_class ) ) {
					$widgets_manager->register( new $widget_class() );
				} elseif ( class_exists( $widget_class ) ) {
					$widgets_manager->register( new $widget_class() );
				}
			}
		}
	}
}
