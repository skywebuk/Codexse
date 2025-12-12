<?php

class Brainforward_Menu_Walker extends Walker_Nav_Menu {

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

        if (Brainforward_Setup::detect_homepage() == true) {

            $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

        } else {

            if ($item->type_label == 'Custom Link' and strpos($item->url, '#') === 0) {

                $attributes .= !empty($item->url) ? ' href="' . home_url() . esc_attr($item->url) . '"' : '';

            } else {

                $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

            }

        }

        $item_output = $args->before;

        $item_output .= '<a' . $attributes . ' class="nav-link">';

        $item_output .= '<span class="nav__icon">' . do_shortcode($item->description) . '</span>';

        $item_output .= $args->link_before . do_shortcode(apply_filters('the_title', $item->title, $item->ID)) . $args->link_after;

        $item_output .= '</a>';

        // Check if the current item has children
        if ($args->walker->has_children) {
            $item_output .= '<button class="sub__menu-toggle"><i class="ri-add-line"></i></button>';
        }

        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}

if (class_exists('WP_Customize_Control')) {
    class WP_Customize_Heading_Control extends WP_Customize_Control {
        public $type = 'heading'; // This type will be used to identify this custom control
        public function render_content() {
            if ( empty( $this->label ) ) {
                return;
            }
            ?>
            <h5 class="devider-title"><?php echo esc_html( $this->label ); ?></h5>
            <?php
        }
    }

    class Brainforward_Radio_Image_Control extends WP_Customize_Control {
        public $type = 'radio_image';
    
        public function render_content() {
            if (empty($this->choices)) {
                return;
            }
            $name = '_customize-radio-' . $this->id; ?>
            <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php if (!empty($this->description)) : ?>
                <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
            <?php endif; ?>
            <div id="input_<?php echo esc_attr($this->id); ?>" class="customizer-select-image-control">
                <?php foreach ($this->choices as $value => $label) : ?>
                    <label for="<?php echo esc_attr($this->id . '_' . $value); ?>">
                        <input type="radio" id="<?php echo esc_attr($this->id . '_' . $value); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>" <?php $this->link(); checked($this->value(), $value); ?> />
                        <img src="<?php echo esc_url($label); ?>" alt="<?php echo esc_attr($value); ?>" />
                    </label>
                <?php endforeach; ?>
            </div>
            <?php
        }
    }
    
}

?>
