<?php
/**
 * My Courses Template (Shortcode).
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $enrollments ) ) {
    return;
}

$progress_manager = new Edugo_LMS\LMS\Progress\Progress_Manager();
$user_id = get_current_user_id();
$columns = isset( $atts['columns'] ) ? absint( $atts['columns'] ) : 3;
?>

<div class="edugo-my-courses">
    <?php if ( ! empty( $enrollments ) ) : ?>
        <div class="edugo-courses-grid edugo-columns-<?php echo esc_attr( $columns ); ?>">
            <?php foreach ( $enrollments as $enrollment ) :
                $course = get_post( $enrollment->course_id );
                if ( ! $course ) continue;

                $progress = $progress_manager->get_course_progress( $user_id, $enrollment->course_id );
                ?>
                <div class="edugo-course-card">
                    <div class="edugo-course-thumbnail">
                        <a href="<?php echo esc_url( get_permalink( $course ) ); ?>">
                            <?php if ( has_post_thumbnail( $course ) ) : ?>
                                <?php echo get_the_post_thumbnail( $course, 'medium' ); ?>
                            <?php else : ?>
                                <div class="edugo-no-thumbnail"></div>
                            <?php endif; ?>
                        </a>

                        <?php if ( $enrollment->status === 'completed' ) : ?>
                            <span class="edugo-course-badge edugo-badge-success"><?php esc_html_e( 'Completed', 'edugo-lms' ); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="edugo-course-content">
                        <h3 class="edugo-course-title">
                            <a href="<?php echo esc_url( get_permalink( $course ) ); ?>">
                                <?php echo esc_html( $course->post_title ); ?>
                            </a>
                        </h3>

                        <div class="edugo-course-progress">
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

                        <a href="<?php echo esc_url( get_permalink( $course ) ); ?>" class="edugo-button edugo-button-primary">
                            <?php
                            if ( $enrollment->status === 'completed' ) {
                                esc_html_e( 'Review', 'edugo-lms' );
                            } elseif ( $progress['percentage'] > 0 ) {
                                esc_html_e( 'Continue', 'edugo-lms' );
                            } else {
                                esc_html_e( 'Start', 'edugo-lms' );
                            }
                            ?>
                        </a>
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
            <p><?php esc_html_e( 'You have not enrolled in any courses yet.', 'edugo-lms' ); ?></p>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'edugo_course' ) ); ?>" class="edugo-button edugo-button-primary">
                <?php esc_html_e( 'Browse Courses', 'edugo-lms' ); ?>
            </a>
        </div>
    <?php endif; ?>
</div>
