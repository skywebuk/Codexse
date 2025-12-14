<?php
/**
 * Admin Reverse Withdrawal View.
 *
 * @package Bazaar\Admin\Views
 */

defined( 'ABSPATH' ) || exit;

// Handle form submission
if ( isset( $_POST['bazaar_add_reverse_withdrawal'] ) && check_admin_referer( 'bazaar_reverse_withdrawal' ) ) {
    $vendor_id = isset( $_POST['vendor_id'] ) ? intval( $_POST['vendor_id'] ) : 0;
    $amount = isset( $_POST['amount'] ) ? floatval( $_POST['amount'] ) : 0;
    $type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
    $note = isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '';

    if ( $vendor_id && $amount > 0 && $type ) {
        $result = Bazaar_Reverse_Withdrawal::add( $vendor_id, $amount, $type, $note );
        if ( $result ) {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Reverse withdrawal added successfully.', 'bazaar' ) . '</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Failed to add reverse withdrawal.', 'bazaar' ) . '</p></div>';
        }
    }
}

// Get filters
$status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
$paged = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;

// Get reverse withdrawals
$reverse_withdrawals = Bazaar_Reverse_Withdrawal::get_all( array(
    'status'   => $status,
    'per_page' => 20,
    'page'     => $paged,
) );

// Get statistics
$stats = Bazaar_Reverse_Withdrawal::get_statistics();

// Get all vendors for dropdown
$vendors = get_users( array(
    'role'       => 'bazaar_vendor',
    'meta_key'   => '_bazaar_vendor_status',
    'meta_value' => 'approved',
    'orderby'    => 'display_name',
    'order'      => 'ASC',
) );
?>
<div class="wrap bazaar-admin-wrap">
    <div class="bazaar-page-header">
        <h1><?php esc_html_e( 'Reverse Withdrawal', 'bazaar' ); ?></h1>
        <p class="header-subtitle"><?php esc_html_e( 'Charge vendors for refunds, adjustments, or fees', 'bazaar' ); ?></p>
    </div>

    <div class="bazaar-two-column">
        <!-- Left Column - Form -->
        <div class="bazaar-column-main">
            <!-- Statistics -->
            <div class="bazaar-stats-row compact">
                <div class="stat-card">
                    <div class="stat-info">
                        <span class="stat-value"><?php echo wc_price( $stats['total_amount'] ); ?></span>
                        <span class="stat-label"><?php esc_html_e( 'Total Charged', 'bazaar' ); ?></span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <span class="stat-value"><?php echo wc_price( $stats['pending_amount'] ); ?></span>
                        <span class="stat-label"><?php esc_html_e( 'Pending', 'bazaar' ); ?></span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <span class="stat-value"><?php echo wc_price( $stats['paid_amount'] ); ?></span>
                        <span class="stat-label"><?php esc_html_e( 'Collected', 'bazaar' ); ?></span>
                    </div>
                </div>
            </div>

            <!-- Reverse Withdrawals Table -->
            <div class="bazaar-table-container">
                <div class="table-header">
                    <h2><?php esc_html_e( 'Reverse Withdrawal History', 'bazaar' ); ?></h2>
                    <div class="status-filters inline">
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-reverse-withdrawal' ) ); ?>" class="<?php echo empty( $status ) ? 'current' : ''; ?>">
                            <?php esc_html_e( 'All', 'bazaar' ); ?>
                        </a>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-reverse-withdrawal&status=pending' ) ); ?>" class="<?php echo 'pending' === $status ? 'current' : ''; ?>">
                            <?php esc_html_e( 'Pending', 'bazaar' ); ?>
                        </a>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-reverse-withdrawal&status=paid' ) ); ?>" class="<?php echo 'paid' === $status ? 'current' : ''; ?>">
                            <?php esc_html_e( 'Paid', 'bazaar' ); ?>
                        </a>
                    </div>
                </div>

                <table class="bazaar-admin-table wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Vendor', 'bazaar' ); ?></th>
                            <th><?php esc_html_e( 'Amount', 'bazaar' ); ?></th>
                            <th><?php esc_html_e( 'Type', 'bazaar' ); ?></th>
                            <th><?php esc_html_e( 'Note', 'bazaar' ); ?></th>
                            <th><?php esc_html_e( 'Status', 'bazaar' ); ?></th>
                            <th><?php esc_html_e( 'Date', 'bazaar' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( empty( $reverse_withdrawals['items'] ) ) : ?>
                            <tr>
                                <td colspan="6" class="no-items">
                                    <div class="empty-state">
                                        <span class="dashicons dashicons-undo"></span>
                                        <p><?php esc_html_e( 'No reverse withdrawals found.', 'bazaar' ); ?></p>
                                    </div>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ( $reverse_withdrawals['items'] as $item ) : ?>
                                <?php $vendor = get_userdata( $item->vendor_id ); ?>
                                <tr>
                                    <td>
                                        <?php if ( $vendor ) : ?>
                                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors&action=edit&vendor=' . $vendor->ID ) ); ?>">
                                                <?php echo esc_html( Bazaar_Vendor::get_store_name( $vendor->ID ) ); ?>
                                            </a>
                                        <?php else : ?>
                                            <?php esc_html_e( 'Deleted Vendor', 'bazaar' ); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo wc_price( $item->amount ); ?></strong></td>
                                    <td><?php echo esc_html( Bazaar_Reverse_Withdrawal::get_type_label( $item->type ) ); ?></td>
                                    <td><?php echo esc_html( $item->note ?: '-' ); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo esc_attr( $item->status ); ?>">
                                            <?php echo esc_html( ucfirst( $item->status ) ); ?>
                                        </span>
                                    </td>
                                    <td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $item->created_at ) ) ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ( $reverse_withdrawals['pages'] > 1 ) : ?>
                <div class="bazaar-pagination">
                    <?php
                    echo paginate_links( array(
                        'base'      => add_query_arg( 'paged', '%#%' ),
                        'format'    => '',
                        'current'   => $paged,
                        'total'     => $reverse_withdrawals['pages'],
                        'prev_text' => '<span class="dashicons dashicons-arrow-left-alt2"></span>',
                        'next_text' => '<span class="dashicons dashicons-arrow-right-alt2"></span>',
                    ) );
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Column - Add Form -->
        <div class="bazaar-column-sidebar">
            <div class="bazaar-card">
                <div class="card-header">
                    <h3><?php esc_html_e( 'Add Reverse Withdrawal', 'bazaar' ); ?></h3>
                </div>
                <div class="card-body">
                    <form method="post" class="bazaar-form">
                        <?php wp_nonce_field( 'bazaar_reverse_withdrawal' ); ?>

                        <div class="form-group">
                            <label for="vendor_id"><?php esc_html_e( 'Select Vendor', 'bazaar' ); ?> <span class="required">*</span></label>
                            <select name="vendor_id" id="vendor_id" class="bazaar-select2" required>
                                <option value=""><?php esc_html_e( 'Choose a vendor...', 'bazaar' ); ?></option>
                                <?php foreach ( $vendors as $vendor ) : ?>
                                    <option value="<?php echo esc_attr( $vendor->ID ); ?>">
                                        <?php echo esc_html( Bazaar_Vendor::get_store_name( $vendor->ID ) . ' (' . $vendor->user_email . ')' ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="amount"><?php esc_html_e( 'Amount', 'bazaar' ); ?> (<?php echo esc_html( get_woocommerce_currency_symbol() ); ?>) <span class="required">*</span></label>
                            <input type="number" name="amount" id="amount" step="0.01" min="0.01" class="regular-text" required>
                        </div>

                        <div class="form-group">
                            <label for="type"><?php esc_html_e( 'Type', 'bazaar' ); ?> <span class="required">*</span></label>
                            <select name="type" id="type" required>
                                <option value=""><?php esc_html_e( 'Select type...', 'bazaar' ); ?></option>
                                <option value="refund"><?php esc_html_e( 'Product Refund', 'bazaar' ); ?></option>
                                <option value="fee"><?php esc_html_e( 'Platform Fee', 'bazaar' ); ?></option>
                                <option value="adjustment"><?php esc_html_e( 'Balance Adjustment', 'bazaar' ); ?></option>
                                <option value="shipping"><?php esc_html_e( 'Shipping Fee', 'bazaar' ); ?></option>
                                <option value="advertising"><?php esc_html_e( 'Advertising Fee', 'bazaar' ); ?></option>
                                <option value="other"><?php esc_html_e( 'Other', 'bazaar' ); ?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="note"><?php esc_html_e( 'Note (Optional)', 'bazaar' ); ?></label>
                            <textarea name="note" id="note" rows="3" class="large-text"></textarea>
                            <p class="description"><?php esc_html_e( 'This note will be visible to the vendor.', 'bazaar' ); ?></p>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="bazaar_add_reverse_withdrawal" class="button button-primary button-large">
                                <span class="dashicons dashicons-plus-alt2"></span>
                                <?php esc_html_e( 'Add Reverse Withdrawal', 'bazaar' ); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bazaar-card info-card">
                <div class="card-header">
                    <h3><?php esc_html_e( 'About Reverse Withdrawal', 'bazaar' ); ?></h3>
                </div>
                <div class="card-body">
                    <p><?php esc_html_e( 'Reverse withdrawal allows you to charge vendors for:', 'bazaar' ); ?></p>
                    <ul>
                        <li><strong><?php esc_html_e( 'Refunds', 'bazaar' ); ?></strong> - <?php esc_html_e( 'When customers return products', 'bazaar' ); ?></li>
                        <li><strong><?php esc_html_e( 'Fees', 'bazaar' ); ?></strong> - <?php esc_html_e( 'Platform or shipping fees', 'bazaar' ); ?></li>
                        <li><strong><?php esc_html_e( 'Adjustments', 'bazaar' ); ?></strong> - <?php esc_html_e( 'Balance corrections', 'bazaar' ); ?></li>
                    </ul>
                    <p class="note"><?php esc_html_e( 'The amount will be deducted from the vendor\'s balance.', 'bazaar' ); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
