<?php
/**
 * Instructor Dashboard Template.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$user_id = get_current_user_id();
$user = get_userdata( $user_id );

$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'dashboard';

// Get instructor stats.
global $wpdb;

$total_courses = wp_count_posts( 'edugo_course' );
$instructor_courses = get_posts( array(
    'post_type'      => 'edugo_course',
    'author'         => $user_id,
    'posts_per_page' => -1,
    'post_status'    => array( 'publish', 'draft', 'pending' ),
) );

$course_ids = wp_list_pluck( $instructor_courses, 'ID' );

// Get total students.
$total_students = 0;
if ( ! empty( $course_ids ) ) {
    $placeholders = implode( ',', array_fill( 0, count( $course_ids ), '%d' ) );
    $total_students = (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(DISTINCT user_id) FROM {$wpdb->prefix}edugo_enrollments WHERE course_id IN ($placeholders)",
            ...$course_ids
        )
    );
}

// Get earnings.
$earnings = $wpdb->get_row(
    $wpdb->prepare(
        "SELECT
            SUM(CASE WHEN status = 'pending' THEN commission_amount ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'completed' THEN commission_amount ELSE 0 END) as completed
        FROM {$wpdb->prefix}edugo_earnings
        WHERE instructor_id = %d",
        $user_id
    )
);

$pending_earnings = $earnings->pending ?? 0;
$completed_earnings = $earnings->completed ?? 0;
?>

<div class="edugo-dashboard edugo-instructor-dashboard">
    <div class="edugo-dashboard-sidebar">
        <div class="edugo-user-info">
            <div class="edugo-avatar">
                <?php echo get_avatar( $user_id, 80 ); ?>
            </div>
            <div class="edugo-user-details">
                <h3 class="edugo-user-name"><?php echo esc_html( $user->display_name ); ?></h3>
                <span class="edugo-user-role"><?php esc_html_e( 'Instructor', 'edugo-lms' ); ?></span>
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
                <li class="<?php echo $active_tab === 'create-course' ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'create-course' ) ); ?>">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?php esc_html_e( 'Create Course', 'edugo-lms' ); ?>
                    </a>
                </li>
                <li class="<?php echo $active_tab === 'students' ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'students' ) ); ?>">
                        <span class="dashicons dashicons-groups"></span>
                        <?php esc_html_e( 'Students', 'edugo-lms' ); ?>
                    </a>
                </li>
                <li class="<?php echo $active_tab === 'assignments' ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'assignments' ) ); ?>">
                        <span class="dashicons dashicons-media-document"></span>
                        <?php esc_html_e( 'Assignments', 'edugo-lms' ); ?>
                    </a>
                </li>
                <li class="<?php echo $active_tab === 'earnings' ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'earnings' ) ); ?>">
                        <span class="dashicons dashicons-chart-bar"></span>
                        <?php esc_html_e( 'Earnings', 'edugo-lms' ); ?>
                    </a>
                </li>
                <li class="<?php echo $active_tab === 'withdraw' ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'withdraw' ) ); ?>">
                        <span class="dashicons dashicons-money-alt"></span>
                        <?php esc_html_e( 'Withdraw', 'edugo-lms' ); ?>
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
                include EDUGO_LMS_PATH . 'templates/dashboard/instructor/courses.php';
                break;

            case 'create-course':
                include EDUGO_LMS_PATH . 'templates/dashboard/instructor/create-course.php';
                break;

            case 'students':
                include EDUGO_LMS_PATH . 'templates/dashboard/instructor/students.php';
                break;

            case 'assignments':
                include EDUGO_LMS_PATH . 'templates/dashboard/instructor/assignments.php';
                break;

            case 'earnings':
                include EDUGO_LMS_PATH . 'templates/dashboard/instructor/earnings.php';
                break;

            case 'withdraw':
                include EDUGO_LMS_PATH . 'templates/dashboard/instructor/withdraw.php';
                break;

            case 'profile':
                include EDUGO_LMS_PATH . 'templates/dashboard/instructor/profile.php';
                break;

            default:
                ?>
                <div class="edugo-dashboard-header">
                    <h2><?php esc_html_e( 'Instructor Dashboard', 'edugo-lms' ); ?></h2>
                </div>

                <div class="edugo-stats-grid">
                    <div class="edugo-stat-card">
                        <div class="edugo-stat-icon">
                            <span class="dashicons dashicons-welcome-learn-more"></span>
                        </div>
                        <div class="edugo-stat-content">
                            <span class="edugo-stat-number"><?php echo count( $instructor_courses ); ?></span>
                            <span class="edugo-stat-label"><?php esc_html_e( 'Total Courses', 'edugo-lms' ); ?></span>
                        </div>
                    </div>

                    <div class="edugo-stat-card">
                        <div class="edugo-stat-icon">
                            <span class="dashicons dashicons-groups"></span>
                        </div>
                        <div class="edugo-stat-content">
                            <span class="edugo-stat-number"><?php echo esc_html( $total_students ); ?></span>
                            <span class="edugo-stat-label"><?php esc_html_e( 'Total Students', 'edugo-lms' ); ?></span>
                        </div>
                    </div>

                    <div class="edugo-stat-card">
                        <div class="edugo-stat-icon">
                            <span class="dashicons dashicons-money-alt"></span>
                        </div>
                        <div class="edugo-stat-content">
                            <span class="edugo-stat-number"><?php echo wc_price( $pending_earnings ); ?></span>
                            <span class="edugo-stat-label"><?php esc_html_e( 'Pending Earnings', 'edugo-lms' ); ?></span>
                        </div>
                    </div>

                    <div class="edugo-stat-card">
                        <div class="edugo-stat-icon">
                            <span class="dashicons dashicons-chart-bar"></span>
                        </div>
                        <div class="edugo-stat-content">
                            <span class="edugo-stat-number"><?php echo wc_price( $completed_earnings ); ?></span>
                            <span class="edugo-stat-label"><?php esc_html_e( 'Total Earnings', 'edugo-lms' ); ?></span>
                        </div>
                    </div>
                </div>

                <div class="edugo-dashboard-section">
                    <h3><?php esc_html_e( 'Recent Courses', 'edugo-lms' ); ?></h3>

                    <?php if ( ! empty( $instructor_courses ) ) : ?>
                        <table class="edugo-table">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e( 'Course', 'edugo-lms' ); ?></th>
                                    <th><?php esc_html_e( 'Status', 'edugo-lms' ); ?></th>
                                    <th><?php esc_html_e( 'Students', 'edugo-lms' ); ?></th>
                                    <th><?php esc_html_e( 'Actions', 'edugo-lms' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $enrollment_manager = new Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
                                foreach ( array_slice( $instructor_courses, 0, 5 ) as $course ) :
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo esc_url( get_permalink( $course ) ); ?>">
                                                <?php echo esc_html( $course->post_title ); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="edugo-status edugo-status-<?php echo esc_attr( $course->post_status ); ?>">
                                                <?php echo esc_html( get_post_status_object( $course->post_status )->label ); ?>
                                            </span>
                                        </td>
                                        <td><?php echo esc_html( $enrollment_manager->get_enrollment_count( $course->ID ) ); ?></td>
                                        <td>
                                            <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'edit-course', 'course_id' => $course->ID ) ) ); ?>" class="edugo-button edugo-button-small">
                                                <?php esc_html_e( 'Edit', 'edugo-lms' ); ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p class="edugo-no-data"><?php esc_html_e( 'You have not created any courses yet.', 'edugo-lms' ); ?></p>
                        <a href="<?php echo esc_url( add_query_arg( 'tab', 'create-course' ) ); ?>" class="edugo-button">
                            <?php esc_html_e( 'Create Your First Course', 'edugo-lms' ); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <?php
                break;
        }
        ?>
    </div>
</div>
