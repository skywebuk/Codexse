<?php
/**
 * Installation related functions and actions.
 *
 * @package Bazaar\Install
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Install Class.
 */
class Bazaar_Install {

    /**
     * Install Bazaar.
     */
    public static function install() {
        if ( ! is_blog_installed() ) {
            return;
        }

        // Check if already installing
        if ( get_transient( 'bazaar_installing' ) === 'yes' ) {
            return;
        }

        set_transient( 'bazaar_installing', 'yes', MINUTE_IN_SECONDS * 10 );

        self::create_tables();
        self::create_roles();
        self::create_options();
        self::create_pages();
        self::setup_rewrite_rules();

        delete_transient( 'bazaar_installing' );

        // Update version
        update_option( 'bazaar_version', BAZAAR_VERSION );

        do_action( 'bazaar_installed' );
    }

    /**
     * Deactivate plugin.
     */
    public static function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * Create database tables.
     */
    private static function create_tables() {
        global $wpdb;

        $wpdb->hide_errors();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();

        $tables = array();

        // Vendor withdrawals table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bazaar_withdrawals (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            vendor_id bigint(20) UNSIGNED NOT NULL,
            amount decimal(19,4) NOT NULL DEFAULT '0.0000',
            method varchar(100) NOT NULL DEFAULT '',
            method_details longtext,
            status varchar(20) NOT NULL DEFAULT 'pending',
            note text,
            admin_note text,
            ip_address varchar(100) DEFAULT '',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY vendor_id (vendor_id),
            KEY status (status)
        ) $charset_collate;";

        // Vendor earnings/transactions table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bazaar_vendor_balance (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            vendor_id bigint(20) UNSIGNED NOT NULL,
            order_id bigint(20) UNSIGNED DEFAULT NULL,
            order_item_id bigint(20) UNSIGNED DEFAULT NULL,
            product_id bigint(20) UNSIGNED DEFAULT NULL,
            trn_type varchar(30) NOT NULL DEFAULT 'credit',
            trn_status varchar(30) NOT NULL DEFAULT 'pending',
            amount decimal(19,4) NOT NULL DEFAULT '0.0000',
            commission decimal(19,4) NOT NULL DEFAULT '0.0000',
            commission_rate decimal(5,2) NOT NULL DEFAULT '0.00',
            net_amount decimal(19,4) NOT NULL DEFAULT '0.0000',
            note text,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY vendor_id (vendor_id),
            KEY order_id (order_id),
            KEY trn_type (trn_type),
            KEY trn_status (trn_status)
        ) $charset_collate;";

        // Sub-orders table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bazaar_sub_orders (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            parent_order_id bigint(20) UNSIGNED NOT NULL,
            vendor_id bigint(20) UNSIGNED NOT NULL,
            order_total decimal(19,4) NOT NULL DEFAULT '0.0000',
            commission decimal(19,4) NOT NULL DEFAULT '0.0000',
            vendor_earning decimal(19,4) NOT NULL DEFAULT '0.0000',
            status varchar(50) NOT NULL DEFAULT 'pending',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY parent_order_id (parent_order_id),
            KEY vendor_id (vendor_id)
        ) $charset_collate;";

        // Vendor reviews table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bazaar_vendor_reviews (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            vendor_id bigint(20) UNSIGNED NOT NULL,
            customer_id bigint(20) UNSIGNED NOT NULL,
            order_id bigint(20) UNSIGNED DEFAULT NULL,
            rating tinyint(1) NOT NULL DEFAULT '5',
            title varchar(255) DEFAULT '',
            content text,
            status varchar(20) NOT NULL DEFAULT 'pending',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY vendor_id (vendor_id),
            KEY customer_id (customer_id),
            KEY status (status)
        ) $charset_collate;";

        // Vendor shipping zones table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bazaar_vendor_shipping (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            vendor_id bigint(20) UNSIGNED NOT NULL,
            zone_name varchar(255) NOT NULL DEFAULT '',
            zone_locations longtext,
            methods longtext,
            is_enabled tinyint(1) NOT NULL DEFAULT '1',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY vendor_id (vendor_id)
        ) $charset_collate;";

        // Vendor notifications table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bazaar_notifications (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            type varchar(50) NOT NULL DEFAULT '',
            title varchar(255) NOT NULL DEFAULT '',
            message text,
            data longtext,
            is_read tinyint(1) NOT NULL DEFAULT '0',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY type (type),
            KEY is_read (is_read)
        ) $charset_collate;";

        foreach ( $tables as $table ) {
            dbDelta( $table );
        }
    }

    /**
     * Create user roles.
     */
    private static function create_roles() {
        global $wp_roles;

        if ( ! class_exists( 'WP_Roles' ) ) {
            return;
        }

        if ( ! isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }

        // Vendor role
        add_role(
            'bazaar_vendor',
            __( 'Vendor', 'bazaar' ),
            array(
                'read'                   => true,
                'edit_posts'             => false,
                'delete_posts'           => false,
                'upload_files'           => true,
                // Vendor specific capabilities
                'bazaar_manage_store'    => true,
                'bazaar_add_product'     => true,
                'bazaar_edit_product'    => true,
                'bazaar_delete_product'  => true,
                'bazaar_view_orders'     => true,
                'bazaar_manage_orders'   => true,
                'bazaar_view_earnings'   => true,
                'bazaar_withdraw'        => true,
                'bazaar_manage_coupons'  => true,
                'bazaar_view_reports'    => true,
            )
        );

        // Add vendor capabilities to admin
        $admin_caps = array(
            'bazaar_manage_marketplace',
            'bazaar_manage_vendors',
            'bazaar_manage_commissions',
            'bazaar_manage_withdrawals',
            'bazaar_manage_all_products',
            'bazaar_view_reports',
            'bazaar_manage_settings',
            'bazaar_moderate_products',
            'bazaar_moderate_reviews',
        );

        foreach ( $admin_caps as $cap ) {
            $wp_roles->add_cap( 'administrator', $cap );
        }

        // Also add vendor caps to admin for testing
        $vendor_caps = array(
            'bazaar_manage_store',
            'bazaar_add_product',
            'bazaar_edit_product',
            'bazaar_delete_product',
            'bazaar_view_orders',
            'bazaar_manage_orders',
            'bazaar_view_earnings',
            'bazaar_withdraw',
            'bazaar_manage_coupons',
        );

        foreach ( $vendor_caps as $cap ) {
            $wp_roles->add_cap( 'administrator', $cap );
        }
    }

    /**
     * Create default options.
     */
    private static function create_options() {
        $default_options = array(
            'bazaar_vendor_store_slug'         => 'store',
            'bazaar_vendor_registration'       => 'enabled',
            'bazaar_vendor_approval'           => 'manual',
            'bazaar_global_commission_type'    => 'percentage',
            'bazaar_global_commission_rate'    => '10',
            'bazaar_min_withdrawal_amount'     => '50',
            'bazaar_withdrawal_methods'        => array( 'paypal', 'bank_transfer' ),
            'bazaar_product_moderation'        => 'enabled',
            'bazaar_vendor_product_types'      => array( 'simple', 'variable', 'external', 'grouped' ),
            'bazaar_vendor_dashboard_page'     => '',
            'bazaar_vendor_store_page'         => '',
            'bazaar_vendor_registration_page'  => '',
            'bazaar_order_split_enabled'       => 'yes',
            'bazaar_vendor_shipping_enabled'   => 'yes',
            'bazaar_vendor_coupon_enabled'     => 'yes',
            'bazaar_vendor_review_enabled'     => 'yes',
            'bazaar_vacation_mode_enabled'     => 'yes',
            'bazaar_email_notifications'       => 'yes',
        );

        foreach ( $default_options as $key => $value ) {
            if ( false === get_option( $key ) ) {
                add_option( $key, $value );
            }
        }
    }

    /**
     * Create pages.
     */
    private static function create_pages() {
        $pages = array(
            'vendor_dashboard' => array(
                'title'   => __( 'Vendor Dashboard', 'bazaar' ),
                'content' => '[bazaar_vendor_dashboard]',
                'option'  => 'bazaar_vendor_dashboard_page',
            ),
            'vendor_registration' => array(
                'title'   => __( 'Become a Vendor', 'bazaar' ),
                'content' => '[bazaar_vendor_registration]',
                'option'  => 'bazaar_vendor_registration_page',
            ),
            'store_listing' => array(
                'title'   => __( 'Stores', 'bazaar' ),
                'content' => '[bazaar_store_listing]',
                'option'  => 'bazaar_store_listing_page',
            ),
        );

        foreach ( $pages as $key => $page ) {
            $page_id = get_option( $page['option'] );

            if ( $page_id && get_post( $page_id ) ) {
                continue;
            }

            $page_id = wp_insert_post(
                array(
                    'post_title'     => $page['title'],
                    'post_content'   => $page['content'],
                    'post_status'    => 'publish',
                    'post_type'      => 'page',
                    'comment_status' => 'closed',
                )
            );

            if ( $page_id && ! is_wp_error( $page_id ) ) {
                update_option( $page['option'], $page_id );
            }
        }
    }

    /**
     * Setup rewrite rules.
     */
    private static function setup_rewrite_rules() {
        flush_rewrite_rules();
    }
}
