<?php
/**
 * Course Product Type Class.
 *
 * @package Edugo_LMS\Integrations\WooCommerce
 */

namespace Edugo_LMS\Integrations\WooCommerce;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Course_Product
 *
 * Custom WooCommerce product type for courses.
 *
 * @since 1.0.0
 */
class Course_Product extends \WC_Product {

    /**
     * Product type.
     *
     * @var string
     */
    protected $product_type = 'edugo_course';

    /**
     * Constructor.
     *
     * @since 1.0.0
     * @param int|WC_Product|object $product Product ID, product object, or post object.
     */
    public function __construct( $product = 0 ) {
        parent::__construct( $product );
    }

    /**
     * Get product type.
     *
     * @since 1.0.0
     * @return string Product type.
     */
    public function get_type(): string {
        return 'edugo_course';
    }

    /**
     * Check if product is virtual.
     *
     * @since 1.0.0
     * @param string $context Context.
     * @return bool Always true for courses.
     */
    public function get_virtual( $context = 'view' ): bool {
        return true;
    }

    /**
     * Check if product is sold individually.
     *
     * @since 1.0.0
     * @param string $context Context.
     * @return bool Always true for courses.
     */
    public function get_sold_individually( $context = 'view' ): bool {
        return true;
    }

    /**
     * Check if product is downloadable.
     *
     * @since 1.0.0
     * @param string $context Context.
     * @return bool Always false for courses.
     */
    public function get_downloadable( $context = 'view' ): bool {
        return false;
    }

    /**
     * Check if product is purchasable.
     *
     * @since 1.0.0
     * @return bool True if purchasable.
     */
    public function is_purchasable(): bool {
        $purchasable = $this->exists() && ( 'publish' === $this->get_status() || current_user_can( 'edit_post', $this->get_id() ) ) && '' !== $this->get_price();

        // Check if linked course exists.
        $course_id = get_post_meta( $this->get_id(), '_edugo_linked_course', true );
        if ( ! $course_id || ! get_post( $course_id ) ) {
            $purchasable = false;
        }

        // Check if user already enrolled.
        if ( is_user_logged_in() && $course_id ) {
            $enrollment_manager = new \Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
            if ( $enrollment_manager->is_enrolled( get_current_user_id(), (int) $course_id ) ) {
                $purchasable = false;
            }
        }

        return apply_filters( 'woocommerce_is_purchasable', $purchasable, $this );
    }

    /**
     * Get linked course ID.
     *
     * @since 1.0.0
     * @return int|null Course ID or null.
     */
    public function get_linked_course_id(): ?int {
        $course_id = get_post_meta( $this->get_id(), '_edugo_linked_course', true );
        return $course_id ? (int) $course_id : null;
    }

    /**
     * Get linked course.
     *
     * @since 1.0.0
     * @return WP_Post|null Course post or null.
     */
    public function get_linked_course(): ?\WP_Post {
        $course_id = $this->get_linked_course_id();

        if ( ! $course_id ) {
            return null;
        }

        return get_post( $course_id );
    }

    /**
     * Check if lifetime access.
     *
     * @since 1.0.0
     * @return bool True if lifetime access.
     */
    public function is_lifetime_access(): bool {
        return get_post_meta( $this->get_id(), '_edugo_lifetime_access', true ) === 'yes';
    }

    /**
     * Get access duration in days.
     *
     * @since 1.0.0
     * @return int Access duration in days (0 for lifetime).
     */
    public function get_access_duration(): int {
        if ( $this->is_lifetime_access() ) {
            return 0;
        }

        return (int) get_post_meta( $this->get_id(), '_edugo_access_days', true );
    }

    /**
     * Get add to cart button text.
     *
     * @since 1.0.0
     * @return string Button text.
     */
    public function add_to_cart_text(): string {
        if ( $this->is_purchasable() && $this->is_in_stock() ) {
            return __( 'Enroll Now', 'edugo-lms' );
        }

        return __( 'Read More', 'edugo-lms' );
    }

    /**
     * Get single add to cart button text.
     *
     * @since 1.0.0
     * @return string Button text.
     */
    public function single_add_to_cart_text(): string {
        return __( 'Enroll Now', 'edugo-lms' );
    }

    /**
     * Returns false if the product cannot be bought.
     *
     * @since 1.0.0
     * @return bool True if can be bought.
     */
    public function is_in_stock(): bool {
        return true;
    }

    /**
     * Get stock status.
     *
     * @since 1.0.0
     * @param string $context Context.
     * @return string Stock status.
     */
    public function get_stock_status( $context = 'view' ): string {
        return 'instock';
    }

    /**
     * Check if needs shipping.
     *
     * @since 1.0.0
     * @return bool Always false for courses.
     */
    public function needs_shipping(): bool {
        return false;
    }
}
