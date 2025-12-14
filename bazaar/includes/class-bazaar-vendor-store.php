<?php
/**
 * Vendor Store Page Class.
 *
 * @package Bazaar\Vendor_Store
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Vendor_Store Class.
 */
class Bazaar_Vendor_Store {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'template_redirect', array( $this, 'handle_store_page' ) );
        add_filter( 'document_title_parts', array( $this, 'store_page_title' ) );
        add_action( 'wp_head', array( $this, 'store_page_meta' ) );
    }

    /**
     * Handle store page display.
     */
    public function handle_store_page() {
        if ( ! bazaar_is_store_page() ) {
            return;
        }

        $vendor_id = bazaar_get_current_store_vendor_id();

        if ( ! $vendor_id ) {
            global $wp_query;
            $wp_query->set_404();
            status_header( 404 );
            return;
        }

        // Check if vendor is active
        if ( ! Bazaar_Roles::is_active_vendor( $vendor_id ) ) {
            global $wp_query;
            $wp_query->set_404();
            status_header( 404 );
            return;
        }

        // Enqueue styles
        wp_enqueue_style( 'bazaar-frontend' );
        wp_enqueue_script( 'bazaar-frontend' );

        // Load template
        add_filter( 'template_include', array( $this, 'load_store_template' ) );
    }

    /**
     * Load store template.
     *
     * @param string $template Template path.
     * @return string
     */
    public function load_store_template( $template ) {
        $store_template = bazaar_locate_template( 'store.php' );

        if ( $store_template ) {
            return $store_template;
        }

        return $template;
    }

    /**
     * Set store page title.
     *
     * @param array $title_parts Title parts.
     * @return array
     */
    public function store_page_title( $title_parts ) {
        if ( ! bazaar_is_store_page() ) {
            return $title_parts;
        }

        $vendor_id = bazaar_get_current_store_vendor_id();

        if ( ! $vendor_id ) {
            return $title_parts;
        }

        // Check for custom SEO title
        $seo_title = get_user_meta( $vendor_id, '_bazaar_seo_title', true );

        if ( $seo_title ) {
            $title_parts['title'] = $seo_title;
        } else {
            $title_parts['title'] = Bazaar_Vendor::get_store_name( $vendor_id );
        }

        return $title_parts;
    }

    /**
     * Add store page meta tags.
     */
    public function store_page_meta() {
        if ( ! bazaar_is_store_page() ) {
            return;
        }

        $vendor_id = bazaar_get_current_store_vendor_id();

        if ( ! $vendor_id ) {
            return;
        }

        $vendor = Bazaar_Vendor::get_vendor( $vendor_id );

        // Meta description
        $description = get_user_meta( $vendor_id, '_bazaar_seo_description', true );

        if ( empty( $description ) ) {
            $description = wp_strip_all_tags( $vendor['description'] );
            $description = wp_trim_words( $description, 30, '...' );
        }

        if ( $description ) {
            echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
        }

        // Open Graph tags
        echo '<meta property="og:title" content="' . esc_attr( $vendor['store_name'] ) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url( $vendor['store_url'] ) . '" />' . "\n";
        echo '<meta property="og:type" content="website" />' . "\n";

        if ( $description ) {
            echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
        }

        if ( $vendor['banner'] ) {
            $banner_url = wp_get_attachment_url( $vendor['banner'] );
            echo '<meta property="og:image" content="' . esc_url( $banner_url ) . '" />' . "\n";
        }
    }

    /**
     * Get store data for template.
     *
     * @param int $vendor_id Vendor ID.
     * @return array
     */
    public static function get_store_data( $vendor_id ) {
        $vendor = Bazaar_Vendor::get_vendor( $vendor_id );

        if ( ! $vendor ) {
            return array();
        }

        // Get products
        $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
        $per_page = apply_filters( 'bazaar_store_products_per_page', 12 );

        $product_args = array(
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $paged,
        );

        // Category filter
        if ( isset( $_GET['product_cat'] ) && ! empty( $_GET['product_cat'] ) ) {
            $product_args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field( wp_unslash( $_GET['product_cat'] ) ),
                ),
            );
        }

        // Orderby
        if ( isset( $_GET['orderby'] ) ) {
            $orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ) );

            switch ( $orderby ) {
                case 'price':
                    $product_args['meta_key'] = '_price';
                    $product_args['orderby'] = 'meta_value_num';
                    $product_args['order'] = 'ASC';
                    break;

                case 'price-desc':
                    $product_args['meta_key'] = '_price';
                    $product_args['orderby'] = 'meta_value_num';
                    $product_args['order'] = 'DESC';
                    break;

                case 'popularity':
                    $product_args['meta_key'] = 'total_sales';
                    $product_args['orderby'] = 'meta_value_num';
                    $product_args['order'] = 'DESC';
                    break;

                case 'rating':
                    $product_args['meta_key'] = '_wc_average_rating';
                    $product_args['orderby'] = 'meta_value_num';
                    $product_args['order'] = 'DESC';
                    break;

                case 'date':
                default:
                    $product_args['orderby'] = 'date';
                    $product_args['order'] = 'DESC';
            }
        }

        $products = Bazaar_Product::get_vendor_products( $vendor_id, $product_args );

        // Get vendor categories
        $categories = self::get_vendor_categories( $vendor_id );

        return array(
            'vendor'     => $vendor,
            'products'   => $products,
            'categories' => $categories,
            'paged'      => $paged,
            'per_page'   => $per_page,
            'is_vacation' => Bazaar_Vendor::is_on_vacation( $vendor_id ),
            'vacation_message' => get_user_meta( $vendor_id, '_bazaar_vacation_message', true ),
        );
    }

    /**
     * Get vendor product categories.
     *
     * @param int $vendor_id Vendor ID.
     * @return array
     */
    public static function get_vendor_categories( $vendor_id ) {
        global $wpdb;

        // Get all product IDs for vendor
        $product_ids = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'product' AND post_status = 'publish' AND post_author = %d",
                $vendor_id
            )
        );

        if ( empty( $product_ids ) ) {
            return array();
        }

        // Get term IDs
        $term_ids = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT DISTINCT tt.term_id
                FROM {$wpdb->term_relationships} tr
                INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                WHERE tt.taxonomy = 'product_cat' AND tr.object_id IN (" . implode( ',', array_map( 'intval', $product_ids ) ) . ")"
            )
        );

        if ( empty( $term_ids ) ) {
            return array();
        }

        return get_terms(
            array(
                'taxonomy'   => 'product_cat',
                'include'    => $term_ids,
                'hide_empty' => true,
            )
        );
    }

    /**
     * Display store header.
     *
     * @param array $vendor Vendor data.
     */
    public static function display_header( $vendor ) {
        bazaar_get_template(
            'store/header.php',
            array( 'vendor' => $vendor )
        );
    }

    /**
     * Display store sidebar.
     *
     * @param array $vendor     Vendor data.
     * @param array $categories Categories.
     */
    public static function display_sidebar( $vendor, $categories ) {
        bazaar_get_template(
            'store/sidebar.php',
            array(
                'vendor'     => $vendor,
                'categories' => $categories,
            )
        );
    }

    /**
     * Display store products.
     *
     * @param array $products Products data.
     * @param int   $paged    Current page.
     */
    public static function display_products( $products, $paged ) {
        bazaar_get_template(
            'store/products.php',
            array(
                'products' => $products,
                'paged'    => $paged,
            )
        );
    }

    /**
     * Display vacation notice.
     *
     * @param string $message Vacation message.
     */
    public static function display_vacation_notice( $message ) {
        ?>
        <div class="bazaar-vacation-notice">
            <span class="dashicons dashicons-palmtree"></span>
            <h3><?php esc_html_e( 'Store Currently Closed', 'bazaar' ); ?></h3>
            <?php if ( $message ) : ?>
                <p><?php echo esc_html( $message ); ?></p>
            <?php else : ?>
                <p><?php esc_html_e( 'This store is temporarily closed for vacation. Please check back later.', 'bazaar' ); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }
}

// Initialize
new Bazaar_Vendor_Store();
