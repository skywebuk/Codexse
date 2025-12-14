<?php
/**
 * Vendor Store Sidebar Template.
 *
 * @package Bazaar\Templates
 */

defined( 'ABSPATH' ) || exit;

$store_info = bazaar_get_vendor_store_info( $vendor->ID );
$categories = bazaar_get_vendor_product_categories( $vendor->ID );
$social_links = bazaar_get_vendor_social_links( $vendor->ID );
?>

<aside class="bazaar-store-sidebar">
    <!-- About Section -->
    <?php if ( ! empty( $store_info['description'] ) ) : ?>
        <div class="sidebar-widget about-widget">
            <h3 class="widget-title"><?php esc_html_e( 'About', 'bazaar' ); ?></h3>
            <div class="widget-content">
                <?php echo wp_kses_post( wpautop( $store_info['description'] ) ); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Contact Info -->
    <div class="sidebar-widget contact-widget">
        <h3 class="widget-title"><?php esc_html_e( 'Contact Info', 'bazaar' ); ?></h3>
        <ul class="contact-list">
            <?php if ( ! empty( $store_info['address'] ) ) : ?>
                <li>
                    <span class="dashicons dashicons-location"></span>
                    <span><?php echo esc_html( $store_info['address'] ); ?></span>
                </li>
            <?php endif; ?>

            <?php if ( ! empty( $store_info['phone'] ) && bazaar_get_option( 'show_vendor_phone', 'yes' ) === 'yes' ) : ?>
                <li>
                    <span class="dashicons dashicons-phone"></span>
                    <a href="tel:<?php echo esc_attr( $store_info['phone'] ); ?>"><?php echo esc_html( $store_info['phone'] ); ?></a>
                </li>
            <?php endif; ?>

            <?php if ( ! empty( $store_info['email'] ) && bazaar_get_option( 'show_vendor_email', 'yes' ) === 'yes' ) : ?>
                <li>
                    <span class="dashicons dashicons-email"></span>
                    <a href="mailto:<?php echo esc_attr( $store_info['email'] ); ?>"><?php echo esc_html( $store_info['email'] ); ?></a>
                </li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Social Links -->
    <?php if ( ! empty( array_filter( $social_links ) ) ) : ?>
        <div class="sidebar-widget social-widget">
            <h3 class="widget-title"><?php esc_html_e( 'Follow Us', 'bazaar' ); ?></h3>
            <div class="social-links">
                <?php if ( ! empty( $social_links['facebook'] ) ) : ?>
                    <a href="<?php echo esc_url( $social_links['facebook'] ); ?>" target="_blank" rel="noopener" class="social-link facebook">
                        <span class="dashicons dashicons-facebook"></span>
                    </a>
                <?php endif; ?>

                <?php if ( ! empty( $social_links['twitter'] ) ) : ?>
                    <a href="<?php echo esc_url( $social_links['twitter'] ); ?>" target="_blank" rel="noopener" class="social-link twitter">
                        <span class="dashicons dashicons-twitter"></span>
                    </a>
                <?php endif; ?>

                <?php if ( ! empty( $social_links['instagram'] ) ) : ?>
                    <a href="<?php echo esc_url( $social_links['instagram'] ); ?>" target="_blank" rel="noopener" class="social-link instagram">
                        <span class="dashicons dashicons-instagram"></span>
                    </a>
                <?php endif; ?>

                <?php if ( ! empty( $social_links['linkedin'] ) ) : ?>
                    <a href="<?php echo esc_url( $social_links['linkedin'] ); ?>" target="_blank" rel="noopener" class="social-link linkedin">
                        <span class="dashicons dashicons-linkedin"></span>
                    </a>
                <?php endif; ?>

                <?php if ( ! empty( $social_links['youtube'] ) ) : ?>
                    <a href="<?php echo esc_url( $social_links['youtube'] ); ?>" target="_blank" rel="noopener" class="social-link youtube">
                        <span class="dashicons dashicons-youtube"></span>
                    </a>
                <?php endif; ?>

                <?php if ( ! empty( $social_links['pinterest'] ) ) : ?>
                    <a href="<?php echo esc_url( $social_links['pinterest'] ); ?>" target="_blank" rel="noopener" class="social-link pinterest">
                        <span class="dashicons dashicons-pinterest"></span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Product Categories -->
    <?php if ( ! empty( $categories ) ) : ?>
        <div class="sidebar-widget categories-widget">
            <h3 class="widget-title"><?php esc_html_e( 'Categories', 'bazaar' ); ?></h3>
            <ul class="category-list">
                <?php foreach ( $categories as $category ) : ?>
                    <li>
                        <a href="<?php echo esc_url( add_query_arg( 'category', $category->term_id, bazaar_get_vendor_store_url( $vendor->ID ) ) ); ?>">
                            <?php echo esc_html( $category->name ); ?>
                            <span class="count">(<?php echo esc_html( $category->count ); ?>)</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Store Policies -->
    <?php
    $policies = bazaar_get_vendor_policies( $vendor->ID );
    if ( ! empty( $policies['shipping'] ) || ! empty( $policies['return'] ) || ! empty( $policies['cancellation'] ) ) :
    ?>
        <div class="sidebar-widget policies-widget">
            <h3 class="widget-title"><?php esc_html_e( 'Store Policies', 'bazaar' ); ?></h3>
            <div class="policies-accordion">
                <?php if ( ! empty( $policies['shipping'] ) ) : ?>
                    <div class="policy-item">
                        <button type="button" class="policy-toggle">
                            <span class="dashicons dashicons-car"></span>
                            <?php esc_html_e( 'Shipping Policy', 'bazaar' ); ?>
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                        </button>
                        <div class="policy-content" style="display: none;">
                            <?php echo wp_kses_post( wpautop( $policies['shipping'] ) ); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $policies['return'] ) ) : ?>
                    <div class="policy-item">
                        <button type="button" class="policy-toggle">
                            <span class="dashicons dashicons-undo"></span>
                            <?php esc_html_e( 'Return Policy', 'bazaar' ); ?>
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                        </button>
                        <div class="policy-content" style="display: none;">
                            <?php echo wp_kses_post( wpautop( $policies['return'] ) ); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $policies['cancellation'] ) ) : ?>
                    <div class="policy-item">
                        <button type="button" class="policy-toggle">
                            <span class="dashicons dashicons-no"></span>
                            <?php esc_html_e( 'Cancellation Policy', 'bazaar' ); ?>
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                        </button>
                        <div class="policy-content" style="display: none;">
                            <?php echo wp_kses_post( wpautop( $policies['cancellation'] ) ); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php
    /**
     * Hook: bazaar_store_sidebar_after.
     */
    do_action( 'bazaar_store_sidebar_after', $vendor );
    ?>
</aside>
