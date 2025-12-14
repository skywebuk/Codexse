<?php
/**
 * Admin Class.
 *
 * @package Bazaar\Admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Admin Class.
 */
class Bazaar_Admin {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_filter( 'plugin_action_links_' . BAZAAR_PLUGIN_BASENAME, array( $this, 'plugin_action_links' ) );
    }

    /**
     * Enqueue admin scripts.
     *
     * @param string $hook Current admin page.
     */
    public function enqueue_scripts( $hook ) {
        $screen = get_current_screen();

        // Only on Bazaar admin pages
        if ( ! $this->is_bazaar_admin_page( $hook ) ) {
            return;
        }

        wp_enqueue_script(
            'bazaar-admin',
            BAZAAR_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery', 'jquery-ui-datepicker' ),
            BAZAAR_VERSION,
            true
        );

        wp_localize_script(
            'bazaar-admin',
            'bazaar_admin',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'bazaar_admin' ),
                'i18n'     => array(
                    'confirm'        => __( 'Are you sure?', 'bazaar' ),
                    'processing'     => __( 'Processing...', 'bazaar' ),
                    'success'        => __( 'Success!', 'bazaar' ),
                    'error'          => __( 'An error occurred.', 'bazaar' ),
                    'approve'        => __( 'Approve', 'bazaar' ),
                    'reject'         => __( 'Reject', 'bazaar' ),
                    'approve_confirm' => __( 'Are you sure you want to approve this?', 'bazaar' ),
                    'reject_confirm' => __( 'Are you sure you want to reject this?', 'bazaar' ),
                ),
            )
        );

        // Chart.js for reports
        if ( strpos( $hook, 'bazaar-reports' ) !== false ) {
            wp_enqueue_script(
                'chartjs',
                'https://cdn.jsdelivr.net/npm/chart.js',
                array(),
                '4.4.0',
                true
            );
        }
    }

    /**
     * Enqueue admin styles.
     *
     * @param string $hook Current admin page.
     */
    public function enqueue_styles( $hook ) {
        if ( ! $this->is_bazaar_admin_page( $hook ) ) {
            return;
        }

        wp_enqueue_style(
            'bazaar-admin',
            BAZAAR_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            BAZAAR_VERSION
        );

        wp_enqueue_style( 'wp-jquery-ui-dialog' );
    }

    /**
     * Check if current page is Bazaar admin page.
     *
     * @param string $hook Current hook.
     * @return bool
     */
    private function is_bazaar_admin_page( $hook ) {
        $bazaar_pages = array(
            'toplevel_page_bazaar',
            'bazaar_page_bazaar-vendors',
            'bazaar_page_bazaar-withdrawals',
            'bazaar_page_bazaar-reports',
            'bazaar_page_bazaar-settings',
        );

        return in_array( $hook, $bazaar_pages, true );
    }

    /**
     * Add plugin action links.
     *
     * @param array $links Existing links.
     * @return array
     */
    public function plugin_action_links( $links ) {
        $plugin_links = array(
            '<a href="' . admin_url( 'admin.php?page=bazaar-settings' ) . '">' . __( 'Settings', 'bazaar' ) . '</a>',
        );

        return array_merge( $plugin_links, $links );
    }

    /**
     * Get admin notice HTML.
     *
     * @param string $message Message.
     * @param string $type    Notice type.
     * @return string
     */
    public static function get_notice( $message, $type = 'info' ) {
        return sprintf(
            '<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
            esc_attr( $type ),
            wp_kses_post( $message )
        );
    }
}

// Initialize
new Bazaar_Admin();
