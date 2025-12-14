<?php
/**
 * Courses Grid Template.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$columns = isset( $atts['columns'] ) ? (int) $atts['columns'] : 3;
?>

<div class="edugo-courses-wrapper">
    <?php if ( $courses->have_posts() ) : ?>
        <div class="edugo-courses-grid edugo-columns-<?php echo esc_attr( $columns ); ?>">
            <?php
            while ( $courses->have_posts() ) :
                $courses->the_post();
                $course_id = get_the_ID();

                $enrollment_manager = new Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
                $student_count = $enrollment_manager->get_enrollment_count( $course_id );

                $is_free = get_post_meta( $course_id, '_edugo_is_free', true ) === 'yes';
                $price = get_post_meta( $course_id, '_edugo_price', true );
                $duration = get_post_meta( $course_id, '_edugo_duration', true );
                $level = wp_get_post_terms( $course_id, 'edugo_course_level', array( 'fields' => 'names' ) );
                ?>

                <article class="edugo-course-card" data-course-id="<?php echo esc_attr( $course_id ); ?>">
                    <div class="edugo-course-thumbnail">
                        <a href="<?php the_permalink(); ?>">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <?php the_post_thumbnail( 'medium_large' ); ?>
                            <?php else : ?>
                                <div class="edugo-no-thumbnail">
                                    <span class="dashicons dashicons-welcome-learn-more"></span>
                                </div>
                            <?php endif; ?>
                        </a>

                        <?php if ( $is_free ) : ?>
                            <span class="edugo-badge edugo-badge-free"><?php esc_html_e( 'Free', 'edugo-lms' ); ?></span>
                        <?php endif; ?>

                        <?php if ( ! empty( $level ) ) : ?>
                            <span class="edugo-badge edugo-badge-level"><?php echo esc_html( $level[0] ); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="edugo-course-content">
                        <div class="edugo-course-meta">
                            <?php
                            $categories = wp_get_post_terms( $course_id, 'edugo_course_category', array( 'fields' => 'names' ) );
                            if ( ! empty( $categories ) ) :
                                ?>
                                <span class="edugo-course-category"><?php echo esc_html( $categories[0] ); ?></span>
                            <?php endif; ?>
                        </div>

                        <h3 class="edugo-course-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>

                        <div class="edugo-course-excerpt">
                            <?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 15 ) ); ?>
                        </div>

                        <div class="edugo-course-author">
                            <?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?>
                            <span class="edugo-author-name"><?php the_author(); ?></span>
                        </div>

                        <div class="edugo-course-footer">
                            <div class="edugo-course-stats">
                                <span class="edugo-stat">
                                    <span class="dashicons dashicons-groups"></span>
                                    <?php echo esc_html( $student_count ); ?>
                                </span>
                                <?php if ( $duration ) : ?>
                                    <span class="edugo-stat">
                                        <span class="dashicons dashicons-clock"></span>
                                        <?php echo esc_html( $duration ); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="edugo-course-price">
                                <?php if ( $is_free ) : ?>
                                    <span class="edugo-price-free"><?php esc_html_e( 'Free', 'edugo-lms' ); ?></span>
                                <?php elseif ( $price ) : ?>
                                    <span class="edugo-price"><?php echo wp_kses_post( wc_price( $price ) ); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </article>

            <?php endwhile; ?>
        </div>

        <?php
        // Pagination.
        $big = 999999999;
        echo '<div class="edugo-pagination">';
        echo paginate_links( array(
            'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format'    => '?paged=%#%',
            'current'   => max( 1, get_query_var( 'paged' ) ),
            'total'     => $courses->max_num_pages,
            'prev_text' => '&laquo; ' . __( 'Previous', 'edugo-lms' ),
            'next_text' => __( 'Next', 'edugo-lms' ) . ' &raquo;',
        ) );
        echo '</div>';
        ?>

        <?php wp_reset_postdata(); ?>

    <?php else : ?>
        <div class="edugo-no-courses">
            <p><?php esc_html_e( 'No courses found.', 'edugo-lms' ); ?></p>
        </div>
    <?php endif; ?>
</div>
