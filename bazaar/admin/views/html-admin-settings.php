<?php
/**
 * Admin Settings View.
 *
 * @package Bazaar\Admin\Views
 */

defined( 'ABSPATH' ) || exit;

// Get current tab
$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';

// Define tabs
$tabs = array(
    'general'       => __( 'General', 'bazaar' ),
    'selling'       => __( 'Selling Options', 'bazaar' ),
    'withdraw'      => __( 'Withdraw', 'bazaar' ),
    'pages'         => __( 'Page Settings', 'bazaar' ),
    'appearance'    => __( 'Appearance', 'bazaar' ),
    'privacy'       => __( 'Privacy Policy', 'bazaar' ),
    'emails'        => __( 'Emails', 'bazaar' ),
);

// Handle form submission
if ( isset( $_POST['bazaar_save_settings'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ?? '' ) ), 'bazaar_settings' ) ) {
    Bazaar_Settings::save( $current_tab, $_POST );
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved.', 'bazaar' ) . '</p></div>';
}

// Get settings
$settings = Bazaar_Settings::get_all();
?>
<div class="wrap bazaar-admin-wrap bazaar-settings-page">
    <div class="bazaar-page-header">
        <h1><?php esc_html_e( 'Settings', 'bazaar' ); ?></h1>
        <p class="header-subtitle"><?php esc_html_e( 'Configure your marketplace settings', 'bazaar' ); ?></p>
    </div>

    <!-- Settings Navigation -->
    <nav class="bazaar-settings-nav">
        <?php foreach ( $tabs as $tab_id => $tab_name ) : ?>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-settings&tab=' . $tab_id ) ); ?>" class="nav-tab <?php echo $current_tab === $tab_id ? 'nav-tab-active' : ''; ?>">
                <?php echo esc_html( $tab_name ); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Settings Form -->
    <form method="post" class="bazaar-settings-form">
        <?php wp_nonce_field( 'bazaar_settings' ); ?>

        <div class="bazaar-settings-content">
            <?php
            switch ( $current_tab ) :
                case 'general':
                    ?>
                    <!-- General Settings -->
                    <div class="bazaar-settings-section">
                        <h2><?php esc_html_e( 'General Settings', 'bazaar' ); ?></h2>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="admin_access"><?php esc_html_e( 'Admin Access', 'bazaar' ); ?></label>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="admin_access" id="admin_access" value="1" <?php checked( $settings['admin_access'] ?? false ); ?>>
                                        <?php esc_html_e( 'Allow vendors to access WordPress admin area', 'bazaar' ); ?>
                                    </label>
                                    <p class="description"><?php esc_html_e( 'If disabled, vendors will only use the frontend dashboard.', 'bazaar' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="vendor_registration"><?php esc_html_e( 'Vendor Registration', 'bazaar' ); ?></label>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="vendor_registration" id="vendor_registration" value="1" <?php checked( $settings['vendor_registration'] ?? true ); ?>>
                                        <?php esc_html_e( 'Enable vendor registration', 'bazaar' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="new_vendor_status"><?php esc_html_e( 'New Vendor Status', 'bazaar' ); ?></label>
                                </th>
                                <td>
                                    <select name="new_vendor_status" id="new_vendor_status">
                                        <option value="pending" <?php selected( $settings['new_vendor_status'] ?? 'pending', 'pending' ); ?>><?php esc_html_e( 'Pending Review', 'bazaar' ); ?></option>
                                        <option value="approved" <?php selected( $settings['new_vendor_status'] ?? '', 'approved' ); ?>><?php esc_html_e( 'Approved (Auto-approve)', 'bazaar' ); ?></option>
                                    </select>
                                    <p class="description"><?php esc_html_e( 'Status for newly registered vendors.', 'bazaar' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="store_url"><?php esc_html_e( 'Vendor Store URL', 'bazaar' ); ?></label>
                                </th>
                                <td>
                                    <code><?php echo esc_html( home_url( '/' ) ); ?></code>
                                    <input type="text" name="store_url" id="store_url" value="<?php echo esc_attr( $settings['store_url'] ?? 'store' ); ?>" class="regular-text">
                                    <code>/vendor-name/</code>
                                    <p class="description"><?php esc_html_e( 'Define vendor store URL structure.', 'bazaar' ); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="bazaar-settings-section">
                        <h2><?php esc_html_e( 'Commission Settings', 'bazaar' ); ?></h2>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="commission_type"><?php esc_html_e( 'Commission Type', 'bazaar' ); ?></label>
                                </th>
                                <td>
                                    <select name="commission_type" id="commission_type">
                                        <option value="percentage" <?php selected( $settings['commission_type'] ?? 'percentage', 'percentage' ); ?>><?php esc_html_e( 'Percentage', 'bazaar' ); ?></option>
                                        <option value="flat" <?php selected( $settings['commission_type'] ?? '', 'flat' ); ?>><?php esc_html_e( 'Flat', 'bazaar' ); ?></option>
                                        <option value="combined" <?php selected( $settings['commission_type'] ?? '', 'combined' ); ?>><?php esc_html_e( 'Percentage + Flat', 'bazaar' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="admin_percentage"><?php esc_html_e( 'Admin Commission (%)', 'bazaar' ); ?></label>
                                </th>
                                <td>
                                    <input type="number" name="admin_percentage" id="admin_percentage" value="<?php echo esc_attr( $settings['admin_percentage'] ?? 10 ); ?>" step="0.01" min="0" max="100" class="small-text">
                                    <span>%</span>
                                    <p class="description"><?php esc_html_e( 'Percentage commission taken by admin from each sale.', 'bazaar' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="admin_flat_commission"><?php esc_html_e( 'Flat Commission', 'bazaar' ); ?></label>
                                </th>
                                <td>
                                    <input type="number" name="admin_flat_commission" id="admin_flat_commission" value="<?php echo esc_attr( $settings['admin_flat_commission'] ?? 0 ); ?>" step="0.01" min="0" class="small-text">
                                    <span><?php echo esc_html( get_woocommerce_currency_symbol() ); ?></span>
                                    <p class="description"><?php esc_html_e( 'Fixed amount commission per order (used with Flat or Combined type).', 'bazaar' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="shipping_fee_recipient"><?php esc_html_e( 'Shipping Fee Recipient', 'bazaar' ); ?></label>
                                </th>
                                <td>
                                    <select name="shipping_fee_recipient" id="shipping_fee_recipient">
                                        <option value="vendor" <?php selected( $settings['shipping_fee_recipient'] ?? 'vendor', 'vendor' ); ?>><?php esc_html_e( 'Vendor', 'bazaar' ); ?></option>
                                        <option value="admin" <?php selected( $settings['shipping_fee_recipient'] ?? '', 'admin' ); ?>><?php esc_html_e( 'Admin', 'bazaar' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="tax_fee_recipient"><?php esc_html_e( 'Tax Fee Recipient', 'bazaar' ); ?></label>
                                </th>
                                <td>
                                    <select name="tax_fee_recipient" id="tax_fee_recipient">
                                        <option value="vendor" <?php selected( $settings['tax_fee_recipient'] ?? 'vendor', 'vendor' ); ?>><?php esc_html_e( 'Vendor', 'bazaar' ); ?></option>
                                        <option value="admin" <?php selected( $settings['tax_fee_recipient'] ?? '', 'admin' ); ?>><?php esc_html_e( 'Admin', 'bazaar' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php
                    break;

                case 'selling':
                    ?>
                    <!-- Selling Options -->
                    <div class="bazaar-settings-section">
                        <h2><?php esc_html_e( 'Product Settings', 'bazaar' ); ?></h2>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php esc_html_e( 'New Product Status', 'bazaar' ); ?></th>
                                <td>
                                    <select name="new_product_status" id="new_product_status">
                                        <option value="pending" <?php selected( $settings['new_product_status'] ?? 'pending', 'pending' ); ?>><?php esc_html_e( 'Pending Review', 'bazaar' ); ?></option>
                                        <option value="publish" <?php selected( $settings['new_product_status'] ?? '', 'publish' ); ?>><?php esc_html_e( 'Published', 'bazaar' ); ?></option>
                                        <option value="draft" <?php selected( $settings['new_product_status'] ?? '', 'draft' ); ?>><?php esc_html_e( 'Draft', 'bazaar' ); ?></option>
                                    </select>
                                    <p class="description"><?php esc_html_e( 'Status for new products added by vendors.', 'bazaar' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Product Types', 'bazaar' ); ?></th>
                                <td>
                                    <?php
                                    $product_types = array(
                                        'simple'   => __( 'Simple', 'bazaar' ),
                                        'variable' => __( 'Variable', 'bazaar' ),
                                        'grouped'  => __( 'Grouped', 'bazaar' ),
                                        'external' => __( 'External/Affiliate', 'bazaar' ),
                                    );
                                    $allowed_types = $settings['allowed_product_types'] ?? array( 'simple', 'variable' );
                                    foreach ( $product_types as $type => $label ) :
                                    ?>
                                        <label style="display: block; margin-bottom: 5px;">
                                            <input type="checkbox" name="allowed_product_types[]" value="<?php echo esc_attr( $type ); ?>" <?php checked( in_array( $type, $allowed_types, true ) ); ?>>
                                            <?php echo esc_html( $label ); ?>
                                        </label>
                                    <?php endforeach; ?>
                                    <p class="description"><?php esc_html_e( 'Product types vendors can create.', 'bazaar' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Category Access', 'bazaar' ); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="vendor_can_create_categories" value="1" <?php checked( $settings['vendor_can_create_categories'] ?? false ); ?>>
                                        <?php esc_html_e( 'Allow vendors to create product categories', 'bazaar' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Tag Access', 'bazaar' ); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="vendor_can_create_tags" value="1" <?php checked( $settings['vendor_can_create_tags'] ?? true ); ?>>
                                        <?php esc_html_e( 'Allow vendors to create product tags', 'bazaar' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Edit Published Products', 'bazaar' ); ?></th>
                                <td>
                                    <select name="edited_product_status">
                                        <option value="publish" <?php selected( $settings['edited_product_status'] ?? 'publish', 'publish' ); ?>><?php esc_html_e( 'Keep Published', 'bazaar' ); ?></option>
                                        <option value="pending" <?php selected( $settings['edited_product_status'] ?? '', 'pending' ); ?>><?php esc_html_e( 'Set to Pending Review', 'bazaar' ); ?></option>
                                    </select>
                                    <p class="description"><?php esc_html_e( 'Product status when vendor edits a published product.', 'bazaar' ); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="bazaar-settings-section">
                        <h2><?php esc_html_e( 'Order Settings', 'bazaar' ); ?></h2>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Order Status for Withdraw', 'bazaar' ); ?></th>
                                <td>
                                    <?php
                                    $order_statuses = wc_get_order_statuses();
                                    $withdraw_statuses = $settings['withdraw_order_status'] ?? array( 'wc-completed' );
                                    foreach ( $order_statuses as $status => $label ) :
                                    ?>
                                        <label style="display: block; margin-bottom: 5px;">
                                            <input type="checkbox" name="withdraw_order_status[]" value="<?php echo esc_attr( $status ); ?>" <?php checked( in_array( $status, $withdraw_statuses, true ) ); ?>>
                                            <?php echo esc_html( $label ); ?>
                                        </label>
                                    <?php endforeach; ?>
                                    <p class="description"><?php esc_html_e( 'Order statuses that count towards vendor balance.', 'bazaar' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Vendor Order Management', 'bazaar' ); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="vendor_can_manage_orders" value="1" <?php checked( $settings['vendor_can_manage_orders'] ?? true ); ?>>
                                        <?php esc_html_e( 'Allow vendors to manage order status', 'bazaar' ); ?>
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php
                    break;

                case 'withdraw':
                    ?>
                    <!-- Withdraw Settings -->
                    <div class="bazaar-settings-section">
                        <h2><?php esc_html_e( 'Withdraw Options', 'bazaar' ); ?></h2>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Withdraw Methods', 'bazaar' ); ?></th>
                                <td>
                                    <?php
                                    $methods = array(
                                        'paypal'        => __( 'PayPal', 'bazaar' ),
                                        'bank_transfer' => __( 'Bank Transfer', 'bazaar' ),
                                        'stripe'        => __( 'Stripe', 'bazaar' ),
                                        'skrill'        => __( 'Skrill', 'bazaar' ),
                                    );
                                    $enabled_methods = $settings['withdraw_methods'] ?? array( 'paypal', 'bank_transfer' );
                                    foreach ( $methods as $method => $label ) :
                                    ?>
                                        <label style="display: block; margin-bottom: 5px;">
                                            <input type="checkbox" name="withdraw_methods[]" value="<?php echo esc_attr( $method ); ?>" <?php checked( in_array( $method, $enabled_methods, true ) ); ?>>
                                            <?php echo esc_html( $label ); ?>
                                        </label>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="minimum_withdraw"><?php esc_html_e( 'Minimum Withdraw Amount', 'bazaar' ); ?></label>
                                </th>
                                <td>
                                    <input type="number" name="minimum_withdraw" id="minimum_withdraw" value="<?php echo esc_attr( $settings['minimum_withdraw'] ?? 50 ); ?>" step="0.01" min="0" class="small-text">
                                    <span><?php echo esc_html( get_woocommerce_currency_symbol() ); ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="withdraw_limit"><?php esc_html_e( 'Withdraw Request Limit', 'bazaar' ); ?></label>
                                </th>
                                <td>
                                    <input type="number" name="withdraw_limit" id="withdraw_limit" value="<?php echo esc_attr( $settings['withdraw_limit'] ?? 0 ); ?>" min="0" class="small-text">
                                    <?php esc_html_e( 'per day (0 = unlimited)', 'bazaar' ); ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Withdraw Threshold', 'bazaar' ); ?></th>
                                <td>
                                    <input type="number" name="withdraw_threshold" id="withdraw_threshold" value="<?php echo esc_attr( $settings['withdraw_threshold'] ?? 0 ); ?>" min="0" class="small-text">
                                    <?php esc_html_e( 'days after order completion', 'bazaar' ); ?>
                                    <p class="description"><?php esc_html_e( 'Number of days after order completion before earnings can be withdrawn.', 'bazaar' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Hide Withdraw Balance', 'bazaar' ); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="hide_withdraw_balance" value="1" <?php checked( $settings['hide_withdraw_balance'] ?? false ); ?>>
                                        <?php esc_html_e( 'Hide balance from vendor dashboard when below minimum', 'bazaar' ); ?>
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="bazaar-settings-section">
                        <h2><?php esc_html_e( 'Disbursement Schedule', 'bazaar' ); ?></h2>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Disbursement Mode', 'bazaar' ); ?></th>
                                <td>
                                    <select name="disbursement_mode">
                                        <option value="manual" <?php selected( $settings['disbursement_mode'] ?? 'manual', 'manual' ); ?>><?php esc_html_e( 'Manual (Process Each Request)', 'bazaar' ); ?></option>
                                        <option value="schedule" <?php selected( $settings['disbursement_mode'] ?? '', 'schedule' ); ?>><?php esc_html_e( 'Scheduled (Automatic)', 'bazaar' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Schedule Frequency', 'bazaar' ); ?></th>
                                <td>
                                    <select name="disbursement_schedule">
                                        <option value="weekly" <?php selected( $settings['disbursement_schedule'] ?? 'weekly', 'weekly' ); ?>><?php esc_html_e( 'Weekly', 'bazaar' ); ?></option>
                                        <option value="biweekly" <?php selected( $settings['disbursement_schedule'] ?? '', 'biweekly' ); ?>><?php esc_html_e( 'Bi-weekly', 'bazaar' ); ?></option>
                                        <option value="monthly" <?php selected( $settings['disbursement_schedule'] ?? '', 'monthly' ); ?>><?php esc_html_e( 'Monthly', 'bazaar' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php
                    break;

                case 'pages':
                    ?>
                    <!-- Page Settings -->
                    <div class="bazaar-settings-section">
                        <h2><?php esc_html_e( 'Page Setup', 'bazaar' ); ?></h2>
                        
                        <table class="form-table">
                            <?php
                            $pages = get_pages();
                            $page_options = array( '' => __( '— Select —', 'bazaar' ) );
                            foreach ( $pages as $page ) {
                                $page_options[ $page->ID ] = $page->post_title;
                            }
                            
                            $page_settings = array(
                                'dashboard_page'    => __( 'Vendor Dashboard', 'bazaar' ),
                                'store_listing'     => __( 'Store Listing', 'bazaar' ),
                                'registration_page' => __( 'Vendor Registration', 'bazaar' ),
                                'terms_page'        => __( 'Terms & Conditions', 'bazaar' ),
                            );
                            
                            foreach ( $page_settings as $setting_key => $setting_label ) :
                            ?>
                            <tr>
                                <th scope="row">
                                    <label for="<?php echo esc_attr( $setting_key ); ?>"><?php echo esc_html( $setting_label ); ?></label>
                                </th>
                                <td>
                                    <select name="<?php echo esc_attr( $setting_key ); ?>" id="<?php echo esc_attr( $setting_key ); ?>">
                                        <?php foreach ( $page_options as $value => $label ) : ?>
                                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $settings[ $setting_key ] ?? '', $value ); ?>>
                                                <?php echo esc_html( $label ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                    <?php
                    break;

                case 'appearance':
                    ?>
                    <!-- Appearance Settings -->
                    <div class="bazaar-settings-section">
                        <h2><?php esc_html_e( 'Store Appearance', 'bazaar' ); ?></h2>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Store Header Template', 'bazaar' ); ?></th>
                                <td>
                                    <select name="store_header_template">
                                        <option value="default" <?php selected( $settings['store_header_template'] ?? 'default', 'default' ); ?>><?php esc_html_e( 'Default', 'bazaar' ); ?></option>
                                        <option value="layout1" <?php selected( $settings['store_header_template'] ?? '', 'layout1' ); ?>><?php esc_html_e( 'Layout 1 (Banner Full Width)', 'bazaar' ); ?></option>
                                        <option value="layout2" <?php selected( $settings['store_header_template'] ?? '', 'layout2' ); ?>><?php esc_html_e( 'Layout 2 (Centered)', 'bazaar' ); ?></option>
                                        <option value="layout3" <?php selected( $settings['store_header_template'] ?? '', 'layout3' ); ?>><?php esc_html_e( 'Layout 3 (Sidebar)', 'bazaar' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Store Sidebar', 'bazaar' ); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="store_sidebar" value="1" <?php checked( $settings['store_sidebar'] ?? true ); ?>>
                                        <?php esc_html_e( 'Show sidebar on store pages', 'bazaar' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Store Tab Style', 'bazaar' ); ?></th>
                                <td>
                                    <select name="store_tab_style">
                                        <option value="horizontal" <?php selected( $settings['store_tab_style'] ?? 'horizontal', 'horizontal' ); ?>><?php esc_html_e( 'Horizontal Tabs', 'bazaar' ); ?></option>
                                        <option value="vertical" <?php selected( $settings['store_tab_style'] ?? '', 'vertical' ); ?>><?php esc_html_e( 'Vertical Tabs', 'bazaar' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Products Per Page', 'bazaar' ); ?></th>
                                <td>
                                    <input type="number" name="products_per_page" value="<?php echo esc_attr( $settings['products_per_page'] ?? 12 ); ?>" min="1" class="small-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Primary Color', 'bazaar' ); ?></th>
                                <td>
                                    <input type="color" name="primary_color" value="<?php echo esc_attr( $settings['primary_color'] ?? '#6366f1' ); ?>">
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="bazaar-settings-section">
                        <h2><?php esc_html_e( 'Map Settings', 'bazaar' ); ?></h2>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Enable Store Map', 'bazaar' ); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_store_map" value="1" <?php checked( $settings['enable_store_map'] ?? false ); ?>>
                                        <?php esc_html_e( 'Show map on store pages', 'bazaar' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="google_maps_api"><?php esc_html_e( 'Google Maps API Key', 'bazaar' ); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="google_maps_api" id="google_maps_api" value="<?php echo esc_attr( $settings['google_maps_api'] ?? '' ); ?>" class="regular-text">
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php
                    break;

                case 'privacy':
                    ?>
                    <!-- Privacy Settings -->
                    <div class="bazaar-settings-section">
                        <h2><?php esc_html_e( 'Privacy Policy', 'bazaar' ); ?></h2>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Enable Privacy Policy', 'bazaar' ); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_privacy_policy" value="1" <?php checked( $settings['enable_privacy_policy'] ?? true ); ?>>
                                        <?php esc_html_e( 'Require vendors to accept privacy policy', 'bazaar' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="privacy_page"><?php esc_html_e( 'Privacy Policy Page', 'bazaar' ); ?></label>
                                </th>
                                <td>
                                    <select name="privacy_page" id="privacy_page">
                                        <?php foreach ( $page_options as $value => $label ) : ?>
                                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $settings['privacy_page'] ?? '', $value ); ?>>
                                                <?php echo esc_html( $label ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="bazaar-settings-section">
                        <h2><?php esc_html_e( 'Store Terms & Conditions', 'bazaar' ); ?></h2>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Enable Store T&C', 'bazaar' ); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_store_terms" value="1" <?php checked( $settings['enable_store_terms'] ?? false ); ?>>
                                        <?php esc_html_e( 'Allow vendors to create store-specific terms', 'bazaar' ); ?>
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php
                    break;

                case 'emails':
                    ?>
                    <!-- Email Settings -->
                    <div class="bazaar-settings-section">
                        <h2><?php esc_html_e( 'Email Notifications', 'bazaar' ); ?></h2>
                        
                        <table class="bazaar-email-table widefat">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e( 'Email', 'bazaar' ); ?></th>
                                    <th><?php esc_html_e( 'Recipient', 'bazaar' ); ?></th>
                                    <th><?php esc_html_e( 'Status', 'bazaar' ); ?></th>
                                    <th><?php esc_html_e( 'Actions', 'bazaar' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $emails = array(
                                    array(
                                        'id'        => 'new_vendor',
                                        'title'     => __( 'New Vendor Registration', 'bazaar' ),
                                        'recipient' => __( 'Admin', 'bazaar' ),
                                    ),
                                    array(
                                        'id'        => 'vendor_approved',
                                        'title'     => __( 'Vendor Approved', 'bazaar' ),
                                        'recipient' => __( 'Vendor', 'bazaar' ),
                                    ),
                                    array(
                                        'id'        => 'new_order',
                                        'title'     => __( 'New Vendor Order', 'bazaar' ),
                                        'recipient' => __( 'Vendor', 'bazaar' ),
                                    ),
                                    array(
                                        'id'        => 'withdraw_request',
                                        'title'     => __( 'Withdraw Request', 'bazaar' ),
                                        'recipient' => __( 'Admin', 'bazaar' ),
                                    ),
                                    array(
                                        'id'        => 'withdraw_approved',
                                        'title'     => __( 'Withdraw Approved', 'bazaar' ),
                                        'recipient' => __( 'Vendor', 'bazaar' ),
                                    ),
                                    array(
                                        'id'        => 'product_published',
                                        'title'     => __( 'Product Published', 'bazaar' ),
                                        'recipient' => __( 'Vendor', 'bazaar' ),
                                    ),
                                );
                                
                                foreach ( $emails as $email ) :
                                    $enabled = $settings[ 'email_' . $email['id'] ] ?? true;
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php echo esc_html( $email['title'] ); ?></strong>
                                    </td>
                                    <td><?php echo esc_html( $email['recipient'] ); ?></td>
                                    <td>
                                        <label class="bazaar-toggle">
                                            <input type="checkbox" name="email_<?php echo esc_attr( $email['id'] ); ?>" value="1" <?php checked( $enabled ); ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <a href="#" class="button button-small"><?php esc_html_e( 'Edit Template', 'bazaar' ); ?></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    break;
            endswitch;
            ?>
        </div>

        <div class="bazaar-settings-footer">
            <button type="submit" name="bazaar_save_settings" class="button button-primary button-large">
                <?php esc_html_e( 'Save Settings', 'bazaar' ); ?>
            </button>
        </div>
    </form>
</div>
