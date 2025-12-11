<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Codexse_Addons_Elementor_Widget_Desktop_Menu extends \Elementor\Widget_Base {

    // Get widget name
    public function get_name() {
        return 'codexse_desktop_menu';
    }

    // Get widget title
    public function get_title() {
        return __( 'Desktop Menu', 'codexse-addons' );
    }

    // Get widget icon
    public function get_icon() {
        return 'cx-addons-icon eicon-nav-menu';
    }

    // Get widget categories
    public function get_categories() {
        return [ 'codexse-addons' ]; // Ensure this category is registered in Elementor
    }

    // Get widget keywords
    public function get_keywords() {
        return [ 'menu', 'desktop menu', 'navigation', 'codexse', 'header', 'navbar' ];
    }

    // Get style dependencies
    public function get_style_depends() {
        return [ 'codexse-desktop-menu' ]; // Enqueue your custom CSS file
    }

    // Register widget controls
    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'codexse-addons' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'menu',
            [
                'label' => __( 'Select Menu', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_available_menus(),
                'default' => 'primary',
            ]
        );
    
        $this->end_controls_section();
        
        // Start the style section
        $this->start_controls_section(
            'desktop_menu_style',
            [
                'label' => __( 'Navmenu', 'codexse-addons' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // Menu Alignment Control using eIcons
        $this->add_control(
            'menu_alignment',
            [
                'label' => __( 'Menu Alignment', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::CHOOSE,
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
                ],
                'default' => 'left',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu' => 'text-align: {{VALUE}};', // Apply text-align to the container
                ],
            ]
        );

        // Width Control
        $this->add_responsive_control(
            'menu_width',
            [
                'label' => __( 'Width', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Height Control
        $this->add_responsive_control(
            'menu_height',
            [
                'label' => __( 'Height', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Margin Control
        $this->add_control(
            'menu_margin',
            [
                'label' => __( 'Margin', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Padding Control
        $this->add_control(
            'menu_padding',
            [
                'label' => __( 'Padding', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'menu_background',
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .codexse-desktop-menu',
			]
		);

        // Border Control
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'menu_border',
                'label' => __( 'Border', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .codexse-desktop-menu',
            ]
        );

        // Border Radius Control
        $this->add_control(
            'menu_border_radius',
            [
                'label' => __( 'Border Radius', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Box Shadow Control
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'menu_box_shadow',
                'label' => __( 'Box Shadow', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .codexse-desktop-menu',
            ]
        );

        // End the style section
        $this->end_controls_section();

        // Start the Navmenu Item style section
        $this->start_controls_section(
            'navmenu_item_style',
            [
                'label' => __( 'Navmenu Item', 'codexse-addons' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // Normal State Controls
        $this->start_controls_tabs( 'navmenu_item_tabs' );

        $this->start_controls_tab(
            'navmenu_item_normal_tab',
            [
                'label' => __( 'Normal', 'codexse-addons' ),
            ]
        );

        // Normal State: Color
        $this->add_control(
            'navmenu_item_color',
            [
                'label' => __( 'Text Color', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items > li > a' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Normal State: Background
        $this->add_control(
            'navmenu_item_background',
            [
                'label' => __( 'Background', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items > li > a' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Normal State: Padding
        $this->add_control(
            'navmenu_item_padding',
            [
                'label' => __( 'Padding', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Normal State: Margin
        $this->add_control(
            'navmenu_item_margin',
            [
                'label' => __( 'Margin', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items > li > a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Normal State: Border
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'navmenu_item_border',
                'label' => __( 'Border', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .codexse-desktop-menu-items > li > a',
            ]
        );

        // Normal State: Border Radius
        $this->add_control(
            'navmenu_item_border_radius',
            [
                'label' => __( 'Border Radius', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items > li > a' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Normal State: Box Shadow
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'navmenu_item_box_shadow',
                'label' => __( 'Box Shadow', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .codexse-desktop-menu-items > li > a',
            ]
        );

        $this->end_controls_tab();

        // Hover State Controls
        $this->start_controls_tab(
            'navmenu_item_hover_tab',
            [
                'label' => __( 'Hover', 'codexse-addons' ),
            ]
        );

        // Hover State: Color
        $this->add_control(
            'navmenu_item_hover_color',
            [
                'label' => __( 'Text Color', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items > li:hover > a' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Hover State: Background
        $this->add_control(
            'navmenu_item_hover_background',
            [
                'label' => __( 'Background', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items > li:hover > a' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Hover State: Box Shadow
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'navmenu_item_hover_box_shadow',
                'label' => __( 'Box Shadow', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .codexse-desktop-menu-items > li:hover > a',
            ]
        );

        // Hover State: Border Color
        $this->add_control(
            'navmenu_item_hover_border_color',
            [
                'label' => __( 'Border Color', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items > li:hover > a' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        // Active State Controls
        $this->start_controls_tab(
            'navmenu_item_active_tab',
            [
                'label' => __( 'Active', 'codexse-addons' ),
            ]
        );

        // Active State: Color
        $this->add_control(
            'navmenu_item_active_color',
            [
                'label' => __( 'Text Color', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items > li.active > a, {{WRAPPER}} .codexse-desktop-menu-items > li.current-menu-item > a' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Active State: Background
        $this->add_control(
            'navmenu_item_active_background',
            [
                'label' => __( 'Background', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items > li.active > a, {{WRAPPER}} .codexse-desktop-menu-items > li.current-menu-item > a' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Active State: Box Shadow
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'navmenu_item_active_box_shadow',
                'label' => __( 'Box Shadow', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .codexse-desktop-menu-items > li.active > a, {{WRAPPER}} .codexse-desktop-menu-items > li.current-menu-item > a',
            ]
        );

        // Active State: Border Color
        $this->add_control(
            'navmenu_item_active_border_color',
            [
                'label' => __( 'Border Color', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items > li.active > a, {{WRAPPER}} .codexse-desktop-menu-items > li.current-menu-item > a' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();


        // End the Tabs
        $this->end_controls_tabs();

        // End the style section
        $this->end_controls_section();


        // Start the Submenu Item style section
        $this->start_controls_section(
            'submenu_item_style',
            [
                'label' => __( 'Submenu Item', 'codexse-addons' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );


        // Width Control
        $this->add_responsive_control(
            'submenu_min_width',
            [
                'label' => __( 'Min Width', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 220,
                ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu ul' => 'min-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->add_control(
            'submenu_background',
            [
                'label' => __( 'Background', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items ul' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'submenu_border',
                'label' => __( 'Border', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .codexse-desktop-menu-items ul',
            ]
        );

        $this->add_control(
            'submenu_border_radius',
            [
                'label' => __( 'Border Radius', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items ul' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'submenu_box_shadow',
                'label' => __( 'Box Shadow', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .codexse-desktop-menu-items ul',
            ]
        );


        // Normal State Controls for Submenu
        $this->start_controls_tabs( 'submenu_item_tabs' );

        $this->start_controls_tab(
            'submenu_item_normal_tab',
            [
                'label' => __( 'Normal', 'codexse-addons' ),
            ]
        );

        // Normal State: Color
        $this->add_control(
            'submenu_item_color',
            [
                'label' => __( 'Text Color', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items li li a' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Normal State: Background
        $this->add_control(
            'submenu_item_background',
            [
                'label' => __( 'Background', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items li li a' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Normal State: Padding
        $this->add_control(
            'submenu_item_padding',
            [
                'label' => __( 'Padding', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items li li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Normal State: Margin
        $this->add_control(
            'submenu_item_margin',
            [
                'label' => __( 'Margin', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items li li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Normal State: Border
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'submenu_item_border',
                'label' => __( 'Border', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .codexse-desktop-menu-items li li',
            ]
        );

        // Normal State: Border Radius
        $this->add_control(
            'submenu_item_border_radius',
            [
                'label' => __( 'Border Radius', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items li li a' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Normal State: Box Shadow
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'submenu_item_box_shadow',
                'label' => __( 'Box Shadow', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .codexse-desktop-menu-items li li a',
            ]
        );

        $this->end_controls_tab();

        // Hover State Controls for Submenu
        $this->start_controls_tab(
            'submenu_item_hover_tab',
            [
                'label' => __( 'Hover', 'codexse-addons' ),
            ]
        );

        // Hover State: Color
        $this->add_control(
            'submenu_item_hover_color',
            [
                'label' => __( 'Text Color', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items li li:hover > a' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Hover State: Background
        $this->add_control(
            'submenu_item_hover_background',
            [
                'label' => __( 'Background', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items li li:hover > a' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Hover State: Box Shadow
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'submenu_item_hover_box_shadow',
                'label' => __( 'Box Shadow', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .codexse-desktop-menu-items li li:hover > a',
            ]
        );

        // Hover State: Border Color
        $this->add_control(
            'submenu_item_hover_border_color',
            [
                'label' => __( 'Border Color', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items li li:hover > a' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        // Active State Controls for Submenu
        $this->start_controls_tab(
            'submenu_item_active_tab',
            [
                'label' => __( 'Active', 'codexse-addons' ),
            ]
        );

        // Active State: Color
        $this->add_control(
            'submenu_item_active_color',
            [
                'label' => __( 'Text Color', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items li li.active > a, {{WRAPPER}} .codexse-desktop-menu-items li li.current-menu-item > a' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Active State: Background
        $this->add_control(
            'submenu_item_active_background',
            [
                'label' => __( 'Background', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items li li.active > a, {{WRAPPER}} .codexse-desktop-menu-items li li.current-menu-item > a' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Active State: Box Shadow
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'submenu_item_active_box_shadow',
                'label' => __( 'Box Shadow', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .codexse-desktop-menu-items li li.active > a, {{WRAPPER}} .codexse-desktop-menu-items li li.current-menu-item > a',
            ]
        );

        // Active State: Border Color
        $this->add_control(
            'submenu_item_active_border_color',
            [
                'label' => __( 'Border Color', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-desktop-menu-items li li.active > a, {{WRAPPER}} .codexse-desktop-menu-items li li.current-menu-item > a' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        // End the Tabs
        $this->end_controls_tabs();

        // End the Submenu Item style section
        $this->end_controls_section();


    }

    // Render widget output on the frontend
    protected function render() {
        $settings = $this->get_settings_for_display();
        $menu_id = $settings['menu'];

        if ( $menu_id ) {
            echo '<div class="codexse-desktop-menu">';
            wp_nav_menu([
                'menu' => $menu_id,
                'container' => false,
                'menu_class' => 'codexse-desktop-menu-items',
                'walker' => new Codexse_Addons_Desktop_Menu_Walker,
            ]);
            echo '</div>';
        }
    }

    // Helper function to retrieve available menus
    private function get_available_menus() {
        $menus = wp_get_nav_menus();
        $options = [];

        if ( ! empty( $menus ) ) {
            foreach ( $menus as $menu ) {
                $options[ $menu->slug ] = $menu->name;
            }
        }

        return $options;
    }

    // Render fallback if Elementor is not active
    public function render_plain_content() {
        _e( 'Please select a menu to display.', 'codexse-addons' );
    }
}
