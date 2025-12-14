<?php
/**
 * Checkout Template (Non-WooCommerce).
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// This template is used when WooCommerce is not active.
// For WooCommerce checkout, the shortcode redirects to [woocommerce_checkout].
?>

<div class="edugo-checkout">
    <div class="edugo-notice edugo-notice-info">
        <p>
            <?php
            esc_html_e( 'Checkout functionality requires WooCommerce. Please install and activate WooCommerce to enable course purchases.', 'edugo-lms' );
            ?>
        </p>
        <?php if ( current_user_can( 'install_plugins' ) ) : ?>
            <a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) ); ?>" class="edugo-button edugo-button-primary">
                <?php esc_html_e( 'Install WooCommerce', 'edugo-lms' ); ?>
            </a>
        <?php endif; ?>
    </div>

    <div class="edugo-free-courses-note">
        <h3><?php esc_html_e( 'Free Courses Available', 'edugo-lms' ); ?></h3>
        <p><?php esc_html_e( 'You can still enroll in free courses without WooCommerce.', 'edugo-lms' ); ?></p>
        <a href="<?php echo esc_url( get_post_type_archive_link( 'edugo_course' ) ); ?>" class="edugo-button edugo-button-secondary">
            <?php esc_html_e( 'Browse Free Courses', 'edugo-lms' ); ?>
        </a>
    </div>
</div>
