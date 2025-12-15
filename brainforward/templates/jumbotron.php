<?php
$elementor_template = get_theme_mod('header_elementor_template_setting', 'default'); 
$arrow              = get_theme_mod('header_scroll_arrow', 'hide'); 

if ( ! function_exists('is_product') ) {
    function is_product() {
        return false;
    }
}
?>

<?php if ( class_exists('Elementor\Plugin') && ! empty($elementor_template) && $elementor_template !== 'default' ) : ?>
    <div class="elementor-header-template">
        <?php echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($elementor_template); ?>
    </div>
<?php else : ?>
    <!-- Site Header Start -->
    <section class="site-header site-header--<?php echo esc_attr( get_post_type() ); ?>">
        <div class="menu__height"></div>
        
        <div class="container">
            <h1 class="site-header__title">
                <?php echo wp_kses_post( Brainforward_Functions::page_title() ); ?>
            </h1>

            <?php if ( class_exists( 'Codexse_Toolkit_Functions' ) ) : ?>
                <div class="site-header__description">
                    <?php echo Codexse_Toolkit_Functions::breadcrumb(); ?>
                </div>
            <?php elseif ( get_bloginfo( 'description' ) ) : ?>
                <div class="site-header__description">
                    <?php echo esc_html( get_bloginfo( 'description' ) ); ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ( $arrow !== 'hide' ) : ?>
            <div class="site-header__scroll">
                <a href="#scrollDown" class="scroll-button">
                    <i class="ri-arrow-down-long-line" aria-hidden="true"></i>
                    <span class="visually-hidden"><?php esc_html_e( 'Scroll down', 'brainforward' ); ?></span>
                </a>
            </div>
        <?php endif; ?>
    </section>
    <!-- Site Header End -->
<?php endif; ?>

<?php if ( $arrow !== 'hide' ) : ?>
    <div id="scrollDown" class="scroll-anchor"></div>
<?php endif; ?>
