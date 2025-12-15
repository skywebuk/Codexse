<?php
/**
 * Progress Manager Class.
 *
 * @package Edugo_LMS\LMS\Progress
 */

namespace Edugo_LMS\LMS\Progress;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Progress_Manager
 *
 * Handles student progress tracking.
 *
 * @since 1.0.0
 */
class Progress_Manager {

    /**
     * Progress table name.
     *
     * @var string
     */
    private string $table;

    /**
     * Quiz attempts table name.
     *
     * @var string
     */
    private string $quiz_table;

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        global $wpdb;
        $this->table      = $wpdb->prefix . 'edugo_progress';
        $this->quiz_table = $wpdb->prefix . 'edugo_quiz_attempts';
    }

    /**
     * Mark a lesson as complete.
     *
     * @since 1.0.0
     * @param int $user_id   The user ID.
     * @param int $lesson_id The lesson ID.
     * @return bool True on success, false on failure.
     */
    public function mark_lesson_complete( int $user_id, int $lesson_id ): bool {
        global $wpdb;

        $course_id = $this->get_lesson_course_id( $lesson_id );

        if ( ! $course_id ) {
            return false;
        }

        // Check if progress record exists.
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE user_id = %d AND lesson_id = %d",
                $user_id,
                $lesson_id
            )
        );

        if ( $existing ) {
            // Update existing record.
            $result = $wpdb->update(
                $this->table,
                array(
                    'status'           => 'completed',
                    'progress_percent' => 100.00,
                    'completed_at'     => current_time( 'mysql' ),
                ),
                array(
                    'user_id'   => $user_id,
                    'lesson_id' => $lesson_id,
                ),
                array( '%s', '%f', '%s' ),
                array( '%d', '%d' )
            );
        } else {
            // Insert new record.
            $result = $wpdb->insert(
                $this->table,
                array(
                    'user_id'          => $user_id,
                    'course_id'        => $course_id,
                    'lesson_id'        => $lesson_id,
                    'status'           => 'completed',
                    'progress_percent' => 100.00,
                    'started_at'       => current_time( 'mysql' ),
                    'completed_at'     => current_time( 'mysql' ),
                ),
                array( '%d', '%d', '%d', '%s', '%f', '%s', '%s' )
            );
        }

        if ( $result !== false ) {
            /**
             * Fires when a lesson is completed.
             *
             * @since 1.0.0
             * @param int $user_id   The user ID.
             * @param int $lesson_id The lesson ID.
             * @param int $course_id The course ID.
             */
            do_action( 'edugo_lesson_complete', $user_id, $lesson_id, $course_id );

            // Check if course is completed.
            $this->check_course_completion( $user_id, $course_id );

            return true;
        }

        return false;
    }

    /**
     * Record a quiz attempt.
     *
     * @since 1.0.0
     * @param int   $user_id The user ID.
     * @param int   $quiz_id The quiz ID.
     * @param array $data    Quiz attempt data.
     * @return int|false Attempt ID on success, false on failure.
     */
    public function record_quiz_attempt( int $user_id, int $quiz_id, array $data ) {
        global $wpdb;

        $course_id = $this->get_quiz_course_id( $quiz_id );

        // Get attempt number.
        $attempt_number = $this->get_attempt_count( $user_id, $quiz_id ) + 1;

        // Calculate score.
        $total_questions = $data['total_questions'] ?? 0;
        $correct_answers = $data['correct_answers'] ?? 0;
        $score = $total_questions > 0 ? ( $correct_answers / $total_questions ) * 100 : 0;

        // Check if passed.
        $passing_grade = (float) get_option( 'edugo_quiz_passing_grade', 60 );
        $passed = $score >= $passing_grade;

        $insert_data = array(
            'user_id'         => $user_id,
            'quiz_id'         => $quiz_id,
            'course_id'       => $course_id,
            'score'           => $score,
            'total_questions' => $total_questions,
            'correct_answers' => $correct_answers,
            'wrong_answers'   => $total_questions - $correct_answers,
            'passed'          => $passed ? 1 : 0,
            'answers'         => wp_json_encode( $data['answers'] ?? array() ),
            'started_at'      => $data['started_at'] ?? current_time( 'mysql' ),
            'completed_at'    => current_time( 'mysql' ),
            'time_taken'      => $data['time_taken'] ?? 0,
            'attempt_number'  => $attempt_number,
        );

        $result = $wpdb->insert(
            $this->quiz_table,
            $insert_data,
            array( '%d', '%d', '%d', '%f', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%d' )
        );

        if ( ! $result ) {
            return false;
        }

        $attempt_id = $wpdb->insert_id;

        /**
         * Fires after a quiz attempt is recorded.
         *
         * @since 1.0.0
         * @param int   $attempt_id The attempt ID.
         * @param int   $user_id    The user ID.
         * @param int   $quiz_id    The quiz ID.
         * @param bool  $passed     Whether the quiz was passed.
         * @param float $score      The quiz score.
         */
        do_action( 'edugo_quiz_attempt_recorded', $attempt_id, $user_id, $quiz_id, $passed, $score );

        // If passed, check course completion.
        if ( $passed && $course_id ) {
            $this->check_course_completion( $user_id, $course_id );
        }

        return $attempt_id;
    }

    /**
     * Get user progress for a course.
     *
     * @since 1.0.0
     * @param int $user_id   The user ID.
     * @param int $course_id The course ID.
     * @return array Progress data.
     */
    public function get_course_progress( int $user_id, int $course_id ): array {
        global $wpdb;

        // Get total lessons.
        $total_lessons = $this->get_course_lesson_count( $course_id );

        if ( $total_lessons === 0 ) {
            return array(
                'completed_lessons' => 0,
                'total_lessons'     => 0,
                'percentage'        => 0,
                'status'            => 'not_started',
            );
        }

        // Get completed lessons.
        $completed_lessons = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table}
                WHERE user_id = %d AND course_id = %d AND status = 'completed'",
                $user_id,
                $course_id
            )
        );

        $percentage = ( $completed_lessons / $total_lessons ) * 100;

        $status = 'not_started';
        if ( $percentage >= 100 ) {
            $status = 'completed';
        } elseif ( $percentage > 0 ) {
            $status = 'in_progress';
        }

        return array(
            'completed_lessons' => $completed_lessons,
            'total_lessons'     => $total_lessons,
            'percentage'        => round( $percentage, 2 ),
            'status'            => $status,
        );
    }

    /**
     * Get lesson progress.
     *
     * @since 1.0.0
     * @param int $user_id   The user ID.
     * @param int $lesson_id The lesson ID.
     * @return object|null Progress data or null.
     */
    public function get_lesson_progress( int $user_id, int $lesson_id ): ?object {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE user_id = %d AND lesson_id = %d",
                $user_id,
                $lesson_id
            )
        );
    }

    /**
     * Check if a lesson is completed.
     *
     * @since 1.0.0
     * @param int $user_id   The user ID.
     * @param int $lesson_id The lesson ID.
     * @return bool True if completed, false otherwise.
     */
    public function is_lesson_completed( int $user_id, int $lesson_id ): bool {
        global $wpdb;

        $status = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT status FROM {$this->table} WHERE user_id = %d AND lesson_id = %d",
                $user_id,
                $lesson_id
            )
        );

        return $status === 'completed';
    }

    /**
     * Get quiz attempts for a user.
     *
     * @since 1.0.0
     * @param int $user_id The user ID.
     * @param int $quiz_id The quiz ID.
     * @return array Array of attempts.
     */
    public function get_quiz_attempts( int $user_id, int $quiz_id ): array {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->quiz_table}
                WHERE user_id = %d AND quiz_id = %d
                ORDER BY attempt_number DESC",
                $user_id,
                $quiz_id
            )
        );
    }

    /**
     * Get attempt count.
     *
     * @since 1.0.0
     * @param int $user_id The user ID.
     * @param int $quiz_id The quiz ID.
     * @return int Attempt count.
     */
    public function get_attempt_count( int $user_id, int $quiz_id ): int {
        global $wpdb;

        return (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->quiz_table} WHERE user_id = %d AND quiz_id = %d",
                $user_id,
                $quiz_id
            )
        );
    }

    /**
     * Check if user can retake quiz.
     *
     * @since 1.0.0
     * @param int $user_id The user ID.
     * @param int $quiz_id The quiz ID.
     * @return bool True if can retake, false otherwise.
     */
    public function can_retake_quiz( int $user_id, int $quiz_id ): bool {
        // Check if quiz has a specific retake limit set.
        $quiz_retake_limit = get_post_meta( $quiz_id, '_edugo_retake_limit', true );

        // If meta exists and is set (even to 0), use it; otherwise use global option.
        if ( $quiz_retake_limit !== '' && $quiz_retake_limit !== false ) {
            $retake_limit = (int) $quiz_retake_limit;
        } else {
            $retake_limit = (int) get_option( 'edugo_quiz_retake_limit', 3 );
        }

        // 0 means unlimited retakes.
        if ( $retake_limit === 0 ) {
            return true;
        }

        $attempt_count = $this->get_attempt_count( $user_id, $quiz_id );

        return $attempt_count < $retake_limit;
    }

    /**
     * Get best quiz score.
     *
     * @since 1.0.0
     * @param int $user_id The user ID.
     * @param int $quiz_id The quiz ID.
     * @return float Best score.
     */
    public function get_best_quiz_score( int $user_id, int $quiz_id ): float {
        global $wpdb;

        $score = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT MAX(score) FROM {$this->quiz_table} WHERE user_id = %d AND quiz_id = %d",
                $user_id,
                $quiz_id
            )
        );

        return (float) $score;
    }

    /**
     * Check course completion.
     *
     * @since 1.0.0
     * @param int $user_id   The user ID.
     * @param int $course_id The course ID.
     * @return bool True if completed, false otherwise.
     */
    public function check_course_completion( int $user_id, int $course_id ): bool {
        $progress = $this->get_course_progress( $user_id, $course_id );

        if ( $progress['percentage'] >= 100 ) {
            // Mark enrollment as completed.
            $enrollment_manager = new \Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
            $enrollment_manager->mark_completed( $user_id, $course_id );

            return true;
        }

        return false;
    }

    /**
     * Update time spent on lesson.
     *
     * @since 1.0.0
     * @param int $user_id   The user ID.
     * @param int $lesson_id The lesson ID.
     * @param int $seconds   Time in seconds.
     * @return bool True on success, false on failure.
     */
    public function update_time_spent( int $user_id, int $lesson_id, int $seconds ): bool {
        global $wpdb;

        $result = $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$this->table} SET time_spent = time_spent + %d WHERE user_id = %d AND lesson_id = %d",
                $seconds,
                $user_id,
                $lesson_id
            )
        );

        return $result !== false;
    }

    /**
     * Get lesson course ID.
     *
     * @since 1.0.0
     * @param int $lesson_id The lesson ID.
     * @return int|null Course ID or null.
     */
    private function get_lesson_course_id( int $lesson_id ): ?int {
        $course_id = get_post_meta( $lesson_id, '_edugo_course_id', true );
        return $course_id ? (int) $course_id : null;
    }

    /**
     * Get quiz course ID.
     *
     * @since 1.0.0
     * @param int $quiz_id The quiz ID.
     * @return int|null Course ID or null.
     */
    private function get_quiz_course_id( int $quiz_id ): ?int {
        $course_id = get_post_meta( $quiz_id, '_edugo_course_id', true );
        return $course_id ? (int) $course_id : null;
    }

    /**
     * Get course lesson count.
     *
     * @since 1.0.0
     * @param int $course_id The course ID.
     * @return int Lesson count.
     */
    private function get_course_lesson_count( int $course_id ): int {
        global $wpdb;

        return (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
                WHERE p.post_type = 'edugo_lesson'
                AND p.post_status = 'publish'
                AND pm.meta_key = '_edugo_course_id'
                AND pm.meta_value = %d",
                $course_id
            )
        );
    }
}
