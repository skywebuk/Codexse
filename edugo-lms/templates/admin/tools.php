<?php
/**
 * Admin Tools Page Template.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$message = '';
$message_type = '';

// Handle tool actions.
if ( isset( $_POST['edugo_tool_action'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'edugo_tool_action' ) ) {
    $action = sanitize_text_field( wp_unslash( $_POST['edugo_tool_action'] ) );

    switch ( $action ) {
        case 'flush_rewrite_rules':
            flush_rewrite_rules();
            $message = __( 'Rewrite rules flushed successfully.', 'edugo-lms' );
            $message_type = 'success';
            break;

        case 'recalculate_progress':
            global $wpdb;
            $enrollments = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}edugo_enrollments WHERE status = 'enrolled'" );
            $progress_manager = new Edugo_LMS\LMS\Progress\Progress_Manager();

            foreach ( $enrollments as $enrollment ) {
                $progress_manager->check_course_completion( $enrollment->user_id, $enrollment->course_id );
            }

            $message = sprintf(
                /* translators: %d: Number of enrollments processed */
                __( 'Progress recalculated for %d enrollments.', 'edugo-lms' ),
                count( $enrollments )
            );
            $message_type = 'success';
            break;

        case 'update_student_counts':
            $courses = get_posts( array(
                'post_type'      => 'edugo_course',
                'posts_per_page' => -1,
                'post_status'    => 'any',
            ) );

            $enrollment_manager = new Edugo_LMS\LMS\Enrollment\Enrollment_Manager();

            foreach ( $courses as $course ) {
                $count = $enrollment_manager->get_enrollment_count( $course->ID );
                update_post_meta( $course->ID, '_edugo_student_count', $count );
            }

            $message = sprintf(
                /* translators: %d: Number of courses updated */
                __( 'Student counts updated for %d courses.', 'edugo-lms' ),
                count( $courses )
            );
            $message_type = 'success';
            break;
    }
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'LMS Tools', 'edugo-lms' ); ?></h1>
    <hr class="wp-header-end">

    <?php if ( $message ) : ?>
        <div class="notice notice-<?php echo esc_attr( $message_type ); ?> is-dismissible">
            <p><?php echo esc_html( $message ); ?></p>
        </div>
    <?php endif; ?>

    <div class="edugo-tools-grid">
        <div class="edugo-tool-card">
            <h3><?php esc_html_e( 'Flush Rewrite Rules', 'edugo-lms' ); ?></h3>
            <p><?php esc_html_e( 'Clear and regenerate URL rewrite rules. Use this if course or lesson pages show 404 errors.', 'edugo-lms' ); ?></p>
            <form method="post">
                <?php wp_nonce_field( 'edugo_tool_action' ); ?>
                <button type="submit" name="edugo_tool_action" value="flush_rewrite_rules" class="button button-primary">
                    <?php esc_html_e( 'Flush Rules', 'edugo-lms' ); ?>
                </button>
            </form>
        </div>

        <div class="edugo-tool-card">
            <h3><?php esc_html_e( 'Recalculate Progress', 'edugo-lms' ); ?></h3>
            <p><?php esc_html_e( 'Recalculate course completion status for all active enrollments.', 'edugo-lms' ); ?></p>
            <form method="post">
                <?php wp_nonce_field( 'edugo_tool_action' ); ?>
                <button type="submit" name="edugo_tool_action" value="recalculate_progress" class="button button-primary">
                    <?php esc_html_e( 'Recalculate', 'edugo-lms' ); ?>
                </button>
            </form>
        </div>

        <div class="edugo-tool-card">
            <h3><?php esc_html_e( 'Update Student Counts', 'edugo-lms' ); ?></h3>
            <p><?php esc_html_e( 'Refresh the student enrollment count for all courses.', 'edugo-lms' ); ?></p>
            <form method="post">
                <?php wp_nonce_field( 'edugo_tool_action' ); ?>
                <button type="submit" name="edugo_tool_action" value="update_student_counts" class="button button-primary">
                    <?php esc_html_e( 'Update Counts', 'edugo-lms' ); ?>
                </button>
            </form>
        </div>
    </div>

    <div class="edugo-system-info">
        <h2><?php esc_html_e( 'System Information', 'edugo-lms' ); ?></h2>
        <table class="wp-list-table widefat fixed striped">
            <tbody>
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
                    <td><?php esc_html_e( 'MySQL Version', 'edugo-lms' ); ?></td>
                    <td><?php global $wpdb; echo esc_html( $wpdb->db_version() ); ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'WooCommerce', 'edugo-lms' ); ?></td>
                    <td>
                        <?php
                        if ( class_exists( 'WooCommerce' ) ) {
                            echo esc_html( WC()->version );
                        } else {
                            esc_html_e( 'Not installed', 'edugo-lms' );
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Memory Limit', 'edugo-lms' ); ?></td>
                    <td><?php echo esc_html( WP_MEMORY_LIMIT ); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
