<?php

    add_action( 'cmb2_admin_init', 'folioedge_register_post_metabox' );    
    function folioedge_register_post_metabox() {        
        $prefix = '_folioedge_';
        
        /*-- User-Meta-Box-Fields --*/
        $folioedge_wc_meta = new_cmb2_box(array(
            'id'            => $prefix . 'wc_product_options',
            'title'         => esc_html__('Add more meta', 'folioedgecore' ),
            'object_types'  => array( 'product' ),
            'context'       => 'side',
		    'priority'      => 'low',
        ));
                
        $wc_meta_option = $folioedge_wc_meta->add_field( array(
            'id'          => $prefix . 'wc_meta_repeat_group',
            'type'        => 'group',
            // 'repeatable'  => false, // use false if you want non-repeatable group
            'options'     => array(
                'group_title'       => __( 'Extra Meta {#}', 'folioedgecore' ), // since version 1.1.4, {#} gets replaced by row number
                'add_button'        => __( 'Add another meta', 'folioedgecore' ),
                'remove_button'     => __( 'Remove meta', 'folioedgecore' ),
                'sortable'          => true,
                // 'closed'         => true, // true to have the groups closed by default
                // 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'folioedgecore' ), // Performs confirmation before removing group.
            ),
        ) );

        // Id's for group's fields only need to be unique for the group. Prefix is not needed.
        $folioedge_wc_meta->add_group_field( $wc_meta_option, array(
            'name' => 'Title',
            'id'   => $prefix . 'wc_meta_title',
            'type' => 'text',
            // 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
        ) );

        // Id's for group's fields only need to be unique for the group. Prefix is not needed.
        $folioedge_wc_meta->add_group_field( $wc_meta_option, array(
            'name' => 'Value',
            'id'   => $prefix . 'wc_meta_value',
            'type' => 'textarea',
            // 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
        ) );
                
        $folioedge_wc_meta->add_field( array(
            'name' => esc_html__( 'Extra Button Label', 'folioedgecore' ),
            'id'   => $prefix . 'wc_ex_button_label',
            'type' => 'text_medium',
            //'default' => esc_html__( 'Live Preview', 'folioedgecore' ),
        ) );
        
        $folioedge_wc_meta->add_field( array(
            'name' => esc_html__( 'Extra Button URL', 'folioedgecore' ),
            'id'   => $prefix . 'wc_ex_button_url',
            'type' => 'text_url',
            //'default' => '#',
        ) );

        
        /*-- User-Meta-Box-Fields --*/
        $folioedge_user_meta = new_cmb2_box(array(
            'id'            => $prefix . 'user_options',
            'title'         => esc_html__('Social Profile', 'folioedgecore' ),
            'object_types'  => array( 'user' ),
            'priority'      => 'high',
        ));
        
        $folioedge_user_meta->add_field( array(
            'name' => esc_html__('Facebook URL', 'folioedgecore' ),
            'id'   => $prefix . 'user_facebook',
            'type' => 'text_url',
        ) );
        
        $folioedge_user_meta->add_field( array(
            'name' => esc_html__('Twitter URL', 'folioedgecore' ),
            'id'   => $prefix . 'user_twitter',
            'type' => 'text_url',
        ) );
        
        $folioedge_user_meta->add_field( array(
            'name' => esc_html__('Linkedin URL', 'folioedgecore' ),
            'id'   => $prefix . 'user_linkedin',
            'type' => 'text_url',
        ) );
        
        $folioedge_user_meta->add_field( array(
            'name' => esc_html__('Instagram URL', 'folioedgecore' ),
            'id'   => $prefix . 'user_instagram',
            'type' => 'text_url',
        ) );
        
        $folioedge_user_meta->add_field( array(
            'name' => esc_html__('Pinterest URL', 'folioedgecore' ),
            'id'   => $prefix . 'user_pinterest',
            'type' => 'text_url',
        ) );
        
        
        /*-- Page-Meta-Box-Fields --*/
        $folioedge_page_meta = new_cmb2_box(array(
            'id'            => $prefix . 'page_options',
            'title'         => esc_html__('Page Options', 'folioedgecore' ),
            'object_types'  => array( 'page', 'service', 'project', 'team' )
        ));  
                
        $folioedge_page_meta->add_field(array(
            'name'    => esc_html__('Onepage Template:', 'folioedgecore' ),
            'id'      => $prefix . 'one_page_template',
            'type'    => 'checkbox',
            'desc' => esc_html__('Will this page use as a onepage template?', 'folioedgecore' )
        ));        
        $folioedge_page_meta->add_field(array(
            'name'    => esc_html__('Onepage Scroll', 'folioedgecore' ),
            'id'      => $prefix . 'one_page_scroll',
            'type'    => 'checkbox',
            'desc' => esc_html__('To get a id selected scroll?', 'folioedgecore' )
        ));        
        $folioedge_page_meta->add_field(array(
            'name'    => esc_html__('Remove Container', 'folioedgecore' ),
            'id'      => $prefix . 'remove_page_container',
            'type'    => 'checkbox',
            'desc' => esc_html__('Remove the default page container to use elementor container.', 'folioedgecore' )
        ));        
        $folioedge_page_meta->add_field(array(
            'name'    => esc_html__('Remove Page Header:', 'folioedgecore' ),
            'id'      => $prefix . 'page_header',
            'type'    => 'checkbox',
            'desc' => esc_html__('Check this field if you want remove page header on this page.', 'folioedgecore' )
        ));
       $folioedge_page_meta->add_field(array(
            'name'    => esc_html__('Remove Footer Area:', 'folioedgecore' ),
            'id'      => $prefix . 'footer_widget',
            'type'    => 'checkbox',
            'desc' => esc_html__('Check this field if you want remove footer widgets on this page.', 'folioedgecore' )
        ));
        $folioedge_page_meta->add_field( array(
            'name'    => esc_html__('Page Background Image', 'folioedgecore' ),
            'desc'    => esc_html__('Upload an image or enter an URL.', 'folioedgecore' ),
            'id'      => $prefix . 'page_background',
            'type'    => 'file',
            'text'    => array(
                'add_upload_file_text' => 'Add Background'
            ),
            'query_args' => array(
                 'type' => array(
                     'image/gif',
                     'image/jpeg',
                     'image/png',
                 ),
            ),
            'preview_size' => 'medium',
        ) );
    
                
        /*-- Post-Meta-Box-Content --*/
        $folioedge_post_meta = new_cmb2_box( array(
            'id'           => $prefix.'post_metabox',
            'title'        => esc_html__('Additional Fields', 'folioedgecore' ),
            'object_types' => array( 'post' ), // post type
        ) );
        
        $folioedge_post_meta->add_field( array(
                'name'       => esc_html__( 'Photo Gallery',  'folioedgecore'  ),
                'desc'       => esc_html__( 'This field for gallery images. This gallery show for select gallery format.',  'folioedgecore'  ),
                'id'         => $prefix . 'post_gallery',
                'type'       => 'file_list',
                'text' => array(
                    'add_upload_files_text' => esc_html__('Add images', 'folioedgecore' ), // default: "Add or Upload Files"
                ),
            )
        );
        
        $folioedge_post_meta->add_field( array(
            'name' => esc_html__('Embed Video', 'folioedgecore' ),
            'desc' => esc_html__('Enter a youtube, twitter, or instagram URL. Supports services listed at ', 'folioedgecore' ).'<a href="http://codex.wordpress.org/Embeds">http://codex.wordpress.org/Embeds</a> '.esc_html__('This video show for select video format', 'folioedgecore' ),
            'id'   => $prefix . 'post_video_embed',
            'type' => 'oembed',
        ) );
        
        $folioedge_post_meta->add_field( array(
            'name' => esc_html__('Embed Audio', 'folioedgecore' ),
            'desc' => esc_html__('Enter a SoundCloud, Mixcloud, or ReverbNation etc URL. Supports services listed at ', 'folioedgecore' ).'<a href="http://codex.wordpress.org/Embeds">http://codex.wordpress.org/Embeds</a> '.esc_html__('This audio show for select audio format', 'folioedgecore' ),
            'id'   => $prefix . 'post_audio_embed',
            'type' => 'oembed',
        ) );
        
        
        /*-- Post-Meta-Box-Content --*/
        $folioedge_team = new_cmb2_box( array(
            'id'           => $prefix.'team_metabox',
            'title'        => esc_html__('Team Details', 'folioedgecore' ),
            'object_types' => array( 'team' ), // post type
        ) );
            
        $folioedge_team->add_field( array(
            'name' => esc_html__('Position', 'folioedgecore' ),
            'id'   => $prefix . 'team_position',
            'type' => 'text_medium',
        ) );
        
        $folioedge_team->add_field( array(
            'name' => esc_html__('Facebook URL', 'folioedgecore' ),
            'id'   => $prefix . 'team_facebook',
            'type' => 'text_url',
        ) );
        
        $folioedge_team->add_field( array(
            'name' => esc_html__('Twitter URL', 'folioedgecore' ),
            'id'   => $prefix . 'team_twitter',
            'type' => 'text_url',
        ) );
        
        $folioedge_team->add_field( array(
            'name' => esc_html__('Linkedin URL', 'folioedgecore' ),
            'id'   => $prefix . 'team_linkedin',
            'type' => 'text_url',
        ) );
        
        $folioedge_team->add_field( array(
            'name' => esc_html__('Instagram URL', 'folioedgecore' ),
            'id'   => $prefix . 'team_instagram',
            'type' => 'text_url',
        ) );
        



    }
    
    if( !function_exists("folioedge_gallery_photo_list") ){
      function folioedge_gallery_photo_list( $gallery_images, $img_size = 'large' ) {
          if( empty($gallery_images) ){
              return false;
          }
            // Get the list of gallery
            $data = '<div class="photo_slider swiper-container post-media">';
            $data .= '<div class="swiper-wrapper">';
            // Loop through them and output an image
            foreach ( (array) $gallery_images[0] as $image_id => $image_url ) {
                $data .= '<div class="swiper-slide" >';
                $data .= '<div class="gallery-item" >';
                $data .= wp_get_attachment_image( $image_id, $img_size );
                $data .= '</div>';
                $data .= '</div>';
            }
            $data .= '</div>';
            $data .= '<div class="slider_arrows post_slider_arrow">';
            $data .= '<button class="slider_arrow arrow_prev"><i class="fal fa-arrow-left"></i></button>';
            $data .= '<button class="slider_arrow arrow_next"><i class="fal fa-arrow-right"></i></button>';
            $data .= '</div>';
            //$data .= '<div class="swiper-pagination"></div>';
            $data .= '</div>';
          return $data;
        }
    }
    
    if( !function_exists('folioedge_video_embed_content') ){
        function folioedge_video_embed_content($video_url){
            if( empty($video_url) ){
                return false;
            }
            ob_start();
            ?>
<div class="post-media video-post">
    <div class="videoPoster" style="background-image: url('<?php echo get_the_post_thumbnail_url('','full'); ?>');">
        <button type="button" class="video-play-bttn"><i class="fa fa-play"></i></button>
        <div class="waves-block">
            <div class="waves wave-1"></div>
            <div class="waves wave-2"></div>
            <div class="waves wave-3"></div>
            <div class="waves wave-4"></div>
        </div>
    </div>
    <?php $get_embed = wp_oembed_get( esc_url($video_url) );
                    $get_embed = str_replace( '?','?autoplay=1&', $get_embed );
                    echo str_replace( 'src','data-src', $get_embed );
                ?>
</div>
<?php 
            $data = ob_get_contents();
            ob_end_clean();
            return $data;
        }
    }
    
    if( !function_exists('folioedge_audio_embed_content') ){
        function folioedge_audio_embed_content($post_audio_embed_url){
            if( empty($post_audio_embed_url) ){
                return false;
            }
            return '<div class="post-media audio-post">'.wp_oembed_get( $post_audio_embed_url ).'</div>';
        }
    }