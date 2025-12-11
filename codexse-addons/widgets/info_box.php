<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;

class Codexse_Addons_Elementor_Widget_Info_Box extends Widget_Base {

    public function get_name() {
        return 'codexse_info_box';
    }

    public function get_title() {
        return __( 'Info Box', 'codexse-addons' );
    }

    public function get_icon() {
        return 'cx-addons-icon  eicon-post-title';
    }

    public function get_categories() {
        return [ 'codexse-addons' ];
    }

    public function get_keywords() {
        return [ 'info box', 'icon box', 'blurb', 'image box', 'image', 'content box', 'box', 'elementor', 'widget', 'ui', 'codexse-addons', 'heading', 'title', 'text', 'description' ];
    }

    // Get style dependencies
    public function get_style_depends() {
        return [ 'codexse-info-box' ];
    }

    // Get script dependencies
    public function get_script_depends() {
        return [ 'lordicon' ];
    }
    
    protected function register_controls() {
        $this->content_controls();
		$this->lordicon_controls();
		$this->box_style_controls();
		$this->media_style_controls();
		$this->title_style_controls();
		$this->description_style_controls();
    }


    protected function content_controls() {
        $this->start_controls_section(
            'media_content_section',
            [
                'label' => __( 'Content', 'codexse-addons' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        // Media Type Selector (Icon, Image, LordIcon)
        $this->add_control(
            'media_type',
            [
                'label' => __( 'Media Type', 'codexse-addons' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'icon' => [
                        'title' => __( 'Icon', 'codexse-addons' ),
                        'icon' => 'eicon-star',
                    ],
                    'image' => [
                        'title' => __( 'Image', 'codexse-addons' ),
                        'icon' => 'eicon-image',
                    ],
                    'lordicon' => [
                        'title' => __( 'LordIcon', 'codexse-addons' ),
                        'icon' => 'eicon-lottie',
                    ],
                ],
                'default' => 'icon',
            ]
        );

        // LordIcon specific controls (method selection and CDN/JSON input)
        $this->add_control(
            'media_icon_method',
            [
                'type'        => Controls_Manager::SELECT,
                'label'       => __('Icon Method', 'codexse-addons'),
                'options'     => [
                    'cdn'  => esc_html__('Paste LordIcon URL', 'codexse-addons'),
                    'file' => esc_html__('Upload LordIcon file', 'codexse-addons'),
                ],
                'default'     => 'cdn',
                'label_block' => true,
                'condition'   => [
                    'media_type' => 'lordicon',
                ],
            ]
        );

        // CDN URL for LordIcon
        $this->add_control(
            'media_icon_cdn',
            [
                'type'        => Controls_Manager::TEXT,
                'label'       => __('Paste CDN URL', 'codexse-addons'),
                'label_block' => true,
                'description' => sprintf(
                    'Paste icon code from <a target="_blank" href="%1$s">lordicon.com</a> <br /><br /> <a target="_blank" href="%2$s">Learn how to get Lordicon CDN</a><br><br> Example: https://cdn.lordicon.com/lupuorrc.json', esc_url('https://lordicon.com/'), esc_url('https://lordicon.com/docs/web')
                ),
                'default'     => 'https://cdn.lordicon.com/lupuorrc.json',
                'condition'   => [
                    'media_icon_method' => 'cdn',
                    'media_type' => 'lordicon',
                ],
            ]
        );

        // Upload JSON File for LordIcon
        $this->add_control(
            'media_icon_json',
            [
                'type'        => Controls_Manager::MEDIA,
                'label'       => __('JSON File', 'codexse-addons'),
                'media_type'  => 'application/json',
                'description' => sprintf('Download JSON file from <a href="%1$s" target="_blank">lordicon.com</a>', esc_url('https://lordicon.com/')),
                'default'     => [
                    'url' => CODEXSE_IMAGES_URL . 'lord-icon/placeholder.json',
                ],
                'condition'   => [
                    'media_icon_method' => 'file',
                    'media_type' => 'lordicon',
                ],
            ]
        );

        // Image Control
        $this->add_control(
            'media_image',
            [
                'label' => __( 'Image', 'codexse-addons' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'media_type' => 'image'
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
                'name' => 'media_image_size',
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
                    'media_type' => 'image'
                ]
            ]
        );

        // Icon Control (FontAwesome)
        $this->add_control(
            'media_icons',
            [
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
                'default' => [
                    'value' => 'cx cx-box-open',
                    'library' => 'codexse-icon',
                ],
                'condition' => [
                    'media_type' => 'icon'
                ]
            ]
        );

        // Media Direction Control (Left or Top)
        $this->add_responsive_control(
            'media_direction',
            [
                'label' => __('Media direction', 'codexse-addons'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'codexse-addons'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'top' => [
                        'title' => __('Top', 'codexse-addons'),
                        'icon' => 'eicon-v-align-top',
                    ],
                ],
                'default' => 'top',
            ]
        );

        // Vertical Alignment Control (Top, Center, Bottom)
        $this->add_responsive_control(
            'media_v_align',
            [
                'label' => __('Vertical Alignment', 'codexse-addons'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'top' => [
                        'title' => __('Top', 'codexse-addons'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => __('Center', 'codexse-addons'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'bottom' => [
                        'title' => __('Bottom', 'codexse-addons'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'top',
                'condition' => [
                    'media_direction' => 'left',
                ],
            ]
        );

		$this->add_control(
			'box_title',
			[
				'label' => __( 'Title', 'codexse-addons' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Info Box Title', 'codexse-addons' ),
				'placeholder' => __( 'Type Info Box Title', 'codexse-addons' ),
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$this->add_control(
			'box_description',
			[
				'label' => __( 'Description', 'codexse-addons' ),
				'description' => '',
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'info box description goes here', 'codexse-addons' ),
				'placeholder' => __( 'Type info box description', 'codexse-addons' ),
				'rows' => 5,
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label' => __( 'Title HTML Tag', 'codexse-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'h1'  => [
						'title' => __( 'H1', 'codexse-addons' ),
						'icon' => 'eicon-editor-h1'
					],
					'h2'  => [
						'title' => __( 'H2', 'codexse-addons' ),
						'icon' => 'eicon-editor-h2'
					],
					'h3'  => [
						'title' => __( 'H3', 'codexse-addons' ),
						'icon' => 'eicon-editor-h3'
					],
					'h4'  => [
						'title' => __( 'H4', 'codexse-addons' ),
						'icon' => 'eicon-editor-h4'
					],
					'h5'  => [
						'title' => __( 'H5', 'codexse-addons' ),
						'icon' => 'eicon-editor-h5'
					],
					'h6'  => [
						'title' => __( 'H6', 'codexse-addons' ),
						'icon' => 'eicon-editor-h6'
					]
				],
				'default' => 'h3',
				'toggle' => false,
			]
		);

		$this->add_responsive_control(
			'title_description_align',
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
						'title' => __( 'Justify', 'codexse-addons' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'toggle' => true,
				'condition' => [
					'media_direction' => 'top',
				],
				'selectors' => [
					'{{WRAPPER}} .codexse-info-box' => 'text-align: {{VALUE}};'
				]
			]
		);

		$this->end_controls_section();
	}


	protected function lordicon_controls(){
		$this->start_controls_section(
			'_section_lordicon_controls',
			[
				'label' => __( 'Lord Icon Settings', 'codexse-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
				'condition' =>[
					'media_type' => 'lordicon'
				]
			]
		);

		$this->add_control(
            'animation_trigger',
            [
                'type'    => Controls_Manager::SELECT,
                'label'   => __('Animation Trigger', 'codexse-addons'),
                'options' => [
                    'loop'          => esc_html__('Loop (infinite)', 'codexse-addons'),
                    'click'         => esc_html__('Click', 'codexse-addons'),
                    'hover'         => esc_html__('Hover', 'codexse-addons'),
                    'loop-on-hover' => esc_html__('Loop on Hover', 'codexse-addons'),
                    'morph'         => esc_html__('Morph', 'codexse-addons'),
                    'morph-two-way' => esc_html__('Morph two way', 'codexse-addons'),
                ],
                'default' => 'loop',
            ]
        );

        $this->add_responsive_control(
            'lord_icon_size',
            [
                'label'   => __('Size', 'codexse-addons'),
                'type'    => Controls_Manager::SLIDER,
                // 'size_units' => [ 'px' ],
                'range'   => [
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                ],
                'default' => [
                    'size' => 150,
                ],
            ]
        );

        $this->add_control(
            'primary_color',
            [
                'label'   => __('Primary Color', 'codexse-addons'),
                'type'    => Controls_Manager::COLOR,
                'default' => '#121331',
                'render_type' => 'template',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'secondary_color',
            [
                'label'   => __('Secondary Color', 'codexse-addons'),
                'type'    => Controls_Manager::COLOR,
                'default' => '#08a88a',
            ]
        );

        $this->add_control(
            'tertiary_color',
            [
                'label'   => __('Tertiary Color', 'codexse-addons'),
                'type'    => Controls_Manager::COLOR,
                'default' => '#0816A8',
            ]
        );

        $this->add_control(
            'quaternary_color',
            [
                'label'   => __('Quaternary Color', 'codexse-addons'),
                'type'    => Controls_Manager::COLOR,
                'default' => '#2CA808',
            ]
        );

        $this->add_control(
            'lord_icon_stroke',
            [
                'label'   => __('Stroke', 'codexse-addons'),
                'type'    => Controls_Manager::SLIDER,
                'range'   => [
                    'min' => 1,
                    'max' => 500,
                ],
                'default' => [
                    'size' => '20',
                ],
            ]
        );
		$this->end_controls_section();
	}


	protected function box_style_controls() {
		$this->start_controls_section(
			'box_style_section',
			[
				'label' => __( 'Info Box', 'codexse-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

        $this->start_controls_tabs("info_box_style_tab");
        $this->start_controls_tab("info_box_normal", [
            "label" => __("Normal", "codexse-addons"),
        ]);

        $this->add_group_control(Group_Control_Background::get_type(), [
            "name" => "info_box_background",
            "label" => __("Background", "codexse-addons"),
            "types" => ["classic", "gradient"],
            "selector" => "{{WRAPPER}} .codexse-info-box",
        ]);

        $this->add_responsive_control("box_width", [
            "label" => __("Width", "codexse-addons"),
            "type" => Controls_Manager::SLIDER,
            "size_units" => ["px", "%", "vw"],
            "range" => [
                "px" => ["min" => 0, "max" => 1000, "step" => 1],
                "%" => ["min" => 0, "max" => 100],
            ],
            "selectors" => [
                "{{WRAPPER}} .codexse-info-box" => "width: {{SIZE}}{{UNIT}};",
            ],
        ]);
        $this->add_responsive_control("box_height", [
            "label" => __("Height", "codexse-addons"),
            "type" => Controls_Manager::SLIDER,
            "size_units" => ["px", "%", "vh"],
            "range" => [
                "px" => ["min" => 0, "max" => 1000, "step" => 1],
                "%" => ["min" => 0, "max" => 100],
            ],
            "selectors" => [
                "{{WRAPPER}} .codexse-info-box" => "height: {{SIZE}}{{UNIT}};",
            ],
        ]);
        
        $this->add_responsive_control("info_box_margin", [
            "label" => __("Margin", "codexse-addons"),
            "type" => Controls_Manager::DIMENSIONS,
            "size_units" => ["px", "%", "em"],
            "selectors" => [
                "{{WRAPPER}} .codexse-info-box" =>
                    "margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
            ],
            "separator" => "before",
        ]);

        $this->add_responsive_control("info_box_padding", [
            "label" => __("Padding", "codexse-addons"),
            "type" => Controls_Manager::DIMENSIONS,
            "size_units" => ["px", "%", "em"],
            "selectors" => [
                "{{WRAPPER}} .codexse-info-box" =>
                    "padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
            ],
            "separator" => "before",
        ]);

        $this->add_group_control(Group_Control_Border::get_type(), [
            "name" => "info_box_border",
            "label" => __("Border", "codexse-addons"),
            "selector" => "{{WRAPPER}} .codexse-info-box",
        ]);
        $this->add_responsive_control("info_box_border_radius", [
            "label" => esc_html__("Border Radius", "codexse-addons"),
            "type" => Controls_Manager::DIMENSIONS,
            "selectors" => [
                "{{WRAPPER}} .codexse-info-box" =>
                    "border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;",
            ],
        ]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            "name" => "info_box_shadow",
            "label" => __("Box Shadow", "codexse-addons"),
            "selector" => "{{WRAPPER}} .codexse-info-box",
        ]);

        $this->add_control("info_box_transform", [
            "label" => __("Transform", "codexse-addons"),
            "type" => Controls_Manager::TEXT,
            "default" => "translateY(0)",
            "selectors" => [
                "{{WRAPPER}} .codexse-info-box" => "transform: {{VALUE}}",
            ],
        ]);

        $this->add_control("info_box_transition", [
            "label" => __("Transition Duration", "codexse-addons"),
            "type" => Controls_Manager::SLIDER,
            "default" => ["size" => 0.3],
            "range" => ["px" => ["max" => 3, "step" => 0.1]],
            "selectors" => [
                "{{WRAPPER}} .codexse-info-box" =>
                    "transition-duration: {{SIZE}}s",
            ],
        ]);
        $this->end_controls_tab();

        // Hover Style tab Start
        $this->start_controls_tab("info_box_hover", [
            "label" => __("Hover", "codexse-addons"),
        ]);

        $this->add_group_control(Group_Control_Background::get_type(), [
            "name" => "info_box_hover_background",
            "label" => __("Background", "codexse-addons"),
            "types" => ["classic", "gradient"],
            "selector" => "{{WRAPPER}} .codexse-info-box:hover",
        ]);

        $this->add_group_control(Group_Control_Border::get_type(), [
            "name" => "info_box_hover_border",
            "label" => __("Border", "codexse-addons"),
            "selector" => "{{WRAPPER}} .codexse-info-box:hover",
        ]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            "name" => "info_box_hover_shadow",
            "label" => __("Box Shadow", "codexse-addons"),
            "selector" => "{{WRAPPER}} .codexse-info-box:hover",
        ]);
        $this->add_control("info_box_hover_transform", [
            "label" => __("Transform", "codexse-addons"),
            "type" => Controls_Manager::TEXT,
            "default" => "translateY(0)",
            "selectors" => [
                "{{WRAPPER}} .codexse-info-box:hover" => "transform: {{VALUE}}",
            ],
        ]);
        $this->end_controls_tab(); // Hover Style tab end
        $this->end_controls_tabs(); // Box Style tabs end
		$this->end_controls_section();
	}
    


	protected function media_style_controls() {
		$this->start_controls_section(
			'media_style_section',
			[
				'label' => __( 'Icon / Image', 'codexse-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);


        $this->start_controls_tabs("codexse_box_icon_style_tab");

        $this->start_controls_tab("codexse_box_icon_normal", [
            "label" => __("Normal", "codexse"),
        ]);

        $this->add_responsive_control("codexse_box_icon_width", [
            "label" => __("Width", "codexse"),
            "type" => Controls_Manager::SLIDER,
            "size_units" => ["px", "%"],
            "range" => [
                "px" => ["min" => 0, "max" => 1000, "step" => 1],
                "%" => ["min" => 0, "max" => 100],
            ],
            "selectors" => [
                "{{WRAPPER}} .codexse-info-box .box-icon" =>
                    "width: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};",
            ],
        ]);

        $this->add_responsive_control("codexse_box_icon_height", [
            "label" => __("Height", "codexse"),
            "type" => Controls_Manager::SLIDER,
            "size_units" => ["px", "%"],
            "range" => [
                "px" => ["min" => 0, "max" => 1000, "step" => 1],
                "%" => ["min" => 0, "max" => 100],
            ],
            "selectors" => [
                "{{WRAPPER}} .codexse-info-box .box-icon" => "height: {{SIZE}}{{UNIT}};",
            ],
        ]);
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'codexse_box_icon_typorgraphy',
                'selector' => '{{WRAPPER}} .codexse-info-box .box-icon',
            ]
        );

        $this->add_control("codexse_box_icon_color", [
            "label" => __("Color", "codexse"),
            "type" => Controls_Manager::COLOR,
            "selectors" => ["{{WRAPPER}} .codexse-info-box .box-icon" => "color: {{VALUE}};"],
        ]);

        $this->add_group_control(Group_Control_Background::get_type(), [
            "name" => "codexse_box_icon_background",
            "label" => __("Background", "codexse"),
            "types" => ["classic", "gradient"],
            "selector" => "{{WRAPPER}} .codexse-info-box .box-icon",
        ]);

        $this->add_responsive_control("codexse_box_icon_margin", [
            "label" => __("Margin", "codexse"),
            "type" => Controls_Manager::DIMENSIONS,
            "size_units" => ["px", "%", "em"],
            "selectors" => [
                "{{WRAPPER}} .codexse-info-box .box-icon" =>
                    "margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
            ],
            "separator" => "before",
        ]);

        $this->add_responsive_control("codexse_box_icon_padding", [
            "label" => __("Padding", "codexse"),
            "type" => Controls_Manager::DIMENSIONS,
            "size_units" => ["px", "%", "em"],
            "selectors" => [
                "{{WRAPPER}} .codexse-info-box .box-icon" =>
                    "padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
            ],
            "separator" => "before",
        ]);
        $this->add_group_control(Group_Control_Border::get_type(), [
            "name" => "codexse_box_icon_border",
            "label" => __("Border", "codexse"),
            "selector" => "{{WRAPPER}} .codexse-info-box .box-icon",
        ]);
        $this->add_responsive_control("codexse_box_icon_border_radius", [
            "label" => esc_html__("Border Radius", "codexse"),
            "type" => Controls_Manager::DIMENSIONS,
            "size_units" => ["px", "%", "em"],
            "range" => [
                "px" => ["min" => 0, "max" => 1000, "step" => 1],
                "%" => ["min" => 0, "max" => 100],
            ],
            "selectors" => [
                "{{WRAPPER}} .codexse-info-box .box-icon" =>
                    "border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
            ],
        ]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            "name" => "codexse_box_icon_shadow",
            "label" => __("Box Shadow", "codexse"),
            "selector" => "{{WRAPPER}} .codexse-info-box .box-icon",
        ]);
        $this->add_control("codexse_box_icon_transition", [
            "label" => __("Transition Duration", "codexse"),
            "type" => Controls_Manager::SLIDER,
            "default" => ["size" => 0.3],
            "range" => ["px" => ["max" => 3, "step" => 0.1]],
            "selectors" => [
                "{{WRAPPER}} .codexse-info-box .box-icon" => "transition-duration: {{SIZE}}s",
            ],
        ]);

        $this->add_control("codexse_box_icon_transform", [
            "label" => __("Transform", "codexse"),
            "type" => Controls_Manager::TEXT,
            "default" => "translateY(0)",
            "selectors" => [
                "{{WRAPPER}} .codexse-info-box .box-icon" => "transform: {{VALUE}}",
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
                "{{WRAPPER}} .codexse-info-box .box-icon:hover" => "color: {{VALUE}};",
            ],
        ]);
        $this->add_group_control(Group_Control_Background::get_type(), [
            "name" => "codexde_icon_hover_background",
            "label" => __("Background", "codexse"),
            "types" => ["classic", "gradient"],
            "selector" => "{{WRAPPER}} .codexse-info-box .box-icon:hover",
        ]);
        $this->add_group_control(Group_Control_Border::get_type(), [
            "name" => "codexde_icon_hover_border",
            "label" => __("Border", "codexse"),
            "selector" => "{{WRAPPER}} .codexse-info-box .box-icon:hover",
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
                "{{WRAPPER}} .codexse-info-box .box-icon:hover" =>
                    "border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
            ],
        ]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            "name" => "codexde_icon_hover_shadow",
            "label" => __("Box Shadow", "codexse"),
            "selector" => "{{WRAPPER}} .codexse-info-box .box-icon:hover",
        ]);

        $this->end_controls_tab(); // Hover Style tab end
        $this->start_controls_tab("box_hover_icon", [
            "label" => __("Box Hover", "codexse"),
        ]);
        $this->add_control("codexse_hover_icon_color", [
            "label" => __("Hover Color", "codexse"),
            "type" => Controls_Manager::COLOR,
            "selectors" => [
                "{{WRAPPER}} .codexse-info-box:hover .box-icon" => "color: {{VALUE}};",
            ],
        ]);
        $this->add_group_control(Group_Control_Background::get_type(), [
            "name" => "codexse_hover_icon_background",
            "label" => __("Background", "codexse"),
            "types" => ["classic", "gradient"],
            "selector" => "{{WRAPPER}} .codexse-info-box:hover .box-icon",
        ]);
        $this->add_group_control(Group_Control_Border::get_type(), [
            "name" => "codexse_hover_icon_border",
            "label" => __("Border", "codexse"),
            "selector" => "{{WRAPPER}} .codexse-info-box:hover .box-icon",
        ]);
        $this->add_responsive_control("codexse_hover_icon_radius", [
            "label" => esc_html__("Border Radius", "codexse"),
            "type" => Controls_Manager::DIMENSIONS,
            "size_units" => ["px", "%", "em"],
            "range" => [
                "px" => ["min" => 0, "max" => 1000, "step" => 1],
                "%" => ["min" => 0, "max" => 100],
            ],
            "selectors" => [
                "{{WRAPPER}} .codexse-info-box:hover .box-icon" =>
                    "border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
            ],
        ]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            "name" => "codexse_hover_icon_shadow",
            "label" => __("Box Shadow", "codexse"),
            "selector" => "{{WRAPPER}} .codexse-info-box:hover .box-icon",
        ]);


        $this->add_control("codexse_hover_icon_transform", [
            "label" => __("Transform", "codexse"),
            "type" => Controls_Manager::TEXT,
            "default" => "translateY(0)",
            "selectors" => [
                "{{WRAPPER}} .codexse-info-box:hover .box-icon" => "transform: {{VALUE}}",
            ],
        ]);

        $this->end_controls_tab(); // Hover Style tab end
        $this->end_controls_tabs(); // Box Style tabs end

		$this->end_controls_section();
	}
    


	protected function title_style_controls() {
		$this->start_controls_section(
			'codexse_box_title_style_section',
			[
				'label' => __( 'Title', 'codexse-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);


        $this->start_controls_tabs('codexse_box_title_tabs');
        
        $this->start_controls_tab( 'codexse_box_title_normal',
			[
				'label' => __( 'Normal', 'codexse-addons' ),
			]
		);        
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'codexse_box_title_typography',
                'selector' => '{{WRAPPER}} .codexse-info-box .box-title',
            ]
        );
        $this->add_control(
            'codexse_box_title_color',
            [
                'label' => __( 'Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-info-box .box-title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'codexse_box_title_margin',
            [
                'label' => __( 'Margin', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-info-box .box-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'codexse_box_title_padding',
            [
                'label' => __( 'Padding', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-info-box .box-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
                
		$this->add_control(
			'codexse_box_title_transition',
			[
				'label' => __( 'Transition Duration', 'codexse-addons' ),
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
					'{{WRAPPER}} .codexse-info-box .box-title' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
        
        $this->end_controls_tab(); // Hover Style tab end
         
        $this->start_controls_tab( 'codexse_box_title_hover_tab',
			[
				'label' => __( 'Hover', 'codexse-addons' ),
			]
		);        
        
        $this->add_control(
            'codexse_box_title_hover_color',
            [
                'label' => __( 'Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-info-box .box-title:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->start_controls_tab( 'codexse_box_hover_title_tab',
			[
				'label' => __( 'Box Hover', 'codexse-addons' ),
			]
		);        
        
        $this->add_control(
            'codexse_box_hover_title_color',
            [
                'label' => __( 'Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-info-box:hover .box-title' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->end_controls_tabs();// Box Style tabs end  
		$this->end_controls_section();
	}

    protected function description_style_controls(){
		$this->start_controls_section(
			'codexse_box_description_style_section',
			[
				'label' => __( 'Description', 'codexse-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);


        $this->start_controls_tabs('codexse_box_description_tabs');
        
        $this->start_controls_tab( 'codexse_box_description_normal',
			[
				'label' => __( 'Normal', 'codexse-addons' ),
			]
		);        
        
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'codexse_box_description_typography',
                'selector' => '{{WRAPPER}} .codexse-info-box .box-description',
            ]
        );
        $this->add_control(
            'codexse_box_description_color',
            [
                'label' => __( 'Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-info-box .box-description' => 'color: {{VALUE}};',
                ],
            ]
        );
                
        $this->add_responsive_control(
            'codexse_box_description_margin',
            [
                'label' => __( 'Margin', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-info-box .box-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'codexse_box_description_padding',
            [
                'label' => __( 'Padding', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-info-box .box-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
		$this->add_control(
			'codexse_box_description_transition',
			[
				'label' => __( 'Transition Duration', 'codexse-addons' ),
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
					'{{WRAPPER}} .codexse-info-box .box-description' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
        
        $this->end_controls_tab(); // Hover Style tab end
         
        $this->start_controls_tab( 'codexse_box_description_hover_tab',
			[
				'label' => __( 'Hover', 'codexse-addons' ),
			]
		);        
        
        $this->add_control(
            'codexse_box_description_hover_color',
            [
                'label' => __( 'Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-info-box .box-description:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->start_controls_tab( 'codexse_box_hover_description_tab',
			[
				'label' => __( 'Box Hover', 'codexse-addons' ),
			]
		);        
        
        $this->add_control(
            'codexse_box_hover_description_color',
            [
                'label' => __( 'Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-info-box:hover .box-description' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->end_controls_tabs();// Box Style tabs end 

		$this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        // Get Lordicon URL
        if ( 'lordicon' === $settings['media_type'] ) {
            if ( 'file' === $settings['media_icon_method'] ) {
                $json_url = $settings['media_icon_json']['url'];
            } else {
                $json_url = $settings['media_icon_cdn'];
            }
        }

        // Direction and vertical alignment classes
        $media_direction = $settings['media_direction'];
        $media_v_align   = $settings['media_v_align'];

        // Add wrapper classes
        $this->add_render_attribute('info_box_wrapper', 'class', 'codexse-info-box');
        $this->add_render_attribute('info_box_wrapper', 'class', 'media-direction-' . $media_direction);

        if ( $media_direction === 'left' ) {
            $this->add_render_attribute('info_box_wrapper', 'class', 'media-align-' . $media_v_align);
        }

        // Add inline editing attributes
        $this->add_inline_editing_attributes( 'box_title', 'basic' );
        $this->add_render_attribute( 'box_title', 'class', 'box-title' );

        $this->add_inline_editing_attributes( 'box_description', 'intermediate' );
        $this->add_render_attribute( 'box_description', 'class', 'box-description' );

        // Start wrapper
        echo '<div ' . $this->get_render_attribute_string('info_box_wrapper') . '>';

            // Media icon/image/lordicon
            if ( $settings['media_type'] === 'media_image' && ( $settings['media_image']['url'] || $settings['media_image']['id'] ) ) {
                echo '<div class="box-icon image-icon">';
                echo \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings, 'media_thumbnail', 'media_image' );
                echo '</div>';
            } elseif ( 'lordicon' === $settings['media_type'] && ! empty( $json_url ) ) {
                echo '<div class="box-icon loard-icon">';
                echo '<lord-icon 
                        src="' . esc_url( $json_url ) . '" 
                        trigger="' . esc_attr( $settings['animation_trigger'] ) . '" 
                        stroke="' . esc_attr( $settings['lord_icon_stroke']['size'] ) . '" 
                        colors="primary:' . esc_attr( $settings['primary_color'] ) . ',
                                secondary:' . esc_attr( $settings['secondary_color'] ) . ',
                                tertiary:' . esc_attr( $settings['tertiary_color'] ) . ',
                                quaternary:' . esc_attr( $settings['quaternary_color'] ) . '" 
                        style="width:' . esc_attr( $settings['lord_icon_size']['size'] ) . 'px; height:' . esc_attr( $settings['lord_icon_size']['size'] ) . 'px">
                    </lord-icon>';
                echo '</div>';
            } elseif ( ! empty( $settings['media_icons'] ) ) {
                echo '<div class="box-icon web-icon">';
                echo \Codexse_Addons_Icon_manager::render_icon( $settings['media_icons'], [ "aria-hidden" => "true" ] );
                echo '</div>';
            }


            echo '<div class="box-content">';
                // Title
                if ( ! empty( $settings['box_title'] ) ) {
                    echo '<' . esc_html( $settings['title_tag'] ) . ' ' . $this->get_render_attribute_string( 'box_title' ) . '>';
                        echo wp_kses_post( $settings['box_title'] );
                    echo '</' . esc_html( $settings['title_tag'] ) . '>';
                }

                // Description
                if ( ! empty( $settings['box_description'] ) ) {
                    echo '<div ' . $this->get_render_attribute_string( 'box_description' ) . '>';
                        echo wp_kses_post( $settings['box_description'] );
                    echo '</div>';
                }  
            echo '</div>'; // End box-content

        echo '</div>'; // End wrapper
    }

    
}
