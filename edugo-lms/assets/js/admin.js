/**
 * Edugo LMS Admin Scripts
 *
 * @package Edugo_LMS
 */

(function($) {
    'use strict';

    /**
     * Admin module
     */
    const EdugoAdmin = {

        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.initQuestionBuilder();
            this.initMetaboxes();
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            // Confirm delete actions
            $(document).on('click', '.edugo-confirm-delete', this.confirmDelete);

            // AJAX form submissions
            $(document).on('submit', '.edugo-ajax-form', this.handleAjaxForm);

            // Lesson ordering
            if ($('#edugo-lessons-sortable').length) {
                this.initSortable();
            }
        },

        /**
         * Confirm delete
         */
        confirmDelete: function(e) {
            if (!confirm(edugoAdmin.i18n.confirmDelete)) {
                e.preventDefault();
                return false;
            }
        },

        /**
         * Handle AJAX form submission
         */
        handleAjaxForm: function(e) {
            e.preventDefault();

            const $form = $(this);
            const $submitBtn = $form.find('[type="submit"]');
            const originalText = $submitBtn.text();

            $submitBtn.prop('disabled', true).text(edugoAdmin.i18n.saving);

            $.ajax({
                url: edugoAdmin.ajaxUrl,
                type: 'POST',
                data: $form.serialize(),
                success: function(response) {
                    if (response.success) {
                        EdugoAdmin.showNotice('success', edugoAdmin.i18n.saved);
                    } else {
                        EdugoAdmin.showNotice('error', response.data.message || edugoAdmin.i18n.error);
                    }
                },
                error: function() {
                    EdugoAdmin.showNotice('error', edugoAdmin.i18n.error);
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).text(originalText);
                }
            });
        },

        /**
         * Initialize sortable for lessons
         */
        initSortable: function() {
            $('#edugo-lessons-sortable').sortable({
                handle: '.edugo-drag-handle',
                update: function(event, ui) {
                    const order = $(this).sortable('toArray', { attribute: 'data-lesson-id' });
                    EdugoAdmin.saveOrder(order);
                }
            });
        },

        /**
         * Save order via AJAX
         */
        saveOrder: function(order) {
            $.ajax({
                url: edugoAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'edugo_save_lesson_order',
                    nonce: edugoAdmin.nonce,
                    order: order
                },
                success: function(response) {
                    if (response.success) {
                        EdugoAdmin.showNotice('success', 'Order saved');
                    }
                }
            });
        },

        /**
         * Initialize question builder
         */
        initQuestionBuilder: function() {
            // Add option
            $(document).on('click', '.edugo-add-option', function(e) {
                e.preventDefault();
                const $container = $(this).siblings('.edugo-options-container');
                const index = $container.find('.edugo-option-row').length;
                const template = `
                    <div class="edugo-option-row">
                        <input type="radio" name="_edugo_correct_option" value="${index}">
                        <input type="text" name="_edugo_options[]" placeholder="Option ${index + 1}">
                        <button type="button" class="button edugo-remove-option">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </div>
                `;
                $container.append(template);
            });

            // Remove option
            $(document).on('click', '.edugo-remove-option', function(e) {
                e.preventDefault();
                $(this).closest('.edugo-option-row').remove();
            });

            // Toggle question type fields
            $(document).on('change', '#edugo_question_type', function() {
                const type = $(this).val();
                $('.edugo-question-type-fields').hide();
                $(`.edugo-question-type-${type}`).show();
            });
        },

        /**
         * Initialize metaboxes
         */
        initMetaboxes: function() {
            // Course selector
            if ($('.edugo-course-selector').length) {
                $('.edugo-course-selector').select2({
                    placeholder: 'Select a course',
                    allowClear: true
                });
            }

            // Date picker
            if ($('.edugo-datepicker').length) {
                $('.edugo-datepicker').datepicker({
                    dateFormat: 'yy-mm-dd'
                });
            }

            // Media uploader
            $(document).on('click', '.edugo-upload-media', function(e) {
                e.preventDefault();
                const $button = $(this);
                const $input = $button.siblings('.edugo-media-input');
                const $preview = $button.siblings('.edugo-media-preview');

                const frame = wp.media({
                    title: 'Select Media',
                    button: { text: 'Use this media' },
                    multiple: false
                });

                frame.on('select', function() {
                    const attachment = frame.state().get('selection').first().toJSON();
                    $input.val(attachment.id);
                    $preview.html(`<img src="${attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url}" alt="">`);
                });

                frame.open();
            });

            // Remove media
            $(document).on('click', '.edugo-remove-media', function(e) {
                e.preventDefault();
                const $button = $(this);
                $button.siblings('.edugo-media-input').val('');
                $button.siblings('.edugo-media-preview').html('');
            });
        },

        /**
         * Show notice
         */
        showNotice: function(type, message) {
            const $notice = $(`
                <div class="notice notice-${type} is-dismissible">
                    <p>${message}</p>
                </div>
            `);

            $('.wrap > h1').after($notice);

            setTimeout(function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        EdugoAdmin.init();
    });

})(jQuery);
