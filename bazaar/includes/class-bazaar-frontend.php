<?php
/**
 * Frontend Class.
 *
 * @package Bazaar\Frontend
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Frontend Class.
 */
class Bazaar_Frontend {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_filter( 'body_class', array( $this, 'body_class' ) );
    }

    /**
     * Enqueue scripts.
     */
    public function enqueue_scripts() {
        // Register scripts
        wp_register_script(
            'bazaar-frontend',
            BAZAAR_PLUGIN_URL . 'assets/js/frontend.js',
            array( 'jquery' ),
            BAZAAR_VERSION,
            true
        );

        wp_register_script(
            'bazaar-dashboard',
            BAZAAR_PLUGIN_URL . 'assets/js/dashboard.js',
            array( 'jquery', 'jquery-ui-datepicker' ),
            BAZAAR_VERSION,
            true
        );

        // Localize scripts
        wp_localize_script(
            'bazaar-frontend',
            'bazaar_params',
            array(
                'ajax_url'           => admin_url( 'admin-ajax.php' ),
                'nonce'              => wp_create_nonce( 'bazaar_frontend' ),
                'i18n'               => array(
                    'loading'        => __( 'Loading...', 'bazaar' ),
                    'error'          => __( 'An error occurred.', 'bazaar' ),
                    'confirm_delete' => __( 'Are you sure you want to delete this?', 'bazaar' ),
                ),
            )
        );

        wp_localize_script(
            'bazaar-dashboard',
            'bazaar_dashboard',
            array(
                'ajax_url'                  => admin_url( 'admin-ajax.php' ),
                'vendor_profile_nonce'      => wp_create_nonce( 'bazaar_vendor_profile' ),
                'save_product_nonce'        => wp_create_nonce( 'bazaar_save_product' ),
                'delete_product_nonce'      => wp_create_nonce( 'bazaar_delete_product' ),
                'update_order_nonce'        => wp_create_nonce( 'bazaar_update_order' ),
                'withdrawal_nonce'          => wp_create_nonce( 'bazaar_withdrawal' ),
                'shipping_nonce'            => wp_create_nonce( 'bazaar_shipping' ),
                'vacation_nonce'            => wp_create_nonce( 'bazaar_vacation' ),
                'notifications_nonce'       => wp_create_nonce( 'bazaar_notifications' ),
                'i18n'                      => array(
                    'loading'               => __( 'Loading...', 'bazaar' ),
                    'saving'                => __( 'Saving...', 'bazaar' ),
                    'error'                 => __( 'An error occurred.', 'bazaar' ),
                    'success'               => __( 'Success!', 'bazaar' ),
                    'confirm_delete'        => __( 'Are you sure you want to delete this?', 'bazaar' ),
                    'select_image'          => __( 'Select Image', 'bazaar' ),
                    'use_image'             => __( 'Use Image', 'bazaar' ),
                ),
                'currency_symbol'           => get_woocommerce_currency_symbol(),
                'currency_position'         => get_option( 'woocommerce_currency_pos' ),
            )
        );

        // Enqueue on relevant pages
        if ( bazaar_is_store_page() || is_page( get_option( 'bazaar_store_listing_page' ) ) ) {
            wp_enqueue_script( 'bazaar-frontend' );
        }
    }

    /**
     * Enqueue styles.
     */
    public function enqueue_styles() {
        // Register styles
        wp_register_style(
            'bazaar-frontend',
            BAZAAR_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            BAZAAR_VERSION
        );

        wp_register_style(
            'bazaar-dashboard',
            BAZAAR_PLUGIN_URL . 'assets/css/dashboard.css',
            array( 'dashicons' ),
            BAZAAR_VERSION
        );

        // Enqueue on relevant pages
        if ( bazaar_is_store_page() || is_page( get_option( 'bazaar_store_listing_page' ) ) ) {
            wp_enqueue_style( 'bazaar-frontend' );
        }
    }

    /**
     * Add body classes.
     *
     * @param array $classes Body classes.
     * @return array
     */
    public function body_class( $classes ) {
        if ( bazaar_is_store_page() ) {
            $classes[] = 'bazaar-store-page';

            $vendor_id = bazaar_get_current_store_vendor_id();

            if ( $vendor_id && Bazaar_Vendor::is_on_vacation( $vendor_id ) ) {
                $classes[] = 'bazaar-store-vacation';
            }
        }

        if ( is_page( get_option( 'bazaar_vendor_dashboard_page' ) ) ) {
            $classes[] = 'bazaar-dashboard-page';
        }

        return $classes;
    }
}

// Initialize
new Bazaar_Frontend();
