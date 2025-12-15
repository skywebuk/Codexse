<?php
/**
 * Admin Earnings Page Template.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
$earnings_table = $wpdb->prefix . 'edugo_earnings';

// Pagination.
$per_page = 20;
$current_page = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
$offset = ( $current_page - 1 ) * $per_page;

// Get summary stats.
$total_earnings = (float) $wpdb->get_var( "SELECT SUM(order_total) FROM {$earnings_table}" );
$total_instructor_earnings = (float) $wpdb->get_var( "SELECT SUM(commission_amount) FROM {$earnings_table}" );
$total_admin_earnings = (float) $wpdb->get_var( "SELECT SUM(admin_amount) FROM {$earnings_table}" );

// Get earnings.
$total = $wpdb->get_var( "SELECT COUNT(*) FROM {$earnings_table}" );
$earnings = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$earnings_table} ORDER BY created_at DESC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    )
);

$total_pages = ceil( $total / $per_page );
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Earnings', 'edugo-lms' ); ?></h1>
    <hr class="wp-header-end">

    <div class="edugo-admin-stats">
        <div class="edugo-stat-box">
            <span class="edugo-stat-label"><?php esc_html_e( 'Total Sales', 'edugo-lms' ); ?></span>
            <span class="edugo-stat-value"><?php echo esc_html( Edugo_LMS\Helpers\Helper::format_price( $total_earnings ) ); ?></span>
        </div>
        <div class="edugo-stat-box">
            <span class="edugo-stat-label"><?php esc_html_e( 'Instructor Earnings', 'edugo-lms' ); ?></span>
            <span class="edugo-stat-value"><?php echo esc_html( Edugo_LMS\Helpers\Helper::format_price( $total_instructor_earnings ) ); ?></span>
        </div>
        <div class="edugo-stat-box">
            <span class="edugo-stat-label"><?php esc_html_e( 'Platform Earnings', 'edugo-lms' ); ?></span>
            <span class="edugo-stat-value"><?php echo esc_html( Edugo_LMS\Helpers\Helper::format_price( $total_admin_earnings ) ); ?></span>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Date', 'edugo-lms' ); ?></th>
                <th><?php esc_html_e( 'Instructor', 'edugo-lms' ); ?></th>
                <th><?php esc_html_e( 'Course', 'edugo-lms' ); ?></th>
                <th><?php esc_html_e( 'Order Total', 'edugo-lms' ); ?></th>
                <th><?php esc_html_e( 'Commission', 'edugo-lms' ); ?></th>
                <th><?php esc_html_e( 'Instructor', 'edugo-lms' ); ?></th>
                <th><?php esc_html_e( 'Platform', 'edugo-lms' ); ?></th>
                <th><?php esc_html_e( 'Status', 'edugo-lms' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ( ! empty( $earnings ) ) : ?>
                <?php foreach ( $earnings as $earning ) :
                    $instructor = get_userdata( $earning->instructor_id );
                    $course = get_post( $earning->course_id );
                    ?>
                    <tr>
                        <td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $earning->created_at ) ) ); ?></td>
                        <td>
                            <?php if ( $instructor ) : ?>
                                <?php echo esc_html( $instructor->display_name ); ?>
                            <?php else : ?>
                                <em><?php esc_html_e( 'Deleted', 'edugo-lms' ); ?></em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ( $course ) : ?>
                                <?php echo esc_html( $course->post_title ); ?>
                            <?php else : ?>
                                <em><?php esc_html_e( 'Deleted', 'edugo-lms' ); ?></em>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html( Edugo_LMS\Helpers\Helper::format_price( (float) $earning->order_total ) ); ?></td>
                        <td><?php echo esc_html( $earning->commission_rate ); ?>%</td>
                        <td><?php echo esc_html( Edugo_LMS\Helpers\Helper::format_price( (float) $earning->commission_amount ) ); ?></td>
                        <td><?php echo esc_html( Edugo_LMS\Helpers\Helper::format_price( (float) $earning->admin_amount ) ); ?></td>
                        <td>
                            <span class="edugo-status edugo-status-<?php echo esc_attr( $earning->status ); ?>">
                                <?php echo esc_html( ucfirst( $earning->status ) ); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="8"><?php esc_html_e( 'No earnings found.', 'edugo-lms' ); ?></td>
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
