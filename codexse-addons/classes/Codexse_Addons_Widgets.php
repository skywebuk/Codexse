<?php

if ( ! defined( 'ABSPATH' ) ) exit;
class Codexse_Addons_Widgets {
    private static $instance = null;
    private function __construct() {
        add_action( 'elementor/elements/categories_registered', [ $this, 'add_category' ], 1 );
        add_action( 'elementor/widgets/register', [ $this, 'init_widgets' ] );
        add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'enqueue_editor_styles' ] );
        add_filter( 'elementor/icons_manager/additional_tabs', [ $this, 'codexse_custom_icons_tab' ] );
    }

    public function codexse_custom_icons_tab( $tabs ) {
        $tabs['codexse-icons'] = [
            'name'          => 'codexse-icons',
            'label'         => esc_html__( 'Codexse - Icons', 'codexse-addons' ),
            'labelIcon'     => 'cx cx-codexse',
            'prefix'        => 'cx-',
            'displayPrefix' => 'cx',
            'ver'           => CODEXSE_VERSION,
            'url'           => CODEXSE_CSS_URL . 'icons.css',
		    'icons'         => Codexse_Addons_Functions::codexse_icons_name(),
            'native' => false,
        ];
        return $tabs;
    }
    public function enqueue_editor_styles() {
        wp_enqueue_style( 'codexse-icon-style', CODEXSE_CSS_URL . 'icons.css', [], CODEXSE_VERSION );
    }
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function add_category( $elements_manager ) {
        $elements_manager->add_category(
            'codexse-addons',
            [
                'title' => __( 'Codexse Addons', 'codexse-addons' ),
                'icon' => 'fa fa-cogs',
            ]
        );
    }
    public function init_widgets() {
        $widget_list = $this->get_widget_list();
        $widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
        foreach ( $widget_list as $option ) {
            $widget_title = isset($option['title']) ? $option['title'] : '';
            $widget_file_name = strtolower( str_replace( ' ', '_', $widget_title ) );
            $widget_class = "Codexse_Addons_Elementor_Widget_" . $widget_file_name;
            $widget_file_path = CODEXSE_ADDONS_DIR . $widget_file_name . '.php';
            if (!file_exists($widget_file_path) || Codexse_Addons_Functions::get_widgets_status('codexse_' . strtolower($widget_file_name)) !== 'on') {
                continue;
            }
            require_once $widget_file_path;
            if ( Codexse_Addons_Functions::elementor_version_check( '>=', '3.5.0' ) ) {
                $widgets_manager->register( new $widget_class() );
            } else {
                $widgets_manager->register_widget_type( new $widget_class() );
            }
        }
    }

    
    private function get_widget_list() {
        $widget_list = [
            'blog' => [
                'title' => 'Blog',
            ],
            'button' => [
                'title' => 'Button',
            ],
            'desktop_menu' => [
                'title' => 'Desktop Menu',
            ],
            'hero_slider' => [
                'title' => 'Hero Slider',
            ],
            'images_slider' => [
                'title' => 'Images Slider',
            ],
            'info_box' => [
                'title' => 'Info Box',
            ],
            'food_menu' => [
                'title' => 'Food Menu',
            ],
            'mobile_menu' => [
                'title' => 'Mobile Menu',
            ],
            'page_title' => [
                'title' => 'Page Title',
            ],
            'shortcode' => [
                'title' => 'Shortcode',
            ],
            'team' => [
                'title' => 'Team',
            ],
            'testimonial' => [
                'title' => 'Testimonial',
            ],
        ];
        return apply_filters( 'codexse_widget_list', $widget_list );
    }


}
Codexse_Addons_Widgets::instance();
