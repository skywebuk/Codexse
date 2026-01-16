<?php
/**
 * Form Builder admin page template
 *
 * @package MailHive
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$default_markup = mailhive()->get_default_form_markup();
$form_markup = get_option( 'mailhive_form_markup', $default_markup );
$form_css = get_option( 'mailhive_form_css', '' );
?>

<div class="wrap mailhive-wrap">
    <h1><?php esc_html_e( 'Form Builder', 'mailhive' ); ?></h1>
    <p class="description"><?php esc_html_e( 'Customize your subscription form. Use the shortcode [mailhive_form] to display it on your site.', 'mailhive' ); ?></p>

    <div class="mailhive-shortcode-info">
        <strong><?php esc_html_e( 'Shortcode:', 'mailhive' ); ?></strong>
        <code id="mailhive-shortcode">[mailhive_form]</code>
        <button type="button" class="button button-small mailhive-copy-shortcode" data-clipboard-target="#mailhive-shortcode">
            <?php esc_html_e( 'Copy', 'mailhive' ); ?>
        </button>
    </div>

    <div class="mailhive-form-builder">
        <div class="mailhive-builder-panel">
            <h2><?php esc_html_e( 'Add Fields', 'mailhive' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Click to insert field HTML at cursor position.', 'mailhive' ); ?></p>

            <div class="mailhive-field-buttons">
                <button type="button" class="button mailhive-add-field" data-field="email">
                    <span class="dashicons dashicons-email"></span>
                    <?php esc_html_e( 'Email Field', 'mailhive' ); ?>
                </button>
                <button type="button" class="button mailhive-add-field" data-field="text">
                    <span class="dashicons dashicons-editor-textcolor"></span>
                    <?php esc_html_e( 'Text Field', 'mailhive' ); ?>
                </button>
                <button type="button" class="button mailhive-add-field" data-field="name">
                    <span class="dashicons dashicons-admin-users"></span>
                    <?php esc_html_e( 'Name Field', 'mailhive' ); ?>
                </button>
                <button type="button" class="button mailhive-add-field" data-field="phone">
                    <span class="dashicons dashicons-phone"></span>
                    <?php esc_html_e( 'Phone Field', 'mailhive' ); ?>
                </button>
                <button type="button" class="button mailhive-add-field" data-field="textarea">
                    <span class="dashicons dashicons-text"></span>
                    <?php esc_html_e( 'Textarea', 'mailhive' ); ?>
                </button>
                <button type="button" class="button mailhive-add-field" data-field="checkbox">
                    <span class="dashicons dashicons-yes"></span>
                    <?php esc_html_e( 'Checkbox', 'mailhive' ); ?>
                </button>
                <button type="button" class="button mailhive-add-field" data-field="select">
                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                    <?php esc_html_e( 'Dropdown', 'mailhive' ); ?>
                </button>
                <button type="button" class="button mailhive-add-field" data-field="submit">
                    <span class="dashicons dashicons-migrate"></span>
                    <?php esc_html_e( 'Submit Button', 'mailhive' ); ?>
                </button>
            </div>

            <hr>

            <h3><?php esc_html_e( 'Form HTML', 'mailhive' ); ?></h3>
            <p class="description"><?php esc_html_e( 'Edit the form HTML directly. The email field with name="email" is required.', 'mailhive' ); ?></p>
            <textarea id="mailhive-form-markup" class="large-text code" rows="15"><?php echo esc_textarea( $form_markup ); ?></textarea>

            <div class="mailhive-markup-actions">
                <button type="button" class="button mailhive-reset-markup">
                    <?php esc_html_e( 'Reset to Default', 'mailhive' ); ?>
                </button>
            </div>

            <hr>

            <h3><?php esc_html_e( 'Custom CSS', 'mailhive' ); ?></h3>
            <p class="description"><?php esc_html_e( 'Add custom styles for your form. Styles will be scoped to the form wrapper.', 'mailhive' ); ?></p>
            <textarea id="mailhive-form-css" class="large-text code" rows="10" placeholder="/* Example:
.mailhive-form-wrapper {
    max-width: 400px;
}
.mailhive-field input {
    border-radius: 4px;
}
*/"><?php echo esc_textarea( $form_css ); ?></textarea>

            <hr>

            <h3><?php esc_html_e( 'Style Options', 'mailhive' ); ?></h3>
            <div class="mailhive-style-options">
                <div class="mailhive-style-option">
                    <label for="mailhive-form-width"><?php esc_html_e( 'Form Width', 'mailhive' ); ?></label>
                    <input type="text" id="mailhive-form-width" placeholder="400px or 100%">
                    <button type="button" class="button button-small mailhive-apply-style" data-style="width">
                        <?php esc_html_e( 'Apply', 'mailhive' ); ?>
                    </button>
                </div>
                <div class="mailhive-style-option">
                    <label for="mailhive-btn-color"><?php esc_html_e( 'Button Color', 'mailhive' ); ?></label>
                    <input type="color" id="mailhive-btn-color" value="#0073aa">
                    <button type="button" class="button button-small mailhive-apply-style" data-style="button-color">
                        <?php esc_html_e( 'Apply', 'mailhive' ); ?>
                    </button>
                </div>
                <div class="mailhive-style-option">
                    <label for="mailhive-btn-text-color"><?php esc_html_e( 'Button Text Color', 'mailhive' ); ?></label>
                    <input type="color" id="mailhive-btn-text-color" value="#ffffff">
                    <button type="button" class="button button-small mailhive-apply-style" data-style="button-text-color">
                        <?php esc_html_e( 'Apply', 'mailhive' ); ?>
                    </button>
                </div>
                <div class="mailhive-style-option">
                    <label for="mailhive-border-radius"><?php esc_html_e( 'Border Radius', 'mailhive' ); ?></label>
                    <input type="text" id="mailhive-border-radius" placeholder="4px">
                    <button type="button" class="button button-small mailhive-apply-style" data-style="border-radius">
                        <?php esc_html_e( 'Apply', 'mailhive' ); ?>
                    </button>
                </div>
            </div>

            <hr>

            <div class="mailhive-save-actions">
                <button type="button" id="mailhive-save-form" class="button button-primary button-large">
                    <?php esc_html_e( 'Save Form', 'mailhive' ); ?>
                </button>
                <span class="mailhive-save-status"></span>
            </div>
        </div>

        <div class="mailhive-preview-panel">
            <h2><?php esc_html_e( 'Live Preview', 'mailhive' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Preview updates as you type.', 'mailhive' ); ?></p>

            <div class="mailhive-preview-container">
                <div id="mailhive-preview" class="mailhive-form-wrapper">
                    <style id="mailhive-preview-css"><?php echo wp_strip_all_tags( $form_css ); ?></style>
                    <form class="mailhive-form" onsubmit="return false;">
                        <?php echo wp_kses_post( $form_markup ); ?>
                    </form>
                    <div class="mailhive-message" style="display: none;"></div>
                </div>
            </div>

            <div class="mailhive-preview-actions">
                <button type="button" class="button mailhive-preview-success">
                    <?php esc_html_e( 'Preview Success Message', 'mailhive' ); ?>
                </button>
                <button type="button" class="button mailhive-preview-error">
                    <?php esc_html_e( 'Preview Error Message', 'mailhive' ); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/html" id="tmpl-mailhive-field-email">
<div class="mailhive-field">
    <label for="mailhive-email">Email Address</label>
    <input type="email" id="mailhive-email" name="email" placeholder="Enter your email" required>
</div>
</script>

<script type="text/html" id="tmpl-mailhive-field-text">
<div class="mailhive-field">
    <label for="mailhive-text">Text Field</label>
    <input type="text" id="mailhive-text" name="text_field" placeholder="Enter text">
</div>
</script>

<script type="text/html" id="tmpl-mailhive-field-name">
<div class="mailhive-field">
    <label for="mailhive-name">Your Name</label>
    <input type="text" id="mailhive-name" name="name" placeholder="Enter your name">
</div>
</script>

<script type="text/html" id="tmpl-mailhive-field-phone">
<div class="mailhive-field">
    <label for="mailhive-phone">Phone Number</label>
    <input type="tel" id="mailhive-phone" name="phone" placeholder="Enter phone number">
</div>
</script>

<script type="text/html" id="tmpl-mailhive-field-textarea">
<div class="mailhive-field">
    <label for="mailhive-message">Message</label>
    <textarea id="mailhive-message" name="message" rows="4" placeholder="Enter your message"></textarea>
</div>
</script>

<script type="text/html" id="tmpl-mailhive-field-checkbox">
<div class="mailhive-field mailhive-field-checkbox">
    <label>
        <input type="checkbox" name="agreement" value="yes">
        I agree to receive emails
    </label>
</div>
</script>

<script type="text/html" id="tmpl-mailhive-field-select">
<div class="mailhive-field">
    <label for="mailhive-select">Select Option</label>
    <select id="mailhive-select" name="selection">
        <option value="">Choose...</option>
        <option value="option1">Option 1</option>
        <option value="option2">Option 2</option>
        <option value="option3">Option 3</option>
    </select>
</div>
</script>

<script type="text/html" id="tmpl-mailhive-field-submit">
<div class="mailhive-field">
    <button type="submit" class="mailhive-submit">Subscribe</button>
</div>
</script>

<script type="text/html" id="tmpl-mailhive-default-markup"><?php echo esc_html( $default_markup ); ?></script>
