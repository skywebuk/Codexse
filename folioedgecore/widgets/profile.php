<?php
// Creating the widget 
class folioedge_author_info extends WP_Widget {
    
    function __construct() {        
        add_action('admin_enqueue_scripts', array($this, 'folioedge_media_scripts'));
        parent::__construct( 
            // Base ID of your widget
            'folioedge_author_info',
            // Widget name will appear in UI
            __('Author Information', 'folioedgecore'), 
            // Widget description
            array( 'description' => __( 'Author information widget.', 'folioedgecore' ), ) 
        );
    }    
    public function folioedge_media_scripts(){
       wp_enqueue_script( 'media-upload' );
       wp_enqueue_media();
    }
    
    // Creating widget front-end 
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        $author_image = ! empty( $instance['author_image'] ) ? $instance['author_image'] : '';
        $author_name = ! empty( $instance['author_name'] ) ? $instance['author_name'] : '';
        $author_bio = ! empty( $instance['author_bio'] ) ? $instance['author_bio'] : '';
        
        $author_facebook = ! empty( $instance['author_facebook'] ) ? $instance['author_facebook'] : '';
        $author_twitter = ! empty( $instance['author_twitter'] ) ? $instance['author_twitter'] : '';
        $author_linkedin = ! empty( $instance['author_linkedin'] ) ? $instance['author_linkedin'] : '';
        $author_instagram = ! empty( $instance['author_instagram'] ) ? $instance['author_instagram'] : '';
        $author_youtube = ! empty( $instance['author_youtube'] ) ? $instance['author_youtube'] : '';
        $author_behance = ! empty( $instance['author_behance'] ) ? $instance['author_behance'] : '';
        $author_dribbble = ! empty( $instance['author_dribbble'] ) ? $instance['author_dribbble'] : '';        
        $data = array();
        $data[] = $args['before_widget'];        
        $data[] = ( !empty($title) ? $args['before_title'] . $title  . $args['after_title'] : '' );        
        $data[] = '<div class="widget-content">';        
        $data[] = ( !empty($author_image) ? '<figure class="author-pic"><img src="'.esc_url($author_image).'" alt="'.esc_attr__( 'Author Image','folioedgecore' ).'"></figure>' : '' );            
        $data[] = ( !empty($author_name) ? '<h4 class="author-name">'.esc_html($author_name).'</h4>' : '' );        
        $data[] = ( !empty($author_bio) ? wpautop(esc_html($author_bio)) : '' );        
        $data[] = '<div class="author-social">';
        $data[] = ( !empty($author_facebook) ? '<a target="_blank" href="'.esc_url($author_facebook).'"><span class="s-icon"><svg><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-facebook"></use></svg></span></a>' : '' );        
        $data[] = ( !empty($author_twitter) ? '<a target="_blank" href="'.esc_url($author_twitter).'"><span class="s-icon"><svg><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-twitter"></use></svg></span></a>' : '' );
        $data[] = ( !empty($author_linkedin) ? '<a target="_blank" href="'.esc_url($author_linkedin).'"><span class="s-icon"><svg><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-linkedin"></use></svg></span></a>' : '' );
        $data[] = ( !empty($author_instagram) ? '<a target="_blank" href="'.esc_url($author_instagram).'"><span class="s-icon"><svg><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-instagram"></use></svg></span></a>' : '' );
        $data[] = ( !empty($author_youtube) ? '<a target="_blank" href="'.esc_url($author_youtube).'"><span class="s-icon"><svg><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-youtube"></use></svg></span></a>' : '' );
        $data[] = ( !empty($author_behance) ? '<a target="_blank" href="'.esc_url($author_behance).'"><span class="s-icon"><svg><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-behance"></use></svg></span></a>' : '' );
        $data[] = ( !empty($author_dribbble) ? '<a target="_blank" href="'.esc_url($author_dribbble).'"><span class="s-icon"><svg><use xlink:href="'.get_theme_file_uri( 'assets/images/symble.svg' ).'#ic-dribbble"></use></svg></span></a>' : '' );        
        $data[] = '</div>';
        $data[] = '</div>';
        $data[] = $args['after_widget'];        
        $data = implode( '', $data );       
        echo $data;
    }
    
    // Widget Backend 
    public function form( $instance ) {
        $title = (isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '' );
        $author_image = ! empty( $instance['author_image'] ) ? $instance['author_image'] : '';
        $author_name = ! empty( $instance['author_name'] ) ? $instance['author_name'] : '';
        $author_bio = ! empty( $instance['author_bio'] ) ? $instance['author_bio'] : '';
        $author_facebook = ! empty( $instance['author_facebook'] ) ? $instance['author_facebook'] : '';
        $author_twitter = ! empty( $instance['author_twitter'] ) ? $instance['author_twitter'] : '';
        $author_linkedin = ! empty( $instance['author_linkedin'] ) ? $instance['author_linkedin'] : '';
        $author_instagram = ! empty( $instance['author_instagram'] ) ? $instance['author_instagram'] : '';
        $author_youtube = ! empty( $instance['author_youtube'] ) ? $instance['author_youtube'] : '';
        $author_behance = ! empty( $instance['author_behance'] ) ? $instance['author_behance'] : '';
        $author_dribbble = ! empty( $instance['author_dribbble'] ) ? $instance['author_dribbble'] : '';
        // Widget admin form
        ?><div class="media-widget-control">
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>">
                    <?php esc_html_e( 'Widget Title:','folioedgecore' ); ?></label>
                <input class="widefat title" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('author_name'); ?>"><?php esc_html_e( 'Author Name','folioedgecore' ); ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id('author_name'); ?>" name="<?php echo $this->get_field_name( 'author_name' ); ?>" value="<?php echo esc_attr( $author_name ); ?>" >
            </p>
            <p class="image_upload_part">
                <label for="<?php echo $this->get_field_id( 'author_image' ); ?>"><?php esc_html_e( 'Upload Author Image:' ); ?></label>
                <input class="author_image_url widefat" id="<?php echo $this->get_field_id( 'author_image' ); ?>" name="<?php echo $this->get_field_name( 'author_image' ); ?>" type="text" value="<?php echo esc_url( $author_image ); ?>" />
                <?php if( empty($author_image) ): ?>
                    <span class="media-widget-preview">
                        <span class="placeholder upload_image_button"><?php esc_html_e( 'No image selected','folioedgecore' ); ?></span>
                    </span>
                <?php else: ?>
                    <span class="image_preview">
                        <img src="<?php echo esc_url( $author_image ); ?>" class="upload_image_button dm_image" alt="">
                    </span>
                <?php endif; ?>

                <span class="media-widget-buttons">
                    <button class="upload_image_button button folioedge_image_upload"><?php esc_html_e( 'Add Image','folioedgecore' ); ?></button>
                </span>
            </p>    
            <p>
                <label for="<?php echo $this->get_field_id('author_bio'); ?>"><?php esc_html_e( 'Author Bio','folioedgecore' ); ?></label>
                <textarea name="<?php echo $this->get_field_name('author_bio'); ?>" id="<?php echo $this->get_field_id('author_bio'); ?>" cols="10" rows="5" class="widefat" ><?php echo esc_html($author_bio); ?></textarea>
            </p>
            <hr>            
            <p>
                <label for="<?php echo $this->get_field_id('author_facebook'); ?>"><?php esc_html_e( 'Facebook URL','folioedgecore' ); ?></label>
                <input type="text" class="widefat" name="<?php echo $this->get_field_name('author_facebook'); ?>" id="<?php echo $this->get_field_id('author_facebook'); ?>" value="<?php echo esc_attr($author_facebook); ?>" >
            </p> 
            <p>
                <label for="<?php echo $this->get_field_id('author_twitter'); ?>"><?php esc_html_e( 'Twitter URL','folioedgecore' ); ?></label>
                <input type="text" class="widefat" name="<?php echo $this->get_field_name('author_twitter'); ?>" id="<?php echo $this->get_field_id('author_twitter'); ?>" value="<?php echo esc_attr($author_twitter); ?>" >
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('author_linkedin'); ?>"><?php esc_html_e( 'Linkedin URL','folioedgecore' ); ?></label>
                <input type="text" class="widefat" name="<?php echo $this->get_field_name('author_linkedin'); ?>" id="<?php echo $this->get_field_id('author_linkedin'); ?>" value="<?php echo esc_attr($author_linkedin); ?>" >
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('author_instagram'); ?>"><?php esc_html_e( 'Instagram URL','folioedgecore' ); ?></label>
                <input type="text" class="widefat" name="<?php echo $this->get_field_name('author_instagram'); ?>" id="<?php echo $this->get_field_id('author_instagram'); ?>" value="<?php echo esc_attr($author_instagram); ?>" >
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('author_youtube'); ?>"><?php esc_html_e( 'Youtube URL','folioedgecore' ); ?></label>
                <input type="text" class="widefat" name="<?php echo $this->get_field_name('author_youtube'); ?>" id="<?php echo $this->get_field_id('author_youtube'); ?>" value="<?php echo esc_attr($author_youtube); ?>" >
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('author_behance'); ?>"><?php esc_html_e( 'Behance URL','folioedgecore' ); ?></label>
                <input type="text" class="widefat" name="<?php echo $this->get_field_name('author_behance'); ?>" id="<?php echo $this->get_field_id('author_behance'); ?>" value="<?php echo esc_attr($author_behance); ?>" >
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('author_dribbble'); ?>"><?php esc_html_e( 'Dribbble URL','folioedgecore' ); ?></label>
                <input type="text" class="widefat" name="<?php echo $this->get_field_name('author_dribbble'); ?>" id="<?php echo $this->get_field_id('author_dribbble'); ?>" value="<?php echo esc_attr($author_dribbble); ?>" >
            </p>
        </div><?php 
    } 
        
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['author_image'] = ( ! empty( $new_instance['author_image'] ) ) ? $new_instance['author_image'] : '';
        $instance['author_name'] = ( ! empty( $new_instance['author_name'] ) ) ? $new_instance['author_name'] : '';
        $instance['author_bio'] = ( ! empty( $new_instance['author_bio'] ) ) ? $new_instance['author_bio'] : '';
        $instance['author_facebook'] = ! empty( $new_instance['author_facebook'] ) ? $new_instance['author_facebook'] : '';
        $instance['author_twitter'] = ! empty( $new_instance['author_twitter'] ) ? $new_instance['author_twitter'] : '';
        $instance['author_linkedin'] = ! empty( $new_instance['author_linkedin'] ) ? $new_instance['author_linkedin'] : '';
        $instance['author_instagram'] = ! empty( $new_instance['author_instagram'] ) ? $new_instance['author_instagram'] : '';
        $instance['author_youtube'] = ! empty( $new_instance['author_youtube'] ) ? $new_instance['author_youtube'] : '';
        $instance['author_behance'] = ! empty( $new_instance['author_behance'] ) ? $new_instance['author_behance'] : '';
        $instance['author_dribbble'] = ! empty( $new_instance['author_dribbble'] ) ? $new_instance['author_dribbble'] : '';        
        return $instance;
    }    
}