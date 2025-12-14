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
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_head', array( $this, 'menu_highlight' ) );
    }

    /**
     * Add admin menu items.
     */
    public function admin_menu() {
        // Main menu
        add_menu_page(
            __( 'Bazaar', 'bazaar' ),
            __( 'Bazaar', 'bazaar' ),
            'bazaar_manage_marketplace',
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
            'bazaar_manage_marketplace',
            'bazaar',
            array( $this, 'dashboard_page' )
        );

        // Vendors
        add_submenu_page(
            'bazaar',
            __( 'Vendors', 'bazaar' ),
            __( 'Vendors', 'bazaar' ),
            'bazaar_manage_vendors',
            'bazaar-vendors',
            array( $this, 'vendors_page' )
        );

        // Withdrawals
        add_submenu_page(
            'bazaar',
            __( 'Withdrawals', 'bazaar' ),
            __( 'Withdrawals', 'bazaar' ),
            'bazaar_manage_withdrawals',
            'bazaar-withdrawals',
            array( $this, 'withdrawals_page' )
        );

        // Reports
        add_submenu_page(
            'bazaar',
            __( 'Reports', 'bazaar' ),
            __( 'Reports', 'bazaar' ),
            'bazaar_view_reports',
            'bazaar-reports',
            array( $this, 'reports_page' )
        );

        // Settings
        add_submenu_page(
            'bazaar',
            __( 'Settings', 'bazaar' ),
            __( 'Settings', 'bazaar' ),
            'bazaar_manage_settings',
            'bazaar-settings',
            array( $this, 'settings_page' )
        );
    }

    /**
     * Highlight parent menu.
     */
    public function menu_highlight() {
        global $parent_file, $submenu_file, $post_type;

        // Highlight Bazaar menu for relevant pages
        if ( 'product' === $post_type ) {
            if ( isset( $_GET['bazaar_vendor'] ) ) {
                $parent_file = 'bazaar';
                $submenu_file = 'bazaar-vendors';
            }
        }
    }

    /**
     * Dashboard page.
     */
    public function dashboard_page() {
        // Get statistics
        $stats = $this->get_dashboard_stats();

        include BAZAAR_ABSPATH . 'admin/views/html-admin-dashboard.php';
    }

    /**
     * Vendors page.
     */
    public function vendors_page() {
        // Get vendors list or single vendor
        $action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : 'list';
        $vendor_id = isset( $_GET['vendor'] ) ? intval( $_GET['vendor'] ) : 0;

        if ( 'view' === $action && $vendor_id ) {
            $vendor = Bazaar_Vendor::get_vendor( $vendor_id );
            include BAZAAR_ABSPATH . 'admin/views/html-admin-vendor-view.php';
        } else {
            $status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
            $paged = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1;
            $search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

            $args = array(
                'number' => 20,
                'paged'  => $paged,
            );

            if ( $status ) {
                $args['status'] = $status;
            }

            if ( $search ) {
                $args['search'] = $search;
            }

            $vendors = Bazaar_Vendor::get_vendors( $args );

            include BAZAAR_ABSPATH . 'admin/views/html-admin-vendors.php';
        }
    }

    /**
     * Withdrawals page.
     */
    public function withdrawals_page() {
        $status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
        $paged = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1;

        $withdrawals = Bazaar_Withdrawal::get_all_withdrawals(
            array(
                'status'   => $status,
                'per_page' => 20,
                'page'     => $paged,
            )
        );

        $stats = Bazaar_Withdrawal::get_statistics();

        include BAZAAR_ABSPATH . 'admin/views/html-admin-withdrawals.php';
    }

    /**
     * Reports page.
     */
    public function reports_page() {
        $report_type = isset( $_GET['report'] ) ? sanitize_text_field( wp_unslash( $_GET['report'] ) ) : 'overview';
        $range = isset( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : 'this_month';

        $date_range = bazaar_get_date_range( $range );
        $report_data = Bazaar_Admin_Reports::get_report_data( $report_type, $date_range );

        include BAZAAR_ABSPATH . 'admin/views/html-admin-reports.php';
    }

    /**
     * Settings page.
     */
    public function settings_page() {
        $tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';

        // Handle save
        if ( isset( $_POST['bazaar_save_settings'] ) ) {
            check_admin_referer( 'bazaar_settings' );
            Bazaar_Admin_Settings::save_settings( $tab );
            echo Bazaar_Admin::get_notice( __( 'Settings saved.', 'bazaar' ), 'success' );
        }

        $tabs = Bazaar_Admin_Settings::get_tabs();
        $settings = Bazaar_Admin_Settings::get_settings( $tab );

        include BAZAAR_ABSPATH . 'admin/views/html-admin-settings.php';
    }

    /**
     * Get dashboard statistics.
     *
     * @return array
     */
    private function get_dashboard_stats() {
        $vendors = Bazaar_Vendor::get_vendors( array( 'number' => -1 ) );
        $pending_vendors = Bazaar_Vendor::get_vendors( array( 'status' => 'pending', 'number' => -1 ) );

        $date_range = bazaar_get_date_range( 'this_month' );
        $commission_report = Bazaar_Commission::get_admin_report( $date_range['start'], $date_range['end'] );

        $withdrawal_stats = Bazaar_Withdrawal::get_statistics();

        // Pending products
        $pending_products = new WP_Query(
            array(
                'post_type'      => 'product',
                'post_status'    => 'pending',
                'posts_per_page' => -1,
                'fields'         => 'ids',
            )
        );

        return array(
            'total_vendors'      => $vendors['total'],
            'pending_vendors'    => $pending_vendors['total'],
            'gross_sales'        => $commission_report['gross_sales'],
            'admin_commission'   => $commission_report['admin_commission'],
            'vendor_earnings'    => $commission_report['vendor_earnings'],
            'total_orders'       => $commission_report['total_orders'],
            'pending_withdrawals' => $withdrawal_stats['pending_count'],
            'pending_withdrawal_amount' => $withdrawal_stats['pending_amount'],
            'pending_products'   => $pending_products->found_posts,
        );
    }
}

// Initialize
new Bazaar_Admin_Menus();
