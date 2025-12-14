<?php
/**
 * Admin Help View.
 *
 * @package Bazaar\Admin\Views
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap bazaar-admin-wrap bazaar-help-page">
    <div class="bazaar-page-header">
        <h1><?php esc_html_e( 'Help & Support', 'bazaar' ); ?></h1>
        <p class="header-subtitle"><?php esc_html_e( 'Documentation, tutorials, and support resources', 'bazaar' ); ?></p>
    </div>

    <!-- Quick Links -->
    <div class="bazaar-help-cards">
        <a href="#getting-started" class="bazaar-help-card">
            <div class="help-card-icon">
                <span class="dashicons dashicons-welcome-learn-more"></span>
            </div>
            <h3><?php esc_html_e( 'Getting Started', 'bazaar' ); ?></h3>
            <p><?php esc_html_e( 'Learn the basics of setting up your marketplace', 'bazaar' ); ?></p>
        </a>
        
        <a href="#vendor-setup" class="bazaar-help-card">
            <div class="help-card-icon">
                <span class="dashicons dashicons-store"></span>
            </div>
            <h3><?php esc_html_e( 'Vendor Setup', 'bazaar' ); ?></h3>
            <p><?php esc_html_e( 'Configure vendor registration and management', 'bazaar' ); ?></p>
        </a>
        
        <a href="#commission" class="bazaar-help-card">
            <div class="help-card-icon">
                <span class="dashicons dashicons-money-alt"></span>
            </div>
            <h3><?php esc_html_e( 'Commission', 'bazaar' ); ?></h3>
            <p><?php esc_html_e( 'Set up commission rates and payment schedules', 'bazaar' ); ?></p>
        </a>
        
        <a href="#withdrawals" class="bazaar-help-card">
            <div class="help-card-icon">
                <span class="dashicons dashicons-bank"></span>
            </div>
            <h3><?php esc_html_e( 'Withdrawals', 'bazaar' ); ?></h3>
            <p><?php esc_html_e( 'Configure withdrawal methods and limits', 'bazaar' ); ?></p>
        </a>
    </div>

    <!-- Documentation Sections -->
    <div class="bazaar-help-sections">
        <!-- Getting Started -->
        <div id="getting-started" class="bazaar-help-section">
            <div class="section-header">
                <span class="dashicons dashicons-welcome-learn-more"></span>
                <h2><?php esc_html_e( 'Getting Started', 'bazaar' ); ?></h2>
            </div>
            <div class="section-content">
                <div class="help-accordion">
                    <div class="accordion-item">
                        <button class="accordion-header">
                            <?php esc_html_e( 'How do I set up my marketplace?', 'bazaar' ); ?>
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                        </button>
                        <div class="accordion-content">
                            <ol>
                                <li><?php esc_html_e( 'Go to Bazaar > Settings > General to configure basic marketplace settings.', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'Set your commission rates in Bazaar > Settings > Commission.', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'Configure withdrawal methods in Bazaar > Settings > Withdraw.', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'Enable desired modules in Bazaar > Modules.', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'Create necessary pages using Bazaar > Tools > Page Setup.', 'bazaar' ); ?></li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <button class="accordion-header">
                            <?php esc_html_e( 'What pages are required?', 'bazaar' ); ?>
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                        </button>
                        <div class="accordion-content">
                            <p><?php esc_html_e( 'Bazaar requires the following pages:', 'bazaar' ); ?></p>
                            <ul>
                                <li><strong><?php esc_html_e( 'Vendor Dashboard', 'bazaar' ); ?></strong> - <?php esc_html_e( 'Where vendors manage their store, products, and orders.', 'bazaar' ); ?></li>
                                <li><strong><?php esc_html_e( 'Store Listing', 'bazaar' ); ?></strong> - <?php esc_html_e( 'Displays all vendor stores to customers.', 'bazaar' ); ?></li>
                                <li><strong><?php esc_html_e( 'Vendor Registration', 'bazaar' ); ?></strong> - <?php esc_html_e( 'Form for new vendors to apply.', 'bazaar' ); ?></li>
                            </ul>
                            <p><?php esc_html_e( 'Go to Bazaar > Tools to automatically create these pages.', 'bazaar' ); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor Setup -->
        <div id="vendor-setup" class="bazaar-help-section">
            <div class="section-header">
                <span class="dashicons dashicons-store"></span>
                <h2><?php esc_html_e( 'Vendor Setup', 'bazaar' ); ?></h2>
            </div>
            <div class="section-content">
                <div class="help-accordion">
                    <div class="accordion-item">
                        <button class="accordion-header">
                            <?php esc_html_e( 'How do vendors register?', 'bazaar' ); ?>
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                        </button>
                        <div class="accordion-content">
                            <p><?php esc_html_e( 'Vendors can register through:', 'bazaar' ); ?></p>
                            <ul>
                                <li><?php esc_html_e( 'The dedicated vendor registration page (using the [bazaar_registration] shortcode)', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'WooCommerce My Account page (if enabled in settings)', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'Admin can manually create vendors from Users > Add New', 'bazaar' ); ?></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <button class="accordion-header">
                            <?php esc_html_e( 'How do I approve vendors?', 'bazaar' ); ?>
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                        </button>
                        <div class="accordion-content">
                            <ol>
                                <li><?php esc_html_e( 'Go to Bazaar > Vendors.', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'Filter by "Pending" status to see awaiting applications.', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'Click on a vendor to review their application.', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'Click "Approve" or "Reject" based on your review.', 'bazaar' ); ?></li>
                            </ol>
                            <p><?php esc_html_e( 'You can also enable auto-approval in Settings > Selling Options.', 'bazaar' ); ?></p>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <button class="accordion-header">
                            <?php esc_html_e( 'Can I set different capabilities for vendors?', 'bazaar' ); ?>
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                        </button>
                        <div class="accordion-content">
                            <p><?php esc_html_e( 'Yes! Go to Settings > Selling Options to configure:', 'bazaar' ); ?></p>
                            <ul>
                                <li><?php esc_html_e( 'Whether vendors can create product categories', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'Product types vendors can create (simple, variable, etc.)', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'Whether products need admin approval', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'Order management capabilities', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'And much more...', 'bazaar' ); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commission -->
        <div id="commission" class="bazaar-help-section">
            <div class="section-header">
                <span class="dashicons dashicons-money-alt"></span>
                <h2><?php esc_html_e( 'Commission', 'bazaar' ); ?></h2>
            </div>
            <div class="section-content">
                <div class="help-accordion">
                    <div class="accordion-item">
                        <button class="accordion-header">
                            <?php esc_html_e( 'How does commission work?', 'bazaar' ); ?>
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                        </button>
                        <div class="accordion-content">
                            <p><?php esc_html_e( 'When a customer places an order:', 'bazaar' ); ?></p>
                            <ol>
                                <li><?php esc_html_e( 'The payment is received by the marketplace.', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'Commission is calculated based on your settings.', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'Admin commission is deducted from the order total.', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'Remaining amount is added to vendor\'s balance.', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'Vendor can request withdrawal of their balance.', 'bazaar' ); ?></li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <button class="accordion-header">
                            <?php esc_html_e( 'What commission types are available?', 'bazaar' ); ?>
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                        </button>
                        <div class="accordion-content">
                            <ul>
                                <li><strong><?php esc_html_e( 'Percentage', 'bazaar' ); ?></strong> - <?php esc_html_e( 'Take a percentage of each sale (e.g., 10%).', 'bazaar' ); ?></li>
                                <li><strong><?php esc_html_e( 'Flat', 'bazaar' ); ?></strong> - <?php esc_html_e( 'Take a fixed amount per order (e.g., $5).', 'bazaar' ); ?></li>
                                <li><strong><?php esc_html_e( 'Combined', 'bazaar' ); ?></strong> - <?php esc_html_e( 'Percentage + flat fee (e.g., 10% + $2).', 'bazaar' ); ?></li>
                            </ul>
                            <p><?php esc_html_e( 'You can also set different commission rates per vendor or per product category.', 'bazaar' ); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Withdrawals -->
        <div id="withdrawals" class="bazaar-help-section">
            <div class="section-header">
                <span class="dashicons dashicons-bank"></span>
                <h2><?php esc_html_e( 'Withdrawals', 'bazaar' ); ?></h2>
            </div>
            <div class="section-content">
                <div class="help-accordion">
                    <div class="accordion-item">
                        <button class="accordion-header">
                            <?php esc_html_e( 'What withdrawal methods are available?', 'bazaar' ); ?>
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                        </button>
                        <div class="accordion-content">
                            <ul>
                                <li><strong><?php esc_html_e( 'PayPal', 'bazaar' ); ?></strong> - <?php esc_html_e( 'Transfer to vendor\'s PayPal account.', 'bazaar' ); ?></li>
                                <li><strong><?php esc_html_e( 'Bank Transfer', 'bazaar' ); ?></strong> - <?php esc_html_e( 'Direct deposit to bank account.', 'bazaar' ); ?></li>
                                <li><strong><?php esc_html_e( 'Stripe', 'bazaar' ); ?></strong> - <?php esc_html_e( 'Automatic payouts via Stripe Connect.', 'bazaar' ); ?></li>
                                <li><strong><?php esc_html_e( 'Skrill', 'bazaar' ); ?></strong> - <?php esc_html_e( 'Transfer to Skrill wallet.', 'bazaar' ); ?></li>
                            </ul>
                            <p><?php esc_html_e( 'Enable methods in Settings > Withdraw.', 'bazaar' ); ?></p>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <button class="accordion-header">
                            <?php esc_html_e( 'How do I process withdrawals?', 'bazaar' ); ?>
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                        </button>
                        <div class="accordion-content">
                            <ol>
                                <li><?php esc_html_e( 'Go to Bazaar > Withdraw.', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'Review pending withdrawal requests.', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'Click "Approve" to approve a request.', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'Process the payment through the selected method.', 'bazaar' ); ?></li>
                                <li><?php esc_html_e( 'Click "Mark as Paid" once payment is sent.', 'bazaar' ); ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Support & Resources -->
    <div class="bazaar-help-footer">
        <div class="footer-section">
            <h3><?php esc_html_e( 'Need More Help?', 'bazaar' ); ?></h3>
            <div class="support-links">
                <a href="#" class="support-link" target="_blank">
                    <span class="dashicons dashicons-book"></span>
                    <?php esc_html_e( 'Documentation', 'bazaar' ); ?>
                </a>
                <a href="#" class="support-link" target="_blank">
                    <span class="dashicons dashicons-video-alt3"></span>
                    <?php esc_html_e( 'Video Tutorials', 'bazaar' ); ?>
                </a>
                <a href="#" class="support-link" target="_blank">
                    <span class="dashicons dashicons-sos"></span>
                    <?php esc_html_e( 'Submit Ticket', 'bazaar' ); ?>
                </a>
                <a href="#" class="support-link" target="_blank">
                    <span class="dashicons dashicons-groups"></span>
                    <?php esc_html_e( 'Community Forum', 'bazaar' ); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Accordion functionality
    $('.accordion-header').on('click', function() {
        var $item = $(this).parent();
        var $content = $(this).next('.accordion-content');
        
        if ($item.hasClass('active')) {
            $item.removeClass('active');
            $content.slideUp(200);
        } else {
            $item.addClass('active');
            $content.slideDown(200);
        }
    });
    
    // Smooth scroll to sections
    $('.bazaar-help-card').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        $('html, body').animate({
            scrollTop: $(target).offset().top - 50
        }, 500);
    });
});
</script>
