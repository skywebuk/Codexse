<?php
// Creating the widget 
class folioedge_social_menu extends WP_Widget {    
    function __construct() {        
        parent::__construct( 
            // Base ID of your widget
            'folioedge_social_menu',
            // Widget name will appear in UI
            __('Social Profile', 'folioedgecore'), 
            // Widget description
            array( 'description' => __( 'To set your social profile link.', 'folioedgecore' ), ) 
        );
    }
    
    // Creating widget front-end 
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        $social_facebook = ! empty( $instance['social_facebook'] ) ? $instance['social_facebook'] : '';
        $social_twitter = ! empty( $instance['social_twitter'] ) ? $instance['social_twitter'] : '';
        $social_linkedin = ! empty( $instance['social_linkedin'] ) ? $instance['social_linkedin'] : '';
        $social_instagram = ! empty( $instance['social_instagram'] ) ? $instance['social_instagram'] : '';
        $social_youtube = ! empty( $instance['social_youtube'] ) ? $instance['social_youtube'] : '';
        $social_pinterest = ! empty( $instance['social_pinterest'] ) ? $instance['social_pinterest'] : '';
        $social_flikr = ! empty( $instance['social_flikr'] ) ? $instance['social_flikr'] : '';        
        $social_whatsapp = ! empty( $instance['social_whatsapp'] ) ? $instance['social_whatsapp'] : '';        
        $data = array();
        $data[] = $args['before_widget'];    
        
        $data[] = ( !empty($title) ? $args['before_title'] . $title  . $args['after_title'] : '' );        
        
        $data[] = '<div class="social_menu">';
        $data[] = ( !empty($social_facebook) ? '<a href="'.esc_url($social_facebook).'"><i class="fab fa-facebook-f"></i></a>' : '' );        
        $data[] = ( !empty($social_twitter) ? '<a href="'.esc_url($social_twitter).'"><i class="fab fa-twitter"></i></a>' : '' );
        $data[] = ( !empty($social_linkedin) ? '<a href="'.esc_url($social_linkedin).'"><i class="fab fa-linkedin-in"></i></a>' : '' );
        $data[] = ( !empty($social_instagram) ? '<a href="'.esc_url($social_instagram).'"><i class="fab fa-instagram"></i></a>' : '' );
        $data[] = ( !empty($social_youtube) ? '<a href="'.esc_url($social_youtube).'"><i class="fab fa-youtube"></i></a>' : '' );
        $data[] = ( !empty($social_pinterest) ? '<a href="'.esc_url($social_pinterest).'"><i class="fab fa-pinterest-p"></i></a>' : '' );
        $data[] = ( !empty($social_flikr) ? '<a href="'.esc_url($social_flikr).'"><i class="fab fa-flickr"></i></a>' : '' );        
        $data[] = ( !empty($social_whatsapp) ? '<a href="'.esc_url($social_whatsapp).'"><i class="fab fa-whatsapp"></i></a>' : '' );        
        $data[] = '</div>';
        
        
        $data[] = $args['after_widget'];        
        $data = implode( '', $data );       
        echo $data;
    }
    
    // Widget Backend 
    public function form( $instance ) {
        $title = (isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '' );        
        $social_facebook = ! empty( $instance['social_facebook'] ) ? $instance['social_facebook'] : '';
        $social_twitter = ! empty( $instance['social_twitter'] ) ? $instance['social_twitter'] : '';
        $social_linkedin = ! empty( $instance['social_linkedin'] ) ? $instance['social_linkedin'] : '';
        $social_instagram = ! empty( $instance['social_instagram'] ) ? $instance['social_instagram'] : '';
        $social_youtube = ! empty( $instance['social_youtube'] ) ? $instance['social_youtube'] : '';
        $social_pinterest = ! empty( $instance['social_pinterest'] ) ? $instance['social_pinterest'] : '';
        $social_flikr = ! empty( $instance['social_flikr'] ) ? $instance['social_flikr'] : '';
        $social_whatsapp = ! empty( $instance['social_whatsapp'] ) ? $instance['social_whatsapp'] : '';
        // Widget admin form
        ?><div class="media-widget-control">
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>">
                    <?php esc_html_e( 'Title:','folioedgecore' ); ?></label>
                <input class="widefat title" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>  
            <p>
                <label for="<?php echo $this->get_field_id('social_facebook'); ?>"><?php esc_html_e( 'Facebook URL','folioedgecore' ); ?></label>
                <input type="text" class="widefat" name="<?php echo $this->get_field_name('social_facebook'); ?>" id="<?php echo $this->get_field_id('social_facebook'); ?>" value="<?php echo esc_attr($social_facebook); ?>" >
            </p> 
            <p>
                <label for="<?php echo $this->get_field_id('social_twitter'); ?>"><?php esc_html_e( 'Twitter URL','folioedgecore' ); ?></label>
                <input type="text" class="widefat" name="<?php echo $this->get_field_name('social_twitter'); ?>" id="<?php echo $this->get_field_id('social_twitter'); ?>" value="<?php echo esc_attr($social_twitter); ?>" >
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('social_linkedin'); ?>"><?php esc_html_e( 'Linkedin URL','folioedgecore' ); ?></label>
                <input type="text" class="widefat" name="<?php echo $this->get_field_name('social_linkedin'); ?>" id="<?php echo $this->get_field_id('social_linkedin'); ?>" value="<?php echo esc_attr($social_linkedin); ?>" >
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('social_instagram'); ?>"><?php esc_html_e( 'Instagram URL','folioedgecore' ); ?></label>
                <input type="text" class="widefat" name="<?php echo $this->get_field_name('social_instagram'); ?>" id="<?php echo $this->get_field_id('social_instagram'); ?>" value="<?php echo esc_attr($social_instagram); ?>" >
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('social_youtube'); ?>"><?php esc_html_e( 'Youtube URL','folioedgecore' ); ?></label>
                <input type="text" class="widefat" name="<?php echo $this->get_field_name('social_youtube'); ?>" id="<?php echo $this->get_field_id('social_youtube'); ?>" value="<?php echo esc_attr($social_youtube); ?>" >
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('social_pinterest'); ?>"><?php esc_html_e( 'Pinterest URL','folioedgecore' ); ?></label>
                <input type="text" class="widefat" name="<?php echo $this->get_field_name('social_pinterest'); ?>" id="<?php echo $this->get_field_id('social_pinterest'); ?>" value="<?php echo esc_attr($social_pinterest); ?>" >
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('social_flikr'); ?>"><?php esc_html_e( 'Flikr URL','folioedgecore' ); ?></label>
                <input type="text" class="widefat" name="<?php echo $this->get_field_name('social_flikr'); ?>" id="<?php echo $this->get_field_id('social_flikr'); ?>" value="<?php echo esc_attr($social_flikr); ?>" >
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('social_whatsapp'); ?>"><?php esc_html_e( 'WhatsApp URL','folioedgecore' ); ?></label>
                <input type="text" class="widefat" name="<?php echo $this->get_field_name('social_whatsapp'); ?>" id="<?php echo $this->get_field_id('social_whatsapp'); ?>" value="<?php echo esc_attr($social_whatsapp); ?>" >
            </p>
        </div><?php 
    } 
        
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {        
        $instance = array();        
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['social_facebook'] = ! empty( $new_instance['social_facebook'] ) ? $new_instance['social_facebook'] : '';
        $instance['social_twitter'] = ! empty( $new_instance['social_twitter'] ) ? $new_instance['social_twitter'] : '';
        $instance['social_linkedin'] = ! empty( $new_instance['social_linkedin'] ) ? $new_instance['social_linkedin'] : '';
        $instance['social_instagram'] = ! empty( $new_instance['social_instagram'] ) ? $new_instance['social_instagram'] : '';
        $instance['social_youtube'] = ! empty( $new_instance['social_youtube'] ) ? $new_instance['social_youtube'] : '';
        $instance['social_pinterest'] = ! empty( $new_instance['social_pinterest'] ) ? $new_instance['social_pinterest'] : '';
        $instance['social_flikr'] = ! empty( $new_instance['social_flikr'] ) ? $new_instance['social_flikr'] : '';        
        $instance['social_whatsapp'] = ! empty( $new_instance['social_whatsapp'] ) ? $new_instance['social_whatsapp'] : '';        
        return $instance;
    }
}