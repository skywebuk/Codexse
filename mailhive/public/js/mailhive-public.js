/**
 * MailHive Public JavaScript
 *
 * @package MailHive
 */

(function($) {
    'use strict';

    /**
     * MailHive Public object
     */
    var MailHive = {

        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.addHoneypot();
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            $(document).on('submit', '.mailhive-form', this.handleSubmit);
        },

        /**
         * Add honeypot field to forms
         */
        addHoneypot: function() {
            $('.mailhive-form').each(function() {
                if (!$(this).find('.mailhive-hp').length) {
                    $(this).append(
                        '<div class="mailhive-hp" aria-hidden="true">' +
                        '<input type="text" name="mailhive_hp" tabindex="-1" autocomplete="off">' +
                        '</div>'
                    );
                }
            });
        },

        /**
         * Handle form submission
         *
         * @param {Event} e Submit event
         */
        handleSubmit: function(e) {
            e.preventDefault();

            var $form = $(this);
            var $wrapper = $form.closest('.mailhive-form-wrapper');
            var $message = $wrapper.find('.mailhive-message');
            var $submit = $form.find('.mailhive-submit, button[type="submit"], input[type="submit"]');

            // Prevent double submission
            if ($form.data('submitting')) {
                return;
            }

            // Validate email
            var email = $form.find('input[name="email"]').val();
            if (!email || !MailHive.isValidEmail(email)) {
                MailHive.showMessage($message, 'Please enter a valid email address.', 'error');
                return;
            }

            // Set submitting state
            $form.data('submitting', true);
            $submit.addClass('loading').prop('disabled', true);

            // Hide previous message
            $message.hide().removeClass('mailhive-success mailhive-error mailhive-warning');

            // Collect form data
            var formData = $form.serializeArray();
            formData.push({ name: 'action', value: 'mailhive_subscribe' });
            formData.push({ name: 'nonce', value: mailhive_params.nonce });

            // Send AJAX request
            $.ajax({
                url: mailhive_params.ajax_url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        MailHive.showMessage($message, response.data.message, 'success');
                        $form[0].reset();

                        // Trigger custom event for integrations
                        $(document).trigger('mailhive:subscribed', [response.data, $form]);
                    } else {
                        var type = response.data.type === 'duplicate' ? 'warning' : 'error';
                        MailHive.showMessage($message, response.data.message, type);

                        // Trigger custom event for integrations
                        $(document).trigger('mailhive:error', [response.data, $form]);
                    }
                },
                error: function(xhr, status, error) {
                    var message = 'An error occurred. Please try again.';

                    if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                        message = xhr.responseJSON.data.message;
                    }

                    MailHive.showMessage($message, message, 'error');

                    // Trigger custom event for integrations
                    $(document).trigger('mailhive:error', [{ message: message }, $form]);
                },
                complete: function() {
                    $form.data('submitting', false);
                    $submit.removeClass('loading').prop('disabled', false);
                }
            });
        },

        /**
         * Show message
         *
         * @param {jQuery} $element Message element
         * @param {string} text Message text
         * @param {string} type Message type (success, error, warning)
         */
        showMessage: function($element, text, type) {
            $element
                .removeClass('mailhive-success mailhive-error mailhive-warning')
                .addClass('mailhive-' + type)
                .text(text)
                .hide()
                .fadeIn(300);

            // Scroll to message if not visible
            if (!MailHive.isInViewport($element[0])) {
                $('html, body').animate({
                    scrollTop: $element.offset().top - 100
                }, 300);
            }

            // Auto-hide success messages after a delay
            if (type === 'success') {
                setTimeout(function() {
                    $element.fadeOut(300);
                }, 5000);
            }
        },

        /**
         * Validate email address
         *
         * @param {string} email Email address to validate
         * @return {boolean}
         */
        isValidEmail: function(email) {
            var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        },

        /**
         * Check if element is in viewport
         *
         * @param {Element} element DOM element
         * @return {boolean}
         */
        isInViewport: function(element) {
            var rect = element.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        MailHive.init();
    });

    // Expose to global scope for custom integrations
    window.MailHive = MailHive;

})(jQuery);
