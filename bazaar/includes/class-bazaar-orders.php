<?php
/**
 * Orders Management Class.
 *
 * @package Bazaar\Orders
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Orders Class.
 */
class Bazaar_Orders {

    /**
     * Constructor.
     */
    public function __construct() {
        // Create sub-orders on checkout
        add_action( 'woocommerce_checkout_order_processed', array( $this, 'create_sub_orders' ), 10, 3 );

        // Sync sub-order status with parent order
        add_action( 'woocommerce_order_status_changed', array( $this, 'sync_order_status' ), 10, 4 );

        // Add vendor info to order items
        add_action( 'woocommerce_order_item_meta_end', array( $this, 'display_vendor_in_order' ), 10, 4 );

        // Filter orders in admin by vendor
        add_action( 'pre_get_posts', array( $this, 'filter_orders_by_vendor' ) );

        // Add vendor column to orders
        add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_vendor_column' ) );
        add_action( 'manage_shop_order_posts_custom_column', array( $this, 'render_vendor_column' ), 10, 2 );
    }

    /**
     * Create sub-orders for each vendor.
     *
     * @param int      $order_id Order ID.
     * @param array    $posted   Posted data.
     * @param WC_Order $order    Order object.
     */
    public function create_sub_orders( $order_id, $posted, $order ) {
        if ( 'yes' !== get_option( 'bazaar_order_split_enabled', 'yes' ) ) {
            return;
        }

        // Group items by vendor
        $vendor_items = $this->group_items_by_vendor( $order );

        if ( count( $vendor_items ) <= 1 ) {
            // Single vendor or no vendor items, no need to split
            if ( ! empty( $vendor_items ) ) {
                $vendor_id = key( $vendor_items );
                $this->create_sub_order( $order_id, $vendor_id, $vendor_items[ $vendor_id ], $order );
            }
            return;
        }

        // Create sub-order for each vendor
        foreach ( $vendor_items as $vendor_id => $items ) {
            $this->create_sub_order( $order_id, $vendor_id, $items, $order );
        }

        // Mark parent order as having sub-orders
        update_post_meta( $order_id, '_bazaar_has_sub_orders', 'yes' );

        do_action( 'bazaar_sub_orders_created', $order_id, $vendor_items );
    }

    /**
     * Group order items by vendor.
     *
     * @param WC_Order $order Order object.
     * @return array
     */
    private function group_items_by_vendor( $order ) {
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
                'item'       => $item,
                'product_id' => $product_id,
                'quantity'   => $item->get_quantity(),
                'total'      => $item->get_total(),
                'tax'        => $item->get_total_tax(),
            );
        }

        return $vendor_items;
    }

    /**
     * Create sub-order.
     *
     * @param int      $parent_order_id Parent order ID.
     * @param int      $vendor_id       Vendor ID.
     * @param array    $items           Order items.
     * @param WC_Order $parent_order    Parent order object.
     */
    private function create_sub_order( $parent_order_id, $vendor_id, $items, $parent_order ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_sub_orders';

        // Calculate totals
        $order_total = 0;
        $total_commission = 0;

        foreach ( $items as $item_data ) {
            $order_total += $item_data['total'] + $item_data['tax'];
            $commission_data = Bazaar_Commission::calculate_product_commission( $item_data['product_id'], $item_data['total'], $vendor_id );
            $total_commission += $commission_data['commission'];
        }

        $vendor_earning = $order_total - $total_commission;

        // Check if sub-order already exists
        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM {$table_name} WHERE parent_order_id = %d AND vendor_id = %d",
                $parent_order_id,
                $vendor_id
            )
        );

        if ( $exists ) {
            return;
        }

        // Create sub-order record
        $wpdb->insert(
            $table_name,
            array(
                'parent_order_id' => $parent_order_id,
                'vendor_id'       => $vendor_id,
                'order_total'     => $order_total,
                'commission'      => $total_commission,
                'vendor_earning'  => $vendor_earning,
                'status'          => $parent_order->get_status(),
            ),
            array( '%d', '%d', '%f', '%f', '%f', '%s' )
        );

        $sub_order_id = $wpdb->insert_id;

        // Save item associations
        foreach ( $items as $item_data ) {
            update_post_meta( $parent_order_id, '_bazaar_item_' . $item_data['item_id'] . '_vendor', $vendor_id );
        }

        do_action( 'bazaar_sub_order_created', $sub_order_id, $parent_order_id, $vendor_id, $items );

        return $sub_order_id;
    }

    /**
     * Sync sub-order status with parent order.
     *
     * @param int      $order_id   Order ID.
     * @param string   $old_status Old status.
     * @param string   $new_status New status.
     * @param WC_Order $order      Order object.
     */
    public function sync_order_status( $order_id, $old_status, $new_status, $order ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_sub_orders';

        // Update all sub-orders
        $wpdb->update(
            $table_name,
            array( 'status' => $new_status ),
            array( 'parent_order_id' => $order_id ),
            array( '%s' ),
            array( '%d' )
        );
    }

    /**
     * Get sub-orders for parent order.
     *
     * @param int $parent_order_id Parent order ID.
     * @return array
     */
    public static function get_sub_orders( $parent_order_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_sub_orders';

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE parent_order_id = %d",
                $parent_order_id
            )
        );
    }

    /**
     * Get vendor orders.
     *
     * @param int   $vendor_id Vendor ID.
     * @param array $args      Query arguments.
     * @return array
     */
    public static function get_vendor_orders( $vendor_id, $args = array() ) {
        global $wpdb;

        $defaults = array(
            'per_page' => 20,
            'page'     => 1,
            'status'   => '',
            'order_by' => 'created_at',
            'order'    => 'DESC',
        );

        $args = wp_parse_args( $args, $defaults );
        $table_name = $wpdb->prefix . 'bazaar_sub_orders';

        $where = $wpdb->prepare( "WHERE vendor_id = %d", $vendor_id );

        if ( ! empty( $args['status'] ) ) {
            if ( is_array( $args['status'] ) ) {
                $statuses = implode( "','", array_map( 'esc_sql', $args['status'] ) );
                $where .= " AND status IN ('{$statuses}')";
            } else {
                $where .= $wpdb->prepare( " AND status = %s", $args['status'] );
            }
        }

        $offset = ( $args['page'] - 1 ) * $args['per_page'];

        $orders = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} {$where} ORDER BY {$args['order_by']} {$args['order']} LIMIT %d OFFSET %d",
                $args['per_page'],
                $offset
            )
        );

        $total = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$table_name} {$where}"
        );

        return array(
            'orders' => $orders,
            'total'  => intval( $total ),
            'pages'  => ceil( $total / $args['per_page'] ),
        );
    }

    /**
     * Get vendor sub-order.
     *
     * @param int $parent_order_id Parent order ID.
     * @param int $vendor_id       Vendor ID.
     * @return object|null
     */
    public static function get_vendor_sub_order( $parent_order_id, $vendor_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_sub_orders';

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE parent_order_id = %d AND vendor_id = %d",
                $parent_order_id,
                $vendor_id
            )
        );
    }

    /**
     * Get vendor order items from parent order.
     *
     * @param int $order_id  Order ID.
     * @param int $vendor_id Vendor ID.
     * @return array
     */
    public static function get_vendor_order_items( $order_id, $vendor_id ) {
        $order = wc_get_order( $order_id );

        if ( ! $order ) {
            return array();
        }

        $vendor_items = array();

        foreach ( $order->get_items() as $item_id => $item ) {
            $product_id = $item->get_product_id();
            $item_vendor = Bazaar_Product::get_product_vendor( $product_id );

            if ( $item_vendor === $vendor_id ) {
                $vendor_items[ $item_id ] = $item;
            }
        }

        return $vendor_items;
    }

    /**
     * Update sub-order status.
     *
     * @param int    $sub_order_id Sub-order ID.
     * @param string $status       New status.
     * @return bool
     */
    public static function update_sub_order_status( $sub_order_id, $status ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_sub_orders';

        $result = $wpdb->update(
            $table_name,
            array( 'status' => $status ),
            array( 'id' => $sub_order_id ),
            array( '%s' ),
            array( '%d' )
        );

        if ( false !== $result ) {
            do_action( 'bazaar_sub_order_status_updated', $sub_order_id, $status );
            return true;
        }

        return false;
    }

    /**
     * Display vendor info in order items.
     *
     * @param int      $item_id Item ID.
     * @param array    $item    Item data.
     * @param WC_Order $order   Order object.
     * @param bool     $plain   Plain text.
     */
    public function display_vendor_in_order( $item_id, $item, $order, $plain = false ) {
        if ( ! is_a( $item, 'WC_Order_Item_Product' ) ) {
            return;
        }

        $product_id = $item->get_product_id();
        $vendor_id = Bazaar_Product::get_product_vendor( $product_id );

        if ( ! $vendor_id ) {
            return;
        }

        $vendor = Bazaar_Vendor::get_vendor( $vendor_id );

        if ( ! $vendor ) {
            return;
        }

        if ( $plain ) {
            echo "\n" . esc_html__( 'Sold by:', 'bazaar' ) . ' ' . esc_html( $vendor['store_name'] );
        } else {
            echo '<p class="bazaar-order-vendor"><strong>' . esc_html__( 'Sold by:', 'bazaar' ) . '</strong> ';
            echo '<a href="' . esc_url( $vendor['store_url'] ) . '">' . esc_html( $vendor['store_name'] ) . '</a></p>';
        }
    }

    /**
     * Filter orders by vendor in admin.
     *
     * @param WP_Query $query Query object.
     */
    public function filter_orders_by_vendor( $query ) {
        global $wpdb;

        if ( ! is_admin() || ! $query->is_main_query() ) {
            return;
        }

        if ( 'shop_order' !== $query->get( 'post_type' ) ) {
            return;
        }

        // Vendor filter
        if ( isset( $_GET['bazaar_vendor'] ) && ! empty( $_GET['bazaar_vendor'] ) ) {
            $vendor_id = intval( $_GET['bazaar_vendor'] );
            $table_name = $wpdb->prefix . 'bazaar_sub_orders';

            $order_ids = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT DISTINCT parent_order_id FROM {$table_name} WHERE vendor_id = %d",
                    $vendor_id
                )
            );

            if ( ! empty( $order_ids ) ) {
                $query->set( 'post__in', $order_ids );
            } else {
                $query->set( 'post__in', array( 0 ) ); // No orders
            }
        }

        // If vendor viewing orders
        if ( ! current_user_can( 'manage_options' ) && Bazaar_Roles::is_vendor( get_current_user_id() ) ) {
            $vendor_id = get_current_user_id();
            $table_name = $wpdb->prefix . 'bazaar_sub_orders';

            $order_ids = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT DISTINCT parent_order_id FROM {$table_name} WHERE vendor_id = %d",
                    $vendor_id
                )
            );

            if ( ! empty( $order_ids ) ) {
                $query->set( 'post__in', $order_ids );
            } else {
                $query->set( 'post__in', array( 0 ) );
            }
        }
    }

    /**
     * Add vendor column to orders list.
     *
     * @param array $columns Columns.
     * @return array
     */
    public function add_vendor_column( $columns ) {
        $new_columns = array();

        foreach ( $columns as $key => $value ) {
            $new_columns[ $key ] = $value;

            if ( 'order_status' === $key ) {
                $new_columns['bazaar_vendor'] = __( 'Vendor(s)', 'bazaar' );
            }
        }

        return $new_columns;
    }

    /**
     * Render vendor column content.
     *
     * @param string $column  Column name.
     * @param int    $post_id Post ID.
     */
    public function render_vendor_column( $column, $post_id ) {
        if ( 'bazaar_vendor' !== $column ) {
            return;
        }

        $sub_orders = self::get_sub_orders( $post_id );

        if ( empty( $sub_orders ) ) {
            echo '&mdash;';
            return;
        }

        $vendor_names = array();

        foreach ( $sub_orders as $sub_order ) {
            $vendor = Bazaar_Vendor::get_vendor( $sub_order->vendor_id );
            if ( $vendor ) {
                $vendor_names[] = '<a href="' . esc_url( admin_url( 'admin.php?page=bazaar-vendors&vendor=' . $sub_order->vendor_id ) ) . '">' . esc_html( $vendor['store_name'] ) . '</a>';
            }
        }

        echo implode( ', ', $vendor_names );
    }

    /**
     * Get order statistics for vendor.
     *
     * @param int $vendor_id Vendor ID.
     * @return array
     */
    public static function get_vendor_order_stats( $vendor_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_sub_orders';

        $stats = array(
            'total'      => 0,
            'pending'    => 0,
            'processing' => 0,
            'completed'  => 0,
            'cancelled'  => 0,
            'refunded'   => 0,
        );

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT status, COUNT(*) as count FROM {$table_name} WHERE vendor_id = %d GROUP BY status",
                $vendor_id
            )
        );

        foreach ( $results as $row ) {
            $status = str_replace( 'wc-', '', $row->status );
            if ( isset( $stats[ $status ] ) ) {
                $stats[ $status ] = intval( $row->count );
            }
            $stats['total'] += intval( $row->count );
        }

        return $stats;
    }
}
