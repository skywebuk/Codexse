<?php
/**
 * REST API Controller Class.
 *
 * @package Edugo_LMS\REST
 */

namespace Edugo_LMS\REST;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class REST_Controller
 *
 * Handles REST API endpoints for the LMS.
 *
 * @since 1.0.0
 */
class REST_Controller {

    /**
     * API namespace.
     *
     * @var string
     */
    private string $namespace = 'edugo/v1';

    /**
     * Register REST routes.
     *
     * @since 1.0.0
     * @return void
     */
    public function register_routes(): void {
        // Courses.
        register_rest_route( $this->namespace, '/courses', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_courses' ),
            'permission_callback' => '__return_true',
            'args'                => $this->get_collection_params(),
        ) );

        register_rest_route( $this->namespace, '/courses/(?P<id>\d+)', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_course' ),
            'permission_callback' => '__return_true',
            'args'                => array(
                'id' => array(
                    'required'          => true,
                    'validate_callback' => array( $this, 'validate_course_id' ),
                ),
            ),
        ) );

        // Lessons.
        register_rest_route( $this->namespace, '/courses/(?P<course_id>\d+)/lessons', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_course_lessons' ),
            'permission_callback' => '__return_true',
        ) );

        // Enrollment.
        register_rest_route( $this->namespace, '/enroll', array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'enroll_user' ),
            'permission_callback' => array( $this, 'check_auth' ),
            'args'                => array(
                'course_id' => array(
                    'required'          => true,
                    'validate_callback' => array( $this, 'validate_course_id' ),
                ),
            ),
        ) );

        register_rest_route( $this->namespace, '/enrollments', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_user_enrollments' ),
            'permission_callback' => array( $this, 'check_auth' ),
        ) );

        // Progress.
        register_rest_route( $this->namespace, '/progress/(?P<course_id>\d+)', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_course_progress' ),
            'permission_callback' => array( $this, 'check_auth' ),
        ) );

        register_rest_route( $this->namespace, '/progress/lesson/(?P<lesson_id>\d+)/complete', array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'mark_lesson_complete' ),
            'permission_callback' => array( $this, 'check_auth' ),
        ) );

        // Quiz.
        register_rest_route( $this->namespace, '/quiz/(?P<quiz_id>\d+)/submit', array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'submit_quiz' ),
            'permission_callback' => array( $this, 'check_auth' ),
            'args'                => array(
                'answers' => array(
                    'required' => true,
                    'type'     => 'object',
                ),
            ),
        ) );

        register_rest_route( $this->namespace, '/quiz/(?P<quiz_id>\d+)/attempts', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_quiz_attempts' ),
            'permission_callback' => array( $this, 'check_auth' ),
        ) );

        // Certificate.
        register_rest_route( $this->namespace, '/certificate/verify/(?P<key>[a-zA-Z0-9]+)', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array( $this, 'verify_certificate' ),
            'permission_callback' => '__return_true',
        ) );

        // User.
        register_rest_route( $this->namespace, '/user/profile', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_user_profile' ),
            'permission_callback' => array( $this, 'check_auth' ),
        ) );

        // Instructor routes.
        register_rest_route( $this->namespace, '/instructor/courses', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_instructor_courses' ),
            'permission_callback' => array( $this, 'check_instructor' ),
        ) );

        register_rest_route( $this->namespace, '/instructor/students', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_instructor_students' ),
            'permission_callback' => array( $this, 'check_instructor' ),
        ) );

        register_rest_route( $this->namespace, '/instructor/earnings', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_instructor_earnings' ),
            'permission_callback' => array( $this, 'check_instructor' ),
        ) );
    }

    /**
     * Get courses.
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response Response object.
     */
    public function get_courses( \WP_REST_Request $request ): \WP_REST_Response {
        $args = array(
            'post_type'      => 'edugo_course',
            'post_status'    => 'publish',
            'posts_per_page' => $request->get_param( 'per_page' ) ?: 10,
            'paged'          => $request->get_param( 'page' ) ?: 1,
            'orderby'        => $request->get_param( 'orderby' ) ?: 'date',
            'order'          => $request->get_param( 'order' ) ?: 'DESC',
        );

        // Category filter.
        if ( $request->get_param( 'category' ) ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'edugo_course_category',
                'field'    => 'slug',
                'terms'    => $request->get_param( 'category' ),
            );
        }

        // Level filter.
        if ( $request->get_param( 'level' ) ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'edugo_course_level',
                'field'    => 'slug',
                'terms'    => $request->get_param( 'level' ),
            );
        }

        // Search.
        if ( $request->get_param( 'search' ) ) {
            $args['s'] = sanitize_text_field( $request->get_param( 'search' ) );
        }

        $query = new \WP_Query( $args );
        $courses = array();

        foreach ( $query->posts as $post ) {
            $courses[] = $this->prepare_course_response( $post );
        }

        $response = new \WP_REST_Response( $courses );
        $response->header( 'X-WP-Total', $query->found_posts );
        $response->header( 'X-WP-TotalPages', $query->max_num_pages );

        return $response;
    }

    /**
     * Get single course.
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response|\WP_Error Response or error.
     */
    public function get_course( \WP_REST_Request $request ) {
        $course_id = (int) $request->get_param( 'id' );
        $course = get_post( $course_id );

        if ( ! $course || $course->post_type !== 'edugo_course' ) {
            return new \WP_Error( 'not_found', __( 'Course not found.', 'edugo-lms' ), array( 'status' => 404 ) );
        }

        return new \WP_REST_Response( $this->prepare_course_response( $course, true ) );
    }

    /**
     * Get course lessons.
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response Response object.
     */
    public function get_course_lessons( \WP_REST_Request $request ): \WP_REST_Response {
        $course_id = (int) $request->get_param( 'course_id' );

        $lessons = get_posts( array(
            'post_type'      => 'edugo_lesson',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'     => '_edugo_course_id',
                    'value'   => $course_id,
                    'compare' => '=',
                ),
                array(
                    'key'  => '_edugo_lesson_order',
                    'type' => 'NUMERIC',
                ),
            ),
            'orderby'        => 'meta_value_num',
            'order'          => 'ASC',
        ) );

        $data = array();

        foreach ( $lessons as $lesson ) {
            $data[] = array(
                'id'        => $lesson->ID,
                'title'     => $lesson->post_title,
                'excerpt'   => get_the_excerpt( $lesson ),
                'duration'  => get_post_meta( $lesson->ID, '_edugo_duration', true ),
                'is_free'   => get_post_meta( $lesson->ID, '_edugo_is_preview', true ) === 'yes',
                'order'     => (int) get_post_meta( $lesson->ID, '_edugo_lesson_order', true ),
            );
        }

        return new \WP_REST_Response( $data );
    }

    /**
     * Enroll user in course.
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response|\WP_Error Response or error.
     */
    public function enroll_user( \WP_REST_Request $request ) {
        $course_id = (int) $request->get_param( 'course_id' );
        $user_id = get_current_user_id();

        // Check if course is free.
        $is_free = get_post_meta( $course_id, '_edugo_is_free', true ) === 'yes';

        if ( ! $is_free ) {
            return new \WP_Error( 'payment_required', __( 'This course requires payment.', 'edugo-lms' ), array( 'status' => 402 ) );
        }

        $enrollment_manager = new \Edugo_LMS\LMS\Enrollment\Enrollment_Manager();

        if ( $enrollment_manager->is_enrolled( $user_id, $course_id ) ) {
            return new \WP_Error( 'already_enrolled', __( 'You are already enrolled in this course.', 'edugo-lms' ), array( 'status' => 400 ) );
        }

        $enrollment_id = $enrollment_manager->enroll_student( $user_id, $course_id );

        if ( ! $enrollment_id ) {
            return new \WP_Error( 'enrollment_failed', __( 'Failed to enroll in course.', 'edugo-lms' ), array( 'status' => 500 ) );
        }

        return new \WP_REST_Response( array(
            'success'       => true,
            'enrollment_id' => $enrollment_id,
            'message'       => __( 'Successfully enrolled in course.', 'edugo-lms' ),
        ) );
    }

    /**
     * Get user enrollments.
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response Response object.
     */
    public function get_user_enrollments( \WP_REST_Request $request ): \WP_REST_Response {
        $user_id = get_current_user_id();
        $enrollment_manager = new \Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
        $progress_manager = new \Edugo_LMS\LMS\Progress\Progress_Manager();

        $enrollments = $enrollment_manager->get_user_enrollments( $user_id );
        $data = array();

        foreach ( $enrollments as $enrollment ) {
            $course = get_post( $enrollment->course_id );

            if ( ! $course ) {
                continue;
            }

            $progress = $progress_manager->get_course_progress( $user_id, $enrollment->course_id );

            $data[] = array(
                'enrollment_id' => $enrollment->id,
                'course_id'     => $enrollment->course_id,
                'course_title'  => $course->post_title,
                'course_image'  => get_the_post_thumbnail_url( $course, 'medium' ),
                'enrolled_at'   => $enrollment->enrolled_at,
                'status'        => $enrollment->status,
                'progress'      => $progress,
            );
        }

        return new \WP_REST_Response( $data );
    }

    /**
     * Get course progress.
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response Response object.
     */
    public function get_course_progress( \WP_REST_Request $request ): \WP_REST_Response {
        $course_id = (int) $request->get_param( 'course_id' );
        $user_id = get_current_user_id();

        $progress_manager = new \Edugo_LMS\LMS\Progress\Progress_Manager();
        $progress = $progress_manager->get_course_progress( $user_id, $course_id );

        return new \WP_REST_Response( $progress );
    }

    /**
     * Mark lesson as complete.
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response|\WP_Error Response or error.
     */
    public function mark_lesson_complete( \WP_REST_Request $request ) {
        $lesson_id = (int) $request->get_param( 'lesson_id' );
        $user_id = get_current_user_id();

        $progress_manager = new \Edugo_LMS\LMS\Progress\Progress_Manager();
        $result = $progress_manager->mark_lesson_complete( $user_id, $lesson_id );

        if ( ! $result ) {
            return new \WP_Error( 'update_failed', __( 'Failed to update progress.', 'edugo-lms' ), array( 'status' => 500 ) );
        }

        $course_id = get_post_meta( $lesson_id, '_edugo_course_id', true );
        $course_progress = $progress_manager->get_course_progress( $user_id, (int) $course_id );

        return new \WP_REST_Response( array(
            'success'         => true,
            'message'         => __( 'Lesson marked as complete.', 'edugo-lms' ),
            'course_progress' => $course_progress,
        ) );
    }

    /**
     * Submit quiz.
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response|\WP_Error Response or error.
     */
    public function submit_quiz( \WP_REST_Request $request ) {
        $quiz_id = (int) $request->get_param( 'quiz_id' );
        $answers = $request->get_param( 'answers' );
        $user_id = get_current_user_id();

        $quiz_manager = new \Edugo_LMS\LMS\Quiz\Quiz_Manager();
        $progress_manager = new \Edugo_LMS\LMS\Progress\Progress_Manager();

        // Check retake limit.
        if ( ! $progress_manager->can_retake_quiz( $user_id, $quiz_id ) ) {
            return new \WP_Error( 'retake_limit', __( 'You have reached the maximum number of attempts.', 'edugo-lms' ), array( 'status' => 403 ) );
        }

        // Grade quiz.
        $result = $quiz_manager->grade_quiz( $quiz_id, $answers );

        // Record attempt.
        $attempt_id = $progress_manager->record_quiz_attempt( $user_id, $quiz_id, array(
            'total_questions' => $result['total_questions'],
            'correct_answers' => $result['correct_answers'],
            'answers'         => $result['answers'],
            'time_taken'      => $request->get_param( 'time_taken' ) ?: 0,
        ) );

        return new \WP_REST_Response( array(
            'attempt_id'      => $attempt_id,
            'score'           => $result['score'],
            'passed'          => $result['passed'],
            'total_questions' => $result['total_questions'],
            'correct_answers' => $result['correct_answers'],
            'passing_grade'   => $result['passing_grade'],
        ) );
    }

    /**
     * Get quiz attempts.
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response Response object.
     */
    public function get_quiz_attempts( \WP_REST_Request $request ): \WP_REST_Response {
        $quiz_id = (int) $request->get_param( 'quiz_id' );
        $user_id = get_current_user_id();

        $progress_manager = new \Edugo_LMS\LMS\Progress\Progress_Manager();
        $attempts = $progress_manager->get_quiz_attempts( $user_id, $quiz_id );

        return new \WP_REST_Response( $attempts );
    }

    /**
     * Verify certificate.
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response|\WP_Error Response or error.
     */
    public function verify_certificate( \WP_REST_Request $request ) {
        $key = sanitize_text_field( $request->get_param( 'key' ) );

        $certificate_manager = new \Edugo_LMS\LMS\Certificate\Certificate_Manager();
        $verification = $certificate_manager->verify_certificate( $key );

        if ( ! $verification ) {
            return new \WP_Error( 'invalid_certificate', __( 'Certificate not found or invalid.', 'edugo-lms' ), array( 'status' => 404 ) );
        }

        return new \WP_REST_Response( $verification );
    }

    /**
     * Get user profile.
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response Response object.
     */
    public function get_user_profile( \WP_REST_Request $request ): \WP_REST_Response {
        $user = wp_get_current_user();
        $enrollment_manager = new \Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
        $certificate_manager = new \Edugo_LMS\LMS\Certificate\Certificate_Manager();

        $data = array(
            'id'              => $user->ID,
            'display_name'    => $user->display_name,
            'email'           => $user->user_email,
            'avatar'          => get_avatar_url( $user->ID ),
            'role'            => \Edugo_LMS\LMS\Roles::get_user_display_role( $user->ID ),
            'is_instructor'   => \Edugo_LMS\LMS\Roles::is_instructor( $user->ID ),
            'enrolled_count'  => count( $enrollment_manager->get_enrolled_course_ids( $user->ID ) ),
            'certificate_count' => count( $certificate_manager->get_user_certificates( $user->ID ) ),
        );

        return new \WP_REST_Response( $data );
    }

    /**
     * Get instructor courses.
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response Response object.
     */
    public function get_instructor_courses( \WP_REST_Request $request ): \WP_REST_Response {
        $courses = get_posts( array(
            'post_type'      => 'edugo_course',
            'author'         => get_current_user_id(),
            'posts_per_page' => -1,
            'post_status'    => array( 'publish', 'draft', 'pending' ),
        ) );

        $data = array();

        foreach ( $courses as $course ) {
            $data[] = $this->prepare_course_response( $course );
        }

        return new \WP_REST_Response( $data );
    }

    /**
     * Get instructor students.
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response Response object.
     */
    public function get_instructor_students( \WP_REST_Request $request ): \WP_REST_Response {
        global $wpdb;

        $instructor_id = get_current_user_id();

        // Get all students enrolled in instructor's courses.
        $students = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT DISTINCT e.user_id, e.course_id, e.enrolled_at
                FROM {$wpdb->prefix}edugo_enrollments e
                INNER JOIN {$wpdb->posts} p ON e.course_id = p.ID
                WHERE p.post_author = %d
                ORDER BY e.enrolled_at DESC",
                $instructor_id
            )
        );

        $data = array();

        foreach ( $students as $student ) {
            $user = get_userdata( $student->user_id );
            $course = get_post( $student->course_id );

            if ( ! $user || ! $course ) {
                continue;
            }

            $data[] = array(
                'user_id'      => $student->user_id,
                'name'         => $user->display_name,
                'email'        => $user->user_email,
                'avatar'       => get_avatar_url( $student->user_id ),
                'course_id'    => $student->course_id,
                'course_title' => $course->post_title,
                'enrolled_at'  => $student->enrolled_at,
            );
        }

        return new \WP_REST_Response( $data );
    }

    /**
     * Get instructor earnings.
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response Response object.
     */
    public function get_instructor_earnings( \WP_REST_Request $request ): \WP_REST_Response {
        global $wpdb;

        $instructor_id = get_current_user_id();

        $earnings = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}edugo_earnings
                WHERE instructor_id = %d
                ORDER BY created_at DESC",
                $instructor_id
            )
        );

        $total_pending = 0;
        $total_completed = 0;

        foreach ( $earnings as $earning ) {
            if ( $earning->status === 'pending' ) {
                $total_pending += (float) $earning->commission_amount;
            } else {
                $total_completed += (float) $earning->commission_amount;
            }
        }

        return new \WP_REST_Response( array(
            'earnings'        => $earnings,
            'total_pending'   => $total_pending,
            'total_completed' => $total_completed,
            'total_earnings'  => $total_pending + $total_completed,
        ) );
    }

    /**
     * Prepare course response.
     *
     * @since 1.0.0
     * @param \WP_Post $course      Course post.
     * @param bool     $full_detail Include full details.
     * @return array Course data.
     */
    private function prepare_course_response( \WP_Post $course, bool $full_detail = false ): array {
        $enrollment_manager = new \Edugo_LMS\LMS\Enrollment\Enrollment_Manager();

        $data = array(
            'id'             => $course->ID,
            'title'          => $course->post_title,
            'slug'           => $course->post_name,
            'excerpt'        => get_the_excerpt( $course ),
            'thumbnail'      => get_the_post_thumbnail_url( $course, 'medium' ),
            'author'         => array(
                'id'     => $course->post_author,
                'name'   => get_the_author_meta( 'display_name', $course->post_author ),
                'avatar' => get_avatar_url( $course->post_author ),
            ),
            'categories'     => wp_get_post_terms( $course->ID, 'edugo_course_category', array( 'fields' => 'names' ) ),
            'level'          => wp_get_post_terms( $course->ID, 'edugo_course_level', array( 'fields' => 'names' ) ),
            'student_count'  => $enrollment_manager->get_enrollment_count( $course->ID ),
            'is_free'        => get_post_meta( $course->ID, '_edugo_is_free', true ) === 'yes',
            'price'          => get_post_meta( $course->ID, '_edugo_price', true ),
            'duration'       => get_post_meta( $course->ID, '_edugo_duration', true ),
            'created_at'     => $course->post_date,
            'updated_at'     => $course->post_modified,
        );

        if ( $full_detail ) {
            $data['content'] = apply_filters( 'the_content', $course->post_content );
            $data['requirements'] = get_post_meta( $course->ID, '_edugo_requirements', true );
            $data['what_will_learn'] = get_post_meta( $course->ID, '_edugo_what_will_learn', true );
            $data['target_audience'] = get_post_meta( $course->ID, '_edugo_target_audience', true );
        }

        return $data;
    }

    /**
     * Get collection parameters.
     *
     * @since 1.0.0
     * @return array Collection parameters.
     */
    private function get_collection_params(): array {
        return array(
            'page'     => array(
                'default'           => 1,
                'sanitize_callback' => 'absint',
            ),
            'per_page' => array(
                'default'           => 10,
                'sanitize_callback' => 'absint',
            ),
            'search'   => array(
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'category' => array(
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'level'    => array(
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'orderby'  => array(
                'default'           => 'date',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'order'    => array(
                'default'           => 'DESC',
                'sanitize_callback' => 'sanitize_text_field',
            ),
        );
    }

    /**
     * Validate course ID.
     *
     * @since 1.0.0
     * @param mixed $value Value to validate.
     * @return bool True if valid.
     */
    public function validate_course_id( $value ): bool {
        $course = get_post( (int) $value );
        return $course && $course->post_type === 'edugo_course';
    }

    /**
     * Check if user is authenticated.
     *
     * @since 1.0.0
     * @return bool|\WP_Error True if authenticated, error otherwise.
     */
    public function check_auth() {
        if ( ! is_user_logged_in() ) {
            return new \WP_Error( 'unauthorized', __( 'You must be logged in.', 'edugo-lms' ), array( 'status' => 401 ) );
        }
        return true;
    }

    /**
     * Check if user is instructor.
     *
     * @since 1.0.0
     * @return bool|\WP_Error True if instructor, error otherwise.
     */
    public function check_instructor() {
        if ( ! is_user_logged_in() ) {
            return new \WP_Error( 'unauthorized', __( 'You must be logged in.', 'edugo-lms' ), array( 'status' => 401 ) );
        }

        if ( ! \Edugo_LMS\LMS\Roles::is_instructor() ) {
            return new \WP_Error( 'forbidden', __( 'You must be an instructor.', 'edugo-lms' ), array( 'status' => 403 ) );
        }

        return true;
    }
}
