<?php
/**
 * Quiz Manager Class.
 *
 * @package Edugo_LMS\LMS\Quiz
 */

namespace Edugo_LMS\LMS\Quiz;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Quiz_Manager
 *
 * Handles quiz functionality including questions, attempts, and grading.
 *
 * @since 1.0.0
 */
class Quiz_Manager {

    /**
     * Handle quiz submission via AJAX.
     *
     * @since 1.0.0
     * @return void
     */
    public function handle_quiz_submission(): void {
        // Verify nonce.
        if ( ! check_ajax_referer( 'edugo_frontend_nonce', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed.', 'edugo-lms' ) ) );
        }

        // Check user is logged in.
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => __( 'You must be logged in to submit a quiz.', 'edugo-lms' ) ) );
        }

        $user_id = get_current_user_id();
        $quiz_id = isset( $_POST['quiz_id'] ) ? absint( $_POST['quiz_id'] ) : 0;
        $answers = isset( $_POST['answers'] ) ? $this->sanitize_answers( wp_unslash( $_POST['answers'] ) ) : array();
        $started_at = isset( $_POST['started_at'] ) ? sanitize_text_field( wp_unslash( $_POST['started_at'] ) ) : '';
        $time_taken = isset( $_POST['time_taken'] ) ? absint( $_POST['time_taken'] ) : 0;

        if ( ! $quiz_id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid quiz.', 'edugo-lms' ) ) );
        }

        // Check if user can take quiz.
        $progress_manager = new \Edugo_LMS\LMS\Progress\Progress_Manager();

        if ( ! $progress_manager->can_retake_quiz( $user_id, $quiz_id ) ) {
            wp_send_json_error( array( 'message' => __( 'You have reached the maximum number of attempts for this quiz.', 'edugo-lms' ) ) );
        }

        // Grade the quiz.
        $result = $this->grade_quiz( $quiz_id, $answers );

        // Record the attempt.
        $attempt_data = array(
            'total_questions' => $result['total_questions'],
            'correct_answers' => $result['correct_answers'],
            'answers'         => $result['answers'],
            'started_at'      => $started_at,
            'time_taken'      => $time_taken,
        );

        $attempt_id = $progress_manager->record_quiz_attempt( $user_id, $quiz_id, $attempt_data );

        if ( ! $attempt_id ) {
            wp_send_json_error( array( 'message' => __( 'Failed to record quiz attempt.', 'edugo-lms' ) ) );
        }

        // Prepare response.
        $response = array(
            'attempt_id'       => $attempt_id,
            'score'            => $result['score'],
            'passed'           => $result['passed'],
            'total_questions'  => $result['total_questions'],
            'correct_answers'  => $result['correct_answers'],
            'passing_grade'    => $result['passing_grade'],
            'message'          => $result['passed']
                ? __( 'Congratulations! You passed the quiz.', 'edugo-lms' )
                : __( 'Unfortunately, you did not pass this time. Keep learning and try again!', 'edugo-lms' ),
        );

        wp_send_json_success( $response );
    }

    /**
     * Grade a quiz.
     *
     * @since 1.0.0
     * @param int   $quiz_id The quiz ID.
     * @param array $answers User answers.
     * @return array Grading results.
     */
    public function grade_quiz( int $quiz_id, array $answers ): array {
        $questions = $this->get_quiz_questions( $quiz_id );
        $total_questions = count( $questions );
        $correct_answers = 0;
        $graded_answers = array();

        foreach ( $questions as $question ) {
            $question_id = $question->ID;
            $user_answer = $answers[ $question_id ] ?? null;
            $correct_answer = $this->get_correct_answer( $question_id );
            $question_type = get_post_meta( $question_id, '_edugo_question_type', true );

            $is_correct = $this->check_answer( $question_type, $user_answer, $correct_answer );

            if ( $is_correct ) {
                $correct_answers++;
            }

            $graded_answers[ $question_id ] = array(
                'user_answer'    => $user_answer,
                'correct_answer' => $correct_answer,
                'is_correct'     => $is_correct,
            );
        }

        $score = $total_questions > 0 ? ( $correct_answers / $total_questions ) * 100 : 0;
        $passing_grade = $this->get_passing_grade( $quiz_id );
        $passed = $score >= $passing_grade;

        return array(
            'total_questions' => $total_questions,
            'correct_answers' => $correct_answers,
            'wrong_answers'   => $total_questions - $correct_answers,
            'score'           => round( $score, 2 ),
            'passing_grade'   => $passing_grade,
            'passed'          => $passed,
            'answers'         => $graded_answers,
        );
    }

    /**
     * Get quiz questions.
     *
     * @since 1.0.0
     * @param int $quiz_id The quiz ID.
     * @return array Array of question posts.
     */
    public function get_quiz_questions( int $quiz_id ): array {
        $question_ids = get_post_meta( $quiz_id, '_edugo_quiz_questions', true );

        if ( empty( $question_ids ) || ! is_array( $question_ids ) ) {
            return array();
        }

        $questions = get_posts( array(
            'post_type'      => 'edugo_question',
            'post__in'       => $question_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ) );

        return $questions;
    }

    /**
     * Get correct answer for a question.
     *
     * @since 1.0.0
     * @param int $question_id The question ID.
     * @return mixed Correct answer.
     */
    public function get_correct_answer( int $question_id ) {
        $question_type = get_post_meta( $question_id, '_edugo_question_type', true );

        switch ( $question_type ) {
            case 'multiple_choice':
            case 'true_false':
                return get_post_meta( $question_id, '_edugo_correct_option', true );

            case 'multiple_answers':
                return get_post_meta( $question_id, '_edugo_correct_options', true );

            case 'short_answer':
                return get_post_meta( $question_id, '_edugo_correct_answers', true );

            default:
                return get_post_meta( $question_id, '_edugo_correct_option', true );
        }
    }

    /**
     * Check if an answer is correct.
     *
     * @since 1.0.0
     * @param string $question_type The question type.
     * @param mixed  $user_answer   The user's answer.
     * @param mixed  $correct       The correct answer.
     * @return bool True if correct, false otherwise.
     */
    public function check_answer( string $question_type, $user_answer, $correct ): bool {
        if ( $user_answer === null || $user_answer === '' ) {
            return false;
        }

        switch ( $question_type ) {
            case 'multiple_choice':
            case 'true_false':
                return (string) $user_answer === (string) $correct;

            case 'multiple_answers':
                if ( ! is_array( $user_answer ) || ! is_array( $correct ) ) {
                    return false;
                }
                sort( $user_answer );
                sort( $correct );
                return $user_answer === $correct;

            case 'short_answer':
                $correct_answers = is_array( $correct ) ? $correct : array( $correct );
                $user_answer_lower = strtolower( trim( $user_answer ) );

                foreach ( $correct_answers as $answer ) {
                    if ( strtolower( trim( $answer ) ) === $user_answer_lower ) {
                        return true;
                    }
                }
                return false;

            default:
                return (string) $user_answer === (string) $correct;
        }
    }

    /**
     * Get passing grade for a quiz.
     *
     * @since 1.0.0
     * @param int $quiz_id The quiz ID.
     * @return float Passing grade percentage.
     */
    public function get_passing_grade( int $quiz_id ): float {
        $passing_grade = get_post_meta( $quiz_id, '_edugo_passing_grade', true );

        if ( ! $passing_grade ) {
            $passing_grade = get_option( 'edugo_quiz_passing_grade', 60 );
        }

        return (float) $passing_grade;
    }

    /**
     * Get quiz time limit.
     *
     * @since 1.0.0
     * @param int $quiz_id The quiz ID.
     * @return int Time limit in minutes (0 for no limit).
     */
    public function get_time_limit( int $quiz_id ): int {
        return (int) get_post_meta( $quiz_id, '_edugo_time_limit', true );
    }

    /**
     * Get quiz settings.
     *
     * @since 1.0.0
     * @param int $quiz_id The quiz ID.
     * @return array Quiz settings.
     */
    public function get_quiz_settings( int $quiz_id ): array {
        return array(
            'time_limit'         => $this->get_time_limit( $quiz_id ),
            'passing_grade'      => $this->get_passing_grade( $quiz_id ),
            'retake_limit'       => (int) get_post_meta( $quiz_id, '_edugo_retake_limit', true ) ?: (int) get_option( 'edugo_quiz_retake_limit', 3 ),
            'randomize'          => (bool) get_post_meta( $quiz_id, '_edugo_randomize_questions', true ),
            'show_correct'       => (bool) get_post_meta( $quiz_id, '_edugo_show_correct_answers', true ),
            'immediate_feedback' => (bool) get_post_meta( $quiz_id, '_edugo_immediate_feedback', true ),
        );
    }

    /**
     * Create a question.
     *
     * @since 1.0.0
     * @param array $data Question data.
     * @return int|WP_Error Question ID or error.
     */
    public function create_question( array $data ) {
        $post_data = array(
            'post_title'   => sanitize_text_field( $data['title'] ?? '' ),
            'post_content' => wp_kses_post( $data['content'] ?? '' ),
            'post_type'    => 'edugo_question',
            'post_status'  => 'publish',
            'post_author'  => get_current_user_id(),
        );

        $question_id = wp_insert_post( $post_data, true );

        if ( is_wp_error( $question_id ) ) {
            return $question_id;
        }

        // Save question meta.
        if ( isset( $data['type'] ) ) {
            update_post_meta( $question_id, '_edugo_question_type', sanitize_text_field( $data['type'] ) );
        }

        if ( isset( $data['options'] ) && is_array( $data['options'] ) ) {
            update_post_meta( $question_id, '_edugo_options', array_map( 'sanitize_text_field', $data['options'] ) );
        }

        if ( isset( $data['correct_option'] ) ) {
            update_post_meta( $question_id, '_edugo_correct_option', sanitize_text_field( $data['correct_option'] ) );
        }

        if ( isset( $data['correct_options'] ) && is_array( $data['correct_options'] ) ) {
            update_post_meta( $question_id, '_edugo_correct_options', array_map( 'sanitize_text_field', $data['correct_options'] ) );
        }

        if ( isset( $data['points'] ) ) {
            update_post_meta( $question_id, '_edugo_points', absint( $data['points'] ) );
        }

        if ( isset( $data['explanation'] ) ) {
            update_post_meta( $question_id, '_edugo_explanation', wp_kses_post( $data['explanation'] ) );
        }

        return $question_id;
    }

    /**
     * Add questions to a quiz.
     *
     * @since 1.0.0
     * @param int   $quiz_id      The quiz ID.
     * @param array $question_ids Array of question IDs.
     * @return bool True on success, false on failure.
     */
    public function add_questions_to_quiz( int $quiz_id, array $question_ids ): bool {
        $existing = get_post_meta( $quiz_id, '_edugo_quiz_questions', true ) ?: array();
        $question_ids = array_map( 'absint', $question_ids );
        $merged = array_unique( array_merge( $existing, $question_ids ) );

        return (bool) update_post_meta( $quiz_id, '_edugo_quiz_questions', $merged );
    }

    /**
     * Remove a question from a quiz.
     *
     * @since 1.0.0
     * @param int $quiz_id     The quiz ID.
     * @param int $question_id The question ID.
     * @return bool True on success, false on failure.
     */
    public function remove_question_from_quiz( int $quiz_id, int $question_id ): bool {
        $questions = get_post_meta( $quiz_id, '_edugo_quiz_questions', true ) ?: array();
        $key = array_search( $question_id, $questions, true );

        if ( $key !== false ) {
            unset( $questions[ $key ] );
            return (bool) update_post_meta( $quiz_id, '_edugo_quiz_questions', array_values( $questions ) );
        }

        return false;
    }

    /**
     * Sanitize answers array.
     *
     * @since 1.0.0
     * @param array $answers Raw answers.
     * @return array Sanitized answers.
     */
    private function sanitize_answers( $answers ): array {
        if ( ! is_array( $answers ) ) {
            return array();
        }

        $sanitized = array();

        foreach ( $answers as $question_id => $answer ) {
            $question_id = absint( $question_id );

            if ( is_array( $answer ) ) {
                $sanitized[ $question_id ] = array_map( 'sanitize_text_field', $answer );
            } else {
                $sanitized[ $question_id ] = sanitize_text_field( $answer );
            }
        }

        return $sanitized;
    }
}
