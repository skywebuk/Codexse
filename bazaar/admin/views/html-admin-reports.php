<?php
/**
 * Admin Reports View.
 *
 * @package Bazaar\Admin\Views
 */

defined( 'ABSPATH' ) || exit;

$report_types = Bazaar_Admin_Reports::get_report_types();
?>
<div class="wrap bazaar-admin-wrap bazaar-reports">
    <h1><?php esc_html_e( 'Reports', 'bazaar' ); ?></h1>

    <div class="bazaar-report-filters">
        <form method="get" class="report-filter-form">
            <input type="hidden" name="page" value="bazaar-reports" />

            <select name="report">
                <?php foreach ( $report_types as $type => $label ) : ?>
                    <option value="<?php echo esc_attr( $type ); ?>" <?php selected( $report_type, $type ); ?>>
                        <?php echo esc_html( $label ); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="range">
                <option value="today" <?php selected( $range, 'today' ); ?>><?php esc_html_e( 'Today', 'bazaar' ); ?></option>
                <option value="yesterday" <?php selected( $range, 'yesterday' ); ?>><?php esc_html_e( 'Yesterday', 'bazaar' ); ?></option>
                <option value="this_week" <?php selected( $range, 'this_week' ); ?>><?php esc_html_e( 'This Week', 'bazaar' ); ?></option>
                <option value="last_week" <?php selected( $range, 'last_week' ); ?>><?php esc_html_e( 'Last Week', 'bazaar' ); ?></option>
                <option value="this_month" <?php selected( $range, 'this_month' ); ?>><?php esc_html_e( 'This Month', 'bazaar' ); ?></option>
                <option value="last_month" <?php selected( $range, 'last_month' ); ?>><?php esc_html_e( 'Last Month', 'bazaar' ); ?></option>
                <option value="this_year" <?php selected( $range, 'this_year' ); ?>><?php esc_html_e( 'This Year', 'bazaar' ); ?></option>
                <option value="last_year" <?php selected( $range, 'last_year' ); ?>><?php esc_html_e( 'Last Year', 'bazaar' ); ?></option>
                <option value="all_time" <?php selected( $range, 'all_time' ); ?>><?php esc_html_e( 'All Time', 'bazaar' ); ?></option>
            </select>

            <button type="submit" class="button"><?php esc_html_e( 'Filter', 'bazaar' ); ?></button>
        </form>
    </div>

    <?php if ( 'overview' === $report_type ) : ?>
        <div class="bazaar-report-summary">
            <div class="summary-box">
                <div class="summary-value"><?php echo wc_price( $report_data['summary']['gross_sales'] ); ?></div>
                <div class="summary-label"><?php esc_html_e( 'Gross Sales', 'bazaar' ); ?></div>
            </div>
            <div class="summary-box">
                <div class="summary-value"><?php echo wc_price( $report_data['summary']['admin_commission'] ); ?></div>
                <div class="summary-label"><?php esc_html_e( 'Admin Commission', 'bazaar' ); ?></div>
            </div>
            <div class="summary-box">
                <div class="summary-value"><?php echo wc_price( $report_data['summary']['vendor_earnings'] ); ?></div>
                <div class="summary-label"><?php esc_html_e( 'Vendor Earnings', 'bazaar' ); ?></div>
            </div>
            <div class="summary-box">
                <div class="summary-value"><?php echo esc_html( $report_data['summary']['total_orders'] ); ?></div>
                <div class="summary-label"><?php esc_html_e( 'Total Orders', 'bazaar' ); ?></div>
            </div>
        </div>

        <div class="bazaar-report-chart">
            <h3><?php esc_html_e( 'Sales Overview', 'bazaar' ); ?></h3>
            <canvas id="bazaar-sales-chart" height="300"></canvas>
        </div>

        <script>
        jQuery(document).ready(function($) {
            var ctx = document.getElementById('bazaar-sales-chart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode( $report_data['chart']['labels'] ); ?>,
                    datasets: [
                        {
                            label: '<?php esc_html_e( 'Gross Sales', 'bazaar' ); ?>',
                            data: <?php echo json_encode( $report_data['chart']['gross'] ); ?>,
                            borderColor: '#4CAF50',
                            backgroundColor: 'rgba(76, 175, 80, 0.1)',
                            fill: true
                        },
                        {
                            label: '<?php esc_html_e( 'Admin Commission', 'bazaar' ); ?>',
                            data: <?php echo json_encode( $report_data['chart']['commission'] ); ?>,
                            borderColor: '#2196F3',
                            backgroundColor: 'rgba(33, 150, 243, 0.1)',
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
        </script>

        <?php if ( ! empty( $report_data['top_vendors'] ) ) : ?>
            <div class="bazaar-report-table">
                <h3><?php esc_html_e( 'Top Vendors', 'bazaar' ); ?></h3>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Vendor', 'bazaar' ); ?></th>
                            <th><?php esc_html_e( 'Sales', 'bazaar' ); ?></th>
                            <th><?php esc_html_e( 'Earnings', 'bazaar' ); ?></th>
                            <th><?php esc_html_e( 'Orders', 'bazaar' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $report_data['top_vendors'] as $vendor_data ) : ?>
                            <?php $vendor = Bazaar_Vendor::get_vendor( $vendor_data->vendor_id ); ?>
                            <tr>
                                <td>
                                    <?php if ( $vendor ) : ?>
                                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors&action=view&vendor=' . $vendor_data->vendor_id ) ); ?>">
                                            <?php echo esc_html( $vendor['store_name'] ); ?>
                                        </a>
                                    <?php else : ?>
                                        <?php esc_html_e( 'Unknown', 'bazaar' ); ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo wc_price( $vendor_data->sales ); ?></td>
                                <td><?php echo wc_price( $vendor_data->earnings ); ?></td>
                                <td><?php echo esc_html( $vendor_data->orders ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    <?php elseif ( 'vendors' === $report_type ) : ?>
        <div class="bazaar-report-summary">
            <div class="summary-box">
                <div class="summary-value"><?php echo esc_html( $report_data['total'] ); ?></div>
                <div class="summary-label"><?php esc_html_e( 'Total Vendors', 'bazaar' ); ?></div>
            </div>
            <div class="summary-box">
                <div class="summary-value"><?php echo esc_html( $report_data['approved'] ); ?></div>
                <div class="summary-label"><?php esc_html_e( 'Approved', 'bazaar' ); ?></div>
            </div>
            <div class="summary-box">
                <div class="summary-value"><?php echo esc_html( $report_data['pending'] ); ?></div>
                <div class="summary-label"><?php esc_html_e( 'Pending', 'bazaar' ); ?></div>
            </div>
        </div>

        <div class="bazaar-report-table">
            <h3><?php esc_html_e( 'Vendor Performance', 'bazaar' ); ?></h3>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Vendor', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Email', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Sales', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Earnings', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Orders', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Rating', 'bazaar' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $report_data['vendors'] as $vendor ) : ?>
                        <tr>
                            <td>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors&action=view&vendor=' . $vendor['id'] ) ); ?>">
                                    <?php echo esc_html( $vendor['name'] ); ?>
                                </a>
                            </td>
                            <td><?php echo esc_html( $vendor['email'] ); ?></td>
                            <td><?php echo wc_price( $vendor['sales'] ); ?></td>
                            <td><?php echo wc_price( $vendor['earnings'] ); ?></td>
                            <td><?php echo esc_html( $vendor['orders'] ); ?></td>
                            <td>
                                <?php if ( $vendor['rating']['count'] > 0 ) : ?>
                                    <?php echo esc_html( $vendor['rating']['average'] ); ?>/5
                                <?php else : ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ( 'commission' === $report_type ) : ?>
        <div class="bazaar-report-summary">
            <div class="summary-box">
                <div class="summary-value"><?php echo wc_price( $report_data['totals']['gross_sales'] ); ?></div>
                <div class="summary-label"><?php esc_html_e( 'Gross Sales', 'bazaar' ); ?></div>
            </div>
            <div class="summary-box">
                <div class="summary-value"><?php echo wc_price( $report_data['totals']['admin_commission'] ); ?></div>
                <div class="summary-label"><?php esc_html_e( 'Total Commission', 'bazaar' ); ?></div>
            </div>
            <div class="summary-box">
                <div class="summary-value"><?php echo wc_price( $report_data['totals']['vendor_earnings'] ); ?></div>
                <div class="summary-label"><?php esc_html_e( 'Vendor Earnings', 'bazaar' ); ?></div>
            </div>
        </div>

        <div class="bazaar-report-table">
            <h3><?php esc_html_e( 'Commission by Vendor', 'bazaar' ); ?></h3>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Vendor', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Gross Sales', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Admin Commission', 'bazaar' ); ?></th>
                        <th><?php esc_html_e( 'Vendor Earning', 'bazaar' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $report_data['by_vendor'] as $row ) : ?>
                        <tr>
                            <td>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors&action=view&vendor=' . $row['vendor_id'] ) ); ?>">
                                    <?php echo esc_html( $row['vendor_name'] ); ?>
                                </a>
                            </td>
                            <td><?php echo wc_price( $row['gross_sales'] ); ?></td>
                            <td><?php echo wc_price( $row['admin_commission'] ); ?></td>
                            <td><?php echo wc_price( $row['vendor_earning'] ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
