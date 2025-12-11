<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor counter widget.
 *
 * Elementor widget that displays stats and numbers in an escalating manner.
 *
 * @since 1.0.0
 */
class folioedge_Cirlce_Progress extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve counter widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cirlce-progress-bar';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve counter widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Circle Progress', 'folioedgecore' );
	}
    

	/**
	 * Get widget icon.
	 *
	 * Retrieve counter widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'folioedge-icon eicon-countdown';
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
    
	/**
	 * Retrieve the list of scripts the counter widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.3.0
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [ 'easypiechart','jquery-easing' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'counter', 'progress', 'bar', 'cirlce' ];
	}

	/**
	 * Register counter widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_counter',
			[
				'label' => __( 'Circle Progress', 'folioedgecore' ),
			]
		);
        
		$this->add_control(
			'number_percentage',
			[
				'label' => __( 'Percentage', 'folioedgecore' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 50,
				'max' => 100,
				'min' => 0,
			]
		);

		$this->add_control(
			'prefix',
			[
				'label' => __( 'Number Prefix', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => 1,
			]
		);

		$this->add_control(
			'suffix',
			[
				'label' => __( 'Number Suffix', 'folioedgecore' ),
				'type' => Controls_Manager::TEXT,
				'default' => '%',
				'placeholder' => __( 'Suffix', 'folioedgecore' ),
			]
		);
        
		$this->add_control(
			'barColor',
			[
				'label' => __( 'Bar Color', 'folioedgecore' ),
				'type' => Controls_Manager::COLOR,
                'default' => '#3AC4E5',
			]
		);
        
		$this->add_control(
			'trackColor',
			[
				'label' => __( 'Track Color', 'folioedgecore' ),
				'type' => Controls_Manager::COLOR,
                'default' => '#C4EDF7',
			]
		);
        
		$this->add_control(
			'scaleColor',
			[
				'label' => __( 'Scale Color', 'folioedgecore' ),
				'type' => Controls_Manager::COLOR,
                'default' => '#dfe0e0',
			]
		);
		$this->add_control(
			'scaleLength',
			[
				'label' => __( 'Scale Length', 'folioedgecore' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 0,
				'max' => 50,
				'min' => 0,
			]
		);
		$this->add_control(
			'lineWidth',
			[
				'label' => __( 'Line Width', 'folioedgecore' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 15,
				'max' => 50,
				'min' => 1,
			]
		);
		$this->add_control(
			'boxSize',
			[
				'label' => __( 'Size', 'folioedgecore' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 150,
				'max' => 500,
				'min' => 1,
			]
		);
        $this->add_control(
            'lineCap',
            [
                'label' => esc_html__( 'Line Cap', 'folioedgecore' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'round',
                'options' => [
                    'round'  => esc_html__( 'Round', 'folioedgecore' ),
                    'square'  => esc_html__( 'Square', 'folioedgecore' ),
                    'butt'  => esc_html__( 'Butt', 'folioedgecore' ),
                ],
            ]
        );
		$this->add_control(
			'animateDuration',
			[
				'label' => __( 'Animation Duration', 'folioedgecore' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 2000,
				'min' => 100,
				'step' => 100,
			]
		);

		$this->end_controls_section();
        
		$this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Percent', 'folioedgecore' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);        
            $this->add_control(
                'title_color',
                [
                    'label' => __( 'Text Color', 'folioedgecore' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .percent' => 'color: {{VALUE}};',
                    ],
                ]
            );
            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'title_typo',
                    'selector' => '{{WRAPPER}} .percent',
                ]
            );
		$this->end_controls_section();
        
	}

	/**
	 * Render counter widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();        
        $this->add_render_attribute( 'wrapper_attributes', 'class', 'cirlce-progress' );
        $slider_settings = [
            'barColor' => $settings['barColor'],
            'trackColor' => $settings['trackColor'],
            'scaleColor' => $settings['scaleColor'],
            'scaleLength' => $settings['scaleLength'],
            'lineWidth' => $settings['lineWidth'],
            'boxSize' => $settings['boxSize'],
            'lineCap' => $settings['lineCap'],
            'animateDuration' => $settings['animateDuration'],
        ];
        $this->add_render_attribute( 'wrapper_attributes', 'data-settings', wp_json_encode( $slider_settings ) );
        echo '<div '.$this->get_render_attribute_string( 'wrapper_attributes' ).' data-percent="'.esc_attr($settings['number_percentage']).'"><h3 class="percent"><span class="prefix">'.esc_html($settings['prefix']).'</span><span class="cont"></span><span class="suffix">'.esc_html($settings['suffix']).'</span></h3></div>';
    } 
}
Plugin::instance()->widgets_manager->register( new folioedge_Cirlce_Progress() );