<?php
/**
 * Plugin Activator Class.
 *
 * @package Edugo_LMS\Core
 */

namespace Edugo_LMS\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Activator
 *
 * Handles plugin activation tasks.
 *
 * @since 1.0.0
 */
class Activator {

    /**
     * Activate the plugin.
     *
     * @since 1.0.0
     * @return void
     */
    public static function activate(): void {
        self::create_tables();
        self::create_roles();
        self::create_capabilities();
        self::create_pages();
        self::set_default_options();
        self::schedule_events();

        // Flush rewrite rules.
        flush_rewrite_rules();

        // Set activation flag.
        update_option( 'edugo_lms_activated', true );
        update_option( 'edugo_lms_version', EDUGO_LMS_VERSION );
    }

    /**
     * Create custom database tables.
     *
     * @since 1.0.0
     * @return void
     */
    private static function create_tables(): void {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // Enrollments table.
        $table_enrollments = $wpdb->prefix . 'edugo_enrollments';
        $sql_enrollments   = "CREATE TABLE $table_enrollments (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            course_id bigint(20) unsigned NOT NULL,
            status varchar(50) NOT NULL DEFAULT 'enrolled',
            enrolled_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            completed_at datetime DEFAULT NULL,
            expires_at datetime DEFAULT NULL,
            order_id bigint(20) unsigned DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY user_course (user_id, course_id),
            KEY user_id (user_id),
            KEY course_id (course_id),
            KEY status (status)
        ) $charset_collate;";

        dbDelta( $sql_enrollments );

        // Progress table.
        $table_progress = $wpdb->prefix . 'edugo_progress';
        $sql_progress   = "CREATE TABLE $table_progress (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            course_id bigint(20) unsigned NOT NULL,
            lesson_id bigint(20) unsigned NOT NULL,
            status varchar(50) NOT NULL DEFAULT 'not_started',
            progress_percent decimal(5,2) NOT NULL DEFAULT 0.00,
            started_at datetime DEFAULT NULL,
            completed_at datetime DEFAULT NULL,
            time_spent int(11) unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY user_lesson (user_id, lesson_id),
            KEY user_id (user_id),
            KEY course_id (course_id),
            KEY lesson_id (lesson_id),
            KEY status (status)
        ) $charset_collate;";

        dbDelta( $sql_progress );

        // Quiz attempts table.
        $table_quiz_attempts = $wpdb->prefix . 'edugo_quiz_attempts';
        $sql_quiz_attempts   = "CREATE TABLE $table_quiz_attempts (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            quiz_id bigint(20) unsigned NOT NULL,
            course_id bigint(20) unsigned NOT NULL,
            score decimal(5,2) NOT NULL DEFAULT 0.00,
            total_questions int(11) unsigned NOT NULL DEFAULT 0,
            correct_answers int(11) unsigned NOT NULL DEFAULT 0,
            wrong_answers int(11) unsigned NOT NULL DEFAULT 0,
            passed tinyint(1) NOT NULL DEFAULT 0,
            answers longtext DEFAULT NULL,
            started_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            completed_at datetime DEFAULT NULL,
            time_taken int(11) unsigned NOT NULL DEFAULT 0,
            attempt_number int(11) unsigned NOT NULL DEFAULT 1,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY quiz_id (quiz_id),
            KEY course_id (course_id),
            KEY passed (passed)
        ) $charset_collate;";

        dbDelta( $sql_quiz_attempts );

        // Assignment submissions table.
        $table_assignments = $wpdb->prefix . 'edugo_assignment_submissions';
        $sql_assignments   = "CREATE TABLE $table_assignments (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            assignment_id bigint(20) unsigned NOT NULL,
            course_id bigint(20) unsigned NOT NULL,
            content longtext DEFAULT NULL,
            attachments longtext DEFAULT NULL,
            grade decimal(5,2) DEFAULT NULL,
            feedback longtext DEFAULT NULL,
            status varchar(50) NOT NULL DEFAULT 'submitted',
            submitted_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            graded_at datetime DEFAULT NULL,
            graded_by bigint(20) unsigned DEFAULT NULL,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY assignment_id (assignment_id),
            KEY course_id (course_id),
            KEY status (status)
        ) $charset_collate;";

        dbDelta( $sql_assignments );

        // Earnings table.
        $table_earnings = $wpdb->prefix . 'edugo_earnings';
        $sql_earnings   = "CREATE TABLE $table_earnings (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            instructor_id bigint(20) unsigned NOT NULL,
            course_id bigint(20) unsigned NOT NULL,
            order_id bigint(20) unsigned NOT NULL,
            order_total decimal(10,2) NOT NULL DEFAULT 0.00,
            commission_rate decimal(5,2) NOT NULL DEFAULT 0.00,
            commission_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            admin_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            status varchar(50) NOT NULL DEFAULT 'pending',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            processed_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            KEY instructor_id (instructor_id),
            KEY course_id (course_id),
            KEY order_id (order_id),
            KEY status (status)
        ) $charset_collate;";

        dbDelta( $sql_earnings );

        // Withdrawals table.
        $table_withdrawals = $wpdb->prefix . 'edugo_withdrawals';
        $sql_withdrawals   = "CREATE TABLE $table_withdrawals (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            instructor_id bigint(20) unsigned NOT NULL,
            amount decimal(10,2) NOT NULL DEFAULT 0.00,
            method varchar(100) NOT NULL,
            method_data longtext DEFAULT NULL,
            status varchar(50) NOT NULL DEFAULT 'pending',
            requested_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            processed_at datetime DEFAULT NULL,
            processed_by bigint(20) unsigned DEFAULT NULL,
            notes longtext DEFAULT NULL,
            PRIMARY KEY (id),
            KEY instructor_id (instructor_id),
            KEY status (status)
        ) $charset_collate;";

        dbDelta( $sql_withdrawals );

        // Certificates table.
        $table_certificates = $wpdb->prefix . 'edugo_certificates';
        $sql_certificates   = "CREATE TABLE $table_certificates (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            course_id bigint(20) unsigned NOT NULL,
            certificate_key varchar(64) NOT NULL,
            issued_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY certificate_key (certificate_key),
            KEY user_id (user_id),
            KEY course_id (course_id)
        ) $charset_collate;";

        dbDelta( $sql_certificates );

        // Store database version.
        update_option( 'edugo_lms_db_version', '1.0.0' );
    }

    /**
     * Create custom user roles.
     *
     * @since 1.0.0
     * @return void
     */
    private static function create_roles(): void {
        // Instructor role.
        add_role(
            'edugo_instructor',
            __( 'Instructor', 'edugo-lms' ),
            array(
                'read'                     => true,
                'edit_posts'               => false,
                'delete_posts'             => false,
                'upload_files'             => true,
                // Edugo capabilities.
                'edugo_create_course'      => true,
                'edugo_edit_own_course'    => true,
                'edugo_delete_own_course'  => true,
                'edugo_view_own_earnings'  => true,
                'edugo_request_withdrawal' => true,
                'edugo_view_own_students'  => true,
                'edugo_grade_assignments'  => true,
                'edugo_view_own_reports'   => true,
            )
        );

        // Student role (if not modifying subscriber).
        add_role(
            'edugo_student',
            __( 'Student', 'edugo-lms' ),
            array(
                'read'                       => true,
                'edugo_view_enrolled_courses' => true,
                'edugo_submit_quiz'          => true,
                'edugo_submit_assignment'    => true,
                'edugo_download_certificate' => true,
            )
        );
    }

    /**
     * Create and assign capabilities.
     *
     * @since 1.0.0
     * @return void
     */
    private static function create_capabilities(): void {
        $admin = get_role( 'administrator' );

        if ( $admin ) {
            // Course capabilities.
            $admin->add_cap( 'edugo_manage_courses' );
            $admin->add_cap( 'edugo_edit_courses' );
            $admin->add_cap( 'edugo_delete_courses' );
            $admin->add_cap( 'edugo_publish_courses' );
            $admin->add_cap( 'edugo_approve_courses' );

            // Lesson capabilities.
            $admin->add_cap( 'edugo_manage_lessons' );
            $admin->add_cap( 'edugo_edit_lessons' );
            $admin->add_cap( 'edugo_delete_lessons' );

            // Quiz capabilities.
            $admin->add_cap( 'edugo_manage_quizzes' );
            $admin->add_cap( 'edugo_edit_quizzes' );
            $admin->add_cap( 'edugo_delete_quizzes' );

            // Assignment capabilities.
            $admin->add_cap( 'edugo_manage_assignments' );
            $admin->add_cap( 'edugo_grade_assignments' );

            // Enrollment capabilities.
            $admin->add_cap( 'edugo_manage_enrollments' );
            $admin->add_cap( 'edugo_enroll_students' );
            $admin->add_cap( 'edugo_unenroll_students' );

            // Earnings capabilities.
            $admin->add_cap( 'edugo_manage_earnings' );
            $admin->add_cap( 'edugo_view_all_earnings' );
            $admin->add_cap( 'edugo_process_withdrawals' );

            // Settings capability.
            $admin->add_cap( 'edugo_manage_settings' );

            // Reports capability.
            $admin->add_cap( 'edugo_view_reports' );
        }
    }

    /**
     * Create default plugin pages.
     *
     * @since 1.0.0
     * @return void
     */
    private static function create_pages(): void {
        $pages = array(
            'student-dashboard' => array(
                'title'   => __( 'Student Dashboard', 'edugo-lms' ),
                'content' => '[edugo_student_dashboard]',
                'option'  => 'edugo_student_dashboard_page',
            ),
            'instructor-dashboard' => array(
                'title'   => __( 'Instructor Dashboard', 'edugo-lms' ),
                'content' => '[edugo_instructor_dashboard]',
                'option'  => 'edugo_instructor_dashboard_page',
            ),
            'courses' => array(
                'title'   => __( 'Courses', 'edugo-lms' ),
                'content' => '[edugo_courses]',
                'option'  => 'edugo_courses_page',
            ),
            'checkout' => array(
                'title'   => __( 'Course Checkout', 'edugo-lms' ),
                'content' => '[edugo_checkout]',
                'option'  => 'edugo_checkout_page',
            ),
        );

        foreach ( $pages as $slug => $page ) {
            $page_id = get_option( $page['option'] );

            if ( ! $page_id || ! get_post( $page_id ) ) {
                $page_id = wp_insert_post(
                    array(
                        'post_title'     => $page['title'],
                        'post_content'   => $page['content'],
                        'post_status'    => 'publish',
                        'post_type'      => 'page',
                        'post_name'      => $slug,
                        'comment_status' => 'closed',
                    )
                );

                if ( $page_id && ! is_wp_error( $page_id ) ) {
                    update_option( $page['option'], $page_id );
                }
            }
        }
    }

    /**
     * Set default plugin options.
     *
     * @since 1.0.0
     * @return void
     */
    private static function set_default_options(): void {
        $defaults = array(
            'edugo_course_approval_required'  => 'yes',
            'edugo_instructor_commission'     => 70,
            'edugo_quiz_passing_grade'        => 60,
            'edugo_quiz_retake_limit'         => 3,
            'edugo_certificate_enabled'       => 'yes',
            'edugo_drip_content_enabled'      => 'yes',
            'edugo_email_notifications'       => 'yes',
            'edugo_currency'                  => 'USD',
            'edugo_minimum_withdrawal'        => 50,
            'edugo_progress_calculation'      => 'lesson_based',
        );

        foreach ( $defaults as $option => $value ) {
            if ( false === get_option( $option ) ) {
                update_option( $option, $value );
            }
        }
    }

    /**
     * Schedule cron events.
     *
     * @since 1.0.0
     * @return void
     */
    private static function schedule_events(): void {
        // Schedule daily cleanup.
        if ( ! wp_next_scheduled( 'edugo_daily_cleanup' ) ) {
            wp_schedule_event( time(), 'daily', 'edugo_daily_cleanup' );
        }

        // Schedule weekly reports.
        if ( ! wp_next_scheduled( 'edugo_weekly_reports' ) ) {
            wp_schedule_event( time(), 'weekly', 'edugo_weekly_reports' );
        }

        // Schedule drip content check.
        if ( ! wp_next_scheduled( 'edugo_check_drip_content' ) ) {
            wp_schedule_event( time(), 'hourly', 'edugo_check_drip_content' );
        }
    }
}
