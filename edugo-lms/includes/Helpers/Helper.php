<?php
/**
 * Helper Class.
 *
 * @package Edugo_LMS\Helpers
 */

namespace Edugo_LMS\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Helper
 *
 * Provides utility functions for the plugin.
 *
 * @since 1.0.0
 */
class Helper {

    /**
     * Format duration string.
     *
     * @since 1.0.0
     * @param int $minutes Duration in minutes.
     * @return string Formatted duration.
     */
    public static function format_duration( int $minutes ): string {
        if ( $minutes < 60 ) {
            /* translators: %d: Number of minutes */
            return sprintf( _n( '%d minute', '%d minutes', $minutes, 'edugo-lms' ), $minutes );
        }

        $hours = floor( $minutes / 60 );
        $remaining_minutes = $minutes % 60;

        if ( $remaining_minutes === 0 ) {
            /* translators: %d: Number of hours */
            return sprintf( _n( '%d hour', '%d hours', $hours, 'edugo-lms' ), $hours );
        }

        /* translators: 1: Number of hours, 2: Number of minutes */
        return sprintf(
            __( '%1$dh %2$dm', 'edugo-lms' ),
            $hours,
            $remaining_minutes
        );
    }

    /**
     * Format price.
     *
     * @since 1.0.0
     * @param float $amount Amount to format.
     * @return string Formatted price.
     */
    public static function format_price( float $amount ): string {
        if ( function_exists( 'wc_price' ) ) {
            return wc_price( $amount );
        }

        $currency = get_option( 'edugo_currency', 'USD' );
        return $currency . ' ' . number_format( $amount, 2 );
    }

    /**
     * Get course lessons.
     *
     * @since 1.0.0
     * @param int $course_id The course ID.
     * @return array Array of lesson posts.
     */
    public static function get_course_lessons( int $course_id ): array {
        return get_posts( array(
            'post_type'      => 'edugo_lesson',
            'posts_per_page' => -1,
            'meta_key'       => '_edugo_course_id',
            'meta_value'     => $course_id,
            'meta_query'     => array(
                array(
                    'key'   => '_edugo_lesson_order',
                    'type'  => 'NUMERIC',
                ),
            ),
            'orderby'        => 'meta_value_num',
            'order'          => 'ASC',
            'post_status'    => 'publish',
        ) );
    }

    /**
     * Get course quizzes.
     *
     * @since 1.0.0
     * @param int $course_id The course ID.
     * @return array Array of quiz posts.
     */
    public static function get_course_quizzes( int $course_id ): array {
        return get_posts( array(
            'post_type'      => 'edugo_quiz',
            'posts_per_page' => -1,
            'meta_key'       => '_edugo_course_id',
            'meta_value'     => $course_id,
            'post_status'    => 'publish',
        ) );
    }

    /**
     * Get course assignments.
     *
     * @since 1.0.0
     * @param int $course_id The course ID.
     * @return array Array of assignment posts.
     */
    public static function get_course_assignments( int $course_id ): array {
        return get_posts( array(
            'post_type'      => 'edugo_assignment',
            'posts_per_page' => -1,
            'meta_key'       => '_edugo_course_id',
            'meta_value'     => $course_id,
            'post_status'    => 'publish',
        ) );
    }

    /**
     * Get course total duration.
     *
     * @since 1.0.0
     * @param int $course_id The course ID.
     * @return int Total duration in minutes.
     */
    public static function get_course_duration( int $course_id ): int {
        $lessons = self::get_course_lessons( $course_id );
        $total = 0;

        foreach ( $lessons as $lesson ) {
            $duration = get_post_meta( $lesson->ID, '_edugo_duration', true );
            $total += (int) $duration;
        }

        return $total;
    }

    /**
     * Get course instructor.
     *
     * @since 1.0.0
     * @param int $course_id The course ID.
     * @return \WP_User|false User object or false.
     */
    public static function get_course_instructor( int $course_id ) {
        $course = get_post( $course_id );

        if ( ! $course ) {
            return false;
        }

        return get_userdata( $course->post_author );
    }

    /**
     * Check if content is drip locked.
     *
     * @since 1.0.0
     * @param int $content_id The content ID (lesson/quiz).
     * @param int $user_id    The user ID.
     * @return bool True if locked, false otherwise.
     */
    public static function is_drip_locked( int $content_id, int $user_id ): bool {
        if ( get_option( 'edugo_drip_content_enabled' ) !== 'yes' ) {
            return false;
        }

        $course_id = get_post_meta( $content_id, '_edugo_course_id', true );

        if ( ! $course_id ) {
            return false;
        }

        // Get enrollment date.
        $enrollment_manager = new \Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
        $enrollment = $enrollment_manager->get_enrollment_id( $user_id, (int) $course_id );

        if ( ! $enrollment ) {
            return true; // Not enrolled.
        }

        // Check drip settings.
        $drip_days = get_post_meta( $content_id, '_edugo_drip_days', true );
        $drip_date = get_post_meta( $content_id, '_edugo_drip_date', true );

        // Days after enrollment.
        if ( $drip_days ) {
            global $wpdb;
            $enrolled_at = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT enrolled_at FROM {$wpdb->prefix}edugo_enrollments WHERE id = %d",
                    $enrollment
                )
            );

            if ( $enrolled_at ) {
                $unlock_date = strtotime( $enrolled_at ) + ( (int) $drip_days * DAY_IN_SECONDS );
                if ( time() < $unlock_date ) {
                    return true;
                }
            }
        }

        // Specific date.
        if ( $drip_date ) {
            if ( time() < strtotime( $drip_date ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get drip unlock date.
     *
     * @since 1.0.0
     * @param int $content_id The content ID.
     * @param int $user_id    The user ID.
     * @return string|null Formatted date or null.
     */
    public static function get_drip_unlock_date( int $content_id, int $user_id ): ?string {
        $drip_date = get_post_meta( $content_id, '_edugo_drip_date', true );

        if ( $drip_date ) {
            return date_i18n( get_option( 'date_format' ), strtotime( $drip_date ) );
        }

        $drip_days = get_post_meta( $content_id, '_edugo_drip_days', true );

        if ( $drip_days ) {
            $course_id = get_post_meta( $content_id, '_edugo_course_id', true );
            $enrollment_manager = new \Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
            $enrollment_id = $enrollment_manager->get_enrollment_id( $user_id, (int) $course_id );

            if ( $enrollment_id ) {
                global $wpdb;
                $enrolled_at = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT enrolled_at FROM {$wpdb->prefix}edugo_enrollments WHERE id = %d",
                        $enrollment_id
                    )
                );

                if ( $enrolled_at ) {
                    $unlock_timestamp = strtotime( $enrolled_at ) + ( (int) $drip_days * DAY_IN_SECONDS );
                    return date_i18n( get_option( 'date_format' ), $unlock_timestamp );
                }
            }
        }

        return null;
    }

    /**
     * Sanitize array recursively.
     *
     * @since 1.0.0
     * @param array $array Array to sanitize.
     * @return array Sanitized array.
     */
    public static function sanitize_array( array $array ): array {
        $sanitized = array();

        foreach ( $array as $key => $value ) {
            $key = sanitize_key( $key );

            if ( is_array( $value ) ) {
                $sanitized[ $key ] = self::sanitize_array( $value );
            } else {
                $sanitized[ $key ] = sanitize_text_field( $value );
            }
        }

        return $sanitized;
    }

    /**
     * Get template part.
     *
     * @since 1.0.0
     * @param string $slug Template slug.
     * @param string $name Optional template name.
     * @param array  $args Optional arguments to pass to template.
     * @return void
     */
    public static function get_template( string $slug, string $name = '', array $args = array() ): void {
        $template = '';

        // Look in theme first.
        if ( $name ) {
            $template = locate_template( array( "edugo-lms/{$slug}-{$name}.php", "edugo-lms/{$slug}.php" ) );
        } else {
            $template = locate_template( array( "edugo-lms/{$slug}.php" ) );
        }

        // Fall back to plugin templates.
        if ( ! $template ) {
            if ( $name ) {
                $template = EDUGO_LMS_PATH . "templates/{$slug}-{$name}.php";
            }

            if ( ! file_exists( $template ) ) {
                $template = EDUGO_LMS_PATH . "templates/{$slug}.php";
            }
        }

        if ( file_exists( $template ) ) {
            // Extract args for use in template.
            if ( ! empty( $args ) ) {
                extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
            }

            include $template;
        }
    }

    /**
     * Generate random string.
     *
     * @since 1.0.0
     * @param int $length String length.
     * @return string Random string.
     */
    public static function generate_random_string( int $length = 16 ): string {
        return wp_generate_password( $length, false, false );
    }

    /**
     * Check if current user can access course content.
     *
     * @since 1.0.0
     * @param int $course_id The course ID.
     * @return bool True if can access, false otherwise.
     */
    public static function can_access_course( int $course_id ): bool {
        // Admin can access everything.
        if ( current_user_can( 'manage_options' ) ) {
            return true;
        }

        // Course author can access.
        $course = get_post( $course_id );
        if ( $course && (int) $course->post_author === get_current_user_id() ) {
            return true;
        }

        // Check enrollment.
        $enrollment_manager = new \Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
        return $enrollment_manager->is_enrolled( get_current_user_id(), $course_id );
    }

    /**
     * Log debug message.
     *
     * @since 1.0.0
     * @param mixed  $message Message to log.
     * @param string $type    Log type.
     * @return void
     */
    public static function log( $message, string $type = 'debug' ): void {
        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            return;
        }

        if ( is_array( $message ) || is_object( $message ) ) {
            $message = print_r( $message, true );
        }

        error_log( sprintf( '[Edugo LMS] [%s] %s', strtoupper( $type ), $message ) );
    }
}
