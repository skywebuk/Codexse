<?php
/**
 * Dashboard Coupons Tab.
 *
 * @package Bazaar\Templates
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="bazaar-tab-content bazaar-coupons">
    <div class="bazaar-tab-header">
        <h2><?php esc_html_e( 'Coupons', 'bazaar' ); ?></h2>
        <a href="#" class="button button-primary bazaar-add-coupon-btn">
            <span class="dashicons dashicons-plus-alt"></span>
            <?php esc_html_e( 'Add New Coupon', 'bazaar' ); ?>
        </a>
    </div>

    <!-- Add/Edit Coupon Form -->
    <div class="bazaar-coupon-form" style="display: none;">
        <h3 class="form-title"><?php esc_html_e( 'Create Coupon', 'bazaar' ); ?></h3>
        <form id="bazaar-coupon-form" method="post">
            <?php wp_nonce_field( 'bazaar_save_coupon', 'coupon_nonce' ); ?>
            <input type="hidden" name="coupon_id" value="" />

            <div class="form-row">
                <div class="form-group half">
                    <label for="coupon_code"><?php esc_html_e( 'Coupon Code', 'bazaar' ); ?> <span class="required">*</span></label>
                    <input type="text" name="coupon_code" id="coupon_code" required />
                    <button type="button" class="button generate-code"><?php esc_html_e( 'Generate', 'bazaar' ); ?></button>
                </div>
                <div class="form-group half">
                    <label for="discount_type"><?php esc_html_e( 'Discount Type', 'bazaar' ); ?></label>
                    <select name="discount_type" id="discount_type">
                        <option value="percent"><?php esc_html_e( 'Percentage Discount', 'bazaar' ); ?></option>
                        <option value="fixed_cart"><?php esc_html_e( 'Fixed Cart Discount', 'bazaar' ); ?></option>
                        <option value="fixed_product"><?php esc_html_e( 'Fixed Product Discount', 'bazaar' ); ?></option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group half">
                    <label for="coupon_amount"><?php esc_html_e( 'Coupon Amount', 'bazaar' ); ?> <span class="required">*</span></label>
                    <input type="number" name="coupon_amount" id="coupon_amount" step="0.01" min="0" required />
                </div>
                <div class="form-group half">
                    <label for="expiry_date"><?php esc_html_e( 'Expiry Date', 'bazaar' ); ?></label>
                    <input type="date" name="expiry_date" id="expiry_date" />
                </div>
            </div>

            <div class="form-row">
                <div class="form-group half">
                    <label for="minimum_amount"><?php esc_html_e( 'Minimum Spend', 'bazaar' ); ?></label>
                    <input type="number" name="minimum_amount" id="minimum_amount" step="0.01" min="0" />
                </div>
                <div class="form-group half">
                    <label for="maximum_amount"><?php esc_html_e( 'Maximum Spend', 'bazaar' ); ?></label>
                    <input type="number" name="maximum_amount" id="maximum_amount" step="0.01" min="0" />
                </div>
            </div>

            <div class="form-row">
                <div class="form-group half">
                    <label for="usage_limit"><?php esc_html_e( 'Usage Limit (Total)', 'bazaar' ); ?></label>
                    <input type="number" name="usage_limit" id="usage_limit" min="0" />
                    <span class="description"><?php esc_html_e( 'Leave blank for unlimited.', 'bazaar' ); ?></span>
                </div>
                <div class="form-group half">
                    <label for="usage_limit_per_user"><?php esc_html_e( 'Usage Limit Per User', 'bazaar' ); ?></label>
                    <input type="number" name="usage_limit_per_user" id="usage_limit_per_user" min="0" />
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="description"><?php esc_html_e( 'Description', 'bazaar' ); ?></label>
                    <textarea name="description" id="description" rows="3"></textarea>
                </div>
            </div>

            <div class="form-row checkboxes">
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="free_shipping" value="yes" />
                        <?php esc_html_e( 'Allow Free Shipping', 'bazaar' ); ?>
                    </label>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="individual_use" value="yes" />
                        <?php esc_html_e( 'Individual Use Only', 'bazaar' ); ?>
                    </label>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="exclude_sale_items" value="yes" />
                        <?php esc_html_e( 'Exclude Sale Items', 'bazaar' ); ?>
                    </label>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="product_ids"><?php esc_html_e( 'Products (Apply to specific products)', 'bazaar' ); ?></label>
                    <select name="product_ids[]" id="product_ids" class="bazaar-product-search" multiple>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary"><?php esc_html_e( 'Save Coupon', 'bazaar' ); ?></button>
                <button type="button" class="button bazaar-cancel-coupon"><?php esc_html_e( 'Cancel', 'bazaar' ); ?></button>
            </div>
        </form>
    </div>

    <!-- Coupons List -->
    <?php if ( empty( $coupons ) ) : ?>
        <div class="bazaar-empty-state">
            <span class="dashicons dashicons-tickets-alt"></span>
            <h3><?php esc_html_e( 'No coupons found', 'bazaar' ); ?></h3>
            <p><?php esc_html_e( 'Create coupons to offer discounts on your products.', 'bazaar' ); ?></p>
        </div>
    <?php else : ?>
        <table class="bazaar-table bazaar-coupons-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Code', 'bazaar' ); ?></th>
                    <th><?php esc_html_e( 'Discount', 'bazaar' ); ?></th>
                    <th><?php esc_html_e( 'Usage / Limit', 'bazaar' ); ?></th>
                    <th><?php esc_html_e( 'Expiry', 'bazaar' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'bazaar' ); ?></th>
                    <th><?php esc_html_e( 'Actions', 'bazaar' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $coupons as $coupon ) :
                    $coupon_obj = new WC_Coupon( $coupon->ID );
                    $discount_type = $coupon_obj->get_discount_type();
                    $amount = $coupon_obj->get_amount();
                    $usage_count = $coupon_obj->get_usage_count();
                    $usage_limit = $coupon_obj->get_usage_limit();
                    $expiry_date = $coupon_obj->get_date_expires();
                    $is_expired = $expiry_date && $expiry_date->getTimestamp() < time();
                ?>
                    <tr class="<?php echo $is_expired ? 'expired' : ''; ?>">
                        <td>
                            <strong><?php echo esc_html( $coupon_obj->get_code() ); ?></strong>
                            <button type="button" class="copy-code" data-code="<?php echo esc_attr( $coupon_obj->get_code() ); ?>" title="<?php esc_attr_e( 'Copy code', 'bazaar' ); ?>">
                                <span class="dashicons dashicons-clipboard"></span>
                            </button>
                        </td>
                        <td>
                            <?php
                            if ( 'percent' === $discount_type ) {
                                echo esc_html( $amount . '%' );
                            } else {
                                echo wc_price( $amount );
                            }
                            ?>
                            <span class="discount-type"><?php echo esc_html( wc_get_coupon_type( $discount_type ) ); ?></span>
                        </td>
                        <td>
                            <?php echo esc_html( $usage_count ); ?> / <?php echo $usage_limit ? esc_html( $usage_limit ) : '∞'; ?>
                        </td>
                        <td>
                            <?php
                            if ( $expiry_date ) {
                                echo esc_html( date_i18n( get_option( 'date_format' ), $expiry_date->getTimestamp() ) );
                            } else {
                                esc_html_e( 'No expiry', 'bazaar' );
                            }
                            ?>
                        </td>
                        <td>
                            <?php if ( $is_expired ) : ?>
                                <span class="status-badge status-expired"><?php esc_html_e( 'Expired', 'bazaar' ); ?></span>
                            <?php elseif ( 'publish' === $coupon->post_status ) : ?>
                                <span class="status-badge status-active"><?php esc_html_e( 'Active', 'bazaar' ); ?></span>
                            <?php else : ?>
                                <span class="status-badge status-draft"><?php esc_html_e( 'Draft', 'bazaar' ); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <button type="button" class="button button-small edit-coupon" data-coupon-id="<?php echo esc_attr( $coupon->ID ); ?>">
                                <?php esc_html_e( 'Edit', 'bazaar' ); ?>
                            </button>
                            <button type="button" class="button button-small button-link-delete delete-coupon" data-coupon-id="<?php echo esc_attr( $coupon->ID ); ?>">
                                <?php esc_html_e( 'Delete', 'bazaar' ); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
