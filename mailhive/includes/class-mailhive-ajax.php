<?php
/**
 * MailHive AJAX Handler Class
 *
 * @package MailHive
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * AJAX handler class.
 */
class MailHive_Ajax {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'wp_ajax_mailhive_subscribe', array( $this, 'handle_subscribe' ) );
        add_action( 'wp_ajax_nopriv_mailhive_subscribe', array( $this, 'handle_subscribe' ) );
    }

    /**
     * Handle subscription AJAX request.
     */
    public function handle_subscribe() {
        // Verify nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'mailhive_subscribe_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Security check failed. Please refresh the page and try again.', 'mailhive' ),
            ) );
        }

        // Honeypot check (if field exists and is filled, it's likely a bot)
        if ( ! empty( $_POST['mailhive_hp'] ) ) {
            wp_send_json_error( array(
                'message' => __( 'Invalid submission.', 'mailhive' ),
            ) );
        }

        // Rate limiting check
        $ip = $this->get_client_ip();
        $rate_limit_key = 'mailhive_rate_' . md5( $ip );
        $rate_limit = get_transient( $rate_limit_key );

        if ( $rate_limit && $rate_limit >= 5 ) {
            wp_send_json_error( array(
                'message' => __( 'Too many requests. Please try again later.', 'mailhive' ),
            ) );
        }

        // Sanitize all POST data
        $data = array();
        foreach ( $_POST as $key => $value ) {
            if ( 'nonce' === $key || 'action' === $key ) {
                continue;
            }

            $key = sanitize_key( $key );

            if ( is_array( $value ) ) {
                $data[ $key ] = array_map( 'sanitize_text_field', $value );
            } else {
                $data[ $key ] = sanitize_text_field( wp_unslash( $value ) );
            }
        }

        // Validate email
        if ( empty( $data['email'] ) ) {
            wp_send_json_error( array(
                'message' => __( 'Email address is required.', 'mailhive' ),
            ) );
        }

        if ( ! is_email( $data['email'] ) ) {
            wp_send_json_error( array(
                'message' => __( 'Please enter a valid email address.', 'mailhive' ),
            ) );
        }

        // Add subscriber
        $subscriber = new MailHive_Subscriber();
        $result = $subscriber->add( $data );

        // Update rate limit
        $current_count = $rate_limit ? $rate_limit : 0;
        set_transient( $rate_limit_key, $current_count + 1, HOUR_IN_SECONDS );

        if ( $result['success'] ) {
            /**
             * Fires after a subscriber is successfully added.
             *
             * @param array $data   The subscriber data.
             * @param int   $id     The subscriber ID.
             */
            do_action( 'mailhive_subscriber_added', $data, $result['id'] );

            wp_send_json_success( array(
                'message' => $result['message'],
            ) );
        } else {
            $error_type = isset( $result['type'] ) ? $result['type'] : 'error';

            /**
             * Fires when a subscription attempt fails.
             *
             * @param array  $data  The attempted subscriber data.
             * @param string $type  The error type (error or duplicate).
             */
            do_action( 'mailhive_subscription_failed', $data, $error_type );

            wp_send_json_error( array(
                'message' => $result['message'],
                'type'    => $error_type,
            ) );
        }
    }

    /**
     * Get client IP address.
     *
     * @return string
     */
    private function get_client_ip() {
        $ip = '';

        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
        } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
        }

        if ( strpos( $ip, ',' ) !== false ) {
            $ip = trim( current( explode( ',', $ip ) ) );
        }

        return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '0.0.0.0';
    }
}
