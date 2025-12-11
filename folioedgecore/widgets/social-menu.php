<?php
/**
 * Social Menu Widget
 *
 * @package Folioedgecore
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class folioedge_social_menu
 *
 * Displays social media profile links
 */
class folioedge_social_menu extends WP_Widget {

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
        parent::__construct(
            'folioedge_social_menu',
            esc_html__( 'Social Profile', 'folioedgecore' ),
            array(
                'description' => esc_html__( 'Display social profile links.', 'folioedgecore' ),
                'classname'   => 'folioedge-social-menu-widget',
            )
        );

        $this->social_networks = array(
            'facebook'  => array(
                'label' => esc_html__( 'Facebook URL', 'folioedgecore' ),
                'icon'  => 'fab fa-facebook-f',
            ),
            'twitter'   => array(
                'label' => esc_html__( 'Twitter URL', 'folioedgecore' ),
                'icon'  => 'fab fa-twitter',
            ),
            'linkedin'  => array(
                'label' => esc_html__( 'LinkedIn URL', 'folioedgecore' ),
                'icon'  => 'fab fa-linkedin-in',
            ),
            'instagram' => array(
                'label' => esc_html__( 'Instagram URL', 'folioedgecore' ),
                'icon'  => 'fab fa-instagram',
            ),
            'youtube'   => array(
                'label' => esc_html__( 'YouTube URL', 'folioedgecore' ),
                'icon'  => 'fab fa-youtube',
            ),
            'pinterest' => array(
                'label' => esc_html__( 'Pinterest URL', 'folioedgecore' ),
                'icon'  => 'fab fa-pinterest-p',
            ),
            'flickr'    => array(
                'label' => esc_html__( 'Flickr URL', 'folioedgecore' ),
                'icon'  => 'fab fa-flickr',
            ),
            'whatsapp'  => array(
                'label' => esc_html__( 'WhatsApp URL', 'folioedgecore' ),
                'icon'  => 'fab fa-whatsapp',
            ),
        );
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

        echo '<div class="social_menu">';

        foreach ( $this->social_networks as $network => $config ) {
            $field_key = 'social_' . $network;
            $url       = ! empty( $instance[ $field_key ] ) ? $instance[ $field_key ] : '';

            if ( $url ) {
                printf(
                    '<a href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s"><i class="%s" aria-hidden="true"></i></a>',
                    esc_url( $url ),
                    esc_attr( sprintf( __( 'Visit our %s profile', 'folioedgecore' ), ucfirst( $network ) ) ),
                    esc_attr( $config['icon'] )
                );
            }
        }

        echo '</div>';

        echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    /**
     * Backend widget form
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        $title = isset( $instance['title'] ) ? $instance['title'] : '';
        ?>
        <div class="media-widget-control">
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
                    <?php esc_html_e( 'Title:', 'folioedgecore' ); ?>
                </label>
                <input
                    class="widefat title"
                    id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                    name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
                    type="text"
                    value="<?php echo esc_attr( $title ); ?>"
                />
            </p>

            <?php foreach ( $this->social_networks as $network => $config ) : ?>
                <?php
                $field_key = 'social_' . $network;
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
        $instance          = array();
        $instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';

        foreach ( $this->social_networks as $network => $config ) {
            $field_key             = 'social_' . $network;
            $instance[ $field_key ] = ! empty( $new_instance[ $field_key ] ) ? esc_url_raw( $new_instance[ $field_key ] ) : '';
        }

        return $instance;
    }
}
