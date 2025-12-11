<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * folioedgecore button widget.
 *
 * folioedgecore widget that displays a button with the ability to control every
 * aspect of the button design.
 *
 * @since 1.0.0
 */
class folioedge_Arrow_Button extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * Retrieve button widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
    public function get_name() {
        return 'folioedge_button';
    }

	/**
	 * Get widget title.
	 *
	 * Retrieve button widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Button', 'folioedgecore' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve button widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'folioedge-icon eicon-button';
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
            'lity',
            'addons-active',
        ];
    }

    public function get_style_depends() {
        return [
            'lity',
        ];
    }
    

	/**
	 * Register button widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_button',
			[
				'label' => __( 'Button', 'folioedgecore' ),
			]
		);

		$this->add_control(
			'button_label',
			[
				'label' => __( 'Label', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'text',
			[
				'label' => __( 'Text', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'Hello World!', 'folioedgecore' ),
				'placeholder' => __( 'type your button text...', 'folioedgecore' ),
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'folioedgecore' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'folioedgecore' ),
				'default' => [
					'url' => '#',
				],
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
                    'none' =>[
                        'title' =>__('None','folioedgecore'),
                        'icon' =>'eicon-warning',
                    ],
                ],
                'default' => 'none',
            ]
        );

        $this->add_control(
            'icon_img',
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
                'name' => 'icon_img_size',
                'default' => 'large',
                'separator' => 'none',
                'condition' => [
                    'icon_type' => 'img',
                ]
            ]
        );

        $this->add_control(
            'font_icon',
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
			'view',
			[
				'label' => __( 'View', 'folioedgecore' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->add_control(
			'button_css_id',
			[
				'label' => __( 'Button ID', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => '',
				'title' => __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'folioedgecore' ),
				'label_block' => false,
				'description' => __( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'folioedgecore' ),
				'separator' => 'before',

			]
		);

        $this->add_control(
			'lightbox_enabel',
			[
				'label' => __( 'Lightbox Enable', 'folioedgecore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'folioedgecore' ),
				'label_off' => __( 'Hide', 'folioedgecore' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Button', 'folioedgecore' ),
				'tab' => Controls_Manager::TAB_STYLE,
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
			'button_text_color',
			[
				'label' => __( 'Text Color', 'folioedgecore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .primary_button' => 'color: {{VALUE}};',
				],
			]
		);
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_color',
				'label' => __( 'Background', 'folioedgecore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .primary_button',
			]
		);
        
        $this->add_responsive_control(
            'button_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .primary_button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );   
        
        $this->add_responsive_control(
            'button_width',
            [
                'label' => __( 'Width', 'folioedgecore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
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
                'selectors' => [
                    '{{WRAPPER}}, {{WRAPPER}} .primary_button' => 'width: {{SIZE}}{{UNIT}};'
                ],
            ]
        );
        $this->add_responsive_control(
            'button_height',
            [
                'label' => __( 'Height', 'folioedgecore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
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
                'selectors' => [
                    '{{WRAPPER}} .primary_button' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border',
				'selector' => '{{WRAPPER}} .primary_button',
			]
		);

        $this->add_control(
			'button_radius',
			[
				'label' => __( 'Border Radius', 'folioedgecore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],          
				'selectors' => [
					'{{WRAPPER}} .primary_button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .primary_button',
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
			'button_hover_color',
			[
				'label' => __( 'Text Color', 'folioedgecore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .primary_button:hover' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'hover_background',
				'label' => __( 'Hover Background', 'folioedgecore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .primary_button:hover',
			]
		);
        
		$this->add_control(
			'button_hover_border_color',
			[
				'label' => __( 'Border Color', 'folioedgecore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .primary_button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_hover_box_shadow',
				'selector' => '{{WRAPPER}} .primary_button:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
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
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .primary_button' => 'text-align: {{VALUE}};',
                ],
                'separator' =>'before',
            ]
        );

		$this->end_controls_section();
        
        
        
        // Text Box Style tab section
        $this->start_controls_section(
            'text_style_section',
            [
                'label' => __( 'Text Style', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'text!' => ''
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_text_typography',
                'selector' => '{{WRAPPER}} .primary_button .button-text .button-title',
            ]
        );     
        
        $this->add_control(
			'button_title_color',
			[
				'label' => __( 'Color', 'folioedgecore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .primary_button .button-text .button-title' => 'color: {{VALUE}};',
				],
			]
		);    
        $this->add_control(
			'button_hover_title_color',
			[
				'label' => __( 'Hover Color', 'folioedgecore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .primary_button:hover .button-text .button-title' => 'color: {{VALUE}};',
				],
			]
		);
        $this->add_responsive_control(
            'button_text_margin',
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
                    '{{WRAPPER}} .primary_button .button-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'button_text_padding',
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
                    '{{WRAPPER}} .primary_button .button-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );   
        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_text_border',
				'label' => __( 'Border', 'folioedgecore' ),
				'selector' => '{{WRAPPER}} .primary_button .button-text',
			]
		);
        $this->end_controls_section();
        
        
        
        
        // Text Box Style tab section
        $this->start_controls_section(
            'label_style_section',
            [
                'label' => __( 'Label Style', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'button_label!' => ''
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_label_typography',
                'selector' => '{{WRAPPER}} .primary_button .button-text .top_label',
            ]
        );
        
        $this->add_control(
			'button_label_color',
			[
				'label' => __( 'Label Color', 'folioedgecore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .primary_button .button-text .top_label' => 'color: {{VALUE}};',
				],
			]
		);    
        $this->add_control(
			'button_hover_label_color',
			[
				'label' => __( 'Hover Label Color', 'folioedgecore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .primary_button:hover .button-text .top_label' => 'color: {{VALUE}};',
				],
			]
		);
        
        $this->add_responsive_control(
            'button_label_margin',
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
                    '{{WRAPPER}} .primary_button .button-text .top_label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'button_label_padding',
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
                    '{{WRAPPER}} .primary_button .button-text .top_label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );   
        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_label_border',
				'label' => __( 'Border', 'folioedgecore' ),
				'selector' => '{{WRAPPER}} .primary_button .button-text .top_label',
			]
		);
        $this->add_responsive_control(
			'button_label_display',
			[
				'label' => __( 'Display', 'folioedgecore' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'block',
				'options' => [
					'block'  => __( 'Block', 'folioedgecore' ),
					'inline-block' => __( 'Inline Block', 'folioedgecore' ),
					'flex' => __( 'Flex', 'folioedgecore' ),
					'inline-flex' => __( 'Inline Flex', 'folioedgecore' ),
				],
                'selectors' => [
                    '{{WRAPPER}} .primary_button .button-text .top_label' => 'display: {{VALUE}};',
                ],
			]
		);

        $this->end_controls_section();
        
        // Feature Style tab section
        $this->start_controls_section(
            'box_icon_section',
            [
                'label' => __( 'Icon Style', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'icon_type!' => 'none'
                ]
            ]
        );     
        $this->add_control(
			'button_icon_color',
			[
				'label' => __( 'Icon Color', 'folioedgecore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .primary_button .x-icon' => 'color: {{VALUE}};',
				],
			]
		);    
        $this->add_control(
			'button_hover_icon_color',
			[
				'label' => __( 'Hover Icon Color', 'folioedgecore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .primary_button:hover .x-icon' => 'color: {{VALUE}};',
				],
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
					'{{WRAPPER}} .primary_button .x-icon' => 'width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .primary_button .x-icon' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .primary_button .x-icon' => 'line-height: {{SIZE}}{{UNIT}};',
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
                'default' => [
                    'size' => 16,
                ],
                'selectors' => [
                    '{{WRAPPER}} .primary_button .x-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'icon_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '0',
                    'left' => '0',
                    'isLinked' => false
                ],
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .primary_button .x-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'icon_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '0',
                    'left' => '0',
                    'isLinked' => false
                ],
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .primary_button .x-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
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
                'default' => 'flex-direction: row-reverse;',
                'selectors' => [
                    '{{WRAPPER}} .primary_button' => 'display: inline-flex; {{VALUE}}',
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
                    'justify-content: space-between; align-items: space-between;' => [
                        'title' => __( 'Space Between', 'folioedgecore' ),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .primary_button' => '{{VALUE}}',
                ],
                'default' => 'justify-content: center; align-items: center;',
                'separator' =>'before',
            ]
        );
        $this->end_controls_section();
        
	}

	/**
	 * Render button widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();        
        
		$this->add_render_attribute( 'button_attr', 'class', 'primary_button' );
		
        if( $settings['lightbox_enabel'] == 'yes' ){
		  $this->add_render_attribute( 'button_attr', 'data-lity', 'true' );            
        }
        if( !empty($settings['button_css_id']) ){
            $this->add_render_attribute( 'button_attr', 'id', 'primary_button-'.$settings['button_css_id'] );
        }
        // Link Generate
        if ( !empty( $settings['link']['url'] ) ) {
            $this->add_render_attribute( 'button_attr', 'href', esc_url($settings['link']['url']) );            
            if ( $settings['link']['is_external'] ) {
                $this->add_render_attribute( 'button_attr', 'target', '_blank' );
            }
            if ( !empty( $settings['link']['nofollow'] ) ) {
                $this->add_render_attribute( 'button_attr', 'rel', 'nofollow' );
            }
        }
        $output = $output_content = '';
        if( $settings['icon_type'] == 'img' and !empty(Group_Control_Image_Size::get_attachment_image_html( $settings, 'icon_img_size', 'icon_img' )) ){
            $iconImage = Group_Control_Image_Size::get_attachment_image_html( $settings, 'icon_img_size', 'icon_img' );  
            $output_content .= '<span class="x-icon">'.$iconImage.'</span>';
        }elseif( $settings['icon_type'] == 'icon' && !empty($settings['font_icon']['value']) ){
            $output_content .= sprintf( '<span class="x-icon" >%1$s</span>', folioedge_icon_manager::render_icon( $settings['font_icon'], [ 'aria-hidden' => 'true' ] ) );
        }
        if( !empty($settings['text']) ){
            $output_content .= '<span class="button-text">'.( (!empty($settings['button_label'])) ? '<span class="top_label">'.esc_html($settings['button_label']).'</span>' : '' ).'<span class="button-title" >'.esc_html($settings['text']).'</span></span>';
        }
        if( !empty($output_content) ){
            $output .= '<a '.$this->get_render_attribute_string( 'button_attr' ).'>';        
            $output .= $output_content;        
            $output .= '</a>';
        }
        echo $output;
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new folioedge_Arrow_Button );