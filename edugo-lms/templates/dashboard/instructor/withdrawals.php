<?php
/**
 * Instructor Dashboard - Withdrawals Tab.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$instructor_id = get_current_user_id();

global $wpdb;
$withdrawals_table = $wpdb->prefix . 'edugo_withdrawals';
$earnings_table = $wpdb->prefix . 'edugo_earnings';

// Get available balance.
$available_balance = (float) $wpdb->get_var(
    $wpdb->prepare(
        "SELECT SUM(commission_amount) FROM {$earnings_table} WHERE instructor_id = %d AND status = 'pending'",
        $instructor_id
    )
);

$minimum_withdrawal = (float) get_option( 'edugo_minimum_withdrawal', 50 );

// Handle withdrawal request.
$message = '';
$message_type = '';

if ( isset( $_POST['edugo_request_withdrawal'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'edugo_request_withdrawal' ) ) {
    $amount = (float) ( $_POST['withdrawal_amount'] ?? 0 );
    $payment_method = sanitize_text_field( wp_unslash( $_POST['payment_method'] ?? '' ) );
    $payment_details = sanitize_textarea_field( wp_unslash( $_POST['payment_details'] ?? '' ) );

    if ( $amount < $minimum_withdrawal ) {
        $message = sprintf(
            /* translators: %s: Minimum amount */
            __( 'Minimum withdrawal amount is %s.', 'edugo-lms' ),
            Edugo_LMS\Helpers\Helper::format_price( $minimum_withdrawal )
        );
        $message_type = 'error';
    } elseif ( $amount > $available_balance ) {
        $message = __( 'Insufficient balance.', 'edugo-lms' );
        $message_type = 'error';
    } elseif ( empty( $payment_method ) || empty( $payment_details ) ) {
        $message = __( 'Please provide payment method and details.', 'edugo-lms' );
        $message_type = 'error';
    } else {
        $wpdb->insert(
            $withdrawals_table,
            array(
                'instructor_id'   => $instructor_id,
                'amount'          => $amount,
                'payment_method'  => $payment_method,
                'payment_details' => $payment_details,
                'status'          => 'pending',
                'requested_at'    => current_time( 'mysql' ),
            ),
            array( '%d', '%f', '%s', '%s', '%s', '%s' )
        );

        $message = __( 'Withdrawal request submitted successfully.', 'edugo-lms' );
        $message_type = 'success';

        // Refresh balance.
        $available_balance = (float) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT SUM(commission_amount) FROM {$earnings_table} WHERE instructor_id = %d AND status = 'pending'",
                $instructor_id
            )
        );
    }
}

// Get withdrawal history.
$withdrawals = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$withdrawals_table} WHERE instructor_id = %d ORDER BY requested_at DESC",
        $instructor_id
    )
);
?>

<div class="edugo-dashboard-header">
    <h2><?php esc_html_e( 'Withdrawals', 'edugo-lms' ); ?></h2>
</div>

<?php if ( $message ) : ?>
    <div class="edugo-notice edugo-notice-<?php echo esc_attr( $message_type ); ?>">
        <p><?php echo esc_html( $message ); ?></p>
    </div>
<?php endif; ?>

<div class="edugo-withdrawal-section">
    <div class="edugo-withdrawal-balance">
        <h3><?php esc_html_e( 'Available Balance', 'edugo-lms' ); ?></h3>
        <span class="edugo-balance-amount"><?php echo esc_html( Edugo_LMS\Helpers\Helper::format_price( $available_balance ) ); ?></span>
        <p class="edugo-balance-note">
            <?php
            printf(
                /* translators: %s: Minimum withdrawal amount */
                esc_html__( 'Minimum withdrawal: %s', 'edugo-lms' ),
                esc_html( Edugo_LMS\Helpers\Helper::format_price( $minimum_withdrawal ) )
            );
            ?>
        </p>
    </div>

    <?php if ( $available_balance >= $minimum_withdrawal ) : ?>
        <div class="edugo-withdrawal-form-wrapper">
            <h3><?php esc_html_e( 'Request Withdrawal', 'edugo-lms' ); ?></h3>

            <form method="post" class="edugo-withdrawal-form">
                <?php wp_nonce_field( 'edugo_request_withdrawal' ); ?>

                <div class="edugo-form-group">
                    <label for="withdrawal_amount"><?php esc_html_e( 'Amount', 'edugo-lms' ); ?></label>
                    <input type="number" id="withdrawal_amount" name="withdrawal_amount" min="<?php echo esc_attr( $minimum_withdrawal ); ?>" max="<?php echo esc_attr( $available_balance ); ?>" step="0.01" value="<?php echo esc_attr( $available_balance ); ?>" class="edugo-input" required>
                </div>

                <div class="edugo-form-group">
                    <label for="payment_method"><?php esc_html_e( 'Payment Method', 'edugo-lms' ); ?></label>
                    <select id="payment_method" name="payment_method" class="edugo-select" required>
                        <option value=""><?php esc_html_e( 'Select payment method', 'edugo-lms' ); ?></option>
                        <option value="paypal"><?php esc_html_e( 'PayPal', 'edugo-lms' ); ?></option>
                        <option value="bank_transfer"><?php esc_html_e( 'Bank Transfer', 'edugo-lms' ); ?></option>
                    </select>
                </div>

                <div class="edugo-form-group">
                    <label for="payment_details"><?php esc_html_e( 'Payment Details', 'edugo-lms' ); ?></label>
                    <textarea id="payment_details" name="payment_details" rows="3" class="edugo-textarea" placeholder="<?php esc_attr_e( 'Enter your PayPal email or bank account details', 'edugo-lms' ); ?>" required></textarea>
                </div>

                <button type="submit" name="edugo_request_withdrawal" class="edugo-button edugo-button-primary">
                    <?php esc_html_e( 'Submit Request', 'edugo-lms' ); ?>
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>

<div class="edugo-withdrawals-history">
    <h3><?php esc_html_e( 'Withdrawal History', 'edugo-lms' ); ?></h3>

    <?php if ( ! empty( $withdrawals ) ) : ?>
        <table class="edugo-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Date', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Amount', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Method', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'edugo-lms' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $withdrawals as $withdrawal ) : ?>
                    <tr>
                        <td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $withdrawal->requested_at ) ) ); ?></td>
                        <td><strong><?php echo esc_html( Edugo_LMS\Helpers\Helper::format_price( (float) $withdrawal->amount ) ); ?></strong></td>
                        <td><?php echo esc_html( ucfirst( str_replace( '_', ' ', $withdrawal->payment_method ) ) ); ?></td>
                        <td>
                            <?php
                            $status_labels = array(
                                'pending'   => __( 'Pending', 'edugo-lms' ),
                                'approved'  => __( 'Approved', 'edugo-lms' ),
                                'completed' => __( 'Completed', 'edugo-lms' ),
                                'rejected'  => __( 'Rejected', 'edugo-lms' ),
                            );
                            $status_classes = array(
                                'pending'   => 'warning',
                                'approved'  => 'info',
                                'completed' => 'success',
                                'rejected'  => 'danger',
                            );
                            ?>
                            <span class="edugo-badge edugo-badge-<?php echo esc_attr( $status_classes[ $withdrawal->status ] ?? 'secondary' ); ?>">
                                <?php echo esc_html( $status_labels[ $withdrawal->status ] ?? $withdrawal->status ); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="edugo-empty-state edugo-empty-state-small">
            <p><?php esc_html_e( 'No withdrawal requests yet.', 'edugo-lms' ); ?></p>
        </div>
    <?php endif; ?>
</div>
