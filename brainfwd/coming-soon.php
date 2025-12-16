<?php
/**
Template Name: Coming soon
**/
?>
<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <main class="main_wrapper">
        <div class="page_wrapper">
            <?php the_content(); ?>
        </div>
    </main>
    <?php wp_footer(); ?>

</body>

</html>