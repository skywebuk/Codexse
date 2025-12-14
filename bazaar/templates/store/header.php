<?php
/**
 * Vendor Store Header Template.
 *
 * @package Bazaar\Templates
 */

defined( 'ABSPATH' ) || exit;

$store_info   = bazaar_get_vendor_store_info( $vendor->ID );
$banner_url   = ! empty( $store_info['banner'] ) ? $store_info['banner'] : BAZAAR_PLUGIN_URL . 'assets/images/default-banner.jpg';
$logo_url     = ! empty( $store_info['logo'] ) ? $store_info['logo'] : get_avatar_url( $vendor->ID, array( 'size' => 150 ) );
$is_open      = bazaar_is_vendor_store_open( $vendor->ID );
$rating       = bazaar_get_vendor_rating( $vendor->ID );
$product_count = bazaar_get_vendor_product_count( $vendor->ID );
$member_since = date_i18n( get_option( 'date_format' ), strtotime( $vendor->user_registered ) );
?>

<div class="bazaar-store-header" style="background-image: url('<?php echo esc_url( $banner_url ); ?>');">
    <div class="store-header-overlay"></div>

    <div class="store-header-content">
        <div class="store-logo">
            <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $store_info['name'] ); ?>" />
            <?php if ( $is_open ) : ?>
                <span class="store-status open"><?php esc_html_e( 'Open', 'bazaar' ); ?></span>
            <?php else : ?>
                <span class="store-status closed"><?php esc_html_e( 'Closed', 'bazaar' ); ?></span>
            <?php endif; ?>
        </div>

        <div class="store-info">
            <h1 class="store-name"><?php echo esc_html( $store_info['name'] ); ?></h1>

            <?php if ( ! empty( $store_info['tagline'] ) ) : ?>
                <p class="store-tagline"><?php echo esc_html( $store_info['tagline'] ); ?></p>
            <?php endif; ?>

            <div class="store-meta">
                <?php if ( $rating['count'] > 0 ) : ?>
                    <div class="store-rating">
                        <?php bazaar_star_rating( $rating['average'] ); ?>
                        <span class="rating-text">
                            <?php echo esc_html( number_format( $rating['average'], 1 ) ); ?>
                            (<?php printf( esc_html( _n( '%s review', '%s reviews', $rating['count'], 'bazaar' ) ), esc_html( $rating['count'] ) ); ?>)
                        </span>
                    </div>
                <?php endif; ?>

                <div class="store-products-count">
                    <span class="dashicons dashicons-archive"></span>
                    <?php printf( esc_html( _n( '%s product', '%s products', $product_count, 'bazaar' ) ), esc_html( $product_count ) ); ?>
                </div>

                <div class="store-member-since">
                    <span class="dashicons dashicons-calendar-alt"></span>
                    <?php printf( esc_html__( 'Member since %s', 'bazaar' ), esc_html( $member_since ) ); ?>
                </div>
            </div>

            <div class="store-actions">
                <?php if ( is_user_logged_in() && get_current_user_id() !== $vendor->ID ) : ?>
                    <button type="button" class="button bazaar-follow-btn <?php echo bazaar_is_following_vendor( $vendor->ID ) ? 'following' : ''; ?>" data-vendor-id="<?php echo esc_attr( $vendor->ID ); ?>">
                        <span class="follow-text">
                            <?php echo bazaar_is_following_vendor( $vendor->ID ) ? esc_html__( 'Following', 'bazaar' ) : esc_html__( 'Follow', 'bazaar' ); ?>
                        </span>
                    </button>
                    <button type="button" class="button bazaar-contact-btn" data-vendor-id="<?php echo esc_attr( $vendor->ID ); ?>">
                        <span class="dashicons dashicons-email"></span>
                        <?php esc_html_e( 'Contact', 'bazaar' ); ?>
                    </button>
                <?php endif; ?>

                <div class="store-share">
                    <button type="button" class="button share-btn">
                        <span class="dashicons dashicons-share"></span>
                        <?php esc_html_e( 'Share', 'bazaar' ); ?>
                    </button>
                    <div class="share-dropdown" style="display: none;">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_url( bazaar_get_vendor_store_url( $vendor->ID ) ); ?>" target="_blank" rel="noopener">
                            <span class="dashicons dashicons-facebook"></span> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo esc_url( bazaar_get_vendor_store_url( $vendor->ID ) ); ?>&text=<?php echo esc_attr( $store_info['name'] ); ?>" target="_blank" rel="noopener">
                            <span class="dashicons dashicons-twitter"></span> Twitter
                        </a>
                        <button type="button" class="copy-link" data-url="<?php echo esc_url( bazaar_get_vendor_store_url( $vendor->ID ) ); ?>">
                            <span class="dashicons dashicons-admin-links"></span> <?php esc_html_e( 'Copy Link', 'bazaar' ); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
