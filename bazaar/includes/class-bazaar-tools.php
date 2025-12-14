<?php
/**
 * Tools Class.
 *
 * @package Bazaar
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Tools Class.
 */
class Bazaar_Tools {

    /**
     * Recount vendor orders.
     *
     * @return int Number of vendors updated.
     */
    public static function recount_vendor_orders() {
        global $wpdb;
        
        $vendors = get_users( array( 'role' => 'bazaar_vendor' ) );
        $count = 0;
        
        foreach ( $vendors as $vendor ) {
            $order_count = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}bazaar_orders WHERE vendor_id = %d",
                $vendor->ID
            ) );
            
            update_user_meta( $vendor->ID, '_bazaar_order_count', $order_count );
            $count++;
        }
        
        return $count;
    }

    /**
     * Recalculate commissions.
     *
     * @return int Number of records updated.
     */
    public static function recalculate_commissions() {
        global $wpdb;
        
        $orders_table = $wpdb->prefix . 'bazaar_orders';
        $table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$orders_table}'" );
        
        if ( ! $table_exists ) {
            return 0;
        }
        
        $orders = $wpdb->get_results(
            "SELECT * FROM {$orders_table} WHERE status IN ('completed', 'processing')"
        );
        
        $count = 0;
        $commission_rate = floatval( Bazaar_Settings::get( 'admin_percentage', 10 ) );
        $commission_type = Bazaar_Settings::get( 'commission_type', 'percentage' );
        $flat_rate = floatval( Bazaar_Settings::get( 'admin_flat_commission', 0 ) );
        
        foreach ( $orders as $order ) {
            $admin_commission = 0;
            
            if ( 'percentage' === $commission_type ) {
                $admin_commission = ( $order->total * $commission_rate ) / 100;
            } elseif ( 'flat' === $commission_type ) {
                $admin_commission = $flat_rate;
            } else {
                $admin_commission = ( ( $order->total * $commission_rate ) / 100 ) + $flat_rate;
            }
            
            $vendor_earning = $order->total - $admin_commission;
            
            $wpdb->update(
                $orders_table,
                array(
                    'admin_commission' => $admin_commission,
                    'vendor_earning'   => $vendor_earning,
                ),
                array( 'id' => $order->id ),
                array( '%f', '%f' ),
                array( '%d' )
            );
            
            $count++;
        }
        
        return $count;
    }

    /**
     * Clear marketplace cache.
     *
     * @return bool
     */
    public static function clear_cache() {
        global $wpdb;
        
        // Clear transients
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '%_transient_bazaar_%'" );
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '%_transient_timeout_bazaar_%'" );
        
        // Clear vendor cache
        $vendors = get_users( array( 'role' => 'bazaar_vendor' ) );
        foreach ( $vendors as $vendor ) {
            delete_user_meta( $vendor->ID, '_bazaar_cached_stats' );
        }
        
        do_action( 'bazaar_cache_cleared' );
        
        return true;
    }

    /**
     * Sync vendor products.
     *
     * @return int Number of products synced.
     */
    public static function sync_vendor_products() {
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'post_status'    => 'any',
        );
        
        $products = get_posts( $args );
        $count = 0;
        
        foreach ( $products as $product ) {
            $vendor_id = get_post_meta( $product->ID, '_bazaar_vendor_id', true );
            
            if ( $vendor_id ) {
                $vendor = get_userdata( $vendor_id );
                if ( $vendor && in_array( 'bazaar_vendor', (array) $vendor->roles, true ) ) {
                    $count++;
                }
            }
        }
        
        return $count;
    }

    /**
     * Regenerate commission log.
     *
     * @return int Number of orders processed.
     */
    public static function regenerate_commission_log() {
        global $wpdb;
        
        $orders = wc_get_orders( array(
            'limit'  => -1,
            'status' => array( 'completed', 'processing' ),
        ) );
        
        $count = 0;
        $orders_table = $wpdb->prefix . 'bazaar_orders';
        
        foreach ( $orders as $order ) {
            foreach ( $order->get_items() as $item ) {
                $product_id = $item->get_product_id();
                $vendor_id = get_post_meta( $product_id, '_bazaar_vendor_id', true );
                
                if ( $vendor_id ) {
                    $existing = $wpdb->get_var( $wpdb->prepare(
                        "SELECT id FROM {$orders_table} WHERE order_id = %d AND vendor_id = %d",
                        $order->get_id(),
                        $vendor_id
                    ) );
                    
                    if ( ! $existing ) {
                        $line_total = $item->get_total();
                        $commission_rate = floatval( Bazaar_Settings::get( 'admin_percentage', 10 ) );
                        $admin_commission = ( $line_total * $commission_rate ) / 100;
                        
                        $wpdb->insert(
                            $orders_table,
                            array(
                                'order_id'         => $order->get_id(),
                                'vendor_id'        => $vendor_id,
                                'total'            => $line_total,
                                'admin_commission' => $admin_commission,
                                'vendor_earning'   => $line_total - $admin_commission,
                                'status'           => $order->get_status(),
                                'created_at'       => $order->get_date_created()->format( 'Y-m-d H:i:s' ),
                            ),
                            array( '%d', '%d', '%f', '%f', '%f', '%s', '%s' )
                        );
                        
                        $count++;
                    }
                }
            }
        }
        
        return $count;
    }

    /**
     * Fix vendor balances.
     *
     * @return int Number of vendors fixed.
     */
    public static function fix_vendor_balances() {
        global $wpdb;
        
        $vendors = get_users( array( 'role' => 'bazaar_vendor' ) );
        $orders_table = $wpdb->prefix . 'bazaar_orders';
        $withdrawals_table = $wpdb->prefix . 'bazaar_withdrawals';
        $count = 0;
        
        foreach ( $vendors as $vendor ) {
            // Calculate total earnings
            $total_earnings = $wpdb->get_var( $wpdb->prepare(
                "SELECT COALESCE(SUM(vendor_earning), 0) FROM {$orders_table} WHERE vendor_id = %d AND status = 'completed'",
                $vendor->ID
            ) );
            
            // Calculate total withdrawn
            $total_withdrawn = $wpdb->get_var( $wpdb->prepare(
                "SELECT COALESCE(SUM(amount), 0) FROM {$withdrawals_table} WHERE vendor_id = %d AND status = 'paid'",
                $vendor->ID
            ) );
            
            // Update balance
            $balance = $total_earnings - $total_withdrawn;
            update_user_meta( $vendor->ID, '_bazaar_balance', max( 0, $balance ) );
            
            $count++;
        }
        
        return $count;
    }

    /**
     * Export vendors to CSV.
     */
    public static function export_vendors_csv() {
        $vendors = get_users( array( 'role' => 'bazaar_vendor' ) );
        
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment; filename="bazaar-vendors-' . date( 'Y-m-d' ) . '.csv"' );
        
        $output = fopen( 'php://output', 'w' );
        
        // Header
        fputcsv( $output, array(
            'ID',
            'Username',
            'Email',
            'Store Name',
            'Status',
            'Balance',
            'Total Earnings',
            'Registered',
        ) );
        
        foreach ( $vendors as $vendor ) {
            $store_name = get_user_meta( $vendor->ID, '_bazaar_store_name', true );
            $status = get_user_meta( $vendor->ID, '_bazaar_vendor_status', true );
            $balance = get_user_meta( $vendor->ID, '_bazaar_balance', true );
            $earnings = get_user_meta( $vendor->ID, '_bazaar_total_earnings', true );
            
            fputcsv( $output, array(
                $vendor->ID,
                $vendor->user_login,
                $vendor->user_email,
                $store_name ?: $vendor->display_name,
                $status ?: 'active',
                $balance ?: 0,
                $earnings ?: 0,
                $vendor->user_registered,
            ) );
        }
        
        fclose( $output );
        exit;
    }

    /**
     * Export commissions to CSV.
     */
    public static function export_commissions_csv() {
        global $wpdb;
        
        $orders_table = $wpdb->prefix . 'bazaar_orders';
        $orders = $wpdb->get_results( "SELECT * FROM {$orders_table} ORDER BY created_at DESC" );
        
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment; filename="bazaar-commissions-' . date( 'Y-m-d' ) . '.csv"' );
        
        $output = fopen( 'php://output', 'w' );
        
        // Header
        fputcsv( $output, array(
            'Order ID',
            'Vendor ID',
            'Vendor Name',
            'Order Total',
            'Admin Commission',
            'Vendor Earning',
            'Status',
            'Date',
        ) );
        
        foreach ( $orders as $order ) {
            $vendor = get_userdata( $order->vendor_id );
            
            fputcsv( $output, array(
                $order->order_id,
                $order->vendor_id,
                $vendor ? $vendor->display_name : 'Unknown',
                $order->total,
                $order->admin_commission,
                $order->vendor_earning,
                $order->status,
                $order->created_at,
            ) );
        }
        
        fclose( $output );
        exit;
    }
}
