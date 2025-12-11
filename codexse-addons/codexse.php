<?php
/**
 * Plugin Name: Codexse Addons – Elementor Full Set of Widgets
 * Description: Codexse is a complete set of advanced widgets for the Elementor page builder plugin for WordPress.
 * Plugin URI:  https://codexse.com/
 * Author:      Codexse
 * Author URI:  https://codexse.com/
 * Version:     1.1.0
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: codexse-addons
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Final class declaration with singleton pattern
final class Codexse_Addons {
    // Singleton instance
    public static $instance = null;

    // Define the minimum required Elementor version
    const MINIMUM_ELEMENTOR_VERSION = '3.0.0';
    const MINIMUM_PHP_VERSION = '7.0';

    // Constructor
    public function __construct() {
        if ( ! function_exists('is_plugin_active') ) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }
        $this->define_constants();
        add_action( 'init', [ $this, 'i18n' ] );
        add_action( 'plugins_loaded', [ $this, 'plugin_loaded' ]);
    }

    // Get instance method
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Define plugin constants
    public function define_constants() {
        if ( ! defined( 'CODEXSE_VERSION' ) ) {
            define( 'CODEXSE_VERSION', '1.0.1' );
        }
        if ( ! defined( 'CODEXSE_PLUGIN_DIR' ) ) {
            define( 'CODEXSE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        }
        if ( ! defined( 'CODEXSE_PLUGIN_URL' ) ) {
            define( 'CODEXSE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        }
        if ( ! defined( 'CODEXSE_CLASSES_DIR' ) ) {
            define( 'CODEXSE_CLASSES_DIR', CODEXSE_PLUGIN_DIR . 'classes/' );
        }
        if ( ! defined( 'CODEXSE_ADDONS_DIR' ) ) {
            define( 'CODEXSE_ADDONS_DIR', CODEXSE_PLUGIN_DIR . 'widgets/' );
        }
        if ( ! defined( 'CODEXSE_FEATURES_DIR' ) ) {
            define( 'CODEXSE_FEATURES_DIR', CODEXSE_PLUGIN_DIR . 'features/' );
        }
        if ( ! defined( 'CODEXSE_ASSETS_URL' ) ) {
            define( 'CODEXSE_ASSETS_URL', CODEXSE_PLUGIN_URL . 'assets/' );
        }
        if ( ! defined( 'CODEXSE_JS_URL' ) ) {
            define( 'CODEXSE_JS_URL', CODEXSE_ASSETS_URL . 'js/' );
        }
        if ( ! defined( 'CODEXSE_CSS_URL' ) ) {
            define( 'CODEXSE_CSS_URL', CODEXSE_ASSETS_URL . 'css/' );
        }
        if ( ! defined( 'CODEXSE_IMAGES_URL' ) ) {
            define( 'CODEXSE_IMAGES_URL', CODEXSE_ASSETS_URL . 'images/' );
        }
        if ( ! defined( 'CODEXSE_ICONS_URL' ) ) {
            define( 'CODEXSE_ICONS_URL', CODEXSE_ASSETS_URL . 'icons/' );
        }
        if ( ! defined( 'CODEXSE_ADMIN_DIR' ) ) {
            define( 'CODEXSE_ADMIN_DIR', CODEXSE_PLUGIN_DIR . 'admin/' );
        }
        if ( ! defined( 'CODEXSE_ADMIN_URL' ) ) {
            define( 'CODEXSE_ADMIN_URL', CODEXSE_PLUGIN_URL . 'admin/' );
        }
    }


    // Initialization method
    public function i18n() {
        $this->load_textdomain(); // Load text domain for translations
    }

    // Load plugin text domain for translations
    public function load_textdomain() {
        load_plugin_textdomain( 'codexse-addons', false, dirname( plugin_basename( __FILE__ )) . '/languages' );
    }

    // Init method to check Elementor activation
    public function plugin_loaded() {
        // Check if Elementor is installed and activated
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
            return;
        }
        
        // Check for required Elementor version
        if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
            return;
        }


        // Check for required PHP version
        if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
            return;
        }

        $this->include_files();
    }

    /**
     * Admin notice if Elementor is deactivated or not installed
     */
    public function admin_notice_missing_main_plugin() {
        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
    
        $elementor = 'elementor/elementor.php';
        if ( self::is_plugin_active( $elementor ) ) {
            if ( ! current_user_can( 'activate_plugins' ) ) {
                return;
            }
    
            $activation_url = esc_url( wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $elementor . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $elementor ) );
    
            $message = '<p>' . esc_html__( 'Codexse Addons for Elementor requires "Elementor" plugin to be active. Please activate Elementor to continue.', 'codexse-addons' ) . '</p>';
            $message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, esc_html__( 'Elementor Activate Now', 'codexse-addons' ) ) . '</p>';
        } else {
            if ( ! current_user_can( 'install_plugins' ) ) {
                return;
            }
    
            $install_url = esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' ) );
    
            $message = '<p>' . esc_html__( 'Codexse Addons for Elementor requires "Elementor" plugin to be active. Please install the Elementor plugin to continue.', 'codexse-addons' ) . '</p>';
            $message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, esc_html__( 'Elementor Install Now', 'codexse-addons' ) ) . '</p>';
        }
        echo '<div class="error"><p>' . $message . '</p></div>'; // Output escaped
    }
    
    public function admin_notice_minimum_elementor_version() {
        $message = sprintf( __( '<strong>Codexse Addons for Elementor</strong> requires Elementor version %s or greater.', 'codexse-addons' ), self::MINIMUM_ELEMENTOR_VERSION );
        echo '<div class="error"><p>' . $message . '</p></div>';
    }

    public function admin_notice_minimum_php_version() {
        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
        $message = sprintf(
            __( '"%1$s" requires "%2$s" version %3$s or greater.', 'codexse-addons' ),
            '<strong>' . __( 'Codexse Addons', 'codexse-addons' ) . '</strong>',
            '<strong>' . __( 'PHP', 'codexse-addons' ) . '</strong>',
             self::MINIMUM_PHP_VERSION
        );
        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    } 

    public static function is_plugin_active( $plugin ) {
        return in_array( $plugin, (array) get_option( 'active_plugins', [] ), true);
    }

    public function include_files() {
        if ( is_user_logged_in() ) {
            require_once( CODEXSE_ADMIN_DIR . 'Codexse_Addons_Admin.php' );
        }
        require_once( CODEXSE_CLASSES_DIR . 'Codexse_Addons_Classes.php' );
        require_once( CODEXSE_CLASSES_DIR . 'Codexse_Addons_Functions.php' );
        require_once( CODEXSE_CLASSES_DIR . 'Codexse_Addons_Enqueue.php' );
        require_once( CODEXSE_CLASSES_DIR . 'Codexse_Addons_Widgets.php' );
        require_once( CODEXSE_CLASSES_DIR . 'Codexse_Section_Options.php' );
        require_once( CODEXSE_CLASSES_DIR . 'Codexse_Addons_Features.php' );
    }

}

// Initialize the plugin
Codexse_Addons::get_instance();
