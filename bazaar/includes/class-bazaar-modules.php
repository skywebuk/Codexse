<?php
/**
 * Modules Management Class.
 *
 * @package Bazaar
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Modules Class.
 */
class Bazaar_Modules {

    /**
     * Get all available modules.
     *
     * @return array
     */
    public static function get_all_modules() {
        return array(
            'vendor_verification',
            'vendor_staff',
            'vendor_vacation',
            'vendor_badges',
            'vendor_seo',
            'vendor_social',
            'stripe_connect',
            'paypal_marketplace',
            'vendor_shipping',
            'table_rate_shipping',
            'product_addon',
            'product_enquiry',
            'auction',
            'booking',
            'subscription',
            'live_chat',
            'order_minmax',
            'return_request',
            'coupons',
            'follow_store',
            'email_marketing',
            'vendor_analytics',
            'export_reports',
            'elementor',
            'geolocation',
            'wholesale',
        );
    }

    /**
     * Get active modules.
     *
     * @return array
     */
    public static function get_active_modules() {
        return get_option( 'bazaar_active_modules', array(
            'vendor_shipping',
            'product_enquiry',
            'vendor_seo',
            'vendor_social',
            'coupons',
            'follow_store',
            'vendor_analytics',
            'export_reports',
        ) );
    }

    /**
     * Activate a module.
     *
     * @param string $module_id Module ID.
     * @return bool
     */
    public static function activate( $module_id ) {
        $active = self::get_active_modules();
        
        if ( ! in_array( $module_id, $active, true ) ) {
            $active[] = $module_id;
            update_option( 'bazaar_active_modules', $active );
        }
        
        do_action( 'bazaar_module_activated', $module_id );
        
        return true;
    }

    /**
     * Deactivate a module.
     *
     * @param string $module_id Module ID.
     * @return bool
     */
    public static function deactivate( $module_id ) {
        $active = self::get_active_modules();
        $key = array_search( $module_id, $active, true );
        
        if ( false !== $key ) {
            unset( $active[ $key ] );
            update_option( 'bazaar_active_modules', array_values( $active ) );
        }
        
        do_action( 'bazaar_module_deactivated', $module_id );
        
        return true;
    }

    /**
     * Check if a module is active.
     *
     * @param string $module_id Module ID.
     * @return bool
     */
    public static function is_active( $module_id ) {
        return in_array( $module_id, self::get_active_modules(), true );
    }
}
