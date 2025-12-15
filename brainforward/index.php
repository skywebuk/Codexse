<?php get_header(); 
$sidebar_display = get_theme_mod('blog_sidebar_display', 'show');
$sidebar_active = $sidebar_display != 'hide' && is_active_sidebar('main_sidebar');
?>
<!-- Post_List_Area-Start -->
<?php if (have_posts()) { ?>
    <?php get_template_part('templates/jumbotron'); ?>
    <section class="posts_list-area section-padding page-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-sm-12 <?php echo esc_attr($sidebar_active ? 'col-lg-8' : ''); ?>">
                    <div class="<?php echo esc_attr($sidebar_active ? 'pe-lg-4' : ''); ?>">
                        <div class="posts_list">
                            <?php 
                                // Start the loop.
                                while (have_posts()) {
                                    the_post();                                    
                                    // Include the Post-Format-specific template for the content.
                                    get_template_part('templates/post-formats/post', get_post_format());
                                }								                      
                            ?>
                        </div>
                        <?php
                            // Previous/next page navigation.
                            the_posts_pagination(array(
                                'prev_text' => '<i class="ri-arrow-left-line"></i>',
                                'next_text' => '<i class="ri-arrow-right-line"></i>',
                                'screen_reader_text' => ' '
                            ));
                        ?>
                    </div>
                </div>
                <?php if ($sidebar_active) { ?>
                    <div class="col-sm-12 col-lg-4 mt-5 mt-lg-0">
                        <?php get_sidebar(); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
<?php 
    } else {
        // If no content, include the "No posts found" template.
        get_template_part('templates/post-formats/post', 'none');
    }  
?>
<!-- Post_List_Area-End -->
<?php get_footer(); ?>
