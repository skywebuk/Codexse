<?php
/**
 * Admin Reports Class.
 *
 * @package Bazaar\Admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Admin_Reports Class.
 */
class Bazaar_Admin_Reports {

    /**
     * Get report types.
     *
     * @return array
     */
    public static function get_report_types() {
        return array(
            'overview'   => __( 'Overview', 'bazaar' ),
            'vendors'    => __( 'Vendors', 'bazaar' ),
            'products'   => __( 'Products', 'bazaar' ),
            'orders'     => __( 'Orders', 'bazaar' ),
            'commission' => __( 'Commission', 'bazaar' ),
        );
    }

    /**
     * Get report data.
     *
     * @param string $type       Report type.
     * @param array  $date_range Date range.
     * @return array
     */
    public static function get_report_data( $type, $date_range ) {
        switch ( $type ) {
            case 'vendors':
                return self::get_vendors_report( $date_range );

            case 'products':
                return self::get_products_report( $date_range );

            case 'orders':
                return self::get_orders_report( $date_range );

            case 'commission':
                return self::get_commission_report( $date_range );

            default:
                return self::get_overview_report( $date_range );
        }
    }

    /**
     * Get overview report.
     *
     * @param array $date_range Date range.
     * @return array
     */
    private static function get_overview_report( $date_range ) {
        $commission = Bazaar_Commission::get_admin_report( $date_range['start'], $date_range['end'] );
        $top_vendors = Bazaar_Commission::get_top_vendors( 5, $date_range['start'], $date_range['end'] );
        $withdrawal_stats = Bazaar_Withdrawal::get_statistics();

        // Get chart data
        $chart_data = self::get_sales_chart_data( $date_range );

        return array(
            'summary' => array(
                'gross_sales'      => $commission['gross_sales'],
                'admin_commission' => $commission['admin_commission'],
                'vendor_earnings'  => $commission['vendor_earnings'],
                'total_orders'     => $commission['total_orders'],
                'pending_payouts'  => $withdrawal_stats['pending_amount'],
            ),
            'top_vendors' => $top_vendors,
            'chart'       => $chart_data,
        );
    }

    /**
     * Get vendors report.
     *
     * @param array $date_range Date range.
     * @return array
     */
    private static function get_vendors_report( $date_range ) {
        $top_vendors = Bazaar_Commission::get_top_vendors( 20, $date_range['start'], $date_range['end'] );

        $vendors_data = array();
        foreach ( $top_vendors as $vendor_data ) {
            $vendor = Bazaar_Vendor::get_vendor( $vendor_data->vendor_id );
            if ( $vendor ) {
                $vendors_data[] = array(
                    'id'         => $vendor_data->vendor_id,
                    'name'       => $vendor['store_name'],
                    'email'      => $vendor['email'],
                    'sales'      => $vendor_data->sales,
                    'earnings'   => $vendor_data->earnings,
                    'orders'     => $vendor_data->orders,
                    'rating'     => $vendor['rating'],
                    'status'     => $vendor['status'],
                );
            }
        }

        // Vendor registration stats
        $vendors = Bazaar_Vendor::get_vendors( array( 'number' => -1 ) );
        $pending = Bazaar_Vendor::get_vendors( array( 'status' => 'pending', 'number' => -1 ) );
        $approved = Bazaar_Vendor::get_vendors( array( 'status' => 'approved', 'number' => -1 ) );

        return array(
            'vendors'       => $vendors_data,
            'total'         => $vendors['total'],
            'pending'       => $pending['total'],
            'approved'      => $approved['total'],
        );
    }

    /**
     * Get products report.
     *
     * @param array $date_range Date range.
     * @return array
     */
    private static function get_products_report( $date_range ) {
        global $wpdb;

        // Best selling products from vendors
        $products = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT p.ID, p.post_title, pm.meta_value as total_sales, pm2.meta_value as vendor_id
                FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'total_sales'
                LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_bazaar_vendor_id'
                WHERE p.post_type = 'product' AND p.post_status = 'publish'
                AND pm2.meta_value IS NOT NULL AND pm2.meta_value != ''
                ORDER BY CAST(pm.meta_value AS UNSIGNED) DESC
                LIMIT 20"
            )
        );

        $products_data = array();
        foreach ( $products as $product ) {
            $wc_product = wc_get_product( $product->ID );
            $vendor = Bazaar_Vendor::get_vendor( $product->vendor_id );

            $products_data[] = array(
                'id'          => $product->ID,
                'name'        => $product->post_title,
                'price'       => $wc_product ? $wc_product->get_price() : 0,
                'total_sales' => $product->total_sales,
                'vendor_name' => $vendor ? $vendor['store_name'] : '-',
                'vendor_id'   => $product->vendor_id,
            );
        }

        // Product stats
        $total = new WP_Query(
            array(
                'post_type'  => 'product',
                'meta_query' => array(
                    array(
                        'key'     => '_bazaar_vendor_id',
                        'compare' => 'EXISTS',
                    ),
                ),
                'fields'     => 'ids',
            )
        );

        $pending = new WP_Query(
            array(
                'post_type'   => 'product',
                'post_status' => 'pending',
                'meta_query'  => array(
                    array(
                        'key'     => '_bazaar_vendor_id',
                        'compare' => 'EXISTS',
                    ),
                ),
                'fields'      => 'ids',
            )
        );

        return array(
            'products'        => $products_data,
            'total_products'  => $total->found_posts,
            'pending_review'  => $pending->found_posts,
        );
    }

    /**
     * Get orders report.
     *
     * @param array $date_range Date range.
     * @return array
     */
    private static function get_orders_report( $date_range ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_sub_orders';

        // Order status breakdown
        $status_breakdown = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT status, COUNT(*) as count, SUM(order_total) as total
                FROM {$table_name}
                WHERE created_at >= %s AND created_at <= %s
                GROUP BY status",
                $date_range['start'],
                $date_range['end']
            )
        );

        // Recent orders
        $recent_orders = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT so.*, u.display_name as vendor_name
                FROM {$table_name} so
                LEFT JOIN {$wpdb->users} u ON so.vendor_id = u.ID
                WHERE so.created_at >= %s AND so.created_at <= %s
                ORDER BY so.created_at DESC
                LIMIT 50",
                $date_range['start'],
                $date_range['end']
            )
        );

        return array(
            'status_breakdown' => $status_breakdown,
            'recent_orders'    => $recent_orders,
        );
    }

    /**
     * Get commission report.
     *
     * @param array $date_range Date range.
     * @return array
     */
    private static function get_commission_report( $date_range ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_vendor_balance';

        // Commission by vendor
        $by_vendor = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT vendor_id, SUM(amount) as gross, SUM(commission) as admin_commission, SUM(net_amount) as vendor_earning
                FROM {$table_name}
                WHERE trn_type = 'credit' AND trn_status = 'completed'
                AND created_at >= %s AND created_at <= %s
                GROUP BY vendor_id
                ORDER BY admin_commission DESC
                LIMIT 20",
                $date_range['start'],
                $date_range['end']
            )
        );

        $vendors_data = array();
        foreach ( $by_vendor as $row ) {
            $vendor = Bazaar_Vendor::get_vendor( $row->vendor_id );
            $vendors_data[] = array(
                'vendor_id'        => $row->vendor_id,
                'vendor_name'      => $vendor ? $vendor['store_name'] : 'Unknown',
                'gross_sales'      => $row->gross,
                'admin_commission' => $row->admin_commission,
                'vendor_earning'   => $row->vendor_earning,
            );
        }

        // Total summary
        $totals = Bazaar_Commission::get_admin_report( $date_range['start'], $date_range['end'] );

        return array(
            'by_vendor' => $vendors_data,
            'totals'    => $totals,
        );
    }

    /**
     * Get sales chart data.
     *
     * @param array $date_range Date range.
     * @return array
     */
    private static function get_sales_chart_data( $date_range ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_vendor_balance';

        // Determine grouping based on range
        $start = strtotime( $date_range['start'] );
        $end = strtotime( $date_range['end'] );
        $days = ( $end - $start ) / DAY_IN_SECONDS;

        if ( $days <= 31 ) {
            $format = '%Y-%m-%d';
        } else {
            $format = '%Y-%m';
        }

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT DATE_FORMAT(created_at, %s) as date,
                        SUM(amount) as gross,
                        SUM(commission) as admin_commission,
                        SUM(net_amount) as vendor_earning
                FROM {$table_name}
                WHERE trn_type = 'credit' AND trn_status = 'completed'
                AND created_at >= %s AND created_at <= %s
                GROUP BY DATE_FORMAT(created_at, %s)
                ORDER BY date ASC",
                $format,
                $date_range['start'],
                $date_range['end'],
                $format
            )
        );

        $labels = array();
        $gross_data = array();
        $commission_data = array();
        $earning_data = array();

        foreach ( $results as $row ) {
            $labels[] = $row->date;
            $gross_data[] = floatval( $row->gross );
            $commission_data[] = floatval( $row->admin_commission );
            $earning_data[] = floatval( $row->vendor_earning );
        }

        return array(
            'labels'     => $labels,
            'gross'      => $gross_data,
            'commission' => $commission_data,
            'earnings'   => $earning_data,
        );
    }
}
