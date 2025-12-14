<?php
/**
 * Dashboard Withdraw Tab.
 *
 * @package Bazaar\Templates
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="bazaar-tab-content bazaar-withdraw">
    <h2><?php esc_html_e( 'Withdrawals', 'bazaar' ); ?></h2>

    <div class="bazaar-withdraw-header">
        <div class="balance-box">
            <span class="balance-label"><?php esc_html_e( 'Available Balance', 'bazaar' ); ?></span>
            <span class="balance-value"><?php echo wc_price( $balance ); ?></span>
        </div>

        <?php if ( $balance >= $min_amount && ! $has_pending ) : ?>
            <button type="button" class="button button-primary" id="request-withdrawal-btn">
                <?php esc_html_e( 'Request Withdrawal', 'bazaar' ); ?>
            </button>
        <?php elseif ( $has_pending ) : ?>
            <p class="notice"><?php esc_html_e( 'You have a pending withdrawal request.', 'bazaar' ); ?></p>
        <?php else : ?>
            <p class="notice">
                <?php
                /* translators: %s: minimum withdrawal amount */
                printf( esc_html__( 'Minimum withdrawal amount is %s.', 'bazaar' ), wc_price( $min_amount ) );
                ?>
            </p>
        <?php endif; ?>
    </div>

    <!-- Withdrawal Request Form -->
    <div id="withdrawal-form" class="bazaar-form" style="display: none;">
        <h3><?php esc_html_e( 'Request Withdrawal', 'bazaar' ); ?></h3>

        <div class="form-row">
            <label for="withdraw-amount"><?php esc_html_e( 'Amount', 'bazaar' ); ?></label>
            <input type="number" id="withdraw-amount" name="amount" min="<?php echo esc_attr( $min_amount ); ?>" max="<?php echo esc_attr( $balance ); ?>" step="0.01" value="<?php echo esc_attr( $balance ); ?>" />
            <span class="description">
                <?php
                /* translators: %s: maximum amount */
                printf( esc_html__( 'Maximum: %s', 'bazaar' ), wc_price( $balance ) );
                ?>
            </span>
        </div>

        <div class="form-row">
            <label for="withdraw-method"><?php esc_html_e( 'Payment Method', 'bazaar' ); ?></label>
            <select id="withdraw-method" name="method">
                <?php foreach ( $methods as $method_id => $method ) : ?>
                    <option value="<?php echo esc_attr( $method_id ); ?>"><?php echo esc_html( $method['title'] ); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <label for="withdraw-note"><?php esc_html_e( 'Note (optional)', 'bazaar' ); ?></label>
            <textarea id="withdraw-note" name="note" rows="3"></textarea>
        </div>

        <div class="form-actions">
            <button type="button" class="button button-primary" id="submit-withdrawal">
                <?php esc_html_e( 'Submit Request', 'bazaar' ); ?>
            </button>
            <button type="button" class="button" id="cancel-withdrawal">
                <?php esc_html_e( 'Cancel', 'bazaar' ); ?>
            </button>
        </div>
    </div>

    <!-- Withdrawal History -->
    <div class="bazaar-withdrawal-history">
        <h3><?php esc_html_e( 'Withdrawal History', 'bazaar' ); ?></h3>

        <?php if ( empty( $withdrawals['withdrawals'] ) ) : ?>
            <p class="no-data"><?php esc_html_e( 'No withdrawal history.', 'bazaar' ); ?></p>
        <?php else : ?>
            <table class="bazaar-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'ID', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Amount', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Method', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Date', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Actions', 'bazaar' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $withdrawals['withdrawals'] as $withdrawal ) : ?>
                        <tr>
                            <td>#<?php echo esc_html( $withdrawal->id ); ?></td>
                            <td><?php echo wc_price( $withdrawal->amount ); ?></td>
                            <td><?php echo esc_html( ucwords( str_replace( '_', ' ', $withdrawal->method ) ) ); ?></td>
                            <td><?php bazaar_withdrawal_status_badge( $withdrawal->status ); ?></td>
                            <td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $withdrawal->created_at ) ) ); ?></td>
                            <td>
                                <?php if ( 'pending' === $withdrawal->status ) : ?>
                                    <button type="button" class="button button-small cancel-withdrawal-btn" data-id="<?php echo esc_attr( $withdrawal->id ); ?>">
                                        <?php esc_html_e( 'Cancel', 'bazaar' ); ?>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Payment Method Settings -->
    <div class="bazaar-payment-methods">
        <h3><?php esc_html_e( 'Payment Method Details', 'bazaar' ); ?></h3>
        <p class="description"><?php esc_html_e( 'Configure your payment methods to receive withdrawals.', 'bazaar' ); ?></p>

        <div class="method-tabs">
            <?php foreach ( $methods as $method_id => $method ) : ?>
                <div class="method-tab">
                    <h4><?php echo esc_html( $method['title'] ); ?></h4>
                    <form class="payment-method-form" data-method="<?php echo esc_attr( $method_id ); ?>">
                        <?php foreach ( $method['fields'] as $field_id => $field ) : ?>
                            <div class="form-row">
                                <label for="<?php echo esc_attr( $method_id . '_' . $field_id ); ?>">
                                    <?php echo esc_html( $field['label'] ); ?>
                                    <?php if ( ! empty( $field['required'] ) ) : ?>
                                        <span class="required">*</span>
                                    <?php endif; ?>
                                </label>
                                <input type="<?php echo esc_attr( $field['type'] ); ?>"
                                       id="<?php echo esc_attr( $method_id . '_' . $field_id ); ?>"
                                       name="<?php echo esc_attr( $field_id ); ?>"
                                       value="<?php echo esc_attr( $method['details'][ $field_id ] ?? '' ); ?>"
                                       <?php echo ! empty( $field['required'] ) ? 'required' : ''; ?> />
                            </div>
                        <?php endforeach; ?>
                        <button type="submit" class="button"><?php esc_html_e( 'Save', 'bazaar' ); ?></button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
