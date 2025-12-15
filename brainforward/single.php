<?php
// Theme settings
$author          = get_theme_mod('blog_details_show_author', 'show');
$date            = get_theme_mod('blog_details_show_date', 'show');
$comment         = get_theme_mod('blog_details_show_comment_count', 'show');
$tags            = get_theme_mod('blog_details_show_tags', 'show');
$social          = get_theme_mod('blog_show_social_share', 'show');
$category        = get_theme_mod('blog_details_show_category', 'show');
$counter         = get_theme_mod('blog_details_show_view_counter', 'show');
$navigation      = get_theme_mod('blog_details_show_post_navigation', 'show');
$sidebar_display = get_theme_mod('blog_details_sidebar_display', 'show');
$sidebar_active  = $sidebar_display !== 'hide' && is_active_sidebar('main_sidebar');

// Header and post
get_header();
the_post();
$header_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
if (empty($header_image)) {
    $header_image = get_theme_mod('header_bg_image');
}
$post_format = get_post_format();
$format_label = $post_format ? get_post_format_string($post_format) : __('Standard', 'brainforward');
?>
<div class="single_post-thumbnail" style="background-image: url('<?php echo esc_url($header_image); ?>');"></div>
<div class="container">
    <div class="single_post-thumbnail__overlay">
        <div class="single_post-thumbnail__meta">
            <span class="post-type"><?php echo esc_html($format_label); ?></span>
            <h1 class="post-title"><?php the_title(); ?></h1>
            <ul class="post-card__meta meta-list">
                <?php if ($author !== 'hide'): ?>
                    <li class="post-meta__author"><i class="ri-user-line icon"></i><?php echo esc_html(get_the_author()); ?></li>
                <?php endif; ?>
                <?php if ($date !== 'hide'): ?>
                    <li class="post-meta__date"><i class="ri-calendar-line icon"></i><?php echo wp_kses_post(Brainforward_Functions::get_post_date()); ?></li>
                <?php endif; ?>
                <?php if ($comment !== 'hide' && !empty(Brainforward_Functions::get_comment_count())): ?>
                    <li class="post-meta__comments"><i class="ri-message-2-line icon"></i><?php echo wp_kses_post(Brainforward_Functions::get_comment_count()); ?></li>
                <?php endif; ?>
                <?php if ($category !== 'hide'): ?>
                    <li class="post-meta__category"><i class="ri-folders-line icon"></i><?php echo wp_kses_post(get_the_category_list(', ')); ?></li>
                <?php endif; ?>

                <?php 
                // Add post views with eye icon
                    if ( class_exists('Codexse_Toolkit_Functions') ) : 
                        $views = Codexse_Toolkit_Functions::get_post_views(get_the_ID());
                    ?>
                    <li class="post-meta__list__views">
                        <i class="ri-eye-line icon"></i>
                        <?php echo esc_html( $views ); ?>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<section class="post-details section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12 <?php echo esc_attr($sidebar_active ? 'col-lg-8' : ''); ?>">
                <div class="<?php echo esc_attr($sidebar_active ? 'pe-lg-4' : ''); ?>">
                    <div class="post-details__wrapper">
                        <article <?php post_class(['post-single', get_post_type()]); ?>>
                            <div class="post-body">
                                <div class="post-content">
                                    <?php
                                    the_content(
                                        sprintf(
                                            esc_html__('Continue reading %s', 'brainforward'),
                                            '<span class="screen-reader-text">' . get_the_title() . '</span>'
                                        )
                                    );

                                    wp_link_pages([
                                        'before'           => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'brainforward') . '</span>',
                                        'after'            => '</div>',
                                        'link_before'      => '<span class="page-numbers">',
                                        'link_after'       => '</span>',
                                        'next_or_number'   => 'number',
                                        'nextpagelink'     => '<i class="ri-arrow-right-line"></i>',
                                        'previouspagelink' => '<i class="ri-arrow-left-line"></i>',
                                    ]);
                                    ?>
                                </div>
                            </div>
                        </article>
                        <?php if ( ( has_tag() && $tags !== 'hide' ) || ( $social !== 'hide' && class_exists( 'Codexse_Toolkit_Functions' ) ) ) : ?>
                            <footer class="post__footer">
                                <?php if ( has_tag() && $tags !== 'hide' ) : ?>
                                    <div class="post__footer-tags tagcloud">
                                        <?php
                                        // Output tags as a list of links separated by spaces
                                        echo wp_kses_post( get_the_tag_list( ' ', '', '' ) );
                                        ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $social !== 'hide' && class_exists( 'Codexse_Toolkit_Functions' ) ) : ?>
                                    <div class="post__footer-social">
                                        <?php Codexse_Toolkit_Functions::share_buttons(); ?>
                                    </div>
                                <?php endif; ?>
                            </footer>
                        <?php endif; ?>
                        <?php if ($navigation !== 'hide'):
                            $prev_post = get_previous_post();
                            $next_post = get_next_post();

                            if ($prev_post || $next_post): ?>
                                <nav class="post-navigation d-flex justify-content-between mt-5" aria-label="<?php esc_attr_e('Post navigation', 'brainforward'); ?>">
                                    <div class="post-navigation__prev">
                                        <?php
                                        if ($prev_post) {
                                            previous_post_link(
                                                '%link',
                                                '<div class="button-content"><span class="label"><i class="ri-arrow-left-line me-2"></i>' . esc_html__('Prev Post', 'brainforward') . '</span><h4 class="button-title">%title</h4></div>'
                                            );
                                        }
                                        ?>
                                    </div>
                                    <div class="post-navigation__next">
                                        <?php
                                        if ($next_post) {
                                            next_post_link(
                                                '%link',
                                                '<div class="button-content"><span class="label">' . esc_html__('Next Post', 'brainforward') . ' <i class="ri-arrow-right-line ms-2"></i></span><h4 class="button-title">%title</h4></div>'
                                            );
                                        }
                                        ?>
                                    </div>
                                </nav>
                        <?php
                            endif;
                        endif; ?>


                        <?php if (comments_open() || get_comments_number()): ?>
                            <?php comments_template(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($sidebar_active): ?>
                <div class="col-sm-12 col-lg-4 mt-5 mt-lg-0">
                    <?php get_sidebar(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
