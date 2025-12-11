<?php
/**
 * Popular Posts Widget
 *
 * @package Folioedgecore
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class folioedge_popular_posts
 *
 * Displays popular posts based on view count
 */
class folioedge_popular_posts extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'folioedge_popular_posts',
            esc_html__( 'Folioedge Popular Posts', 'folioedgecore' ),
            array(
                'description' => esc_html__( 'Display popular posts based on view count.', 'folioedgecore' ),
                'classname'   => 'folioedge-popular-posts-widget',
            )
        );
    }

    /**
     * Frontend display of widget
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        $number       = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $title_length = isset( $instance['title_length'] ) ? absint( $instance['title_length'] ) : 5;
        $show_meta    = ! empty( $instance['show_meta'] );
        $show_thumb   = ! empty( $instance['show_thumb'] );

        echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . esc_html( $instance['title'] ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        $post_query = new WP_Query( array(
            'post_type'      => 'post',
            'posts_per_page' => $number,
            'meta_key'       => 'post_views_count', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
            'post_status'    => 'publish',
        ) );

        if ( $post_query->have_posts() ) :
            ?>
            <div class="popular-posts">
                <?php
                while ( $post_query->have_posts() ) :
                    $post_query->the_post();
                    ?>
                    <article <?php post_class( 'post-item' ); ?>>
                        <?php if ( has_post_thumbnail() && $show_thumb ) : ?>
                            <a href="<?php the_permalink(); ?>" class="post-pic">
                                <?php the_post_thumbnail( 'thumbnail', array( 'loading' => 'lazy' ) ); ?>
                            </a>
                        <?php endif; ?>

                        <div class="post-content">
                            <?php if ( get_the_title() ) : ?>
                                <h4 class="title">
                                    <a href="<?php the_permalink(); ?>" rel="bookmark">
                                        <?php echo esc_html( wp_trim_words( get_the_title(), $title_length, '...' ) ); ?>
                                    </a>
                                </h4>
                            <?php endif; ?>

                            <?php if ( $show_meta ) : ?>
                                <ul class="post-meta-list">
                                    <li class="post-meta-item post-date">
                                        <span class="icon"><i class="fal fa-clock" aria-hidden="true"></i></span>
                                        <span class="value"><?php echo esc_html( get_the_date() ); ?></span>
                                    </li>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
            <?php
        else :
            ?>
            <p class="no-posts"><?php esc_html_e( 'No popular posts found.', 'folioedgecore' ); ?></p>
            <?php
        endif;

        echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    /**
     * Backend widget form
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        $title        = isset( $instance['title'] ) ? $instance['title'] : '';
        $number       = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $title_length = isset( $instance['title_length'] ) ? absint( $instance['title_length'] ) : 5;
        $show_meta    = isset( $instance['show_meta'] ) ? (bool) $instance['show_meta'] : true;
        $show_thumb   = isset( $instance['show_thumb'] ) ? (bool) $instance['show_thumb'] : true;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
                <?php esc_html_e( 'Title:', 'folioedgecore' ); ?>
            </label>
            <input
                class="widefat"
                id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
                type="text"
                value="<?php echo esc_attr( $title ); ?>"
            />
        </p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title_length' ) ); ?>">
                <?php esc_html_e( 'Post Title Length (words):', 'folioedgecore' ); ?>
            </label>
            <input
                class="tiny-text"
                id="<?php echo esc_attr( $this->get_field_id( 'title_length' ) ); ?>"
                name="<?php echo esc_attr( $this->get_field_name( 'title_length' ) ); ?>"
                type="number"
                step="1"
                min="1"
                max="50"
                value="<?php echo esc_attr( $title_length ); ?>"
                size="3"
            />
        </p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>">
                <?php esc_html_e( 'Number of posts to show:', 'folioedgecore' ); ?>
            </label>
            <input
                class="tiny-text"
                id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"
                name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>"
                type="number"
                step="1"
                min="1"
                max="20"
                value="<?php echo esc_attr( $number ); ?>"
                size="3"
            />
        </p>

        <p>
            <input
                class="checkbox"
                type="checkbox"
                id="<?php echo esc_attr( $this->get_field_id( 'show_meta' ) ); ?>"
                name="<?php echo esc_attr( $this->get_field_name( 'show_meta' ) ); ?>"
                <?php checked( $show_meta ); ?>
            />
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_meta' ) ); ?>">
                <?php esc_html_e( 'Display Post Date?', 'folioedgecore' ); ?>
            </label>
        </p>

        <p>
            <input
                class="checkbox"
                type="checkbox"
                id="<?php echo esc_attr( $this->get_field_id( 'show_thumb' ) ); ?>"
                name="<?php echo esc_attr( $this->get_field_name( 'show_thumb' ) ); ?>"
                <?php checked( $show_thumb ); ?>
            />
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_thumb' ) ); ?>">
                <?php esc_html_e( 'Display Post Thumbnail?', 'folioedgecore' ); ?>
            </label>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance                 = array();
        $instance['title']        = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
        $instance['number']       = ! empty( $new_instance['number'] ) ? absint( $new_instance['number'] ) : 5;
        $instance['title_length'] = ! empty( $new_instance['title_length'] ) ? absint( $new_instance['title_length'] ) : 5;
        $instance['show_meta']    = ! empty( $new_instance['show_meta'] );
        $instance['show_thumb']   = ! empty( $new_instance['show_thumb'] );

        return $instance;
    }
}
