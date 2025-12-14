<?php
/**
 * Vendor Reviews Management Class.
 *
 * @package Bazaar\Reviews
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Reviews Class.
 */
class Bazaar_Reviews {

    /**
     * Constructor.
     */
    public function __construct() {
        // Add review form to vendor store page
        add_action( 'bazaar_after_store_products', array( $this, 'display_vendor_reviews' ) );

        // Handle review submission
        add_action( 'template_redirect', array( $this, 'handle_review_submission' ) );
    }

    /**
     * Submit vendor review.
     *
     * @param int    $vendor_id   Vendor ID.
     * @param int    $customer_id Customer ID.
     * @param array  $data        Review data.
     * @return int|WP_Error
     */
    public static function submit_review( $vendor_id, $customer_id, $data ) {
        global $wpdb;

        // Validate
        if ( ! Bazaar_Roles::is_vendor( $vendor_id ) ) {
            return new WP_Error( 'invalid_vendor', __( 'Invalid vendor.', 'bazaar' ) );
        }

        if ( ! $customer_id ) {
            return new WP_Error( 'not_logged_in', __( 'You must be logged in to leave a review.', 'bazaar' ) );
        }

        if ( $customer_id === $vendor_id ) {
            return new WP_Error( 'self_review', __( 'You cannot review your own store.', 'bazaar' ) );
        }

        // Check if already reviewed
        if ( self::has_reviewed( $vendor_id, $customer_id ) ) {
            return new WP_Error( 'already_reviewed', __( 'You have already reviewed this vendor.', 'bazaar' ) );
        }

        // Check if customer has purchased from vendor
        $has_purchased = self::has_purchased_from_vendor( $vendor_id, $customer_id );

        if ( ! $has_purchased && 'yes' === get_option( 'bazaar_review_require_purchase', 'yes' ) ) {
            return new WP_Error( 'no_purchase', __( 'You must purchase from this vendor before leaving a review.', 'bazaar' ) );
        }

        // Validate rating
        $rating = isset( $data['rating'] ) ? intval( $data['rating'] ) : 0;

        if ( $rating < 1 || $rating > 5 ) {
            return new WP_Error( 'invalid_rating', __( 'Please provide a rating between 1 and 5.', 'bazaar' ) );
        }

        $table_name = $wpdb->prefix . 'bazaar_vendor_reviews';

        // Determine status
        $moderation = get_option( 'bazaar_review_moderation', 'yes' );
        $status = 'yes' === $moderation ? 'pending' : 'approved';

        $result = $wpdb->insert(
            $table_name,
            array(
                'vendor_id'   => $vendor_id,
                'customer_id' => $customer_id,
                'order_id'    => isset( $data['order_id'] ) ? intval( $data['order_id'] ) : null,
                'rating'      => $rating,
                'title'       => isset( $data['title'] ) ? sanitize_text_field( $data['title'] ) : '',
                'content'     => isset( $data['content'] ) ? sanitize_textarea_field( $data['content'] ) : '',
                'status'      => $status,
            ),
            array( '%d', '%d', '%d', '%d', '%s', '%s', '%s' )
        );

        if ( false === $result ) {
            return new WP_Error( 'db_error', __( 'Could not save review.', 'bazaar' ) );
        }

        $review_id = $wpdb->insert_id;

        do_action( 'bazaar_vendor_review_submitted', $review_id, $vendor_id, $customer_id );

        return $review_id;
    }

    /**
     * Check if customer has reviewed vendor.
     *
     * @param int $vendor_id   Vendor ID.
     * @param int $customer_id Customer ID.
     * @return bool
     */
    public static function has_reviewed( $vendor_id, $customer_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_vendor_reviews';

        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE vendor_id = %d AND customer_id = %d",
                $vendor_id,
                $customer_id
            )
        );

        return $count > 0;
    }

    /**
     * Check if customer has purchased from vendor.
     *
     * @param int $vendor_id   Vendor ID.
     * @param int $customer_id Customer ID.
     * @return bool
     */
    public static function has_purchased_from_vendor( $vendor_id, $customer_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_sub_orders';

        // Get customer orders
        $orders = wc_get_orders(
            array(
                'customer_id' => $customer_id,
                'status'      => array( 'wc-completed', 'wc-processing' ),
                'limit'       => -1,
                'return'      => 'ids',
            )
        );

        if ( empty( $orders ) ) {
            return false;
        }

        // Check if any order has vendor
        $order_ids = implode( ',', array_map( 'intval', $orders ) );

        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE vendor_id = %d AND parent_order_id IN ({$order_ids})",
                $vendor_id
            )
        );

        return $count > 0;
    }

    /**
     * Get vendor reviews.
     *
     * @param int   $vendor_id Vendor ID.
     * @param array $args      Query arguments.
     * @return array
     */
    public static function get_vendor_reviews( $vendor_id, $args = array() ) {
        global $wpdb;

        $defaults = array(
            'per_page' => 10,
            'page'     => 1,
            'status'   => 'approved',
            'orderby'  => 'created_at',
            'order'    => 'DESC',
        );

        $args = wp_parse_args( $args, $defaults );
        $table_name = $wpdb->prefix . 'bazaar_vendor_reviews';

        $where = $wpdb->prepare( "WHERE vendor_id = %d", $vendor_id );

        if ( ! empty( $args['status'] ) ) {
            $where .= $wpdb->prepare( " AND status = %s", $args['status'] );
        }

        $offset = ( $args['page'] - 1 ) * $args['per_page'];

        $reviews = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} {$where} ORDER BY {$args['orderby']} {$args['order']} LIMIT %d OFFSET %d",
                $args['per_page'],
                $offset
            )
        );

        $total = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$table_name} {$where}"
        );

        return array(
            'reviews' => $reviews,
            'total'   => intval( $total ),
            'pages'   => ceil( $total / $args['per_page'] ),
        );
    }

    /**
     * Get review.
     *
     * @param int $review_id Review ID.
     * @return object|null
     */
    public static function get_review( $review_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_vendor_reviews';

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE id = %d",
                $review_id
            )
        );
    }

    /**
     * Update review status.
     *
     * @param int    $review_id Review ID.
     * @param string $status    New status.
     * @return bool
     */
    public static function update_review_status( $review_id, $status ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_vendor_reviews';

        $result = $wpdb->update(
            $table_name,
            array( 'status' => $status ),
            array( 'id' => $review_id ),
            array( '%s' ),
            array( '%d' )
        );

        if ( false !== $result ) {
            do_action( 'bazaar_review_status_updated', $review_id, $status );
            return true;
        }

        return false;
    }

    /**
     * Delete review.
     *
     * @param int $review_id Review ID.
     * @return bool
     */
    public static function delete_review( $review_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_vendor_reviews';

        $result = $wpdb->delete(
            $table_name,
            array( 'id' => $review_id ),
            array( '%d' )
        );

        return false !== $result;
    }

    /**
     * Get all reviews (admin).
     *
     * @param array $args Query arguments.
     * @return array
     */
    public static function get_all_reviews( $args = array() ) {
        global $wpdb;

        $defaults = array(
            'per_page'  => 20,
            'page'      => 1,
            'status'    => '',
            'vendor_id' => '',
            'rating'    => '',
        );

        $args = wp_parse_args( $args, $defaults );
        $table_name = $wpdb->prefix . 'bazaar_vendor_reviews';

        $where = "WHERE 1=1";

        if ( ! empty( $args['status'] ) ) {
            $where .= $wpdb->prepare( " AND status = %s", $args['status'] );
        }

        if ( ! empty( $args['vendor_id'] ) ) {
            $where .= $wpdb->prepare( " AND vendor_id = %d", $args['vendor_id'] );
        }

        if ( ! empty( $args['rating'] ) ) {
            $where .= $wpdb->prepare( " AND rating = %d", $args['rating'] );
        }

        $offset = ( $args['page'] - 1 ) * $args['per_page'];

        $reviews = $wpdb->get_results(
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
            'reviews' => $reviews,
            'total'   => intval( $total ),
            'pages'   => ceil( $total / $args['per_page'] ),
        );
    }

    /**
     * Display vendor reviews on store page.
     *
     * @param int $vendor_id Vendor ID.
     */
    public function display_vendor_reviews( $vendor_id ) {
        if ( 'yes' !== get_option( 'bazaar_vendor_review_enabled', 'yes' ) ) {
            return;
        }

        $reviews = self::get_vendor_reviews( $vendor_id );

        bazaar_get_template(
            'store/reviews.php',
            array(
                'vendor_id' => $vendor_id,
                'reviews'   => $reviews,
            )
        );
    }

    /**
     * Handle review submission.
     */
    public function handle_review_submission() {
        if ( ! isset( $_POST['bazaar_submit_review'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bazaar_review_nonce'] ?? '' ) ), 'bazaar_submit_review' ) ) {
            wc_add_notice( __( 'Security check failed.', 'bazaar' ), 'error' );
            return;
        }

        $vendor_id = isset( $_POST['vendor_id'] ) ? intval( $_POST['vendor_id'] ) : 0;
        $customer_id = get_current_user_id();

        $data = array(
            'rating'  => isset( $_POST['rating'] ) ? intval( $_POST['rating'] ) : 0,
            'title'   => isset( $_POST['review_title'] ) ? sanitize_text_field( wp_unslash( $_POST['review_title'] ) ) : '',
            'content' => isset( $_POST['review_content'] ) ? sanitize_textarea_field( wp_unslash( $_POST['review_content'] ) ) : '',
        );

        $result = self::submit_review( $vendor_id, $customer_id, $data );

        if ( is_wp_error( $result ) ) {
            wc_add_notice( $result->get_error_message(), 'error' );
        } else {
            $moderation = get_option( 'bazaar_review_moderation', 'yes' );
            if ( 'yes' === $moderation ) {
                wc_add_notice( __( 'Thank you for your review! It will be visible after moderation.', 'bazaar' ), 'success' );
            } else {
                wc_add_notice( __( 'Thank you for your review!', 'bazaar' ), 'success' );
            }
        }
    }

    /**
     * Get rating breakdown for vendor.
     *
     * @param int $vendor_id Vendor ID.
     * @return array
     */
    public static function get_rating_breakdown( $vendor_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_vendor_reviews';

        $breakdown = array(
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        );

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT rating, COUNT(*) as count FROM {$table_name} WHERE vendor_id = %d AND status = 'approved' GROUP BY rating",
                $vendor_id
            )
        );

        foreach ( $results as $row ) {
            if ( isset( $breakdown[ $row->rating ] ) ) {
                $breakdown[ $row->rating ] = intval( $row->count );
            }
        }

        return $breakdown;
    }
}

// Initialize
new Bazaar_Reviews();
