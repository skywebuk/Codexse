<?php
/**
 * Plugin Deactivator Class.
 *
 * @package Edugo_LMS\Core
 */

namespace Edugo_LMS\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Deactivator
 *
 * Handles plugin deactivation tasks.
 *
 * @since 1.0.0
 */
class Deactivator {

    /**
     * Deactivate the plugin.
     *
     * @since 1.0.0
     * @return void
     */
    public static function deactivate(): void {
        self::clear_scheduled_events();
        self::clear_transients();

        // Flush rewrite rules.
        flush_rewrite_rules();

        // Update deactivation flag.
        update_option( 'edugo_lms_activated', false );
    }

    /**
     * Clear all scheduled cron events.
     *
     * @since 1.0.0
     * @return void
     */
    private static function clear_scheduled_events(): void {
        $events = array(
            'edugo_daily_cleanup',
            'edugo_weekly_reports',
            'edugo_check_drip_content',
        );

        foreach ( $events as $event ) {
            $timestamp = wp_next_scheduled( $event );
            if ( $timestamp ) {
                wp_unschedule_event( $timestamp, $event );
            }
        }
    }

    /**
     * Clear plugin transients.
     *
     * @since 1.0.0
     * @return void
     */
    private static function clear_transients(): void {
        global $wpdb;

        // Delete all plugin transients.
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                '_transient_edugo_%',
                '_transient_timeout_edugo_%'
            )
        );
    }
}
