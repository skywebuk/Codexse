<?php
/**
 * My Account navigation
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );
?>

<nav class="woocommerce-MyAccount-navigation">
	<ul>
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>">
					<span class="icon">
						<?php 
							if ( $label === "Dashboard" ) {
								echo '<i class="ri-dashboard-line"></i>';
							} elseif ( $label === "Orders" ) {
								echo '<i class="ri-file-list-2-line"></i>';
							} elseif ( $label === "Downloads" ) {
								echo '<i class="ri-download-cloud-2-line"></i>';
							} elseif ( $label === "Addresses" ) {
								echo '<i class="ri-map-pin-line"></i>';
							} elseif ( $label === "Account details" ) {
								echo '<i class="ri-user-3-line"></i>';
							} elseif ( $label === "Log out" ) {
								echo '<i class="ri-logout-box-r-line"></i>';
							} else {
								echo '<i class="ri-more-2-line"></i>';
							}
						?>
					</span>
					<span class="text"><?php echo esc_html( $label ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
