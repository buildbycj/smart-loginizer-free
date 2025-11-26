<?php
/**
 * Core plugin class.
 *
 * @package SmartLoginizer\Core
 */

namespace SmartLoginizer\Core;

use SmartLoginizer\Admin\Admin;
use SmartLoginizer\Admin\Page_Restriction as Admin_Page_Restriction;
use SmartLoginizer\Frontend\Frontend;
use SmartLoginizer\Frontend\Page_Restriction as Frontend_Page_Restriction;
use SmartLoginizer\Widgets\Widgets_Manager;
use SmartLoginizer\Widgets\Elementor_Widget_Restriction;
use SmartLoginizer\Security\Security;
use SmartLoginizer\Helpers\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Core class.
 */
class Core {

	/**
	 * Admin instance.
	 *
	 * @var Admin
	 */
	public Admin $admin;

	/**
	 * Frontend instance.
	 *
	 * @var Frontend
	 */
	public Frontend $frontend;

	/**
	 * Widgets manager instance.
	 *
	 * @var Widgets_Manager
	 */
	public Widgets_Manager $widgets_manager;

	/**
	 * Security instance.
	 *
	 * @var Security
	 */
	public Security $security;

	/**
	 * Helpers instance.
	 *
	 * @var Helpers
	 */
	public Helpers $helpers;

	/**
	 * Admin Page Restriction instance.
	 *
	 * @var Admin_Page_Restriction
	 */
	public Admin_Page_Restriction $admin_page_restriction;

	/**
	 * Frontend Page Restriction instance.
	 *
	 * @var Frontend_Page_Restriction
	 */
	public Frontend_Page_Restriction $frontend_page_restriction;

	/**
	 * Elementor Widget Restriction instance.
	 *
	 * @var Elementor_Widget_Restriction
	 */
	public Elementor_Widget_Restriction $elementor_widget_restriction;

	/**
	 * Initialize core.
	 *
	 * @return void
	 */
	public function init(): void {
		// Initialize helpers first.
		$this->helpers = new Helpers();

		// Initialize security.
		$this->security = new Security();

		// Initialize admin.
		$this->admin = new Admin();

		// Initialize admin page restriction.
		$this->admin_page_restriction = new Admin_Page_Restriction();

		// Initialize frontend.
		$this->frontend = new Frontend();

		// Initialize frontend page restriction.
		$this->frontend_page_restriction = new Frontend_Page_Restriction();

		// Initialize widgets manager.
		$this->widgets_manager = new Widgets_Manager();
		$this->widgets_manager->init();

		// Initialize Elementor widget restriction.
		if ( class_exists( '\Elementor\Plugin' ) ) {
			$this->elementor_widget_restriction = new Elementor_Widget_Restriction();
		}
	}
}

