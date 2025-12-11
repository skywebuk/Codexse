<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class folioedge_Post_Widget extends Widget_Base {

    public function get_name() {
        return 'folioedge-post-widget';
    }

    public function get_title() {
        return __( 'Posts', 'folioedgecore' );
    }

    public function get_icon() {
        return "folioedge-icon eicon-posts-carousel";
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
    
    public function get_categories() {
        return [ 'folioedgecore' ];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Post Settings', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_CONTENT,
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
                    'default'       => 'no',
                ]
            );
        
            $this->add_control(
                'post_style',
                [
                    'label' => __( 'Box Style', 'folioedgecore' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'style1',
                    'options' => [
                        'style1'  => esc_html__( 'Style One', 'folioedgecore' ),
                        'style2' => esc_html__( 'Style Two', 'folioedgecore' ),
                        'style3' => esc_html__( 'Style Three', 'folioedgecore' ),
                        'style4' => esc_html__( 'Style Four', 'folioedgecore' ),
                        'style5' => esc_html__( 'Style Five', 'folioedgecore' ),
                        'style6' => esc_html__( 'Style Six', 'folioedgecore' ),
                    ],
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
                    'condition' => [
                        'slider_on!' => 'yes',
                    ]
                ]
            );
        
            $this->add_control(
                'post_categories',
                [
                    'label' => esc_html__( 'Categories', 'folioedgecore' ),
                    'type' => Controls_Manager::SELECT2,
                    'label_block' => true,
                    'multiple' => true,
                    'options' => folioedge_get_taxonomies('category'),
                ]
            );
            $this->add_control(
                'title_list',
                [
                    'label' => esc_html__( 'Title', 'folioedgecore' ),
                    'type' => Controls_Manager::SELECT2,
                    'multiple' => true,
                    'options' => folioedge_get_title('post'),
                    'separator'=>'before',
                ]
            );
            $this->add_control(
                'post_limit',
                [
                    'label' => __('Limit', 'folioedgecore'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 6,
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
        
            $this->add_group_control(
                Group_Control_Image_Size::get_type(),
                [
                    'name' => 'thumb_size', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
                    'default' => 'full',
                    'separator' => 'none',
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
                    'default' => 'yes',
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
        
        // Feature Style tab section
        $this->start_controls_section(
            'folioedge_posts_style_section',
            [
                'label' => __( 'Single Item', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->start_controls_tabs('posts_box_style_tab');
        $this->start_controls_tab( 'posts_box_normal',
			[
				'label' => __( 'Normal', 'folioedgecore' ),
			]
		);
        
        $this->add_responsive_control(
            'posts_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .post-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'posts_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .post-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'posts_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .post-box',
            ]
        );

        $this->add_responsive_control(
            'posts_text_align',
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
                    '{{WRAPPER}} .post-box' => 'text-align: {{VALUE}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'posts_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .post-box',
            ]
        );
        $this->add_responsive_control(
            'posts_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .post-box' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'posts_box_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .post-box',
            ]
        );

        
        $this->add_control(
			'posts_box_transform',
			[
				'label' => __( 'Transform', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'translateY(0)',
				'selectors' => [
					'{{WRAPPER}} .post-box' => 'transform: {{VALUE}}',
				],
			]
		);
        
		$this->add_control(
			'posts_box_transition',
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
					'{{WRAPPER}} .post-box' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
		$this->end_controls_tab();

             
        // Hover Style tab Start
        $this->start_controls_tab(
            'posts_box_hover',
            [
                'label' => __( 'Hover', 'folioedgecore' ),
            ]
        );
        
        
              
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'posts_hover_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .post-box:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'posts_border_hover',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .post-box:hover',
            ]
        );
        $this->add_responsive_control(
            'posts_hover_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .post-box:hover' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'posts_box_hover_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .post-box:hover',
            ]
        );
        $this->add_control(
			'posts_box_hover_transform',
			[
				'label' => __( 'Transform', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'translateY(0)',
				'selectors' => [
					'{{WRAPPER}} .post-box:hover' => 'transform: {{VALUE}}',
				],
			]
		);
        
        $this->end_controls_tab(); // Hover Style tab end        
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section(); // Feature Box section style end
        
    }
    protected function render() {
        $column = $button_icon = '';
        $settings = $this->get_settings_for_display();
        
        $custom_order_ck    = $this->get_settings_for_display('custom_order');
        $orderby            = $this->get_settings_for_display('orderby');
        $postorder          = $this->get_settings_for_display('postorder');
        
        // Query
        $args = array(
            'post_type'             => 'post',
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
        if( !empty($settings['post_categories']) ){
            $get_categories = $settings['post_categories'];
        }else{
            $get_categories = '';
        }        
        $post_cats = str_replace(' ', '', $get_categories);
        if (  !empty( $get_categories ) ) {
            if( is_array($post_cats) and count($post_cats) > 0 ){
                $field_name = is_numeric( $post_cats[0] ) ? 'term_id' : 'slug';
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'category',
                        'terms' => $post_cats,
                        'field' => $field_name,
                        'include_children' => false
                    )
                );
            }
        }
        $post = new \WP_Query( $args );
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
                    $column .= "col-md-6 col-sm-12";
                    break;
                case "3grid":
                    $column .= "col-lg-4 col-md-6";
                    break;
                default:
                    $column .= "col-lg-3 col-md-6";
            }
        }
                
        if($post->have_posts()){
            if( $settings['slider_on'] == 'yes' ) {                
                echo '<div '.$this->get_render_attribute_string( "wrapper_attributes" ).' >';
                    echo '<div class="swiper-wrapper">';
                        while ($post->have_posts()) {
                            $post->the_post();
                            echo '<div class="swiper-slide">';                  
                                if($settings['post_style'] == 'style1'){
                                    $this->blog_style_1($settings);
                                }elseif($settings['post_style'] == 'style2'){
                                    $this->blog_style_2($settings);
                                }elseif($settings['post_style'] == 'style3'){
                                    $this->blog_style_3($settings);
                                }elseif($settings['post_style'] == 'style4'){
                                    $this->blog_style_4($settings);
                                }elseif($settings['post_style'] == 'style5'){
                                    $this->blog_style_5($settings);
                                }elseif($settings['post_style'] == 'style6'){
                                    $this->blog_style_6($settings);
                                }
                            echo '</div>';
                        }
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
                    while ($post->have_posts()) {
                        $post->the_post();
                        echo '<div class="'.$column.'">';                        
                            if($settings['post_style'] == 'style1'){
                                $this->blog_style_1($settings);
                            }elseif($settings['post_style'] == 'style2'){
                                $this->blog_style_2($settings);
                            }elseif($settings['post_style'] == 'style3'){
                                $this->blog_style_3($settings);
                            }elseif($settings['post_style'] == 'style4'){
                                $this->blog_style_4($settings);
                            }elseif($settings['post_style'] == 'style5'){
                                $this->blog_style_5($settings);
                            }elseif($settings['post_style'] == 'style6'){
                                $this->blog_style_6($settings);
                            }
                        echo '</div>';  
                    }
                echo '</div>';                
            }
        }else {
            get_template_part( 'components/post-formats/post', 'none' );
        }

        wp_reset_postdata();
        
    }// End Rendar
    
    protected function blog_style_1($settings){
        echo '<div class="post-box post-addons-box no-shadow" >';
            echo '<figure class="post-media">';
                echo '<a href="'.get_the_permalink().'" >';
                    if(has_post_thumbnail() && get_the_post_thumbnail()){
                        the_post_thumbnail($settings['thumb_size_size']);
                    }else{
                        echo '<img src="'.Utils::get_placeholder_image_src().'" alt="'.esc_attr__('Placeholder','folioedgecore').'" />';
                    }
                echo '</a>';
            echo '</figure>';
            echo '<div class="content"> ';
                if(get_the_category()):
                    echo '<ul class="top-meta-list">';
                        echo '<li>';
                            echo '<svg class="svg-icon icon"><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-folder"></use></svg>';
                            echo '<a href="'.get_category_link(get_the_category()[0]->cat_ID).'">'.get_the_category()[0]->name.'</a>';
                        echo '</li>';
                    echo '</ul>';
                endif;
                echo '<h4 class="title"><a href="'. get_the_permalink() .'">'.get_the_title().'</a></h4>';
                echo '<a href="'.get_the_permalink().'" class="read-more-link">'.__('Read more','folioedgecore').'</a>';
            echo '</div>';
        echo '</div>';
    }
    
    protected function blog_style_2($settings){
        echo '<div class="post-box post-addons-box no-shadow box-2" >';
            echo '<figure class="post-media">';
                echo '<a href="'.get_the_permalink().'" >';
                    if(has_post_thumbnail() && get_the_post_thumbnail()){
                        the_post_thumbnail($settings['thumb_size_size']);
                    }else{
                        echo '<img src="'.Utils::get_placeholder_image_src().'" alt="'.__('Placeholder','folioedgecore').'" />';
                    }
                echo '</a>';
            echo '</figure>';
            echo '<div class="content"> ';  
                echo '<ul class="top-meta-list">';
                    if(folioedge_get_post_date()):
                        echo '<li class="date">';
                        echo '<svg class="svg-icon icon"><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-calander"></use></svg>';
                        echo folioedge_get_post_date('j F, Y');
                        echo '</li>';
                    endif;
                    if(get_the_author()):
                        echo '<li class="author">';
                        echo '<svg class="svg-icon icon"><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-profile"></use></svg>';
                        echo  get_the_author();
                        echo '</li>';        
                    endif;
                echo '</ul>';
                echo '<h4 class="title"><a href="'. get_the_permalink() .'">'.get_the_title().'</a></h4>';
            echo '</div>';
        echo '</div>';
    }
    
    
    protected function blog_style_3($settings){
        echo '<div class="post-box post-addons-box box-3 overlay" >';
            echo '<figure class="post-media">';
                echo '<a href="'.get_the_permalink().'" >';
                    if(has_post_thumbnail() && get_the_post_thumbnail()){
                        the_post_thumbnail($settings['thumb_size_size']);
                    }else{
                        echo '<img src="'.Utils::get_placeholder_image_src().'" alt="'.__('Placeholder','folioedgecore').'" />';
                    }
                echo '</a>';
            echo '</figure>';
            echo '<div class="content"> '; 
                if(folioedge_get_post_date()):
                    echo '<div class="date-info">';
                        echo folioedge_get_post_date('j F, Y');
                    echo '</div>';
                endif;
                if(get_the_category()):
                    echo '<ul class="top-meta-list">';
                        echo '<li>';
                        echo '<svg class="svg-icon icon"><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-folder"></use></svg>';
                        echo '<a href="'.get_category_link(get_the_category()[0]->cat_ID).'">'.get_the_category()[0]->name.'</a>';
                        echo '</li>';
                    echo '</ul>';
                endif;
                echo '<h4 class="title"><a href="'. get_the_permalink() .'">'.get_the_title().'</a></h4>';
            echo '</div>';
        echo '</div>';
    }

    protected function blog_style_4($settings){
        echo '<div class="post-box post-addons-box box-4" >';
            echo '<figure class="post-media">';
                echo '<a href="'.get_the_permalink().'" >';
                    if(has_post_thumbnail() && get_the_post_thumbnail()){
                        the_post_thumbnail($settings['thumb_size_size']);
                    }else{
                        echo '<img src="'.Utils::get_placeholder_image_src().'" alt="'.__('Placeholder','folioedgecore').'" />';
                    }
                echo '</a>';
            echo '</figure>';
            echo '<div class="content"> ';     
                if(folioedge_get_post_date()):
                    echo '<ul class="top-meta-list">';
                        echo '<li class="date">';
                            echo '<svg class="svg-icon icon"><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-calander"></use></svg>';
                            echo folioedge_get_post_date('j F, Y');
                        echo '</li>';
                    echo '</ul>';
                endif;
                echo '<h4 class="title"><a href="'. get_the_permalink() .'">'.get_the_title().'</a></h4>';
                echo '<a href="'.get_the_permalink().'" class="read-more-link">'.__('Read more','folioedgecore').'</a>';
            echo '</div>';
        echo '</div>';
    }

    protected function blog_style_5($settings){
        echo '<div class="post-box post-addons-box box-5" >';
            echo '<figure class="post-media">';
                echo '<a href="'.get_the_permalink().'" >';
                    if(has_post_thumbnail() && get_the_post_thumbnail()){
                        the_post_thumbnail($settings['thumb_size_size']);
                    }else{
                        echo '<img src="'.Utils::get_placeholder_image_src().'" alt="'.__('Placeholder','folioedgecore').'" />';
                    }
                echo '</a>';
            echo '</figure>';
            echo '<div class="content"> ';  
                echo '<ul class="top-meta-list">';
                    if(folioedge_get_post_date()):
                        echo '<li class="date">';
                        echo '<svg class="svg-icon icon"><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-calander"></use></svg>';
                        echo folioedge_get_post_date('j F, Y');
                        echo '</li>';
                    endif;
                    if(get_the_author()):
                        echo '<li class="author">';
                        echo '<svg class="svg-icon icon"><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-profile"></use></svg>';
                        echo  get_the_author();
                        echo '</li>';        
                    endif;
                echo '</ul>';
                echo '<h4 class="title"><a href="'. get_the_permalink() .'">'.get_the_title().'</a></h4>';
                echo '<a href="'.get_the_permalink().'" class="read-more-button">'.__('Read more','folioedgecore').'</a>';
            echo '</div>';
        echo '</div>';
    }

    protected function blog_style_6($settings){
        echo '<div class="post-box post-addons-box box-6" >';
            echo '<figure class="post-media">';
                echo '<a href="'.get_the_permalink().'" >';
                    if(has_post_thumbnail() && get_the_post_thumbnail()){
                        the_post_thumbnail($settings['thumb_size_size']);
                    }else{
                        echo '<img src="'.Utils::get_placeholder_image_src().'" alt="'.__('Placeholder','folioedgecore').'" />';
                    }
                echo '</a>';
            echo '</figure>';
            echo '<div class="content"> ';  
                echo '<h4 class="title"><a href="'. get_the_permalink() .'">'.get_the_title().'</a></h4>';
                echo '<div class="footer_meta_list">';        
                    echo '<ul>';        
                        if(get_the_category()):
                            echo '<li class="category">';
                                echo '<a href="'.get_category_link(get_the_category()[0]->cat_ID).'">'.get_the_category()[0]->name.'</a>';
                            echo '</li>';
                        endif;
                        if(folioedge_get_post_date()):
                            echo '<li class="date">';
                            echo folioedge_get_post_date('j F, Y');
                            echo '</li>';
                        endif;
                        echo '<li class="link-arrow">';
                            echo '<a href="'.get_the_permalink().'"><i class="fal fa-arrow-right"></i></a>';
                        echo '</li>';
                    echo '</ul>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }
    
}// End Class


Plugin::instance()->widgets_manager->register_widget_type( new folioedge_Post_Widget );