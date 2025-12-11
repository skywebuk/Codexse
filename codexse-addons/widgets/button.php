<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Codexse_Addons_Elementor_Widget_Button extends Widget_Base {

    // Get widget name
    public function get_name() {
        return 'codexse_button';
    }

    // Get widget title
    public function get_title() {
        return __( 'Button', 'codexse-addons' );
    }

    // Get widget icon
    public function get_icon() {
        return 'cx-addons-icon eicon-button';
    }

    // Get widget categories
    public function get_categories() {
        return [ 'codexse-addons' ]; // Ensure this category is registered in Elementor
    }

    // Get widget keywords
    public function get_keywords() {
        return [ 'button', 'cta', 'link', 'action', 'codexse' ];
    }

    // Get style dependencies
    public function get_style_depends() {
        return [ 'codexse-button' ]; // Enqueue your custom CSS file
    }

    // Register widget controls
    protected function _register_controls() {
        $this->start_controls_section(
            'button_content',
            [
                'label' => __( 'Button', 'codexse-addons' ),
            ]
        );
        
        $this->add_control(
            'button_style',
            [
                'label' => __( 'Button Style', 'codexse-addons' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'cloude',
                'options' => [
                    'normal' => __( 'Normal', 'codexse-addons' ),
                    'cloude' => __( 'Cloude', 'codexse-addons' ),
                    'wave' => __( 'Wave', 'codexse-addons' ),
                ],
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => __( 'Text', 'codexse-addons' ),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __( 'Enter your Text', 'codexse-addons' ),
                'default' => __( 'Click Me', 'codexse-addons' ),
                'title' => __( 'Enter your Text', 'codexse-addons' ),
            ]
        );

        $this->add_control(
            'button_link',
            [
                'label' => __( 'Link', 'codexse-addons' ),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __( 'https://your-link.com', 'codexse-addons' ),
                'default' => [
                    'url' => '#',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'button_icon',
            [
                'label'       => __( 'Icon', 'codexse-addons' ),
                'type'        => Controls_Manager::ICONS,
                'label_block' => true,
            ]
        );

        // New control for icon position
        $this->add_control(
            'icon_position',
            [
                'label' => __( 'Icon Position', 'codexse-addons' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'left',
                'options' => [
                    'left' => __( 'Left', 'codexse-addons' ),
                    'right' => __( 'Right', 'codexse-addons' ),
                ],
            ]
        );

        $this->add_responsive_control(
            'buttonalign',
            [
                'label' => __( 'Alignment', 'codexse-addons' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'codexse-addons' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'codexse-addons' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'codexse-addons' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __( 'Justified', 'codexse-addons' ),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

            
        // Button Style Section
        $this->start_controls_section(
            'tab_style',
            [
                'label' => __( 'Button', 'codexse-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // Normal and Hover Tabs
        $this->start_controls_tabs('tabs_button_style');

        // Normal Tab
        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => __( 'Normal', 'codexse-addons' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => __( 'Typography', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .codexse-button',
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => __( 'Text Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'button_background',
                'label' => __( 'Background', 'codexse-addons' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .codexse-button',
            ]
        );

        $this->add_responsive_control(
            'button_margin',
            [
                'label' => __( 'Margin', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => __( 'Padding', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'label' => __( 'Border', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .codexse-button',
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => __( 'Border Radius', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow',
                'label' => __( 'Box Shadow', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .codexse-button',
            ]
        );


        $this->add_responsive_control("button_width", [
            "label" => __("Width", "codexse"),
            "type" => Controls_Manager::SLIDER,
            "size_units" => ["px", "%"],
            "range" => [
                "px" => ["min" => 0, "max" => 1000, "step" => 1],
                "%" => ["min" => 0, "max" => 100],
            ],
            "selectors" => [
                "{{WRAPPER}} .codexse-button" =>
                    "width: {{SIZE}}{{UNIT}};",
            ],
        ]);

        $this->add_responsive_control("button_height", [
            "label" => __("Height", "codexse"),
            "type" => Controls_Manager::SLIDER,
            "size_units" => ["px", "%"],
            "range" => [
                "px" => ["min" => 0, "max" => 1000, "step" => 1],
                "%" => ["min" => 0, "max" => 100],
            ],
            "selectors" => [
                "{{WRAPPER}} .codexse-button" => "height: {{SIZE}}{{UNIT}};",
            ],
        ]);

        $this->end_controls_tab();

        // Hover Tab
        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => __( 'Hover', 'codexse-addons' ),
            ]
        );

        $this->add_control(
            'button_hover_text_color',
            [
                'label' => __( 'Text Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'button_hover_background',
                'label' => __( 'Background', 'codexse-addons' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .codexse-button.normal-button:hover,{{WRAPPER}} .codexse-button.cloude-button:before,{{WRAPPER}} .codexse-button.wave-button:before',
            ]
        );

        $this->add_control(
            'button_hover_border_color',
            [
                'label' => __( 'Border Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-button:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_hover_box_shadow',
                'label' => __( 'Box Shadow', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .codexse-button:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();
        $this->end_controls_section();


        $this->icon_style_controls();
    }


	protected function icon_style_controls() {
		$this->start_controls_section(
			'icon_style_section',
			[
				'label' => __( 'Icon', 'codexse-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);


        $this->start_controls_tabs("icon_style_tab");

        $this->start_controls_tab("icon_normal", [
            "label" => __("Normal", "codexse"),
        ]);

        $this->add_responsive_control("icon_width", [
            "label" => __("Width", "codexse"),
            "type" => Controls_Manager::SLIDER,
            "size_units" => ["px", "%"],
            "range" => [
                "px" => ["min" => 0, "max" => 1000, "step" => 1],
                "%" => ["min" => 0, "max" => 100],
            ],
            "selectors" => [
                "{{WRAPPER}} .codexse-button .button-icon" =>
                    "width: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};font-size: {{SIZE}}{{UNIT}};",
            ],
        ]);

        $this->add_responsive_control("icon_height", [
            "label" => __("Height", "codexse"),
            "type" => Controls_Manager::SLIDER,
            "size_units" => ["px", "%"],
            "range" => [
                "px" => ["min" => 0, "max" => 1000, "step" => 1],
                "%" => ["min" => 0, "max" => 100],
            ],
            "selectors" => [
                "{{WRAPPER}} .codexse-button .button-icon" => "height: {{SIZE}}{{UNIT}};",
            ],
        ]);
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'icon_typorgraphy',
                'selector' => '{{WRAPPER}} .codexse-button .button-icon',
            ]
        );

        $this->add_control("icon_color", [
            "label" => __("Color", "codexse"),
            "type" => Controls_Manager::COLOR,
            "selectors" => ["{{WRAPPER}} .codexse-button .button-icon" => "color: {{VALUE}};"],
        ]);

        $this->add_group_control(Group_Control_Background::get_type(), [
            "name" => "icon_background",
            "label" => __("Background", "codexse"),
            "types" => ["classic", "gradient"],
            "selector" => "{{WRAPPER}} .codexse-button .button-icon",
        ]);

        $this->add_responsive_control("icon_margin", [
            "label" => __("Margin", "codexse"),
            "type" => Controls_Manager::DIMENSIONS,
            "size_units" => ["px", "%", "em"],
            "selectors" => [
                "{{WRAPPER}} .codexse-button .button-icon" =>
                    "margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
            ],
            "separator" => "before",
        ]);

        $this->add_responsive_control("icon_padding", [
            "label" => __("Padding", "codexse"),
            "type" => Controls_Manager::DIMENSIONS,
            "size_units" => ["px", "%", "em"],
            "selectors" => [
                "{{WRAPPER}} .codexse-button .button-icon" =>
                    "padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
            ],
            "separator" => "before",
        ]);
        $this->add_group_control(Group_Control_Border::get_type(), [
            "name" => "icon_border",
            "label" => __("Border", "codexse"),
            "selector" => "{{WRAPPER}} .codexse-button .button-icon",
        ]);
        $this->add_responsive_control("icon_border_radius", [
            "label" => esc_html__("Border Radius", "codexse"),
            "type" => Controls_Manager::DIMENSIONS,
            "size_units" => ["px", "%", "em"],
            "range" => [
                "px" => ["min" => 0, "max" => 1000, "step" => 1],
                "%" => ["min" => 0, "max" => 100],
            ],
            "selectors" => [
                "{{WRAPPER}} .codexse-button .button-icon" =>
                    "border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
            ],
        ]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            "name" => "icon_shadow",
            "label" => __("Box Shadow", "codexse"),
            "selector" => "{{WRAPPER}} .codexse-button .button-icon",
        ]);
        $this->add_control("icon_transition", [
            "label" => __("Transition Duration", "codexse"),
            "type" => Controls_Manager::SLIDER,
            "default" => ["size" => 0.3],
            "range" => ["px" => ["max" => 3, "step" => 0.1]],
            "selectors" => [
                "{{WRAPPER}} .codexse-button .button-icon" => "transition-duration: {{SIZE}}s",
            ],
        ]);

        $this->add_control("icon_transform", [
            "label" => __("Transform", "codexse"),
            "type" => Controls_Manager::TEXT,
            "default" => "translateY(0)",
            "selectors" => [
                "{{WRAPPER}} .codexse-button .button-icon" => "transform: {{VALUE}}",
            ],
        ]);
        $this->end_controls_tab(); // Hover Style tab end
        $this->start_controls_tab("codexde_icon_hover", [
            "label" => __("Hover", "codexse"),
        ]);
        $this->add_control("codexde_icon_hover_color", [
            "label" => __("Hover Color", "codexse"),
            "type" => Controls_Manager::COLOR,
            "selectors" => [
                "{{WRAPPER}} .codexse-button:hover .button-icon" => "color: {{VALUE}};",
            ],
        ]);
        $this->add_group_control(Group_Control_Background::get_type(), [
            "name" => "codexde_icon_hover_background",
            "label" => __("Background", "codexse"),
            "types" => ["classic", "gradient"],
            "selector" => "{{WRAPPER}} .codexse-button:hover .button-icon",
        ]);
        $this->add_group_control(Group_Control_Border::get_type(), [
            "name" => "codexde_icon_hover_border",
            "label" => __("Border", "codexse"),
            "selector" => "{{WRAPPER}} .codexse-button:hover .button-icon",
        ]);
        $this->add_responsive_control("codexde_icon_hover_radius", [
            "label" => esc_html__("Border Radius", "codexse"),
            "type" => Controls_Manager::DIMENSIONS,
            "size_units" => ["px", "%", "em"],
            "range" => [
                "px" => ["min" => 0, "max" => 1000, "step" => 1],
                "%" => ["min" => 0, "max" => 100],
            ],
            "selectors" => [
                "{{WRAPPER}} .codexse-button:hover .button-icon" =>
                    "border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
            ],
        ]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            "name" => "codexde_icon_hover_shadow",
            "label" => __("Box Shadow", "codexse"),
            "selector" => "{{WRAPPER}} .codexse-button:hover .button-icon",
        ]);

        $this->end_controls_tab(); // Hover Style tab end
        $this->end_controls_tabs(); // Box Style tabs end

		$this->end_controls_section();
	}

    protected function render( $instance = [] ) {
        // Get settings for display
        $settings = $this->get_settings_for_display();
        
        // Retrieve button style, text, icon, and icon position settings
        $button_style = isset( $settings['button_style'] ) ? $settings['button_style'] : '';
        $text = ! empty( $settings['button_text'] ) ? wp_kses_post( $settings['button_text'] ) : '';
        
        // Render icon if set
        $icon = '';
        if ( ! empty( $settings['button_icon']['value'] ) ) {
            $icon = Codexse_Addons_Icon_manager::render_icon($settings['button_icon'], ["aria-hidden" => "true"]);
            $icon_position = $settings['icon_position'] === 'right' ? $text . '<span class="button-icon right-icon">' . $icon . '</span>' : '<span class="button-icon left-icon">' . $icon . '</span>' . $text;
        }else {
            $icon_position = $text;            
        }
        
        // Determine icon position
        
        // Use Elementor's get_link_url to fetch URL
        $url = ! empty( $settings['button_link']['url'] ) ? esc_url( $settings['button_link']['url'] ) : '#';
        $target = ! empty( $settings['button_link']['is_external'] ) ? ' target="_blank"' : '';
        $nofollow = ! empty( $settings['button_link']['nofollow'] ) ? ' rel="nofollow"' : '';
        $link_attributes = sprintf( 'href="%s"%s%s', $url, $target, $nofollow );

        // Display the appropriate button style
        switch ( $button_style ) {
            case 'cloude':
                $this->cloude_button_content( $settings, $icon_position, $link_attributes );
                break;
            case 'wave':
                $this->wave_button_content( $settings, $icon_position, $link_attributes );
                break;
            default:
                $this->normal_button_content( $settings, $icon_position, $link_attributes );
                break;
        }
    }
    
    private function normal_button_content( $settings, $icon_position, $link_attributes ) {
        echo '<a ' . $link_attributes . ' class="codexse-button normal-button">' . $icon_position . '</a>';
    }
    
    private function cloude_button_content( $settings, $icon_position, $link_attributes ) {
        echo '<a ' . $link_attributes . ' class="codexse-button cloude-button">' . $icon_position . '</a>';
    }

    private function wave_button_content( $settings, $icon_position, $link_attributes ) {
        echo '<a ' . $link_attributes . ' class="codexse-button wave-button">' . $icon_position . '</a>';
    }
    
}
