<?php
/**
 * Settings Management Class.
 *
 * @package Bazaar
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Settings Class.
 */
class Bazaar_Settings {

    /**
     * Get all settings.
     *
     * @return array
     */
    public static function get_all() {
        return get_option( 'bazaar_settings', self::get_defaults() );
    }

    /**
     * Get a specific setting.
     *
     * @param string $key     Setting key.
     * @param mixed  $default Default value.
     * @return mixed
     */
    public static function get( $key, $default = '' ) {
        $settings = self::get_all();
        return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
    }

    /**
     * Get default settings.
     *
     * @return array
     */
    public static function get_defaults() {
        return array(
            // General
            'admin_access'             => false,
            'vendor_registration'      => true,
            'new_vendor_status'        => 'pending',
            'store_url'                => 'store',
            
            // Commission
            'commission_type'          => 'percentage',
            'admin_percentage'         => 10,
            'admin_flat_commission'    => 0,
            'shipping_fee_recipient'   => 'vendor',
            'tax_fee_recipient'        => 'vendor',
            
            // Selling
            'new_product_status'       => 'pending',
            'allowed_product_types'    => array( 'simple', 'variable' ),
            'vendor_can_create_categories' => false,
            'vendor_can_create_tags'   => true,
            'edited_product_status'    => 'publish',
            'withdraw_order_status'    => array( 'wc-completed' ),
            'vendor_can_manage_orders' => true,
            
            // Withdraw
            'withdraw_methods'         => array( 'paypal', 'bank_transfer' ),
            'minimum_withdraw'         => 50,
            'withdraw_limit'           => 0,
            'withdraw_threshold'       => 0,
            'hide_withdraw_balance'    => false,
            'disbursement_mode'        => 'manual',
            'disbursement_schedule'    => 'weekly',
            
            // Pages
            'dashboard_page'           => '',
            'store_listing'            => '',
            'registration_page'        => '',
            'terms_page'               => '',
            
            // Appearance
            'store_header_template'    => 'default',
            'store_sidebar'            => true,
            'store_tab_style'          => 'horizontal',
            'products_per_page'        => 12,
            'primary_color'            => '#6366f1',
            'enable_store_map'         => false,
            'google_maps_api'          => '',
            
            // Privacy
            'enable_privacy_policy'    => true,
            'privacy_page'             => '',
            'enable_store_terms'       => false,
            
            // Emails
            'email_new_vendor'         => true,
            'email_vendor_approved'    => true,
            'email_new_order'          => true,
            'email_withdraw_request'   => true,
            'email_withdraw_approved'  => true,
            'email_product_published'  => true,
        );
    }

    /**
     * Save settings.
     *
     * @param string $tab  Current tab.
     * @param array  $data POST data.
     * @return bool
     */
    public static function save( $tab, $data ) {
        $settings = self::get_all();
        
        switch ( $tab ) {
            case 'general':
                $settings['admin_access']           = isset( $data['admin_access'] );
                $settings['vendor_registration']    = isset( $data['vendor_registration'] );
                $settings['new_vendor_status']      = sanitize_text_field( $data['new_vendor_status'] ?? 'pending' );
                $settings['store_url']              = sanitize_title( $data['store_url'] ?? 'store' );
                $settings['commission_type']        = sanitize_text_field( $data['commission_type'] ?? 'percentage' );
                $settings['admin_percentage']       = floatval( $data['admin_percentage'] ?? 10 );
                $settings['admin_flat_commission']  = floatval( $data['admin_flat_commission'] ?? 0 );
                $settings['shipping_fee_recipient'] = sanitize_text_field( $data['shipping_fee_recipient'] ?? 'vendor' );
                $settings['tax_fee_recipient']      = sanitize_text_field( $data['tax_fee_recipient'] ?? 'vendor' );
                break;
                
            case 'selling':
                $settings['new_product_status']          = sanitize_text_field( $data['new_product_status'] ?? 'pending' );
                $settings['allowed_product_types']       = array_map( 'sanitize_text_field', $data['allowed_product_types'] ?? array() );
                $settings['vendor_can_create_categories'] = isset( $data['vendor_can_create_categories'] );
                $settings['vendor_can_create_tags']      = isset( $data['vendor_can_create_tags'] );
                $settings['edited_product_status']       = sanitize_text_field( $data['edited_product_status'] ?? 'publish' );
                $settings['withdraw_order_status']       = array_map( 'sanitize_text_field', $data['withdraw_order_status'] ?? array() );
                $settings['vendor_can_manage_orders']    = isset( $data['vendor_can_manage_orders'] );
                break;
                
            case 'withdraw':
                $settings['withdraw_methods']       = array_map( 'sanitize_text_field', $data['withdraw_methods'] ?? array() );
                $settings['minimum_withdraw']       = floatval( $data['minimum_withdraw'] ?? 50 );
                $settings['withdraw_limit']         = intval( $data['withdraw_limit'] ?? 0 );
                $settings['withdraw_threshold']     = intval( $data['withdraw_threshold'] ?? 0 );
                $settings['hide_withdraw_balance']  = isset( $data['hide_withdraw_balance'] );
                $settings['disbursement_mode']      = sanitize_text_field( $data['disbursement_mode'] ?? 'manual' );
                $settings['disbursement_schedule']  = sanitize_text_field( $data['disbursement_schedule'] ?? 'weekly' );
                break;
                
            case 'pages':
                $settings['dashboard_page']    = intval( $data['dashboard_page'] ?? 0 );
                $settings['store_listing']     = intval( $data['store_listing'] ?? 0 );
                $settings['registration_page'] = intval( $data['registration_page'] ?? 0 );
                $settings['terms_page']        = intval( $data['terms_page'] ?? 0 );
                break;
                
            case 'appearance':
                $settings['store_header_template'] = sanitize_text_field( $data['store_header_template'] ?? 'default' );
                $settings['store_sidebar']         = isset( $data['store_sidebar'] );
                $settings['store_tab_style']       = sanitize_text_field( $data['store_tab_style'] ?? 'horizontal' );
                $settings['products_per_page']     = intval( $data['products_per_page'] ?? 12 );
                $settings['primary_color']         = sanitize_hex_color( $data['primary_color'] ?? '#6366f1' );
                $settings['enable_store_map']      = isset( $data['enable_store_map'] );
                $settings['google_maps_api']       = sanitize_text_field( $data['google_maps_api'] ?? '' );
                break;
                
            case 'privacy':
                $settings['enable_privacy_policy'] = isset( $data['enable_privacy_policy'] );
                $settings['privacy_page']          = intval( $data['privacy_page'] ?? 0 );
                $settings['enable_store_terms']    = isset( $data['enable_store_terms'] );
                break;
                
            case 'emails':
                $settings['email_new_vendor']        = isset( $data['email_new_vendor'] );
                $settings['email_vendor_approved']   = isset( $data['email_vendor_approved'] );
                $settings['email_new_order']         = isset( $data['email_new_order'] );
                $settings['email_withdraw_request']  = isset( $data['email_withdraw_request'] );
                $settings['email_withdraw_approved'] = isset( $data['email_withdraw_approved'] );
                $settings['email_product_published'] = isset( $data['email_product_published'] );
                break;
        }
        
        return update_option( 'bazaar_settings', $settings );
    }
}
