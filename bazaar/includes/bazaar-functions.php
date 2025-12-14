<?php
/**
 * Core Functions.
 *
 * @package Bazaar\Functions
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get the vendor dashboard URL.
 *
 * @return string
 */
function bazaar_get_dashboard_url() {
    $page_id = get_option( 'bazaar_vendor_dashboard_page' );

    if ( $page_id ) {
        return get_permalink( $page_id );
    }

    return home_url( '/vendor-dashboard/' );
}

/**
 * Get the vendor registration URL.
 *
 * @return string
 */
function bazaar_get_registration_url() {
    $page_id = get_option( 'bazaar_vendor_registration_page' );

    if ( $page_id ) {
        return get_permalink( $page_id );
    }

    return home_url( '/become-a-vendor/' );
}

/**
 * Get store listing URL.
 *
 * @return string
 */
function bazaar_get_store_listing_url() {
    $page_id = get_option( 'bazaar_store_listing_page' );

    if ( $page_id ) {
        return get_permalink( $page_id );
    }

    return home_url( '/stores/' );
}

/**
 * Get dashboard tab URL.
 *
 * @param string $tab Tab slug.
 * @return string
 */
function bazaar_get_dashboard_tab_url( $tab ) {
    $dashboard_url = bazaar_get_dashboard_url();

    return add_query_arg( 'tab', $tab, $dashboard_url );
}

/**
 * Check if current user is vendor.
 *
 * @return bool
 */
function bazaar_is_vendor() {
    return Bazaar_Roles::is_vendor();
}

/**
 * Check if current user is active vendor.
 *
 * @return bool
 */
function bazaar_is_active_vendor() {
    return Bazaar_Roles::is_active_vendor();
}

/**
 * Get current vendor.
 *
 * @return array|false
 */
function bazaar_get_current_vendor() {
    $user_id = get_current_user_id();

    if ( ! $user_id || ! Bazaar_Roles::is_vendor( $user_id ) ) {
        return false;
    }

    return Bazaar_Vendor::get_vendor( $user_id );
}

/**
 * Get vendor store URL.
 *
 * @param int $vendor_id Vendor ID.
 * @return string
 */
function bazaar_get_vendor_store_url( $vendor_id ) {
    return Bazaar_Vendor::get_store_url( $vendor_id );
}

/**
 * Get vendor from product.
 *
 * @param int $product_id Product ID.
 * @return array|false
 */
function bazaar_get_product_vendor( $product_id ) {
    $vendor_id = Bazaar_Product::get_product_vendor( $product_id );

    if ( ! $vendor_id ) {
        return false;
    }

    return Bazaar_Vendor::get_vendor( $vendor_id );
}

/**
 * Format price with currency.
 *
 * @param float $price Price.
 * @return string
 */
function bazaar_format_price( $price ) {
    if ( function_exists( 'wc_price' ) ) {
        return wc_price( $price );
    }

    return number_format( $price, 2 );
}

/**
 * Get dashboard tabs.
 *
 * @return array
 */
function bazaar_get_dashboard_tabs() {
    $tabs = array(
        'overview'     => array(
            'title' => __( 'Dashboard', 'bazaar' ),
            'icon'  => 'dashicons-dashboard',
        ),
        'products'     => array(
            'title' => __( 'Products', 'bazaar' ),
            'icon'  => 'dashicons-products',
        ),
        'orders'       => array(
            'title' => __( 'Orders', 'bazaar' ),
            'icon'  => 'dashicons-cart',
        ),
        'earnings'     => array(
            'title' => __( 'Earnings', 'bazaar' ),
            'icon'  => 'dashicons-chart-area',
        ),
        'withdraw'     => array(
            'title' => __( 'Withdrawals', 'bazaar' ),
            'icon'  => 'dashicons-money-alt',
        ),
        'coupons'      => array(
            'title' => __( 'Coupons', 'bazaar' ),
            'icon'  => 'dashicons-tickets-alt',
        ),
        'reviews'      => array(
            'title' => __( 'Reviews', 'bazaar' ),
            'icon'  => 'dashicons-star-filled',
        ),
        'shipping'     => array(
            'title' => __( 'Shipping', 'bazaar' ),
            'icon'  => 'dashicons-car',
        ),
        'settings'     => array(
            'title' => __( 'Settings', 'bazaar' ),
            'icon'  => 'dashicons-admin-settings',
        ),
    );

    // Add coupons only if enabled
    if ( 'yes' !== get_option( 'bazaar_vendor_coupon_enabled', 'yes' ) ) {
        unset( $tabs['coupons'] );
    }

    // Add shipping only if enabled
    if ( 'yes' !== get_option( 'bazaar_vendor_shipping_enabled', 'yes' ) ) {
        unset( $tabs['shipping'] );
    }

    return apply_filters( 'bazaar_dashboard_tabs', $tabs );
}

/**
 * Get vendor balance formatted.
 *
 * @param int $vendor_id Vendor ID.
 * @return string
 */
function bazaar_get_vendor_balance_formatted( $vendor_id ) {
    $balance = Bazaar_Vendor::get_balance( $vendor_id );

    return bazaar_format_price( $balance );
}

/**
 * Check if on vendor store page.
 *
 * @return bool
 */
function bazaar_is_store_page() {
    return (bool) get_query_var( 'bazaar_store' );
}

/**
 * Get current store vendor ID.
 *
 * @return int
 */
function bazaar_get_current_store_vendor_id() {
    $store_slug = get_query_var( 'bazaar_store' );

    if ( ! $store_slug ) {
        return 0;
    }

    return Bazaar_Vendor::get_vendor_id_by_slug( $store_slug );
}

/**
 * Get order statuses for vendor.
 *
 * @return array
 */
function bazaar_get_order_statuses() {
    return array(
        'pending'    => __( 'Pending', 'bazaar' ),
        'processing' => __( 'Processing', 'bazaar' ),
        'on-hold'    => __( 'On Hold', 'bazaar' ),
        'completed'  => __( 'Completed', 'bazaar' ),
        'cancelled'  => __( 'Cancelled', 'bazaar' ),
        'refunded'   => __( 'Refunded', 'bazaar' ),
        'failed'     => __( 'Failed', 'bazaar' ),
    );
}

/**
 * Get product statuses.
 *
 * @return array
 */
function bazaar_get_product_statuses() {
    return array(
        'publish' => __( 'Published', 'bazaar' ),
        'draft'   => __( 'Draft', 'bazaar' ),
        'pending' => __( 'Pending Review', 'bazaar' ),
    );
}

/**
 * Get countries list.
 *
 * @return array
 */
function bazaar_get_countries() {
    if ( function_exists( 'WC' ) && WC()->countries ) {
        return WC()->countries->get_countries();
    }

    return array();
}

/**
 * Get states for country.
 *
 * @param string $country Country code.
 * @return array
 */
function bazaar_get_states( $country ) {
    if ( function_exists( 'WC' ) && WC()->countries ) {
        return WC()->countries->get_states( $country );
    }

    return array();
}

/**
 * Get product categories.
 *
 * @return array
 */
function bazaar_get_product_categories() {
    $terms = get_terms(
        array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
        )
    );

    if ( is_wp_error( $terms ) ) {
        return array();
    }

    return $terms;
}

/**
 * Get product tags.
 *
 * @return array
 */
function bazaar_get_product_tags() {
    $terms = get_terms(
        array(
            'taxonomy'   => 'product_tag',
            'hide_empty' => false,
        )
    );

    if ( is_wp_error( $terms ) ) {
        return array();
    }

    return $terms;
}

/**
 * Sanitize commission rate.
 *
 * @param mixed $rate Rate value.
 * @return float
 */
function bazaar_sanitize_commission_rate( $rate ) {
    $rate = floatval( $rate );

    if ( $rate < 0 ) {
        $rate = 0;
    }

    if ( $rate > 100 ) {
        $rate = 100;
    }

    return $rate;
}

/**
 * Get date range for reports.
 *
 * @param string $range Range key.
 * @return array
 */
function bazaar_get_date_range( $range = 'this_month' ) {
    $start_date = '';
    $end_date = current_time( 'Y-m-d 23:59:59' );

    switch ( $range ) {
        case 'today':
            $start_date = current_time( 'Y-m-d 00:00:00' );
            break;

        case 'yesterday':
            $start_date = gmdate( 'Y-m-d 00:00:00', strtotime( '-1 day' ) );
            $end_date = gmdate( 'Y-m-d 23:59:59', strtotime( '-1 day' ) );
            break;

        case 'this_week':
            $start_date = gmdate( 'Y-m-d 00:00:00', strtotime( 'monday this week' ) );
            break;

        case 'last_week':
            $start_date = gmdate( 'Y-m-d 00:00:00', strtotime( 'monday last week' ) );
            $end_date = gmdate( 'Y-m-d 23:59:59', strtotime( 'sunday last week' ) );
            break;

        case 'this_month':
            $start_date = gmdate( 'Y-m-01 00:00:00' );
            break;

        case 'last_month':
            $start_date = gmdate( 'Y-m-01 00:00:00', strtotime( 'first day of last month' ) );
            $end_date = gmdate( 'Y-m-t 23:59:59', strtotime( 'last month' ) );
            break;

        case 'this_year':
            $start_date = gmdate( 'Y-01-01 00:00:00' );
            break;

        case 'last_year':
            $start_date = gmdate( 'Y-01-01 00:00:00', strtotime( 'last year' ) );
            $end_date = gmdate( 'Y-12-31 23:59:59', strtotime( 'last year' ) );
            break;

        case 'all_time':
            $start_date = '2000-01-01 00:00:00';
            break;

        default:
            $start_date = gmdate( 'Y-m-01 00:00:00' );
    }

    return array(
        'start' => $start_date,
        'end'   => $end_date,
    );
}

/**
 * Generate unique store slug.
 *
 * @param string $name Store name.
 * @return string
 */
function bazaar_generate_store_slug( $name ) {
    $slug = sanitize_title( $name );
    $original_slug = $slug;
    $counter = 1;

    while ( Bazaar_Vendor::get_vendor_id_by_slug( $slug ) ) {
        $slug = $original_slug . '-' . $counter;
        $counter++;
    }

    return $slug;
}

/**
 * Log debug message.
 *
 * @param mixed  $message Message to log.
 * @param string $level   Log level.
 */
function bazaar_log( $message, $level = 'debug' ) {
    if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
        return;
    }

    if ( is_array( $message ) || is_object( $message ) ) {
        $message = print_r( $message, true );
    }

    error_log( sprintf( '[Bazaar][%s] %s', strtoupper( $level ), $message ) );
}
