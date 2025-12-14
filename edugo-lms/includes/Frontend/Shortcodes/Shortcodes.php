<?php
/**
 * Shortcodes Registration Class.
 *
 * @package Edugo_LMS\Frontend\Shortcodes
 */

namespace Edugo_LMS\Frontend\Shortcodes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Shortcodes
 *
 * Registers and handles all plugin shortcodes.
 *
 * @since 1.0.0
 */
class Shortcodes {

    /**
     * Register all shortcodes.
     *
     * @since 1.0.0
     * @return void
     */
    public function register(): void {
        // Dashboard shortcodes.
        add_shortcode( 'edugo_student_dashboard', array( $this, 'student_dashboard' ) );
        add_shortcode( 'edugo_instructor_dashboard', array( $this, 'instructor_dashboard' ) );

        // Course shortcodes.
        add_shortcode( 'edugo_courses', array( $this, 'courses_grid' ) );
        add_shortcode( 'edugo_course', array( $this, 'single_course' ) );
        add_shortcode( 'edugo_course_content', array( $this, 'course_content' ) );
        add_shortcode( 'edugo_my_courses', array( $this, 'my_courses' ) );

        // Misc shortcodes.
        add_shortcode( 'edugo_checkout', array( $this, 'checkout' ) );
        add_shortcode( 'edugo_certificate_verify', array( $this, 'certificate_verify' ) );
        add_shortcode( 'edugo_login', array( $this, 'login_form' ) );
        add_shortcode( 'edugo_register', array( $this, 'register_form' ) );
    }

    /**
     * Student dashboard shortcode.
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string Shortcode output.
     */
    public function student_dashboard( array $atts = array() ): string {
        if ( ! is_user_logged_in() ) {
            return $this->login_required_message();
        }

        $atts = shortcode_atts( array(
            'tab' => '',
        ), $atts, 'edugo_student_dashboard' );

        ob_start();
        include EDUGO_LMS_PATH . 'templates/dashboard/student-dashboard.php';
        return ob_get_clean();
    }

    /**
     * Instructor dashboard shortcode.
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string Shortcode output.
     */
    public function instructor_dashboard( array $atts = array() ): string {
        if ( ! is_user_logged_in() ) {
            return $this->login_required_message();
        }

        // Check if user is instructor.
        if ( ! \Edugo_LMS\LMS\Roles::is_instructor() ) {
            return '<div class="edugo-notice edugo-notice-error">' . esc_html__( 'You must be an instructor to access this dashboard.', 'edugo-lms' ) . '</div>';
        }

        $atts = shortcode_atts( array(
            'tab' => '',
        ), $atts, 'edugo_instructor_dashboard' );

        ob_start();
        include EDUGO_LMS_PATH . 'templates/dashboard/instructor-dashboard.php';
        return ob_get_clean();
    }

    /**
     * Courses grid shortcode.
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string Shortcode output.
     */
    public function courses_grid( array $atts = array() ): string {
        $atts = shortcode_atts( array(
            'columns'     => 3,
            'per_page'    => 12,
            'category'    => '',
            'level'       => '',
            'instructor'  => '',
            'orderby'     => 'date',
            'order'       => 'DESC',
            'featured'    => '',
            'free'        => '',
        ), $atts, 'edugo_courses' );

        $args = array(
            'post_type'      => 'edugo_course',
            'post_status'    => 'publish',
            'posts_per_page' => (int) $atts['per_page'],
            'orderby'        => $atts['orderby'],
            'order'          => $atts['order'],
            'paged'          => get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1,
        );

        // Category filter.
        if ( ! empty( $atts['category'] ) ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'edugo_course_category',
                'field'    => 'slug',
                'terms'    => explode( ',', $atts['category'] ),
            );
        }

        // Level filter.
        if ( ! empty( $atts['level'] ) ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'edugo_course_level',
                'field'    => 'slug',
                'terms'    => explode( ',', $atts['level'] ),
            );
        }

        // Instructor filter.
        if ( ! empty( $atts['instructor'] ) ) {
            $args['author__in'] = array_map( 'absint', explode( ',', $atts['instructor'] ) );
        }

        // Featured filter.
        if ( $atts['featured'] === 'yes' ) {
            $args['meta_query'][] = array(
                'key'   => '_edugo_featured',
                'value' => 'yes',
            );
        }

        // Free courses filter.
        if ( $atts['free'] === 'yes' ) {
            $args['meta_query'][] = array(
                'key'   => '_edugo_is_free',
                'value' => 'yes',
            );
        }

        $courses = new \WP_Query( $args );

        ob_start();
        include EDUGO_LMS_PATH . 'templates/course/courses-grid.php';
        return ob_get_clean();
    }

    /**
     * Single course shortcode.
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string Shortcode output.
     */
    public function single_course( array $atts = array() ): string {
        $atts = shortcode_atts( array(
            'id' => 0,
        ), $atts, 'edugo_course' );

        $course_id = (int) $atts['id'];

        if ( ! $course_id ) {
            $course_id = get_the_ID();
        }

        if ( ! $course_id || get_post_type( $course_id ) !== 'edugo_course' ) {
            return '';
        }

        $course = get_post( $course_id );

        ob_start();
        include EDUGO_LMS_PATH . 'templates/course/single-course.php';
        return ob_get_clean();
    }

    /**
     * Course content shortcode.
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string Shortcode output.
     */
    public function course_content( array $atts = array() ): string {
        $atts = shortcode_atts( array(
            'id' => 0,
        ), $atts, 'edugo_course_content' );

        $course_id = (int) $atts['id'];

        if ( ! $course_id ) {
            $course_id = get_the_ID();
        }

        // Check enrollment.
        $enrollment_manager = new \Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
        $is_enrolled = is_user_logged_in() && $enrollment_manager->is_enrolled( get_current_user_id(), $course_id );

        ob_start();
        include EDUGO_LMS_PATH . 'templates/course/course-content.php';
        return ob_get_clean();
    }

    /**
     * My courses shortcode.
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string Shortcode output.
     */
    public function my_courses( array $atts = array() ): string {
        if ( ! is_user_logged_in() ) {
            return $this->login_required_message();
        }

        $atts = shortcode_atts( array(
            'columns'  => 3,
            'per_page' => 12,
            'status'   => 'enrolled',
        ), $atts, 'edugo_my_courses' );

        $user_id = get_current_user_id();
        $enrollment_manager = new \Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
        $enrollments = $enrollment_manager->get_user_enrollments( $user_id, array( 'status' => $atts['status'] ) );

        ob_start();
        include EDUGO_LMS_PATH . 'templates/course/my-courses.php';
        return ob_get_clean();
    }

    /**
     * Checkout shortcode.
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string Shortcode output.
     */
    public function checkout( array $atts = array() ): string {
        if ( ! is_user_logged_in() ) {
            return $this->login_required_message();
        }

        // If WooCommerce is active, redirect to WC checkout.
        if ( class_exists( 'WooCommerce' ) ) {
            return do_shortcode( '[woocommerce_checkout]' );
        }

        ob_start();
        include EDUGO_LMS_PATH . 'templates/checkout.php';
        return ob_get_clean();
    }

    /**
     * Certificate verification shortcode.
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string Shortcode output.
     */
    public function certificate_verify( array $atts = array() ): string {
        $atts = shortcode_atts( array(
            'key' => '',
        ), $atts, 'edugo_certificate_verify' );

        $certificate_key = ! empty( $atts['key'] ) ? $atts['key'] : ( isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '' );

        $verification = null;

        if ( $certificate_key ) {
            $certificate_manager = new \Edugo_LMS\LMS\Certificate\Certificate_Manager();
            $verification = $certificate_manager->verify_certificate( $certificate_key );
        }

        ob_start();
        include EDUGO_LMS_PATH . 'templates/certificate/verify.php';
        return ob_get_clean();
    }

    /**
     * Login form shortcode.
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string Shortcode output.
     */
    public function login_form( array $atts = array() ): string {
        if ( is_user_logged_in() ) {
            $dashboard_page = get_option( 'edugo_student_dashboard_page' );
            return '<div class="edugo-notice">' . sprintf(
                /* translators: %s: Dashboard URL */
                esc_html__( 'You are already logged in. Go to your %s.', 'edugo-lms' ),
                '<a href="' . esc_url( get_permalink( $dashboard_page ) ) . '">' . esc_html__( 'dashboard', 'edugo-lms' ) . '</a>'
            ) . '</div>';
        }

        $atts = shortcode_atts( array(
            'redirect' => '',
        ), $atts, 'edugo_login' );

        ob_start();
        include EDUGO_LMS_PATH . 'templates/auth/login.php';
        return ob_get_clean();
    }

    /**
     * Register form shortcode.
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string Shortcode output.
     */
    public function register_form( array $atts = array() ): string {
        if ( is_user_logged_in() ) {
            return '<div class="edugo-notice">' . esc_html__( 'You are already registered and logged in.', 'edugo-lms' ) . '</div>';
        }

        if ( ! get_option( 'users_can_register' ) ) {
            return '<div class="edugo-notice edugo-notice-error">' . esc_html__( 'User registration is currently not allowed.', 'edugo-lms' ) . '</div>';
        }

        $atts = shortcode_atts( array(
            'role'     => 'edugo_student',
            'redirect' => '',
        ), $atts, 'edugo_register' );

        ob_start();
        include EDUGO_LMS_PATH . 'templates/auth/register.php';
        return ob_get_clean();
    }

    /**
     * Get login required message.
     *
     * @since 1.0.0
     * @return string HTML message.
     */
    private function login_required_message(): string {
        $login_url = wp_login_url( get_permalink() );

        return '<div class="edugo-notice edugo-notice-warning">' .
            sprintf(
                /* translators: %s: Login URL */
                esc_html__( 'Please %s to access this content.', 'edugo-lms' ),
                '<a href="' . esc_url( $login_url ) . '">' . esc_html__( 'log in', 'edugo-lms' ) . '</a>'
            ) .
        '</div>';
    }
}
