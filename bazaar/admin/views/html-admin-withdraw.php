<?php
/**
 * Admin Withdraw View.
 *
 * @package Bazaar\Admin\Views
 */

defined( 'ABSPATH' ) || exit;

// Handle actions
if ( isset( $_GET['action'] ) && isset( $_GET['id'] ) && isset( $_GET['_wpnonce'] ) ) {
    if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'bazaar_withdraw_action' ) ) {
        $action = sanitize_text_field( wp_unslash( $_GET['action'] ) );
        $withdraw_id = intval( $_GET['id'] );

        switch ( $action ) {
            case 'approve':
                Bazaar_Withdrawal::update_status( $withdraw_id, 'approved' );
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Withdrawal request approved.', 'bazaar' ) . '</p></div>';
                break;
            case 'reject':
                Bazaar_Withdrawal::update_status( $withdraw_id, 'cancelled' );
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Withdrawal request rejected.', 'bazaar' ) . '</p></div>';
                break;
            case 'paid':
                Bazaar_Withdrawal::update_status( $withdraw_id, 'paid' );
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Withdrawal marked as paid.', 'bazaar' ) . '</p></div>';
                break;
        }
    }
}

// Get filters
$status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
$paged = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
$per_page = 20;

// Get withdrawals
$withdrawals = Bazaar_Withdrawal::get_all_withdrawals( array(
    'status'   => $status,
    'per_page' => $per_page,
    'page'     => $paged,
) );

// Get statistics
$stats = Bazaar_Withdrawal::get_statistics();
?>
<div class="wrap bazaar-admin-wrap">
    <div class="bazaar-page-header">
        <h1><?php esc_html_e( 'Withdraw Requests', 'bazaar' ); ?></h1>
        <p class="header-subtitle"><?php esc_html_e( 'Manage vendor withdrawal requests', 'bazaar' ); ?></p>
    </div>

    <!-- Statistics Cards -->
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
        <div class="stat-card paid">
            <div class="stat-icon"><span class="dashicons dashicons-money-alt"></span></div>
            <div class="stat-info">
                <span class="stat-value"><?php echo esc_html( number_format_i18n( $stats['paid_count'] ) ); ?></span>
                <span class="stat-label"><?php esc_html_e( 'Paid', 'bazaar' ); ?></span>
                <span class="stat-amount"><?php echo wc_price( $stats['paid_amount'] ); ?></span>
            </div>
        </div>
        <div class="stat-card cancelled">
            <div class="stat-icon"><span class="dashicons dashicons-dismiss"></span></div>
            <div class="stat-info">
                <span class="stat-value"><?php echo esc_html( number_format_i18n( $stats['cancelled_count'] ) ); ?></span>
                <span class="stat-label"><?php esc_html_e( 'Cancelled', 'bazaar' ); ?></span>
                <span class="stat-amount"><?php echo wc_price( $stats['cancelled_amount'] ); ?></span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bazaar-filters-bar">
        <ul class="status-filters">
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-withdraw' ) ); ?>" class="<?php echo empty( $status ) ? 'current' : ''; ?>">
                    <?php esc_html_e( 'All', 'bazaar' ); ?>
                    <span class="count">(<?php echo esc_html( $stats['total_count'] ); ?>)</span>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-withdraw&status=pending' ) ); ?>" class="<?php echo 'pending' === $status ? 'current' : ''; ?>">
                    <?php esc_html_e( 'Pending', 'bazaar' ); ?>
                    <span class="count">(<?php echo esc_html( $stats['pending_count'] ); ?>)</span>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-withdraw&status=approved' ) ); ?>" class="<?php echo 'approved' === $status ? 'current' : ''; ?>">
                    <?php esc_html_e( 'Approved', 'bazaar' ); ?>
                    <span class="count">(<?php echo esc_html( $stats['approved_count'] ); ?>)</span>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-withdraw&status=paid' ) ); ?>" class="<?php echo 'paid' === $status ? 'current' : ''; ?>">
                    <?php esc_html_e( 'Paid', 'bazaar' ); ?>
                    <span class="count">(<?php echo esc_html( $stats['paid_count'] ); ?>)</span>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-withdraw&status=cancelled' ) ); ?>" class="<?php echo 'cancelled' === $status ? 'current' : ''; ?>">
                    <?php esc_html_e( 'Cancelled', 'bazaar' ); ?>
                    <span class="count">(<?php echo esc_html( $stats['cancelled_count'] ); ?>)</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Withdrawals Table -->
    <div class="bazaar-table-container">
        <table class="bazaar-admin-table wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th class="column-id"><?php esc_html_e( 'ID', 'bazaar' ); ?></th>
                    <th class="column-vendor"><?php esc_html_e( 'Vendor', 'bazaar' ); ?></th>
                    <th class="column-amount"><?php esc_html_e( 'Amount', 'bazaar' ); ?></th>
                    <th class="column-method"><?php esc_html_e( 'Method', 'bazaar' ); ?></th>
                    <th class="column-details"><?php esc_html_e( 'Payment Details', 'bazaar' ); ?></th>
                    <th class="column-status"><?php esc_html_e( 'Status', 'bazaar' ); ?></th>
                    <th class="column-date"><?php esc_html_e( 'Date', 'bazaar' ); ?></th>
                    <th class="column-actions"><?php esc_html_e( 'Actions', 'bazaar' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $withdrawals['withdrawals'] ) ) : ?>
                    <tr>
                        <td colspan="8" class="no-items">
                            <div class="empty-state">
                                <span class="dashicons dashicons-money-alt"></span>
                                <p><?php esc_html_e( 'No withdrawal requests found.', 'bazaar' ); ?></p>
                            </div>
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ( $withdrawals['withdrawals'] as $withdraw ) : ?>
                        <?php
                        $vendor = get_userdata( $withdraw->vendor_id );
                        $store_name = $vendor ? Bazaar_Vendor::get_store_name( $withdraw->vendor_id ) : __( 'Unknown', 'bazaar' );
                        $method_details = maybe_unserialize( $withdraw->method_details );
                        ?>
                        <tr>
                            <td class="column-id">
                                <strong>#<?php echo esc_html( $withdraw->id ); ?></strong>
                            </td>
                            <td class="column-vendor">
                                <div class="vendor-info">
                                    <?php if ( $vendor ) : ?>
                                        <?php echo get_avatar( $vendor->ID, 32 ); ?>
                                        <div class="vendor-details">
                                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors&action=edit&vendor=' . $vendor->ID ) ); ?>">
                                                <?php echo esc_html( $store_name ); ?>
                                            </a>
                                            <span class="vendor-email"><?php echo esc_html( $vendor->user_email ); ?></span>
                                        </div>
                                    <?php else : ?>
                                        <span class="deleted-vendor"><?php esc_html_e( 'Deleted Vendor', 'bazaar' ); ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="column-amount">
                                <strong><?php echo wc_price( $withdraw->amount ); ?></strong>
                            </td>
                            <td class="column-method">
                                <?php echo esc_html( Bazaar_Withdrawal::get_method_label( $withdraw->method ) ); ?>
                            </td>
                            <td class="column-details">
                                <?php if ( ! empty( $method_details ) ) : ?>
                                    <div class="method-details-popup" title="<?php esc_attr_e( 'Click to view details', 'bazaar' ); ?>">
                                        <?php
                                        if ( 'paypal' === $withdraw->method && isset( $method_details['email'] ) ) {
                                            echo esc_html( $method_details['email'] );
                                        } elseif ( 'bank_transfer' === $withdraw->method ) {
                                            echo isset( $method_details['account_number'] ) ? esc_html( '****' . substr( $method_details['account_number'], -4 ) ) : '-';
                                        } else {
                                            esc_html_e( 'View Details', 'bazaar' );
                                        }
                                        ?>
                                    </div>
                                <?php else : ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="column-status">
                                <span class="status-badge status-<?php echo esc_attr( $withdraw->status ); ?>">
                                    <?php echo esc_html( Bazaar_Withdrawal::get_status_label( $withdraw->status ) ); ?>
                                </span>
                            </td>
                            <td class="column-date">
                                <span class="date"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $withdraw->created_at ) ) ); ?></span>
                                <span class="time"><?php echo esc_html( date_i18n( get_option( 'time_format' ), strtotime( $withdraw->created_at ) ) ); ?></span>
                            </td>
                            <td class="column-actions">
                                <div class="action-buttons">
                                    <?php if ( 'pending' === $withdraw->status ) : ?>
                                        <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=bazaar-withdraw&action=approve&id=' . $withdraw->id ), 'bazaar_withdraw_action' ) ); ?>" class="button button-small button-primary" title="<?php esc_attr_e( 'Approve', 'bazaar' ); ?>">
                                            <span class="dashicons dashicons-yes"></span>
                                        </a>
                                        <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=bazaar-withdraw&action=reject&id=' . $withdraw->id ), 'bazaar_withdraw_action' ) ); ?>" class="button button-small button-danger" title="<?php esc_attr_e( 'Reject', 'bazaar' ); ?>" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to reject this request?', 'bazaar' ); ?>');">
                                            <span class="dashicons dashicons-no"></span>
                                        </a>
                                    <?php elseif ( 'approved' === $withdraw->status ) : ?>
                                        <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=bazaar-withdraw&action=paid&id=' . $withdraw->id ), 'bazaar_withdraw_action' ) ); ?>" class="button button-small button-success" title="<?php esc_attr_e( 'Mark as Paid', 'bazaar' ); ?>">
                                            <span class="dashicons dashicons-money-alt"></span> <?php esc_html_e( 'Mark Paid', 'bazaar' ); ?>
                                        </a>
                                    <?php else : ?>
                                        <span class="no-actions">-</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php if ( ! empty( $withdraw->note ) ) : ?>
                            <tr class="row-note">
                                <td colspan="8">
                                    <div class="note-content">
                                        <strong><?php esc_html_e( 'Note:', 'bazaar' ); ?></strong>
                                        <?php echo esc_html( $withdraw->note ); ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ( $withdrawals['pages'] > 1 ) : ?>
        <div class="bazaar-pagination">
            <?php
            echo paginate_links( array(
                'base'      => add_query_arg( 'paged', '%#%' ),
                'format'    => '',
                'current'   => $paged,
                'total'     => $withdrawals['pages'],
                'prev_text' => '<span class="dashicons dashicons-arrow-left-alt2"></span>',
                'next_text' => '<span class="dashicons dashicons-arrow-right-alt2"></span>',
            ) );
            ?>
        </div>
    <?php endif; ?>
</div>
