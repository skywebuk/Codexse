<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Elementor\Controls_Manager;
use Elementor\Element_Base;

class Codexse_Section_Options {
    // Singleton instance
    private static $instance = null;

    // Singleton instance access
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        // Hook into Elementor actions
        add_action( 'elementor/element/container/section_layout/after_section_end', [ $this , 'section_options' ], 1 );
        add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this , 'section_options' ], 1 );
        add_action( 'elementor/frontend/before_render', [ $this , 'before_render' ], 1 );
        add_action( 'elementor/frontend/before_register_scripts', [ $this, 'register_scripts' ] );
        add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
    }

    // Enqueue preview scripts
    public function enqueue_scripts() {
        wp_enqueue_script('codexse-sticky');
        wp_enqueue_style('codexse-sticky');
    }

    // Register scripts
    public function register_scripts() {
        wp_register_style( 'codexse-sticky', CODEXSE_CSS_URL . 'options/codexse-sticky.css', array(), CODEXSE_VERSION );
        wp_register_script( 'codexse-sticky', CODEXSE_JS_URL . 'options/codexse-sticky.js', array( 'jquery' ), CODEXSE_VERSION, true );
    }

    // Add custom section options to Elementor
    public function section_options( Element_Base $element ) {
        $tabs = Controls_Manager::TAB_CONTENT;

        if ( 'section' === $element->get_name() || 'container' === $element->get_name() ) {
            $tabs = Controls_Manager::TAB_LAYOUT;
        }

        // Start section
        $element->start_controls_section(
            '_section_codexse_wrapper_sticky',
            [
                'label' => __( 'Codexse Options', 'codexse-addons' ),
                'tab'   => $tabs,
            ]
        );

        // Add Sticky Select Control
        if ( 'section' === $element->get_name() || 'container' === $element->get_name() ) {
            $element->add_control(
                'codexse_sticky_option',
                [
                    'label'   => __( 'Sticky', 'codexse-addons' ),
                    'type'    => Controls_Manager::SELECT,
                    'options' => [
                        'none'   => __( 'None', 'codexse-addons' ),
                        'top'    => __( 'Top', 'codexse-addons' ),
                        'bottom' => __( 'Bottom', 'codexse-addons' ),
                    ],
                    'default' => 'none', // Default value
                ]
            );

            $element->add_control(
                'codexse_sticky_offset',
                [
                    'label'     => __( 'Sticky Offset', 'codexse-addons' ),
                    'type'      => Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ], // Units (can add %, em, etc., if needed)
                    'range'     => [
                        'px' => [
                            'min' => 0,
                            'max' => 500,
                            'step' => 1,
                        ],
                    ],
                    'default'   => [
                        'unit' => 'px',
                        'size' => 0,
                    ],
                    'condition' => [
                        'codexse_sticky_option!' => 'none', // Show only if sticky is enabled
                    ],
                    'description' => __( 'Set the offset value for the sticky element (in pixels).', 'codexse-addons' ),
                ]
            );
        }

        // End section
        $element->end_controls_section();
    }

    // Handle rendering of sticky options
    public function before_render( Element_Base $element ) {
        if ( 'section' === $element->get_name() || 'container' === $element->get_name() ) {
            $sticky_option = $element->get_settings_for_display( 'codexse_sticky_option' );
            $sticky_offset = $element->get_settings_for_display( 'codexse_sticky_offset' );
            if ( 'none' !== $sticky_option ) {
                $element->add_render_attribute(
                    '_wrapper',
                    'data-sticky',
                    $sticky_option
                );

                if ( ! empty( $sticky_offset['size'] ) ) {
                    $element->add_render_attribute(
                        '_wrapper',
                        'data-sticky-offset',
                        $sticky_offset['size']
                    );
                }
            }
        }
    }
}

// Initialize the singleton instance
Codexse_Section_Options::instance();
