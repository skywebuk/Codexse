<?php
/**
 * Notifications Management Class.
 *
 * @package Bazaar\Notifications
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Notifications Class.
 */
class Bazaar_Notifications {

    /**
     * Constructor.
     */
    public function __construct() {
        // Hook into various events for notifications
        add_action( 'bazaar_vendor_registered', array( $this, 'notify_admin_new_vendor' ), 10, 2 );
        add_action( 'bazaar_vendor_approved', array( $this, 'notify_vendor_approved' ) );
        add_action( 'bazaar_vendor_rejected', array( $this, 'notify_vendor_rejected' ) );
        add_action( 'bazaar_product_created', array( $this, 'notify_admin_new_product' ), 10, 3 );
        add_action( 'bazaar_sub_order_created', array( $this, 'notify_vendor_new_order' ), 10, 4 );
    }

    /**
     * Create notification.
     *
     * @param int    $user_id User ID.
     * @param string $type    Notification type.
     * @param string $title   Title.
     * @param string $message Message.
     * @param array  $data    Additional data.
     * @return int
     */
    public static function create_notification( $user_id, $type, $title, $message, $data = array() ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_notifications';

        $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'type'    => $type,
                'title'   => $title,
                'message' => $message,
                'data'    => maybe_serialize( $data ),
            ),
            array( '%d', '%s', '%s', '%s', '%s' )
        );

        return $wpdb->insert_id;
    }

    /**
     * Get user notifications.
     *
     * @param int   $user_id User ID.
     * @param array $args    Query arguments.
     * @return array
     */
    public static function get_notifications( $user_id, $args = array() ) {
        global $wpdb;

        $defaults = array(
            'per_page' => 20,
            'page'     => 1,
            'is_read'  => null,
            'type'     => '',
        );

        $args = wp_parse_args( $args, $defaults );
        $table_name = $wpdb->prefix . 'bazaar_notifications';

        $where = $wpdb->prepare( "WHERE user_id = %d", $user_id );

        if ( null !== $args['is_read'] ) {
            $where .= $wpdb->prepare( " AND is_read = %d", $args['is_read'] ? 1 : 0 );
        }

        if ( ! empty( $args['type'] ) ) {
            $where .= $wpdb->prepare( " AND type = %s", $args['type'] );
        }

        $offset = ( $args['page'] - 1 ) * $args['per_page'];

        $notifications = $wpdb->get_results(
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
            'notifications' => $notifications,
            'total'         => intval( $total ),
            'pages'         => ceil( $total / $args['per_page'] ),
        );
    }

    /**
     * Get unread count.
     *
     * @param int $user_id User ID.
     * @return int
     */
    public static function get_unread_count( $user_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_notifications';

        return intval(
            $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$table_name} WHERE user_id = %d AND is_read = 0",
                    $user_id
                )
            )
        );
    }

    /**
     * Mark notification as read.
     *
     * @param int $notification_id Notification ID.
     * @return bool
     */
    public static function mark_as_read( $notification_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_notifications';

        $result = $wpdb->update(
            $table_name,
            array( 'is_read' => 1 ),
            array( 'id' => $notification_id ),
            array( '%d' ),
            array( '%d' )
        );

        return false !== $result;
    }

    /**
     * Mark all notifications as read for user.
     *
     * @param int $user_id User ID.
     * @return bool
     */
    public static function mark_all_as_read( $user_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_notifications';

        $result = $wpdb->update(
            $table_name,
            array( 'is_read' => 1 ),
            array( 'user_id' => $user_id, 'is_read' => 0 ),
            array( '%d' ),
            array( '%d', '%d' )
        );

        return false !== $result;
    }

    /**
     * Delete notification.
     *
     * @param int $notification_id Notification ID.
     * @return bool
     */
    public static function delete_notification( $notification_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_notifications';

        $result = $wpdb->delete(
            $table_name,
            array( 'id' => $notification_id ),
            array( '%d' )
        );

        return false !== $result;
    }

    /**
     * Send email notification.
     *
     * @param string $to      Email recipient.
     * @param string $subject Subject.
     * @param string $message Message.
     * @param array  $headers Headers.
     * @return bool
     */
    public static function send_email( $to, $subject, $message, $headers = array() ) {
        if ( 'yes' !== get_option( 'bazaar_email_notifications', 'yes' ) ) {
            return false;
        }

        $default_headers = array(
            'Content-Type: text/html; charset=UTF-8',
        );

        $headers = array_merge( $default_headers, $headers );

        // Wrap message in template
        $message = self::get_email_template( $subject, $message );

        return wp_mail( $to, $subject, $message, $headers );
    }

    /**
     * Get email template.
     *
     * @param string $subject Subject.
     * @param string $content Content.
     * @return string
     */
    private static function get_email_template( $subject, $content ) {
        $site_name = get_bloginfo( 'name' );

        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title><?php echo esc_html( $subject ); ?></title>
        </head>
        <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
                <tr>
                    <td align="center">
                        <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <tr>
                                <td style="padding: 30px; text-align: center; background-color: #4CAF50; border-radius: 5px 5px 0 0;">
                                    <h1 style="color: #ffffff; margin: 0;"><?php echo esc_html( $site_name ); ?></h1>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 30px;">
                                    <?php echo wp_kses_post( $content ); ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 5px 5px;">
                                    <p style="margin: 0; color: #666; font-size: 12px;">
                                        <?php
                                        /* translators: %s: site name */
                                        printf( esc_html__( 'This email was sent from %s', 'bazaar' ), esc_html( $site_name ) );
                                        ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

    /**
     * Notify admin of new vendor registration.
     *
     * @param int   $user_id User ID.
     * @param array $data    Registration data.
     */
    public function notify_admin_new_vendor( $user_id, $data ) {
        $admin_email = get_option( 'admin_email' );
        $user = get_userdata( $user_id );

        if ( ! $user ) {
            return;
        }

        $subject = __( 'New Vendor Registration', 'bazaar' );
        $message = sprintf(
            /* translators: 1: vendor name, 2: vendor email */
            __( '<p>A new vendor has registered on your marketplace.</p><p><strong>Name:</strong> %1$s<br><strong>Email:</strong> %2$s</p><p>Please review the application in your admin panel.</p>', 'bazaar' ),
            esc_html( $user->display_name ),
            esc_html( $user->user_email )
        );

        self::send_email( $admin_email, $subject, $message );

        // Create admin notification
        $admins = get_users( array( 'role' => 'administrator' ) );
        foreach ( $admins as $admin ) {
            self::create_notification(
                $admin->ID,
                'new_vendor',
                __( 'New Vendor Registration', 'bazaar' ),
                sprintf(
                    /* translators: %s: vendor name */
                    __( '%s has registered as a vendor.', 'bazaar' ),
                    $user->display_name
                ),
                array( 'vendor_id' => $user_id )
            );
        }
    }

    /**
     * Notify vendor of approval.
     *
     * @param int $vendor_id Vendor ID.
     */
    public function notify_vendor_approved( $vendor_id ) {
        $user = get_userdata( $vendor_id );

        if ( ! $user ) {
            return;
        }

        $dashboard_url = bazaar_get_dashboard_url();
        $subject = __( 'Your Vendor Account Has Been Approved', 'bazaar' );
        $message = sprintf(
            /* translators: 1: vendor name, 2: dashboard URL */
            __( '<p>Hi %1$s,</p><p>Congratulations! Your vendor account has been approved. You can now start adding products and selling on our marketplace.</p><p><a href="%2$s">Go to Vendor Dashboard</a></p>', 'bazaar' ),
            esc_html( $user->display_name ),
            esc_url( $dashboard_url )
        );

        self::send_email( $user->user_email, $subject, $message );

        // Create notification
        self::create_notification(
            $vendor_id,
            'account_approved',
            __( 'Account Approved', 'bazaar' ),
            __( 'Your vendor account has been approved. You can now start selling!', 'bazaar' )
        );
    }

    /**
     * Notify vendor of rejection.
     *
     * @param int $vendor_id Vendor ID.
     */
    public function notify_vendor_rejected( $vendor_id ) {
        $user = get_userdata( $vendor_id );

        if ( ! $user ) {
            return;
        }

        $subject = __( 'Your Vendor Application', 'bazaar' );
        $message = sprintf(
            /* translators: %s: vendor name */
            __( '<p>Hi %s,</p><p>We regret to inform you that your vendor application has not been approved at this time. If you have any questions, please contact us.</p>', 'bazaar' ),
            esc_html( $user->display_name )
        );

        self::send_email( $user->user_email, $subject, $message );

        // Create notification
        self::create_notification(
            $vendor_id,
            'account_rejected',
            __( 'Application Not Approved', 'bazaar' ),
            __( 'Your vendor application was not approved. Please contact support for more information.', 'bazaar' )
        );
    }

    /**
     * Notify admin of new product.
     *
     * @param int   $product_id Product ID.
     * @param int   $vendor_id  Vendor ID.
     * @param array $data       Product data.
     */
    public function notify_admin_new_product( $product_id, $vendor_id, $data ) {
        if ( 'enabled' !== get_option( 'bazaar_product_moderation', 'enabled' ) ) {
            return;
        }

        $product = wc_get_product( $product_id );
        $vendor = Bazaar_Vendor::get_vendor( $vendor_id );

        if ( ! $product || ! $vendor ) {
            return;
        }

        // Create admin notification
        $admins = get_users( array( 'role' => 'administrator' ) );
        foreach ( $admins as $admin ) {
            self::create_notification(
                $admin->ID,
                'product_pending',
                __( 'Product Pending Review', 'bazaar' ),
                sprintf(
                    /* translators: 1: product title, 2: vendor name */
                    __( '%1$s has submitted "%2$s" for review.', 'bazaar' ),
                    $vendor['store_name'],
                    $product->get_title()
                ),
                array(
                    'product_id' => $product_id,
                    'vendor_id'  => $vendor_id,
                )
            );
        }
    }

    /**
     * Notify vendor of new order.
     *
     * @param int   $sub_order_id Sub-order ID.
     * @param int   $order_id     Parent order ID.
     * @param int   $vendor_id    Vendor ID.
     * @param array $items        Order items.
     */
    public function notify_vendor_new_order( $sub_order_id, $order_id, $vendor_id, $items ) {
        $user = get_userdata( $vendor_id );
        $order = wc_get_order( $order_id );

        if ( ! $user || ! $order ) {
            return;
        }

        $order_total = array_sum( array_column( $items, 'total' ) );

        $subject = sprintf(
            /* translators: %d: order number */
            __( 'New Order #%d', 'bazaar' ),
            $order_id
        );

        $message = sprintf(
            /* translators: 1: order number, 2: order total */
            __( '<p>You have received a new order!</p><p><strong>Order:</strong> #%1$d<br><strong>Total:</strong> %2$s</p><p>Please process this order from your vendor dashboard.</p>', 'bazaar' ),
            $order_id,
            wc_price( $order_total )
        );

        self::send_email( $user->user_email, $subject, $message );

        // Create notification
        self::create_notification(
            $vendor_id,
            'new_order',
            sprintf( __( 'New Order #%d', 'bazaar' ), $order_id ),
            sprintf(
                /* translators: %s: order total */
                __( 'You have a new order worth %s.', 'bazaar' ),
                wc_price( $order_total )
            ),
            array(
                'order_id'     => $order_id,
                'sub_order_id' => $sub_order_id,
            )
        );
    }

    /**
     * Send withdrawal request notification.
     *
     * @param int $withdrawal_id Withdrawal ID.
     */
    public static function send_withdrawal_request_notification( $withdrawal_id ) {
        $withdrawal = Bazaar_Withdrawal::get_withdrawal( $withdrawal_id );

        if ( ! $withdrawal ) {
            return;
        }

        // Notify admins
        $admin_email = get_option( 'admin_email' );
        $vendor = get_userdata( $withdrawal->vendor_id );

        $subject = __( 'New Withdrawal Request', 'bazaar' );
        $message = sprintf(
            /* translators: 1: vendor name, 2: withdrawal amount */
            __( '<p>A new withdrawal request has been submitted.</p><p><strong>Vendor:</strong> %1$s<br><strong>Amount:</strong> %2$s<br><strong>Method:</strong> %3$s</p>', 'bazaar' ),
            esc_html( $vendor ? $vendor->display_name : 'Unknown' ),
            wc_price( $withdrawal->amount ),
            ucfirst( $withdrawal->method )
        );

        self::send_email( $admin_email, $subject, $message );

        // Create admin notification
        $admins = get_users( array( 'role' => 'administrator' ) );
        foreach ( $admins as $admin ) {
            self::create_notification(
                $admin->ID,
                'withdrawal_request',
                __( 'New Withdrawal Request', 'bazaar' ),
                sprintf(
                    /* translators: 1: vendor name, 2: amount */
                    __( '%1$s requested a withdrawal of %2$s.', 'bazaar' ),
                    $vendor ? $vendor->display_name : 'Unknown',
                    wc_price( $withdrawal->amount )
                ),
                array( 'withdrawal_id' => $withdrawal_id )
            );
        }
    }

    /**
     * Send withdrawal status notification.
     *
     * @param int    $withdrawal_id Withdrawal ID.
     * @param string $status        New status.
     */
    public static function send_withdrawal_status_notification( $withdrawal_id, $status ) {
        $withdrawal = Bazaar_Withdrawal::get_withdrawal( $withdrawal_id );

        if ( ! $withdrawal ) {
            return;
        }

        $vendor = get_userdata( $withdrawal->vendor_id );

        if ( ! $vendor ) {
            return;
        }

        $status_labels = array(
            'approved' => __( 'Approved', 'bazaar' ),
            'paid'     => __( 'Paid', 'bazaar' ),
            'rejected' => __( 'Rejected', 'bazaar' ),
        );

        $subject = sprintf(
            /* translators: %s: status */
            __( 'Withdrawal Request %s', 'bazaar' ),
            $status_labels[ $status ] ?? $status
        );

        $message = sprintf(
            /* translators: 1: amount, 2: status */
            __( '<p>Your withdrawal request for %1$s has been %2$s.</p>', 'bazaar' ),
            wc_price( $withdrawal->amount ),
            strtolower( $status_labels[ $status ] ?? $status )
        );

        if ( ! empty( $withdrawal->admin_note ) ) {
            $message .= sprintf(
                /* translators: %s: admin note */
                __( '<p><strong>Note:</strong> %s</p>', 'bazaar' ),
                esc_html( $withdrawal->admin_note )
            );
        }

        self::send_email( $vendor->user_email, $subject, $message );

        // Create notification
        self::create_notification(
            $withdrawal->vendor_id,
            'withdrawal_' . $status,
            $subject,
            sprintf(
                /* translators: 1: amount, 2: status */
                __( 'Your withdrawal of %1$s has been %2$s.', 'bazaar' ),
                wc_price( $withdrawal->amount ),
                strtolower( $status_labels[ $status ] ?? $status )
            ),
            array( 'withdrawal_id' => $withdrawal_id )
        );
    }
}

// Initialize
new Bazaar_Notifications();
