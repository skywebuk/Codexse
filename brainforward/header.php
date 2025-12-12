<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="google-site-verification" content="EfmzoelFj2-8evrMd6WguHvAhcjC5cVLfN4ku1D6QTk" />
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-K22XJJNQ');</script>
	<!-- End Google Tag Manager -->

	<?php wp_head(); ?>
	
	<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-R9RY2G3HQR"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-R9RY2G3HQR');
</script>
	
	
</head>
<body <?php body_class('overflow-hidden'); ?> >
	<?php wp_body_open(); ?>
    <!-- Preloader Start -->
    <?php
    $loader_visibility = get_theme_mod('brainforward_loader_visibility', 'show');
    $loader_type = get_theme_mod('brainforward_loader_type', 'loader');
    $loader_text = get_theme_mod('loader_text_setting', __('Loading', 'brainforward'));
    $loader_image  = get_theme_mod('brainforward_loader_image', get_theme_file_uri('assets/images/loader.gif'));
    $offer = get_theme_mod('navbar_offer_setting', 'hide');
    $offer_text = get_theme_mod('navbar_offer_text', __('🧠 Special Offer: Get 50% off your first course - Enroll today!', 'brainforward'));
    $elementor_template = get_theme_mod('loader_template_settings', 'default'); 

    if ($loader_visibility === 'show') : ?>
        <?php if ($loader_type == 'template' && class_exists('Elementor\Plugin') && !empty($elementor_template) && $elementor_template !== 'default') : ?>
        <div class="preloader">
            <div class="loader">
                <div class="loader-template">
                    <?php 
                    // Render the Elementor template content
                    echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($elementor_template); 
                    ?>
                </div>
            </div>
        </div>
        <?php elseif($loader_type == 'text' && !empty($loader_text)) : ?>
            <!-- Display Default Preloader with Text -->
            <div class="preloader">
                <div class="loader">
                    <svg class="loader__svg">
                        <text class="loader__text" text-anchor="middle" x="50%" y="75%">
                            <?php echo substr($loader_text, 0, 10); // Display the first 10 characters of the loader text ?>
                        </text>
                    </svg>
                </div>
            </div>
        <?php else: ?>
            <div class="preloader">
                <div class="loader">
                    <div class="spinner"></div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
	 
	<main class="main_wrapper">
        <?php if ($offer !== 'hide' && !empty($offer_text)) : ?>
            <div class="alert__bar">
                <div class="alert__text"><?php echo wp_kses_post($offer_text); ?></div>
                <button type="button" class="alert__close" aria-label="<?php esc_attr_e('Close alert', 'brainforward'); ?>">
                    <i class="ri-close-large-line"></i>
                </button>
            </div>
        <?php endif; ?>
		<div class="page_wrapper">
    		<?php get_template_part('templates/navbar'); ?>
