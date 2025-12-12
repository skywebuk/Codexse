<?php
// Check if the class Brainforward_Setup doesn't already exist to avoid redeclaration
if ( ! class_exists( 'Brainforward_Setup' ) ) {
    // Define the Brainforward_Setup class
    class Brainforward_Setup {
        // Constructor method to initialize the class
        public function __construct() {
            // Hook the setup_theme method to the after_setup_theme action
            add_action( 'after_setup_theme', array( $this, 'setup_theme' ) );
            add_action('init', [$this, 'brainforward_load_textdomain']);
        }

        public function brainforward_load_textdomain() {
            // Load the theme's translation files
            load_theme_textdomain( 'brainforward', BRAINFORWARD_LANG_DIR );
        }

        // Method to set up theme features
        public function setup_theme() {

            // Enable automatic feed links for the theme
            add_theme_support( 'automatic-feed-links' );

            // Allow the theme to manage the title tag
            add_theme_support( 'title-tag' );

            // Enable custom logo support with flexible height
            add_theme_support( 'custom-logo', array(
                'flex-height' => true
            ) );

            // Check if WooCommerce is active before adding WooCommerce support
            if ( class_exists( 'WooCommerce' ) ) {   
                add_theme_support( 'woocommerce', 
                    array(
                        // Set image widths for product thumbnails
                        'thumbnail_image_width' => 400,
                        'gallery_thumbnail_image_width' => 300,
                        'single_image_width'    => 800,
                        // Configure product grid layout settings
                        'product_grid' => array(
                            'default_rows'    => 3,
                            'min_rows'        => 1,
                            'max_rows'        => 6,
                            'default_columns' => 3,
                            'min_columns'     => 1,
                            'max_columns'     => 5,
                        ),
                    )
                );
                // Add support for WooCommerce product gallery features
                add_theme_support( 'wc-product-gallery-zoom' );
                add_theme_support( 'wc-product-gallery-lightbox' );
                add_theme_support( 'wc-product-gallery-slider' );
            }

            // Enable custom background support
            add_theme_support(
                'custom-background',
                array(
                    'default-color' => '000000' // Default background color
                )
            );

            // Enable custom header support
            add_theme_support(
                'custom-header',
                array(
                    'default-text-color' => '000000', // Default text color for header
                    'wp-head-callback'   => array( $this, 'header_style' ), // Callback for header styles
                )
            );


            // Enable post thumbnails for featured images
            add_theme_support( 'post-thumbnails' );

            // Register a primary navigation menu
            register_nav_menus( array(
                'primary_menu' => esc_html__( 'Primary Menu', 'brainforward' )
            ) );

            // Enable HTML5 support for various elements
            add_theme_support( 'html5', array(
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
            ) );

            // Enable support for post formats
            add_theme_support( 'post-formats', array(
                'aside',
                'image',
                'video',
                'quote',
                'link',
                'gallery',
                'status',
                'audio',
                'chat',
            ) );

            // Add custom editor styles for the block editor
            add_editor_style( array( 'assets/css/editor-style.css' ) );

            // Enable selective refresh for widgets in the Customizer
            add_theme_support( 'customize-selective-refresh-widgets' );

            // Add support for block styles in the editor
            add_theme_support( 'wp-block-styles' );
            add_theme_support( 'align-wide' ); // Allow wide alignment options

            // Enable editor styles
            add_theme_support( 'editor-styles' );

            // Disable the new block editor for widgets
            add_filter( 'gutenberg_use_widgets_block_editor', '__return_false', 100 );
            add_filter( 'use_widgets_block_editor', '__return_false' );

            // Enable responsive embeds for better media handling
            add_theme_support( 'responsive-embeds' );

            // Additional HTML5 support
            add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );

            // Enable custom spacing and line height support
            add_theme_support( 'custom-spacing' );
            add_theme_support( 'custom-line-height' );

            // Set the global content width for media elements
            $GLOBALS['content_width'] = apply_filters( 'brainforward_content_width', 850 );
        }

        // Callback function for customizing header styles
        public function header_style() {
            // Only proceed if the default text color is different from the current header text color
            if ( get_theme_support( 'custom-header', 'default-text-color' ) === get_header_textcolor() ) {
                return; // Exit if no changes are needed
            }
        }

        // Method to detect if the current page is the homepage
        public static function detect_homepage() {
            // Get the homepage ID set in the WordPress settings
            $homepage_id = get_option( 'page_on_front' );
            // Get the current page ID
            $current_page_id = ( is_page( get_the_ID() ) ) ? get_the_ID() : '';
            // Check if the current page is the homepage or if the one-page effect is not set
            if ( $homepage_id == $current_page_id ) {
                return true; // It is the homepage
            } else {
                return false; // It is not the homepage
            }
        }
    }

    // Instantiate the Brainforward_Setup class to initialize the theme setup
    new Brainforward_Setup();
}
?>
