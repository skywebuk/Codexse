<?php
/**
 * Subscribers admin page template
 *
 * @package MailHive
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$subscriber_model = new MailHive_Subscriber();

// Pagination
$per_page = 20;
$current_page = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
$status_filter = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';

$args = array(
    'per_page' => $per_page,
    'page'     => $current_page,
    'search'   => $search,
    'status'   => $status_filter,
);

$subscribers = $subscriber_model->get_all( $args );
$total_items = $subscriber_model->get_total_count( $args );
$total_pages = ceil( $total_items / $per_page );

// Export URL
$export_url = wp_nonce_url(
    add_query_arg( 'action', 'mailhive_export_csv', admin_url( 'admin.php' ) ),
    'mailhive_export_csv'
);
?>

<div class="wrap mailhive-wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Subscribers', 'mailhive' ); ?></h1>
    <a href="<?php echo esc_url( $export_url ); ?>" class="page-title-action">
        <?php esc_html_e( 'Export CSV', 'mailhive' ); ?>
    </a>
    <hr class="wp-header-end">

    <div class="mailhive-stats">
        <div class="mailhive-stat-box">
            <span class="mailhive-stat-number"><?php echo esc_html( $total_items ); ?></span>
            <span class="mailhive-stat-label"><?php esc_html_e( 'Total Subscribers', 'mailhive' ); ?></span>
        </div>
    </div>

    <form method="get" class="mailhive-filter-form">
        <input type="hidden" name="page" value="mailhive">

        <div class="mailhive-filters">
            <div class="mailhive-search-box">
                <input type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Search by email or name...', 'mailhive' ); ?>">
                <button type="submit" class="button"><?php esc_html_e( 'Search', 'mailhive' ); ?></button>
            </div>

            <select name="status" onchange="this.form.submit()">
                <option value=""><?php esc_html_e( 'All Statuses', 'mailhive' ); ?></option>
                <option value="subscribed" <?php selected( $status_filter, 'subscribed' ); ?>><?php esc_html_e( 'Subscribed', 'mailhive' ); ?></option>
                <option value="unsubscribed" <?php selected( $status_filter, 'unsubscribed' ); ?>><?php esc_html_e( 'Unsubscribed', 'mailhive' ); ?></option>
            </select>
        </div>
    </form>

    <form id="mailhive-subscribers-form" method="post">
        <input type="hidden" name="mailhive_bulk_nonce" value="<?php echo esc_attr( wp_create_nonce( 'mailhive_bulk_action' ) ); ?>">

        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <select name="bulk_action" id="bulk-action-selector">
                    <option value=""><?php esc_html_e( 'Bulk Actions', 'mailhive' ); ?></option>
                    <option value="delete"><?php esc_html_e( 'Delete', 'mailhive' ); ?></option>
                </select>
                <button type="button" id="mailhive-bulk-apply" class="button action"><?php esc_html_e( 'Apply', 'mailhive' ); ?></button>
            </div>

            <?php if ( $total_pages > 1 ) : ?>
            <div class="tablenav-pages">
                <span class="displaying-num">
                    <?php
                    printf(
                        /* translators: %s: number of items */
                        esc_html( _n( '%s item', '%s items', $total_items, 'mailhive' ) ),
                        esc_html( number_format_i18n( $total_items ) )
                    );
                    ?>
                </span>
                <span class="pagination-links">
                    <?php
                    echo wp_kses_post( paginate_links( array(
                        'base'      => add_query_arg( 'paged', '%#%' ),
                        'format'    => '',
                        'prev_text' => '&laquo;',
                        'next_text' => '&raquo;',
                        'total'     => $total_pages,
                        'current'   => $current_page,
                    ) ) );
                    ?>
                </span>
            </div>
            <?php endif; ?>
        </div>

        <table class="wp-list-table widefat fixed striped mailhive-table">
            <thead>
                <tr>
                    <td class="manage-column column-cb check-column">
                        <input type="checkbox" id="cb-select-all">
                    </td>
                    <th class="manage-column column-email"><?php esc_html_e( 'Email', 'mailhive' ); ?></th>
                    <th class="manage-column column-name"><?php esc_html_e( 'Name', 'mailhive' ); ?></th>
                    <th class="manage-column column-status"><?php esc_html_e( 'Status', 'mailhive' ); ?></th>
                    <th class="manage-column column-ip"><?php esc_html_e( 'IP Address', 'mailhive' ); ?></th>
                    <th class="manage-column column-date"><?php esc_html_e( 'Subscribed', 'mailhive' ); ?></th>
                    <th class="manage-column column-actions"><?php esc_html_e( 'Actions', 'mailhive' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $subscribers ) ) : ?>
                    <tr>
                        <td colspan="7" class="mailhive-no-items">
                            <?php esc_html_e( 'No subscribers found.', 'mailhive' ); ?>
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ( $subscribers as $sub ) : ?>
                        <tr data-id="<?php echo esc_attr( $sub['id'] ); ?>">
                            <th scope="row" class="check-column">
                                <input type="checkbox" name="subscriber_ids[]" value="<?php echo esc_attr( $sub['id'] ); ?>">
                            </th>
                            <td class="column-email">
                                <strong><?php echo esc_html( $sub['email'] ); ?></strong>
                            </td>
                            <td class="column-name">
                                <?php echo esc_html( $sub['name'] ?: '—' ); ?>
                            </td>
                            <td class="column-status">
                                <span class="mailhive-status mailhive-status-<?php echo esc_attr( $sub['status'] ); ?>">
                                    <?php echo esc_html( ucfirst( $sub['status'] ) ); ?>
                                </span>
                            </td>
                            <td class="column-ip">
                                <?php echo esc_html( $sub['ip_address'] ?: '—' ); ?>
                            </td>
                            <td class="column-date">
                                <?php
                                $date = strtotime( $sub['created_at'] );
                                echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $date ) );
                                ?>
                            </td>
                            <td class="column-actions">
                                <button type="button" class="button button-small mailhive-delete-subscriber" data-id="<?php echo esc_attr( $sub['id'] ); ?>">
                                    <?php esc_html_e( 'Delete', 'mailhive' ); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column">
                        <input type="checkbox">
                    </td>
                    <th class="manage-column column-email"><?php esc_html_e( 'Email', 'mailhive' ); ?></th>
                    <th class="manage-column column-name"><?php esc_html_e( 'Name', 'mailhive' ); ?></th>
                    <th class="manage-column column-status"><?php esc_html_e( 'Status', 'mailhive' ); ?></th>
                    <th class="manage-column column-ip"><?php esc_html_e( 'IP Address', 'mailhive' ); ?></th>
                    <th class="manage-column column-date"><?php esc_html_e( 'Subscribed', 'mailhive' ); ?></th>
                    <th class="manage-column column-actions"><?php esc_html_e( 'Actions', 'mailhive' ); ?></th>
                </tr>
            </tfoot>
        </table>

        <?php if ( $total_pages > 1 ) : ?>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <span class="pagination-links">
                    <?php
                    echo wp_kses_post( paginate_links( array(
                        'base'      => add_query_arg( 'paged', '%#%' ),
                        'format'    => '',
                        'prev_text' => '&laquo;',
                        'next_text' => '&raquo;',
                        'total'     => $total_pages,
                        'current'   => $current_page,
                    ) ) );
                    ?>
                </span>
            </div>
        </div>
        <?php endif; ?>
    </form>
</div>
