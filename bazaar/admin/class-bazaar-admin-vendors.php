<?php
/**
 * Admin Vendors Management Class.
 *
 * @package Bazaar\Admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Admin_Vendors Class.
 */
class Bazaar_Admin_Vendors {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'admin_init', array( $this, 'handle_vendor_actions' ) );
    }

    /**
     * Handle vendor actions (approve, reject, etc.).
     */
    public function handle_vendor_actions() {
        if ( ! isset( $_GET['page'] ) || 'bazaar-vendors' !== $_GET['page'] ) {
            return;
        }

        if ( ! isset( $_GET['action'] ) || ! isset( $_GET['vendor'] ) ) {
            return;
        }

        $action = sanitize_text_field( wp_unslash( $_GET['action'] ) );
        $vendor_id = intval( $_GET['vendor'] );

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ?? '' ) ), 'bazaar_vendor_action' ) ) {
            return;
        }

        if ( ! current_user_can( 'bazaar_manage_vendors' ) ) {
            return;
        }

        switch ( $action ) {
            case 'approve':
                Bazaar_Roles::update_vendor_status( $vendor_id, 'approved' );
                $redirect_url = add_query_arg(
                    array(
                        'page'    => 'bazaar-vendors',
                        'message' => 'approved',
                    ),
                    admin_url( 'admin.php' )
                );
                break;

            case 'reject':
                Bazaar_Roles::update_vendor_status( $vendor_id, 'rejected' );
                $redirect_url = add_query_arg(
                    array(
                        'page'    => 'bazaar-vendors',
                        'message' => 'rejected',
                    ),
                    admin_url( 'admin.php' )
                );
                break;

            case 'disable':
                Bazaar_Roles::update_vendor_status( $vendor_id, 'disabled' );
                $redirect_url = add_query_arg(
                    array(
                        'page'    => 'bazaar-vendors',
                        'message' => 'disabled',
                    ),
                    admin_url( 'admin.php' )
                );
                break;

            case 'enable':
                Bazaar_Roles::update_vendor_status( $vendor_id, 'approved' );
                $redirect_url = add_query_arg(
                    array(
                        'page'    => 'bazaar-vendors',
                        'message' => 'enabled',
                    ),
                    admin_url( 'admin.php' )
                );
                break;

            default:
                return;
        }

        wp_safe_redirect( $redirect_url );
        exit;
    }

    /**
     * Get vendor action links.
     *
     * @param int $vendor_id Vendor ID.
     * @return array
     */
    public static function get_action_links( $vendor_id ) {
        $status = Bazaar_Roles::get_vendor_status( $vendor_id );
        $links = array();

        // View
        $links['view'] = array(
            'url'   => admin_url( 'admin.php?page=bazaar-vendors&action=view&vendor=' . $vendor_id ),
            'label' => __( 'View', 'bazaar' ),
            'class' => '',
        );

        // Edit (link to user profile)
        $links['edit'] = array(
            'url'   => admin_url( 'user-edit.php?user_id=' . $vendor_id ),
            'label' => __( 'Edit', 'bazaar' ),
            'class' => '',
        );

        // Status-specific actions
        if ( 'pending' === $status ) {
            $links['approve'] = array(
                'url'   => wp_nonce_url(
                    admin_url( 'admin.php?page=bazaar-vendors&action=approve&vendor=' . $vendor_id ),
                    'bazaar_vendor_action'
                ),
                'label' => __( 'Approve', 'bazaar' ),
                'class' => 'approve',
            );

            $links['reject'] = array(
                'url'   => wp_nonce_url(
                    admin_url( 'admin.php?page=bazaar-vendors&action=reject&vendor=' . $vendor_id ),
                    'bazaar_vendor_action'
                ),
                'label' => __( 'Reject', 'bazaar' ),
                'class' => 'reject',
            );
        }

        if ( 'approved' === $status ) {
            $links['disable'] = array(
                'url'   => wp_nonce_url(
                    admin_url( 'admin.php?page=bazaar-vendors&action=disable&vendor=' . $vendor_id ),
                    'bazaar_vendor_action'
                ),
                'label' => __( 'Disable', 'bazaar' ),
                'class' => 'disable',
            );
        }

        if ( 'disabled' === $status || 'rejected' === $status ) {
            $links['enable'] = array(
                'url'   => wp_nonce_url(
                    admin_url( 'admin.php?page=bazaar-vendors&action=enable&vendor=' . $vendor_id ),
                    'bazaar_vendor_action'
                ),
                'label' => __( 'Enable', 'bazaar' ),
                'class' => 'enable',
            );
        }

        // View products
        $links['products'] = array(
            'url'   => admin_url( 'edit.php?post_type=product&author=' . $vendor_id ),
            'label' => __( 'Products', 'bazaar' ),
            'class' => '',
        );

        return $links;
    }

    /**
     * Get status badge HTML.
     *
     * @param string $status Status.
     * @return string
     */
    public static function get_status_badge( $status ) {
        $label = Bazaar_Roles::get_status_label( $status );
        $class = 'bazaar-status-' . $status;

        return sprintf(
            '<span class="bazaar-status-badge %s">%s</span>',
            esc_attr( $class ),
            esc_html( $label )
        );
    }
}

// Initialize
new Bazaar_Admin_Vendors();
