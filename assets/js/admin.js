/**
 * Admin JavaScript
 *
 * @package SmartLoginizer
 */

(function() {
	'use strict';

	/**
	 * Initialize WooCommerce action field handlers.
	 */
	function initWooCommerceFields() {
		const actionRadios = document.querySelectorAll('input[name="smart_loginizer_settings[woocommerce_logged_out_action]"]');
		const templateSelect = document.getElementById('woocommerce_login_replacement_template_id');
		const redirectSelect = document.getElementById('woocommerce_logged_out_redirect_page_id');
		
		if (actionRadios.length === 0) {
			return;
		}

		function updateFields() {
			const selectedAction = document.querySelector('input[name="smart_loginizer_settings[woocommerce_logged_out_action]"]:checked')?.value || 'default';
			
			if (templateSelect) {
				templateSelect.disabled = selectedAction !== 'template';
			}
			if (redirectSelect) {
				redirectSelect.disabled = selectedAction !== 'redirect';
			}
		}
		
		actionRadios.forEach(function(radio) {
			radio.addEventListener('change', updateFields);
		});
		
		updateFields();
	}

	/**
	 * Initialize custom login page field handler.
	 */
	function initCustomLoginPageField() {
		const checkbox = document.querySelector('input[name="smart_loginizer_settings[enable_custom_login_page]"]');
		const select = document.getElementById('custom_login_page_id');
		
		if (checkbox && select) {
			checkbox.addEventListener('change', function() {
				select.disabled = !this.checked;
			});
		}
	}

	/**
	 * Move notices from header to correct location.
	 */
	function moveNoticesFromHeader() {
		const header = document.querySelector('.smart-loginizer-header');
		const settingsPage = document.querySelector('.smart-loginizer-settings-page');
		
		if (!header || !settingsPage) {
			return;
		}
		
		// Find all notices inside the header.
		const noticesInHeader = header.querySelectorAll('.notice, .notice-error, .notice-warning, .notice-success, .notice-info');
		
		if (noticesInHeader.length > 0) {
			// Create a container for notices if it doesn't exist.
			let noticesContainer = settingsPage.querySelector('.smart-loginizer-notices-container');
			if (!noticesContainer) {
				noticesContainer = document.createElement('div');
				noticesContainer.className = 'smart-loginizer-notices-container';
				settingsPage.insertBefore(noticesContainer, settingsPage.firstChild);
			}
			
			// Move each notice to the container.
			noticesInHeader.forEach(function(notice) {
				noticesContainer.appendChild(notice);
			});
		}
	}

	/**
	 * Initialize all admin functionality when DOM is ready.
	 */
	function init() {
		initWooCommerceFields();
		initCustomLoginPageField();
		// Move notices immediately and also after a short delay to catch any that load later.
		moveNoticesFromHeader();
		setTimeout(moveNoticesFromHeader, 100);
	}

	// Initialize when DOM is ready.
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();

