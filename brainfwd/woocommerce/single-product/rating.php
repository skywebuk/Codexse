<?php
/**
 * Single Product Rating
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/rating.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;

if ( ! wc_review_ratings_enabled() ) {
	return;
}

$average_rating = $product->get_average_rating();
$rating_count = $product->get_rating_count();

?>
<div class="product__rating">
	<div class="star__rating">
		<span class="star front" style="width: <?php echo esc_attr( $average_rating / 5 ) * 100; ?>% !important;"></span>
		<span class="star back"></span>
	</div>
	<span class="rating_count">(<?php echo esc_html( $rating_count ); ?>)</span>
</div>

