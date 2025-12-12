<?php
/**
 * Checkout login form (Tutor LMS integrated)
 *
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

if ( is_user_logged_in() ) {
	return;
}
?>

<div class="woocommerce-form-login tutor-login-form mb-4">
	<?php echo do_shortcode('[tutor_login redirect_to="' . esc_url( wc_get_checkout_url() ) . '"]'); ?>
</div>
