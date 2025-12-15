<?php
/**
 * Course Content Template (Lesson View).
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $course_id ) || ! $course_id ) {
    return;
}

$course = get_post( $course_id );
if ( ! $course ) {
    return;
}

$lessons = Edugo_LMS\Helpers\Helper::get_course_lessons( $course_id );
$progress_manager = new Edugo_LMS\LMS\Progress\Progress_Manager();
$user_id = get_current_user_id();

// Get current lesson.
$current_lesson_id = isset( $_GET['lesson'] ) ? absint( $_GET['lesson'] ) : 0;
$current_lesson = null;

if ( $current_lesson_id ) {
    $current_lesson = get_post( $current_lesson_id );
} elseif ( ! empty( $lessons ) ) {
    $current_lesson = $lessons[0];
    $current_lesson_id = $current_lesson->ID;
}

$progress = $progress_manager->get_course_progress( $user_id, $course_id );
?>

<div class="edugo-course-content-wrapper">
    <div class="edugo-course-sidebar-nav">
        <div class="edugo-course-progress-header">
            <h3><?php echo esc_html( $course->post_title ); ?></h3>
            <div class="edugo-progress-bar">
                <div class="edugo-progress-fill" style="width: <?php echo esc_attr( $progress['percentage'] ); ?>%"></div>
            </div>
            <span class="edugo-progress-text"><?php echo esc_html( $progress['percentage'] ); ?>% <?php esc_html_e( 'Complete', 'edugo-lms' ); ?></span>
        </div>

        <nav class="edugo-lessons-nav">
            <?php foreach ( $lessons as $index => $lesson ) :
                $is_completed = $progress_manager->is_lesson_completed( $user_id, $lesson->ID );
                $is_current = $lesson->ID === $current_lesson_id;
                $is_locked = Edugo_LMS\Helpers\Helper::is_drip_locked( $lesson->ID, $user_id );
                ?>
                <a href="<?php echo $is_locked ? '#' : esc_url( add_query_arg( 'lesson', $lesson->ID ) ); ?>"
                   class="edugo-lesson-nav-item <?php echo $is_current ? 'current' : ''; ?> <?php echo $is_completed ? 'completed' : ''; ?> <?php echo $is_locked ? 'locked' : ''; ?>">
                    <span class="edugo-lesson-number"><?php echo esc_html( $index + 1 ); ?></span>
                    <span class="edugo-lesson-title"><?php echo esc_html( $lesson->post_title ); ?></span>
                    <?php if ( $is_completed ) : ?>
                        <span class="edugo-lesson-status dashicons dashicons-yes-alt"></span>
                    <?php elseif ( $is_locked ) : ?>
                        <span class="edugo-lesson-status dashicons dashicons-lock"></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <div class="edugo-lesson-content" data-lesson-id="<?php echo esc_attr( $current_lesson_id ); ?>">
        <?php if ( $current_lesson ) : ?>
            <?php
            $is_locked = Edugo_LMS\Helpers\Helper::is_drip_locked( $current_lesson_id, $user_id );

            if ( $is_locked && $is_enrolled ) :
                $unlock_date = Edugo_LMS\Helpers\Helper::get_drip_unlock_date( $current_lesson_id, $user_id );
                ?>
                <div class="edugo-lesson-locked">
                    <span class="dashicons dashicons-lock"></span>
                    <h2><?php esc_html_e( 'Lesson Locked', 'edugo-lms' ); ?></h2>
                    <?php if ( $unlock_date ) : ?>
                        <p>
                            <?php
                            printf(
                                /* translators: %s: Unlock date */
                                esc_html__( 'This lesson will be available on %s.', 'edugo-lms' ),
                                esc_html( $unlock_date )
                            );
                            ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php elseif ( ! $is_enrolled ) : ?>
                <div class="edugo-lesson-locked">
                    <span class="dashicons dashicons-lock"></span>
                    <h2><?php esc_html_e( 'Enroll to Access', 'edugo-lms' ); ?></h2>
                    <p><?php esc_html_e( 'Please enroll in this course to access the lesson content.', 'edugo-lms' ); ?></p>
                    <a href="<?php echo esc_url( get_permalink( $course ) ); ?>" class="edugo-button edugo-button-primary">
                        <?php esc_html_e( 'View Course', 'edugo-lms' ); ?>
                    </a>
                </div>
            <?php else : ?>
                <div class="edugo-lesson-header">
                    <h1 class="edugo-lesson-title"><?php echo esc_html( $current_lesson->post_title ); ?></h1>
                </div>

                <div class="edugo-lesson-body">
                    <?php echo wp_kses_post( apply_filters( 'the_content', $current_lesson->post_content ) ); ?>
                </div>

                <?php
                $is_completed = $progress_manager->is_lesson_completed( $user_id, $current_lesson_id );
                ?>
                <div class="edugo-lesson-footer">
                    <?php if ( ! $is_completed ) : ?>
                        <button class="edugo-button edugo-button-primary edugo-complete-lesson" data-lesson-id="<?php echo esc_attr( $current_lesson_id ); ?>">
                            <?php esc_html_e( 'Mark as Complete', 'edugo-lms' ); ?>
                        </button>
                    <?php else : ?>
                        <span class="edugo-lesson-completed-badge">
                            <span class="dashicons dashicons-yes-alt"></span>
                            <?php esc_html_e( 'Completed', 'edugo-lms' ); ?>
                        </span>
                    <?php endif; ?>

                    <?php
                    // Next lesson navigation.
                    $current_index = array_search( $current_lesson, $lessons, true );
                    $next_lesson = isset( $lessons[ $current_index + 1 ] ) ? $lessons[ $current_index + 1 ] : null;

                    if ( $next_lesson ) :
                        ?>
                        <a href="<?php echo esc_url( add_query_arg( 'lesson', $next_lesson->ID ) ); ?>" class="edugo-button edugo-button-secondary edugo-next-lesson">
                            <?php esc_html_e( 'Next Lesson', 'edugo-lms' ); ?>
                            <span class="dashicons dashicons-arrow-right-alt"></span>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <div class="edugo-no-lessons">
                <p><?php esc_html_e( 'No lessons available in this course yet.', 'edugo-lms' ); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
