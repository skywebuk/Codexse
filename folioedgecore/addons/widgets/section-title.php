<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class folioedge_Elementor_Widget_Section_Title extends Widget_Base {

    public function get_name() {
        return 'section-title-addons';
    }
    
    public function get_title() {
        return __( 'Section Title', 'folioedgecore' );
    }

    public function get_icon() {
        return 'folioedge-icon eicon-animation-text';
    }
    public function get_categories() {
        return [ 'folioedgecore' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'start_top_heading_section',
            [
                'label' => __( 'Top Heading','folioedgecore' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'top_heading',
            [
                'label' => __( 'Top Heading', 'folioedgecore' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Top Heading Here.', 'folioedgecore' ),
                'placeholder' => __( 'Type your top title here', 'folioedgecore' ),
            ]
        );

        $this->add_control(
            'top_heading_tag',
            [
                'label' => __( 'HTML Tag', 'folioedgecore' ),
                'type' => Controls_Manager::SELECT,
                'options' => folioedge_html_tag_lists(),
                'default' => 'h5',
            ]
        );

        $this->add_responsive_control(
            'top_heading_align',
            [
                'label' => __( 'Alignment', 'elementor' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'elementor' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'elementor' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'elementor' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __( 'Justified', 'elementor' ),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .top-heading' => 'text-align: {{VALUE}};',
                ],
            ]
        );
      
        $this->end_controls_section();


        $this->start_controls_section(
            'start_main_heading_section',
            [
                'label' => __( 'Main Heading','folioedgecore' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'main_heading',
            [
                'label' => __( 'Main Heading', 'folioedgecore' ),
                'type' => Controls_Manager::WYSIWYG,
                'default' => __( 'Main Heading Here.', 'folioedgecore' ),
                'placeholder' => __( 'Type your main title here', 'folioedgecore' ),
            ]
        );

        $this->add_control(
            'main_heading_tag',
            [
                'label' => __( 'HTML Tag', 'folioedgecore' ),
                'type' => Controls_Manager::SELECT,
                'options' => folioedge_html_tag_lists(),
                'default' => 'h2',
            ]
        );
        $this->add_responsive_control(
            'main_heading_align',
            [
                'label' => __( 'Alignment', 'elementor' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'elementor' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'elementor' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'elementor' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __( 'Justified', 'elementor' ),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .main-heading' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'start_sub_heading_section',
            [
                'label' => __( 'Sub Heading','folioedgecore' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'sub_heading',
            [
                'label' => __( 'Sub Heading', 'folioedgecore' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Sub Heading Here.', 'folioedgecore' ),
                'placeholder' => __( 'Type your sub title here', 'folioedgecore' ),
            ]
        );
        $this->add_control(
            'sub_heading_tag',
            [
                'label' => __( 'HTML Tag', 'folioedgecore' ),
                'type' => Controls_Manager::SELECT,
                'options' => folioedge_html_tag_lists(),
                'default' => 'p',
            ]
        );
        $this->add_responsive_control(
            'sub_heading_align',
            [
                'label' => __( 'Alignment', 'elementor' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'elementor' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'elementor' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'elementor' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __( 'Justified', 'elementor' ),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .sub-heading' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_section();



        $this->start_controls_section(
            'start_desc_section',
            [
                'label' => __( 'Description','folioedgecore' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'desc_content',
            [
                'label' => __( 'Description', 'folioedgecore' ),
                'type' => Controls_Manager::WYSIWYG,
                'default' => __( 'Description Content Here.', 'folioedgecore' ),
                'placeholder' => __( 'Type your description here', 'folioedgecore' ),
            ]
        );
        $this->add_control(
            'desc_tag',
            [
                'label' => __( 'HTML Tag', 'folioedgecore' ),
                'type' => Controls_Manager::SELECT,
                'options' => folioedge_html_tag_lists(),
                'default' => 'div',
            ]
        );
        $this->add_responsive_control(
            'desc_align',
            [
                'label' => __( 'Alignment', 'elementor' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'elementor' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'elementor' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'elementor' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __( 'Justified', 'elementor' ),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .desc' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'start_icon_section',
            [
                'label' => __( 'Icon','folioedgecore' ),
                'tab' => Controls_Manager::TAB_CONTENT,
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
                        'icon' =>'fa fa-image',
                    ],
                    'icon' =>[
                        'title' =>__('Icon','folioedgecore'),
                        'icon' =>'fa fa-icons',
                    ]
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
            'title_icon',
            [
                'label'       => __( 'Icon', 'folioedgecore-addons' ),
                'type'        => Controls_Manager::ICONS,
                'label_block' => true,
                'condition' => [
                    'icon_type' => 'icon',
                ]
            ]
        );       
        
        $this->add_responsive_control(
            'icon_align',
            [
                'label' => __( 'Alignment', 'elementor' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'elementor' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'elementor' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'elementor' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __( 'Justified', 'elementor' ),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .x-icon' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'frontend_available' => true,
                'selectors' => [
                    '{{WRAPPER}} .x-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => __( 'Size', 'folioedgecore' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .x-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'icon_type' => 'icon',
                ]
            ]
        );

        $this->add_responsive_control(
            'icon_opacity',
            [
                'label' => __( 'Opacity (%)', 'folioedgecore' ),
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
                    '{{WRAPPER}} .x-icon' => 'opacity: {{SIZE}}',
                    
                ],
            ]
        );    
        $this->add_responsive_control(
            'icon_width',
            [
                'label' => __( 'Icon Width', 'folioedgecore' ),
                'type' => Controls_Manager::SLIDER,
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
                    'vw' => [
                        'min' => 0,
                        'max' => 100,
                    ]
                ],
                'size_units' => [ 'px', '%', 'vw' ],
                'selectors' => [
                    '{{WRAPPER}} .x-icon' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'icon_height',
            [
                'label' => __( 'Icon Height', 'folioedgecore' ),
                'type' => Controls_Manager::SLIDER,
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
                    'vh' => [
                        'min' => 0,
                        'max' => 100,
                    ]
                ],
                'size_units' => [ 'px', '%', 'vh' ],
                'selectors' => [
                    '{{WRAPPER}} .x-icon' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );      
        $this->add_responsive_control(
            'icon_hr_offset',
            [
                'label' => __( 'Horizontal Offset', 'folioedgecore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [  'px', '%', 'vw' ],
                'range' => [
                    'px' => [
                        'min' => -2000,
                        'max' => 2000,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                    'vw' => [
                        'min' => -100,
                        'max' => 100,
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .x-icon' => 'position:absolute;left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );        
        $this->add_responsive_control(
            'icon_vr_offset',
            [
                'label' => __( 'Vertical Offset', 'folioedgecore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [  'px', '%', 'vh' ],
                'range' => [
                    'px' => [
                        'min' => -2000,
                        'max' => 2000,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                    'vh' => [
                        'min' => -100,
                        'max' => 100,
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .x-icon' => 'position:absolute;top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'icon_z_index',
            [
                'label' => __( 'Z Index', 'folioedgecore' ),
                'type' => Controls_Manager::NUMBER,
                'min' => -100,
                'max' => 100,
                'step' => 1,
                'default' => -1,
                'selectors' => [
                    '{{WRAPPER}} .x-icon' => 'z-index: {{SIZE}};',
                ],
            ]
        );
        $this->end_controls_section();


        
        // blog Style tab section
        $this->start_controls_section(
            'box_style_section',
            [
                'label' => __( 'Area Style', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

            $this->start_controls_tabs('box_style_tab');
            $this->start_controls_tab( 'box_style_normal',
                [
                    'label' => __( 'Normal', 'folioedgecore' ),
                ]
            );

            $this->add_responsive_control(
                'box_style_margin',
                [
                    'label' => __( 'Margin', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-section-heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' =>'before',
                ]
            );

            $this->add_responsive_control(
                'box_style_padding',
                [
                    'label' => __( 'Padding', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-section-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' =>'before',
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'box_style_background',
                    'label' => __( 'Background', 'folioedgecore' ),
                    'types' => [ 'classic', 'gradient' ],
                    'selector' => '{{WRAPPER}} .folioedge-section-heading',
                ]
            );
            $this->add_responsive_control(
                'box_style_text_align',
                [
                    'label' => __( 'Alignment', 'elementor' ),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'align-items: flex-start;' => [
                            'title' => __( 'Left', 'elementor' ),
                            'icon' => 'eicon-text-align-left',
                        ],
                        'align-items: center;' => [
                            'title' => __( 'Center', 'elementor' ),
                            'icon' => 'eicon-text-align-center',
                        ],
                        'align-items: flex-end;' => [
                            'title' => __( 'Right', 'elementor' ),
                            'icon' => 'eicon-text-align-right',
                        ],
                        'align-items: space-bettwen;' => [
                            'title' => __( 'Justified', 'elementor' ),
                            'icon' => 'eicon-text-align-justify',
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-section-heading' => 'display: flex; flex-direction: column;  {{VALUE}}',
                    ],
                ]
            );
            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'box_style_border',
                    'label' => __( 'Border', 'folioedgecore' ),
                    'selector' => '{{WRAPPER}} .folioedge-section-heading',
                ]
            );
            $this->add_responsive_control(
                'box_style_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-section-heading' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                    ]
                ]
            );
            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'box_style_box_shadow',
                    'label' => __( 'Box Shadow', 'folioedgecore' ),
                    'selector' => '{{WRAPPER}} .folioedge-section-heading',
                ]
            );


            $this->add_control(
                'box_style_box_transform',
                [
                    'label' => __( 'Transform', 'folioedgecore' ),
                    'type' => Controls_Manager::TEXT,
                    'default' => 'translateY(0)',
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-section-heading' => 'transform: {{VALUE}}',
                    ],
                ]
            );

            $this->add_control(
                'box_style_transition',
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
                        '{{WRAPPER}} .folioedge-section-heading' => 'transition-duration: {{SIZE}}s',
                    ],
                ]
            );
            $this->end_controls_tab();


            // Hover Style tab Start
            $this->start_controls_tab(
                'box_style_hover',
                [
                    'label' => __( 'Hover', 'folioedgecore' ),
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'box_style_hover_background',
                    'label' => __( 'Background', 'folioedgecore' ),
                    'types' => [ 'classic', 'gradient' ],
                    'selector' => '{{WRAPPER}} .folioedge-section-heading:hover',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'box_style_border_hover',
                    'label' => __( 'Border', 'folioedgecore' ),
                    'selector' => '{{WRAPPER}} .folioedge-section-heading:hover',
                ]
            );
            $this->add_responsive_control(
                'box_style_hover_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-section-heading:hover' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                    ],
                ]
            );
            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'box_style_box_hover_shadow',
                    'label' => __( 'Box Shadow', 'folioedgecore' ),
                    'selector' => '{{WRAPPER}} .folioedge-section-heading:hover',
                ]
            );
            $this->add_control(
                'box_style_hover_transform',
                [
                    'label' => __( 'Transform', 'folioedgecore' ),
                    'type' => Controls_Manager::TEXT,
                    'default' => 'translateY(0)',
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-section-heading:hover' => 'transform: {{VALUE}}',
                    ],
                ]
            );

            $this->end_controls_tab(); // Hover Style tab end        
            $this->end_controls_tabs();// Box Style tabs end  
            $this->end_controls_section(); // blog Box section style end



        // Text Box Style tab section
        $this->start_controls_section(
            'start_top_heading_style',
            [
                'label' => __( 'Top Heading', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'top_heading_typo',
                'selector' => '{{WRAPPER}} .folioedge-section-heading .top-heading',
            ]
        );
        
        $this->add_control(
            'top_heading_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .folioedge-section-heading .top-heading' => 'color: {{VALUE}};',
                ],
            ]
        );               
        $this->add_responsive_control(
            'top_heading_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '15',
                    'left' => '0',
                    'isLinked' => true
                ],
                'selectors' => [
                    '{{WRAPPER}} .folioedge-section-heading .top-heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'top_heading_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '0',
                    'left' => '0',
                    'isLinked' => true
                ],
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .top-heading span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );   
        $this->add_responsive_control(
          'top_heading_width',
          [
            'label' => __( 'Width', 'folioedgecore' ),
            'type' => Controls_Manager::SLIDER,
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
              'vw' => [
                'min' => 0,
                'max' => 100,
              ]
            ],
            'size_units' => [ 'px', '%', 'vw' ],
            'selectors' => [
              '{{WRAPPER}} .folioedge-section-heading .top-heading' => 'width: {{SIZE}}{{UNIT}};max-width: 100%;display: inline-block;',
            ],
          ]
        );        
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'top_heading_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .top-heading span',
            ]
        );
        $this->end_controls_section(); 

        // Text Box Style tab section
        $this->start_controls_section(
            'start_main_heading_style',
            [
                'label' => __( 'Main Heading', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'main_heading_typo',
                'selector' => '{{WRAPPER}} .folioedge-section-heading .main-heading',
            ]
        );        
        $this->add_control(
            'main_heading_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .folioedge-section-heading .main-heading' => 'color: {{VALUE}};',
                ],
            ]
        );               
        $this->add_responsive_control(
            'main_heading_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '15',
                    'left' => '0',
                    'isLinked' => true
                ],
                'selectors' => [
                    '{{WRAPPER}} .folioedge-section-heading .main-heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'main_heading_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '0',
                    'left' => '0',
                    'isLinked' => true
                ],
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .folioedge-section-heading .main-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );   
      
        $this->add_responsive_control(
            'main_title_width',
            [
                'label' => __( 'Width', 'folioedgecore' ),
                'type' => Controls_Manager::SLIDER,
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
                    'vw' => [
                        'min' => 0,
                        'max' => 100,
                    ]
                ],
                'size_units' => [ 'px', '%', 'vw' ],
                'selectors' => [
                    '{{WRAPPER}} .main-heading' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section(); 

        // Text Box Style tab section
        $this->start_controls_section(
            'start_sub_heading_style',
            [
                'label' => __( 'Sub Heading', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'sub_heading_typo',
                'selector' => '{{WRAPPER}} .folioedge-section-heading .sub-heading',
            ]
        );        
        $this->add_control(
            'sub_heading_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .folioedge-section-heading .sub-heading' => 'color: {{VALUE}};',
                ],
            ]
        );               
        $this->add_responsive_control(
            'sub_heading_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '15',
                    'left' => '0',
                    'isLinked' => true
                ],
                'selectors' => [
                    '{{WRAPPER}} .folioedge-section-heading .sub-heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );        
        $this->add_responsive_control(
            'sub_heading_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '0',
                    'left' => '0',
                    'isLinked' => true
                ],
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .folioedge-section-heading .sub-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );   

        $this->add_responsive_control(
            'sub_heading_opacity',
            [
                'label' => __( 'Opacity (%)', 'folioedgecore' ),
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
                    '{{WRAPPER}} .sub-heading' => 'opacity: {{SIZE}}',
                    
                ],
            ]
        );    
        $this->add_responsive_control(
            'sub_heading_width',
            [
                'label' => __( 'Width', 'folioedgecore' ),
                'type' => Controls_Manager::SLIDER,
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
                    'vw' => [
                        'min' => 0,
                        'max' => 100,
                    ]
                ],
                'size_units' => [ 'px', '%', 'vw' ],
                'selectors' => [
                    '{{WRAPPER}} .sub-heading' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'sub_heading_height',
            [
                'label' => __( 'Height', 'folioedgecore' ),
                'type' => Controls_Manager::SLIDER,
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
                    'vh' => [
                        'min' => 0,
                        'max' => 100,
                    ]
                ],
                'size_units' => [ 'px', '%', 'vh' ],
                'selectors' => [
                    '{{WRAPPER}} .sub-heading' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );      
        $this->add_responsive_control(
            'sub_heading_hr_offset',
            [
                'label' => __( 'Horizontal Offset', 'folioedgecore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [  'px', '%', 'vw' ],
                'range' => [
                    'px' => [
                        'min' => -2000,
                        'max' => 2000,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                    'vw' => [
                        'min' => -100,
                        'max' => 100,
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .sub-heading' => 'position:absolute;left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );        
        $this->add_responsive_control(
            'sub_heading_vr_offset',
            [
                'label' => __( 'Vertical Offset', 'folioedgecore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [  'px', '%', 'vh' ],
                'range' => [
                    'px' => [
                        'min' => -2000,
                        'max' => 2000,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                    'vh' => [
                        'min' => -100,
                        'max' => 100,
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .sub-heading' => 'position:absolute;top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'sub_heading_z_index',
            [
                'label' => __( 'Z Index', 'folioedgecore' ),
                'type' => Controls_Manager::NUMBER,
                'min' => -100,
                'max' => 100,
                'step' => 1,
                'default' => -1,
                'selectors' => [
                    '{{WRAPPER}} .sub-heading' => 'z-index: {{SIZE}};',
                ],
            ]
        );
      
        $this->add_responsive_control(
            'sub_title_width',
            [
                'label' => __( 'Width', 'folioedgecore' ),
                'type' => Controls_Manager::SLIDER,
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
                    'vw' => [
                        'min' => 0,
                        'max' => 100,
                    ]
                ],
                'size_units' => [ 'px', '%', 'vw' ],
                'selectors' => [
                    '{{WRAPPER}} .sub-heading' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();


        // Text Box Style tab section
        $this->start_controls_section(
            'start_desc_style',
            [
                'label' => __( 'Description', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'desc_typo',
                'selector' => '{{WRAPPER}} .folioedge-section-heading .desc',
            ]
        );        
        $this->add_control(
            'desc_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .folioedge-section-heading .desc' => 'color: {{VALUE}};',
                ],
            ]
        );               
        $this->add_responsive_control(
            'desc_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '0',
                    'left' => '0',
                    'isLinked' => true
                ],
                'selectors' => [
                    '{{WRAPPER}} .folioedge-section-heading .desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );        
        $this->add_responsive_control(
            'desc_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '0',
                    'left' => '0',
                    'isLinked' => true
                ],
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .folioedge-section-heading .desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );   
        $this->add_responsive_control(
            'desc_title_width',
            [
                'label' => __( 'Width', 'folioedgecore' ),
                'type' => Controls_Manager::SLIDER,
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
                    'vw' => [
                        'min' => 0,
                        'max' => 100,
                    ]
                ],
                'size_units' => [ 'px', '%', 'vw' ],
                'selectors' => [
                    '{{WRAPPER}} .desc' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();
        
    }

    protected function render( $instance = [] ) {
        $html_output = '';
        $settings   = $this->get_settings_for_display();

        /*== Value-Check ==*/
        if ( empty( $settings['top_heading'] ) and empty( $settings['main_heading'] ) and empty( $settings['sub_heading'] )  ) {
            return;
        }

        /*== Set-Area-Class ==*/
        $this->add_render_attribute( 'section_area_attr', 'class', 'folioedge-section-heading' );


        /*== Cullect-Top-Heading-Content ==*/
        $this->add_render_attribute( 'top_heading', 'class', [ 'top-heading' ] );
        $this->add_inline_editing_attributes( 'top_heading' );
        $top_heading = wp_kses_post($settings['top_heading']);


        /*== Cullect-Main-Heading-Content ==*/
        $this->add_render_attribute( 'main_heading', 'class', [ 'main-heading' ] );
        $this->add_inline_editing_attributes( 'main_heading' );
        $main_heading = wp_kses_post($settings['main_heading']);
        $main_heading = str_replace('<p', '<span', $main_heading );
        $main_heading = str_replace('</p>', '</span>', $main_heading );

        /*== Cullect-Sub-Heading-Content ==*/
        $this->add_render_attribute( 'sub_heading', 'class', ['sub-heading' ] );
        $this->add_inline_editing_attributes( 'sub_heading' );
        $sub_heading = wp_kses_post($settings['sub_heading']);

        /*== Cullect-Description-Content ==*/
        $this->add_render_attribute( 'desc_content', 'class', [ 'desc' ] );
        $this->add_inline_editing_attributes( 'desc_content' );
        $desc = wp_kses_post($settings['desc_content']);

        /*== HTML-Output ==*/
        $html_output .= '<div '.$this->get_render_attribute_string( 'section_area_attr' ).' >'; /*== Start-Tag ==*/
        if( $top_heading != '' ){
            $html_output .= sprintf( '<%1$s %2$s><span>%3$s</span></%1$s>', $settings['top_heading_tag'], $this->get_render_attribute_string( 'top_heading' ), $top_heading );
        }

        if( $main_heading != '' ){
            $html_output .= sprintf( '<%1$s %2$s>%3$s</%1$s>', $settings['main_heading_tag'], $this->get_render_attribute_string( 'main_heading' ), $main_heading );
        }


        if( $sub_heading != '' ){
            $html_output .= sprintf( '<%1$s %2$s>%3$s</%1$s>', $settings['sub_heading_tag'], $this->get_render_attribute_string( 'sub_heading' ), $sub_heading );
        }


        if( $desc != '' ){
            $html_output .= sprintf( '<%1$s %2$s>%3$s</%1$s>', $settings['desc_tag'], $this->get_render_attribute_string( 'desc_content' ), $desc );
        }

        if( $settings['icon_type'] == 'img' and !empty(Group_Control_Image_Size::get_attachment_image_html( $settings, 'imagesize', 'image' )) ){
            $image = Group_Control_Image_Size::get_attachment_image_html( $settings, 'imagesize', 'image' );  
            $html_output .= '<div class="x-icon">'.$image.'</div>';
        }elseif( $settings['icon_type'] == 'icon' && !empty($settings['title_icon']['value']) ){
            $html_output .= sprintf( '<div class="x-icon" >%1$s</div>', folioedge_icon_manager::render_icon( $settings['title_icon'], [ 'aria-hidden' => 'true' ] ) );
        }


        $html_output .= '</div>'; /*== End-Tag ==*/

        /*== Display-Output ==*/
        echo $html_output;

    }

}


Plugin::instance()->widgets_manager->register_widget_type( new folioedge_Elementor_Widget_Section_Title );