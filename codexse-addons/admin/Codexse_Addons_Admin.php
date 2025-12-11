<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class Codexse_Addons_Admin {
    public function __construct() {
        add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_assets' ] );
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'admin_init', [ $this, 'register_elementor_widgets' ] );
        add_action( 'wp_ajax_codexse_save_setting', [ $this, 'save_elementor_widgets' ] );
        add_action( 'admin_head', [ $this, 'hide_admin_notices' ] );
    }
    
    public function load_admin_assets( $hook ) {
        if ( defined( 'CODEXSE_ADMIN_URL' ) && defined( 'CODEXSE_VERSION' ) ) {
            wp_enqueue_style( 'codexse-admin-style', CODEXSE_ADMIN_URL . 'assets/css/admin-style.css', [], CODEXSE_VERSION );
            wp_enqueue_style( 'codexse-icon-style', CODEXSE_CSS_URL . 'icons.css', [], CODEXSE_VERSION );
            if ( $hook === 'codexse_page_codexse-widgets' || $hook === 'codexse_page_codexse-features' ) {
                wp_enqueue_script( 'codexse-admin-script', CODEXSE_ADMIN_URL . 'assets/js/admin-script.js', [ 'jquery' ], CODEXSE_VERSION, true );
                wp_localize_script( 'codexse-admin-script', 'codexse_setting', [
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'nonce'    => wp_create_nonce( 'codexse_settings_nonce' ),
                ]);
            }
        }
    }
    
    public function add_admin_menu() {
        add_menu_page(
            esc_html__( 'Codexse', 'codexse-addons' ),
            esc_html__( 'Codexse', 'codexse-addons' ),
            'manage_options',
            'codexse-addons',
            [ $this, 'general_page_content' ],
            'dashicons-codexse',
            20
        );

        add_submenu_page(
            'codexse-addons',
            esc_html__( 'General', 'codexse-addons' ),
            esc_html__( 'General', 'codexse-addons' ),
            'manage_options',
            'codexse-addons',
            [ $this, 'general_page_content' ]
        );

        add_submenu_page(
            'codexse-addons',
            esc_html__( 'Widgets', 'codexse-addons' ),
            esc_html__( 'Widgets', 'codexse-addons' ),
            'manage_options',
            'codexse-widgets',
            [ $this, 'widgets_page_content' ]
        );

        add_submenu_page(
            'codexse-addons',
            esc_html__( 'Features', 'codexse-addons' ),
            esc_html__( 'Features', 'codexse-addons' ),
            'manage_options',
            'codexse-features',
            [ $this, 'features_page_content' ]
        );
    }

    public function general_page_content() {
        $this->display_page_content( 'general' );
    }

    public function widgets_page_content() {
        $this->display_page_content( 'widgets' );
    }

    // Content for the Features page
    public function features_page_content() {
        $this->display_page_content( 'features' );
    }

    // Helper function to display page content
    private function display_page_content( $template ) {
        echo '<div class="wrap codexse-page-wrapper ' . esc_attr( $template ) . '-template">';
            echo '<div class="codexse-navigation-wrapper">';
                include_once CODEXSE_ADMIN_DIR . 'template/dashboard-tabs.php';
            echo '</div>';
            echo '<div class="codexse-content-wrapper">';
                include_once CODEXSE_ADMIN_DIR . "template/dashboard-{$template}.php";
            echo '</div>';
        echo '</div>';
    }

    // Callback to hide admin notices on Codexse-related pages
    public function hide_admin_notices() {
        $current_screen = get_current_screen();
        $allowed_screens = [
            'toplevel_page_codexse-addons',         // Codexse main page
            'codexse_page_codexse-widgets', // Widgets submenu
            'codexse_page_codexse-features', // Features submenu
        ];

        if ( in_array( $current_screen->id, $allowed_screens, true ) ) {
            echo '<style>.notice { display: none !important; }</style>';
        }
    }

    // Register Elementor addon widgets
    public function register_elementor_widgets() {
        register_setting( 'codexse_widgets_group', 'codexse_widgets' );
        register_setting( 'codexse_widgets_group', 'codexse_features' );
    }

    public function save_elementor_widgets() {
        // Check the AJAX nonce for security
        check_ajax_referer( 'codexse_settings_nonce', 'nonce' );

        // Check user capability
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( esc_html__( 'You do not have permission to perform this action.', 'codexse-addons' ) );
        }

        // Get and sanitize the posted data
        $widgets  = isset( $_POST['widgets'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['widgets'] ) ) : array();
        $features = isset( $_POST['features'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['features'] ) ) : array();

        if ( ! empty( $widgets ) && is_array( $widgets ) ) {
            update_option( 'codexse_widgets', $widgets );
            wp_send_json_success( esc_html__( 'Settings saved successfully!', 'codexse-addons' ) );
        } elseif ( ! empty( $features ) && is_array( $features ) ) {
            update_option( 'codexse_features', $features );
            wp_send_json_success( esc_html__( 'Settings saved successfully!', 'codexse-addons' ) );
        } else {
            wp_send_json_error( esc_html__( 'Nothing to save.', 'codexse-addons' ) );
        }

        wp_die();
    }
    

}

// Initialize the Codexse_Addons_Admin class
new Codexse_Addons_Admin();