<?php
/**
 * Dashboard Settings Tab.
 *
 * @package Bazaar\Templates
 */

defined( 'ABSPATH' ) || exit;

$countries = bazaar_get_countries();
?>
<div class="bazaar-tab-content bazaar-settings">
    <h2><?php esc_html_e( 'Store Settings', 'bazaar' ); ?></h2>

    <form id="vendor-settings-form" class="bazaar-form">
        <!-- Store Information -->
        <div class="settings-section">
            <h3><?php esc_html_e( 'Store Information', 'bazaar' ); ?></h3>

            <div class="form-row form-row-half">
                <label for="store_name"><?php esc_html_e( 'Store Name', 'bazaar' ); ?> <span class="required">*</span></label>
                <input type="text" id="store_name" name="store_name" value="<?php echo esc_attr( $vendor['store_name'] ); ?>" required />
            </div>

            <div class="form-row form-row-half">
                <label for="store_phone"><?php esc_html_e( 'Phone', 'bazaar' ); ?></label>
                <input type="tel" id="store_phone" name="store_phone" value="<?php echo esc_attr( $vendor['phone'] ); ?>" />
            </div>

            <div class="form-row">
                <label for="store_description"><?php esc_html_e( 'Store Description', 'bazaar' ); ?></label>
                <textarea id="store_description" name="store_description" rows="5"><?php echo esc_textarea( $vendor['description'] ); ?></textarea>
            </div>

            <div class="form-row form-row-half">
                <label><?php esc_html_e( 'Store Logo', 'bazaar' ); ?></label>
                <div class="image-upload" data-target="store_logo">
                    <div class="image-preview">
                        <?php if ( $vendor['logo'] ) : ?>
                            <?php echo wp_get_attachment_image( $vendor['logo'], 'thumbnail' ); ?>
                        <?php else : ?>
                            <span class="dashicons dashicons-format-image"></span>
                        <?php endif; ?>
                    </div>
                    <input type="hidden" name="store_logo" value="<?php echo esc_attr( $vendor['logo'] ); ?>" />
                    <button type="button" class="button upload-image"><?php esc_html_e( 'Upload Logo', 'bazaar' ); ?></button>
                    <button type="button" class="button remove-image" <?php echo ! $vendor['logo'] ? 'style="display:none;"' : ''; ?>><?php esc_html_e( 'Remove', 'bazaar' ); ?></button>
                </div>
            </div>

            <div class="form-row form-row-half">
                <label><?php esc_html_e( 'Store Banner', 'bazaar' ); ?></label>
                <div class="image-upload" data-target="store_banner">
                    <div class="image-preview banner-preview">
                        <?php if ( $vendor['banner'] ) : ?>
                            <?php echo wp_get_attachment_image( $vendor['banner'], 'medium' ); ?>
                        <?php else : ?>
                            <span class="dashicons dashicons-format-image"></span>
                        <?php endif; ?>
                    </div>
                    <input type="hidden" name="store_banner" value="<?php echo esc_attr( $vendor['banner'] ); ?>" />
                    <button type="button" class="button upload-image"><?php esc_html_e( 'Upload Banner', 'bazaar' ); ?></button>
                    <button type="button" class="button remove-image" <?php echo ! $vendor['banner'] ? 'style="display:none;"' : ''; ?>><?php esc_html_e( 'Remove', 'bazaar' ); ?></button>
                </div>
            </div>
        </div>

        <!-- Address -->
        <div class="settings-section">
            <h3><?php esc_html_e( 'Address', 'bazaar' ); ?></h3>

            <div class="form-row">
                <label for="address_1"><?php esc_html_e( 'Street Address', 'bazaar' ); ?></label>
                <input type="text" id="address_1" name="address_1" value="<?php echo esc_attr( $vendor['address']['street_1'] ); ?>" />
            </div>

            <div class="form-row">
                <label for="address_2"><?php esc_html_e( 'Address Line 2', 'bazaar' ); ?></label>
                <input type="text" id="address_2" name="address_2" value="<?php echo esc_attr( $vendor['address']['street_2'] ); ?>" />
            </div>

            <div class="form-row form-row-half">
                <label for="city"><?php esc_html_e( 'City', 'bazaar' ); ?></label>
                <input type="text" id="city" name="city" value="<?php echo esc_attr( $vendor['address']['city'] ); ?>" />
            </div>

            <div class="form-row form-row-half">
                <label for="state"><?php esc_html_e( 'State/Province', 'bazaar' ); ?></label>
                <input type="text" id="state" name="state" value="<?php echo esc_attr( $vendor['address']['state'] ); ?>" />
            </div>

            <div class="form-row form-row-half">
                <label for="postcode"><?php esc_html_e( 'Postcode', 'bazaar' ); ?></label>
                <input type="text" id="postcode" name="postcode" value="<?php echo esc_attr( $vendor['address']['postcode'] ); ?>" />
            </div>

            <div class="form-row form-row-half">
                <label for="country"><?php esc_html_e( 'Country', 'bazaar' ); ?></label>
                <select id="country" name="country">
                    <option value=""><?php esc_html_e( 'Select Country', 'bazaar' ); ?></option>
                    <?php foreach ( $countries as $code => $name ) : ?>
                        <option value="<?php echo esc_attr( $code ); ?>" <?php selected( $vendor['address']['country'], $code ); ?>>
                            <?php echo esc_html( $name ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Social Links -->
        <div class="settings-section">
            <h3><?php esc_html_e( 'Social Links', 'bazaar' ); ?></h3>

            <div class="form-row form-row-half">
                <label for="social_facebook"><?php esc_html_e( 'Facebook', 'bazaar' ); ?></label>
                <input type="url" id="social_facebook" name="social_facebook" value="<?php echo esc_url( $vendor['social']['facebook'] ); ?>" placeholder="https://facebook.com/..." />
            </div>

            <div class="form-row form-row-half">
                <label for="social_twitter"><?php esc_html_e( 'Twitter', 'bazaar' ); ?></label>
                <input type="url" id="social_twitter" name="social_twitter" value="<?php echo esc_url( $vendor['social']['twitter'] ); ?>" placeholder="https://twitter.com/..." />
            </div>

            <div class="form-row form-row-half">
                <label for="social_instagram"><?php esc_html_e( 'Instagram', 'bazaar' ); ?></label>
                <input type="url" id="social_instagram" name="social_instagram" value="<?php echo esc_url( $vendor['social']['instagram'] ); ?>" placeholder="https://instagram.com/..." />
            </div>

            <div class="form-row form-row-half">
                <label for="social_youtube"><?php esc_html_e( 'YouTube', 'bazaar' ); ?></label>
                <input type="url" id="social_youtube" name="social_youtube" value="<?php echo esc_url( $vendor['social']['youtube'] ); ?>" placeholder="https://youtube.com/..." />
            </div>
        </div>

        <!-- Vacation Mode -->
        <?php if ( 'yes' === get_option( 'bazaar_vacation_mode_enabled', 'yes' ) ) : ?>
            <div class="settings-section">
                <h3><?php esc_html_e( 'Vacation Mode', 'bazaar' ); ?></h3>
                <p class="description"><?php esc_html_e( 'Enable vacation mode to temporarily close your store.', 'bazaar' ); ?></p>

                <div class="form-row">
                    <label class="checkbox-label">
                        <input type="checkbox" id="vacation_mode" name="vacation_mode" value="yes" <?php checked( $vendor['vacation_mode'], 'yes' ); ?> />
                        <?php esc_html_e( 'Enable Vacation Mode', 'bazaar' ); ?>
                    </label>
                </div>

                <div class="form-row vacation-message" <?php echo 'yes' !== $vendor['vacation_mode'] ? 'style="display:none;"' : ''; ?>>
                    <label for="vacation_message"><?php esc_html_e( 'Vacation Message', 'bazaar' ); ?></label>
                    <textarea id="vacation_message" name="vacation_message" rows="3"><?php echo esc_textarea( get_user_meta( $vendor['id'], '_bazaar_vacation_message', true ) ); ?></textarea>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-actions">
            <button type="submit" class="button button-primary">
                <?php esc_html_e( 'Save Settings', 'bazaar' ); ?>
            </button>
        </div>
    </form>
</div>
