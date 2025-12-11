<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Elementor\Controls_Manager;
use Elementor\Element_Base;

class Codexse_Floating_Effect {
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
        add_action( 'elementor/element/common/_section_style/after_section_end', [ $this , 'floating_options' ], 1 );
        add_action( 'elementor/frontend/before_register_scripts', [ $this, 'register_scripts' ] );
    }
	
    // Register scripts
    public function register_scripts() {
        wp_register_script( 'anime', CODEXSE_JS_URL . 'options/anime-min.js', array( 'jquery' ), CODEXSE_VERSION, true );
        wp_register_script( 'codexse-floating-effects', CODEXSE_JS_URL . 'options/codexse-floating-effects.js', array( 'jquery' ), CODEXSE_VERSION, true );
    }

    // Add custom section options to Elementor
    public function floating_options( Element_Base $element ) {
		$element->start_controls_section(
			'codexse_section_floating_effects',
			[
				'label' => __( 'Floating Effects', 'codexse-addons' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->add_control(
			'codexse_floating_fx',
			[
				'label' => __( 'Enable', 'codexse-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'assets' => [
					'scripts' => [
						[
							'name' => 'anime',
							'conditions' => [
								'terms' => [
									[
										'name' => 'codexse_floating_fx',
										'operator' => '===',
										'value' => 'yes',
									],
								],
							],
						],
						[
							'name' => 'codexse-floating-effects',
							'conditions' => [
								'terms' => [
									[
										'name' => 'codexse_floating_fx',
										'operator' => '===',
										'value' => 'yes',
									],
								],
							],
						],
					],
				],
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'codexse_floating_fx_translate_toggle',
			[
				'label' => __( 'Translate', 'codexse-addons' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'frontend_available' => true,
				'condition' => [
					'codexse_floating_fx' => 'yes',
				]
			]
		);

		$element->start_popover();

		$element->add_control(
			'codexse_floating_fx_translate_x',
			[
				'label' => __( 'Translate X', 'codexse-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 0,
						'to' => 5,
					],
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					]
				],
				'labels' => [
					__( 'From', 'codexse-addons' ),
					__( 'To', 'codexse-addons' ),
				],
				'scales' => 1,
				'handles' => 'range',
				'condition' => [
					'codexse_floating_fx_translate_toggle' => 'yes',
					'codexse_floating_fx' => 'yes',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'codexse_floating_fx_translate_y',
			[
				'label' => __( 'Translate Y', 'codexse-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 0,
						'to' => 5,
					],
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					]
				],
				'labels' => [
					__( 'From', 'codexse-addons' ),
					__( 'To', 'codexse-addons' ),
				],
				'scales' => 1,
				'handles' => 'range',
				'condition' => [
					'codexse_floating_fx_translate_toggle' => 'yes',
					'codexse_floating_fx' => 'yes',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'codexse_floating_fx_translate_duration',
			[
				'label' => __( 'Duration', 'codexse-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10000,
						'step' => 100
					]
				],
				'default' => [
					'size' => 1000,
				],
				'condition' => [
					'codexse_floating_fx_translate_toggle' => 'yes',
					'codexse_floating_fx' => 'yes',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'codexse_floating_fx_translate_delay',
			[
				'label' => __( 'Delay', 'codexse-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5000,
						'step' => 100
					]
				],
				'condition' => [
					'codexse_floating_fx_translate_toggle' => 'yes',
					'codexse_floating_fx' => 'yes',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->end_popover();

		$element->add_control(
			'codexse_floating_fx_rotate_toggle',
			[
				'label' => __( 'Rotate', 'codexse-addons' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'frontend_available' => true,
				'condition' => [
					'codexse_floating_fx' => 'yes',
				]
			]
		);

		$element->start_popover();

		$element->add_control(
			'codexse_floating_fx_rotate_x',
			[
				'label' => __( 'Rotate X', 'codexse-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 0,
						'to' => 45,
					],
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'labels' => [
					__( 'From', 'codexse-addons' ),
					__( 'To', 'codexse-addons' ),
				],
				'scales' => 1,
				'handles' => 'range',
				'condition' => [
					'codexse_floating_fx_rotate_toggle' => 'yes',
					'codexse_floating_fx' => 'yes',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'codexse_floating_fx_rotate_y',
			[
				'label' => __( 'Rotate Y', 'codexse-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 0,
						'to' => 45,
					],
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'labels' => [
					__( 'From', 'codexse-addons' ),
					__( 'To', 'codexse-addons' ),
				],
				'scales' => 1,
				'handles' => 'range',
				'condition' => [
					'codexse_floating_fx_rotate_toggle' => 'yes',
					'codexse_floating_fx' => 'yes',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'codexse_floating_fx_rotate_z',
			[
				'label' => __( 'Rotate Z', 'codexse-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 0,
						'to' => 45,
					],
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'labels' => [
					__( 'From', 'codexse-addons' ),
					__( 'To', 'codexse-addons' ),
				],
				'scales' => 1,
				'handles' => 'range',
				'condition' => [
					'codexse_floating_fx_rotate_toggle' => 'yes',
					'codexse_floating_fx' => 'yes',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'codexse_floating_fx_rotate_duration',
			[
				'label' => __( 'Duration', 'codexse-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10000,
						'step' => 100
					]
				],
				'default' => [
					'size' => 1000,
				],
				'condition' => [
					'codexse_floating_fx_rotate_toggle' => 'yes',
					'codexse_floating_fx' => 'yes',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'codexse_floating_fx_rotate_delay',
			[
				'label' => __( 'Delay', 'codexse-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5000,
						'step' => 100
					]
				],
				'condition' => [
					'codexse_floating_fx_rotate_toggle' => 'yes',
					'codexse_floating_fx' => 'yes',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->end_popover();

		$element->add_control(
			'codexse_floating_fx_scale_toggle',
			[
				'label' => __( 'Scale', 'codexse-addons' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'frontend_available' => true,
				'condition' => [
					'codexse_floating_fx' => 'yes',
				]
			]
		);

		$element->start_popover();

		$element->add_control(
			'codexse_floating_fx_scale_x',
			[
				'label' => __( 'Scale X', 'codexse-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 1,
						'to' => 1.2,
					],
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5,
						'step' => .1
					]
				],
				'labels' => [
					__( 'From', 'codexse-addons' ),
					__( 'To', 'codexse-addons' ),
				],
				'scales' => 1,
				'handles' => 'range',
				'condition' => [
					'codexse_floating_fx_scale_toggle' => 'yes',
					'codexse_floating_fx' => 'yes',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'codexse_floating_fx_scale_y',
			[
				'label' => __( 'Scale Y', 'codexse-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 1,
						'to' => 1.2,
					],
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5,
						'step' => .1
					]
				],
				'labels' => [
					__( 'From', 'codexse-addons' ),
					__( 'To', 'codexse-addons' ),
				],
				'scales' => 1,
				'handles' => 'range',
				'condition' => [
					'codexse_floating_fx_scale_toggle' => 'yes',
					'codexse_floating_fx' => 'yes',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'codexse_floating_fx_scale_duration',
			[
				'label' => __( 'Duration', 'codexse-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10000,
						'step' => 100
					]
				],
				'default' => [
					'size' => 1000,
				],
				'condition' => [
					'codexse_floating_fx_scale_toggle' => 'yes',
					'codexse_floating_fx' => 'yes',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'codexse_floating_fx_scale_delay',
			[
				'label' => __( 'Delay', 'codexse-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5000,
						'step' => 100
					]
				],
				'condition' => [
					'codexse_floating_fx_scale_toggle' => 'yes',
					'codexse_floating_fx' => 'yes',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);
		$element->end_popover();
		$element->end_controls_section();
    }
}

// Initialize the singleton instance
Codexse_Floating_Effect::instance();
