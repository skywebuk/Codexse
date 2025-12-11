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

class Codexse_Addons_Elementor_Widget_Food_Menu extends Widget_Base {

    public function get_name() {
        return 'codexse_food_menu';
    }

    public function get_title() {
        return __( 'Food Menu', 'codexse-addons' );
    }

    public function get_icon() {
        return 'cx-addons-icon  eicon-menu-card';
    }

    public function get_categories() {
        return [ 'codexse-addons' ];
    }

    public function get_keywords() {
        return [ 'food', 'menu', 'dish', 'restaurant', 'codexse' ];
    }

    public function get_style_depends() {
        return [ 'codexse-food-menu'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'food_menu_section',
            [
                'label' => __( 'Food Menu Items', 'codexse-addons' ),
            ]
        );

        $repeater = new Repeater();

        // Food Image
        $repeater->add_control(
            'food_image',
            [
                'label' => __( 'Food Image', 'codexse-addons' ),
                'type' => Controls_Manager::MEDIA,
            ]
        );

        // Food Image Size
        $repeater->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'food_image_size',
                'default' => 'medium',
                'separator' => 'none',
            ]
        );

        // Food Name
        $repeater->add_control(
            'food_name',
            [
                'label' => __( 'Food Name', 'codexse-addons' ),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __( 'Enter food name', 'codexse-addons' ),
            ]
        );

        // Food Description
        $repeater->add_control(
            'food_description',
            [
                'label' => __( 'Food Description', 'codexse-addons' ),
                'type' => Controls_Manager::TEXTAREA,
                'placeholder' => __( 'Enter food description', 'codexse-addons' ),
            ]
        );

        // Food Price
        $repeater->add_control(
            'food_price',
            [
                'label' => __( 'Food Price', 'codexse-addons' ),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __( 'Enter food price', 'codexse-addons' ),
            ]
        );
        
        // Food Link
        $repeater->add_control(
            'food_link',
            [
                'label' => __( 'Food Link', 'codexse-addons' ),
                'type' => Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com', 'codexse-addons' ),
                'show_external' => true,
                'default' => [
                    'url' => '',
                    'is_external' => false,
                    'nofollow' => false,
                ],
            ]
        );


        $this->add_control(
            'food_menu_list',
            [
                'label' => __( 'Menu Items', 'codexse-addons' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'food_name' => __( 'Grilled Chicken', 'codexse-addons' ),
                        'food_description' => __( 'Delicious grilled chicken with herbs.', 'codexse-addons' ),
                        'food_price' => __( '$12.99', 'codexse-addons' ),
                    ],
                    [
                        'food_name' => __( 'Veggie Pizza', 'codexse-addons' ),
                        'food_description' => __( 'Fresh veggies with cheese on a crispy crust.', 'codexse-addons' ),
                        'food_price' => __( '$9.99', 'codexse-addons' ),
                    ],
                ],
                'title_field' => '{{{ food_name }}}',
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if ( empty( $settings['food_menu_list'] ) ) {
            return;
        }

        echo '<div class="codexse-food-menu">';

        foreach ( $settings['food_menu_list'] as $item ) {

            echo '<div class="food-menu-item">';
            echo '<span class="food-menu-shape shape-top"></span>';
            echo '<span class="food-menu-shape shape-right"></span>';
            echo '<span class="food-menu-shape shape-bottom"></span>';
            echo '<span class="food-menu-shape shape-left"></span>';

            // Image
            if ( ! empty( $item['food_image']['id'] ) ) {
                $image_html = \Elementor\Group_Control_Image_Size::get_attachment_image_html( $item, 'food_image_size', 'food_image' );
                echo '<div class="food-menu-image">' . $image_html . '</div>';
            }

            echo '<div class="food-menu-content">';

            // Food Price
            if ( ! empty( $item['food_price'] ) ) {
                echo '<span class="food-menu-price">' . esc_html( $item['food_price'] ) . '</span>';
            }

            // Food Name
            if ( ! empty( $item['food_name'] ) ) {
                echo '<h6 class="food-menu-name">' . esc_html( $item['food_name'] ) . '</h6>';
            }

            // Food Description
            if ( ! empty( $item['food_description'] ) ) {
                echo '<p class="food-menu-description">' . esc_html( $item['food_description'] ) . '</p>';
            }


            echo '</div>'; // .food-menu-content

            echo '</div>'; // .food-menu-item
        }

        echo '</div>'; // .codexse-food-menu
    }


}
