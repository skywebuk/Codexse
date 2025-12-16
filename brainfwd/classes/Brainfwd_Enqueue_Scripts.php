<?php
class Brainfwd_Enqueue_Scripts {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', [$this, 'brainfwd_scripts']);
        add_action( 'admin_enqueue_scripts', [$this, 'brainfwd_admin_scripts']);
        add_action( 'wp_print_footer_scripts', [$this, 'brainfwd_skip_link_focus_fix'] );
    }

    
    public function brainfwd_fonts_url() {
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



    public function brainfwd_scripts() {
        $body_fonts = get_theme_mod('body_font_family_setting', 'Roboto');
        $heading_fonts = get_theme_mod('heading_font_family_setting', 'Bebas Neue');
        if (empty($body_fonts) || empty($heading_fonts) || $body_fonts === 'default' || $heading_fonts === 'default') {
            wp_enqueue_style('brainfwd-custom-fonts', $this->brainfwd_fonts_url(), [], null);
        }

        wp_enqueue_style('bootstrap', BRAINFWD_ASSETS_URI . '/css/bootstrap-min.css', [], BRAINFWD_THEME_VERSION);
        wp_enqueue_style('normalizer', BRAINFWD_ASSETS_URI . '/css/normalize.css', [], BRAINFWD_THEME_VERSION);
        wp_enqueue_style('remixicon', BRAINFWD_ASSETS_URI . '/css/remixicon.css', [], BRAINFWD_THEME_VERSION);
        wp_enqueue_style('brainfwd-login', BRAINFWD_ASSETS_URI . '/css/login.css', [], BRAINFWD_THEME_VERSION);

        if (class_exists('WooCommerce')) {
            // Dequeue default WooCommerce styles
            wp_dequeue_style('woocommerce-layout');
            wp_dequeue_style('woocommerce-smallscreen');
            wp_dequeue_style('woocommerce-general');

            wp_enqueue_style('brainfwd-woocommerce-general', BRAINFWD_ASSETS_URI . '/css/woocommerce.css', [], BRAINFWD_THEME_VERSION);
            wp_enqueue_style('brainfwd-woocommerce-layout', BRAINFWD_ASSETS_URI . '/css/woocommerce-layout.css', [], BRAINFWD_THEME_VERSION);
            wp_enqueue_style('brainfwd-woocommerce-smallscreen', BRAINFWD_ASSETS_URI . '/css/woocommerce-smallscreen.css', [], BRAINFWD_THEME_VERSION);
        }

        wp_enqueue_style('brainfwd-button', BRAINFWD_ASSETS_URI . '/css/button.css', [], BRAINFWD_THEME_VERSION);
        wp_enqueue_style('brainfwd-navmenu', BRAINFWD_ASSETS_URI . '/css/navmenu.css', [], BRAINFWD_THEME_VERSION);
        wp_enqueue_style('brainfwd-theme', BRAINFWD_ASSETS_URI . '/css/theme.css', [], BRAINFWD_THEME_VERSION);
        wp_enqueue_style('brainfwd-style', get_stylesheet_uri(), [], BRAINFWD_THEME_VERSION);
        wp_style_add_data('brainfwd-style', 'rtl', 'replace');
        wp_enqueue_style('brainfwd-responsive', BRAINFWD_ASSETS_URI . '/css/responsive.css', [], BRAINFWD_THEME_VERSION);

        wp_enqueue_script('html5shiv', BRAINFWD_ASSETS_URI . '/js/vendor/html5shiv-min.js', [], '3.7.2');
        wp_script_add_data('html5shiv', 'conditional', 'lt IE 9');
        wp_enqueue_script('respond', BRAINFWD_ASSETS_URI . '/js/vendor/respond-min.js', [], '1.4.2');
        wp_script_add_data('respond', 'conditional', 'lt IE 9');
        wp_enqueue_script('brainfwd-login', BRAINFWD_ASSETS_URI . '/js/login.js', ['jquery'], BRAINFWD_THEME_VERSION, true);
        wp_localize_script('brainfwd-login', 'brainfwd_login_obj', [ 'ajax_url' => admin_url('admin-ajax.php'), 'nonce'    => wp_create_nonce('brainfwd_login_nonce'), ]);
        wp_enqueue_script('jquery-fitvids', BRAINFWD_ASSETS_URI . '/js/fitvids.js', ['jquery'], '1.1.0', true);
        wp_enqueue_script('jquery-prefixfree', BRAINFWD_ASSETS_URI . '/js/prefixfree-min.js', ['jquery'], '1.1.0', true);
        wp_enqueue_script('animatenumber', BRAINFWD_ASSETS_URI . '/js/animatenumber-min.js', ['jquery'], '1.0.0', true);
        wp_enqueue_script('jquery-appear', BRAINFWD_ASSETS_URI . '/js/jquery-appear.js', ['jquery'], '0.3.3', true);
        wp_enqueue_script('bootstrap', BRAINFWD_ASSETS_URI . '/js/bootstrap-bundle-min.js', ['jquery'], '5.1.1', true);

        if (class_exists('WooCommerce')) {
            wp_enqueue_script('brainfwd-wc-scripts', BRAINFWD_ASSETS_URI . '/js/wc-scripts.js', ['jquery'], BRAINFWD_THEME_VERSION, true);
        }

        wp_enqueue_script('brainfwd-scripts', BRAINFWD_ASSETS_URI . '/js/scripts.js', ['jquery', 'jquery-masonry', 'imagesloaded'], BRAINFWD_THEME_VERSION, true);

        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }

    }

    public function brainfwd_admin_scripts() {
        wp_enqueue_style('admin-css', BRAINFWD_ASSETS_URI . '/css/admin.css', [], BRAINFWD_THEME_VERSION);
    }

    public function brainfwd_skip_link_focus_fix() {
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
new Brainfwd_Enqueue_Scripts();
