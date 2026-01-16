<?php
/**
 * Settings admin page template
 *
 * @package MailHive
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$success_message = get_option( 'mailhive_success_message', __( 'Thank you for subscribing!', 'mailhive' ) );
$error_message = get_option( 'mailhive_error_message', __( 'An error occurred. Please try again.', 'mailhive' ) );
$duplicate_message = get_option( 'mailhive_duplicate_message', __( 'This email is already subscribed.', 'mailhive' ) );

// Handle form submission
if ( isset( $_POST['mailhive_settings_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mailhive_settings_nonce'] ) ), 'mailhive_save_settings' ) ) {
    if ( current_user_can( 'manage_options' ) ) {
        $success_message = isset( $_POST['mailhive_success_message'] ) ? sanitize_text_field( wp_unslash( $_POST['mailhive_success_message'] ) ) : '';
        $error_message = isset( $_POST['mailhive_error_message'] ) ? sanitize_text_field( wp_unslash( $_POST['mailhive_error_message'] ) ) : '';
        $duplicate_message = isset( $_POST['mailhive_duplicate_message'] ) ? sanitize_text_field( wp_unslash( $_POST['mailhive_duplicate_message'] ) ) : '';

        update_option( 'mailhive_success_message', $success_message );
        update_option( 'mailhive_error_message', $error_message );
        update_option( 'mailhive_duplicate_message', $duplicate_message );

        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved successfully.', 'mailhive' ) . '</p></div>';
    }
}
?>

<div class="wrap mailhive-wrap">
    <h1><?php esc_html_e( 'MailHive Settings', 'mailhive' ); ?></h1>

    <form method="post" class="mailhive-settings-form">
        <?php wp_nonce_field( 'mailhive_save_settings', 'mailhive_settings_nonce' ); ?>

        <div class="mailhive-settings-section">
            <h2><?php esc_html_e( 'Form Messages', 'mailhive' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Customize the messages displayed to users after form submission.', 'mailhive' ); ?></p>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="mailhive_success_message"><?php esc_html_e( 'Success Message', 'mailhive' ); ?></label>
                    </th>
                    <td>
                        <input type="text"
                               id="mailhive_success_message"
                               name="mailhive_success_message"
                               value="<?php echo esc_attr( $success_message ); ?>"
                               class="large-text">
                        <p class="description"><?php esc_html_e( 'Displayed when a user successfully subscribes.', 'mailhive' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="mailhive_error_message"><?php esc_html_e( 'Error Message', 'mailhive' ); ?></label>
                    </th>
                    <td>
                        <input type="text"
                               id="mailhive_error_message"
                               name="mailhive_error_message"
                               value="<?php echo esc_attr( $error_message ); ?>"
                               class="large-text">
                        <p class="description"><?php esc_html_e( 'Displayed when an error occurs during subscription.', 'mailhive' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="mailhive_duplicate_message"><?php esc_html_e( 'Duplicate Message', 'mailhive' ); ?></label>
                    </th>
                    <td>
                        <input type="text"
                               id="mailhive_duplicate_message"
                               name="mailhive_duplicate_message"
                               value="<?php echo esc_attr( $duplicate_message ); ?>"
                               class="large-text">
                        <p class="description"><?php esc_html_e( 'Displayed when an email address is already subscribed.', 'mailhive' ); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="mailhive-settings-section">
            <h2><?php esc_html_e( 'Shortcode Usage', 'mailhive' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Use the following shortcode to display the subscription form on any page or post:', 'mailhive' ); ?></p>

            <div class="mailhive-shortcode-box">
                <code>[mailhive_form]</code>
            </div>

            <h3><?php esc_html_e( 'PHP Usage', 'mailhive' ); ?></h3>
            <p class="description"><?php esc_html_e( 'You can also use PHP to display the form in your theme:', 'mailhive' ); ?></p>

            <div class="mailhive-shortcode-box">
                <code>&lt;?php echo do_shortcode( '[mailhive_form]' ); ?&gt;</code>
            </div>
        </div>

        <div class="mailhive-settings-section">
            <h2><?php esc_html_e( 'Plugin Information', 'mailhive' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Version', 'mailhive' ); ?></th>
                    <td><?php echo esc_html( MAILHIVE_VERSION ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Database Version', 'mailhive' ); ?></th>
                    <td><?php echo esc_html( get_option( 'mailhive_version', '1.0.0' ) ); ?></td>
                </tr>
            </table>
        </div>

        <p class="submit">
            <button type="submit" class="button button-primary"><?php esc_html_e( 'Save Settings', 'mailhive' ); ?></button>
        </p>
    </form>
</div>
