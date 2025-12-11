<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Codexse_Addons_Elementor_Widget_Page_Title extends Widget_Base {

    public function get_name() {
        return 'codexse_page_title';
    }

    public function get_title() {
        return __( 'Page Title', 'codexse-addons' );
    }

    public function get_icon() {
        return 'cx-addons-icon eicon-post-title';
    }

    public function get_categories() {
        return [ 'codexse-addons' ];
    }

    public function get_keywords() {
        return [ 'title', 'page title', 'heading', 'codexse' ];
    }

    protected function _register_controls() {
        // Title Settings Section
        $this->start_controls_section(
            'section_title',
            [
                'label' => __( 'Title Settings', 'codexse-addons' ),
            ]
        );

        $this->add_control(
            'blog_list_title',
            [
                'label' => __( 'Blog Page Title', 'codexse-addons' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Blog', 'codexse-addons' ),
            ]
        );

        $this->add_control(
            'shop_page_title',
            [
                'label' => __( 'Shop Page Title', 'codexse-addons' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Shop', 'codexse-addons' ),
            ]
        );

        $this->add_control(
            'error_page_title',
            [
                'label' => __( 'Error Page Title', 'codexse-addons' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Page Not Found', 'codexse-addons' ),
            ]
        );

        $this->add_control(
            'heading_tag',
            [
                'label' => __( 'HTML Tag', 'codexse-addons' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'h2',
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'DIV',
                    'span' => 'SPAN',
                    'p' => 'P',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Controls Section
        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Style', 'codexse-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'codexse-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .codexse-page-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'label' => __('Typography', 'codexse-addons'),
                'selector' => '{{WRAPPER}} .codexse-page-title',
            ]
        );

        $this->add_responsive_control(
            'text_align',
            [
                'label' => __('Text Alignment', 'codexse-addons'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'codexse-addons'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'codexse-addons'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'codexse-addons'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justify', 'codexse-addons'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .codexse-page-title' => 'text-align: {{VALUE}};',
                ],
                'default' => 'left',
            ]
        );

        $this->add_responsive_control(
            'margin',
            [
                'label' => __('Margin', 'codexse-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .codexse-page-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'padding',
            [
                'label' => __('Padding', 'codexse-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .codexse-page-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'label' => __('Background', 'codexse-addons'),
                'types' => ['classic', 'gradient', 'video'],
                'selector' => '{{WRAPPER}} .codexse-page-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'label' => __('Border', 'codexse-addons'),
                'selector' => '{{WRAPPER}} .codexse-page-title',
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => __('Border Radius', 'codexse-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .codexse-page-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow',
                'label' => __('Box Shadow', 'codexse-addons'),
                'selector' => '{{WRAPPER}} .codexse-page-title',
            ]
        );

        $this->end_controls_section();
    }

    private function get_page_title($settings) {
        $blog_title = $settings['blog_list_title'];
        $shop_title = $settings['shop_page_title'];
        $error_title = $settings['error_page_title'];

        if (is_home()) {
            return esc_html($blog_title);
        } elseif (is_single()) {
            return get_the_title();
        } elseif (is_search()) {
            return __('Search', 'codexse-addons') . ' : <span class="search_select">' . esc_html(get_search_query()) . '</span>';
        } elseif (is_archive()) {
            if (class_exists('WooCommerce') && is_shop()) {
                return esc_html($shop_title);
            } else {
                return get_the_archive_title();
            }
        } elseif (class_exists('WooCommerce') && is_woocommerce()) {
            if (is_shop()) {
                return esc_html($shop_title);
            } elseif (is_cart()) {
                return esc_html__('Cart', 'codexse-addons');
            } elseif (is_checkout()) {
                return esc_html__('Checkout', 'codexse-addons');
            } elseif (is_account_page()) {
                return esc_html__('My Account', 'codexse-addons');
            } else {
                return woocommerce_page_title(false);
            }
        } elseif (is_404()) {
            return esc_html($error_title);
        } else {
            return single_post_title('', false);
        }
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $page_title = $this->get_page_title($settings);

        if ($page_title) {
            $tag = isset($settings['heading_tag']) ? $settings['heading_tag'] : 'h2';
            echo sprintf('<%1$s class="codexse-page-title">%2$s</%1$s>', esc_attr($tag), wp_kses_post($page_title));
        }
    }
}
