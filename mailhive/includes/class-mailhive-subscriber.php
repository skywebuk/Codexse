<?php
/**
 * MailHive Subscriber Class
 *
 * @package MailHive
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Subscriber class for database operations.
 */
class MailHive_Subscriber {

    /**
     * Table name.
     *
     * @var string
     */
    private $table_name;

    /**
     * Constructor.
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'mailhive_subscribers';
    }

    /**
     * Add a new subscriber.
     *
     * @param array $data Subscriber data.
     * @return array Result with success status and message.
     */
    public function add( $data ) {
        global $wpdb;

        $email = isset( $data['email'] ) ? sanitize_email( $data['email'] ) : '';

        if ( empty( $email ) || ! is_email( $email ) ) {
            return array(
                'success' => false,
                'message' => __( 'Please enter a valid email address.', 'mailhive' ),
            );
        }

        // Check for duplicate
        if ( $this->email_exists( $email ) ) {
            return array(
                'success' => false,
                'type'    => 'duplicate',
                'message' => get_option( 'mailhive_duplicate_message', __( 'This email is already subscribed.', 'mailhive' ) ),
            );
        }

        $name = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';

        // Handle custom fields
        $custom_fields = array();
        $reserved_fields = array( 'email', 'name', 'action', 'nonce', 'mailhive_form_id' );

        foreach ( $data as $key => $value ) {
            if ( ! in_array( $key, $reserved_fields, true ) ) {
                $custom_fields[ sanitize_key( $key ) ] = sanitize_text_field( $value );
            }
        }

        $insert_data = array(
            'email'         => $email,
            'name'          => $name,
            'custom_fields' => wp_json_encode( $custom_fields ),
            'status'        => 'subscribed',
            'ip_address'    => $this->get_client_ip(),
            'created_at'    => current_time( 'mysql' ),
            'updated_at'    => current_time( 'mysql' ),
        );

        $result = $wpdb->insert(
            $this->table_name,
            $insert_data,
            array( '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
        );

        if ( false === $result ) {
            return array(
                'success' => false,
                'message' => get_option( 'mailhive_error_message', __( 'An error occurred. Please try again.', 'mailhive' ) ),
            );
        }

        return array(
            'success' => true,
            'message' => get_option( 'mailhive_success_message', __( 'Thank you for subscribing!', 'mailhive' ) ),
            'id'      => $wpdb->insert_id,
        );
    }

    /**
     * Check if email exists.
     *
     * @param string $email Email address.
     * @return bool
     */
    public function email_exists( $email ) {
        global $wpdb;

        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table_name} WHERE email = %s",
                sanitize_email( $email )
            )
        );

        return $count > 0;
    }

    /**
     * Get all subscribers.
     *
     * @param array $args Query arguments.
     * @return array
     */
    public function get_all( $args = array() ) {
        global $wpdb;

        $defaults = array(
            'per_page' => 20,
            'page'     => 1,
            'orderby'  => 'created_at',
            'order'    => 'DESC',
            'status'   => '',
            'search'   => '',
        );

        $args = wp_parse_args( $args, $defaults );

        $offset = ( $args['page'] - 1 ) * $args['per_page'];
        $orderby = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] );

        if ( ! $orderby ) {
            $orderby = 'created_at DESC';
        }

        $where = '1=1';
        $values = array();

        if ( ! empty( $args['status'] ) ) {
            $where .= ' AND status = %s';
            $values[] = $args['status'];
        }

        if ( ! empty( $args['search'] ) ) {
            $where .= ' AND (email LIKE %s OR name LIKE %s)';
            $search_term = '%' . $wpdb->esc_like( $args['search'] ) . '%';
            $values[] = $search_term;
            $values[] = $search_term;
        }

        $query = "SELECT * FROM {$this->table_name} WHERE {$where} ORDER BY {$orderby} LIMIT %d OFFSET %d";
        $values[] = $args['per_page'];
        $values[] = $offset;

        if ( ! empty( $values ) ) {
            $query = $wpdb->prepare( $query, $values );
        }

        return $wpdb->get_results( $query, ARRAY_A );
    }

    /**
     * Get total subscriber count.
     *
     * @param array $args Query arguments.
     * @return int
     */
    public function get_total_count( $args = array() ) {
        global $wpdb;

        $where = '1=1';
        $values = array();

        if ( ! empty( $args['status'] ) ) {
            $where .= ' AND status = %s';
            $values[] = $args['status'];
        }

        if ( ! empty( $args['search'] ) ) {
            $where .= ' AND (email LIKE %s OR name LIKE %s)';
            $search_term = '%' . $wpdb->esc_like( $args['search'] ) . '%';
            $values[] = $search_term;
            $values[] = $search_term;
        }

        $query = "SELECT COUNT(*) FROM {$this->table_name} WHERE {$where}";

        if ( ! empty( $values ) ) {
            $query = $wpdb->prepare( $query, $values );
        }

        return (int) $wpdb->get_var( $query );
    }

    /**
     * Get subscriber by ID.
     *
     * @param int $id Subscriber ID.
     * @return array|null
     */
    public function get_by_id( $id ) {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE id = %d",
                absint( $id )
            ),
            ARRAY_A
        );
    }

    /**
     * Update subscriber.
     *
     * @param int   $id   Subscriber ID.
     * @param array $data Data to update.
     * @return bool
     */
    public function update( $id, $data ) {
        global $wpdb;

        $update_data = array();
        $format = array();

        if ( isset( $data['email'] ) ) {
            $update_data['email'] = sanitize_email( $data['email'] );
            $format[] = '%s';
        }

        if ( isset( $data['name'] ) ) {
            $update_data['name'] = sanitize_text_field( $data['name'] );
            $format[] = '%s';
        }

        if ( isset( $data['status'] ) ) {
            $update_data['status'] = sanitize_text_field( $data['status'] );
            $format[] = '%s';
        }

        if ( isset( $data['custom_fields'] ) ) {
            $update_data['custom_fields'] = wp_json_encode( $data['custom_fields'] );
            $format[] = '%s';
        }

        if ( empty( $update_data ) ) {
            return false;
        }

        $update_data['updated_at'] = current_time( 'mysql' );
        $format[] = '%s';

        return $wpdb->update(
            $this->table_name,
            $update_data,
            array( 'id' => absint( $id ) ),
            $format,
            array( '%d' )
        );
    }

    /**
     * Delete subscriber.
     *
     * @param int $id Subscriber ID.
     * @return bool
     */
    public function delete( $id ) {
        global $wpdb;

        return $wpdb->delete(
            $this->table_name,
            array( 'id' => absint( $id ) ),
            array( '%d' )
        );
    }

    /**
     * Bulk delete subscribers.
     *
     * @param array $ids Subscriber IDs.
     * @return int Number of deleted rows.
     */
    public function bulk_delete( $ids ) {
        global $wpdb;

        $ids = array_map( 'absint', (array) $ids );
        $ids_string = implode( ',', $ids );

        return $wpdb->query(
            "DELETE FROM {$this->table_name} WHERE id IN ({$ids_string})"
        );
    }

    /**
     * Export subscribers to CSV.
     *
     * @param array $args Query arguments.
     * @return array
     */
    public function export_csv( $args = array() ) {
        global $wpdb;

        $where = '1=1';
        $values = array();

        if ( ! empty( $args['status'] ) ) {
            $where .= ' AND status = %s';
            $values[] = $args['status'];
        }

        $query = "SELECT * FROM {$this->table_name} WHERE {$where} ORDER BY created_at DESC";

        if ( ! empty( $values ) ) {
            $query = $wpdb->prepare( $query, $values );
        }

        return $wpdb->get_results( $query, ARRAY_A );
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

        // Handle multiple IPs (in case of proxies)
        if ( strpos( $ip, ',' ) !== false ) {
            $ip = trim( current( explode( ',', $ip ) ) );
        }

        return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '';
    }
}
