<?php
/**
 * Elementor Widgets Controller
 *
 * @package Folioedgecore
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class Folioedgecore_Widgets_Controller
 *
 * Handles registration and loading of all Elementor widgets
 */
class Folioedgecore_Widgets_Controller {

    /**
     * Instance
     *
     * @var Folioedgecore_Widgets_Controller|null
     */
    private static $instance = null;

    /**
     * Widget files to load
     *
     * @var array
     */
    private $widgets = array(
        'section-title',
        'accordion',
        'slider-arrow',
        'progress-cirlce',
        'countdown',
        'button',
        'feature-box',
        'testimonial',
        'service',
        'post-slider',
        'team',
        'counter',
        'lightbox',
        'shortcode',
        'tabs',
        'range-slider',
        'price',
        'case-studie',
        'history',
        'slide-box',
        'roadmap',
        'progress',
        'image-gallery',
        'experiences',
        'image-carousel',
        'switch',
        'audio',
    );

    /**
     * Extension files to load (not widgets)
     *
     * @var array
     */
    private $extensions = array(
        'flotingeffect',
    );

    /**
     * Get Instance
     *
     * @return Folioedgecore_Widgets_Controller
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
        $this->load_widgets();
        $this->load_extensions();
    }

    /**
     * Load widget files
     */
    private function load_widgets() {
        $widgets_dir = __DIR__ . '/widgets/';

        foreach ( $this->widgets as $widget ) {
            $widget_file = $widgets_dir . $widget . '.php';

            if ( file_exists( $widget_file ) ) {
                require_once $widget_file;
            }
        }
    }

    /**
     * Load extension files (non-widget Elementor extensions)
     */
    private function load_extensions() {
        $widgets_dir = __DIR__ . '/widgets/';

        foreach ( $this->extensions as $extension ) {
            $extension_file = $widgets_dir . $extension . '.php';

            if ( file_exists( $extension_file ) ) {
                require_once $extension_file;
            }
        }
    }

    /**
     * Get all widget class names
     *
     * @return array
     */
    public function get_widget_classes() {
        return array(
            'folioedge_Elementor_Widget_Section_Title',
            'folioedge_Elementor_Widget_Accordion',
            'folioedge_slider_arrow_widget',
            'folioedge_progress_circle_widget',
            'folioedge_countdown_widget',
            'folioedge_button_widget',
            'folioedge_feature_box_widget',
            'folioedge_testimonial_widget',
            'folioedge_service_widget',
            'folioedge_post_slider_widget',
            'folioedge_team_widget',
            'folioedge_counter_widget',
            'folioedge_lightbox_widget',
            'folioedge_shortcode_widget',
            'folioedge_tabs_widget',
            'folioedge_range_slider_widget',
            'folioedge_price_widget',
            'folioedge_case_studie_widget',
            'folioedge_history_widget',
            'folioedge_slide_box_widget',
            'folioedge_roadmap_widget',
            'folioedge_progress_widget',
            'folioedge_image_gallery_widget',
            'folioedge_experiences_widget',
            'folioedge_image_carousel_widget',
            'folioedge_switch_widget',
            'folioedge_audio_widget',
        );
    }
}

// Initialize the widgets controller
Folioedgecore_Widgets_Controller::instance();
