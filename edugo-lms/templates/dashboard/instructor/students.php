<?php
/**
 * Instructor Dashboard - Students Tab.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$instructor_id = get_current_user_id();

global $wpdb;

// Get all students enrolled in instructor's courses.
$students = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT DISTINCT e.user_id, e.course_id, e.enrolled_at, e.status as enrollment_status
        FROM {$wpdb->prefix}edugo_enrollments e
        INNER JOIN {$wpdb->posts} p ON e.course_id = p.ID
        WHERE p.post_author = %d
        ORDER BY e.enrolled_at DESC",
        $instructor_id
    )
);

$progress_manager = new Edugo_LMS\LMS\Progress\Progress_Manager();
?>

<div class="edugo-dashboard-header">
    <h2><?php esc_html_e( 'My Students', 'edugo-lms' ); ?></h2>
</div>

<?php if ( ! empty( $students ) ) : ?>
    <div class="edugo-students-table-wrapper">
        <table class="edugo-table edugo-students-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Student', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Course', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Progress', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Enrolled', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'edugo-lms' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $students as $student ) :
                    $user = get_userdata( $student->user_id );
                    $course = get_post( $student->course_id );

                    if ( ! $user || ! $course ) continue;

                    $progress = $progress_manager->get_course_progress( $student->user_id, $student->course_id );
                    ?>
                    <tr>
                        <td>
                            <div class="edugo-student-info">
                                <?php echo get_avatar( $student->user_id, 40 ); ?>
                                <div class="edugo-student-details">
                                    <span class="edugo-student-name"><?php echo esc_html( $user->display_name ); ?></span>
                                    <span class="edugo-student-email"><?php echo esc_html( $user->user_email ); ?></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="<?php echo esc_url( get_permalink( $course ) ); ?>">
                                <?php echo esc_html( $course->post_title ); ?>
                            </a>
                        </td>
                        <td>
                            <div class="edugo-progress-mini">
                                <div class="edugo-progress-bar-mini">
                                    <div class="edugo-progress-fill" style="width: <?php echo esc_attr( $progress['percentage'] ); ?>%"></div>
                                </div>
                                <span><?php echo esc_html( $progress['percentage'] ); ?>%</span>
                            </div>
                        </td>
                        <td>
                            <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $student->enrolled_at ) ) ); ?>
                        </td>
                        <td>
                            <?php if ( $student->enrollment_status === 'completed' ) : ?>
                                <span class="edugo-badge edugo-badge-success"><?php esc_html_e( 'Completed', 'edugo-lms' ); ?></span>
                            <?php else : ?>
                                <span class="edugo-badge edugo-badge-info"><?php esc_html_e( 'Enrolled', 'edugo-lms' ); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else : ?>
    <div class="edugo-empty-state">
        <div class="edugo-empty-icon">
            <span class="dashicons dashicons-groups"></span>
        </div>
        <h3><?php esc_html_e( 'No Students Yet', 'edugo-lms' ); ?></h3>
        <p><?php esc_html_e( 'Once students enroll in your courses, they will appear here.', 'edugo-lms' ); ?></p>
    </div>
<?php endif; ?>
