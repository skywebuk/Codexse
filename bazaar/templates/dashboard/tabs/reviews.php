<?php
/**
 * Dashboard Reviews Tab.
 *
 * @package Bazaar\Templates
 */

defined( 'ABSPATH' ) || exit;

$current_filter = isset( $_GET['filter'] ) ? sanitize_text_field( wp_unslash( $_GET['filter'] ) ) : '';
?>
<div class="bazaar-tab-content bazaar-reviews">
    <h2><?php esc_html_e( 'Store Reviews', 'bazaar' ); ?></h2>

    <!-- Review Stats -->
    <div class="bazaar-review-stats">
        <div class="overall-rating">
            <div class="rating-value"><?php echo esc_html( number_format( $review_stats['average'], 1 ) ); ?></div>
            <div class="rating-stars">
                <?php bazaar_star_rating( $review_stats['average'] ); ?>
            </div>
            <div class="rating-count">
                <?php
                printf(
                    esc_html( _n( '%s review', '%s reviews', $review_stats['total'], 'bazaar' ) ),
                    esc_html( number_format_i18n( $review_stats['total'] ) )
                );
                ?>
            </div>
        </div>

        <div class="rating-breakdown">
            <?php for ( $i = 5; $i >= 1; $i-- ) :
                $count = isset( $review_stats['breakdown'][ $i ] ) ? $review_stats['breakdown'][ $i ] : 0;
                $percentage = $review_stats['total'] > 0 ? ( $count / $review_stats['total'] ) * 100 : 0;
            ?>
                <div class="rating-bar">
                    <span class="stars"><?php echo esc_html( $i ); ?> <?php esc_html_e( 'stars', 'bazaar' ); ?></span>
                    <div class="bar">
                        <div class="fill" style="width: <?php echo esc_attr( $percentage ); ?>%"></div>
                    </div>
                    <span class="count"><?php echo esc_html( $count ); ?></span>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Filters -->
    <div class="bazaar-filters">
        <ul class="review-filter">
            <li>
                <a href="<?php echo esc_url( bazaar_get_dashboard_tab_url( 'reviews' ) ); ?>"
                   class="<?php echo empty( $current_filter ) ? 'active' : ''; ?>">
                    <?php esc_html_e( 'All', 'bazaar' ); ?>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( add_query_arg( 'filter', 'pending', bazaar_get_dashboard_tab_url( 'reviews' ) ) ); ?>"
                   class="<?php echo 'pending' === $current_filter ? 'active' : ''; ?>">
                    <?php esc_html_e( 'Pending Reply', 'bazaar' ); ?>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( add_query_arg( 'filter', 'replied', bazaar_get_dashboard_tab_url( 'reviews' ) ) ); ?>"
                   class="<?php echo 'replied' === $current_filter ? 'active' : ''; ?>">
                    <?php esc_html_e( 'Replied', 'bazaar' ); ?>
                </a>
            </li>
        </ul>
    </div>

    <!-- Reviews List -->
    <?php if ( empty( $reviews['reviews'] ) ) : ?>
        <div class="bazaar-empty-state">
            <span class="dashicons dashicons-star-filled"></span>
            <h3><?php esc_html_e( 'No reviews yet', 'bazaar' ); ?></h3>
            <p><?php esc_html_e( 'Reviews from customers will appear here.', 'bazaar' ); ?></p>
        </div>
    <?php else : ?>
        <div class="bazaar-reviews-list">
            <?php foreach ( $reviews['reviews'] as $review ) :
                $reviewer = get_user_by( 'id', $review->reviewer_id );
                $reviewer_name = $reviewer ? $reviewer->display_name : __( 'Anonymous', 'bazaar' );
            ?>
                <div class="review-item <?php echo $review->vendor_reply ? 'has-reply' : 'no-reply'; ?>">
                    <div class="review-header">
                        <div class="reviewer-info">
                            <?php echo get_avatar( $review->reviewer_id, 48 ); ?>
                            <div class="reviewer-details">
                                <strong class="reviewer-name"><?php echo esc_html( $reviewer_name ); ?></strong>
                                <span class="review-date">
                                    <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $review->created_at ) ) ); ?>
                                </span>
                            </div>
                        </div>
                        <div class="review-rating">
                            <?php bazaar_star_rating( $review->rating ); ?>
                        </div>
                    </div>

                    <?php if ( ! empty( $review->title ) ) : ?>
                        <h4 class="review-title"><?php echo esc_html( $review->title ); ?></h4>
                    <?php endif; ?>

                    <div class="review-content">
                        <?php echo wp_kses_post( wpautop( $review->content ) ); ?>
                    </div>

                    <?php if ( $review->order_id ) : ?>
                        <div class="review-order">
                            <span class="dashicons dashicons-cart"></span>
                            <?php
                            printf(
                                esc_html__( 'Order #%s', 'bazaar' ),
                                esc_html( $review->order_id )
                            );
                            ?>
                        </div>
                    <?php endif; ?>

                    <!-- Vendor Reply -->
                    <?php if ( $review->vendor_reply ) : ?>
                        <div class="vendor-reply">
                            <div class="reply-header">
                                <span class="dashicons dashicons-format-chat"></span>
                                <strong><?php esc_html_e( 'Your Reply', 'bazaar' ); ?></strong>
                                <span class="reply-date">
                                    <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $review->reply_date ) ) ); ?>
                                </span>
                            </div>
                            <div class="reply-content">
                                <?php echo wp_kses_post( wpautop( $review->vendor_reply ) ); ?>
                            </div>
                            <button type="button" class="button button-small edit-reply" data-review-id="<?php echo esc_attr( $review->id ); ?>">
                                <?php esc_html_e( 'Edit Reply', 'bazaar' ); ?>
                            </button>
                        </div>
                    <?php else : ?>
                        <div class="reply-form-wrapper">
                            <button type="button" class="button button-primary reply-btn" data-review-id="<?php echo esc_attr( $review->id ); ?>">
                                <span class="dashicons dashicons-format-chat"></span>
                                <?php esc_html_e( 'Reply', 'bazaar' ); ?>
                            </button>
                            <form class="reply-form" style="display: none;" data-review-id="<?php echo esc_attr( $review->id ); ?>">
                                <?php wp_nonce_field( 'bazaar_reply_review', 'reply_nonce' ); ?>
                                <textarea name="reply_content" rows="3" placeholder="<?php esc_attr_e( 'Write your reply...', 'bazaar' ); ?>" required></textarea>
                                <div class="form-actions">
                                    <button type="submit" class="button button-primary"><?php esc_html_e( 'Submit Reply', 'bazaar' ); ?></button>
                                    <button type="button" class="button cancel-reply"><?php esc_html_e( 'Cancel', 'bazaar' ); ?></button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ( $reviews['pages'] > 1 ) : ?>
            <?php bazaar_pagination( $reviews['pages'], isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1 ); ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
