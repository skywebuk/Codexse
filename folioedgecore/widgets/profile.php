<?php
/**
 * Author Information Widget
 *
 * @package Folioedgecore
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class folioedge_author_info
 *
 * Displays author information with image and social links
 */
class folioedge_author_info extends WP_Widget {

    /**
     * Social networks configuration
     *
     * @var array
     */
    private $social_networks = array();

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_media_scripts' ) );

        parent::__construct(
            'folioedge_author_info',
            esc_html__( 'Author Information', 'folioedgecore' ),
            array(
                'description' => esc_html__( 'Display author information with image and social links.', 'folioedgecore' ),
                'classname'   => 'folioedge-author-info-widget',
            )
        );

        $this->social_networks = array(
            'facebook'  => array(
                'label' => esc_html__( 'Facebook URL', 'folioedgecore' ),
                'icon'  => 'ic-facebook',
            ),
            'twitter'   => array(
                'label' => esc_html__( 'Twitter URL', 'folioedgecore' ),
                'icon'  => 'ic-twitter',
            ),
            'linkedin'  => array(
                'label' => esc_html__( 'LinkedIn URL', 'folioedgecore' ),
                'icon'  => 'ic-linkedin',
            ),
            'instagram' => array(
                'label' => esc_html__( 'Instagram URL', 'folioedgecore' ),
                'icon'  => 'ic-instagram',
            ),
            'youtube'   => array(
                'label' => esc_html__( 'YouTube URL', 'folioedgecore' ),
                'icon'  => 'ic-youtube',
            ),
            'behance'   => array(
                'label' => esc_html__( 'Behance URL', 'folioedgecore' ),
                'icon'  => 'ic-behance',
            ),
            'dribbble'  => array(
                'label' => esc_html__( 'Dribbble URL', 'folioedgecore' ),
                'icon'  => 'ic-dribbble',
            ),
        );
    }

    /**
     * Enqueue media scripts for image upload
     */
    public function enqueue_media_scripts() {
        global $pagenow;

        if ( 'widgets.php' === $pagenow || 'customize.php' === $pagenow ) {
            wp_enqueue_script( 'media-upload' );
            wp_enqueue_media();
        }
    }

    /**
     * Frontend display of widget
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
        $title = apply_filters( 'widget_title', $title );

        if ( $title ) {
            echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        $author_image = ! empty( $instance['author_image'] ) ? $instance['author_image'] : '';
        $author_name  = ! empty( $instance['author_name'] ) ? $instance['author_name'] : '';
        $author_bio   = ! empty( $instance['author_bio'] ) ? $instance['author_bio'] : '';
        ?>
        <div class="widget-content">
            <?php if ( $author_image ) : ?>
                <figure class="author-pic">
                    <img
                        src="<?php echo esc_url( $author_image ); ?>"
                        alt="<?php echo esc_attr( $author_name ); ?>"
                        loading="lazy"
                    />
                </figure>
            <?php endif; ?>

            <?php if ( $author_name ) : ?>
                <h4 class="author-name"><?php echo esc_html( $author_name ); ?></h4>
            <?php endif; ?>

            <?php if ( $author_bio ) : ?>
                <?php echo wp_kses_post( wpautop( $author_bio ) ); ?>
            <?php endif; ?>

            <?php $this->render_social_links( $instance ); ?>
        </div>
        <?php

        echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    /**
     * Render social links
     *
     * @param array $instance Widget instance.
     */
    private function render_social_links( $instance ) {
        $has_links = false;

        foreach ( $this->social_networks as $network => $config ) {
            $field_key = 'author_' . $network;
            if ( ! empty( $instance[ $field_key ] ) ) {
                $has_links = true;
                break;
            }
        }

        if ( ! $has_links ) {
            return;
        }

        $svg_url = get_theme_file_uri( 'assets/images/symble.svg' );
        ?>
        <div class="author-social">
            <?php foreach ( $this->social_networks as $network => $config ) : ?>
                <?php
                $field_key = 'author_' . $network;
                $url       = ! empty( $instance[ $field_key ] ) ? $instance[ $field_key ] : '';

                if ( $url ) :
                    ?>
                    <a href="<?php echo esc_url( $url ); ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       aria-label="<?php echo esc_attr( sprintf( __( '%s profile', 'folioedgecore' ), ucfirst( $network ) ) ); ?>">
                        <span class="s-icon">
                            <svg aria-hidden="true">
                                <use xlink:href="<?php echo esc_url( $svg_url . '#' . $config['icon'] ); ?>"></use>
                            </svg>
                        </span>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Backend widget form
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        $title        = isset( $instance['title'] ) ? $instance['title'] : '';
        $author_image = ! empty( $instance['author_image'] ) ? $instance['author_image'] : '';
        $author_name  = ! empty( $instance['author_name'] ) ? $instance['author_name'] : '';
        $author_bio   = ! empty( $instance['author_bio'] ) ? $instance['author_bio'] : '';
        ?>
        <div class="media-widget-control">
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
                    <?php esc_html_e( 'Widget Title:', 'folioedgecore' ); ?>
                </label>
                <input
                    class="widefat title"
                    id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                    name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
                    type="text"
                    value="<?php echo esc_attr( $title ); ?>"
                />
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'author_name' ) ); ?>">
                    <?php esc_html_e( 'Author Name', 'folioedgecore' ); ?>
                </label>
                <input
                    type="text"
                    class="widefat"
                    id="<?php echo esc_attr( $this->get_field_id( 'author_name' ) ); ?>"
                    name="<?php echo esc_attr( $this->get_field_name( 'author_name' ) ); ?>"
                    value="<?php echo esc_attr( $author_name ); ?>"
                />
            </p>

            <p class="image_upload_part">
                <label for="<?php echo esc_attr( $this->get_field_id( 'author_image' ) ); ?>">
                    <?php esc_html_e( 'Upload Author Image:', 'folioedgecore' ); ?>
                </label>
                <input
                    class="author_image_url widefat"
                    id="<?php echo esc_attr( $this->get_field_id( 'author_image' ) ); ?>"
                    name="<?php echo esc_attr( $this->get_field_name( 'author_image' ) ); ?>"
                    type="url"
                    value="<?php echo esc_url( $author_image ); ?>"
                />
                <?php if ( empty( $author_image ) ) : ?>
                    <span class="media-widget-preview">
                        <span class="placeholder upload_image_button">
                            <?php esc_html_e( 'No image selected', 'folioedgecore' ); ?>
                        </span>
                    </span>
                <?php else : ?>
                    <span class="image_preview">
                        <img src="<?php echo esc_url( $author_image ); ?>" class="upload_image_button dm_image" alt="" />
                    </span>
                <?php endif; ?>

                <span class="media-widget-buttons">
                    <button type="button" class="upload_image_button button folioedge_image_upload">
                        <?php esc_html_e( 'Add Image', 'folioedgecore' ); ?>
                    </button>
                </span>
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'author_bio' ) ); ?>">
                    <?php esc_html_e( 'Author Bio', 'folioedgecore' ); ?>
                </label>
                <textarea
                    name="<?php echo esc_attr( $this->get_field_name( 'author_bio' ) ); ?>"
                    id="<?php echo esc_attr( $this->get_field_id( 'author_bio' ) ); ?>"
                    cols="10"
                    rows="5"
                    class="widefat"
                ><?php echo esc_textarea( $author_bio ); ?></textarea>
            </p>

            <hr />

            <?php foreach ( $this->social_networks as $network => $config ) : ?>
                <?php
                $field_key = 'author_' . $network;
                $value     = ! empty( $instance[ $field_key ] ) ? $instance[ $field_key ] : '';
                ?>
                <p>
                    <label for="<?php echo esc_attr( $this->get_field_id( $field_key ) ); ?>">
                        <?php echo esc_html( $config['label'] ); ?>
                    </label>
                    <input
                        type="url"
                        class="widefat"
                        name="<?php echo esc_attr( $this->get_field_name( $field_key ) ); ?>"
                        id="<?php echo esc_attr( $this->get_field_id( $field_key ) ); ?>"
                        value="<?php echo esc_url( $value ); ?>"
                    />
                </p>
            <?php endforeach; ?>
        </div>
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
        $instance['author_image'] = ! empty( $new_instance['author_image'] ) ? esc_url_raw( $new_instance['author_image'] ) : '';
        $instance['author_name']  = ! empty( $new_instance['author_name'] ) ? sanitize_text_field( $new_instance['author_name'] ) : '';
        $instance['author_bio']   = ! empty( $new_instance['author_bio'] ) ? sanitize_textarea_field( $new_instance['author_bio'] ) : '';

        foreach ( $this->social_networks as $network => $config ) {
            $field_key             = 'author_' . $network;
            $instance[ $field_key ] = ! empty( $new_instance[ $field_key ] ) ? esc_url_raw( $new_instance[ $field_key ] ) : '';
        }

        return $instance;
    }
}
