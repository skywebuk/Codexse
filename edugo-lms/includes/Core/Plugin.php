<?php
/**
 * Main Plugin Class.
 *
 * @package Edugo_LMS\Core
 */

namespace Edugo_LMS\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Plugin
 *
 * The main plugin class that orchestrates all plugin functionality.
 *
 * @since 1.0.0
 */
final class Plugin {

    /**
     * Plugin instance.
     *
     * @var Plugin|null
     */
    private static ?Plugin $instance = null;

    /**
     * Loader instance.
     *
     * @var Loader
     */
    private Loader $loader;

    /**
     * Get the singleton instance.
     *
     * @since 1.0.0
     * @return Plugin
     */
    public static function get_instance(): Plugin {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor to prevent direct instantiation.
     *
     * @since 1.0.0
     */
    private function __construct() {
        $this->loader = new Loader();
        $this->define_admin_hooks();
        $this->define_frontend_hooks();
        $this->define_lms_hooks();
        $this->define_integration_hooks();
        $this->define_rest_hooks();
        $this->loader->run();
    }

    /**
     * Prevent cloning.
     *
     * @since 1.0.0
     */
    private function __clone() {}

    /**
     * Prevent unserialization.
     *
     * @since 1.0.0
     * @throws \Exception When trying to unserialize.
     */
    public function __wakeup() {
        throw new \Exception( 'Cannot unserialize singleton.' );
    }

    /**
     * Define admin-related hooks.
     *
     * @since 1.0.0
     * @return void
     */
    private function define_admin_hooks(): void {
        $admin = new \Edugo_LMS\Admin\Admin();

        $this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_menu', $admin, 'add_admin_menu' );
        $this->loader->add_action( 'admin_init', $admin, 'register_settings' );
    }

    /**
     * Define frontend-related hooks.
     *
     * @since 1.0.0
     * @return void
     */
    private function define_frontend_hooks(): void {
        $frontend = new \Edugo_LMS\Frontend\Frontend();

        $this->loader->add_action( 'wp_enqueue_scripts', $frontend, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $frontend, 'enqueue_scripts' );
        $this->loader->add_action( 'init', $frontend, 'register_shortcodes' );
    }

    /**
     * Define LMS-related hooks.
     *
     * @since 1.0.0
     * @return void
     */
    private function define_lms_hooks(): void {
        // Post Types.
        $post_types = new \Edugo_LMS\LMS\Post_Types();
        $this->loader->add_action( 'init', $post_types, 'register_post_types' );
        $this->loader->add_action( 'init', $post_types, 'register_taxonomies' );

        // Roles.
        $roles = new \Edugo_LMS\LMS\Roles();
        $this->loader->add_filter( 'editable_roles', $roles, 'filter_editable_roles' );

        // Enrollment.
        $enrollment = new \Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
        $this->loader->add_action( 'edugo_enroll_student', $enrollment, 'enroll_student', 10, 2 );
        $this->loader->add_action( 'edugo_unenroll_student', $enrollment, 'unenroll_student', 10, 2 );

        // Progress.
        $progress = new \Edugo_LMS\LMS\Progress\Progress_Manager();
        $this->loader->add_action( 'edugo_lesson_completed', $progress, 'mark_lesson_complete', 10, 2 );
        $this->loader->add_action( 'edugo_quiz_completed', $progress, 'record_quiz_attempt', 10, 3 );

        // Quiz.
        $quiz = new \Edugo_LMS\LMS\Quiz\Quiz_Manager();
        $this->loader->add_action( 'wp_ajax_edugo_submit_quiz', $quiz, 'handle_quiz_submission' );
        $this->loader->add_action( 'wp_ajax_nopriv_edugo_submit_quiz', $quiz, 'handle_quiz_submission' );

        // Assignment.
        $assignment = new \Edugo_LMS\LMS\Assignment\Assignment_Manager();
        $this->loader->add_action( 'wp_ajax_edugo_submit_assignment', $assignment, 'handle_assignment_submission' );
        $this->loader->add_action( 'wp_ajax_nopriv_edugo_submit_assignment', $assignment, 'handle_assignment_submission' );

        // Certificate.
        $certificate = new \Edugo_LMS\LMS\Certificate\Certificate_Manager();
        $this->loader->add_action( 'edugo_course_completed', $certificate, 'generate_certificate', 10, 2 );
    }

    /**
     * Define integration hooks.
     *
     * @since 1.0.0
     * @return void
     */
    private function define_integration_hooks(): void {
        // WooCommerce.
        if ( class_exists( 'WooCommerce' ) ) {
            $woocommerce = new \Edugo_LMS\Integrations\WooCommerce\WooCommerce_Integration();
            $this->loader->add_action( 'woocommerce_loaded', $woocommerce, 'init' );
            $this->loader->add_action( 'woocommerce_order_status_completed', $woocommerce, 'process_course_purchase' );
        }
    }

    /**
     * Define REST API hooks.
     *
     * @since 1.0.0
     * @return void
     */
    private function define_rest_hooks(): void {
        $rest_api = new \Edugo_LMS\REST\REST_Controller();
        $this->loader->add_action( 'rest_api_init', $rest_api, 'register_routes' );
    }

    /**
     * Get the loader instance.
     *
     * @since 1.0.0
     * @return Loader
     */
    public function get_loader(): Loader {
        return $this->loader;
    }

    /**
     * Get the plugin version.
     *
     * @since 1.0.0
     * @return string
     */
    public function get_version(): string {
        return EDUGO_LMS_VERSION;
    }
}
