<?php
/**
 * Admin Enrollments Page Template.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
$enrollments_table = $wpdb->prefix . 'edugo_enrollments';

// Pagination.
$per_page = 20;
$current_page = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
$offset = ( $current_page - 1 ) * $per_page;

// Filters.
$status_filter = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
$course_filter = isset( $_GET['course_id'] ) ? absint( $_GET['course_id'] ) : 0;

// Build query.
$where = array( '1=1' );
$params = array();

if ( $status_filter ) {
    $where[] = 'e.status = %s';
    $params[] = $status_filter;
}

if ( $course_filter ) {
    $where[] = 'e.course_id = %d';
    $params[] = $course_filter;
}

$where_clause = implode( ' AND ', $where );

// Get total count.
$total_sql = "SELECT COUNT(*) FROM {$enrollments_table} e WHERE {$where_clause}";
$total = $wpdb->get_var( empty( $params ) ? $total_sql : $wpdb->prepare( $total_sql, ...$params ) );

// Get enrollments.
$sql = "SELECT e.* FROM {$enrollments_table} e WHERE {$where_clause} ORDER BY e.enrolled_at DESC LIMIT %d OFFSET %d";
$params[] = $per_page;
$params[] = $offset;

$enrollments = $wpdb->get_results( $wpdb->prepare( $sql, ...$params ) );

$total_pages = ceil( $total / $per_page );
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Enrollments', 'edugo-lms' ); ?></h1>
    <hr class="wp-header-end">

    <div class="edugo-admin-filters">
        <form method="get">
            <input type="hidden" name="page" value="edugo-enrollments">

            <select name="status">
                <option value=""><?php esc_html_e( 'All Statuses', 'edugo-lms' ); ?></option>
                <option value="enrolled" <?php selected( $status_filter, 'enrolled' ); ?>><?php esc_html_e( 'Enrolled', 'edugo-lms' ); ?></option>
                <option value="completed" <?php selected( $status_filter, 'completed' ); ?>><?php esc_html_e( 'Completed', 'edugo-lms' ); ?></option>
            </select>

            <select name="course_id">
                <option value=""><?php esc_html_e( 'All Courses', 'edugo-lms' ); ?></option>
                <?php
                $courses = get_posts( array(
                    'post_type'      => 'edugo_course',
                    'posts_per_page' => -1,
                    'post_status'    => 'publish',
                    'orderby'        => 'title',
                    'order'          => 'ASC',
                ) );
                foreach ( $courses as $course ) :
                    ?>
                    <option value="<?php echo esc_attr( $course->ID ); ?>" <?php selected( $course_filter, $course->ID ); ?>>
                        <?php echo esc_html( $course->post_title ); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="button"><?php esc_html_e( 'Filter', 'edugo-lms' ); ?></button>
        </form>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Student', 'edugo-lms' ); ?></th>
                <th><?php esc_html_e( 'Course', 'edugo-lms' ); ?></th>
                <th><?php esc_html_e( 'Enrolled', 'edugo-lms' ); ?></th>
                <th><?php esc_html_e( 'Status', 'edugo-lms' ); ?></th>
                <th><?php esc_html_e( 'Actions', 'edugo-lms' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ( ! empty( $enrollments ) ) : ?>
                <?php foreach ( $enrollments as $enrollment ) :
                    $user = get_userdata( $enrollment->user_id );
                    $course = get_post( $enrollment->course_id );
                    ?>
                    <tr>
                        <td>
                            <?php if ( $user ) : ?>
                                <?php echo get_avatar( $user->ID, 32 ); ?>
                                <strong><?php echo esc_html( $user->display_name ); ?></strong>
                                <br><small><?php echo esc_html( $user->user_email ); ?></small>
                            <?php else : ?>
                                <em><?php esc_html_e( 'User deleted', 'edugo-lms' ); ?></em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ( $course ) : ?>
                                <a href="<?php echo esc_url( get_edit_post_link( $course ) ); ?>">
                                    <?php echo esc_html( $course->post_title ); ?>
                                </a>
                            <?php else : ?>
                                <em><?php esc_html_e( 'Course deleted', 'edugo-lms' ); ?></em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $enrollment->enrolled_at ) ) ); ?>
                        </td>
                        <td>
                            <span class="edugo-status edugo-status-<?php echo esc_attr( $enrollment->status ); ?>">
                                <?php echo esc_html( ucfirst( $enrollment->status ) ); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ( $user ) : ?>
                                <a href="<?php echo esc_url( get_edit_user_link( $user->ID ) ); ?>" class="button button-small">
                                    <?php esc_html_e( 'View User', 'edugo-lms' ); ?>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5"><?php esc_html_e( 'No enrollments found.', 'edugo-lms' ); ?></td>
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
