<?php
/**
 * Dashboard Overview Tab - Dokan Style.
 *
 * @package Bazaar\Templates
 */

defined( 'ABSPATH' ) || exit;

// Get chart data
$chart_data = Bazaar_Vendor_Dashboard::get_sales_chart_data( $vendor_id, '30days' );
?>
<div class="bazaar-tab-content bazaar-overview">
    <!-- Stats Overview -->
    <div class="bazaar-overview-stats">
        <div class="stat-box stat-earnings">
            <span class="stat-icon dashicons dashicons-money-alt"></span>
            <span class="stat-value"><?php echo wp_kses_post( wc_price( $overview['balance'] ) ); ?></span>
            <span class="stat-label"><?php esc_html_e( 'Current Balance', 'bazaar' ); ?></span>
        </div>
        <div class="stat-box stat-orders">
            <span class="stat-icon dashicons dashicons-chart-line"></span>
            <span class="stat-value"><?php echo wp_kses_post( wc_price( $overview['earnings_this_month'] ) ); ?></span>
            <span class="stat-label"><?php esc_html_e( 'This Month', 'bazaar' ); ?></span>
        </div>
        <div class="stat-box stat-products">
            <span class="stat-icon dashicons dashicons-cart"></span>
            <span class="stat-value"><?php echo esc_html( $overview['orders_total'] ); ?></span>
            <span class="stat-label"><?php esc_html_e( 'Total Orders', 'bazaar' ); ?></span>
        </div>
        <div class="stat-box stat-reviews">
            <span class="stat-icon dashicons dashicons-products"></span>
            <span class="stat-value"><?php echo esc_html( $overview['products_published'] ); ?></span>
            <span class="stat-label"><?php esc_html_e( 'Products', 'bazaar' ); ?></span>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bazaar-quick-actions">
        <a href="<?php echo esc_url( bazaar_get_dashboard_tab_url( 'products', array( 'action' => 'add' ) ) ); ?>" class="quick-action-card">
            <span class="action-icon dashicons dashicons-plus-alt"></span>
            <span class="action-label"><?php esc_html_e( 'Add Product', 'bazaar' ); ?></span>
        </a>
        <a href="<?php echo esc_url( bazaar_get_dashboard_tab_url( 'orders' ) ); ?>" class="quick-action-card">
            <span class="action-icon dashicons dashicons-list-view"></span>
            <span class="action-label"><?php esc_html_e( 'View Orders', 'bazaar' ); ?></span>
        </a>
        <a href="<?php echo esc_url( bazaar_get_dashboard_tab_url( 'coupons', array( 'action' => 'add' ) ) ); ?>" class="quick-action-card">
            <span class="action-icon dashicons dashicons-tickets-alt"></span>
            <span class="action-label"><?php esc_html_e( 'Create Coupon', 'bazaar' ); ?></span>
        </a>
        <a href="<?php echo esc_url( bazaar_get_dashboard_tab_url( 'withdraw' ) ); ?>" class="quick-action-card">
            <span class="action-icon dashicons dashicons-bank"></span>
            <span class="action-label"><?php esc_html_e( 'Request Withdraw', 'bazaar' ); ?></span>
        </a>
    </div>

    <div class="bazaar-dashboard-row">
        <!-- Sales Chart -->
        <div class="bazaar-dashboard-section bazaar-sales-chart">
            <div class="section-header">
                <h3><?php esc_html_e( 'Sales Overview', 'bazaar' ); ?></h3>
                <div class="chart-period">
                    <button type="button" class="period-btn active" data-period="7days"><?php esc_html_e( '7 Days', 'bazaar' ); ?></button>
                    <button type="button" class="period-btn" data-period="30days"><?php esc_html_e( '30 Days', 'bazaar' ); ?></button>
                    <button type="button" class="period-btn" data-period="this_year"><?php esc_html_e( 'This Year', 'bazaar' ); ?></button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="salesChart" height="300"></canvas>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="bazaar-dashboard-section bazaar-order-summary">
            <div class="section-header">
                <h3><?php esc_html_e( 'Order Status', 'bazaar' ); ?></h3>
                <a href="<?php echo esc_url( bazaar_get_dashboard_tab_url( 'orders' ) ); ?>" class="view-all"><?php esc_html_e( 'View All', 'bazaar' ); ?></a>
            </div>
            <div class="order-status-grid">
                <a href="<?php echo esc_url( bazaar_get_dashboard_tab_url( 'orders', array( 'status' => 'pending' ) ) ); ?>" class="status-item status-pending">
                    <span class="status-count"><?php echo esc_html( $overview['orders_pending'] ); ?></span>
                    <span class="status-label"><?php esc_html_e( 'Pending', 'bazaar' ); ?></span>
                </a>
                <a href="<?php echo esc_url( bazaar_get_dashboard_tab_url( 'orders', array( 'status' => 'processing' ) ) ); ?>" class="status-item status-processing">
                    <span class="status-count"><?php echo esc_html( $overview['orders_processing'] ); ?></span>
                    <span class="status-label"><?php esc_html_e( 'Processing', 'bazaar' ); ?></span>
                </a>
                <a href="<?php echo esc_url( bazaar_get_dashboard_tab_url( 'orders', array( 'status' => 'completed' ) ) ); ?>" class="status-item status-completed">
                    <span class="status-count"><?php echo esc_html( $overview['orders_completed'] ); ?></span>
                    <span class="status-label"><?php esc_html_e( 'Completed', 'bazaar' ); ?></span>
                </a>
            </div>
        </div>
    </div>

    <div class="bazaar-dashboard-row">
        <!-- Store Review Summary -->
        <div class="bazaar-dashboard-section bazaar-review-summary">
            <div class="section-header">
                <h3><?php esc_html_e( 'Store Reviews', 'bazaar' ); ?></h3>
                <a href="<?php echo esc_url( bazaar_get_dashboard_tab_url( 'reviews' ) ); ?>" class="view-all"><?php esc_html_e( 'View All', 'bazaar' ); ?></a>
            </div>
            <div class="review-summary-content">
                <div class="rating-display">
                    <span class="rating-value"><?php echo esc_html( number_format( $overview['rating']['average'], 1 ) ); ?></span>
                    <div class="rating-stars">
                        <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                            <span class="star <?php echo $i <= round( $overview['rating']['average'] ) ? 'filled' : ''; ?>">&#9733;</span>
                        <?php endfor; ?>
                    </div>
                    <span class="rating-count">
                        <?php
                        /* translators: %d: review count */
                        printf( esc_html( _n( '%d review', '%d reviews', $overview['rating']['count'], 'bazaar' ) ), esc_html( $overview['rating']['count'] ) );
                        ?>
                    </span>
                </div>
                <?php if ( $overview['rating']['count'] > 0 ) : ?>
                    <div class="rating-breakdown">
                        <?php for ( $star = 5; $star >= 1; $star-- ) :
                            $count = isset( $overview['rating']['breakdown'][ $star ] ) ? $overview['rating']['breakdown'][ $star ] : 0;
                            $percent = $overview['rating']['count'] > 0 ? ( $count / $overview['rating']['count'] ) * 100 : 0;
                        ?>
                            <div class="rating-bar">
                                <span class="stars"><?php echo esc_html( $star ); ?> star</span>
                                <div class="bar">
                                    <div class="fill" style="width: <?php echo esc_attr( $percent ); ?>%"></div>
                                </div>
                                <span class="count"><?php echo esc_html( $count ); ?></span>
                            </div>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Status -->
        <div class="bazaar-dashboard-section bazaar-product-status">
            <div class="section-header">
                <h3><?php esc_html_e( 'Products', 'bazaar' ); ?></h3>
                <a href="<?php echo esc_url( bazaar_get_dashboard_tab_url( 'products' ) ); ?>" class="view-all"><?php esc_html_e( 'Manage', 'bazaar' ); ?></a>
            </div>
            <div class="product-status-grid">
                <div class="product-stat published">
                    <span class="dashicons dashicons-yes-alt"></span>
                    <div class="stat-info">
                        <span class="stat-count"><?php echo esc_html( $overview['products_published'] ); ?></span>
                        <span class="stat-label"><?php esc_html_e( 'Published', 'bazaar' ); ?></span>
                    </div>
                </div>
                <div class="product-stat pending">
                    <span class="dashicons dashicons-clock"></span>
                    <div class="stat-info">
                        <span class="stat-count"><?php echo esc_html( $overview['products_pending'] ); ?></span>
                        <span class="stat-label"><?php esc_html_e( 'Pending', 'bazaar' ); ?></span>
                    </div>
                </div>
                <div class="product-stat draft">
                    <span class="dashicons dashicons-edit"></span>
                    <div class="stat-info">
                        <span class="stat-count"><?php echo esc_html( $overview['products_draft'] ); ?></span>
                        <span class="stat-label"><?php esc_html_e( 'Draft', 'bazaar' ); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ( ! empty( $recent_orders ) ) : ?>
        <!-- Recent Orders -->
        <div class="bazaar-dashboard-section bazaar-recent-orders full-width">
            <div class="section-header">
                <h3><?php esc_html_e( 'Recent Orders', 'bazaar' ); ?></h3>
                <a href="<?php echo esc_url( bazaar_get_dashboard_tab_url( 'orders' ) ); ?>" class="view-all"><?php esc_html_e( 'View All', 'bazaar' ); ?></a>
            </div>
            <table class="bazaar-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Order', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Customer', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Total', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Your Earning', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Date', 'bazaar' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $recent_orders as $order ) : ?>
                        <tr>
                            <td>
                                <a href="<?php echo esc_url( bazaar_get_dashboard_tab_url( 'orders', array( 'view' => $order['id'] ) ) ); ?>">
                                    <strong>#<?php echo esc_html( $order['order_id'] ); ?></strong>
                                </a>
                            </td>
                            <td><?php echo esc_html( $order['customer'] ); ?></td>
                            <td><?php echo wp_kses_post( wc_price( $order['total'] ) ); ?></td>
                            <td class="earning"><?php echo wp_kses_post( wc_price( $order['earning'] ) ); ?></td>
                            <td><?php bazaar_order_status_badge( $order['status'] ); ?></td>
                            <td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $order['date'] ) ) ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if ( ! empty( $notifications ) ) : ?>
        <!-- Recent Notifications -->
        <div class="bazaar-dashboard-section bazaar-notifications-preview full-width">
            <div class="section-header">
                <h3><?php esc_html_e( 'Recent Notifications', 'bazaar' ); ?></h3>
            </div>
            <ul class="notification-list">
                <?php foreach ( $notifications as $notification ) : ?>
                    <li class="<?php echo $notification->is_read ? '' : 'unread'; ?>">
                        <span class="notification-icon dashicons <?php echo esc_attr( bazaar_get_notification_icon( $notification->type ) ); ?>"></span>
                        <div class="notification-content">
                            <span class="notification-title"><?php echo esc_html( $notification->title ); ?></span>
                            <span class="notification-date">
                                <?php echo esc_html( human_time_diff( strtotime( $notification->created_at ), current_time( 'timestamp' ) ) ); ?>
                                <?php esc_html_e( 'ago', 'bazaar' ); ?>
                            </span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>

<script>
jQuery(document).ready(function($) {
    // Initialize Sales Chart
    var ctx = document.getElementById('salesChart');
    if (ctx) {
        var salesChart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: <?php echo wp_json_encode( $chart_data['labels'] ); ?>,
                datasets: [{
                    label: '<?php esc_attr_e( 'Sales', 'bazaar' ); ?>',
                    data: <?php echo wp_json_encode( $chart_data['data'] ); ?>,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { size: 14 },
                        bodyFont: { size: 13 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return '<?php echo esc_js( get_woocommerce_currency_symbol() ); ?>' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b' }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            color: '#64748b',
                            callback: function(value) {
                                return '<?php echo esc_js( get_woocommerce_currency_symbol() ); ?>' + value;
                            }
                        }
                    }
                }
            }
        });

        // Period buttons
        $('.period-btn').on('click', function() {
            var $btn = $(this);
            var period = $btn.data('period');

            $('.period-btn').removeClass('active');
            $btn.addClass('active');

            // AJAX call to get new data
            $.ajax({
                url: bazaar_dashboard.ajax_url,
                type: 'POST',
                data: {
                    action: 'bazaar_get_sales_chart',
                    period: period,
                    nonce: bazaar_dashboard.nonce
                },
                success: function(response) {
                    if (response.success) {
                        salesChart.data.labels = response.data.labels;
                        salesChart.data.datasets[0].data = response.data.data;
                        salesChart.update();
                    }
                }
            });
        });
    }
});
</script>
