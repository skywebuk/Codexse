<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Folioedge_Experiences extends Widget_Base {

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
		return 'experiences-widget';
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
		return __( 'Experiences', 'folioedgecore' );
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
		return 'folioedge-icon eicon-hypster';
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
		return [ 'experience','qualification' ];
	}

	protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Experiences', 'folioedgecore' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $repeater = new Repeater();
        $repeater->add_control(
            'institute_name',
            [
                'label' => __( 'Institute Name', 'folioedgecore' ),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __( 'Enter your institute name.', 'folioedgecore' ),
                'default' => __( 'Google Software Inc', 'folioedgecore' ),
                'title' => __( 'Institute Name', 'folioedgecore' ),
            ]
        );
        $repeater->add_control(
            'session_year',
            [
                'label' => __( 'Session year', 'folioedgecore' ),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __( 'Enter your session year.', 'folioedgecore' ),
                'default' => __( 'Sep 2016 - Aug 2014', 'folioedgecore' ),
                'title' => __( 'Session year', 'folioedgecore' ),
            ]
        );
        $repeater->add_control(
            'experience_title',
            [
                'label' => __( 'Title', 'folioedgecore' ),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __( 'Enter the Experiences title', 'folioedgecore' ),
                'default' => __( 'Mid Level UI/UX Designer', 'folioedgecore' ),
                'title' => __( 'Experiences title', 'folioedgecore' ),
            ]
        );
        $repeater->add_control(
            'experience_content',
            [
                'label' => __( 'Description', 'folioedgecore' ),
                'type' => Controls_Manager::TEXTAREA,
                'placeholder' => __( 'Enter your Experiences description.', 'folioedgecore' ),
                'default' => __( 'Their creativity, innovation, technological expertise, and project completion steps were impressive. Project management was professional. We’re a full-service creative digital marketing agency, collaborating with brands.', 'folioedgecore' ),
                'title' => __( 'Experiences Description', 'folioedgecore' ),
            ]
        );
        $repeater->add_control(
            'cirlce_color',
            [
                'label' => __( 'Circle Color', 'folioedgecore' ),
                'type' => Controls_Manager::COLOR,
				'default' => '#ffb525',
                'selectors' => [
                    '{{WRAPPER}} .experience-boxes {{CURRENT_ITEM}} .circle span' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'item_list',
            [
                'label' => __( 'Repeater List', 'folioedgecore' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'institute_name' => __( 'Google Software Inc', 'folioedgecore' ),
                        'session_year' => __( 'Sep 2016 - Aug 2014', 'folioedgecore' ),
                        'experience_title' => __( 'Mid Level UI/UX Designer', 'folioedgecore' ),
                        'experience_content' => __( 'Their creativity, innovation, technological expertise, and project completion steps were impressive. Project management was professional. We’re a full-service creative digital marketing agency, collaborating with brands.', 'folioedgecore' ),
                    ],
                ],
                'title_field' => '{{{ experience_title }}}',
            ]
        );
        
        $this->end_controls_section();  
        
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
        $html = '';
        $this->add_render_attribute( 'folioedge_experience_attr', 'class', 'experience-boxes' );        
		if ( $settings['item_list'] ) {
            $html .= '<div '.$this->get_render_attribute_string( "folioedge_experience_attr" ).' >';
            foreach (  $settings['item_list'] as $item ) {
                $html .= '<div class="experience-box elementor-repeater-item-'.$item['_id'].'">';
                    $html .= '<div class="left-side">';
                        if( !empty($item['institute_name']) ){
                            $html .= '<h4 class="institute">'.esc_html($item['institute_name']).'</h4>';
                        }
                        if(!empty($item['session_year']) ){
                            $html .= '<div class="session" >'.esc_html($item['session_year']).'</div>';
                        }
                    $html .= '</div>';
                    $html .= '<div class="circle"><span></span></div>';
                    $html .= '<div class="right-side">';
                        if( !empty($item['experience_title']) ){
                            $html .= '<h4 class="title">'.esc_html($item['experience_title']).'</h4>';
                        }
                        if( !empty($item['experience_content']) ){
                            $html .= '<div class="desc">'.wp_kses_post($item['experience_content']).'</div>';
                        }
                    $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</div>';
        }
        echo $html;        
    }// End Rendar
    
}// End Class


Plugin::instance()->widgets_manager->register_widget_type( new Folioedge_Experiences );