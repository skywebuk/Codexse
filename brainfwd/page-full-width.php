<?php
/**
Template Name: Full Width with Header
**/
    get_header();
?>
<!-- Post_List_Area-Start -->
<?php if( have_posts() ){
    get_template_part('templates/jumbotron');
?>
<?php
    // Start the loop.
    while(have_posts()){
        the_post();                                    
        /*
            * Include the Post-Format-specific template for the content.
            * If you want to override this in a child theme, then include a file
            * called content-___.php (where ___ is the Post Format name) and that will be used instead.
            */
        get_template_part( 'templates/post-formats/post', 'page' );
            // End the loop.
        /* If comments are open or we have at least one comment, load up the comment template.*/
        if ( get_post_type() && comments_open() || get_comments_number() and $elementor_ready !== 'yes' ) :
            comments_template();
        endif;
    }
?>
<?php
    }else{
        // If no content, include the "No posts found" template.
        get_template_part( 'templates/post-formats/post', 'none' );
    }
?>
<!-- Post_List_Area-End -->
<?php get_footer();