<?php
use Elementor\Icons_Manager; // Correct use statement
use Elementor\Utils; // Correct use statement

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Icon render
 */
class Codexse_Addons_Icon_manager extends Icons_Manager {
    private static function render_svg_icon( $value ) {
        if ( ! isset( $value['id'] ) ) {
            return '';
        }
        error_log( 'Rendering SVG Icon: ' . print_r( $value, true ) );
        return Codexse_Addons_Functions::elementor_version_check( '>=', '3.5.0' ) ? 
            \Elementor\Core\Files\File_Types\Svg::get_inline_svg( $value['id'] ) : 
            \Elementor\Core\Files\Assets\Svg\Svg_Handler::get_inline_svg( $value['id'] );
    }

    private static function render_icon_html( $icon, $attributes = [], $tag = 'i' ) {
        $icon_types = self::get_icon_manager_tabs();
        if ( isset( $icon_types[ $icon['library'] ]['render_callback'] ) && is_callable( $icon_types[ $icon['library'] ]['render_callback'] ) ) {
            return call_user_func_array( $icon_types[ $icon['library'] ]['render_callback'], [ $icon, $attributes, $tag ] );
        }

        if ( empty( $attributes['class'] ) ) {
            $attributes['class'] = $icon['value'];
        } else {
            if ( is_array( $attributes['class'] ) ) {
                $attributes['class'][] = $icon['value'];
            } else {
                $attributes['class'] .= ' ' . $icon['value'];
            }
        }
        return '<' . $tag . ' ' . Utils::render_html_attributes( $attributes ) . '></' . $tag . '>';
    }

    public static function render_icon( $icon, $attributes = [], $tag = 'i' ) {
        if ( empty( $icon['library'] ) ) {
            return false;
        }
        $output = '';
        // handler SVG Icon
        if ( 'svg' === $icon['library'] ) {
            $output = self::render_svg_icon( $icon['value'] );
        } else {
            $output = self::render_icon_html( $icon, $attributes, $tag );
        }
        return $output;
    }
}

class Codexse_Addons_Desktop_Menu_Walker extends Walker_Nav_Menu {

    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {

        global $wp_query;

        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $class_names = $value = '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));

        $class_names = ' class="' . esc_attr($class_names) . ' nav-item"';

        $output .= $indent . '<li id="menu-item-' . $item->ID . '"' . $value . $class_names . '>';

        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';

        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';

        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';

        if (Codexse_Addons_Functions::detect_homepage() == true) {

            $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

        } else {

            if ($item->type_label == 'Custom Link' and strpos($item->url, '#') === 0) {

                $attributes .= !empty($item->url) ? ' href="' . home_url() . esc_attr($item->url) . '"' : '';

            } else {

                $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

            }

        }

        $item_output = $args->before;

        $item_output .= '<a' . $attributes . '>';

        $item_output .= '<span class="nav-icon">' . do_shortcode($item->description) . '</span>';

        $item_output .= $args->link_before . do_shortcode(apply_filters('the_title', $item->title, $item->ID)) . $args->link_after;

        // Check if the current item has children
        if ($args->walker->has_children) {
            $item_output .= '<i class="plus"></i>';
        }

        $item_output .= '</a>';

        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}

class Codexse_Addons_Mobile_Menu_Walker extends Walker_Nav_Menu {

    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {

        global $wp_query;

        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $class_names = $value = '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));

        $class_names = ' class="' . esc_attr($class_names) . ' nav-item"';

        $output .= $indent . '<li id="menu-item-' . $item->ID . '"' . $value . $class_names . '>';

        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';

        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';

        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';

        if (Codexse_Addons_Functions::detect_homepage() == true) {

            $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

        } else {

            if ($item->type_label == 'Custom Link' and strpos($item->url, '#') === 0) {

                $attributes .= !empty($item->url) ? ' href="' . home_url() . esc_attr($item->url) . '"' : '';

            } else {

                $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

            }

        }

        $item_output = $args->before;

        $item_output .= '<a' . $attributes . '>';

        $item_output .= '<span class="nav-icon">' . do_shortcode($item->description) . '</span>';

        $item_output .= $args->link_before . do_shortcode(apply_filters('the_title', $item->title, $item->ID)) . $args->link_after;

        $item_output .= '</a>';

        // Check if the current item has children
        if ($args->walker->has_children) {
            $item_output .= '<button class="collapse-arrow"><i class="cx cx-plus"></i></button>';
        }

        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}
