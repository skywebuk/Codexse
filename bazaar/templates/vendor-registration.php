<?php
/**
 * Vendor Registration Template.
 *
 * @package Bazaar\Templates
 */

defined( 'ABSPATH' ) || exit;

$registration_enabled = bazaar_get_option( 'enable_vendor_registration', 'yes' );

if ( 'yes' !== $registration_enabled ) {
    echo '<div class="bazaar-notice notice-error">' . esc_html__( 'Vendor registration is currently disabled.', 'bazaar' ) . '</div>';
    return;
}

if ( is_user_logged_in() ) {
    $current_user = wp_get_current_user();

    if ( bazaar_is_vendor( $current_user->ID ) ) {
        echo '<div class="bazaar-notice notice-info">';
        echo '<p>' . esc_html__( 'You are already registered as a vendor.', 'bazaar' ) . '</p>';
        echo '<a href="' . esc_url( bazaar_get_dashboard_url() ) . '" class="button button-primary">' . esc_html__( 'Go to Dashboard', 'bazaar' ) . '</a>';
        echo '</div>';
        return;
    }

    if ( bazaar_has_pending_vendor_request( $current_user->ID ) ) {
        echo '<div class="bazaar-notice notice-warning">';
        echo '<p>' . esc_html__( 'Your vendor application is pending approval. You will be notified once it is reviewed.', 'bazaar' ) . '</p>';
        echo '</div>';
        return;
    }
}
?>

<div class="bazaar-vendor-registration">
    <div class="registration-header">
        <h2><?php esc_html_e( 'Become a Vendor', 'bazaar' ); ?></h2>
        <p><?php esc_html_e( 'Join our marketplace and start selling your products to thousands of customers.', 'bazaar' ); ?></p>
    </div>

    <!-- Benefits Section -->
    <div class="vendor-benefits">
        <div class="benefit-item">
            <span class="dashicons dashicons-store"></span>
            <h4><?php esc_html_e( 'Your Own Store', 'bazaar' ); ?></h4>
            <p><?php esc_html_e( 'Get your personalized storefront with your branding.', 'bazaar' ); ?></p>
        </div>
        <div class="benefit-item">
            <span class="dashicons dashicons-chart-line"></span>
            <h4><?php esc_html_e( 'Track Sales', 'bazaar' ); ?></h4>
            <p><?php esc_html_e( 'Monitor your earnings and analytics in real-time.', 'bazaar' ); ?></p>
        </div>
        <div class="benefit-item">
            <span class="dashicons dashicons-money-alt"></span>
            <h4><?php esc_html_e( 'Easy Payments', 'bazaar' ); ?></h4>
            <p><?php esc_html_e( 'Receive payments directly via multiple methods.', 'bazaar' ); ?></p>
        </div>
        <div class="benefit-item">
            <span class="dashicons dashicons-groups"></span>
            <h4><?php esc_html_e( 'Reach Customers', 'bazaar' ); ?></h4>
            <p><?php esc_html_e( 'Access our established customer base instantly.', 'bazaar' ); ?></p>
        </div>
    </div>

    <!-- Registration Form -->
    <div class="registration-form-wrapper">
        <form id="bazaar-vendor-registration-form" class="bazaar-form" method="post">
            <?php wp_nonce_field( 'bazaar_vendor_registration', 'registration_nonce' ); ?>

            <?php if ( ! is_user_logged_in() ) : ?>
                <!-- Account Section -->
                <div class="form-section">
                    <h3><?php esc_html_e( 'Account Information', 'bazaar' ); ?></h3>

                    <div class="form-row">
                        <div class="form-group half">
                            <label for="first_name"><?php esc_html_e( 'First Name', 'bazaar' ); ?> <span class="required">*</span></label>
                            <input type="text" name="first_name" id="first_name" required />
                        </div>
                        <div class="form-group half">
                            <label for="last_name"><?php esc_html_e( 'Last Name', 'bazaar' ); ?> <span class="required">*</span></label>
                            <input type="text" name="last_name" id="last_name" required />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email"><?php esc_html_e( 'Email Address', 'bazaar' ); ?> <span class="required">*</span></label>
                            <input type="email" name="email" id="email" required />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group half">
                            <label for="username"><?php esc_html_e( 'Username', 'bazaar' ); ?> <span class="required">*</span></label>
                            <input type="text" name="username" id="username" required />
                        </div>
                        <div class="form-group half">
                            <label for="phone"><?php esc_html_e( 'Phone Number', 'bazaar' ); ?></label>
                            <input type="tel" name="phone" id="phone" />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group half">
                            <label for="password"><?php esc_html_e( 'Password', 'bazaar' ); ?> <span class="required">*</span></label>
                            <input type="password" name="password" id="password" required minlength="8" />
                            <span class="description"><?php esc_html_e( 'Minimum 8 characters', 'bazaar' ); ?></span>
                        </div>
                        <div class="form-group half">
                            <label for="confirm_password"><?php esc_html_e( 'Confirm Password', 'bazaar' ); ?> <span class="required">*</span></label>
                            <input type="password" name="confirm_password" id="confirm_password" required />
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Store Section -->
            <div class="form-section">
                <h3><?php esc_html_e( 'Store Information', 'bazaar' ); ?></h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="store_name"><?php esc_html_e( 'Store Name', 'bazaar' ); ?> <span class="required">*</span></label>
                        <input type="text" name="store_name" id="store_name" required />
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="store_url"><?php esc_html_e( 'Store URL', 'bazaar' ); ?> <span class="required">*</span></label>
                        <div class="input-with-prefix">
                            <span class="input-prefix"><?php echo esc_html( home_url( '/' . bazaar_get_option( 'vendor_store_slug', 'store' ) . '/' ) ); ?></span>
                            <input type="text" name="store_url" id="store_url" required pattern="[a-z0-9-]+" />
                        </div>
                        <span class="description"><?php esc_html_e( 'Only lowercase letters, numbers, and hyphens allowed.', 'bazaar' ); ?></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="store_description"><?php esc_html_e( 'Store Description', 'bazaar' ); ?></label>
                        <textarea name="store_description" id="store_description" rows="4" placeholder="<?php esc_attr_e( 'Tell customers about your store...', 'bazaar' ); ?>"></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="product_categories"><?php esc_html_e( 'What will you sell?', 'bazaar' ); ?></label>
                        <select name="product_categories[]" id="product_categories" class="bazaar-select2" multiple>
                            <?php
                            $categories = get_terms( array(
                                'taxonomy'   => 'product_cat',
                                'hide_empty' => false,
                            ) );
                            foreach ( $categories as $category ) {
                                echo '<option value="' . esc_attr( $category->term_id ) . '">' . esc_html( $category->name ) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Address Section -->
            <div class="form-section">
                <h3><?php esc_html_e( 'Business Address', 'bazaar' ); ?></h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="address_street"><?php esc_html_e( 'Street Address', 'bazaar' ); ?></label>
                        <input type="text" name="address_street" id="address_street" />
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="address_city"><?php esc_html_e( 'City', 'bazaar' ); ?></label>
                        <input type="text" name="address_city" id="address_city" />
                    </div>
                    <div class="form-group half">
                        <label for="address_state"><?php esc_html_e( 'State / Province', 'bazaar' ); ?></label>
                        <input type="text" name="address_state" id="address_state" />
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="address_postcode"><?php esc_html_e( 'Postcode / ZIP', 'bazaar' ); ?></label>
                        <input type="text" name="address_postcode" id="address_postcode" />
                    </div>
                    <div class="form-group half">
                        <label for="address_country"><?php esc_html_e( 'Country', 'bazaar' ); ?></label>
                        <select name="address_country" id="address_country">
                            <option value=""><?php esc_html_e( 'Select Country', 'bazaar' ); ?></option>
                            <?php
                            $countries = WC()->countries->get_countries();
                            foreach ( $countries as $code => $name ) {
                                echo '<option value="' . esc_attr( $code ) . '">' . esc_html( $name ) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="form-section">
                <h3><?php esc_html_e( 'Payment Information', 'bazaar' ); ?></h3>
                <p class="section-description"><?php esc_html_e( 'This information will be used to send your earnings. You can update it later.', 'bazaar' ); ?></p>

                <div class="form-row">
                    <div class="form-group">
                        <label for="payment_method"><?php esc_html_e( 'Preferred Payment Method', 'bazaar' ); ?></label>
                        <select name="payment_method" id="payment_method">
                            <option value="paypal"><?php esc_html_e( 'PayPal', 'bazaar' ); ?></option>
                            <option value="bank_transfer"><?php esc_html_e( 'Bank Transfer', 'bazaar' ); ?></option>
                            <?php if ( bazaar_is_stripe_enabled() ) : ?>
                                <option value="stripe"><?php esc_html_e( 'Stripe', 'bazaar' ); ?></option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row payment-fields paypal-fields">
                    <div class="form-group">
                        <label for="paypal_email"><?php esc_html_e( 'PayPal Email', 'bazaar' ); ?></label>
                        <input type="email" name="paypal_email" id="paypal_email" />
                    </div>
                </div>

                <div class="form-row payment-fields bank-fields" style="display: none;">
                    <div class="form-group half">
                        <label for="bank_account_name"><?php esc_html_e( 'Account Name', 'bazaar' ); ?></label>
                        <input type="text" name="bank_account_name" id="bank_account_name" />
                    </div>
                    <div class="form-group half">
                        <label for="bank_account_number"><?php esc_html_e( 'Account Number', 'bazaar' ); ?></label>
                        <input type="text" name="bank_account_number" id="bank_account_number" />
                    </div>
                </div>

                <div class="form-row payment-fields bank-fields" style="display: none;">
                    <div class="form-group half">
                        <label for="bank_name"><?php esc_html_e( 'Bank Name', 'bazaar' ); ?></label>
                        <input type="text" name="bank_name" id="bank_name" />
                    </div>
                    <div class="form-group half">
                        <label for="bank_routing"><?php esc_html_e( 'Routing Number / SWIFT', 'bazaar' ); ?></label>
                        <input type="text" name="bank_routing" id="bank_routing" />
                    </div>
                </div>
            </div>

            <!-- Terms Section -->
            <div class="form-section terms-section">
                <div class="form-row">
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="agree_terms" required />
                            <?php
                            $terms_page = bazaar_get_option( 'vendor_terms_page' );
                            if ( $terms_page ) {
                                printf(
                                    wp_kses(
                                        __( 'I have read and agree to the <a href="%s" target="_blank">Vendor Terms and Conditions</a>', 'bazaar' ),
                                        array( 'a' => array( 'href' => array(), 'target' => array() ) )
                                    ),
                                    esc_url( get_permalink( $terms_page ) )
                                );
                            } else {
                                esc_html_e( 'I agree to the vendor terms and conditions', 'bazaar' );
                            }
                            ?>
                            <span class="required">*</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary button-large">
                    <?php esc_html_e( 'Submit Application', 'bazaar' ); ?>
                </button>
            </div>
        </form>
    </div>

    <?php if ( ! is_user_logged_in() ) : ?>
        <div class="registration-footer">
            <p>
                <?php esc_html_e( 'Already have an account?', 'bazaar' ); ?>
                <a href="<?php echo esc_url( wp_login_url( bazaar_get_vendor_registration_url() ) ); ?>"><?php esc_html_e( 'Log in', 'bazaar' ); ?></a>
            </p>
        </div>
    <?php endif; ?>
</div>
