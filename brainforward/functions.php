<?php

class Brainforward {
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
        if ( ! defined( 'BRAINFORWARD_THEME_VERSION' ) ) {
            $theme = wp_get_theme();
            define( 'BRAINFORWARD_THEME_VERSION', $theme->get( 'Version' ) );
        }

        // Define theme directory path
        if ( ! defined( 'BRAINFORWARD_THEME_DIR' ) ) {
            define( 'BRAINFORWARD_THEME_DIR', get_template_directory() );
        }

        // Define theme directory URI
        if ( ! defined( 'BRAINFORWARD_THEME_URI' ) ) {
            define( 'BRAINFORWARD_THEME_URI', get_template_directory_uri() );
        }

        // Define assets directory URI
        if ( ! defined( 'BRAINFORWARD_ASSETS_URI' ) ) {
            define( 'BRAINFORWARD_ASSETS_URI', BRAINFORWARD_THEME_URI . '/assets' );
        }

        // Define CSS directory URI
        if ( ! defined( 'BRAINFORWARD_CSS_URI' ) ) {
            define( 'BRAINFORWARD_CSS_URI', BRAINFORWARD_ASSETS_URI . '/css' );
        }

        // Define JS directory URI
        if ( ! defined( 'BRAINFORWARD_JS_URI' ) ) {
            define( 'BRAINFORWARD_JS_URI', BRAINFORWARD_ASSETS_URI . '/js' );
        }

        // Define images directory URI
        if ( ! defined( 'BRAINFORWARD_IMAGES_URI' ) ) {
            define( 'BRAINFORWARD_IMAGES_URI', BRAINFORWARD_ASSETS_URI . '/images' );
        }

        // Define inc directory path for including files
        if ( ! defined( 'BRAINFORWARD_INC_DIR' ) ) {
            define( 'BRAINFORWARD_INC_DIR', BRAINFORWARD_THEME_DIR . '/inc' );
        }

        // Define language directory path
        if ( ! defined( 'BRAINFORWARD_LANG_DIR' ) ) {
            define( 'BRAINFORWARD_LANG_DIR', BRAINFORWARD_THEME_DIR . '/languages' );
        }
    }

    // Include necessary classes
    private function include_classes() {
        require get_theme_file_path('/classes/Brainforward_Setup.php');
        require get_theme_file_path('/classes/Brainforward_Widget_Init.php');
        require get_theme_file_path('/classes/Brainforward_Enqueue_Scripts.php');
        require get_theme_file_path('/classes/Brainforward_Functions.php');
        require get_theme_file_path('/classes/Brainforward_Plugin_Activation.php');
        require get_theme_file_path('/classes/Brainforward_Classes.php');
        require get_theme_file_path('/classes/Brainforward_Customizer.php');

        if( class_exists('WooCommerce') ){
            require get_theme_file_path('/classes/WooCommerce_Functions.php');
        }

        if( class_exists('OCDI_Plugin') ){
            require get_theme_file_path('/classes/Brainforward_OCDI.php');
        }
    }
}

// Initialize the theme setup
Brainforward::get_instance();