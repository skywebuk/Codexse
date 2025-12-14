/**
 * Bazaar Frontend JavaScript
 *
 * @package Bazaar
 */

(function($) {
    'use strict';

    var BazaarFrontend = {
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.initSelect2();
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            // Follow vendor
            $(document).on('click', '.bazaar-follow-btn', this.followVendor);

            // Contact vendor
            $(document).on('click', '.bazaar-contact-btn', this.openContactModal);
            $(document).on('submit', '#bazaar-contact-form', this.sendContactMessage);

            // Share
            $(document).on('click', '.share-btn', this.toggleShareDropdown);
            $(document).on('click', '.copy-link', this.copyLink);
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.store-share').length) {
                    $('.share-dropdown').hide();
                }
            });

            // Reviews
            $(document).on('click', '.open-review-form', this.showReviewForm);
            $(document).on('click', '.cancel-review', this.hideReviewForm);
            $(document).on('submit', '#bazaar-vendor-review-form', this.submitReview);
            $(document).on('click', '.helpful-btn', this.markReviewHelpful);
            $(document).on('click', '.report-btn', this.reportReview);

            // View toggle
            $(document).on('click', '.view-toggle button', this.toggleView);

            // Policy accordion
            $(document).on('click', '.policy-toggle', this.togglePolicy);

            // Vendor registration
            $(document).on('submit', '#bazaar-vendor-registration-form', this.submitRegistration);
            $(document).on('change', '#payment_method', this.togglePaymentFields);
            $(document).on('input', '#store_url', this.validateStoreUrl);
        },

        /**
         * Initialize Select2
         */
        initSelect2: function() {
            if ($.fn.select2) {
                $('.bazaar-select2').select2({
                    width: '100%',
                    placeholder: function() {
                        return $(this).data('placeholder') || 'Select...';
                    }
                });
            }
        },

        /**
         * Follow vendor
         */
        followVendor: function(e) {
            e.preventDefault();
            var $btn = $(this);
            var vendorId = $btn.data('vendor-id');
            var isFollowing = $btn.hasClass('following');

            $btn.prop('disabled', true);

            $.ajax({
                url: bazaarFrontend.ajaxUrl,
                type: 'POST',
                data: {
                    action: isFollowing ? 'bazaar_unfollow_vendor' : 'bazaar_follow_vendor',
                    vendor_id: vendorId,
                    nonce: bazaarFrontend.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $btn.toggleClass('following');
                        if ($btn.hasClass('following')) {
                            $btn.find('.follow-text').text(bazaarFrontend.i18n.following);
                        } else {
                            $btn.find('.follow-text').text(bazaarFrontend.i18n.follow);
                        }
                    } else {
                        alert(response.data.message || bazaarFrontend.i18n.error);
                    }
                    $btn.prop('disabled', false);
                },
                error: function() {
                    alert(bazaarFrontend.i18n.error);
                    $btn.prop('disabled', false);
                }
            });
        },

        /**
         * Open contact modal
         */
        openContactModal: function(e) {
            e.preventDefault();
            var vendorId = $(this).data('vendor-id');
            $('#bazaar-contact-form input[name="vendor_id"]').val(vendorId);
            $('.bazaar-contact-modal').fadeIn();
        },

        /**
         * Send contact message
         */
        sendContactMessage: function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');

            $btn.prop('disabled', true).text(bazaarFrontend.i18n.sending);

            $.ajax({
                url: bazaarFrontend.ajaxUrl,
                type: 'POST',
                data: $form.serialize() + '&action=bazaar_contact_vendor&nonce=' + bazaarFrontend.nonce,
                success: function(response) {
                    if (response.success) {
                        alert(bazaarFrontend.i18n.messageSent);
                        $form[0].reset();
                        $('.bazaar-contact-modal').fadeOut();
                    } else {
                        alert(response.data.message || bazaarFrontend.i18n.error);
                    }
                    $btn.prop('disabled', false).text(bazaarFrontend.i18n.send);
                },
                error: function() {
                    alert(bazaarFrontend.i18n.error);
                    $btn.prop('disabled', false).text(bazaarFrontend.i18n.send);
                }
            });
        },

        /**
         * Toggle share dropdown
         */
        toggleShareDropdown: function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).siblings('.share-dropdown').toggle();
        },

        /**
         * Copy link
         */
        copyLink: function(e) {
            e.preventDefault();
            var url = $(this).data('url');

            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(function() {
                    alert(bazaarFrontend.i18n.linkCopied);
                });
            } else {
                var $temp = $('<input>');
                $('body').append($temp);
                $temp.val(url).select();
                document.execCommand('copy');
                $temp.remove();
                alert(bazaarFrontend.i18n.linkCopied);
            }

            $('.share-dropdown').hide();
        },

        /**
         * Show review form
         */
        showReviewForm: function(e) {
            e.preventDefault();
            $('.review-form-wrapper').slideDown();
            $('html, body').animate({
                scrollTop: $('.review-form-wrapper').offset().top - 100
            }, 500);
        },

        /**
         * Hide review form
         */
        hideReviewForm: function(e) {
            e.preventDefault();
            $('.review-form-wrapper').slideUp();
        },

        /**
         * Submit review
         */
        submitReview: function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');

            if (!$form.find('input[name="rating"]:checked').length) {
                alert(bazaarFrontend.i18n.selectRating);
                return;
            }

            $btn.prop('disabled', true).text(bazaarFrontend.i18n.submitting);

            $.ajax({
                url: bazaarFrontend.ajaxUrl,
                type: 'POST',
                data: $form.serialize() + '&action=bazaar_submit_vendor_review&nonce=' + bazaarFrontend.nonce,
                success: function(response) {
                    if (response.success) {
                        alert(bazaarFrontend.i18n.reviewSubmitted);
                        location.reload();
                    } else {
                        alert(response.data.message || bazaarFrontend.i18n.error);
                        $btn.prop('disabled', false).text(bazaarFrontend.i18n.submitReview);
                    }
                },
                error: function() {
                    alert(bazaarFrontend.i18n.error);
                    $btn.prop('disabled', false).text(bazaarFrontend.i18n.submitReview);
                }
            });
        },

        /**
         * Mark review as helpful
         */
        markReviewHelpful: function(e) {
            e.preventDefault();
            var $btn = $(this);
            var reviewId = $btn.data('review-id');

            if ($btn.hasClass('voted')) {
                return;
            }

            $.ajax({
                url: bazaarFrontend.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bazaar_mark_review_helpful',
                    review_id: reviewId,
                    nonce: bazaarFrontend.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $btn.addClass('voted');
                        var count = parseInt($btn.find('.helpful-count').text().replace(/[()]/g, '')) || 0;
                        $btn.find('.helpful-count').text('(' + (count + 1) + ')');
                    }
                }
            });
        },

        /**
         * Report review
         */
        reportReview: function(e) {
            e.preventDefault();
            var reason = prompt(bazaarFrontend.i18n.reportReason);
            if (!reason) {
                return;
            }

            var reviewId = $(this).data('review-id');

            $.ajax({
                url: bazaarFrontend.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bazaar_report_review',
                    review_id: reviewId,
                    reason: reason,
                    nonce: bazaarFrontend.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert(bazaarFrontend.i18n.reviewReported);
                    } else {
                        alert(response.data.message || bazaarFrontend.i18n.error);
                    }
                }
            });
        },

        /**
         * Toggle view (grid/list)
         */
        toggleView: function(e) {
            e.preventDefault();
            var view = $(this).data('view');

            $('.view-toggle button').removeClass('active');
            $(this).addClass('active');

            if (view === 'list') {
                $('.bazaar-products-grid').addClass('list-view');
            } else {
                $('.bazaar-products-grid').removeClass('list-view');
            }
        },

        /**
         * Toggle policy accordion
         */
        togglePolicy: function(e) {
            e.preventDefault();
            var $item = $(this).closest('.policy-item');
            var $content = $item.find('.policy-content');

            $item.toggleClass('active');
            $content.slideToggle();
        },

        /**
         * Submit vendor registration
         */
        submitRegistration: function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');

            // Validate password match
            var password = $form.find('#password').val();
            var confirmPassword = $form.find('#confirm_password').val();

            if (password && password !== confirmPassword) {
                alert(bazaarFrontend.i18n.passwordMismatch);
                return;
            }

            $btn.prop('disabled', true).text(bazaarFrontend.i18n.submitting);

            $.ajax({
                url: bazaarFrontend.ajaxUrl,
                type: 'POST',
                data: $form.serialize() + '&action=bazaar_vendor_registration&nonce=' + bazaarFrontend.nonce,
                success: function(response) {
                    if (response.success) {
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                        } else {
                            alert(response.data.message || bazaarFrontend.i18n.registrationSuccess);
                            location.reload();
                        }
                    } else {
                        alert(response.data.message || bazaarFrontend.i18n.error);
                        $btn.prop('disabled', false).text(bazaarFrontend.i18n.submitApplication);
                    }
                },
                error: function() {
                    alert(bazaarFrontend.i18n.error);
                    $btn.prop('disabled', false).text(bazaarFrontend.i18n.submitApplication);
                }
            });
        },

        /**
         * Toggle payment fields
         */
        togglePaymentFields: function() {
            var method = $(this).val();

            $('.payment-fields').hide();

            if (method === 'paypal') {
                $('.paypal-fields').show();
            } else if (method === 'bank_transfer') {
                $('.bank-fields').show();
            }
        },

        /**
         * Validate store URL
         */
        validateStoreUrl: function() {
            var $input = $(this);
            var value = $input.val().toLowerCase();

            // Remove invalid characters
            value = value.replace(/[^a-z0-9-]/g, '');
            $input.val(value);
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        BazaarFrontend.init();
    });

})(jQuery);
