<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php 
// Default widget features
$default_features = [
    'codexse_scroll_effect' => 'on',
    'codexse_floating_effect' => 'on',
];

// Get saved options or use defaults
$features = get_option( 'codexse_features', [] );
$features = wp_parse_args( $features, $default_features );
?>

<div class="codexse-dashboard-content">
    <form class="codexse-option-form" method="post" action="">
        <!-- Header Section -->
        <div class="codexse-header">
            <h1 class="header-title"><?php esc_html_e( 'Feature List', 'codexse' ); ?></h1>
            <p class="header-desc"><?php esc_html_e( 'Select the features you need for your site. Enable only those you\'re actively using—disabling unused features will optimize loading speed by removing their associated assets.', 'codexse' ); ?></p>
            <div class="codexse-header-actions">
                <label class="codexse-switcher header-switch">
                    <input id="toggleAll" type="checkbox" />
                    <span class="switch-label enable"><?php esc_html_e( 'Enable All', 'codexse' ); ?></span>
                    <span class="switch-label disable"><?php esc_html_e( 'Disable All', 'codexse' ); ?></span>
                    <span class="switch-toggle">
                        <span class="switch-text on"><?php esc_html_e( 'On', 'codexse' ); ?></span>
                        <span class="switch-text off"><?php esc_html_e( 'Off', 'codexse' ); ?></span>
                    </span>
                </label>
                <button type="submit" class="codexse-button"><?php esc_html_e( 'Save Settings', 'codexse' ); ?></button>
            </div>
        </div>
        <!-- Features Section -->
        <div class="codexse-checkboxs codexse-features">
            <!-- Scroll Effect Feature -->
            <label class="codexse-switcher">
                <span class="switch-label"><?php esc_html_e( 'Scroll Effect', 'codexse' ); ?></span>
                <input 
                    id="codexse_scroll_effect" 
                    name="features[codexse_scroll_effect]" 
                    type="checkbox" 
                    <?php checked( isset( $features['codexse_scroll_effect'] ) && $features['codexse_scroll_effect'] === 'on' ); ?> 
                />
                <span class="switch-toggle">
                    <span class="switch-text on"><?php esc_html_e( 'On', 'codexse' ); ?></span>
                    <span class="switch-text off"><?php esc_html_e( 'Off', 'codexse' ); ?></span>
                </span>
            </label>
            <!-- Floating Effect Feature -->
            <label class="codexse-switcher">
                <span class="switch-label"><?php esc_html_e( 'Floating Effect', 'codexse' ); ?></span>
                <input 
                    id="codexse_floating_effect" 
                    name="features[codexse_floating_effect]" 
                    type="checkbox" 
                    <?php checked( isset( $features['codexse_floating_effect'] ) && $features['codexse_floating_effect'] === 'on' ); ?> 
                />
                <span class="switch-toggle">
                    <span class="switch-text on"><?php esc_html_e( 'On', 'codexse' ); ?></span>
                    <span class="switch-text off"><?php esc_html_e( 'Off', 'codexse' ); ?></span>
                </span>
            </label>
        </div>
        <!-- Footer Section -->
        <div class="codexse-footer">
            <button type="submit" class="codexse-button"><?php esc_html_e( 'Save Settings', 'codexse' ); ?></button>
        </div>
    </form>
</div>