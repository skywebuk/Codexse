<?php
/**
 * Withdrawal Management Class.
 *
 * @package Bazaar\Withdrawal
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Withdrawal Class.
 */
class Bazaar_Withdrawal {

    /**
     * Constructor.
     */
    public function __construct() {
        // Schedule automatic payouts if enabled
        add_action( 'bazaar_process_automatic_payouts', array( $this, 'process_automatic_payouts' ) );
    }

    /**
     * Get available withdrawal methods.
     *
     * @return array
     */
    public static function get_withdrawal_methods() {
        $methods = array(
            'paypal'        => array(
                'id'          => 'paypal',
                'title'       => __( 'PayPal', 'bazaar' ),
                'description' => __( 'Receive payments via PayPal', 'bazaar' ),
                'fields'      => array(
                    'email' => array(
                        'label'    => __( 'PayPal Email', 'bazaar' ),
                        'type'     => 'email',
                        'required' => true,
                    ),
                ),
            ),
            'bank_transfer' => array(
                'id'          => 'bank_transfer',
                'title'       => __( 'Bank Transfer', 'bazaar' ),
                'description' => __( 'Receive payments via bank transfer', 'bazaar' ),
                'fields'      => array(
                    'account_name'   => array(
                        'label'    => __( 'Account Holder Name', 'bazaar' ),
                        'type'     => 'text',
                        'required' => true,
                    ),
                    'account_number' => array(
                        'label'    => __( 'Account Number', 'bazaar' ),
                        'type'     => 'text',
                        'required' => true,
                    ),
                    'bank_name'      => array(
                        'label'    => __( 'Bank Name', 'bazaar' ),
                        'type'     => 'text',
                        'required' => true,
                    ),
                    'routing_number' => array(
                        'label'    => __( 'Routing Number / SWIFT', 'bazaar' ),
                        'type'     => 'text',
                        'required' => false,
                    ),
                    'iban'           => array(
                        'label'    => __( 'IBAN', 'bazaar' ),
                        'type'     => 'text',
                        'required' => false,
                    ),
                ),
            ),
            'stripe'        => array(
                'id'          => 'stripe',
                'title'       => __( 'Stripe', 'bazaar' ),
                'description' => __( 'Receive payments via Stripe', 'bazaar' ),
                'fields'      => array(
                    'email' => array(
                        'label'    => __( 'Stripe Email', 'bazaar' ),
                        'type'     => 'email',
                        'required' => true,
                    ),
                ),
            ),
        );

        return apply_filters( 'bazaar_withdrawal_methods', $methods );
    }

    /**
     * Get enabled withdrawal methods.
     *
     * @return array
     */
    public static function get_enabled_methods() {
        $enabled = get_option( 'bazaar_withdrawal_methods', array( 'paypal', 'bank_transfer' ) );
        $all_methods = self::get_withdrawal_methods();

        $result = array();

        foreach ( $enabled as $method_id ) {
            if ( isset( $all_methods[ $method_id ] ) ) {
                $result[ $method_id ] = $all_methods[ $method_id ];
            }
        }

        return $result;
    }

    /**
     * Get vendor withdrawal method details.
     *
     * @param int    $vendor_id Vendor ID.
     * @param string $method    Method ID.
     * @return array
     */
    public static function get_vendor_method_details( $vendor_id, $method ) {
        return get_user_meta( $vendor_id, '_bazaar_withdrawal_' . $method, true );
    }

    /**
     * Save vendor withdrawal method details.
     *
     * @param int    $vendor_id Vendor ID.
     * @param string $method    Method ID.
     * @param array  $details   Method details.
     * @return bool
     */
    public static function save_vendor_method_details( $vendor_id, $method, $details ) {
        $sanitized = array();

        foreach ( $details as $key => $value ) {
            $sanitized[ sanitize_key( $key ) ] = sanitize_text_field( $value );
        }

        update_user_meta( $vendor_id, '_bazaar_withdrawal_' . $method, $sanitized );

        return true;
    }

    /**
     * Request withdrawal.
     *
     * @param int    $vendor_id Vendor ID.
     * @param float  $amount    Amount.
     * @param string $method    Withdrawal method.
     * @param string $note      Note.
     * @return int|WP_Error
     */
    public static function request_withdrawal( $vendor_id, $amount, $method, $note = '' ) {
        global $wpdb;

        // Validate vendor
        if ( ! Bazaar_Roles::is_active_vendor( $vendor_id ) ) {
            return new WP_Error( 'invalid_vendor', __( 'Invalid or inactive vendor.', 'bazaar' ) );
        }

        // Validate amount
        $min_amount = floatval( get_option( 'bazaar_min_withdrawal_amount', 50 ) );
        $balance = Bazaar_Vendor::get_balance( $vendor_id );

        if ( $amount < $min_amount ) {
            return new WP_Error( 'min_amount', sprintf( __( 'Minimum withdrawal amount is %s.', 'bazaar' ), wc_price( $min_amount ) ) );
        }

        if ( $amount > $balance ) {
            return new WP_Error( 'insufficient_balance', __( 'Insufficient balance.', 'bazaar' ) );
        }

        // Validate method
        $enabled_methods = self::get_enabled_methods();

        if ( ! isset( $enabled_methods[ $method ] ) ) {
            return new WP_Error( 'invalid_method', __( 'Invalid withdrawal method.', 'bazaar' ) );
        }

        // Get method details
        $method_details = self::get_vendor_method_details( $vendor_id, $method );

        if ( empty( $method_details ) ) {
            return new WP_Error( 'no_method_details', __( 'Please configure your withdrawal method details first.', 'bazaar' ) );
        }

        // Check for pending withdrawals
        $pending = self::has_pending_withdrawal( $vendor_id );

        if ( $pending ) {
            return new WP_Error( 'pending_exists', __( 'You already have a pending withdrawal request.', 'bazaar' ) );
        }

        // Create withdrawal request
        $table_name = $wpdb->prefix . 'bazaar_withdrawals';

        $wpdb->insert(
            $table_name,
            array(
                'vendor_id'      => $vendor_id,
                'amount'         => $amount,
                'method'         => $method,
                'method_details' => maybe_serialize( $method_details ),
                'status'         => 'pending',
                'note'           => sanitize_textarea_field( $note ),
                'ip_address'     => self::get_client_ip(),
            ),
            array( '%d', '%f', '%s', '%s', '%s', '%s', '%s' )
        );

        $withdrawal_id = $wpdb->insert_id;

        do_action( 'bazaar_withdrawal_requested', $withdrawal_id, $vendor_id, $amount, $method );

        // Send notification
        Bazaar_Notifications::send_withdrawal_request_notification( $withdrawal_id );

        return $withdrawal_id;
    }

    /**
     * Check if vendor has pending withdrawal.
     *
     * @param int $vendor_id Vendor ID.
     * @return bool
     */
    public static function has_pending_withdrawal( $vendor_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_withdrawals';

        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE vendor_id = %d AND status = 'pending'",
                $vendor_id
            )
        );

        return $count > 0;
    }

    /**
     * Get withdrawal.
     *
     * @param int $withdrawal_id Withdrawal ID.
     * @return object|null
     */
    public static function get_withdrawal( $withdrawal_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_withdrawals';

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE id = %d",
                $withdrawal_id
            )
        );
    }

    /**
     * Get vendor withdrawals.
     *
     * @param int   $vendor_id Vendor ID.
     * @param array $args      Query arguments.
     * @return array
     */
    public static function get_vendor_withdrawals( $vendor_id, $args = array() ) {
        global $wpdb;

        $defaults = array(
            'per_page' => 20,
            'page'     => 1,
            'status'   => '',
        );

        $args = wp_parse_args( $args, $defaults );
        $table_name = $wpdb->prefix . 'bazaar_withdrawals';

        $where = $wpdb->prepare( "WHERE vendor_id = %d", $vendor_id );

        if ( ! empty( $args['status'] ) ) {
            $where .= $wpdb->prepare( " AND status = %s", $args['status'] );
        }

        $offset = ( $args['page'] - 1 ) * $args['per_page'];

        $withdrawals = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} {$where} ORDER BY created_at DESC LIMIT %d OFFSET %d",
                $args['per_page'],
                $offset
            )
        );

        $total = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$table_name} {$where}"
        );

        return array(
            'withdrawals' => $withdrawals,
            'total'       => intval( $total ),
            'pages'       => ceil( $total / $args['per_page'] ),
        );
    }

    /**
     * Get all withdrawals (admin).
     *
     * @param array $args Query arguments.
     * @return array
     */
    public static function get_all_withdrawals( $args = array() ) {
        global $wpdb;

        $defaults = array(
            'per_page'  => 20,
            'page'      => 1,
            'status'    => '',
            'vendor_id' => '',
            'method'    => '',
        );

        $args = wp_parse_args( $args, $defaults );
        $table_name = $wpdb->prefix . 'bazaar_withdrawals';

        $where = "WHERE 1=1";

        if ( ! empty( $args['status'] ) ) {
            $where .= $wpdb->prepare( " AND status = %s", $args['status'] );
        }

        if ( ! empty( $args['vendor_id'] ) ) {
            $where .= $wpdb->prepare( " AND vendor_id = %d", $args['vendor_id'] );
        }

        if ( ! empty( $args['method'] ) ) {
            $where .= $wpdb->prepare( " AND method = %s", $args['method'] );
        }

        $offset = ( $args['page'] - 1 ) * $args['per_page'];

        $withdrawals = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} {$where} ORDER BY created_at DESC LIMIT %d OFFSET %d",
                $args['per_page'],
                $offset
            )
        );

        $total = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$table_name} {$where}"
        );

        return array(
            'withdrawals' => $withdrawals,
            'total'       => intval( $total ),
            'pages'       => ceil( $total / $args['per_page'] ),
        );
    }

    /**
     * Approve withdrawal.
     *
     * @param int    $withdrawal_id Withdrawal ID.
     * @param string $admin_note    Admin note.
     * @return bool|WP_Error
     */
    public static function approve_withdrawal( $withdrawal_id, $admin_note = '' ) {
        global $wpdb;

        $withdrawal = self::get_withdrawal( $withdrawal_id );

        if ( ! $withdrawal ) {
            return new WP_Error( 'invalid_withdrawal', __( 'Invalid withdrawal request.', 'bazaar' ) );
        }

        if ( 'pending' !== $withdrawal->status ) {
            return new WP_Error( 'not_pending', __( 'Withdrawal is not pending.', 'bazaar' ) );
        }

        $table_name = $wpdb->prefix . 'bazaar_withdrawals';

        $wpdb->update(
            $table_name,
            array(
                'status'     => 'approved',
                'admin_note' => sanitize_textarea_field( $admin_note ),
            ),
            array( 'id' => $withdrawal_id ),
            array( '%s', '%s' ),
            array( '%d' )
        );

        // Create balance deduction record
        $balance_table = $wpdb->prefix . 'bazaar_vendor_balance';

        $wpdb->insert(
            $balance_table,
            array(
                'vendor_id'   => $withdrawal->vendor_id,
                'trn_type'    => 'debit',
                'trn_status'  => 'completed',
                'amount'      => $withdrawal->amount,
                'commission'  => 0,
                'net_amount'  => $withdrawal->amount,
                'note'        => sprintf( __( 'Withdrawal #%d', 'bazaar' ), $withdrawal_id ),
            ),
            array( '%d', '%s', '%s', '%f', '%f', '%f', '%s' )
        );

        do_action( 'bazaar_withdrawal_approved', $withdrawal_id, $withdrawal );

        // Send notification
        Bazaar_Notifications::send_withdrawal_status_notification( $withdrawal_id, 'approved' );

        return true;
    }

    /**
     * Reject withdrawal.
     *
     * @param int    $withdrawal_id Withdrawal ID.
     * @param string $admin_note    Admin note.
     * @return bool|WP_Error
     */
    public static function reject_withdrawal( $withdrawal_id, $admin_note = '' ) {
        global $wpdb;

        $withdrawal = self::get_withdrawal( $withdrawal_id );

        if ( ! $withdrawal ) {
            return new WP_Error( 'invalid_withdrawal', __( 'Invalid withdrawal request.', 'bazaar' ) );
        }

        if ( 'pending' !== $withdrawal->status ) {
            return new WP_Error( 'not_pending', __( 'Withdrawal is not pending.', 'bazaar' ) );
        }

        $table_name = $wpdb->prefix . 'bazaar_withdrawals';

        $wpdb->update(
            $table_name,
            array(
                'status'     => 'rejected',
                'admin_note' => sanitize_textarea_field( $admin_note ),
            ),
            array( 'id' => $withdrawal_id ),
            array( '%s', '%s' ),
            array( '%d' )
        );

        do_action( 'bazaar_withdrawal_rejected', $withdrawal_id, $withdrawal );

        // Send notification
        Bazaar_Notifications::send_withdrawal_status_notification( $withdrawal_id, 'rejected' );

        return true;
    }

    /**
     * Mark withdrawal as paid.
     *
     * @param int    $withdrawal_id Withdrawal ID.
     * @param string $admin_note    Admin note.
     * @return bool|WP_Error
     */
    public static function mark_as_paid( $withdrawal_id, $admin_note = '' ) {
        global $wpdb;

        $withdrawal = self::get_withdrawal( $withdrawal_id );

        if ( ! $withdrawal ) {
            return new WP_Error( 'invalid_withdrawal', __( 'Invalid withdrawal request.', 'bazaar' ) );
        }

        if ( ! in_array( $withdrawal->status, array( 'pending', 'approved' ), true ) ) {
            return new WP_Error( 'invalid_status', __( 'Cannot mark this withdrawal as paid.', 'bazaar' ) );
        }

        $table_name = $wpdb->prefix . 'bazaar_withdrawals';

        // If was pending, also create balance deduction
        if ( 'pending' === $withdrawal->status ) {
            $balance_table = $wpdb->prefix . 'bazaar_vendor_balance';

            $wpdb->insert(
                $balance_table,
                array(
                    'vendor_id'   => $withdrawal->vendor_id,
                    'trn_type'    => 'debit',
                    'trn_status'  => 'completed',
                    'amount'      => $withdrawal->amount,
                    'commission'  => 0,
                    'net_amount'  => $withdrawal->amount,
                    'note'        => sprintf( __( 'Withdrawal #%d', 'bazaar' ), $withdrawal_id ),
                ),
                array( '%d', '%s', '%s', '%f', '%f', '%f', '%s' )
            );
        }

        $wpdb->update(
            $table_name,
            array(
                'status'     => 'paid',
                'admin_note' => sanitize_textarea_field( $admin_note ),
            ),
            array( 'id' => $withdrawal_id ),
            array( '%s', '%s' ),
            array( '%d' )
        );

        do_action( 'bazaar_withdrawal_paid', $withdrawal_id, $withdrawal );

        // Send notification
        Bazaar_Notifications::send_withdrawal_status_notification( $withdrawal_id, 'paid' );

        return true;
    }

    /**
     * Cancel withdrawal.
     *
     * @param int $withdrawal_id Withdrawal ID.
     * @param int $vendor_id     Vendor ID.
     * @return bool|WP_Error
     */
    public static function cancel_withdrawal( $withdrawal_id, $vendor_id ) {
        global $wpdb;

        $withdrawal = self::get_withdrawal( $withdrawal_id );

        if ( ! $withdrawal ) {
            return new WP_Error( 'invalid_withdrawal', __( 'Invalid withdrawal request.', 'bazaar' ) );
        }

        if ( $withdrawal->vendor_id !== $vendor_id && ! current_user_can( 'manage_options' ) ) {
            return new WP_Error( 'permission_denied', __( 'You cannot cancel this withdrawal.', 'bazaar' ) );
        }

        if ( 'pending' !== $withdrawal->status ) {
            return new WP_Error( 'not_pending', __( 'Only pending withdrawals can be cancelled.', 'bazaar' ) );
        }

        $table_name = $wpdb->prefix . 'bazaar_withdrawals';

        $wpdb->update(
            $table_name,
            array( 'status' => 'cancelled' ),
            array( 'id' => $withdrawal_id ),
            array( '%s' ),
            array( '%d' )
        );

        do_action( 'bazaar_withdrawal_cancelled', $withdrawal_id, $withdrawal );

        return true;
    }

    /**
     * Get withdrawal statistics.
     *
     * @return array
     */
    public static function get_statistics() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_withdrawals';

        $stats = array(
            'pending_count'   => 0,
            'pending_amount'  => 0,
            'approved_count'  => 0,
            'approved_amount' => 0,
            'paid_count'      => 0,
            'paid_amount'     => 0,
            'rejected_count'  => 0,
        );

        $results = $wpdb->get_results(
            "SELECT status, COUNT(*) as count, SUM(amount) as total FROM {$table_name} GROUP BY status"
        );

        foreach ( $results as $row ) {
            $stats[ $row->status . '_count' ] = intval( $row->count );
            if ( isset( $stats[ $row->status . '_amount' ] ) ) {
                $stats[ $row->status . '_amount' ] = floatval( $row->total );
            }
        }

        return $stats;
    }

    /**
     * Process automatic payouts.
     */
    public function process_automatic_payouts() {
        $auto_payout = get_option( 'bazaar_auto_payout', 'no' );

        if ( 'yes' !== $auto_payout ) {
            return;
        }

        // Get all pending withdrawals
        $withdrawals = self::get_all_withdrawals(
            array(
                'status'   => 'pending',
                'per_page' => 100,
            )
        );

        foreach ( $withdrawals['withdrawals'] as $withdrawal ) {
            // Process based on method
            $result = $this->process_payout( $withdrawal );

            if ( $result ) {
                self::mark_as_paid( $withdrawal->id, __( 'Automatic payout', 'bazaar' ) );
            }
        }
    }

    /**
     * Process individual payout.
     *
     * @param object $withdrawal Withdrawal object.
     * @return bool
     */
    private function process_payout( $withdrawal ) {
        // This would integrate with payment gateways
        // For now, just return false to require manual processing
        return apply_filters( 'bazaar_process_payout', false, $withdrawal );
    }

    /**
     * Get client IP address.
     *
     * @return string
     */
    private static function get_client_ip() {
        $ip = '';

        if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
        } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
        } elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
        }

        return $ip;
    }

    /**
     * Get status label.
     *
     * @param string $status Status.
     * @return string
     */
    public static function get_status_label( $status ) {
        $labels = array(
            'pending'   => __( 'Pending', 'bazaar' ),
            'approved'  => __( 'Approved', 'bazaar' ),
            'paid'      => __( 'Paid', 'bazaar' ),
            'rejected'  => __( 'Rejected', 'bazaar' ),
            'cancelled' => __( 'Cancelled', 'bazaar' ),
        );

        return isset( $labels[ $status ] ) ? $labels[ $status ] : $status;
    }
}
