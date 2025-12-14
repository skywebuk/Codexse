<?php
/**
 * Commission Management Class.
 *
 * @package Bazaar\Commission
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Commission Class.
 */
class Bazaar_Commission {

    /**
     * Constructor.
     */
    public function __construct() {
        // Hook into order completion to calculate commission
        add_action( 'woocommerce_order_status_completed', array( $this, 'process_order_commission' ), 10, 1 );
        add_action( 'woocommerce_order_status_processing', array( $this, 'process_order_commission' ), 10, 1 );

        // Handle refunds
        add_action( 'woocommerce_order_refunded', array( $this, 'process_refund' ), 10, 2 );
    }

    /**
     * Get commission rate for product.
     *
     * @param int $product_id Product ID.
     * @param int $vendor_id  Vendor ID (optional).
     * @return array Commission type and rate.
     */
    public static function get_commission_rate( $product_id, $vendor_id = null ) {
        // Check product-specific commission first
        $product_type = get_post_meta( $product_id, '_bazaar_commission_type', true );
        $product_rate = get_post_meta( $product_id, '_bazaar_commission_rate', true );

        if ( ! empty( $product_type ) && '' !== $product_rate ) {
            return array(
                'type' => $product_type,
                'rate' => floatval( $product_rate ),
            );
        }

        // Check vendor-specific commission
        if ( ! $vendor_id ) {
            $vendor_id = Bazaar_Product::get_product_vendor( $product_id );
        }

        if ( $vendor_id ) {
            $vendor_type = get_user_meta( $vendor_id, '_bazaar_commission_type', true );
            $vendor_rate = get_user_meta( $vendor_id, '_bazaar_commission_rate', true );

            if ( ! empty( $vendor_type ) && '' !== $vendor_rate ) {
                return array(
                    'type' => $vendor_type,
                    'rate' => floatval( $vendor_rate ),
                );
            }
        }

        // Fall back to global settings
        return array(
            'type' => get_option( 'bazaar_global_commission_type', 'percentage' ),
            'rate' => floatval( get_option( 'bazaar_global_commission_rate', 10 ) ),
        );
    }

    /**
     * Calculate commission for an amount.
     *
     * @param float  $amount     Amount.
     * @param string $type       Commission type (percentage/fixed).
     * @param float  $rate       Commission rate.
     * @return float
     */
    public static function calculate_commission( $amount, $type, $rate ) {
        if ( 'percentage' === $type ) {
            $commission = ( $amount * $rate ) / 100;
        } else {
            $commission = $rate;
        }

        return round( $commission, 2 );
    }

    /**
     * Calculate commission for product.
     *
     * @param int   $product_id Product ID.
     * @param float $amount     Amount.
     * @param int   $vendor_id  Vendor ID (optional).
     * @return array Commission details.
     */
    public static function calculate_product_commission( $product_id, $amount, $vendor_id = null ) {
        $rate_info = self::get_commission_rate( $product_id, $vendor_id );
        $commission = self::calculate_commission( $amount, $rate_info['type'], $rate_info['rate'] );
        $net_amount = $amount - $commission;

        return array(
            'amount'          => $amount,
            'commission'      => $commission,
            'commission_type' => $rate_info['type'],
            'commission_rate' => $rate_info['rate'],
            'net_amount'      => $net_amount,
        );
    }

    /**
     * Process order commission.
     *
     * @param int $order_id Order ID.
     */
    public function process_order_commission( $order_id ) {
        $order = wc_get_order( $order_id );

        if ( ! $order ) {
            return;
        }

        // Check if already processed
        $processed = get_post_meta( $order_id, '_bazaar_commission_processed', true );

        if ( 'yes' === $processed ) {
            return;
        }

        // Group items by vendor
        $vendor_items = array();

        foreach ( $order->get_items() as $item_id => $item ) {
            $product_id = $item->get_product_id();
            $vendor_id = Bazaar_Product::get_product_vendor( $product_id );

            if ( ! $vendor_id ) {
                continue;
            }

            if ( ! isset( $vendor_items[ $vendor_id ] ) ) {
                $vendor_items[ $vendor_id ] = array();
            }

            $vendor_items[ $vendor_id ][] = array(
                'item_id'    => $item_id,
                'product_id' => $product_id,
                'quantity'   => $item->get_quantity(),
                'total'      => $item->get_total(),
            );
        }

        // Process each vendor's items
        foreach ( $vendor_items as $vendor_id => $items ) {
            $this->process_vendor_commission( $order_id, $vendor_id, $items );
        }

        // Mark as processed
        update_post_meta( $order_id, '_bazaar_commission_processed', 'yes' );

        do_action( 'bazaar_order_commission_processed', $order_id, $vendor_items );
    }

    /**
     * Process vendor commission for order.
     *
     * @param int   $order_id  Order ID.
     * @param int   $vendor_id Vendor ID.
     * @param array $items     Order items.
     */
    private function process_vendor_commission( $order_id, $vendor_id, $items ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_vendor_balance';

        foreach ( $items as $item ) {
            $commission_data = self::calculate_product_commission( $item['product_id'], $item['total'], $vendor_id );

            // Check if already exists
            $exists = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT id FROM {$table_name} WHERE order_id = %d AND order_item_id = %d AND vendor_id = %d",
                    $order_id,
                    $item['item_id'],
                    $vendor_id
                )
            );

            if ( $exists ) {
                continue;
            }

            // Insert balance record
            $wpdb->insert(
                $table_name,
                array(
                    'vendor_id'       => $vendor_id,
                    'order_id'        => $order_id,
                    'order_item_id'   => $item['item_id'],
                    'product_id'      => $item['product_id'],
                    'trn_type'        => 'credit',
                    'trn_status'      => 'completed',
                    'amount'          => $commission_data['amount'],
                    'commission'      => $commission_data['commission'],
                    'commission_rate' => $commission_data['commission_rate'],
                    'net_amount'      => $commission_data['net_amount'],
                    'note'            => sprintf( __( 'Order #%d', 'bazaar' ), $order_id ),
                ),
                array( '%d', '%d', '%d', '%d', '%s', '%s', '%f', '%f', '%f', '%f', '%s' )
            );

            do_action( 'bazaar_vendor_earning_added', $vendor_id, $order_id, $item, $commission_data );
        }
    }

    /**
     * Process refund.
     *
     * @param int $order_id  Order ID.
     * @param int $refund_id Refund ID.
     */
    public function process_refund( $order_id, $refund_id ) {
        global $wpdb;

        $refund = wc_get_order( $refund_id );

        if ( ! $refund ) {
            return;
        }

        $table_name = $wpdb->prefix . 'bazaar_vendor_balance';

        foreach ( $refund->get_items() as $item ) {
            $product_id = $item->get_product_id();
            $vendor_id = Bazaar_Product::get_product_vendor( $product_id );

            if ( ! $vendor_id ) {
                continue;
            }

            $refund_total = abs( $item->get_total() );
            $commission_data = self::calculate_product_commission( $product_id, $refund_total, $vendor_id );

            // Insert refund record
            $wpdb->insert(
                $table_name,
                array(
                    'vendor_id'       => $vendor_id,
                    'order_id'        => $order_id,
                    'order_item_id'   => $item->get_id(),
                    'product_id'      => $product_id,
                    'trn_type'        => 'debit',
                    'trn_status'      => 'completed',
                    'amount'          => $refund_total,
                    'commission'      => $commission_data['commission'],
                    'commission_rate' => $commission_data['commission_rate'],
                    'net_amount'      => $commission_data['net_amount'],
                    'note'            => sprintf( __( 'Refund for Order #%d', 'bazaar' ), $order_id ),
                ),
                array( '%d', '%d', '%d', '%d', '%s', '%s', '%f', '%f', '%f', '%f', '%s' )
            );

            do_action( 'bazaar_vendor_refund_processed', $vendor_id, $order_id, $refund_id, $commission_data );
        }
    }

    /**
     * Get vendor earnings for period.
     *
     * @param int    $vendor_id  Vendor ID.
     * @param string $start_date Start date.
     * @param string $end_date   End date.
     * @return array
     */
    public static function get_vendor_earnings( $vendor_id, $start_date = null, $end_date = null ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_vendor_balance';

        $where = $wpdb->prepare( "WHERE vendor_id = %d AND trn_status = 'completed'", $vendor_id );

        if ( $start_date ) {
            $where .= $wpdb->prepare( " AND created_at >= %s", $start_date );
        }

        if ( $end_date ) {
            $where .= $wpdb->prepare( " AND created_at <= %s", $end_date );
        }

        // Total earnings (credits)
        $total_earnings = $wpdb->get_var(
            "SELECT SUM(net_amount) FROM {$table_name} {$where} AND trn_type = 'credit'"
        );

        // Total deductions (debits - refunds, withdrawals)
        $total_deductions = $wpdb->get_var(
            "SELECT SUM(net_amount) FROM {$table_name} {$where} AND trn_type = 'debit'"
        );

        // Total commission paid
        $total_commission = $wpdb->get_var(
            "SELECT SUM(commission) FROM {$table_name} {$where} AND trn_type = 'credit'"
        );

        // Gross sales
        $gross_sales = $wpdb->get_var(
            "SELECT SUM(amount) FROM {$table_name} {$where} AND trn_type = 'credit'"
        );

        return array(
            'gross_sales'      => floatval( $gross_sales ),
            'total_earnings'   => floatval( $total_earnings ),
            'total_commission' => floatval( $total_commission ),
            'total_deductions' => floatval( $total_deductions ),
            'net_earnings'     => floatval( $total_earnings ) - floatval( $total_deductions ),
        );
    }

    /**
     * Get earnings transactions.
     *
     * @param int   $vendor_id Vendor ID.
     * @param array $args      Query arguments.
     * @return array
     */
    public static function get_transactions( $vendor_id, $args = array() ) {
        global $wpdb;

        $defaults = array(
            'per_page' => 20,
            'page'     => 1,
            'type'     => '',
            'status'   => '',
            'order_id' => '',
        );

        $args = wp_parse_args( $args, $defaults );
        $table_name = $wpdb->prefix . 'bazaar_vendor_balance';

        $where = $wpdb->prepare( "WHERE vendor_id = %d", $vendor_id );

        if ( ! empty( $args['type'] ) ) {
            $where .= $wpdb->prepare( " AND trn_type = %s", $args['type'] );
        }

        if ( ! empty( $args['status'] ) ) {
            $where .= $wpdb->prepare( " AND trn_status = %s", $args['status'] );
        }

        if ( ! empty( $args['order_id'] ) ) {
            $where .= $wpdb->prepare( " AND order_id = %d", $args['order_id'] );
        }

        $offset = ( $args['page'] - 1 ) * $args['per_page'];

        $transactions = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} {$where} ORDER BY created_at DESC LIMIT %d OFFSET %d",
                $args['per_page'],
                $offset
            )
        );

        $total = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$table_name} {$where}"
        );

        return array(
            'transactions' => $transactions,
            'total'        => intval( $total ),
            'pages'        => ceil( $total / $args['per_page'] ),
        );
    }

    /**
     * Get admin commission report.
     *
     * @param string $start_date Start date.
     * @param string $end_date   End date.
     * @return array
     */
    public static function get_admin_report( $start_date = null, $end_date = null ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_vendor_balance';

        $where = "WHERE trn_status = 'completed'";

        if ( $start_date ) {
            $where .= $wpdb->prepare( " AND created_at >= %s", $start_date );
        }

        if ( $end_date ) {
            $where .= $wpdb->prepare( " AND created_at <= %s", $end_date );
        }

        // Total admin commission
        $total_commission = $wpdb->get_var(
            "SELECT SUM(commission) FROM {$table_name} {$where} AND trn_type = 'credit'"
        );

        // Total vendor earnings
        $total_vendor_earnings = $wpdb->get_var(
            "SELECT SUM(net_amount) FROM {$table_name} {$where} AND trn_type = 'credit'"
        );

        // Total gross sales
        $gross_sales = $wpdb->get_var(
            "SELECT SUM(amount) FROM {$table_name} {$where} AND trn_type = 'credit'"
        );

        // Total orders
        $total_orders = $wpdb->get_var(
            "SELECT COUNT(DISTINCT order_id) FROM {$table_name} {$where} AND trn_type = 'credit'"
        );

        return array(
            'gross_sales'           => floatval( $gross_sales ),
            'admin_commission'      => floatval( $total_commission ),
            'vendor_earnings'       => floatval( $total_vendor_earnings ),
            'total_orders'          => intval( $total_orders ),
        );
    }

    /**
     * Get top vendors by earnings.
     *
     * @param int    $limit      Number of vendors.
     * @param string $start_date Start date.
     * @param string $end_date   End date.
     * @return array
     */
    public static function get_top_vendors( $limit = 10, $start_date = null, $end_date = null ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_vendor_balance';

        $where = "WHERE trn_status = 'completed' AND trn_type = 'credit'";

        if ( $start_date ) {
            $where .= $wpdb->prepare( " AND created_at >= %s", $start_date );
        }

        if ( $end_date ) {
            $where .= $wpdb->prepare( " AND created_at <= %s", $end_date );
        }

        $vendors = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT vendor_id, SUM(net_amount) as earnings, SUM(amount) as sales, COUNT(DISTINCT order_id) as orders
                FROM {$table_name} {$where}
                GROUP BY vendor_id
                ORDER BY earnings DESC
                LIMIT %d",
                $limit
            )
        );

        return $vendors;
    }
}
