<?php
/**
 * Dashboard Orders Tab.
 *
 * @package Bazaar\Templates
 */

defined( 'ABSPATH' ) || exit;

$statuses = bazaar_get_order_statuses();
$current_status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
?>
<div class="bazaar-tab-content bazaar-orders">
    <h2><?php esc_html_e( 'Orders', 'bazaar' ); ?></h2>

    <div class="bazaar-order-stats">
        <div class="stat-box">
            <span class="stat-value"><?php echo esc_html( $order_stats['total'] ); ?></span>
            <span class="stat-label"><?php esc_html_e( 'Total', 'bazaar' ); ?></span>
        </div>
        <div class="stat-box stat-pending">
            <span class="stat-value"><?php echo esc_html( $order_stats['pending'] ); ?></span>
            <span class="stat-label"><?php esc_html_e( 'Pending', 'bazaar' ); ?></span>
        </div>
        <div class="stat-box stat-processing">
            <span class="stat-value"><?php echo esc_html( $order_stats['processing'] ); ?></span>
            <span class="stat-label"><?php esc_html_e( 'Processing', 'bazaar' ); ?></span>
        </div>
        <div class="stat-box stat-completed">
            <span class="stat-value"><?php echo esc_html( $order_stats['completed'] ); ?></span>
            <span class="stat-label"><?php esc_html_e( 'Completed', 'bazaar' ); ?></span>
        </div>
    </div>

    <div class="bazaar-filters">
        <ul class="status-filter">
            <li>
                <a href="<?php echo esc_url( bazaar_get_dashboard_tab_url( 'orders' ) ); ?>"
                   class="<?php echo empty( $current_status ) ? 'active' : ''; ?>">
                    <?php esc_html_e( 'All', 'bazaar' ); ?>
                </a>
            </li>
            <?php foreach ( $statuses as $status_key => $status_label ) : ?>
                <li>
                    <a href="<?php echo esc_url( add_query_arg( 'status', $status_key, bazaar_get_dashboard_tab_url( 'orders' ) ) ); ?>"
                       class="<?php echo $current_status === $status_key ? 'active' : ''; ?>">
                        <?php echo esc_html( $status_label ); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <?php if ( empty( $orders['orders'] ) ) : ?>
        <div class="bazaar-empty-state">
            <span class="dashicons dashicons-cart"></span>
            <h3><?php esc_html_e( 'No orders found', 'bazaar' ); ?></h3>
            <p><?php esc_html_e( 'Orders will appear here when customers purchase your products.', 'bazaar' ); ?></p>
        </div>
    <?php else : ?>
        <table class="bazaar-table bazaar-orders-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Order', 'bazaar' ); ?></th>
                    <th><?php esc_html_e( 'Customer', 'bazaar' ); ?></th>
                    <th><?php esc_html_e( 'Items', 'bazaar' ); ?></th>
                    <th><?php esc_html_e( 'Total', 'bazaar' ); ?></th>
                    <th><?php esc_html_e( 'Earning', 'bazaar' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'bazaar' ); ?></th>
                    <th><?php esc_html_e( 'Date', 'bazaar' ); ?></th>
                    <th><?php esc_html_e( 'Actions', 'bazaar' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $orders['orders'] as $sub_order ) : ?>
                    <?php
                    $order = wc_get_order( $sub_order->parent_order_id );
                    if ( ! $order ) continue;
                    $items = Bazaar_Orders::get_vendor_order_items( $sub_order->parent_order_id, $vendor_id );
                    ?>
                    <tr>
                        <td>
                            <strong>#<?php echo esc_html( $sub_order->parent_order_id ); ?></strong>
                        </td>
                        <td>
                            <?php echo esc_html( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ); ?>
                            <br>
                            <small><?php echo esc_html( $order->get_billing_email() ); ?></small>
                        </td>
                        <td>
                            <?php echo esc_html( count( $items ) ); ?> <?php esc_html_e( 'item(s)', 'bazaar' ); ?>
                        </td>
                        <td><?php echo wc_price( $sub_order->order_total ); ?></td>
                        <td><strong><?php echo wc_price( $sub_order->vendor_earning ); ?></strong></td>
                        <td>
                            <?php bazaar_order_status_badge( $sub_order->status ); ?>
                        </td>
                        <td>
                            <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $sub_order->created_at ) ) ); ?>
                        </td>
                        <td>
                            <a href="<?php echo esc_url( add_query_arg( array( 'action' => 'view', 'order_id' => $sub_order->parent_order_id ), bazaar_get_dashboard_tab_url( 'orders' ) ) ); ?>" class="button button-small">
                                <?php esc_html_e( 'View', 'bazaar' ); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ( $orders['pages'] > 1 ) : ?>
            <?php bazaar_pagination( $orders['pages'], isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1 ); ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
