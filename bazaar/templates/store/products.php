<?php
/**
 * Vendor Store Products Template.
 *
 * @package Bazaar\Templates
 */

defined( 'ABSPATH' ) || exit;

$current_category = isset( $_GET['category'] ) ? intval( $_GET['category'] ) : 0;
$current_orderby  = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'date';
$search_query     = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
?>

<div class="bazaar-store-products">
    <!-- Store Toolbar -->
    <div class="store-toolbar">
        <div class="toolbar-left">
            <form class="store-search" method="get" action="<?php echo esc_url( bazaar_get_vendor_store_url( $vendor->ID ) ); ?>">
                <?php if ( $current_category ) : ?>
                    <input type="hidden" name="category" value="<?php echo esc_attr( $current_category ); ?>" />
                <?php endif; ?>
                <input type="text" name="s" value="<?php echo esc_attr( $search_query ); ?>" placeholder="<?php esc_attr_e( 'Search products...', 'bazaar' ); ?>" />
                <button type="submit">
                    <span class="dashicons dashicons-search"></span>
                </button>
            </form>
        </div>

        <div class="toolbar-right">
            <div class="products-count">
                <?php
                printf(
                    esc_html( _n( 'Showing %1$d of %2$d product', 'Showing %1$d of %2$d products', $products['total'], 'bazaar' ) ),
                    esc_html( count( $products['products'] ) ),
                    esc_html( $products['total'] )
                );
                ?>
            </div>

            <form class="orderby-form" method="get">
                <?php if ( $current_category ) : ?>
                    <input type="hidden" name="category" value="<?php echo esc_attr( $current_category ); ?>" />
                <?php endif; ?>
                <?php if ( $search_query ) : ?>
                    <input type="hidden" name="s" value="<?php echo esc_attr( $search_query ); ?>" />
                <?php endif; ?>
                <select name="orderby" onchange="this.form.submit()">
                    <option value="date" <?php selected( $current_orderby, 'date' ); ?>><?php esc_html_e( 'Latest', 'bazaar' ); ?></option>
                    <option value="popularity" <?php selected( $current_orderby, 'popularity' ); ?>><?php esc_html_e( 'Popularity', 'bazaar' ); ?></option>
                    <option value="rating" <?php selected( $current_orderby, 'rating' ); ?>><?php esc_html_e( 'Rating', 'bazaar' ); ?></option>
                    <option value="price" <?php selected( $current_orderby, 'price' ); ?>><?php esc_html_e( 'Price: Low to High', 'bazaar' ); ?></option>
                    <option value="price-desc" <?php selected( $current_orderby, 'price-desc' ); ?>><?php esc_html_e( 'Price: High to Low', 'bazaar' ); ?></option>
                </select>
            </form>

            <div class="view-toggle">
                <button type="button" class="grid-view active" data-view="grid">
                    <span class="dashicons dashicons-grid-view"></span>
                </button>
                <button type="button" class="list-view" data-view="list">
                    <span class="dashicons dashicons-list-view"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <?php if ( empty( $products['products'] ) ) : ?>
        <div class="bazaar-empty-state">
            <span class="dashicons dashicons-archive"></span>
            <h3><?php esc_html_e( 'No products found', 'bazaar' ); ?></h3>
            <?php if ( $search_query ) : ?>
                <p><?php printf( esc_html__( 'No products matching "%s" were found.', 'bazaar' ), esc_html( $search_query ) ); ?></p>
                <a href="<?php echo esc_url( bazaar_get_vendor_store_url( $vendor->ID ) ); ?>" class="button"><?php esc_html_e( 'View All Products', 'bazaar' ); ?></a>
            <?php else : ?>
                <p><?php esc_html_e( 'This vendor has not added any products yet.', 'bazaar' ); ?></p>
            <?php endif; ?>
        </div>
    <?php else : ?>
        <ul class="products columns-4 bazaar-products-grid">
            <?php
            foreach ( $products['products'] as $product_id ) :
                $product = wc_get_product( $product_id );
                if ( ! $product ) {
                    continue;
                }

                // Set up post data for WooCommerce template functions
                $post_object = get_post( $product_id );
                setup_postdata( $GLOBALS['post'] =& $post_object );
                ?>
                <li <?php wc_product_class( '', $product ); ?>>
                    <?php
                    /**
                     * Hook: woocommerce_before_shop_loop_item.
                     */
                    do_action( 'woocommerce_before_shop_loop_item' );

                    /**
                     * Hook: woocommerce_before_shop_loop_item_title.
                     *
                     * @hooked woocommerce_show_product_loop_sale_flash - 10
                     * @hooked woocommerce_template_loop_product_thumbnail - 10
                     */
                    do_action( 'woocommerce_before_shop_loop_item_title' );

                    /**
                     * Hook: woocommerce_shop_loop_item_title.
                     *
                     * @hooked woocommerce_template_loop_product_title - 10
                     */
                    do_action( 'woocommerce_shop_loop_item_title' );

                    /**
                     * Hook: woocommerce_after_shop_loop_item_title.
                     *
                     * @hooked woocommerce_template_loop_rating - 5
                     * @hooked woocommerce_template_loop_price - 10
                     */
                    do_action( 'woocommerce_after_shop_loop_item_title' );

                    /**
                     * Hook: woocommerce_after_shop_loop_item.
                     *
                     * @hooked woocommerce_template_loop_product_link_close - 5
                     * @hooked woocommerce_template_loop_add_to_cart - 10
                     */
                    do_action( 'woocommerce_after_shop_loop_item' );
                    ?>
                </li>
            <?php
            endforeach;
            wp_reset_postdata();
            ?>
        </ul>

        <?php if ( $products['pages'] > 1 ) : ?>
            <?php bazaar_pagination( $products['pages'], isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1 ); ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
