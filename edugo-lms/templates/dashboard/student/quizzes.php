<?php
/**
 * Student Dashboard - Quiz Results Tab.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$user_id = get_current_user_id();

global $wpdb;
$quiz_table = $wpdb->prefix . 'edugo_quiz_attempts';

$attempts = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$quiz_table} WHERE user_id = %d ORDER BY completed_at DESC",
        $user_id
    )
);
?>

<div class="edugo-dashboard-header">
    <h2><?php esc_html_e( 'Quiz Results', 'edugo-lms' ); ?></h2>
</div>

<?php if ( ! empty( $attempts ) ) : ?>
    <div class="edugo-quiz-results-table">
        <table class="edugo-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Quiz', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Course', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Score', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Result', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Attempt', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Date', 'edugo-lms' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $attempts as $attempt ) :
                    $quiz = get_post( $attempt->quiz_id );
                    $course = get_post( $attempt->course_id );

                    if ( ! $quiz ) continue;
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html( $quiz->post_title ); ?></strong>
                        </td>
                        <td>
                            <?php if ( $course ) : ?>
                                <a href="<?php echo esc_url( get_permalink( $course ) ); ?>">
                                    <?php echo esc_html( $course->post_title ); ?>
                                </a>
                            <?php else : ?>
                                <span class="edugo-text-muted"><?php esc_html_e( 'N/A', 'edugo-lms' ); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="edugo-score <?php echo $attempt->passed ? 'passed' : 'failed'; ?>">
                                <?php echo esc_html( round( $attempt->score, 1 ) ); ?>%
                            </span>
                        </td>
                        <td>
                            <?php if ( $attempt->passed ) : ?>
                                <span class="edugo-badge edugo-badge-success"><?php esc_html_e( 'Passed', 'edugo-lms' ); ?></span>
                            <?php else : ?>
                                <span class="edugo-badge edugo-badge-danger"><?php esc_html_e( 'Failed', 'edugo-lms' ); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            printf(
                                /* translators: %d: Attempt number */
                                esc_html__( '#%d', 'edugo-lms' ),
                                (int) $attempt->attempt_number
                            );
                            ?>
                        </td>
                        <td>
                            <?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $attempt->completed_at ) ) ); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else : ?>
    <div class="edugo-empty-state">
        <div class="edugo-empty-icon">
            <span class="dashicons dashicons-editor-help"></span>
        </div>
        <h3><?php esc_html_e( 'No Quiz Attempts', 'edugo-lms' ); ?></h3>
        <p><?php esc_html_e( 'You have not taken any quizzes yet. Complete lessons to unlock quizzes.', 'edugo-lms' ); ?></p>
    </div>
<?php endif; ?>
