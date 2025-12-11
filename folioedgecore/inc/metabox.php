<?php
/**
 * Custom Metaboxes using CMB2
 *
 * @package Folioedgecore
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Register custom metaboxes
 */
add_action( 'cmb2_admin_init', 'folioedge_register_post_metabox' );

/**
 * Register all metaboxes
 */
function folioedge_register_post_metabox() {
    $prefix = '_folioedge_';

    // WooCommerce Product Meta Box (only if WooCommerce is active)
    if ( class_exists( 'WooCommerce' ) ) {
        folioedge_register_wc_product_metabox( $prefix );
    }

    // User Meta Box
    folioedge_register_user_metabox( $prefix );

    // Page Meta Box
    folioedge_register_page_metabox( $prefix );

    // Post Meta Box
    folioedge_register_post_metabox_fields( $prefix );

    // Team Meta Box
    folioedge_register_team_metabox( $prefix );
}

/**
 * Register WooCommerce Product Metabox
 *
 * @param string $prefix Meta key prefix
 */
function folioedge_register_wc_product_metabox( $prefix ) {
    $folioedge_wc_meta = new_cmb2_box( array(
        'id'            => $prefix . 'wc_product_options',
        'title'         => esc_html__( 'Product Addons', 'folioedgecore' ),
        'object_types'  => array( 'product' ),
        'context'       => 'side',
        'priority'      => 'low',
    ) );

    // Repeatable group for extra meta
    $wc_meta_option = $folioedge_wc_meta->add_field( array(
        'id'          => $prefix . 'wc_meta_repeat_group',
        'type'        => 'group',
        'options'     => array(
            'group_title'   => esc_html__( 'Extra Meta {#}', 'folioedgecore' ),
            'add_button'    => esc_html__( 'Add another meta', 'folioedgecore' ),
            'remove_button' => esc_html__( 'Remove meta', 'folioedgecore' ),
            'sortable'      => true,
        ),
    ) );

    $folioedge_wc_meta->add_group_field( $wc_meta_option, array(
        'name' => esc_html__( 'Title', 'folioedgecore' ),
        'id'   => $prefix . 'wc_meta_title',
        'type' => 'text',
    ) );

    $folioedge_wc_meta->add_group_field( $wc_meta_option, array(
        'name' => esc_html__( 'Value', 'folioedgecore' ),
        'id'   => $prefix . 'wc_meta_value',
        'type' => 'textarea_small',
    ) );

    $folioedge_wc_meta->add_field( array(
        'name' => esc_html__( 'Extra Button Label', 'folioedgecore' ),
        'id'   => $prefix . 'wc_ex_button_label',
        'type' => 'text_medium',
        'desc' => esc_html__( 'Text for the extra button on product page', 'folioedgecore' ),
    ) );

    $folioedge_wc_meta->add_field( array(
        'name' => esc_html__( 'Extra Button URL', 'folioedgecore' ),
        'id'   => $prefix . 'wc_ex_button_url',
        'type' => 'text_url',
        'desc' => esc_html__( 'URL for the extra button', 'folioedgecore' ),
    ) );
}

/**
 * Register User Metabox
 *
 * @param string $prefix Meta key prefix
 */
function folioedge_register_user_metabox( $prefix ) {
    $folioedge_user_meta = new_cmb2_box( array(
        'id'            => $prefix . 'user_options',
        'title'         => esc_html__( 'Social Profile', 'folioedgecore' ),
        'object_types'  => array( 'user' ),
        'priority'      => 'high',
    ) );

    $social_fields = array(
        'facebook'  => esc_html__( 'Facebook URL', 'folioedgecore' ),
        'twitter'   => esc_html__( 'Twitter URL', 'folioedgecore' ),
        'linkedin'  => esc_html__( 'LinkedIn URL', 'folioedgecore' ),
        'instagram' => esc_html__( 'Instagram URL', 'folioedgecore' ),
        'pinterest' => esc_html__( 'Pinterest URL', 'folioedgecore' ),
    );

    foreach ( $social_fields as $key => $label ) {
        $folioedge_user_meta->add_field( array(
            'name' => $label,
            'id'   => $prefix . 'user_' . $key,
            'type' => 'text_url',
        ) );
    }
}

/**
 * Register Page Metabox
 *
 * @param string $prefix Meta key prefix
 */
function folioedge_register_page_metabox( $prefix ) {
    $folioedge_page_meta = new_cmb2_box( array(
        'id'            => $prefix . 'page_options',
        'title'         => esc_html__( 'Page Options', 'folioedgecore' ),
        'object_types'  => array( 'page', 'service', 'project', 'team' ),
    ) );

    $folioedge_page_meta->add_field( array(
        'name' => esc_html__( 'Onepage Template', 'folioedgecore' ),
        'id'   => $prefix . 'one_page_template',
        'type' => 'checkbox',
        'desc' => esc_html__( 'Will this page use as a onepage template?', 'folioedgecore' ),
    ) );

    $folioedge_page_meta->add_field( array(
        'name' => esc_html__( 'Onepage Scroll', 'folioedgecore' ),
        'id'   => $prefix . 'one_page_scroll',
        'type' => 'checkbox',
        'desc' => esc_html__( 'Enable smooth scroll to ID selected sections?', 'folioedgecore' ),
    ) );

    $folioedge_page_meta->add_field( array(
        'name' => esc_html__( 'Remove Container', 'folioedgecore' ),
        'id'   => $prefix . 'remove_page_container',
        'type' => 'checkbox',
        'desc' => esc_html__( 'Remove the default page container to use Elementor container.', 'folioedgecore' ),
    ) );

    $folioedge_page_meta->add_field( array(
        'name' => esc_html__( 'Remove Page Header', 'folioedgecore' ),
        'id'   => $prefix . 'page_header',
        'type' => 'checkbox',
        'desc' => esc_html__( 'Check this field if you want to remove page header on this page.', 'folioedgecore' ),
    ) );

    $folioedge_page_meta->add_field( array(
        'name' => esc_html__( 'Remove Footer Area', 'folioedgecore' ),
        'id'   => $prefix . 'footer_widget',
        'type' => 'checkbox',
        'desc' => esc_html__( 'Check this field if you want to remove footer widgets on this page.', 'folioedgecore' ),
    ) );

    $folioedge_page_meta->add_field( array(
        'name'       => esc_html__( 'Page Background Image', 'folioedgecore' ),
        'desc'       => esc_html__( 'Upload an image or enter a URL.', 'folioedgecore' ),
        'id'         => $prefix . 'page_background',
        'type'       => 'file',
        'text'       => array(
            'add_upload_file_text' => esc_html__( 'Add Background', 'folioedgecore' ),
        ),
        'query_args' => array(
            'type' => array(
                'image/gif',
                'image/jpeg',
                'image/png',
                'image/webp',
            ),
        ),
        'preview_size' => 'medium',
    ) );
}

/**
 * Register Post Metabox
 *
 * @param string $prefix Meta key prefix
 */
function folioedge_register_post_metabox_fields( $prefix ) {
    $folioedge_post_meta = new_cmb2_box( array(
        'id'            => $prefix . 'post_metabox',
        'title'         => esc_html__( 'Additional Fields', 'folioedgecore' ),
        'object_types'  => array( 'post' ),
    ) );

    $folioedge_post_meta->add_field( array(
        'name' => esc_html__( 'Photo Gallery', 'folioedgecore' ),
        'desc' => esc_html__( 'This field is for gallery images. This gallery shows for gallery post format.', 'folioedgecore' ),
        'id'   => $prefix . 'post_gallery',
        'type' => 'file_list',
        'text' => array(
            'add_upload_files_text' => esc_html__( 'Add images', 'folioedgecore' ),
        ),
    ) );

    $folioedge_post_meta->add_field( array(
        'name' => esc_html__( 'Embed Video', 'folioedgecore' ),
        'desc' => sprintf(
            /* translators: %s: WordPress Embeds URL */
            esc_html__( 'Enter a YouTube, Twitter, or Instagram URL. Supports services listed at %s. This video shows for video post format.', 'folioedgecore' ),
            '<a href="https://wordpress.org/documentation/article/embeds/" target="_blank">WordPress Embeds</a>'
        ),
        'id'   => $prefix . 'post_video_embed',
        'type' => 'oembed',
    ) );

    $folioedge_post_meta->add_field( array(
        'name' => esc_html__( 'Embed Audio', 'folioedgecore' ),
        'desc' => sprintf(
            /* translators: %s: WordPress Embeds URL */
            esc_html__( 'Enter a SoundCloud, Mixcloud, or ReverbNation URL. Supports services listed at %s. This audio shows for audio post format.', 'folioedgecore' ),
            '<a href="https://wordpress.org/documentation/article/embeds/" target="_blank">WordPress Embeds</a>'
        ),
        'id'   => $prefix . 'post_audio_embed',
        'type' => 'oembed',
    ) );
}

/**
 * Register Team Metabox
 *
 * @param string $prefix Meta key prefix
 */
function folioedge_register_team_metabox( $prefix ) {
    $folioedge_team = new_cmb2_box( array(
        'id'            => $prefix . 'team_metabox',
        'title'         => esc_html__( 'Team Details', 'folioedgecore' ),
        'object_types'  => array( 'team' ),
    ) );

    $folioedge_team->add_field( array(
        'name' => esc_html__( 'Position', 'folioedgecore' ),
        'id'   => $prefix . 'team_position',
        'type' => 'text_medium',
        'desc' => esc_html__( 'Job title or position', 'folioedgecore' ),
    ) );

    $team_social_fields = array(
        'facebook'  => esc_html__( 'Facebook URL', 'folioedgecore' ),
        'twitter'   => esc_html__( 'Twitter URL', 'folioedgecore' ),
        'linkedin'  => esc_html__( 'LinkedIn URL', 'folioedgecore' ),
        'instagram' => esc_html__( 'Instagram URL', 'folioedgecore' ),
    );

    foreach ( $team_social_fields as $key => $label ) {
        $folioedge_team->add_field( array(
            'name' => $label,
            'id'   => $prefix . 'team_' . $key,
            'type' => 'text_url',
        ) );
    }
}

/**
 * Gallery Photo List Output
 *
 * @param array  $gallery_images Gallery images array
 * @param string $img_size Image size
 * @return string|false
 */
if ( ! function_exists( 'folioedge_gallery_photo_list' ) ) {
    function folioedge_gallery_photo_list( $gallery_images, $img_size = 'large' ) {
        if ( empty( $gallery_images ) ) {
            return false;
        }

        $output = '<div class="photo_slider swiper-container post-media">';
        $output .= '<div class="swiper-wrapper">';

        // Handle both array formats
        $images = is_array( $gallery_images[0] ) ? $gallery_images[0] : $gallery_images;

        foreach ( $images as $image_id => $image_url ) {
            $output .= '<div class="swiper-slide">';
            $output .= '<div class="gallery-item">';
            $output .= wp_get_attachment_image( $image_id, $img_size );
            $output .= '</div>';
            $output .= '</div>';
        }

        $output .= '</div>';
        $output .= '<div class="slider_arrows post_slider_arrow">';
        $output .= '<button class="slider_arrow arrow_prev" aria-label="' . esc_attr__( 'Previous', 'folioedgecore' ) . '"><i class="fal fa-arrow-left"></i></button>';
        $output .= '<button class="slider_arrow arrow_next" aria-label="' . esc_attr__( 'Next', 'folioedgecore' ) . '"><i class="fal fa-arrow-right"></i></button>';
        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }
}

/**
 * Video Embed Content Output
 *
 * @param string $video_url Video URL
 * @return string|false
 */
if ( ! function_exists( 'folioedge_video_embed_content' ) ) {
    function folioedge_video_embed_content( $video_url ) {
        if ( empty( $video_url ) ) {
            return false;
        }

        $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
        $get_embed     = wp_oembed_get( esc_url( $video_url ) );

        if ( ! $get_embed ) {
            return false;
        }

        // Add autoplay parameter and lazy loading
        $get_embed = str_replace( '?', '?autoplay=1&', $get_embed );
        $get_embed = str_replace( 'src', 'data-src', $get_embed );

        ob_start();
        ?>
        <div class="post-media video-post">
            <div class="videoPoster" style="background-image: url('<?php echo esc_url( $thumbnail_url ); ?>');">
                <button type="button" class="video-play-bttn" aria-label="<?php esc_attr_e( 'Play video', 'folioedgecore' ); ?>">
                    <i class="fa fa-play"></i>
                </button>
                <div class="waves-block">
                    <div class="waves wave-1"></div>
                    <div class="waves wave-2"></div>
                    <div class="waves wave-3"></div>
                    <div class="waves wave-4"></div>
                </div>
            </div>
            <?php echo $get_embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
        <?php
        return ob_get_clean();
    }
}

/**
 * Audio Embed Content Output
 *
 * @param string $audio_url Audio URL
 * @return string|false
 */
if ( ! function_exists( 'folioedge_audio_embed_content' ) ) {
    function folioedge_audio_embed_content( $audio_url ) {
        if ( empty( $audio_url ) ) {
            return false;
        }

        $embed = wp_oembed_get( esc_url( $audio_url ) );

        if ( ! $embed ) {
            return false;
        }

        return '<div class="post-media audio-post">' . $embed . '</div>';
    }
}
