<?php
/**
 * Vendor Dashboard Template.
 *
 * @package Bazaar\Templates
 */

defined( 'ABSPATH' ) || exit;

$vendor = Bazaar_Vendor::get_vendor( $vendor_id );
$unread_notifications = Bazaar_Notifications::get_unread_count( $vendor_id );
?>
<div class="bazaar-dashboard">
    <div class="bazaar-dashboard-header">
        <div class="dashboard-welcome">
            <?php bazaar_vendor_avatar( $vendor_id, 60 ); ?>
            <div class="welcome-text">
                <h2><?php printf( esc_html__( 'Welcome, %s!', 'bazaar' ), esc_html( $vendor['store_name'] ) ); ?></h2>
                <p><?php esc_html_e( 'Manage your store from your dashboard.', 'bazaar' ); ?></p>
            </div>
        </div>
        <div class="dashboard-actions">
            <?php if ( $unread_notifications > 0 ) : ?>
                <span class="notification-badge"><?php echo esc_html( $unread_notifications ); ?></span>
            <?php endif; ?>
            <a href="<?php echo esc_url( $vendor['store_url'] ); ?>" class="button" target="_blank">
                <span class="dashicons dashicons-store"></span>
                <?php esc_html_e( 'View Store', 'bazaar' ); ?>
            </a>
        </div>
    </div>

    <div class="bazaar-dashboard-container">
        <aside class="bazaar-dashboard-sidebar">
            <?php bazaar_dashboard_navigation( $current_tab ); ?>
        </aside>

        <main class="bazaar-dashboard-content">
            <?php Bazaar_Vendor_Dashboard::render_tab_content( $current_tab, $vendor_id ); ?>
        </main>
    </div>
</div>
