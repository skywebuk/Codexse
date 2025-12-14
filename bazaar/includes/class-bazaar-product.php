<?php
/**
 * Product Management Class.
 *
 * @package Bazaar\Product
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Product Class.
 */
class Bazaar_Product {

    /**
     * Constructor.
     */
    public function __construct() {
        // Filter products by vendor
        add_action( 'pre_get_posts', array( $this, 'filter_products_by_vendor' ) );

        // Add vendor info to product
        add_action( 'woocommerce_product_meta_start', array( $this, 'display_vendor_info' ) );

        // Product save hooks
        add_action( 'save_post_product', array( $this, 'save_product_vendor' ), 10, 3 );

        // Product data panel
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_vendor_product_tab' ) );
        add_action( 'woocommerce_product_data_panels', array( $this, 'vendor_product_data_panel' ) );
        add_action( 'woocommerce_process_product_meta', array( $this, 'save_vendor_product_data' ) );
    }

    /**
     * Get product vendor ID.
     *
     * @param int $product_id Product ID.
     * @return int
     */
    public static function get_product_vendor( $product_id ) {
        $product = wc_get_product( $product_id );

        if ( ! $product ) {
            return 0;
        }

        // First check meta
        $vendor_id = get_post_meta( $product_id, '_bazaar_vendor_id', true );

        if ( $vendor_id ) {
            return intval( $vendor_id );
        }

        // Fallback to author
        $post = get_post( $product_id );

        if ( $post && Bazaar_Roles::is_vendor( $post->post_author ) ) {
            return $post->post_author;
        }

        return 0;
    }

    /**
     * Check if product belongs to vendor.
     *
     * @param int $product_id Product ID.
     * @param int $vendor_id  Vendor ID.
     * @return bool
     */
    public static function is_vendor_product( $product_id, $vendor_id ) {
        return self::get_product_vendor( $product_id ) === $vendor_id;
    }

    /**
     * Get vendor products.
     *
     * @param int   $vendor_id Vendor ID.
     * @param array $args      Query arguments.
     * @return array
     */
    public static function get_vendor_products( $vendor_id, $args = array() ) {
        $defaults = array(
            'post_type'      => 'product',
            'post_status'    => 'any',
            'author'         => $vendor_id,
            'posts_per_page' => 20,
            'paged'          => 1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        $args = wp_parse_args( $args, $defaults );

        // Status filter
        if ( isset( $args['status'] ) && 'all' !== $args['status'] ) {
            $args['post_status'] = $args['status'];
            unset( $args['status'] );
        }

        // Search
        if ( ! empty( $args['search'] ) ) {
            $args['s'] = $args['search'];
            unset( $args['search'] );
        }

        // Product type filter
        if ( ! empty( $args['product_type'] ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => $args['product_type'],
                ),
            );
            unset( $args['product_type'] );
        }

        $query = new WP_Query( $args );

        return array(
            'products' => $query->posts,
            'total'    => $query->found_posts,
            'pages'    => $query->max_num_pages,
        );
    }

    /**
     * Create product for vendor.
     *
     * @param int   $vendor_id Vendor ID.
     * @param array $data      Product data.
     * @return int|WP_Error
     */
    public static function create_product( $vendor_id, $data ) {
        if ( ! Bazaar_Roles::current_user_can( 'bazaar_add_product', $vendor_id ) ) {
            return new WP_Error( 'permission_denied', __( 'You do not have permission to add products.', 'bazaar' ) );
        }

        // Validate required fields
        if ( empty( $data['title'] ) ) {
            return new WP_Error( 'missing_title', __( 'Product title is required.', 'bazaar' ) );
        }

        // Determine post status based on moderation settings
        $moderation = get_option( 'bazaar_product_moderation', 'enabled' );
        $post_status = 'enabled' === $moderation ? 'pending' : 'publish';

        // Allow override for admins
        if ( current_user_can( 'manage_options' ) && ! empty( $data['status'] ) ) {
            $post_status = $data['status'];
        }

        // Create the product
        $product_data = array(
            'post_title'   => sanitize_text_field( $data['title'] ),
            'post_content' => isset( $data['description'] ) ? wp_kses_post( $data['description'] ) : '',
            'post_excerpt' => isset( $data['short_description'] ) ? wp_kses_post( $data['short_description'] ) : '',
            'post_status'  => $post_status,
            'post_type'    => 'product',
            'post_author'  => $vendor_id,
        );

        $product_id = wp_insert_post( $product_data );

        if ( is_wp_error( $product_id ) ) {
            return $product_id;
        }

        // Set vendor meta
        update_post_meta( $product_id, '_bazaar_vendor_id', $vendor_id );

        // Set product type
        $product_type = isset( $data['product_type'] ) ? $data['product_type'] : 'simple';
        wp_set_object_terms( $product_id, $product_type, 'product_type' );

        // Update product meta
        self::update_product_meta( $product_id, $data );

        do_action( 'bazaar_product_created', $product_id, $vendor_id, $data );

        return $product_id;
    }

    /**
     * Update product.
     *
     * @param int   $product_id Product ID.
     * @param array $data       Product data.
     * @return bool|WP_Error
     */
    public static function update_product( $product_id, $data ) {
        $vendor_id = self::get_product_vendor( $product_id );

        if ( ! $vendor_id ) {
            return new WP_Error( 'invalid_product', __( 'Invalid product.', 'bazaar' ) );
        }

        $current_user_id = get_current_user_id();

        // Check permissions
        if ( ! current_user_can( 'manage_options' ) && $vendor_id !== $current_user_id ) {
            return new WP_Error( 'permission_denied', __( 'You do not have permission to edit this product.', 'bazaar' ) );
        }

        // Update post
        $post_data = array( 'ID' => $product_id );

        if ( isset( $data['title'] ) ) {
            $post_data['post_title'] = sanitize_text_field( $data['title'] );
        }

        if ( isset( $data['description'] ) ) {
            $post_data['post_content'] = wp_kses_post( $data['description'] );
        }

        if ( isset( $data['short_description'] ) ) {
            $post_data['post_excerpt'] = wp_kses_post( $data['short_description'] );
        }

        wp_update_post( $post_data );

        // Update product meta
        self::update_product_meta( $product_id, $data );

        do_action( 'bazaar_product_updated', $product_id, $vendor_id, $data );

        return true;
    }

    /**
     * Update product meta.
     *
     * @param int   $product_id Product ID.
     * @param array $data       Product data.
     */
    private static function update_product_meta( $product_id, $data ) {
        $product = wc_get_product( $product_id );

        if ( ! $product ) {
            return;
        }

        // Price
        if ( isset( $data['regular_price'] ) ) {
            $product->set_regular_price( wc_format_decimal( $data['regular_price'] ) );
        }

        if ( isset( $data['sale_price'] ) ) {
            $product->set_sale_price( wc_format_decimal( $data['sale_price'] ) );
        }

        // Stock
        if ( isset( $data['manage_stock'] ) ) {
            $product->set_manage_stock( 'yes' === $data['manage_stock'] );
        }

        if ( isset( $data['stock_quantity'] ) ) {
            $product->set_stock_quantity( intval( $data['stock_quantity'] ) );
        }

        if ( isset( $data['stock_status'] ) ) {
            $product->set_stock_status( $data['stock_status'] );
        }

        // SKU
        if ( isset( $data['sku'] ) ) {
            $product->set_sku( sanitize_text_field( $data['sku'] ) );
        }

        // Weight and dimensions
        if ( isset( $data['weight'] ) ) {
            $product->set_weight( wc_format_decimal( $data['weight'] ) );
        }

        if ( isset( $data['length'] ) ) {
            $product->set_length( wc_format_decimal( $data['length'] ) );
        }

        if ( isset( $data['width'] ) ) {
            $product->set_width( wc_format_decimal( $data['width'] ) );
        }

        if ( isset( $data['height'] ) ) {
            $product->set_height( wc_format_decimal( $data['height'] ) );
        }

        // Virtual and downloadable
        if ( isset( $data['virtual'] ) ) {
            $product->set_virtual( 'yes' === $data['virtual'] );
        }

        if ( isset( $data['downloadable'] ) ) {
            $product->set_downloadable( 'yes' === $data['downloadable'] );
        }

        // Categories
        if ( isset( $data['categories'] ) && is_array( $data['categories'] ) ) {
            $product->set_category_ids( array_map( 'intval', $data['categories'] ) );
        }

        // Tags
        if ( isset( $data['tags'] ) && is_array( $data['tags'] ) ) {
            $product->set_tag_ids( array_map( 'intval', $data['tags'] ) );
        }

        // Images
        if ( isset( $data['featured_image'] ) ) {
            $product->set_image_id( intval( $data['featured_image'] ) );
        }

        if ( isset( $data['gallery'] ) && is_array( $data['gallery'] ) ) {
            $product->set_gallery_image_ids( array_map( 'intval', $data['gallery'] ) );
        }

        // Product-specific commission
        if ( isset( $data['commission_rate'] ) ) {
            update_post_meta( $product_id, '_bazaar_commission_rate', floatval( $data['commission_rate'] ) );
        }

        if ( isset( $data['commission_type'] ) ) {
            update_post_meta( $product_id, '_bazaar_commission_type', sanitize_text_field( $data['commission_type'] ) );
        }

        $product->save();
    }

    /**
     * Delete product.
     *
     * @param int  $product_id Product ID.
     * @param bool $force      Force delete (bypass trash).
     * @return bool|WP_Error
     */
    public static function delete_product( $product_id, $force = false ) {
        $vendor_id = self::get_product_vendor( $product_id );

        if ( ! $vendor_id ) {
            return new WP_Error( 'invalid_product', __( 'Invalid product.', 'bazaar' ) );
        }

        $current_user_id = get_current_user_id();

        // Check permissions
        if ( ! current_user_can( 'manage_options' ) && $vendor_id !== $current_user_id ) {
            return new WP_Error( 'permission_denied', __( 'You do not have permission to delete this product.', 'bazaar' ) );
        }

        if ( $force ) {
            wp_delete_post( $product_id, true );
        } else {
            wp_trash_post( $product_id );
        }

        do_action( 'bazaar_product_deleted', $product_id, $vendor_id );

        return true;
    }

    /**
     * Filter products by vendor in admin.
     *
     * @param WP_Query $query Query object.
     */
    public function filter_products_by_vendor( $query ) {
        if ( ! is_admin() || ! $query->is_main_query() ) {
            return;
        }

        if ( 'product' !== $query->get( 'post_type' ) ) {
            return;
        }

        // If admin, check for vendor filter
        if ( current_user_can( 'manage_options' ) ) {
            if ( isset( $_GET['bazaar_vendor'] ) && ! empty( $_GET['bazaar_vendor'] ) ) {
                $query->set( 'author', intval( $_GET['bazaar_vendor'] ) );
            }
            return;
        }

        // If vendor, show only their products
        if ( Bazaar_Roles::is_vendor( get_current_user_id() ) ) {
            $query->set( 'author', get_current_user_id() );
        }
    }

    /**
     * Display vendor info on product page.
     */
    public function display_vendor_info() {
        global $product;

        if ( ! $product ) {
            return;
        }

        $vendor_id = self::get_product_vendor( $product->get_id() );

        if ( ! $vendor_id ) {
            return;
        }

        $vendor = Bazaar_Vendor::get_vendor( $vendor_id );

        if ( ! $vendor ) {
            return;
        }

        ?>
        <div class="bazaar-product-vendor">
            <span class="vendor-label"><?php esc_html_e( 'Sold by:', 'bazaar' ); ?></span>
            <a href="<?php echo esc_url( $vendor['store_url'] ); ?>" class="vendor-name">
                <?php echo esc_html( $vendor['store_name'] ); ?>
            </a>
            <?php if ( $vendor['rating']['count'] > 0 ) : ?>
                <span class="vendor-rating">
                    <?php echo esc_html( $vendor['rating']['average'] ); ?>/5
                    (<?php echo esc_html( $vendor['rating']['count'] ); ?>)
                </span>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Save product vendor on save.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @param bool    $update  Whether this is an update.
     */
    public function save_product_vendor( $post_id, $post, $update ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( 'product' !== $post->post_type ) {
            return;
        }

        // Set vendor ID if author is vendor
        if ( Bazaar_Roles::is_vendor( $post->post_author ) ) {
            $existing_vendor = get_post_meta( $post_id, '_bazaar_vendor_id', true );

            if ( ! $existing_vendor ) {
                update_post_meta( $post_id, '_bazaar_vendor_id', $post->post_author );
            }
        }
    }

    /**
     * Add vendor tab to product data.
     *
     * @param array $tabs Product data tabs.
     * @return array
     */
    public function add_vendor_product_tab( $tabs ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return $tabs;
        }

        $tabs['bazaar_vendor'] = array(
            'label'    => __( 'Vendor', 'bazaar' ),
            'target'   => 'bazaar_vendor_data',
            'class'    => array(),
            'priority' => 100,
        );

        return $tabs;
    }

    /**
     * Vendor product data panel.
     */
    public function vendor_product_data_panel() {
        global $post;

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $vendor_id = get_post_meta( $post->ID, '_bazaar_vendor_id', true );
        $commission_type = get_post_meta( $post->ID, '_bazaar_commission_type', true );
        $commission_rate = get_post_meta( $post->ID, '_bazaar_commission_rate', true );

        // Get all vendors
        $vendors = Bazaar_Vendor::get_vendors( array( 'status' => 'approved', 'number' => -1 ) );

        ?>
        <div id="bazaar_vendor_data" class="panel woocommerce_options_panel">
            <div class="options_group">
                <p class="form-field">
                    <label for="_bazaar_vendor_id"><?php esc_html_e( 'Vendor', 'bazaar' ); ?></label>
                    <select id="_bazaar_vendor_id" name="_bazaar_vendor_id" class="wc-enhanced-select">
                        <option value=""><?php esc_html_e( 'No vendor', 'bazaar' ); ?></option>
                        <?php foreach ( $vendors['vendors'] as $vendor ) : ?>
                            <option value="<?php echo esc_attr( $vendor->ID ); ?>" <?php selected( $vendor_id, $vendor->ID ); ?>>
                                <?php echo esc_html( Bazaar_Vendor::get_store_name( $vendor->ID ) . ' (#' . $vendor->ID . ')' ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <p class="form-field">
                    <label for="_bazaar_commission_type"><?php esc_html_e( 'Commission Type', 'bazaar' ); ?></label>
                    <select id="_bazaar_commission_type" name="_bazaar_commission_type">
                        <option value=""><?php esc_html_e( 'Use global settings', 'bazaar' ); ?></option>
                        <option value="percentage" <?php selected( $commission_type, 'percentage' ); ?>><?php esc_html_e( 'Percentage', 'bazaar' ); ?></option>
                        <option value="fixed" <?php selected( $commission_type, 'fixed' ); ?>><?php esc_html_e( 'Fixed', 'bazaar' ); ?></option>
                    </select>
                </p>

                <p class="form-field">
                    <label for="_bazaar_commission_rate"><?php esc_html_e( 'Commission Rate', 'bazaar' ); ?></label>
                    <input type="number" id="_bazaar_commission_rate" name="_bazaar_commission_rate" value="<?php echo esc_attr( $commission_rate ); ?>" step="0.01" min="0" />
                    <span class="description"><?php esc_html_e( 'Leave empty to use global or vendor settings.', 'bazaar' ); ?></span>
                </p>
            </div>
        </div>
        <?php
    }

    /**
     * Save vendor product data.
     *
     * @param int $post_id Post ID.
     */
    public function save_vendor_product_data( $post_id ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( isset( $_POST['_bazaar_vendor_id'] ) ) {
            update_post_meta( $post_id, '_bazaar_vendor_id', intval( $_POST['_bazaar_vendor_id'] ) );
        }

        if ( isset( $_POST['_bazaar_commission_type'] ) ) {
            update_post_meta( $post_id, '_bazaar_commission_type', sanitize_text_field( wp_unslash( $_POST['_bazaar_commission_type'] ) ) );
        }

        if ( isset( $_POST['_bazaar_commission_rate'] ) ) {
            update_post_meta( $post_id, '_bazaar_commission_rate', floatval( $_POST['_bazaar_commission_rate'] ) );
        }
    }

    /**
     * Get allowed product types for vendors.
     *
     * @return array
     */
    public static function get_allowed_product_types() {
        $types = get_option( 'bazaar_vendor_product_types', array( 'simple', 'variable', 'external', 'grouped' ) );

        return apply_filters( 'bazaar_allowed_product_types', $types );
    }
}

// Initialize
new Bazaar_Product();
