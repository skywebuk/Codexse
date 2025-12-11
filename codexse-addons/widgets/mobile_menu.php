<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Codexse_Addons_Elementor_Widget_Mobile_Menu extends Widget_Base {

    // Get widget name
    public function get_name() {
        return 'codexse_mobile_menu';
    }

    // Get widget title
    public function get_title() {
        return __( 'Mobile Menu', 'codexse-addons' );
    }

    // Get widget icon
    public function get_icon() {
        return 'cx-addons-icon eicon-menu-toggle';
    }

    // Get widget categories
    public function get_categories() {
        return [ 'codexse-addons' ]; // Ensure this category is registered in Elementor
    }

    // Get widget keywords
    public function get_keywords() {
        return [ 'menu', 'mobile menu', 'navigation', 'codexse-addons', 'header', 'navbar', 'toggle', 'button', "hamburger" ];
    }
    
      // Get style dependencies
    public function get_style_depends() {
        return [ 'codexse-mobile-menu' ]; // Enqueue your custom CSS file
    }
      // Get script dependencies
    public function get_script_depends() {
        return [ 'codexse-mobile-menu' ]; // Enqueue your custom CSS file
    }

    // Register widget controls
    protected function _register_controls() {

        $this->start_controls_section(
            'select_menu_options',
            [
                'label' => __( 'Select Menu', 'codexse-addons' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'selected_menu',
            [
                'label' => __( 'Select Menu', 'codexse-addons' ),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_available_menus(),
                'default' => 'primary',
                'description' => __( 'Choose a menu from the available WordPress menus to display in this widget.', 'codexse-addons' ),
            ]
        );

        $this->add_control(
            'menu_style', // Updated name
            [
                'label' => __('Select Menu Layout', 'codexse-addons'), // Updated label
                'description' => __('Choose the layout of the menu. You can select a full-width layout or align the menu to the left or right side of the screen.', 'codexse-addons'), // Added description
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'right' => __('Right Side', 'codexse-addons'),
                    'full' => __('Full Width', 'codexse-addons'),
                    'left' => __('Left Side', 'codexse-addons'),
                ],
                'default' => 'right',
            ]
        );        
        
        $this->end_controls_section();
        $this->start_controls_section(
            'open_button_section',
            [
                'label' => __( 'Open Button Settings', 'codexse-addons' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'open_icon_type',
            [
                'label' => __( 'Button Icon Type', 'codexse-addons' ),
                'description' => __( 'Choose the type of icon to display on the open button: an icon or an image.', 'codexse-addons' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'icon' => [
                        'title' => __( 'Use Icon', 'codexse-addons' ),
                        'icon' => 'eicon-star',
                    ],
                    'image' => [
                        'title' => __( 'Use Image', 'codexse-addons' ),
                        'icon' => 'eicon-image',
                    ],
                ],
                'default' => 'icon',
            ]
        );
        
        // Image Control
        $this->add_control(
            'open_icon_image',
            [
                'label' => __( 'Button Image', 'codexse-addons' ),
                'description' => __( 'Upload an image to use as the button icon. This will replace the default icon.', 'codexse-addons' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'open_icon_type' => 'image'
                ],
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );
        
        // Image Size Control (for images)
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'open_icon_image_size',
                'default' => 'large',
                'separator' => 'none',
                'exclude' => [
                    'full',
                    'custom',
                    'large',
                    'shop_catalog',
                    'shop_single',
                    'shop_thumbnail'
                ],
                'condition' => [
                    'open_icon_type' => 'image'
                ],
            ]
        );
        
        // Icon Control (FontAwesome)
        $this->add_control(
            'open_web_icon',
            [
                'label' => __( 'Button Icon', 'codexse-addons' ),
                'description' => __( 'Select an icon from the Font Awesome library to display on the button.', 'codexse-addons' ),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
                'default' => [
                    'value' => 'fas fa-bars',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'open_icon_type' => 'icon'
                ],
            ]
        );
        // Button Alignment
        $this->add_control(
            'open_button_alignment',
            [
                'label' => __('Button Alignment', 'codexse-addons'),
                'description' => __('Select the alignment for the button (left, center, or right).', 'codexse-addons'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Align Left', 'codexse-addons'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Align Center', 'codexse-addons'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Align Right', 'codexse-addons'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .open-button-wrapper' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'open_button_style_mode',
            [
                'label' => __('Button Style', 'codexse-addons'),
                'description' => __('Choose a style mode for the button: flat, border, or clean.', 'codexse-addons'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'flat' => __('Flat', 'codexse-addons'),
                    'border' => __('Border', 'codexse-addons'),
                    'clean' => __('Clean', 'codexse-addons'),
                ],
                'default' => 'flat',
            ]
        );

        $this->add_control(
            'open_button_size',
            [
                'label' => __('Button Size', 'codexse-addons'),
                'description' => __('Select a predefined size for the button.', 'codexse-addons'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'small' => __('Small', 'codexse-addons'),
                    'medium' => __('Medium', 'codexse-addons'),
                    'large' => __('Large', 'codexse-addons'),
                    'extra_large' => __('Extra Large', 'codexse-addons'),
                ],
                'default' => 'medium',
                'condition' => [
                    'open_button_style_mode!' => [ 'clean' ],
                ],
            ]
        );

        $this->add_control(
            'open_button_shape',
            [
                'label' => __('Button Shape', 'codexse-addons'),
                'description' => __('Define the shape of the button (rectangle, round, or rounded).', 'codexse-addons'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'rectangle' => __('Rectangle', 'codexse-addons'),
                    'round' => __('Round', 'codexse-addons'),
                    'rounded' => __('Rounded', 'codexse-addons'),
                ],
                'default' => 'rectangle',
                'condition' => [
                    'open_button_style_mode!' => [ 'clean' ],
                ],
            ]
        );


        $this->start_controls_tabs('button_style_tabs');

        // Normal Tab
        $this->start_controls_tab(
            'open_button_normal_tab',
            [
                'label' => __('Normal', 'skyweb-donation'),
            ]
        );

        // Button Text Color (Normal)
        $this->add_control(
            'open_button_color_normal',
            [
                'label' => __('Color', 'skyweb-donation'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-mobile-menu-wrapper .open-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Button Background Color (Normal)
        $this->add_control(
            'open_button_background_normal',
            [
                'label' => __('Background Color', 'skyweb-donation'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-mobile-menu-wrapper .open-button' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'open_button_style_mode' => 'flat',
                    'open_button_style_mode!' => [ 'clean' ],
                ],
            ]
        );

        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            "name" => "open_button_border",
            "label" => __("Border", "skyweb-donation"),
            "selector" => "'{{WRAPPER}} .codexse-mobile-menu-wrapper .open-button",
            'condition' => [
                'open_button_style_mode' => 'border',
                'open_button_style_mode!' => [ 'clean' ],
            ],
        ]);

        $this->end_controls_tab();

        // Hover Tab
        $this->start_controls_tab(
            'open_button_hover_tab',
            [
                'label' => __('Hover', 'skyweb-donation'),
            ]
        );

        // Button Text Color (Hover)
        $this->add_control(
            'open_button_color_hover',
            [
                'label' => __('Color', 'skyweb-donation'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-mobile-menu-wrapper .open-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Button Background Color (Hover)
        $this->add_control(
            'open_button_background_hover',
            [
                'label' => __('Background Color', 'skyweb-donation'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-mobile-menu-wrapper .open-button:hover' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'open_button_style_mode!' => [ 'clean' ],
                ],
            ]
        );

        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            "name" => "open_button_hover_border",
            "label" => __("Border", "skyweb-donation"),
            "selector" => "'{{WRAPPER}} .codexse-mobile-menu-wrapper .open-button:hover",
            'condition' => [
                'open_button_style_mode' => 'border',
                'open_button_style_mode!' => [ 'clean' ],
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();
        
        $this->add_responsive_control("open_button_font_size", [
            "label" => __("Icon Size", "codexse-addons"),
            "type" => Controls_Manager::SLIDER,
            "size_units" => ["px", "%", "vw"],
            "range" => [
                "px" => ["min" => 0, "max" => 1000, "step" => 1],
                "%" => ["min" => 0, "max" => 100],
            ],
            "selectors" => [
                "{{WRAPPER}} .codexse-mobile-menu-wrapper .open-button" => "font-size: {{SIZE}}{{UNIT}};",
            ],
            'condition' => [
                'open_icon_type' => 'icon',
                'open_button_style_mode' => 'clean',
            ],
        ]);
        $this->add_responsive_control("open_button_width", [
            "label" => __("Width", "codexse-addons"),
            "type" => Controls_Manager::SLIDER,
            "size_units" => ["px", "%", "vw"],
            "range" => [
                "px" => ["min" => 0, "max" => 1000, "step" => 1],
                "%" => ["min" => 0, "max" => 100],
            ],
            "selectors" => [
                "{{WRAPPER}} .codexse-mobile-menu-wrapper .open-button" => "width: {{SIZE}}{{UNIT}};",
            ],
        ]);
        $this->add_responsive_control("open_button_height", [
            "label" => __("Height", "codexse-addons"),
            "type" => Controls_Manager::SLIDER,
            "size_units" => ["px", "%", "vh"],
            "range" => [
                "px" => ["min" => 0, "max" => 1000, "step" => 1],
                "%" => ["min" => 0, "max" => 100],
            ],
            "selectors" => [
                "{{WRAPPER}} .codexse-mobile-menu-wrapper .open-button" => "height: {{SIZE}}{{UNIT}};",
            ],
        ]);

        $this->end_controls_section();
        
        // Close Button Section
        $this->start_controls_section(
            'close_button_section',
            [
                'label' => __( 'Close Button Settings', 'codexse-addons' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        // Close Button Icon Type
        $this->add_control(
            'close_icon_type',
            [
                'label' => __( 'Button Icon Type', 'codexse-addons' ),
                'description' => __( 'Choose the type of icon to display on the close button: an icon or an image.', 'codexse-addons' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'icon' => [
                        'title' => __( 'Use Icon', 'codexse-addons' ),
                        'icon' => 'eicon-star',
                    ],
                    'image' => [
                        'title' => __( 'Use Image', 'codexse-addons' ),
                        'icon' => 'eicon-image',
                    ],
                ],
                'default' => 'icon',
            ]
        );

        // Image Control (Close Button)
        $this->add_control(
            'close_icon_image',
            [
                'label' => __( 'Button Image', 'codexse-addons' ),
                'description' => __( 'Upload an image to use as the close button icon. This will replace the default icon.', 'codexse-addons' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'close_icon_type' => 'image'
                ],
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        // Image Size Control (for images)
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'close_icon_image_size',
                'default' => 'large',
                'separator' => 'none',
                'exclude' => [
                    'full',
                    'custom',
                    'large',
                    'shop_catalog',
                    'shop_single',
                    'shop_thumbnail'
                ],
                'condition' => [
                    'close_icon_type' => 'image'
                ],
            ]
        );

        // Icon Control (FontAwesome for Close Button)
        $this->add_control(
            'close_web_icon',
            [
                'label' => __( 'Button Icon', 'codexse-addons' ),
                'description' => __( 'Select an icon from the Font Awesome library to display on the close button.', 'codexse-addons' ),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
                'default' => [
                    'value' => 'fas fa-times',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'close_icon_type' => 'icon'
                ],
            ]
        );

        // Close Button Alignment
        $this->add_control(
            'close_button_alignment',
            [
                'label' => __('Button Alignment', 'codexse-addons'),
                'description' => __('Select the alignment for the close button (left, center, or right).', 'codexse-addons'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Align Left', 'codexse-addons'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Align Center', 'codexse-addons'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Align Right', 'codexse-addons'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'right',
                'selectors' => [
                    '{{WRAPPER}} .close-button-wrapper' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        // Button Style Mode for Close Button
        $this->add_control(
            'close_button_style_mode',
            [
                'label' => __('Button Style', 'codexse-addons'),
                'description' => __('Choose a style mode for the close button: flat, border, or clean.', 'codexse-addons'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'flat' => __('Flat', 'codexse-addons'),
                    'border' => __('Border', 'codexse-addons'),
                    'clean' => __('Clean', 'codexse-addons'),
                ],
                'default' => 'flat',
            ]
        );

        // Close Button Size
        $this->add_control(
            'close_button_size',
            [
                'label' => __('Button Size', 'codexse-addons'),
                'description' => __('Select a predefined size for the close button.', 'codexse-addons'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'small' => __('Small', 'codexse-addons'),
                    'medium' => __('Medium', 'codexse-addons'),
                    'large' => __('Large', 'codexse-addons'),
                    'extra_large' => __('Extra Large', 'codexse-addons'),
                ],
                'default' => 'medium',
                'condition' => [
                    'close_button_style_mode!' => [ 'clean' ],
                ],
            ]
        );

        // Button Shape for Close Button
        $this->add_control(
            'close_button_shape',
            [
                'label' => __('Button Shape', 'codexse-addons'),
                'description' => __('Define the shape of the close button (rectangle, round, or rounded).', 'codexse-addons'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'rectangle' => __('Rectangle', 'codexse-addons'),
                    'round' => __('Round', 'codexse-addons'),
                    'rounded' => __('Rounded', 'codexse-addons'),
                ],
                'default' => 'rectangle',
                'condition' => [
                    'close_button_style_mode!' => [ 'clean' ],
                ],
            ]
        );

        // Normal Tab for Close Button Style
        $this->start_controls_tabs('close_button_style_tabs');

        $this->start_controls_tab(
            'close_button_normal_tab',
            [
                'label' => __('Normal', 'skyweb-donation'),
            ]
        );

        // Button Text Color (Normal)
        $this->add_control(
            'close_button_color_normal',
            [
                'label' => __('Color', 'skyweb-donation'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-mobile-menu-wrapper .close-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Button Background Color (Normal)
        $this->add_control(
            'close_button_background_normal',
            [
                'label' => __('Background Color', 'skyweb-donation'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-mobile-menu-wrapper .close-button' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'close_button_style_mode' => 'flat',
                    'close_button_style_mode!' => [ 'clean' ],
                ],
            ]
        );

        // Border Control for Normal State
        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            "name" => "close_button_border",
            "label" => __("Border", "skyweb-donation"),
            "selector" => "'{{WRAPPER}} .codexse-mobile-menu-wrapper .close-button",
            'condition' => [
                'close_button_style_mode' => 'border',
                'close_button_style_mode!' => [ 'clean' ],
            ],
        ]);

        $this->end_controls_tab();

        // Hover Tab for Close Button Style
        $this->start_controls_tab(
            'close_button_hover_tab',
            [
                'label' => __('Hover', 'skyweb-donation'),
            ]
        );

        // Button Text Color (Hover)
        $this->add_control(
            'close_button_color_hover',
            [
                'label' => __('Color', 'skyweb-donation'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-mobile-menu-wrapper .close-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Button Background Color (Hover)
        $this->add_control(
            'close_button_background_hover',
            [
                'label' => __('Background Color', 'skyweb-donation'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-mobile-menu-wrapper .close-button:hover' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'close_button_style_mode!' => [ 'clean' ],
                ],
            ]
        );

        // Border Control for Hover State
        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            "name" => "close_button_hover_border",
            "label" => __("Border", "skyweb-donation"),
            "selector" => "'{{WRAPPER}} .codexse-mobile-menu-wrapper .close-button:hover",
            'condition' => [
                'close_button_style_mode' => 'border',
                'close_button_style_mode!' => [ 'clean' ],
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        // Close Button Size Control
        $this->end_controls_section();

        
        $this->start_controls_section(
            'social_menu_section',
            [
                'label' => __( 'Social Menu Settings', 'codexse-addons' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'social_switch',
            [
                'label' => __( 'Enable Social Menu', 'codexse-addons' ),
                'description' => __( 'Toggle to display or hide the social menu on your widget.', 'codexse-addons' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' => __( 'Show', 'codexse-addons' ),
                'label_off' => __( 'Hide', 'codexse-addons' ),
                'separator' => 'before',
            ]
        );
        
        // Social menu repeater
        $repeater = new Repeater();
        
        $repeater->add_control(
            'social_icon',
            [
                'label' => __( 'Social Media Icon', 'codexse-addons' ),
                'description' => __( 'Select an icon to represent the social media platform.', 'codexse-addons' ),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fab fa-facebook-f',
                    'library' => 'fa-brands',
                ],
            ]
        );
        
        $repeater->add_control(
            'social_url',
            [
                'label' => __( 'Social Media URL', 'codexse-addons' ),
                'description' => __( 'Enter the URL for the selected social media platform.', 'codexse-addons' ),
                'type' => Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com', 'codexse-addons' ),
                'default' => [
                    'url' => '',
                ],
            ]
        );
        
        $this->add_control(
            'social_menu',
            [
                'label' => __( 'Social Menu Items', 'codexse-addons' ),
                'description' => __( 'Add and customize social media icons with their respective links.', 'codexse-addons' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'social_icon' => 'fab fa-facebook-f',
                        'social_url' => [ 'url' => 'https://facebook.com' ],
                    ],
                    [
                        'social_icon' => 'fab fa-x-twitter',
                        'social_url' => [ 'url' => 'https://twitter.com' ],
                    ],
                ],
                'title_field' => '<i class="{{ social_icon.value }}"></i> {{ social_url.url }}',
                'condition' => ['social_switch' => 'yes'],
            ]
        );
        
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

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'navmenu_item_typo',
                'selector' => '{{WRAPPER}} .codexse-mobile-menu-items > li > a',
            ]
        );

        // Normal State: Color
        $this->add_control(
            'navmenu_item_color',
            [
                'label' => __( 'Text Color', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-mobile-menu-items > li > a' => 'color: {{VALUE}};',
                ],
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
                    '{{WRAPPER}} .codexse-mobile-menu-items > li:hover > a' => 'color: {{VALUE}};',
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
                    '{{WRAPPER}} .codexse-mobile-menu-items > li.active > a, {{WRAPPER}} .codexse-mobile-menu-items > li.current-menu-item > a' => 'color: {{VALUE}};',
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

        // Normal State Controls for Submenu
        $this->start_controls_tabs( 'submenu_item_tabs' );

        $this->start_controls_tab(
            'submenu_item_normal_tab',
            [
                'label' => __( 'Normal', 'codexse-addons' ),
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'submenu_item_typo',
                'selector' => '{{WRAPPER}} .codexse-mobile-menu-items li li a',
            ]
        );

        // Normal State: Color
        $this->add_control(
            'submenu_item_color',
            [
                'label' => __( 'Text Color', 'codexse-addons' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-mobile-menu-items li li a' => 'color: {{VALUE}};',
                ],
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
                    '{{WRAPPER}} .codexse-mobile-menu-items li li:hover > a' => 'color: {{VALUE}};',
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
                    '{{WRAPPER}} .codexse-mobile-menu-items li li.active > a, {{WRAPPER}} .codexse-mobile-menu-items li li.current-menu-item > a' => 'color: {{VALUE}};',
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
        $open_button_classes = $close_button_classes = '';
        $settings = $this->get_settings_for_display();
        $menu_id = $settings['selected_menu'];
        $social_links = $settings['social_menu'];
        $open_button_style = $settings['open_button_style_mode'];
        $open_button_size = $settings['open_button_size'];
        $open_button_shape = $settings['open_button_shape'];
        $close_button_style = $settings['close_button_style_mode'];
        $close_button_size = $settings['close_button_size'];
        $close_button_shape = $settings['close_button_shape'];
        $menu_style = $settings['menu_style'];


		$this->add_render_attribute( 'main_wrapper', 'class', 'codexse-mobile-menu-wrapper' );
		$this->add_render_attribute( 'open_button', 'class', 'mobile-toggle-button open-button' );
		$this->add_render_attribute( 'close_button', 'class', 'mobile-toggle-button close-button' );

        if(isset($menu_style)){
            $this->add_render_attribute( 'main_wrapper', 'class', 'menu-'.esc_attr($menu_style) );
        }



        if(isset($open_button_style)){
            $this->add_render_attribute( 'open_button', 'class', 'button-'.esc_attr($open_button_style) );
        }
        if(isset($open_button_size)){
            $this->add_render_attribute( 'open_button', 'class', 'button-'.esc_attr($open_button_size) );
        }
        if(isset($open_button_shape)){
            $this->add_render_attribute( 'open_button', 'class', 'button-'.esc_attr($open_button_shape) );
        }


        if(isset($close_button_style)){
            $this->add_render_attribute( 'close_button', 'class', 'button-'.esc_attr($close_button_style) );
        }
        if(isset($close_button_size)){
            $this->add_render_attribute( 'close_button', 'class', 'button-'.esc_attr($close_button_size) );
        }
        if(isset($close_button_shape)){
            $this->add_render_attribute( 'close_button', 'class', 'button-'.esc_attr($close_button_shape) );
        }

        echo '<div '.$this->get_render_attribute_string( 'main_wrapper' ).'>';

        // Open button

        echo '<div class="open-button-wrapper">';
            echo '<button '.$this->get_render_attribute_string( 'open_button' ).'>';
                if ( $settings['open_icon_type'] === 'image' && ( $settings['open_icon_image']['url'] || $settings['open_icon_image']['id'] ) ) :
                    echo '<span class="image-icon">';
                    echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'open_icon_image_size', 'open_icon_image' );
                    echo '</span>';
                    elseif ( ! empty( $settings['open_web_icon'] ) ) :
                    echo '<span class="web-icon">';
                    echo Codexse_Addons_Icon_manager::render_icon($settings['open_web_icon'], ["aria-hidden" => "true"]);
                    echo '</span>';
                endif;
            echo '</button>';
        echo '</div>';

        echo '<div class="codexse-mobile-menu">';
        if ( $menu_id ) {
            echo '<div class="close-button-wrapper">';
                echo '<button '.$this->get_render_attribute_string( 'close_button' ).'>';
                    if ( $settings['close_icon_type'] === 'image' && ( $settings['close_icon_image']['url'] || $settings['close_icon_image']['id'] ) ) :
                        echo '<span class="image-icon">';
                        echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'close_icon_image_size', 'close_icon_image' );
                        echo '</span>';
                        elseif ( ! empty( $settings['close_web_icon'] ) ) :
                        echo '<span class="web-icon">';
                        echo Codexse_Addons_Icon_manager::render_icon($settings['close_web_icon'], ["aria-hidden" => "true"]);
                        echo '</span>';
                    endif;
                echo '</button>';
            echo '</div>';
            wp_nav_menu([
                'menu' => $menu_id,
                'container' => false,
                'menu_class' => 'codexse-mobile-menu-items',
                'walker' => new Codexse_Addons_Mobile_Menu_Walker,
            ]);
        }
        

        // Social Links
        echo '<div class="codexse-social-links">';
        if ( $settings['social_switch'] == 'yes' && !empty( $social_links ) ) {
            foreach ( $social_links as $social ) {
                $icon = $social['social_icon'];
                $url = $social['social_url']['url'];

                if ( ! empty( $url ) && ! empty( $icon['value'] ) ) {
                    echo '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer" class="codexse-social-link">';
                    echo Codexse_Addons_Icon_manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
                    echo '</a>';
                }
            }
        }
        echo '</div>';
        
        echo '</div>';
        echo '</div>';
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
