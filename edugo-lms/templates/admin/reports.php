<?php
/**
 * Admin Reports Page Template.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;

// Get report data.
$courses_count = wp_count_posts( 'edugo_course' )->publish ?? 0;
$lessons_count = wp_count_posts( 'edugo_lesson' )->publish ?? 0;
$quizzes_count = wp_count_posts( 'edugo_quiz' )->publish ?? 0;

$enrollments_table = $wpdb->prefix . 'edugo_enrollments';
$earnings_table = $wpdb->prefix . 'edugo_earnings';
$quiz_table = $wpdb->prefix . 'edugo_quiz_attempts';

$total_enrollments = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$enrollments_table}" );
$completed_enrollments = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$enrollments_table} WHERE status = 'completed'" );
$total_revenue = (float) $wpdb->get_var( "SELECT SUM(order_total) FROM {$earnings_table}" );
$quiz_attempts = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$quiz_table}" );
$quiz_passed = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$quiz_table} WHERE passed = 1" );

// Top courses by enrollment.
$top_courses = $wpdb->get_results(
    "SELECT course_id, COUNT(*) as enrollment_count
    FROM {$enrollments_table}
    GROUP BY course_id
    ORDER BY enrollment_count DESC
    LIMIT 10"
);

// Recent enrollments per month (last 6 months).
$enrollment_trend = $wpdb->get_results(
    "SELECT DATE_FORMAT(enrolled_at, '%Y-%m') as month, COUNT(*) as count
    FROM {$enrollments_table}
    WHERE enrolled_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY month
    ORDER BY month ASC"
);
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'LMS Reports', 'edugo-lms' ); ?></h1>
    <hr class="wp-header-end">

    <div class="edugo-admin-stats">
        <div class="edugo-stat-box">
            <span class="edugo-stat-label"><?php esc_html_e( 'Total Courses', 'edugo-lms' ); ?></span>
            <span class="edugo-stat-value"><?php echo esc_html( $courses_count ); ?></span>
        </div>
        <div class="edugo-stat-box">
            <span class="edugo-stat-label"><?php esc_html_e( 'Total Lessons', 'edugo-lms' ); ?></span>
            <span class="edugo-stat-value"><?php echo esc_html( $lessons_count ); ?></span>
        </div>
        <div class="edugo-stat-box">
            <span class="edugo-stat-label"><?php esc_html_e( 'Total Quizzes', 'edugo-lms' ); ?></span>
            <span class="edugo-stat-value"><?php echo esc_html( $quizzes_count ); ?></span>
        </div>
        <div class="edugo-stat-box">
            <span class="edugo-stat-label"><?php esc_html_e( 'Total Enrollments', 'edugo-lms' ); ?></span>
            <span class="edugo-stat-value"><?php echo esc_html( $total_enrollments ); ?></span>
        </div>
        <div class="edugo-stat-box">
            <span class="edugo-stat-label"><?php esc_html_e( 'Completion Rate', 'edugo-lms' ); ?></span>
            <span class="edugo-stat-value">
                <?php echo $total_enrollments > 0 ? esc_html( round( ( $completed_enrollments / $total_enrollments ) * 100, 1 ) ) : 0; ?>%
            </span>
        </div>
        <div class="edugo-stat-box">
            <span class="edugo-stat-label"><?php esc_html_e( 'Total Revenue', 'edugo-lms' ); ?></span>
            <span class="edugo-stat-value"><?php echo esc_html( Edugo_LMS\Helpers\Helper::format_price( $total_revenue ) ); ?></span>
        </div>
    </div>

    <div class="edugo-admin-reports-grid">
        <div class="edugo-report-section">
            <h2><?php esc_html_e( 'Top Courses by Enrollment', 'edugo-lms' ); ?></h2>
            <?php if ( ! empty( $top_courses ) ) : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Course', 'edugo-lms' ); ?></th>
                            <th><?php esc_html_e( 'Enrollments', 'edugo-lms' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $top_courses as $item ) :
                            $course = get_post( $item->course_id );
                            if ( ! $course ) continue;
                            ?>
                            <tr>
                                <td>
                                    <a href="<?php echo esc_url( get_edit_post_link( $course ) ); ?>">
                                        <?php echo esc_html( $course->post_title ); ?>
                                    </a>
                                </td>
                                <td><?php echo esc_html( $item->enrollment_count ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php esc_html_e( 'No data available.', 'edugo-lms' ); ?></p>
            <?php endif; ?>
        </div>

        <div class="edugo-report-section">
            <h2><?php esc_html_e( 'Quiz Statistics', 'edugo-lms' ); ?></h2>
            <table class="wp-list-table widefat fixed striped">
                <tbody>
                    <tr>
                        <td><?php esc_html_e( 'Total Quiz Attempts', 'edugo-lms' ); ?></td>
                        <td><strong><?php echo esc_html( $quiz_attempts ); ?></strong></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Passed', 'edugo-lms' ); ?></td>
                        <td><strong><?php echo esc_html( $quiz_passed ); ?></strong></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Pass Rate', 'edugo-lms' ); ?></td>
                        <td><strong><?php echo $quiz_attempts > 0 ? esc_html( round( ( $quiz_passed / $quiz_attempts ) * 100, 1 ) ) : 0; ?>%</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="edugo-report-section">
            <h2><?php esc_html_e( 'Enrollment Trend (Last 6 Months)', 'edugo-lms' ); ?></h2>
            <?php if ( ! empty( $enrollment_trend ) ) : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Month', 'edugo-lms' ); ?></th>
                            <th><?php esc_html_e( 'Enrollments', 'edugo-lms' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $enrollment_trend as $item ) : ?>
                            <tr>
                                <td><?php echo esc_html( date_i18n( 'F Y', strtotime( $item->month . '-01' ) ) ); ?></td>
                                <td><?php echo esc_html( $item->count ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php esc_html_e( 'No data available.', 'edugo-lms' ); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
