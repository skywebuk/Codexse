<?php
/**
 * MailHive Uninstall
 *
 * Fired when the plugin is uninstalled.
 *
 * @package MailHive
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Check user permissions
if ( ! current_user_can( 'activate_plugins' ) ) {
    exit;
}

global $wpdb;

// Delete plugin options
$options = array(
    'mailhive_version',
    'mailhive_success_message',
    'mailhive_error_message',
    'mailhive_duplicate_message',
    'mailhive_form_markup',
    'mailhive_form_css',
);

foreach ( $options as $option ) {
    delete_option( $option );
}

// Drop the subscribers table
$table_name = $wpdb->prefix . 'mailhive_subscribers';
$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );

// Delete any transients
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_mailhive_%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_mailhive_%'" );

// Clear any cached data
wp_cache_flush();
