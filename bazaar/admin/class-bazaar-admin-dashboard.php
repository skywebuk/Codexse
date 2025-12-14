<?php
/**
 * Admin Dashboard Class.
 *
 * @package Bazaar\Admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Admin_Dashboard Class.
 */
class Bazaar_Admin_Dashboard {

    /**
     * Get dashboard statistics.
     *
     * @param string $range Date range (today, week, month, year).
     * @return array
     */
    public static function get_stats( $range = 'month' ) {
        global $wpdb;

        $date_range = self::get_date_range( $range );
        $start_date = $date_range['start'];
        $end_date = $date_range['end'];

        // Get vendor counts
        $total_vendors = count( get_users( array(
            'role'   => 'bazaar_vendor',
            'fields' => 'ID',
        ) ) );

        $pending_vendors = count( get_users( array(
            'role'       => 'bazaar_vendor',
            'meta_key'   => '_bazaar_vendor_status',
            'meta_value' => 'pending',
            'fields'     => 'ID',
        ) ) );

        // Get product counts
        $total_products = wp_count_posts( 'product' )->publish;
        $pending_products = wp_count_posts( 'product' )->pending;

        // Get sales data
        $balance_table = $wpdb->prefix . 'bazaar_vendor_balance';
        $table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$balance_table}'" );

        $gross_sales = 0;
        $admin_commission = 0;
        $vendor_earnings = 0;
        $total_orders = 0;

        if ( $table_exists ) {
            $sales_data = $wpdb->get_row( $wpdb->prepare(
                "SELECT
                    COALESCE(SUM(amount), 0) as gross_sales,
                    COALESCE(SUM(commission), 0) as admin_commission,
                    COALESCE(SUM(net_amount), 0) as vendor_earnings,
                    COUNT(DISTINCT order_id) as total_orders
                FROM {$balance_table}
                WHERE trn_type = 'credit'
                AND trn_status = 'completed'
                AND created_at >= %s
                AND created_at <= %s",
                $start_date,
                $end_date
            ) );

            if ( $sales_data ) {
                $gross_sales = floatval( $sales_data->gross_sales );
                $admin_commission = floatval( $sales_data->admin_commission );
                $vendor_earnings = floatval( $sales_data->vendor_earnings );
                $total_orders = intval( $sales_data->total_orders );
            }
        }

        // Get withdrawal stats
        $withdrawals_table = $wpdb->prefix . 'bazaar_withdrawals';
        $withdrawals_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$withdrawals_table}'" );

        $pending_withdrawals = 0;
        $pending_withdrawal_amount = 0;

        if ( $withdrawals_exists ) {
            $pending_data = $wpdb->get_row(
                "SELECT COUNT(*) as count, COALESCE(SUM(amount), 0) as amount
                FROM {$withdrawals_table}
                WHERE status = 'pending'"
            );

            if ( $pending_data ) {
                $pending_withdrawals = intval( $pending_data->count );
                $pending_withdrawal_amount = floatval( $pending_data->amount );
            }
        }

        // Calculate sales growth
        $prev_range = self::get_date_range( $range, true );
        $prev_sales = 0;

        if ( $table_exists ) {
            $prev_sales = $wpdb->get_var( $wpdb->prepare(
                "SELECT COALESCE(SUM(amount), 0)
                FROM {$balance_table}
                WHERE trn_type = 'credit'
                AND trn_status = 'completed'
                AND created_at >= %s
                AND created_at <= %s",
                $prev_range['start'],
                $prev_range['end']
            ) );
        }

        $sales_growth = 0;
        if ( $prev_sales > 0 ) {
            $sales_growth = round( ( ( $gross_sales - $prev_sales ) / $prev_sales ) * 100, 1 );
        }

        return array(
            'total_vendors'            => $total_vendors,
            'pending_vendors'          => $pending_vendors,
            'total_products'           => $total_products,
            'pending_products'         => $pending_products,
            'gross_sales'              => $gross_sales,
            'admin_commission'         => $admin_commission,
            'vendor_earnings'          => $vendor_earnings,
            'total_orders'             => $total_orders,
            'pending_withdrawals'      => $pending_withdrawals,
            'pending_withdrawal_amount' => $pending_withdrawal_amount,
            'sales_growth'             => $sales_growth,
        );
    }

    /**
     * Get chart data for sales overview.
     *
     * @param string $range Date range.
     * @return array
     */
    public static function get_chart_data( $range = 'month' ) {
        global $wpdb;

        $balance_table = $wpdb->prefix . 'bazaar_vendor_balance';
        $table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$balance_table}'" );

        $labels = array();
        $sales = array();
        $commission = array();

        if ( ! $table_exists ) {
            // Return sample data for empty state
            for ( $i = 1; $i <= 30; $i++ ) {
                $labels[] = $i;
                $sales[] = 0;
                $commission[] = 0;
            }
            return compact( 'labels', 'sales', 'commission' );
        }

        $date_range = self::get_date_range( $range );

        // Determine grouping based on range
        $group_format = '%Y-%m-%d';
        $label_format = 'd';

        if ( $range === 'year' ) {
            $group_format = '%Y-%m';
            $label_format = 'M';
        } elseif ( $range === 'today' ) {
            $group_format = '%Y-%m-%d %H:00:00';
            $label_format = 'H:i';
        }

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT
                DATE_FORMAT(created_at, %s) as period,
                COALESCE(SUM(amount), 0) as total_sales,
                COALESCE(SUM(commission), 0) as total_commission
            FROM {$balance_table}
            WHERE trn_type = 'credit'
            AND trn_status = 'completed'
            AND created_at >= %s
            AND created_at <= %s
            GROUP BY period
            ORDER BY period ASC",
            $group_format,
            $date_range['start'],
            $date_range['end']
        ) );

        $data = array();
        foreach ( $results as $row ) {
            $data[ $row->period ] = array(
                'sales'      => floatval( $row->total_sales ),
                'commission' => floatval( $row->total_commission ),
            );
        }

        // Fill in all dates/periods
        $current = new DateTime( $date_range['start'] );
        $end = new DateTime( $date_range['end'] );

        while ( $current <= $end ) {
            if ( $range === 'year' ) {
                $key = $current->format( 'Y-m' );
                $label = $current->format( 'M' );
                $current->modify( '+1 month' );
            } elseif ( $range === 'today' ) {
                $key = $current->format( 'Y-m-d H:00:00' );
                $label = $current->format( 'H:i' );
                $current->modify( '+1 hour' );
            } else {
                $key = $current->format( 'Y-m-d' );
                $label = $current->format( 'd' );
                $current->modify( '+1 day' );
            }

            $labels[] = $label;
            $sales[] = isset( $data[ $key ] ) ? $data[ $key ]['sales'] : 0;
            $commission[] = isset( $data[ $key ] ) ? $data[ $key ]['commission'] : 0;
        }

        return compact( 'labels', 'sales', 'commission' );
    }

    /**
     * Get top vendors by earnings.
     *
     * @param int    $limit Number of vendors.
     * @param string $range Date range.
     * @return array
     */
    public static function get_top_vendors( $limit = 5, $range = 'month' ) {
        global $wpdb;

        $balance_table = $wpdb->prefix . 'bazaar_vendor_balance';
        $table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$balance_table}'" );

        if ( ! $table_exists ) {
            return array();
        }

        $date_range = self::get_date_range( $range );

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT
                vendor_id,
                COALESCE(SUM(net_amount), 0) as earnings,
                COALESCE(SUM(amount), 0) as sales,
                COUNT(DISTINCT order_id) as orders
            FROM {$balance_table}
            WHERE trn_type = 'credit'
            AND trn_status = 'completed'
            AND created_at >= %s
            AND created_at <= %s
            GROUP BY vendor_id
            ORDER BY earnings DESC
            LIMIT %d",
            $date_range['start'],
            $date_range['end'],
            $limit
        ) );
    }

    /**
     * Get recent orders.
     *
     * @param int $limit Number of orders.
     * @return array
     */
    public static function get_recent_orders( $limit = 5 ) {
        $orders = wc_get_orders( array(
            'limit'   => $limit,
            'orderby' => 'date',
            'order'   => 'DESC',
            'status'  => array( 'wc-processing', 'wc-completed', 'wc-on-hold' ),
        ) );

        return $orders;
    }

    /**
     * Get pending tasks count.
     *
     * @return array
     */
    public static function get_pending_tasks() {
        global $wpdb;

        // Pending vendors
        $pending_vendors = count( get_users( array(
            'role'       => 'bazaar_vendor',
            'meta_key'   => '_bazaar_vendor_status',
            'meta_value' => 'pending',
            'fields'     => 'ID',
        ) ) );

        // Pending withdrawals
        $withdrawals_table = $wpdb->prefix . 'bazaar_withdrawals';
        $withdrawals_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$withdrawals_table}'" );
        $pending_withdrawals = 0;

        if ( $withdrawals_exists ) {
            $pending_withdrawals = (int) $wpdb->get_var(
                "SELECT COUNT(*) FROM {$withdrawals_table} WHERE status = 'pending'"
            );
        }

        // Pending products
        $pending_products = wp_count_posts( 'product' )->pending;

        // Pending refunds
        $refunds_table = $wpdb->prefix . 'bazaar_refunds';
        $refunds_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$refunds_table}'" );
        $pending_refunds = 0;

        if ( $refunds_exists ) {
            $pending_refunds = (int) $wpdb->get_var(
                "SELECT COUNT(*) FROM {$refunds_table} WHERE status = 'pending'"
            );
        }

        return array(
            'vendors'     => $pending_vendors,
            'withdrawals' => $pending_withdrawals,
            'products'    => $pending_products,
            'refunds'     => $pending_refunds,
        );
    }

    /**
     * Get date range.
     *
     * @param string $range    Range type.
     * @param bool   $previous Get previous period.
     * @return array
     */
    public static function get_date_range( $range, $previous = false ) {
        $now = new DateTime();

        switch ( $range ) {
            case 'today':
                $start = new DateTime( 'today' );
                $end = new DateTime( 'tomorrow' );
                if ( $previous ) {
                    $start->modify( '-1 day' );
                    $end->modify( '-1 day' );
                }
                break;

            case 'week':
                $start = new DateTime( 'monday this week' );
                $end = new DateTime( 'sunday this week' );
                $end->setTime( 23, 59, 59 );
                if ( $previous ) {
                    $start->modify( '-1 week' );
                    $end->modify( '-1 week' );
                }
                break;

            case 'year':
                $start = new DateTime( 'first day of January' );
                $end = new DateTime( 'last day of December' );
                $end->setTime( 23, 59, 59 );
                if ( $previous ) {
                    $start->modify( '-1 year' );
                    $end->modify( '-1 year' );
                }
                break;

            case 'month':
            default:
                $start = new DateTime( 'first day of this month' );
                $end = new DateTime( 'last day of this month' );
                $end->setTime( 23, 59, 59 );
                if ( $previous ) {
                    $start->modify( '-1 month' );
                    $end->modify( '-1 month' );
                }
                break;
        }

        return array(
            'start' => $start->format( 'Y-m-d H:i:s' ),
            'end'   => $end->format( 'Y-m-d H:i:s' ),
        );
    }
}
