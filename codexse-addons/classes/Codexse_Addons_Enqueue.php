<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Codexse_Addons_Enqueue {
    // Singleton instance
    private static $instance = null;

    // Private constructor to prevent direct instantiation
    private function __construct() {
        // Hook into the WordPress init action
        add_action( 'wp_enqueue_scripts', array( $this, 'load_frontend_assets' ), 99 ); // Frontend assets
        add_action('wp_footer', [$this, 'add_svg_filters_to_footer']);
    }

    // Method to get the singleton instance
    public static function get_instance() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Method to enqueue frontend styles and scripts
    public function load_frontend_assets() {
        // Dequeue Elementor's Swiper CSS and JS
        wp_dequeue_style('swiper'); // Elementor's Swiper
        wp_dequeue_script('swiper'); // Elementor's Swiper JS
        
        // Enqueue general styles and scripts
        wp_enqueue_style( 'codexse-addons-icons', CODEXSE_CSS_URL . 'icons.css', array(), CODEXSE_VERSION );
    
        // Register widget-specific styles
        wp_register_style( 'codexse-images-slider', CODEXSE_CSS_URL . 'addons/codexse-images-slider.css', array(), CODEXSE_VERSION );
        wp_register_style( 'codexse-desktop-menu', CODEXSE_CSS_URL . 'addons/codexse-desktop-menu.css', array(), CODEXSE_VERSION );
        wp_register_style( 'codexse-mobile-menu', CODEXSE_CSS_URL . 'addons/codexse-mobile-menu.css', array(), CODEXSE_VERSION );
        wp_register_style( 'codexse-button', CODEXSE_CSS_URL . 'addons/codexse-button.css', array(), CODEXSE_VERSION );
        wp_register_style( 'codexse-info-box', CODEXSE_CSS_URL . 'addons/codexse-info-box.css', array(), CODEXSE_VERSION );
        wp_register_style( 'codexse-testimonial', CODEXSE_CSS_URL . 'addons/codexse-testimonial.css', array(), CODEXSE_VERSION );
        wp_register_style( 'codexse-swiper', CODEXSE_CSS_URL . 'addons/swiper-bundle-min.css', array(), CODEXSE_VERSION );
        wp_register_style( 'codexse-blog', CODEXSE_CSS_URL . 'addons/codexse-blog.css', array(), CODEXSE_VERSION );
        wp_register_style( 'codexse-shortcode', CODEXSE_CSS_URL . 'addons/codexse-shortcode.css', array(), CODEXSE_VERSION );
        wp_register_style( 'codexse-team', CODEXSE_CSS_URL . 'addons/codexse-team.css', array(), CODEXSE_VERSION );
        wp_register_style( 'codexse-hero-slider', CODEXSE_CSS_URL . 'addons/codexse-hero-slider.css', array(), CODEXSE_VERSION );
        wp_register_style( 'codexse-food-menu', CODEXSE_CSS_URL . 'addons/codexse-food-menu.css', array(), CODEXSE_VERSION );


        // Register widget-specific scripts
        wp_register_script( 'codexse-hero-slider', CODEXSE_JS_URL . 'addons/codexse-hero-slider.js', array( 'jquery' ), CODEXSE_VERSION, true );
        wp_register_script( 'codexse-mobile-menu', CODEXSE_JS_URL . 'addons/codexse-mobile-menu.js', array( 'jquery' ), CODEXSE_VERSION, true );
        wp_register_script( 'codexse-swiper', CODEXSE_JS_URL . 'addons/swiper-bundle-min.js', array( 'jquery' ), CODEXSE_VERSION, true );
        wp_register_script( 'codexse-carousel', CODEXSE_JS_URL . 'addons/codexse-carousel.js', array( 'jquery' ), CODEXSE_VERSION, true );

        wp_register_script( 'lordicon', CODEXSE_JS_URL . 'addons/lordicon.js', array( 'jquery' ), CODEXSE_VERSION, true );
    }



    // Hook into wp_footer to add SVG filters
    public function add_svg_filters_to_footer() {
        ?>
        <svg width="0" height="0" class="d-none">
            <!-- Cloud Effect Filter -->
            <filter id="cloude">
                <feTurbulence type="fractalNoise" baseFrequency=".01" numOctaves="6"></feTurbulence>
                <feDisplacementMap in="SourceGraphic" scale="100"></feDisplacementMap>
            </filter>
            <!-- Wave Effect Filter -->
            <filter id="wave">
                <feTurbulence type="fractalNoise" baseFrequency="0.02" numOctaves="3" />
                <feDisplacementMap in="SourceGraphic" scale="30" />
            </filter>
        </svg>
        <?php
    }
    

    // Prevent cloning
}

// Initialize the singleton instance
Codexse_Addons_Enqueue::get_instance();
