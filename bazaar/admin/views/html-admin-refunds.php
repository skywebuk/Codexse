<?php
/**
 * Admin Refunds View.
 *
 * @package Bazaar\Admin\Views
 */

defined( 'ABSPATH' ) || exit;

// Handle actions
if ( isset( $_GET['action'] ) && isset( $_GET['id'] ) && isset( $_GET['_wpnonce'] ) ) {
    if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'bazaar_refund_action' ) ) {
        $action = sanitize_text_field( wp_unslash( $_GET['action'] ) );
        $refund_id = intval( $_GET['id'] );

        switch ( $action ) {
            case 'approve':
                Bazaar_Refunds::update_status( $refund_id, 'approved' );
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Refund approved.', 'bazaar' ) . '</p></div>';
                break;
            case 'reject':
                Bazaar_Refunds::update_status( $refund_id, 'rejected' );
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Refund rejected.', 'bazaar' ) . '</p></div>';
                break;
            case 'complete':
                Bazaar_Refunds::update_status( $refund_id, 'completed' );
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Refund completed.', 'bazaar' ) . '</p></div>';
                break;
        }
    }
}

// Get filters
$status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
$paged = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;

// Get refunds
$refunds = Bazaar_Refunds::get_all( array(
    'status'   => $status,
    'per_page' => 20,
    'page'     => $paged,
) );

// Get statistics
$stats = Bazaar_Refunds::get_statistics();
?>
<div class="wrap bazaar-admin-wrap">
    <div class="bazaar-page-header">
        <h1><?php esc_html_e( 'Refund Requests', 'bazaar' ); ?></h1>
        <p class="header-subtitle"><?php esc_html_e( 'Manage customer refund requests from vendor orders', 'bazaar' ); ?></p>
    </div>

    <!-- Statistics -->
    <div class="bazaar-stats-row">
        <div class="stat-card pending">
            <div class="stat-icon"><span class="dashicons dashicons-clock"></span></div>
            <div class="stat-info">
                <span class="stat-value"><?php echo esc_html( number_format_i18n( $stats['pending_count'] ) ); ?></span>
                <span class="stat-label"><?php esc_html_e( 'Pending', 'bazaar' ); ?></span>
                <span class="stat-amount"><?php echo wc_price( $stats['pending_amount'] ); ?></span>
            </div>
        </div>
        <div class="stat-card approved">
            <div class="stat-icon"><span class="dashicons dashicons-yes-alt"></span></div>
            <div class="stat-info">
                <span class="stat-value"><?php echo esc_html( number_format_i18n( $stats['approved_count'] ) ); ?></span>
                <span class="stat-label"><?php esc_html_e( 'Approved', 'bazaar' ); ?></span>
                <span class="stat-amount"><?php echo wc_price( $stats['approved_amount'] ); ?></span>
            </div>
        </div>
        <div class="stat-card completed">
            <div class="stat-icon"><span class="dashicons dashicons-saved"></span></div>
            <div class="stat-info">
                <span class="stat-value"><?php echo esc_html( number_format_i18n( $stats['completed_count'] ) ); ?></span>
                <span class="stat-label"><?php esc_html_e( 'Completed', 'bazaar' ); ?></span>
                <span class="stat-amount"><?php echo wc_price( $stats['completed_amount'] ); ?></span>
            </div>
        </div>
        <div class="stat-card rejected">
            <div class="stat-icon"><span class="dashicons dashicons-dismiss"></span></div>
            <div class="stat-info">
                <span class="stat-value"><?php echo esc_html( number_format_i18n( $stats['rejected_count'] ) ); ?></span>
                <span class="stat-label"><?php esc_html_e( 'Rejected', 'bazaar' ); ?></span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bazaar-filters-bar">
        <ul class="status-filters">
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-refunds' ) ); ?>" class="<?php echo empty( $status ) ? 'current' : ''; ?>">
                    <?php esc_html_e( 'All', 'bazaar' ); ?>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-refunds&status=pending' ) ); ?>" class="<?php echo 'pending' === $status ? 'current' : ''; ?>">
                    <?php esc_html_e( 'Pending', 'bazaar' ); ?>
                    <?php if ( $stats['pending_count'] > 0 ) : ?>
                        <span class="count">(<?php echo esc_html( $stats['pending_count'] ); ?>)</span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-refunds&status=approved' ) ); ?>" class="<?php echo 'approved' === $status ? 'current' : ''; ?>">
                    <?php esc_html_e( 'Approved', 'bazaar' ); ?>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-refunds&status=completed' ) ); ?>" class="<?php echo 'completed' === $status ? 'current' : ''; ?>">
                    <?php esc_html_e( 'Completed', 'bazaar' ); ?>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-refunds&status=rejected' ) ); ?>" class="<?php echo 'rejected' === $status ? 'current' : ''; ?>">
                    <?php esc_html_e( 'Rejected', 'bazaar' ); ?>
                </a>
            </li>
        </ul>
    </div>

    <!-- Refunds Table -->
    <div class="bazaar-table-container">
        <table class="bazaar-admin-table wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th class="column-id"><?php esc_html_e( 'ID', 'bazaar' ); ?></th>
                    <th class="column-order"><?php esc_html_e( 'Order', 'bazaar' ); ?></th>
                    <th class="column-vendor"><?php esc_html_e( 'Vendor', 'bazaar' ); ?></th>
                    <th class="column-customer"><?php esc_html_e( 'Customer', 'bazaar' ); ?></th>
                    <th class="column-amount"><?php esc_html_e( 'Amount', 'bazaar' ); ?></th>
                    <th class="column-reason"><?php esc_html_e( 'Reason', 'bazaar' ); ?></th>
                    <th class="column-status"><?php esc_html_e( 'Status', 'bazaar' ); ?></th>
                    <th class="column-date"><?php esc_html_e( 'Date', 'bazaar' ); ?></th>
                    <th class="column-actions"><?php esc_html_e( 'Actions', 'bazaar' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $refunds['items'] ) ) : ?>
                    <tr>
                        <td colspan="9" class="no-items">
                            <div class="empty-state">
                                <span class="dashicons dashicons-undo"></span>
                                <p><?php esc_html_e( 'No refund requests found.', 'bazaar' ); ?></p>
                            </div>
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ( $refunds['items'] as $refund ) : ?>
                        <?php
                        $order = wc_get_order( $refund->order_id );
                        $vendor = get_userdata( $refund->vendor_id );
                        ?>
                        <tr>
                            <td class="column-id">#<?php echo esc_html( $refund->id ); ?></td>
                            <td class="column-order">
                                <?php if ( $order ) : ?>
                                    <a href="<?php echo esc_url( $order->get_edit_order_url() ); ?>">
                                        #<?php echo esc_html( $order->get_order_number() ); ?>
                                    </a>
                                <?php else : ?>
                                    #<?php echo esc_html( $refund->order_id ); ?>
                                <?php endif; ?>
                            </td>
                            <td class="column-vendor">
                                <?php if ( $vendor ) : ?>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors&action=edit&vendor=' . $vendor->ID ) ); ?>">
                                        <?php echo esc_html( Bazaar_Vendor::get_store_name( $vendor->ID ) ); ?>
                                    </a>
                                <?php else : ?>
                                    <?php esc_html_e( 'Deleted', 'bazaar' ); ?>
                                <?php endif; ?>
                            </td>
                            <td class="column-customer">
                                <?php echo $order ? esc_html( $order->get_formatted_billing_full_name() ) : '-'; ?>
                            </td>
                            <td class="column-amount">
                                <strong><?php echo wc_price( $refund->amount ); ?></strong>
                            </td>
                            <td class="column-reason">
                                <?php echo esc_html( wp_trim_words( $refund->reason, 10 ) ); ?>
                            </td>
                            <td class="column-status">
                                <span class="status-badge status-<?php echo esc_attr( $refund->status ); ?>">
                                    <?php echo esc_html( ucfirst( $refund->status ) ); ?>
                                </span>
                            </td>
                            <td class="column-date">
                                <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $refund->created_at ) ) ); ?>
                            </td>
                            <td class="column-actions">
                                <?php if ( 'pending' === $refund->status ) : ?>
                                    <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=bazaar-refunds&action=approve&id=' . $refund->id ), 'bazaar_refund_action' ) ); ?>" class="button button-small button-primary" title="<?php esc_attr_e( 'Approve', 'bazaar' ); ?>">
                                        <span class="dashicons dashicons-yes"></span>
                                    </a>
                                    <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=bazaar-refunds&action=reject&id=' . $refund->id ), 'bazaar_refund_action' ) ); ?>" class="button button-small button-danger" title="<?php esc_attr_e( 'Reject', 'bazaar' ); ?>">
                                        <span class="dashicons dashicons-no"></span>
                                    </a>
                                <?php elseif ( 'approved' === $refund->status ) : ?>
                                    <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=bazaar-refunds&action=complete&id=' . $refund->id ), 'bazaar_refund_action' ) ); ?>" class="button button-small button-success">
                                        <?php esc_html_e( 'Mark Complete', 'bazaar' ); ?>
                                    </a>
                                <?php else : ?>
                                    <span class="no-actions">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ( $refunds['pages'] > 1 ) : ?>
        <div class="bazaar-pagination">
            <?php
            echo paginate_links( array(
                'base'      => add_query_arg( 'paged', '%#%' ),
                'format'    => '',
                'current'   => $paged,
                'total'     => $refunds['pages'],
            ) );
            ?>
        </div>
    <?php endif; ?>
</div>
