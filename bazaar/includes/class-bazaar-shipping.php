<?php
/**
 * Vendor Shipping Management Class.
 *
 * @package Bazaar\Shipping
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Shipping Class.
 */
class Bazaar_Shipping {

    /**
     * Constructor.
     */
    public function __construct() {
        // Add vendor shipping to cart
        add_filter( 'woocommerce_package_rates', array( $this, 'filter_shipping_rates' ), 10, 2 );

        // Calculate shipping per vendor
        add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'split_shipping_packages' ) );
    }

    /**
     * Get vendor shipping methods.
     *
     * @param int $vendor_id Vendor ID.
     * @return array
     */
    public static function get_vendor_shipping_methods( $vendor_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_vendor_shipping';

        $shipping = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE vendor_id = %d AND is_enabled = 1",
                $vendor_id
            )
        );

        return $shipping;
    }

    /**
     * Get vendor shipping zone.
     *
     * @param int $zone_id Zone ID.
     * @return object|null
     */
    public static function get_shipping_zone( $zone_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_vendor_shipping';

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE id = %d",
                $zone_id
            )
        );
    }

    /**
     * Save vendor shipping zone.
     *
     * @param int   $vendor_id Vendor ID.
     * @param array $data      Zone data.
     * @return int|WP_Error
     */
    public static function save_shipping_zone( $vendor_id, $data ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_vendor_shipping';

        $zone_data = array(
            'vendor_id'      => $vendor_id,
            'zone_name'      => sanitize_text_field( $data['zone_name'] ),
            'zone_locations' => maybe_serialize( isset( $data['zone_locations'] ) ? $data['zone_locations'] : array() ),
            'methods'        => maybe_serialize( isset( $data['methods'] ) ? $data['methods'] : array() ),
            'is_enabled'     => isset( $data['is_enabled'] ) ? 1 : 0,
        );

        if ( ! empty( $data['id'] ) ) {
            // Update existing
            $wpdb->update(
                $table_name,
                $zone_data,
                array( 'id' => intval( $data['id'] ) ),
                array( '%d', '%s', '%s', '%s', '%d' ),
                array( '%d' )
            );

            return intval( $data['id'] );
        } else {
            // Insert new
            $wpdb->insert(
                $table_name,
                $zone_data,
                array( '%d', '%s', '%s', '%s', '%d' )
            );

            return $wpdb->insert_id;
        }
    }

    /**
     * Delete shipping zone.
     *
     * @param int $zone_id   Zone ID.
     * @param int $vendor_id Vendor ID.
     * @return bool
     */
    public static function delete_shipping_zone( $zone_id, $vendor_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_vendor_shipping';

        $result = $wpdb->delete(
            $table_name,
            array(
                'id'        => $zone_id,
                'vendor_id' => $vendor_id,
            ),
            array( '%d', '%d' )
        );

        return false !== $result;
    }

    /**
     * Get available shipping method types.
     *
     * @return array
     */
    public static function get_shipping_method_types() {
        return array(
            'flat_rate'     => __( 'Flat Rate', 'bazaar' ),
            'free_shipping' => __( 'Free Shipping', 'bazaar' ),
            'local_pickup'  => __( 'Local Pickup', 'bazaar' ),
        );
    }

    /**
     * Split shipping packages by vendor.
     *
     * @param array $packages Shipping packages.
     * @return array
     */
    public function split_shipping_packages( $packages ) {
        if ( 'yes' !== get_option( 'bazaar_vendor_shipping_enabled', 'yes' ) ) {
            return $packages;
        }

        $vendor_packages = array();

        foreach ( $packages as $package_key => $package ) {
            $vendor_items = array();

            foreach ( $package['contents'] as $item_key => $item ) {
                $product_id = $item['product_id'];
                $vendor_id = Bazaar_Product::get_product_vendor( $product_id );

                if ( ! $vendor_id ) {
                    $vendor_id = 0; // Admin products
                }

                if ( ! isset( $vendor_items[ $vendor_id ] ) ) {
                    $vendor_items[ $vendor_id ] = array();
                }

                $vendor_items[ $vendor_id ][ $item_key ] = $item;
            }

            // Create separate package for each vendor
            foreach ( $vendor_items as $vendor_id => $items ) {
                $vendor_package = $package;
                $vendor_package['contents'] = $items;
                $vendor_package['bazaar_vendor_id'] = $vendor_id;

                // Recalculate package totals
                $vendor_package['contents_cost'] = array_sum(
                    array_map(
                        function ( $item ) {
                            return $item['line_total'];
                        },
                        $items
                    )
                );

                $vendor_packages[] = $vendor_package;
            }
        }

        return $vendor_packages;
    }

    /**
     * Filter shipping rates by vendor.
     *
     * @param array $rates   Shipping rates.
     * @param array $package Package.
     * @return array
     */
    public function filter_shipping_rates( $rates, $package ) {
        if ( 'yes' !== get_option( 'bazaar_vendor_shipping_enabled', 'yes' ) ) {
            return $rates;
        }

        if ( ! isset( $package['bazaar_vendor_id'] ) || empty( $package['bazaar_vendor_id'] ) ) {
            return $rates;
        }

        $vendor_id = $package['bazaar_vendor_id'];
        $vendor_shipping = self::get_vendor_shipping_methods( $vendor_id );

        if ( empty( $vendor_shipping ) ) {
            return $rates; // Use default shipping
        }

        $vendor_rates = array();
        $destination = $package['destination'];

        foreach ( $vendor_shipping as $zone ) {
            // Check if destination matches zone
            if ( ! self::destination_matches_zone( $destination, $zone ) ) {
                continue;
            }

            $methods = maybe_unserialize( $zone->methods );

            if ( empty( $methods ) ) {
                continue;
            }

            foreach ( $methods as $method ) {
                $rate = self::create_shipping_rate( $method, $package, $vendor_id );

                if ( $rate ) {
                    $vendor_rates[ $rate['id'] ] = new WC_Shipping_Rate(
                        $rate['id'],
                        $rate['label'],
                        $rate['cost'],
                        array(),
                        $rate['method_id']
                    );
                }
            }
        }

        // Return vendor rates if available, otherwise default rates
        return ! empty( $vendor_rates ) ? $vendor_rates : $rates;
    }

    /**
     * Check if destination matches zone.
     *
     * @param array  $destination Destination.
     * @param object $zone        Zone object.
     * @return bool
     */
    private static function destination_matches_zone( $destination, $zone ) {
        $locations = maybe_unserialize( $zone->zone_locations );

        if ( empty( $locations ) ) {
            return true; // Empty locations = everywhere
        }

        foreach ( $locations as $location ) {
            if ( isset( $location['type'] ) ) {
                switch ( $location['type'] ) {
                    case 'country':
                        if ( $destination['country'] === $location['code'] ) {
                            return true;
                        }
                        break;
                    case 'state':
                        $parts = explode( ':', $location['code'] );
                        if ( count( $parts ) === 2 ) {
                            if ( $destination['country'] === $parts[0] && $destination['state'] === $parts[1] ) {
                                return true;
                            }
                        }
                        break;
                    case 'postcode':
                        if ( self::postcode_matches( $destination['postcode'], $location['code'] ) ) {
                            return true;
                        }
                        break;
                }
            }
        }

        return false;
    }

    /**
     * Check if postcode matches.
     *
     * @param string $postcode Destination postcode.
     * @param string $pattern  Pattern to match.
     * @return bool
     */
    private static function postcode_matches( $postcode, $pattern ) {
        $postcode = strtoupper( trim( $postcode ) );
        $pattern = strtoupper( trim( $pattern ) );

        // Exact match
        if ( $postcode === $pattern ) {
            return true;
        }

        // Wildcard match
        if ( strpos( $pattern, '*' ) !== false ) {
            $regex = '/^' . str_replace( '*', '.*', preg_quote( $pattern, '/' ) ) . '$/';
            return preg_match( $regex, $postcode );
        }

        // Range match (e.g., "10000...20000")
        if ( strpos( $pattern, '...' ) !== false ) {
            list( $min, $max ) = explode( '...', $pattern );
            return $postcode >= $min && $postcode <= $max;
        }

        return false;
    }

    /**
     * Create shipping rate from method data.
     *
     * @param array $method    Method data.
     * @param array $package   Package.
     * @param int   $vendor_id Vendor ID.
     * @return array|false
     */
    private static function create_shipping_rate( $method, $package, $vendor_id ) {
        if ( empty( $method['type'] ) || empty( $method['enabled'] ) ) {
            return false;
        }

        $rate_id = 'bazaar_' . $vendor_id . '_' . sanitize_key( $method['type'] );
        $label = ! empty( $method['title'] ) ? $method['title'] : self::get_shipping_method_types()[ $method['type'] ];
        $cost = 0;

        switch ( $method['type'] ) {
            case 'flat_rate':
                $cost = isset( $method['cost'] ) ? floatval( $method['cost'] ) : 0;
                break;

            case 'free_shipping':
                $min_amount = isset( $method['min_amount'] ) ? floatval( $method['min_amount'] ) : 0;
                if ( $min_amount > 0 && $package['contents_cost'] < $min_amount ) {
                    return false; // Free shipping not available
                }
                $cost = 0;
                break;

            case 'local_pickup':
                $cost = isset( $method['cost'] ) ? floatval( $method['cost'] ) : 0;
                break;
        }

        return array(
            'id'        => $rate_id,
            'label'     => $label,
            'cost'      => $cost,
            'method_id' => $method['type'],
        );
    }

    /**
     * Get vendor shipping settings.
     *
     * @param int $vendor_id Vendor ID.
     * @return array
     */
    public static function get_vendor_shipping_settings( $vendor_id ) {
        return array(
            'processing_time' => get_user_meta( $vendor_id, '_bazaar_shipping_processing_time', true ),
            'policy'          => get_user_meta( $vendor_id, '_bazaar_shipping_policy', true ),
            'return_policy'   => get_user_meta( $vendor_id, '_bazaar_return_policy', true ),
        );
    }

    /**
     * Save vendor shipping settings.
     *
     * @param int   $vendor_id Vendor ID.
     * @param array $data      Settings data.
     */
    public static function save_vendor_shipping_settings( $vendor_id, $data ) {
        if ( isset( $data['processing_time'] ) ) {
            update_user_meta( $vendor_id, '_bazaar_shipping_processing_time', sanitize_text_field( $data['processing_time'] ) );
        }

        if ( isset( $data['policy'] ) ) {
            update_user_meta( $vendor_id, '_bazaar_shipping_policy', wp_kses_post( $data['policy'] ) );
        }

        if ( isset( $data['return_policy'] ) ) {
            update_user_meta( $vendor_id, '_bazaar_return_policy', wp_kses_post( $data['return_policy'] ) );
        }
    }
}

// Initialize
new Bazaar_Shipping();
