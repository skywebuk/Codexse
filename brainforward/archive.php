<?php get_header(); ?>

<?php if ( have_posts() ) : ?>

    <?php get_template_part('templates/jumbotron'); ?>

    <section class="post-list section-padding page-section">
        <div class="container">
            <div class="row justify-content-center">

                <!-- Main Content -->
                <div class="col-sm-12 <?php echo is_active_sidebar('main_sidebar') ? 'col-lg-8' : ''; ?>">
                    <div class="<?php echo is_active_sidebar('main_sidebar') ? 'pe-lg-4' : ''; ?>">

                        <?php
                        while ( have_posts() ) :
                            the_post();

                            // Load template based on post format.
                            get_template_part('templates/post-formats/post', get_post_format());

                        endwhile;
                        ?>

                    </div>

                    <!-- Pagination -->
                    <?php
                    the_posts_pagination(array(
                        'prev_text'          => '<i class="ri-arrow-left-line"></i>',
                        'next_text'          => '<i class="ri-arrow-right-line"></i>',
                        'screen_reader_text' => __('Posts navigation', 'brainforward'),
                    ));
                    ?>
                </div>

                <!-- Sidebar -->
                <?php if ( is_active_sidebar('main_sidebar') ) : ?>
                    <div class="col-sm-12 col-lg-4 mt-5 mt-lg-0">
                        <?php get_sidebar(); ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </section>

<?php else : ?>

    <?php get_template_part('templates/post-formats/post', 'none'); ?>

<?php endif; ?>

<?php get_footer(); ?>
