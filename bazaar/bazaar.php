<?php
/**
 * Plugin Name: Bazaar - Multi-Vendor Marketplace for WooCommerce
 * Plugin URI: https://example.com/bazaar
 * Description: A full-featured multi-vendor marketplace plugin built on WordPress + WooCommerce. Allows multiple vendors to sell products on a single platform.
 * Version: 1.0.0
 * Author: Codexse
 * Author URI: https://example.com
 * Text Domain: bazaar
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Bazaar
 */

defined( 'ABSPATH' ) || exit;

// Plugin constants
define( 'BAZAAR_VERSION', '1.0.0' );
define( 'BAZAAR_PLUGIN_FILE', __FILE__ );
define( 'BAZAAR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BAZAAR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BAZAAR_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main Bazaar Class
 *
 * @class Bazaar
 */
final class Bazaar {

    /**
     * Bazaar version.
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * The single instance of the class.
     *
     * @var Bazaar
     */
    protected static $instance = null;

    /**
     * Vendor instance.
     *
     * @var Bazaar_Vendor
     */
    public $vendor = null;

    /**
     * Commission instance.
     *
     * @var Bazaar_Commission
     */
    public $commission = null;

    /**
     * Withdrawal instance.
     *
     * @var Bazaar_Withdrawal
     */
    public $withdrawal = null;

    /**
     * Orders instance.
     *
     * @var Bazaar_Orders
     */
    public $orders = null;

    /**
     * Main Bazaar Instance.
     *
     * @return Bazaar - Main instance.
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Bazaar Constructor.
     */
    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Define constants.
     */
    private function define_constants() {
        $this->define( 'BAZAAR_ABSPATH', dirname( BAZAAR_PLUGIN_FILE ) . '/' );
        $this->define( 'BAZAAR_TEMPLATE_PATH', BAZAAR_ABSPATH . 'templates/' );
    }

    /**
     * Define constant if not already set.
     *
     * @param string $name  Constant name.
     * @param mixed  $value Constant value.
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    /**
     * Include required files.
     */
    public function includes() {
        // Core includes
        include_once BAZAAR_ABSPATH . 'includes/class-bazaar-install.php';
        include_once BAZAAR_ABSPATH . 'includes/class-bazaar-roles.php';
        include_once BAZAAR_ABSPATH . 'includes/class-bazaar-vendor.php';
        include_once BAZAAR_ABSPATH . 'includes/class-bazaar-product.php';
        include_once BAZAAR_ABSPATH . 'includes/class-bazaar-commission.php';
        include_once BAZAAR_ABSPATH . 'includes/class-bazaar-withdrawal.php';
        include_once BAZAAR_ABSPATH . 'includes/class-bazaar-orders.php';
        include_once BAZAAR_ABSPATH . 'includes/class-bazaar-shipping.php';
        include_once BAZAAR_ABSPATH . 'includes/class-bazaar-reviews.php';
        include_once BAZAAR_ABSPATH . 'includes/class-bazaar-notifications.php';
        include_once BAZAAR_ABSPATH . 'includes/class-bazaar-ajax.php';
        include_once BAZAAR_ABSPATH . 'includes/class-bazaar-shortcodes.php';
        include_once BAZAAR_ABSPATH . 'includes/class-bazaar-rest-api.php';
        include_once BAZAAR_ABSPATH . 'includes/bazaar-functions.php';
        include_once BAZAAR_ABSPATH . 'includes/bazaar-template-functions.php';

        // Admin includes
        if ( is_admin() ) {
            include_once BAZAAR_ABSPATH . 'admin/class-bazaar-admin.php';
            include_once BAZAAR_ABSPATH . 'admin/class-bazaar-admin-menus.php';
            include_once BAZAAR_ABSPATH . 'admin/class-bazaar-admin-settings.php';
            include_once BAZAAR_ABSPATH . 'admin/class-bazaar-admin-vendors.php';
            include_once BAZAAR_ABSPATH . 'admin/class-bazaar-admin-withdrawals.php';
            include_once BAZAAR_ABSPATH . 'admin/class-bazaar-admin-reports.php';
        }

        // Frontend includes
        if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
            include_once BAZAAR_ABSPATH . 'includes/class-bazaar-frontend.php';
            include_once BAZAAR_ABSPATH . 'includes/class-bazaar-vendor-dashboard.php';
            include_once BAZAAR_ABSPATH . 'includes/class-bazaar-vendor-store.php';
        }
    }

    /**
     * Hook into actions and filters.
     */
    private function init_hooks() {
        register_activation_hook( BAZAAR_PLUGIN_FILE, array( 'Bazaar_Install', 'install' ) );
        register_deactivation_hook( BAZAAR_PLUGIN_FILE, array( 'Bazaar_Install', 'deactivate' ) );

        add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), -1 );
        add_action( 'init', array( $this, 'init' ), 0 );
        add_action( 'init', array( $this, 'load_textdomain' ) );
    }

    /**
     * When WP has loaded all plugins.
     */
    public function on_plugins_loaded() {
        // Check if WooCommerce is active
        if ( ! class_exists( 'WooCommerce' ) ) {
            add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
            return;
        }

        // Initialize modules
        $this->init_modules();
    }

    /**
     * Initialize modules.
     */
    public function init_modules() {
        $this->vendor     = new Bazaar_Vendor();
        $this->commission = new Bazaar_Commission();
        $this->withdrawal = new Bazaar_Withdrawal();
        $this->orders     = new Bazaar_Orders();
    }

    /**
     * Init Bazaar when WordPress Initialises.
     */
    public function init() {
        // Before init action.
        do_action( 'before_bazaar_init' );

        // Register vendor store rewrite rules
        $this->register_rewrite_rules();

        // Init action.
        do_action( 'bazaar_init' );
    }

    /**
     * Register rewrite rules for vendor stores.
     */
    public function register_rewrite_rules() {
        $store_slug = get_option( 'bazaar_vendor_store_slug', 'store' );
        add_rewrite_rule( '^' . $store_slug . '/([^/]+)/?$', 'index.php?bazaar_store=$matches[1]', 'top' );
        add_rewrite_rule( '^' . $store_slug . '/([^/]+)/page/([0-9]+)/?$', 'index.php?bazaar_store=$matches[1]&paged=$matches[2]', 'top' );
        add_rewrite_tag( '%bazaar_store%', '([^&]+)' );
    }

    /**
     * Load plugin textdomain.
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'bazaar', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * WooCommerce missing notice.
     */
    public function woocommerce_missing_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php esc_html_e( 'Bazaar requires WooCommerce to be installed and active. Please install WooCommerce first.', 'bazaar' ); ?></p>
        </div>
        <?php
    }

    /**
     * Get the plugin path.
     *
     * @return string
     */
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( BAZAAR_PLUGIN_FILE ) );
    }

    /**
     * Get the plugin url.
     *
     * @return string
     */
    public function plugin_url() {
        return untrailingslashit( plugins_url( '/', BAZAAR_PLUGIN_FILE ) );
    }

    /**
     * Get the template path.
     *
     * @return string
     */
    public function template_path() {
        return apply_filters( 'bazaar_template_path', 'bazaar/' );
    }

    /**
     * Get Ajax URL.
     *
     * @return string
     */
    public function ajax_url() {
        return admin_url( 'admin-ajax.php', 'relative' );
    }
}

/**
 * Returns the main instance of Bazaar.
 *
 * @return Bazaar
 */
function bazaar() {
    return Bazaar::instance();
}

// Global for backwards compatibility.
$GLOBALS['bazaar'] = bazaar();
