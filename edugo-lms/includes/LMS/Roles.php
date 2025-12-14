<?php
/**
 * User Roles and Capabilities Class.
 *
 * @package Edugo_LMS\LMS
 */

namespace Edugo_LMS\LMS;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Roles
 *
 * Handles custom user roles and capabilities for the LMS.
 *
 * @since 1.0.0
 */
class Roles {

    /**
     * Get all Edugo LMS capabilities.
     *
     * @since 1.0.0
     * @return array Array of capabilities.
     */
    public static function get_all_capabilities(): array {
        return array(
            // Course capabilities.
            'edugo_manage_courses'    => __( 'Manage Courses', 'edugo-lms' ),
            'edugo_create_course'     => __( 'Create Courses', 'edugo-lms' ),
            'edugo_edit_courses'      => __( 'Edit All Courses', 'edugo-lms' ),
            'edugo_edit_own_course'   => __( 'Edit Own Courses', 'edugo-lms' ),
            'edugo_delete_courses'    => __( 'Delete All Courses', 'edugo-lms' ),
            'edugo_delete_own_course' => __( 'Delete Own Courses', 'edugo-lms' ),
            'edugo_publish_courses'   => __( 'Publish Courses', 'edugo-lms' ),
            'edugo_approve_courses'   => __( 'Approve Courses', 'edugo-lms' ),

            // Lesson capabilities.
            'edugo_manage_lessons'    => __( 'Manage Lessons', 'edugo-lms' ),
            'edugo_edit_lessons'      => __( 'Edit Lessons', 'edugo-lms' ),
            'edugo_delete_lessons'    => __( 'Delete Lessons', 'edugo-lms' ),

            // Quiz capabilities.
            'edugo_manage_quizzes'    => __( 'Manage Quizzes', 'edugo-lms' ),
            'edugo_edit_quizzes'      => __( 'Edit Quizzes', 'edugo-lms' ),
            'edugo_delete_quizzes'    => __( 'Delete Quizzes', 'edugo-lms' ),

            // Assignment capabilities.
            'edugo_manage_assignments' => __( 'Manage Assignments', 'edugo-lms' ),
            'edugo_grade_assignments'  => __( 'Grade Assignments', 'edugo-lms' ),

            // Enrollment capabilities.
            'edugo_manage_enrollments' => __( 'Manage Enrollments', 'edugo-lms' ),
            'edugo_enroll_students'    => __( 'Enroll Students', 'edugo-lms' ),
            'edugo_unenroll_students'  => __( 'Unenroll Students', 'edugo-lms' ),

            // Earnings capabilities.
            'edugo_manage_earnings'     => __( 'Manage Earnings', 'edugo-lms' ),
            'edugo_view_all_earnings'   => __( 'View All Earnings', 'edugo-lms' ),
            'edugo_view_own_earnings'   => __( 'View Own Earnings', 'edugo-lms' ),
            'edugo_process_withdrawals' => __( 'Process Withdrawals', 'edugo-lms' ),
            'edugo_request_withdrawal'  => __( 'Request Withdrawal', 'edugo-lms' ),

            // Student capabilities.
            'edugo_view_enrolled_courses' => __( 'View Enrolled Courses', 'edugo-lms' ),
            'edugo_submit_quiz'           => __( 'Submit Quiz', 'edugo-lms' ),
            'edugo_submit_assignment'     => __( 'Submit Assignment', 'edugo-lms' ),
            'edugo_download_certificate'  => __( 'Download Certificate', 'edugo-lms' ),

            // Instructor capabilities.
            'edugo_view_own_students' => __( 'View Own Students', 'edugo-lms' ),
            'edugo_view_own_reports'  => __( 'View Own Reports', 'edugo-lms' ),

            // Settings capabilities.
            'edugo_manage_settings' => __( 'Manage Settings', 'edugo-lms' ),
            'edugo_view_reports'    => __( 'View Reports', 'edugo-lms' ),
        );
    }

    /**
     * Get instructor capabilities.
     *
     * @since 1.0.0
     * @return array Array of instructor capabilities.
     */
    public static function get_instructor_capabilities(): array {
        return array(
            'read'                     => true,
            'upload_files'             => true,
            'edugo_create_course'      => true,
            'edugo_edit_own_course'    => true,
            'edugo_delete_own_course'  => true,
            'edugo_view_own_earnings'  => true,
            'edugo_request_withdrawal' => true,
            'edugo_view_own_students'  => true,
            'edugo_grade_assignments'  => true,
            'edugo_view_own_reports'   => true,
        );
    }

    /**
     * Get student capabilities.
     *
     * @since 1.0.0
     * @return array Array of student capabilities.
     */
    public static function get_student_capabilities(): array {
        return array(
            'read'                        => true,
            'edugo_view_enrolled_courses' => true,
            'edugo_submit_quiz'           => true,
            'edugo_submit_assignment'     => true,
            'edugo_download_certificate'  => true,
        );
    }

    /**
     * Filter editable roles to include custom roles.
     *
     * @since 1.0.0
     * @param array $roles Array of roles.
     * @return array Modified array of roles.
     */
    public function filter_editable_roles( array $roles ): array {
        return $roles;
    }

    /**
     * Check if a user has a specific role.
     *
     * @since 1.0.0
     * @param int    $user_id The user ID.
     * @param string $role    The role to check.
     * @return bool True if user has role, false otherwise.
     */
    public static function user_has_role( int $user_id, string $role ): bool {
        $user = get_userdata( $user_id );

        if ( ! $user ) {
            return false;
        }

        return in_array( $role, (array) $user->roles, true );
    }

    /**
     * Check if user is an instructor.
     *
     * @since 1.0.0
     * @param int|null $user_id Optional. User ID. Default current user.
     * @return bool True if user is instructor, false otherwise.
     */
    public static function is_instructor( ?int $user_id = null ): bool {
        if ( null === $user_id ) {
            $user_id = get_current_user_id();
        }

        if ( ! $user_id ) {
            return false;
        }

        return self::user_has_role( $user_id, 'edugo_instructor' ) ||
               self::user_has_role( $user_id, 'administrator' );
    }

    /**
     * Check if user is a student.
     *
     * @since 1.0.0
     * @param int|null $user_id Optional. User ID. Default current user.
     * @return bool True if user is student, false otherwise.
     */
    public static function is_student( ?int $user_id = null ): bool {
        if ( null === $user_id ) {
            $user_id = get_current_user_id();
        }

        if ( ! $user_id ) {
            return false;
        }

        return self::user_has_role( $user_id, 'edugo_student' ) ||
               self::user_has_role( $user_id, 'subscriber' );
    }

    /**
     * Add instructor role to user.
     *
     * @since 1.0.0
     * @param int $user_id The user ID.
     * @return bool True on success, false on failure.
     */
    public static function make_instructor( int $user_id ): bool {
        $user = get_userdata( $user_id );

        if ( ! $user ) {
            return false;
        }

        $user->add_role( 'edugo_instructor' );

        /**
         * Fires after a user is made an instructor.
         *
         * @since 1.0.0
         * @param int $user_id The user ID.
         */
        do_action( 'edugo_user_became_instructor', $user_id );

        return true;
    }

    /**
     * Remove instructor role from user.
     *
     * @since 1.0.0
     * @param int $user_id The user ID.
     * @return bool True on success, false on failure.
     */
    public static function remove_instructor( int $user_id ): bool {
        $user = get_userdata( $user_id );

        if ( ! $user ) {
            return false;
        }

        $user->remove_role( 'edugo_instructor' );

        /**
         * Fires after a user is removed as instructor.
         *
         * @since 1.0.0
         * @param int $user_id The user ID.
         */
        do_action( 'edugo_user_removed_instructor', $user_id );

        return true;
    }

    /**
     * Get user display role.
     *
     * @since 1.0.0
     * @param int $user_id The user ID.
     * @return string The display role name.
     */
    public static function get_user_display_role( int $user_id ): string {
        if ( self::user_has_role( $user_id, 'administrator' ) ) {
            return __( 'Administrator', 'edugo-lms' );
        }

        if ( self::user_has_role( $user_id, 'edugo_instructor' ) ) {
            return __( 'Instructor', 'edugo-lms' );
        }

        if ( self::user_has_role( $user_id, 'edugo_student' ) ) {
            return __( 'Student', 'edugo-lms' );
        }

        $user = get_userdata( $user_id );
        if ( $user && ! empty( $user->roles ) ) {
            $role = reset( $user->roles );
            $wp_roles = wp_roles();
            return $wp_roles->role_names[ $role ] ?? $role;
        }

        return __( 'User', 'edugo-lms' );
    }
}
