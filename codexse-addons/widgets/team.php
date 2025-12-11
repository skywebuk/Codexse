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

class Codexse_Addons_Elementor_Widget_Team extends Widget_Base {

    public function get_name() {
        return 'codexse_team';
    }

    public function get_title() {
        return __( 'Team', 'codexse-addons' );
    }

    public function get_icon() {
        return 'cx-addons-icon eicon-person';
    }

    public function get_categories() {
        return [ 'codexse-addons' ];
    }

    public function get_keywords() {
        return [ 'team', 'member', 'staff', 'codexse-addons' ];
    }

    public function get_style_depends() {
        return [ 'codexse-team', 'codexse-swiper' ];
    }

    public function get_script_depends() {
        return [ 'codexse-swiper', 'codexse-carousel' ];
    }
                                                                                                                                                              
    protected function register_controls() {                                                                                                                        
                                                                                                                                                                    
        $this->start_controls_section(                                                                                                                              
            'team_content',                                                                                                                                      
            [                                                                                                                                                       
                'label' => __( 'Team', 'codexse-addons' ),                                                                                                      
            ]                                                                                                                                                       
        );         
            $repeater = new Repeater();

            // Image
            $repeater->add_control(
                'team_image',
                [
                    'label' => __( 'Image', 'codexse-addons' ),
                    'type' => Controls_Manager::MEDIA,
                ]
            );

            // Image Size
            $repeater->add_group_control(
                Group_Control_Image_Size::get_type(),
                [
                    'name' => 'team_imagesize',
                    'default' => 'large',
                    'separator' => 'none',
                ]
            );

            // Name
            $repeater->add_control(
                'team_name',
                [
                    'label' => __( 'Name', 'codexse-addons' ),
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => __('Enter name','codexse-addons'),
                ]
            );

            // Designation
            $repeater->add_control(
                'team_position',
                [
                    'label' => __( 'Designation', 'codexse-addons' ),
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => __('Enter designation','codexse-addons'),
                ]
            );

            // Social Links (Textarea)
            $repeater->add_control(
                'team_social',
                [
                    'label' => __( 'Social Links', 'codexse-addons' ),
                    'type' => Controls_Manager::TEXTAREA,
                    'placeholder' => __('Enter social links HTML','codexse-addons'),
                ]
            );

            $this->add_control(
                'team_list',
                [
                    'type'    => Controls_Manager::REPEATER,
                    'fields'  => $repeater->get_controls(),
                    'default' => [
                        [
                            'team_name' => __('Helena Paitora','codexse-addons'),
                            'team_position' => __('Digital Marketer','codexse-addons'),
                            'team_social' => __('<a href="#"><i class="fab fa-facebook"></i></a>','codexse-addons'),
                        ],
                        [
                            'team_name' => __('Jason Kink','codexse-addons'),
                            'team_position' => __('Developer','codexse-addons'),
                            'team_social' => __('<a href="#"><i class="fab fa-twitter"></i></a>','codexse-addons'),
                        ],
                    ],
                    'title_field' => '{{{ team_name }}}',
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
                    'label' => __( 'Column', 'codexse-addons' ),
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
                    ]
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
                    ]
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
                    'default' => 'yes',                                                                                                                             
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
    }                                                                                                                                                               
        
    protected function render() {
        $column   = "col-lg-4 col-md-6";
        $settings = $this->get_settings_for_display();

        // Slider on
        if ( $settings['slider_on'] === 'yes' ) {
            $this->add_render_attribute( 'wrapper_attributes', 'class', ['swiper-container','codexse-slider-active', 'swiper-pagination-'.$settings['slider_pagination_type']] );

            $slider_settings = [ /* unchanged slider settings */ ];
            $this->add_render_attribute( 'wrapper_attributes', 'data-settings', wp_json_encode( $slider_settings ) );
        } else {
            $this->add_render_attribute( 'wrapper_attributes', 'class', ['row', esc_attr($settings['columns_space'])] );
            switch ( $settings['item_columns'] ) {
                case "1grid": $column = "col-lg-12"; break;
                case "2grid": $column = "col-lg-6 col-md-12"; break;
                case "3grid": $column = "col-lg-4 col-md-6"; break;
                default:      $column = "col-xl-3 col-lg-4 col-md-6";
            }
        }

        echo '<div ' . $this->get_render_attribute_string( "wrapper_attributes" ) . '>';

        if ( $settings['slider_on'] === 'yes' ) {
            echo '<div class="swiper-wrapper">';
            foreach ( $settings['team_list'] as $item ) {
                echo '<div class="swiper-slide elementor-repeater-item-' . esc_attr($item['_id']) . '">';
                $this->render_team_member_item( $item );
                echo '</div>';
            }
            echo '</div>';

            if ( $settings['slider_navigation'] && !$settings['slider_custom_navigation'] ) {
                echo '<div class="swiper-navigation">';
                echo '<div class="swiper-arrow swiper-prev"><i class="'.esc_attr($settings['slider_previus_icon']).'"></i></div>';
                echo '<div class="swiper-arrow swiper-next"><i class="'.esc_attr($settings['slider_next_icon']).'"></i></div>';
                echo '</div>';
            }

            if ( $settings['slider_pagination'] && !$settings['slider_custom_pagination'] ) {
                echo '<div class="swiper-pagination"></div>';
            }
        } else {
            foreach ( $settings['team_list'] as $item ) {
                echo '<div class="'.$column.' elementor-repeater-item-' . esc_attr($item['_id']) . '">';
                $this->render_team_member_item( $item );
                echo '</div>';
            }
        }

        echo '</div>';
    }

    private function render_team_member_item( $item ) {
        echo '<div class="codexse-team-item">';
            echo '<div class="team-header">';
                if ( Group_Control_Image_Size::get_attachment_image_html( $item, 'team_imagesize', 'team_image' ) ) {
                    echo '<div class="thumbnail">';
                    echo Group_Control_Image_Size::get_attachment_image_html( $item, 'team_imagesize', 'team_image' );
                    echo '</div>';
                }
                if ( !empty( $item['team_social'] ) ) {
                    echo '<div class="team-social">' . wp_kses_post( $item['team_social'] ) . '</div>';
                }
            echo '</div>';

            if ( !empty( $item['team_name'] ) ) {
                echo '<h4 class="title">' . esc_html( $item['team_name'] ) . '</h4>';
            }

            if ( !empty( $item['team_position'] ) ) {
                echo '<div class="position">' . esc_html( $item['team_position'] ) . '</div>';
            }

        echo '</div>';
    }


}
