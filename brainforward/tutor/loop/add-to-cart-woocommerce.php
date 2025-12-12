<?php
/**
 * A single course loop Buy Now button (WooCommerce)
 *
 * @package Tutor\Templates
 * @subpackage WooCommerceIntegration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$course_id  = get_the_ID();
$product_id = tutor_utils()->get_course_product_id();
$product    = wc_get_product( $product_id );

if ( ! $product_id || ! $product ) {
	return;
}

// Build Buy Now URL
$buy_now_url = esc_url( add_query_arg(
	array(
		'add-to-cart' => $product->get_id(),
		'quantity'    => 1,
		'buy-now'     => 'yes',
	),
	wc_get_checkout_url()
) );

// Output Buy Now Button
echo apply_filters(
	'tutor_course_restrict_new_entry',
	sprintf(
		'<a href="%s" class="tutor-btn tutor-btn-primary tutor-btn-md tutor-btn-block tutor-buy-now-btn">
			<span class="tutor-icon-cart-line tutor-mr-8"></span>
			<span class="cart-text">%s</span>
		</a>',
		$buy_now_url,
		esc_html__( 'Buy Now', 'tutor' )
	),
	$course_id
);
