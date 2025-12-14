<?php
/**
 * Shortcodes Class.
 *
 * @package Bazaar\Shortcodes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Shortcodes Class.
 */
class Bazaar_Shortcodes {

    /**
     * Init shortcodes.
     */
    public static function init() {
        $shortcodes = array(
            'bazaar_vendor_dashboard'     => __CLASS__ . '::vendor_dashboard',
            'bazaar_vendor_registration'  => __CLASS__ . '::vendor_registration',
            'bazaar_store_listing'        => __CLASS__ . '::store_listing',
            'bazaar_best_selling_vendors' => __CLASS__ . '::best_selling_vendors',
            'bazaar_featured_vendors'     => __CLASS__ . '::featured_vendors',
            'bazaar_vendor_products'      => __CLASS__ . '::vendor_products',
        );

        foreach ( $shortcodes as $shortcode => $callback ) {
            add_shortcode( $shortcode, $callback );
        }
    }

    /**
     * Vendor dashboard shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public static function vendor_dashboard( $atts ) {
        $atts = shortcode_atts(
            array(),
            $atts,
            'bazaar_vendor_dashboard'
        );

        // Check if user is logged in
        if ( ! is_user_logged_in() ) {
            return self::get_login_form();
        }

        // Check if user is vendor
        $user_id = get_current_user_id();

        if ( ! Bazaar_Roles::is_vendor( $user_id ) ) {
            return '<div class="bazaar-notice">' . __( 'You are not registered as a vendor.', 'bazaar' ) . '</div>';
        }

        // Check vendor status
        $status = Bazaar_Roles::get_vendor_status( $user_id );

        if ( 'pending' === $status ) {
            return '<div class="bazaar-notice bazaar-notice-info">' . __( 'Your vendor account is pending approval.', 'bazaar' ) . '</div>';
        }

        if ( 'rejected' === $status ) {
            return '<div class="bazaar-notice bazaar-notice-error">' . __( 'Your vendor application was not approved.', 'bazaar' ) . '</div>';
        }

        if ( 'disabled' === $status ) {
            return '<div class="bazaar-notice bazaar-notice-error">' . __( 'Your vendor account has been disabled.', 'bazaar' ) . '</div>';
        }

        // Enqueue assets
        wp_enqueue_style( 'bazaar-dashboard' );
        wp_enqueue_script( 'bazaar-dashboard' );

        // Get current tab
        $current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'overview';

        ob_start();
        bazaar_get_template(
            'dashboard/dashboard.php',
            array(
                'vendor_id'   => $user_id,
                'current_tab' => $current_tab,
            )
        );
        return ob_get_clean();
    }

    /**
     * Vendor registration shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public static function vendor_registration( $atts ) {
        $atts = shortcode_atts(
            array(),
            $atts,
            'bazaar_vendor_registration'
        );

        // Check if registration is enabled
        if ( 'enabled' !== get_option( 'bazaar_vendor_registration', 'enabled' ) ) {
            return '<div class="bazaar-notice">' . __( 'Vendor registration is currently closed.', 'bazaar' ) . '</div>';
        }

        // Check if already logged in as vendor
        if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();

            if ( Bazaar_Roles::is_vendor( $user_id ) ) {
                $dashboard_url = bazaar_get_dashboard_url();
                return '<div class="bazaar-notice bazaar-notice-info">' .
                    sprintf(
                        /* translators: %s: dashboard URL */
                        __( 'You are already a vendor. <a href="%s">Go to Dashboard</a>', 'bazaar' ),
                        esc_url( $dashboard_url )
                    ) . '</div>';
            }
        }

        wp_enqueue_style( 'bazaar-frontend' );
        wp_enqueue_script( 'bazaar-frontend' );

        ob_start();
        bazaar_get_template( 'vendor-registration.php' );
        return ob_get_clean();
    }

    /**
     * Store listing shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public static function store_listing( $atts ) {
        $atts = shortcode_atts(
            array(
                'per_page' => 12,
                'orderby'  => 'registered',
                'order'    => 'DESC',
                'category' => '',
            ),
            $atts,
            'bazaar_store_listing'
        );

        wp_enqueue_style( 'bazaar-frontend' );

        $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

        $args = array(
            'status'  => 'approved',
            'number'  => intval( $atts['per_page'] ),
            'paged'   => $paged,
            'orderby' => $atts['orderby'],
            'order'   => $atts['order'],
        );

        $vendors = Bazaar_Vendor::get_vendors( $args );

        ob_start();
        bazaar_get_template(
            'store-listing.php',
            array(
                'vendors'  => $vendors,
                'per_page' => $atts['per_page'],
                'paged'    => $paged,
            )
        );
        return ob_get_clean();
    }

    /**
     * Best selling vendors shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public static function best_selling_vendors( $atts ) {
        $atts = shortcode_atts(
            array(
                'limit' => 4,
            ),
            $atts,
            'bazaar_best_selling_vendors'
        );

        wp_enqueue_style( 'bazaar-frontend' );

        $top_vendors = Bazaar_Commission::get_top_vendors( intval( $atts['limit'] ) );

        $vendors = array();
        foreach ( $top_vendors as $vendor_data ) {
            $vendor = Bazaar_Vendor::get_vendor( $vendor_data->vendor_id );
            if ( $vendor ) {
                $vendor['sales'] = $vendor_data->sales;
                $vendors[] = $vendor;
            }
        }

        ob_start();
        bazaar_get_template(
            'vendors-grid.php',
            array(
                'vendors' => $vendors,
                'title'   => __( 'Best Selling Vendors', 'bazaar' ),
            )
        );
        return ob_get_clean();
    }

    /**
     * Featured vendors shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public static function featured_vendors( $atts ) {
        $atts = shortcode_atts(
            array(
                'limit' => 4,
            ),
            $atts,
            'bazaar_featured_vendors'
        );

        wp_enqueue_style( 'bazaar-frontend' );

        $args = array(
            'status'     => 'approved',
            'number'     => intval( $atts['limit'] ),
            'meta_query' => array(
                array(
                    'key'   => '_bazaar_featured',
                    'value' => 'yes',
                ),
            ),
        );

        $vendor_query = Bazaar_Vendor::get_vendors( $args );
        $vendors = array();

        foreach ( $vendor_query['vendors'] as $user ) {
            $vendors[] = Bazaar_Vendor::get_vendor( $user->ID );
        }

        ob_start();
        bazaar_get_template(
            'vendors-grid.php',
            array(
                'vendors' => $vendors,
                'title'   => __( 'Featured Vendors', 'bazaar' ),
            )
        );
        return ob_get_clean();
    }

    /**
     * Vendor products shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public static function vendor_products( $atts ) {
        $atts = shortcode_atts(
            array(
                'vendor_id' => 0,
                'limit'     => 12,
                'columns'   => 4,
                'orderby'   => 'date',
                'order'     => 'DESC',
            ),
            $atts,
            'bazaar_vendor_products'
        );

        $vendor_id = intval( $atts['vendor_id'] );

        if ( ! $vendor_id ) {
            return '';
        }

        $products = Bazaar_Product::get_vendor_products(
            $vendor_id,
            array(
                'post_status'    => 'publish',
                'posts_per_page' => intval( $atts['limit'] ),
                'orderby'        => $atts['orderby'],
                'order'          => $atts['order'],
            )
        );

        if ( empty( $products['products'] ) ) {
            return '';
        }

        ob_start();

        woocommerce_product_loop_start();

        foreach ( $products['products'] as $product_post ) {
            $GLOBALS['post'] = $product_post;
            setup_postdata( $product_post );
            wc_get_template_part( 'content', 'product' );
        }

        woocommerce_product_loop_end();

        wp_reset_postdata();

        return ob_get_clean();
    }

    /**
     * Get login form.
     *
     * @return string
     */
    private static function get_login_form() {
        ob_start();
        ?>
        <div class="bazaar-login-notice">
            <p><?php esc_html_e( 'Please log in to access your vendor dashboard.', 'bazaar' ); ?></p>
            <?php
            wp_login_form(
                array(
                    'redirect' => bazaar_get_dashboard_url(),
                )
            );
            ?>
            <p>
                <?php
                printf(
                    /* translators: %s: registration URL */
                    esc_html__( 'Not a vendor yet? %s', 'bazaar' ),
                    '<a href="' . esc_url( bazaar_get_registration_url() ) . '">' . esc_html__( 'Register Now', 'bazaar' ) . '</a>'
                );
                ?>
            </p>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Initialize shortcodes
add_action( 'init', array( 'Bazaar_Shortcodes', 'init' ) );
