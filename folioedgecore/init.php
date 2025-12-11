<?php
/**
 * Plugin Name: Folioedge Core
 * Description: Creates interfaces to manage store/business locations on your website. Useful for showing location based information quickly. Includes both a widget and shortcode for ease of use.
 * Version: 2.0.0
 * Author: Ashekur Rahman
 * Author URI: https://www.polothemes.com/
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: folioedgecore
 * Domain Path: /language/
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define( 'FOLIOEDGECORE_VERSION', '2.0.0' );
define( 'FOLIOEDGECORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'FOLIOEDGECORE_URL', plugin_dir_url( __FILE__ ) );
define( 'FOLIOEDGECORE_ASSETS', FOLIOEDGECORE_URL . 'assets/' );

/**
 * Main Plugin Class
 */
final class Folioedgecore {

    /**
     * Instance
     *
     * @var Folioedgecore|null
     */
    private static $instance = null;

    /**
     * Minimum Elementor Version
     *
     * @var string
     */
    const MINIMUM_ELEMENTOR_VERSION = '3.5.0';

    /**
     * Minimum PHP Version
     *
     * @var string
     */
    const MINIMUM_PHP_VERSION = '7.4';

    /**
     * Get Instance
     *
     * @return Folioedgecore
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        // Load plugin translations
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

        // Include required files
        add_action( 'plugins_loaded', array( $this, 'include_files' ) );

        // Register sidebar widgets
        add_action( 'widgets_init', array( $this, 'register_widgets' ) );

        // Enqueue frontend scripts and styles
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );

        // Enqueue admin scripts and styles
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

        // Initialize Elementor integration
        add_action( 'elementor/init', array( $this, 'elementor_init' ) );

        // Register Elementor widgets (new hook since Elementor 3.5)
        add_action( 'elementor/widgets/register', array( $this, 'register_elementor_widgets' ) );

        // Register frontend scripts for Elementor
        add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_frontend_scripts' ) );

        // Register frontend styles for Elementor
        add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_frontend_styles' ) );

        // Add custom fonts to Elementor
        add_action( 'elementor/fonts/additional_fonts', array( $this, 'add_custom_fonts' ) );

        // Enqueue admin styles in Elementor editor
        add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_admin_assets' ) );

        // Add SVG upload support
        add_filter( 'upload_mimes', array( $this, 'allow_svg_upload' ) );

        // WooCommerce integration
        if ( class_exists( 'WooCommerce' ) ) {
            add_action( 'woocommerce_product_meta_end', array( $this, 'display_product_extra_meta' ) );
            add_action( 'woocommerce_single_product_summary', array( $this, 'display_product_extra_button' ), 35 );
        }
    }

    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'folioedgecore',
            false,
            dirname( plugin_basename( __FILE__ ) ) . '/language/'
        );
    }

    /**
     * Include required files
     */
    public function include_files() {
        require_once FOLIOEDGECORE_PATH . 'inc/plugin-functions.php';
        require_once FOLIOEDGECORE_PATH . 'inc/metabox.php';
        require_once FOLIOEDGECORE_PATH . 'inc/service-post-type.php';
        require_once FOLIOEDGECORE_PATH . 'inc/case-studies-post-type.php';
        require_once FOLIOEDGECORE_PATH . 'inc/team-post-type.php';
        require_once FOLIOEDGECORE_PATH . 'widgets/popular-post.php';
        require_once FOLIOEDGECORE_PATH . 'widgets/social-menu.php';
        require_once FOLIOEDGECORE_PATH . 'widgets/profile.php';
    }

    /**
     * Register sidebar widgets
     */
    public function register_widgets() {
        register_widget( 'folioedge_social_menu' );
        register_widget( 'folioedge_author_info' );
        register_widget( 'folioedge_popular_posts' );
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_frontend_assets() {
        // Styles
        wp_enqueue_style(
            'folioedge-ui-css',
            FOLIOEDGECORE_ASSETS . 'css/jquery-ui.css',
            array(),
            '1.13.0'
        );

        wp_enqueue_style(
            'folioedgecore-audio',
            FOLIOEDGECORE_ASSETS . 'css/audio.css',
            array(),
            FOLIOEDGECORE_VERSION
        );

        wp_enqueue_style(
            'folioedgecore-main',
            FOLIOEDGECORE_ASSETS . 'css/main.css',
            array(),
            FOLIOEDGECORE_VERSION
        );

        wp_enqueue_style(
            'swiper',
            FOLIOEDGECORE_ASSETS . 'css/swiper-bundle-min.css',
            array(),
            '8.4.5'
        );

        // Scripts
        wp_register_script(
            'jquery-easing',
            FOLIOEDGECORE_ASSETS . 'js/easing-min.js',
            array( 'jquery' ),
            '1.3.0',
            true
        );

        wp_register_script(
            'easypiechart',
            FOLIOEDGECORE_ASSETS . 'js/easypiechart-min.js',
            array( 'jquery' ),
            '2.1.7',
            true
        );

        wp_register_script(
            'anime',
            FOLIOEDGECORE_ASSETS . 'js/anime.js',
            array( 'jquery' ),
            '3.2.1',
            true
        );

        wp_enqueue_script(
            'swiper',
            FOLIOEDGECORE_ASSETS . 'js/swiper-bundle-min.js',
            array( 'jquery' ),
            '8.4.5',
            true
        );

        wp_enqueue_script(
            'folioedgecore-active',
            FOLIOEDGECORE_ASSETS . 'js/plugin-core.js',
            array( 'jquery' ),
            FOLIOEDGECORE_VERSION,
            true
        );
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_assets() {
        wp_enqueue_style(
            'folioedge_admin_style',
            FOLIOEDGECORE_ASSETS . 'css/plugin-admin.css',
            array(),
            FOLIOEDGECORE_VERSION
        );

        wp_enqueue_script(
            'folioedge_admin_script',
            FOLIOEDGECORE_ASSETS . 'js/plugin-admin.js',
            array( 'jquery' ),
            FOLIOEDGECORE_VERSION,
            true
        );
    }

    /**
     * Initialize Elementor integration
     */
    public function elementor_init() {
        // Check Elementor version
        if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
            return;
        }

        // Add custom category
        \Elementor\Plugin::instance()->elements_manager->add_category(
            'folioedgecore',
            array(
                'title' => esc_html__( 'Folioedge', 'folioedgecore' ),
                'icon' => 'fa fa-plug',
            ),
            1
        );

        // Include icon manager
        require_once FOLIOEDGECORE_PATH . 'inc/plugin-icon-manager.php';
    }

    /**
     * Register Elementor widgets
     *
     * @param \Elementor\Widgets_Manager $widgets_manager
     */
    public function register_elementor_widgets( $widgets_manager ) {
        require_once FOLIOEDGECORE_PATH . 'addons/widgets_control.php';
    }

    /**
     * Register frontend scripts for Elementor
     */
    public function register_frontend_scripts() {
        wp_register_script(
            'countdown',
            FOLIOEDGECORE_ASSETS . 'js/countdown.js',
            array( 'jquery' ),
            FOLIOEDGECORE_VERSION,
            true
        );

        wp_register_script(
            'isotope',
            FOLIOEDGECORE_ASSETS . 'js/isotope-min.js',
            array( 'jquery' ),
            '3.0.6',
            true
        );

        wp_register_script(
            'addons-active',
            FOLIOEDGECORE_ASSETS . 'js/addons-active.js',
            array( 'jquery' ),
            FOLIOEDGECORE_VERSION,
            true
        );

        wp_register_script(
            'lity',
            FOLIOEDGECORE_ASSETS . 'js/lity-min.js',
            array( 'jquery' ),
            '2.4.1',
            true
        );

        wp_register_script(
            'bootstrap-js',
            FOLIOEDGECORE_ASSETS . 'js/bootstrap-min.js',
            array( 'jquery' ),
            '5.2.3',
            true
        );

        wp_register_script(
            'plyr',
            FOLIOEDGECORE_ASSETS . 'js/plyr.min.js',
            array( 'jquery' ),
            '3.7.8',
            true
        );

        wp_register_script(
            'polyfilled',
            FOLIOEDGECORE_ASSETS . 'js/plyr.polyfilled.min.js',
            array( 'jquery' ),
            '3.7.8',
            true
        );
    }

    /**
     * Register frontend styles for Elementor
     */
    public function register_frontend_styles() {
        wp_register_style(
            'lity',
            FOLIOEDGECORE_ASSETS . 'css/lity-min.css',
            array(),
            '2.4.1'
        );
    }

    /**
     * Add custom fonts to Elementor
     *
     * @param array $fonts
     * @return array
     */
    public function add_custom_fonts( $fonts ) {
        $fonts['Satoshi'] = 'system';
        $fonts['Recoleta'] = 'system';
        return $fonts;
    }

    /**
     * Allow SVG upload
     *
     * @param array $mimes
     * @return array
     */
    public function allow_svg_upload( $mimes ) {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }

    /**
     * Display product extra meta on single product page
     */
    public function display_product_extra_meta() {
        global $product;

        if ( ! $product ) {
            return;
        }

        $product_id = $product->get_id();
        $extra_meta = get_post_meta( $product_id, '_folioedge_wc_meta_repeat_group', true );

        if ( ! empty( $extra_meta ) && is_array( $extra_meta ) ) {
            echo '<div class="folioedge-product-extra-meta">';
            foreach ( $extra_meta as $meta ) {
                $title = isset( $meta['_folioedge_wc_meta_title'] ) ? $meta['_folioedge_wc_meta_title'] : '';
                $value = isset( $meta['_folioedge_wc_meta_value'] ) ? $meta['_folioedge_wc_meta_value'] : '';

                if ( ! empty( $title ) && ! empty( $value ) ) {
                    echo '<div class="extra-meta-item">';
                    echo '<span class="meta-title">' . esc_html( $title ) . ':</span> ';
                    echo '<span class="meta-value">' . wp_kses_post( $value ) . '</span>';
                    echo '</div>';
                }
            }
            echo '</div>';
        }
    }

    /**
     * Display product extra button on single product page
     */
    public function display_product_extra_button() {
        global $product;

        if ( ! $product ) {
            return;
        }

        $product_id = $product->get_id();
        $button_label = get_post_meta( $product_id, '_folioedge_wc_ex_button_label', true );
        $button_url = get_post_meta( $product_id, '_folioedge_wc_ex_button_url', true );

        if ( ! empty( $button_label ) && ! empty( $button_url ) ) {
            echo '<div class="folioedge-product-extra-button">';
            echo '<a href="' . esc_url( $button_url ) . '" class="button alt" target="_blank" rel="noopener noreferrer">';
            echo esc_html( $button_label );
            echo '</a>';
            echo '</div>';
        }
    }

    /**
     * Admin notice for minimum Elementor version
     */
    public function admin_notice_minimum_elementor_version() {
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'folioedgecore' ),
            '<strong>' . esc_html__( 'Folioedge Core', 'folioedgecore' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'folioedgecore' ) . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }
}

/**
 * Initialize the plugin
 */
function folioedgecore_init() {
    return Folioedgecore::instance();
}

// Run the plugin
folioedgecore_init();
