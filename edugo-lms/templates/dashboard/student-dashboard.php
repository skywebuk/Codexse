<?php
/**
 * Student Dashboard Template.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$user_id = get_current_user_id();
$user = get_userdata( $user_id );

$enrollment_manager = new Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
$progress_manager = new Edugo_LMS\LMS\Progress\Progress_Manager();
$certificate_manager = new Edugo_LMS\LMS\Certificate\Certificate_Manager();

$enrollments = $enrollment_manager->get_user_enrollments( $user_id );
$certificates = $certificate_manager->get_user_certificates( $user_id );

$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'dashboard';
?>

<div class="edugo-dashboard edugo-student-dashboard">
    <div class="edugo-dashboard-sidebar">
        <div class="edugo-user-info">
            <div class="edugo-avatar">
                <?php echo get_avatar( $user_id, 80 ); ?>
            </div>
            <div class="edugo-user-details">
                <h3 class="edugo-user-name"><?php echo esc_html( $user->display_name ); ?></h3>
                <span class="edugo-user-role"><?php echo esc_html( Edugo_LMS\LMS\Roles::get_user_display_role( $user_id ) ); ?></span>
            </div>
        </div>

        <nav class="edugo-dashboard-nav">
            <ul>
                <li class="<?php echo $active_tab === 'dashboard' ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'dashboard' ) ); ?>">
                        <span class="dashicons dashicons-dashboard"></span>
                        <?php esc_html_e( 'Dashboard', 'edugo-lms' ); ?>
                    </a>
                </li>
                <li class="<?php echo $active_tab === 'courses' ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'courses' ) ); ?>">
                        <span class="dashicons dashicons-welcome-learn-more"></span>
                        <?php esc_html_e( 'My Courses', 'edugo-lms' ); ?>
                    </a>
                </li>
                <li class="<?php echo $active_tab === 'quizzes' ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'quizzes' ) ); ?>">
                        <span class="dashicons dashicons-editor-help"></span>
                        <?php esc_html_e( 'Quiz Results', 'edugo-lms' ); ?>
                    </a>
                </li>
                <li class="<?php echo $active_tab === 'assignments' ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'assignments' ) ); ?>">
                        <span class="dashicons dashicons-media-document"></span>
                        <?php esc_html_e( 'Assignments', 'edugo-lms' ); ?>
                    </a>
                </li>
                <li class="<?php echo $active_tab === 'certificates' ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'certificates' ) ); ?>">
                        <span class="dashicons dashicons-awards"></span>
                        <?php esc_html_e( 'Certificates', 'edugo-lms' ); ?>
                    </a>
                </li>
                <li class="<?php echo $active_tab === 'profile' ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'profile' ) ); ?>">
                        <span class="dashicons dashicons-admin-users"></span>
                        <?php esc_html_e( 'Profile', 'edugo-lms' ); ?>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="edugo-dashboard-content">
        <?php
        switch ( $active_tab ) {
            case 'courses':
                include EDUGO_LMS_PATH . 'templates/dashboard/student/courses.php';
                break;

            case 'quizzes':
                include EDUGO_LMS_PATH . 'templates/dashboard/student/quizzes.php';
                break;

            case 'assignments':
                include EDUGO_LMS_PATH . 'templates/dashboard/student/assignments.php';
                break;

            case 'certificates':
                include EDUGO_LMS_PATH . 'templates/dashboard/student/certificates.php';
                break;

            case 'profile':
                include EDUGO_LMS_PATH . 'templates/dashboard/student/profile.php';
                break;

            default:
                ?>
                <div class="edugo-dashboard-header">
                    <h2><?php esc_html_e( 'Dashboard', 'edugo-lms' ); ?></h2>
                </div>

                <div class="edugo-stats-grid">
                    <div class="edugo-stat-card">
                        <div class="edugo-stat-icon">
                            <span class="dashicons dashicons-welcome-learn-more"></span>
                        </div>
                        <div class="edugo-stat-content">
                            <span class="edugo-stat-number"><?php echo count( $enrollments ); ?></span>
                            <span class="edugo-stat-label"><?php esc_html_e( 'Enrolled Courses', 'edugo-lms' ); ?></span>
                        </div>
                    </div>

                    <div class="edugo-stat-card">
                        <div class="edugo-stat-icon">
                            <span class="dashicons dashicons-yes-alt"></span>
                        </div>
                        <div class="edugo-stat-content">
                            <?php
                            $completed = array_filter( $enrollments, function( $e ) {
                                return $e->status === 'completed';
                            } );
                            ?>
                            <span class="edugo-stat-number"><?php echo count( $completed ); ?></span>
                            <span class="edugo-stat-label"><?php esc_html_e( 'Completed', 'edugo-lms' ); ?></span>
                        </div>
                    </div>

                    <div class="edugo-stat-card">
                        <div class="edugo-stat-icon">
                            <span class="dashicons dashicons-awards"></span>
                        </div>
                        <div class="edugo-stat-content">
                            <span class="edugo-stat-number"><?php echo count( $certificates ); ?></span>
                            <span class="edugo-stat-label"><?php esc_html_e( 'Certificates', 'edugo-lms' ); ?></span>
                        </div>
                    </div>
                </div>

                <div class="edugo-dashboard-section">
                    <h3><?php esc_html_e( 'Continue Learning', 'edugo-lms' ); ?></h3>

                    <?php if ( ! empty( $enrollments ) ) : ?>
                        <div class="edugo-course-list">
                            <?php
                            $in_progress = array_filter( $enrollments, function( $e ) {
                                return $e->status === 'enrolled';
                            } );

                            foreach ( array_slice( $in_progress, 0, 4 ) as $enrollment ) :
                                $course = get_post( $enrollment->course_id );
                                if ( ! $course ) continue;

                                $progress = $progress_manager->get_course_progress( $user_id, $enrollment->course_id );
                                ?>
                                <div class="edugo-course-card">
                                    <div class="edugo-course-thumbnail">
                                        <?php if ( has_post_thumbnail( $course ) ) : ?>
                                            <?php echo get_the_post_thumbnail( $course, 'medium' ); ?>
                                        <?php else : ?>
                                            <div class="edugo-no-thumbnail"></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="edugo-course-info">
                                        <h4 class="edugo-course-title">
                                            <a href="<?php echo esc_url( get_permalink( $course ) ); ?>">
                                                <?php echo esc_html( $course->post_title ); ?>
                                            </a>
                                        </h4>
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
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <p class="edugo-no-data"><?php esc_html_e( 'You have not enrolled in any courses yet.', 'edugo-lms' ); ?></p>
                        <a href="<?php echo esc_url( get_post_type_archive_link( 'edugo_course' ) ); ?>" class="edugo-button">
                            <?php esc_html_e( 'Browse Courses', 'edugo-lms' ); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <?php
                break;
        }
        ?>
    </div>
</div>
