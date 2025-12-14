/**
 * Bazaar Vendor Dashboard JavaScript
 *
 * @package Bazaar
 */

(function($) {
    'use strict';

    var BazaarDashboard = {
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.initSelect2();
            this.initMediaUploader();
            this.initCharts();
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            // Products
            $(document).on('click', '.bazaar-add-product-btn', this.showProductForm);
            $(document).on('click', '.edit-product', this.editProduct);
            $(document).on('click', '.delete-product', this.deleteProduct);
            $(document).on('submit', '#bazaar-product-form', this.saveProduct);
            $(document).on('click', '.bazaar-cancel-product', this.hideProductForm);

            // Withdrawals
            $(document).on('click', '.bazaar-request-withdrawal-btn', this.showWithdrawalModal);
            $(document).on('submit', '#bazaar-withdrawal-form', this.requestWithdrawal);
            $(document).on('click', '.cancel-withdrawal', this.cancelWithdrawal);

            // Coupons
            $(document).on('click', '.bazaar-add-coupon-btn', this.showCouponForm);
            $(document).on('click', '.edit-coupon', this.editCoupon);
            $(document).on('click', '.delete-coupon', this.deleteCoupon);
            $(document).on('submit', '#bazaar-coupon-form', this.saveCoupon);
            $(document).on('click', '.bazaar-cancel-coupon', this.hideCouponForm);
            $(document).on('click', '.generate-code', this.generateCouponCode);
            $(document).on('click', '.copy-code', this.copyCouponCode);

            // Reviews
            $(document).on('click', '.reply-btn', this.showReplyForm);
            $(document).on('click', '.cancel-reply', this.hideReplyForm);
            $(document).on('submit', '.reply-form', this.submitReply);

            // Shipping
            $(document).on('click', '.bazaar-add-shipping-zone-btn', this.showShippingZoneForm);
            $(document).on('click', '.bazaar-cancel-zone', this.hideShippingZoneForm);
            $(document).on('submit', '#bazaar-shipping-zone-form', this.saveShippingZone);
            $(document).on('click', '.add-method-btn', this.showMethodModal);
            $(document).on('submit', '#bazaar-shipping-method-form', this.saveShippingMethod);
            $(document).on('change', '#method_type', this.toggleMethodFields);

            // Settings
            $(document).on('submit', '#bazaar-settings-form', this.saveSettings);
            $(document).on('change', '#payment_method', this.togglePaymentFields);

            // Modal
            $(document).on('click', '.modal-close, .modal-cancel', this.closeModal);
            $(document).on('click', '.bazaar-modal', this.closeModalOnOverlay);

            // Image upload
            $(document).on('click', '.upload-image-btn', this.uploadImage);
            $(document).on('click', '.remove-image-btn', this.removeImage);
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

                // Product search
                $('.bazaar-product-search').select2({
                    width: '100%',
                    placeholder: 'Search products...',
                    ajax: {
                        url: bazaarDashboard.ajaxUrl,
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                action: 'bazaar_search_products',
                                search: params.term,
                                nonce: bazaarDashboard.nonce
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.data || []
                            };
                        }
                    },
                    minimumInputLength: 2
                });
            }
        },

        /**
         * Initialize media uploader
         */
        initMediaUploader: function() {
            this.mediaUploader = null;
        },

        /**
         * Initialize charts
         */
        initCharts: function() {
            if (typeof Chart === 'undefined') {
                return;
            }

            var earningsChart = document.getElementById('earnings-chart');
            if (earningsChart && typeof bazaarDashboard.earningsData !== 'undefined') {
                new Chart(earningsChart, {
                    type: 'line',
                    data: {
                        labels: bazaarDashboard.earningsData.labels || [],
                        datasets: [{
                            label: bazaarDashboard.i18n.earnings,
                            data: bazaarDashboard.earningsData.values || [],
                            borderColor: '#11998e',
                            backgroundColor: 'rgba(17, 153, 142, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        },

        /**
         * Show product form
         */
        showProductForm: function(e) {
            e.preventDefault();
            $('#bazaar-product-form')[0].reset();
            $('input[name="product_id"]').val('');
            $('.bazaar-product-form .form-title').text(bazaarDashboard.i18n.addProduct);
            $('.bazaar-product-form').slideDown();
        },

        /**
         * Hide product form
         */
        hideProductForm: function(e) {
            e.preventDefault();
            $('.bazaar-product-form').slideUp();
        },

        /**
         * Edit product
         */
        editProduct: function(e) {
            e.preventDefault();
            var productId = $(this).data('product-id');

            $.ajax({
                url: bazaarDashboard.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bazaar_get_product',
                    product_id: productId,
                    nonce: bazaarDashboard.nonce
                },
                success: function(response) {
                    if (response.success) {
                        var product = response.data;
                        $('#bazaar-product-form input[name="product_id"]').val(product.id);
                        $('#bazaar-product-form input[name="product_name"]').val(product.name);
                        $('#bazaar-product-form input[name="regular_price"]').val(product.regular_price);
                        $('#bazaar-product-form input[name="sale_price"]').val(product.sale_price);
                        $('#bazaar-product-form textarea[name="short_description"]').val(product.short_description);
                        $('#bazaar-product-form textarea[name="description"]').val(product.description);

                        $('.bazaar-product-form .form-title').text(bazaarDashboard.i18n.editProduct);
                        $('.bazaar-product-form').slideDown();

                        $('html, body').animate({
                            scrollTop: $('.bazaar-product-form').offset().top - 100
                        }, 500);
                    }
                }
            });
        },

        /**
         * Save product
         */
        saveProduct: function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');

            $btn.prop('disabled', true).text(bazaarDashboard.i18n.saving);

            $.ajax({
                url: bazaarDashboard.ajaxUrl,
                type: 'POST',
                data: $form.serialize() + '&action=bazaar_save_product&nonce=' + bazaarDashboard.nonce,
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || bazaarDashboard.i18n.error);
                        $btn.prop('disabled', false).text(bazaarDashboard.i18n.saveProduct);
                    }
                },
                error: function() {
                    alert(bazaarDashboard.i18n.error);
                    $btn.prop('disabled', false).text(bazaarDashboard.i18n.saveProduct);
                }
            });
        },

        /**
         * Delete product
         */
        deleteProduct: function(e) {
            e.preventDefault();

            if (!confirm(bazaarDashboard.i18n.confirmDeleteProduct)) {
                return;
            }

            var productId = $(this).data('product-id');

            $.ajax({
                url: bazaarDashboard.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bazaar_delete_product',
                    product_id: productId,
                    nonce: bazaarDashboard.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || bazaarDashboard.i18n.error);
                    }
                }
            });
        },

        /**
         * Show withdrawal modal
         */
        showWithdrawalModal: function(e) {
            e.preventDefault();
            $('.bazaar-withdrawal-modal').fadeIn();
        },

        /**
         * Request withdrawal
         */
        requestWithdrawal: function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');

            $btn.prop('disabled', true).text(bazaarDashboard.i18n.processing);

            $.ajax({
                url: bazaarDashboard.ajaxUrl,
                type: 'POST',
                data: $form.serialize() + '&action=bazaar_request_withdrawal&nonce=' + bazaarDashboard.nonce,
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || bazaarDashboard.i18n.error);
                        $btn.prop('disabled', false).text(bazaarDashboard.i18n.requestWithdrawal);
                    }
                },
                error: function() {
                    alert(bazaarDashboard.i18n.error);
                    $btn.prop('disabled', false).text(bazaarDashboard.i18n.requestWithdrawal);
                }
            });
        },

        /**
         * Cancel withdrawal
         */
        cancelWithdrawal: function(e) {
            e.preventDefault();

            if (!confirm(bazaarDashboard.i18n.confirmCancelWithdrawal)) {
                return;
            }

            var withdrawalId = $(this).data('withdrawal-id');

            $.ajax({
                url: bazaarDashboard.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bazaar_cancel_withdrawal',
                    withdrawal_id: withdrawalId,
                    nonce: bazaarDashboard.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || bazaarDashboard.i18n.error);
                    }
                }
            });
        },

        /**
         * Show coupon form
         */
        showCouponForm: function(e) {
            e.preventDefault();
            $('#bazaar-coupon-form')[0].reset();
            $('input[name="coupon_id"]').val('');
            $('.bazaar-coupon-form .form-title').text(bazaarDashboard.i18n.createCoupon);
            $('.bazaar-coupon-form').slideDown();
        },

        /**
         * Hide coupon form
         */
        hideCouponForm: function(e) {
            e.preventDefault();
            $('.bazaar-coupon-form').slideUp();
        },

        /**
         * Edit coupon
         */
        editCoupon: function(e) {
            e.preventDefault();
            var couponId = $(this).data('coupon-id');

            $.ajax({
                url: bazaarDashboard.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bazaar_get_coupon',
                    coupon_id: couponId,
                    nonce: bazaarDashboard.nonce
                },
                success: function(response) {
                    if (response.success) {
                        var coupon = response.data;
                        var $form = $('#bazaar-coupon-form');

                        $form.find('input[name="coupon_id"]').val(coupon.id);
                        $form.find('input[name="coupon_code"]').val(coupon.code);
                        $form.find('select[name="discount_type"]').val(coupon.discount_type);
                        $form.find('input[name="coupon_amount"]').val(coupon.amount);
                        $form.find('input[name="expiry_date"]').val(coupon.expiry_date);
                        $form.find('input[name="minimum_amount"]').val(coupon.minimum_amount);
                        $form.find('input[name="maximum_amount"]').val(coupon.maximum_amount);
                        $form.find('input[name="usage_limit"]').val(coupon.usage_limit);
                        $form.find('input[name="usage_limit_per_user"]').val(coupon.usage_limit_per_user);
                        $form.find('textarea[name="description"]').val(coupon.description);

                        $('.bazaar-coupon-form .form-title').text(bazaarDashboard.i18n.editCoupon);
                        $('.bazaar-coupon-form').slideDown();
                    }
                }
            });
        },

        /**
         * Save coupon
         */
        saveCoupon: function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');

            $btn.prop('disabled', true).text(bazaarDashboard.i18n.saving);

            $.ajax({
                url: bazaarDashboard.ajaxUrl,
                type: 'POST',
                data: $form.serialize() + '&action=bazaar_save_coupon&nonce=' + bazaarDashboard.nonce,
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || bazaarDashboard.i18n.error);
                        $btn.prop('disabled', false).text(bazaarDashboard.i18n.saveCoupon);
                    }
                },
                error: function() {
                    alert(bazaarDashboard.i18n.error);
                    $btn.prop('disabled', false).text(bazaarDashboard.i18n.saveCoupon);
                }
            });
        },

        /**
         * Delete coupon
         */
        deleteCoupon: function(e) {
            e.preventDefault();

            if (!confirm(bazaarDashboard.i18n.confirmDeleteCoupon)) {
                return;
            }

            var couponId = $(this).data('coupon-id');

            $.ajax({
                url: bazaarDashboard.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bazaar_delete_coupon',
                    coupon_id: couponId,
                    nonce: bazaarDashboard.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || bazaarDashboard.i18n.error);
                    }
                }
            });
        },

        /**
         * Generate coupon code
         */
        generateCouponCode: function(e) {
            e.preventDefault();
            var code = 'BAZAAR-' + Math.random().toString(36).substr(2, 8).toUpperCase();
            $('#coupon_code').val(code);
        },

        /**
         * Copy coupon code
         */
        copyCouponCode: function(e) {
            e.preventDefault();
            var code = $(this).data('code');

            if (navigator.clipboard) {
                navigator.clipboard.writeText(code).then(function() {
                    alert(bazaarDashboard.i18n.codeCopied);
                });
            } else {
                var $temp = $('<input>');
                $('body').append($temp);
                $temp.val(code).select();
                document.execCommand('copy');
                $temp.remove();
                alert(bazaarDashboard.i18n.codeCopied);
            }
        },

        /**
         * Show reply form
         */
        showReplyForm: function(e) {
            e.preventDefault();
            var reviewId = $(this).data('review-id');
            $(this).hide();
            $('.reply-form[data-review-id="' + reviewId + '"]').slideDown();
        },

        /**
         * Hide reply form
         */
        hideReplyForm: function(e) {
            e.preventDefault();
            var $form = $(this).closest('.reply-form');
            var reviewId = $form.data('review-id');
            $form.slideUp();
            $('.reply-btn[data-review-id="' + reviewId + '"]').show();
        },

        /**
         * Submit reply
         */
        submitReply: function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');

            $btn.prop('disabled', true).text(bazaarDashboard.i18n.submitting);

            $.ajax({
                url: bazaarDashboard.ajaxUrl,
                type: 'POST',
                data: $form.serialize() + '&action=bazaar_reply_review&nonce=' + bazaarDashboard.nonce,
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || bazaarDashboard.i18n.error);
                        $btn.prop('disabled', false).text(bazaarDashboard.i18n.submitReply);
                    }
                },
                error: function() {
                    alert(bazaarDashboard.i18n.error);
                    $btn.prop('disabled', false).text(bazaarDashboard.i18n.submitReply);
                }
            });
        },

        /**
         * Show shipping zone form
         */
        showShippingZoneForm: function(e) {
            e.preventDefault();
            $('#bazaar-shipping-zone-form')[0].reset();
            $('input[name="zone_id"]').val('');
            $('.bazaar-shipping-zone-form .form-title').text(bazaarDashboard.i18n.addShippingZone);
            $('.bazaar-shipping-zone-form').slideDown();
        },

        /**
         * Hide shipping zone form
         */
        hideShippingZoneForm: function(e) {
            e.preventDefault();
            $('.bazaar-shipping-zone-form').slideUp();
        },

        /**
         * Save shipping zone
         */
        saveShippingZone: function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');

            $btn.prop('disabled', true).text(bazaarDashboard.i18n.saving);

            $.ajax({
                url: bazaarDashboard.ajaxUrl,
                type: 'POST',
                data: $form.serialize() + '&action=bazaar_save_shipping_zone&nonce=' + bazaarDashboard.nonce,
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || bazaarDashboard.i18n.error);
                        $btn.prop('disabled', false).text(bazaarDashboard.i18n.saveZone);
                    }
                },
                error: function() {
                    alert(bazaarDashboard.i18n.error);
                    $btn.prop('disabled', false).text(bazaarDashboard.i18n.saveZone);
                }
            });
        },

        /**
         * Show method modal
         */
        showMethodModal: function(e) {
            e.preventDefault();
            var zoneId = $(this).data('zone-id');
            $('#bazaar-shipping-method-form')[0].reset();
            $('#bazaar-shipping-method-form input[name="zone_id"]').val(zoneId);
            $('#bazaar-shipping-method-form input[name="method_id"]').val('');
            BazaarDashboard.toggleMethodFields();
            $('.bazaar-shipping-method-modal').fadeIn();
        },

        /**
         * Save shipping method
         */
        saveShippingMethod: function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');

            $btn.prop('disabled', true).text(bazaarDashboard.i18n.saving);

            $.ajax({
                url: bazaarDashboard.ajaxUrl,
                type: 'POST',
                data: $form.serialize() + '&action=bazaar_save_shipping_method&nonce=' + bazaarDashboard.nonce,
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || bazaarDashboard.i18n.error);
                        $btn.prop('disabled', false).text(bazaarDashboard.i18n.saveMethod);
                    }
                },
                error: function() {
                    alert(bazaarDashboard.i18n.error);
                    $btn.prop('disabled', false).text(bazaarDashboard.i18n.saveMethod);
                }
            });
        },

        /**
         * Toggle method fields based on type
         */
        toggleMethodFields: function() {
            var type = $('#method_type').val();

            if (type === 'free_shipping') {
                $('.method-cost-row').hide();
                $('.free-shipping-options').show();
            } else {
                $('.method-cost-row').show();
                $('.free-shipping-options').hide();
            }
        },

        /**
         * Save settings
         */
        saveSettings: function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');

            $btn.prop('disabled', true).text(bazaarDashboard.i18n.saving);

            $.ajax({
                url: bazaarDashboard.ajaxUrl,
                type: 'POST',
                data: $form.serialize() + '&action=bazaar_save_vendor_settings&nonce=' + bazaarDashboard.nonce,
                success: function(response) {
                    if (response.success) {
                        alert(bazaarDashboard.i18n.settingsSaved);
                        $btn.prop('disabled', false).text(bazaarDashboard.i18n.saveSettings);
                    } else {
                        alert(response.data.message || bazaarDashboard.i18n.error);
                        $btn.prop('disabled', false).text(bazaarDashboard.i18n.saveSettings);
                    }
                },
                error: function() {
                    alert(bazaarDashboard.i18n.error);
                    $btn.prop('disabled', false).text(bazaarDashboard.i18n.saveSettings);
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
            } else if (method === 'stripe') {
                $('.stripe-fields').show();
            }
        },

        /**
         * Close modal
         */
        closeModal: function(e) {
            e.preventDefault();
            $(this).closest('.bazaar-modal').fadeOut();
        },

        /**
         * Close modal on overlay click
         */
        closeModalOnOverlay: function(e) {
            if (e.target === this) {
                $(this).fadeOut();
            }
        },

        /**
         * Upload image
         */
        uploadImage: function(e) {
            e.preventDefault();
            var $btn = $(this);
            var $container = $btn.closest('.image-upload-container');
            var $input = $container.find('input[type="hidden"]');
            var $preview = $container.find('.image-preview');

            if (BazaarDashboard.mediaUploader) {
                BazaarDashboard.mediaUploader.open();
                return;
            }

            BazaarDashboard.mediaUploader = wp.media({
                title: bazaarDashboard.i18n.selectImage,
                button: {
                    text: bazaarDashboard.i18n.useImage
                },
                multiple: false
            });

            BazaarDashboard.mediaUploader.on('select', function() {
                var attachment = BazaarDashboard.mediaUploader.state().get('selection').first().toJSON();
                $input.val(attachment.id);
                $preview.html('<img src="' + attachment.url + '" alt="" />');
                $container.addClass('has-image');
            });

            BazaarDashboard.mediaUploader.open();
        },

        /**
         * Remove image
         */
        removeImage: function(e) {
            e.preventDefault();
            var $container = $(this).closest('.image-upload-container');
            $container.find('input[type="hidden"]').val('');
            $container.find('.image-preview').empty();
            $container.removeClass('has-image');
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        BazaarDashboard.init();
    });

})(jQuery);
