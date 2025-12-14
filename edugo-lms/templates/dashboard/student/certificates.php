<?php
/**
 * Student Dashboard - Certificates Tab.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$user_id = get_current_user_id();
$certificate_manager = new Edugo_LMS\LMS\Certificate\Certificate_Manager();
$certificates = $certificate_manager->get_user_certificates( $user_id );
?>

<div class="edugo-dashboard-header">
    <h2><?php esc_html_e( 'My Certificates', 'edugo-lms' ); ?></h2>
</div>

<?php if ( ! empty( $certificates ) ) : ?>
    <div class="edugo-certificates-grid">
        <?php foreach ( $certificates as $certificate ) :
            $course = get_post( $certificate->course_id );

            if ( ! $course ) continue;

            $download_url = $certificate_manager->get_download_url( $user_id, $certificate->course_id );
            $verification_url = $certificate_manager->get_verification_url( $certificate->certificate_key );
            ?>
            <div class="edugo-certificate-card">
                <div class="edugo-certificate-icon">
                    <span class="dashicons dashicons-awards"></span>
                </div>

                <div class="edugo-certificate-details">
                    <h3 class="edugo-certificate-course"><?php echo esc_html( $course->post_title ); ?></h3>

                    <div class="edugo-certificate-meta">
                        <span class="edugo-certificate-date">
                            <?php
                            printf(
                                /* translators: %s: Issue date */
                                esc_html__( 'Issued: %s', 'edugo-lms' ),
                                esc_html( date_i18n( get_option( 'date_format' ), strtotime( $certificate->issued_at ) ) )
                            );
                            ?>
                        </span>
                        <span class="edugo-certificate-id">
                            <?php
                            printf(
                                /* translators: %s: Certificate ID */
                                esc_html__( 'ID: %s', 'edugo-lms' ),
                                esc_html( $certificate->certificate_key )
                            );
                            ?>
                        </span>
                    </div>
                </div>

                <div class="edugo-certificate-actions">
                    <?php if ( $download_url ) : ?>
                        <a href="<?php echo esc_url( $download_url ); ?>" class="edugo-button edugo-button-primary" target="_blank">
                            <span class="dashicons dashicons-download"></span>
                            <?php esc_html_e( 'Download', 'edugo-lms' ); ?>
                        </a>
                    <?php endif; ?>

                    <a href="<?php echo esc_url( $verification_url ); ?>" class="edugo-button edugo-button-secondary" target="_blank">
                        <span class="dashicons dashicons-yes-alt"></span>
                        <?php esc_html_e( 'Verify', 'edugo-lms' ); ?>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else : ?>
    <div class="edugo-empty-state">
        <div class="edugo-empty-icon">
            <span class="dashicons dashicons-awards"></span>
        </div>
        <h3><?php esc_html_e( 'No Certificates Yet', 'edugo-lms' ); ?></h3>
        <p><?php esc_html_e( 'Complete courses to earn certificates. Keep learning!', 'edugo-lms' ); ?></p>
        <a href="<?php echo esc_url( add_query_arg( 'tab', 'courses' ) ); ?>" class="edugo-button edugo-button-primary">
            <?php esc_html_e( 'View My Courses', 'edugo-lms' ); ?>
        </a>
    </div>
<?php endif; ?>
