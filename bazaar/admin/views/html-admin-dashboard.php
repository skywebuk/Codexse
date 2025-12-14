<?php
/**
 * Admin Dashboard View.
 *
 * @package Bazaar\Admin\Views
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap bazaar-admin-wrap">
    <h1><?php esc_html_e( 'Bazaar Dashboard', 'bazaar' ); ?></h1>

    <div class="bazaar-dashboard-widgets">
        <div class="bazaar-widget-row">
            <div class="bazaar-widget bazaar-widget-stat">
                <div class="widget-icon"><span class="dashicons dashicons-groups"></span></div>
                <div class="widget-content">
                    <div class="widget-value"><?php echo esc_html( $stats['total_vendors'] ); ?></div>
                    <div class="widget-label"><?php esc_html_e( 'Total Vendors', 'bazaar' ); ?></div>
                </div>
                <?php if ( $stats['pending_vendors'] > 0 ) : ?>
                    <div class="widget-badge"><?php echo esc_html( $stats['pending_vendors'] ); ?> <?php esc_html_e( 'pending', 'bazaar' ); ?></div>
                <?php endif; ?>
            </div>

            <div class="bazaar-widget bazaar-widget-stat">
                <div class="widget-icon"><span class="dashicons dashicons-chart-bar"></span></div>
                <div class="widget-content">
                    <div class="widget-value"><?php echo wc_price( $stats['gross_sales'] ); ?></div>
                    <div class="widget-label"><?php esc_html_e( 'Gross Sales (This Month)', 'bazaar' ); ?></div>
                </div>
            </div>

            <div class="bazaar-widget bazaar-widget-stat">
                <div class="widget-icon"><span class="dashicons dashicons-money-alt"></span></div>
                <div class="widget-content">
                    <div class="widget-value"><?php echo wc_price( $stats['admin_commission'] ); ?></div>
                    <div class="widget-label"><?php esc_html_e( 'Admin Commission', 'bazaar' ); ?></div>
                </div>
            </div>

            <div class="bazaar-widget bazaar-widget-stat">
                <div class="widget-icon"><span class="dashicons dashicons-cart"></span></div>
                <div class="widget-content">
                    <div class="widget-value"><?php echo esc_html( $stats['total_orders'] ); ?></div>
                    <div class="widget-label"><?php esc_html_e( 'Orders (This Month)', 'bazaar' ); ?></div>
                </div>
            </div>
        </div>

        <div class="bazaar-widget-row">
            <div class="bazaar-widget bazaar-widget-stat bazaar-widget-warning">
                <div class="widget-icon"><span class="dashicons dashicons-clock"></span></div>
                <div class="widget-content">
                    <div class="widget-value"><?php echo esc_html( $stats['pending_withdrawals'] ); ?></div>
                    <div class="widget-label"><?php esc_html_e( 'Pending Withdrawals', 'bazaar' ); ?></div>
                    <div class="widget-subvalue"><?php echo wc_price( $stats['pending_withdrawal_amount'] ); ?></div>
                </div>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-withdrawals&status=pending' ) ); ?>" class="widget-link">
                    <?php esc_html_e( 'View', 'bazaar' ); ?> &rarr;
                </a>
            </div>

            <div class="bazaar-widget bazaar-widget-stat bazaar-widget-warning">
                <div class="widget-icon"><span class="dashicons dashicons-visibility"></span></div>
                <div class="widget-content">
                    <div class="widget-value"><?php echo esc_html( $stats['pending_products'] ); ?></div>
                    <div class="widget-label"><?php esc_html_e( 'Products Pending Review', 'bazaar' ); ?></div>
                </div>
                <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=product&post_status=pending' ) ); ?>" class="widget-link">
                    <?php esc_html_e( 'Review', 'bazaar' ); ?> &rarr;
                </a>
            </div>

            <div class="bazaar-widget bazaar-widget-stat">
                <div class="widget-icon"><span class="dashicons dashicons-businessman"></span></div>
                <div class="widget-content">
                    <div class="widget-value"><?php echo wc_price( $stats['vendor_earnings'] ); ?></div>
                    <div class="widget-label"><?php esc_html_e( 'Vendor Earnings', 'bazaar' ); ?></div>
                </div>
            </div>

            <div class="bazaar-widget bazaar-widget-stat">
                <div class="widget-icon"><span class="dashicons dashicons-admin-users"></span></div>
                <div class="widget-content">
                    <div class="widget-value"><?php echo esc_html( $stats['pending_vendors'] ); ?></div>
                    <div class="widget-label"><?php esc_html_e( 'Pending Vendor Approval', 'bazaar' ); ?></div>
                </div>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors&status=pending' ) ); ?>" class="widget-link">
                    <?php esc_html_e( 'View', 'bazaar' ); ?> &rarr;
                </a>
            </div>
        </div>
    </div>

    <div class="bazaar-dashboard-actions">
        <h2><?php esc_html_e( 'Quick Actions', 'bazaar' ); ?></h2>
        <div class="action-buttons">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors' ) ); ?>" class="button button-primary">
                <span class="dashicons dashicons-groups"></span>
                <?php esc_html_e( 'Manage Vendors', 'bazaar' ); ?>
            </a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-withdrawals' ) ); ?>" class="button button-primary">
                <span class="dashicons dashicons-money-alt"></span>
                <?php esc_html_e( 'Process Withdrawals', 'bazaar' ); ?>
            </a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-reports' ) ); ?>" class="button">
                <span class="dashicons dashicons-chart-area"></span>
                <?php esc_html_e( 'View Reports', 'bazaar' ); ?>
            </a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-settings' ) ); ?>" class="button">
                <span class="dashicons dashicons-admin-settings"></span>
                <?php esc_html_e( 'Settings', 'bazaar' ); ?>
            </a>
        </div>
    </div>
</div>
