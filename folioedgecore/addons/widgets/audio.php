<?php
namespace Elementor;

// Elementor Classes
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Folioedge_Audio_Player extends Widget_Base {

    public function get_name() {
        return "Folioedge_Audio_Player";
    }

    public function get_title() {
        return esc_html__( "Audio Player", 'folioedgecore' );
    }

    public function get_icon() {
        return 'folioedge-icon eicon-headphones';
    }

    public function get_categories() {
        return array( 'folioedgecore' );
    }
    
    public function get_script_depends() {
        return [
            'plyr',
            'polyfilled',
            'addons-active',
        ];
    }

	public function get_keywords() {
		return [ 'audio', 'music', 'player' ];
	}

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'General Options', 'folioedgecore' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'src_type',
            [
                'label' => esc_html__( 'Audio Source', 'folioedgecore' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'upload',
                'options' => [
                    'upload' => esc_html__( 'Upload Audio', 'folioedgecore' ),
                    'link' => esc_html__( 'Audio Link', 'folioedgecore' ),
                ],
            ]
        );

        $this->add_control(
            'audio_upload',
            array(
                'label' => esc_html__( 'Upload Audio', 'folioedgecore' ),
                'type'  => Controls_Manager::MEDIA,
                'media_type' => 'audio',
                'condition' => array(
                    'src_type' => 'upload',
                ),
            )
        );

        $this->add_control(
            'audio_link',
            [
                'label' => esc_html__( 'Audio Link', 'folioedgecore' ),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__( 'https://example.com/music-name.mp3', 'folioedgecore' ),
                'show_external' => false,
                'default' => [
                    'url' => '',
                    'is_external' => false,
                    'nofollow' => false,
                ],
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'src_type'    =>  'link',
                ]
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => esc_html__( 'Autoplay', 'folioedgecore' ),
                'type' => Controls_Manager::SWITCHER,
                'description' => __('Note: Mobile browsers don’t allow autoplay for Audio. Some desktop or laptop browsers also automatically block videos from automatically playing or may automatically mute the audio.', 'folioedgecore'),
                'label_on' => esc_html__( 'Yes', 'folioedgecore' ),
                'label_off' => esc_html__( 'No', 'folioedgecore' ),
                'return_value' => 'true',
                'default' => '',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'muted',
            [
                'label' => esc_html__( 'Muted', 'folioedgecore' ),
                'type' => Controls_Manager::SWITCHER,
                'description' => esc_html__('Enable this to start playback muted. This is also usefull if you experience autoplay is not working from your browser.', 'folioedgecore'),
                'label_on' => esc_html__( 'Yes', 'folioedgecore' ),
                'label_off' => esc_html__( 'No', 'folioedgecore' ),
                'return_value' => 'true',
                'default' => '',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'loop',
            [
                'label' => esc_html__( 'Loop', 'folioedgecore' ),
                'type' => Controls_Manager::SWITCHER,
                'description' => esc_html__('Loop the current media. ', 'folioedgecore'),
                'label_on' => esc_html__( 'Yes', 'folioedgecore' ),
                'label_off' => esc_html__( 'No', 'folioedgecore' ),
                'return_value' => 'true',
                'default' => '',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'invert_time',
            [
                'label' => esc_html__( 'Display Time As Countdown', 'folioedgecore' ),
                'type' => Controls_Manager::SWITCHER,
                'description' => esc_html__('Display the current time as a countdown rather than an incremental counter.', 'folioedgecore'),
                'label_on' => esc_html__( 'Yes', 'folioedgecore' ),
                'label_off' => esc_html__( 'No', 'folioedgecore' ),
                'return_value' => 'true',
                'default' => 'true',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'seek_time',
            [
                'label' => esc_html__( 'Seek Time', 'folioedgecore' ),
                'type' => Controls_Manager::NUMBER,
                'description' => esc_html__('The time, in seconds, to seek when a user hits fast forward or rewind.', 'folioedgecore'),
                'min' => 5,
                'max' => 100,
                'step' => 1,
                'default' => 10,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'tooltips_seek',
            [
                'label' => esc_html__( 'Display Seek Tooltip', 'folioedgecore' ),
                'type' => Controls_Manager::SWITCHER,
                'description' => esc_html__('Display a seek tooltip to indicate on click where the media would seek to.', 'folioedgecore'),
                'label_on' => esc_html__( 'Yes', 'folioedgecore' ),
                'label_off' => esc_html__( 'No', 'folioedgecore' ),
                'return_value' => 'true',
                'default' => 'true',
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'speed_selected',
            [
                'label' => esc_html__( 'Initial Speed', 'folioedgecore' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'speed_1',
                'options' => [
                    'speed_.5'  => esc_html__( '0.5', 'folioedgecore' ),
                    'speed_.75' => esc_html__( '0.75', 'folioedgecore' ),
                    'speed_1' => esc_html__( '1', 'folioedgecore' ),
                    'speed_1.25' => esc_html__( '1.25', 'folioedgecore' ),
                    'speed_1.5' => esc_html__( '1.5', 'folioedgecore' ),
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'preload',
            [
                'label' => esc_html__( 'Preload', 'folioedgecore' ),
                'description' => __( 'Specifies how the the audio should be loaded when the page loads. <a target="_blank" href="https://www.w3schools.com/tags/att_audio_preload.asp">Learn More</a>', 'folioedgecore' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'upload',
                'options' => [
                    'auto' => esc_html__( 'Auto', 'folioedgecore' ),
                    'metadata' => esc_html__( 'Metadata', 'folioedgecore' ),
                    'none' => esc_html__( 'None', 'folioedgecore' ),
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'controls',
            [
                'label' => esc_html__( 'Control Options', 'folioedgecore' ),
                'type' => Controls_Manager::SELECT2,
                'description'   =>  esc_html__('Add/Remove your prefered audio control options'),
                'multiple' => true,
                'options' => [
                    'play' => esc_html__( 'Play Icon', 'folioedgecore' ),
                    'progress' => esc_html__( 'Progress Bar', 'folioedgecore' ),
                    'mute' => esc_html__( 'Mute Icon', 'folioedgecore' ),
                    'volume' => esc_html__( 'Volume Bar', 'folioedgecore' ),
                    'settings' => esc_html__( 'Settings Icon', 'folioedgecore' ),
                    'airplay' => esc_html__( 'Airplay Icon', 'folioedgecore' ),
                    'download' => esc_html__( 'Download Button', 'folioedgecore' ),
                ],
                'default' => [ 'play', 'progress', 'mute', 'volume', 'settings' ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'debug_section',
            [
                'label' => esc_html__( 'Debugging', 'folioedgecore' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

            $this->add_control(
                'debug_mode',
                [
                    'label' => esc_html__( 'Debug Mode', 'folioedgecore' ),
                    'type' => Controls_Manager::SWITCHER,
                    'description' => esc_html__('Enable it when the player does not work properly. When debug is enable, the browser will show the informations about this player in the browser console. This is helpful for developer.', 'folioedgecore'),
                    'label_on' => esc_html__( 'Yes', 'folioedgecore' ),
                    'label_off' => esc_html__( 'No', 'folioedgecore' ),
                    'return_value' => 'true',
                    'default' => 'false',
                ]
            );

        $this->end_controls_section();

        // Feature Style tab section
        $this->start_controls_section(
            'box_icon_section',
            [
                'label' => __( 'Play Icon', 'folioedgecore' ),
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
					'{{WRAPPER}} .plyr__control[data-plyr="play"]' => 'width: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .plyr__control[data-plyr="play"]' => 'height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .plyr__control[data-plyr="play"]' => 'line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
        $this->add_responsive_control(
            'icon_size',
            [
                'label' => __( 'Size', 'folioedgecore' ),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .plyr__control[data-plyr="play"]' => 'font-size: {{SIZE}}{{UNIT}};',
                ],           
                'condition' => [
                    'icon_type!' => 'text',
                ]
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'icon_typography',
                'selector' => '{{WRAPPER}} .plyr__control[data-plyr="play"]',                
                'condition' => [
                    'icon_type' => 'text',
                ]
            ]
        );
        
        $this->add_control(
            'icon_color',
            [
                'label' => __( 'Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .plyr__control[data-plyr="play"]' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'icon_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .plyr__control[data-plyr="play"]',
            ]
        );
        $this->add_responsive_control(
            'icon_alignment',
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
                    '{{WRAPPER}} .plyr__control[data-plyr="play"]' => 'text-align: {{VALUE}}',
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
                    '{{WRAPPER}} .plyr__control[data-plyr="play"]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .plyr__control[data-plyr="play"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'icon_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .plyr__control[data-plyr="play"]',
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
                    '{{WRAPPER}} .plyr__control[data-plyr="play"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'icon_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .plyr__control[data-plyr="play"]',
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
					'{{WRAPPER}} .plyr__control[data-plyr="play"]' => 'transition-duration: {{SIZE}}s',
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
                    '{{WRAPPER}} .plyr__control[data-plyr="play"]:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'hover_icon_background',
                'label' => __( 'Background', 'folioedgecore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .plyr__control[data-plyr="play"]:hover',
            ]
        );               
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'hover_icon_border',
                'label' => __( 'Border', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .plyr__control[data-plyr="play"]:hover',
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
                    '{{WRAPPER}} .plyr__control[data-plyr="play"]:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'hover_icon_shadow',
                'label' => __( 'Box Shadow', 'folioedgecore' ),
                'selector' => '{{WRAPPER}} .plyr__control[data-plyr="play"]:hover',
            ]
        );        
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section();
                
        $this->start_controls_section(
            'styling_progress_bar_section',
            [
                'label'     => esc_html__( 'Seek Progress Bar', 'folioedgecore' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );
            // pbar_pointer_color
            $this->add_control(
                'pbar_pointer_color',
                [
                    'label' => esc_html__( 'Bar Pointer Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .plyr__progress__container input[type=range]::-webkit-slider-thumb' => 'background:{{VALUE}}',
                        '{{WRAPPER}} .plyr__progress__container input[type=range]::-moz-range-thumb' => 'background:{{VALUE}}',
                        '{{WRAPPER}} .plyr__progress__container input[type=range]::-ms-thumb' => 'background:{{VALUE}}',
                    ],
                ]
            );

            // pbar_color
            $this->add_control(
                'pbar_color_1',
                [
                    'label' => esc_html__( 'Bar Color 1', 'folioedgecore' ),
                    'desc'  => esc_html__( 'Use RGB color with some opacity. E.g: rgba(255,68,115,0.60). Otherwise buffer color will now show.', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .plyr__progress input[type=range]::-webkit-slider-runnable-track' => 'background-color:{{VALUE}}',
                        '{{WRAPPER}} .plyr__progress input[type=range]::-moz-range-track' => 'background-color:{{VALUE}}',
                        '{{WRAPPER}} .plyr__progress input[type=range]::-ms-track' => 'background-color:{{VALUE}}',
                    ],
                ]
            );

            // pbar_color_2
            $this->add_control(
                'pbar_color_2',
                [
                    'label' => esc_html__( 'Bar Color 2', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .plyr__progress__container input[type=range]' => 'color:{{VALUE}}',
                    ],
                ]
            );

            // pbar_buffer_color
            $this->add_control(
                'pbar_buffer_color',
                [
                    'label' => esc_html__( 'Buffered Bar Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .plyr--audio .plyr__progress__buffer' => 'color:{{VALUE}}',
                    ],
                ]
            );

        $this->end_controls_section(); // styling_progress_bar_section end

        $this->start_controls_section(
            'styling_volume_section',
            [
                'label'     => esc_html__( 'Volume Icon', 'folioedgecore' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );
            // volume_icon_bg_color
            $this->add_control(
                'volume_icon_bg_color',
                [
                    'label' => esc_html__( 'BG Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .plyr__control[data-plyr="mute"]' => 'background-color:{{VALUE}}',
                    ],
                ]
            );

            // volume_icon_color
            $this->add_control(
                'volume_icon_color',
                [
                    'label' => esc_html__( 'Icon Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .plyr__control[data-plyr="mute"] svg' => 'color:{{VALUE}}',
                    ],
                ]
            );

            // volume_icon_hover_bg_color
            $this->add_control(
                'volume_icon_hover_bg_color',
                [
                    'label' => esc_html__( 'Hover BG Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .plyr__control[data-plyr="mute"]:hover' => 'background-color:{{VALUE}}',
                    ],
                ]
            );

            // volume_icon_hover_color
            $this->add_control(
                'volume_icon_hover_color',
                [
                    'label' => esc_html__( 'Hover Icon Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .plyr__control[data-plyr="mute"]:hover svg' => 'color:{{VALUE}}',
                    ],
                ]
            );

            // volume_icon_border
            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'volume_icon_border',
                    'label' => esc_html__( 'Border', 'folioedgecore' ),
                    'selector' => '{{WRAPPER}} .plyr__control[data-plyr="mute"]'
                ]
            );

            $this->end_controls_section(); // Styling- volume icon section end
            $this->start_controls_section(
                'styling_volume_bar_section',
                [
                    'label'     => esc_html__( 'Volume Bar', 'folioedgecore' ),
                    'tab'       => Controls_Manager::TAB_STYLE,
                ]
            );
            // vbar_pointer_color
            $this->add_control(
                'vbar_pointer_color',
                [
                    'label' => esc_html__( 'Bar Pointer Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .plyr__volume input[type=range]::-webkit-slider-thumb' => 'background:{{VALUE}}',
                        '{{WRAPPER}} .plyr__volume input[type=range]::-moz-range-thumb' => 'background:{{VALUE}}',
                        '{{WRAPPER}} .plyr__volume input[type=range]::-ms-thumb' => 'background:{{VALUE}}',
                    ],
                ]

            );
            // vbar_color
            $this->add_control(
                'vbar_color',
                [
                    'label' => esc_html__( 'Bar Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .plyr__volume input[type=range]' => 'color:{{VALUE}}',
                    ],
                ]
            );

            // vbar_remaining_color
            $this->add_control(
                'vbar_remaining_color',
                [
                    'label' => esc_html__( 'Bar Empty Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .plyr__volume input[type=range]::-webkit-slider-runnable-track' => 'background-color:{{VALUE}}',
                        '{{WRAPPER}} .plyr__volume input[type=range]::-moz-range-track' => 'background-color:{{VALUE}}',
                        '{{WRAPPER}} .plyr__volume input[type=range]::-ms-track' => 'background-color:{{VALUE}}',
                    ],
                ]
            );

        $this->end_controls_section(); // style tab volume_section end

        $this->start_controls_section(
            'styling_setting_icon_section',
            [
                'label'     => esc_html__( 'Setting Icon', 'folioedgecore' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

            // settings_icon_bg_color
            $this->add_control(
                'settings_icon_bg_color',
                [
                    'label' => esc_html__( 'BG Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .plyr__control[data-plyr="settings"]' => 'background-color:{{VALUE}}',
                    ],
                ]
            );

            // settings_icon_color
            $this->add_control(
                'settings_icon_color',
                [
                    'label' => esc_html__( 'Icon Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .plyr__control[data-plyr="settings"] svg' => 'color:{{VALUE}}',
                    ],
                ]
            );

            // settings_icon_hover_bg_color
            $this->add_control(
                'settings_icon_hover_bg_color',
                [
                    'label' => esc_html__( 'Hover BG Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .plyr__control[data-plyr="settings"]:hover' => 'background-color:{{VALUE}}',
                    ],
                ]
            );

            // settings_icon_hover_color
            $this->add_control(
                'settings_icon_hover_color',
                [
                    'label' => esc_html__( 'Hover Icon Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .plyr__control[data-plyr="settings"]:hover svg' => 'color:{{VALUE}}',
                    ],
                ]
            );

            // volume_icon_border
            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'settings_icon_border',
                    'label' => esc_html__( 'Border', 'folioedgecore' ),
                    'selector' => '{{WRAPPER}} .plyr__control[data-plyr="settings"]'
                ]
            );
        $this->end_controls_section(); // Style tab setting_icon_section end

        $this->start_controls_section(
            'styling_others_section',
            [
                'label'     => esc_html__( 'Others', 'folioedgecore' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

            // timer_color
            $this->add_control(
                'timer_color',
                [
                    'label' => esc_html__( 'Timer Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .plyr__controls .plyr__time' => 'color:{{VALUE}}',
                    ],
                ]
            );

        $this->end_controls_section(); // Style tab others_section end

    }

    protected function render() {
        $settings    = $this->get_settings_for_display();

        // audio link
        if($settings['src_type'] == 'upload'){
            $audio_link = $settings['audio_upload']['url'];
        } else {
            $audio_link = $settings['audio_link']['url'];
        }

        $autoplay = $settings['autoplay'] == 'true' ? 'true' : 'false';
        $muted = $settings['muted'] == 'true' ? 'true' : 'false';
        $loop = $settings['loop'] == 'true' ? 'true' : 'false';
        $seek_time = $settings['seek_time'];
        $tooltips_seek = $settings['tooltips_seek'] == 'true' ? 'true' : 'false';
        $invert_time = $settings['invert_time'] == 'true' ? 'true' : 'false';
        $speed_selected = $settings['speed_selected'];
        $speed_selected = substr($speed_selected, 6 );
        $preload = $settings['preload'];
        $controls = $settings['controls'];
        $debug_mode = $settings['debug_mode'] == 'true' ? 'true' : 'false';

        // data settings
        $data_settings = array();
        $data_settings['muted'] = $muted;
        $data_settings['seek_time'] = $seek_time;
        $data_settings['tooltips_seek'] = $tooltips_seek;
        $data_settings['invertTime'] = $invert_time;
        $data_settings['speed_selected'] = $speed_selected;
        $data_settings['controls'] = $controls;
        $data_settings['debug_mode'] = $debug_mode;

        if($audio_link):
            $arr = explode('.', $audio_link);
            $file_ext = end($arr);
        ?>
        <audio
            class="folioedgecore_player folioedgecore_audio" 
            data-settings='<?php echo wp_json_encode($data_settings); ?>' 
            <?php echo esc_attr($autoplay == 'true' ? 'autoplay allow="autoplay"' : ''); ?>
            <?php echo esc_attr($loop == 'true' ? 'loop' : ''); ?> 
            preload="<?php echo esc_attr($preload); ?>"
        >
            <source
                src="<?php echo esc_url($audio_link); ?>"
                type="audio/<?php echo esc_attr($file_ext); ?>"
            />
        </audio>
        <?php
        else:
            echo '<div class="folioedgecore_not_found">';
            echo "<span>". esc_html__('No Audio File Selected/Uploaded', 'folioedgecore') ."</span>";
            echo '</div>';
        endif;
    }
}
Plugin::instance()->widgets_manager->register( new Folioedge_Audio_Player() );