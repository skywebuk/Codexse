<?php
/**
 * Plugin Name: Edugo LMS
 * Plugin URI: https://edugo-lms.com
 * Description: A comprehensive WordPress LMS plugin for creating and selling online courses. Features multi-instructor support, frontend dashboards, WooCommerce monetization, quizzes, assignments, certificates, and more.
 * Version: 1.0.0
 * Author: Edugo Team
 * Author URI: https://edugo-lms.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: edugo-lms
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Plugin version.
 */
define( 'EDUGO_LMS_VERSION', '1.0.0' );

/**
 * Plugin file path.
 */
define( 'EDUGO_LMS_FILE', __FILE__ );

/**
 * Plugin directory path.
 */
define( 'EDUGO_LMS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL.
 */
define( 'EDUGO_LMS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin basename.
 */
define( 'EDUGO_LMS_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Minimum PHP version required.
 */
define( 'EDUGO_LMS_MIN_PHP_VERSION', '8.0' );

/**
 * Minimum WordPress version required.
 */
define( 'EDUGO_LMS_MIN_WP_VERSION', '6.0' );

/**
 * Check PHP version compatibility.
 *
 * @return bool True if compatible, false otherwise.
 */
function edugo_lms_check_php_version() {
    return version_compare( PHP_VERSION, EDUGO_LMS_MIN_PHP_VERSION, '>=' );
}

/**
 * Check WordPress version compatibility.
 *
 * @return bool True if compatible, false otherwise.
 */
function edugo_lms_check_wp_version() {
    return version_compare( get_bloginfo( 'version' ), EDUGO_LMS_MIN_WP_VERSION, '>=' );
}

/**
 * Display admin notice for PHP version requirement.
 */
function edugo_lms_php_version_notice() {
    ?>
    <div class="notice notice-error">
        <p>
            <?php
            printf(
                /* translators: %s: Minimum PHP version required */
                esc_html__( 'Edugo LMS requires PHP version %s or higher. Please upgrade your PHP version.', 'edugo-lms' ),
                esc_html( EDUGO_LMS_MIN_PHP_VERSION )
            );
            ?>
        </p>
    </div>
    <?php
}

/**
 * Display admin notice for WordPress version requirement.
 */
function edugo_lms_wp_version_notice() {
    ?>
    <div class="notice notice-error">
        <p>
            <?php
            printf(
                /* translators: %s: Minimum WordPress version required */
                esc_html__( 'Edugo LMS requires WordPress version %s or higher. Please upgrade WordPress.', 'edugo-lms' ),
                esc_html( EDUGO_LMS_MIN_WP_VERSION )
            );
            ?>
        </p>
    </div>
    <?php
}

/**
 * Load the plugin autoloader.
 */
function edugo_lms_autoloader() {
    require_once EDUGO_LMS_PATH . 'includes/Core/Autoloader.php';
    \Edugo_LMS\Core\Autoloader::register();
}

/**
 * Initialize the plugin.
 *
 * @return \Edugo_LMS\Core\Plugin The main plugin instance.
 */
function edugo_lms() {
    static $instance = null;

    if ( null === $instance ) {
        require_once EDUGO_LMS_PATH . 'includes/Core/Plugin.php';
        $instance = \Edugo_LMS\Core\Plugin::get_instance();
    }

    return $instance;
}

/**
 * Plugin activation hook.
 */
function edugo_lms_activate() {
    require_once EDUGO_LMS_PATH . 'includes/Core/Activator.php';
    \Edugo_LMS\Core\Activator::activate();
}

/**
 * Plugin deactivation hook.
 */
function edugo_lms_deactivate() {
    require_once EDUGO_LMS_PATH . 'includes/Core/Deactivator.php';
    \Edugo_LMS\Core\Deactivator::deactivate();
}

/**
 * Bootstrap the plugin.
 */
function edugo_lms_bootstrap() {
    // Check PHP version.
    if ( ! edugo_lms_check_php_version() ) {
        add_action( 'admin_notices', 'edugo_lms_php_version_notice' );
        return;
    }

    // Check WordPress version.
    if ( ! edugo_lms_check_wp_version() ) {
        add_action( 'admin_notices', 'edugo_lms_wp_version_notice' );
        return;
    }

    // Load autoloader.
    edugo_lms_autoloader();

    // Initialize the plugin.
    edugo_lms();
}

// Register activation and deactivation hooks.
register_activation_hook( EDUGO_LMS_FILE, 'edugo_lms_activate' );
register_deactivation_hook( EDUGO_LMS_FILE, 'edugo_lms_deactivate' );

// Bootstrap the plugin on plugins_loaded.
add_action( 'plugins_loaded', 'edugo_lms_bootstrap', 10 );
