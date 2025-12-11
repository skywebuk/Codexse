<?php

if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

class Codexse_Addons_Elementor_Widget_Hero_Slider extends Widget_Base {
    
    public function get_name() {
        return 'codexse_hero_slider';
    }

    public function get_title() {
        return __( 'Hero Slider', 'codexse-addons' );
    }

    public function get_icon() {
        return 'eicon-slider-device';
    }

    public function get_categories() {
        return [ 'codexse-addons' ];
    }

    public function get_keywords() {
        return [ 'hero', 'slider', 'banner', 'hero section', 'full-width banner', 'cta section', 'landing section' ];
    }

    public function get_style_depends() {
        return [ 'codexse-swiper', 'codexse-hero-slider' ];
    }

    public function get_script_depends() {
        return [ 'codexse-swiper', 'codexse-hero-slider' ];
    }

    protected function register_controls() {
        
        $this->start_controls_section(
            'banner_section_content',
            [
                'label' => __( 'Slides', 'codexse-addons' ),
            ]
        );
        
        $repeater = new Repeater();
        
        $repeater->add_control(
            'background_image',
            [
                'label' => __( 'Background Image', 'codexse-addons' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'background_image_size',
                'default' => 'full',
                'separator' => 'none',
            ]
        );

        $repeater->add_control(
            'top_title',
            [
                'label' => __( 'Top Title', 'codexse-addons' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Hello this is top title', 'codexse-addons' ),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'main_title',
            [
                'label' => __( 'Main Title', 'codexse-addons' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'This is main title', 'codexse-addons' ),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'description',
            [
                'label' => __( 'Description', 'codexse-addons' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'codexse-addons' ),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'primary_button_text',
            [
                'label' => __( 'Primary Button Text', 'codexse-addons' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Get Started', 'codexse-addons' ),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'primary_button_link',
            [
                'label' => __( 'Primary Button Link', 'codexse-addons' ),
                'type' => Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com', 'codexse-addons' ),
                'default' => [
                    'url' => '#',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'secondary_button_text',
            [
                'label' => __( 'Secondary Button Text', 'codexse-addons' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Learn More', 'codexse-addons' ),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'secondary_button_link',
            [
                'label' => __( 'Secondary Button Link', 'codexse-addons' ),
                'type' => Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com', 'codexse-addons' ),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'slides',
            [
                'label' => __( 'Slides', 'codexse-addons' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'top_title' => __( 'Hello this is top title', 'codexse-addons' ),
                        'main_title' => __( 'This is main title', 'codexse-addons' ),
                        'description' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'codexse-addons' ),
                        'primary_button_text' => __( 'Get Started', 'codexse-addons' ),
                        'primary_button_link' => [ 'url' => '#' ],
                    ],
                ],
                'title_field' => '{{{ main_title }}}',
            ]
        );
        
        $this->end_controls_section();

        // Slider Settings
        $this->start_controls_section(
            'slider_settings_section',
            [
                'label' => __( 'Slider Settings', 'codexse-addons' ),
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => __( 'Autoplay', 'codexse-addons' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => __( 'Autoplay Speed (ms)', 'codexse-addons' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 5000,
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'loop',
            [
                'label' => __( 'Infinite Loop', 'codexse-addons' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'speed',
            [
                'label' => __( 'Transition Speed (ms)', 'codexse-addons' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 800,
            ]
        );

        $this->add_control(
            'effect',
            [
                'label' => __( 'Effect', 'codexse-addons' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'slide',
                'options' => [
                    'slide' => __( 'Slide', 'codexse-addons' ),
                    'fade' => __( 'Fade', 'codexse-addons' ),
                    'cube' => __( 'Cube', 'codexse-addons' ),
                    'coverflow' => __( 'Coverflow', 'codexse-addons' ),
                    'flip' => __( 'Flip', 'codexse-addons' ),
                ],
            ]
        );

        $this->add_control(
            'show_navigation',
            [
                'label' => __( 'Show Navigation', 'codexse-addons' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'navigation_prev_icon',
            [
                'label' => __( 'Previous Icon', 'codexse-addons' ),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'eicon-chevron-left',
                    'library' => 'eicons',
                ],
                'condition' => [
                    'show_navigation' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'navigation_next_icon',
            [
                'label' => __( 'Next Icon', 'codexse-addons' ),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'eicon-chevron-right',
                    'library' => 'eicons',
                ],
                'condition' => [
                    'show_navigation' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label' => __( 'Show Pagination', 'codexse-addons' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'pagination_type',
            [
                'label' => __( 'Pagination Type', 'codexse-addons' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'bullets',
                'options' => [
                    'bullets' => __( 'Bullets', 'codexse-addons' ),
                    'fraction' => __( 'Fraction', 'codexse-addons' ),
                    'progressbar' => __( 'Progressbar', 'codexse-addons' ),
                ],
                'condition' => [
                    'show_pagination' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Tab: Slide Content
        $this->start_controls_section(
            'section_slide_content_style',
            [
                'label' => __( 'Slide Content', 'codexse-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // Style controls would go here...

        $this->end_controls_section();

        // Style Tab: Navigation
        $this->start_controls_section(
            'section_navigation_style',
            [
                'label' => __( 'Navigation', 'codexse-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_navigation' => 'yes',
                ],
            ]
        );

        // Style controls would go here...

        $this->end_controls_section();

        // Style Tab: Pagination
        $this->start_controls_section(
            'section_pagination_style',
            [
                'label' => __( 'Pagination', 'codexse-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_pagination' => 'yes',
                ],
            ]
        );

        // Style controls would go here...

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute( 'hero__slider', 'class', 'codexse-hero-slider swiper' );
        $this->add_render_attribute( 'slider__wrapper', 'class', 'swiper-wrapper' );

        $slider_options = [
            'autoplay' => ('yes' === $settings['autoplay']) ? ['delay' => $settings['autoplay_speed']] : false,
            'loop' => ('yes' === $settings['loop']),
            'speed' => $settings['speed'],
            'effect' => $settings['effect'],
        ];

        $this->add_render_attribute( 'hero__slider', 'data-settings', wp_json_encode($slider_options) );
        ?>
        <div <?php echo $this->get_render_attribute_string( 'hero__slider' ); ?>>
            <div <?php echo $this->get_render_attribute_string( 'slider__wrapper' ); ?>>
                <?php foreach ( $settings['slides'] as $slide ) : ?>
                    <div class="swiper-slide elementor-repeater-item-<?php echo esc_attr($slide['_id']); ?>">
                        <?php $this->render_slide_content($slide); ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ( 'yes' === $settings['show_navigation'] ) : ?>
                <div class="swiper-navigation">
                    <div class="swiper-arrow swiper-prev">
                        <?php \Elementor\Icons_Manager::render_icon( $settings['navigation_prev_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                    </div>
                    <div class="swiper-arrow swiper-next">
                        <?php \Elementor\Icons_Manager::render_icon( $settings['navigation_next_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ( 'yes' === $settings['show_pagination'] ) : ?>
                <div class="swiper-pagination"></div>
            <?php endif; ?>     
        </div>
        <?php
    }

    protected function render_slide_content( $slide ) {
        $image_url = Group_Control_Image_Size::get_attachment_image_src( $slide['background_image']['id'], 'background_image_size', $slide );
        if ( empty( $image_url ) ) {
            $image_url = $slide['background_image']['url'];
        }

        $this->add_render_attribute( 'slide-background', 'class', 'codexse-hero-slide-bg' );
        $this->add_render_attribute( 'slide-background', 'style', 'background-image: url(' . esc_url($image_url) . ')' );

        $this->add_render_attribute( 'primary_button', 'class', 'primary_button one' );
        if ( ! empty( $slide['primary_button_link']['url'] ) ) {
            $this->add_link_attributes( 'primary_button', $slide['primary_button_link'] );
        }

        $this->add_render_attribute( 'secondary_button', 'class', 'primary_button outline two' );
        if ( ! empty( $slide['secondary_button_link']['url'] ) ) {
            $this->add_link_attributes( 'secondary_button', $slide['secondary_button_link'] );
        }
        ?>
        <div <?php echo $this->get_render_attribute_string( 'slide-background' ); ?>></div>
        
        <div class="codexse-hero-slide-overlay"></div>
        
        <div class="codexse-hero-slide-content">
            <?php if ( ! empty( $slide['top_title'] ) ) : ?>
                <div class="codexse-hero-slide-top-title"><?php echo esc_html($slide['top_title']); ?></div>
            <?php endif; ?>
            
            <?php if ( ! empty( $slide['main_title'] ) ) : ?>
                <h2 class="codexse-hero-slide-main-title"><?php echo esc_html($slide['main_title']); ?></h2>
            <?php endif; ?>
            
            <?php if ( ! empty( $slide['description'] ) ) : ?>
                <div class="codexse-hero-slide-description"><?php echo wp_kses_post($slide['description']); ?></div>
            <?php endif; ?>
            
            <div class="codexse-hero-slide-buttons">
                <?php if ( ! empty( $slide['primary_button_text'] ) ) : ?>
                    <a <?php echo $this->get_render_attribute_string( 'primary_button' ); ?>>
                        <?php echo esc_html($slide['primary_button_text']); ?>
                    </a>
                <?php endif; ?>
                
                <?php if ( ! empty( $slide['secondary_button_text'] ) ) : ?>
                    <a <?php echo $this->get_render_attribute_string( 'secondary_button' ); ?>>
                        <?php echo esc_html($slide['secondary_button_text']); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
