<?php
/**
 * MailHive Admin Class
 *
 * @package MailHive
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Admin class for managing the plugin backend.
 */
class MailHive_Admin {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_init', array( $this, 'handle_actions' ) );

        // AJAX handlers for admin
        add_action( 'wp_ajax_mailhive_save_form', array( $this, 'ajax_save_form' ) );
        add_action( 'wp_ajax_mailhive_delete_subscriber', array( $this, 'ajax_delete_subscriber' ) );
        add_action( 'wp_ajax_mailhive_bulk_action', array( $this, 'ajax_bulk_action' ) );
    }

    /**
     * Add admin menu.
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'MailHive', 'mailhive' ),
            __( 'MailHive', 'mailhive' ),
            'manage_options',
            'mailhive',
            array( $this, 'render_subscribers_page' ),
            'dashicons-email-alt',
            30
        );

        add_submenu_page(
            'mailhive',
            __( 'Subscribers', 'mailhive' ),
            __( 'Subscribers', 'mailhive' ),
            'manage_options',
            'mailhive',
            array( $this, 'render_subscribers_page' )
        );

        add_submenu_page(
            'mailhive',
            __( 'Form Builder', 'mailhive' ),
            __( 'Form Builder', 'mailhive' ),
            'manage_options',
            'mailhive-form-builder',
            array( $this, 'render_form_builder_page' )
        );

        add_submenu_page(
            'mailhive',
            __( 'Settings', 'mailhive' ),
            __( 'Settings', 'mailhive' ),
            'manage_options',
            'mailhive-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Enqueue admin assets.
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_admin_assets( $hook ) {
        if ( strpos( $hook, 'mailhive' ) === false ) {
            return;
        }

        wp_enqueue_style(
            'mailhive-admin',
            MAILHIVE_PLUGIN_URL . 'admin/css/mailhive-admin.css',
            array(),
            MAILHIVE_VERSION
        );

        wp_enqueue_script(
            'mailhive-admin',
            MAILHIVE_PLUGIN_URL . 'admin/js/mailhive-admin.js',
            array( 'jquery' ),
            MAILHIVE_VERSION,
            true
        );

        wp_localize_script( 'mailhive-admin', 'mailhive_admin', array(
            'ajax_url'        => admin_url( 'admin-ajax.php' ),
            'nonce'           => wp_create_nonce( 'mailhive_admin_nonce' ),
            'confirm_delete'  => __( 'Are you sure you want to delete this subscriber?', 'mailhive' ),
            'confirm_bulk'    => __( 'Are you sure you want to perform this action on the selected subscribers?', 'mailhive' ),
            'saving'          => __( 'Saving...', 'mailhive' ),
            'saved'           => __( 'Saved!', 'mailhive' ),
            'error'           => __( 'An error occurred.', 'mailhive' ),
        ) );
    }

    /**
     * Register settings.
     */
    public function register_settings() {
        register_setting( 'mailhive_settings', 'mailhive_success_message', 'sanitize_text_field' );
        register_setting( 'mailhive_settings', 'mailhive_error_message', 'sanitize_text_field' );
        register_setting( 'mailhive_settings', 'mailhive_duplicate_message', 'sanitize_text_field' );
        register_setting( 'mailhive_settings', 'mailhive_form_markup', array( $this, 'sanitize_form_markup' ) );
        register_setting( 'mailhive_settings', 'mailhive_form_css', array( $this, 'sanitize_css' ) );
    }

    /**
     * Sanitize form markup.
     *
     * @param string $markup Form markup.
     * @return string
     */
    public function sanitize_form_markup( $markup ) {
        return wp_kses_post( $markup );
    }

    /**
     * Sanitize CSS.
     *
     * @param string $css CSS content.
     * @return string
     */
    public function sanitize_css( $css ) {
        return wp_strip_all_tags( $css );
    }

    /**
     * Handle admin actions (CSV export, etc.).
     */
    public function handle_actions() {
        // Handle CSV export
        if ( isset( $_GET['action'] ) && 'mailhive_export_csv' === $_GET['action'] ) {
            if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'mailhive_export_csv' ) ) {
                wp_die( esc_html__( 'Security check failed.', 'mailhive' ) );
            }

            if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( esc_html__( 'You do not have permission to perform this action.', 'mailhive' ) );
            }

            $this->export_csv();
        }
    }

    /**
     * Export subscribers to CSV.
     */
    private function export_csv() {
        $subscriber = new MailHive_Subscriber();
        $data = $subscriber->export_csv();

        $filename = 'mailhive-subscribers-' . gmdate( 'Y-m-d-H-i-s' ) . '.csv';

        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=' . $filename );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );

        $output = fopen( 'php://output', 'w' );

        // Add BOM for Excel
        fprintf( $output, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );

        // Headers
        fputcsv( $output, array( 'ID', 'Email', 'Name', 'Status', 'IP Address', 'Custom Fields', 'Subscribed Date' ) );

        // Data rows
        foreach ( $data as $row ) {
            fputcsv( $output, array(
                $row['id'],
                $row['email'],
                $row['name'],
                $row['status'],
                $row['ip_address'],
                $row['custom_fields'],
                $row['created_at'],
            ) );
        }

        fclose( $output );
        exit;
    }

    /**
     * Render subscribers page.
     */
    public function render_subscribers_page() {
        include MAILHIVE_PLUGIN_DIR . 'admin/views/subscribers.php';
    }

    /**
     * Render form builder page.
     */
    public function render_form_builder_page() {
        include MAILHIVE_PLUGIN_DIR . 'admin/views/form-builder.php';
    }

    /**
     * Render settings page.
     */
    public function render_settings_page() {
        include MAILHIVE_PLUGIN_DIR . 'admin/views/settings.php';
    }

    /**
     * AJAX: Save form.
     */
    public function ajax_save_form() {
        check_ajax_referer( 'mailhive_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'mailhive' ) ) );
        }

        $markup = isset( $_POST['markup'] ) ? wp_kses_post( wp_unslash( $_POST['markup'] ) ) : '';
        $css = isset( $_POST['css'] ) ? wp_strip_all_tags( wp_unslash( $_POST['css'] ) ) : '';

        update_option( 'mailhive_form_markup', $markup );
        update_option( 'mailhive_form_css', $css );

        wp_send_json_success( array( 'message' => __( 'Form saved successfully.', 'mailhive' ) ) );
    }

    /**
     * AJAX: Delete subscriber.
     */
    public function ajax_delete_subscriber() {
        check_ajax_referer( 'mailhive_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'mailhive' ) ) );
        }

        $id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

        if ( ! $id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid subscriber ID.', 'mailhive' ) ) );
        }

        $subscriber = new MailHive_Subscriber();
        $result = $subscriber->delete( $id );

        if ( $result ) {
            wp_send_json_success( array( 'message' => __( 'Subscriber deleted.', 'mailhive' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to delete subscriber.', 'mailhive' ) ) );
        }
    }

    /**
     * AJAX: Bulk action.
     */
    public function ajax_bulk_action() {
        check_ajax_referer( 'mailhive_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'mailhive' ) ) );
        }

        $action = isset( $_POST['bulk_action'] ) ? sanitize_text_field( wp_unslash( $_POST['bulk_action'] ) ) : '';
        $ids = isset( $_POST['ids'] ) ? array_map( 'absint', (array) $_POST['ids'] ) : array();

        if ( empty( $ids ) ) {
            wp_send_json_error( array( 'message' => __( 'No subscribers selected.', 'mailhive' ) ) );
        }

        $subscriber = new MailHive_Subscriber();

        switch ( $action ) {
            case 'delete':
                $count = $subscriber->bulk_delete( $ids );
                wp_send_json_success( array(
                    'message' => sprintf(
                        /* translators: %d: number of deleted subscribers */
                        _n( '%d subscriber deleted.', '%d subscribers deleted.', $count, 'mailhive' ),
                        $count
                    ),
                ) );
                break;

            default:
                wp_send_json_error( array( 'message' => __( 'Invalid action.', 'mailhive' ) ) );
        }
    }
}
