<?php
namespace Elementor;

// Elementor Classes
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class folioedgecore_Elementor_Widget_Countdown extends Widget_Base {

    public function get_name() {
        return 'folioedgecore-countdown-addons';
    }
    
    public function get_title() {
        return __( 'Countdown', 'folioedgecore' );
    }

    public function get_icon() {
        return 'folioedge-icon eicon-countdown';
    }
    
    public function get_categories() {
        return [ 'folioedgecore' ];
    }

    public function get_script_depends() {
        return [
            'countdown',
            'addons-active',
        ];
    }

    protected function register_controls() {

        // Start Date option tab 
        $this->start_controls_section(
            'countdown_content',
            [
                'label' => __( 'Countdown', 'folioedgecore' ),
            ]
        );

            $this->add_control(
                'target_date',
                [
                    'label'       => __( 'Due Date', 'folioedgecore' ),
                    'type'        => Controls_Manager::DATE_TIME,
                    'picker_options'=>array(
                        'dateFormat' =>"Y/m/d",
                    ),
                    'default'     => date( 'Y/m/d', strtotime( '+1 month' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ),
                ]
            );

            $this->add_control(
                'counter_timing_heading',
                [
                    'label' => __( 'Time Setting', 'woomentor' ),
                    'type' => Controls_Manager::HEADING,
                ]
            );

            $this->add_control(
                'count_down_days',
                [
                    'label'        => __( 'Day', 'folioedgecore' ),
                    'type'         => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' =>'yes',
                ]
            );

            $this->add_control(
                'count_down_hours',
                [
                    'label'        => __( 'Hours', 'folioedgecore' ),
                    'type'         => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' =>'yes',
                ]
            );

            $this->add_control(
                'count_down_miniute',
                [
                    'label'        => __( 'Minutes', 'folioedgecore' ),
                    'type'         => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' =>'yes',
                ]
            );

            $this->add_control(
                'count_down_second',
                [
                    'label'        => __( 'Seconds', 'folioedgecore' ),
                    'type'         => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' =>'yes',
                ]
            );

            $this->add_control(
                'counter_lavel_heading',
                [
                    'label' => __( 'Label Setting', 'woomentor' ),
                    'type' => Controls_Manager::HEADING,
                ]
            );

            $this->add_control(
                'count_down_labels',
                [
                    'label'        => __( 'Hide Label', 'folioedgecore' ),
                    'type'         => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' =>'no',
                ]
            );

            $this->add_control(
                'custom_labels',
                [
                    'label'        => __( 'Custom Label', 'folioedgecore' ),
                    'type'         => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'condition'   => [
                        'count_down_labels!' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'customlabel_days',
                [
                    'label'       => __( 'Days', 'folioedgecore' ),
                    'type'        => Controls_Manager::TEXT,
                    'placeholder' => __( 'Days', 'folioedgecore' ),
                    'condition'   => [
                        'custom_labels!'     => '',
                        'count_down_labels!' => 'yes',
                        'count_down_days'    => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'customlabel_hours',
                [
                    'label'       => __( 'Hours', 'folioedgecore' ),
                    'type'        => Controls_Manager::TEXT,
                    'placeholder' => __( 'Hours', 'folioedgecore' ),
                    'condition'   => [
                        'custom_labels!'     => '',
                        'count_down_labels!' => 'yes',
                        'count_down_hours'   => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'customlabel_minutes',
                [
                    'label'       => __( 'Minutes', 'folioedgecore' ),
                    'type'        => Controls_Manager::TEXT,
                    'placeholder' => __( 'Minutes', 'folioedgecore' ),
                    'condition'   => [
                        'custom_labels!'     => '',
                        'count_down_labels!' => 'yes',
                        'count_down_miniute' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'customlabel_seconds',
                [
                    'label'       => __( 'Seconds', 'folioedgecore' ),
                    'type'        => Controls_Manager::TEXT,
                    'placeholder' => __( 'Seconds', 'folioedgecore' ),
                    'condition'   => [
                        'custom_labels!'     => '',
                        'count_down_labels!' => 'yes',
                        'count_down_second'  => 'yes',
                    ],
                ]
            );

        $this->end_controls_section(); // Date Optiin end

        // Feature Style tab section
        $this->start_controls_section(
            'folioedge_counter_area_style_section',
            [
                'label' => __( 'Counter Area', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->start_controls_tabs('counter_area_box_style_tab');
        $this->start_controls_tab( 'counter_area_box_normal',
			[
				'label' => __( 'Normal', 'folioedgecore' ),
			]
		);
        
        $this->add_responsive_control(
            'counter_area_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .folioedgecore-countdown' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'counter_area_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .folioedgecore-countdown' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'counter_area_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .folioedgecore-countdown',
            ]
        );

        $this->add_responsive_control(
            'counter_area_text_align',
            [
                'label' => __( 'Alignment', 'folioedgecore' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __( 'Left', 'folioedgecore' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'folioedgecore' ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'flex-end' => [
                        'title' => __( 'Right', 'folioedgecore' ),
                        'icon' => 'fa fa-align-right',
                    ],
                    'space-between' => [
                        'title' => __( 'Justified', 'folioedgecore' ),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .folioedgecore-countdown' => 'justify-content: {{VALUE}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'counter_area_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .folioedgecore-countdown',
            ]
        );
        $this->add_responsive_control(
            'counter_area_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .folioedgecore-countdown' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'counter_area_box_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .folioedgecore-countdown',
            ]
        );

        
        $this->add_control(
			'counter_area_box_transform',
			[
				'label' => __( 'Transform', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'translateY(0)',
				'selectors' => [
					'{{WRAPPER}} .folioedgecore-countdown' => 'transform: {{VALUE}}',
				],
			]
		);
        
		$this->add_control(
			'counter_area_box_transition',
			[
				'label' => __( 'Transition Duration', 'folioedgecore' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.3,
				],
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .folioedgecore-countdown' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
        
		$this->add_responsive_control(
			'counter_area_width',
			[
				'label' => __( 'Width', 'folioedgecore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw', 'vh' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .folioedgecore-countdown' => 'width: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);  
        
		$this->add_responsive_control(
			'counter_area_height',
			[
				'label' => __( 'Height', 'folioedgecore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw', 'vh' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .folioedgecore-countdown' => 'height: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);  
		$this->end_controls_tab();

             
        // Hover Style tab Start
        $this->start_controls_tab(
            'counter_area_box_hover',
            [
                'label' => __( 'Hover', 'folioedgecore' ),
            ]
        );
                
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'counter_area_hover_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .folioedgecore-countdown:hover',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'counter_area_border_hover',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .folioedgecore-countdown:hover',
            ]
        );
        $this->add_responsive_control(
            'counter_area_hover_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .folioedgecore-countdown:hover' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'counter_area_box_hover_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .folioedgecore-countdown:hover',
            ]
        );
        $this->add_control(
			'counter_area_box_hover_transform',
			[
				'label' => __( 'Transform', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'translateY(0)',
				'selectors' => [
					'{{WRAPPER}} .folioedgecore-countdown:hover' => 'transform: {{VALUE}}',
				],
			]
		);
        
        $this->end_controls_tab(); // Hover Style tab end        
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section(); // Feature Box section style end
        
        // Feature Style tab section
        $this->start_controls_section(
            'folioedge_counter_item_style_section',
            [
                'label' => __( 'Counter Item', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->start_controls_tabs('counter_item_box_style_tab');
        $this->start_controls_tab( 'counter_item_box_normal',
			[
				'label' => __( 'Normal', 'folioedgecore' ),
			]
		);
        
        $this->add_responsive_control(
            'counter_item_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .folioedgecore-countdown .folioedge-count' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'counter_item_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .folioedgecore-countdown .folioedge-count' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'counter_item_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .folioedgecore-countdown .folioedge-count',
            ]
        );

        $this->add_responsive_control(
            'counter_item_text_align',
            [
                'label' => __( 'Alignment', 'folioedgecore' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'folioedgecore' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'folioedgecore' ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'folioedgecore' ),
                        'icon' => 'fa fa-align-right',
                    ],
                    'justify' => [
                        'title' => __( 'Justified', 'folioedgecore' ),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .folioedgecore-countdown .folioedge-count' => 'text-align: {{VALUE}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'counter_item_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .folioedgecore-countdown .folioedge-count',
            ]
        );
        $this->add_responsive_control(
            'counter_item_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .folioedgecore-countdown .folioedge-count' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'counter_item_box_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .folioedgecore-countdown .folioedge-count',
            ]
        );

        
        $this->add_control(
			'counter_item_box_transform',
			[
				'label' => __( 'Transform', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'translateY(0)',
				'selectors' => [
					'{{WRAPPER}} .folioedgecore-countdown .folioedge-count' => 'transform: {{VALUE}}',
				],
			]
		);
        
		$this->add_control(
			'counter_item_box_transition',
			[
				'label' => __( 'Transition Duration', 'folioedgecore' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.3,
				],
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .folioedgecore-countdown .folioedge-count' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
        
		$this->add_responsive_control(
			'counter_item_box_width',
			[
				'label' => __( 'Width', 'folioedgecore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw', 'vh' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .folioedgecore-countdown .folioedge-count' => 'width: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);  
        
		$this->add_responsive_control(
			'counter_item_box_height',
			[
				'label' => __( 'Height', 'folioedgecore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw', 'vh' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .folioedgecore-countdown .folioedge-count' => 'height: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);        
        
		$this->end_controls_tab();

             
        // Hover Style tab Start
        $this->start_controls_tab(
            'counter_item_box_hover',
            [
                'label' => __( 'Hover', 'folioedgecore' ),
            ]
        );
                
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'counter_item_hover_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .folioedgecore-countdown .folioedge-count:hover',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'counter_item_border_hover',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .folioedgecore-countdown .folioedge-count:hover',
            ]
        );
        $this->add_responsive_control(
            'counter_item_hover_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .folioedgecore-countdown .folioedge-count:hover' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'counter_item_box_hover_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .folioedgecore-countdown .folioedge-count:hover',
            ]
        );
        $this->add_control(
			'counter_item_box_hover_transform',
			[
				'label' => __( 'Transform', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'translateY(0)',
				'selectors' => [
					'{{WRAPPER}} .folioedgecore-countdown .folioedge-count:hover' => 'transform: {{VALUE}}',
				],
			]
		);
        
        $this->end_controls_tab(); // Hover Style tab end        
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section(); // Feature Box section style end
        
               
        // Feature Style tab section
        $this->start_controls_section(
            'counter_number_section',
            [
                'label' => __( 'Number', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->start_controls_tabs('counter_number_tabs');
        
        $this->start_controls_tab( 'counter_number_tab',
			[
				'label' => __( 'Normal', 'folioedgecore' ),
			]
		);        
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'counter_number_typography',
                'selector' => '{{WRAPPER}} .folioedge-count .time-count',
            ]
        );
        $this->add_control(
            'counter_number_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .folioedge-count .time-count' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'counter_number_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .folioedge-count .time-count' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'counter_number_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .folioedge-count .time-count' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
                
		$this->add_control(
			'counter_number_transition',
			[
				'label' => __( 'Transition Duration', 'folioedgecore' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.3,
				],
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .folioedge-count .time-count' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
        
        $this->end_controls_tab(); // Hover Style tab end
         
        $this->start_controls_tab( 'counter_number_hover_tab',
			[
				'label' => __( 'Hover', 'folioedgecore' ),
			]
		);        
        
        $this->add_control(
            'counter_number_hover_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .folioedge-count:hover .time-count' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section();
        
        
        
        // Feature Style tab section
        $this->start_controls_section(
            'counter_label_section',
            [
                'label' => __( 'Label', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->start_controls_tabs('counter_label_tabs');
        
        $this->start_controls_tab( 'counter_label_tab',
			[
				'label' => __( 'Normal', 'folioedgecore' ),
			]
		);        
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'counter_label_typography',
                'selector' => '{{WRAPPER}} .folioedge-count .count-label',
            ]
        );
        $this->add_control(
            'counter_label_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .folioedge-count .count-label' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'counter_label_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .folioedge-count .count-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'counter_label_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .folioedge-count .count-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
                
		$this->add_control(
			'counter_label_transition',
			[
				'label' => __( 'Transition Duration', 'folioedgecore' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.3,
				],
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .folioedge-count .count-label' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
        
        $this->end_controls_tab(); // Hover Style tab end
         
        $this->start_controls_tab( 'counter_label_hover_tab',
			[
				'label' => __( 'Hover', 'folioedgecore' ),
			]
		);        
        
        $this->add_control(
            'counter_label_hover_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .folioedge-count:hover .count-label' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section();
        
        
        
    }

    protected function render( $instance = [] ) {

        $settings   = $this->get_settings_for_display();
        $data_options = [];

        $data_options['folioedgecoredate'] = isset( $settings['target_date'] ) ? $settings['target_date'] : date( 'Y/m/d', strtotime( '+1 month' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );

        // Hide Countdownload item
        $data_options['lavelhide']      = $settings['count_down_labels'];
        $data_options['folioedgecoreday']      = $settings['count_down_days'];
        $data_options['folioedgecorehours']    = $settings['count_down_hours'];
        $data_options['folioedgecoreminiute']  = $settings['count_down_miniute'];
        $data_options['folioedgecoresecond']   = $settings['count_down_second'];

        // Custom Label
        $data_options['folioedgecoredaytxt'] = ! empty( $settings['customlabel_days'] ) ? $settings['customlabel_days'] : 'Days';
        $data_options['folioedgecorehourtxt'] = ! empty( $settings['customlabel_hours'] ) ? $settings['customlabel_hours'] : 'Hours';
        $data_options['folioedgecoreminutestxt'] = ! empty( $settings['customlabel_minutes'] ) ? $settings['customlabel_minutes'] : 'Minutes';
        $data_options['folioedgecoresecondstxt'] = ! empty( $settings['customlabel_seconds'] ) ? $settings['customlabel_seconds'] : 'Seconds';
        
        $this->add_render_attribute( 'countdown_wrapper_attr', 'class', 'folioedgecore-countdown' );
        
        echo '<div '.$this->get_render_attribute_string( 'countdown_wrapper_attr' ).' data-countdown=\'' . wp_json_encode( $data_options ) . '\' ></div>';
        
    }
}


Plugin::instance()->widgets_manager->register_widget_type( new folioedgecore_Elementor_Widget_Countdown );