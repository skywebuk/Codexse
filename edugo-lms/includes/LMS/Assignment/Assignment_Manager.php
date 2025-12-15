<?php
/**
 * Assignment Manager Class.
 *
 * @package Edugo_LMS\LMS\Assignment
 */

namespace Edugo_LMS\LMS\Assignment;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Assignment_Manager
 *
 * Handles assignment submissions and grading.
 *
 * @since 1.0.0
 */
class Assignment_Manager {

    /**
     * Submissions table name.
     *
     * @var string
     */
    private string $table;

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'edugo_assignment_submissions';
    }

    /**
     * Handle assignment submission via AJAX.
     *
     * @since 1.0.0
     * @return void
     */
    public function handle_assignment_submission(): void {
        // Verify nonce.
        if ( ! check_ajax_referer( 'edugo_frontend_nonce', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed.', 'edugo-lms' ) ) );
        }

        // Check user is logged in.
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => __( 'You must be logged in to submit an assignment.', 'edugo-lms' ) ) );
        }

        $user_id = get_current_user_id();
        $assignment_id = isset( $_POST['assignment_id'] ) ? absint( $_POST['assignment_id'] ) : 0;
        $content = isset( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';

        if ( ! $assignment_id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid assignment.', 'edugo-lms' ) ) );
        }

        // Check if already submitted.
        $existing = $this->get_submission( $user_id, $assignment_id );
        if ( $existing && $existing->status !== 'draft' ) {
            wp_send_json_error( array( 'message' => __( 'You have already submitted this assignment.', 'edugo-lms' ) ) );
        }

        // Handle file uploads.
        $attachments = $this->handle_file_uploads();

        // Submit assignment.
        $submission_id = $this->submit_assignment( $user_id, $assignment_id, array(
            'content'     => $content,
            'attachments' => $attachments,
        ) );

        if ( ! $submission_id ) {
            wp_send_json_error( array( 'message' => __( 'Failed to submit assignment.', 'edugo-lms' ) ) );
        }

        wp_send_json_success( array(
            'submission_id' => $submission_id,
            'message'       => __( 'Assignment submitted successfully!', 'edugo-lms' ),
        ) );
    }

    /**
     * Submit an assignment.
     *
     * @since 1.0.0
     * @param int   $user_id       The user ID.
     * @param int   $assignment_id The assignment ID.
     * @param array $data          Submission data.
     * @return int|false Submission ID on success, false on failure.
     */
    public function submit_assignment( int $user_id, int $assignment_id, array $data ) {
        global $wpdb;

        $course_id = get_post_meta( $assignment_id, '_edugo_course_id', true );

        $insert_data = array(
            'user_id'       => $user_id,
            'assignment_id' => $assignment_id,
            'course_id'     => (int) $course_id,
            'content'       => $data['content'] ?? '',
            'attachments'   => wp_json_encode( $data['attachments'] ?? array() ),
            'status'        => 'submitted',
            'submitted_at'  => current_time( 'mysql' ),
        );

        // Check for existing draft.
        $existing = $this->get_submission( $user_id, $assignment_id );

        if ( $existing ) {
            $result = $wpdb->update(
                $this->table,
                $insert_data,
                array( 'id' => $existing->id ),
                array( '%d', '%d', '%d', '%s', '%s', '%s', '%s' ),
                array( '%d' )
            );

            if ( $result !== false ) {
                $submission_id = $existing->id;
            } else {
                return false;
            }
        } else {
            $result = $wpdb->insert(
                $this->table,
                $insert_data,
                array( '%d', '%d', '%d', '%s', '%s', '%s', '%s' )
            );

            if ( ! $result ) {
                return false;
            }

            $submission_id = $wpdb->insert_id;
        }

        /**
         * Fires after an assignment is submitted.
         *
         * @since 1.0.0
         * @param int $submission_id  The submission ID.
         * @param int $user_id        The user ID.
         * @param int $assignment_id  The assignment ID.
         */
        do_action( 'edugo_assignment_submitted', $submission_id, $user_id, $assignment_id );

        return $submission_id;
    }

    /**
     * Grade an assignment.
     *
     * @since 1.0.0
     * @param int   $submission_id The submission ID.
     * @param float $grade         The grade (0-100).
     * @param string $feedback     Optional feedback.
     * @return bool True on success, false on failure.
     */
    public function grade_assignment( int $submission_id, float $grade, string $feedback = '' ): bool {
        global $wpdb;

        $grade = max( 0, min( 100, $grade ) );

        $result = $wpdb->update(
            $this->table,
            array(
                'grade'     => $grade,
                'feedback'  => $feedback,
                'status'    => 'graded',
                'graded_at' => current_time( 'mysql' ),
                'graded_by' => get_current_user_id(),
            ),
            array( 'id' => $submission_id ),
            array( '%f', '%s', '%s', '%s', '%d' ),
            array( '%d' )
        );

        if ( $result !== false ) {
            $submission = $this->get_submission_by_id( $submission_id );

            /**
             * Fires after an assignment is graded.
             *
             * @since 1.0.0
             * @param int    $submission_id The submission ID.
             * @param float  $grade         The grade.
             * @param object $submission    The submission object.
             */
            do_action( 'edugo_assignment_graded', $submission_id, $grade, $submission );

            return true;
        }

        return false;
    }

    /**
     * Get a submission.
     *
     * @since 1.0.0
     * @param int $user_id       The user ID.
     * @param int $assignment_id The assignment ID.
     * @return object|null Submission object or null.
     */
    public function get_submission( int $user_id, int $assignment_id ): ?object {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE user_id = %d AND assignment_id = %d ORDER BY id DESC LIMIT 1",
                $user_id,
                $assignment_id
            )
        );
    }

    /**
     * Get a submission by ID.
     *
     * @since 1.0.0
     * @param int $submission_id The submission ID.
     * @return object|null Submission object or null.
     */
    public function get_submission_by_id( int $submission_id ): ?object {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE id = %d",
                $submission_id
            )
        );
    }

    /**
     * Get submissions for an assignment.
     *
     * @since 1.0.0
     * @param int   $assignment_id The assignment ID.
     * @param array $args          Optional query arguments.
     * @return array Array of submissions.
     */
    public function get_assignment_submissions( int $assignment_id, array $args = array() ): array {
        global $wpdb;

        $defaults = array(
            'status'  => '',
            'orderby' => 'submitted_at',
            'order'   => 'DESC',
            'limit'   => -1,
            'offset'  => 0,
        );

        $args = wp_parse_args( $args, $defaults );

        $sql = "SELECT * FROM {$this->table} WHERE assignment_id = %d";
        $params = array( $assignment_id );

        if ( ! empty( $args['status'] ) ) {
            $sql .= ' AND status = %s';
            $params[] = $args['status'];
        }

        $sql .= " ORDER BY {$args['orderby']} {$args['order']}";

        if ( $args['limit'] > 0 ) {
            $sql .= ' LIMIT %d OFFSET %d';
            $params[] = $args['limit'];
            $params[] = $args['offset'];
        }

        return $wpdb->get_results( $wpdb->prepare( $sql, ...$params ) );
    }

    /**
     * Get user submissions.
     *
     * @since 1.0.0
     * @param int   $user_id The user ID.
     * @param array $args    Optional query arguments.
     * @return array Array of submissions.
     */
    public function get_user_submissions( int $user_id, array $args = array() ): array {
        global $wpdb;

        $defaults = array(
            'status'  => '',
            'orderby' => 'submitted_at',
            'order'   => 'DESC',
        );

        $args = wp_parse_args( $args, $defaults );

        $sql = "SELECT * FROM {$this->table} WHERE user_id = %d";
        $params = array( $user_id );

        if ( ! empty( $args['status'] ) ) {
            $sql .= ' AND status = %s';
            $params[] = $args['status'];
        }

        $sql .= " ORDER BY {$args['orderby']} {$args['order']}";

        return $wpdb->get_results( $wpdb->prepare( $sql, ...$params ) );
    }

    /**
     * Get pending submissions for instructor.
     *
     * @since 1.0.0
     * @param int $instructor_id The instructor ID.
     * @return array Array of submissions.
     */
    public function get_pending_submissions_for_instructor( int $instructor_id ): array {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT s.* FROM {$this->table} s
                INNER JOIN {$wpdb->posts} p ON s.assignment_id = p.ID
                WHERE p.post_author = %d AND s.status = 'submitted'
                ORDER BY s.submitted_at ASC",
                $instructor_id
            )
        );
    }

    /**
     * Handle file uploads.
     *
     * @since 1.0.0
     * @return array Array of attachment IDs.
     */
    private function handle_file_uploads(): array {
        if ( empty( $_FILES['attachments'] ) ) {
            return array();
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attachments = array();
        $files = $_FILES['attachments'];

        // Handle multiple files.
        if ( is_array( $files['name'] ) ) {
            $file_count = count( $files['name'] );

            for ( $i = 0; $i < $file_count; $i++ ) {
                if ( $files['error'][ $i ] !== UPLOAD_ERR_OK ) {
                    continue;
                }

                // Validate file type.
                if ( ! $this->is_allowed_file_type( $files['name'][ $i ] ) ) {
                    continue;
                }

                // Validate file size.
                if ( ! $this->is_allowed_file_size( $files['size'][ $i ] ) ) {
                    continue;
                }

                $file = array(
                    'name'     => $files['name'][ $i ],
                    'type'     => $files['type'][ $i ],
                    'tmp_name' => $files['tmp_name'][ $i ],
                    'error'    => $files['error'][ $i ],
                    'size'     => $files['size'][ $i ],
                );

                $_FILES['upload_file'] = $file;
                $attachment_id = media_handle_upload( 'upload_file', 0 );

                if ( ! is_wp_error( $attachment_id ) ) {
                    $attachments[] = $attachment_id;
                }
            }
        }

        return $attachments;
    }

    /**
     * Check if file type is allowed.
     *
     * @since 1.0.0
     * @param string $filename The filename.
     * @return bool True if allowed, false otherwise.
     */
    private function is_allowed_file_type( string $filename ): bool {
        $allowed = array( 'pdf', 'doc', 'docx', 'txt', 'zip', 'jpg', 'jpeg', 'png', 'gif' );

        /**
         * Filters allowed file types for assignment uploads.
         *
         * @since 1.0.0
         * @param array $allowed Array of allowed extensions.
         */
        $allowed = apply_filters( 'edugo_assignment_allowed_file_types', $allowed );

        $extension = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );

        return in_array( $extension, $allowed, true );
    }

    /**
     * Check if file size is allowed.
     *
     * @since 1.0.0
     * @param int $size File size in bytes.
     * @return bool True if allowed, false otherwise.
     */
    private function is_allowed_file_size( int $size ): bool {
        $max_size = 10 * 1024 * 1024; // 10MB default.

        /**
         * Filters maximum file size for assignment uploads.
         *
         * @since 1.0.0
         * @param int $max_size Maximum size in bytes.
         */
        $max_size = apply_filters( 'edugo_assignment_max_file_size', $max_size );

        return $size <= $max_size;
    }

    /**
     * Get assignment settings.
     *
     * @since 1.0.0
     * @param int $assignment_id The assignment ID.
     * @return array Assignment settings.
     */
    public function get_assignment_settings( int $assignment_id ): array {
        return array(
            'due_date'        => get_post_meta( $assignment_id, '_edugo_due_date', true ),
            'total_marks'     => (int) get_post_meta( $assignment_id, '_edugo_total_marks', true ) ?: 100,
            'passing_marks'   => (int) get_post_meta( $assignment_id, '_edugo_passing_marks', true ) ?: 50,
            'allow_late'      => (bool) get_post_meta( $assignment_id, '_edugo_allow_late_submission', true ),
            'file_required'   => (bool) get_post_meta( $assignment_id, '_edugo_file_required', true ),
            'max_file_size'   => (int) get_post_meta( $assignment_id, '_edugo_max_file_size', true ) ?: 10,
            'allowed_formats' => get_post_meta( $assignment_id, '_edugo_allowed_formats', true ) ?: array( 'pdf', 'doc', 'docx' ),
        );
    }
}
