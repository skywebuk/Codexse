<?php
/**
 * Vendor Management Class.
 *
 * @package Bazaar\Vendor
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bazaar_Vendor Class.
 */
class Bazaar_Vendor {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'user_register', array( $this, 'maybe_set_vendor_role' ), 10, 1 );
        add_action( 'show_user_profile', array( $this, 'vendor_profile_fields' ) );
        add_action( 'edit_user_profile', array( $this, 'vendor_profile_fields' ) );
        add_action( 'personal_options_update', array( $this, 'save_vendor_profile_fields' ) );
        add_action( 'edit_user_profile_update', array( $this, 'save_vendor_profile_fields' ) );
    }

    /**
     * Get vendor by ID.
     *
     * @param int $vendor_id Vendor user ID.
     * @return array|false
     */
    public static function get_vendor( $vendor_id ) {
        if ( ! Bazaar_Roles::is_vendor( $vendor_id ) ) {
            return false;
        }

        $user = get_userdata( $vendor_id );

        if ( ! $user ) {
            return false;
        }

        return array(
            'id'             => $vendor_id,
            'user'           => $user,
            'store_name'     => self::get_store_name( $vendor_id ),
            'store_slug'     => self::get_store_slug( $vendor_id ),
            'store_url'      => self::get_store_url( $vendor_id ),
            'description'    => get_user_meta( $vendor_id, '_bazaar_store_description', true ),
            'logo'           => get_user_meta( $vendor_id, '_bazaar_store_logo', true ),
            'banner'         => get_user_meta( $vendor_id, '_bazaar_store_banner', true ),
            'email'          => $user->user_email,
            'phone'          => get_user_meta( $vendor_id, '_bazaar_store_phone', true ),
            'address'        => self::get_store_address( $vendor_id ),
            'social'         => self::get_social_links( $vendor_id ),
            'status'         => Bazaar_Roles::get_vendor_status( $vendor_id ),
            'registered'     => get_user_meta( $vendor_id, '_bazaar_vendor_registered', true ),
            'vacation_mode'  => get_user_meta( $vendor_id, '_bazaar_vacation_mode', true ),
            'verified'       => get_user_meta( $vendor_id, '_bazaar_vendor_verified', true ),
            'rating'         => self::get_vendor_rating( $vendor_id ),
            'total_sales'    => self::get_total_sales( $vendor_id ),
            'balance'        => self::get_balance( $vendor_id ),
        );
    }

    /**
     * Get store name.
     *
     * @param int $vendor_id Vendor ID.
     * @return string
     */
    public static function get_store_name( $vendor_id ) {
        $store_name = get_user_meta( $vendor_id, '_bazaar_store_name', true );

        if ( empty( $store_name ) ) {
            $user = get_userdata( $vendor_id );
            $store_name = $user ? $user->display_name : '';
        }

        return $store_name;
    }

    /**
     * Get store slug.
     *
     * @param int $vendor_id Vendor ID.
     * @return string
     */
    public static function get_store_slug( $vendor_id ) {
        $slug = get_user_meta( $vendor_id, '_bazaar_store_slug', true );

        if ( empty( $slug ) ) {
            $user = get_userdata( $vendor_id );
            $slug = $user ? sanitize_title( $user->user_login ) : '';
        }

        return $slug;
    }

    /**
     * Get store URL.
     *
     * @param int $vendor_id Vendor ID.
     * @return string
     */
    public static function get_store_url( $vendor_id ) {
        $slug = self::get_store_slug( $vendor_id );
        $store_slug = get_option( 'bazaar_vendor_store_slug', 'store' );

        return home_url( '/' . $store_slug . '/' . $slug . '/' );
    }

    /**
     * Get store address.
     *
     * @param int $vendor_id Vendor ID.
     * @return array
     */
    public static function get_store_address( $vendor_id ) {
        return array(
            'street_1' => get_user_meta( $vendor_id, '_bazaar_store_address_1', true ),
            'street_2' => get_user_meta( $vendor_id, '_bazaar_store_address_2', true ),
            'city'     => get_user_meta( $vendor_id, '_bazaar_store_city', true ),
            'state'    => get_user_meta( $vendor_id, '_bazaar_store_state', true ),
            'postcode' => get_user_meta( $vendor_id, '_bazaar_store_postcode', true ),
            'country'  => get_user_meta( $vendor_id, '_bazaar_store_country', true ),
        );
    }

    /**
     * Get social links.
     *
     * @param int $vendor_id Vendor ID.
     * @return array
     */
    public static function get_social_links( $vendor_id ) {
        return array(
            'facebook'  => get_user_meta( $vendor_id, '_bazaar_social_facebook', true ),
            'twitter'   => get_user_meta( $vendor_id, '_bazaar_social_twitter', true ),
            'instagram' => get_user_meta( $vendor_id, '_bazaar_social_instagram', true ),
            'youtube'   => get_user_meta( $vendor_id, '_bazaar_social_youtube', true ),
            'linkedin'  => get_user_meta( $vendor_id, '_bazaar_social_linkedin', true ),
            'pinterest' => get_user_meta( $vendor_id, '_bazaar_social_pinterest', true ),
        );
    }

    /**
     * Get vendor rating.
     *
     * @param int $vendor_id Vendor ID.
     * @return array
     */
    public static function get_vendor_rating( $vendor_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_vendor_reviews';

        $results = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT AVG(rating) as average, COUNT(*) as count FROM {$table_name} WHERE vendor_id = %d AND status = 'approved'",
                $vendor_id
            )
        );

        return array(
            'average' => $results ? round( floatval( $results->average ), 2 ) : 0,
            'count'   => $results ? intval( $results->count ) : 0,
        );
    }

    /**
     * Get vendor total sales.
     *
     * @param int $vendor_id Vendor ID.
     * @return float
     */
    public static function get_total_sales( $vendor_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_vendor_balance';

        $total = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT SUM(amount) FROM {$table_name} WHERE vendor_id = %d AND trn_type = 'credit' AND trn_status = 'completed'",
                $vendor_id
            )
        );

        return floatval( $total );
    }

    /**
     * Get vendor balance.
     *
     * @param int $vendor_id Vendor ID.
     * @return float
     */
    public static function get_balance( $vendor_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_vendor_balance';

        // Get total credits
        $credits = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT SUM(net_amount) FROM {$table_name} WHERE vendor_id = %d AND trn_type = 'credit' AND trn_status = 'completed'",
                $vendor_id
            )
        );

        // Get total debits (withdrawals)
        $debits = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT SUM(net_amount) FROM {$table_name} WHERE vendor_id = %d AND trn_type = 'debit' AND trn_status = 'completed'",
                $vendor_id
            )
        );

        return floatval( $credits ) - floatval( $debits );
    }

    /**
     * Get all vendors.
     *
     * @param array $args Query arguments.
     * @return array
     */
    public static function get_vendors( $args = array() ) {
        $defaults = array(
            'role'       => 'bazaar_vendor',
            'orderby'    => 'registered',
            'order'      => 'DESC',
            'number'     => 20,
            'paged'      => 1,
            'meta_query' => array(),
        );

        $args = wp_parse_args( $args, $defaults );

        // Filter by status
        if ( ! empty( $args['status'] ) ) {
            $args['meta_query'][] = array(
                'key'   => '_bazaar_vendor_status',
                'value' => $args['status'],
            );
        }

        // Search
        if ( ! empty( $args['search'] ) ) {
            $args['search'] = '*' . $args['search'] . '*';
            $args['search_columns'] = array( 'user_login', 'user_email', 'user_nicename', 'display_name' );
        }

        $query = new WP_User_Query( $args );

        return array(
            'vendors' => $query->get_results(),
            'total'   => $query->get_total(),
            'pages'   => ceil( $query->get_total() / $args['number'] ),
        );
    }

    /**
     * Get vendor by store slug.
     *
     * @param string $slug Store slug.
     * @return int|false
     */
    public static function get_vendor_id_by_slug( $slug ) {
        $vendors = get_users(
            array(
                'role'       => 'bazaar_vendor',
                'meta_key'   => '_bazaar_store_slug',
                'meta_value' => $slug,
                'number'     => 1,
            )
        );

        if ( ! empty( $vendors ) ) {
            return $vendors[0]->ID;
        }

        // Try matching by user_login
        $user = get_user_by( 'login', $slug );

        if ( $user && Bazaar_Roles::is_vendor( $user->ID ) ) {
            return $user->ID;
        }

        return false;
    }

    /**
     * Register vendor.
     *
     * @param array $data Registration data.
     * @return int|WP_Error
     */
    public static function register_vendor( $data ) {
        // Validate required fields
        $required = array( 'username', 'email', 'password', 'store_name' );

        foreach ( $required as $field ) {
            if ( empty( $data[ $field ] ) ) {
                return new WP_Error( 'missing_field', sprintf( __( '%s is required.', 'bazaar' ), $field ) );
            }
        }

        // Check if user already exists
        if ( username_exists( $data['username'] ) ) {
            return new WP_Error( 'username_exists', __( 'Username already exists.', 'bazaar' ) );
        }

        if ( email_exists( $data['email'] ) ) {
            return new WP_Error( 'email_exists', __( 'Email already exists.', 'bazaar' ) );
        }

        // Create user
        $user_id = wp_create_user( $data['username'], $data['password'], $data['email'] );

        if ( is_wp_error( $user_id ) ) {
            return $user_id;
        }

        // Update user data
        wp_update_user(
            array(
                'ID'           => $user_id,
                'display_name' => sanitize_text_field( $data['store_name'] ),
                'first_name'   => isset( $data['first_name'] ) ? sanitize_text_field( $data['first_name'] ) : '',
                'last_name'    => isset( $data['last_name'] ) ? sanitize_text_field( $data['last_name'] ) : '',
            )
        );

        // Determine initial status
        $approval = get_option( 'bazaar_vendor_approval', 'manual' );
        $status = 'manual' === $approval ? 'pending' : 'approved';

        // Make user a vendor
        Bazaar_Roles::make_vendor( $user_id, $status );

        // Save store information
        update_user_meta( $user_id, '_bazaar_store_name', sanitize_text_field( $data['store_name'] ) );
        update_user_meta( $user_id, '_bazaar_store_slug', sanitize_title( $data['store_name'] ) );

        if ( ! empty( $data['phone'] ) ) {
            update_user_meta( $user_id, '_bazaar_store_phone', sanitize_text_field( $data['phone'] ) );
        }

        if ( ! empty( $data['description'] ) ) {
            update_user_meta( $user_id, '_bazaar_store_description', wp_kses_post( $data['description'] ) );
        }

        // Address fields
        $address_fields = array( 'address_1', 'address_2', 'city', 'state', 'postcode', 'country' );
        foreach ( $address_fields as $field ) {
            if ( ! empty( $data[ $field ] ) ) {
                update_user_meta( $user_id, '_bazaar_store_' . $field, sanitize_text_field( $data[ $field ] ) );
            }
        }

        do_action( 'bazaar_vendor_registered', $user_id, $data );

        return $user_id;
    }

    /**
     * Update vendor profile.
     *
     * @param int   $vendor_id Vendor ID.
     * @param array $data      Profile data.
     * @return bool|WP_Error
     */
    public static function update_profile( $vendor_id, $data ) {
        if ( ! Bazaar_Roles::is_vendor( $vendor_id ) ) {
            return new WP_Error( 'not_vendor', __( 'User is not a vendor.', 'bazaar' ) );
        }

        // Store information
        $meta_fields = array(
            'store_name'        => '_bazaar_store_name',
            'store_slug'        => '_bazaar_store_slug',
            'store_description' => '_bazaar_store_description',
            'store_phone'       => '_bazaar_store_phone',
            'store_logo'        => '_bazaar_store_logo',
            'store_banner'      => '_bazaar_store_banner',
            'address_1'         => '_bazaar_store_address_1',
            'address_2'         => '_bazaar_store_address_2',
            'city'              => '_bazaar_store_city',
            'state'             => '_bazaar_store_state',
            'postcode'          => '_bazaar_store_postcode',
            'country'           => '_bazaar_store_country',
        );

        foreach ( $meta_fields as $key => $meta_key ) {
            if ( isset( $data[ $key ] ) ) {
                $value = 'store_description' === $key ? wp_kses_post( $data[ $key ] ) : sanitize_text_field( $data[ $key ] );
                update_user_meta( $vendor_id, $meta_key, $value );
            }
        }

        // Social links
        $social_fields = array( 'facebook', 'twitter', 'instagram', 'youtube', 'linkedin', 'pinterest' );
        foreach ( $social_fields as $social ) {
            if ( isset( $data[ 'social_' . $social ] ) ) {
                update_user_meta( $vendor_id, '_bazaar_social_' . $social, esc_url_raw( $data[ 'social_' . $social ] ) );
            }
        }

        // SEO fields
        if ( isset( $data['seo_title'] ) ) {
            update_user_meta( $vendor_id, '_bazaar_seo_title', sanitize_text_field( $data['seo_title'] ) );
        }
        if ( isset( $data['seo_description'] ) ) {
            update_user_meta( $vendor_id, '_bazaar_seo_description', sanitize_textarea_field( $data['seo_description'] ) );
        }

        do_action( 'bazaar_vendor_profile_updated', $vendor_id, $data );

        return true;
    }

    /**
     * Enable vacation mode.
     *
     * @param int    $vendor_id Vendor ID.
     * @param string $message   Vacation message.
     * @return bool
     */
    public static function enable_vacation_mode( $vendor_id, $message = '' ) {
        update_user_meta( $vendor_id, '_bazaar_vacation_mode', 'yes' );
        update_user_meta( $vendor_id, '_bazaar_vacation_message', sanitize_textarea_field( $message ) );
        update_user_meta( $vendor_id, '_bazaar_vacation_start', current_time( 'mysql' ) );

        do_action( 'bazaar_vendor_vacation_enabled', $vendor_id );

        return true;
    }

    /**
     * Disable vacation mode.
     *
     * @param int $vendor_id Vendor ID.
     * @return bool
     */
    public static function disable_vacation_mode( $vendor_id ) {
        update_user_meta( $vendor_id, '_bazaar_vacation_mode', 'no' );
        delete_user_meta( $vendor_id, '_bazaar_vacation_message' );
        delete_user_meta( $vendor_id, '_bazaar_vacation_start' );

        do_action( 'bazaar_vendor_vacation_disabled', $vendor_id );

        return true;
    }

    /**
     * Check if vendor is on vacation.
     *
     * @param int $vendor_id Vendor ID.
     * @return bool
     */
    public static function is_on_vacation( $vendor_id ) {
        return 'yes' === get_user_meta( $vendor_id, '_bazaar_vacation_mode', true );
    }

    /**
     * Get vendor product count.
     *
     * @param int    $vendor_id Vendor ID.
     * @param string $status    Product status.
     * @return int
     */
    public static function get_product_count( $vendor_id, $status = 'publish' ) {
        $args = array(
            'post_type'   => 'product',
            'post_status' => $status,
            'author'      => $vendor_id,
            'fields'      => 'ids',
        );

        $query = new WP_Query( $args );

        return $query->found_posts;
    }

    /**
     * Get vendor order count.
     *
     * @param int $vendor_id Vendor ID.
     * @return int
     */
    public static function get_order_count( $vendor_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bazaar_sub_orders';

        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE vendor_id = %d",
                $vendor_id
            )
        );

        return intval( $count );
    }

    /**
     * Maybe set vendor role on registration.
     *
     * @param int $user_id User ID.
     */
    public function maybe_set_vendor_role( $user_id ) {
        if ( isset( $_POST['bazaar_become_vendor'] ) && 'yes' === $_POST['bazaar_become_vendor'] ) {
            if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bazaar_vendor_nonce'] ?? '' ) ), 'bazaar_vendor_registration' ) ) {
                return;
            }

            $approval = get_option( 'bazaar_vendor_approval', 'manual' );
            $status = 'manual' === $approval ? 'pending' : 'approved';

            Bazaar_Roles::make_vendor( $user_id, $status );
        }
    }

    /**
     * Display vendor profile fields in admin.
     *
     * @param WP_User $user User object.
     */
    public function vendor_profile_fields( $user ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( ! Bazaar_Roles::is_vendor( $user->ID ) ) {
            return;
        }

        $vendor = self::get_vendor( $user->ID );
        ?>
        <h2><?php esc_html_e( 'Vendor Information', 'bazaar' ); ?></h2>
        <table class="form-table">
            <tr>
                <th><label for="bazaar_store_name"><?php esc_html_e( 'Store Name', 'bazaar' ); ?></label></th>
                <td>
                    <input type="text" name="bazaar_store_name" id="bazaar_store_name" value="<?php echo esc_attr( $vendor['store_name'] ); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="bazaar_vendor_status"><?php esc_html_e( 'Vendor Status', 'bazaar' ); ?></label></th>
                <td>
                    <select name="bazaar_vendor_status" id="bazaar_vendor_status">
                        <option value="pending" <?php selected( $vendor['status'], 'pending' ); ?>><?php esc_html_e( 'Pending', 'bazaar' ); ?></option>
                        <option value="approved" <?php selected( $vendor['status'], 'approved' ); ?>><?php esc_html_e( 'Approved', 'bazaar' ); ?></option>
                        <option value="rejected" <?php selected( $vendor['status'], 'rejected' ); ?>><?php esc_html_e( 'Rejected', 'bazaar' ); ?></option>
                        <option value="disabled" <?php selected( $vendor['status'], 'disabled' ); ?>><?php esc_html_e( 'Disabled', 'bazaar' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="bazaar_store_phone"><?php esc_html_e( 'Store Phone', 'bazaar' ); ?></label></th>
                <td>
                    <input type="text" name="bazaar_store_phone" id="bazaar_store_phone" value="<?php echo esc_attr( $vendor['phone'] ); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label><?php esc_html_e( 'Balance', 'bazaar' ); ?></label></th>
                <td>
                    <strong><?php echo wc_price( $vendor['balance'] ); ?></strong>
                </td>
            </tr>
            <tr>
                <th><label><?php esc_html_e( 'Total Sales', 'bazaar' ); ?></label></th>
                <td>
                    <strong><?php echo wc_price( $vendor['total_sales'] ); ?></strong>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Save vendor profile fields.
     *
     * @param int $user_id User ID.
     */
    public function save_vendor_profile_fields( $user_id ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( ! Bazaar_Roles::is_vendor( $user_id ) ) {
            return;
        }

        if ( isset( $_POST['bazaar_store_name'] ) ) {
            update_user_meta( $user_id, '_bazaar_store_name', sanitize_text_field( wp_unslash( $_POST['bazaar_store_name'] ) ) );
        }

        if ( isset( $_POST['bazaar_vendor_status'] ) ) {
            Bazaar_Roles::update_vendor_status( $user_id, sanitize_text_field( wp_unslash( $_POST['bazaar_vendor_status'] ) ) );
        }

        if ( isset( $_POST['bazaar_store_phone'] ) ) {
            update_user_meta( $user_id, '_bazaar_store_phone', sanitize_text_field( wp_unslash( $_POST['bazaar_store_phone'] ) ) );
        }
    }
}
