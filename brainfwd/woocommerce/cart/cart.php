<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.1.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); 

$cart_items = WC()->cart->get_cart();
?>

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
    <?php do_action( 'woocommerce_before_cart_table' ); ?>

    <div class="woocommerce-cart-form__contents">
        <?php do_action( 'woocommerce_before_cart_contents' ); ?>
        <div class="row g-4">
            <div class="col-lg-8">

            <?php foreach ( $cart_items as $cart_item_key => $cart_item ) : 
                $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) :

                    $product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
                    $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
            ?>
                <div class="mb-4 woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                    <div class="cart__product">

                        <figure class="product__image">
                            <div class="product-remove">
                                <?php
                                    echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
                                        '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><i class="ri-close-large-line"></i></a>',
                                        esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                        esc_attr( sprintf( __( 'Remove %s from cart', 'brainfwd' ), wp_strip_all_tags( $product_name ) ) ),
                                        esc_attr( $product_id ),
                                        esc_attr( $_product->get_sku() )
                                    ), $cart_item_key );
                                ?>
                            </div>
                            <?php
                                $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
                                if ( ! $product_permalink ) {
                                    echo wp_kses_post( $thumbnail );
                                } else {
                                    printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
                                }
                            ?>
                        </figure>

                        <div class="product__details">
                            <div class="product__price">
                                <span><?php esc_html_e( 'Price', 'brainfwd' ); ?>:</span>
                                <strong><?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?></strong>
                            </div>
                            <?php
                                if ( ! $product_permalink ) {
                                    echo '<h5 class="product__title">' . wp_kses_post( $product_name ) . '</h5>';
                                } else {
                                    echo wp_kses_post( sprintf( '<h5 class="product__title"><a href="%s">%s</a></h5>', esc_url( $product_permalink ), $product_name ) );
                                }

                                do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

                                echo wc_get_formatted_cart_item_data( $cart_item );

                                if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
                                    echo wp_kses_post( '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'brainfwd' ) . '</p>' );
                                }
                            ?>
                            <?php
                                $min_quantity = $_product->is_sold_individually() ? 1 : 0;
                                $max_quantity = $_product->is_sold_individually() ? 1 : $_product->get_max_purchase_quantity();

                                echo woocommerce_quantity_input(
                                    array(
                                        'input_name'   => "cart[{$cart_item_key}][qty]",
                                        'input_value'  => $cart_item['quantity'],
                                        'max_value'    => $max_quantity,
                                        'min_value'    => $min_quantity,
                                        'product_name' => $product_name,
                                    ),
                                    $_product,
                                    false
                                );
                            ?>

                            <div class="product__subtitle">
                                <span class="m-0"><?php esc_html_e( 'Subtotal', 'brainfwd' ); ?>:</span>
                                <strong><?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; endforeach; ?>
            </div>
            <div class="col-lg-4">

    <?php do_action( 'woocommerce_cart_contents' ); ?>

    <div class="mb-4">
        <?php if ( wc_coupons_enabled() ) : ?>
            <div class="coupon">
                <label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'brainfwd' ); ?></label>
                <div class="coupon_field">
                    <label for="coupon_code" class="coupon_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0" viewBox="0 0 30 30" style="enable-background:new 0 0 512 512" xml:space="preserve"><g><path d="M21.321 15.719c-3.01.072-3.015 4.484-.006 4.564 3.01-.073 3.014-4.483.006-4.564zm-.785 2.285c.001-1.024 1.558-1.028 1.564-.004-.004 1.021-1.556 1.025-1.564.004zM15.32 14.281c3.009-.076 3.009-4.488 0-4.564-3.01.077-3.008 4.486 0 4.564zm0-3.063c1.023.004 1.023 1.56 0 1.564-1.023-.005-1.022-1.558 0-1.564zM23.19 10.138a.753.753 0 0 0-1.064-.002l-8.69 8.649a.75.75 0 1 0 1.058 1.063l8.691-8.648a.75.75 0 0 0 .005-1.062zM8.852 10.537a.75.75 0 0 0-.75.75v1.923a.75.75 0 0 0 1.5 0v-1.923a.75.75 0 0 0-.75-.75zM8.852 16.041a.75.75 0 0 0-.75.75v1.922a.75.75 0 0 0 1.5 0v-1.922a.75.75 0 0 0-.75-.75z" fill="currentColor" opacity="1" data-original="currentColor"></path><path d="M28.379 12.522a2.6 2.6 0 0 0 1.371-2.3V7.65a2.62 2.62 0 0 0-2.617-2.617H2.867A2.62 2.62 0 0 0 .25 7.65v2.57a2.6 2.6 0 0 0 1.371 2.3 2.812 2.812 0 0 1 0 4.958A2.6 2.6 0 0 0 .25 19.78v2.57a2.62 2.62 0 0 0 2.617 2.617h24.266a2.62 2.62 0 0 0 2.617-2.617v-2.57a2.6 2.6 0 0 0-1.371-2.3c-1.964-.983-1.949-3.975 0-4.958zm-2.47 4.512a4.307 4.307 0 0 0 1.764 1.764c.359.194.581.57.577.978v2.57a1.118 1.118 0 0 1-1.117 1.117H9.6c-.041-.65.258-1.894-.75-1.919-1.009.024-.709 1.274-.75 1.923H2.867A1.118 1.118 0 0 1 1.75 22.35v-2.57a1.099 1.099 0 0 1 .577-.978 4.311 4.311 0 0 0 0-7.6 1.099 1.099 0 0 1-.577-.978V7.65c.001-.617.5-1.116 1.117-1.117H8.1c.041.651-.26 1.898.75 1.923 1.009-.024.709-1.274.75-1.923h17.533c.617.001 1.116.5 1.117 1.117v2.57c.004.408-.218.784-.577.978a4.31 4.31 0 0 0-1.764 5.836z" fill="currentColor" opacity="1" data-original="currentColor"></path></g></svg>
                    </label>
                    <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'brainfwd' ); ?>" />
                    <button type="submit" class="<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'brainfwd' ); ?>"><?php esc_html_e( 'Apply coupon', 'brainfwd' ); ?></button>    
                </div>
                <?php do_action( 'woocommerce_cart_coupon' ); ?>
            </div>
        <?php endif; ?>
        <button type="submit" class="d-none update-cart-button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'brainfwd' ); ?>"><?php esc_html_e( 'Update cart', 'brainfwd' ); ?></button>
    </div>

    <?php do_action( 'woocommerce_cart_actions' ); ?>
    <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
    <?php do_action( 'woocommerce_after_cart_contents' ); ?>
    <?php do_action( 'woocommerce_after_cart_table' ); ?>
    <?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

    <div class="cart-collaterals">
        <?php do_action( 'woocommerce_cart_collaterals' ); ?>
    </div>

            </div>
        </div>
    </div>

    <?php do_action( 'woocommerce_after_cart' ); ?>
</form>
