<?php
/**
 * Vendor Store Reviews Template.
 *
 * @package Bazaar\Templates
 */

defined( 'ABSPATH' ) || exit;

$rating_stats = bazaar_get_vendor_rating( $vendor->ID );
$can_review   = is_user_logged_in() && get_current_user_id() !== $vendor->ID && bazaar_can_user_review_vendor( get_current_user_id(), $vendor->ID );
?>

<div class="bazaar-store-reviews">
    <!-- Rating Summary -->
    <div class="reviews-summary">
        <div class="overall-rating">
            <span class="rating-number"><?php echo esc_html( number_format( $rating_stats['average'], 1 ) ); ?></span>
            <div class="rating-stars">
                <?php bazaar_star_rating( $rating_stats['average'] ); ?>
            </div>
            <span class="rating-count">
                <?php printf( esc_html( _n( '%s review', '%s reviews', $rating_stats['count'], 'bazaar' ) ), esc_html( $rating_stats['count'] ) ); ?>
            </span>
        </div>

        <div class="rating-breakdown">
            <?php for ( $i = 5; $i >= 1; $i-- ) :
                $count = isset( $rating_stats['breakdown'][ $i ] ) ? $rating_stats['breakdown'][ $i ] : 0;
                $percentage = $rating_stats['count'] > 0 ? ( $count / $rating_stats['count'] ) * 100 : 0;
            ?>
                <div class="rating-row">
                    <span class="rating-label"><?php echo esc_html( $i ); ?> <span class="dashicons dashicons-star-filled"></span></span>
                    <div class="rating-bar">
                        <div class="rating-fill" style="width: <?php echo esc_attr( $percentage ); ?>%"></div>
                    </div>
                    <span class="rating-count"><?php echo esc_html( $count ); ?></span>
                </div>
            <?php endfor; ?>
        </div>

        <?php if ( $can_review ) : ?>
            <div class="write-review-cta">
                <p><?php esc_html_e( 'Have you purchased from this vendor?', 'bazaar' ); ?></p>
                <button type="button" class="button button-primary open-review-form">
                    <?php esc_html_e( 'Write a Review', 'bazaar' ); ?>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Review Form -->
    <?php if ( $can_review ) : ?>
        <div class="review-form-wrapper" style="display: none;">
            <h3><?php esc_html_e( 'Write Your Review', 'bazaar' ); ?></h3>
            <form id="bazaar-vendor-review-form" method="post">
                <?php wp_nonce_field( 'bazaar_submit_review', 'review_nonce' ); ?>
                <input type="hidden" name="vendor_id" value="<?php echo esc_attr( $vendor->ID ); ?>" />

                <div class="form-row">
                    <label><?php esc_html_e( 'Your Rating', 'bazaar' ); ?> <span class="required">*</span></label>
                    <div class="rating-selector">
                        <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                            <input type="radio" name="rating" id="rating-<?php echo esc_attr( $i ); ?>" value="<?php echo esc_attr( $i ); ?>" required />
                            <label for="rating-<?php echo esc_attr( $i ); ?>" class="star-label">
                                <span class="dashicons dashicons-star-empty"></span>
                                <span class="dashicons dashicons-star-filled"></span>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="form-row">
                    <label for="review-title"><?php esc_html_e( 'Review Title', 'bazaar' ); ?></label>
                    <input type="text" name="title" id="review-title" placeholder="<?php esc_attr_e( 'Summarize your experience', 'bazaar' ); ?>" />
                </div>

                <div class="form-row">
                    <label for="review-content"><?php esc_html_e( 'Your Review', 'bazaar' ); ?> <span class="required">*</span></label>
                    <textarea name="content" id="review-content" rows="5" placeholder="<?php esc_attr_e( 'Share your experience with this vendor...', 'bazaar' ); ?>" required></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="button button-primary"><?php esc_html_e( 'Submit Review', 'bazaar' ); ?></button>
                    <button type="button" class="button cancel-review"><?php esc_html_e( 'Cancel', 'bazaar' ); ?></button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Reviews List -->
    <div class="reviews-list">
        <?php if ( empty( $reviews['reviews'] ) ) : ?>
            <div class="bazaar-empty-state">
                <span class="dashicons dashicons-star-empty"></span>
                <h3><?php esc_html_e( 'No reviews yet', 'bazaar' ); ?></h3>
                <p><?php esc_html_e( 'Be the first to review this vendor!', 'bazaar' ); ?></p>
            </div>
        <?php else : ?>
            <?php foreach ( $reviews['reviews'] as $review ) :
                $reviewer = get_user_by( 'id', $review->reviewer_id );
                $reviewer_name = $reviewer ? $reviewer->display_name : __( 'Anonymous', 'bazaar' );
                $reviewer_avatar = get_avatar_url( $review->reviewer_id, array( 'size' => 60 ) );
            ?>
                <div class="review-item">
                    <div class="review-header">
                        <img src="<?php echo esc_url( $reviewer_avatar ); ?>" alt="<?php echo esc_attr( $reviewer_name ); ?>" class="reviewer-avatar" />
                        <div class="reviewer-info">
                            <strong class="reviewer-name"><?php echo esc_html( $reviewer_name ); ?></strong>
                            <div class="review-meta">
                                <?php bazaar_star_rating( $review->rating ); ?>
                                <span class="review-date">
                                    <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $review->created_at ) ) ); ?>
                                </span>
                            </div>
                        </div>
                        <?php if ( $review->order_id ) : ?>
                            <span class="verified-badge" title="<?php esc_attr_e( 'Verified Purchase', 'bazaar' ); ?>">
                                <span class="dashicons dashicons-yes-alt"></span>
                                <?php esc_html_e( 'Verified', 'bazaar' ); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if ( ! empty( $review->title ) ) : ?>
                        <h4 class="review-title"><?php echo esc_html( $review->title ); ?></h4>
                    <?php endif; ?>

                    <div class="review-content">
                        <?php echo wp_kses_post( wpautop( $review->content ) ); ?>
                    </div>

                    <?php if ( ! empty( $review->vendor_reply ) ) : ?>
                        <div class="vendor-reply">
                            <div class="reply-header">
                                <span class="dashicons dashicons-admin-comments"></span>
                                <strong><?php esc_html_e( 'Vendor Response', 'bazaar' ); ?></strong>
                                <span class="reply-date">
                                    <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $review->reply_date ) ) ); ?>
                                </span>
                            </div>
                            <div class="reply-content">
                                <?php echo wp_kses_post( wpautop( $review->vendor_reply ) ); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="review-actions">
                        <button type="button" class="helpful-btn" data-review-id="<?php echo esc_attr( $review->id ); ?>">
                            <span class="dashicons dashicons-thumbs-up"></span>
                            <?php esc_html_e( 'Helpful', 'bazaar' ); ?>
                            <?php if ( $review->helpful_count > 0 ) : ?>
                                <span class="helpful-count">(<?php echo esc_html( $review->helpful_count ); ?>)</span>
                            <?php endif; ?>
                        </button>
                        <button type="button" class="report-btn" data-review-id="<?php echo esc_attr( $review->id ); ?>">
                            <span class="dashicons dashicons-flag"></span>
                            <?php esc_html_e( 'Report', 'bazaar' ); ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if ( $reviews['pages'] > 1 ) : ?>
                <?php bazaar_pagination( $reviews['pages'], isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1 ); ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
