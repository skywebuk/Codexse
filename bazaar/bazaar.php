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
define( 'BAZAAR_MIN_WC_VERSION', '5.0.0' );
define( 'BAZAAR_MIN_PHP_VERSION', '7.4' );
define( 'BAZAAR_MIN_WP_VERSION', '5.8' );

/**
 * Check if WooCommerce is active.
 *
 * @return bool
 */
function bazaar_is_woocommerce_active() {
    $active_plugins = (array) get_option( 'active_plugins', array() );

    if ( is_multisite() ) {
        $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
    }

    return in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
}

/**
 * Check plugin requirements.
 *
 * @return bool|WP_Error
 */
function bazaar_check_requirements() {
    $errors = array();

    // Check PHP version
    if ( version_compare( PHP_VERSION, BAZAAR_MIN_PHP_VERSION, '<' ) ) {
        $errors[] = sprintf(
            /* translators: 1: Current PHP version, 2: Required PHP version */
            __( 'Bazaar requires PHP version %2$s or higher. Your current version is %1$s.', 'bazaar' ),
            PHP_VERSION,
            BAZAAR_MIN_PHP_VERSION
        );
    }

    // Check WordPress version
    if ( version_compare( get_bloginfo( 'version' ), BAZAAR_MIN_WP_VERSION, '<' ) ) {
        $errors[] = sprintf(
            /* translators: 1: Current WP version, 2: Required WP version */
            __( 'Bazaar requires WordPress version %2$s or higher. Your current version is %1$s.', 'bazaar' ),
            get_bloginfo( 'version' ),
            BAZAAR_MIN_WP_VERSION
        );
    }

    // Check if WooCommerce is active
    if ( ! bazaar_is_woocommerce_active() ) {
        $errors[] = __( 'Bazaar requires WooCommerce to be installed and activated.', 'bazaar' );
    }

    // Check WooCommerce version (only if WooCommerce is active)
    if ( bazaar_is_woocommerce_active() && defined( 'WC_VERSION' ) && version_compare( WC_VERSION, BAZAAR_MIN_WC_VERSION, '<' ) ) {
        $errors[] = sprintf(
            /* translators: 1: Current WC version, 2: Required WC version */
            __( 'Bazaar requires WooCommerce version %2$s or higher. Your current version is %1$s.', 'bazaar' ),
            WC_VERSION,
            BAZAAR_MIN_WC_VERSION
        );
    }

    if ( ! empty( $errors ) ) {
        return new WP_Error( 'bazaar_requirements', implode( '<br>', $errors ) );
    }

    return true;
}

/**
 * Activation check - prevents activation if requirements not met.
 */
function bazaar_activation_check() {
    // Load plugin textdomain for translation
    load_plugin_textdomain( 'bazaar', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    // Check PHP version first (can't use WP functions if PHP is too old)
    if ( version_compare( PHP_VERSION, BAZAAR_MIN_PHP_VERSION, '<' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die(
            sprintf(
                /* translators: 1: Current PHP version, 2: Required PHP version */
                esc_html__( 'Bazaar requires PHP version %2$s or higher. Your current version is %1$s. Please upgrade PHP to activate this plugin.', 'bazaar' ),
                PHP_VERSION,
                BAZAAR_MIN_PHP_VERSION
            ),
            esc_html__( 'Plugin Activation Error', 'bazaar' ),
            array( 'back_link' => true )
        );
    }

    // Check WordPress version
    if ( version_compare( get_bloginfo( 'version' ), BAZAAR_MIN_WP_VERSION, '<' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die(
            sprintf(
                /* translators: 1: Current WP version, 2: Required WP version */
                esc_html__( 'Bazaar requires WordPress version %2$s or higher. Your current version is %1$s. Please upgrade WordPress to activate this plugin.', 'bazaar' ),
                get_bloginfo( 'version' ),
                BAZAAR_MIN_WP_VERSION
            ),
            esc_html__( 'Plugin Activation Error', 'bazaar' ),
            array( 'back_link' => true )
        );
    }

    // Check if WooCommerce is active
    if ( ! bazaar_is_woocommerce_active() ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die(
            sprintf(
                /* translators: %s: WooCommerce install URL */
                __( 'Bazaar requires WooCommerce to be installed and activated. <a href="%s">Install WooCommerce</a>', 'bazaar' ),
                esc_url( admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) )
            ),
            esc_html__( 'Plugin Activation Error', 'bazaar' ),
            array( 'back_link' => true )
        );
    }
}
register_activation_hook( __FILE__, 'bazaar_activation_check' );

/**
 * Deactivate Bazaar if WooCommerce is deactivated.
 *
 * @param string $plugin Plugin being deactivated.
 */
function bazaar_woocommerce_deactivated( $plugin ) {
    if ( 'woocommerce/woocommerce.php' === $plugin ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        add_action( 'admin_notices', 'bazaar_woocommerce_deactivated_notice' );
    }
}
add_action( 'deactivated_plugin', 'bazaar_woocommerce_deactivated' );

/**
 * Show notice when Bazaar is deactivated due to WooCommerce deactivation.
 */
function bazaar_woocommerce_deactivated_notice() {
    ?>
    <div class="notice notice-warning is-dismissible">
        <p><?php esc_html_e( 'Bazaar has been deactivated because WooCommerce was deactivated. Please reactivate WooCommerce first, then reactivate Bazaar.', 'bazaar' ); ?></p>
    </div>
    <?php
}

// Don't proceed if WooCommerce is not active
if ( ! bazaar_is_woocommerce_active() ) {
    add_action( 'admin_notices', 'bazaar_woocommerce_missing_notice' );
    return;
}

/**
 * Show admin notice when WooCommerce is not active.
 */
function bazaar_woocommerce_missing_notice() {
    ?>
    <div class="notice notice-error">
        <p>
            <?php
            printf(
                /* translators: %s: WooCommerce install URL */
                __( '<strong>Bazaar</strong> requires WooCommerce to be installed and activated. <a href="%s">Install WooCommerce</a>', 'bazaar' ),
                esc_url( admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) )
            );
            ?>
        </p>
    </div>
    <?php
}

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
        // Double-check WooCommerce is available (class should exist at this point)
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        // Check WooCommerce version
        if ( version_compare( WC_VERSION, BAZAAR_MIN_WC_VERSION, '<' ) ) {
            add_action( 'admin_notices', array( $this, 'woocommerce_version_notice' ) );
            return;
        }

        // Initialize modules
        $this->init_modules();

        // Declare compatibility with WooCommerce HPOS
        add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );
    }

    /**
     * Declare compatibility with WooCommerce High-Performance Order Storage.
     */
    public function declare_hpos_compatibility() {
        if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', BAZAAR_PLUGIN_FILE, true );
        }
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
     * WooCommerce version notice.
     */
    public function woocommerce_version_notice() {
        ?>
        <div class="notice notice-error">
            <p>
                <?php
                printf(
                    /* translators: 1: Current WC version, 2: Required WC version */
                    esc_html__( 'Bazaar requires WooCommerce version %2$s or higher. Your current version is %1$s. Please update WooCommerce.', 'bazaar' ),
                    esc_html( WC_VERSION ),
                    esc_html( BAZAAR_MIN_WC_VERSION )
                );
                ?>
            </p>
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
