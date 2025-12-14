<?php
/**
 * Admin Withdrawals Management Class.
 *
 * @package Bazaar\Admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Admin_Withdrawals Class.
 */
class Bazaar_Admin_Withdrawals {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'admin_init', array( $this, 'handle_withdrawal_actions' ) );
    }

    /**
     * Handle withdrawal actions.
     */
    public function handle_withdrawal_actions() {
        if ( ! isset( $_GET['page'] ) || 'bazaar-withdrawals' !== $_GET['page'] ) {
            return;
        }

        if ( ! isset( $_GET['action'] ) || ! isset( $_GET['withdrawal'] ) ) {
            return;
        }

        $action = sanitize_text_field( wp_unslash( $_GET['action'] ) );
        $withdrawal_id = intval( $_GET['withdrawal'] );

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ?? '' ) ), 'bazaar_withdrawal_action' ) ) {
            return;
        }

        if ( ! current_user_can( 'bazaar_manage_withdrawals' ) ) {
            return;
        }

        $note = isset( $_GET['note'] ) ? sanitize_textarea_field( wp_unslash( $_GET['note'] ) ) : '';

        switch ( $action ) {
            case 'approve':
                Bazaar_Withdrawal::approve_withdrawal( $withdrawal_id, $note );
                $message = 'approved';
                break;

            case 'reject':
                Bazaar_Withdrawal::reject_withdrawal( $withdrawal_id, $note );
                $message = 'rejected';
                break;

            case 'paid':
                Bazaar_Withdrawal::mark_as_paid( $withdrawal_id, $note );
                $message = 'paid';
                break;

            default:
                return;
        }

        wp_safe_redirect(
            add_query_arg(
                array(
                    'page'    => 'bazaar-withdrawals',
                    'message' => $message,
                ),
                admin_url( 'admin.php' )
            )
        );
        exit;
    }

    /**
     * Get action links for withdrawal.
     *
     * @param object $withdrawal Withdrawal object.
     * @return array
     */
    public static function get_action_links( $withdrawal ) {
        $links = array();

        if ( 'pending' === $withdrawal->status ) {
            $links['approve'] = array(
                'url'   => wp_nonce_url(
                    admin_url( 'admin.php?page=bazaar-withdrawals&action=approve&withdrawal=' . $withdrawal->id ),
                    'bazaar_withdrawal_action'
                ),
                'label' => __( 'Approve', 'bazaar' ),
                'class' => 'approve',
            );

            $links['reject'] = array(
                'url'   => wp_nonce_url(
                    admin_url( 'admin.php?page=bazaar-withdrawals&action=reject&withdrawal=' . $withdrawal->id ),
                    'bazaar_withdrawal_action'
                ),
                'label' => __( 'Reject', 'bazaar' ),
                'class' => 'reject',
            );
        }

        if ( 'approved' === $withdrawal->status ) {
            $links['paid'] = array(
                'url'   => wp_nonce_url(
                    admin_url( 'admin.php?page=bazaar-withdrawals&action=paid&withdrawal=' . $withdrawal->id ),
                    'bazaar_withdrawal_action'
                ),
                'label' => __( 'Mark as Paid', 'bazaar' ),
                'class' => 'paid',
            );
        }

        return $links;
    }

    /**
     * Get status badge HTML.
     *
     * @param string $status Status.
     * @return string
     */
    public static function get_status_badge( $status ) {
        $label = Bazaar_Withdrawal::get_status_label( $status );

        return sprintf(
            '<span class="bazaar-status-badge withdrawal-%s">%s</span>',
            esc_attr( $status ),
            esc_html( $label )
        );
    }

    /**
     * Format method details.
     *
     * @param string $method  Method ID.
     * @param string $details Serialized details.
     * @return string
     */
    public static function format_method_details( $method, $details ) {
        $details = maybe_unserialize( $details );

        if ( ! is_array( $details ) ) {
            return '-';
        }

        $output = '<div class="bazaar-method-details">';

        switch ( $method ) {
            case 'paypal':
                if ( isset( $details['email'] ) ) {
                    $output .= '<strong>' . __( 'PayPal Email:', 'bazaar' ) . '</strong> ' . esc_html( $details['email'] );
                }
                break;

            case 'bank_transfer':
                if ( isset( $details['account_name'] ) ) {
                    $output .= '<strong>' . __( 'Account Name:', 'bazaar' ) . '</strong> ' . esc_html( $details['account_name'] ) . '<br>';
                }
                if ( isset( $details['account_number'] ) ) {
                    $output .= '<strong>' . __( 'Account Number:', 'bazaar' ) . '</strong> ' . esc_html( $details['account_number'] ) . '<br>';
                }
                if ( isset( $details['bank_name'] ) ) {
                    $output .= '<strong>' . __( 'Bank Name:', 'bazaar' ) . '</strong> ' . esc_html( $details['bank_name'] ) . '<br>';
                }
                if ( isset( $details['routing_number'] ) && ! empty( $details['routing_number'] ) ) {
                    $output .= '<strong>' . __( 'Routing/SWIFT:', 'bazaar' ) . '</strong> ' . esc_html( $details['routing_number'] ) . '<br>';
                }
                if ( isset( $details['iban'] ) && ! empty( $details['iban'] ) ) {
                    $output .= '<strong>' . __( 'IBAN:', 'bazaar' ) . '</strong> ' . esc_html( $details['iban'] );
                }
                break;

            case 'stripe':
                if ( isset( $details['email'] ) ) {
                    $output .= '<strong>' . __( 'Stripe Email:', 'bazaar' ) . '</strong> ' . esc_html( $details['email'] );
                }
                break;

            default:
                foreach ( $details as $key => $value ) {
                    $output .= '<strong>' . esc_html( ucwords( str_replace( '_', ' ', $key ) ) ) . ':</strong> ' . esc_html( $value ) . '<br>';
                }
        }

        $output .= '</div>';

        return $output;
    }
}

// Initialize
new Bazaar_Admin_Withdrawals();
