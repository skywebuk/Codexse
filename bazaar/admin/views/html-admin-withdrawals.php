<?php
/**
 * Admin Withdrawals View.
 *
 * @package Bazaar\Admin\Views
 */

defined( 'ABSPATH' ) || exit;

// Handle messages
if ( isset( $_GET['message'] ) ) {
    $messages = array(
        'approved' => __( 'Withdrawal approved.', 'bazaar' ),
        'rejected' => __( 'Withdrawal rejected.', 'bazaar' ),
        'paid'     => __( 'Withdrawal marked as paid.', 'bazaar' ),
    );

    if ( isset( $messages[ $_GET['message'] ] ) ) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $messages[ $_GET['message'] ] ) . '</p></div>';
    }
}
?>
<div class="wrap bazaar-admin-wrap">
    <h1><?php esc_html_e( 'Withdrawals', 'bazaar' ); ?></h1>

    <div class="bazaar-withdrawal-stats">
        <div class="stat-box">
            <div class="stat-value"><?php echo esc_html( $stats['pending_count'] ); ?></div>
            <div class="stat-label"><?php esc_html_e( 'Pending', 'bazaar' ); ?></div>
            <div class="stat-amount"><?php echo wc_price( $stats['pending_amount'] ); ?></div>
        </div>
        <div class="stat-box">
            <div class="stat-value"><?php echo esc_html( $stats['approved_count'] ); ?></div>
            <div class="stat-label"><?php esc_html_e( 'Approved', 'bazaar' ); ?></div>
            <div class="stat-amount"><?php echo wc_price( $stats['approved_amount'] ); ?></div>
        </div>
        <div class="stat-box">
            <div class="stat-value"><?php echo esc_html( $stats['paid_count'] ); ?></div>
            <div class="stat-label"><?php esc_html_e( 'Paid', 'bazaar' ); ?></div>
            <div class="stat-amount"><?php echo wc_price( $stats['paid_amount'] ); ?></div>
        </div>
    </div>

    <div class="bazaar-filters">
        <ul class="subsubsub">
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-withdrawals' ) ); ?>" class="<?php echo empty( $status ) ? 'current' : ''; ?>">
                    <?php esc_html_e( 'All', 'bazaar' ); ?>
                </a> |
            </li>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-withdrawals&status=pending' ) ); ?>" class="<?php echo 'pending' === $status ? 'current' : ''; ?>">
                    <?php esc_html_e( 'Pending', 'bazaar' ); ?>
                    <?php if ( $stats['pending_count'] > 0 ) : ?>
                        <span class="count">(<?php echo esc_html( $stats['pending_count'] ); ?>)</span>
                    <?php endif; ?>
                </a> |
            </li>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-withdrawals&status=approved' ) ); ?>" class="<?php echo 'approved' === $status ? 'current' : ''; ?>">
                    <?php esc_html_e( 'Approved', 'bazaar' ); ?>
                </a> |
            </li>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-withdrawals&status=paid' ) ); ?>" class="<?php echo 'paid' === $status ? 'current' : ''; ?>">
                    <?php esc_html_e( 'Paid', 'bazaar' ); ?>
                </a> |
            </li>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-withdrawals&status=rejected' ) ); ?>" class="<?php echo 'rejected' === $status ? 'current' : ''; ?>">
                    <?php esc_html_e( 'Rejected', 'bazaar' ); ?>
                </a>
            </li>
        </ul>
    </div>

    <table class="wp-list-table widefat fixed striped bazaar-withdrawals-table">
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
                    <td colspan="8"><?php esc_html_e( 'No withdrawals found.', 'bazaar' ); ?></td>
                </tr>
            <?php else : ?>
                <?php foreach ( $withdrawals['withdrawals'] as $withdrawal ) : ?>
                    <?php
                    $vendor = get_userdata( $withdrawal->vendor_id );
                    $actions = Bazaar_Admin_Withdrawals::get_action_links( $withdrawal );
                    ?>
                    <tr>
                        <td class="column-id">#<?php echo esc_html( $withdrawal->id ); ?></td>
                        <td class="column-vendor">
                            <?php if ( $vendor ) : ?>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors&action=view&vendor=' . $withdrawal->vendor_id ) ); ?>">
                                    <?php echo esc_html( $vendor->display_name ); ?>
                                </a>
                            <?php else : ?>
                                <?php esc_html_e( 'Unknown', 'bazaar' ); ?>
                            <?php endif; ?>
                        </td>
                        <td class="column-amount">
                            <strong><?php echo wc_price( $withdrawal->amount ); ?></strong>
                        </td>
                        <td class="column-method">
                            <?php echo esc_html( ucwords( str_replace( '_', ' ', $withdrawal->method ) ) ); ?>
                        </td>
                        <td class="column-details">
                            <?php echo Bazaar_Admin_Withdrawals::format_method_details( $withdrawal->method, $withdrawal->method_details ); ?>
                        </td>
                        <td class="column-status">
                            <?php echo Bazaar_Admin_Withdrawals::get_status_badge( $withdrawal->status ); ?>
                        </td>
                        <td class="column-date">
                            <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $withdrawal->created_at ) ) ); ?>
                        </td>
                        <td class="column-actions">
                            <?php foreach ( $actions as $action_key => $action ) : ?>
                                <a href="<?php echo esc_url( $action['url'] ); ?>" class="button button-small <?php echo esc_attr( $action['class'] ); ?>">
                                    <?php echo esc_html( $action['label'] ); ?>
                                </a>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                    <?php if ( $withdrawal->note ) : ?>
                        <tr class="withdrawal-note">
                            <td></td>
                            <td colspan="7">
                                <em><?php esc_html_e( 'Vendor Note:', 'bazaar' ); ?></em> <?php echo esc_html( $withdrawal->note ); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if ( $withdrawal->admin_note ) : ?>
                        <tr class="withdrawal-admin-note">
                            <td></td>
                            <td colspan="7">
                                <em><?php esc_html_e( 'Admin Note:', 'bazaar' ); ?></em> <?php echo esc_html( $withdrawal->admin_note ); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ( $withdrawals['pages'] > 1 ) : ?>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <?php
                echo paginate_links(
                    array(
                        'base'      => add_query_arg( 'paged', '%#%' ),
                        'format'    => '',
                        'current'   => $paged,
                        'total'     => $withdrawals['pages'],
                        'prev_text' => '&laquo;',
                        'next_text' => '&raquo;',
                    )
                );
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>
