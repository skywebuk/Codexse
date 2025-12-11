<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class folioedge_Testimonial_Widget extends Widget_Base {

    public function get_name() {
        return 'folioedge-testimonial-widget';
    }
    
    public function get_title() {
        return __( 'Testimonial', 'folioedgecore' );
    }

    public function get_icon() {
        return 'folioedge-icon eicon-testimonial-carousel';
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
            'folioedge_testimonial_content_section',
            [
                'label' => __( 'Testimonial', 'folioedgecore' ),
            ]
        );
        
            $this->add_control(
                'testimonial_style',
                [
                    'label' => esc_html__( 'Box Style', 'folioedgecore' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 's1',
                    'options' => [
                        's1'  => __( 'One', 'folioedgecore' ),
                        's2'  => __( 'Two', 'folioedgecore' ),
                        's3'  => __( 'Three', 'folioedgecore' ),
                        's4'  => __( 'Four', 'folioedgecore' ),
                        's5'  => __( 'Five', 'folioedgecore' ),
                    ],
                    'separator'=>'none',
                ]
            );

            $this->add_control(
                'slider_on',
                [
                    'label' => esc_html__( 'Slider', 'folioedgecore' ),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'yes',
                    'separator'=>'before',
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
                'masonry',
                [
                    'label'         => __( 'Masonry', 'folioedgecore' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'label_on'      => __( 'On', 'folioedgecore' ),
                    'label_off'     => __( 'Off', 'folioedgecore' ),
                    'return_value'  => 'yes',
                    'default'       => 'no',
                    'separator'     => 'after',
                    'condition' => [
                        'slider_on!' => 'yes',
                    ]
                ]
            );
        
            $repeater = new Repeater();
            $repeater->add_control(
                'client_image',
                [
                    'label' => __( 'Client Photo', 'folioedgecore' ),
                    'type' => Controls_Manager::MEDIA,
                ]
            );

            $repeater->add_group_control(
                Group_Control_Image_Size::get_type(),
                [
                    'name' => 'client_imagesize',
                    'default' => 'full',
                    'separator' => 'none',
                ]
            );
            $repeater->add_control(
                'client_name',
                [
                    'label'   => __( 'Name', 'folioedgecore' ),
                    'type'    => Controls_Manager::TEXT,
                    'default' => __('Carolina Monntoya','folioedgecore'),
                ]
            );

            $repeater->add_control(
                'client_designation',
                [
                    'label'   => __( 'Designation', 'folioedgecore' ),
                    'type'    => Controls_Manager::TEXT,
                    'default' => __('Managing Director','folioedgecore'),
                ]
            );

            $repeater->add_control(
                'client_say',
                [
                    'label'   => __( 'Client Said', 'folioedgecore' ),
                    'type'    => Controls_Manager::TEXTAREA,
                    'default' => __('Massa eget egestas purus viverra accumsan in nisl nisi. Aliquam faucibus purus in massa nibh tellus tempor nec lorem','folioedgecore'),
                ]
            );
        
            $repeater->add_control(
                'feed_rating',
                [
                    'label' => __( 'Rating', 'folioedgecore' ),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 3,
                    ],
                    'range' => [
                        'px' => [
                            'max' => 5,
                            'step' => 0.1,
                        ],
                    ],
                ]
            );
        
            $repeater->add_control(
                'client_logo_image',
                [
                    'label' => __( 'Client Logo', 'folioedgecore' ),
                    'type' => Controls_Manager::MEDIA,
                ]
            );

            $repeater->add_group_control(
                Group_Control_Image_Size::get_type(),
                [
                    'name' => 'client_logo_imagesize',
                    'default' => 'full',
                    'separator' => 'none',
                ]
            );

            $this->add_control(
                'folioedge_testimonial_list',
                [
                    
				    'label' => esc_html__( 'Testimonial Items', 'folioedgecore' ),
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'separator'=>'before',
                    'default' => [
                        [
                            'feed_rating'           => 4,
                            'client_name'           => __('Alex John','folioedgecore'),
                            'client_designation'    => __( 'Voluntter','folioedgecore' ),
                            'client_say'            => __( 'Massa eget egestas purus viverra accumsan in nisl nisi. Aliquam faucibus purus in massa nibh tellus tempor nec lorem', 'folioedgecore' ),
                        ],
                        [
                            'feed_rating'           => 4,
                            'client_name'           => __('J ack lamps','folioedgecore'),
                            'client_designation'    => __( 'Voluntter','folioedgecore' ),
                            'client_say'            => __( 'Massa eget egestas purus viverra accumsan in nisl nisi. Aliquam faucibus purus in massa nibh tellus tempor nec lorem', 'folioedgecore' ),
                        ],
                        [
                            'feed_rating'           => 4,
                            'client_name'           => __('Lona Shiro','folioedgecore'),
                            'client_designation'    => __( 'Voluntter','folioedgecore' ),
                            'client_say'            => __( 'Massa eget egestas purus viverra accumsan in nisl nisi. Aliquam faucibus purus in massa nibh tellus tempor nec lorem', 'folioedgecore' ),
                        ],
                    ],
                    'title_field' => '{{{ client_name }}}',
                ]
            );
            $this->add_control(
                'quote_icon_type',
                [
                    'label' => __('Quote Icon Type','folioedgecore'),
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
                        'none' =>[
                            'title' =>__('None','folioedgecore'),
                            'icon' =>'eicon-warning',
                        ],
                    ],
                    'default' => 'none',
                    'condition' => [
                        'testimonial_style' => 's4',
                    ]
                ]
            );

            $this->add_control(
                'quote_image',
                [
                    'label' => __('Image','folioedgecore'),
                    'type'=>Controls_Manager::MEDIA,
                    'default' => [
                        'url' => Utils::get_placeholder_image_src(),
                    ],
                    'condition' => [
                        'quote_icon_type' => 'img',
                        'testimonial_style' => 's4',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Image_Size::get_type(),
                [
                    'name' => 'quote_imagesize',
                    'default' => 'full',
                    'separator' => 'none',
                    'condition' => [
                        'quote_icon_type' => 'img',
                        'testimonial_style' => 's4',
                    ]
                ]
            );

            $this->add_control(
                'quote_icon',
                [
                    'label'       => __( 'Icon', 'folioedgecore' ),
                    'type'        => Controls_Manager::ICONS,
                    'label_block' => true,
                    'condition' => [
                        'quote_icon_type' => 'icon',
                        'testimonial_style' => 's4',
                    ]
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
                    'default' => 1,
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
                    '{{WRAPPER}} .testimonial-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .testimonial-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                'selector' => '{{WRAPPER}} .testimonial-box',
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
                    '{{WRAPPER}} .testimonial-box' => 'text-align: {{VALUE}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'box_style_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .testimonial-box',
            ]
        );
        $this->add_responsive_control(
            'box_style_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .testimonial-box' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_style_box_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .testimonial-box',
            ]
        );

        
        $this->add_control(
			'box_style_box_transform',
			[
				'label' => __( 'Transform', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'translateY(0)',
				'selectors' => [
					'{{WRAPPER}} .testimonial-box' => 'transform: {{VALUE}}',
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
					'{{WRAPPER}} .testimonial-box' => 'transition-duration: {{SIZE}}s',
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
                'selector' => '{{WRAPPER}} .testimonial-box:hover',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'box_style_border_hover',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .testimonial-box:hover',
            ]
        );
        $this->add_responsive_control(
            'box_style_hover_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .testimonial-box:hover' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_style_box_hover_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .testimonial-box:hover',
            ]
        );
        $this->add_control(
			'box_style_hover_transform',
			[
				'label' => __( 'Transform', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'translateY(0)',
				'selectors' => [
					'{{WRAPPER}} .testimonial-box:hover' => 'transform: {{VALUE}}',
				],
			]
		);
        
        $this->end_controls_tab(); // Hover Style tab end        
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section(); // blog Box section style end
                
        
        // Feature Style tab section
        $this->start_controls_section(
            'box_icon_section',
            [
                'label' => __( 'Quote', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' =>[
                    'quote_icon_type!' => 'none',
                    'testimonial_style' => 's4',
                ],
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
					'{{WRAPPER}} .testimonial-box .quote' => 'width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .testimonial-box .quote' => 'height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .testimonial-box .quote' => 'line-height: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .testimonial-box .quote' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'icon_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .testimonial-box .quote' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'icon_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .testimonial-box .quote',
            ]
        );
        $this->add_responsive_control(
            'quote_alignment',
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
                    '{{WRAPPER}} .testimonial-box .quote' => 'text-align: {{VALUE}};'
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
                    '{{WRAPPER}} .testimonial-box .quote' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .testimonial-box .quote' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'icon_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .testimonial-box .quote',
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
                    '{{WRAPPER}} .testimonial-box .quote' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'icon_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .testimonial-box .quote',
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
					'{{WRAPPER}} .testimonial-box .quote' => 'transition-duration: {{SIZE}}s',
				],
			]
		); 
        
            $this->add_responsive_control(
                'ex_icon_z_index',
                [
                    'label' => __( 'Z Index', 'maruncycore' ),
                    'type' => Controls_Manager::NUMBER,
                    'min' => -100,
                    'max' => 100,
                    'step' => 1,
                    'selectors' => [
                        '{{WRAPPER}} .quote' => 'z-index: {{SIZE}};',
                    ],
                ]
            );
                
            $this->add_control(
                '_tst_position',
                [
                    'label' => esc_html__( 'Position', 'elementor' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => '',
                    'options' => [
                        '' => esc_html__( 'Default', 'elementor' ),
                        'absolute' => esc_html__( 'Absolute', 'elementor' ),
                        'fixed' => esc_html__( 'Fixed', 'elementor' ),
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .quote' => 'position: {{VALUE}};',
                    ],
                ]
            );

            $start = is_rtl() ? esc_html__( 'Right', 'elementor' ) : esc_html__( 'Left', 'elementor' );
            $end = ! is_rtl() ? esc_html__( 'Right', 'elementor' ) : esc_html__( 'Left', 'elementor' );

            $this->add_control(
                '_tst_offset_orientation_h',
                [
                    'label' => esc_html__( 'Horizontal Orientation', 'elementor' ),
                    'type' => Controls_Manager::CHOOSE,
                    'toggle' => false,
                    'default' => 'start',
                    'options' => [
                        'start' => [
                            'title' => $start,
                            'icon' => 'eicon-h-align-left',
                        ],
                        'end' => [
                            'title' => $end,
                            'icon' => 'eicon-h-align-right',
                        ],
                    ],
                    'classes' => 'elementor-control-start-end',
                    'render_type' => 'ui',
                    'condition' => [
                        '_tst_position!' => '',
                    ],
                ]
            );

            $this->add_responsive_control(
                '_tst_offset_x',
                [
                    'label' => esc_html__( 'Offset', 'elementor' ),
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => -1000,
                            'max' => 1000,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => -200,
                            'max' => 200,
                        ],
                        'vw' => [
                            'min' => -200,
                            'max' => 200,
                        ],
                        'vh' => [
                            'min' => -200,
                            'max' => 200,
                        ],
                    ],
                    'default' => [
                        'size' => '0',
                    ],
                    'size_units' => [ 'px', '%', 'vw', 'vh' ],
                    'selectors' => [
                        'body:not(.rtl) {{WRAPPER}} .quote' => 'left: {{SIZE}}{{UNIT}}',
                        'body.rtl {{WRAPPER}} .quote' => 'right: {{SIZE}}{{UNIT}}',
                    ],
                    'condition' => [
                        '_tst_offset_orientation_h!' => 'end',
                        '_tst_position!' => '',
                    ],
                ]
            );

            $this->add_responsive_control(
                '_tst_offset_x_end',
                [
                    'label' => esc_html__( 'Offset', 'elementor' ),
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => -1000,
                            'max' => 1000,
                            'step' => 0.1,
                        ],
                        '%' => [
                            'min' => -200,
                            'max' => 200,
                        ],
                        'vw' => [
                            'min' => -200,
                            'max' => 200,
                        ],
                        'vh' => [
                            'min' => -200,
                            'max' => 200,
                        ],
                    ],
                    'default' => [
                        'size' => '0',
                    ],
                    'size_units' => [ 'px', '%', 'vw', 'vh' ],
                    'selectors' => [
                        'body:not(.rtl) {{WRAPPER}} .quote' => 'right: {{SIZE}}{{UNIT}}',
                        'body.rtl {{WRAPPER}} .quote' => 'left: {{SIZE}}{{UNIT}}',
                    ],
                    'condition' => [
                        '_tst_offset_orientation_h' => 'end',
                        '_tst_position!' => '',
                    ],
                ]
            );

            $this->add_control(
                '_tst_offset_orientation_v',
                [
                    'label' => esc_html__( 'Vertical Orientation', 'elementor' ),
                    'type' => Controls_Manager::CHOOSE,
                    'toggle' => false,
                    'default' => 'start',
                    'options' => [
                        'start' => [
                            'title' => esc_html__( 'Top', 'elementor' ),
                            'icon' => 'eicon-v-align-top',
                        ],
                        'end' => [
                            'title' => esc_html__( 'Bottom', 'elementor' ),
                            'icon' => 'eicon-v-align-bottom',
                        ],
                    ],
                    'render_type' => 'ui',
                    'condition' => [
                        '_tst_position!' => '',
                    ],
                ]
            );

            $this->add_responsive_control(
                '_tst_offset_y',
                [
                    'label' => esc_html__( 'Offset', 'elementor' ),
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => -1000,
                            'max' => 1000,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => -200,
                            'max' => 200,
                        ],
                        'vh' => [
                            'min' => -200,
                            'max' => 200,
                        ],
                        'vw' => [
                            'min' => -200,
                            'max' => 200,
                        ],
                    ],
                    'size_units' => [ 'px', '%', 'vh', 'vw' ],
                    'default' => [
                        'size' => '0',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .quote' => 'top: {{SIZE}}{{UNIT}}',
                    ],
                    'condition' => [
                        '_tst_offset_orientation_v!' => 'end',
                        '_tst_position!' => '',
                    ],
                ]
            );

            $this->add_responsive_control(
                '_tst_offset_y_end',
                [
                    'label' => esc_html__( 'Offset', 'elementor' ),
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => -1000,
                            'max' => 1000,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => -200,
                            'max' => 200,
                        ],
                        'vh' => [
                            'min' => -200,
                            'max' => 200,
                        ],
                        'vw' => [
                            'min' => -200,
                            'max' => 200,
                        ],
                    ],
                    'size_units' => [ 'px', '%', 'vh', 'vw' ],
                    'default' => [
                        'size' => '0',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .quote' => 'bottom: {{SIZE}}{{UNIT}}',
                    ],
                    'condition' => [
                        '_tst_offset_orientation_v' => 'end',
                        '_tst_position!' => '',
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
                    '{{WRAPPER}} .testimonial-box:hover .quote' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'hover_icon_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .testimonial-box:hover .quote',
            ]
        );               
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'hover_icon_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .testimonial-box:hover .quote',
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
                    '{{WRAPPER}} .testimonial-box:hover .quote' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'hover_icon_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .testimonial-box:hover .quote',
            ]
        );        
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section();
        
        $this->start_controls_section(
            'content_style_section',
            [
                'label' => __( 'Client Say', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
            $this->add_control(
                'content_color',
                [
                    'label' => __( 'Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .testimonial-box .desc' => 'color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'content_typography',
                    'label' => __( 'Typography', 'folioedgecore' ),
                    'selector' => '{{WRAPPER}} .testimonial-box .desc',
                ]
            );

            $this->add_responsive_control(
                'content_margin',
                [
                    'label' => __( 'Margin', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .testimonial-box .desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'content_padding',
                [
                    'label' => __( 'Padding', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .testimonial-box .desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

        $this->end_controls_section();
        
        
        // blog Style tab section
        $this->start_controls_section(
            'box_thumbnail_section',
            [
                'label' => __( 'Thumbnail', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'testimonial_style' => ['s1','s2','s5'],
                ]
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
					'{{WRAPPER}} .testimonial-box .photo' => 'width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .testimonial-box .photo' => 'height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .testimonial-box .photo' => 'line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'thumbnail_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .testimonial-box .photo',
            ]
        );
        $this->add_responsive_control(
            'thumbnail_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .testimonial-box .photo' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .testimonial-box .photo' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'thumbnail_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .testimonial-box .photo',
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
                    '{{WRAPPER}} .testimonial-box .photo' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'thumbnail_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .testimonial-box .photo',
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
					'{{WRAPPER}} .testimonial-box .photo' => 'transition-duration: {{SIZE}}s',
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
                        '{{WRAPPER}} .testimonial-box .client-name' => 'color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'title_typography',
                    'label' => __( 'Typography', 'folioedgecore' ),
                    'selector' => '{{WRAPPER}} .testimonial-box .client-name',
                ]
            );

            $this->add_responsive_control(
                'title_margin',
                [
                    'label' => __( 'Margin', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .testimonial-box .client-name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                        '{{WRAPPER}} .testimonial-box .client-name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'cl_name_align',
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
                    'selectors' => [
                        '{{WRAPPER}} .testimonial-box .client-name' => 'text-align: {{VALUE}};',
                    ],
                ]
            );

        $this->end_controls_section();
        
        
        $this->start_controls_section(
            'designation_style_section',
            [
                'label' => __( 'Designation', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
            $this->add_control(
                'designation_color',
                [
                    'label' => __( 'Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .testimonial-box .designation' => 'color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'designation_typography',
                    'label' => __( 'Typography', 'folioedgecore' ),
                    'selector' => '{{WRAPPER}} .testimonial-box .designation',
                ]
            );

            $this->add_responsive_control(
                'designation_margin',
                [
                    'label' => __( 'Margin', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .testimonial-box .designation' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'designation_padding',
                [
                    'label' => __( 'Padding', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .testimonial-box .designation' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );
        
            $this->add_responsive_control(
                'designation_align',
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
                    'selectors' => [
                        '{{WRAPPER}} .testimonial-box .designation' => 'text-align: {{VALUE}};',
                    ],
                ]
            );

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
                            'selectors' => [
                                '{{WRAPPER}} .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}};',
                            ],
                        ]
                    );        
                    $this->add_responsive_control(
                        'slider_pagination_active_height',
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
                                '{{WRAPPER}} .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'height: {{SIZE}}{{UNIT}};',
                            ],
                        ]
                    );
                    $this->add_responsive_control(
                        'pagination_active_opacity',
                        [
                            'label' => __( 'Opacity (%)', 'folioedgecore' ),
                            'type' => Controls_Manager::SLIDER,
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
    protected function render( $instance = [] ) {
        $column = '';
        $settings   = $this->get_settings_for_display();
        $this->add_render_attribute( 'wrapper_attributes', 'class', 'testimonial-box-area' );
       
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
            $this->add_render_attribute( 'wrapper_attributes', 'class', ['row', esc_attr($settings['grid_space'])] );
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
                    $column .= "col-lg-3 col-md-6";
            }
        }
        echo '<div class="testimonial-widget-section">';
        if($settings['folioedge_testimonial_list']){
            if( $settings['slider_on'] == 'yes' ) {                
                echo '<div '.$this->get_render_attribute_string( "wrapper_attributes" ).'  >';
                    echo '<div class="swiper-wrapper">';
                        foreach( $settings['folioedge_testimonial_list'] as $items ):  
                            echo '<div class="swiper-slide">';
                                if($settings['testimonial_style'] == 's1'){   
                                    $this->team_style1($items);
                                }elseif($settings['testimonial_style'] == 's2'){   
                                    $this->team_style2($items);
                                }elseif($settings['testimonial_style'] == 's3'){   
                                    $this->team_style3($items);
                                }elseif($settings['testimonial_style'] == 's4'){   
                                    $this->team_style4($items);
                                }elseif($settings['testimonial_style'] == 's5'){   
                                    $this->team_style5($items);
                                }else{
                                    $this->team_style1($items);
                                }
                            echo '</div>';
                        endforeach;
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
                    foreach( $settings['folioedge_testimonial_list'] as $items ):  
                        echo '<div class="'.$column.'">';
                            if($settings['testimonial_style'] == 's1'){   
                                $this->team_style1($items);
                            }elseif($settings['testimonial_style'] == 's2'){   
                                $this->team_style2($items);
                            }elseif($settings['testimonial_style'] == 's3'){   
                                $this->team_style3($items);
                            }elseif($settings['testimonial_style'] == 's4'){   
                                $this->team_style4($items);
                            }elseif($settings['testimonial_style'] == 's5'){   
                                $this->team_style5($items);
                            }else{
                                $this->team_style1($items);
                            }
                        echo '</div>';
                    endforeach;
                echo '</div>';                
            }
        }else {
            esc_html_e('Please make a testimonial! It is empty.','folioedgecore');
        }
        echo '</div>';        
    }
    
    protected function team_style1($items){
        $settings   = $this->get_settings_for_display();
        echo '<div class="testimonial-box s1">';
            echo '<div class="feed-rating">';
                echo '<span class="star front"></span>';
                echo '<span class="star back" style="width: '.($items['feed_rating']['size']*20).'%"></span>';
            echo '</div>';
            echo '<div class="desc">';
                echo esc_html($items['client_say']);
            echo '</div>';
            echo '<div class="footer">';            
                if( !empty(Group_Control_Image_Size::get_attachment_image_html( $items, 'client_imagesize', 'client_image' )) ){
                    echo '<div class="photo">'.Group_Control_Image_Size::get_attachment_image_html( $items, 'client_imagesize', 'client_image' ).'</div>';
                }
                echo '<div class="name-and-designation">';
                    if(!empty($items['client_name'])){
                        echo '<h4 class="client-name">'.esc_html($items['client_name']).'</h4>';
                    }
                    if(!empty($items['client_designation'])){
                        echo '<div class="designation">'.esc_html($items['client_designation']).'</div>';
                    }
                echo '</div>';
            echo '</div>';
        echo '</div>';        
    }
    
    protected function team_style2($items){
        $settings   = $this->get_settings_for_display();
        echo '<div class="testimonial-box s2">';           
            if( !empty(Group_Control_Image_Size::get_attachment_image_html( $items, 'client_imagesize', 'client_image' )) ){
                echo '<div class="thumb"><div class="photo">'.Group_Control_Image_Size::get_attachment_image_html( $items, 'client_imagesize', 'client_image' ).'</div></div>';
            }
            echo '<div class="feed-rating">';
                echo '<span class="star front"></span>';
                echo '<span class="star back" style="width: '.($items['feed_rating']['size']*20).'%"></span>';
            echo '</div>';
            echo '<div class="desc">';
                echo esc_html($items['client_say']);
            echo '</div>';
            echo '<div class="name-and-designation">';
                if(!empty($items['client_name'])){
                    echo '<h4 class="client-name">'.esc_html($items['client_name']).'</h4>';
                }
                if(!empty($items['client_designation'])){
                    echo '<div class="designation">'.esc_html($items['client_designation']).'</div>';
                }
            echo '</div>';
        echo '</div>';
        
    }
    
    protected function team_style3($items){
        $settings   = $this->get_settings_for_display();
        echo '<div class="testimonial-box s3">';
            echo '<div class="desc">';
                echo esc_html($items['client_say']);
            echo '</div>';
            echo '<div class="name-and-designation">';
                if(!empty($items['client_name'])){
                    echo '<h4 class="client-name">'.esc_html($items['client_name']).'</h4>';
                }
                if(!empty($items['client_designation'])){
                    echo '<div class="designation">'.esc_html($items['client_designation']).'</div>';
                }
            echo '</div>';
        echo '</div>';        
    }
    
    protected function team_style4($items){
        $settings   = $this->get_settings_for_display();
        echo '<div class="testimonial-box s4">';        
            if( $settings['quote_icon_type'] == 'img' and !empty(Group_Control_Image_Size::get_attachment_image_html( $settings, 'quote_imagesize', 'quote_image' )) ){
                echo '<div class="quote"><div class="quote">'.Group_Control_Image_Size::get_attachment_image_html( $settings, 'quote_imagesize', 'quote_image' ).'</div></div>';
            }elseif( $settings['quote_icon_type'] == 'icon' && !empty($settings['quote_icon']['value']) ){
                echo sprintf( '<div class="quote" ><div class="quote" >%1$s</div></div>', folioedge_icon_manager::render_icon( $settings['quote_icon'], [ 'aria-hidden' => 'true' ] ) );
            }        
        
            echo '<div class="header-info">';            
                if( !empty(Group_Control_Image_Size::get_attachment_image_html( $items, 'client_imagesize', 'client_image' )) ){
                    echo '<div class="photo">'.Group_Control_Image_Size::get_attachment_image_html( $items, 'client_imagesize', 'client_image' ).'</div>';
                }
                echo '<div class="name-and-designation">';
                    if(!empty($items['client_name'])){
                        echo '<h4 class="client-name">'.esc_html($items['client_name']).'</h4>';
                    }
                    if(!empty($items['client_designation'])){
                        echo '<div class="designation">'.esc_html($items['client_designation']).'</div>';
                    }
                echo '</div>';
            echo '</div>';       
        
            echo '<div class="desc">';
                echo esc_html($items['client_say']);
            echo '</div>';      
        
            if( !empty(Group_Control_Image_Size::get_attachment_image_html( $items, 'client_logo_imagesize', 'client_logo_image' )) ){
                echo '<div class="client-logo"><div class="photo">'.Group_Control_Image_Size::get_attachment_image_html( $items, 'client_logo_imagesize', 'client_logo_image' ).'</div></div>';
            }
        
        echo '</div>';
        
    }
    
    protected function team_style5($items){
        $settings   = $this->get_settings_for_display();
        echo '<div class="testimonial-box s5">';
            echo '<div class="feed-rating">';
                echo '<span class="star front"></span>';
                echo '<span class="star back" style="width: '.($items['feed_rating']['size']*20).'%"></span>';
            echo '</div>';
            echo '<div class="desc">';
                echo esc_html($items['client_say']);
            echo '</div>';
            echo '<div class="footer">';            
                if( !empty(Group_Control_Image_Size::get_attachment_image_html( $items, 'client_imagesize', 'client_image' )) ){
                    echo '<div class="photo">'.Group_Control_Image_Size::get_attachment_image_html( $items, 'client_imagesize', 'client_image' ).'</div>';
                }
                echo '<div class="name-and-designation">';
                    if(!empty($items['client_name'])){
                        echo '<h4 class="client-name">'.esc_html($items['client_name']).'</h4>';
                    }
                    if(!empty($items['client_designation'])){
                        echo '<div class="designation">'.esc_html($items['client_designation']).'</div>';
                    }
                echo '</div>';
            echo '</div>';
        echo '</div>';        
    }
    
}
Plugin::instance()->widgets_manager->register_widget_type( new folioedge_Testimonial_Widget );
