<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * folioedgecore shortcode-form widget.
 *
 * folioedgecore widget that displays a shortcode-form with the ability to control every
 * aspect of the shortcode-form design.
 *
 * @since 1.0.0
 */
class folioedge_Subscribe_Form extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * Retrieve shortcode-form widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
    public function get_name() {
        return 'folioedge_Subscribe_Form';
    }

	/**
	 * Get widget title.
	 *
	 * Retrieve shortcode-form widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Form Shortcode', 'folioedgecore' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve shortcode-form widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'folioedge-icon eicon-shortcode';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the shortcode-form widget belongs to.
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

	/**
	 * Register shortcode-form widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		
        
		$this->start_controls_section(
			'subscribe_section_start',
			[
				'label' => __( 'Subscribe Form', 'folioedgecore' ),
			]
		);
        
        $this->add_control(
			'shortcode_box',
			[
				'label' => __( 'Shortcode', 'folioedgecore' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'description' => __( 'Please enter MailChimp form Shortcode.','folioedge' ),
				'default' => '[mc4wp_form id="665"]',
				'placeholder' => '[mc4wp_form id="665"]',
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
                'icon_image',
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
                    'name' => 'icon_imagesize',
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
                'ex_icon_align',
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
                'ex_icon_color',
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
                'ex_icon_size',
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
                'ex_icon_opacity',
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
                        '{{WRAPPER}} .x-icon' => 'opacity: {{SIZE}}',

                    ],
                ]
            );    
            $this->add_responsive_control(
                'ex_icon_width',
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
                'ex_icon_height',
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
                'ex_icon_z_index',
                [
                    'label' => __( 'Z Index', 'folioedgecore' ),
                    'type' => Controls_Manager::NUMBER,
                    'min' => -100,
                    'max' => 100,
                    'step' => 1,
                    'selectors' => [
                        '{{WRAPPER}} .x-icon' => 'z-index: {{SIZE}};',
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
                        '{{WRAPPER}} .x-icon' => 'position: {{VALUE}};',
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
                        'body:not(.rtl) {{WRAPPER}} .x-icon' => 'left: {{SIZE}}{{UNIT}}',
                        'body.rtl {{WRAPPER}} .x-icon' => 'right: {{SIZE}}{{UNIT}}',
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
                        'body:not(.rtl) {{WRAPPER}} .x-icon' => 'right: {{SIZE}}{{UNIT}}',
                        'body.rtl {{WRAPPER}} .x-icon' => 'left: {{SIZE}}{{UNIT}}',
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
                        '{{WRAPPER}} .x-icon' => 'top: {{SIZE}}{{UNIT}}',
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
                        '{{WRAPPER}} .x-icon' => 'bottom: {{SIZE}}{{UNIT}}',
                    ],
                    'condition' => [
                        '_tst_offset_orientation_v' => 'end',
                        '_tst_position!' => '',
                    ],
                ]
            );
        $this->end_controls_section();
        // Text Box Style tab section
        $this->start_controls_section(
            'top_title_section',
            [
                'label' => __( 'Lavel', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'top_title_typography',
                'selector' => '{{WRAPPER}} label',
            ]
        );
        $this->add_control(
            'top_title_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} label' => 'color: {{VALUE}};',
                ],
            ]
        );                
        $this->add_responsive_control(
            'top_title_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'top_title_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );   
        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'top_title_border',
				'label' => __( 'Border', 'folioedgecore' ),
				'selector' => '{{WRAPPER}} label',
			]
		);
        $this->add_responsive_control(
            'custom_top_title_css',
            [
                'label' => __( 'Lavel Custom CSS', 'folioedgecore' ),
                'type' => Controls_Manager::CODE,
				'rows' => 20,
                'language' => 'css',
                'selectors' => [
                    '{{WRAPPER}} label' => '{{VALUE}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->end_controls_section();
        
        $this->start_controls_section(
			'input_style_section',
			[
				'label' => __( 'Input Field', 'folioedgecore' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'input_typography',
				'selector' => '{{WRAPPER}} select, {{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="url"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="month"], {{WRAPPER}} input[type="week"], {{WRAPPER}} input[type="time"], {{WRAPPER}} input[type="datetime"], {{WRAPPER}} input[type="datetime-local"], {{WRAPPER}} input[type="color"], {{WRAPPER}} textarea',
			]
		);
		
        $this->add_responsive_control(
            'input_box_height',
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
                    '{{WRAPPER}} select, {{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="url"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="month"], {{WRAPPER}} input[type="week"], {{WRAPPER}} input[type="time"], {{WRAPPER}} input[type="datetime"], {{WRAPPER}} input[type="datetime-local"], {{WRAPPER}} input[type="color"], {{WRAPPER}} textarea' => 'min-height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'input_box_width',
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
                    '{{WRAPPER}} select, {{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="url"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="month"], {{WRAPPER}} input[type="week"], {{WRAPPER}} input[type="time"], {{WRAPPER}} input[type="datetime"], {{WRAPPER}} input[type="datetime-local"], {{WRAPPER}} input[type="color"], {{WRAPPER}} textarea' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
		$this->start_controls_tabs( 'tabs_input_style' );

		$this->start_controls_tab(
			'tab_input_normal',
			[
				'label' => __( 'Normal', 'folioedgecore' ),
			]
		);

		$this->add_control(
			'input_text_color',
			[
				'label' => __( 'Text Color', 'folioedgecore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} select, {{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="url"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="month"], {{WRAPPER}} input[type="week"], {{WRAPPER}} input[type="time"], {{WRAPPER}} input[type="datetime"], {{WRAPPER}} input[type="datetime-local"], {{WRAPPER}} input[type="color"], {{WRAPPER}} textarea, {{WRAPPER}} ::placeholder' => 'color: {{VALUE}};',
				],
			]
		);
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'input_background_color',
				'label' => __( 'Background', 'folioedgecore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} select, {{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="url"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="month"], {{WRAPPER}} input[type="week"], {{WRAPPER}} input[type="time"], {{WRAPPER}} input[type="datetime"], {{WRAPPER}} input[type="datetime-local"], {{WRAPPER}} input[type="color"], {{WRAPPER}} textarea'
			]
		);
        
        $this->add_responsive_control(
            'input_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} select, {{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="url"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="month"], {{WRAPPER}} input[type="week"], {{WRAPPER}} input[type="time"], {{WRAPPER}} input[type="datetime"], {{WRAPPER}} input[type="datetime-local"], {{WRAPPER}} input[type="color"], {{WRAPPER}} textarea' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'input_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} select, {{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="url"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="month"], {{WRAPPER}} input[type="week"], {{WRAPPER}} input[type="time"], {{WRAPPER}} input[type="datetime"], {{WRAPPER}} input[type="datetime-local"], {{WRAPPER}} input[type="color"], {{WRAPPER}} textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );   
        
        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'input_border',
				'selector' => '{{WRAPPER}} select, {{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="url"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="month"], {{WRAPPER}} input[type="week"], {{WRAPPER}} input[type="time"], {{WRAPPER}} input[type="datetime"], {{WRAPPER}} input[type="datetime-local"], {{WRAPPER}} input[type="color"], {{WRAPPER}} textarea',
			]
		);

        $this->add_responsive_control(
			'input_radius',
			[
				'label' => __( 'Border Radius', 'folioedgecore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} select, {{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="url"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="month"], {{WRAPPER}} input[type="week"], {{WRAPPER}} input[type="time"], {{WRAPPER}} input[type="datetime"], {{WRAPPER}} input[type="datetime-local"], {{WRAPPER}} input[type="color"], {{WRAPPER}} textarea' => 'border-radius : {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
			]
		);
        
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'input_box_shadow',
				'selector' => '{{WRAPPER}} select, {{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="url"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="month"], {{WRAPPER}} input[type="week"], {{WRAPPER}} input[type="time"], {{WRAPPER}} input[type="datetime"], {{WRAPPER}} input[type="datetime-local"], {{WRAPPER}} input[type="color"], {{WRAPPER}} textarea',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_input_focus',
			[
				'label' => __( 'Focus', 'folioedgecore' ),
			]
		);

		$this->add_control(
			'input_focus_color',
			[
				'label' => __( 'Text Color', 'folioedgecore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} select:focus, {{WRAPPER}} input[type="text"]:focus, {{WRAPPER}} input[type="email"]:focus, {{WRAPPER}} input[type="url"]:focus, {{WRAPPER}} input[type="password"]:focus, {{WRAPPER}} input[type="search"]:focus, {{WRAPPER}} input[type="number"]:focus, {{WRAPPER}} input[type="tel"]:focus, {{WRAPPER}} input[type="date"]:focus, {{WRAPPER}} input[type="month"]:focus, {{WRAPPER}} input[type="week"]:focus, {{WRAPPER}} input[type="time"]:focus, {{WRAPPER}} input[type="datetime"]:focus, {{WRAPPER}} input[type="datetime-local"]:focus, {{WRAPPER}} input[type="color"]:focus, {{WRAPPER}} textarea:focus' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'input_focus_background',
				'label' => __( 'focus Background', 'folioedgecore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} select:focus, {{WRAPPER}} input[type="text"]:focus, {{WRAPPER}} input[type="email"]:focus, {{WRAPPER}} input[type="url"]:focus, {{WRAPPER}} input[type="password"]:focus, {{WRAPPER}} input[type="search"]:focus, {{WRAPPER}} input[type="number"]:focus, {{WRAPPER}} input[type="tel"]:focus, {{WRAPPER}} input[type="date"]:focus, {{WRAPPER}} input[type="month"]:focus, {{WRAPPER}} input[type="week"]:focus, {{WRAPPER}} input[type="time"]:focus, {{WRAPPER}} input[type="datetime"]:focus, {{WRAPPER}} input[type="datetime-local"]:focus, {{WRAPPER}} input[type="color"]:focus, {{WRAPPER}} textarea:focus'
			]
		);
        
		$this->add_control(
			'input_focus_border_color',
			[
				'label' => __( 'Border Color', 'folioedgecore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} select:focus, {{WRAPPER}} input[type="text"]:focus, {{WRAPPER}} input[type="email"]:focus, {{WRAPPER}} input[type="url"]:focus, {{WRAPPER}} input[type="password"]:focus, {{WRAPPER}} input[type="search"]:focus, {{WRAPPER}} input[type="number"]:focus, {{WRAPPER}} input[type="tel"]:focus, {{WRAPPER}} input[type="date"]:focus, {{WRAPPER}} input[type="month"]:focus, {{WRAPPER}} input[type="week"]:focus, {{WRAPPER}} input[type="time"]:focus, {{WRAPPER}} input[type="datetime"]:focus, {{WRAPPER}} input[type="datetime-local"]:focus, {{WRAPPER}} input[type="color"]:focus, {{WRAPPER}} textarea:focus' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'input_focus_box_shadow',
				'selector' => '{{WRAPPER}} select:focus, {{WRAPPER}} input[type="text"]:focus, {{WRAPPER}} input[type="email"]:focus, {{WRAPPER}} input[type="url"]:focus, {{WRAPPER}} input[type="password"]:focus, {{WRAPPER}} input[type="search"]:focus, {{WRAPPER}} input[type="number"]:focus, {{WRAPPER}} input[type="tel"]:focus, {{WRAPPER}} input[type="date"]:focus, {{WRAPPER}} input[type="month"]:focus, {{WRAPPER}} input[type="week"]:focus, {{WRAPPER}} input[type="time"]:focus, {{WRAPPER}} input[type="datetime"]:focus, {{WRAPPER}} input[type="datetime-local"]:focus, {{WRAPPER}} input[type="color"]:focus, {{WRAPPER}} textarea:focus',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
		        $this->add_responsive_control(
            'custom_input_css',
            [
                'label' => __( 'Input Field CSS', 'folioedgecore' ),
                'type' => Controls_Manager::CODE,
				'rows' => 20,
                'language' => 'css',
                'selectors' => [
                    '{{WRAPPER}} select, {{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="url"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="month"], {{WRAPPER}} input[type="week"], {{WRAPPER}} input[type="time"], {{WRAPPER}} input[type="datetime"], {{WRAPPER}} input[type="datetime-local"], {{WRAPPER}} input[type="color"], {{WRAPPER}} textarea' => '{{VALUE}};',
                ],
                'separator' =>'before',
            ]
        );

		$this->end_controls_section();
        
        
        $this->start_controls_section(
			'button_section_style',
			[
				'label' => __( 'Button', 'folioedgecore' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} input[type="submit"], {{WRAPPER}} button',
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
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} input[type="submit"], {{WRAPPER}} button' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                ],
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
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} input[type="submit"], {{WRAPPER}} button' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
         $this->add_responsive_control(
            'button_floting',
            [
                'label' => __( 'Button Floating', 'folioedgecore' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'folioedgecore' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'none' => [
                        'title' => __( 'None', 'folioedgecore' ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'folioedgecore' ),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} input[type="submit"], {{WRAPPER}} button' => 'float: {{VALUE}};',
                ],
                'default' => 'none',
                'separator' =>'before',
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
					'{{WRAPPER}} input[type="submit"], {{WRAPPER}} button' => 'color: {{VALUE}};',
				],
			]
		);
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background_color',
				'label' => __( 'Background', 'folioedgecore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} input[type="submit"], {{WRAPPER}} button',
			]
		);
        
        $this->add_responsive_control(
            'button_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} input[type="submit"], {{WRAPPER}} button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} input[type="submit"], {{WRAPPER}} button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );   
        
        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border',
				'selector' => '{{WRAPPER}} input[type="submit"], {{WRAPPER}} button',
			]
		);

        $this->add_responsive_control(
			'button_radius',
			[
				'label' => __( 'Border Radius', 'folioedgecore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} input[type="submit"], {{WRAPPER}} button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} input[type="submit"], {{WRAPPER}} button',
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
					'{{WRAPPER}} input[type="submit"]:hover, {{WRAPPER}} button:hover' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_hover_background',
				'label' => __( 'Hover Background', 'folioedgecore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} input[type="submit"]:hover, {{WRAPPER}} button:hover, {{WRAPPER}} button .dir-part',
			]
		);
        
		$this->add_control(
			'button_hover_border_color',
			[
				'label' => __( 'Border Color', 'folioedgecore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} input[type="submit"]:hover, {{WRAPPER}} button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_hover_box_shadow',
				'selector' => '{{WRAPPER}} input[type="submit"]:hover, {{WRAPPER}} button:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
        
        
	}

	/**
	 * Render shortcode-form widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();        
		
?>
   <div class="shortcode-form">
        <?php
            if( $settings['icon_type'] == 'img' and !empty(Group_Control_Image_Size::get_attachment_image_html( $settings, 'icon_imagesize', 'icon_image' )) ){
                $iconimage = Group_Control_Image_Size::get_attachment_image_html( $settings, 'icon_imagesize', 'icon_image' );
                echo '<div class="x-icon">'.$iconimage.'</div>';
            }elseif( $settings['icon_type'] == 'icon' && !empty($settings['title_icon']['value']) ){
                echo sprintf( '<div class="x-icon" >%1$s</div>', folioedge_icon_manager::render_icon( $settings['title_icon'], [ 'aria-hidden' => 'true' ] ) );
            }
        ?>
       <?php echo do_shortcode( shortcode_unautop( $settings['shortcode_box'] ) ); ?>
   </div>
   <?php
    }
}

Plugin::instance()->widgets_manager->register_widget_type( new folioedge_Subscribe_Form );