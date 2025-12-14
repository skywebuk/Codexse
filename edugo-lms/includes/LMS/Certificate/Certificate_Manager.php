<?php
/**
 * Certificate Manager Class.
 *
 * @package Edugo_LMS\LMS\Certificate
 */

namespace Edugo_LMS\LMS\Certificate;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Certificate_Manager
 *
 * Handles course completion certificates.
 *
 * @since 1.0.0
 */
class Certificate_Manager {

    /**
     * Certificates table name.
     *
     * @var string
     */
    private string $table;

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'edugo_certificates';
    }

    /**
     * Generate a certificate for course completion.
     *
     * @since 1.0.0
     * @param int $user_id   The user ID.
     * @param int $course_id The course ID.
     * @return int|false Certificate ID on success, false on failure.
     */
    public function generate_certificate( int $user_id, int $course_id ) {
        global $wpdb;

        // Check if certificates are enabled.
        if ( get_option( 'edugo_certificate_enabled' ) !== 'yes' ) {
            return false;
        }

        // Check if certificate already exists.
        $existing = $this->get_certificate( $user_id, $course_id );
        if ( $existing ) {
            return $existing->id;
        }

        // Generate unique certificate key.
        $certificate_key = $this->generate_certificate_key();

        $result = $wpdb->insert(
            $this->table,
            array(
                'user_id'         => $user_id,
                'course_id'       => $course_id,
                'certificate_key' => $certificate_key,
                'issued_at'       => current_time( 'mysql' ),
            ),
            array( '%d', '%d', '%s', '%s' )
        );

        if ( ! $result ) {
            return false;
        }

        $certificate_id = $wpdb->insert_id;

        /**
         * Fires after a certificate is generated.
         *
         * @since 1.0.0
         * @param int    $certificate_id  The certificate ID.
         * @param int    $user_id         The user ID.
         * @param int    $course_id       The course ID.
         * @param string $certificate_key The certificate key.
         */
        do_action( 'edugo_certificate_generated', $certificate_id, $user_id, $course_id, $certificate_key );

        return $certificate_id;
    }

    /**
     * Get a certificate.
     *
     * @since 1.0.0
     * @param int $user_id   The user ID.
     * @param int $course_id The course ID.
     * @return object|null Certificate object or null.
     */
    public function get_certificate( int $user_id, int $course_id ): ?object {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE user_id = %d AND course_id = %d",
                $user_id,
                $course_id
            )
        );
    }

    /**
     * Get certificate by key.
     *
     * @since 1.0.0
     * @param string $key The certificate key.
     * @return object|null Certificate object or null.
     */
    public function get_certificate_by_key( string $key ): ?object {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE certificate_key = %s",
                $key
            )
        );
    }

    /**
     * Get user certificates.
     *
     * @since 1.0.0
     * @param int $user_id The user ID.
     * @return array Array of certificates.
     */
    public function get_user_certificates( int $user_id ): array {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE user_id = %d ORDER BY issued_at DESC",
                $user_id
            )
        );
    }

    /**
     * Verify a certificate.
     *
     * @since 1.0.0
     * @param string $key The certificate key.
     * @return array|false Verification data or false if invalid.
     */
    public function verify_certificate( string $key ) {
        $certificate = $this->get_certificate_by_key( $key );

        if ( ! $certificate ) {
            return false;
        }

        $user = get_userdata( $certificate->user_id );
        $course = get_post( $certificate->course_id );

        if ( ! $user || ! $course ) {
            return false;
        }

        return array(
            'valid'            => true,
            'certificate_id'   => $certificate->id,
            'certificate_key'  => $certificate->certificate_key,
            'student_name'     => $user->display_name,
            'student_email'    => $user->user_email,
            'course_title'     => $course->post_title,
            'issued_at'        => $certificate->issued_at,
            'issued_at_formatted' => date_i18n( get_option( 'date_format' ), strtotime( $certificate->issued_at ) ),
        );
    }

    /**
     * Generate PDF certificate.
     *
     * @since 1.0.0
     * @param int $user_id   The user ID.
     * @param int $course_id The course ID.
     * @return string|false PDF content or false on failure.
     */
    public function generate_pdf( int $user_id, int $course_id ) {
        $certificate = $this->get_certificate( $user_id, $course_id );

        if ( ! $certificate ) {
            return false;
        }

        $user = get_userdata( $user_id );
        $course = get_post( $course_id );

        if ( ! $user || ! $course ) {
            return false;
        }

        // Get template.
        $template = $this->get_certificate_template();

        // Replace placeholders.
        $replacements = array(
            '{student_name}'     => $user->display_name,
            '{course_title}'     => $course->post_title,
            '{completion_date}'  => date_i18n( get_option( 'date_format' ), strtotime( $certificate->issued_at ) ),
            '{certificate_id}'   => $certificate->certificate_key,
            '{site_name}'        => get_bloginfo( 'name' ),
            '{instructor_name}'  => get_the_author_meta( 'display_name', $course->post_author ),
        );

        $content = str_replace( array_keys( $replacements ), array_values( $replacements ), $template );

        /**
         * Filters the certificate content before PDF generation.
         *
         * @since 1.0.0
         * @param string $content      The certificate content.
         * @param object $certificate  The certificate object.
         * @param object $user         The user object.
         * @param object $course       The course post object.
         */
        $content = apply_filters( 'edugo_certificate_content', $content, $certificate, $user, $course );

        return $content;
    }

    /**
     * Get certificate template.
     *
     * @since 1.0.0
     * @return string Template HTML.
     */
    public function get_certificate_template(): string {
        $template = get_option( 'edugo_certificate_template' );

        if ( empty( $template ) ) {
            $template = $this->get_default_template();
        }

        return $template;
    }

    /**
     * Get default certificate template.
     *
     * @since 1.0.0
     * @return string Default template HTML.
     */
    private function get_default_template(): string {
        return '
        <div class="edugo-certificate">
            <div class="certificate-header">
                <h1>Certificate of Completion</h1>
            </div>
            <div class="certificate-body">
                <p class="certificate-intro">This is to certify that</p>
                <h2 class="student-name">{student_name}</h2>
                <p class="certificate-text">has successfully completed the course</p>
                <h3 class="course-title">{course_title}</h3>
                <p class="completion-date">on {completion_date}</p>
            </div>
            <div class="certificate-footer">
                <div class="instructor-signature">
                    <p class="instructor-name">{instructor_name}</p>
                    <p class="instructor-label">Instructor</p>
                </div>
                <div class="certificate-id">
                    <p>Certificate ID: {certificate_id}</p>
                </div>
            </div>
        </div>';
    }

    /**
     * Generate unique certificate key.
     *
     * @since 1.0.0
     * @return string Unique certificate key.
     */
    private function generate_certificate_key(): string {
        return strtoupper( wp_generate_password( 16, false, false ) );
    }

    /**
     * Get certificate download URL.
     *
     * @since 1.0.0
     * @param int $user_id   The user ID.
     * @param int $course_id The course ID.
     * @return string|false Download URL or false if no certificate.
     */
    public function get_download_url( int $user_id, int $course_id ) {
        $certificate = $this->get_certificate( $user_id, $course_id );

        if ( ! $certificate ) {
            return false;
        }

        return add_query_arg(
            array(
                'edugo_action'    => 'download_certificate',
                'certificate_key' => $certificate->certificate_key,
                'nonce'           => wp_create_nonce( 'edugo_download_certificate_' . $certificate->id ),
            ),
            home_url()
        );
    }

    /**
     * Get certificate verification URL.
     *
     * @since 1.0.0
     * @param string $key The certificate key.
     * @return string Verification URL.
     */
    public function get_verification_url( string $key ): string {
        return add_query_arg(
            array(
                'edugo_action'    => 'verify_certificate',
                'certificate_key' => $key,
            ),
            home_url()
        );
    }

    /**
     * Check if user has certificate for course.
     *
     * @since 1.0.0
     * @param int $user_id   The user ID.
     * @param int $course_id The course ID.
     * @return bool True if has certificate, false otherwise.
     */
    public function has_certificate( int $user_id, int $course_id ): bool {
        return $this->get_certificate( $user_id, $course_id ) !== null;
    }
}
