<?php
/**
 * Frontend Class.
 *
 * @package Edugo_LMS\Frontend
 */

namespace Edugo_LMS\Frontend;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Frontend
 *
 * Handles all frontend functionality.
 *
 * @since 1.0.0
 */
class Frontend {

    /**
     * Enqueue frontend styles.
     *
     * @since 1.0.0
     * @return void
     */
    public function enqueue_styles(): void {
        wp_enqueue_style(
            'edugo-frontend',
            EDUGO_LMS_URL . 'assets/css/frontend.css',
            array(),
            EDUGO_LMS_VERSION
        );

        // Dashboard styles.
        if ( $this->is_dashboard_page() ) {
            wp_enqueue_style(
                'edugo-dashboard',
                EDUGO_LMS_URL . 'assets/css/dashboard.css',
                array( 'edugo-frontend' ),
                EDUGO_LMS_VERSION
            );
        }
    }

    /**
     * Enqueue frontend scripts.
     *
     * @since 1.0.0
     * @return void
     */
    public function enqueue_scripts(): void {
        wp_enqueue_script(
            'edugo-frontend',
            EDUGO_LMS_URL . 'assets/js/frontend.js',
            array( 'jquery' ),
            EDUGO_LMS_VERSION,
            true
        );

        wp_localize_script(
            'edugo-frontend',
            'edugoFrontend',
            array(
                'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
                'restUrl'  => rest_url( 'edugo/v1/' ),
                'nonce'    => wp_create_nonce( 'edugo_frontend_nonce' ),
                'restNonce' => wp_create_nonce( 'wp_rest' ),
                'userId'   => get_current_user_id(),
                'i18n'     => array(
                    'loading'          => __( 'Loading...', 'edugo-lms' ),
                    'error'            => __( 'An error occurred.', 'edugo-lms' ),
                    'lessonCompleted'  => __( 'Lesson completed!', 'edugo-lms' ),
                    'quizSubmitted'    => __( 'Quiz submitted!', 'edugo-lms' ),
                    'confirmSubmit'    => __( 'Are you sure you want to submit?', 'edugo-lms' ),
                    'timeWarning'      => __( 'You have less than 5 minutes remaining!', 'edugo-lms' ),
                    'markComplete'     => __( 'Mark Complete', 'edugo-lms' ),
                    'startCourse'      => __( 'Start Course', 'edugo-lms' ),
                    'enrollNow'        => __( 'Enroll Now', 'edugo-lms' ),
                    'submitQuiz'       => __( 'Submit Quiz', 'edugo-lms' ),
                    'congratulations'  => __( 'Congratulations!', 'edugo-lms' ),
                    'keepLearning'     => __( 'Keep Learning!', 'edugo-lms' ),
                    'yourScore'        => __( 'Your Score', 'edugo-lms' ),
                    'correctAnswers'   => __( 'Correct Answers', 'edugo-lms' ),
                    'passingGrade'     => __( 'Passing Grade', 'edugo-lms' ),
                    'viewDetails'      => __( 'View Details', 'edugo-lms' ),
                ),
            )
        );

        // Quiz scripts.
        if ( is_singular( 'edugo_quiz' ) ) {
            wp_enqueue_script(
                'edugo-quiz',
                EDUGO_LMS_URL . 'assets/js/quiz.js',
                array( 'edugo-frontend' ),
                EDUGO_LMS_VERSION,
                true
            );
        }
    }

    /**
     * Register shortcodes.
     *
     * @since 1.0.0
     * @return void
     */
    public function register_shortcodes(): void {
        $shortcodes = new Shortcodes\Shortcodes();
        $shortcodes->register();
    }

    /**
     * Check if current page is a dashboard page.
     *
     * @since 1.0.0
     * @return bool True if dashboard page, false otherwise.
     */
    private function is_dashboard_page(): bool {
        $dashboard_pages = array(
            (int) get_option( 'edugo_student_dashboard_page' ),
            (int) get_option( 'edugo_instructor_dashboard_page' ),
        );

        return is_page( $dashboard_pages );
    }
}
