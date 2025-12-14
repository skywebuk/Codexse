<?php
/**
 * Vendor Dashboard Class.
 *
 * @package Bazaar\Vendor_Dashboard
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Vendor_Dashboard Class.
 */
class Bazaar_Vendor_Dashboard {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_head', array( $this, 'hide_admin_bar' ) );
    }

    /**
     * Enqueue assets on dashboard page.
     */
    public function enqueue_assets() {
        if ( ! is_page( get_option( 'bazaar_vendor_dashboard_page' ) ) ) {
            return;
        }

        wp_enqueue_style( 'bazaar-dashboard' );
        wp_enqueue_script( 'bazaar-dashboard' );

        // Media uploader
        if ( Bazaar_Roles::is_vendor() ) {
            wp_enqueue_media();
        }

        // Chart.js for reports
        wp_enqueue_script(
            'chartjs',
            'https://cdn.jsdelivr.net/npm/chart.js',
            array(),
            '4.4.0',
            true
        );
    }

    /**
     * Hide admin bar for vendors on frontend.
     */
    public function hide_admin_bar() {
        if ( Bazaar_Roles::is_vendor() && ! current_user_can( 'manage_options' ) ) {
            show_admin_bar( false );
        }
    }

    /**
     * Get dashboard overview data.
     *
     * @param int $vendor_id Vendor ID.
     * @return array
     */
    public static function get_overview_data( $vendor_id ) {
        $range = bazaar_get_date_range( 'this_month' );
        $earnings = Bazaar_Commission::get_vendor_earnings( $vendor_id, $range['start'], $range['end'] );
        $order_stats = Bazaar_Orders::get_vendor_order_stats( $vendor_id );

        return array(
            'balance'            => Bazaar_Vendor::get_balance( $vendor_id ),
            'earnings_this_month' => $earnings['net_earnings'],
            'orders_total'       => $order_stats['total'],
            'orders_pending'     => $order_stats['pending'],
            'orders_processing'  => $order_stats['processing'],
            'orders_completed'   => $order_stats['completed'],
            'products_published' => Bazaar_Vendor::get_product_count( $vendor_id, 'publish' ),
            'products_pending'   => Bazaar_Vendor::get_product_count( $vendor_id, 'pending' ),
            'products_draft'     => Bazaar_Vendor::get_product_count( $vendor_id, 'draft' ),
            'rating'             => Bazaar_Vendor::get_vendor_rating( $vendor_id ),
        );
    }

    /**
     * Get sales chart data.
     *
     * @param int    $vendor_id Vendor ID.
     * @param string $period    Period (7days, 30days, this_year).
     * @return array
     */
    public static function get_sales_chart_data( $vendor_id, $period = '30days' ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_vendor_balance';

        switch ( $period ) {
            case '7days':
                $days = 7;
                $format = '%Y-%m-%d';
                break;

            case 'this_year':
                $days = 365;
                $format = '%Y-%m';
                break;

            default:
                $days = 30;
                $format = '%Y-%m-%d';
        }

        $start_date = gmdate( 'Y-m-d', strtotime( "-{$days} days" ) );

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT DATE_FORMAT(created_at, %s) as date, SUM(net_amount) as total
                FROM {$table_name}
                WHERE vendor_id = %d AND trn_type = 'credit' AND trn_status = 'completed' AND created_at >= %s
                GROUP BY DATE_FORMAT(created_at, %s)
                ORDER BY date ASC",
                $format,
                $vendor_id,
                $start_date,
                $format
            )
        );

        $labels = array();
        $data = array();

        foreach ( $results as $row ) {
            $labels[] = $row->date;
            $data[] = floatval( $row->total );
        }

        return array(
            'labels' => $labels,
            'data'   => $data,
        );
    }

    /**
     * Get recent orders.
     *
     * @param int $vendor_id Vendor ID.
     * @param int $limit     Limit.
     * @return array
     */
    public static function get_recent_orders( $vendor_id, $limit = 5 ) {
        $orders = Bazaar_Orders::get_vendor_orders(
            $vendor_id,
            array(
                'per_page' => $limit,
                'page'     => 1,
            )
        );

        $data = array();

        foreach ( $orders['orders'] as $sub_order ) {
            $order = wc_get_order( $sub_order->parent_order_id );

            if ( ! $order ) {
                continue;
            }

            $data[] = array(
                'id'            => $sub_order->id,
                'order_id'      => $sub_order->parent_order_id,
                'customer'      => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                'total'         => $sub_order->order_total,
                'earning'       => $sub_order->vendor_earning,
                'status'        => $sub_order->status,
                'date'          => $sub_order->created_at,
                'items'         => Bazaar_Orders::get_vendor_order_items( $sub_order->parent_order_id, $vendor_id ),
            );
        }

        return $data;
    }

    /**
     * Get recent notifications.
     *
     * @param int $vendor_id Vendor ID.
     * @param int $limit     Limit.
     * @return array
     */
    public static function get_recent_notifications( $vendor_id, $limit = 5 ) {
        $notifications = Bazaar_Notifications::get_notifications(
            $vendor_id,
            array(
                'per_page' => $limit,
                'is_read'  => false,
            )
        );

        return $notifications['notifications'];
    }

    /**
     * Render dashboard tab content.
     *
     * @param string $tab       Tab slug.
     * @param int    $vendor_id Vendor ID.
     */
    public static function render_tab_content( $tab, $vendor_id ) {
        $template = 'dashboard/tabs/' . $tab . '.php';

        $args = array(
            'vendor_id' => $vendor_id,
        );

        // Add tab-specific data
        switch ( $tab ) {
            case 'overview':
                $args['overview'] = self::get_overview_data( $vendor_id );
                $args['recent_orders'] = self::get_recent_orders( $vendor_id );
                $args['notifications'] = self::get_recent_notifications( $vendor_id );
                break;

            case 'products':
                $page = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1;
                $status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : 'all';
                $args['products'] = Bazaar_Product::get_vendor_products(
                    $vendor_id,
                    array(
                        'paged'  => $page,
                        'status' => $status,
                    )
                );
                $args['current_status'] = $status;
                break;

            case 'orders':
                $page = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1;
                $status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
                $args['orders'] = Bazaar_Orders::get_vendor_orders(
                    $vendor_id,
                    array(
                        'per_page' => 20,
                        'page'     => $page,
                        'status'   => $status,
                    )
                );
                $args['order_stats'] = Bazaar_Orders::get_vendor_order_stats( $vendor_id );
                break;

            case 'earnings':
                $range = isset( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : 'this_month';
                $date_range = bazaar_get_date_range( $range );
                $args['earnings'] = Bazaar_Commission::get_vendor_earnings( $vendor_id, $date_range['start'], $date_range['end'] );
                $args['transactions'] = Bazaar_Commission::get_transactions( $vendor_id );
                $args['current_range'] = $range;
                break;

            case 'withdraw':
                $args['balance'] = Bazaar_Vendor::get_balance( $vendor_id );
                $args['min_amount'] = floatval( get_option( 'bazaar_min_withdrawal_amount', 50 ) );
                $args['methods'] = Bazaar_Withdrawal::get_enabled_methods();
                $args['withdrawals'] = Bazaar_Withdrawal::get_vendor_withdrawals( $vendor_id );
                $args['has_pending'] = Bazaar_Withdrawal::has_pending_withdrawal( $vendor_id );
                break;

            case 'coupons':
                $page = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1;
                $args['coupons'] = self::get_vendor_coupons( $vendor_id, $page );
                break;

            case 'reviews':
                $page = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1;
                $args['reviews'] = Bazaar_Reviews::get_vendor_reviews(
                    $vendor_id,
                    array(
                        'per_page' => 10,
                        'page'     => $page,
                        'status'   => '',
                    )
                );
                $args['rating_breakdown'] = Bazaar_Reviews::get_rating_breakdown( $vendor_id );
                break;

            case 'shipping':
                $args['shipping_zones'] = Bazaar_Shipping::get_vendor_shipping_methods( $vendor_id );
                $args['shipping_settings'] = Bazaar_Shipping::get_vendor_shipping_settings( $vendor_id );
                break;

            case 'settings':
                $args['vendor'] = Bazaar_Vendor::get_vendor( $vendor_id );
                $args['withdrawal_methods'] = Bazaar_Withdrawal::get_enabled_methods();
                foreach ( $args['withdrawal_methods'] as $method_id => $method ) {
                    $args['withdrawal_methods'][ $method_id ]['details'] = Bazaar_Withdrawal::get_vendor_method_details( $vendor_id, $method_id );
                }
                break;
        }

        bazaar_get_template( $template, $args );
    }

    /**
     * Get vendor coupons.
     *
     * @param int $vendor_id Vendor ID.
     * @param int $page      Page number.
     * @return array
     */
    private static function get_vendor_coupons( $vendor_id, $page = 1 ) {
        $args = array(
            'post_type'      => 'shop_coupon',
            'post_status'    => 'any',
            'author'         => $vendor_id,
            'posts_per_page' => 20,
            'paged'          => $page,
        );

        $query = new WP_Query( $args );

        return array(
            'coupons' => $query->posts,
            'total'   => $query->found_posts,
            'pages'   => $query->max_num_pages,
        );
    }
}

// Initialize
new Bazaar_Vendor_Dashboard();
