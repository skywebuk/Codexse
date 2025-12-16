<?php

class Brainfwd {
    // Singleton instance
    private static $instance = null;
    // Constructor
    private function __construct() {
        $this->define_constants();
        $this->include_classes();
    }

    // Get instance method
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Define theme constants
    private function define_constants() {
        // Define theme version - dynamically get from style.css
        if ( ! defined( 'BRAINFWD_THEME_VERSION' ) ) {
            $theme = wp_get_theme();
            define( 'BRAINFWD_THEME_VERSION', $theme->get( 'Version' ) );
        }

        // Define theme directory path
        if ( ! defined( 'BRAINFWD_THEME_DIR' ) ) {
            define( 'BRAINFWD_THEME_DIR', get_template_directory() );
        }

        // Define theme directory URI
        if ( ! defined( 'BRAINFWD_THEME_URI' ) ) {
            define( 'BRAINFWD_THEME_URI', get_template_directory_uri() );
        }

        // Define assets directory URI
        if ( ! defined( 'BRAINFWD_ASSETS_URI' ) ) {
            define( 'BRAINFWD_ASSETS_URI', BRAINFWD_THEME_URI . '/assets' );
        }

        // Define CSS directory URI
        if ( ! defined( 'BRAINFWD_CSS_URI' ) ) {
            define( 'BRAINFWD_CSS_URI', BRAINFWD_ASSETS_URI . '/css' );
        }

        // Define JS directory URI
        if ( ! defined( 'BRAINFWD_JS_URI' ) ) {
            define( 'BRAINFWD_JS_URI', BRAINFWD_ASSETS_URI . '/js' );
        }

        // Define images directory URI
        if ( ! defined( 'BRAINFWD_IMAGES_URI' ) ) {
            define( 'BRAINFWD_IMAGES_URI', BRAINFWD_ASSETS_URI . '/images' );
        }

        // Define inc directory path for including files
        if ( ! defined( 'BRAINFWD_INC_DIR' ) ) {
            define( 'BRAINFWD_INC_DIR', BRAINFWD_THEME_DIR . '/inc' );
        }

        // Define language directory path
        if ( ! defined( 'BRAINFWD_LANG_DIR' ) ) {
            define( 'BRAINFWD_LANG_DIR', BRAINFWD_THEME_DIR . '/languages' );
        }
    }

    // Include necessary classes
    private function include_classes() {
        require get_theme_file_path('/classes/Brainfwd_Setup.php');
        require get_theme_file_path('/classes/Brainfwd_Widget_Init.php');
        require get_theme_file_path('/classes/Brainfwd_Enqueue_Scripts.php');
        require get_theme_file_path('/classes/Brainfwd_Functions.php');
        require get_theme_file_path('/classes/Brainfwd_Plugin_Activation.php');
        require get_theme_file_path('/classes/Brainfwd_Classes.php');
        require get_theme_file_path('/classes/Brainfwd_Customizer.php');

        if( class_exists('WooCommerce') ){
            require get_theme_file_path('/classes/WooCommerce_Functions.php');
        }

        if( class_exists('OCDI_Plugin') ){
            require get_theme_file_path('/classes/Brainfwd_OCDI.php');
        }
    }
}

// Initialize the theme setup
Brainfwd::get_instance();
