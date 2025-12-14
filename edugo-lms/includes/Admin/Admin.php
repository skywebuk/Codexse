<?php
/**
 * Admin Class.
 *
 * @package Edugo_LMS\Admin
 */

namespace Edugo_LMS\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Admin
 *
 * Handles all admin-side functionality.
 *
 * @since 1.0.0
 */
class Admin {

    /**
     * Enqueue admin styles.
     *
     * @since 1.0.0
     * @param string $hook The current admin page hook.
     * @return void
     */
    public function enqueue_styles( string $hook ): void {
        // Only load on Edugo admin pages.
        if ( ! $this->is_edugo_admin_page( $hook ) ) {
            return;
        }

        wp_enqueue_style(
            'edugo-admin',
            EDUGO_LMS_URL . 'assets/css/admin.css',
            array(),
            EDUGO_LMS_VERSION
        );
    }

    /**
     * Enqueue admin scripts.
     *
     * @since 1.0.0
     * @param string $hook The current admin page hook.
     * @return void
     */
    public function enqueue_scripts( string $hook ): void {
        // Only load on Edugo admin pages.
        if ( ! $this->is_edugo_admin_page( $hook ) ) {
            return;
        }

        wp_enqueue_media();

        wp_enqueue_script(
            'edugo-admin',
            EDUGO_LMS_URL . 'assets/js/admin.js',
            array( 'jquery', 'wp-util' ),
            EDUGO_LMS_VERSION,
            true
        );

        wp_localize_script(
            'edugo-admin',
            'edugoAdmin',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'edugo_admin_nonce' ),
                'i18n'    => array(
                    'confirmDelete' => __( 'Are you sure you want to delete this?', 'edugo-lms' ),
                    'saving'        => __( 'Saving...', 'edugo-lms' ),
                    'saved'         => __( 'Saved!', 'edugo-lms' ),
                    'error'         => __( 'An error occurred.', 'edugo-lms' ),
                ),
            )
        );
    }

    /**
     * Add admin menu pages.
     *
     * @since 1.0.0
     * @return void
     */
    public function add_admin_menu(): void {
        // Main menu.
        add_menu_page(
            __( 'Edugo LMS', 'edugo-lms' ),
            __( 'Edugo LMS', 'edugo-lms' ),
            'manage_options',
            'edugo-lms',
            array( $this, 'render_dashboard_page' ),
            'dashicons-welcome-learn-more',
            30
        );

        // Dashboard submenu.
        add_submenu_page(
            'edugo-lms',
            __( 'Dashboard', 'edugo-lms' ),
            __( 'Dashboard', 'edugo-lms' ),
            'manage_options',
            'edugo-lms',
            array( $this, 'render_dashboard_page' )
        );

        // Enrollments submenu.
        add_submenu_page(
            'edugo-lms',
            __( 'Enrollments', 'edugo-lms' ),
            __( 'Enrollments', 'edugo-lms' ),
            'edugo_manage_enrollments',
            'edugo-enrollments',
            array( $this, 'render_enrollments_page' )
        );

        // Earnings submenu.
        add_submenu_page(
            'edugo-lms',
            __( 'Earnings', 'edugo-lms' ),
            __( 'Earnings', 'edugo-lms' ),
            'edugo_manage_earnings',
            'edugo-earnings',
            array( $this, 'render_earnings_page' )
        );

        // Withdrawals submenu.
        add_submenu_page(
            'edugo-lms',
            __( 'Withdrawals', 'edugo-lms' ),
            __( 'Withdrawals', 'edugo-lms' ),
            'edugo_process_withdrawals',
            'edugo-withdrawals',
            array( $this, 'render_withdrawals_page' )
        );

        // Reports submenu.
        add_submenu_page(
            'edugo-lms',
            __( 'Reports', 'edugo-lms' ),
            __( 'Reports', 'edugo-lms' ),
            'edugo_view_reports',
            'edugo-reports',
            array( $this, 'render_reports_page' )
        );

        // Settings submenu.
        add_submenu_page(
            'edugo-lms',
            __( 'Settings', 'edugo-lms' ),
            __( 'Settings', 'edugo-lms' ),
            'edugo_manage_settings',
            'edugo-settings',
            array( $this, 'render_settings_page' )
        );

        // Tools submenu.
        add_submenu_page(
            'edugo-lms',
            __( 'Tools', 'edugo-lms' ),
            __( 'Tools', 'edugo-lms' ),
            'manage_options',
            'edugo-tools',
            array( $this, 'render_tools_page' )
        );
    }

    /**
     * Register plugin settings.
     *
     * @since 1.0.0
     * @return void
     */
    public function register_settings(): void {
        // General settings.
        register_setting( 'edugo_general_settings', 'edugo_course_approval_required' );
        register_setting( 'edugo_general_settings', 'edugo_progress_calculation' );
        register_setting( 'edugo_general_settings', 'edugo_drip_content_enabled' );

        // Monetization settings.
        register_setting( 'edugo_monetization_settings', 'edugo_instructor_commission' );
        register_setting( 'edugo_monetization_settings', 'edugo_minimum_withdrawal' );
        register_setting( 'edugo_monetization_settings', 'edugo_currency' );

        // Quiz settings.
        register_setting( 'edugo_quiz_settings', 'edugo_quiz_passing_grade' );
        register_setting( 'edugo_quiz_settings', 'edugo_quiz_retake_limit' );

        // Certificate settings.
        register_setting( 'edugo_certificate_settings', 'edugo_certificate_enabled' );
        register_setting( 'edugo_certificate_settings', 'edugo_certificate_template' );

        // Email settings.
        register_setting( 'edugo_email_settings', 'edugo_email_notifications' );
        register_setting( 'edugo_email_settings', 'edugo_admin_email' );

        // Add settings sections.
        add_settings_section(
            'edugo_general_section',
            __( 'General Settings', 'edugo-lms' ),
            array( $this, 'render_general_section' ),
            'edugo-settings'
        );
    }

    /**
     * Check if current page is an Edugo admin page.
     *
     * @since 1.0.0
     * @param string $hook The current admin page hook.
     * @return bool True if Edugo page, false otherwise.
     */
    private function is_edugo_admin_page( string $hook ): bool {
        $edugo_pages = array(
            'toplevel_page_edugo-lms',
            'edugo-lms_page_edugo-enrollments',
            'edugo-lms_page_edugo-earnings',
            'edugo-lms_page_edugo-withdrawals',
            'edugo-lms_page_edugo-reports',
            'edugo-lms_page_edugo-settings',
            'edugo-lms_page_edugo-tools',
        );

        // Check for CPT edit screens.
        $screen = get_current_screen();
        if ( $screen && in_array( $screen->post_type, array( 'edugo_course', 'edugo_lesson', 'edugo_quiz', 'edugo_assignment', 'edugo_question' ), true ) ) {
            return true;
        }

        return in_array( $hook, $edugo_pages, true );
    }

    /**
     * Render dashboard page.
     *
     * @since 1.0.0
     * @return void
     */
    public function render_dashboard_page(): void {
        $stats = $this->get_dashboard_stats();
        include EDUGO_LMS_PATH . 'templates/admin/dashboard.php';
    }

    /**
     * Render enrollments page.
     *
     * @since 1.0.0
     * @return void
     */
    public function render_enrollments_page(): void {
        include EDUGO_LMS_PATH . 'templates/admin/enrollments.php';
    }

    /**
     * Render earnings page.
     *
     * @since 1.0.0
     * @return void
     */
    public function render_earnings_page(): void {
        include EDUGO_LMS_PATH . 'templates/admin/earnings.php';
    }

    /**
     * Render withdrawals page.
     *
     * @since 1.0.0
     * @return void
     */
    public function render_withdrawals_page(): void {
        include EDUGO_LMS_PATH . 'templates/admin/withdrawals.php';
    }

    /**
     * Render reports page.
     *
     * @since 1.0.0
     * @return void
     */
    public function render_reports_page(): void {
        include EDUGO_LMS_PATH . 'templates/admin/reports.php';
    }

    /**
     * Render settings page.
     *
     * @since 1.0.0
     * @return void
     */
    public function render_settings_page(): void {
        $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';
        include EDUGO_LMS_PATH . 'templates/admin/settings.php';
    }

    /**
     * Render tools page.
     *
     * @since 1.0.0
     * @return void
     */
    public function render_tools_page(): void {
        include EDUGO_LMS_PATH . 'templates/admin/tools.php';
    }

    /**
     * Render general settings section.
     *
     * @since 1.0.0
     * @return void
     */
    public function render_general_section(): void {
        echo '<p>' . esc_html__( 'Configure general LMS settings.', 'edugo-lms' ) . '</p>';
    }

    /**
     * Get dashboard statistics.
     *
     * @since 1.0.0
     * @return array Dashboard statistics.
     */
    private function get_dashboard_stats(): array {
        global $wpdb;

        $stats = array(
            'total_courses'     => wp_count_posts( 'edugo_course' )->publish ?? 0,
            'total_lessons'     => wp_count_posts( 'edugo_lesson' )->publish ?? 0,
            'total_quizzes'     => wp_count_posts( 'edugo_quiz' )->publish ?? 0,
            'total_students'    => $this->count_users_by_role( 'edugo_student' ),
            'total_instructors' => $this->count_users_by_role( 'edugo_instructor' ),
            'total_enrollments' => 0,
            'total_earnings'    => 0,
            'pending_courses'   => wp_count_posts( 'edugo_course' )->pending ?? 0,
        );

        // Get enrollment count.
        $enrollments_table = $wpdb->prefix . 'edugo_enrollments';
        if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $enrollments_table ) ) === $enrollments_table ) {
            $stats['total_enrollments'] = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$enrollments_table}" );
        }

        // Get total earnings.
        $earnings_table = $wpdb->prefix . 'edugo_earnings';
        if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $earnings_table ) ) === $earnings_table ) {
            $stats['total_earnings'] = (float) $wpdb->get_var(
                "SELECT SUM(commission_amount) FROM {$earnings_table} WHERE status = 'completed'"
            );
        }

        return $stats;
    }

    /**
     * Count users by role.
     *
     * @since 1.0.0
     * @param string $role The role to count.
     * @return int User count.
     */
    private function count_users_by_role( string $role ): int {
        $users = count_users();
        return $users['avail_roles'][ $role ] ?? 0;
    }
}
