<?php
/**
 * Certificate Verification Template.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$certificate_key = $certificate_key ?? '';
$verification = $verification ?? null;
?>

<div class="edugo-certificate-verify">
    <div class="edugo-verify-header">
        <h2><?php esc_html_e( 'Certificate Verification', 'edugo-lms' ); ?></h2>
        <p><?php esc_html_e( 'Enter a certificate ID to verify its authenticity.', 'edugo-lms' ); ?></p>
    </div>

    <form method="get" class="edugo-verify-form">
        <div class="edugo-form-group">
            <label for="certificate_key"><?php esc_html_e( 'Certificate ID', 'edugo-lms' ); ?></label>
            <div class="edugo-input-group">
                <input type="text" id="certificate_key" name="key" value="<?php echo esc_attr( $certificate_key ); ?>" placeholder="<?php esc_attr_e( 'Enter certificate ID...', 'edugo-lms' ); ?>" class="edugo-input" required>
                <button type="submit" class="edugo-button edugo-button-primary">
                    <?php esc_html_e( 'Verify', 'edugo-lms' ); ?>
                </button>
            </div>
        </div>
    </form>

    <?php if ( $certificate_key ) : ?>
        <?php if ( $verification && $verification['valid'] ) : ?>
            <div class="edugo-verify-result edugo-verify-success">
                <div class="edugo-verify-icon">
                    <span class="dashicons dashicons-yes-alt"></span>
                </div>
                <h3><?php esc_html_e( 'Certificate Verified', 'edugo-lms' ); ?></h3>
                <p><?php esc_html_e( 'This certificate is valid and authentic.', 'edugo-lms' ); ?></p>

                <div class="edugo-certificate-details">
                    <table>
                        <tr>
                            <th><?php esc_html_e( 'Certificate ID', 'edugo-lms' ); ?></th>
                            <td><?php echo esc_html( $verification['certificate_key'] ); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Student Name', 'edugo-lms' ); ?></th>
                            <td><?php echo esc_html( $verification['student_name'] ); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Course', 'edugo-lms' ); ?></th>
                            <td><?php echo esc_html( $verification['course_title'] ); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Issued Date', 'edugo-lms' ); ?></th>
                            <td><?php echo esc_html( $verification['issued_at_formatted'] ); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        <?php else : ?>
            <div class="edugo-verify-result edugo-verify-failed">
                <div class="edugo-verify-icon">
                    <span class="dashicons dashicons-dismiss"></span>
                </div>
                <h3><?php esc_html_e( 'Certificate Not Found', 'edugo-lms' ); ?></h3>
                <p><?php esc_html_e( 'The certificate ID you entered could not be verified. Please check and try again.', 'edugo-lms' ); ?></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
