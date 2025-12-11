<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * folioedgecore button widget.
 *
 * folioedgecore widget that displays a button with the ability to control every
 * aspect of the button design.
 *
 * @since 1.0.0
 */
class folioedge_Price_slider extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * Retrieve button widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
    public function get_name() {
        return 'folioedge_range_slider';
    }

	/**
	 * Get widget title.
	 *
	 * Retrieve button widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Range Slider', 'folioedgecore' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve button widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'folioedge-icon eicon-button';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the button widget belongs to.
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
    
    public function get_script_depends() {
        return [
            'addons-active',
            'jquery-ui-slider',
        ];
    }

    public function get_style_depends() {
        return [
            'lity',
            'folioedge-ui-css',
        ];
    }
    

	/**
	 * Register button widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_slider',
			[
				'label' => __( 'Range Slider', 'folioedgecore' ),
			]
		);
        

		$this->add_control(
			'starting_number',
			[
				'label' => __( 'Starting Number', 'folioedgecore' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 1,
			]
		);

		$this->add_control(
			'ending_number',
			[
				'label' => __( 'Ending Number', 'folioedgecore' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 20000,
			]
		);

		$this->add_control(
			'prefix',
			[
				'label' => __( 'Number Prefix', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( 'Symble', 'folioedgecore' ),
			]
		);

		$this->add_control(
			'suffix',
			[
				'label' => __( 'Number Suffix', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'default' => '+',
				'placeholder' => __( 'Plus', 'folioedgecore' ),
			]
		);
        
        $this->add_control(
            'slider_color',
            [
                'label' => __( 'Slider Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slider_range .slider .ui-slider-range, {{WRAPPER}} .slider_range .slider .ui-slider-handle' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
             'slider_target_id',
             [
                 'label'     => __( 'Price Table Connector ID', 'folioedgecore' ),
                 'type'      => Controls_Manager::TEXT,
                 'title' => __( 'Copy this ID to use at your price table "Range Slider ID" field.', 'folioedgecore' ),
                 'default' => uniqid(),
             ]
         );

		$this->end_controls_section();
	}

	/**
	 * Render button widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();       
        
		$this->add_render_attribute( 'slider_attr', 'class', 'slider_range' );
        $slider_settings = [
            'suffix' => $settings['suffix'],
            'prefix' => $settings['prefix'],
            'target_id' => $settings['slider_target_id'],
            'starting_number' => ($settings['starting_number'] != '' ? absint($settings['starting_number']) : ' '),
            'ending_number' => ($settings['ending_number'] != '' ? absint($settings['ending_number']) : ' '),
        ];
        $this->add_render_attribute( 'slider_attr', 'data-settings', wp_json_encode( $slider_settings ) );
		
        if( !empty($settings['slider_css_id']) ){
            $this->add_render_attribute( 'slider_attr', 'id', 'slider_range-'.$settings['slider_css_id'] );
        }
  
        echo '<div '.$this->get_render_attribute_string( 'slider_attr' ).' >';
            echo '<div class="slider">';
                echo '<div class="ui-slider-handle"><span></span></div>';
            echo '</div>';
        echo '</div>';
	}
}

Plugin::instance()->widgets_manager->register( new folioedge_Price_slider() );