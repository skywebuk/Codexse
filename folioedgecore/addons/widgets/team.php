<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class folioedge_team_Widget extends Widget_Base {

    public function get_name() {
        return 'folioedge-team-widget';
    }
    
    public function get_title() {
        return __( 'Team', 'folioedgecore' );
    }

    public function get_icon() {
        return 'folioedge-icon eicon-person';
    }

    public function get_categories() {
        return [ 'folioedgecore' ];
    }

    public function get_script_depends() {
        return [
            'jquery-masonry',
            'imagesloaded',
            'swiper',
            'addons-active',
        ];
    }
    
    public function get_style_depends() {
        return [
            'swiper'
        ];
    }
    
    protected function register_controls() {

        $this->start_controls_section(
            'folioedge_team_content_section',
            [
                'label' => __( 'Team', 'folioedgecore' ),
            ]
        );

            $this->add_control(
                'slider_on',
                [
                    'label'         => __( 'Slider', 'folioedgecore' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'label_on'      => __( 'On', 'folioedgecore' ),
                    'label_off'     => __( 'Off', 'folioedgecore' ),
                    'return_value'  => 'yes',
                    'default'       => 'yes',
                ]
            );
        
            $this->add_control(
                'item_column',
                [
                    'label' => __( 'Column', 'folioedgecore' ),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        '1grid' => [
                            'title' => __( 'One Column', 'folioedgecore' ),
                            'icon' => 'icon-grid-1',
                        ],
                        '2grid' => [
                            'title' => __( 'Two Columns', 'folioedgecore' ),
                            'icon' => 'icon-grid-2',
                        ],
                        '3grid' => [
                            'title' => __( 'Three Columns', 'folioedgecore' ),
                            'icon' => 'icon-grid-3',
                        ],
                        '4grid' => [
                            'title' => __( 'Four Columns', 'folioedgecore' ),
                            'icon' => 'icon-grid-4',
                        ],
                    ],
                    'default' => '3grid',
                    'toggle' => true,
                    'condition' => [
                        'slider_on!' => 'yes',
                    ]
                ]
            );
        
            $this->add_control(
                'grid_space',
                [
                    'label' => esc_html__( 'Grid Space', 'folioedgecore' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'g-4',
                    'options' => [
                        'g-1'  => __( 'One', 'folioedgecore' ),
                        'g-2'  => __( 'Two', 'folioedgecore' ),
                        'g-3'  => __( 'Three', 'folioedgecore' ),
                        'g-4'  => __( 'Four', 'folioedgecore' ),
                        'g-5'  => __( 'Five', 'folioedgecore' ),
                    ],
                    'condition' => [
                        'slider_on!' => 'yes',
                    ]
                ]
            );
        
            $this->add_control(
                'team_style',
                [
                    'label' => __( 'Grid Style', 'folioedgecore' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'style1',
                    'options' => [
                        'style1' => esc_html__( 'Style One', 'folioedgecore' ),
                        'style2' => esc_html__( 'Style Two', 'folioedgecore' ),
                        'style3' => esc_html__( 'Style Three', 'folioedgecore' ),
                        'style4' => esc_html__( 'Style Four', 'folioedgecore' ),
                    ],
                ]
            );

            $this->add_control(
                'masonry',
                [
                    'label'         => __( 'Masonry', 'folioedgecore' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'label_on'      => __( 'On', 'folioedgecore' ),
                    'label_off'     => __( 'Off', 'folioedgecore' ),
                    'return_value'  => 'yes',
                    'default'       => 'no',
                    'condition' => [
                        'slider_on!' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'category_list',
                [
                    'label' => esc_html__( 'Categories', 'folioedgecore' ),
                    'type' => Controls_Manager::SELECT2,
                    'multiple' => true,
                    'options' => folioedge_get_taxonomies('team_category'),
                    'separator'=>'before',
                ]
            );
            $this->add_control(
                'title_list',
                [
                    'label' => esc_html__( 'Title', 'folioedgecore' ),
                    'type' => Controls_Manager::SELECT2,
                    'multiple' => true,
                    'options' => folioedge_get_title('team'),
                    'separator'=>'before',
                ]
            );
            $this->add_control(
                'post_limit',
                [
                    'label' => __('Limit', 'folioedgecore'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 5,
                    'separator'=>'before',
                ]
            );

            $this->add_control(
                'custom_order',
                [
                    'label' => esc_html__( 'Custom order', 'folioedgecore' ),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'no',
                ]
            );

            $this->add_control(
                'postorder',
                [
                    'label' => esc_html__( 'Order', 'folioedgecore' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'DESC',
                    'options' => [
                        'DESC'  => esc_html__('Descending','folioedgecore'),
                        'ASC'   => esc_html__('Ascending','folioedgecore'),
                    ],
                    'condition' => [
                        'custom_order!' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'orderby',
                [
                    'label' => esc_html__( 'Orderby', 'folioedgecore' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'none',
                    'options' => [
                        'none'          => esc_html__('None','folioedgecore'),
                        'ID'            => esc_html__('ID','folioedgecore'),
                        'date'          => esc_html__('Date','folioedgecore'),
                        'name'          => esc_html__('Name','folioedgecore'),
                        'title'         => esc_html__('Title','folioedgecore'),
                        'comment_count' => esc_html__('Comment count','folioedgecore'),
                        'rand'          => esc_html__('Random','folioedgecore'),
                    ],
                    'condition' => [
                        'custom_order' => 'yes',
                    ]
                ]
            );
            $this->add_control(
                'show_thumb',
                [
                    'label' => esc_html__( 'Show Image', 'folioedgecore' ),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'yes',
                ]
            );
            $this->add_group_control(
                Group_Control_Image_Size::get_type(),
                [
                    'name' => 'thumb_size',
                    'default' => 'full',
                    'separator' => 'none',
                ]
            );
            $this->add_control(
                'show_name',
                [
                    'label' => esc_html__( 'Name', 'folioedgecore' ),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'yes',
                ]
            );

            $this->add_control(
                'show_position',
                [
                    'label' => esc_html__( 'Position', 'folioedgecore' ),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'yes',
                ]
            );

            $this->add_control(
                'show_content',
                [
                    'label' => esc_html__( 'Description', 'folioedgecore' ),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'no',
                ]
            );

            $this->add_control(
                'content_length',
                [
                    'label' => __( 'Description Length', 'folioedgecore' ),
                    'type' => Controls_Manager::NUMBER,
                    'step' => 1,
                    'default' => 30,
                    'condition'=>[
                        'show_content'=>'yes',
                    ]
                ]
            );
        
            $this->add_control(
                'content_line',
                [
                    'label' => __( 'Content Column', 'folioedgecore' ),
                    'type' => Controls_Manager::NUMBER,
                    'step' => 1,
                    'default' => 3,
                    'selectors' => [
                        '{{WRAPPER}} .team_box .desc' => '-webkit-line-clamp: {{VALUE}};line-clamp: {{VALUE}};',
                    ],
                    'condition'=>[
                        'show_content' => 'yes',
                    ]
                ]
            );
                
        
            $this->add_control(
                'show_social',
                [
                    'label' => esc_html__( 'Social Menu', 'folioedgecore' ),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'yes',
                ]
            );
          
        $this->end_controls_section();
        
        $this->start_controls_section(
            'slider_option',
            [
                'label' => esc_html__( 'Slider Option', 'folioedgecore' ),
                'condition'=>[
                    'slider_on'=>'yes',
                ]
            ]
        );
            $this->add_control(
                'sl_navigation',
                [
                    'label' => esc_html__( 'Arrow', 'folioedgecore' ),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'no',
                ]
            );
                
            $this->add_control(
                'slider_custom_arrow',
                [
                    'label' => esc_html__( 'Custom Arrow', 'folioedgecore' ),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'yes',
                    'condition'=>[
                        'sl_navigation'=>'yes',
                    ]
                ]
            );
            
            $this->add_control(
                 'slider_target_id',
                 [
                     'label'     => __( 'Arrows ID', 'folioedgecore' ),
                     'type'      => Controls_Manager::TEXT,
                     'title' => __( 'Take arrow id from "Custom Navigation" addons and paste here!', 'folioedgecore' ),
                     'condition' => [
                         'slider_custom_arrow' => 'yes',
                        'sl_navigation'=>'yes',
                     ]
                 ]
             );

            $this->add_control(
                'sl_nav_prev_icon',
                [
                    'label' => __( 'Previus Icon', 'folioedgecore' ),
                    'type' => Controls_Manager::ICON,
                    'default' => 'fa fa-angle-left',
                    'condition'=>[
                        'sl_navigation'=>'yes',
                        'slider_custom_arrow!'=>'yes',
                    ]
                ]
            );

            $this->add_control(
                'sl_nav_next_icon',
                [
                    'label' => __( 'Next Arrow', 'folioedgecore' ),
                    'type' => Controls_Manager::ICON,
                    'default' => 'fa fa-angle-right',
                    'condition'=>[
                        'sl_navigation'=>'yes',
                        'slider_custom_arrow!'=>'yes',
                    ]
                ]
            );
        
            $this->add_control(
                'slpaginate',
                [
                    'label' => esc_html__( 'Paginate', 'folioedgecore' ),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'no',
                ]
            );

            $this->add_control(
                'sleffect',
                [
                    'label' => esc_html__( 'Effect', 'folioedgecore' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'slide',
                    'options' => [
                        'slide'  => __( 'Slide', 'folioedgecore' ),
                        'fade'  => __( 'Fade', 'folioedgecore' ),
                        'cube'  => __( 'Cube', 'folioedgecore' ),
                        'coverflow'  => __( 'Coverflow', 'folioedgecore' ),
                        'flip'  => __( 'Flip', 'folioedgecore' ),
                    ],
                ]
            );
        
            $this->add_control(
                'slloop',
                [
                    'label' => esc_html__( 'Loop', 'folioedgecore' ),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'yes',
                ]
            );
            $this->add_control(
                'slautolay',
                [
                    'label' => esc_html__( 'Autoplay', 'folioedgecore' ),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'yes',
                ]
            );      
                
            $this->add_control(
                'slautolaydelay',
                [
                    'label' => __('Autoplay Delay', 'folioedgecore'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 6500,
                ]
            );
        
                
            $this->add_control(
                'slcenter',
                [
                    'label' => esc_html__( 'Center', 'folioedgecore' ),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'no',
                ]
            );
        
        
            $this->add_control(
                'sldisplay_columns',
                [
                    'label' => __('Slider Items', 'folioedgecore'),
                    'type' => Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 8,
                    'step' => 1,
                    'default' => 3,
                ]
            );

            $this->add_control(
                'slcenter_padding',
                [
                    'label' => __( 'Center padding', 'folioedgecore' ),
                    'type' => Controls_Manager::NUMBER,
                    'min' => 0,
                    'max' => 500,
                    'step' => 1,
                    'default' => 30,
                ]
            );

            $this->add_control(
                'slanimation_speed',
                [
                    'label' => __('Slide Speed', 'folioedgecore'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 1000,
                ]
            );
        
            $this->add_control(
                'heading_laptop',
                [
                    'label' => __( 'Laptop', 'folioedgecore' ),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'after',
                ]
            );

            $this->add_control(
                'sllaptop_width',
                [
                    'label' => __('Laptop Resolution', 'folioedgecore'),
                    'description' => __('The resolution to laptop.', 'folioedgecore'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 1200,
                ]
            );
        
            $this->add_control(
                'sllaptop_display_columns',
                [
                    'label' => __('Slider Items', 'folioedgecore'),
                    'type' => Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 8,
                    'step' => 1,
                    'default' => 3,
                ]
            );
        
            $this->add_control(
                'sllaptop_padding',
                [
                    'label' => __( 'Center padding', 'folioedgecore' ),
                    'type' => Controls_Manager::NUMBER,
                    'min' => 0,
                    'max' => 500,
                    'step' => 1,
                    'default' => 30,
                ]
            );

            $this->add_control(
                'heading_tablet',
                [
                    'label' => __( 'Tablet', 'folioedgecore' ),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'after',
                ]
            );

            $this->add_control(
                'sltablet_width',
                [
                    'label' => __('Tablet Resolution', 'folioedgecore'),
                    'description' => __('The resolution to tablet.', 'folioedgecore'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 992,
                ]
            );
        
            $this->add_control(
                'sltablet_display_columns',
                [
                    'label' => __('Slider Items', 'folioedgecore'),
                    'type' => Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 8,
                    'step' => 1,
                    'default' => 2,
                ]
            );
        
            $this->add_control(
                'sltablet_padding',
                [
                    'label' => __( 'Center padding', 'folioedgecore' ),
                    'type' => Controls_Manager::NUMBER,
                    'min' => 0,
                    'max' => 768,
                    'step' => 1,
                    'default' => 30,
                ]
            );

            $this->add_control(
                'heading_mobile',
                [
                    'label' => __( 'Mobile Phone', 'folioedgecore' ),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'after',
                ]
            );
        
            $this->add_control(
                'slmobile_width',
                [
                    'label' => __('Mobile Resolution', 'folioedgecore'),
                    'description' => __('The resolution to mobile.', 'folioedgecore'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 768,
                ]
            );

            $this->add_control(
                'slmobile_display_columns',
                [
                    'label' => __('Slider Items', 'folioedgecore'),
                    'type' => Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 4,
                    'step' => 1,
                    'default' => 1,
                ]
            );

            $this->add_control(
                'slmobile_padding',
                [
                    'label' => __( 'Center padding', 'folioedgecore' ),
                    'type' => Controls_Manager::NUMBER,
                    'min' => 0,
                    'max' => 500,
                    'step' => 1,
                    'default' => 30,
                ]
            );
        $this->end_controls_section();
        
        // blog Style tab section
        $this->start_controls_section(
            'box_style_section',
            [
                'label' => __( 'Single Card', 'folioedgecore' ),
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
                    '{{WRAPPER}} .team-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .team-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                'selector' => '{{WRAPPER}} .team-box',
            ]
        );

        $this->add_responsive_control(
            'box_style_text_align',
            [
                'label' => __( 'Alignment', 'folioedgecore' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'folioedgecore' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'folioedgecore' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'folioedgecore' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __( 'Justified', 'folioedgecore' ),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .team-box' => 'text-align: {{VALUE}};',
                ],
                'default' => 'left',
                'separator' =>'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'box_style_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .team-box',
            ]
        );
        $this->add_responsive_control(
            'box_style_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .team-box' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_style_box_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .team-box',
            ]
        );

        
        $this->add_control(
			'box_style_box_transform',
			[
				'label' => __( 'Transform', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'translateY(0)',
				'selectors' => [
					'{{WRAPPER}} .team-box' => 'transform: {{VALUE}}',
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
					'{{WRAPPER}} .team-box' => 'transition-duration: {{SIZE}}s',
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
                'selector' => '{{WRAPPER}} .team-box:hover',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'box_style_border_hover',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .team-box:hover',
            ]
        );
        $this->add_responsive_control(
            'box_style_hover_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .team-box:hover' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_style_box_hover_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .team-box:hover',
            ]
        );
        $this->add_control(
			'box_style_hover_transform',
			[
				'label' => __( 'Transform', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'translateY(0)',
				'selectors' => [
					'{{WRAPPER}} .team-box:hover' => 'transform: {{VALUE}}',
				],
			]
		);
        
        $this->end_controls_tab(); // Hover Style tab end        
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section(); // blog Box section style end
        
        // blog Style tab section
        $this->start_controls_section(
            'content_style_section',
            [
                'label' => __( 'Content Area', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->start_controls_tabs('content_style_tab');
        $this->start_controls_tab( 'content_style_normal',
			[
				'label' => __( 'Normal', 'folioedgecore' ),
			]
		);
        
        $this->add_responsive_control(
            'content_style_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .team-box .team_content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'content_style_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .team-box .team_content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'content_style_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .team-box .team_content',
            ]
        );

        $this->add_responsive_control(
            'content_style_text_align',
            [
                'label' => __( 'Alignment', 'folioedgecore' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'folioedgecore' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'folioedgecore' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'folioedgecore' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __( 'Justified', 'folioedgecore' ),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .team-box .team_content' => 'text-align: {{VALUE}};',
                ],
                'default' => 'left',
                'separator' =>'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'content_style_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .team-box .team_content',
            ]
        );
        $this->add_responsive_control(
            'content_style_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .team-box .team_content' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'content_style_content_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .team-box .team_content',
            ]
        );

        
        $this->add_control(
			'content_style_content_transform',
			[
				'label' => __( 'Transform', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'translateY(0)',
				'selectors' => [
					'{{WRAPPER}} .team-box .team_content' => 'transform: {{VALUE}}',
				],
			]
		);
        
		$this->add_control(
			'content_style_transition',
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
					'{{WRAPPER}} .team-box .team_content' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
		$this->end_controls_tab();

             
        // Hover Style tab Start
        $this->start_controls_tab(
            'content_style_hover',
            [
                'label' => __( 'Hover', 'folioedgecore' ),
            ]
        );
                
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'content_style_hover_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .team-box:hover .team_content',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'content_style_border_hover',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .team-box:hover .team_content',
            ]
        );
        $this->add_responsive_control(
            'content_style_hover_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .team-box:hover .team_content' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'content_style_content_hover_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .team-box:hover .team_content',
            ]
        );
        $this->add_control(
			'content_style_hover_transform',
			[
				'label' => __( 'Transform', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'translateY(0)',
				'selectors' => [
					'{{WRAPPER}} .team-box:hover .team_content' => 'transform: {{VALUE}}',
				],
			]
		);
        
        $this->end_controls_tab(); // Hover Style tab end        
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section(); // blog Box section style end
        
        // blog Style tab section
        $this->start_controls_section(
            'box_thumbnail_section',
            [
                'label' => __( 'Photo', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'thumbnail_width',
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
					'{{WRAPPER}} .team-box .photo' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);        
		$this->add_responsive_control(
			'thumbnail_height',
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
					'{{WRAPPER}} .team-box .photo' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'thumbnail_line_height',
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
					'{{WRAPPER}} .team-box .photo' => 'line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'thumbnail_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .team-box .photo',
            ]
        );
        $this->add_responsive_control(
            'thumbnail_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .team-box .photo' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'thumbnail_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .team-box .photo' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'thumbnail_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .team-box .photo',
            ]
        );
        $this->add_responsive_control(
            'thumbnail_border_radius',
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
                    '{{WRAPPER}} .team-box .photo' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'thumbnail_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .team-box .photo',
            ]
        );        
        $this->add_control(
			'box_thumbnail_transition',
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
					'{{WRAPPER}} .team-box .photo' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
        
        $this->end_controls_section();        
        
        
        $this->start_controls_section(
            'title_style_section',
            [
                'label' => __( 'Name', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
            $this->add_control(
                'title_color',
                [
                    'label' => __( 'Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .team-box .team-name a' => 'color: {{VALUE}}',
                    ],
                ]
            );
        
            $this->add_control(
                'title_hover_color',
                [
                    'label' => __( 'Hover Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .team-box:hover .team-name a' => 'color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'title_typography',
                    'label' => __( 'Typography', 'folioedgecore' ),
                    'selector' => '{{WRAPPER}} .team-box .team-name',
                ]
            );

            $this->add_responsive_control(
                'title_margin',
                [
                    'label' => __( 'Margin', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .team-box .team-name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'title_padding',
                [
                    'label' => __( 'Padding', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .team-box .team-name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

        $this->end_controls_section();
        
        
        $this->start_controls_section(
            'position_style_section',
            [
                'label' => __( 'position', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
            $this->add_control(
                'position_color',
                [
                    'label' => __( 'Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .team-box .position' => 'color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_control(
                'position_hover_color',
                [
                    'label' => __( 'Hover Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .team-box:hover .position' => 'color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'position_typography',
                    'label' => __( 'Typography', 'folioedgecore' ),
                    'selector' => '{{WRAPPER}} .team-box .position',
                ]
            );

            $this->add_responsive_control(
                'position_margin',
                [
                    'label' => __( 'Margin', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .team-box .position' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'position_padding',
                [
                    'label' => __( 'Padding', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .team-box .position' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

        $this->end_controls_section();
        
        $this->start_controls_section(
            'desc_style_section',
            [
                'label' => __( 'Description', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
            $this->add_control(
                'desc_color',
                [
                    'label' => __( 'Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .team-box .desc' => 'color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'desc_typography',
                    'label' => __( 'Typography', 'folioedgecore' ),
                    'selector' => '{{WRAPPER}} .team-box .desc',
                ]
            );

            $this->add_responsive_control(
                'desc_margin',
                [
                    'label' => __( 'Margin', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .team-box .desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'desc_padding',
                [
                    'label' => __( 'Padding', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .team-box .desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

        $this->end_controls_section();
        
        // Social Style tab section
        $this->start_controls_section(
            'box_icon_section',
            [
                'label' => __( 'Social', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'social_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .team-box .social-menu',
            ]
        );  
        $this->add_responsive_control(
            'social_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .team-box .social-menu' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'social_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .team-box .social-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'social_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .team-box .social-menu',
            ]
        );
        
        $this->start_controls_tabs('box_icon_style_tab');
        
        $this->start_controls_tab( 'box_icon_normal',
			[
				'label' => __( 'Normal', 'folioedgecore' ),
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
					'{{WRAPPER}} .team-box .social-menu li a' => 'width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .team-box .social-menu li a' => 'height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .team-box .social-menu li a' => 'line-height: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .team-box .social-menu li a' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'icon_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .team-box .social-menu li a' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'icon_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .team-box .social-menu li a',
            ]
        );  
        $this->add_responsive_control(
            'icon_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .team-box .social-menu li a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .team-box .social-menu li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'icon_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .team-box .social-menu li a',
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
                    '{{WRAPPER}} .team-box .social-menu li a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'icon_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .team-box .social-menu li a',
            ]
        );        
        $this->add_control(
			'box_icon_transition',
			[
				'label' => __( 'Transition Duration', 'folioedgecore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .team-box .social-menu li a' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
        $this->end_controls_tab(); // Hover Style tab end
        $this->start_controls_tab( 'box_icon_hover',
			[
				'label' => __( 'Hover', 'folioedgecore' ),
			]
		);        
        $this->add_control(
            'hover_icon_color',
            [
                'label' => __( 'Hover Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .team-box .social-menu li a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'hover_icon_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .team-box .social-menu li a:hover',
            ]
        );               
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'hover_icon_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .team-box .social-menu li a:hover',
            ]
        );
        $this->add_responsive_control(
            'hover_icon_border_radius',
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
                    '{{WRAPPER}} .team-box .social-menu li a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'hover_icon_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .team-box .social-menu li a:hover',
            ]
        );        
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section();
        
        // Social Style tab section
        $this->start_controls_section(
            'box_toggle_section',
            [
                'label' => __( 'Social Toggle', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' =>[
                    'team_style' => 'style2',
                ],
            ]
        );
                
        $this->start_controls_tabs('box_toggle_style_tab');
        
        $this->start_controls_tab( 'box_toggle_normal',
			[
				'label' => __( 'Normal', 'folioedgecore' ),
			]
		);        
        
		$this->add_responsive_control(
			'toggle_width',
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
					'{{WRAPPER}} .team-box .toggle-social' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);        
        
		$this->add_responsive_control(
			'toggle_height',
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
					'{{WRAPPER}} .team-box .toggle-social' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'toggle_line_height',
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
					'{{WRAPPER}} .team-box .toggle-social' => 'line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
        $this->add_responsive_control(
            'toggle_size',
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
                    '{{WRAPPER}} .team-box .toggle-social' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'toggle_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .team-box .toggle-social' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'toggle_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .team-box .toggle-social',
            ]
        );  
        $this->add_responsive_control(
            'toggle_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .team-box .toggle-social' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'toggle_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .team-box .toggle-social' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'toggle_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .team-box .toggle-social',
            ]
        );
        $this->add_responsive_control(
            'toggle_border_radius',
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
                    '{{WRAPPER}} .team-box .toggle-social' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'toggle_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .team-box .toggle-social',
            ]
        );        
        $this->add_control(
			'box_toggle_transition',
			[
				'label' => __( 'Transition Duration', 'folioedgecore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .team-box .toggle-social' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
        $this->end_controls_tab(); // Hover Style tab end
        $this->start_controls_tab( 'box_toggle_hover',
			[
				'label' => __( 'Hover', 'folioedgecore' ),
			]
		);        
        $this->add_control(
            'hover_toggle_color',
            [
                'label' => __( 'Hover Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .team-box .toggle-social:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'hover_toggle_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .team-box .toggle-social:hover',
            ]
        );               
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'hover_toggle_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .team-box .toggle-social:hover',
            ]
        );
        $this->add_responsive_control(
            'hover_toggle_border_radius',
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
                    '{{WRAPPER}} .team-box .toggle-social:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'hover_toggle_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .team-box .toggle-social:hover',
            ]
        );        
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section();
        // Style Slider arrow style start
        $this->start_controls_section(
            'slider_arrow_style',
            [
                'label'     => __( 'Arrow', 'folioedgecore' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' =>[
                    'slider_on' => 'yes',
                    'sl_navigation'  => 'yes',
                ],
            ]
        );
        
            $this->start_controls_tabs( 'slider_arrow_style_tabs' );

                // Normal tab Start
                $this->start_controls_tab(
                    'slider_arrow_style_normal_tab',
                    [
                        'label' => __( 'Normal', 'folioedgecore' ),
                    ]
                );

                    $this->add_control(
                        'slider_arrow_color',
                        [
                            'label' => __( 'Color', 'folioedgecore' ),
                            'type' => Controls_Manager::COLOR,
                            'default' => '#100F0F',
                            'selectors' => [
                                '{{WRAPPER}} .swiper-navigation .swiper-arrow' => 'color: {{VALUE}};',
                            ],
                        ]
                    );
                    $this->add_responsive_control(
                        'slider_arrow_gap',
                        [
                            'label' => __( 'Arrow Gap', 'folioedgecore' ),
                            'type' => Controls_Manager::SLIDER,
                            'size_units' => [ 'px' ],
                            'range' => [
                                'px' => [
                                    'min' => -100,
                                    'max' => 100,
                                    'step' => 1,
                                ]
                            ],
                            'default' => [
                                'unit' => 'px',
                                'size' => 30,
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .swiper-navigation .swiper-arrow.swiper-prev' => 'left: {{SIZE}}{{UNIT}};',
                                '{{WRAPPER}} .swiper-navigation .swiper-arrow.swiper-next' => 'right: {{SIZE}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_responsive_control(
                        'slider_arrow_fontsize',
                        [
                            'label' => __( 'Font Size', 'folioedgecore' ),
                            'type' => Controls_Manager::SLIDER,
                            'size_units' => [ 'px', '%' ],
                            'range' => [
                                'px' => [
                                    'min' => 0,
                                    'max' => 100,
                                    'step' => 1,
                                ],
                                '%' => [
                                    'min' => 0,
                                    'max' => 100,
                                ],
                            ],
                            'default' => [
                                'unit' => 'px',
                                'size' => 20,
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .swiper-navigation .swiper-arrow' => 'font-size: {{SIZE}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name' => 'slider_arrow_background',
                            'label' => __( 'Background', 'folioedgecore' ),
                            'types' => [ 'classic', 'gradient' ],
                            'selector' => '{{WRAPPER}} .swiper-navigation .swiper-arrow',
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Border::get_type(),
                        [
                            'name' => 'slider_arrow_border',
                            'label' => __( 'Border', 'folioedgecore' ),
                            'selector' => '{{WRAPPER}} .swiper-navigation .swiper-arrow',
                        ]
                    );

                    $this->add_responsive_control(
                        'slider_border_radius',
                        [
                            'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'selectors' => [
                                '{{WRAPPER}} .swiper-navigation .swiper-arrow' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                            ],
                        ]
                    );

                    $this->add_responsive_control(
                        'slider_arrow_width',
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
                            'default' => [
                                'unit' => 'px',
                                'size' => 40,
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .swiper-navigation .swiper-arrow' => 'width: {{SIZE}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_responsive_control(
                        'slider_arrow_height',
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
                            'default' => [
                                'unit' => 'px',
                                'size' => 40,
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .swiper-navigation .swiper-arrow' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_responsive_control(
                        'slider_arrow_line_height',
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
                            'default' => [
                                'unit' => 'px',
                                'size' => 40,
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .swiper-navigation .swiper-arrow' => 'line-height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_responsive_control(
                        'slider_arrow_padding',
                        [
                            'label' => __( 'Padding', 'folioedgecore' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'size_units' => [ 'px', '%', 'em' ],
                            'selectors' => [
                                '{{WRAPPER}} .swiper-navigation .swiper-arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                            'separator' =>'before',
                        ]
                    );

                $this->end_controls_tab(); // Normal tab end

                // Hover tab Start
                $this->start_controls_tab(
                    'slider_arrow_style_hover_tab',
                    [
                        'label' => __( 'Hover', 'folioedgecore' ),
                    ]
                );

                    $this->add_control(
                        'slider_arrow_hover_color',
                        [
                            'label' => __( 'Color', 'folioedgecore' ),
                            'type' => Controls_Manager::COLOR,
                            'default' => '#ffffff',
                            'selectors' => [
                                '{{WRAPPER}} .swiper-navigation .swiper-arrow:hover' => 'color: {{VALUE}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name' => 'slider_arrow_hover_background',
                            'label' => __( 'Background', 'folioedgecore' ),
                            'types' => [ 'classic', 'gradient' ],
                            'selector' => '{{WRAPPER}} .swiper-navigation .swiper-arrow:hover',
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Border::get_type(),
                        [
                            'name' => 'slider_arrow_hover_border',
                            'label' => __( 'Border', 'folioedgecore' ),
                            'selector' => '{{WRAPPER}} .swiper-navigation .swiper-arrow:hover',
                        ]
                    );

                    $this->add_responsive_control(
                        'slider_arrow_hover_border_radius',
                        [
                            'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'selectors' => [
                                '{{WRAPPER}} .swiper-navigation .swiper-arrow:hover' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                            ],
                        ]
                    );

                $this->end_controls_tab(); // Hover tab end

            $this->end_controls_tabs();

        $this->end_controls_section(); // Style Slider arrow style end

        // Style Pagination button tab section
        $this->start_controls_section(
            'post_slider_pagination_style_section',
            [
                'label' => __( 'Pagination', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'=>[
                    'slider_on' => 'yes',
                    'slpaginate'=>'yes',
                ]
            ]
        );
            
            $this->start_controls_tabs('pagination_style_tabs');
            $this->add_responsive_control(
                'pagination_alignment',
                [
                    'label' => __( 'Alignment', 'folioedgecore' ),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => __( 'Left', 'folioedgecore' ),
                            'icon' => 'eicon-text-align-left',
                        ],
                        'center' => [
                            'title' => __( 'Center', 'folioedgecore' ),
                            'icon' => 'eicon-text-align-center',
                        ],
                        'right' => [
                            'title' => __( 'Right', 'folioedgecore' ),
                            'icon' => 'eicon-text-align-right',
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .swiper-pagination-bullet' => 'text-align: {{VALUE}};'
                    ],
                    'default' => 'center',
                    'separator' =>'before',
                ]
            );
                $this->start_controls_tab(
                    'pagination_style_normal_tab',
                    [
                        'label' => __( 'Normal', 'folioedgecore' ),
                    ]
                );

                    $this->add_responsive_control(
                        'slider_pagination_height',
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
                            'default' => [
                                'unit' => 'px',
                                'size' => 15,
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_responsive_control(
                        'slider_pagination_width',
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
                            'default' => [
                                'unit' => 'px',
                                'size' => 15,
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name' => 'pagination_background',
                            'label' => __( 'Background', 'folioedgecore' ),
                            'types' => [ 'classic', 'gradient' ],
                            'selector' => '{{WRAPPER}} .swiper-pagination-bullet',
                        ]
                    );

                    $this->add_responsive_control(
                        'pagination_margin',
                        [
                            'label' => __( 'Margin', 'folioedgecore' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'size_units' => [ 'px', '%', 'em' ],
                            'selectors' => [
                                '{{WRAPPER}} .swiper-pagination-bullet' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Border::get_type(),
                        [
                            'name' => 'pagination_border',
                            'label' => __( 'Border', 'folioedgecore' ),
                            'selector' => '{{WRAPPER}} .swiper-pagination-bullet',
                        ]
                    );

                    $this->add_responsive_control(
                        'pagination_border_radius',
                        [
                            'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'selectors' => [
                                '{{WRAPPER}} .swiper-pagination-bullet' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                            ],
                        ]
                    );
                    $this->add_responsive_control(
                        'pagination_opacity',
                        [
                            'label' => __( 'Opacity (%)', 'folioedgecore' ),
                            'type' => Controls_Manager::SLIDER,
                            'default' => [
                                'size' => 0.1,
                            ],
                            'range' => [
                                'px' => [
                                    'max' => 1,
                                    'step' => 0.01,
                                ],
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .swiper-pagination-bullet' => 'opacity: {{SIZE}}',
                                
                            ],
                        ]
                    ); 
                $this->end_controls_tab(); // Normal Tab end

                $this->start_controls_tab(
                    'pagination_style_active_tab',
                    [
                        'label' => __( 'Active', 'folioedgecore' ),
                    ]
                );
                    
                    $this->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name' => 'pagination_hover_background',
                            'label' => __( 'Background', 'folioedgecore' ),
                            'types' => [ 'classic', 'gradient' ],
                            'selector' => '{{WRAPPER}} .swiper-pagination-bullet:hover, {{WRAPPER}} .swiper-pagination-bullet.swiper-pagination-bullet-active',
                        ]
                    );
                    $this->add_responsive_control(
                        'slider_pagination_active_width',
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
                            'default' => [
                                'unit' => 'px',
                                'size' => 15,
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}};',
                            ],
                        ]
                    );
                    $this->add_responsive_control(
                        'pagination_active_opacity',
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
                                '{{WRAPPER}} .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'opacity: {{SIZE}}',
                                
                            ],
                        ]
                    ); 

                $this->end_controls_tab(); // Hover Tab end
            $this->end_controls_tabs();
        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display();
        $custom_order_ck    = $this->get_settings_for_display('custom_order');
        $orderby            = $this->get_settings_for_display('orderby');
        $postorder          = $this->get_settings_for_display('postorder');     
        $column = $button_icon = $starting = $start_date = $start_time = $team_url = '';
        // Query
        $args = array(
            'post_type'             => 'team',
            'post_status'           => 'publish',
            'post__in'              => $settings['title_list'],
            'ignore_sticky_posts'   => 1,
            'posts_per_page'        => !empty( $settings['post_limit'] ) ? $settings['post_limit'] : 3,
            'order'                 => $postorder
        );
        // Custom Order
        if( $custom_order_ck == 'yes' ){
            $args['orderby']    = $orderby;
        }        
        $category_list = str_replace(' ', '', $settings['category_list']);        
        if (  !empty( $category_list ) ) {
            if( is_array($category_list) and count($category_list) > 0 ){
                $field_name = is_numeric( $category_list[0] ) ? 'term_id' : 'slug';
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'team_category',
                        'terms' => $category_list,
                        'field' => $field_name,
                        'include_children' => false
                    )
                );
            }
        }
        $team_post = new \WP_Query( $args );
       
        if( $settings['slider_on'] == 'yes' ) {               
            $this->add_render_attribute( 'wrapper_attributes', 'class', 'swiper-container' );
            $slider_settings = [
                'sleffect' => $settings['sleffect'],
                'slloop' => ('yes' === $settings['slloop']),
                'slautolay' => ('yes' === $settings['slautolay']),
                'slautolaydelay' => absint($settings['slautolaydelay']),
                'slanimation_speed' => absint($settings['slanimation_speed']),
                'slcustom_arrow' => ('yes' === $settings['slider_custom_arrow']),
                'sltarget_id' => $settings['slider_target_id'],
                'sldisplay_columns' => $settings['sldisplay_columns'],
                'slcenter' => ('yes' === $settings['slcenter']),
                'slcenter_padding' => $settings['slcenter_padding'],
            ];
            $slider_responsive_settings = [
                'laptop_width' => $settings['sllaptop_width'],
                'laptop_padding' => $settings['sllaptop_padding'],
                'laptop_display_columns' => $settings['sllaptop_display_columns'],
                'tablet_width' => $settings['sltablet_width'],
                'tablet_padding' => $settings['sltablet_padding'],
                'tablet_display_columns' => $settings['sltablet_display_columns'],
                'mobile_width' => $settings['slmobile_width'],
                'mobile_padding' => $settings['slmobile_padding'],
                'mobile_display_columns' => $settings['slmobile_display_columns'],
            ];
            $slider_settings = array_merge( $slider_settings, $slider_responsive_settings );
            $this->add_render_attribute( 'wrapper_attributes', 'data-settings', wp_json_encode( $slider_settings ) );
        }else {
            $this->add_render_attribute( 'wrapper_attributes', 'class', ['team-grid-list row', esc_attr($settings['grid_space'])] );
            if($settings['masonry'] == 'yes'){
                $this->add_render_attribute( 'wrapper_attributes', 'class', 'masonry_lists' );
            }
            switch ($settings['item_column']) {
                case "1grid":
                    $column .= "col-lg-12 ";
                    break;
                case "2grid":
                    $column .= "col-lg-6 col-md-12";
                    break;
                case "3grid":
                    $column .= "col-lg-4 col-md-6";
                    break;
                default:
                    $column .= "col-xl-3 col-lg-4 col-md-6";
            }
        }
        echo '<div class="team-widget-section">';
        if($team_post->have_posts()){
            if( $settings['slider_on'] == 'yes' ) {                
                echo '<div '.$this->get_render_attribute_string( "wrapper_attributes" ).'  >';
                    echo '<div class="swiper-wrapper">';
                        while ($team_post->have_posts()):
                            $team_post->the_post();
                            echo '<div class="swiper-slide">';   
                                if($settings['team_style'] == 'style1'){
                                    $this->team_style1($settings);
                                }elseif($settings['team_style'] == 'style2'){
                                    $this->team_style2($settings, $team_post);
                                }
                            echo '</div>';
                        endwhile;
                    echo '</div>';
                    if( $settings['sl_navigation'] == true && $settings['slider_custom_arrow'] != true ){
                        echo '<div class="swiper-navigation" >';
                            echo '<div class="swiper-arrow swiper-prev"><i class="'.esc_attr($settings['sl_nav_prev_icon']).'" ></i></div>';
                            echo '<div class="swiper-arrow swiper-next"><i class="'.esc_attr($settings['sl_nav_next_icon']).'" ></i></div>';
                        echo '</div>';
                    }
                    if( $settings['slpaginate'] == true ){
                        echo '<div class="swiper-pagination"></div>';
                    }
                echo '</div>';
            }else{
                echo '<div '.$this->get_render_attribute_string( "wrapper_attributes" ).'  >';
                    while ($team_post->have_posts()):
                        $team_post->the_post();
                        echo '<div class="'.$column.'">';
                            if($settings['team_style'] == 'style1'){
                                $this->team_style1($settings);
                            }elseif($settings['team_style'] == 'style2'){
                                $this->team_style2($settings, $team_post);
                            }
                        echo '</div>';
                    endwhile;
                echo '</div>';                
            }
        }elseif(current_user_can('edit_posts')){
            echo '<div class="empty-desc">';
            echo esc_html__( 'Ready to publish your first team member?', 'folioedgecore' );
            echo ' <a href="'.esc_url( admin_url( 'post-new.php?post_type=team' ) ).'">';
            echo esc_html__('Get started here','folioedgecore');
            echo '</a>';
            echo '</div>';
        }
        echo '</div>';        
    }
    
    protected function team_style1($settings){        
        echo '<div class="team-box style1">'; 
            if(has_post_thumbnail() && $settings['show_thumb'] == 'yes'):
                echo '<figure class="photo">';            
                    the_post_thumbnail($settings['thumb_size_size']);
                echo '</figure>';
            endif;
            echo '<div class="team_content">';      
                if($settings['show_name'] == 'yes' && !empty(get_the_title())){
                    echo '<h4 class="team-name"><a href="'.get_the_permalink().'">'. get_the_title() .'</a></h3>';
                }
                if($settings['show_position'] == 'yes' && get_post_meta( get_the_ID(), '_folioedge_team_position', true )){
                    echo '<div class="position">'.get_post_meta( get_the_ID(), '_folioedge_team_position', true ).'</div>';
                }
                if($settings['show_content'] == 'yes'){
                    echo '<div class="desc">'.wpautop(wp_trim_words( get_the_content(), $settings['content_length'], ' ' )).'</div>';
                }       
                if( $settings['show_social'] == 'yes' ){                                          
                    echo '<ul class="social-menu">';
                        if(get_post_meta( get_the_ID(), '_folioedge_team_facebook', true )){
                            echo '<li><a href="' . esc_url(get_post_meta( get_the_ID(), '_folioedge_team_facebook', true )) . '"><svg class="svg-icon"><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-facebook"></use></svg></a></li>';
                        }
                        if(get_post_meta( get_the_ID(), '_folioedge_team_twitter', true )){
                            echo '<li><a href="' . esc_url(get_post_meta( get_the_ID(), '_folioedge_team_twitter', true )) . '"><svg class="svg-icon"><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-twitter"></use></svg></a></li>';
                        }
                        if(get_post_meta( get_the_ID(), '_folioedge_team_linkedin', true )){
                            echo '<li><a href="' . esc_url(get_post_meta( get_the_ID(), '_folioedge_team_linkedin', true )) . '"><svg class="svg-icon"><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-linkedin"></use></svg></a></li>';
                        }
                        if(get_post_meta( get_the_ID(), '_folioedge_team_instagram', true )){
                            echo '<li><a href="' . esc_url(get_post_meta( get_the_ID(), '_folioedge_team_instagram', true )) . '"><svg class="svg-icon"><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-instagram"></use></svg></a></li>';
                        }
                    echo '</ul>';
                }
            echo '</div>';
        echo '</div>';
    }
    protected function team_style2($settings, $post){        
        echo '<div class="team-box style2">'; 
                echo '<div class="photo">';            
                    if(has_post_thumbnail() && $settings['show_thumb'] == 'yes'):
                        the_post_thumbnail($settings['thumb_size_size']);
                    endif;
                    if( $settings['show_social'] == 'yes' ){
                        echo '<div class="social-collapse collapse" id="tm-social-'.get_the_ID().'" >';
                            echo '<ul class="social-menu" >';
                                if(get_post_meta( get_the_ID(), '_folioedge_team_facebook', true )){
                                    echo '<li><a href="' . esc_url(get_post_meta( get_the_ID(), '_folioedge_team_facebook', true )) . '"><svg class="svg-icon"><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-facebook"></use></svg></a></li>';
                                }
                                if(get_post_meta( get_the_ID(), '_folioedge_team_twitter', true )){
                                    echo '<li><a href="' . esc_url(get_post_meta( get_the_ID(), '_folioedge_team_twitter', true )) . '"><svg class="svg-icon"><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-twitter"></use></svg></a></li>';
                                }
                                if(get_post_meta( get_the_ID(), '_folioedge_team_linkedin', true )){
                                    echo '<li><a href="' . esc_url(get_post_meta( get_the_ID(), '_folioedge_team_linkedin', true )) . '"><svg class="svg-icon"><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-linkedin"></use></svg></a></li>';
                                }
                                if(get_post_meta( get_the_ID(), '_folioedge_team_instagram', true )){
                                    echo '<li><a href="' . esc_url(get_post_meta( get_the_ID(), '_folioedge_team_instagram', true )) . '"><svg class="svg-icon"><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-instagram"></use></svg></a></li>';
                                }
                            echo '</ul>';
                        echo '</div>';
                    }
                echo '</div>';
            echo '<div class="team_content">';  
                echo '<div class="left-side">';
                    if($settings['show_name'] == 'yes' && !empty(get_the_title())){
                        echo '<h4 class="team-name"><a href="'.get_the_permalink().'">'. get_the_title() .'</a></h3>';
                    }
                    if($settings['show_position'] == 'yes' && get_post_meta( get_the_ID(), '_folioedge_team_position', true )){
                        echo '<div class="position">'.get_post_meta( get_the_ID(), '_folioedge_team_position', true ).'</div>';
                    }
                    if($settings['show_content'] == 'yes'){
                        echo '<div class="desc">'.wpautop(wp_trim_words( get_the_content(), $settings['content_length'], ' ' )).'</div>';
                    }
                echo '</div>';
                if( $settings['show_social'] == 'yes' ){
                    echo '<button type="button" data-bs-toggle="collapse" data-bs-target="#tm-social-'.get_the_ID().'" class="toggle-social collapsed"><svg class="svg-icon"><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-plus"></use></svg></button>';
                }
            echo '</div>';
        echo '</div>';
    }
}
Plugin::instance()->widgets_manager->register( new folioedge_team_Widget() );
