<?php
/**
 * Student Dashboard - My Courses Tab.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$user_id = get_current_user_id();
$enrollment_manager = new Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
$progress_manager = new Edugo_LMS\LMS\Progress\Progress_Manager();
$enrollments = $enrollment_manager->get_user_enrollments( $user_id );
?>

<div class="edugo-dashboard-header">
    <h2><?php esc_html_e( 'My Courses', 'edugo-lms' ); ?></h2>
</div>

<?php if ( ! empty( $enrollments ) ) : ?>
    <div class="edugo-courses-filter">
        <select id="edugo-course-status-filter" class="edugo-select">
            <option value="all"><?php esc_html_e( 'All Courses', 'edugo-lms' ); ?></option>
            <option value="in_progress"><?php esc_html_e( 'In Progress', 'edugo-lms' ); ?></option>
            <option value="completed"><?php esc_html_e( 'Completed', 'edugo-lms' ); ?></option>
        </select>
    </div>

    <div class="edugo-enrolled-courses">
        <?php foreach ( $enrollments as $enrollment ) :
            $course = get_post( $enrollment->course_id );
            if ( ! $course ) continue;

            $progress = $progress_manager->get_course_progress( $user_id, $enrollment->course_id );
            $status_class = $enrollment->status === 'completed' ? 'completed' : ( $progress['percentage'] > 0 ? 'in_progress' : 'not_started' );
            ?>
            <div class="edugo-enrolled-course-card" data-status="<?php echo esc_attr( $status_class ); ?>">
                <div class="edugo-course-thumbnail">
                    <?php if ( has_post_thumbnail( $course ) ) : ?>
                        <a href="<?php echo esc_url( get_permalink( $course ) ); ?>">
                            <?php echo get_the_post_thumbnail( $course, 'medium' ); ?>
                        </a>
                    <?php else : ?>
                        <div class="edugo-no-thumbnail"></div>
                    <?php endif; ?>

                    <?php if ( $enrollment->status === 'completed' ) : ?>
                        <span class="edugo-course-badge edugo-badge-completed"><?php esc_html_e( 'Completed', 'edugo-lms' ); ?></span>
                    <?php endif; ?>
                </div>

                <div class="edugo-course-details">
                    <h3 class="edugo-course-title">
                        <a href="<?php echo esc_url( get_permalink( $course ) ); ?>">
                            <?php echo esc_html( $course->post_title ); ?>
                        </a>
                    </h3>

                    <div class="edugo-course-meta">
                        <span class="edugo-enrolled-date">
                            <?php
                            printf(
                                /* translators: %s: Enrollment date */
                                esc_html__( 'Enrolled: %s', 'edugo-lms' ),
                                esc_html( date_i18n( get_option( 'date_format' ), strtotime( $enrollment->enrolled_at ) ) )
                            );
                            ?>
                        </span>
                    </div>

                    <div class="edugo-progress-wrapper">
                        <div class="edugo-progress-bar">
                            <div class="edugo-progress-fill" style="width: <?php echo esc_attr( $progress['percentage'] ); ?>%"></div>
                        </div>
                        <span class="edugo-progress-text">
                            <?php
                            printf(
                                /* translators: %d: Progress percentage */
                                esc_html__( '%d%% Complete', 'edugo-lms' ),
                                (int) $progress['percentage']
                            );
                            ?>
                        </span>
                    </div>

                    <div class="edugo-course-actions">
                        <a href="<?php echo esc_url( get_permalink( $course ) ); ?>" class="edugo-button edugo-button-primary">
                            <?php
                            if ( $enrollment->status === 'completed' ) {
                                esc_html_e( 'Review Course', 'edugo-lms' );
                            } elseif ( $progress['percentage'] > 0 ) {
                                esc_html_e( 'Continue Learning', 'edugo-lms' );
                            } else {
                                esc_html_e( 'Start Course', 'edugo-lms' );
                            }
                            ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else : ?>
    <div class="edugo-empty-state">
        <div class="edugo-empty-icon">
            <span class="dashicons dashicons-welcome-learn-more"></span>
        </div>
        <h3><?php esc_html_e( 'No Courses Yet', 'edugo-lms' ); ?></h3>
        <p><?php esc_html_e( 'You have not enrolled in any courses yet. Start learning today!', 'edugo-lms' ); ?></p>
        <a href="<?php echo esc_url( get_post_type_archive_link( 'edugo_course' ) ); ?>" class="edugo-button edugo-button-primary">
            <?php esc_html_e( 'Browse Courses', 'edugo-lms' ); ?>
        </a>
    </div>
<?php endif; ?>
