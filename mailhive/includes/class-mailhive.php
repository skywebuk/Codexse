<?php
/**
 * Main MailHive Class
 *
 * @package MailHive
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main MailHive class.
 */
final class MailHive {

    /**
     * The single instance of the class.
     *
     * @var MailHive
     */
    protected static $instance = null;

    /**
     * Admin instance.
     *
     * @var MailHive_Admin
     */
    public $admin;

    /**
     * AJAX instance.
     *
     * @var MailHive_Ajax
     */
    public $ajax;

    /**
     * Subscriber instance.
     *
     * @var MailHive_Subscriber
     */
    public $subscriber;

    /**
     * Main MailHive Instance.
     *
     * @return MailHive
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    public function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Include required files.
     */
    private function includes() {
        require_once MAILHIVE_PLUGIN_DIR . 'includes/class-mailhive-subscriber.php';
        require_once MAILHIVE_PLUGIN_DIR . 'includes/class-mailhive-ajax.php';

        if ( is_admin() ) {
            require_once MAILHIVE_PLUGIN_DIR . 'includes/class-mailhive-admin.php';
        }
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks() {
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
        add_shortcode( 'mailhive_form', array( $this, 'render_form_shortcode' ) );
    }

    /**
     * Initialize plugin.
     */
    public function init() {
        $this->subscriber = new MailHive_Subscriber();
        $this->ajax = new MailHive_Ajax();

        if ( is_admin() ) {
            $this->admin = new MailHive_Admin();
        }
    }

    /**
     * Enqueue frontend assets.
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'mailhive-public',
            MAILHIVE_PLUGIN_URL . 'public/css/mailhive-public.css',
            array(),
            MAILHIVE_VERSION
        );

        wp_enqueue_script(
            'mailhive-public',
            MAILHIVE_PLUGIN_URL . 'public/js/mailhive-public.js',
            array( 'jquery' ),
            MAILHIVE_VERSION,
            true
        );

        wp_localize_script( 'mailhive-public', 'mailhive_params', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'mailhive_subscribe_nonce' ),
        ) );
    }

    /**
     * Render form shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function render_form_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'id' => 'default',
        ), $atts, 'mailhive_form' );

        $form_markup = get_option( 'mailhive_form_markup', $this->get_default_form_markup() );
        $custom_css = get_option( 'mailhive_form_css', '' );

        ob_start();
        ?>
        <div class="mailhive-form-wrapper" data-form-id="<?php echo esc_attr( $atts['id'] ); ?>">
            <?php if ( ! empty( $custom_css ) ) : ?>
                <style><?php echo wp_strip_all_tags( $custom_css ); ?></style>
            <?php endif; ?>
            <form class="mailhive-form" method="post">
                <?php echo wp_kses_post( $form_markup ); ?>
                <input type="hidden" name="mailhive_form_id" value="<?php echo esc_attr( $atts['id'] ); ?>">
            </form>
            <div class="mailhive-message" style="display: none;"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Get default form markup.
     *
     * @return string
     */
    public function get_default_form_markup() {
        return '<div class="mailhive-field">
    <label for="mailhive-email">Email Address</label>
    <input type="email" id="mailhive-email" name="email" placeholder="Enter your email" required>
</div>
<div class="mailhive-field">
    <button type="submit" class="mailhive-submit">Subscribe</button>
</div>';
    }

    /**
     * Plugin activation.
     */
    public static function activate() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'mailhive_subscribers';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            email varchar(255) NOT NULL,
            name varchar(255) DEFAULT '',
            custom_fields longtext DEFAULT '',
            status varchar(20) DEFAULT 'subscribed',
            ip_address varchar(45) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY email (email),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        // Set default options
        $default_options = array(
            'mailhive_success_message'   => 'Thank you for subscribing!',
            'mailhive_error_message'     => 'An error occurred. Please try again.',
            'mailhive_duplicate_message' => 'This email is already subscribed.',
        );

        foreach ( $default_options as $key => $value ) {
            if ( false === get_option( $key ) ) {
                add_option( $key, $value );
            }
        }

        update_option( 'mailhive_version', MAILHIVE_VERSION );
    }

    /**
     * Plugin deactivation.
     */
    public static function deactivate() {
        // Clean up if needed
    }
}
