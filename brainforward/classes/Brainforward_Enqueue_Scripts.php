<?php
class Enqueue_Scripts {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', [$this, 'brainforward_scripts']);
        add_action( 'admin_enqueue_scripts', [$this, 'brainforward_admin_scripts']);
        add_action( 'wp_print_footer_scripts', [$this, 'brainforward_skip_link_focus_fix'] );
    }

    
    public function brainforward_fonts_url() {
        $fonts_url = '';
        $fonts     = [];
        $subsets   = 'latin,latin-ext';
        $fonts[] = 'Roboto:ital,wght@0,100..900;1,100..900';
        $fonts[] = 'Noto+Sans+Bengali:wght@100..900';
        if ( ! empty( $fonts ) ) {
            $fonts_url = add_query_arg(
                [
                    'family' => implode( '&family=', $fonts ),
                    'subset' => $subsets,
                    'display' => 'swap',
                ],
                'https://fonts.googleapis.com/css2'
            );
        }
        return esc_url_raw( $fonts_url );
    }



    public function brainforward_scripts() {
        $body_fonts = get_theme_mod('body_font_family', 'Kumbh Sans');
        $heading_fonts = get_theme_mod('heading_font_family', 'Kumbh Sans');
        if (!isset($body_fonts) || !isset($heading_fonts) || $body_fonts == 'default' || $heading_fonts == 'default') {
            wp_enqueue_style('brainforward-custom-fonts', $this->brainforward_fonts_url(), [], null);
        }

        wp_enqueue_style('bootstrap', BRAINFORWARD_ASSETS_URI . '/css/bootstrap-min.css', [], wp_get_theme()->get('Version'));
        wp_enqueue_style('normalizer', BRAINFORWARD_ASSETS_URI . '/css/normalize.css', [], wp_get_theme()->get('Version'));
        wp_enqueue_style('remixicon', BRAINFORWARD_ASSETS_URI . '/css/remixicon.css', [], wp_get_theme()->get('Version'));
        wp_enqueue_style('brainforward-login', BRAINFORWARD_ASSETS_URI . '/css/login.css', [], wp_get_theme()->get('Version'));

        if (class_exists('WooCommerce')) {
            // Dequeue default WooCommerce styles
            wp_dequeue_style('woocommerce-layout');
            wp_dequeue_style('woocommerce-smallscreen');
            wp_dequeue_style('woocommerce-general');
    
            wp_enqueue_style('brainforward-woocommerce-general', BRAINFORWARD_ASSETS_URI . '/css/woocommerce.css', [], wp_get_theme()->get('Version'));
            wp_enqueue_style('brainforward-woocommerce-layout', BRAINFORWARD_ASSETS_URI . '/css/woocommerce-layout.css', [], wp_get_theme()->get('Version'));
            wp_enqueue_style('brainforward-woocommerce-smallscreen', BRAINFORWARD_ASSETS_URI . '/css/woocommerce-smallscreen.css', [], wp_get_theme()->get('Version'));
        }

        wp_enqueue_style('brainforward-button', BRAINFORWARD_ASSETS_URI . '/css/button.css', [], wp_get_theme()->get('Version'));
        wp_enqueue_style('brainforward-navmenu', BRAINFORWARD_ASSETS_URI . '/css/navmenu.css', [], wp_get_theme()->get('Version'));
        wp_enqueue_style('brainforward-theme', BRAINFORWARD_ASSETS_URI . '/css/theme.css', [], wp_get_theme()->get('Version'));
        wp_enqueue_style('brainforward-style', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
        wp_style_add_data('brainforward-style', 'rtl', 'replace');
        wp_enqueue_style('brainforward-responsive', BRAINFORWARD_ASSETS_URI . '/css/responsive.css', [], wp_get_theme()->get('Version'));

        wp_enqueue_script('html5shiv', BRAINFORWARD_ASSETS_URI . '/js/vendor/html5shiv-min.js', [], '3.7.2');
        wp_script_add_data('html5shiv', 'conditional', 'lt IE 9');
        wp_enqueue_script('respond', BRAINFORWARD_ASSETS_URI . '/js/vendor/respond-min.js', [], '1.4.2');
        wp_script_add_data('respond', 'conditional', 'lt IE 9');
        wp_enqueue_script('brainforward-login', BRAINFORWARD_ASSETS_URI . '/js/login.js', ['jquery'], wp_get_theme()->get('Version'), true);
        wp_localize_script('brainforward-login', 'brainforward_login_obj', [ 'ajax_url' => admin_url('admin-ajax.php'), 'nonce'    => wp_create_nonce('brainforward_login_nonce'), ]);
        wp_enqueue_script('jquery-fitvids', BRAINFORWARD_ASSETS_URI . '/js/fitvids.js', ['jquery'], '1.1.0', true);
        wp_enqueue_script('jquery-prefixfree', BRAINFORWARD_ASSETS_URI . '/js/prefixfree-min.js', ['jquery'], '1.1.0', true);
        wp_enqueue_script('animatenumber', BRAINFORWARD_ASSETS_URI . '/js/animatenumber-min.js', ['jquery'], '1.0.0', true);
        wp_enqueue_script('jquery-appear', BRAINFORWARD_ASSETS_URI . '/js/jquery-appear.js', ['jquery'], '0.3.3', true);
        wp_enqueue_script('bootstrap', BRAINFORWARD_ASSETS_URI . '/js/bootstrap-bundle-min.js', ['jquery'], '5.1.1', true);

        if (class_exists('WooCommerce')) {
            wp_enqueue_script('brainforward-wc-scripts', BRAINFORWARD_ASSETS_URI . '/js/wc-scripts.js', ['jquery'], wp_get_theme()->get('Version'), true);
        }

        wp_enqueue_script('brainforward-scripts', BRAINFORWARD_ASSETS_URI . '/js/scripts.js', ['jquery', 'jquery-masonry', 'imagesloaded'], wp_get_theme()->get('Version'), true);

        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }

    }

    public function brainforward_admin_scripts() {
        wp_enqueue_style('admin-css', BRAINFORWARD_ASSETS_URI . '/css/admin.css', [], '1.0.0');
    }

    public function brainforward_skip_link_focus_fix() {
        ?>
        <script>
            /(trident|msie)/i.test(navigator.userAgent) && document.getElementById && window.addEventListener && window.addEventListener("hashchange", function() {
                var t, e = location.hash.substring(1);
                /^[A-z0-9_-]+$/.test(e) && (t = document.getElementById(e)) && (/^(?:a|select|input|button|textarea)$/i.test(t.tagName) || (t.tabIndex = -1), t.focus())
            }, !1);
        </script>
        <?php
    }


}

// Instantiate the class
new Enqueue_Scripts();
