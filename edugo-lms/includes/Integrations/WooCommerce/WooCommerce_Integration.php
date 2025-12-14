<?php
/**
 * WooCommerce Integration Class.
 *
 * @package Edugo_LMS\Integrations\WooCommerce
 */

namespace Edugo_LMS\Integrations\WooCommerce;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class WooCommerce_Integration
 *
 * Handles WooCommerce integration for selling courses.
 *
 * @since 1.0.0
 */
class WooCommerce_Integration {

    /**
     * Initialize WooCommerce integration.
     *
     * @since 1.0.0
     * @return void
     */
    public function init(): void {
        // Product type.
        add_filter( 'product_type_selector', array( $this, 'add_course_product_type' ) );
        add_filter( 'woocommerce_product_class', array( $this, 'get_course_product_class' ), 10, 2 );

        // Product data tabs.
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_course_product_tab' ) );
        add_action( 'woocommerce_product_data_panels', array( $this, 'render_course_product_panel' ) );
        add_action( 'woocommerce_process_product_meta', array( $this, 'save_course_product_data' ) );

        // Cart and checkout.
        add_filter( 'woocommerce_is_purchasable', array( $this, 'is_course_purchasable' ), 10, 2 );
        add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_course_cart_data' ), 10, 3 );
        add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_course_order_meta' ), 10, 4 );

        // Order processing.
        add_action( 'woocommerce_order_status_completed', array( $this, 'process_course_purchase' ) );
        add_action( 'woocommerce_order_status_processing', array( $this, 'process_course_purchase' ) );

        // Refunds.
        add_action( 'woocommerce_order_status_refunded', array( $this, 'process_course_refund' ) );
        add_action( 'woocommerce_order_status_cancelled', array( $this, 'process_course_refund' ) );

        // My Account.
        add_filter( 'woocommerce_account_menu_items', array( $this, 'add_courses_menu_item' ) );
        add_action( 'woocommerce_account_edugo-courses_endpoint', array( $this, 'render_courses_endpoint' ) );
        add_action( 'init', array( $this, 'add_courses_endpoint' ) );

        // Course purchase button.
        add_action( 'edugo_course_purchase_button', array( $this, 'render_purchase_button' ) );
    }

    /**
     * Add course product type.
     *
     * @since 1.0.0
     * @param array $types Product types.
     * @return array Modified product types.
     */
    public function add_course_product_type( array $types ): array {
        $types['edugo_course'] = __( 'Edugo Course', 'edugo-lms' );
        return $types;
    }

    /**
     * Get course product class.
     *
     * @since 1.0.0
     * @param string $classname Product class name.
     * @param string $product_type Product type.
     * @return string Modified class name.
     */
    public function get_course_product_class( string $classname, string $product_type ): string {
        if ( $product_type === 'edugo_course' ) {
            return Course_Product::class;
        }
        return $classname;
    }

    /**
     * Add course product data tab.
     *
     * @since 1.0.0
     * @param array $tabs Product data tabs.
     * @return array Modified tabs.
     */
    public function add_course_product_tab( array $tabs ): array {
        $tabs['edugo_course'] = array(
            'label'    => __( 'Course', 'edugo-lms' ),
            'target'   => 'edugo_course_product_data',
            'class'    => array( 'show_if_edugo_course' ),
            'priority' => 21,
        );

        return $tabs;
    }

    /**
     * Render course product data panel.
     *
     * @since 1.0.0
     * @return void
     */
    public function render_course_product_panel(): void {
        global $post;

        $course_id = get_post_meta( $post->ID, '_edugo_linked_course', true );

        ?>
        <div id="edugo_course_product_data" class="panel woocommerce_options_panel">
            <div class="options_group">
                <p class="form-field">
                    <label for="_edugo_linked_course"><?php esc_html_e( 'Linked Course', 'edugo-lms' ); ?></label>
                    <select id="_edugo_linked_course" name="_edugo_linked_course" class="wc-enhanced-select" style="width: 50%;">
                        <option value=""><?php esc_html_e( 'Select a course', 'edugo-lms' ); ?></option>
                        <?php
                        $courses = get_posts( array(
                            'post_type'      => 'edugo_course',
                            'posts_per_page' => -1,
                            'post_status'    => 'publish',
                            'orderby'        => 'title',
                            'order'          => 'ASC',
                        ) );

                        foreach ( $courses as $course ) {
                            printf(
                                '<option value="%d" %s>%s</option>',
                                esc_attr( $course->ID ),
                                selected( $course_id, $course->ID, false ),
                                esc_html( $course->post_title )
                            );
                        }
                        ?>
                    </select>
                </p>

                <?php
                woocommerce_wp_checkbox( array(
                    'id'          => '_edugo_lifetime_access',
                    'label'       => __( 'Lifetime Access', 'edugo-lms' ),
                    'description' => __( 'Grant lifetime access to this course.', 'edugo-lms' ),
                ) );

                woocommerce_wp_text_input( array(
                    'id'          => '_edugo_access_days',
                    'label'       => __( 'Access Duration (Days)', 'edugo-lms' ),
                    'description' => __( 'Number of days the student has access. Leave empty for lifetime.', 'edugo-lms' ),
                    'type'        => 'number',
                    'custom_attributes' => array(
                        'min'  => '1',
                        'step' => '1',
                    ),
                ) );
                ?>
            </div>
        </div>
        <?php
    }

    /**
     * Save course product data.
     *
     * @since 1.0.0
     * @param int $post_id Product post ID.
     * @return void
     */
    public function save_course_product_data( int $post_id ): void {
        $course_id = isset( $_POST['_edugo_linked_course'] ) ? absint( $_POST['_edugo_linked_course'] ) : 0;
        $lifetime = isset( $_POST['_edugo_lifetime_access'] ) ? 'yes' : 'no';
        $access_days = isset( $_POST['_edugo_access_days'] ) ? absint( $_POST['_edugo_access_days'] ) : 0;

        update_post_meta( $post_id, '_edugo_linked_course', $course_id );
        update_post_meta( $post_id, '_edugo_lifetime_access', $lifetime );
        update_post_meta( $post_id, '_edugo_access_days', $access_days );

        // Link course to product.
        if ( $course_id ) {
            update_post_meta( $course_id, '_edugo_wc_product_id', $post_id );
        }
    }

    /**
     * Check if course product is purchasable.
     *
     * @since 1.0.0
     * @param bool       $purchasable Is purchasable.
     * @param WC_Product $product     The product.
     * @return bool Modified purchasable status.
     */
    public function is_course_purchasable( bool $purchasable, $product ): bool {
        if ( $product->get_type() !== 'edugo_course' ) {
            return $purchasable;
        }

        $course_id = get_post_meta( $product->get_id(), '_edugo_linked_course', true );

        if ( ! $course_id ) {
            return false;
        }

        // Check if already enrolled.
        if ( is_user_logged_in() ) {
            $enrollment_manager = new \Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
            if ( $enrollment_manager->is_enrolled( get_current_user_id(), $course_id ) ) {
                return false;
            }
        }

        return $purchasable;
    }

    /**
     * Add course data to cart.
     *
     * @since 1.0.0
     * @param array $cart_item_data Cart item data.
     * @param int   $product_id     Product ID.
     * @param int   $variation_id   Variation ID.
     * @return array Modified cart item data.
     */
    public function add_course_cart_data( array $cart_item_data, int $product_id, int $variation_id ): array {
        $product = wc_get_product( $product_id );

        if ( $product && $product->get_type() === 'edugo_course' ) {
            $cart_item_data['edugo_course_id'] = get_post_meta( $product_id, '_edugo_linked_course', true );
        }

        return $cart_item_data;
    }

    /**
     * Add course meta to order item.
     *
     * @since 1.0.0
     * @param WC_Order_Item_Product $item          Order item.
     * @param string                $cart_item_key Cart item key.
     * @param array                 $values        Cart item values.
     * @param WC_Order              $order         Order.
     * @return void
     */
    public function add_course_order_meta( $item, $cart_item_key, $values, $order ): void {
        if ( isset( $values['edugo_course_id'] ) ) {
            $item->add_meta_data( '_edugo_course_id', $values['edugo_course_id'], true );
        }
    }

    /**
     * Process course purchase on order completion.
     *
     * @since 1.0.0
     * @param int $order_id The order ID.
     * @return void
     */
    public function process_course_purchase( int $order_id ): void {
        $order = wc_get_order( $order_id );

        if ( ! $order ) {
            return;
        }

        // Check if already processed.
        if ( $order->get_meta( '_edugo_courses_enrolled' ) === 'yes' ) {
            return;
        }

        $user_id = $order->get_user_id();

        if ( ! $user_id ) {
            return;
        }

        $enrollment_manager = new \Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
        $courses_enrolled = array();

        foreach ( $order->get_items() as $item ) {
            $course_id = $item->get_meta( '_edugo_course_id' );

            if ( $course_id ) {
                $enrollment_id = $enrollment_manager->enroll_student( $user_id, (int) $course_id, $order_id );

                if ( $enrollment_id ) {
                    $courses_enrolled[] = $course_id;

                    // Calculate and record instructor earnings.
                    $this->record_instructor_earnings( $order, $item, (int) $course_id );
                }
            }
        }

        if ( ! empty( $courses_enrolled ) ) {
            $order->update_meta_data( '_edugo_courses_enrolled', 'yes' );
            $order->update_meta_data( '_edugo_enrolled_course_ids', $courses_enrolled );
            $order->save();

            /**
             * Fires after courses are enrolled from an order.
             *
             * @since 1.0.0
             * @param int   $order_id         The order ID.
             * @param int   $user_id          The user ID.
             * @param array $courses_enrolled Array of enrolled course IDs.
             */
            do_action( 'edugo_courses_enrolled_from_order', $order_id, $user_id, $courses_enrolled );
        }
    }

    /**
     * Process course refund.
     *
     * @since 1.0.0
     * @param int $order_id The order ID.
     * @return void
     */
    public function process_course_refund( int $order_id ): void {
        $order = wc_get_order( $order_id );

        if ( ! $order ) {
            return;
        }

        $user_id = $order->get_user_id();

        if ( ! $user_id ) {
            return;
        }

        $enrollment_manager = new \Edugo_LMS\LMS\Enrollment\Enrollment_Manager();

        foreach ( $order->get_items() as $item ) {
            $course_id = $item->get_meta( '_edugo_course_id' );

            if ( $course_id ) {
                $enrollment_manager->unenroll_student( $user_id, (int) $course_id );
            }
        }

        $order->update_meta_data( '_edugo_courses_enrolled', 'no' );
        $order->save();
    }

    /**
     * Record instructor earnings.
     *
     * @since 1.0.0
     * @param WC_Order              $order     The order.
     * @param WC_Order_Item_Product $item      The order item.
     * @param int                   $course_id The course ID.
     * @return void
     */
    private function record_instructor_earnings( $order, $item, int $course_id ): void {
        global $wpdb;

        $course = get_post( $course_id );

        if ( ! $course ) {
            return;
        }

        $instructor_id = $course->post_author;
        $item_total = $item->get_total();
        $commission_rate = (float) get_option( 'edugo_instructor_commission', 70 );

        // Check for custom instructor rate.
        $custom_rate = get_user_meta( $instructor_id, '_edugo_commission_rate', true );
        if ( $custom_rate ) {
            $commission_rate = (float) $custom_rate;
        }

        $commission_amount = ( $item_total * $commission_rate ) / 100;
        $admin_amount = $item_total - $commission_amount;

        $wpdb->insert(
            $wpdb->prefix . 'edugo_earnings',
            array(
                'instructor_id'     => $instructor_id,
                'course_id'         => $course_id,
                'order_id'          => $order->get_id(),
                'order_total'       => $item_total,
                'commission_rate'   => $commission_rate,
                'commission_amount' => $commission_amount,
                'admin_amount'      => $admin_amount,
                'status'            => 'pending',
                'created_at'        => current_time( 'mysql' ),
            ),
            array( '%d', '%d', '%d', '%f', '%f', '%f', '%f', '%s', '%s' )
        );
    }

    /**
     * Add courses menu item to My Account.
     *
     * @since 1.0.0
     * @param array $items Menu items.
     * @return array Modified menu items.
     */
    public function add_courses_menu_item( array $items ): array {
        $new_items = array();

        foreach ( $items as $key => $value ) {
            $new_items[ $key ] = $value;

            if ( $key === 'orders' ) {
                $new_items['edugo-courses'] = __( 'My Courses', 'edugo-lms' );
            }
        }

        return $new_items;
    }

    /**
     * Add courses endpoint.
     *
     * @since 1.0.0
     * @return void
     */
    public function add_courses_endpoint(): void {
        add_rewrite_endpoint( 'edugo-courses', EP_ROOT | EP_PAGES );
    }

    /**
     * Render courses endpoint content.
     *
     * @since 1.0.0
     * @return void
     */
    public function render_courses_endpoint(): void {
        $user_id = get_current_user_id();
        $enrollment_manager = new \Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
        $enrollments = $enrollment_manager->get_user_enrollments( $user_id );

        include EDUGO_LMS_PATH . 'templates/woocommerce/my-courses.php';
    }

    /**
     * Render course purchase button.
     *
     * @since 1.0.0
     * @param int $course_id The course ID.
     * @return void
     */
    public function render_purchase_button( int $course_id ): void {
        $product_id = get_post_meta( $course_id, '_edugo_wc_product_id', true );

        if ( ! $product_id ) {
            return;
        }

        $product = wc_get_product( $product_id );

        if ( ! $product ) {
            return;
        }

        // Check if already enrolled.
        if ( is_user_logged_in() ) {
            $enrollment_manager = new \Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
            if ( $enrollment_manager->is_enrolled( get_current_user_id(), $course_id ) ) {
                echo '<a href="' . esc_url( get_permalink( $course_id ) ) . '" class="edugo-button edugo-start-course">' . esc_html__( 'Start Course', 'edugo-lms' ) . '</a>';
                return;
            }
        }

        echo '<a href="' . esc_url( $product->add_to_cart_url() ) . '" class="edugo-button edugo-buy-course">' . esc_html( $product->add_to_cart_text() ) . ' - ' . wp_kses_post( $product->get_price_html() ) . '</a>';
    }

    /**
     * Get course product.
     *
     * @since 1.0.0
     * @param int $course_id The course ID.
     * @return WC_Product|null Product or null.
     */
    public function get_course_product( int $course_id ) {
        $product_id = get_post_meta( $course_id, '_edugo_wc_product_id', true );

        if ( ! $product_id ) {
            return null;
        }

        return wc_get_product( $product_id );
    }

    /**
     * Check if course is free.
     *
     * @since 1.0.0
     * @param int $course_id The course ID.
     * @return bool True if free, false otherwise.
     */
    public function is_course_free( int $course_id ): bool {
        $is_free = get_post_meta( $course_id, '_edugo_is_free', true );

        if ( $is_free === 'yes' ) {
            return true;
        }

        $product = $this->get_course_product( $course_id );

        if ( ! $product ) {
            return true; // No product means free.
        }

        return $product->get_price() <= 0;
    }
}
