<?php
/**
 * Admin Reports View.
 *
 * @package Bazaar\Admin\Views
 */

defined( 'ABSPATH' ) || exit;

// Get current report type
$report_type = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : 'overview';
$date_range = isset( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : 'month';
$vendor_id = isset( $_GET['vendor'] ) ? intval( $_GET['vendor'] ) : 0;

// Get date range
$dates = Bazaar_Admin_Dashboard::get_date_range( $date_range );

// Get report data based on type
$report_data = Bazaar_Reports::get_data( $report_type, $dates, $vendor_id );

// Get vendors for filter
$vendors = get_users( array(
    'role'    => 'bazaar_vendor',
    'orderby' => 'display_name',
    'order'   => 'ASC',
) );
?>
<div class="wrap bazaar-admin-wrap bazaar-reports">
    <div class="bazaar-page-header">
        <h1><?php esc_html_e( 'Reports', 'bazaar' ); ?></h1>
        <p class="header-subtitle"><?php esc_html_e( 'Marketplace analytics and insights', 'bazaar' ); ?></p>
    </div>

    <!-- Report Navigation -->
    <div class="bazaar-report-nav">
        <ul class="report-tabs">
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-reports&type=overview' ) ); ?>" class="<?php echo 'overview' === $report_type ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-chart-area"></span>
                    <?php esc_html_e( 'Overview', 'bazaar' ); ?>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-reports&type=sales' ) ); ?>" class="<?php echo 'sales' === $report_type ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-chart-bar"></span>
                    <?php esc_html_e( 'Sales by Day', 'bazaar' ); ?>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-reports&type=vendors' ) ); ?>" class="<?php echo 'vendors' === $report_type ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-groups"></span>
                    <?php esc_html_e( 'Top Vendors', 'bazaar' ); ?>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-reports&type=products' ) ); ?>" class="<?php echo 'products' === $report_type ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-archive"></span>
                    <?php esc_html_e( 'Top Products', 'bazaar' ); ?>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-reports&type=commission' ) ); ?>" class="<?php echo 'commission' === $report_type ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-money-alt"></span>
                    <?php esc_html_e( 'Commission', 'bazaar' ); ?>
                </a>
            </li>
        </ul>

        <div class="report-filters">
            <form method="get" class="filter-form">
                <input type="hidden" name="page" value="bazaar-reports">
                <input type="hidden" name="type" value="<?php echo esc_attr( $report_type ); ?>">

                <select name="range" class="bazaar-select" onchange="this.form.submit()">
                    <option value="today" <?php selected( $date_range, 'today' ); ?>><?php esc_html_e( 'Today', 'bazaar' ); ?></option>
                    <option value="week" <?php selected( $date_range, 'week' ); ?>><?php esc_html_e( 'This Week', 'bazaar' ); ?></option>
                    <option value="month" <?php selected( $date_range, 'month' ); ?>><?php esc_html_e( 'This Month', 'bazaar' ); ?></option>
                    <option value="year" <?php selected( $date_range, 'year' ); ?>><?php esc_html_e( 'This Year', 'bazaar' ); ?></option>
                </select>

                <button type="submit" class="button">
                    <span class="dashicons dashicons-filter"></span>
                    <?php esc_html_e( 'Filter', 'bazaar' ); ?>
                </button>

                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-reports&type=' . $report_type . '&range=' . $date_range . '&export=csv' ) ); ?>" class="button">
                    <span class="dashicons dashicons-download"></span>
                    <?php esc_html_e( 'Export CSV', 'bazaar' ); ?>
                </a>
            </form>
        </div>
    </div>

    <!-- Report Content -->
    <div class="bazaar-report-content">
        <!-- Overview Report -->
        <div class="bazaar-stats-grid large">
            <div class="bazaar-stat-card">
                <div class="stat-icon sales"><span class="dashicons dashicons-chart-bar"></span></div>
                <div class="stat-content">
                    <span class="stat-value"><?php echo wc_price( $report_data['gross_sales'] ?? 0 ); ?></span>
                    <span class="stat-label"><?php esc_html_e( 'Gross Sales', 'bazaar' ); ?></span>
                </div>
            </div>
            <div class="bazaar-stat-card">
                <div class="stat-icon commission"><span class="dashicons dashicons-money-alt"></span></div>
                <div class="stat-content">
                    <span class="stat-value"><?php echo wc_price( $report_data['admin_commission'] ?? 0 ); ?></span>
                    <span class="stat-label"><?php esc_html_e( 'Admin Commission', 'bazaar' ); ?></span>
                </div>
            </div>
            <div class="bazaar-stat-card">
                <div class="stat-icon vendors"><span class="dashicons dashicons-businessman"></span></div>
                <div class="stat-content">
                    <span class="stat-value"><?php echo wc_price( $report_data['vendor_earnings'] ?? 0 ); ?></span>
                    <span class="stat-label"><?php esc_html_e( 'Vendor Earnings', 'bazaar' ); ?></span>
                </div>
            </div>
            <div class="bazaar-stat-card">
                <div class="stat-icon orders"><span class="dashicons dashicons-cart"></span></div>
                <div class="stat-content">
                    <span class="stat-value"><?php echo esc_html( number_format_i18n( $report_data['total_orders'] ?? 0 ) ); ?></span>
                    <span class="stat-label"><?php esc_html_e( 'Orders', 'bazaar' ); ?></span>
                </div>
            </div>
        </div>

        <div class="bazaar-dashboard-card chart-card full-width">
            <div class="card-header">
                <h2><?php esc_html_e( 'Sales Overview', 'bazaar' ); ?></h2>
            </div>
            <div class="card-content">
                <canvas id="bazaar-report-chart" height="350"></canvas>
            </div>
        </div>
    </div>
</div>
