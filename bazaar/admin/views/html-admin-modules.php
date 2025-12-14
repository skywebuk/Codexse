<?php
/**
 * Admin Modules View.
 *
 * @package Bazaar\Admin\Views
 */

defined( 'ABSPATH' ) || exit;

// Get all available modules
$modules = Bazaar_Modules::get_all_modules();
$active_modules = Bazaar_Modules::get_active_modules();

// Handle module toggle
if ( isset( $_POST['bazaar_toggle_module'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ?? '' ) ), 'bazaar_module_toggle' ) ) {
    $module_id = sanitize_text_field( wp_unslash( $_POST['module_id'] ?? '' ) );
    $action = sanitize_text_field( wp_unslash( $_POST['module_action'] ?? '' ) );
    
    if ( 'activate' === $action ) {
        Bazaar_Modules::activate( $module_id );
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Module activated successfully.', 'bazaar' ) . '</p></div>';
    } elseif ( 'deactivate' === $action ) {
        Bazaar_Modules::deactivate( $module_id );
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Module deactivated successfully.', 'bazaar' ) . '</p></div>';
    }
    
    // Refresh active modules
    $active_modules = Bazaar_Modules::get_active_modules();
}

// Module categories
$categories = array(
    'vendor'     => __( 'Vendor Features', 'bazaar' ),
    'payment'    => __( 'Payment & Shipping', 'bazaar' ),
    'product'    => __( 'Product Management', 'bazaar' ),
    'order'      => __( 'Order Management', 'bazaar' ),
    'marketing'  => __( 'Marketing & SEO', 'bazaar' ),
    'reporting'  => __( 'Reports & Analytics', 'bazaar' ),
    'integration' => __( 'Integrations', 'bazaar' ),
);

// Filter by category
$current_category = isset( $_GET['category'] ) ? sanitize_text_field( wp_unslash( $_GET['category'] ) ) : '';
?>
<div class="wrap bazaar-admin-wrap bazaar-modules-page">
    <div class="bazaar-page-header">
        <h1><?php esc_html_e( 'Modules', 'bazaar' ); ?></h1>
        <p class="header-subtitle"><?php esc_html_e( 'Enable or disable marketplace features', 'bazaar' ); ?></p>
    </div>

    <!-- Module Stats -->
    <div class="bazaar-module-stats">
        <div class="stat-item">
            <span class="stat-number"><?php echo esc_html( count( $modules ) ); ?></span>
            <span class="stat-label"><?php esc_html_e( 'Total Modules', 'bazaar' ); ?></span>
        </div>
        <div class="stat-item active">
            <span class="stat-number"><?php echo esc_html( count( $active_modules ) ); ?></span>
            <span class="stat-label"><?php esc_html_e( 'Active', 'bazaar' ); ?></span>
        </div>
        <div class="stat-item inactive">
            <span class="stat-number"><?php echo esc_html( count( $modules ) - count( $active_modules ) ); ?></span>
            <span class="stat-label"><?php esc_html_e( 'Inactive', 'bazaar' ); ?></span>
        </div>
    </div>

    <!-- Category Filter -->
    <div class="bazaar-module-filters">
        <ul class="category-tabs">
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-modules' ) ); ?>" class="<?php echo empty( $current_category ) ? 'active' : ''; ?>">
                    <?php esc_html_e( 'All', 'bazaar' ); ?>
                </a>
            </li>
            <?php foreach ( $categories as $cat_id => $cat_name ) : ?>
                <li>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-modules&category=' . $cat_id ) ); ?>" class="<?php echo $current_category === $cat_id ? 'active' : ''; ?>">
                        <?php echo esc_html( $cat_name ); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="module-search">
            <input type="text" id="module-search" placeholder="<?php esc_attr_e( 'Search modules...', 'bazaar' ); ?>">
            <span class="dashicons dashicons-search"></span>
        </div>
    </div>

    <!-- Modules Grid -->
    <div class="bazaar-modules-grid">
        <?php
        // Define all modules with their details
        $all_modules = array(
            // Vendor Features
            array(
                'id'          => 'vendor_verification',
                'name'        => __( 'Vendor Verification', 'bazaar' ),
                'description' => __( 'Verify vendors with ID documents, business license, and social profiles.', 'bazaar' ),
                'category'    => 'vendor',
                'icon'        => 'dashicons-yes-alt',
                'pro'         => false,
            ),
            array(
                'id'          => 'vendor_staff',
                'name'        => __( 'Vendor Staff', 'bazaar' ),
                'description' => __( 'Allow vendors to add staff members with different permission levels.', 'bazaar' ),
                'category'    => 'vendor',
                'icon'        => 'dashicons-groups',
                'pro'         => true,
            ),
            array(
                'id'          => 'vendor_vacation',
                'name'        => __( 'Vendor Vacation', 'bazaar' ),
                'description' => __( 'Vendors can set vacation mode to temporarily close their store.', 'bazaar' ),
                'category'    => 'vendor',
                'icon'        => 'dashicons-palmtree',
                'pro'         => false,
            ),
            array(
                'id'          => 'vendor_badges',
                'name'        => __( 'Vendor Badges', 'bazaar' ),
                'description' => __( 'Award badges to vendors based on performance, sales, and reviews.', 'bazaar' ),
                'category'    => 'vendor',
                'icon'        => 'dashicons-awards',
                'pro'         => true,
            ),
            array(
                'id'          => 'vendor_seo',
                'name'        => __( 'Store SEO', 'bazaar' ),
                'description' => __( 'Let vendors optimize their store pages for search engines.', 'bazaar' ),
                'category'    => 'vendor',
                'icon'        => 'dashicons-search',
                'pro'         => false,
            ),
            array(
                'id'          => 'vendor_social',
                'name'        => __( 'Store Social Profiles', 'bazaar' ),
                'description' => __( 'Vendors can add social media links to their store profile.', 'bazaar' ),
                'category'    => 'vendor',
                'icon'        => 'dashicons-share',
                'pro'         => false,
            ),
            // Payment & Shipping
            array(
                'id'          => 'stripe_connect',
                'name'        => __( 'Stripe Connect', 'bazaar' ),
                'description' => __( 'Split payments automatically using Stripe Connect integration.', 'bazaar' ),
                'category'    => 'payment',
                'icon'        => 'dashicons-money-alt',
                'pro'         => true,
            ),
            array(
                'id'          => 'paypal_marketplace',
                'name'        => __( 'PayPal Marketplace', 'bazaar' ),
                'description' => __( 'Split payments using PayPal for Marketplaces.', 'bazaar' ),
                'category'    => 'payment',
                'icon'        => 'dashicons-money',
                'pro'         => true,
            ),
            array(
                'id'          => 'vendor_shipping',
                'name'        => __( 'Vendor Shipping', 'bazaar' ),
                'description' => __( 'Allow vendors to manage their own shipping zones and rates.', 'bazaar' ),
                'category'    => 'payment',
                'icon'        => 'dashicons-car',
                'pro'         => false,
            ),
            array(
                'id'          => 'table_rate_shipping',
                'name'        => __( 'Table Rate Shipping', 'bazaar' ),
                'description' => __( 'Advanced shipping rules based on weight, quantity, and location.', 'bazaar' ),
                'category'    => 'payment',
                'icon'        => 'dashicons-editor-table',
                'pro'         => true,
            ),
            // Product Management
            array(
                'id'          => 'product_addon',
                'name'        => __( 'Product Addons', 'bazaar' ),
                'description' => __( 'Let vendors add custom fields and options to products.', 'bazaar' ),
                'category'    => 'product',
                'icon'        => 'dashicons-plus-alt',
                'pro'         => true,
            ),
            array(
                'id'          => 'product_enquiry',
                'name'        => __( 'Product Enquiry', 'bazaar' ),
                'description' => __( 'Allow customers to send enquiries about products to vendors.', 'bazaar' ),
                'category'    => 'product',
                'icon'        => 'dashicons-email',
                'pro'         => false,
            ),
            array(
                'id'          => 'auction',
                'name'        => __( 'Auction', 'bazaar' ),
                'description' => __( 'Enable vendors to create auction-style product listings.', 'bazaar' ),
                'category'    => 'product',
                'icon'        => 'dashicons-hammer',
                'pro'         => true,
            ),
            array(
                'id'          => 'booking',
                'name'        => __( 'Booking & Appointments', 'bazaar' ),
                'description' => __( 'Vendors can sell bookable products and services.', 'bazaar' ),
                'category'    => 'product',
                'icon'        => 'dashicons-calendar-alt',
                'pro'         => true,
            ),
            array(
                'id'          => 'subscription',
                'name'        => __( 'Subscriptions', 'bazaar' ),
                'description' => __( 'Enable vendors to sell subscription-based products.', 'bazaar' ),
                'category'    => 'product',
                'icon'        => 'dashicons-update',
                'pro'         => true,
            ),
            // Order Management
            array(
                'id'          => 'live_chat',
                'name'        => __( 'Live Chat', 'bazaar' ),
                'description' => __( 'Real-time chat between customers and vendors.', 'bazaar' ),
                'category'    => 'order',
                'icon'        => 'dashicons-format-chat',
                'pro'         => true,
            ),
            array(
                'id'          => 'order_minmax',
                'name'        => __( 'Order Min/Max', 'bazaar' ),
                'description' => __( 'Set minimum and maximum order amounts per vendor.', 'bazaar' ),
                'category'    => 'order',
                'icon'        => 'dashicons-editor-ul',
                'pro'         => false,
            ),
            array(
                'id'          => 'return_request',
                'name'        => __( 'Return & Warranty', 'bazaar' ),
                'description' => __( 'Manage product returns and warranty requests.', 'bazaar' ),
                'category'    => 'order',
                'icon'        => 'dashicons-undo',
                'pro'         => true,
            ),
            // Marketing
            array(
                'id'          => 'coupons',
                'name'        => __( 'Vendor Coupons', 'bazaar' ),
                'description' => __( 'Allow vendors to create and manage their own discount coupons.', 'bazaar' ),
                'category'    => 'marketing',
                'icon'        => 'dashicons-tickets-alt',
                'pro'         => false,
            ),
            array(
                'id'          => 'follow_store',
                'name'        => __( 'Follow Store', 'bazaar' ),
                'description' => __( 'Customers can follow their favorite stores for updates.', 'bazaar' ),
                'category'    => 'marketing',
                'icon'        => 'dashicons-heart',
                'pro'         => false,
            ),
            array(
                'id'          => 'email_marketing',
                'name'        => __( 'Email Marketing', 'bazaar' ),
                'description' => __( 'Vendors can send email campaigns to their followers.', 'bazaar' ),
                'category'    => 'marketing',
                'icon'        => 'dashicons-email-alt',
                'pro'         => true,
            ),
            // Reports
            array(
                'id'          => 'vendor_analytics',
                'name'        => __( 'Vendor Analytics', 'bazaar' ),
                'description' => __( 'Detailed sales and traffic analytics for vendors.', 'bazaar' ),
                'category'    => 'reporting',
                'icon'        => 'dashicons-chart-area',
                'pro'         => false,
            ),
            array(
                'id'          => 'export_reports',
                'name'        => __( 'Export Reports', 'bazaar' ),
                'description' => __( 'Export sales and commission reports to CSV/PDF.', 'bazaar' ),
                'category'    => 'reporting',
                'icon'        => 'dashicons-download',
                'pro'         => false,
            ),
            // Integrations
            array(
                'id'          => 'elementor',
                'name'        => __( 'Elementor', 'bazaar' ),
                'description' => __( 'Design vendor stores with Elementor page builder.', 'bazaar' ),
                'category'    => 'integration',
                'icon'        => 'dashicons-edit',
                'pro'         => true,
            ),
            array(
                'id'          => 'geolocation',
                'name'        => __( 'Geolocation', 'bazaar' ),
                'description' => __( 'Show nearby vendors and store locations on maps.', 'bazaar' ),
                'category'    => 'integration',
                'icon'        => 'dashicons-location-alt',
                'pro'         => true,
            ),
            array(
                'id'          => 'wholesale',
                'name'        => __( 'Wholesale', 'bazaar' ),
                'description' => __( 'B2B wholesale pricing and bulk order support.', 'bazaar' ),
                'category'    => 'integration',
                'icon'        => 'dashicons-store',
                'pro'         => true,
            ),
        );

        foreach ( $all_modules as $module ) :
            // Filter by category
            if ( ! empty( $current_category ) && $module['category'] !== $current_category ) {
                continue;
            }
            
            $is_active = in_array( $module['id'], $active_modules, true );
            ?>
            <div class="bazaar-module-card <?php echo $is_active ? 'active' : ''; ?>" data-module="<?php echo esc_attr( $module['id'] ); ?>" data-category="<?php echo esc_attr( $module['category'] ); ?>">
                <?php if ( $module['pro'] ) : ?>
                    <span class="pro-badge"><?php esc_html_e( 'PRO', 'bazaar' ); ?></span>
                <?php endif; ?>
                
                <div class="module-icon">
                    <span class="dashicons <?php echo esc_attr( $module['icon'] ); ?>"></span>
                </div>
                
                <div class="module-content">
                    <h3><?php echo esc_html( $module['name'] ); ?></h3>
                    <p><?php echo esc_html( $module['description'] ); ?></p>
                </div>
                
                <div class="module-footer">
                    <form method="post" class="module-toggle-form">
                        <?php wp_nonce_field( 'bazaar_module_toggle' ); ?>
                        <input type="hidden" name="module_id" value="<?php echo esc_attr( $module['id'] ); ?>">
                        <input type="hidden" name="module_action" value="<?php echo $is_active ? 'deactivate' : 'activate'; ?>">
                        <input type="hidden" name="bazaar_toggle_module" value="1">
                        
                        <label class="bazaar-toggle">
                            <input type="checkbox" <?php checked( $is_active ); ?> onchange="this.form.submit()">
                            <span class="toggle-slider"></span>
                        </label>
                    </form>
                    
                    <?php if ( $is_active ) : ?>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-settings&tab=' . $module['id'] ) ); ?>" class="module-settings">
                            <span class="dashicons dashicons-admin-generic"></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Module search
    $('#module-search').on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase();
        
        $('.bazaar-module-card').each(function() {
            var moduleName = $(this).find('h3').text().toLowerCase();
            var moduleDesc = $(this).find('p').text().toLowerCase();
            
            if (moduleName.indexOf(searchTerm) > -1 || moduleDesc.indexOf(searchTerm) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
</script>
