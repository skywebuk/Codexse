<?php
/**
 * Admin Withdrawals Page Template.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
$withdrawals_table = $wpdb->prefix . 'edugo_withdrawals';
$earnings_table = $wpdb->prefix . 'edugo_earnings';

// Handle status update.
if ( isset( $_POST['edugo_update_withdrawal'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'edugo_update_withdrawal' ) ) {
    $withdrawal_id = absint( $_POST['withdrawal_id'] );
    $new_status = sanitize_text_field( wp_unslash( $_POST['new_status'] ) );

    if ( in_array( $new_status, array( 'approved', 'completed', 'rejected' ), true ) ) {
        $wpdb->update(
            $withdrawals_table,
            array(
                'status'       => $new_status,
                'processed_at' => $new_status !== 'pending' ? current_time( 'mysql' ) : null,
            ),
            array( 'id' => $withdrawal_id ),
            array( '%s', '%s' ),
            array( '%d' )
        );

        // If completed, mark related earnings as completed.
        if ( $new_status === 'completed' ) {
            $withdrawal = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$withdrawals_table} WHERE id = %d", $withdrawal_id ) );
            if ( $withdrawal ) {
                $wpdb->update(
                    $earnings_table,
                    array( 'status' => 'completed' ),
                    array( 'instructor_id' => $withdrawal->instructor_id, 'status' => 'pending' ),
                    array( '%s' ),
                    array( '%d', '%s' )
                );
            }
        }
    }
}

// Pagination.
$per_page = 20;
$current_page = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
$offset = ( $current_page - 1 ) * $per_page;

// Get withdrawals.
$total = $wpdb->get_var( "SELECT COUNT(*) FROM {$withdrawals_table}" );
$withdrawals = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$withdrawals_table} ORDER BY requested_at DESC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    )
);

$total_pages = ceil( $total / $per_page );
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Withdrawal Requests', 'edugo-lms' ); ?></h1>
    <hr class="wp-header-end">

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e( 'ID', 'edugo-lms' ); ?></th>
                <th><?php esc_html_e( 'Instructor', 'edugo-lms' ); ?></th>
                <th><?php esc_html_e( 'Amount', 'edugo-lms' ); ?></th>
                <th><?php esc_html_e( 'Payment Method', 'edugo-lms' ); ?></th>
                <th><?php esc_html_e( 'Payment Details', 'edugo-lms' ); ?></th>
                <th><?php esc_html_e( 'Requested', 'edugo-lms' ); ?></th>
                <th><?php esc_html_e( 'Status', 'edugo-lms' ); ?></th>
                <th><?php esc_html_e( 'Actions', 'edugo-lms' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ( ! empty( $withdrawals ) ) : ?>
                <?php foreach ( $withdrawals as $withdrawal ) :
                    $instructor = get_userdata( $withdrawal->instructor_id );
                    ?>
                    <tr>
                        <td>#<?php echo esc_html( $withdrawal->id ); ?></td>
                        <td>
                            <?php if ( $instructor ) : ?>
                                <?php echo esc_html( $instructor->display_name ); ?>
                                <br><small><?php echo esc_html( $instructor->user_email ); ?></small>
                            <?php else : ?>
                                <em><?php esc_html_e( 'Deleted', 'edugo-lms' ); ?></em>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo esc_html( Edugo_LMS\Helpers\Helper::format_price( (float) $withdrawal->amount ) ); ?></strong></td>
                        <td><?php echo esc_html( ucfirst( str_replace( '_', ' ', $withdrawal->payment_method ) ) ); ?></td>
                        <td><small><?php echo esc_html( $withdrawal->payment_details ); ?></small></td>
                        <td><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $withdrawal->requested_at ) ) ); ?></td>
                        <td>
                            <?php
                            $status_classes = array(
                                'pending'   => 'warning',
                                'approved'  => 'info',
                                'completed' => 'success',
                                'rejected'  => 'danger',
                            );
                            ?>
                            <span class="edugo-status edugo-status-<?php echo esc_attr( $status_classes[ $withdrawal->status ] ?? 'secondary' ); ?>">
                                <?php echo esc_html( ucfirst( $withdrawal->status ) ); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ( $withdrawal->status === 'pending' ) : ?>
                                <form method="post" style="display: inline;">
                                    <?php wp_nonce_field( 'edugo_update_withdrawal' ); ?>
                                    <input type="hidden" name="withdrawal_id" value="<?php echo esc_attr( $withdrawal->id ); ?>">
                                    <select name="new_status" onchange="this.form.submit()">
                                        <option value=""><?php esc_html_e( 'Change Status', 'edugo-lms' ); ?></option>
                                        <option value="approved"><?php esc_html_e( 'Approve', 'edugo-lms' ); ?></option>
                                        <option value="completed"><?php esc_html_e( 'Mark Completed', 'edugo-lms' ); ?></option>
                                        <option value="rejected"><?php esc_html_e( 'Reject', 'edugo-lms' ); ?></option>
                                    </select>
                                    <input type="hidden" name="edugo_update_withdrawal" value="1">
                                </form>
                            <?php elseif ( $withdrawal->status === 'approved' ) : ?>
                                <form method="post" style="display: inline;">
                                    <?php wp_nonce_field( 'edugo_update_withdrawal' ); ?>
                                    <input type="hidden" name="withdrawal_id" value="<?php echo esc_attr( $withdrawal->id ); ?>">
                                    <input type="hidden" name="new_status" value="completed">
                                    <button type="submit" name="edugo_update_withdrawal" class="button button-small button-primary">
                                        <?php esc_html_e( 'Mark Paid', 'edugo-lms' ); ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="8"><?php esc_html_e( 'No withdrawal requests found.', 'edugo-lms' ); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ( $total_pages > 1 ) : ?>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <?php
                echo paginate_links( array(
                    'base'      => add_query_arg( 'paged', '%#%' ),
                    'format'    => '',
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                    'total'     => $total_pages,
                    'current'   => $current_page,
                ) );
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>
