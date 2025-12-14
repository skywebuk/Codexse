<?php
/**
 * REST API Class.
 *
 * @package Bazaar\REST_API
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_REST_API Class.
 */
class Bazaar_REST_API {

    /**
     * Namespace.
     *
     * @var string
     */
    protected $namespace = 'bazaar/v1';

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    /**
     * Register routes.
     */
    public function register_routes() {
        // Vendors endpoints
        register_rest_route(
            $this->namespace,
            '/vendors',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_vendors' ),
                    'permission_callback' => '__return_true',
                    'args'                => $this->get_collection_params(),
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            '/vendors/(?P<id>[\d]+)',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_vendor' ),
                    'permission_callback' => '__return_true',
                    'args'                => array(
                        'id' => array(
                            'validate_callback' => function ( $param ) {
                                return is_numeric( $param );
                            },
                        ),
                    ),
                ),
            )
        );

        // Vendor products
        register_rest_route(
            $this->namespace,
            '/vendors/(?P<id>[\d]+)/products',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_vendor_products' ),
                    'permission_callback' => '__return_true',
                    'args'                => $this->get_collection_params(),
                ),
            )
        );

        // Vendor reviews
        register_rest_route(
            $this->namespace,
            '/vendors/(?P<id>[\d]+)/reviews',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_vendor_reviews' ),
                    'permission_callback' => '__return_true',
                    'args'                => $this->get_collection_params(),
                ),
            )
        );

        // Current vendor endpoints (authenticated)
        register_rest_route(
            $this->namespace,
            '/vendor/me',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_current_vendor' ),
                    'permission_callback' => array( $this, 'is_vendor' ),
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            '/vendor/products',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_my_products' ),
                    'permission_callback' => array( $this, 'is_vendor' ),
                    'args'                => $this->get_collection_params(),
                ),
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'create_product' ),
                    'permission_callback' => array( $this, 'can_create_product' ),
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            '/vendor/orders',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_my_orders' ),
                    'permission_callback' => array( $this, 'is_vendor' ),
                    'args'                => $this->get_collection_params(),
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            '/vendor/earnings',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_my_earnings' ),
                    'permission_callback' => array( $this, 'is_vendor' ),
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            '/vendor/withdrawals',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_my_withdrawals' ),
                    'permission_callback' => array( $this, 'is_vendor' ),
                    'args'                => $this->get_collection_params(),
                ),
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'request_withdrawal' ),
                    'permission_callback' => array( $this, 'can_withdraw' ),
                ),
            )
        );

        // Store lookup by slug
        register_rest_route(
            $this->namespace,
            '/stores/(?P<slug>[a-zA-Z0-9-]+)',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_store_by_slug' ),
                    'permission_callback' => '__return_true',
                ),
            )
        );
    }

    /**
     * Get collection parameters.
     *
     * @return array
     */
    protected function get_collection_params() {
        return array(
            'page'     => array(
                'default'           => 1,
                'sanitize_callback' => 'absint',
            ),
            'per_page' => array(
                'default'           => 10,
                'sanitize_callback' => 'absint',
            ),
            'search'   => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'orderby'  => array(
                'default'           => 'date',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'order'    => array(
                'default'           => 'DESC',
                'sanitize_callback' => 'sanitize_text_field',
            ),
        );
    }

    /**
     * Check if user is vendor.
     *
     * @return bool
     */
    public function is_vendor() {
        return Bazaar_Roles::is_active_vendor();
    }

    /**
     * Check if user can create product.
     *
     * @return bool
     */
    public function can_create_product() {
        return Bazaar_Roles::current_user_can( 'bazaar_add_product' );
    }

    /**
     * Check if user can withdraw.
     *
     * @return bool
     */
    public function can_withdraw() {
        return Bazaar_Roles::current_user_can( 'bazaar_withdraw' );
    }

    /**
     * Get vendors.
     *
     * @param WP_REST_Request $request Request.
     * @return WP_REST_Response
     */
    public function get_vendors( $request ) {
        $args = array(
            'status' => 'approved',
            'number' => $request->get_param( 'per_page' ),
            'paged'  => $request->get_param( 'page' ),
        );

        if ( $request->get_param( 'search' ) ) {
            $args['search'] = $request->get_param( 'search' );
        }

        $vendors = Bazaar_Vendor::get_vendors( $args );

        $data = array();

        foreach ( $vendors['vendors'] as $user ) {
            $vendor = Bazaar_Vendor::get_vendor( $user->ID );
            $data[] = $this->prepare_vendor_for_response( $vendor );
        }

        return new WP_REST_Response(
            array(
                'vendors' => $data,
                'total'   => $vendors['total'],
                'pages'   => $vendors['pages'],
            ),
            200
        );
    }

    /**
     * Get vendor.
     *
     * @param WP_REST_Request $request Request.
     * @return WP_REST_Response|WP_Error
     */
    public function get_vendor( $request ) {
        $vendor_id = $request->get_param( 'id' );
        $vendor = Bazaar_Vendor::get_vendor( $vendor_id );

        if ( ! $vendor ) {
            return new WP_Error( 'not_found', __( 'Vendor not found.', 'bazaar' ), array( 'status' => 404 ) );
        }

        return new WP_REST_Response( $this->prepare_vendor_for_response( $vendor ), 200 );
    }

    /**
     * Get vendor products.
     *
     * @param WP_REST_Request $request Request.
     * @return WP_REST_Response
     */
    public function get_vendor_products( $request ) {
        $vendor_id = $request->get_param( 'id' );

        $products = Bazaar_Product::get_vendor_products(
            $vendor_id,
            array(
                'post_status'    => 'publish',
                'posts_per_page' => $request->get_param( 'per_page' ),
                'paged'          => $request->get_param( 'page' ),
            )
        );

        $data = array();

        foreach ( $products['products'] as $product_post ) {
            $product = wc_get_product( $product_post->ID );
            if ( $product ) {
                $data[] = $this->prepare_product_for_response( $product );
            }
        }

        return new WP_REST_Response(
            array(
                'products' => $data,
                'total'    => $products['total'],
                'pages'    => $products['pages'],
            ),
            200
        );
    }

    /**
     * Get vendor reviews.
     *
     * @param WP_REST_Request $request Request.
     * @return WP_REST_Response
     */
    public function get_vendor_reviews( $request ) {
        $vendor_id = $request->get_param( 'id' );

        $reviews = Bazaar_Reviews::get_vendor_reviews(
            $vendor_id,
            array(
                'per_page' => $request->get_param( 'per_page' ),
                'page'     => $request->get_param( 'page' ),
            )
        );

        $data = array();

        foreach ( $reviews['reviews'] as $review ) {
            $data[] = $this->prepare_review_for_response( $review );
        }

        return new WP_REST_Response(
            array(
                'reviews' => $data,
                'total'   => $reviews['total'],
                'pages'   => $reviews['pages'],
            ),
            200
        );
    }

    /**
     * Get current vendor.
     *
     * @return WP_REST_Response
     */
    public function get_current_vendor() {
        $vendor = Bazaar_Vendor::get_vendor( get_current_user_id() );

        return new WP_REST_Response( $this->prepare_vendor_for_response( $vendor, true ), 200 );
    }

    /**
     * Get my products.
     *
     * @param WP_REST_Request $request Request.
     * @return WP_REST_Response
     */
    public function get_my_products( $request ) {
        $vendor_id = get_current_user_id();

        $products = Bazaar_Product::get_vendor_products(
            $vendor_id,
            array(
                'posts_per_page' => $request->get_param( 'per_page' ),
                'paged'          => $request->get_param( 'page' ),
            )
        );

        $data = array();

        foreach ( $products['products'] as $product_post ) {
            $product = wc_get_product( $product_post->ID );
            if ( $product ) {
                $data[] = $this->prepare_product_for_response( $product );
            }
        }

        return new WP_REST_Response(
            array(
                'products' => $data,
                'total'    => $products['total'],
                'pages'    => $products['pages'],
            ),
            200
        );
    }

    /**
     * Create product.
     *
     * @param WP_REST_Request $request Request.
     * @return WP_REST_Response|WP_Error
     */
    public function create_product( $request ) {
        $vendor_id = get_current_user_id();
        $params = $request->get_json_params();

        $result = Bazaar_Product::create_product( $vendor_id, $params );

        if ( is_wp_error( $result ) ) {
            return $result;
        }

        $product = wc_get_product( $result );

        return new WP_REST_Response( $this->prepare_product_for_response( $product ), 201 );
    }

    /**
     * Get my orders.
     *
     * @param WP_REST_Request $request Request.
     * @return WP_REST_Response
     */
    public function get_my_orders( $request ) {
        $vendor_id = get_current_user_id();

        $orders = Bazaar_Orders::get_vendor_orders(
            $vendor_id,
            array(
                'per_page' => $request->get_param( 'per_page' ),
                'page'     => $request->get_param( 'page' ),
            )
        );

        $data = array();

        foreach ( $orders['orders'] as $sub_order ) {
            $order = wc_get_order( $sub_order->parent_order_id );
            if ( $order ) {
                $data[] = array(
                    'id'             => $sub_order->id,
                    'order_id'       => $sub_order->parent_order_id,
                    'order_total'    => $sub_order->order_total,
                    'commission'     => $sub_order->commission,
                    'vendor_earning' => $sub_order->vendor_earning,
                    'status'         => $sub_order->status,
                    'customer_name'  => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                    'date'           => $sub_order->created_at,
                );
            }
        }

        return new WP_REST_Response(
            array(
                'orders' => $data,
                'total'  => $orders['total'],
                'pages'  => $orders['pages'],
            ),
            200
        );
    }

    /**
     * Get my earnings.
     *
     * @return WP_REST_Response
     */
    public function get_my_earnings() {
        $vendor_id = get_current_user_id();

        $earnings = Bazaar_Commission::get_vendor_earnings( $vendor_id );
        $balance = Bazaar_Vendor::get_balance( $vendor_id );

        return new WP_REST_Response(
            array(
                'balance'        => $balance,
                'gross_sales'    => $earnings['gross_sales'],
                'total_earnings' => $earnings['total_earnings'],
                'commission'     => $earnings['total_commission'],
                'net_earnings'   => $earnings['net_earnings'],
            ),
            200
        );
    }

    /**
     * Get my withdrawals.
     *
     * @param WP_REST_Request $request Request.
     * @return WP_REST_Response
     */
    public function get_my_withdrawals( $request ) {
        $vendor_id = get_current_user_id();

        $withdrawals = Bazaar_Withdrawal::get_vendor_withdrawals(
            $vendor_id,
            array(
                'per_page' => $request->get_param( 'per_page' ),
                'page'     => $request->get_param( 'page' ),
            )
        );

        return new WP_REST_Response(
            array(
                'withdrawals' => $withdrawals['withdrawals'],
                'total'       => $withdrawals['total'],
                'pages'       => $withdrawals['pages'],
            ),
            200
        );
    }

    /**
     * Request withdrawal.
     *
     * @param WP_REST_Request $request Request.
     * @return WP_REST_Response|WP_Error
     */
    public function request_withdrawal( $request ) {
        $vendor_id = get_current_user_id();
        $params = $request->get_json_params();

        $result = Bazaar_Withdrawal::request_withdrawal(
            $vendor_id,
            floatval( $params['amount'] ?? 0 ),
            sanitize_text_field( $params['method'] ?? '' ),
            sanitize_textarea_field( $params['note'] ?? '' )
        );

        if ( is_wp_error( $result ) ) {
            return $result;
        }

        return new WP_REST_Response(
            array(
                'message'       => __( 'Withdrawal request submitted.', 'bazaar' ),
                'withdrawal_id' => $result,
            ),
            201
        );
    }

    /**
     * Get store by slug.
     *
     * @param WP_REST_Request $request Request.
     * @return WP_REST_Response|WP_Error
     */
    public function get_store_by_slug( $request ) {
        $slug = $request->get_param( 'slug' );
        $vendor_id = Bazaar_Vendor::get_vendor_id_by_slug( $slug );

        if ( ! $vendor_id ) {
            return new WP_Error( 'not_found', __( 'Store not found.', 'bazaar' ), array( 'status' => 404 ) );
        }

        $vendor = Bazaar_Vendor::get_vendor( $vendor_id );

        return new WP_REST_Response( $this->prepare_vendor_for_response( $vendor ), 200 );
    }

    /**
     * Prepare vendor for response.
     *
     * @param array $vendor      Vendor data.
     * @param bool  $include_private Include private data.
     * @return array
     */
    protected function prepare_vendor_for_response( $vendor, $include_private = false ) {
        $data = array(
            'id'          => $vendor['id'],
            'store_name'  => $vendor['store_name'],
            'store_slug'  => $vendor['store_slug'],
            'store_url'   => $vendor['store_url'],
            'description' => $vendor['description'],
            'logo'        => $vendor['logo'] ? wp_get_attachment_url( $vendor['logo'] ) : '',
            'banner'      => $vendor['banner'] ? wp_get_attachment_url( $vendor['banner'] ) : '',
            'rating'      => $vendor['rating'],
            'social'      => $vendor['social'],
            'address'     => array(
                'city'    => $vendor['address']['city'] ?? '',
                'state'   => $vendor['address']['state'] ?? '',
                'country' => $vendor['address']['country'] ?? '',
            ),
        );

        if ( $include_private ) {
            $data['email'] = $vendor['email'];
            $data['phone'] = $vendor['phone'];
            $data['balance'] = $vendor['balance'];
            $data['total_sales'] = $vendor['total_sales'];
            $data['status'] = $vendor['status'];
            $data['vacation_mode'] = $vendor['vacation_mode'];
            $data['address'] = $vendor['address'];
        }

        return $data;
    }

    /**
     * Prepare product for response.
     *
     * @param WC_Product $product Product object.
     * @return array
     */
    protected function prepare_product_for_response( $product ) {
        return array(
            'id'             => $product->get_id(),
            'name'           => $product->get_name(),
            'slug'           => $product->get_slug(),
            'permalink'      => $product->get_permalink(),
            'status'         => $product->get_status(),
            'type'           => $product->get_type(),
            'price'          => $product->get_price(),
            'regular_price'  => $product->get_regular_price(),
            'sale_price'     => $product->get_sale_price(),
            'stock_status'   => $product->get_stock_status(),
            'stock_quantity' => $product->get_stock_quantity(),
            'image'          => wp_get_attachment_url( $product->get_image_id() ),
            'categories'     => wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) ),
        );
    }

    /**
     * Prepare review for response.
     *
     * @param object $review Review object.
     * @return array
     */
    protected function prepare_review_for_response( $review ) {
        $customer = get_userdata( $review->customer_id );

        return array(
            'id'            => $review->id,
            'rating'        => $review->rating,
            'title'         => $review->title,
            'content'       => $review->content,
            'customer_name' => $customer ? $customer->display_name : __( 'Anonymous', 'bazaar' ),
            'date'          => $review->created_at,
        );
    }
}

// Initialize
new Bazaar_REST_API();
