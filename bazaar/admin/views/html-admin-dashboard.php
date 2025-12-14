<?php
/**
 * Admin Dashboard View.
 *
 * @package Bazaar\Admin\Views
 */

defined( 'ABSPATH' ) || exit;

// Get dashboard statistics
$stats = Bazaar_Admin_Dashboard::get_stats();
$chart_data = Bazaar_Admin_Dashboard::get_chart_data();
$top_vendors = Bazaar_Admin_Dashboard::get_top_vendors( 5 );
$recent_orders = Bazaar_Admin_Dashboard::get_recent_orders( 5 );
$pending_tasks = Bazaar_Admin_Dashboard::get_pending_tasks();
?>
<div class="wrap bazaar-admin-wrap bazaar-dashboard">
    <!-- Header -->
    <div class="bazaar-dashboard-header">
        <div class="header-content">
            <h1><?php esc_html_e( 'Bazaar Marketplace Dashboard', 'bazaar' ); ?></h1>
            <p class="header-subtitle"><?php esc_html_e( 'Welcome back! Here\'s what\'s happening with your marketplace.', 'bazaar' ); ?></p>
        </div>
        <div class="header-actions">
            <select id="bazaar-date-range" class="bazaar-select">
                <option value="today"><?php esc_html_e( 'Today', 'bazaar' ); ?></option>
                <option value="week"><?php esc_html_e( 'This Week', 'bazaar' ); ?></option>
                <option value="month" selected><?php esc_html_e( 'This Month', 'bazaar' ); ?></option>
                <option value="year"><?php esc_html_e( 'This Year', 'bazaar' ); ?></option>
                <option value="custom"><?php esc_html_e( 'Custom Range', 'bazaar' ); ?></option>
            </select>
        </div>
    </div>

    <!-- At a Glance Stats -->
    <div class="bazaar-stats-grid">
        <div class="bazaar-stat-card">
            <div class="stat-icon vendors">
                <span class="dashicons dashicons-groups"></span>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?php echo esc_html( number_format_i18n( $stats['total_vendors'] ) ); ?></span>
                <span class="stat-label"><?php esc_html_e( 'Total Vendors', 'bazaar' ); ?></span>
                <?php if ( $stats['pending_vendors'] > 0 ) : ?>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors&status=pending' ) ); ?>" class="stat-badge warning">
                        <?php printf( esc_html__( '%d pending', 'bazaar' ), $stats['pending_vendors'] ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="bazaar-stat-card highlight">
            <div class="stat-icon sales">
                <span class="dashicons dashicons-chart-bar"></span>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?php echo wc_price( $stats['gross_sales'] ); ?></span>
                <span class="stat-label"><?php esc_html_e( 'Gross Sales', 'bazaar' ); ?></span>
                <?php if ( isset( $stats['sales_growth'] ) && $stats['sales_growth'] != 0 ) : ?>
                    <span class="stat-trend <?php echo $stats['sales_growth'] > 0 ? 'up' : 'down'; ?>">
                        <span class="dashicons dashicons-arrow-<?php echo $stats['sales_growth'] > 0 ? 'up' : 'down'; ?>-alt"></span>
                        <?php echo esc_html( abs( $stats['sales_growth'] ) ); ?>%
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="bazaar-stat-card">
            <div class="stat-icon orders">
                <span class="dashicons dashicons-cart"></span>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?php echo esc_html( number_format_i18n( $stats['total_orders'] ) ); ?></span>
                <span class="stat-label"><?php esc_html_e( 'Orders', 'bazaar' ); ?></span>
            </div>
        </div>

        <div class="bazaar-stat-card">
            <div class="stat-icon commission">
                <span class="dashicons dashicons-money-alt"></span>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?php echo wc_price( $stats['admin_commission'] ); ?></span>
                <span class="stat-label"><?php esc_html_e( 'Admin Commission', 'bazaar' ); ?></span>
            </div>
        </div>

        <div class="bazaar-stat-card">
            <div class="stat-icon products">
                <span class="dashicons dashicons-archive"></span>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?php echo esc_html( number_format_i18n( $stats['total_products'] ) ); ?></span>
                <span class="stat-label"><?php esc_html_e( 'Products', 'bazaar' ); ?></span>
                <?php if ( $stats['pending_products'] > 0 ) : ?>
                    <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=product&post_status=pending' ) ); ?>" class="stat-badge warning">
                        <?php printf( esc_html__( '%d pending', 'bazaar' ), $stats['pending_products'] ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="bazaar-stat-card">
            <div class="stat-icon withdrawals">
                <span class="dashicons dashicons-upload"></span>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?php echo wc_price( $stats['pending_withdrawal_amount'] ); ?></span>
                <span class="stat-label"><?php esc_html_e( 'Pending Withdrawals', 'bazaar' ); ?></span>
                <?php if ( $stats['pending_withdrawals'] > 0 ) : ?>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-withdraw&status=pending' ) ); ?>" class="stat-badge warning">
                        <?php printf( esc_html__( '%d requests', 'bazaar' ), $stats['pending_withdrawals'] ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="bazaar-dashboard-grid">
        <!-- Sales Chart -->
        <div class="bazaar-dashboard-card chart-card">
            <div class="card-header">
                <h2><?php esc_html_e( 'Sales Overview', 'bazaar' ); ?></h2>
                <div class="chart-legend">
                    <span class="legend-item sales"><span class="legend-dot"></span> <?php esc_html_e( 'Sales', 'bazaar' ); ?></span>
                    <span class="legend-item commission"><span class="legend-dot"></span> <?php esc_html_e( 'Commission', 'bazaar' ); ?></span>
                </div>
            </div>
            <div class="card-content">
                <canvas id="bazaar-sales-chart" height="300"></canvas>
            </div>
        </div>

        <!-- Pending Tasks -->
        <div class="bazaar-dashboard-card tasks-card">
            <div class="card-header">
                <h2><?php esc_html_e( 'Pending Tasks', 'bazaar' ); ?></h2>
            </div>
            <div class="card-content">
                <ul class="pending-tasks-list">
                    <?php if ( $pending_tasks['vendors'] > 0 ) : ?>
                        <li class="task-item">
                            <span class="task-icon vendors"><span class="dashicons dashicons-admin-users"></span></span>
                            <span class="task-text">
                                <?php printf( esc_html__( '%d vendors awaiting approval', 'bazaar' ), $pending_tasks['vendors'] ); ?>
                            </span>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors&status=pending' ) ); ?>" class="task-action">
                                <?php esc_html_e( 'Review', 'bazaar' ); ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ( $pending_tasks['withdrawals'] > 0 ) : ?>
                        <li class="task-item">
                            <span class="task-icon withdrawals"><span class="dashicons dashicons-money-alt"></span></span>
                            <span class="task-text">
                                <?php printf( esc_html__( '%d withdrawal requests pending', 'bazaar' ), $pending_tasks['withdrawals'] ); ?>
                            </span>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-withdraw&status=pending' ) ); ?>" class="task-action">
                                <?php esc_html_e( 'Process', 'bazaar' ); ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ( $pending_tasks['products'] > 0 ) : ?>
                        <li class="task-item">
                            <span class="task-icon products"><span class="dashicons dashicons-archive"></span></span>
                            <span class="task-text">
                                <?php printf( esc_html__( '%d products pending review', 'bazaar' ), $pending_tasks['products'] ); ?>
                            </span>
                            <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=product&post_status=pending' ) ); ?>" class="task-action">
                                <?php esc_html_e( 'Review', 'bazaar' ); ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ( $pending_tasks['refunds'] > 0 ) : ?>
                        <li class="task-item">
                            <span class="task-icon refunds"><span class="dashicons dashicons-undo"></span></span>
                            <span class="task-text">
                                <?php printf( esc_html__( '%d refund requests pending', 'bazaar' ), $pending_tasks['refunds'] ); ?>
                            </span>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-refunds&status=pending' ) ); ?>" class="task-action">
                                <?php esc_html_e( 'Review', 'bazaar' ); ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ( empty( $pending_tasks['vendors'] ) && empty( $pending_tasks['withdrawals'] ) && empty( $pending_tasks['products'] ) && empty( $pending_tasks['refunds'] ) ) : ?>
                        <li class="task-item empty">
                            <span class="task-icon success"><span class="dashicons dashicons-yes-alt"></span></span>
                            <span class="task-text"><?php esc_html_e( 'All caught up! No pending tasks.', 'bazaar' ); ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Top Vendors -->
        <div class="bazaar-dashboard-card vendors-card">
            <div class="card-header">
                <h2><?php esc_html_e( 'Top Vendors', 'bazaar' ); ?></h2>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors' ) ); ?>" class="view-all">
                    <?php esc_html_e( 'View All', 'bazaar' ); ?>
                </a>
            </div>
            <div class="card-content">
                <?php if ( ! empty( $top_vendors ) ) : ?>
                    <table class="top-vendors-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Vendor', 'bazaar' ); ?></th>
                                <th><?php esc_html_e( 'Earnings', 'bazaar' ); ?></th>
                                <th><?php esc_html_e( 'Orders', 'bazaar' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $top_vendors as $vendor ) : ?>
                                <?php $user = get_userdata( $vendor->vendor_id ); ?>
                                <?php if ( $user ) : ?>
                                    <tr>
                                        <td class="vendor-cell">
                                            <?php echo get_avatar( $vendor->vendor_id, 36 ); ?>
                                            <div class="vendor-info">
                                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors&action=edit&vendor=' . $vendor->vendor_id ) ); ?>">
                                                    <?php echo esc_html( Bazaar_Vendor::get_store_name( $vendor->vendor_id ) ); ?>
                                                </a>
                                                <span class="vendor-email"><?php echo esc_html( $user->user_email ); ?></span>
                                            </div>
                                        </td>
                                        <td class="earnings-cell"><?php echo wc_price( $vendor->earnings ); ?></td>
                                        <td class="orders-cell"><?php echo esc_html( number_format_i18n( $vendor->orders ) ); ?></td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <div class="empty-state">
                        <span class="dashicons dashicons-groups"></span>
                        <p><?php esc_html_e( 'No vendor data available yet.', 'bazaar' ); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bazaar-dashboard-card orders-card">
            <div class="card-header">
                <h2><?php esc_html_e( 'Recent Orders', 'bazaar' ); ?></h2>
                <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=shop_order' ) ); ?>" class="view-all">
                    <?php esc_html_e( 'View All', 'bazaar' ); ?>
                </a>
            </div>
            <div class="card-content">
                <?php if ( ! empty( $recent_orders ) ) : ?>
                    <table class="recent-orders-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Order', 'bazaar' ); ?></th>
                                <th><?php esc_html_e( 'Customer', 'bazaar' ); ?></th>
                                <th><?php esc_html_e( 'Status', 'bazaar' ); ?></th>
                                <th><?php esc_html_e( 'Total', 'bazaar' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $recent_orders as $order ) : ?>
                                <tr>
                                    <td class="order-cell">
                                        <a href="<?php echo esc_url( $order->get_edit_order_url() ); ?>">
                                            #<?php echo esc_html( $order->get_order_number() ); ?>
                                        </a>
                                        <span class="order-date"><?php echo esc_html( $order->get_date_created()->date_i18n( get_option( 'date_format' ) ) ); ?></span>
                                    </td>
                                    <td class="customer-cell">
                                        <?php echo esc_html( $order->get_formatted_billing_full_name() ); ?>
                                    </td>
                                    <td class="status-cell">
                                        <span class="order-status status-<?php echo esc_attr( $order->get_status() ); ?>">
                                            <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
                                        </span>
                                    </td>
                                    <td class="total-cell">
                                        <?php echo wp_kses_post( $order->get_formatted_order_total() ); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <div class="empty-state">
                        <span class="dashicons dashicons-cart"></span>
                        <p><?php esc_html_e( 'No orders yet.', 'bazaar' ); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bazaar-quick-actions">
        <h2><?php esc_html_e( 'Quick Actions', 'bazaar' ); ?></h2>
        <div class="actions-grid">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors' ) ); ?>" class="action-card">
                <span class="action-icon"><span class="dashicons dashicons-groups"></span></span>
                <span class="action-label"><?php esc_html_e( 'Manage Vendors', 'bazaar' ); ?></span>
            </a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-withdraw' ) ); ?>" class="action-card">
                <span class="action-icon"><span class="dashicons dashicons-money-alt"></span></span>
                <span class="action-label"><?php esc_html_e( 'Process Withdrawals', 'bazaar' ); ?></span>
            </a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-announcements' ) ); ?>" class="action-card">
                <span class="action-icon"><span class="dashicons dashicons-megaphone"></span></span>
                <span class="action-label"><?php esc_html_e( 'Send Announcement', 'bazaar' ); ?></span>
            </a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-reports' ) ); ?>" class="action-card">
                <span class="action-icon"><span class="dashicons dashicons-chart-area"></span></span>
                <span class="action-label"><?php esc_html_e( 'View Reports', 'bazaar' ); ?></span>
            </a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-modules' ) ); ?>" class="action-card">
                <span class="action-icon"><span class="dashicons dashicons-admin-plugins"></span></span>
                <span class="action-label"><?php esc_html_e( 'Manage Modules', 'bazaar' ); ?></span>
            </a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-settings' ) ); ?>" class="action-card">
                <span class="action-icon"><span class="dashicons dashicons-admin-settings"></span></span>
                <span class="action-label"><?php esc_html_e( 'Settings', 'bazaar' ); ?></span>
            </a>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Initialize sales chart
    if (typeof Chart !== 'undefined' && document.getElementById('bazaar-sales-chart')) {
        var ctx = document.getElementById('bazaar-sales-chart').getContext('2d');
        var chartData = <?php echo wp_json_encode( $chart_data ); ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: '<?php esc_html_e( 'Sales', 'bazaar' ); ?>',
                    data: chartData.sales,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }, {
                    label: '<?php esc_html_e( 'Commission', 'bazaar' ); ?>',
                    data: chartData.commission,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '<?php echo get_woocommerce_currency_symbol(); ?>' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});
</script>
