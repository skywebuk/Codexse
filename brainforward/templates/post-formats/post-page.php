<div <?php post_class('post-content post-content--' . get_post_type()); ?>>

    <div class="post-content__body">
        <?php the_content(); ?>
    </div>

    <?php
    wp_link_pages( array(
        'before'           => '<nav class="post-content__pagination pagination"><span class="pagination__title">' . esc_html__( 'Pages:', 'brainforward' ) . '</span>',
        'after'            => '</nav>',
        'link_before'      => '<span class="pagination__number">',
        'link_after'       => '</span>',
        'next_or_number'   => 'number',
        'nextpagelink'     => '<span class="pagination__next"><i class="ri-arrow-right-line"></i></span>',
        'previouspagelink' => '<span class="pagination__prev"><i class="ri-arrow-left-line"></i></span>',
    ) );
    ?>

</div>
