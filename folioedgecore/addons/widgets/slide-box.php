<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Folioedge_Slide_Box extends Widget_Base{

	public function get_name(){
		return "slide-box";
	}    
    
	public function get_title(){
		return __( 'Slide Icon Box','folioedgecore' );
	}
    
	public function get_categories() {
		return [ 'folioedgecore' ];
	}
    
	public function get_icon() {
		return 'folioedge-icon eicon-menu-card';
	}

    protected function  register_controls(){
        $this->start_controls_section(
            'slide_box',
            [
                'label' => __( 'Slide Icon Box', 'folioedgecore' ),
            ]
        );
        
        $this->add_control(
            'icon_type',
            [
                'label' => __('Icon Type','folioedgecore'),
                'type' =>Controls_Manager::CHOOSE,
                'options' =>[
                    'img' =>[
                        'title' =>__('Image','folioedgecore'),
                        'icon' =>'eicon-image-bold',
                    ],
                    'icon' =>[
                        'title' =>__('Icon','folioedgecore'),
                        'icon' =>'eicon-icon-box',
                    ],
                ],
                'default' => 'icon',
            ]
        );

        $this->add_control(
            'image',
            [
                'label' => __('Image','folioedgecore'),
                'type'=>Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'icon_type' => 'img',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'imagesize',
                'default' => 'large',
                'separator' => 'none',
                'condition' => [
                    'icon_type' => 'img',
                ]
            ]
        );

        $this->add_control(
            'slide_icon',
            [
                'label'       => __( 'Icon', 'folioedgecore-addons' ),
                'type'        => Controls_Manager::ICONS,
                'label_block' => true,
                'condition' => [
                    'icon_type' => 'icon',
                ]
            ]
        );

        $this->add_control(
            'slide_title',
            [
                'label' => __( 'Slide Title', 'folioedgecore' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Educations for Children','folioedgecore' ),
                'placeholder' => __( 'Slide Title', 'folioedgecore' ),
            ]
        );
        $this->add_control(
            'slide_content',
            [
                'label' => __( 'Slide Content', 'folioedgecore' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incidi.','folioedgecore' ),
                'placeholder' => __( 'Slide content', 'folioedgecore' ),
            ]
        );
		$this->add_control(
			'read_switch',
			[
				'label' => __( 'Button', 'folioedgecore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'folioedgecore' ),
				'label_off' => __( 'Hide', 'folioedgecore' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
        $this->add_control(
            'read_more_button_text',
            [
                'label' => __( 'Label', 'folioedgecore' ),
                'type'=>Controls_Manager::TEXT,
				'default' => __( 'Read More','folioedgecore' ),
				'condition' => [
					'read_switch' => 'yes'
				]
            ]
        );

        $this->add_control(
            'read_more_link',
            [
                'label' => __( 'Link', 'folioedgecore' ),
                'type' => Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com', 'folioedgecore' ),
                'show_external' => true,
                'default' => [
                    'url' => '#',
                    'is_external' => false,
                    'nofollow' => false,
                ],
				'condition' => [
					'read_switch' => 'yes'
				]
            ]
        );
        $this->end_controls_section();
        
        // Slide Style tab section
        $this->start_controls_section(
            'folioedge_slide_style_section',
            [
                'label' => __( 'Box', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->start_controls_tabs('slide_box_style_tab');
        $this->start_controls_tab( 'slide_box_normal',
			[
				'label' => __( 'Front Side', 'folioedgecore' ),
			]
		);
        
        $this->add_responsive_control(
            'slide_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
		$this->add_responsive_control(
			'slide_front_width',
			[
				'label' => __( 'Width', 'folioedgecore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'{{WRAPPER}} .double-part-box .front-part,{{WRAPPER}} .double-part-box .back-part' => 'width: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);        
        
		$this->add_responsive_control(
			'slide_front_height',
			[
				'label' => __( 'Height', 'folioedgecore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'{{WRAPPER}} .double-part-box .front-part,{{WRAPPER}} .double-part-box .back-part' => 'height: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'slide_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .double-part-box .front-part',
            ]
        );
        
        $this->add_control(
            'front_overlay_color',
            [
                'label' => __( 'Overlay Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .front-part:before' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'slide_text_align',
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
                    '{{WRAPPER}} .double-part-box .front-part .content' => 'text-align: {{VALUE}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'slide_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .double-part-box .front-part .content',
            ]
        );
        $this->add_responsive_control(
            'slide_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .front-part .content' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'slide_box_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .double-part-box .front-part .content',
            ]
        );
		$this->end_controls_tab();
        
        $this->start_controls_tab( 'slide_box_back',
			[
				'label' => __( 'Back Side', 'folioedgecore' ),
			]
		);
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'slide_back_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .double-part-box .back-part',
            ]
        );
        
        $this->add_control(
            'back_overlay_color',
            [
                'label' => __( 'Overlay Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .back-part:before' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'slide_back_text_align',
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
                    '{{WRAPPER}} .double-part-box .back-part .content' => 'text-align: {{VALUE}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'slide_back_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .double-part-box .back-part .content',
            ]
        );
        $this->add_responsive_control(
            'slide_back_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .back-part .content' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'slide_back_box_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .double-part-box .back-part .content',
            ]
        );
		$this->end_controls_tab();                   
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section(); // Slide Box section style end
        
        
        // Slide Style tab section
        $this->start_controls_section(
            'box_icon_section',
            [
                'label' => __( 'Icon', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->start_controls_tabs('box_icon_style_tab');
        
        $this->start_controls_tab( 'box_icon_front',
			[
				'label' => __( 'Front Side', 'folioedgecore' ),
			]
		);        
        
		$this->add_responsive_control(
			'icon_width',
			[
				'label' => __( 'Width', 'folioedgecore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'{{WRAPPER}} .double-part-box .content .slide-icon' => 'width: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);        
        
		$this->add_responsive_control(
			'icon_height',
			[
				'label' => __( 'Height', 'folioedgecore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'{{WRAPPER}} .double-part-box .content .slide-icon' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'icon_line_height',
			[
				'label' => __( 'Line Height', 'folioedgecore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'{{WRAPPER}} .double-part-box .content .slide-icon' => 'line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
        $this->add_responsive_control(
            'icon_size',
            [
                'label' => __( 'Font Icon Size', 'folioedgecore' ),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .content .slide-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'icon_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .content .slide-icon' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'icon_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .double-part-box .content .slide-icon',
            ]
        );        
         $this->add_responsive_control(
            'icon_floting',
            [
                'label' => __( 'Float', 'folioedgecore' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-direction: row;' => [
                        'title' => __( 'Left', 'folioedgecore' ),
                        'icon' => 'eicon-arrow-left',
                    ],
                    'flex-direction: column;' => [
                        'title' => __( 'Top', 'folioedgecore' ),
                        'icon' => 'eicon-arrow-up',
                    ],
                    'flex-direction: row-reverse;' => [
                        'title' => __( 'Right', 'folioedgecore' ),
                        'icon' => 'eicon-arrow-right',
                    ],
                    'flex-direction: column-reverse;' => [
                        'title' => __( 'Bottom', 'folioedgecore' ),
                        'icon' => 'eicon-arrow-down',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .content' => 'display: flex; {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'icon_alignment',
            [
                'label' => __( 'Alignment', 'folioedgecore' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'justify-content: flex-start; align-items: flex-start;' => [
                        'title' => __( 'Start', 'folioedgecore' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'justify-content: center; align-items: center;' => [
                        'title' => __( 'Center', 'folioedgecore' ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'justify-content: flex-end; align-items: flex-end;' => [
                        'title' => __( 'End', 'folioedgecore' ),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .content' => '{{VALUE}}',
                ],
                'separator' =>'before',
            ]
        );
        $this->add_responsive_control(
            'icon_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .content .slide-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'icon_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .content .slide-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'icon_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .double-part-box .content .slide-icon',
            ]
        );
        $this->add_responsive_control(
            'icon_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
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
                    '{{WRAPPER}} .double-part-box .content .slide-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'icon_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .double-part-box .content .slide-icon',
            ]
        );        
        $this->add_control(
			'box_icon_transition',
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
					'{{WRAPPER}} .double-part-box .content .slide-icon' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
        $this->end_controls_tab(); // Hover Style tab end
        $this->start_controls_tab( 'box_icon_back',
			[
				'label' => __( 'Back Side', 'folioedgecore' ),
			]
		);        
        $this->add_control(
            'back_icon_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .back-part .content .slide-icon' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'back_icon_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .double-part-box .back-part .content .slide-icon',
            ]
        );               
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'back_icon_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .double-part-box .back-part .content .slide-icon',
            ]
        );
        $this->add_responsive_control(
            'back_icon_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
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
                    '{{WRAPPER}} .double-part-box .back-part .content .slide-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'back_icon_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .double-part-box .back-part .content .slide-icon',
            ]
        );        
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section();
                
        // Slide Style tab section
        $this->start_controls_section(
            'box_title_section',
            [
                'label' => __( 'Title', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->start_controls_tabs('box_title_style_tab');
        
        $this->start_controls_tab( 'box_title_front',
			[
				'label' => __( 'Front Side', 'folioedgecore' ),
			]
		);        
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'slide_title_typography',
                'selector' => '{{WRAPPER}} .double-part-box .content .slide-title',
            ]
        );
        $this->add_control(
            'title_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .content .slide-title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'title_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .content .slide-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'title_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .content .slide-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
                
		$this->add_control(
			'slide_title_transition',
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
					'{{WRAPPER}} .double-part-box .content .slide-title' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
        
        $this->end_controls_tab(); // Hover Style tab end
         
        $this->start_controls_tab( 'box_title_back',
			[
				'label' => __( 'Back Side', 'folioedgecore' ),
			]
		);        
        
        $this->add_control(
            'title_back_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .back-part .content .slide-title' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section();
         
        
        // Slide Style tab section
        $this->start_controls_section(
            'box_content_section',
            [
                'label' => __( 'Content', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'slide_content_typography',
                'selector' => '{{WRAPPER}} .double-part-box .content .slide-content',
            ]
        );
        $this->add_control(
            'content_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .content .slide-content' => 'color: {{VALUE}};',
                ],
            ]
        );                
        $this->add_control(
            'back_content_color',
            [
                'label' => __( 'Back Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .back-part .content .slide-content' => 'color: {{VALUE}};',
                ],
            ]
        );
                
        $this->add_responsive_control(
            'content_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .content .slide-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'content_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .content .slide-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->end_controls_section();
        
        // Slide Style tab section
        $this->start_controls_section(
            'read_more_style_section',
            [
                'label' => __( 'Read More', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'read_switch' => 'yes'
				]
            ]
        );
        
        $this->start_controls_tabs('read_more_style_tab');
        
        $this->start_controls_tab( 'read_more_normal',
			[
				'label' => __( 'Normal', 'folioedgecore' ),
			]
		);        
        
        $this->add_control(
			'read_more_width',
			[
				'label' => __( 'Width', 'folioedgecore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'{{WRAPPER}} .double-part-box .content .read-more' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);        
        
		$this->add_control(
			'button_height',
			[
				'label' => __( 'Height', 'folioedgecore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'{{WRAPPER}} .double-part-box .content .read-more' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
		$this->add_control(
			'button_line_height',
			[
				'label' => __( 'Line Height', 'folioedgecore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'{{WRAPPER}} .double-part-box .content .read-more' => 'line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);
        $this->add_control(
            'button_size',
            [
                'label' => __( 'Font Icon Size', 'folioedgecore' ),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .content .read-more' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'button_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .content .read-more' => 'color: {{VALUE}};',
                ],
            ]
        );
          $this->add_control(
            'button_background',
            [
                'label' => __( 'Background Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .content .read-more' => 'background-color: {{VALUE}};',
                ],
            ]
        );
         $this->add_responsive_control(
            'button_text_align',
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
                    '{{WRAPPER}} .double-part-box .content .read-more' => 'text-align: {{VALUE}};',
                ],
                'separator' =>'before',
            ]
        );
                
        $this->add_responsive_control(
            'button_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .content .read-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'button_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .content .read-more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .double-part-box .content .read-more',
            ]
        );
        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
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
                    '{{WRAPPER}} .double-part-box .content .read-more' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .double-part-box .content .read-more',
            ]
        );        
        $this->add_control(
			'box_button_transition',
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
					'{{WRAPPER}} .double-part-box .content .read-more' => 'transition-duration: {{SIZE}}s',
				],
			]
		);            
        $this->end_controls_tab(); // Hover Style tab end
        
        
        $this->start_controls_tab( 'box_button_hover',
			[
				'label' => __( 'Hover', 'folioedgecore' ),
			]
		);        
        $this->add_control(
            'hover_button_color',
            [
                'label' => __( 'Hover Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .content .read-more:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'hover_button_background',
            [
                'label' => __( 'Background Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .double-part-box .content .read-more:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );             
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'hover_button_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .double-part-box .content .read-more:hover',
            ]
        );
        $this->add_responsive_control(
            'hover_button_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
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
                    '{{WRAPPER}} .double-part-box .content .read-more:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'hover_button_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .double-part-box .content .read-more:hover',
            ]
        );
        $this->end_controls_tab(); // Hover Style tab end
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section();
    }
    protected function render() {       
		$settings = $this->get_settings_for_display();        
        $this->add_render_attribute( 'folioedge_slide_attr', 'class', 'double-part-box' );           
        
        $html_output = '';        
        $html_output .= '<div '.$this->get_render_attribute_string( 'folioedge_slide_attr' ).' >';   
            $html_output .= '<div class="front-part">';
                $html_output .= '<div class="content">';
                    if( $settings['icon_type'] == 'img' and !empty(Group_Control_Image_Size::get_attachment_image_html( $settings, 'imagesize', 'image' )) ){
                        $image = Group_Control_Image_Size::get_attachment_image_html( $settings, 'imagesize', 'image' );  
                        $html_output .= '<div class="slide-icon">'.$image.'</div>';
                    }elseif( $settings['icon_type'] == 'icon' && !empty($settings['slide_icon']['value']) ){
                        $html_output .= sprintf( '<div class="slide-icon" >%1$s</div>', folioedge_icon_manager::render_icon( $settings['slide_icon'], [ 'aria-hidden' => 'true' ] ) );
                    }        
                    if( !empty($settings['slide_title']) ){
                        $html_output .= '<h4 class="slide-title">'.esc_html($settings['slide_title']).'</h4>';
                    }
                    if( !empty($settings['slide_content']) ){
                        $html_output .= '<div class="slide-content">'.wpautop(esc_html($settings['slide_content'])).'</div>';
                    }
                    // Link Generate
                    if ( $settings['read_switch'] == 'yes' ) {
                        $this->add_render_attribute( 'url', 'href', esc_url($settings['read_more_link']['url']) );            
                        if ( $settings['read_more_link']['is_external'] ) {
                            $this->add_render_attribute( 'url', 'target', '_blank' );
                        }
                        if ( !empty( $settings['read_more_link']['nofollow'] ) ) {
                            $this->add_render_attribute( 'url', 'rel', 'nofollow' );
                        }
                        $this->add_render_attribute( 'url', 'class', 'read-more');
                        $html_output .= '<a '.$this->get_render_attribute_string( 'url' ).' >'.wp_kses_post($settings['read_more_button_text']).'</a>';
                    }
                $html_output .= '</div>';
            $html_output .= '</div>';
            $html_output .= '<div class="back-part">';
                $html_output .= '<div class="content">';
                    if( $settings['icon_type'] == 'img' and !empty(Group_Control_Image_Size::get_attachment_image_html( $settings, 'imagesize', 'image' )) ){
                        $image = Group_Control_Image_Size::get_attachment_image_html( $settings, 'imagesize', 'image' );  
                        $html_output .= '<div class="slide-icon">'.$image.'</div>';
                    }elseif( $settings['icon_type'] == 'icon' && !empty($settings['slide_icon']['value']) ){
                        $html_output .= sprintf( '<div class="slide-icon" >%1$s</div>', folioedge_icon_manager::render_icon( $settings['slide_icon'], [ 'aria-hidden' => 'true' ] ) );
                    }        
                    if( !empty($settings['slide_title']) ){
                        $html_output .= '<h4 class="slide-title">'.esc_html($settings['slide_title']).'</h4>';
                    }
                    if( !empty($settings['slide_content']) ){
                        $html_output .= '<div class="slide-content">'.wpautop(esc_html($settings['slide_content'])).'</div>';
                    }
                    // Link Generate
                    if ( $settings['read_switch'] == 'yes' ) {
                        $this->add_render_attribute( 'url', 'href', esc_url($settings['read_more_link']['url']) );            
                        if ( $settings['read_more_link']['is_external'] ) {
                            $this->add_render_attribute( 'url', 'target', '_blank' );
                        }
                        if ( !empty( $settings['read_more_link']['nofollow'] ) ) {
                            $this->add_render_attribute( 'url', 'rel', 'nofollow' );
                        }
                        $this->add_render_attribute( 'url', 'class', 'read-more');
                        $html_output .= '<a '.$this->get_render_attribute_string( 'url' ).' >'.wp_kses_post($settings['read_more_button_text']).'</a>';
                    }
                $html_output .= '</div>';
            $html_output .= '</div>';
        $html_output .= '</div>';        
        echo $html_output;  
        
	}

}
Plugin::instance()->widgets_manager->register_widget_type( new Folioedge_Slide_Box );