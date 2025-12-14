<?php
/**
 * Dashboard Overview Tab.
 *
 * @package Bazaar\Templates
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="bazaar-tab-content bazaar-overview">
    <h2><?php esc_html_e( 'Dashboard Overview', 'bazaar' ); ?></h2>

    <div class="bazaar-stats-grid">
        <?php
        bazaar_stats_card(
            __( 'Balance', 'bazaar' ),
            wc_price( $overview['balance'] ),
            'dashicons-money-alt',
            bazaar_get_dashboard_tab_url( 'withdraw' )
        );

        bazaar_stats_card(
            __( 'Earnings This Month', 'bazaar' ),
            wc_price( $overview['earnings_this_month'] ),
            'dashicons-chart-area',
            bazaar_get_dashboard_tab_url( 'earnings' )
        );

        bazaar_stats_card(
            __( 'Total Orders', 'bazaar' ),
            $overview['orders_total'],
            'dashicons-cart',
            bazaar_get_dashboard_tab_url( 'orders' )
        );

        bazaar_stats_card(
            __( 'Published Products', 'bazaar' ),
            $overview['products_published'],
            'dashicons-products',
            bazaar_get_dashboard_tab_url( 'products' )
        );
        ?>
    </div>

    <div class="bazaar-overview-sections">
        <div class="bazaar-section bazaar-order-summary">
            <h3><?php esc_html_e( 'Order Summary', 'bazaar' ); ?></h3>
            <div class="order-status-grid">
                <div class="status-item status-pending">
                    <span class="status-count"><?php echo esc_html( $overview['orders_pending'] ); ?></span>
                    <span class="status-label"><?php esc_html_e( 'Pending', 'bazaar' ); ?></span>
                </div>
                <div class="status-item status-processing">
                    <span class="status-count"><?php echo esc_html( $overview['orders_processing'] ); ?></span>
                    <span class="status-label"><?php esc_html_e( 'Processing', 'bazaar' ); ?></span>
                </div>
                <div class="status-item status-completed">
                    <span class="status-count"><?php echo esc_html( $overview['orders_completed'] ); ?></span>
                    <span class="status-label"><?php esc_html_e( 'Completed', 'bazaar' ); ?></span>
                </div>
            </div>
        </div>

        <div class="bazaar-section bazaar-rating-summary">
            <h3><?php esc_html_e( 'Store Rating', 'bazaar' ); ?></h3>
            <div class="rating-display">
                <span class="rating-value"><?php echo esc_html( $overview['rating']['average'] ); ?></span>
                <span class="rating-stars">
                    <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                        <span class="star <?php echo $i <= $overview['rating']['average'] ? 'filled' : ''; ?>">&#9733;</span>
                    <?php endfor; ?>
                </span>
                <span class="rating-count">
                    <?php
                    /* translators: %d: review count */
                    printf( esc_html( _n( '%d review', '%d reviews', $overview['rating']['count'], 'bazaar' ) ), $overview['rating']['count'] );
                    ?>
                </span>
            </div>
        </div>
    </div>

    <?php if ( ! empty( $recent_orders ) ) : ?>
        <div class="bazaar-section bazaar-recent-orders">
            <h3>
                <?php esc_html_e( 'Recent Orders', 'bazaar' ); ?>
                <a href="<?php echo esc_url( bazaar_get_dashboard_tab_url( 'orders' ) ); ?>" class="view-all">
                    <?php esc_html_e( 'View All', 'bazaar' ); ?>
                </a>
            </h3>
            <table class="bazaar-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Order', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Customer', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Total', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Earning', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Date', 'bazaar' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $recent_orders as $order ) : ?>
                        <tr>
                            <td>#<?php echo esc_html( $order['order_id'] ); ?></td>
                            <td><?php echo esc_html( $order['customer'] ); ?></td>
                            <td><?php echo wc_price( $order['total'] ); ?></td>
                            <td><?php echo wc_price( $order['earning'] ); ?></td>
                            <td><?php bazaar_order_status_badge( $order['status'] ); ?></td>
                            <td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $order['date'] ) ) ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if ( ! empty( $notifications ) ) : ?>
        <div class="bazaar-section bazaar-notifications-preview">
            <h3><?php esc_html_e( 'Recent Notifications', 'bazaar' ); ?></h3>
            <ul class="notification-list">
                <?php foreach ( $notifications as $notification ) : ?>
                    <li class="<?php echo $notification->is_read ? '' : 'unread'; ?>">
                        <span class="notification-title"><?php echo esc_html( $notification->title ); ?></span>
                        <span class="notification-date">
                            <?php echo esc_html( human_time_diff( strtotime( $notification->created_at ), current_time( 'timestamp' ) ) ); ?>
                            <?php esc_html_e( 'ago', 'bazaar' ); ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
