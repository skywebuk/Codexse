<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class folioedge_Elementor_Widget_Tabs extends Widget_Base {

    public function get_name() {
        return 'folioedge-tab-addons';
    }
    
    public function get_title() {
        return __( 'Tabs', 'folioedgecore' );
    }

	public function get_icon() {
		return "folioedge-icon eicon-tabs";
	}
    
	public function get_categories() {
		return [ 'folioedgecore' ];
	} 
    
	public function get_keywords() {
		return [ 'tabs', 'accordion', 'toggle' ];
	}

    public function get_script_depends() {
        return [
            'bootstrap-js',
        ];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'tab_content',
            [
                'label' => __( 'Tabs', 'folioedgecore' ),
            ]
        );
            
            $this->add_control(
                'menu_position',
                [
                    'label'   => esc_html__( 'Menu Position', 'folioedgecore' ),
                    'type'    => Controls_Manager::SELECT,
                    'default' => 'top',
                    'options' => [
                        'top'   => esc_html__( 'Top', 'folioedgecore' ),
                        'left'  => esc_html__( 'Left', 'folioedgecore' ),
                        'right' => esc_html__( 'Right', 'folioedgecore' ),
                    ],
                ]
            );    
            
            $this->add_control(
                'menu_style',
                [
                    'label'   => esc_html__( 'Menu Style', 'folioedgecore' ),
                    'type'    => Controls_Manager::SELECT,
                    'default' => 'style1',
                    'options' => [
                        'style1' => esc_html__( 'Style 1', 'folioedgecore' ),
                        'style2' => esc_html__( 'Style 2', 'folioedgecore' ),
                        'style3' => esc_html__( 'Normal', 'folioedgecore' ),
                    ],
                ]
            );    
            $repeater = new Repeater();
            $repeater->start_controls_tabs('tab_content_item_area_tabs');

                $repeater->start_controls_tab(
                    'tab_content_item_area',
                    [
                        'label' => __( 'Content', 'folioedgecore' ),
                    ]
                );
                    
                    $repeater->add_control(
                        'tab_title',
                        [
                            'label'   => esc_html__( 'Title', 'folioedgecore' ),
                            'type'    => Controls_Manager::TEXT,
                            'default' => esc_html__( 'Tab #1', 'folioedgecore' ),
                        ]
                    );

                    $repeater->add_control(
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

                    $repeater->add_control(
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

                    $repeater->add_group_control(
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

                    $repeater->add_control(
                        'feature_icon',
                        [
                            'label'       => __( 'Icon', 'folioedgecore-addons' ),
                            'type'        => Controls_Manager::ICONS,
                            'label_block' => true,
                            'condition' => [
                                'icon_type' => 'icon',
                            ]
                        ]
                    );

                    $repeater->add_control(
                        'content_source',
                        [
                            'label'   => esc_html__( 'Select Content Source', 'folioedgecore' ),
                            'type'    => Controls_Manager::SELECT,
                            'default' => 'custom',
                            'options' => [
                                'custom'    => esc_html__( 'Content', 'folioedgecore' ),
                                "elementor" => esc_html__( 'Template', 'folioedgecore' ),
                            ],
                        ]
                    );

                     $repeater->add_control(
                        'template_id',
                        [
                            'label'       => __( 'Content', 'folioedgecore' ),
                            'type'        => Controls_Manager::SELECT,
                            'default'     => '0',
                            'options'     => folioedge_elementor_template(),
                            'condition'   => [
                                'content_source' => "elementor"
                            ],
                        ]
                    );

                     $repeater->add_control(
                        'custom_content',
                        [
                            'label' => __( 'Content', 'folioedgecore' ),
                            'type' => Controls_Manager::WYSIWYG,
                            'title' => __( 'Content', 'folioedgecore' ),
                            'show_label' => false,
                            'condition' => [
                                'content_source' =>'custom',
                            ],
                        ]
                    );

                $repeater->end_controls_tab();// Tab Content area end

                // Style area start
                $repeater->start_controls_tab(
                    'tab_item_style_area',
                    [
                        'label' => __( 'Style', 'folioedgecore' ),
                    ]
                );
                    
                    $repeater->add_control(
                        'tab_title_color',
                        [
                            'label'     => esc_html__( 'Title Color', 'folioedgecore' ),
                            'type'      => Controls_Manager::COLOR,
                            'selectors' => [
                                '{{WRAPPER}} .folioedge-tab-nav a{{CURRENT_ITEM}}' => 'color: {{VALUE}}',
                            ],
                        ]
                    );
                    
                    $repeater->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name' => 'title_background',
                            'label' => __( 'Background', 'folioedgecore' ),
                            'types' => [ 'classic', 'gradient' ],
                            'selector' => '{{WRAPPER}} .folioedge-tab-nav a{{CURRENT_ITEM}}',
                        ]
                    );

                    $repeater->add_control(
                        'tab_title_active_color',
                        [
                            'label'     => esc_html__( 'Title Active Color', 'folioedgecore' ),
                            'type'      => Controls_Manager::COLOR,
                            'selectors' => [
                                '{{WRAPPER}} .folioedge-tab-nav li.active a{{CURRENT_ITEM}}' => 'color: {{VALUE}}',
                            ],
                        ]
                    );

                    $repeater->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name' => 'title_active_background',
                            'label' => __( 'Background', 'folioedgecore' ),
                            'types' => [ 'classic', 'gradient' ],
                            'selector' => '{{WRAPPER}} .folioedge-tab-nav li.active a{{CURRENT_ITEM}}',
                        ]
                    );

                    $repeater->add_control(
                        'tab_icon_color',
                        [
                            'label'     => esc_html__( 'Icon Color', 'folioedgecore' ),
                            'type'      => Controls_Manager::COLOR,
                            'selectors' => [
                                '{{WRAPPER}} .folioedge-tab-nav a{{CURRENT_ITEM}} .icon' => 'color: {{VALUE}}',
                            ],
                            'separator' => 'before',
                        ]
                    );

                    $repeater->add_control(
                        'tab_icon_size',
                        [
                            'label' => __( 'Icon Size', 'folioedgecore' ),
                            'type'  => Controls_Manager::SLIDER,
                            'range' => [
                                'px' => [
                                    'min' => 0,
                                    'max' => 100,
                                ],
                            ],
                            'default' => [
                                'size' => 14,
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .folioedge-tab-nav a{{CURRENT_ITEM}} .icon' => 'font-size: {{SIZE}}{{UNIT}};',
                            ],
                        ]
                    );

                    $repeater->add_control(
                        'tab_icon_active_color',
                        [
                            'label'     => esc_html__( 'Active Icon Color', 'folioedgecore' ),
                            'type'      => Controls_Manager::COLOR,
                            'selectors' => [
                                '{{WRAPPER}} .folioedge-tab-nav li.active a{{CURRENT_ITEM}} .icon' => 'color: {{VALUE}}',
                            ],
                        ]
                    );

                $repeater->end_controls_tab(); // Style area end

            $repeater->end_controls_tabs();

            $this->add_control(
                'folioedge_tabs_list',
                [
                    'type'    => Controls_Manager::REPEATER,
                    'fields'  => array_values( $repeater->get_controls() ),
                    'default' => [
                        [
                            'tab_title' => esc_html__( 'Title #1', 'folioedgecore' ),
                            'custom_content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolo magna aliqua. Ut enim ad minim veniam, quis nostrud exerci ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in repre in voluptate.','folioedgecore' ),
                        ],
                        [
                            'tab_title' => esc_html__( 'Title #2', 'folioedgecore' ),
                            'custom_content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolo magna aliqua. Ut enim ad minim veniam, quis nostrud exerci ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in repre in voluptate.','folioedgecore' ),
                        ],
                        [
                            'tab_title' => esc_html__( 'Title #3', 'folioedgecore' ),
                            'custom_content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolo magna aliqua. Ut enim ad minim veniam, quis nostrud exerci ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in repre in voluptate.','folioedgecore' ),
                        ],
                    ],
                    'title_field' => '{{{ tab_title }}}',
                ]
            );
        
        $this->end_controls_section();
        
        // Style tab area tab section
        $this->start_controls_section(
            'folioedge_tab_style_area',
            [
                'label' => __( 'Tab Menu', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
            
            $this->add_responsive_control(
                'folioedge_tab_section_margin',
                [
                    'label' => __( 'Margin', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-tab-nav' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' =>'before',
                ]
            );

            $this->add_responsive_control(
                'folioedge_tab_section-padding',
                [
                    'label' => __( 'Padding', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-tab-nav' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' =>'before',
                ]
            );
        
            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'folioedge_tab_section_bg',
                    'label' => __( 'Background', 'folioedgecore' ),
                    'types' => [ 'classic', 'gradient' ],
                    'selector' => '{{WRAPPER}} .folioedge-tab-nav',
                ]
            );
            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'folioedge_tab_section_border',
                    'label' => __( 'Border', 'folioedgecore' ),
                    'selector' => '{{WRAPPER}} .folioedge-tab-nav',
                ]
            );
            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'folioedge_tab_section_shadow',
                    'label' => __( 'Box Shadow', 'folioedgecore' ),
                    'selector' => '{{WRAPPER}} .folioedge-tab-nav',
                ]
            );
            $this->add_responsive_control(
                'folioedge_tab_section_width',
                [
                    'label' => __( 'Width', 'folioedgecore' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'vw' ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 9999,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-tab-nav' => 'width: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );
            $this->add_responsive_control(
                'folioedge_tab_section_height',
                [
                    'label' => __( 'Height', 'folioedgecore' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%' ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 9999,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-tab-nav' => 'height: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );
        
            $this->add_responsive_control(
                'folioedge_tab_section_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-tab-nav' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                    ],
                ]
            );
            $this->add_responsive_control(
                'menu_list_align',
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
                        ]
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-tab-nav' => 'text-align: {{VALUE}};',
                    ],
                    'separator' =>'before',
                ]
            );
        $this->end_controls_section();
        
        
		$this->start_controls_section(
			'tab_button_style_section',
			[
				'label' => __( 'Tab Button', 'folioedgecore' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
        $this->add_responsive_control(
            'menu_text_align',
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
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .folioedge-tab-nav .tab-button' => 'text-align: {{VALUE}};',
                ],
                'separator' =>'before',
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tab_button_typography',
				'selector' => '{{WRAPPER}} .folioedge-tab-nav .tab-button',
			]
		);
		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => __( 'Normal', 'folioedgecore' ),
			]
		);

		$this->add_control(
			'tab_button__color',
			[
				'label' => __( 'Color', 'folioedgecore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .folioedge-tab-nav .tab-button' => 'color: {{VALUE}};',
				],
			]
		);
        $this->add_control(
            'folioedge_tab_item_width',
            [
                'label' => __( 'Width', 'folioedgecore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'vw' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 9999,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .folioedge-tab-nav li' => 'min-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'tab_button_background_color',
				'label' => __( 'Background', 'folioedgecore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .folioedge-tab-nav .tab-button',
			]
		);
        
        $this->add_responsive_control(
            'tab_button_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .folioedge-tab-nav .tab-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'tab_button_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .folioedge-tab-nav .tab-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );   
        
        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tab_button_border',
				'selector' => '{{WRAPPER}} .folioedge-tab-nav .tab-button',
			]
		);

        $this->add_control(
			'tab_button_radius',
			[
				'label' => __( 'Border Radius', 'folioedgecore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],                
				'selectors' => [
					'{{WRAPPER}} .folioedge-tab-nav .tab-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tab_button_box_shadow',
				'selector' => '{{WRAPPER}} .folioedge-tab-nav .tab-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => __( 'Hover', 'folioedgecore' ),
			]
		);

		$this->add_control(
			'tab_button_hover_color',
			[
				'label' => __( 'Color', 'folioedgecore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .folioedge-tab-nav .tab-button:hover, {{WRAPPER}} .folioedge-tab-nav li.active .tab-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_button_hover_background',
			[
				'label' => __( 'Background Color', 'folioedgecore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .folioedge-tab-nav .tab-button:hover, {{WRAPPER}} .folioedge-tab-nav li.active .tab-button' => 'background-color: {{VALUE}};',
				],
			]
		);
        
		$this->add_control(
			'tab_button_hover_border_color',
			[
				'label' => __( 'Border Color', 'folioedgecore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .folioedge-tab-nav .tab-button:hover, {{WRAPPER}} .folioedge-tab-nav li.active .tab-button' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tab_button_hover_box_shadow',
				'selector' => '{{WRAPPER}} .folioedge-tab-nav .tab-button:hover, {{WRAPPER}} .folioedge-tab-nav li.active .tab-button',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
		
		$this->end_controls_section();
        
        $this->start_controls_section(
			'tab_button_icon_section',
			[
				'label' => __( 'Button Icon', 'folioedgecore' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);
        
        
            $this->add_responsive_control(
                'button_icon_floting',
                [
                    'label' => __( 'Float', 'folioedgecore' ),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => __( 'Left', 'folioedgecore' ),
                            'icon' => 'fa fa-align-left',
                        ],
                        'none' => [
                            'title' => __( 'Center', 'folioedgecore' ),
                            'icon' => 'fa fa-align-center',
                        ],
                        'right' => [
                            'title' => __( 'Right', 'folioedgecore' ),
                            'icon' => 'fa fa-align-right',
                        ]
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-tab-nav .tab-button .icon' => 'float: {{VALUE}};',
                    ],
                    'separator' =>'before',
                ]
            );
        

        $this->start_controls_tabs( 'tabs_button_icon_style' );

		$this->start_controls_tab(
			'tab_button_icon_normal',
			[
				'label' => __( 'Normal', 'folioedgecore' ),
			]
		);
            $this->add_responsive_control(
                'button_icon_margin',
                [
                    'label' => __( 'Margin', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-tab-nav .tab-button .icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );
            $this->add_responsive_control(
                'button_icon_opacity',
                [
                    'label' => __( 'Opacity (%)', 'vcharitycore' ),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 1,
                    ],
                    'range' => [
                        'px' => [
                            'max' => 1,
                            'step' => 0.01,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-tab-nav .tab-button .icon' => 'opacity: {{SIZE}}',

                    ],
                ]
            );
        
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_icon_hover',
			[
				'label' => __( 'Hover', 'folioedgecore' ),
			]
		);
            $this->add_responsive_control(
                'button_icon_margin_hover',
                [
                    'label' => __( 'Margin', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-tab-nav .tab-button:hover .icon,{{WRAPPER}} .folioedge-tab-nav .active .tab-button .icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );
            $this->add_responsive_control(
                'button_icon_opacity_hover',
                [
                    'label' => __( 'Opacity (%)', 'vcharitycore' ),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 1,
                    ],
                    'range' => [
                        'px' => [
                            'max' => 1,
                            'step' => 0.01,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-tab-nav .tab-button:hover .icon,{{WRAPPER}} .folioedge-tab-nav .active .tab-button .icon' => 'opacity: {{SIZE}}',

                    ],
                ]
            );        

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
        
        
        // Style tab section
        $this->start_controls_section(
            'tab_style_content_section',
            [
                'label' => __( 'Content', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
            
            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'tab_content_background',
                    'label' => __( 'Background', 'folioedgecore' ),
                    'types' => [ 'classic', 'gradient' ],
                    'selector' => '{{WRAPPER}} .folioedge-tab-content',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'tab_content_border',
                    'label' => __( 'Border', 'folioedgecore' ),
                    'selector' => '{{WRAPPER}} .folioedge-tab-content',
                ]
            );

            $this->add_responsive_control(
                'tab_content_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-tab-content' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                    ],
                ]
            );

            $this->add_responsive_control(
                'tab_content_padding',
                [
                    'label' => __( 'Padding', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-tab-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' => 'before',
                ]
            );

            $this->add_responsive_control(
                'tab_content_margin',
                [
                    'label' => __( 'Margin', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-tab-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

        $this->end_controls_section();

    }

    protected function render() {
        $settings   = $this->get_settings_for_display();
        $this->add_render_attribute( 'folioedge_tab_attr', 'class', ['folioedge-tabs', 'row g-4' ,'menu-position-'.$settings['menu_position']] );
        $this->add_render_attribute( 'folioedge_tab_menu_attr', 'class', ['folioedge-tab-nav', esc_attr($settings['menu_style'])]);
        $this->add_render_attribute( 'folioedge_tab_menu_attr', 'role', 'tablist');
        $id = $this->get_id();       
        ?>
    <div <?php echo $this->get_render_attribute_string( 'folioedge_tab_attr' ); ?>>
        <div class="col-12 <?php echo ($settings['menu_position'] != 'top' ? 'col-md-4' : ''); ?>">
            <ul <?php echo $this->get_render_attribute_string( 'folioedge_tab_menu_attr' ); ?>>
                <?php
                $i=0;
                foreach ( $settings['folioedge_tabs_list'] as $item ) {
                    $i++;
                    $tabbuttontxt = '';
                    if( $i == 1 ){ $active_tab = 'active'; } else{ $active_tab = ''; }
                    if( $item['icon_type'] == 'img' and !empty(Group_Control_Image_Size::get_attachment_image_html( $item, 'imagesize', 'image' )) ){
                        $image = Group_Control_Image_Size::get_attachment_image_html( $item, 'imagesize', 'image' );  
                        $tabbuttontxt .= '<span class="icon">'.$image.'</span>';
                    }elseif( $item['icon_type'] == 'icon' && !empty($item['feature_icon']['value']) ){
                        $tabbuttontxt .= sprintf( '<span class="icon" >%1$s</span>', folioedge_icon_manager::render_icon( $item['feature_icon'], [ 'aria-hidden' => 'true' ] ) );
                    }
                    $tabbuttontxt .= $item['tab_title'];
                    
                    echo sprintf( '<li class="%1$s" ><a class="tab-button %4$s" href="#folioedgetab-%2$s" data-toggle="tab" role="tab">%3$s</a></li>',$active_tab, $id.$i, $tabbuttontxt, 'elementor-repeater-item-'.$item['_id']);
                }
                ?>
            </ul>
        </div>
        <div class="col-12 <?php echo ($settings['menu_position'] != 'top' ? 'col-md-8' : ''); ?>">
            <div class="folioedge-tab-content tab-content">
                <?php
                    $i=0;
                    foreach ( $settings['folioedge_tabs_list'] as $item ) {
                        $i++;
                        if( $i == 1 ){ $active_tab = 'active in'; } else{ $active_tab = ''; }

                        if ( $item['content_source'] == 'custom' && !empty( $item['custom_content'] ) ) {
                            $tab_content =  wp_kses_post( $item['custom_content'] );
                        } elseif ( $item['content_source'] == "elementor" && !empty( $item['template_id'] )) {
                            $tab_content =  Plugin::instance()->frontend->get_builder_content_for_display( $item['template_id'] );
                        }
                        echo sprintf('<div class="folioedge-single-tab tab-pane fade %1$s %4$s" id="folioedgetab-%2$s" role="tabpanel"><div class="folioedge-tab-content">%3$s</div></div>', $active_tab, $id.$i, $tab_content,'elementor-repeater-item-'.$item['_id'] );
                    }
                ?>
            </div>
        </div>
    </div>
<?php
    }
}
Plugin::instance()->widgets_manager->register( new folioedge_Elementor_Widget_Tabs() );
