<?php
/**
 * Admin Menus Class.
 *
 * @package Bazaar\Admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Admin_Menus Class.
 */
class Bazaar_Admin_Menus {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
        add_action( 'admin_head', array( $this, 'menu_highlight' ) );
        add_action( 'admin_head', array( $this, 'add_menu_styles' ) );
    }

    /**
     * Add admin menu items.
     */
    public function admin_menu() {
        // Get pending counts for badges
        $pending_vendors = $this->get_pending_vendors_count();
        $pending_withdrawals = $this->get_pending_withdrawals_count();
        $pending_refunds = $this->get_pending_refunds_count();
        $total_pending = $pending_vendors + $pending_withdrawals + $pending_refunds;

        // Main menu with badge
        $menu_title = __( 'Bazaar', 'bazaar' );
        if ( $total_pending > 0 ) {
            $menu_title .= ' <span class="awaiting-mod update-plugins count-' . $total_pending . '"><span class="pending-count">' . $total_pending . '</span></span>';
        }

        add_menu_page(
            __( 'Bazaar', 'bazaar' ),
            $menu_title,
            'manage_woocommerce',
            'bazaar',
            array( $this, 'dashboard_page' ),
            'dashicons-store',
            55
        );

        // Dashboard submenu
        add_submenu_page(
            'bazaar',
            __( 'Dashboard', 'bazaar' ),
            __( 'Dashboard', 'bazaar' ),
            'manage_woocommerce',
            'bazaar',
            array( $this, 'dashboard_page' )
        );

        // Withdraw
        $withdraw_title = __( 'Withdraw', 'bazaar' );
        if ( $pending_withdrawals > 0 ) {
            $withdraw_title .= ' <span class="awaiting-mod">' . $pending_withdrawals . '</span>';
        }
        add_submenu_page(
            'bazaar',
            __( 'Withdraw', 'bazaar' ),
            $withdraw_title,
            'manage_woocommerce',
            'bazaar-withdraw',
            array( $this, 'withdraw_page' )
        );

        // Reverse Withdrawal
        add_submenu_page(
            'bazaar',
            __( 'Reverse Withdrawal', 'bazaar' ),
            __( 'Reverse Withdrawal', 'bazaar' ),
            'manage_woocommerce',
            'bazaar-reverse-withdrawal',
            array( $this, 'reverse_withdrawal_page' )
        );

        // Vendors
        $vendors_title = __( 'Vendors', 'bazaar' );
        if ( $pending_vendors > 0 ) {
            $vendors_title .= ' <span class="awaiting-mod">' . $pending_vendors . '</span>';
        }
        add_submenu_page(
            'bazaar',
            __( 'Vendors', 'bazaar' ),
            $vendors_title,
            'manage_woocommerce',
            'bazaar-vendors',
            array( $this, 'vendors_page' )
        );

        // Announcements
        add_submenu_page(
            'bazaar',
            __( 'Announcements', 'bazaar' ),
            __( 'Announcements', 'bazaar' ),
            'manage_woocommerce',
            'bazaar-announcements',
            array( $this, 'announcements_page' )
        );

        // Refunds
        $refunds_title = __( 'Refunds', 'bazaar' );
        if ( $pending_refunds > 0 ) {
            $refunds_title .= ' <span class="awaiting-mod">' . $pending_refunds . '</span>';
        }
        add_submenu_page(
            'bazaar',
            __( 'Refunds', 'bazaar' ),
            $refunds_title,
            'manage_woocommerce',
            'bazaar-refunds',
            array( $this, 'refunds_page' )
        );

        // Reports
        add_submenu_page(
            'bazaar',
            __( 'Reports', 'bazaar' ),
            __( 'Reports', 'bazaar' ),
            'manage_woocommerce',
            'bazaar-reports',
            array( $this, 'reports_page' )
        );

        // Modules
        add_submenu_page(
            'bazaar',
            __( 'Modules', 'bazaar' ),
            __( 'Modules', 'bazaar' ),
            'manage_woocommerce',
            'bazaar-modules',
            array( $this, 'modules_page' )
        );

        // Tools
        add_submenu_page(
            'bazaar',
            __( 'Tools', 'bazaar' ),
            __( 'Tools', 'bazaar' ),
            'manage_woocommerce',
            'bazaar-tools',
            array( $this, 'tools_page' )
        );

        // Help
        add_submenu_page(
            'bazaar',
            __( 'Help', 'bazaar' ),
            __( 'Help', 'bazaar' ),
            'manage_woocommerce',
            'bazaar-help',
            array( $this, 'help_page' )
        );

        // Settings
        add_submenu_page(
            'bazaar',
            __( 'Settings', 'bazaar' ),
            __( 'Settings', 'bazaar' ),
            'manage_woocommerce',
            'bazaar-settings',
            array( $this, 'settings_page' )
        );
    }

    /**
     * Highlight parent menu.
     */
    public function menu_highlight() {
        global $parent_file, $submenu_file, $post_type;

        if ( 'product' === $post_type ) {
            if ( isset( $_GET['bazaar_vendor'] ) ) {
                $parent_file = 'bazaar';
                $submenu_file = 'bazaar-vendors';
            }
        }
    }

    /**
     * Add menu styles.
     */
    public function add_menu_styles() {
        ?>
        <style>
            #adminmenu .toplevel_page_bazaar .wp-menu-image:before {
                content: '\f513';
            }
            #adminmenu .toplevel_page_bazaar .awaiting-mod {
                background: #6366f1;
            }
        </style>
        <?php
    }

    /**
     * Get pending vendors count.
     *
     * @return int
     */
    private function get_pending_vendors_count() {
        $vendors = get_users( array(
            'role'       => 'bazaar_vendor',
            'meta_key'   => '_bazaar_vendor_status',
            'meta_value' => 'pending',
            'fields'     => 'ID',
        ) );
        return count( $vendors );
    }

    /**
     * Get pending withdrawals count.
     *
     * @return int
     */
    private function get_pending_withdrawals_count() {
        global $wpdb;
        $table = $wpdb->prefix . 'bazaar_withdrawals';
        $table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" );
        if ( ! $table_exists ) {
            return 0;
        }
        return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'pending'" );
    }

    /**
     * Get pending refunds count.
     *
     * @return int
     */
    private function get_pending_refunds_count() {
        global $wpdb;
        $table = $wpdb->prefix . 'bazaar_refunds';
        $table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" );
        if ( ! $table_exists ) {
            return 0;
        }
        return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'pending'" );
    }

    /**
     * Dashboard page.
     */
    public function dashboard_page() {
        include BAZAAR_ABSPATH . 'admin/views/html-admin-dashboard.php';
    }

    /**
     * Withdraw page.
     */
    public function withdraw_page() {
        include BAZAAR_ABSPATH . 'admin/views/html-admin-withdraw.php';
    }

    /**
     * Reverse Withdrawal page.
     */
    public function reverse_withdrawal_page() {
        include BAZAAR_ABSPATH . 'admin/views/html-admin-reverse-withdrawal.php';
    }

    /**
     * Vendors page.
     */
    public function vendors_page() {
        $action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : 'list';
        $vendor_id = isset( $_GET['vendor'] ) ? intval( $_GET['vendor'] ) : 0;

        if ( 'edit' === $action && $vendor_id ) {
            include BAZAAR_ABSPATH . 'admin/views/html-admin-vendor-edit.php';
        } else {
            include BAZAAR_ABSPATH . 'admin/views/html-admin-vendors.php';
        }
    }

    /**
     * Announcements page.
     */
    public function announcements_page() {
        include BAZAAR_ABSPATH . 'admin/views/html-admin-announcements.php';
    }

    /**
     * Refunds page.
     */
    public function refunds_page() {
        include BAZAAR_ABSPATH . 'admin/views/html-admin-refunds.php';
    }

    /**
     * Reports page.
     */
    public function reports_page() {
        include BAZAAR_ABSPATH . 'admin/views/html-admin-reports.php';
    }

    /**
     * Modules page.
     */
    public function modules_page() {
        include BAZAAR_ABSPATH . 'admin/views/html-admin-modules.php';
    }

    /**
     * Tools page.
     */
    public function tools_page() {
        include BAZAAR_ABSPATH . 'admin/views/html-admin-tools.php';
    }

    /**
     * Help page.
     */
    public function help_page() {
        include BAZAAR_ABSPATH . 'admin/views/html-admin-help.php';
    }

    /**
     * Settings page.
     */
    public function settings_page() {
        include BAZAAR_ABSPATH . 'admin/views/html-admin-settings.php';
    }
}

// Initialize
new Bazaar_Admin_Menus();
