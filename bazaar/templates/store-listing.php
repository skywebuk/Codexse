<?php
/**
 * Store Listing Template.
 *
 * @package Bazaar\Templates
 */

defined( 'ABSPATH' ) || exit;

$current_category = isset( $_GET['category'] ) ? intval( $_GET['category'] ) : 0;
$search_query     = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
$current_orderby  = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'newest';
?>

<div class="bazaar-store-listing">
    <div class="listing-header">
        <h1><?php esc_html_e( 'Our Vendors', 'bazaar' ); ?></h1>
        <p><?php esc_html_e( 'Discover amazing vendors and their unique products.', 'bazaar' ); ?></p>
    </div>

    <!-- Filters -->
    <div class="listing-filters">
        <form class="store-search-form" method="get">
            <input type="text" name="s" value="<?php echo esc_attr( $search_query ); ?>" placeholder="<?php esc_attr_e( 'Search vendors...', 'bazaar' ); ?>" />
            <button type="submit">
                <span class="dashicons dashicons-search"></span>
            </button>
        </form>

        <div class="filter-options">
            <select name="category" class="category-filter" onchange="this.form.submit()">
                <option value=""><?php esc_html_e( 'All Categories', 'bazaar' ); ?></option>
                <?php
                $categories = get_terms( array(
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => true,
                ) );
                foreach ( $categories as $category ) {
                    printf(
                        '<option value="%d" %s>%s</option>',
                        esc_attr( $category->term_id ),
                        selected( $current_category, $category->term_id, false ),
                        esc_html( $category->name )
                    );
                }
                ?>
            </select>

            <select name="orderby" class="orderby-filter" onchange="window.location.href = '<?php echo esc_js( add_query_arg( 'orderby', '', bazaar_get_store_listing_url() ) ); ?>' + this.value;">
                <option value="newest" <?php selected( $current_orderby, 'newest' ); ?>><?php esc_html_e( 'Newest', 'bazaar' ); ?></option>
                <option value="rating" <?php selected( $current_orderby, 'rating' ); ?>><?php esc_html_e( 'Top Rated', 'bazaar' ); ?></option>
                <option value="products" <?php selected( $current_orderby, 'products' ); ?>><?php esc_html_e( 'Most Products', 'bazaar' ); ?></option>
                <option value="name" <?php selected( $current_orderby, 'name' ); ?>><?php esc_html_e( 'Alphabetical', 'bazaar' ); ?></option>
            </select>
        </div>
    </div>

    <!-- Vendors Grid -->
    <?php if ( empty( $vendors['vendors'] ) ) : ?>
        <div class="bazaar-empty-state">
            <span class="dashicons dashicons-store"></span>
            <h3><?php esc_html_e( 'No vendors found', 'bazaar' ); ?></h3>
            <?php if ( $search_query ) : ?>
                <p><?php printf( esc_html__( 'No vendors matching "%s" were found.', 'bazaar' ), esc_html( $search_query ) ); ?></p>
                <a href="<?php echo esc_url( bazaar_get_store_listing_url() ); ?>" class="button"><?php esc_html_e( 'View All Vendors', 'bazaar' ); ?></a>
            <?php else : ?>
                <p><?php esc_html_e( 'There are no vendors yet. Be the first to join!', 'bazaar' ); ?></p>
                <a href="<?php echo esc_url( bazaar_get_vendor_registration_url() ); ?>" class="button button-primary"><?php esc_html_e( 'Become a Vendor', 'bazaar' ); ?></a>
            <?php endif; ?>
        </div>
    <?php else : ?>
        <div class="vendors-grid">
            <?php foreach ( $vendors['vendors'] as $vendor ) :
                $store_info   = bazaar_get_vendor_store_info( $vendor->ID );
                $rating       = bazaar_get_vendor_rating( $vendor->ID );
                $product_count = bazaar_get_vendor_product_count( $vendor->ID );
                $store_url    = bazaar_get_vendor_store_url( $vendor->ID );
                $logo_url     = ! empty( $store_info['logo'] ) ? $store_info['logo'] : get_avatar_url( $vendor->ID, array( 'size' => 150 ) );
                $banner_url   = ! empty( $store_info['banner'] ) ? $store_info['banner'] : '';
                $is_open      = bazaar_is_vendor_store_open( $vendor->ID );
            ?>
                <div class="vendor-card">
                    <?php if ( $banner_url ) : ?>
                        <div class="vendor-banner" style="background-image: url('<?php echo esc_url( $banner_url ); ?>');">
                        </div>
                    <?php else : ?>
                        <div class="vendor-banner vendor-banner-placeholder">
                        </div>
                    <?php endif; ?>

                    <div class="vendor-info">
                        <div class="vendor-avatar">
                            <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $store_info['name'] ); ?>" />
                            <?php if ( ! $is_open ) : ?>
                                <span class="store-closed-badge"><?php esc_html_e( 'Closed', 'bazaar' ); ?></span>
                            <?php endif; ?>
                        </div>

                        <h3 class="vendor-name">
                            <a href="<?php echo esc_url( $store_url ); ?>"><?php echo esc_html( $store_info['name'] ); ?></a>
                        </h3>

                        <?php if ( ! empty( $store_info['tagline'] ) ) : ?>
                            <p class="vendor-tagline"><?php echo esc_html( $store_info['tagline'] ); ?></p>
                        <?php endif; ?>

                        <div class="vendor-meta">
                            <?php if ( $rating['count'] > 0 ) : ?>
                                <div class="vendor-rating">
                                    <?php bazaar_star_rating( $rating['average'] ); ?>
                                    <span class="rating-value"><?php echo esc_html( number_format( $rating['average'], 1 ) ); ?></span>
                                    <span class="rating-count">(<?php echo esc_html( $rating['count'] ); ?>)</span>
                                </div>
                            <?php else : ?>
                                <div class="vendor-rating no-rating">
                                    <span class="dashicons dashicons-star-empty"></span>
                                    <span><?php esc_html_e( 'No reviews yet', 'bazaar' ); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="vendor-products">
                                <span class="dashicons dashicons-archive"></span>
                                <?php printf( esc_html( _n( '%s product', '%s products', $product_count, 'bazaar' ) ), esc_html( $product_count ) ); ?>
                            </div>
                        </div>

                        <?php if ( ! empty( $store_info['address'] ) ) : ?>
                            <div class="vendor-location">
                                <span class="dashicons dashicons-location"></span>
                                <?php echo esc_html( $store_info['address'] ); ?>
                            </div>
                        <?php endif; ?>

                        <div class="vendor-actions">
                            <a href="<?php echo esc_url( $store_url ); ?>" class="button button-primary"><?php esc_html_e( 'Visit Store', 'bazaar' ); ?></a>
                            <?php if ( is_user_logged_in() && get_current_user_id() !== $vendor->ID ) : ?>
                                <button type="button" class="button bazaar-follow-btn <?php echo bazaar_is_following_vendor( $vendor->ID ) ? 'following' : ''; ?>" data-vendor-id="<?php echo esc_attr( $vendor->ID ); ?>">
                                    <?php echo bazaar_is_following_vendor( $vendor->ID ) ? esc_html__( 'Following', 'bazaar' ) : esc_html__( 'Follow', 'bazaar' ); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ( $vendors['pages'] > 1 ) : ?>
            <?php bazaar_pagination( $vendors['pages'], isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1 ); ?>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Become a Vendor CTA -->
    <?php if ( ! is_user_logged_in() || ! bazaar_is_vendor( get_current_user_id() ) ) : ?>
        <div class="become-vendor-cta">
            <div class="cta-content">
                <h2><?php esc_html_e( 'Want to sell on our marketplace?', 'bazaar' ); ?></h2>
                <p><?php esc_html_e( 'Join our community of vendors and start earning today.', 'bazaar' ); ?></p>
                <a href="<?php echo esc_url( bazaar_get_vendor_registration_url() ); ?>" class="button button-primary button-large"><?php esc_html_e( 'Become a Vendor', 'bazaar' ); ?></a>
            </div>
        </div>
    <?php endif; ?>
</div>
