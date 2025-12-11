<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php 
// Default widget widgets
$default_widgets = [
    'codexse_testimonial' => 'off',
    'codexse_blog' => 'off',
    'codexse_page_title' => 'off',
    'codexse_shortcode' => 'off',
    'codexse_desktop_menu' => 'off',
    'codexse_team' => 'off',
    'codexse_mobile_menu' => 'off',
    'codexse_info_box' => 'off',
    'codexse_food_menu' => 'off',
    'codexse_buttoff' => 'off',
    'codexse_images_slider' => 'off',
    'codexse_hero_slider' => 'off',
];

// Get saved options or use defaults
$widgets = get_option('codexse_widgets', []);
$widgets = wp_parse_args($widgets, $default_widgets);
?>
<div class="codexse-dashboard-content">
    <form class="codexse-option-form" method="post" action="">
        <div class="codexse-header">        
            <h1 class="header-title"><?php _e( 'Widget List', 'codexse' ); ?></h1>
            <p class="header-desc"><?php _e( 'Select the widgets you need for your site. Enable only those you\'re actively using—disabling unused widgets will optimize loading speed by removing their associated assets.', 'codexse' ); ?></p>
            <div class="codexse-header-actions">
                <label class="codexse-switcher header-switch">
                    <input id="toggleAll" type="checkbox" />
                    <span class="switch-label enable"><?php _e( 'Enable All', 'codexse' ); ?></span>
                    <span class="switch-label disable"><?php _e( 'Disable All', 'codexse' ); ?></span>
                    <span class="switch-toggle">
                        <span class="switch-text on"><?php _e( 'On', 'codexse' ); ?></span>
                        <span class="switch-text off"><?php _e( 'Off', 'codexse' ); ?></span>
                    </span>
                </label>
                <button type="submit" class="codexse-button"><?php _e( 'Save Settings', 'codexse' ); ?></button>
            </div>
        </div>
        <div class="codexse-checkboxs codexse-widgets">
            <label class="codexse-switcher">
                <span class="switch-label"><?php _e( 'Page Title', 'codexse' ); ?></span>
                <input id="codexse_page_title" name="widgets[codexse_page_title]" type="checkbox" 
                    <?php checked( isset( $widgets['codexse_page_title'] ) && $widgets['codexse_page_title'] === 'on' ); ?>>
                <span class="switch-toggle">
                    <span class="switch-text on"><?php _e( 'On', 'codexse' ); ?></span>
                    <span class="switch-text off"><?php _e( 'Off', 'codexse' ); ?></span>
                </span>
            </label>
            <label class="codexse-switcher">
                <span class="switch-label"><?php _e( 'Shortcode', 'codexse' ); ?></span>
                <input id="codexse_shortcode" name="widgets[codexse_shortcode]" type="checkbox" 
                    <?php checked( isset( $widgets['codexse_shortcode'] ) && $widgets['codexse_shortcode'] === 'on' ); ?>>
                <span class="switch-toggle">
                    <span class="switch-text on"><?php _e( 'On', 'codexse' ); ?></span>
                    <span class="switch-text off"><?php _e( 'Off', 'codexse' ); ?></span>
                </span>
            </label>
            <!-- Desktop Menu Option -->
            <label class="codexse-switcher">
                <span class="switch-label"><?php _e( 'Desktop Menu', 'codexse' ); ?></span>
                <input id="codexse_desktop_menu" name="widgets[codexse_desktop_menu]" type="checkbox" 
                    <?php checked( isset( $widgets['codexse_desktop_menu'] ) && $widgets['codexse_desktop_menu'] === 'on' ); ?>>
                <span class="switch-toggle">
                    <span class="switch-text on"><?php _e( 'On', 'codexse' ); ?></span>
                    <span class="switch-text off"><?php _e( 'Off', 'codexse' ); ?></span>
                </span>
            </label>
            <!-- Team Menu Option -->
            <label class="codexse-switcher">
                <span class="switch-label"><?php _e( 'Team', 'codexse' ); ?></span>
                <input id="codexse_team" name="widgets[codexse_team]" type="checkbox" 
                    <?php checked( isset( $widgets['codexse_team'] ) && $widgets['codexse_team'] === 'on' ); ?>>
                <span class="switch-toggle">
                    <span class="switch-text on"><?php _e( 'On', 'codexse' ); ?></span>
                    <span class="switch-text off"><?php _e( 'Off', 'codexse' ); ?></span>
                </span>
            </label>
            <!-- Mobile Menu Option -->
            <label class="codexse-switcher">
                <span class="switch-label"><?php _e( 'Mobile Menu', 'codexse' ); ?></span>
                <input id="codexse_mobile_menu" name="widgets[codexse_mobile_menu]" type="checkbox" 
                    <?php checked( isset( $widgets['codexse_mobile_menu'] ) && $widgets['codexse_mobile_menu'] === 'on' ); ?>>
                <span class="switch-toggle">
                    <span class="switch-text on"><?php _e( 'On', 'codexse' ); ?></span>
                    <span class="switch-text off"><?php _e( 'Off', 'codexse' ); ?></span>
                </span>
            </label>
            <!-- Button Option -->
            <label class="codexse-switcher">
                <span class="switch-label"><?php _e( 'Button', 'codexse' ); ?></span>
                <input id="codexse_button" name="widgets[codexse_button]" type="checkbox" 
                    <?php checked( isset( $widgets['codexse_button'] ) && $widgets['codexse_button'] === 'on' ); ?>>
                <span class="switch-toggle">
                    <span class="switch-text on"><?php _e( 'On', 'codexse' ); ?></span>
                    <span class="switch-text off"><?php _e( 'Off', 'codexse' ); ?></span>
                </span>
            </label>
            <label class="codexse-switcher" for="codexse_info_box">
                <span class="switch-label"><?php _e( 'Info Box', 'codexse' ); ?></span>
                <input id="codexse_info_box" name="widgets[codexse_info_box]" type="checkbox" 
                    aria-label="<?php esc_attr_e( 'Info box', 'codexse' ); ?>"
                    value="on"
                    <?php checked( isset( $widgets['codexse_info_box'] ) && $widgets['codexse_info_box'] === 'on' ); ?>>
                <span class="switch-toggle" aria-hidden="true">
                    <span class="switch-text on"><?php _e( 'On', 'codexse' ); ?></span>
                    <span class="switch-text off"><?php _e( 'Off', 'codexse' ); ?></span>
                </span>
            </label>
            <label class="codexse-switcher" for="codexse_food_menu">
                <span class="switch-label"><?php _e( 'Food Menu', 'codexse' ); ?></span>
                <input id="codexse_food_menu" name="widgets[codexse_food_menu]" type="checkbox" 
                    aria-label="<?php esc_attr_e( 'Food Menu', 'codexse' ); ?>"
                    value="on"
                    <?php checked( isset( $widgets['codexse_food_menu'] ) && $widgets['codexse_food_menu'] === 'on' ); ?>>
                <span class="switch-toggle" aria-hidden="true">
                    <span class="switch-text on"><?php _e( 'On', 'codexse' ); ?></span>
                    <span class="switch-text off"><?php _e( 'Off', 'codexse' ); ?></span>
                </span>
            </label>
            <label class="codexse-switcher" for="codexse_images_slider">
                <span class="switch-label"><?php _e( 'Images Slider', 'codexse' ); ?></span>
                <input id="codexse_images_slider" name="widgets[codexse_images_slider]" type="checkbox" 
                    aria-label="<?php esc_attr_e( 'Images Slider', 'codexse' ); ?>"
                    value="on"
                    <?php checked( isset( $widgets['codexse_images_slider'] ) && $widgets['codexse_images_slider'] === 'on' ); ?>>
                <span class="switch-toggle" aria-hidden="true">
                    <span class="switch-text on"><?php _e( 'On', 'codexse' ); ?></span>
                    <span class="switch-text off"><?php _e( 'Off', 'codexse' ); ?></span>
                </span>
            </label>
            <label class="codexse-switcher" for="codexse_hero_slider">
                <span class="switch-label"><?php _e( 'Hero Slider', 'codexse' ); ?></span>
                <input id="codexse_hero_slider" name="widgets[codexse_hero_slider]" type="checkbox" 
                    aria-label="<?php esc_attr_e( 'Hero Slider', 'codexse' ); ?>"
                    value="on"
                    <?php checked( isset( $widgets['codexse_hero_slider'] ) && $widgets['codexse_hero_slider'] === 'on' ); ?>>
                <span class="switch-toggle" aria-hidden="true">
                    <span class="switch-text on"><?php _e( 'On', 'codexse' ); ?></span>
                    <span class="switch-text off"><?php _e( 'Off', 'codexse' ); ?></span>
                </span>
            </label>
            <label class="codexse-switcher" for="codexse_testimonial">
                <span class="switch-label"><?php _e( 'Testimonial', 'codexse' ); ?></span>
                <input id="codexse_testimonial" name="widgets[codexse_testimonial]" type="checkbox" 
                    aria-label="<?php esc_attr_e( 'Testimonial', 'codexse' ); ?>"
                    value="on"
                    <?php checked( isset( $widgets['codexse_testimonial'] ) && $widgets['codexse_testimonial'] === 'on' ); ?>>
                <span class="switch-toggle" aria-hidden="true">
                    <span class="switch-text on"><?php _e( 'On', 'codexse' ); ?></span>
                    <span class="switch-text off"><?php _e( 'Off', 'codexse' ); ?></span>
                </span>
            </label>
            <label class="codexse-switcher" for="codexse_blog">
                <span class="switch-label"><?php _e( 'Blog', 'codexse' ); ?></span>
                <input id="codexse_blog" name="widgets[codexse_blog]" type="checkbox" 
                    aria-label="<?php esc_attr_e( 'blog', 'codexse' ); ?>"
                    value="on"
                    <?php checked( isset( $widgets['codexse_blog'] ) && $widgets['codexse_blog'] === 'on' ); ?>>
                <span class="switch-toggle" aria-hidden="true">
                    <span class="switch-text on"><?php _e( 'On', 'codexse' ); ?></span>
                    <span class="switch-text off"><?php _e( 'Off', 'codexse' ); ?></span>
                </span>
            </label>
        </div>
        <div class="codexse-footer">
            <button type="submit" class="codexse-button"><?php _e( 'Save Settings', 'codexse' ); ?></button>
        </div>
    </form>
</div>