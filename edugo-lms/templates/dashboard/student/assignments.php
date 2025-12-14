<?php
/**
 * Student Dashboard - Assignments Tab.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$user_id = get_current_user_id();

global $wpdb;
$assignments_table = $wpdb->prefix . 'edugo_assignments';

$submissions = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$assignments_table} WHERE user_id = %d ORDER BY submitted_at DESC",
        $user_id
    )
);
?>

<div class="edugo-dashboard-header">
    <h2><?php esc_html_e( 'My Assignments', 'edugo-lms' ); ?></h2>
</div>

<?php if ( ! empty( $submissions ) ) : ?>
    <div class="edugo-assignments-list">
        <?php foreach ( $submissions as $submission ) :
            $assignment = get_post( $submission->assignment_id );
            $course = get_post( $submission->course_id );

            if ( ! $assignment ) continue;
            ?>
            <div class="edugo-assignment-card">
                <div class="edugo-assignment-header">
                    <h3 class="edugo-assignment-title"><?php echo esc_html( $assignment->post_title ); ?></h3>
                    <span class="edugo-assignment-status edugo-status-<?php echo esc_attr( $submission->status ); ?>">
                        <?php
                        $status_labels = array(
                            'pending'  => __( 'Pending Review', 'edugo-lms' ),
                            'graded'   => __( 'Graded', 'edugo-lms' ),
                            'rejected' => __( 'Needs Revision', 'edugo-lms' ),
                        );
                        echo esc_html( $status_labels[ $submission->status ] ?? $submission->status );
                        ?>
                    </span>
                </div>

                <div class="edugo-assignment-meta">
                    <?php if ( $course ) : ?>
                        <span class="edugo-assignment-course">
                            <span class="dashicons dashicons-welcome-learn-more"></span>
                            <?php echo esc_html( $course->post_title ); ?>
                        </span>
                    <?php endif; ?>

                    <span class="edugo-assignment-date">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        <?php
                        printf(
                            /* translators: %s: Submission date */
                            esc_html__( 'Submitted: %s', 'edugo-lms' ),
                            esc_html( date_i18n( get_option( 'date_format' ), strtotime( $submission->submitted_at ) ) )
                        );
                        ?>
                    </span>
                </div>

                <?php if ( $submission->status === 'graded' && $submission->grade !== null ) : ?>
                    <div class="edugo-assignment-grade">
                        <span class="edugo-grade-label"><?php esc_html_e( 'Grade:', 'edugo-lms' ); ?></span>
                        <span class="edugo-grade-value"><?php echo esc_html( $submission->grade ); ?>%</span>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $submission->feedback ) ) : ?>
                    <div class="edugo-assignment-feedback">
                        <h4><?php esc_html_e( 'Instructor Feedback', 'edugo-lms' ); ?></h4>
                        <p><?php echo wp_kses_post( $submission->feedback ); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php else : ?>
    <div class="edugo-empty-state">
        <div class="edugo-empty-icon">
            <span class="dashicons dashicons-media-document"></span>
        </div>
        <h3><?php esc_html_e( 'No Assignments', 'edugo-lms' ); ?></h3>
        <p><?php esc_html_e( 'You have not submitted any assignments yet.', 'edugo-lms' ); ?></p>
    </div>
<?php endif; ?>
