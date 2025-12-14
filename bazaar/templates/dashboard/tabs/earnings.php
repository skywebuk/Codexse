<?php
/**
 * Dashboard Earnings Tab.
 *
 * @package Bazaar\Templates
 */

defined( 'ABSPATH' ) || exit;

$ranges = array(
    'today'      => __( 'Today', 'bazaar' ),
    'this_week'  => __( 'This Week', 'bazaar' ),
    'this_month' => __( 'This Month', 'bazaar' ),
    'last_month' => __( 'Last Month', 'bazaar' ),
    'this_year'  => __( 'This Year', 'bazaar' ),
    'all_time'   => __( 'All Time', 'bazaar' ),
);
?>
<div class="bazaar-tab-content bazaar-earnings">
    <h2><?php esc_html_e( 'Earnings', 'bazaar' ); ?></h2>

    <div class="bazaar-filters">
        <form method="get" class="range-filter">
            <input type="hidden" name="tab" value="earnings" />
            <select name="range" onchange="this.form.submit()">
                <?php foreach ( $ranges as $range_key => $range_label ) : ?>
                    <option value="<?php echo esc_attr( $range_key ); ?>" <?php selected( $current_range, $range_key ); ?>>
                        <?php echo esc_html( $range_label ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="bazaar-earnings-summary">
        <div class="summary-box">
            <span class="summary-value"><?php echo wc_price( $earnings['gross_sales'] ); ?></span>
            <span class="summary-label"><?php esc_html_e( 'Gross Sales', 'bazaar' ); ?></span>
        </div>
        <div class="summary-box">
            <span class="summary-value"><?php echo wc_price( $earnings['total_commission'] ); ?></span>
            <span class="summary-label"><?php esc_html_e( 'Admin Commission', 'bazaar' ); ?></span>
        </div>
        <div class="summary-box highlight">
            <span class="summary-value"><?php echo wc_price( $earnings['net_earnings'] ); ?></span>
            <span class="summary-label"><?php esc_html_e( 'Net Earnings', 'bazaar' ); ?></span>
        </div>
    </div>

    <!-- Transactions History -->
    <div class="bazaar-transactions">
        <h3><?php esc_html_e( 'Transaction History', 'bazaar' ); ?></h3>

        <?php if ( empty( $transactions['transactions'] ) ) : ?>
            <p class="no-data"><?php esc_html_e( 'No transactions found.', 'bazaar' ); ?></p>
        <?php else : ?>
            <table class="bazaar-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Date', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Type', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Order', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Amount', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Commission', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Net Amount', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Note', 'bazaar' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $transactions['transactions'] as $transaction ) : ?>
                        <tr class="transaction-<?php echo esc_attr( $transaction->trn_type ); ?>">
                            <td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $transaction->created_at ) ) ); ?></td>
                            <td>
                                <span class="transaction-type <?php echo esc_attr( $transaction->trn_type ); ?>">
                                    <?php echo 'credit' === $transaction->trn_type ? esc_html__( 'Credit', 'bazaar' ) : esc_html__( 'Debit', 'bazaar' ); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ( $transaction->order_id ) : ?>
                                    #<?php echo esc_html( $transaction->order_id ); ?>
                                <?php else : ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?php echo wc_price( $transaction->amount ); ?></td>
                            <td><?php echo wc_price( $transaction->commission ); ?></td>
                            <td>
                                <strong>
                                    <?php echo 'credit' === $transaction->trn_type ? '+' : '-'; ?>
                                    <?php echo wc_price( $transaction->net_amount ); ?>
                                </strong>
                            </td>
                            <td><?php echo esc_html( $transaction->note ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ( $transactions['pages'] > 1 ) : ?>
                <?php bazaar_pagination( $transactions['pages'], isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1 ); ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
