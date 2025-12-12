<?php
$logo           = '';
$button         = get_theme_mod('navbar_button_setting', 'hide');
$button_text    = get_theme_mod('navbar_button_text_setting', __('Get Started', 'brainforward'));
$button_link    = get_theme_mod('navbar_button_link_setting', home_url());
$elementor_tpl  = get_theme_mod('navbar_elementor_template_setting', 'default');
$cart           = get_theme_mod('navbar_cart_setting', 'hide');
$search         = get_theme_mod('navbar_search_setting', 'show');
$sidebar        = get_theme_mod('navbar_sidebar_setting', 'hide');
$light_logo     = get_theme_mod('brainforward_light_logo');

$nav_actions  = $nav_button = '';

// WooCommerce Mini Cart
if (class_exists('WooCommerce') && $cart !== 'hide') {
    // Mini cart panel
    echo '<div class="navbar__mini-cart">';
        echo '<button class="navbar__close"><i class="ri-close-large-line"></i></button>';
        WooCommerce_Functions::mini_cart();
    echo '</div>';

    // Cart toggle button
    $nav_actions .= '<button class="nav_action cart_toggle" title="' . esc_attr__('View your shopping cart', 'brainforward') . '">';
        $nav_actions .= '<div class="position-relative">';
            $nav_actions .= '<i class="ri-shopping-bag-3-line"></i>';
            $nav_actions .= '<span class="cart_count">' . WC()->cart->get_cart_contents_count() . '</span>';
        $nav_actions .= '</div>';
    $nav_actions .= '</button>';
}


// Search Toggle
if ($search !== 'hide') {
    $nav_actions .= '<button type="button" class="navbar__action navbar__search-toggle" >';
    $nav_actions .= '<i class="ri-search-2-line"></i>';
    $nav_actions .= '</button>';
}

// Sidebar Toggle
if ($sidebar !== 'hide') {
    $nav_actions .= '<button type="button" class="navbar__action navbar__sidebar-toggle">';
    $nav_actions .= '<i class="ri-indent-decrease"></i>';
    $nav_actions .= '</button>';
}


$nav_actions .= '<button type="button" class="navbar__action navbar__mobile-toggle">';
$nav_actions .= '<i class="ri-menu-line"></i>';
$nav_actions .= '</button>';

// Button
if ($button !== 'hide') {
    $nav_button .= '<a href="' . esc_url($button_link) . '" class="primary_button">' . wp_kses_post($button_text) . '</a>';
    $nav_actions .= '<div class="d-none d-sm-inline-block">'.$nav_button.'</div>';
}

$tutor_profile = Codexse_Toolkit_Functions::codexse_tutor_profile_menu();
if(!empty($tutor_profile)){
	$nav_actions .= $tutor_profile;
}


// Logo Setup
if (!empty($light_logo) && has_custom_logo()) {
    $logo .= '<div class="navbar__logo-main">' . get_custom_logo() . '</div>';
    $logo .= '<a href="' . esc_url(home_url('/')) . '" class="navbar__logo-light custom-logo-link"><img src="' . esc_url($light_logo) . '" alt="' . esc_attr(get_bloginfo('name')) . '"></a>';
} elseif (has_custom_logo()) {
    $logo .= get_custom_logo();
} elseif (!empty($light_logo)) {
    $logo .= '<a href="' . esc_url(home_url('/')) . '"><img src="' . esc_url($light_logo) . '" alt="' . esc_attr(get_bloginfo('name')) . '"></a>';
} else {
    $logo .= '<h3 class="m-0"><a href="' . esc_url(home_url('/')) . '">' . esc_html(get_bloginfo('title')) . '</a></h3>';
}


// Sidebar Content
if (is_active_sidebar('navbar_toggle_sidebar') && $sidebar !== 'hide') {
    echo '<div class="navbar__sidebar">';
    echo '<div class="navbar__sidebar-header">';
        echo '<div class="navbar__sidebar-logo">';
            echo wp_kses_post($logo);
        echo '</div>';
        echo '<button class="navbar__sidebar-close" type="button" aria-label="' . esc_attr__('Close sidebar', 'brainforward') . '">';
            echo '<i class="ri-close-large-line"></i>';
        echo '</button>';
    echo '</div>';
    echo '<div class="navbar__sidebar-content">';
    dynamic_sidebar('navbar_toggle_sidebar');
    echo '</div>';
    echo '</div>';
}


if ($search !== 'hide') {
    echo '<div class="navbar__search">';
        echo '<div class="container">';
            echo '<div class="navbar__search-header">';
                echo '<div class="navbar__search-logo">';
                    echo wp_kses_post($logo);
                echo '</div>';
                echo '<button class="navbar__search-close" type="button" aria-label="' . esc_attr__('Close search', 'brainforward') . '">';
                    echo '<i class="ri-close-large-line"></i>';
                echo '</button>';
            echo '</div>';
            echo '<div class="navbar__search-body">';
                echo '<form role="search" method="get" class="navbar__search-form" action="' . esc_url(home_url('/')) . '">';
                    echo '<input type="search" class="navbar__search-input" placeholder="' . esc_attr__('Search here...', 'brainforward') . '" value="' . esc_attr(get_search_query()) . '" name="s" />';
                    echo '<button type="submit" class="navbar__search-submit" aria-label="' . esc_attr__('Submit search', 'brainforward') . '">';
                        echo '<i class="ri-search-2-line"></i>';
                    echo '</button>';
                echo '</form>';
            echo '</div>';
        echo '</div>';
    echo '</div>';
}


?>

<?php if (class_exists('Elementor\Plugin') && !empty($elementor_tpl) && $elementor_tpl !== 'default') : ?>
    <div class="navbar__elementor-template">
        <?php echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($elementor_tpl); ?>
    </div>
<?php else : ?>
<div class="navbar__height"></div>
<div class="main__navbar" data-sticky="<?php echo esc_attr(get_theme_mod('sticky_menu_setting', 'enabled')); ?>">
    <div class="container wide">
        <div class="navbar__row">
            <div class="navbar__logo">
                <?php echo wp_kses_post($logo); ?>
            </div>
            <?php if (!empty($nav_actions)) : ?>
                <div class="navbar__actions">
                    <?php echo wp_kses_post($nav_actions); ?>
                </div>
            <?php endif; ?>
            <div class="navbar__menu" id="nav_menu">
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary_menu',
                    'menu_class'     => 'nav',
                    'container'      => '',
                    'fallback_cb'    => 'brainforward_mainmenu_demo_content',
                    'walker'         => new Brainforward_Menu_Walker,
                ]);
                ?>
                <div class="d-sm-none"><?php echo wp_kses_post($nav_button); ?></div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>
