<?php
if ( ! class_exists( 'Brainforward_Widget_Init' ) ) {
    class Brainforward_Widget_Init {
        public function __construct() {
            add_action( 'widgets_init', array( $this, 'widgets_init' ) );
        }

        public function widgets_init() {
            $sidebar = get_theme_mod('navbar_sidebar_setting', 'hide');


            register_sidebar( array(
                'name'          => esc_html__( 'Sidebar', 'brainforward' ),
                'id'            => 'main_sidebar',
                'description'   => esc_html__( 'This sidebar appears in the blog pages on the website.', 'brainforward' ),
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h5 class="widget_title">',
                'after_title'   => '</h5>',
            ) );
            
            if ( class_exists( 'WooCommerce' ) ) {            
                register_sidebar( array(
                    'name'          => esc_html__( 'WooCommerce Sidebar', 'brainforward' ),
                    'id'            => 'woocommerce_sidebar',
                    'description'   => esc_html__( 'This sidebar appears in the wooCommerce pages on the website.', 'brainforward' ),
                    'before_widget' => '<div id="%1$s" class="widget %2$s">',
                    'after_widget'  => '</div>',
                    'before_title'  => '<h5 class="widget_title">',
                    'after_title'   => '</h5>',
                ) );
            }

            if ( $sidebar != 'hide' ) {
                register_sidebar( array(
                    'name'          => esc_html__( 'Navbar Toggle Sidebar', 'brainforward' ),
                    'id'            => 'navbar_toggle_sidebar',
                    'description'   => esc_html__( 'This sidebar appears in the toggle navigation menu on the website.', 'brainforward' ),
                    'before_widget' => '<div id="%1$s" class="widget %2$s">',
                    'after_widget'  => '</div>',
                    'before_title'  => '<h5 class="widget_title">',
                    'after_title'   => '</h5>',
                ) );
            }
        }
    }

    new Brainforward_Widget_Init();
}
?>
