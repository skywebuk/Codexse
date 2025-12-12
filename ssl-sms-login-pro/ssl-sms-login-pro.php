<?php
/**
 * Plugin Name: SSL SMS Login Pro
 * Plugin URI: https://codexse.com/plugins/ssl-sms-login-pro
 * Description: Professional SMS-based login and registration system for WordPress using SSL Wireless SMS Gateway. Features OTP verification, mobile number login, forgot password via SMS, and more.
 * Version: 1.0.0
 * Author: Codexse
 * Author URI: https://codexse.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ssl-sms-login-pro
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('SSL_SMS_LOGIN_VERSION', '1.0.0');
define('SSL_SMS_LOGIN_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SSL_SMS_LOGIN_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SSL_SMS_LOGIN_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
final class SSL_SMS_Login_Pro {

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Plugin components
     */
    public $admin_settings;
    public $sms_gateway;
    public $otp_handler;
    public $login_forms;

    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Load required files
     */
    private function load_dependencies() {
        require_once SSL_SMS_LOGIN_PLUGIN_DIR . 'includes/class-admin-settings.php';
        require_once SSL_SMS_LOGIN_PLUGIN_DIR . 'includes/class-sms-gateway.php';
        require_once SSL_SMS_LOGIN_PLUGIN_DIR . 'includes/class-otp-handler.php';
        require_once SSL_SMS_LOGIN_PLUGIN_DIR . 'includes/class-login-forms.php';
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Load text domain
        add_action('plugins_loaded', array($this, 'load_textdomain'));

        // Initialize components
        add_action('init', array($this, 'init_components'));

        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    /**
     * Load plugin text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'ssl-sms-login-pro',
            false,
            dirname(SSL_SMS_LOGIN_PLUGIN_BASENAME) . '/languages'
        );
    }

    /**
     * Initialize plugin components
     */
    public function init_components() {
        $this->admin_settings = new SSL_SMS_Admin_Settings();
        $this->sms_gateway = new SSL_SMS_Gateway();
        $this->otp_handler = new SSL_SMS_OTP_Handler($this->sms_gateway);
        $this->login_forms = new SSL_SMS_Login_Forms($this->otp_handler);
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'ssl-sms-login-style',
            SSL_SMS_LOGIN_PLUGIN_URL . 'assets/css/style.css',
            array(),
            SSL_SMS_LOGIN_VERSION
        );

        wp_enqueue_script(
            'ssl-sms-login-script',
            SSL_SMS_LOGIN_PLUGIN_URL . 'assets/js/script.js',
            array('jquery'),
            SSL_SMS_LOGIN_VERSION,
            true
        );

        wp_localize_script('ssl-sms-login-script', 'sslSmsLogin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ssl_sms_login_nonce'),
            'i18n' => array(
                'sending' => __('Sending...', 'ssl-sms-login-pro'),
                'verifying' => __('Verifying...', 'ssl-sms-login-pro'),
                'resendIn' => __('Resend in', 'ssl-sms-login-pro'),
                'seconds' => __('seconds', 'ssl-sms-login-pro'),
                'resendOtp' => __('Resend OTP', 'ssl-sms-login-pro'),
                'otpSent' => __('OTP sent successfully!', 'ssl-sms-login-pro'),
                'error' => __('An error occurred. Please try again.', 'ssl-sms-login-pro'),
            ),
        ));
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'ssl-sms-login') === false) {
            return;
        }

        wp_enqueue_style(
            'ssl-sms-login-admin-style',
            SSL_SMS_LOGIN_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            SSL_SMS_LOGIN_VERSION
        );
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Set default options
        $default_options = array(
            'api_token' => '',
            'sender_id' => '',
            'otp_expiry' => 5,
            'max_otp_attempts' => 3,
            'block_duration' => 24,
            'enable_registration' => 'yes',
            'enable_login' => 'yes',
            'enable_forgot_password' => 'yes',
            'redirect_after_login' => '',
            'otp_message_template' => __('Your OTP is: {otp}. Valid for {expiry} minutes.', 'ssl-sms-login-pro'),
            'welcome_message_template' => __('Welcome! Your username: {username}, Password: {password}', 'ssl-sms-login-pro'),
        );

        if (!get_option('ssl_sms_login_settings')) {
            add_option('ssl_sms_login_settings', $default_options);
        }

        // Create database table for logs
        $this->create_log_table();

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * Create SMS log table
     */
    private function create_log_table() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ssl_sms_logs';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            mobile_number varchar(20) NOT NULL,
            message_type varchar(50) NOT NULL,
            status varchar(20) NOT NULL,
            api_response text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY mobile_number (mobile_number),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /**
     * Get plugin option
     */
    public static function get_option($key, $default = '') {
        $options = get_option('ssl_sms_login_settings', array());
        return isset($options[$key]) ? $options[$key] : $default;
    }

    /**
     * Update plugin option
     */
    public static function update_option($key, $value) {
        $options = get_option('ssl_sms_login_settings', array());
        $options[$key] = $value;
        update_option('ssl_sms_login_settings', $options);
    }
}

/**
 * Initialize plugin
 */
function ssl_sms_login_pro() {
    return SSL_SMS_Login_Pro::get_instance();
}

// Start the plugin
ssl_sms_login_pro();
