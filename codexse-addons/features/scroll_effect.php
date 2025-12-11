<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Elementor\Controls_Manager;
use Elementor\Element_Base;

class Codexse_Scroll_Effect {
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
        add_action( 'elementor/element/common/_section_style/after_section_end', [ $this , 'scroll_options' ], 1 );
        add_action( 'elementor/frontend/before_register_scripts', [ $this, 'register_scripts' ] );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'add_lax_attributes' ], 10 );
    }

    // Register scripts
    public function register_scripts() {
        wp_register_script( 'lax', CODEXSE_JS_URL . 'options/lax.js', array(), CODEXSE_VERSION, true );
        wp_register_script( 'codexse-scroll-effects', CODEXSE_JS_URL . 'options/codexse-scroll-effects.js', array( 'jquery', 'lax' ), CODEXSE_VERSION, true );
    }

    // Add custom section options to Elementor
    public function scroll_options( Element_Base $element ) {
        $element->start_controls_section(
            '_section_scroll_effects',
            [
                'label' => __( 'Scroll Effects', 'codexse-addons' ),
                'tab' => Controls_Manager::TAB_ADVANCED,
            ]
        );
    
        $element->add_control(
            'codexse_scroll',
            [
                'label' => __( 'Enable', 'codexse-addons' ),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'assets' => [
                    'scripts' => [
                        [
                            'name' => 'lax',
                            'conditions' => [
                                'terms' => [
                                    [
                                        'name' => 'codexse_scroll',
                                        'operator' => '===',
                                        'value' => 'yes',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'name' => 'codexse-scroll-effects',
                            'conditions' => [
                                'terms' => [
                                    [
                                        'name' => 'codexse_scroll',
                                        'operator' => '===',
                                        'value' => 'yes',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );
    
        $element->add_control(
            'codexse_scroll_fade_toggle',
            [
                'label' => __( 'Fade Control', 'codexse-addons' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition' => [
                    'codexse_scroll' => 'yes',
                ]
            ]
        );
    
        $element->start_popover();
        // Add a control for selecting fade effect
        $element->add_control(
            'codexse_scroll_fade_effect',
            [
                'label' => __( 'Fade Effect', 'codexse-addons' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'fadeIn' => __( 'Fade In', 'codexse-addons' ),
                    'fadeOut' => __( 'Fade Out', 'codexse-addons' ),
                    'fadeInOut' => __( 'Fade In & Out', 'codexse-addons' ),
                ],
                'default' => 'fadeIn',
                'condition' => [
                    'codexse_scroll' => 'yes',
                ],
            ]
        );
    
        // Slider for Distance
        $element->add_control(
            'codexse_scroll_fade_distance',
            [
                'label' => __( 'Distance', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 150,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 600,
                    ],
                ],
                'condition' => [
                    'codexse_scroll' => 'yes',
                ],
            ]
        );
    
        // Slider for Starting Opacity
        $element->add_control(
            'codexse_scroll_fade_opacity',
            [
                'label' => __( 'Starting Opacity', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 0.9,
                        'step' => 0.1,
                    ],
                ],
                'condition' => [
                    'codexse_scroll' => 'yes',
                ],
            ]
        );
        $element->end_popover();
    
        $element->add_control(
            'codexse_scroll_scale_toggle',
            [
                'label' => __( 'Scale Control', 'codexse-addons' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition' => [
                    'codexse_scroll' => 'yes',
                ]
            ]
        );
        
        $element->start_popover();
        // Add a control for selecting scale effect
        $element->add_control(
            'codexse_scroll_scale_effect',
            [
                'label' => __( 'Effect', 'codexse-addons' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'scaleIn' => __( 'Scale In', 'codexse-addons' ),
                    'scaleOut' => __( 'Scale Out', 'codexse-addons' ),
                    'scaleInOut' => __( 'Scale In & Out', 'codexse-addons' ),
                ],
                'default' => 'scaleIn',
                'condition' => [
                    'codexse_scroll' => 'yes',
                ],
            ]
        );
        
        // Slider for Scale Distance
        $element->add_control(
            'codexse_scroll_scale_distance',
            [
                'label' => __( 'Distance', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 150,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 600,
                        'step' => 0.1,
                    ],
                ],
                'condition' => [
                    'codexse_scroll' => 'yes',
                ],
            ]
        );
        
        // Slider for Starting Scale
        $element->add_control(
            'codexse_scroll_scale_starting',
            [
                'label' => __( 'Starting Scale', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0.6,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => 0.1,
                    ],
                ],
                'condition' => [
                    'codexse_scroll' => 'yes',
                ],
            ]
        );
        
        $element->end_popover();    
        
        $element->add_control(
            'codexse_scroll_slideX_toggle',
            [
                'label' => __( 'SlideX Control', 'codexse-addons' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition' => [
                    'codexse_scroll' => 'yes',
                ]
            ]
        );
        
        $element->start_popover();
        
        $element->add_control(
            'codexse_scroll_slideX_distance',
            [
                'label' => __( 'Distance', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 248,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1500,
                        'step' => 1,
                    ],
                ],
            ]
        );
        
        $element->add_control(
            'codexse_scroll_slideX_amount',
            [
                'label' => __( 'Amount', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 500,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 1,
                    ],
                ],
            ]
        );
        
        $element->end_popover();    
    
        $element->add_control(
            'codexse_scroll_slideY_toggle',
            [
                'label' => __( 'SlideY Control', 'codexse-addons' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition' => [
                    'codexse_scroll' => 'yes',
                ]
            ]
        );
        
        $element->start_popover();
        
        $element->add_control(
            'codexse_scroll_slideY_distance',
            [
                'label' => __( 'Distance', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 248,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1500,
                        'step' => 1,
                    ],
                ],
            ]
        );
        
        $element->add_control(
            'codexse_scroll_slideY_amount',
            [
                'label' => __( 'Amount', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 500,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 1,
                    ],
                ],
            ]
        );
        
        $element->end_popover();  
        
        

        $element->add_control(
            'codexse_scroll_jiggle_toggle',
            [
                'label' => __( 'Jiggle Control', 'codexse-addons' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition' => [
                    'codexse_scroll' => 'yes',
                ]
            ]
        );
        
        $element->start_popover();
        
        $element->add_control(
            'codexse_scroll_jiggle_distance',
            [
                'label' => __( 'Distance', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 50,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                ],
            ]
        );
        
        $element->add_control(
            'codexse_scroll_jiggle_strength',
            [
                'label' => __( 'Strength', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
            ]
        );
        
        $element->end_popover();  
    


        $element->add_control(
            'codexse_scroll_seesaw_toggle',
            [
                'label' => __( 'Seesaw Control', 'codexse-addons' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition' => [
                    'codexse_scroll' => 'yes',
                ]
            ]
        );
        
        $element->start_popover();
        
        $element->add_control(
            'codexse_scroll_seesaw_distance',
            [
                'label' => __( 'Distance', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 40,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                ],
            ]
        );
        
        $element->add_control(
            'codexse_scroll_seesaw_strength',
            [
                'label' => __( 'Strength', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
            ]
        );
        
        $element->end_popover();  
    



        $element->add_control(
            'codexse_scroll_zigzag_toggle',
            [
                'label' => __( 'Zigzag Control', 'codexse-addons' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition' => [
                    'codexse_scroll' => 'yes',
                ]
            ]
        );
        
        $element->start_popover();
        
        $element->add_control(
            'codexse_scroll_zigzag_distance',
            [
                'label' => __( 'Distance', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 170,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                ],
            ]
        );
        
        $element->add_control(
            'codexse_scroll_zigzag_strength',
            [
                'label' => __( 'Strength', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 200,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 500,
                        'step' => 1,
                    ],
                ],
            ]
        );
        
        $element->end_popover();








        $element->add_control(
            'codexse_scroll_hueRotate_toggle',
            [
                'label' => __( 'HueRotate Control', 'codexse-addons' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition' => [
                    'codexse_scroll' => 'yes',
                ]
            ]
        );
        
        $element->start_popover();
        
        $element->add_control(
            'codexse_scroll_hueRotate_distance',
            [
                'label' => __( 'Distance', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 962,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 3000,
                        'step' => 1,
                    ],
                ],
            ]
        );
        
        $element->add_control(
            'codexse_scroll_hueRotate_strength',
            [
                'label' => __( 'Strength', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 360,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 360,
                        'step' => 1,
                    ],
                ],
            ]
        );
        
        $element->end_popover();





        $element->add_control(
            'codexse_scroll_spin_toggle',
            [
                'label' => __( 'Spin Control', 'codexse-addons' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition' => [
                    'codexse_scroll' => 'yes',
                ]
            ]
        );
        
        $element->start_popover();
        
        $element->add_control(
            'codexse_scroll_spin_distance',
            [
                'label' => __( 'Distance', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 603,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 3000,
                        'step' => 1,
                    ],
                ],
            ]
        );
        
        $element->add_control(
            'codexse_scroll_spin_strength',
            [
                'label' => __( 'Strength', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 360,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 360,
                        'step' => 1,
                    ],
                ],
            ]
        );
        
        $element->end_popover();





        $element->add_control(
            'codexse_scroll_flipX_toggle',
            [
                'label' => __( 'FlipX Control', 'codexse-addons' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition' => [
                    'codexse_scroll' => 'yes',
                ]
            ]
        );
        
        $element->start_popover();
        
        $element->add_control(
            'codexse_scroll_flipX_distance',
            [
                'label' => __( 'Distance', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 603,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 3000,
                        'step' => 1,
                    ],
                ],
            ]
        );
        
        $element->add_control(
            'codexse_scroll_flipX_strength',
            [
                'label' => __( 'Strength', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 360,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 360,
                        'step' => 1,
                    ],
                ],
            ]
        );
        
        $element->end_popover();

        $element->add_control(
            'codexse_scroll_flipY_toggle',
            [
                'label' => __( 'FlipY Control', 'codexse-addons' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition' => [
                    'codexse_scroll' => 'yes',
                ]
            ]
        );
        
        $element->start_popover();
        
        $element->add_control(
            'codexse_scroll_flipY_distance',
            [
                'label' => __( 'Distance', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 603,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 3000,
                        'step' => 1,
                    ],
                ],
            ]
        );
        
        $element->add_control(
            'codexse_scroll_flipY_strength',
            [
                'label' => __( 'Strength', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 360,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 360,
                        'step' => 1,
                    ],
                ],
            ]
        );
        
        $element->end_popover();



        $element->add_control(
            'codexse_scroll_blur_toggle',
            [
                'label' => __( 'Blur Control', 'codexse-addons' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition' => [
                    'codexse_scroll' => 'yes',
                ]
            ]
        );
    
        $element->start_popover();
        // Add a control for selecting blur effect
        $element->add_control(
            'codexse_scroll_blur_effect',
            [
                'label' => __( 'Blur Effect', 'codexse-addons' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'blurIn' => __( 'Blur In', 'codexse-addons' ),
                    'blurOut' => __( 'Blur Out', 'codexse-addons' ),
                    'blurInOut' => __( 'Blur In & Out', 'codexse-addons' ),
                ],
                'default' => 'blurIn',
                'condition' => [
                    'codexse_scroll' => 'yes',
                ],
            ]
        );
    
        // Slider for Distance
        $element->add_control(
            'codexse_scroll_blur_distance',
            [
                'label' => __( 'Distance', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 150,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'codexse_scroll' => 'yes',
                ],
            ]
        );
    
        $element->add_control(
            'codexse_scroll_blur_strength',
            [
                'label' => __( 'Strength', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'codexse_scroll' => 'yes',
                ],
            ]
        );
        $element->end_popover();
    

        $element->end_controls_section();
    }
    


    public function add_lax_attributes( $element ) {
        $settings = $element->get_settings_for_display();
        
        // Check if scroll effects are enabled
        if ( isset( $settings['codexse_scroll'] ) && 'yes' === $settings['codexse_scroll'] ) {
            $lax_class = 'lax';
            
            // Get fade effect settings
            $fade_switch = isset( $settings['codexse_scroll_fade_toggle'] ) ? $settings['codexse_scroll_fade_toggle'] : 'no';
            $fade_effect = isset( $settings['codexse_scroll_fade_effect'] ) ? $settings['codexse_scroll_fade_effect'] : null;
            $fade_distance = isset( $settings['codexse_scroll_fade_distance'] ) ? $settings['codexse_scroll_fade_distance']['size'] : null;
            $fade_opacity = isset( $settings['codexse_scroll_fade_opacity'] ) ? $settings['codexse_scroll_fade_opacity']['size'] : null;


            // Get scale effect settings
            $scale_switch = isset( $settings['codexse_scroll_scale_toggle'] ) ? $settings['codexse_scroll_scale_toggle'] : 'no';
            $scale_effect = isset( $settings['codexse_scroll_scale_effect'] ) ? $settings['codexse_scroll_scale_effect'] : null;
            $scale_distance = isset( $settings['codexse_scroll_scale_distance'] ) ? $settings['codexse_scroll_scale_distance']['size'] : null;
            $scale_starting = isset( $settings['codexse_scroll_scale_starting'] ) ? $settings['codexse_scroll_scale_starting']['size'] : null;

            // Get slide effect settings
            $slideX_switch = isset( $settings['codexse_scroll_slideX_toggle'] ) ? $settings['codexse_scroll_slideX_toggle'] : 'no';
            $slideX_distance = isset( $settings['codexse_scroll_slideX_distance'] ) ? $settings['codexse_scroll_slideX_distance']['size'] : null;
            $slideX_amount = isset( $settings['codexse_scroll_slideX_amount'] ) ? $settings['codexse_scroll_slideX_amount']['size'] : null;


            $slideY_switch = isset( $settings['codexse_scroll_slideY_toggle'] ) ? $settings['codexse_scroll_slideY_toggle'] : 'no';
            $slideY_distance = isset( $settings['codexse_scroll_slideY_distance'] ) ? $settings['codexse_scroll_slideY_distance']['size'] : null;
            $slideY_amount = isset( $settings['codexse_scroll_slideY_amount'] ) ? $settings['codexse_scroll_slideY_amount']['size'] : null;


            $jiggle_switch = isset( $settings['codexse_scroll_jiggle_toggle'] ) ? $settings['codexse_scroll_jiggle_toggle'] : 'no';
            $jiggle_distance = isset( $settings['codexse_scroll_jiggle_distance'] ) ? $settings['codexse_scroll_jiggle_distance']['size'] : null;
            $jiggle_strength = isset( $settings['codexse_scroll_jiggle_strength'] ) ? $settings['codexse_scroll_jiggle_strength']['size'] : null;


            $seesaw_switch = isset( $settings['codexse_scroll_seesaw_toggle'] ) ? $settings['codexse_scroll_seesaw_toggle'] : 'no';
            $seesaw_distance = isset( $settings['codexse_scroll_seesaw_distance'] ) ? $settings['codexse_scroll_seesaw_distance']['size'] : null;
            $seesaw_strength = isset( $settings['codexse_scroll_seesaw_strength'] ) ? $settings['codexse_scroll_seesaw_strength']['size'] : null;


            $zigzag_switch = isset( $settings['codexse_scroll_zigzag_toggle'] ) ? $settings['codexse_scroll_zigzag_toggle'] : 'no';
            $zigzag_distance = isset( $settings['codexse_scroll_zigzag_distance'] ) ? $settings['codexse_scroll_zigzag_distance']['size'] : null;
            $zigzag_strength = isset( $settings['codexse_scroll_zigzag_strength'] ) ? $settings['codexse_scroll_zigzag_strength']['size'] : null;


            $hueRotate_switch = isset( $settings['codexse_scroll_hueRotate_toggle'] ) ? $settings['codexse_scroll_hueRotate_toggle'] : 'no';
            $hueRotate_distance = isset( $settings['codexse_scroll_hueRotate_distance'] ) ? $settings['codexse_scroll_hueRotate_distance']['size'] : null;
            $hueRotate_strength = isset( $settings['codexse_scroll_hueRotate_strength'] ) ? $settings['codexse_scroll_hueRotate_strength']['size'] : null;
                

            $spin_switch = isset( $settings['codexse_scroll_spin_toggle'] ) ? $settings['codexse_scroll_spin_toggle'] : 'no';
            $spin_distance = isset( $settings['codexse_scroll_spin_distance'] ) ? $settings['codexse_scroll_spin_distance']['size'] : null;
            $spin_strength = isset( $settings['codexse_scroll_spin_strength'] ) ? $settings['codexse_scroll_spin_strength']['size'] : null;
                

            $flipX_switch = isset( $settings['codexse_scroll_flipX_toggle'] ) ? $settings['codexse_scroll_flipX_toggle'] : 'no';
            $flipX_distance = isset( $settings['codexse_scroll_flipX_distance'] ) ? $settings['codexse_scroll_flipX_distance']['size'] : null;
            $flipX_strength = isset( $settings['codexse_scroll_flipX_strength'] ) ? $settings['codexse_scroll_flipX_strength']['size'] : null;
                

            $flipY_switch = isset( $settings['codexse_scroll_flipY_toggle'] ) ? $settings['codexse_scroll_flipY_toggle'] : 'no';
            $flipY_distance = isset( $settings['codexse_scroll_flipY_distance'] ) ? $settings['codexse_scroll_flipY_distance']['size'] : null;
            $flipY_strength = isset( $settings['codexse_scroll_flipY_strength'] ) ? $settings['codexse_scroll_flipY_strength']['size'] : null;


            $blur_switch = isset( $settings['codexse_scroll_blur_toggle'] ) ? $settings['codexse_scroll_blur_toggle'] : 'no';
            $blur_effect = isset( $settings['codexse_scroll_blur_effect'] ) ? $settings['codexse_scroll_blur_effect'] : null;
            $blur_distance = isset( $settings['codexse_scroll_blur_distance'] ) ? $settings['codexse_scroll_blur_distance']['size'] : null;
            $blur_strength = isset( $settings['codexse_scroll_blur_strength'] ) ? $settings['codexse_scroll_blur_strength']['size'] : null;
            
                
  
            // Construct the fade effect class if all fade settings are present
            if ( $fade_switch == 'yes' && isset( $fade_effect, $fade_distance, $fade_opacity ) ) {
                $lax_class .= ' lax_preset_' . $fade_effect . ':' . $fade_distance . ':' . $fade_opacity;
            }

            // Construct the scale effect class if all scale settings are present
            if ( $scale_switch == 'yes' && isset( $scale_effect, $scale_distance, $scale_starting ) ) {
                $lax_class .= ' lax_preset_' . $scale_effect . ':' . $scale_distance . ':' . $scale_starting;
            }
            
            // Construct the scale effect class if all scale settings are present
            if (  $slideX_switch == 'yes' && isset( $slideX_distance, $slideX_amount ) ) {
                $lax_class .= ' lax_preset_slideX:' . $slideX_distance . ':' . $slideX_amount;
            }

            // Construct the scale effect class if all scale settings are present
            if (  $slideY_switch == 'yes' && isset( $slideY_distance, $slideY_amount ) ) {
                $lax_class .= ' lax_preset_slideY:' . $slideY_distance . ':' . $slideY_amount;
            }

            // Construct the scale effect class if all scale settings are present
            
            if (  $jiggle_switch == 'yes' && isset( $jiggle_distance, $jiggle_strength ) ) {
                $lax_class .= ' lax_preset_jiggle:' . $jiggle_distance . ':' . $jiggle_strength;
            }

            if (  $seesaw_switch == 'yes' && isset( $seesaw_distance, $seesaw_strength ) ) {
                $lax_class .= ' lax_preset_seesaw:' . $seesaw_distance . ':' . $seesaw_strength;
            }

            if (  $zigzag_switch == 'yes' && isset( $zigzag_distance, $zigzag_strength ) ) {
                $lax_class .= ' lax_preset_zigzag:' . $zigzag_distance . ':' . $zigzag_strength;
            }
            
            if (  $hueRotate_switch == 'yes' && isset( $hueRotate_distance, $hueRotate_strength ) ) {
                $lax_class .= ' lax_preset_hueRotate:' . $hueRotate_distance . ':' . $hueRotate_strength;
            }
            
            if (  $spin_switch == 'yes' && isset( $spin_distance, $spin_strength ) ) {
                $lax_class .= ' lax_preset_spin:' . $spin_distance . ':' . $spin_strength;
            }

            if (  $flipX_switch == 'yes' && isset( $flipX_distance, $flipX_strength ) ) {
                $lax_class .= ' lax_preset_flipX:' . $flipX_distance . ':' . $flipX_strength;
            }

            if (  $flipY_switch == 'yes' && isset( $flipY_distance, $flipY_strength ) ) {
                $lax_class .= ' lax_preset_flipY:' . $flipY_distance . ':' . $flipY_strength;
            }

            if (  $blur_switch == 'yes' && isset( $blur_effect, $blur_distance, $blur_strength ) ) {
                $lax_class .= ' lax_preset_' . $blur_effect . ':' . $blur_distance . ':' . $blur_strength;
            }
            // Add the constructed class to the element
            $element->add_render_attribute( '_wrapper', 'class', $lax_class );
        }
    }
}

// Initialize the class
Codexse_Scroll_Effect::instance();
