<?php
/**
 * Dashboard Shipping Tab.
 *
 * @package Bazaar\Templates
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="bazaar-tab-content bazaar-shipping">
    <div class="bazaar-tab-header">
        <h2><?php esc_html_e( 'Shipping Settings', 'bazaar' ); ?></h2>
        <a href="#" class="button button-primary bazaar-add-shipping-zone-btn">
            <span class="dashicons dashicons-plus-alt"></span>
            <?php esc_html_e( 'Add Shipping Zone', 'bazaar' ); ?>
        </a>
    </div>

    <p class="description">
        <?php esc_html_e( 'Configure shipping zones and methods for your products. Customers will see these shipping options during checkout.', 'bazaar' ); ?>
    </p>

    <!-- Add/Edit Shipping Zone Form -->
    <div class="bazaar-shipping-zone-form" style="display: none;">
        <h3 class="form-title"><?php esc_html_e( 'Add Shipping Zone', 'bazaar' ); ?></h3>
        <form id="bazaar-shipping-zone-form" method="post">
            <?php wp_nonce_field( 'bazaar_save_shipping_zone', 'shipping_zone_nonce' ); ?>
            <input type="hidden" name="zone_id" value="" />

            <div class="form-row">
                <div class="form-group">
                    <label for="zone_name"><?php esc_html_e( 'Zone Name', 'bazaar' ); ?> <span class="required">*</span></label>
                    <input type="text" name="zone_name" id="zone_name" required placeholder="<?php esc_attr_e( 'e.g., United States', 'bazaar' ); ?>" />
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="zone_regions"><?php esc_html_e( 'Zone Regions', 'bazaar' ); ?></label>
                    <select name="zone_regions[]" id="zone_regions" class="bazaar-select2" multiple>
                        <?php
                        $countries = WC()->countries->get_countries();
                        foreach ( $countries as $code => $name ) {
                            echo '<option value="' . esc_attr( $code ) . '">' . esc_html( $name ) . '</option>';
                        }
                        ?>
                    </select>
                    <span class="description"><?php esc_html_e( 'Select countries/regions for this shipping zone. Leave empty for all locations.', 'bazaar' ); ?></span>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary"><?php esc_html_e( 'Save Zone', 'bazaar' ); ?></button>
                <button type="button" class="button bazaar-cancel-zone"><?php esc_html_e( 'Cancel', 'bazaar' ); ?></button>
            </div>
        </form>
    </div>

    <!-- Shipping Zones List -->
    <?php if ( empty( $shipping_zones ) ) : ?>
        <div class="bazaar-empty-state">
            <span class="dashicons dashicons-car"></span>
            <h3><?php esc_html_e( 'No shipping zones configured', 'bazaar' ); ?></h3>
            <p><?php esc_html_e( 'Add shipping zones to define where you ship and what methods are available.', 'bazaar' ); ?></p>
        </div>
    <?php else : ?>
        <div class="bazaar-shipping-zones">
            <?php foreach ( $shipping_zones as $zone ) : ?>
                <div class="shipping-zone-card" data-zone-id="<?php echo esc_attr( $zone->id ); ?>">
                    <div class="zone-header">
                        <div class="zone-info">
                            <h3 class="zone-name"><?php echo esc_html( $zone->zone_name ); ?></h3>
                            <span class="zone-regions">
                                <?php
                                if ( empty( $zone->zone_regions ) ) {
                                    esc_html_e( 'All locations', 'bazaar' );
                                } else {
                                    $regions = maybe_unserialize( $zone->zone_regions );
                                    if ( is_array( $regions ) ) {
                                        $region_names = array();
                                        foreach ( array_slice( $regions, 0, 3 ) as $code ) {
                                            $region_names[] = WC()->countries->get_countries()[ $code ] ?? $code;
                                        }
                                        echo esc_html( implode( ', ', $region_names ) );
                                        if ( count( $regions ) > 3 ) {
                                            printf( ' ' . esc_html__( 'and %d more', 'bazaar' ), count( $regions ) - 3 );
                                        }
                                    }
                                }
                                ?>
                            </span>
                        </div>
                        <div class="zone-actions">
                            <button type="button" class="button button-small edit-zone" data-zone-id="<?php echo esc_attr( $zone->id ); ?>">
                                <?php esc_html_e( 'Edit', 'bazaar' ); ?>
                            </button>
                            <button type="button" class="button button-small button-link-delete delete-zone" data-zone-id="<?php echo esc_attr( $zone->id ); ?>">
                                <?php esc_html_e( 'Delete', 'bazaar' ); ?>
                            </button>
                        </div>
                    </div>

                    <!-- Shipping Methods -->
                    <div class="zone-methods">
                        <h4><?php esc_html_e( 'Shipping Methods', 'bazaar' ); ?></h4>

                        <?php
                        $methods = bazaar_get_zone_shipping_methods( $zone->id );
                        ?>

                        <?php if ( empty( $methods ) ) : ?>
                            <p class="no-methods"><?php esc_html_e( 'No shipping methods added yet.', 'bazaar' ); ?></p>
                        <?php else : ?>
                            <table class="shipping-methods-table">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e( 'Method', 'bazaar' ); ?></th>
                                        <th><?php esc_html_e( 'Cost', 'bazaar' ); ?></th>
                                        <th><?php esc_html_e( 'Status', 'bazaar' ); ?></th>
                                        <th><?php esc_html_e( 'Actions', 'bazaar' ); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ( $methods as $method ) : ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo esc_html( $method->method_title ); ?></strong>
                                                <span class="method-type"><?php echo esc_html( $method->method_type ); ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                if ( 'free_shipping' === $method->method_type ) {
                                                    esc_html_e( 'Free', 'bazaar' );
                                                } elseif ( 'flat_rate' === $method->method_type ) {
                                                    echo wc_price( $method->cost );
                                                } else {
                                                    esc_html_e( 'Calculated', 'bazaar' );
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php if ( $method->is_enabled ) : ?>
                                                    <span class="status-badge status-enabled"><?php esc_html_e( 'Enabled', 'bazaar' ); ?></span>
                                                <?php else : ?>
                                                    <span class="status-badge status-disabled"><?php esc_html_e( 'Disabled', 'bazaar' ); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button type="button" class="button button-small edit-method" data-method-id="<?php echo esc_attr( $method->id ); ?>">
                                                    <?php esc_html_e( 'Edit', 'bazaar' ); ?>
                                                </button>
                                                <button type="button" class="button button-small button-link-delete delete-method" data-method-id="<?php echo esc_attr( $method->id ); ?>">
                                                    <?php esc_html_e( 'Delete', 'bazaar' ); ?>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>

                        <button type="button" class="button add-method-btn" data-zone-id="<?php echo esc_attr( $zone->id ); ?>">
                            <span class="dashicons dashicons-plus"></span>
                            <?php esc_html_e( 'Add Shipping Method', 'bazaar' ); ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Add Shipping Method Modal -->
    <div class="bazaar-modal bazaar-shipping-method-modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><?php esc_html_e( 'Add Shipping Method', 'bazaar' ); ?></h3>
                <button type="button" class="modal-close">&times;</button>
            </div>
            <form id="bazaar-shipping-method-form" method="post">
                <?php wp_nonce_field( 'bazaar_save_shipping_method', 'shipping_method_nonce' ); ?>
                <input type="hidden" name="zone_id" value="" />
                <input type="hidden" name="method_id" value="" />

                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="method_type"><?php esc_html_e( 'Method Type', 'bazaar' ); ?></label>
                            <select name="method_type" id="method_type" required>
                                <option value="flat_rate"><?php esc_html_e( 'Flat Rate', 'bazaar' ); ?></option>
                                <option value="free_shipping"><?php esc_html_e( 'Free Shipping', 'bazaar' ); ?></option>
                                <option value="local_pickup"><?php esc_html_e( 'Local Pickup', 'bazaar' ); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="method_title"><?php esc_html_e( 'Method Title', 'bazaar' ); ?> <span class="required">*</span></label>
                            <input type="text" name="method_title" id="method_title" required />
                        </div>
                    </div>

                    <div class="form-row method-cost-row">
                        <div class="form-group">
                            <label for="method_cost"><?php esc_html_e( 'Cost', 'bazaar' ); ?></label>
                            <input type="number" name="method_cost" id="method_cost" step="0.01" min="0" />
                        </div>
                    </div>

                    <div class="form-row free-shipping-options" style="display: none;">
                        <div class="form-group">
                            <label for="free_shipping_min"><?php esc_html_e( 'Minimum Order Amount', 'bazaar' ); ?></label>
                            <input type="number" name="free_shipping_min" id="free_shipping_min" step="0.01" min="0" />
                            <span class="description"><?php esc_html_e( 'Minimum order amount required for free shipping. Leave empty for no minimum.', 'bazaar' ); ?></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="is_enabled" value="1" checked />
                                <?php esc_html_e( 'Enable this shipping method', 'bazaar' ); ?>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="button button-primary"><?php esc_html_e( 'Save Method', 'bazaar' ); ?></button>
                    <button type="button" class="button modal-cancel"><?php esc_html_e( 'Cancel', 'bazaar' ); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
