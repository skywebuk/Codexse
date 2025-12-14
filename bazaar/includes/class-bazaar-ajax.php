<?php
/**
 * AJAX Handlers Class.
 *
 * @package Bazaar\Ajax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Ajax Class.
 */
class Bazaar_Ajax {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->add_ajax_events();
    }

    /**
     * Register AJAX events.
     */
    public function add_ajax_events() {
        $ajax_events = array(
            // Vendor registration
            'vendor_registration' => false,

            // Vendor dashboard actions
            'save_vendor_profile'    => true,
            'save_product'           => true,
            'delete_product'         => true,
            'update_order_status'    => true,
            'request_withdrawal'     => true,
            'cancel_withdrawal'      => true,
            'save_shipping_zone'     => true,
            'delete_shipping_zone'   => true,
            'toggle_vacation_mode'   => true,
            'mark_notifications_read' => true,

            // Admin actions
            'approve_vendor'         => true,
            'reject_vendor'          => true,
            'approve_withdrawal'     => true,
            'reject_withdrawal'      => true,
            'mark_withdrawal_paid'   => true,
            'approve_product'        => true,
            'reject_product'         => true,
            'approve_review'         => true,
            'reject_review'          => true,

            // Frontend actions
            'submit_vendor_review'   => true,
            'load_more_products'     => false,
            'load_more_reviews'      => false,
        );

        foreach ( $ajax_events as $event => $nopriv ) {
            add_action( 'wp_ajax_bazaar_' . $event, array( $this, $event ) );

            if ( ! $nopriv ) {
                add_action( 'wp_ajax_nopriv_bazaar_' . $event, array( $this, $event ) );
            }
        }
    }

    /**
     * Vendor registration.
     */
    public function vendor_registration() {
        check_ajax_referer( 'bazaar_vendor_registration', 'nonce' );

        $data = array(
            'username'    => isset( $_POST['username'] ) ? sanitize_user( wp_unslash( $_POST['username'] ) ) : '',
            'email'       => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
            'password'    => isset( $_POST['password'] ) ? $_POST['password'] : '',
            'store_name'  => isset( $_POST['store_name'] ) ? sanitize_text_field( wp_unslash( $_POST['store_name'] ) ) : '',
            'first_name'  => isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '',
            'last_name'   => isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '',
            'phone'       => isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '',
            'description' => isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '',
        );

        $result = Bazaar_Vendor::register_vendor( $data );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        $approval = get_option( 'bazaar_vendor_approval', 'manual' );

        if ( 'manual' === $approval ) {
            $message = __( 'Registration successful! Your application is pending approval.', 'bazaar' );
        } else {
            $message = __( 'Registration successful! You can now access your vendor dashboard.', 'bazaar' );
        }

        wp_send_json_success(
            array(
                'message'      => $message,
                'redirect_url' => bazaar_get_dashboard_url(),
            )
        );
    }

    /**
     * Save vendor profile.
     */
    public function save_vendor_profile() {
        check_ajax_referer( 'bazaar_vendor_profile', 'nonce' );

        $vendor_id = get_current_user_id();

        if ( ! Bazaar_Roles::is_vendor( $vendor_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bazaar' ) ) );
        }

        $data = array();
        $fields = array(
            'store_name', 'store_slug', 'store_description', 'store_phone',
            'address_1', 'address_2', 'city', 'state', 'postcode', 'country',
            'social_facebook', 'social_twitter', 'social_instagram', 'social_youtube',
            'seo_title', 'seo_description',
        );

        foreach ( $fields as $field ) {
            if ( isset( $_POST[ $field ] ) ) {
                $data[ $field ] = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
            }
        }

        // Handle logo and banner
        if ( isset( $_POST['store_logo'] ) ) {
            $data['store_logo'] = intval( $_POST['store_logo'] );
        }

        if ( isset( $_POST['store_banner'] ) ) {
            $data['store_banner'] = intval( $_POST['store_banner'] );
        }

        $result = Bazaar_Vendor::update_profile( $vendor_id, $data );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array( 'message' => __( 'Profile updated successfully.', 'bazaar' ) ) );
    }

    /**
     * Save product.
     */
    public function save_product() {
        check_ajax_referer( 'bazaar_save_product', 'nonce' );

        $vendor_id = get_current_user_id();

        if ( ! Bazaar_Roles::current_user_can( 'bazaar_add_product', $vendor_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bazaar' ) ) );
        }

        $product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;

        $data = array(
            'title'             => isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '',
            'description'       => isset( $_POST['description'] ) ? wp_kses_post( wp_unslash( $_POST['description'] ) ) : '',
            'short_description' => isset( $_POST['short_description'] ) ? wp_kses_post( wp_unslash( $_POST['short_description'] ) ) : '',
            'regular_price'     => isset( $_POST['regular_price'] ) ? sanitize_text_field( wp_unslash( $_POST['regular_price'] ) ) : '',
            'sale_price'        => isset( $_POST['sale_price'] ) ? sanitize_text_field( wp_unslash( $_POST['sale_price'] ) ) : '',
            'sku'               => isset( $_POST['sku'] ) ? sanitize_text_field( wp_unslash( $_POST['sku'] ) ) : '',
            'manage_stock'      => isset( $_POST['manage_stock'] ) ? 'yes' : 'no',
            'stock_quantity'    => isset( $_POST['stock_quantity'] ) ? intval( $_POST['stock_quantity'] ) : 0,
            'stock_status'      => isset( $_POST['stock_status'] ) ? sanitize_text_field( wp_unslash( $_POST['stock_status'] ) ) : 'instock',
            'product_type'      => isset( $_POST['product_type'] ) ? sanitize_text_field( wp_unslash( $_POST['product_type'] ) ) : 'simple',
            'virtual'           => isset( $_POST['virtual'] ) ? 'yes' : 'no',
            'downloadable'      => isset( $_POST['downloadable'] ) ? 'yes' : 'no',
            'featured_image'    => isset( $_POST['featured_image'] ) ? intval( $_POST['featured_image'] ) : 0,
            'gallery'           => isset( $_POST['gallery'] ) ? array_map( 'intval', (array) $_POST['gallery'] ) : array(),
            'categories'        => isset( $_POST['categories'] ) ? array_map( 'intval', (array) $_POST['categories'] ) : array(),
            'tags'              => isset( $_POST['tags'] ) ? array_map( 'intval', (array) $_POST['tags'] ) : array(),
        );

        if ( $product_id ) {
            // Update existing product
            $result = Bazaar_Product::update_product( $product_id, $data );
        } else {
            // Create new product
            $result = Bazaar_Product::create_product( $vendor_id, $data );
        }

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        $message = $product_id ? __( 'Product updated successfully.', 'bazaar' ) : __( 'Product created successfully.', 'bazaar' );

        wp_send_json_success(
            array(
                'message'    => $message,
                'product_id' => is_int( $result ) ? $result : $product_id,
            )
        );
    }

    /**
     * Delete product.
     */
    public function delete_product() {
        check_ajax_referer( 'bazaar_delete_product', 'nonce' );

        $product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;

        if ( ! $product_id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid product.', 'bazaar' ) ) );
        }

        $result = Bazaar_Product::delete_product( $product_id );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array( 'message' => __( 'Product deleted successfully.', 'bazaar' ) ) );
    }

    /**
     * Update order status.
     */
    public function update_order_status() {
        check_ajax_referer( 'bazaar_update_order', 'nonce' );

        $vendor_id = get_current_user_id();
        $order_id = isset( $_POST['order_id'] ) ? intval( $_POST['order_id'] ) : 0;
        $status = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';

        if ( ! $order_id || ! $status ) {
            wp_send_json_error( array( 'message' => __( 'Invalid request.', 'bazaar' ) ) );
        }

        // Verify vendor owns this order
        $sub_order = Bazaar_Orders::get_vendor_sub_order( $order_id, $vendor_id );

        if ( ! $sub_order && ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bazaar' ) ) );
        }

        // Update sub-order status
        Bazaar_Orders::update_sub_order_status( $sub_order->id, $status );

        wp_send_json_success( array( 'message' => __( 'Order status updated.', 'bazaar' ) ) );
    }

    /**
     * Request withdrawal.
     */
    public function request_withdrawal() {
        check_ajax_referer( 'bazaar_withdrawal', 'nonce' );

        $vendor_id = get_current_user_id();
        $amount = isset( $_POST['amount'] ) ? floatval( $_POST['amount'] ) : 0;
        $method = isset( $_POST['method'] ) ? sanitize_text_field( wp_unslash( $_POST['method'] ) ) : '';
        $note = isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '';

        $result = Bazaar_Withdrawal::request_withdrawal( $vendor_id, $amount, $method, $note );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array( 'message' => __( 'Withdrawal request submitted successfully.', 'bazaar' ) ) );
    }

    /**
     * Cancel withdrawal.
     */
    public function cancel_withdrawal() {
        check_ajax_referer( 'bazaar_withdrawal', 'nonce' );

        $vendor_id = get_current_user_id();
        $withdrawal_id = isset( $_POST['withdrawal_id'] ) ? intval( $_POST['withdrawal_id'] ) : 0;

        $result = Bazaar_Withdrawal::cancel_withdrawal( $withdrawal_id, $vendor_id );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array( 'message' => __( 'Withdrawal cancelled.', 'bazaar' ) ) );
    }

    /**
     * Save shipping zone.
     */
    public function save_shipping_zone() {
        check_ajax_referer( 'bazaar_shipping', 'nonce' );

        $vendor_id = get_current_user_id();

        if ( ! Bazaar_Roles::current_user_can( 'bazaar_manage_shipping', $vendor_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bazaar' ) ) );
        }

        $data = array(
            'id'             => isset( $_POST['zone_id'] ) ? intval( $_POST['zone_id'] ) : 0,
            'zone_name'      => isset( $_POST['zone_name'] ) ? sanitize_text_field( wp_unslash( $_POST['zone_name'] ) ) : '',
            'zone_locations' => isset( $_POST['zone_locations'] ) ? (array) $_POST['zone_locations'] : array(),
            'methods'        => isset( $_POST['methods'] ) ? (array) $_POST['methods'] : array(),
            'is_enabled'     => isset( $_POST['is_enabled'] ),
        );

        $result = Bazaar_Shipping::save_shipping_zone( $vendor_id, $data );

        wp_send_json_success(
            array(
                'message' => __( 'Shipping zone saved.', 'bazaar' ),
                'zone_id' => $result,
            )
        );
    }

    /**
     * Delete shipping zone.
     */
    public function delete_shipping_zone() {
        check_ajax_referer( 'bazaar_shipping', 'nonce' );

        $vendor_id = get_current_user_id();
        $zone_id = isset( $_POST['zone_id'] ) ? intval( $_POST['zone_id'] ) : 0;

        $result = Bazaar_Shipping::delete_shipping_zone( $zone_id, $vendor_id );

        if ( ! $result ) {
            wp_send_json_error( array( 'message' => __( 'Could not delete shipping zone.', 'bazaar' ) ) );
        }

        wp_send_json_success( array( 'message' => __( 'Shipping zone deleted.', 'bazaar' ) ) );
    }

    /**
     * Toggle vacation mode.
     */
    public function toggle_vacation_mode() {
        check_ajax_referer( 'bazaar_vacation', 'nonce' );

        $vendor_id = get_current_user_id();
        $enabled = isset( $_POST['enabled'] ) && 'yes' === $_POST['enabled'];
        $message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

        if ( $enabled ) {
            Bazaar_Vendor::enable_vacation_mode( $vendor_id, $message );
        } else {
            Bazaar_Vendor::disable_vacation_mode( $vendor_id );
        }

        wp_send_json_success(
            array(
                'message' => $enabled ? __( 'Vacation mode enabled.', 'bazaar' ) : __( 'Vacation mode disabled.', 'bazaar' ),
            )
        );
    }

    /**
     * Mark notifications as read.
     */
    public function mark_notifications_read() {
        check_ajax_referer( 'bazaar_notifications', 'nonce' );

        $user_id = get_current_user_id();
        $notification_id = isset( $_POST['notification_id'] ) ? intval( $_POST['notification_id'] ) : 0;

        if ( $notification_id ) {
            Bazaar_Notifications::mark_as_read( $notification_id );
        } else {
            Bazaar_Notifications::mark_all_as_read( $user_id );
        }

        wp_send_json_success();
    }

    /**
     * Admin: Approve vendor.
     */
    public function approve_vendor() {
        check_ajax_referer( 'bazaar_admin', 'nonce' );

        if ( ! current_user_can( 'bazaar_manage_vendors' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bazaar' ) ) );
        }

        $vendor_id = isset( $_POST['vendor_id'] ) ? intval( $_POST['vendor_id'] ) : 0;

        Bazaar_Roles::update_vendor_status( $vendor_id, 'approved' );

        wp_send_json_success( array( 'message' => __( 'Vendor approved.', 'bazaar' ) ) );
    }

    /**
     * Admin: Reject vendor.
     */
    public function reject_vendor() {
        check_ajax_referer( 'bazaar_admin', 'nonce' );

        if ( ! current_user_can( 'bazaar_manage_vendors' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bazaar' ) ) );
        }

        $vendor_id = isset( $_POST['vendor_id'] ) ? intval( $_POST['vendor_id'] ) : 0;

        Bazaar_Roles::update_vendor_status( $vendor_id, 'rejected' );

        wp_send_json_success( array( 'message' => __( 'Vendor rejected.', 'bazaar' ) ) );
    }

    /**
     * Admin: Approve withdrawal.
     */
    public function approve_withdrawal() {
        check_ajax_referer( 'bazaar_admin', 'nonce' );

        if ( ! current_user_can( 'bazaar_manage_withdrawals' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bazaar' ) ) );
        }

        $withdrawal_id = isset( $_POST['withdrawal_id'] ) ? intval( $_POST['withdrawal_id'] ) : 0;
        $note = isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '';

        $result = Bazaar_Withdrawal::approve_withdrawal( $withdrawal_id, $note );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array( 'message' => __( 'Withdrawal approved.', 'bazaar' ) ) );
    }

    /**
     * Admin: Reject withdrawal.
     */
    public function reject_withdrawal() {
        check_ajax_referer( 'bazaar_admin', 'nonce' );

        if ( ! current_user_can( 'bazaar_manage_withdrawals' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bazaar' ) ) );
        }

        $withdrawal_id = isset( $_POST['withdrawal_id'] ) ? intval( $_POST['withdrawal_id'] ) : 0;
        $note = isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '';

        $result = Bazaar_Withdrawal::reject_withdrawal( $withdrawal_id, $note );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array( 'message' => __( 'Withdrawal rejected.', 'bazaar' ) ) );
    }

    /**
     * Admin: Mark withdrawal as paid.
     */
    public function mark_withdrawal_paid() {
        check_ajax_referer( 'bazaar_admin', 'nonce' );

        if ( ! current_user_can( 'bazaar_manage_withdrawals' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bazaar' ) ) );
        }

        $withdrawal_id = isset( $_POST['withdrawal_id'] ) ? intval( $_POST['withdrawal_id'] ) : 0;
        $note = isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '';

        $result = Bazaar_Withdrawal::mark_as_paid( $withdrawal_id, $note );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array( 'message' => __( 'Withdrawal marked as paid.', 'bazaar' ) ) );
    }

    /**
     * Admin: Approve product.
     */
    public function approve_product() {
        check_ajax_referer( 'bazaar_admin', 'nonce' );

        if ( ! current_user_can( 'bazaar_moderate_products' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bazaar' ) ) );
        }

        $product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;

        wp_update_post(
            array(
                'ID'          => $product_id,
                'post_status' => 'publish',
            )
        );

        wp_send_json_success( array( 'message' => __( 'Product approved.', 'bazaar' ) ) );
    }

    /**
     * Admin: Reject product.
     */
    public function reject_product() {
        check_ajax_referer( 'bazaar_admin', 'nonce' );

        if ( ! current_user_can( 'bazaar_moderate_products' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bazaar' ) ) );
        }

        $product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;

        wp_update_post(
            array(
                'ID'          => $product_id,
                'post_status' => 'draft',
            )
        );

        wp_send_json_success( array( 'message' => __( 'Product rejected.', 'bazaar' ) ) );
    }

    /**
     * Admin: Approve review.
     */
    public function approve_review() {
        check_ajax_referer( 'bazaar_admin', 'nonce' );

        if ( ! current_user_can( 'bazaar_moderate_reviews' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bazaar' ) ) );
        }

        $review_id = isset( $_POST['review_id'] ) ? intval( $_POST['review_id'] ) : 0;

        Bazaar_Reviews::update_review_status( $review_id, 'approved' );

        wp_send_json_success( array( 'message' => __( 'Review approved.', 'bazaar' ) ) );
    }

    /**
     * Admin: Reject review.
     */
    public function reject_review() {
        check_ajax_referer( 'bazaar_admin', 'nonce' );

        if ( ! current_user_can( 'bazaar_moderate_reviews' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bazaar' ) ) );
        }

        $review_id = isset( $_POST['review_id'] ) ? intval( $_POST['review_id'] ) : 0;

        Bazaar_Reviews::update_review_status( $review_id, 'rejected' );

        wp_send_json_success( array( 'message' => __( 'Review rejected.', 'bazaar' ) ) );
    }

    /**
     * Submit vendor review.
     */
    public function submit_vendor_review() {
        check_ajax_referer( 'bazaar_submit_review', 'nonce' );

        $vendor_id = isset( $_POST['vendor_id'] ) ? intval( $_POST['vendor_id'] ) : 0;
        $customer_id = get_current_user_id();

        $data = array(
            'rating'  => isset( $_POST['rating'] ) ? intval( $_POST['rating'] ) : 0,
            'title'   => isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '',
            'content' => isset( $_POST['content'] ) ? sanitize_textarea_field( wp_unslash( $_POST['content'] ) ) : '',
        );

        $result = Bazaar_Reviews::submit_review( $vendor_id, $customer_id, $data );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array( 'message' => __( 'Thank you for your review!', 'bazaar' ) ) );
    }

    /**
     * Load more products.
     */
    public function load_more_products() {
        $vendor_id = isset( $_POST['vendor_id'] ) ? intval( $_POST['vendor_id'] ) : 0;
        $page = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;

        $products = Bazaar_Product::get_vendor_products(
            $vendor_id,
            array(
                'post_status' => 'publish',
                'paged'       => $page,
            )
        );

        ob_start();

        foreach ( $products['products'] as $product_post ) {
            $product = wc_get_product( $product_post->ID );
            wc_get_template_part( 'content', 'product' );
        }

        $html = ob_get_clean();

        wp_send_json_success(
            array(
                'html'     => $html,
                'has_more' => $page < $products['pages'],
            )
        );
    }

    /**
     * Load more reviews.
     */
    public function load_more_reviews() {
        $vendor_id = isset( $_POST['vendor_id'] ) ? intval( $_POST['vendor_id'] ) : 0;
        $page = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;

        $reviews = Bazaar_Reviews::get_vendor_reviews(
            $vendor_id,
            array( 'page' => $page )
        );

        ob_start();

        foreach ( $reviews['reviews'] as $review ) {
            bazaar_get_template(
                'store/review-item.php',
                array( 'review' => $review )
            );
        }

        $html = ob_get_clean();

        wp_send_json_success(
            array(
                'html'     => $html,
                'has_more' => $page < $reviews['pages'],
            )
        );
    }
}

// Initialize
new Bazaar_Ajax();
