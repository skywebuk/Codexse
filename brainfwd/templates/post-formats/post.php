<?php
$image          = get_theme_mod('blog_thumb_settings', 'show');
$image_size     = get_theme_mod('blog_thumb_size', 'large');
$author         = get_theme_mod('blog_show_author', 'show');
$date           = get_theme_mod('blog_show_date', 'show');
$comment        = get_theme_mod('blog_show_comment_count', 'show');
$tags           = get_theme_mod('blog_show_tags', 'show');
$category       = get_theme_mod('blog_show_category', 'show');
$counter        = get_theme_mod('blog_show_view_counter', 'show');
$title          = get_theme_mod('blog_show_title', 'show');
$excerpt        = get_theme_mod('blog_show_excerpt', 'show');
$excerpt_length = get_theme_mod('blog_excerpt_length', 30);
$read_more      = get_theme_mod('blog_read_more', 'show');
$read_more_text = get_theme_mod('blog_read_more_text', __('Read More', 'brainfwd'));
?>

<div <?php post_class(['post-card', 'post-card--' . get_post_type()]); ?>>

    <?php if ($image !== 'hide') : ?>
        <div class="post-card__thumbnail">
            <?php Brainfwd_Functions::post_thumbnail($image_size); ?>
        </div>
    <?php endif; ?>

    <div class="post-card__content">


        <?php if ($title !== 'hide' && get_the_title()) : ?>
            <h3 class="post-card__title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>
        <?php endif; ?>

        <?php if (
            ($author !== 'hide' && get_the_author()) ||
            ($date !== 'hide' && Brainfwd_Functions::get_post_date()) ||
            ($comment !== 'hide' && Brainfwd_Functions::get_comment_count()) ||
            ($tags !== 'hide' && has_tag()) ||
            ($category !== 'hide' && get_the_category())
        ) : ?>
            <ul class="post-card__meta meta-list">
                <?php if ($author !== 'hide' && get_the_author()) : ?>
                    <li class="meta-list__item meta-list__author">
                        <i class="ri-user-line icon"></i>
                        <?php echo esc_html(get_the_author()); ?>
                    </li>
                <?php endif; ?>

                <?php if ($date !== 'hide' && Brainfwd_Functions::get_post_date()) : ?>
                    <li class="meta-list__item meta-list__date">
                        <i class="ri-calendar-line icon"></i>
                        <?php echo wp_kses_post(Brainfwd_Functions::get_post_date()); ?>
                    </li>
                <?php endif; ?>

                <?php if ($comment !== 'hide' && Brainfwd_Functions::get_comment_count()) : ?>
                    <li class="meta-list__item meta-list__comments">
                        <i class="ri-message-2-line icon"></i>
                        <?php echo esc_html(Brainfwd_Functions::get_comment_count()); ?>
                    </li>
                <?php endif; ?>

                <?php if ($tags !== 'hide' && has_tag() && !is_single()) : ?>
                    <li class="meta-list__item meta-list__tags">
                        <i class="ri-price-tag-2-line icon"></i>
                        <?php echo wp_kses_post(get_the_tag_list('', ', ')); ?>
                    </li>
                <?php endif; ?>

                <?php if ($category !== 'hide' && get_the_category()) : ?>
                    <li class="meta-list__item meta-list__categories">
                        <i class="ri-folders-line icon"></i>
                        <?php echo wp_kses_post(get_the_category_list(', ')); ?>
                    </li>
                <?php endif; ?>

                <?php 
                // Add post views with eye icon
                    if ( class_exists('Codexse_Toolkit_Functions') ) : 
                        $views = Codexse_Toolkit_Functions::get_post_views(get_the_ID());
                    ?>
                    <li class="meta-list__item meta-list__views">
                        <i class="ri-eye-line icon"></i>
                        <?php echo esc_html( $views ); ?>
                    </li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>

        <?php if ($excerpt !== 'hide') : ?>
            <div class="post-card__excerpt">
                <?php echo esc_html(wp_trim_words(get_the_content(), $excerpt_length, '...')); ?>
            </div>
        <?php endif; ?>

        <?php if ($read_more !== 'hide' && !empty($read_more_text) && get_post_type() !== 'product') : ?>
            <a href="<?php the_permalink(); ?>" class="post-card__read-more">
                <?php echo wp_kses_post($read_more_text); ?>
                <i class="ri-add-line ms-2"></i>
            </a>
        <?php endif; ?>

    </div>
</div>