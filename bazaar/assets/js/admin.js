/**
 * Bazaar Admin JavaScript
 *
 * @package Bazaar
 */

(function($) {
    'use strict';

    var BazaarAdmin = {
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.initTabs();
            this.initCharts();
            this.initSelect2();
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            // Vendor actions
            $(document).on('click', '.approve-vendor', this.approveVendor);
            $(document).on('click', '.reject-vendor', this.rejectVendor);
            $(document).on('click', '.delete-vendor', this.deleteVendor);

            // Withdrawal actions
            $(document).on('click', '.approve-withdrawal', this.approveWithdrawal);
            $(document).on('click', '.reject-withdrawal', this.rejectWithdrawal);
            $(document).on('click', '.mark-paid', this.markWithdrawalPaid);

            // Modal
            $(document).on('click', '.bazaar-modal-close, .bazaar-modal-overlay', this.closeModal);

            // Settings tabs
            $(document).on('click', '.nav-tab', this.switchTab);

            // Export
            $(document).on('click', '.export-data', this.exportData);
        },

        /**
         * Initialize tabs
         */
        initTabs: function() {
            var hash = window.location.hash;
            if (hash) {
                $('.nav-tab[href="' + hash + '"]').trigger('click');
            }
        },

        /**
         * Initialize Select2
         */
        initSelect2: function() {
            if ($.fn.select2) {
                $('.bazaar-select2').select2({
                    width: '100%',
                    placeholder: $(this).data('placeholder') || 'Select...'
                });
            }
        },

        /**
         * Initialize charts
         */
        initCharts: function() {
            if (typeof Chart === 'undefined') {
                return;
            }

            // Sales Chart
            var salesChart = document.getElementById('bazaar-sales-chart');
            if (salesChart) {
                new Chart(salesChart, {
                    type: 'line',
                    data: {
                        labels: bazaarAdmin.salesData.labels || [],
                        datasets: [{
                            label: 'Sales',
                            data: bazaarAdmin.salesData.values || [],
                            borderColor: '#0073aa',
                            backgroundColor: 'rgba(0, 115, 170, 0.1)',
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

            // Vendors Chart
            var vendorsChart = document.getElementById('bazaar-vendors-chart');
            if (vendorsChart) {
                new Chart(vendorsChart, {
                    type: 'doughnut',
                    data: {
                        labels: ['Active', 'Pending', 'Rejected'],
                        datasets: [{
                            data: bazaarAdmin.vendorStats || [0, 0, 0],
                            backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }
        },

        /**
         * Switch tab
         */
        switchTab: function(e) {
            e.preventDefault();
            var $tab = $(this);
            var target = $tab.attr('href');

            $('.nav-tab').removeClass('nav-tab-active');
            $tab.addClass('nav-tab-active');

            $('.tab-content').hide();
            $(target).show();

            window.location.hash = target;
        },

        /**
         * Approve vendor
         */
        approveVendor: function(e) {
            e.preventDefault();

            if (!confirm(bazaarAdmin.i18n.confirmApprove)) {
                return;
            }

            var $btn = $(this);
            var vendorId = $btn.data('vendor-id');

            $btn.prop('disabled', true).text(bazaarAdmin.i18n.processing);

            $.ajax({
                url: bazaarAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bazaar_approve_vendor',
                    vendor_id: vendorId,
                    nonce: bazaarAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || bazaarAdmin.i18n.error);
                        $btn.prop('disabled', false).text(bazaarAdmin.i18n.approve);
                    }
                },
                error: function() {
                    alert(bazaarAdmin.i18n.error);
                    $btn.prop('disabled', false).text(bazaarAdmin.i18n.approve);
                }
            });
        },

        /**
         * Reject vendor
         */
        rejectVendor: function(e) {
            e.preventDefault();

            var reason = prompt(bazaarAdmin.i18n.rejectReason);
            if (reason === null) {
                return;
            }

            var $btn = $(this);
            var vendorId = $btn.data('vendor-id');

            $btn.prop('disabled', true).text(bazaarAdmin.i18n.processing);

            $.ajax({
                url: bazaarAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bazaar_reject_vendor',
                    vendor_id: vendorId,
                    reason: reason,
                    nonce: bazaarAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || bazaarAdmin.i18n.error);
                        $btn.prop('disabled', false).text(bazaarAdmin.i18n.reject);
                    }
                },
                error: function() {
                    alert(bazaarAdmin.i18n.error);
                    $btn.prop('disabled', false).text(bazaarAdmin.i18n.reject);
                }
            });
        },

        /**
         * Delete vendor
         */
        deleteVendor: function(e) {
            e.preventDefault();

            if (!confirm(bazaarAdmin.i18n.confirmDelete)) {
                return;
            }

            var $btn = $(this);
            var vendorId = $btn.data('vendor-id');

            $.ajax({
                url: bazaarAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bazaar_delete_vendor',
                    vendor_id: vendorId,
                    nonce: bazaarAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || bazaarAdmin.i18n.error);
                    }
                },
                error: function() {
                    alert(bazaarAdmin.i18n.error);
                }
            });
        },

        /**
         * Approve withdrawal
         */
        approveWithdrawal: function(e) {
            e.preventDefault();

            if (!confirm(bazaarAdmin.i18n.confirmApproveWithdrawal)) {
                return;
            }

            var $btn = $(this);
            var withdrawalId = $btn.data('withdrawal-id');

            $btn.prop('disabled', true).text(bazaarAdmin.i18n.processing);

            $.ajax({
                url: bazaarAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bazaar_approve_withdrawal',
                    withdrawal_id: withdrawalId,
                    nonce: bazaarAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || bazaarAdmin.i18n.error);
                        $btn.prop('disabled', false).text(bazaarAdmin.i18n.approve);
                    }
                },
                error: function() {
                    alert(bazaarAdmin.i18n.error);
                    $btn.prop('disabled', false).text(bazaarAdmin.i18n.approve);
                }
            });
        },

        /**
         * Reject withdrawal
         */
        rejectWithdrawal: function(e) {
            e.preventDefault();

            var reason = prompt(bazaarAdmin.i18n.rejectWithdrawalReason);
            if (reason === null) {
                return;
            }

            var $btn = $(this);
            var withdrawalId = $btn.data('withdrawal-id');

            $btn.prop('disabled', true).text(bazaarAdmin.i18n.processing);

            $.ajax({
                url: bazaarAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bazaar_reject_withdrawal',
                    withdrawal_id: withdrawalId,
                    reason: reason,
                    nonce: bazaarAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || bazaarAdmin.i18n.error);
                        $btn.prop('disabled', false).text(bazaarAdmin.i18n.reject);
                    }
                },
                error: function() {
                    alert(bazaarAdmin.i18n.error);
                    $btn.prop('disabled', false).text(bazaarAdmin.i18n.reject);
                }
            });
        },

        /**
         * Mark withdrawal as paid
         */
        markWithdrawalPaid: function(e) {
            e.preventDefault();

            if (!confirm(bazaarAdmin.i18n.confirmMarkPaid)) {
                return;
            }

            var $btn = $(this);
            var withdrawalId = $btn.data('withdrawal-id');

            $btn.prop('disabled', true).text(bazaarAdmin.i18n.processing);

            $.ajax({
                url: bazaarAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bazaar_mark_withdrawal_paid',
                    withdrawal_id: withdrawalId,
                    nonce: bazaarAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || bazaarAdmin.i18n.error);
                        $btn.prop('disabled', false).text(bazaarAdmin.i18n.markPaid);
                    }
                },
                error: function() {
                    alert(bazaarAdmin.i18n.error);
                    $btn.prop('disabled', false).text(bazaarAdmin.i18n.markPaid);
                }
            });
        },

        /**
         * Close modal
         */
        closeModal: function(e) {
            if (e.target === this || $(e.target).hasClass('bazaar-modal-close')) {
                $('.bazaar-modal-overlay').fadeOut();
            }
        },

        /**
         * Export data
         */
        exportData: function(e) {
            e.preventDefault();

            var type = $(this).data('type');
            var format = $(this).data('format') || 'csv';

            window.location.href = bazaarAdmin.ajaxUrl + '?' + $.param({
                action: 'bazaar_export_' + type,
                format: format,
                nonce: bazaarAdmin.nonce
            });
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        BazaarAdmin.init();
    });

})(jQuery);
