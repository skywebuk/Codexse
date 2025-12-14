<?php
/**
 * Admin Settings Class.
 *
 * @package Bazaar\Admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Admin_Settings Class.
 */
class Bazaar_Admin_Settings {

    /**
     * Get settings tabs.
     *
     * @return array
     */
    public static function get_tabs() {
        return array(
            'general'     => __( 'General', 'bazaar' ),
            'vendor'      => __( 'Vendor', 'bazaar' ),
            'commission'  => __( 'Commission', 'bazaar' ),
            'withdrawal'  => __( 'Withdrawal', 'bazaar' ),
            'pages'       => __( 'Pages', 'bazaar' ),
            'emails'      => __( 'Emails', 'bazaar' ),
        );
    }

    /**
     * Get settings for tab.
     *
     * @param string $tab Tab slug.
     * @return array
     */
    public static function get_settings( $tab ) {
        $settings = array();

        switch ( $tab ) {
            case 'general':
                $settings = array(
                    array(
                        'id'    => 'bazaar_vendor_store_slug',
                        'title' => __( 'Store URL Slug', 'bazaar' ),
                        'type'  => 'text',
                        'default' => 'store',
                        'desc'  => __( 'The URL slug for vendor stores (e.g., yoursite.com/store/vendor-name)', 'bazaar' ),
                    ),
                    array(
                        'id'    => 'bazaar_order_split_enabled',
                        'title' => __( 'Split Orders by Vendor', 'bazaar' ),
                        'type'  => 'checkbox',
                        'default' => 'yes',
                        'desc'  => __( 'Create sub-orders for each vendor in multi-vendor orders.', 'bazaar' ),
                    ),
                );
                break;

            case 'vendor':
                $settings = array(
                    array(
                        'id'    => 'bazaar_vendor_registration',
                        'title' => __( 'Vendor Registration', 'bazaar' ),
                        'type'  => 'select',
                        'options' => array(
                            'enabled'  => __( 'Enabled', 'bazaar' ),
                            'disabled' => __( 'Disabled', 'bazaar' ),
                        ),
                        'default' => 'enabled',
                    ),
                    array(
                        'id'    => 'bazaar_vendor_approval',
                        'title' => __( 'Vendor Approval', 'bazaar' ),
                        'type'  => 'select',
                        'options' => array(
                            'manual' => __( 'Manual Approval Required', 'bazaar' ),
                            'auto'   => __( 'Auto Approve', 'bazaar' ),
                        ),
                        'default' => 'manual',
                    ),
                    array(
                        'id'    => 'bazaar_product_moderation',
                        'title' => __( 'Product Moderation', 'bazaar' ),
                        'type'  => 'select',
                        'options' => array(
                            'enabled'  => __( 'Require Admin Approval', 'bazaar' ),
                            'disabled' => __( 'Auto Publish', 'bazaar' ),
                        ),
                        'default' => 'enabled',
                    ),
                    array(
                        'id'    => 'bazaar_vendor_product_types',
                        'title' => __( 'Allowed Product Types', 'bazaar' ),
                        'type'  => 'multiselect',
                        'options' => array(
                            'simple'   => __( 'Simple', 'bazaar' ),
                            'variable' => __( 'Variable', 'bazaar' ),
                            'grouped'  => __( 'Grouped', 'bazaar' ),
                            'external' => __( 'External/Affiliate', 'bazaar' ),
                        ),
                        'default' => array( 'simple', 'variable' ),
                    ),
                    array(
                        'id'    => 'bazaar_vendor_coupon_enabled',
                        'title' => __( 'Allow Vendor Coupons', 'bazaar' ),
                        'type'  => 'checkbox',
                        'default' => 'yes',
                    ),
                    array(
                        'id'    => 'bazaar_vendor_shipping_enabled',
                        'title' => __( 'Allow Vendor Shipping', 'bazaar' ),
                        'type'  => 'checkbox',
                        'default' => 'yes',
                    ),
                    array(
                        'id'    => 'bazaar_vacation_mode_enabled',
                        'title' => __( 'Allow Vacation Mode', 'bazaar' ),
                        'type'  => 'checkbox',
                        'default' => 'yes',
                    ),
                    array(
                        'id'    => 'bazaar_vendor_review_enabled',
                        'title' => __( 'Enable Vendor Reviews', 'bazaar' ),
                        'type'  => 'checkbox',
                        'default' => 'yes',
                    ),
                    array(
                        'id'    => 'bazaar_review_moderation',
                        'title' => __( 'Review Moderation', 'bazaar' ),
                        'type'  => 'checkbox',
                        'default' => 'yes',
                        'desc'  => __( 'Require admin approval for vendor reviews.', 'bazaar' ),
                    ),
                    array(
                        'id'    => 'bazaar_review_require_purchase',
                        'title' => __( 'Reviews Require Purchase', 'bazaar' ),
                        'type'  => 'checkbox',
                        'default' => 'yes',
                        'desc'  => __( 'Only customers who have purchased can review.', 'bazaar' ),
                    ),
                );
                break;

            case 'commission':
                $settings = array(
                    array(
                        'id'    => 'bazaar_global_commission_type',
                        'title' => __( 'Commission Type', 'bazaar' ),
                        'type'  => 'select',
                        'options' => array(
                            'percentage' => __( 'Percentage', 'bazaar' ),
                            'fixed'      => __( 'Fixed Amount', 'bazaar' ),
                        ),
                        'default' => 'percentage',
                    ),
                    array(
                        'id'    => 'bazaar_global_commission_rate',
                        'title' => __( 'Commission Rate', 'bazaar' ),
                        'type'  => 'number',
                        'default' => '10',
                        'desc'  => __( 'Admin commission (% or fixed amount per order).', 'bazaar' ),
                        'attrs' => array(
                            'step' => '0.01',
                            'min'  => '0',
                        ),
                    ),
                );
                break;

            case 'withdrawal':
                $settings = array(
                    array(
                        'id'    => 'bazaar_min_withdrawal_amount',
                        'title' => __( 'Minimum Withdrawal Amount', 'bazaar' ),
                        'type'  => 'number',
                        'default' => '50',
                        'attrs' => array(
                            'step' => '0.01',
                            'min'  => '0',
                        ),
                    ),
                    array(
                        'id'    => 'bazaar_withdrawal_methods',
                        'title' => __( 'Withdrawal Methods', 'bazaar' ),
                        'type'  => 'multiselect',
                        'options' => array(
                            'paypal'        => __( 'PayPal', 'bazaar' ),
                            'bank_transfer' => __( 'Bank Transfer', 'bazaar' ),
                            'stripe'        => __( 'Stripe', 'bazaar' ),
                        ),
                        'default' => array( 'paypal', 'bank_transfer' ),
                    ),
                    array(
                        'id'    => 'bazaar_auto_payout',
                        'title' => __( 'Automatic Payouts', 'bazaar' ),
                        'type'  => 'checkbox',
                        'default' => 'no',
                        'desc'  => __( 'Automatically process approved withdrawals.', 'bazaar' ),
                    ),
                );
                break;

            case 'pages':
                $pages = self::get_pages_list();
                $settings = array(
                    array(
                        'id'    => 'bazaar_vendor_dashboard_page',
                        'title' => __( 'Vendor Dashboard Page', 'bazaar' ),
                        'type'  => 'select',
                        'options' => $pages,
                    ),
                    array(
                        'id'    => 'bazaar_vendor_registration_page',
                        'title' => __( 'Vendor Registration Page', 'bazaar' ),
                        'type'  => 'select',
                        'options' => $pages,
                    ),
                    array(
                        'id'    => 'bazaar_store_listing_page',
                        'title' => __( 'Store Listing Page', 'bazaar' ),
                        'type'  => 'select',
                        'options' => $pages,
                    ),
                );
                break;

            case 'emails':
                $settings = array(
                    array(
                        'id'    => 'bazaar_email_notifications',
                        'title' => __( 'Enable Email Notifications', 'bazaar' ),
                        'type'  => 'checkbox',
                        'default' => 'yes',
                    ),
                );
                break;
        }

        return apply_filters( 'bazaar_settings_' . $tab, $settings );
    }

    /**
     * Get pages list for select.
     *
     * @return array
     */
    private static function get_pages_list() {
        $pages = get_pages();
        $options = array( '' => __( '— Select Page —', 'bazaar' ) );

        foreach ( $pages as $page ) {
            $options[ $page->ID ] = $page->post_title;
        }

        return $options;
    }

    /**
     * Save settings.
     *
     * @param string $tab Tab slug.
     */
    public static function save_settings( $tab ) {
        $settings = self::get_settings( $tab );

        foreach ( $settings as $setting ) {
            $value = isset( $_POST[ $setting['id'] ] ) ? $_POST[ $setting['id'] ] : '';

            switch ( $setting['type'] ) {
                case 'checkbox':
                    $value = ! empty( $value ) ? 'yes' : 'no';
                    break;

                case 'multiselect':
                    $value = is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : array();
                    break;

                case 'number':
                    $value = floatval( $value );
                    break;

                case 'textarea':
                    $value = wp_kses_post( wp_unslash( $value ) );
                    break;

                default:
                    $value = sanitize_text_field( wp_unslash( $value ) );
            }

            update_option( $setting['id'], $value );
        }

        // Flush rewrite rules if store slug changed
        if ( 'general' === $tab ) {
            flush_rewrite_rules();
        }

        do_action( 'bazaar_settings_saved', $tab );
    }

    /**
     * Render setting field.
     *
     * @param array $setting Setting data.
     */
    public static function render_field( $setting ) {
        $value = get_option( $setting['id'], $setting['default'] ?? '' );
        $attrs = isset( $setting['attrs'] ) ? $setting['attrs'] : array();
        $attrs_html = '';

        foreach ( $attrs as $attr_key => $attr_value ) {
            $attrs_html .= ' ' . esc_attr( $attr_key ) . '="' . esc_attr( $attr_value ) . '"';
        }

        switch ( $setting['type'] ) {
            case 'text':
            case 'number':
            case 'email':
                ?>
                <input type="<?php echo esc_attr( $setting['type'] ); ?>"
                       name="<?php echo esc_attr( $setting['id'] ); ?>"
                       id="<?php echo esc_attr( $setting['id'] ); ?>"
                       value="<?php echo esc_attr( $value ); ?>"
                       class="regular-text"
                       <?php echo $attrs_html; ?> />
                <?php
                break;

            case 'textarea':
                ?>
                <textarea name="<?php echo esc_attr( $setting['id'] ); ?>"
                          id="<?php echo esc_attr( $setting['id'] ); ?>"
                          rows="5"
                          class="large-text"><?php echo esc_textarea( $value ); ?></textarea>
                <?php
                break;

            case 'select':
                ?>
                <select name="<?php echo esc_attr( $setting['id'] ); ?>"
                        id="<?php echo esc_attr( $setting['id'] ); ?>">
                    <?php foreach ( $setting['options'] as $option_value => $option_label ) : ?>
                        <option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $value, $option_value ); ?>>
                            <?php echo esc_html( $option_label ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php
                break;

            case 'multiselect':
                $value = is_array( $value ) ? $value : array();
                ?>
                <select name="<?php echo esc_attr( $setting['id'] ); ?>[]"
                        id="<?php echo esc_attr( $setting['id'] ); ?>"
                        multiple="multiple"
                        class="regular-text"
                        style="height: auto;">
                    <?php foreach ( $setting['options'] as $option_value => $option_label ) : ?>
                        <option value="<?php echo esc_attr( $option_value ); ?>" <?php echo in_array( $option_value, $value, true ) ? 'selected' : ''; ?>>
                            <?php echo esc_html( $option_label ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php
                break;

            case 'checkbox':
                ?>
                <input type="checkbox"
                       name="<?php echo esc_attr( $setting['id'] ); ?>"
                       id="<?php echo esc_attr( $setting['id'] ); ?>"
                       value="yes"
                       <?php checked( $value, 'yes' ); ?> />
                <?php
                break;
        }

        if ( isset( $setting['desc'] ) ) {
            echo '<p class="description">' . esc_html( $setting['desc'] ) . '</p>';
        }
    }
}
