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

class Codexse_Addons_Elementor_Widget_Testimonial extends Widget_Base {

    public function get_name() {
        return 'codexse_testimonial';
    }

    public function get_title() {
        return __( 'Testimonial', 'codexse-addons' );
    }

    public function get_icon() {
        return 'cx-addons-icon eicon-testimonial-carousel';
    }

    public function get_categories() {
        return [ 'codexse-addons' ];
    }

    public function get_keywords() {
        return [ 'testimonial', 'review', 'feedback', 'codexse-addons' ];
    }

    public function get_style_depends() {
        return [ 'codexse-testimonial', 'codexse-swiper' ];
    }

    public function get_script_depends() {
        return [ 'codexse-swiper', 'codexse-carousel' ];
    }
                                                                                                                                                              
    protected function register_controls() {                                                                                                                        
                                                                                                                                                                    
        $this->start_controls_section(                                                                                                                              
            'testimonial_content',                                                                                                                                      
            [                                                                                                                                                       
                'label' => __( 'Testimonial', 'codexse-addons' ),                                                                                                      
            ]                                                                                                                                                       
        );                                                                                                                                                          
            $repeater = new Repeater();    

            $repeater->add_control(                                                                                                                                 
                'testimonial_image',                                                                                                                                    
                [                                                                                                                                                   
                    'label' => __( 'Image', 'codexse-addons' ),                                                                                                     
                    'type' => Controls_Manager::MEDIA,                                                                                                              
                ]                                                                                                                                                   
            );                                                                                                                                                      
            
            $repeater->add_group_control(                                                                                                                           
                Group_Control_Image_Size::get_type(),                                                                                                               
                [                                                                                                                                                   
                    'name' => 'testimonial_imagesize',                                                                                                                  
                    'default' => 'large',                                                                                                                           
                    'separator' => 'none',                                                                                                                          
                ]                                                                                                                                                   
            );  

            $repeater->add_control(                                                                                                                                 
                'testimonial_name',                                                                                                                              
                [                                                                                                                                                   
                    'label'   => __( 'Name', 'codexse-addons' ),                                                                                                   
                    'type'    => Controls_Manager::TEXT,                                                                                                            
                    'placeholder' => __('Enter client name.','codexse-addons'),                                                                                      
                ]                                                                                                                                                   
            );                                                                                                                       
            
            $repeater->add_control(                                                                                                                                 
                'testimonial_position',                                                                                                                              
                [                                                                                                                                                   
                    'label'   => __( 'Position', 'codexse-addons' ),                                                                                                   
                    'type'    => Controls_Manager::TEXT,                                                                                                            
                    'placeholder' => __('Enter client position.','codexse-addons'),                                                                                      
                ]                                                                                                                                                   
            );

            $repeater->add_control(
                'rating_switch', 
                [
                    'label' => __('Show rating star', 'codexse-addons'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'no',
                    'label_on' => __('Show', 'codexse-addons'),
                    'label_off' => __('Hide', 'codexse-addons'),
                ]
            );
            
            $repeater->add_control(
                'rating_number', 
                [
                    'label' => __('Rating', 'codexse-addons'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => ['size' => 3],
                    'range' => ['px' => ['max' => 5, 'step' => 0.1]],
                    'condition' => ['rating_switch' => 'yes'],
                ]
            );       

            $repeater->add_control(                                                                                                                                 
                'testimonial_description',                                                                                                                              
                [                                                                                                                                                   
                    'label'   => __( 'Description', 'codexse-addons' ),                                                                                                   
                    'type'    => Controls_Manager::TEXTAREA,                                                                                                            
                    'placeholder' => __('Enter your client feedback message.','codexse-addons'),                                                                                      
                ]                                                                                                                                                   
            );                                                                                                                       
            
            $this->add_control(                                                                                                                                     
                'testimonial_list',                                                                                                                               
                [                                                                                                                                                   
                    'type'    => Controls_Manager::REPEATER,                                                                                                        
                    'fields'  => $repeater->get_controls(),                                                                                                         
                    'default' => [                                                                                                                                  
                                                                                                                                                                                
                        [                                                                                                                                           
                            'testimonial_description' => __('Lorem Ipsum generator with dictionary of words, model sentence structures, and looks reasonable.','codexse-addons'),                                                                   
                            'testimonial_name' => __('Helena Paitora','codexse-addons'),                                                                   
                            'testimonial_position' => __('Digital Marketer','codexse-addons'),                                                                
                        ],                                                                                                                              
                        [                                                                                                                                           
                            'testimonial_description' => __('Lorem Ipsum generator with dictionary of words, model sentence structures, and looks reasonable.','codexse-addons'),                                                                   
                            'testimonial_name' => __('Jason Kink','codexse-addons'),                                                                   
                            'testimonial_position' => __('Digital Marketer','codexse-addons'),                                                                
                        ],                                                                                                                              
                        [                                                                                                                                           
                            'testimonial_description' => __('Lorem Ipsum generator with dictionary of words, model sentence structures, and looks reasonable.','codexse-addons'),                                                                   
                            'testimonial_name' => __('Irfan Raza','codexse-addons'),                                                                   
                            'testimonial_position' => __('Digital Marketer','codexse-addons'),                                                                
                        ],                                                                                                                                          
                                                                                                                                                                                
                    ],                                                                                                                                              
                    'title_field' => '{{{ testimonial_name }}}',                                                                                                 
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
// Testimonial Item Style Tab Section
$this->start_controls_section(
    'testimonials_style_section',
    [
        'label' => __( 'Testimonial Item', 'codexse-addons' ),
        'tab' => Controls_Manager::TAB_STYLE,
    ]
);

$this->start_controls_tabs('testimonial_item_style_tabs');

// Normal State Tab
$this->start_controls_tab(
    'testimonial_item_normal_state',
    [
        'label' => __( 'Normal', 'codexse-addons' ),
    ]
);

// Margin Control
$this->add_responsive_control(
    'testimonial_item_margin',
    [
        'label' => __( 'Margin', 'codexse-addons' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%', 'em' ],
        'selectors' => [
            '{{WRAPPER}} .testimonial-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'separator' => 'before',
    ]
);

// Padding Control
$this->add_responsive_control(
    'testimonial_item_padding',
    [
        'label' => __( 'Padding', 'codexse-addons' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%', 'em' ],
        'selectors' => [
            '{{WRAPPER}} .testimonial-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'separator' => 'before',
    ]
);

// Background Control
$this->add_group_control(
    Group_Control_Background::get_type(),
    [
        'name' => 'testimonial_item_background',
        'label' => __( 'Background', 'codexse-addons' ),
        'types' => [ 'classic', 'gradient' ],
        'selector' => '{{WRAPPER}} .testimonial-item',
    ]
);

// Border Control
$this->add_group_control(
    Group_Control_Border::get_type(),
    [
        'name' => 'testimonial_item_border',
        'label' => __( 'Border', 'codexse-addons' ),
        'selector' => '{{WRAPPER}} .testimonial-item',
    ]
);

// Border Radius Control
$this->add_responsive_control(
    'testimonial_item_border_radius',
    [
        'label' => esc_html__( 'Border Radius', 'codexse-addons' ),
        'type' => Controls_Manager::DIMENSIONS,
        'selectors' => [
            '{{WRAPPER}} .testimonial-item, {{WRAPPER}} .testimonial-item:before, {{WRAPPER}} .testimonial-item:after' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
        ],
    ]
);

// Box Shadow Control
$this->add_group_control(
    Group_Control_Box_Shadow::get_type(),
    [
        'name' => 'testimonial_item_box_shadow',
        'label' => __( 'Box Shadow', 'codexse-addons' ),
        'selector' => '{{WRAPPER}} .testimonial-item',
    ]
);

// Transform Control
$this->add_control(
    'testimonial_item_transform',
    [
        'label' => __( 'Transform', 'codexse-addons' ),
        'type' => Controls_Manager::TEXT,
        'default' => 'translateY(0)',
        'selectors' => [
            '{{WRAPPER}} .testimonial-item' => 'transform: {{VALUE}}',
        ],
    ]
);

// Transition Duration Control
$this->add_control(
    'testimonial_item_transition_duration',
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
            '{{WRAPPER}} .testimonial-item' => 'transition-duration: {{SIZE}}s',
        ],
    ]
);

$this->end_controls_tab();

// Hover State Tab
$this->start_controls_tab(
    'testimonial_item_hover_state',
    [
        'label' => __( 'Hover', 'codexse-addons' ),
    ]
);

// Hover Background Control
$this->add_group_control(
    Group_Control_Background::get_type(),
    [
        'name' => 'testimonial_item_hover_background',
        'label' => __( 'Hover Background', 'codexse-addons' ),
        'types' => [ 'classic', 'gradient' ],
        'selector' => '{{WRAPPER}} .testimonial-item:hover',
    ]
);

// Hover Border Control
$this->add_group_control(
    Group_Control_Border::get_type(),
    [
        'name' => 'testimonial_item_hover_border',
        'label' => __( 'Hover Border', 'codexse-addons' ),
        'selector' => '{{WRAPPER}} .testimonial-item:hover',
    ]
);

// Hover Border Radius Control
$this->add_responsive_control(
    'testimonial_item_hover_border_radius',
    [
        'label' => esc_html__( 'Hover Border Radius', 'codexse-addons' ),
        'type' => Controls_Manager::DIMENSIONS,
        'selectors' => [
            '{{WRAPPER}} .testimonial-item:hover' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
        ],
    ]
);

// Hover Box Shadow Control
$this->add_group_control(
    Group_Control_Box_Shadow::get_type(),
    [
        'name' => 'testimonial_item_hover_box_shadow',
        'label' => __( 'Hover Box Shadow', 'codexse-addons' ),
        'selector' => '{{WRAPPER}} .testimonial-item:hover',
    ]
);

// Hover Transform Control
$this->add_control(
    'testimonial_item_hover_transform',
    [
        'label' => __( 'Hover Transform', 'codexse-addons' ),
        'type' => Controls_Manager::TEXT,
        'default' => 'translateY(0)',
        'selectors' => [
            '{{WRAPPER}} .testimonial-item:hover' => 'transform: {{VALUE}}',
        ],
    ]
);

// Hover Padding Control
$this->add_responsive_control(
    'testimonial_item_hover_padding',
    [
        'label' => __( 'Hover Padding', 'codexse-addons' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%', 'em' ],
        'selectors' => [
            '{{WRAPPER}} .testimonial-item:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'separator' => 'before',
    ]
);

$this->end_controls_tab(); // End Hover Style Tab

$this->end_controls_tabs(); // End Style Tabs
$this->end_controls_section(); // End Testimonial Item Style Section
     
        
        
        // Icon Box Style tab section
        $this->start_controls_section(
            'description_style_section',
            [
                'label' => __( 'Description', 'codexse-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->start_controls_tabs('description_style_tabs');
        
        $this->start_controls_tab( 'description_style_normal',
			[
				'label' => __( 'Normal', 'codexse-addons' ),
			]
		);        
        
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .testimonial-item .description',
            ]
        );
        $this->add_control(
            'description_color',
            [
                'label' => __( 'Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .testimonial-item .description' => 'color: {{VALUE}};',
                ],
            ]
        );
                
        $this->add_responsive_control(
            'description_margin',
            [
                'label' => __( 'Margin', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .testimonial-item .description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'description_padding',
            [
                'label' => __( 'Padding', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .testimonial-item .description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
		$this->add_control(
			'description_transition',
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
					'{{WRAPPER}} .testimonial-item .description' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
        
        $this->end_controls_tab(); // Hover Style tab end
         
        $this->start_controls_tab( 'description_hover_style_tab',
			[
				'label' => __( 'Hover', 'codexse-addons' ),
			]
		);        
        
        $this->add_control(
            'description_hover_color',
            [
                'label' => __( 'Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .testimonial-item .description:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->start_controls_tab( 'hover_description_style_tab',
			[
				'label' => __( 'Box Hover', 'codexse-addons' ),
			]
		);        
        
        $this->add_control(
            'hover_description_color',
            [
                'label' => __( 'Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .testimonial-item:hover .description' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->end_controls_tabs();// Box Style tabs end 
        $this->end_controls_section();

        // Icon Style tab section
        $this->start_controls_section(
            'image_style_section',
            [
                'label' => __( 'Thumbnail', 'codexse-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
		$this->add_responsive_control(
			'image_width',
			[
				'label' => __( 'Width', 'codexse-addons' ),
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
					'{{WRAPPER}} .testimonial-item .thumbnail' => 'width: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);        
        
		$this->add_responsive_control(
			'image_height',
			[
				'label' => __( 'Height', 'codexse-addons' ),
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
					'{{WRAPPER}} .testimonial-item .thumbnail' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'image_background',
                'label' => __( 'Background', 'codexse-addons' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .testimonial-item .thumbnail',
            ]
        );
        $this->add_responsive_control(
            'image_alignment',
            [
                'label' => __( 'Float', 'codexse-addons' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'codexse-addons' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'none' => [
                        'title' => __( 'None', 'codexse-addons' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'codexse-addons' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .testimonial-item .thumbnail' => 'float: {{VALUE}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->add_responsive_control(
            'image_margin',
            [
                'label' => __( 'Margin', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .testimonial-item .thumbnail' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'image_padding',
            [
                'label' => __( 'Padding', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .testimonial-item .thumbnail' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'label' => __( 'Border', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .testimonial-item .thumbnail',
            ]
        );
        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'codexse-addons' ),
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
                    '{{WRAPPER}} .testimonial-item .thumbnail' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_shadow',
                'label' => __( 'Box Shadow', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .testimonial-item .thumbnail',
            ]
        );        
        $this->add_control(
			'image_transition',
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
					'{{WRAPPER}} .testimonial-item .thumbnail' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
        $this->end_controls_section();



        // Icon Box Style tab section
        $this->start_controls_section(
            'title_section',
            [
                'label' => __( 'Name', 'codexse-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,  
            ]
        );
        
        $this->start_controls_tabs('title_style_tabs');
        
        $this->start_controls_tab( 'title_style_normal',
			[
				'label' => __( 'Normal', 'codexse-addons' ),
			]
		);        
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .testimonial-item .title',
            ]
        );
        $this->add_control(
            'title_color',
            [
                'label' => __( 'Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .testimonial-item .title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'title_margin',
            [
                'label' => __( 'Margin', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .testimonial-item .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'title_padding',
            [
                'label' => __( 'Padding', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .testimonial-item .title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
                
		$this->add_control(
			'title_transition',
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
					'{{WRAPPER}} .testimonial-item .title' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
        $this->add_responsive_control(                                                                                                                          
            'title_alignment',                                                                                                                             
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
                ],                                                                                                                                              
                'selectors' => [                                                                                                                                
                    '{{WRAPPER}} .testimonial-item .title' => 'text-align: {{VALUE}};'                                                                         
                ],                                                                                                                                              
                'separator' =>'before',                                                                                                                         
            ]                                                                                                                                                   
        );  
        $this->end_controls_tab(); // Hover Style tab end
         
        $this->start_controls_tab( 'title_hover_style_tab',
			[
				'label' => __( 'Hover', 'codexse-addons' ),
			]
		);        
        
        $this->add_control(
            'title_hover_color',
            [
                'label' => __( 'Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .testimonial-item .title:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->start_controls_tab( 'hover_title_style_tab',
			[
				'label' => __( 'Box Hover', 'codexse-addons' ),
			]
		);        
        
        $this->add_control(
            'hover_title_color',
            [
                'label' => __( 'Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .testimonial-item:hover .title' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section();



                
        // Icon Box Style tab section
        $this->start_controls_section(
            'position_style_section',
            [
                'label' => __( 'Position', 'codexse-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->start_controls_tabs('position_style_tabs');
        
        $this->start_controls_tab( 'position_style_normal_tab',
			[
				'label' => __( 'Normal', 'codexse-addons' ),
			]
		);        
        
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'position_typography',
                'selector' => '{{WRAPPER}} .testimonial-item .position',
            ]
        );
        $this->add_control(
            'position_color',
            [
                'label' => __( 'Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .testimonial-item .position' => 'color: {{VALUE}};',
                ],
            ]
        );
                
        $this->add_responsive_control(
            'position_margin',
            [
                'label' => __( 'Margin', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .testimonial-item .position' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'position_padding',
            [
                'label' => __( 'Padding', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .testimonial-item .position' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
		$this->add_control(
			'position_transition',
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
					'{{WRAPPER}} .testimonial-item .position' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
        
        $this->end_controls_tab(); // Hover Style tab end
         
        $this->start_controls_tab( 'position_hover_style_tab',
			[
				'label' => __( 'Hover', 'codexse-addons' ),
			]
		);        
        
        $this->add_control(
            'position_hover_color',
            [
                'label' => __( 'Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .testimonial-item .position:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->start_controls_tab( 'hover_position_style_tab',
			[
				'label' => __( 'Box Hover', 'codexse-addons' ),
			]
		);        
        
        $this->add_control(
            'hover_position_color',
            [
                'label' => __( 'Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .testimonial-item:hover .position' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->end_controls_tabs();// Box Style tabs end 
        $this->end_controls_section();

        $this->start_controls_section(
            "rating_style_section", 
            [
                "label" => __("Rating", "codexse"),
                "tab" => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->start_controls_tabs("rating_style_tabs");
        $this->start_controls_tab(
            "rating_style_normal_tab", 
            [
                "label" => __("Normal", "codexse"),
            ]
        );
        $this->add_control(
            "rating_font_size", 
            [
                "label" => __("Font Size", "codexse"),
                "type" => Controls_Manager::SLIDER,
                "default" => ["size" => 16],
                "range" => ["px" => ["max" => 100, "step" => 1]],
                "selectors" => [
                    "{{WRAPPER}} .feed-rating" => "font-size: {{SIZE}}px",
                ],
            ]
        );
        $this->add_control(
            "rating_color", 
            [
                "label" => __("Color", "codexse"),
                "type" => Controls_Manager::COLOR,
                "selectors" => ["{{WRAPPER}} .testimonial-item .feed-rating .star" => "color: {{VALUE}};"],
            ]
        );
        $this->add_responsive_control(
            "rating_margin", 
            [
                "label" => __("Margin", "codexse"),
                "type" => Controls_Manager::DIMENSIONS,
                "size_units" => ["px", "%", "em"],
                "selectors" => [
                    "{{WRAPPER}} .feed-rating" =>
                        "margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
                ],
                "separator" => "before",
            ]
        );
        $this->add_responsive_control(
            "rating_padding", 
            [
                "label" => __("Padding", "codexse"),
                "type" => Controls_Manager::DIMENSIONS,
                "size_units" => ["px", "%", "em"],
                "selectors" => [
                    "{{WRAPPER}} .feed-rating" =>
                        "padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
                ],
                "separator" => "before",
            ]
        );

        $this->add_control(
            "rating_transition", 
            [
                "label" => __("Transition Duration", "codexse"),
                "type" => Controls_Manager::SLIDER,
                "default" => ["size" => 0.3],
                "range" => ["px" => ["max" => 3, "step" => 0.1]],
                "selectors" => [
                    "{{WRAPPER}} .feed-rating" => "transition-duration: {{SIZE}}s",
                ],
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab("rating_hover_style_tab", [
            "label" => __("Hover", "codexse"),
        ]);
        $this->add_control(
            "rating_hover_color", 
            [
                "label" => __("Color", "codexse"),
                "type" => Controls_Manager::COLOR,
                "selectors" => [
                    "{{WRAPPER}} .feed-rating:hover" => "color: {{VALUE}};",
                ],
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            "hover_rating_style_tab", 
            [
                "label" => __("Box Hover", "codexse"),
            ]
        );
        $this->add_control(
            "hover_rating_color", 
            [
                "label" => __("Color", "codexse"),
                "type" => Controls_Manager::COLOR,
                "selectors" => [
                    "{{WRAPPER}} .testimonial-item:hover .feed-rating" => "color: {{VALUE}};",
                ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();                                                                                                                            
    }                                                                                                                                                               
                                                                                                                                                                    
    protected function render() {
        $column   = "col-lg-4 col-md-6";
        $settings = $this->get_settings_for_display();

        // Carousel Attribute
        if ( $settings['slider_on'] === 'yes' ) {
            $this->add_render_attribute( 'wrapper_attributes', 'class', [
                'swiper-container',
                'codexse-slider-active',
                'swiper-pagination-' . $settings['slider_pagination_type']
            ]);

            $slider_settings = [
                'slider_effect'                 => $settings['slider_effect'],
                'slider_direction'              => $settings['slider_direction'],
                'slider_loop'                   => ( 'yes' === $settings['slider_loop'] ),
                'slider_pagination'            => ( 'yes' === $settings['slider_pagination'] ),
                'slider_pagination_type'       => $settings['slider_pagination_type'] ?: 'progress',
                'slider_autoplay'              => ( 'yes' === $settings['slider_autoplay'] ),
                'slider_autoplay_delay'        => absint( $settings['slider_autoplay_delay'] ),
                'slider_speed'                 => absint( $settings['slider_speed'] ),
                'slider_coverflow_rotate'      => absint( $settings['slider_coverflow_rotate'] ),
                'slider_coverflow_stretch'     => absint( $settings['slider_coverflow_stretch'] ),
                'slider_coverflow_depth'       => absint( $settings['slider_coverflow_depth'] ),
                'slider_coverflow_shadow'      => $settings['slider_coverflow_shadow'],
                'slider_custom_navigation'     => ( 'yes' === $settings['slider_custom_navigation'] ),
                'slider_navigation'            => $settings['slider_navigation'],
                'slider_navigation_target'     => $settings['slider_navigation_target'],
                'slider_custom_pagination'     => ( 'yes' === $settings['slider_custom_pagination'] ),
                'slider_pagination_target'     => $settings['slider_pagination_target'],
                'slider_per_view'              => $settings['slider_per_view'],
                'slider_position'              => ( 'yes' === $settings['slider_position'] ),
                'slider_center_padding'        => $settings['slider_center_padding'],
                'slider_laptop_brack'          => $settings['slider_laptop_brack'],
                'slider_laptop_center_padding' => $settings['slider_laptop_center_padding'],
                'slider_laptop_per_view'       => $settings['slider_laptop_per_view'],
                'slider_tablet_brack'          => $settings['slider_tablet_brack'],
                'slider_tablet_center_padding' => $settings['slider_tablet_center_padding'],
                'slider_tablet_per_view'       => $settings['slider_tablet_per_view'],
                'slider_mobile_break'          => $settings['slider_mobile_break'],
                'slider_mobile_center_padding' => $settings['slider_mobile_center_padding'],
                'slider_mobile_per_view'       => $settings['slider_mobile_per_view'],
            ];

            $this->add_render_attribute( 'wrapper_attributes', 'data-settings', wp_json_encode( $slider_settings ) );
        } else {
            $this->add_render_attribute( 'wrapper_attributes', 'class', ['row', esc_attr( $settings['columns_space'] )] );
            switch ( $settings['item_columns'] ) {
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
        echo '<div ' . $this->get_render_attribute_string( "wrapper_attributes" ) . ' >';
        if ( $settings['slider_on'] === 'yes' ) {
            echo '<div class="swiper-wrapper">';
            foreach ( $settings['testimonial_list'] as $item ) {
                $this->render_testimonial_item( $item, $column, true );
            }
            echo '</div>';
            if ( $settings['slider_navigation'] && $settings['slider_custom_navigation'] == 'no' ) {
                echo '<div class="swiper-navigation">';
                    echo '<div class="swiper-arrow swiper-prev"><i class="' . esc_attr( $settings['slider_previus_icon'] ) . '"></i></div>';
                    echo '<div class="swiper-arrow swiper-next"><i class="' . esc_attr( $settings['slider_next_icon'] ) . '"></i></div>';
                echo '</div>';
            }

            if ( $settings['slider_pagination'] && $settings['slider_custom_pagination'] == 'no' ) {
                echo '<div class="swiper-pagination"></div>';
            }
        } else {
            foreach ( $settings['testimonial_list'] as $item ) {
                $this->render_testimonial_item( $item, $column, false );
            }
        }

        echo '</div>';
    }

    private function render_testimonial_item( $item = [], $column = '', $is_slider = false ) {
        $wrap_class = $is_slider
            ? 'swiper-slide elementor-repeater-item-' . $item['_id']
            : $column . ' elementor-repeater-item-' . $item['_id'];

        echo '<div class="' . esc_attr( $wrap_class ) . '">';
            echo '<div class="testimonial-item">';
                echo '<div class="testimonial-header">';

                // Image
                $image_html = Group_Control_Image_Size::get_attachment_image_html( $item, 'testimonial_imagesize', 'testimonial_image' );
                if ( $image_html ) {
                    echo '<div class="thumbnail">' . $image_html . '</div>';
                }

                // Name
                if ( ! empty( $item['testimonial_name'] ) ) {
                    echo '<h5 class="title">' . esc_html( $item['testimonial_name'] ) . '</h5>';
                }

                // Position
                if ( ! empty( $item['testimonial_position'] ) ) {
                    echo '<div class="position">' . esc_html( $item['testimonial_position'] ) . '</div>';
                }

                // Rating
                if ( isset( $item["rating_switch"] ) && $item["rating_switch"] === "yes" ) {
                    $rating_width = isset( $item["rating_number"]["size"] ) ? $item["rating_number"]["size"] * 20 : 0;
                    echo '<div class="feed-rating">';
                        echo '<span class="star front" style="width: ' . esc_attr( $rating_width ) . '%"></span>';
                        echo '<span class="star back"></span>';
                    echo '</div>';
                }

                echo '</div>'; // .testimonial-header

                // Description
                if ( ! empty( $item['testimonial_description'] ) ) {
                    echo '<div class="description">' . esc_html( $item['testimonial_description'] ) . '</div>';
                }

            echo '</div>'; // .testimonial-item
        echo '</div>';
    }

}
