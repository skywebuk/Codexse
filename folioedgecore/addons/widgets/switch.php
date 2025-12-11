<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Folioedge_Elementor_Widget_Switch extends Widget_Base {

    public function get_name() {
        return 'folioedge-switch-addons';
    }
    
    public function get_title() {
        return __( 'Switch', 'folioedgecore' );
    }

	public function get_icon() {
		return "folioedge-icon eicon-dual-button";
	}
    
	public function get_categories() {
		return [ 'folioedgecore' ];
	} 
    
	public function get_keywords() {
		return [ 'tabs', 'accordion', 'toggle', 'switch' ];
	}

    public function get_script_depends() {
        return [
            'bootstrap-js',
        ];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'switch_content_section',
            [
                'label' => __( 'Switch', 'folioedgecore' ),
            ]
        );
            
		    $this->start_controls_tabs( 'switch_content_tabs' );
                $this->start_controls_tab(
                    'switch_content_tab_left',
                    [
                        'label' => __( 'Left Side', 'folioedgecore' ),
                    ]
                );   

                    $this->add_control(
                        'switch_content_left_label',
                        [
                            'label'   => esc_html__( 'Label', 'folioedgecore' ),
                            'type'    => Controls_Manager::TEXT,
                            'default' => esc_html__( 'Left Side', 'folioedgecore' ),
                        ]
                    );

                    $this->add_control(
                        'switch_left_content_sourch',
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

                    $this->add_control(
                        'switch_left_content_template_id',
                        [
                            'label'       => __( 'Select Template', 'folioedgecore' ),
                            'type'        => Controls_Manager::SELECT,
                            'default'     => '0',
                            'options'     => folioedge_elementor_template(),
                            'condition'   => [
                                'switch_left_content_sourch' => "elementor"
                            ],
                        ]
                    );

                    $this->add_control(
                        'switch_left_content_custom',
                        [
                            'label' => __( 'Select Template', 'folioedgecore' ),
                            'type' => Controls_Manager::WYSIWYG,
                            'title' => __( 'Select Template', 'folioedgecore' ),
                            'show_label' => false,
                            'condition' => [
                                'switch_left_content_sourch' =>'custom',
                            ],
                        ]
                    );

                $this->end_controls_tab();
                $this->start_controls_tab(
                    'switch_content_tab_right',
                    [
                        'label' => __( 'Right Side', 'folioedgecore' ),
                    ]
                );   

                    $this->add_control(
                        'switch_content_right_label',
                        [
                            'label'   => esc_html__( 'Label', 'folioedgecore' ),
                            'type'    => Controls_Manager::TEXT,
                            'default' => esc_html__( 'Right Side', 'folioedgecore' ),
                        ]
                    );  
                    
                    
                    $this->add_control(
                        'switch_right_content_sourch',
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

                    $this->add_control(
                        'switch_right_content_template_id',
                        [
                            'label'       => __( 'Select Template', 'folioedgecore' ),
                            'type'        => Controls_Manager::SELECT,
                            'default'     => '0',
                            'options'     => folioedge_elementor_template(),
                            'condition'   => [
                                'switch_right_content_sourch' => "elementor"
                            ],
                        ]
                    );

                    $this->add_control(
                        'switch_right_content_custom',
                        [
                            'label' => __( 'Select Template', 'folioedgecore' ),
                            'type' => Controls_Manager::WYSIWYG,
                            'title' => __( 'Select Template', 'folioedgecore' ),
                            'show_label' => false,
                            'condition' => [
                                'switch_right_content_sourch' =>'custom',
                            ],
                        ]
                    );

                $this->end_controls_tab();

		    $this->end_controls_tabs();            

            $this->add_control(
                'switch_content_description',
                [
                    'label'   => esc_html__( 'Description', 'folioedgecore' ),
                    'type'    => Controls_Manager::TEXT,
                    'default' => esc_html__( 'Save 60%', 'folioedgecore' ),
                ]
            );
        
        $this->end_controls_section();


        
        // Style tab area tab section
        $this->start_controls_section(
            'folioedge_switch_toggle_area',
            [
                'label' => __( 'Switch', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
            
            $this->add_responsive_control(
                'folioedge_switch_toggle_margin',
                [
                    'label' => __( 'Margin', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-switches' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' =>'before',
                ]
            );

            $this->add_responsive_control(
                'folioedge_switch_toggle_padding',
                [
                    'label' => __( 'Padding', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-switches' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' =>'before',
                ]
            );
        
            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'folioedge_switch_toggle_background',
                    'label' => __( 'Background', 'folioedgecore' ),
                    'types' => [ 'classic', 'gradient' ],
                    'selector' => '{{WRAPPER}} .folioedge-switches',
                ]
            );
            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'folioedge_switch_toggle_border',
                    'label' => __( 'Border', 'folioedgecore' ),
                    'selector' => '{{WRAPPER}} .folioedge-switches',
                ]
            );
            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'folioedge_switch_toggle_shadow',
                    'label' => __( 'Box Shadow', 'folioedgecore' ),
                    'selector' => '{{WRAPPER}} .folioedge-switches',
                ]
            );
            $this->add_responsive_control(
                'folioedge_switch_toggle_width',
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
                        '{{WRAPPER}} .folioedge-switches' => 'width: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );
            $this->add_responsive_control(
                'folioedge_switch_toggle_height',
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
                        '{{WRAPPER}} .folioedge-switches' => 'height: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );
        
            $this->add_responsive_control(
                'folioedge_switch_toggle_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'folioedgecore' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-switches' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                    ],
                ]
            );
            $this->add_responsive_control(
                'folioedge_switch_toggle_align',
                [
                    'label' => __( 'Alignment', 'folioedgecore' ),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'flex-start' => [
                            'title' => __( 'Left', 'folioedgecore' ),
                            'icon' => 'eicon-text-align-left',
                        ],
                        'center' => [
                            'title' => __( 'Center', 'folioedgecore' ),
                            'icon' => 'eicon-text-align-center',
                        ],
                        'flex-end' => [
                            'title' => __( 'Right', 'folioedgecore' ),
                            'icon' => 'eicon-text-align-right',
                        ]
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-switches' => 'justify-content: {{VALUE}};',
                    ],
                ]
            );
            $this->add_control(
                'folioedge_switch_toggle_label_heading',
                [
                    'label' => esc_html__( 'Label', 'folioedgecore' ),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );
            $this->add_control(
                'folioedge_switch_toggle_label_color',
                [
                    'label' => __( 'Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-switches .switch-item' => 'color: {{VALUE}};',
                    ],
                ]
            );
            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'folioedge_switch_toggle_label_typography',
                    'selector' => '{{WRAPPER}} .folioedge-switches .switch-item',
                ]
            );            
            $this->add_control(
                'folioedge_switch_toggle_circle_heading',
                [
                    'label' => esc_html__( 'Toggle', 'folioedgecore' ),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );
            
            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'folioedge_switch_toggle_circle_background',
                    'label' => __( 'Background', 'folioedgecore' ),
                    'types' => [ 'classic', 'gradient' ],
                    'selector' => '{{WRAPPER}} .folioedge-switches .swtich-toggle',
                ]
            );            
            $this->add_control(
                'folioedge_switch_toggle_circle_color',
                [
                    'label' => __( 'Circle Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .folioedge-switches .swtich-toggle .circle' => 'background-color: {{VALUE}};',
                    ],
                ]
            );
        $this->end_controls_section();

        
        // Feature Style tab section
        $this->start_controls_section(
            'folioedge_switch_descirption_section',
            [
                'label' => __( 'Description', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'folioedge_switch_descirption_typography',
                'selector' => '{{WRAPPER}} .switch-description',
            ]
        );
        $this->add_control(
            'folioedge_switch_descirption_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .switch-description' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'folioedge_switch_descirption_margin',
            [
                'label' => __( 'Margin', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .switch-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'folioedge_switch_descirption_padding',
            [
                'label' => __( 'Padding', 'folioedgecore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .switch-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );   
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'folioedge_switch_descirption_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .switch-description',
            ]
        );
        
        $this->add_responsive_control(
            'folioedge_switch_descirption_align',
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
                    '{{WRAPPER}} .switch-description' => 'text-align: {{VALUE}};',
                ],
            ]
        );
          
        $this->end_controls_section();

    }

    protected function render() {
        $settings   = $this->get_settings_for_display();

        $data = '';

        $data .= '<div class="folioedge-switch-area" >';

            $data .= '<div class="folioedge-switches" >';
                $data .= '<button type="button" class="switch-item left-switch" data-switch="left" >';
                    $data .= esc_html($settings['switch_content_left_label']);
                $data .= '</button>';
                $data .= '<button type="button" class="swtich-toggle" ><span class="circle"></span></button>';
                $data .= '<button type="button" class="switch-item right-switch" data-switch="right" >';
                    $data .= esc_html($settings['switch_content_right_label']);
                $data .= '</button>';
            $data .= '</div>';
            
            if( !empty($settings['switch_content_description'])){
                $data .= '<div class="switch-description">';
                $data .= esc_html($settings['switch_content_description']);
                $data .= '</div>';
            }

            $data .= '<div class="switch-contents" >';
                $data .= '<div class="switch-content left-content" >';
                    if ( $settings['switch_left_content_sourch'] == 'custom' && !empty( $settings['switch_left_content_custom'] ) ) {
                        $data .=  wp_kses_post( $settings['switch_left_content_custom'] );
                    } elseif ( $settings['switch_left_content_sourch'] == "elementor" && !empty( $settings['switch_left_content_template_id'] )) {
                        $data .=  Plugin::instance()->frontend->get_builder_content_for_display( $settings['switch_left_content_template_id'] );
                    }
                $data .= '</div>';
                $data .= '<div class="switch-content right-content" >';
                    if ( $settings['switch_right_content_sourch'] == 'custom' && !empty( $settings['switch_right_content_custom'] ) ) {
                        $data .=  wp_kses_post( $settings['switch_right_content_custom'] );
                    } elseif ( $settings['switch_right_content_sourch'] == "elementor" && !empty( $settings['switch_right_content_template_id'] )) {
                        $data .=  Plugin::instance()->frontend->get_builder_content_for_display( $settings['switch_right_content_template_id'] );
                    }
                $data .= '</div>';
            $data .= '</div>';

        $data .= '</div>';

        echo $data; 
        
    }
}
Plugin::instance()->widgets_manager->register( new Folioedge_Elementor_Widget_Switch() );
