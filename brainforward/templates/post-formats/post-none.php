<div class="not-found section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-7 text-center">

                <h3 class="not-found__code">404</h3>
                <h2 class="not-found__title"><?php esc_html_e('Nothing Found', 'brainforward'); ?></h2>

                <?php if ( is_home() && current_user_can('publish_posts') ) : ?>
                    <p class="not-found__description">
                        <?php
                        printf(
                            esc_html__('Ready to publish your first post? ', 'brainforward') .
                            '<a href="%1$s" class="not-found__link">%2$s</a>',
                            esc_url(admin_url('post-new.php')),
                            esc_html__('Get started', 'brainforward')
                        );
                        ?>
                    </p>

                <?php elseif ( is_search() ) : ?>
                    <p class="not-found__description">
                        <?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with different keywords.', 'brainforward'); ?>
                    </p>
                    <div class="not-found__form mt-4">
                        <?php get_search_form(); ?>
                    </div>

                <?php else : ?>
                    <p class="not-found__description">
                        <?php esc_html_e('It seems we can’t find what you’re looking for. Perhaps searching can help.', 'brainforward'); ?>
                    </p>
                    <div class="not-found__form mt-4">
                        <?php get_search_form(); ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
