<?php
/**
 * Dashboard Products Tab.
 *
 * @package Bazaar\Templates
 */

defined( 'ABSPATH' ) || exit;

$statuses = bazaar_get_product_statuses();
?>
<div class="bazaar-tab-content bazaar-products">
    <div class="tab-header">
        <h2><?php esc_html_e( 'Products', 'bazaar' ); ?></h2>
        <a href="<?php echo esc_url( add_query_arg( 'action', 'new', bazaar_get_dashboard_tab_url( 'products' ) ) ); ?>" class="button button-primary">
            <span class="dashicons dashicons-plus"></span>
            <?php esc_html_e( 'Add New Product', 'bazaar' ); ?>
        </a>
    </div>

    <div class="bazaar-filters">
        <ul class="status-filter">
            <li>
                <a href="<?php echo esc_url( bazaar_get_dashboard_tab_url( 'products' ) ); ?>"
                   class="<?php echo 'all' === $current_status ? 'active' : ''; ?>">
                    <?php esc_html_e( 'All', 'bazaar' ); ?>
                </a>
            </li>
            <?php foreach ( $statuses as $status_key => $status_label ) : ?>
                <li>
                    <a href="<?php echo esc_url( add_query_arg( 'status', $status_key, bazaar_get_dashboard_tab_url( 'products' ) ) ); ?>"
                       class="<?php echo $current_status === $status_key ? 'active' : ''; ?>">
                        <?php echo esc_html( $status_label ); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <?php if ( empty( $products['products'] ) ) : ?>
        <div class="bazaar-empty-state">
            <span class="dashicons dashicons-products"></span>
            <h3><?php esc_html_e( 'No products found', 'bazaar' ); ?></h3>
            <p><?php esc_html_e( 'Start selling by adding your first product.', 'bazaar' ); ?></p>
            <a href="<?php echo esc_url( add_query_arg( 'action', 'new', bazaar_get_dashboard_tab_url( 'products' ) ) ); ?>" class="button button-primary">
                <?php esc_html_e( 'Add Product', 'bazaar' ); ?>
            </a>
        </div>
    <?php else : ?>
        <table class="bazaar-table bazaar-products-table">
            <thead>
                <tr>
                    <th class="column-image"><?php esc_html_e( 'Image', 'bazaar' ); ?></th>
                    <th class="column-name"><?php esc_html_e( 'Name', 'bazaar' ); ?></th>
                    <th class="column-price"><?php esc_html_e( 'Price', 'bazaar' ); ?></th>
                    <th class="column-stock"><?php esc_html_e( 'Stock', 'bazaar' ); ?></th>
                    <th class="column-status"><?php esc_html_e( 'Status', 'bazaar' ); ?></th>
                    <th class="column-date"><?php esc_html_e( 'Date', 'bazaar' ); ?></th>
                    <th class="column-actions"><?php esc_html_e( 'Actions', 'bazaar' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $products['products'] as $product_post ) : ?>
                    <?php $product = wc_get_product( $product_post->ID ); ?>
                    <?php if ( ! $product ) continue; ?>
                    <tr>
                        <td class="column-image">
                            <?php echo $product->get_image( array( 50, 50 ) ); ?>
                        </td>
                        <td class="column-name">
                            <strong><?php echo esc_html( $product->get_name() ); ?></strong>
                            <br>
                            <small>SKU: <?php echo esc_html( $product->get_sku() ? $product->get_sku() : '-' ); ?></small>
                        </td>
                        <td class="column-price">
                            <?php echo $product->get_price_html(); ?>
                        </td>
                        <td class="column-stock">
                            <?php
                            if ( $product->is_in_stock() ) {
                                $stock = $product->get_stock_quantity();
                                if ( $stock ) {
                                    echo esc_html( $stock );
                                } else {
                                    esc_html_e( 'In Stock', 'bazaar' );
                                }
                            } else {
                                echo '<span class="out-of-stock">' . esc_html__( 'Out of Stock', 'bazaar' ) . '</span>';
                            }
                            ?>
                        </td>
                        <td class="column-status">
                            <span class="product-status status-<?php echo esc_attr( $product_post->post_status ); ?>">
                                <?php echo esc_html( $statuses[ $product_post->post_status ] ?? $product_post->post_status ); ?>
                            </span>
                        </td>
                        <td class="column-date">
                            <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $product_post->post_date ) ) ); ?>
                        </td>
                        <td class="column-actions">
                            <a href="<?php echo esc_url( add_query_arg( array( 'action' => 'edit', 'product_id' => $product->get_id() ), bazaar_get_dashboard_tab_url( 'products' ) ) ); ?>" class="button button-small">
                                <?php esc_html_e( 'Edit', 'bazaar' ); ?>
                            </a>
                            <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="button button-small" target="_blank">
                                <?php esc_html_e( 'View', 'bazaar' ); ?>
                            </a>
                            <button type="button" class="button button-small delete-product" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
                                <?php esc_html_e( 'Delete', 'bazaar' ); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ( $products['pages'] > 1 ) : ?>
            <?php bazaar_pagination( $products['pages'], isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1 ); ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
