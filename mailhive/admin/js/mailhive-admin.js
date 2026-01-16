/**
 * MailHive Admin JavaScript
 *
 * @package MailHive
 */

(function($) {
    'use strict';

    /**
     * MailHive Admin object
     */
    var MailHiveAdmin = {

        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.initFormBuilder();
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            // Subscribers page
            $(document).on('click', '.mailhive-delete-subscriber', this.deleteSubscriber);
            $(document).on('click', '#mailhive-bulk-apply', this.bulkAction);
            $(document).on('change', '#cb-select-all', this.toggleSelectAll);

            // Form builder page
            $(document).on('click', '.mailhive-add-field', this.addField);
            $(document).on('click', '#mailhive-save-form', this.saveForm);
            $(document).on('click', '.mailhive-reset-markup', this.resetMarkup);
            $(document).on('click', '.mailhive-apply-style', this.applyStyle);
            $(document).on('click', '.mailhive-preview-success', this.previewSuccess);
            $(document).on('click', '.mailhive-preview-error', this.previewError);
            $(document).on('click', '.mailhive-copy-shortcode', this.copyShortcode);

            // Live preview
            $(document).on('input', '#mailhive-form-markup', this.updatePreview);
            $(document).on('input', '#mailhive-form-css', this.updatePreviewCSS);
        },

        /**
         * Initialize form builder
         */
        initFormBuilder: function() {
            if ($('#mailhive-form-markup').length) {
                this.updatePreview();
            }
        },

        /**
         * Delete subscriber
         */
        deleteSubscriber: function(e) {
            e.preventDefault();

            var $button = $(this);
            var id = $button.data('id');

            if (!confirm(mailhive_admin.confirm_delete)) {
                return;
            }

            $button.addClass('loading').prop('disabled', true);

            $.ajax({
                url: mailhive_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'mailhive_delete_subscriber',
                    nonce: mailhive_admin.nonce,
                    id: id
                },
                success: function(response) {
                    if (response.success) {
                        $button.closest('tr').fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        alert(response.data.message || mailhive_admin.error);
                        $button.removeClass('loading').prop('disabled', false);
                    }
                },
                error: function() {
                    alert(mailhive_admin.error);
                    $button.removeClass('loading').prop('disabled', false);
                }
            });
        },

        /**
         * Bulk action
         */
        bulkAction: function(e) {
            e.preventDefault();

            var action = $('#bulk-action-selector').val();
            var ids = [];

            $('input[name="subscriber_ids[]"]:checked').each(function() {
                ids.push($(this).val());
            });

            if (!action) {
                alert('Please select an action.');
                return;
            }

            if (ids.length === 0) {
                alert('Please select at least one subscriber.');
                return;
            }

            if (!confirm(mailhive_admin.confirm_bulk)) {
                return;
            }

            var $button = $(this);
            $button.addClass('loading').prop('disabled', true);

            $.ajax({
                url: mailhive_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'mailhive_bulk_action',
                    nonce: mailhive_admin.nonce,
                    bulk_action: action,
                    ids: ids
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || mailhive_admin.error);
                        $button.removeClass('loading').prop('disabled', false);
                    }
                },
                error: function() {
                    alert(mailhive_admin.error);
                    $button.removeClass('loading').prop('disabled', false);
                }
            });
        },

        /**
         * Toggle select all checkboxes
         */
        toggleSelectAll: function() {
            var isChecked = $(this).prop('checked');
            $('input[name="subscriber_ids[]"]').prop('checked', isChecked);
        },

        /**
         * Add field to markup
         */
        addField: function(e) {
            e.preventDefault();

            var fieldType = $(this).data('field');
            var template = $('#tmpl-mailhive-field-' + fieldType).html();

            if (!template) {
                return;
            }

            var $textarea = $('#mailhive-form-markup');
            var cursorPos = $textarea[0].selectionStart;
            var textBefore = $textarea.val().substring(0, cursorPos);
            var textAfter = $textarea.val().substring(cursorPos);

            // Add newlines for proper formatting
            var newValue = textBefore + (textBefore.length > 0 ? '\n' : '') + template.trim() + (textAfter.length > 0 ? '\n' : '') + textAfter;

            $textarea.val(newValue);
            MailHiveAdmin.updatePreview();

            // Focus back to textarea
            $textarea.focus();
        },

        /**
         * Save form
         */
        saveForm: function(e) {
            e.preventDefault();

            var $button = $(this);
            var $status = $('.mailhive-save-status');
            var markup = $('#mailhive-form-markup').val();
            var css = $('#mailhive-form-css').val();

            $button.prop('disabled', true);
            $status.text(mailhive_admin.saving);

            $.ajax({
                url: mailhive_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'mailhive_save_form',
                    nonce: mailhive_admin.nonce,
                    markup: markup,
                    css: css
                },
                success: function(response) {
                    if (response.success) {
                        $status.text(mailhive_admin.saved);
                        setTimeout(function() {
                            $status.text('');
                        }, 3000);
                    } else {
                        $status.text(response.data.message || mailhive_admin.error);
                    }
                    $button.prop('disabled', false);
                },
                error: function() {
                    $status.text(mailhive_admin.error);
                    $button.prop('disabled', false);
                }
            });
        },

        /**
         * Reset markup to default
         */
        resetMarkup: function(e) {
            e.preventDefault();

            if (!confirm('Are you sure you want to reset the form to default?')) {
                return;
            }

            var defaultMarkup = $('#tmpl-mailhive-default-markup').html();
            $('#mailhive-form-markup').val(defaultMarkup);
            $('#mailhive-form-css').val('');
            MailHiveAdmin.updatePreview();
            MailHiveAdmin.updatePreviewCSS();
        },

        /**
         * Apply style option
         */
        applyStyle: function(e) {
            e.preventDefault();

            var styleType = $(this).data('style');
            var $cssTextarea = $('#mailhive-form-css');
            var currentCSS = $cssTextarea.val();
            var newCSS = '';

            switch (styleType) {
                case 'width':
                    var width = $('#mailhive-form-width').val();
                    if (width) {
                        newCSS = '.mailhive-form-wrapper { max-width: ' + width + '; }';
                    }
                    break;
                case 'button-color':
                    var btnColor = $('#mailhive-btn-color').val();
                    newCSS = '.mailhive-submit { background-color: ' + btnColor + ' !important; }';
                    break;
                case 'button-text-color':
                    var btnTextColor = $('#mailhive-btn-text-color').val();
                    newCSS = '.mailhive-submit { color: ' + btnTextColor + ' !important; }';
                    break;
                case 'border-radius':
                    var radius = $('#mailhive-border-radius').val();
                    if (radius) {
                        newCSS = '.mailhive-field input, .mailhive-field textarea, .mailhive-field select, .mailhive-submit { border-radius: ' + radius + ' !important; }';
                    }
                    break;
            }

            if (newCSS) {
                if (currentCSS.trim()) {
                    currentCSS += '\n';
                }
                $cssTextarea.val(currentCSS + newCSS);
                MailHiveAdmin.updatePreviewCSS();
            }
        },

        /**
         * Update live preview
         */
        updatePreview: function() {
            var markup = $('#mailhive-form-markup').val();
            $('#mailhive-preview .mailhive-form').html(markup);
            $('#mailhive-preview .mailhive-message').hide().removeClass('mailhive-success mailhive-error');
        },

        /**
         * Update preview CSS
         */
        updatePreviewCSS: function() {
            var css = $('#mailhive-form-css').val();
            $('#mailhive-preview-css').html(css);
        },

        /**
         * Preview success message
         */
        previewSuccess: function(e) {
            e.preventDefault();
            var $message = $('#mailhive-preview .mailhive-message');
            $message
                .removeClass('mailhive-error')
                .addClass('mailhive-success')
                .text('Thank you for subscribing!')
                .show();
        },

        /**
         * Preview error message
         */
        previewError: function(e) {
            e.preventDefault();
            var $message = $('#mailhive-preview .mailhive-message');
            $message
                .removeClass('mailhive-success')
                .addClass('mailhive-error')
                .text('This email is already subscribed.')
                .show();
        },

        /**
         * Copy shortcode to clipboard
         */
        copyShortcode: function(e) {
            e.preventDefault();

            var $button = $(this);
            var text = $('#mailhive-shortcode').text();

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function() {
                    var originalText = $button.text();
                    $button.text('Copied!');
                    setTimeout(function() {
                        $button.text(originalText);
                    }, 2000);
                });
            } else {
                // Fallback for older browsers
                var $temp = $('<textarea>');
                $('body').append($temp);
                $temp.val(text).select();
                document.execCommand('copy');
                $temp.remove();

                var originalText = $button.text();
                $button.text('Copied!');
                setTimeout(function() {
                    $button.text(originalText);
                }, 2000);
            }
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        MailHiveAdmin.init();
    });

})(jQuery);
