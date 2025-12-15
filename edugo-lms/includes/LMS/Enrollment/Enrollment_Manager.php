<?php
/**
 * Enrollment Manager Class.
 *
 * @package Edugo_LMS\LMS\Enrollment
 */

namespace Edugo_LMS\LMS\Enrollment;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Enrollment_Manager
 *
 * Handles student enrollments in courses.
 *
 * @since 1.0.0
 */
class Enrollment_Manager {

    /**
     * Database table name.
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
        $this->table = $wpdb->prefix . 'edugo_enrollments';
    }

    /**
     * Enroll a student in a course.
     *
     * @since 1.0.0
     * @param int      $user_id   The user ID.
     * @param int      $course_id The course ID.
     * @param int|null $order_id  Optional. WooCommerce order ID.
     * @return int|false Enrollment ID on success, false on failure.
     */
    public function enroll_student( int $user_id, int $course_id, ?int $order_id = null ) {
        global $wpdb;

        // Check if already enrolled.
        if ( $this->is_enrolled( $user_id, $course_id ) ) {
            return $this->get_enrollment_id( $user_id, $course_id );
        }

        // Validate course exists.
        if ( ! get_post( $course_id ) || get_post_type( $course_id ) !== 'edugo_course' ) {
            return false;
        }

        // Validate user exists.
        if ( ! get_user_by( 'ID', $user_id ) ) {
            return false;
        }

        $data = array(
            'user_id'     => $user_id,
            'course_id'   => $course_id,
            'status'      => 'enrolled',
            'enrolled_at' => current_time( 'mysql' ),
            'order_id'    => $order_id,
        );

        $result = $wpdb->insert( $this->table, $data, array( '%d', '%d', '%s', '%s', '%d' ) );

        if ( ! $result ) {
            return false;
        }

        $enrollment_id = $wpdb->insert_id;

        // Update course meta.
        $this->update_course_student_count( $course_id );

        /**
         * Fires after a student is enrolled in a course.
         *
         * @since 1.0.0
         * @param int $user_id       The user ID.
         * @param int $course_id     The course ID.
         * @param int $enrollment_id The enrollment ID.
         */
        do_action( 'edugo_student_enrolled', $user_id, $course_id, $enrollment_id );

        return $enrollment_id;
    }

    /**
     * Unenroll a student from a course.
     *
     * @since 1.0.0
     * @param int $user_id   The user ID.
     * @param int $course_id The course ID.
     * @return bool True on success, false on failure.
     */
    public function unenroll_student( int $user_id, int $course_id ): bool {
        global $wpdb;

        if ( ! $this->is_enrolled( $user_id, $course_id ) ) {
            return false;
        }

        $result = $wpdb->delete(
            $this->table,
            array(
                'user_id'   => $user_id,
                'course_id' => $course_id,
            ),
            array( '%d', '%d' )
        );

        if ( ! $result ) {
            return false;
        }

        // Update course meta.
        $this->update_course_student_count( $course_id );

        /**
         * Fires after a student is unenrolled from a course.
         *
         * @since 1.0.0
         * @param int $user_id   The user ID.
         * @param int $course_id The course ID.
         */
        do_action( 'edugo_student_unenrolled', $user_id, $course_id );

        return true;
    }

    /**
     * Check if a user is enrolled in a course.
     *
     * @since 1.0.0
     * @param int $user_id   The user ID.
     * @param int $course_id The course ID.
     * @return bool True if enrolled, false otherwise.
     */
    public function is_enrolled( int $user_id, int $course_id ): bool {
        global $wpdb;

        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM {$this->table} WHERE user_id = %d AND course_id = %d AND status = 'enrolled'",
                $user_id,
                $course_id
            )
        );

        return ! empty( $result );
    }

    /**
     * Get enrollment ID.
     *
     * @since 1.0.0
     * @param int $user_id   The user ID.
     * @param int $course_id The course ID.
     * @return int|null Enrollment ID or null.
     */
    public function get_enrollment_id( int $user_id, int $course_id ): ?int {
        global $wpdb;

        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM {$this->table} WHERE user_id = %d AND course_id = %d",
                $user_id,
                $course_id
            )
        );

        return $result ? (int) $result : null;
    }

    /**
     * Get enrollment data.
     *
     * @since 1.0.0
     * @param int $enrollment_id The enrollment ID.
     * @return object|null Enrollment data or null.
     */
    public function get_enrollment( int $enrollment_id ): ?object {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE id = %d",
                $enrollment_id
            )
        );
    }

    /**
     * Get user enrollments.
     *
     * @since 1.0.0
     * @param int   $user_id The user ID.
     * @param array $args    Optional. Query arguments.
     * @return array Array of enrollments.
     */
    public function get_user_enrollments( int $user_id, array $args = array() ): array {
        global $wpdb;

        $defaults = array(
            'status'  => 'enrolled',
            'orderby' => 'enrolled_at',
            'order'   => 'DESC',
            'limit'   => -1,
            'offset'  => 0,
        );

        $args = wp_parse_args( $args, $defaults );

        // Whitelist allowed orderby columns to prevent SQL injection.
        $allowed_orderby = array( 'id', 'user_id', 'course_id', 'status', 'enrolled_at', 'completed_at' );
        $orderby = in_array( $args['orderby'], $allowed_orderby, true ) ? $args['orderby'] : 'enrolled_at';

        // Whitelist allowed order directions.
        $order = strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC';

        $sql = "SELECT * FROM {$this->table} WHERE user_id = %d";
        $params = array( $user_id );

        if ( ! empty( $args['status'] ) ) {
            $sql .= ' AND status = %s';
            $params[] = $args['status'];
        }

        $sql .= " ORDER BY {$orderby} {$order}";

        if ( $args['limit'] > 0 ) {
            $sql .= " LIMIT %d OFFSET %d";
            $params[] = $args['limit'];
            $params[] = $args['offset'];
        }

        return $wpdb->get_results( $wpdb->prepare( $sql, ...$params ) );
    }

    /**
     * Get course enrollments.
     *
     * @since 1.0.0
     * @param int   $course_id The course ID.
     * @param array $args      Optional. Query arguments.
     * @return array Array of enrollments.
     */
    public function get_course_enrollments( int $course_id, array $args = array() ): array {
        global $wpdb;

        $defaults = array(
            'status'  => 'enrolled',
            'orderby' => 'enrolled_at',
            'order'   => 'DESC',
            'limit'   => -1,
            'offset'  => 0,
        );

        $args = wp_parse_args( $args, $defaults );

        // Whitelist allowed orderby columns to prevent SQL injection.
        $allowed_orderby = array( 'id', 'user_id', 'course_id', 'status', 'enrolled_at', 'completed_at' );
        $orderby = in_array( $args['orderby'], $allowed_orderby, true ) ? $args['orderby'] : 'enrolled_at';

        // Whitelist allowed order directions.
        $order = strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC';

        $sql = "SELECT * FROM {$this->table} WHERE course_id = %d";
        $params = array( $course_id );

        if ( ! empty( $args['status'] ) ) {
            $sql .= ' AND status = %s';
            $params[] = $args['status'];
        }

        $sql .= " ORDER BY {$orderby} {$order}";

        if ( $args['limit'] > 0 ) {
            $sql .= " LIMIT %d OFFSET %d";
            $params[] = $args['limit'];
            $params[] = $args['offset'];
        }

        return $wpdb->get_results( $wpdb->prepare( $sql, ...$params ) );
    }

    /**
     * Mark enrollment as completed.
     *
     * @since 1.0.0
     * @param int $user_id   The user ID.
     * @param int $course_id The course ID.
     * @return bool True on success, false on failure.
     */
    public function mark_completed( int $user_id, int $course_id ): bool {
        global $wpdb;

        $result = $wpdb->update(
            $this->table,
            array(
                'status'       => 'completed',
                'completed_at' => current_time( 'mysql' ),
            ),
            array(
                'user_id'   => $user_id,
                'course_id' => $course_id,
            ),
            array( '%s', '%s' ),
            array( '%d', '%d' )
        );

        if ( $result !== false ) {
            /**
             * Fires when a course is marked as completed.
             *
             * @since 1.0.0
             * @param int $user_id   The user ID.
             * @param int $course_id The course ID.
             */
            do_action( 'edugo_course_completed', $user_id, $course_id );
            return true;
        }

        return false;
    }

    /**
     * Update course student count.
     *
     * @since 1.0.0
     * @param int $course_id The course ID.
     * @return void
     */
    private function update_course_student_count( int $course_id ): void {
        global $wpdb;

        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table} WHERE course_id = %d AND status = 'enrolled'",
                $course_id
            )
        );

        update_post_meta( $course_id, '_edugo_student_count', (int) $count );
    }

    /**
     * Get enrolled courses for a user.
     *
     * @since 1.0.0
     * @param int   $user_id The user ID.
     * @param array $args    Optional. Query arguments.
     * @return array Array of course IDs.
     */
    public function get_enrolled_course_ids( int $user_id, array $args = array() ): array {
        global $wpdb;

        $defaults = array(
            'status' => 'enrolled',
        );

        $args = wp_parse_args( $args, $defaults );

        $sql = "SELECT course_id FROM {$this->table} WHERE user_id = %d";
        $params = array( $user_id );

        if ( ! empty( $args['status'] ) ) {
            $sql .= ' AND status = %s';
            $params[] = $args['status'];
        }

        return $wpdb->get_col( $wpdb->prepare( $sql, ...$params ) );
    }

    /**
     * Get enrollment count for a course.
     *
     * @since 1.0.0
     * @param int $course_id The course ID.
     * @return int Enrollment count.
     */
    public function get_enrollment_count( int $course_id ): int {
        global $wpdb;

        return (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table} WHERE course_id = %d AND status = 'enrolled'",
                $course_id
            )
        );
    }
}
