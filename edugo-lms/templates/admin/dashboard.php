<?php
/**
 * Admin Dashboard Template.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap edugo-admin-wrap">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-welcome-learn-more"></span>
        <?php esc_html_e( 'Edugo LMS Dashboard', 'edugo-lms' ); ?>
    </h1>

    <div class="edugo-admin-dashboard">
        <div class="edugo-admin-stats">
            <div class="edugo-stat-box">
                <div class="edugo-stat-icon dashicons dashicons-welcome-learn-more"></div>
                <div class="edugo-stat-content">
                    <span class="edugo-stat-number"><?php echo esc_html( $stats['total_courses'] ); ?></span>
                    <span class="edugo-stat-label"><?php esc_html_e( 'Total Courses', 'edugo-lms' ); ?></span>
                </div>
                <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=edugo_course' ) ); ?>" class="edugo-stat-link">
                    <?php esc_html_e( 'View All', 'edugo-lms' ); ?>
                </a>
            </div>

            <div class="edugo-stat-box">
                <div class="edugo-stat-icon dashicons dashicons-media-document"></div>
                <div class="edugo-stat-content">
                    <span class="edugo-stat-number"><?php echo esc_html( $stats['total_lessons'] ); ?></span>
                    <span class="edugo-stat-label"><?php esc_html_e( 'Total Lessons', 'edugo-lms' ); ?></span>
                </div>
                <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=edugo_lesson' ) ); ?>" class="edugo-stat-link">
                    <?php esc_html_e( 'View All', 'edugo-lms' ); ?>
                </a>
            </div>

            <div class="edugo-stat-box">
                <div class="edugo-stat-icon dashicons dashicons-editor-help"></div>
                <div class="edugo-stat-content">
                    <span class="edugo-stat-number"><?php echo esc_html( $stats['total_quizzes'] ); ?></span>
                    <span class="edugo-stat-label"><?php esc_html_e( 'Total Quizzes', 'edugo-lms' ); ?></span>
                </div>
                <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=edugo_quiz' ) ); ?>" class="edugo-stat-link">
                    <?php esc_html_e( 'View All', 'edugo-lms' ); ?>
                </a>
            </div>

            <div class="edugo-stat-box">
                <div class="edugo-stat-icon dashicons dashicons-groups"></div>
                <div class="edugo-stat-content">
                    <span class="edugo-stat-number"><?php echo esc_html( $stats['total_students'] ); ?></span>
                    <span class="edugo-stat-label"><?php esc_html_e( 'Total Students', 'edugo-lms' ); ?></span>
                </div>
                <a href="<?php echo esc_url( admin_url( 'users.php?role=edugo_student' ) ); ?>" class="edugo-stat-link">
                    <?php esc_html_e( 'View All', 'edugo-lms' ); ?>
                </a>
            </div>

            <div class="edugo-stat-box">
                <div class="edugo-stat-icon dashicons dashicons-businessman"></div>
                <div class="edugo-stat-content">
                    <span class="edugo-stat-number"><?php echo esc_html( $stats['total_instructors'] ); ?></span>
                    <span class="edugo-stat-label"><?php esc_html_e( 'Total Instructors', 'edugo-lms' ); ?></span>
                </div>
                <a href="<?php echo esc_url( admin_url( 'users.php?role=edugo_instructor' ) ); ?>" class="edugo-stat-link">
                    <?php esc_html_e( 'View All', 'edugo-lms' ); ?>
                </a>
            </div>

            <div class="edugo-stat-box">
                <div class="edugo-stat-icon dashicons dashicons-clipboard"></div>
                <div class="edugo-stat-content">
                    <span class="edugo-stat-number"><?php echo esc_html( $stats['total_enrollments'] ); ?></span>
                    <span class="edugo-stat-label"><?php esc_html_e( 'Total Enrollments', 'edugo-lms' ); ?></span>
                </div>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=edugo-enrollments' ) ); ?>" class="edugo-stat-link">
                    <?php esc_html_e( 'View All', 'edugo-lms' ); ?>
                </a>
            </div>
        </div>

        <?php if ( $stats['pending_courses'] > 0 ) : ?>
            <div class="edugo-admin-notice notice notice-warning">
                <p>
                    <?php
                    printf(
                        /* translators: %d: Number of pending courses */
                        esc_html( _n( 'You have %d course pending review.', 'You have %d courses pending review.', $stats['pending_courses'], 'edugo-lms' ) ),
                        esc_html( $stats['pending_courses'] )
                    );
                    ?>
                    <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=edugo_course&post_status=pending' ) ); ?>">
                        <?php esc_html_e( 'Review now', 'edugo-lms' ); ?>
                    </a>
                </p>
            </div>
        <?php endif; ?>

        <div class="edugo-admin-columns">
            <div class="edugo-admin-column">
                <div class="edugo-admin-box">
                    <h2><?php esc_html_e( 'Quick Links', 'edugo-lms' ); ?></h2>
                    <ul class="edugo-quick-links">
                        <li>
                            <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=edugo_course' ) ); ?>">
                                <span class="dashicons dashicons-plus-alt"></span>
                                <?php esc_html_e( 'Add New Course', 'edugo-lms' ); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=edugo_lesson' ) ); ?>">
                                <span class="dashicons dashicons-plus-alt"></span>
                                <?php esc_html_e( 'Add New Lesson', 'edugo-lms' ); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=edugo_quiz' ) ); ?>">
                                <span class="dashicons dashicons-plus-alt"></span>
                                <?php esc_html_e( 'Add New Quiz', 'edugo-lms' ); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=edugo-settings' ) ); ?>">
                                <span class="dashicons dashicons-admin-settings"></span>
                                <?php esc_html_e( 'LMS Settings', 'edugo-lms' ); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=edugo-reports' ) ); ?>">
                                <span class="dashicons dashicons-chart-area"></span>
                                <?php esc_html_e( 'View Reports', 'edugo-lms' ); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="edugo-admin-column">
                <div class="edugo-admin-box">
                    <h2><?php esc_html_e( 'Earnings Overview', 'edugo-lms' ); ?></h2>
                    <?php if ( $stats['total_earnings'] > 0 ) : ?>
                        <div class="edugo-earnings-summary">
                            <div class="edugo-earnings-total">
                                <span class="edugo-earnings-label"><?php esc_html_e( 'Total Earnings', 'edugo-lms' ); ?></span>
                                <span class="edugo-earnings-amount">
                                    <?php echo wp_kses_post( wc_price( $stats['total_earnings'] ) ); ?>
                                </span>
                            </div>
                        </div>
                    <?php else : ?>
                        <p><?php esc_html_e( 'No earnings recorded yet.', 'edugo-lms' ); ?></p>
                    <?php endif; ?>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=edugo-earnings' ) ); ?>" class="button">
                        <?php esc_html_e( 'View Earnings', 'edugo-lms' ); ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="edugo-admin-box">
            <h2><?php esc_html_e( 'System Information', 'edugo-lms' ); ?></h2>
            <table class="edugo-system-info">
                <tr>
                    <td><?php esc_html_e( 'Plugin Version', 'edugo-lms' ); ?></td>
                    <td><?php echo esc_html( EDUGO_LMS_VERSION ); ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'WordPress Version', 'edugo-lms' ); ?></td>
                    <td><?php echo esc_html( get_bloginfo( 'version' ) ); ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'PHP Version', 'edugo-lms' ); ?></td>
                    <td><?php echo esc_html( PHP_VERSION ); ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'WooCommerce', 'edugo-lms' ); ?></td>
                    <td>
                        <?php
                        if ( class_exists( 'WooCommerce' ) ) {
                            echo '<span class="edugo-status-active">' . esc_html( WC()->version ) . '</span>';
                        } else {
                            echo '<span class="edugo-status-inactive">' . esc_html__( 'Not Installed', 'edugo-lms' ) . '</span>';
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
