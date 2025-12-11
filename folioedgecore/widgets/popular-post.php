<?php
class folioedge_popular_posts extends WP_Widget {
    public function __construct() {
        parent::__construct(
            // Base ID of your widget
            'folioedge_popular_posts', 
            // Widget name will appear in UI
            esc_html__('folioedge Popular Posts', 'folioedgecore'),
            // Widget description
            array( 'description' => esc_html__( 'folioedge Popular posts widget.', 'folioedgecore' ), ) 
        );
    }    
    public function widget( $args, $instance ){        
        $number = isset($instance['number']) ? $instance['number'] : 5 ;
        $title_length = isset($instance['title_length']) ? $instance['title_length'] : 5 ;
        $show_meta = isset($instance['show_meta']) ? $instance['show_meta'] : '' ;
        $show_thumb = isset($instance['show_thumb']) ? $instance['show_thumb'] : '' ;
        
        echo $args['before_widget']; 
        if( !empty($instance['title']) ){
            echo $args['before_title'] . $instance['title'] . $args['after_title'];
        }
        ?>
<div class="popular-posts">
    <?php            
            $post_q = new WP_Query(array(
                'post_type' => 'post',
                'posts_per_page' => $number,
                'meta_key' => 'post_views_count',
                'orderby' => 'meta_value_num',
                'order' => 'DESC' 
            ));
            
            if( $post_q->have_posts() ):
               while( $post_q->have_posts() ):
               $post_q->the_post();        
                $cats = get_the_category();
            ?>
    <div <?php post_class( 'post-item'); ?>>
        <?php if(has_post_thumbnail() and $show_thumb == 'on'): ?>
        <a href="<?php the_permalink(); ?>" class="post-pic">
            <?php the_post_thumbnail('thumbnail'); ?>
        </a>
        <?php endif; ?>
        <div class="post-content">
            <?php if(get_the_title()): ?>
            <h4 class="title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php echo wp_trim_words( get_the_title(), $title_length, '...' ); ?></a></h4>
            <?php endif; ?>
            <?php if( $show_meta == 'on' ): ?>
            <ul class="post-meta-list">
                <!-- Post-Meta-Item -->
                <li class="post-meta-item post-comment-count">
                    <span class="icon"><i title="Post publish date" class="fal fa-clock"></i></span>
                    <span class="value"><?php echo get_the_date(); ?></span>
                </li>
            </ul>
            <?php endif; ?>
        </div>
    </div>
    <?php
                endwhile;
            endif;
            ?>
</div>
<?php echo $args['after_widget']; ?>
<?php
    }    
    public function form( $instance ){
        $title      = (isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '' );
        $number     = (isset( $instance[ 'number' ] ) ? $instance[ 'number' ] : 5 );
        $title_length     = (isset( $instance[ 'title_length' ] ) ? $instance[ 'title_length' ] : 5 );
        $show_meta  = (isset( $instance[ 'show_meta' ] ) ? $instance[ 'show_meta' ] : 'on' );        
        $show_thumb = (isset( $instance[ 'show_thumb' ] ) ? $instance[ 'show_thumb' ] : 'on' );        
        ?>
<p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>">
        <?php esc_html_e( 'Title:','folioedgecore' ); ?>
    </label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'title_length' ); ?>">
        <?php esc_html_e('Post Title Length:','folioedgecore'); ?>
    </label>
    <input class="tiny-text" id="<?php echo $this->get_field_id( 'title_length' ); ?>" name="<?php echo $this->get_field_name( 'title_length' ); ?>" type="number" step="1" value="<?php echo esc_attr( $title_length ); ?>" size="3">
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'number' ); ?>">
        <?php esc_html_e('Number of posts to show:','folioedgecore'); ?>
    </label>
    <input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" value="<?php echo esc_attr( $number ); ?>" size="3">
</p>
<p>
    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id( 'show_meta' ); ?>" name="<?php echo $this->get_field_name( 'show_meta' ); ?>" <?php echo ( $show_meta=='on' ? 'checked' : '' ); ?>>
    <label for="<?php echo $this->get_field_id( 'show_meta' ); ?>">
        <?php esc_html_e('Display Post Meta?','folioedgecore'); ?>
    </label>
</p>
<p>
    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id( 'show_thumb' ); ?>" name="<?php echo $this->get_field_name( 'show_thumb' ); ?>" <?php echo ( $show_thumb=='on' ? 'checked' : '' ); ?>>
    <label for="<?php echo $this->get_field_id( 'show_thumb' ); ?>">
        <?php esc_html_e('Display Post Thumbnail?','folioedgecore'); ?>
    </label>
</p>
<?php
    }    
    
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? esc_html( $new_instance['title'] ) : '';
        $instance['number'] = ( ! empty( $new_instance['number'] ) ) ? esc_html( $new_instance['number'] ) : '';
        $instance['title_length'] = ( ! empty( $new_instance['title_length'] ) ) ? esc_html( $new_instance['title_length'] ) : '';
        $instance['show_meta'] = ( ! empty( $new_instance['show_meta'] ) ) ? esc_html( $new_instance['show_meta'] ) : '';
        $instance['show_thumb'] = ( ! empty( $new_instance['show_thumb'] ) ) ? esc_html( $new_instance['show_thumb'] ) : '';
        return $instance;
    }
    
    
}