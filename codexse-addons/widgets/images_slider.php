<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

class Codexse_Addons_Elementor_Widget_Images_Slider extends Widget_Base {

    public function get_name() {
        return 'codexse_images_slider';
    }

    public function get_title() {
        return __( 'Images Slider', 'codexse-addons' );
    }

    public function get_icon() {
        return 'cx-addons-icon eicon-carousel-loop';
    }

    public function get_categories() {
        return [ 'codexse-addons' ];
    }

    public function get_keywords() {
        return [ 'image slider', 'carousel', 'gallery', 'slider', 'image gallery', 'codexse-addons' ];
    }    

    public function get_style_depends() {
        return [ 'codexse-image-slider', 'codexse-swiper' ];
    }

    public function get_script_depends() {
        return [ 'codexse-swiper', 'codexse-carousel' ];
    }
                                                                                                                                                              
    protected function register_controls() {                                                                                                                        
                                                                                                                                                                    
        $this->start_controls_section(
            'image_slider_content',
            [
                'label' => __( 'Images Slider', 'codexse-addons' ),
            ]
        );
        
        $repeater = new Repeater();
        
        $repeater->add_control(
            'slider_image',
            [
                'label' => __( 'Image', 'codexse-addons' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );
        
        $repeater->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'slider_imagesize',
                'default' => 'large',
                'separator' => 'none',
            ]
        );
        
        $repeater->add_control(
            'image_link',
            [
                'label'       => __( 'Image Link', 'codexse-addons' ),
                'type'        => Controls_Manager::URL,
                'placeholder' => __( 'Enter image URL.', 'codexse-addons' ),
                'show_external' => true, // Show the "open in new tab" option
                'default'     => [
                    'url' => '',
                ],
            ]
        );
        
        $this->add_control(
            'slider_list',
            [
                'type'    => Controls_Manager::REPEATER,
                'fields'  => $repeater->get_controls(),
                'default' => [
                    [
                        'slider_image' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                        'image_link' => [
                            'url' => '/',
                        ],
                    ],
                    [
                        'slider_image' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                        'image_link' => [
                            'url' => '/',
                        ],
                    ],
                    [
                        'slider_image' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                        'image_link' => [
                            'url' => '/',
                        ],
                    ],
                ],
                'title_field' => '{{{ slider_image.url ? "Image Slide" : "Slide" }}}',
            ]
        );
        
        $this->add_control(
            'slider_on',
            [
                'label'         => __( 'Slider', 'codexse-addons' ),
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => __( 'On', 'codexse-addons' ),
                'label_off'     => __( 'Off', 'codexse-addons' ),
                'return_value'  => 'yes',
                'default'       => 'yes',
            ]
        );
        
        $this->add_control(
            'item_columns',
            [
                'label' => __( 'Columns', 'codexse-addons' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    '1grid' => [
                        'title' => __( 'One Column', 'codexse-addons' ),
                        'icon' => 'cx cx-1',
                    ],
                    '2grid' => [
                        'title' => __( 'Two Columns', 'codexse-addons' ),
                        'icon' => 'cx cx-2',
                    ],
                    '3grid' => [
                        'title' => __( 'Three Columns', 'codexse-addons' ),
                        'icon' => 'cx cx-3',
                    ],
                    '4grid' => [
                        'title' => __( 'Four Columns', 'codexse-addons' ),
                        'icon' => 'cx cx-4',
                    ],
                ],
                'default' => '3grid',
                'toggle' => true,
                'condition' => [
                    'slider_on!' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'columns_space',
            [
                'label' => esc_html__( 'Columns Space', 'codexse-addons' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'g-4',
                'options' => [
                    'g-1'  => __( 'One', 'codexse-addons' ),
                    'g-2'  => __( 'Two', 'codexse-addons' ),
                    'g-3'  => __( 'Three', 'codexse-addons' ),
                    'g-4'  => __( 'Four', 'codexse-addons' ),
                    'g-5'  => __( 'Five', 'codexse-addons' ),
                ],
                'condition' => [
                    'slider_on!' => 'yes',
                ],
            ]
        );
        
        $this->end_controls_section();
        



        $this->start_controls_section(                                                                                                                              
            'slider_settings_section',                                                                                                                                        
            [                                                                                                                                                       
                'label' => esc_html__( 'Slider Settings', 'codexse-addons' ),                                                                                                
                'condition'=>[                                                                                                                                      
                    'slider_on'=>'yes',                                                                                                                             
                ]                                                                                                                                                   
            ]                                                                                                                                                       
        );                                                                                                                                                          
            $this->add_control(                                                                                                                                     
                'slider_navigation',                                                                                                                                    
                [                                                                                                                                                   
                    'label' => esc_html__( 'Navigation', 'codexse-addons' ),                                                                                                    
                    'type' => Controls_Manager::SWITCHER,                                                                                                           
                    'return_value' => 'yes',                                                                                                                        
                    'default' => 'yes',                                                                                                                             
                ]                                                                                                                                                   
            );                                                                                                                                                      
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'slider_custom_navigation',                                                                                                                              
                [                                                                                                                                                   
                    'label' => esc_html__( 'Custom Navigation', 'codexse-addons' ),                                                                                             
                    'type' => Controls_Manager::SWITCHER,                                                                                                           
                    'return_value' => 'yes',                                                                                                                        
                    'default' => 'no',                                                                                                                             
                    'condition'=>[                                                                                                                                  
                        'slider_navigation'=>'yes',                                                                                                                     
                    ]                                                                                                                                               
                ]                                                                                                                                                   
            );                                                                                                                                                      
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                 'slider_navigation_target',                                                                                                                                
                 [                                                                                                                                                  
                     'label'     => __( 'Navigation ID', 'codexse-addons' ),                                                                                                   
                     'type'      => Controls_Manager::TEXT,                                                                                                         
                     'title' => __( 'Collect this id from "Custom Navigation" addons and paste here!', 'codexse-addons' ),                                                   
                     'condition' => [                                                                                                                               
                        'slider_custom_navigation' => 'yes',                                                                                                            
                        'slider_navigation'=>'yes',                                                                                                                     
                     ]                                                                                                                                              
                 ]                                                                                                                                                  
             );                                                                                                                                                     
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'slider_previus_icon',                                                                                                                                 
                [                                                                                                                                                   
                    'label' => __( 'Previus Icon', 'codexse-addons' ),                                                                                                     
                    'type' => Controls_Manager::ICON,                                                                                                               
                    'default' => 'fa fa-angle-left',                                                                                                                
                    'condition'=>[                                                                                                                                  
                        'slider_navigation'=>'yes',                                                                                                                     
                        'slider_custom_navigation!'=>'yes',                                                                                                              
                    ]                                                                                                                                               
                ]                                                                                                                                                   
            );                                                                                                                                                      
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'slider_next_icon',                                                                                                                                 
                [                                                                                                                                                   
                    'label' => __( 'Next Navigation', 'codexse-addons' ),                                                                                                       
                    'type' => Controls_Manager::ICON,                                                                                                               
                    'default' => 'fa fa-angle-right',                                                                                                               
                    'condition'=>[                                                                                                                                  
                        'slider_navigation'=>'yes',                                                                                                                     
                        'slider_custom_navigation!'=>'yes',                                                                                                              
                    ]                                                                                                                                               
                ]                                                                                                                                                   
            );                                                                                                                                                      
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'slider_pagination',                                                                                                                                       
                [                                                                                                                                                   
                    'label' => esc_html__( 'Pagination', 'codexse-addons' ),                                                                                                 
                    'type' => Controls_Manager::SWITCHER,                                                                                                           
                    'return_value' => 'yes',                                                                                                                        
                    'default' => 'no',                                                                                                          
                    'separator' => 'before',                                                                                                                           
                ]                                                                                                                                                   
            );                                                                                                                                                  
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'slider_custom_pagination',                                                                                                                              
                [                                                                                                                                                   
                    'label' => esc_html__( 'Custom Pagination', 'codexse-addons' ),                                                                                             
                    'type' => Controls_Manager::SWITCHER,                                                                                                           
                    'return_value' => 'yes',                                                                                                                        
                    'default' => 'no',                                                                                                                             
                    'condition'=>[                                                                                                                                  
                        'slider_pagination'=>'yes',                                                                                                                     
                    ]                                                                                                                                               
                ]                                                                                                                                                   
            );                                                                                                                                                      
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                 'slider_pagination_target',                                                                                                                                
                 [                                                                                                                                                  
                     'label'     => __( 'Pagination ID', 'codexse-addons' ),                                                                                                   
                     'type'      => Controls_Manager::TEXT,                                                                                                         
                     'title' => __( 'Collect this id from "Custom Pagination" addons and paste here!', 'codexse-addons' ),                                                   
                     'condition' => [                                                                                                                               
                        'slider_custom_pagination' => 'yes',                                                                                                            
                        'slider_pagination'=>'yes',                                                                                                                     
                     ]                                                                                                                                              
                 ]                                                                                                                                                  
             );   
            $this->add_control(                                                                                                                                     
                'slider_pagination_type',                                                                                                                                         
                [                                                                                                                                                   
                    'label' => esc_html__( 'Pagination Type', 'codexse-addons' ),                                                                                                   
                    'type' => Controls_Manager::SELECT,                                                                                                             
                    'default' => 'progress',                                                                                                                           
                    'options' => [                                                                                                                                
                        'progress'  => __( 'Circle Progress', 'codexse-addons' ),                                                                                                         
                        'number'  => __( 'Number', 'codexse-addons' ),                                                                                                
                        'dots'  => __( 'Dots', 'codexse-addons' )                                                                                               
                    ],                                                                                           
                    'condition'=>[                                                                                                                                  
                        'slider_pagination' => 'yes'
                    ]
                ]                                                                                                                                                   
            );                                                                                                                                                      
            $this->add_control(                                                                                                                                     
                'slider_effect',                                                                                                                                         
                [                                                                                                                                                   
                    'label' => esc_html__( 'Effect', 'codexse-addons' ),                                                                                                   
                    'type' => Controls_Manager::SELECT,                                                                                                             
                    'default' => 'slide',                                                                                                       
                    'separator' => 'before',                                                                                                                           
                    'options' => [                                                                                                                                  
                        'slide'  => __( 'Slide', 'codexse-addons' ),                                                                                                       
                        'fade'  => __( 'Fade', 'codexse-addons' ),                                                                                                         
                        'cube'  => __( 'Cube', 'codexse-addons' ),                                                                                                         
                        'coverflow'  => __( 'Coverflow', 'codexse-addons' ),                                                                                               
                        'flip'  => __( 'Flip', 'codexse-addons' ),                                                                                                         
                    ],                                                                                                                                              
                ]                                                                                                                                                   
            );   

            $this->add_control(                                                                                                                                     
                'coverflow_heading',                                                                                                                                   
                [                                                                                                                                                   
                    'label' => __( 'Coverflow Options', 'codexse-addons' ),                                                                                                           
                    'type' => Controls_Manager::HEADING,                                                                                                            
                    'separator' => 'before',   
                    'condition' => [
                        'slider_effect' => 'coverflow',
                    ]                                                                                                                      
                ]                                                                                                                                                   
            );            
                                                                                                                                                                                
            $this->add_control(                                                                                                                                     
                'slider_coverflow_rotate',                                                                                                                                
                [                                                                                                                                                   
                    'label' => __('Rotate', 'codexse-addons'),                                                                                                       
                    'type' => Controls_Manager::NUMBER,                                                                                                             
                    'min' => 0,                                                                                                                                     
                    'max' => 360,                                                                                                                                     
                    'step' => 1,                                                                                                                                    
                    'default' => 0,      
                    'condition' => [
                        'slider_effect' => 'coverflow',
                    ]                                                                                                                                 
                ]                                                                                                                                                   
            );                                                                                                                                                      
                                                                                                                                                                                                   
            $this->add_control(                                                                                                                                     
                'slider_coverflow_stretch',                                                                                                                                
                [                                                                                                                                                   
                    'label' => __('Stretch', 'codexse-addons'),                                                                                                       
                    'type' => Controls_Manager::NUMBER,                                                                                                             
                    'min' => 0,                                                                                                                                     
                    'max' => 9999,                                                                                                                                     
                    'step' => 1,                                                                                                                                    
                    'default' => 0,      
                    'condition' => [
                        'slider_effect' => 'coverflow',
                    ]                                                                                                                                 
                ]                                                                                                                                                   
            );              
                                                                                                                                                                     
            $this->add_control(                                                                                                                                     
                'slider_coverflow_depth',                                                                                                                                
                [                                                                                                                                                   
                    'label' => __('Depth', 'codexse-addons'),                                                                                                       
                    'type' => Controls_Manager::NUMBER,                                                                                                             
                    'min' => 0,                                                                                                                                     
                    'max' => 9999,                                                                                                                                     
                    'step' => 1,                                                                                                                                    
                    'default' => 0,      
                    'condition' => [
                        'slider_effect' => 'coverflow',
                    ],                                                                                                                                          
                ]                                                                                                                                                   
            );                                                                                                                                                                  
            $this->add_control(                                                                                                                                     
                'slider_coverflow_shadow',                                                                                                                                           
                [                                                                                                                                                   
                    'label' => esc_html__( 'Shadow', 'codexse-addons' ),                                                                                                     
                    'type' => Controls_Manager::SWITCHER,                                                                                                           
                    'return_value' => 'yes',                                                                                                                        
                    'default' => 'no',                                                                                   
                    'separator' => 'after',                                                                                                                         
                ]                                                                                                                                                   
            );                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'slider_loop',                                                                                                                                           
                [                                                                                                                                                   
                    'label' => esc_html__( 'Loop', 'codexse-addons' ),                                                                                                     
                    'type' => Controls_Manager::SWITCHER,                                                                                                           
                    'return_value' => 'yes',                                                                                                                        
                    'default' => 'yes',                                                                                                                             
                ]                                                                                                                                                   
            );  

            $this->add_control(                                                                                                                                     
                'slider_autoplay',                                                                                                                                        
                [                                                                                                                                                   
                    'label' => esc_html__( 'Autoplay', 'codexse-addons' ),                                                                                                 
                    'type' => Controls_Manager::SWITCHER,                                                                                                           
                    'return_value' => 'yes',                                                                                                                        
                    'default' => 'yes',                                                                                                                             
                ]                                                                                                                                                   
            );                                                                                                                                                      
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'slider_autoplay_delay',                                                                                                                                   
                [                                                                                                                                                   
                    'label' => __('Autoplay Delay', 'codexse-addons'),                                                                                                     
                    'type' => Controls_Manager::NUMBER,                                                                                                             
                    'default' => 3000,                                                                                                                              
                ]                                                                                                                                                   
            );                                                                                                                                                      
                                                                                                                                                                    
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'slider_position',                                                                                                                                         
                [                                                                                                                                                   
                    'label' => esc_html__( 'Center', 'codexse-addons' ),                                                                                                   
                    'type' => Controls_Manager::SWITCHER,                                                                                                           
                    'return_value' => 'yes',                                                                                                                        
                    'default' => 'no',                                                                                                                              
                ]                                                                                                                                                   
            );                                                                                                                                                      
                                                                                                                                                                    
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'slider_per_view',                                                                                                                                
                [                                                                                                                                                   
                    'label' => __('Slide Per View', 'codexse-addons'),                                                                                                       
                    'type' => Controls_Manager::NUMBER,                                                                                                             
                    'min' => 1,                                                                                                                                     
                    'max' => 8,                                                                                                                                     
                    'step' => 1,                                                                                                                                    
                    'default' => 1,                                                                                                                                 
                ]                                                                                                                                                   
            );                                                                                                                                                      
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'slider_center_padding',                                                                                                                                 
                [                                                                                                                                                   
                    'label' => __( 'Center padding', 'codexse-addons' ),                                                                                                   
                    'type' => Controls_Manager::NUMBER,                                                                                                             
                    'min' => 0,                                                                                                                                     
                    'max' => 500,                                                                                                                                   
                    'step' => 1,                                                                                                                                    
                    'default' => 30,                                                                                                                                
                ]                                                                                                                                                   
            );                                                                                                                                                      
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'slider_speed',                                                                                                                                
                [                                                                                                                                                   
                    'label' => __('Slide Speed', 'codexse-addons'),                                                                                                        
                    'type' => Controls_Manager::NUMBER,                                                                                                             
                    'default' => 1000,                                                                                                                              
                ]                                                                                                                                                   
            );                                                                                                                                                   
            $this->add_control(                                                                                                                                     
                'slider_direction',                                                                                                                                         
                [                                                                                                                                                   
                    'label' => esc_html__( 'Direction', 'codexse-addons' ),                                                                                                   
                    'type' => Controls_Manager::SELECT,                                                                                                             
                    'default' => 'horizontal',                                                                                                                           
                    'options' => [                                                                                                                                  
                        'horizontal'  => __( 'horizontal', 'codexse-addons' ),                                                                                                       
                        'vertical'  => __( 'vertical', 'codexse-addons' ),                                                                                                       
                    ],                                                                                                                                              
                ]                                                                                                                                                   
            );        
        
            $this->add_responsive_control(
                'slider_height',
                [
                    'label' => __( 'Height', 'codexse-addons' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'vh', 'vw' ],
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
                        ],
                        'vw' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .codexse-slider-active' => 'height: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'slider_direction' => 'vertical',
                    ]
                ]
            );                                                                                                                                                  
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'heading_laptop',                                                                                                                                   
                [                                                                                                                                                   
                    'label' => __( 'Laptop', 'codexse-addons' ),                                                                                                           
                    'type' => Controls_Manager::HEADING,                                                                                                            
                    'separator' => 'after',                                                                                                                         
                ]                                                                                                                                                   
            );                                                                                                                                                      
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'slider_laptop_brack',                                                                                                                                   
                [                                                                                                                                                   
                    'label' => __('Laptop Resolution', 'codexse-addons'),                                                                                                  
                    'description' => __('The resolution to laptop.', 'codexse-addons'),                                                                                    
                    'type' => Controls_Manager::NUMBER,                                                                                                             
                    'default' => 1200,                                                                                                                              
                ]                                                                                                                                                   
            );                                                                                                                                                      
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'slider_laptop_per_view',                                                                                                                         
                [                                                                                                                                                   
                    'label' => __('Slide Per View', 'codexse-addons'),                                                                                                       
                    'type' => Controls_Manager::NUMBER,                                                                                                             
                    'min' => 1,                                                                                                                                     
                    'max' => 8,                                                                                                                                     
                    'step' => 1,                                                                                                                                    
                    'default' => 3,                                                                                                                                 
                ]                                                                                                                                                   
            );                                                                                                                                                      
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'slider_laptop_center_padding',                                                                                                                                 
                [                                                                                                                                                   
                    'label' => __( 'Center padding', 'codexse-addons' ),                                                                                                   
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
                    'label' => __( 'Tablet', 'codexse-addons' ),                                                                                                           
                    'type' => Controls_Manager::HEADING,                                                                                                            
                    'separator' => 'after',                                                                                                                         
                ]                                                                                                                                                   
            );                                                                                                                                                      
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'slider_tablet_brack',                                                                                                                                   
                [                                                                                                                                                   
                    'label' => __('Tablet Resolution', 'codexse-addons'),                                                                                                  
                    'description' => __('The resolution to tablet.', 'codexse-addons'),                                                                                    
                    'type' => Controls_Manager::NUMBER,                                                                                                             
                    'default' => 992,                                                                                                                               
                ]                                                                                                                                                   
            );                                                                                                                                                      
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'slider_tablet_per_view',                                                                                                                         
                [                                                                                                                                                   
                    'label' => __('Slide Per View', 'codexse-addons'),                                                                                                       
                    'type' => Controls_Manager::NUMBER,                                                                                                             
                    'min' => 1,                                                                                                                                     
                    'max' => 8,                                                                                                                                     
                    'step' => 1,                                                                                                                                    
                    'default' => 2,                                                                                                                                 
                ]                                                                                                                                                   
            );                                                                                                                                                      
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'slider_tablet_center_padding',                                                                                                                                 
                [                                                                                                                                                   
                    'label' => __( 'Center padding', 'codexse-addons' ),                                                                                                   
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
                    'label' => __( 'Mobile Phone', 'codexse-addons' ),                                                                                                     
                    'type' => Controls_Manager::HEADING,                                                                                                            
                    'separator' => 'after',                                                                                                                         
                ]                                                                                                                                                   
            );                                                                                                                                                      
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'slider_mobile_break',                                                                                                                                   
                [                                                                                                                                                   
                    'label' => __('Mobile Resolution', 'codexse-addons'),                                                                                                  
                    'description' => __('The resolution to mobile.', 'codexse-addons'),                                                                                    
                    'type' => Controls_Manager::NUMBER,                                                                                                             
                    'default' => 768,                                                                                                                               
                ]                                                                                                                                                   
            );                                                                                                                                                      
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'slider_mobile_per_view',                                                                                                                         
                [                                                                                                                                                   
                    'label' => __('Slide Per View', 'codexse-addons'),                                                                                                       
                    'type' => Controls_Manager::NUMBER,                                                                                                             
                    'min' => 1,                                                                                                                                     
                    'max' => 4,                                                                                                                                     
                    'step' => 1,                                                                                                                                    
                    'default' => 1,                                                                                                                                 
                ]                                                                                                                                                   
            );                                                                                                                                                      
                                                                                                                                                                    
            $this->add_control(                                                                                                                                     
                'slider_mobile_center_padding',                                                                                                                                 
                [                                                                                                                                                   
                    'label' => __( 'Center padding', 'codexse-addons' ),                                                                                                   
                    'type' => Controls_Manager::NUMBER,                                                                                                             
                    'min' => 0,                                                                                                                                     
                    'max' => 500,                                                                                                                                   
                    'step' => 1,                                                                                                                                    
                    'default' => 30,                                                                                                                                
                ]                                                                                                                                                   
            );                                                                                                                                                      
        $this->end_controls_section();                                                                                                                               
        // Testimonial Item Style Tab Section
        $this->start_controls_section(
            'slider_style_section',
            [
                'label' => __( 'Slider Item', 'codexse-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('slider_item_style_tabs');

        // Normal State Tab
        $this->start_controls_tab(
            'slider_item_normal_state',
            [
                'label' => __( 'Normal', 'codexse-addons' ),
            ]
        );

        // Margin Control
        $this->add_responsive_control(
            'slider_item_margin',
            [
                'label' => __( 'Margin', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .slider-image-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        // Padding Control
        $this->add_responsive_control(
            'slider_item_padding',
            [
                'label' => __( 'Padding', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .slider-image-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        // Background Control
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'slider_item_background',
                'label' => __( 'Background', 'codexse-addons' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .slider-image-item',
            ]
        );

        // Border Control
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'slider_item_border',
                'label' => __( 'Border', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .slider-image-item',
            ]
        );

        // Border Radius Control
        $this->add_responsive_control(
            'slider_item_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .slider-image-item, {{WRAPPER}} .slider-image-item:before, {{WRAPPER}} .slider-image-item:after' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );

        // Box Shadow Control
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'slider_item_box_shadow',
                'label' => __( 'Box Shadow', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .slider-image-item',
            ]
        );

        // Transform Control
        $this->add_control(
            'slider_item_transform',
            [
                'label' => __( 'Transform', 'codexse-addons' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'translateY(0)',
                'selectors' => [
                    '{{WRAPPER}} .slider-image-item' => 'transform: {{VALUE}}',
                ],
            ]
        );

        // Transition Duration Control
        $this->add_control(
            'slider_item_transition_duration',
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
                    '{{WRAPPER}} .slider-image-item' => 'transition-duration: {{SIZE}}s',
                ],
            ]
        );

        $this->end_controls_tab();

        // Hover State Tab
        $this->start_controls_tab(
            'slider_item_hover_state',
            [
                'label' => __( 'Hover', 'codexse-addons' ),
            ]
        );

        // Hover Border Control
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'slider_item_hover_border',
                'label' => __( 'Border', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .slider-image-item:hover',
            ]
        );

        // Border Radius Control
        $this->add_responsive_control(
            'slider_item_hover_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .slider-image-item:hover' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );

        // Box Shadow Control
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'slider_item_hover_box_shadow',
                'label' => __( 'Box Shadow', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .slider-image-item:hover',
            ]
        );

        // Transform Control
        $this->add_control(
            'slider_item_hover_transform',
            [
                'label' => __( 'Transform', 'codexse-addons' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'translateY(0)',
                'selectors' => [
                    '{{WRAPPER}} .slider-image-item:hover' => 'transform: {{VALUE}}',
                ],
            ]
        );
        $this->end_controls_tab(); // End Hover Style Tab
        $this->end_controls_tabs(); // End Style Tabs
        $this->end_controls_section(); // End Testimonial Item Style Section                                                                                                         
    }                                                                                                                                                               
                                                                                                                                                                    
    protected function render() {      
        $column = "col-lg-4 col-md-6";                                                                                                   
        $settings   = $this->get_settings_for_display();                                                                                                       

        // Carousel Attribute                                                                
        if( $settings['slider_on'] == 'yes' ){                                                                                                                      
            $this->add_render_attribute( 'wrapper_attributes', 'class', ['swiper-container','codexse-slider-active', 'swiper-pagination-'.$settings['slider_pagination_type']] );                                                                       
            $slider_settings = [
                'slider_effect' => $settings['slider_effect'],                                                                                                                
                'slider_direction' => $settings['slider_direction'],                                                                                                                
                'slider_loop' => ('yes' === $settings['slider_loop']),                                                                                               
                'slider_pagination' => ('yes' === $settings['slider_pagination']),                                                                                    
                'slider_pagination_type' => $settings['slider_pagination_type'] ? $settings['slider_pagination_type'] : 'progress',                                                                                                            
                'slider_autoplay' => ('yes' === $settings['slider_autoplay']),                                                                                                  
                'slider_autoplay_delay' => absint($settings['slider_autoplay_delay']), 
                'slider_speed' => absint($settings['slider_speed']),   
                'slider_coverflow_rotate' => absint($settings['slider_coverflow_rotate']),    
                'slider_coverflow_stretch' => absint($settings['slider_coverflow_stretch']),    
                'slider_coverflow_depth' => absint($settings['slider_coverflow_depth']),                                                                                                   
                'slider_coverflow_shadow' => $settings['slider_coverflow_shadow'],   
                'slider_custom_navigation' => ('yes' === $settings['slider_custom_navigation']),                                                                                 
                'slider_navigation' => $settings['slider_navigation'],                                                                                          
                'slider_navigation_target' => $settings['slider_navigation_target'],                
                'slider_custom_pagination' => ('yes' === $settings['slider_custom_pagination']),                                                                                   
                'slider_pagination_target' => $settings['slider_pagination_target'],     
                'slider_per_view' => $settings['slider_per_view'],                                                                                              
                'slider_position' => ('yes' === $settings['slider_position']),                                                                                           
                'slider_center_padding' => $settings['slider_center_padding'],                                                                                                
                'slider_laptop_brack' => $settings['slider_laptop_brack'],                                                                                                      
                'slider_laptop_center_padding' => $settings['slider_laptop_center_padding'],                                                                                                  
                'slider_laptop_per_view' => $settings['slider_laptop_per_view'],                                                                                  
                'slider_tablet_brack' => $settings['slider_tablet_brack'],                                                                                                      
                'slider_tablet_center_padding' => $settings['slider_tablet_center_padding'],                                                                                                  
                'slider_tablet_per_view' => $settings['slider_tablet_per_view'],                                                                                  
                'slider_mobile_break' => $settings['slider_mobile_break'],                                                                                                      
                'slider_mobile_center_padding' => $settings['slider_mobile_center_padding'],                                                                                                  
                'slider_mobile_per_view' => $settings['slider_mobile_per_view'],                                                                                  
            ];                                                                                                                                                      
            $this->add_render_attribute( 'wrapper_attributes', 'data-settings', wp_json_encode( $slider_settings ) );                                         
        }else {
            $this->add_render_attribute( 'wrapper_attributes', 'class', ['row', esc_attr($settings['columns_space'])] );
            switch ($settings['item_columns']) {
                case "1grid":
                    $column = "col-lg-12";
                    break;
                case "2grid":
                    $column = "col-lg-6 col-md-12";
                    break;
                case "3grid":
                    $column = "col-lg-4 col-md-6";
                    break;
                default:
                    $column = "col-xl-3 col-lg-4 col-md-6";
            }
        }
        echo '<div '.$this->get_render_attribute_string( "wrapper_attributes" ).' >';                                                                               
            if($settings['slider_on'] == 'yes'){
                echo '<div class="swiper-wrapper">';                                                                                                                        
                    foreach ( $settings['slider_list'] as $item ):
                        echo '<div class="swiper-slide elementor-repeater-item-'.$item['_id'].'">';
                            $this->slider_content($item);
                        echo '</div>';                                                                                                                                      
                    endforeach;                                                                                                                                             
                echo '</div>';                                                                                                           
                if( $settings['slider_navigation'] == true && $settings['slider_custom_navigation'] != true ){                                                                       
                    echo '<div class="swiper-navigation" >';                                                                                                                
                        echo '<div class="swiper-arrow swiper-prev"><i class="'.esc_attr($settings['slider_previus_icon']).'" ></i></div>';                                    
                        echo '<div class="swiper-arrow swiper-next"><i class="'.esc_attr($settings['slider_next_icon']).'" ></i></div>';                                    
                    echo '</div>';                                                                                                                                          
                }                                                                                                                                                           
                if( $settings['slider_pagination'] == true && $settings['slider_custom_pagination'] != true ){                                                                                                                      
                    echo '<div class="swiper-pagination"></div>';                                                                                                           
                }           
            }else {                                                                                        
                foreach ( $settings['slider_list'] as $item ):   
                    echo '<div class="'.$column.' elementor-repeater-item-'.$item['_id'].'">'; 
                    $this->slider_content($item);  
                    echo '</div>';
                endforeach;
            }            
        echo '</div>';                                                                                                                                              
    } 
    public function slider_content($item) {
        $settings = $this->get_settings_for_display();                                         
        echo '<div class="slider-image-item">';
        
        // Get the image link from the settings
        $image_link = isset($item['image_link']['url']) ? $item['image_link']['url'] : '';
    
        // Check if the link is not empty
        if ($image_link) {
            echo '<a href="' . esc_url($image_link) . '" target="' . (isset($item['image_link']['is_external']) && $item['image_link']['is_external'] ? '_blank' : '_self') . '">';
        }
    
        // Display the image
        if (Group_Control_Image_Size::get_attachment_image_html($item, 'slider_imagesize', 'slider_image')) {
            echo '<figure class="thumbnail">';
            echo Group_Control_Image_Size::get_attachment_image_html($item, 'slider_imagesize', 'slider_image');
            echo '</figure>';
        }
    
        // Close the <a> tag if it was opened
        if ($image_link) {
            echo '</a>';
        }
    
        echo '</div>';
    }
}
