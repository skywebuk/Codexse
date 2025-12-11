<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor accordion widget.
 *
 * Elementor widget that displays a collapsible display of content in an
 * accordion style, showing only one item at a time.
 *
 * @since 1.0.0
 */
class folioedgecore_Widget_Accordion extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve accordion widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'folioedge-accordion';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve accordion widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Accordion', 'folioedgecore' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve accordion widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'folioedge-icon eicon-accordion';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'accordion', 'tabs', 'toggle' ];
	}
    
	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the button widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'folioedgecore' ];
	}
    
    
    public function get_script_depends() {
        return [
            'addons-active',
        ];
    }
    
	/**
	 * Register accordion widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Accordion', 'folioedgecore' ),
			]
		);

		$repeater = new Repeater();
        
		$repeater->add_control(
			'tab_title',
			[
				'label' => __( 'Title & Description', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Accordion Title', 'folioedgecore' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'tab_content',
			[
				'label' => __( 'Content', 'folioedgecore' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => __( 'Accordion Content', 'folioedgecore' ),
				'opened_label' => false,
			]
		);
        
        $repeater->add_control(
			'active_item',
			[
				'label' => __( 'Active', 'folioedgecore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'folioedgecore' ),
				'label_off' => __( 'Hide', 'folioedgecore' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			'tabs',
			[
				'label' => __( 'Accordion Items', 'folioedgecore' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'tab_title' => __( 'Accordion #1', 'folioedgecore' ),
						'tab_content' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'folioedgecore' ),
					],
					[
						'tab_title' => __( 'Accordion #2', 'folioedgecore' ),
						'tab_content' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'folioedgecore' ),
					],
				],
				'title_field' => '{{{ tab_title }}}',
			]
		);
        
		$this->add_control(
			'view',
			[
				'label' => __( 'View', 'folioedgecore' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->add_control(
			'title_html_tag',
			[
				'label' => __( 'Title HTML Tag', 'folioedgecore' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
				],
				'default' => 'div',
				'separator' => 'before',
			]
		);
        
        $this->add_control(
            'closed_icon_type',
            [
                'label' => __('Opened Icon','folioedgecore'),
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
				'separator' => 'before',
            ]
        );

        $this->add_control(
            'closed_icon_img',
            [
                'label' => __('Image','folioedgecore'),
                'type'=>Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'closed_icon_type' => 'img',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'closed_icon_img_size',
                'default' => 'large',
                'separator' => 'none',
                'condition' => [
                    'closed_icon_type' => 'img',
                ]
            ]
        );

        $this->add_control(
            'closed_font_icon',
            [
                'label'       => __( 'Icon', 'folioedgecore-addons' ),
                'type'        => Controls_Manager::ICONS,
                'label_block' => true,
                'condition' => [
                    'closed_icon_type' => 'icon',
                ]
            ]
        );
        
        
        $this->add_control(
            'opened_icon_type',
            [
                'label' => __('Closed Icon','folioedgecore'),
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
				'separator' => 'before',
            ]
        );

        $this->add_control(
            'opened_icon_img',
            [
                'label' => __('Image','folioedgecore'),
                'type'=>Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'opened_icon_type' => 'img',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'opened_icon_img_size',
                'default' => 'large',
                'separator' => 'none',
                'condition' => [
                    'opened_icon_type' => 'img',
                ]
            ]
        );

        $this->add_control(
            'opened_font_icon',
            [
                'label'       => __( 'Icon', 'folioedgecore-addons' ),
                'type'        => Controls_Manager::ICONS,
                'label_block' => true,
                'condition' => [
                    'opened_icon_type' => 'icon',
                ]
            ]
        );

		$this->end_controls_section();
        
        
        
        // Accordion Style tab section
        $this->start_controls_section(
            'folioedge_accordion_style_section',
            [
                'label' => __( 'Single Card', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->start_controls_tabs('accordion_box_style_tab');
        $this->start_controls_tab( 'accordion_box_normal',
			[
				'label' => __( 'Normal', 'folioedgecore' ),
			]
		);
        
        $this->add_responsive_control(
            'accordion_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .accoridon-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'accordion_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .accoridon-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'accordion_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .accoridon-item',
            ]
        );

        $this->add_responsive_control(
            'accordion_text_align',
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
                    '{{WRAPPER}} .accoridon-item' => 'text-align: {{VALUE}};',
                ],
                'default' => 'left',
                'separator' =>'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'accordion_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .accoridon-item',
            ]
        );
        $this->add_responsive_control(
            'accordion_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .accoridon-item' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'accordion_box_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .accoridon-item',
            ]
        );

        
        $this->add_control(
			'accordion_box_transform',
			[
				'label' => __( 'Transform', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'translateY(0)',
				'selectors' => [
					'{{WRAPPER}} .accoridon-item' => 'transform: {{VALUE}}',
				],
			]
		);
        
		$this->add_control(
			'accordion_box_transition',
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
					'{{WRAPPER}} .accoridon-item' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
		$this->end_controls_tab();

             
        // Hover Style tab Start
        $this->start_controls_tab(
            'accordion_box_hover',
            [
                'label' => __( 'Active', 'folioedgecore' ),
            ]
        );
                
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'accordion_hover_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .accoridon-item.active',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'accordion_border_hover',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .accoridon-item.active',
            ]
        );
        $this->add_responsive_control(
            'accordion_hover_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .accoridon-item.active' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'accordion_box_hover_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .accoridon-item.active',
            ]
        );
        $this->add_control(
			'accordion_box_hover_transform',
			[
				'label' => __( 'Transform', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'translateY(0)',
				'selectors' => [
					'{{WRAPPER}} .accoridon-item.active' => 'transform: {{VALUE}}',
				],
			]
		);
        
        $this->end_controls_tab(); // Hover Style tab end        
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section(); // Accordion Box section style end
        
        // Accordion Style tab section
        $this->start_controls_section(
            'box_icon_section',
            [
                'label' => __( 'Icon', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} .accoridon-item .icon' => 'width: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .accoridon-item .icon' => 'height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .accoridon-item .icon' => 'line-height: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .accoridon-item .icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'icon_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .accoridon-item .icon' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'icon_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .accoridon-item .icon',
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
                    '{{WRAPPER}} .accoridon-item .title' => 'display: flex; align-items: center; {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'icon_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .accoridon-item .icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .accoridon-item .icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'icon_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .accoridon-item .icon',
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
                    '{{WRAPPER}} .accoridon-item .icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'icon_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .accoridon-item .icon',
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
					'{{WRAPPER}} .accoridon-item .icon' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
        $this->end_controls_tab(); // Hover Style tab end
        $this->start_controls_tab( 'box_icon_hover',
			[
				'label' => __( 'Active', 'folioedgecore' ),
			]
		);        
        $this->add_control(
            'hover_icon_color',
            [
                'label' => __( 'Hover Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                
                'selectors' => [
                    '{{WRAPPER}} .accoridon-item.active .icon' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'hover_icon_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .accoridon-item.active .icon',
            ]
        );               
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'hover_icon_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .accoridon-item.active .icon',
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
                    '{{WRAPPER}} .accoridon-item.active .icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'hover_icon_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .accoridon-item.active .icon',
            ]
        );        
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section();
        
        // Accordion Style tab section
        $this->start_controls_section(
            'box_title_section',
            [
                'label' => __( 'Title', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->start_controls_tabs('box_title_style_tab');
        
        $this->start_controls_tab( 'box_title_normal',
			[
				'label' => __( 'Normal', 'folioedgecore' ),
			]
		);        
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'accordion_title_typography',
                'selector' => '{{WRAPPER}} .accoridon-item .title',
            ]
        );
        $this->add_control(
            'title_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .accoridon-item .title' => 'color: {{VALUE}};',
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
                    '{{WRAPPER}} .accoridon-item .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'title_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .accoridon-item .title',
            ]
        );
        
        $this->add_responsive_control(
            'title_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .accoridon-item .title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
                
		$this->add_control(
			'accordion_title_transition',
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
					'{{WRAPPER}} .accoridon-item .title' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
        
        
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'accordion_title_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .accoridon-item .title',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'accordion_title_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .accoridon-item .title',
            ]
        );
        
        $this->add_responsive_control(
            'accordion_title_radius',
            [
                'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .accoridon-item .title' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );

        
        $this->end_controls_tab(); // Hover Style tab end
         
        $this->start_controls_tab( 'box_title_hover',
			[
				'label' => __( 'Hover', 'folioedgecore' ),
			]
		);        
        
        $this->add_control(
            'title_hover_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .accoridon-item .title:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'accordion_title_hover_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .accoridon-item .title:hover',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'accordion_title_hover_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .accoridon-item .title:hover',
            ]
        );
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section();
         
        
        // Accordion Style tab section
        $this->start_controls_section(
            'box_desc_section',
            [
                'label' => __( 'Description', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'accordion_desc_typography',
                    'selector' => '{{WRAPPER}} .accoridon-item .desc',
                ]
            );
            $this->add_control(
                'accordion_desc_color',
                [
                    'label' => __( 'Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .accoridon-item .desc' => 'color: {{VALUE}};',
                    ],
                ]
            );
            $this->add_responsive_control(
                'accordion_desc_margin',
                [
                    'label' => __( 'Margin', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .accoridon-item .desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' =>'before',
                ]
            );

            $this->add_responsive_control(
                'accordion_desc_padding',
                [
                    'label' => __( 'Padding', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .accoridon-item .desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' =>'before',
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'accordion_desc_background',
                    'label' => __( 'Background', 'folioedgecore' ),
                    'types' => [ 'classic', 'gradient' ],
                    'selector' => '{{WRAPPER}} .accoridon-item .desc',
                ]
            );
            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'accordion_desc_border',
                    'label' => __( 'Border', 'folioedgecore' ),
                    'selector' => '{{WRAPPER}} .accoridon-item .desc',
                ]
            );
            $this->add_responsive_control(
                'accordion_desc_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'selectors' => [
                        '{{WRAPPER}} .accoridon-item .desc' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                    ],
                ]
            );
            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'accordion_desc_box_shadow',
                    'label' => __( 'Box Shadow', 'folioedgecore' ),
                    'selector' => '{{WRAPPER}} .accoridon-item .desc',
                ]
            );
        $this->end_controls_section();        
	}

	/**
	 * Render accordion widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$id_int = substr( $this->get_id_int(), 0, 3 );
		?>		
		<div class="accordion-list">
            <?php
                foreach ( $settings['tabs'] as $index => $item ) :
        
                $tab_count = $index + 1;

                $tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $index );

                $tab_desc_setting_key = $this->get_repeater_setting_key( 'tab_content', 'tabs', $index );

                $this->add_render_attribute( $tab_title_setting_key, [
                    'id' => 'tab-title-' . $id_int . $tab_count,
                    'class' => [ 'title', $item['active_item'] == 'yes' ? 'active' : '' ],
                ] );

                $this->add_render_attribute( $tab_desc_setting_key, [
                    'id' => 'tab-content-' . $id_int . $tab_count,
                    'class' => [ 'desc' ],
                ] );

                $this->add_inline_editing_attributes( $tab_desc_setting_key, 'advanced' );
            ?>
            <div class="accoridon-item <?php echo ($item['active_item'] == 'yes' ? 'active' : ''); ?>">
                <<?php echo $settings['title_html_tag']; ?> <?php echo $this->get_render_attribute_string( $tab_title_setting_key ); ?>>
                    <span class="title-value"><?php echo wp_kses_post($item['tab_title']); ?></span>
                   <?php 
                        if( $settings['closed_icon_type'] == 'img' and !empty(Group_Control_Image_Size::get_attachment_image_html( $settings, 'closed_icon_img_size', 'closed_icon_img' )) ){
                            $closed_iconImage = Group_Control_Image_Size::get_attachment_image_html( $settings, 'closed_icon_img_size', 'closed_icon_img' );  
                            echo '<span class="icon icon-closed">'.$closed_iconImage.'</span>';
                        }elseif( $settings['closed_icon_type'] == 'icon' && !empty($settings['closed_font_icon']['value']) ){
                            echo sprintf( '<span class="icon icon-closed" >%1$s</span>', folioedge_icon_manager::render_icon( $settings['closed_font_icon'], [ 'aria-hidden' => 'true' ] ) );
                        }
                    ?>
                   <?php 
                        if( $settings['opened_icon_type'] == 'img' and !empty(Group_Control_Image_Size::get_attachment_image_html( $settings, 'opened_icon_img_size', 'opened_icon_img' )) ){
                            $opened_iconImage = Group_Control_Image_Size::get_attachment_image_html( $settings, 'opened_icon_img_size', 'opened_icon_img' );  
                            echo '<span class="icon icon-opened">'.$opened_iconImage.'</span>';
                        }elseif( $settings['opened_icon_type'] == 'icon' && !empty($settings['opened_font_icon']['value']) ){
                            echo sprintf( '<span class="icon icon-opened" >%1$s</span>', folioedge_icon_manager::render_icon( $settings['opened_font_icon'], [ 'aria-hidden' => 'true' ] ) );
                        }
                    ?>
                </<?php echo $settings['title_html_tag']; ?>>
                <div <?php echo $this->get_render_attribute_string( $tab_desc_setting_key ); ?>>
                    <?php echo wp_kses_post($this->parse_text_editor( $item['tab_content'])); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>		
		<?php
	}
}

Plugin::instance()->widgets_manager->register( new folioedgecore_Widget_Accordion() );