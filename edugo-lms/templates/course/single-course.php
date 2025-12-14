<?php
/**
 * Single Course Template.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $course ) || ! $course ) {
    return;
}

$course_id = $course->ID;
$instructor = get_userdata( $course->post_author );
$enrollment_manager = new Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
$progress_manager = new Edugo_LMS\LMS\Progress\Progress_Manager();

$is_enrolled = is_user_logged_in() && $enrollment_manager->is_enrolled( get_current_user_id(), $course_id );
$is_free = get_post_meta( $course_id, '_edugo_is_free', true ) === 'yes';
$price = get_post_meta( $course_id, '_edugo_price', true );
$lessons = Edugo_LMS\Helpers\Helper::get_course_lessons( $course_id );
$duration = Edugo_LMS\Helpers\Helper::get_course_duration( $course_id );
$student_count = $enrollment_manager->get_enrollment_count( $course_id );

$progress = array( 'percentage' => 0 );
if ( $is_enrolled ) {
    $progress = $progress_manager->get_course_progress( get_current_user_id(), $course_id );
}
?>

<div class="edugo-single-course">
    <div class="edugo-course-header">
        <div class="edugo-course-header-content">
            <?php
            $categories = get_the_terms( $course_id, 'edugo_course_category' );
            if ( $categories && ! is_wp_error( $categories ) ) :
                ?>
                <div class="edugo-course-categories">
                    <?php foreach ( $categories as $category ) : ?>
                        <a href="<?php echo esc_url( get_term_link( $category ) ); ?>" class="edugo-category-badge">
                            <?php echo esc_html( $category->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h1 class="edugo-course-title"><?php echo esc_html( $course->post_title ); ?></h1>

            <div class="edugo-course-excerpt">
                <?php echo wp_kses_post( get_the_excerpt( $course ) ); ?>
            </div>

            <div class="edugo-course-meta">
                <?php if ( $instructor ) : ?>
                    <div class="edugo-meta-item edugo-instructor">
                        <?php echo get_avatar( $instructor->ID, 32 ); ?>
                        <span><?php echo esc_html( $instructor->display_name ); ?></span>
                    </div>
                <?php endif; ?>

                <div class="edugo-meta-item">
                    <span class="dashicons dashicons-groups"></span>
                    <span>
                        <?php
                        printf(
                            /* translators: %d: Number of students */
                            esc_html( _n( '%d student', '%d students', $student_count, 'edugo-lms' ) ),
                            $student_count
                        );
                        ?>
                    </span>
                </div>

                <div class="edugo-meta-item">
                    <span class="dashicons dashicons-list-view"></span>
                    <span>
                        <?php
                        printf(
                            /* translators: %d: Number of lessons */
                            esc_html( _n( '%d lesson', '%d lessons', count( $lessons ), 'edugo-lms' ) ),
                            count( $lessons )
                        );
                        ?>
                    </span>
                </div>

                <?php if ( $duration > 0 ) : ?>
                    <div class="edugo-meta-item">
                        <span class="dashicons dashicons-clock"></span>
                        <span><?php echo esc_html( Edugo_LMS\Helpers\Helper::format_duration( $duration ) ); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ( has_post_thumbnail( $course ) ) : ?>
            <div class="edugo-course-header-image">
                <?php echo get_the_post_thumbnail( $course, 'large' ); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="edugo-course-body">
        <div class="edugo-course-content">
            <div class="edugo-course-description">
                <h2><?php esc_html_e( 'About This Course', 'edugo-lms' ); ?></h2>
                <?php echo wp_kses_post( apply_filters( 'the_content', $course->post_content ) ); ?>
            </div>

            <?php
            $what_will_learn = get_post_meta( $course_id, '_edugo_what_will_learn', true );
            if ( $what_will_learn ) :
                ?>
                <div class="edugo-course-section">
                    <h2><?php esc_html_e( 'What You Will Learn', 'edugo-lms' ); ?></h2>
                    <div class="edugo-learn-list">
                        <?php echo wp_kses_post( wpautop( $what_will_learn ) ); ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="edugo-course-section">
                <h2><?php esc_html_e( 'Course Curriculum', 'edugo-lms' ); ?></h2>

                <?php if ( ! empty( $lessons ) ) : ?>
                    <div class="edugo-curriculum">
                        <?php foreach ( $lessons as $index => $lesson ) :
                            $lesson_duration = get_post_meta( $lesson->ID, '_edugo_duration', true );
                            $is_preview = get_post_meta( $lesson->ID, '_edugo_is_preview', true ) === 'yes';
                            $is_completed = $is_enrolled && $progress_manager->is_lesson_completed( get_current_user_id(), $lesson->ID );
                            ?>
                            <div class="edugo-curriculum-item <?php echo $is_completed ? 'completed' : ''; ?>">
                                <div class="edugo-curriculum-number"><?php echo esc_html( $index + 1 ); ?></div>
                                <div class="edugo-curriculum-content">
                                    <h4 class="edugo-curriculum-title"><?php echo esc_html( $lesson->post_title ); ?></h4>
                                    <?php if ( $lesson_duration ) : ?>
                                        <span class="edugo-curriculum-duration">
                                            <?php echo esc_html( Edugo_LMS\Helpers\Helper::format_duration( (int) $lesson_duration ) ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="edugo-curriculum-status">
                                    <?php if ( $is_completed ) : ?>
                                        <span class="dashicons dashicons-yes-alt edugo-text-success"></span>
                                    <?php elseif ( $is_preview ) : ?>
                                        <span class="edugo-preview-badge"><?php esc_html_e( 'Preview', 'edugo-lms' ); ?></span>
                                    <?php elseif ( ! $is_enrolled ) : ?>
                                        <span class="dashicons dashicons-lock"></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p class="edugo-no-content"><?php esc_html_e( 'No lessons available yet.', 'edugo-lms' ); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="edugo-course-sidebar">
            <div class="edugo-course-card">
                <?php if ( $is_enrolled ) : ?>
                    <div class="edugo-enrolled-info">
                        <div class="edugo-progress-circle" data-progress="<?php echo esc_attr( $progress['percentage'] ); ?>">
                            <span><?php echo esc_html( $progress['percentage'] ); ?>%</span>
                        </div>
                        <p><?php esc_html_e( 'Course Progress', 'edugo-lms' ); ?></p>
                    </div>

                    <a href="<?php echo esc_url( get_permalink( $course ) ); ?>" class="edugo-button edugo-button-primary edugo-button-block">
                        <?php
                        if ( $progress['percentage'] >= 100 ) {
                            esc_html_e( 'Review Course', 'edugo-lms' );
                        } elseif ( $progress['percentage'] > 0 ) {
                            esc_html_e( 'Continue Learning', 'edugo-lms' );
                        } else {
                            esc_html_e( 'Start Course', 'edugo-lms' );
                        }
                        ?>
                    </a>
                <?php else : ?>
                    <div class="edugo-price-box">
                        <?php if ( $is_free ) : ?>
                            <span class="edugo-price edugo-free"><?php esc_html_e( 'Free', 'edugo-lms' ); ?></span>
                        <?php elseif ( $price ) : ?>
                            <span class="edugo-price"><?php echo esc_html( Edugo_LMS\Helpers\Helper::format_price( (float) $price ) ); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if ( $is_free ) : ?>
                        <?php if ( is_user_logged_in() ) : ?>
                            <button class="edugo-button edugo-button-primary edugo-button-block edugo-enroll-btn" data-course-id="<?php echo esc_attr( $course_id ); ?>">
                                <?php esc_html_e( 'Enroll Now - Free', 'edugo-lms' ); ?>
                            </button>
                        <?php else : ?>
                            <a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="edugo-button edugo-button-primary edugo-button-block">
                                <?php esc_html_e( 'Login to Enroll', 'edugo-lms' ); ?>
                            </a>
                        <?php endif; ?>
                    <?php else : ?>
                        <?php do_action( 'edugo_course_purchase_button', $course_id ); ?>
                    <?php endif; ?>
                <?php endif; ?>

                <ul class="edugo-course-features">
                    <li>
                        <span class="dashicons dashicons-video-alt3"></span>
                        <?php
                        printf(
                            /* translators: %d: Number of lessons */
                            esc_html__( '%d lessons', 'edugo-lms' ),
                            count( $lessons )
                        );
                        ?>
                    </li>
                    <?php if ( $duration > 0 ) : ?>
                        <li>
                            <span class="dashicons dashicons-clock"></span>
                            <?php echo esc_html( Edugo_LMS\Helpers\Helper::format_duration( $duration ) ); ?>
                        </li>
                    <?php endif; ?>
                    <li>
                        <span class="dashicons dashicons-yes"></span>
                        <?php esc_html_e( 'Full lifetime access', 'edugo-lms' ); ?>
                    </li>
                    <li>
                        <span class="dashicons dashicons-awards"></span>
                        <?php esc_html_e( 'Certificate of completion', 'edugo-lms' ); ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
