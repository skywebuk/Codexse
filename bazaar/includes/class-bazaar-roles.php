<?php
/**
 * User Roles and Capabilities.
 *
 * @package Bazaar\Roles
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Roles Class.
 */
class Bazaar_Roles {

    /**
     * Get all vendor capabilities.
     *
     * @return array
     */
    public static function get_vendor_capabilities() {
        return array(
            'read'                   => true,
            'upload_files'           => true,
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
            'bazaar_manage_shipping' => true,
        );
    }

    /**
     * Get all admin capabilities.
     *
     * @return array
     */
    public static function get_admin_capabilities() {
        return array(
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
    }

    /**
     * Check if user is vendor.
     *
     * @param int|null $user_id User ID.
     * @return bool
     */
    public static function is_vendor( $user_id = null ) {
        if ( null === $user_id ) {
            $user_id = get_current_user_id();
        }

        if ( ! $user_id ) {
            return false;
        }

        $user = get_userdata( $user_id );

        if ( ! $user ) {
            return false;
        }

        return in_array( 'bazaar_vendor', (array) $user->roles, true );
    }

    /**
     * Check if user is active vendor.
     *
     * @param int|null $user_id User ID.
     * @return bool
     */
    public static function is_active_vendor( $user_id = null ) {
        if ( ! self::is_vendor( $user_id ) ) {
            return false;
        }

        if ( null === $user_id ) {
            $user_id = get_current_user_id();
        }

        $status = get_user_meta( $user_id, '_bazaar_vendor_status', true );

        return 'approved' === $status;
    }

    /**
     * Check if user can access vendor dashboard.
     *
     * @param int|null $user_id User ID.
     * @return bool
     */
    public static function can_access_dashboard( $user_id = null ) {
        if ( null === $user_id ) {
            $user_id = get_current_user_id();
        }

        if ( ! $user_id ) {
            return false;
        }

        // Admins can always access
        if ( user_can( $user_id, 'manage_options' ) ) {
            return true;
        }

        return self::is_active_vendor( $user_id );
    }

    /**
     * Check if current user can perform action.
     *
     * @param string   $capability Capability to check.
     * @param int|null $user_id    User ID.
     * @return bool
     */
    public static function current_user_can( $capability, $user_id = null ) {
        if ( null === $user_id ) {
            $user_id = get_current_user_id();
        }

        if ( ! $user_id ) {
            return false;
        }

        // Admins can do everything
        if ( user_can( $user_id, 'manage_options' ) ) {
            return true;
        }

        // Check if active vendor
        if ( ! self::is_active_vendor( $user_id ) ) {
            return false;
        }

        return user_can( $user_id, $capability );
    }

    /**
     * Make user a vendor.
     *
     * @param int    $user_id User ID.
     * @param string $status  Vendor status (pending, approved, rejected).
     * @return bool
     */
    public static function make_vendor( $user_id, $status = 'pending' ) {
        $user = get_userdata( $user_id );

        if ( ! $user ) {
            return false;
        }

        // Add vendor role
        $user->add_role( 'bazaar_vendor' );

        // Set vendor status
        update_user_meta( $user_id, '_bazaar_vendor_status', $status );
        update_user_meta( $user_id, '_bazaar_vendor_registered', current_time( 'mysql' ) );

        do_action( 'bazaar_user_became_vendor', $user_id, $status );

        return true;
    }

    /**
     * Remove vendor role from user.
     *
     * @param int $user_id User ID.
     * @return bool
     */
    public static function remove_vendor( $user_id ) {
        $user = get_userdata( $user_id );

        if ( ! $user ) {
            return false;
        }

        $user->remove_role( 'bazaar_vendor' );

        delete_user_meta( $user_id, '_bazaar_vendor_status' );

        do_action( 'bazaar_vendor_removed', $user_id );

        return true;
    }

    /**
     * Update vendor status.
     *
     * @param int    $user_id User ID.
     * @param string $status  New status.
     * @return bool
     */
    public static function update_vendor_status( $user_id, $status ) {
        if ( ! self::is_vendor( $user_id ) ) {
            return false;
        }

        $old_status = get_user_meta( $user_id, '_bazaar_vendor_status', true );

        update_user_meta( $user_id, '_bazaar_vendor_status', $status );

        if ( 'approved' === $status && $old_status !== 'approved' ) {
            update_user_meta( $user_id, '_bazaar_vendor_approved_date', current_time( 'mysql' ) );
            do_action( 'bazaar_vendor_approved', $user_id );
        } elseif ( 'rejected' === $status ) {
            do_action( 'bazaar_vendor_rejected', $user_id );
        }

        do_action( 'bazaar_vendor_status_changed', $user_id, $status, $old_status );

        return true;
    }

    /**
     * Get vendor status.
     *
     * @param int $user_id User ID.
     * @return string
     */
    public static function get_vendor_status( $user_id ) {
        return get_user_meta( $user_id, '_bazaar_vendor_status', true );
    }

    /**
     * Get vendor status label.
     *
     * @param string $status Status key.
     * @return string
     */
    public static function get_status_label( $status ) {
        $labels = array(
            'pending'  => __( 'Pending Approval', 'bazaar' ),
            'approved' => __( 'Approved', 'bazaar' ),
            'rejected' => __( 'Rejected', 'bazaar' ),
            'disabled' => __( 'Disabled', 'bazaar' ),
        );

        return isset( $labels[ $status ] ) ? $labels[ $status ] : $status;
    }
}
