/**
 * Frontend JavaScript
 *
 * @package SmartLoginizer
 */

(function ($) {
	'use strict';

	// Initialize on document ready
	$(document).ready(function () {
		initAjaxForms();
		initPasswordStrength();
		initRecaptcha();
		initFormStyles();
		initSocialLogin();
		initSocialButtonSpacers();
		initPasswordToggle();
	});

	// Re-initialize spacers when content is dynamically loaded
	$(document).on('elementor/popup/show', function() {
		setTimeout(initSocialButtonSpacers, 100);
	});

	/**
	 * Initialize AJAX forms
	 */
	function initAjaxForms() {
		$('.smart-loginizer-ajax-form').on('submit', function (e) {
			e.preventDefault();

			const $form = $(this);
			const $message = $form.find('.smart-loginizer-form-message');
			const formData = $form.serialize();
			const action = $form.find('input[name="action"]').val();

			// Get reCAPTCHA token if enabled globally
			if (typeof smartLoginizer !== 'undefined' && smartLoginizer.enableRecaptcha && smartLoginizer.recaptchaSiteKey) {
				const version = smartLoginizer.recaptchaVersion || 'v3';
				
				if (typeof grecaptcha !== 'undefined') {
					if (version === 'v3') {
						// reCAPTCHA v3
						grecaptcha.ready(function () {
							grecaptcha
								.execute(smartLoginizer.recaptchaSiteKey, { action: action })
								.then(function (token) {
									// Find any recaptcha_token input in the form
									const $tokenInput = $form.find('input[name="recaptcha_token"]');
									if ($tokenInput.length) {
										$tokenInput.val(token);
									}
									submitForm($form, formData, $message);
								})
								.catch(function (error) {
									console.error('reCAPTCHA v3 error:', error);
									submitForm($form, formData, $message);
								});
						});
					} else if (version === 'v2_invisible') {
						// reCAPTCHA v2 Invisible
						const widgetId = $form.data('recaptcha-widget-id');
						if (widgetId) {
							grecaptcha.execute(widgetId).then(function (token) {
								const $tokenInput = $form.find('input[name="recaptcha_token"]');
								if ($tokenInput.length) {
									$tokenInput.val(token);
								}
								submitForm($form, formData, $message);
							});
						} else {
							submitForm($form, formData, $message);
						}
					} else {
						// reCAPTCHA v2 Checkbox - token is already set by user interaction
						const token = $form.find('input[name="recaptcha_token"]').val();
						if (token) {
							submitForm($form, formData, $message);
						} else {
							$message
								.addClass('error')
								.html('Please complete the reCAPTCHA verification.')
								.show();
						}
					}
				} else {
					submitForm($form, formData, $message);
				}
			} else {
				submitForm($form, formData, $message);
			}
		});
	}

	/**
	 * Submit form via AJAX
	 */
	function submitForm($form, formData, $message) {
		$message.removeClass('success error').hide();

		$.ajax({
			url: smartLoginizer.ajaxUrl,
			type: 'POST',
			data: formData + '&nonce=' + smartLoginizer.nonce,
			success: function (response) {
				if (response.success) {
					$message
						.addClass('success')
						.html(response.data.message || 'Success!')
						.show();

					// Redirect if provided
					if (response.data.redirect) {
						setTimeout(function () {
							window.location.href = response.data.redirect;
						}, 1000);
					} else {
						// Reset form after 2 seconds
						setTimeout(function () {
							$form[0].reset();
							$message.hide();
						}, 2000);
					}
				} else {
					$message
						.addClass('error')
						.html(response.data.message || 'An error occurred.')
						.show();
				}
			},
			error: function () {
				$message
					.addClass('error')
					.html('An error occurred. Please try again.')
					.show();
			},
		});
	}

	/**
	 * Initialize password strength meter
	 */
	function initPasswordStrength() {
		$('.smart-loginizer-password-strength input[name="password"]').on('input', function () {
			const $input = $(this);
			const $meter = $input
				.closest('.smart-loginizer-form-field')
				.find('.smart-loginizer-password-strength-meter');
			const password = $input.val();

			if (password.length === 0) {
				$meter.removeClass('weak fair good strong');
				return;
			}

			// Simple strength calculation
			let strength = 0;
			if (password.length >= 8) strength++;
			if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
			if (password.match(/\d/)) strength++;
			if (password.match(/[^a-zA-Z\d]/)) strength++;

			$meter.removeClass('weak fair good strong');

			if (strength <= 1) {
				$meter.addClass('weak');
			} else if (strength === 2) {
				$meter.addClass('fair');
			} else if (strength === 3) {
				$meter.addClass('good');
			} else {
				$meter.addClass('strong');
			}
		});
	}

	/**
	 * Initialize reCAPTCHA
	 */
	function initRecaptcha() {
		if (typeof smartLoginizer === 'undefined' || !smartLoginizer.enableRecaptcha || !smartLoginizer.recaptchaSiteKey) {
			return;
		}

		const version = smartLoginizer.recaptchaVersion || 'v3';

		if (version === 'v2_checkbox') {
			// Initialize v2 checkbox widgets - wait for grecaptcha to be ready
			if (typeof grecaptcha !== 'undefined') {
				grecaptcha.ready(function () {
					$('.smart-loginizer-recaptcha-v2').each(function () {
						const $container = $(this);
						if ($container.data('recaptcha-rendered')) {
							return; // Already rendered
						}
						const containerId = $container.attr('id');
						if (!containerId) {
							return;
						}
						try {
							const widgetId = grecaptcha.render(containerId, {
								'sitekey': smartLoginizer.recaptchaSiteKey,
								'callback': function (token) {
									// Token is set automatically, find the form and set it
									const $form = $container.closest('form');
									const $tokenInput = $form.find('input[name="recaptcha_token"]');
									if ($tokenInput.length) {
										$tokenInput.val(token);
									}
								},
								'expired-callback': function () {
									const $form = $container.closest('form');
									const $tokenInput = $form.find('input[name="recaptcha_token"]');
									if ($tokenInput.length) {
										$tokenInput.val('');
									}
								}
							});
							$container.data('recaptcha-rendered', true);
							$container.closest('form').data('recaptcha-widget-id', widgetId);
						} catch (e) {
							console.error('reCAPTCHA v2 checkbox render error:', e);
						}
					});
				});
			}
		} else if (version === 'v2_invisible') {
			// Initialize v2 invisible widgets
			if (typeof grecaptcha !== 'undefined') {
				grecaptcha.ready(function () {
					$('.smart-loginizer-recaptcha-v2-invisible').each(function () {
						const $container = $(this);
						if ($container.data('recaptcha-rendered')) {
							return; // Already rendered
						}
						const containerId = $container.attr('id');
						if (!containerId) {
							return;
						}
						const $form = $container.closest('form');
						try {
							const widgetId = grecaptcha.render(containerId, {
								'sitekey': smartLoginizer.recaptchaSiteKey,
								'size': 'invisible',
								'callback': function (token) {
									const $tokenInput = $form.find('input[name="recaptcha_token"]');
									if ($tokenInput.length) {
										$tokenInput.val(token);
									}
									// Auto-submit form if it was triggered
									if ($form.data('pending-submit')) {
										$form.data('pending-submit', false);
										$form.trigger('submit');
									}
								}
							});
							$container.data('recaptcha-rendered', true);
							$form.data('recaptcha-widget-id', widgetId);
						} catch (e) {
							console.error('reCAPTCHA v2 invisible render error:', e);
						}
					});
				});
			}
		}
		// v3 doesn't need initialization, it's handled on form submit
	}

	/**
	 * Initialize responsive form styles
	 */
	function initFormStyles() {
		function updateFormStyles() {
			// Find all form wrappers with form style data attributes (including hidden ones)
			$('.smart-loginizer-modal-body[data-form-style-desktop], .smart-loginizer-form-wrapper[data-form-style-desktop]').each(function () {
				const $body = $(this);
				// Use attr() instead of data() for hyphenated attributes
				const desktopStyle = $body.attr('data-form-style-desktop') || 'default';
				const tabletStyle = $body.attr('data-form-style-tablet') || desktopStyle;
				const mobileStyle = $body.attr('data-form-style-mobile') || desktopStyle;
				
				// Remove all form style classes (handle both underscore and hyphen variations)
				$body.removeClass(function (index, className) {
					return (className.match(/(^|\s)smart-loginizer-form-style-\S+/g) || []).join(' ');
				});
				
				// Determine current viewport
				const width = window.innerWidth || $(window).width();
				let currentStyle = desktopStyle;
				
				if (width <= 768) {
					currentStyle = mobileStyle;
				} else if (width <= 1024) {
					currentStyle = tabletStyle;
				}
				
				// Add appropriate class
				if (currentStyle) {
					$body.addClass('smart-loginizer-form-style-' + currentStyle);
				}
			});
		}
		
		// Update on load (with a small delay to ensure DOM is ready)
		setTimeout(updateFormStyles, 100);
		
		// Update on resize (with debounce)
		let resizeTimer;
		$(window).on('resize', function () {
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(updateFormStyles, 250);
		});
		
		// Store updateFormStyles function globally so it can be called from modal open
		window.smartLoginizerUpdateFormStyles = updateFormStyles;
		
		// Update when modal is opened
		$(document).on('click', '.smart-loginizer-modal-trigger', function () {
			setTimeout(updateFormStyles, 200);
		});
	}

	/**
	 * Apply open effect to modal
	 */
	function applyOpenEffect($modal, effect, duration) {
		const $content = $modal.find('.smart-loginizer-modal-content');
		
		// Set transition duration
		$modal.css({
			'transition-duration': duration + 'ms',
			'transition-timing-function': 'ease'
		});
		$content.css({
			'transition-duration': duration + 'ms',
			'transition-timing-function': 'ease',
			'transition-property': 'transform, opacity'
		});

		// Remove all previous effect classes
		$modal.removeClass('effect-fade effect-slide effect-zoom effect-flip effect-rotate effect-bounce effect-elastic effect-back');
		$content.removeClass('effect-fade effect-slide effect-zoom effect-flip effect-rotate effect-bounce effect-elastic effect-back');

		if (effect !== 'none') {
			// Add effect class before showing
			$modal.addClass('effect-' + effect);
			$content.addClass('effect-' + effect);
			
			// Force reflow to ensure initial state is applied
			$modal[0].offsetHeight;
		}
	}

	/**
	 * Apply close effect to modal
	 */
	function applyCloseEffect($modal, effect, duration, callback) {
		const $content = $modal.find('.smart-loginizer-modal-content');
		
		if (effect === 'none') {
			if (callback) callback();
			return;
		}

		// Ensure transition is set
		$modal.css({
			'transition-duration': duration + 'ms',
			'transition-timing-function': 'ease'
		});
		$content.css({
			'transition-duration': duration + 'ms',
			'transition-timing-function': 'ease',
			'transition-property': 'transform, opacity'
		});

		// Add closing class
		$modal.addClass('closing');
		$content.addClass('closing');

		// Remove active class after animation
		setTimeout(function () {
			if (callback) callback();
			$modal.removeClass('closing');
			$content.removeClass('closing');
		}, duration);
	}

	/**
	 * Initialize Auth Modal
	 */
	function initAuthModal() {
		// Open modal on trigger click
		$(document).on('click', '.smart-loginizer-modal-trigger', function (e) {
			e.preventDefault();
			const modalId = $(this).data('modal-id');
			if (modalId) {
				const $modal = $('#' + modalId);
				const openEffect = $modal.data('open-effect') || 'fade';
				const duration = parseInt($modal.data('animation-duration')) || 300;

				// Show modal first (display: flex)
				$modal.css('display', 'flex');
				
				// Apply open effect
				applyOpenEffect($modal, openEffect, duration);
				
				// Add active class after a tiny delay to trigger animation
				setTimeout(function() {
					$modal.addClass('active');
					$('body').css('overflow', 'hidden');
					// Update form styles when modal opens
					if (typeof window.smartLoginizerUpdateFormStyles === 'function') {
						window.smartLoginizerUpdateFormStyles();
					}
					// Re-initialize reCAPTCHA for modals (in case forms are dynamically loaded)
					if (typeof initRecaptcha === 'function') {
						setTimeout(initRecaptcha, 100);
					}
				}, 10);
			}
		});

		// Close modal
		$(document).on('click', '.smart-loginizer-modal-close, .smart-loginizer-modal-overlay', function (e) {
			// Don't close if clicking inside modal content
			if ($(e.target).hasClass('smart-loginizer-modal-overlay')) {
				const closeOnBg = $(e.target).data('close-bg');
				if (closeOnBg === 'no') {
					return;
				}
			} else if (!$(e.target).closest('.smart-loginizer-modal-content').length && !$(e.target).hasClass('smart-loginizer-modal-close')) {
				return;
			}

			const $modal = $(this).closest('.smart-loginizer-modal-overlay');
			if (!$modal.hasClass('active')) {
				return;
			}

			const closeEffect = $modal.data('close-effect') || 'fade';
			const duration = parseInt($modal.data('animation-duration')) || 300;

			// Remove active class first to trigger closing animation
			$modal.removeClass('active');
			
			// Apply close effect
			applyCloseEffect($modal, closeEffect, duration, function () {
				$modal.css('display', 'none');
				$('body').css('overflow', '');
			});
		});

		// Close on ESC key
		$(document).on('keydown', function (e) {
			if (e.key === 'Escape' || e.keyCode === 27) {
				$('.smart-loginizer-modal-overlay.active').each(function () {
					const closeOnEscape = $(this).data('close-escape');
					if (closeOnEscape !== 'no') {
						const $modal = $(this);
						const closeEffect = $modal.data('close-effect') || 'fade';
						const duration = parseInt($modal.data('animation-duration')) || 300;

						// Remove active class first
						$modal.removeClass('active');
						
						// Apply close effect
						applyCloseEffect($modal, closeEffect, duration, function () {
							$modal.css('display', 'none');
							$('body').css('overflow', '');
						});
					}
				});
			}
		});

		// Form switching via links (for both modal and inline forms)
		$(document).on('click', '.smart-loginizer-form-link', function (e) {
			e.preventDefault();
			const targetPanel = $(this).data('switch-to');
			if (targetPanel) {
				// Check if it's in a modal or inline form
				const $container = $(this).closest('.smart-loginizer-modal-content, .smart-loginizer-auth-form-container');
				
				// Update active panel (works for both modal and inline)
				$container.find('.smart-loginizer-modal-panel, .smart-loginizer-auth-form-panel').removeClass('active');
				$container.find('[data-panel="' + targetPanel + '"]').addClass('active');
				
				// Re-initialize reCAPTCHA after panel switch (in case forms are dynamically shown)
				if (typeof initRecaptcha === 'function') {
					setTimeout(initRecaptcha, 100);
				}
			}
		});

		// Prevent closing when clicking inside modal content
		$(document).on('click', '.smart-loginizer-modal-content', function (e) {
			e.stopPropagation();
		});
	}

	/**
	 * Initialize social button spacers to match icon width
	 */
	function initSocialButtonSpacers() {
		$('.smart-loginizer-social-btn').each(function() {
			const $button = $(this);
			const $icon = $button.find('.smart-loginizer-icon-left');
			const $spacer = $button.find('.smart-loginizer-social-icon-spacer');
			
			if ($icon.length && $spacer.length) {
				// Get the computed width of the icon
				const iconWidth = $icon.outerWidth();
				if (iconWidth > 0) {
					$spacer.css('width', iconWidth + 'px');
				}
			}
		});
	}

	/**
	 * Initialize social login
	 */
	function initSocialLogin() {
		$(document).on('click', '.smart-loginizer-social-btn', function (e) {
			e.preventDefault();
			
			const $btn = $(this);
			const provider = $btn.data('provider');
			
			if (!provider) {
				return;
			}
			
			// Disable button during request
			$btn.prop('disabled', true);
			
			// Get OAuth URL via AJAX
			$.ajax({
				url: smartLoginizer.ajaxUrl,
				type: 'POST',
				data: {
					action: 'smart_loginizer_oauth_init',
					nonce: smartLoginizer.nonce,
					provider: provider,
				},
				success: function (response) {
					if (response.success && response.data.auth_url) {
						// Redirect to OAuth provider
						window.location.href = response.data.auth_url;
					} else {
						alert(response.data.message || 'OAuth is not configured.');
						$btn.prop('disabled', false);
					}
				},
				error: function () {
					alert('An error occurred. Please try again.');
					$btn.prop('disabled', false);
				},
			});
		});
	}

	// Initialize modal
	initAuthModal();

	/**
	 * Initialize password visibility toggle
	 */
	function initPasswordToggle() {
		$(document).on('click', '.smart-loginizer-password-toggle', function(e) {
			e.preventDefault();
			const $button = $(this);
			const $eyeIcon = $button.find('.smart-loginizer-eye-icon');
			const $eyeSlashIcon = $button.find('.smart-loginizer-eye-slash-icon');
			const targetId = $button.data('target');
			const $input = $('#' + targetId);

			if ($input.length) {
				if ($input.attr('type') === 'password') {
					$input.attr('type', 'text');
					$eyeIcon.hide();
					$eyeSlashIcon.show();
					$button.attr('aria-label', 'Hide password');
				} else {
					$input.attr('type', 'password');
					$eyeIcon.show();
					$eyeSlashIcon.hide();
					$button.attr('aria-label', 'Show password');
				}
			}
		});

		// Re-initialize for dynamically loaded content (modals, form switches)
		$(document).on('elementor/popup/show', function() {
			// Toggle buttons are already initialized via event delegation
		});
	}
})(jQuery);


