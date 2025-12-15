<?php

class WooCommerce_Functions {
    public function __construct() {
        add_action("init", [$this, "init_function"]);
        add_action('wp_ajax_update_mini_cart', [$this, 'update_mini_cart']);
        add_action('wp_ajax_nopriv_update_mini_cart', [$this, 'update_mini_cart']);
        add_filter('woocommerce_add_to_cart_fragments', [$this, 'shoping_mini_cart_fragment']);
        add_filter( 'comment_form_defaults', [$this, 'brainforward_change_woocommerce_review_submit_button'] );

        add_filter( 'woocommerce_checkout_fields', [ $this, 'brainforward_checkout_fields' ] );
        add_filter( 'woocommerce_get_terms_and_conditions_checkbox_text', [$this, 'custom_terms_and_conditions_text'] );
        add_filter( 'woocommerce_order_button_text', [ $this, 'custom_place_order_button_text' ] );
        add_filter( 'woocommerce_product_add_to_cart_text', [ $this, 'archive_add_to_cart_text' ], 10, 2 );
        add_filter( 'woocommerce_product_single_add_to_cart_text', [ $this, 'single_add_to_cart_text' ], 10, 2 );
        add_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'loop_add_to_cart_link' ], 10, 2 );
    
    }
	
	
    /**
     * Shop / Archive Add to Cart text (based on type)
     */
    public function archive_add_to_cart_text( $text, $product ) {
        if ( $product->is_type( 'simple' ) ) {
            return __( 'Buy Now', 'brainforward' );
        }
        if ( $product->is_type( 'variable' ) ) {
            return __( 'Choose Options', 'brainforward' );
        }
        if ( $product->is_type( 'external' ) ) {
            return __( 'View Product', 'brainforward' );
        }
        if ( $product->is_type( 'grouped' ) ) {
            return __( 'View Group', 'brainforward' );
        }
        return $text; // fallback
    }

    /**
     * Single Product Add to Cart text (based on type)
     */
    public function single_add_to_cart_text( $text, $product ) {
        if ( $product->is_type( 'simple' ) ) {
            return __( 'Purchase Now', 'brainforward' );
        }
        if ( $product->is_type( 'variable' ) ) {
            return __( 'Select Your Option', 'brainforward' );
        }
        if ( $product->is_type( 'external' ) ) {
            return __( 'Go to Shop', 'brainforward' );
        }
        if ( $product->is_type( 'grouped' ) ) {
            return __( 'View Grouped Product', 'brainforward' );
        }
        return $text; // fallback
    }

    /**
     * Loop Add to Cart HTML (replace text inside button)
     */
    public function loop_add_to_cart_link( $html, $product ) {
        // Example: change only "Add to cart" inside loop buttons
        return str_replace( 'Add to cart', __( 'Shop Now', 'brainforward' ), $html );
    }



    public function custom_place_order_button_text( $button_text ) {
        return __( 'Confirm Order', 'brainforward' ); // Change 'Confirm Order' to your desired text
    }

    public function custom_terms_and_conditions_text( $text ) {
        $terms_url  = esc_url( get_permalink( wc_get_page_id( 'terms' ) ) );
        $refund_url = esc_url( home_url( '/refund-policy' ) );

        $terms_link  = '<a href="' . $terms_url . '" class="woocommerce-terms-and-conditions-link" target="_blank">' . __( 'Terms and Conditions', 'brainforward' ) . '</a>';
        $refund_link = '<a href="' . $refund_url . '" target="_blank">' . __( 'Refund Policy', 'brainforward' ) . '</a>';

        $text = sprintf(
            __( 'I have read and agree to the website %1$s and %2$s.', 'brainforward' ),
            $terms_link,
            $refund_link
        );

        return $text;
    }

	// Public function to customize checkout fields
	public function brainforward_checkout_fields( $fields ) {
		$fields['billing'] = [];

		// Get current user
		$current_user = wp_get_current_user();
		$full_name    = '';
		if ( $current_user && $current_user->ID ) {
			$full_name = trim( $current_user->first_name . ' ' . $current_user->last_name );
		}

		// Full Name field (with default value if logged in)
		$fields['billing']['billing_full_name'] = [
			'label'       => __('Full Name', 'brainforward'),
			'required'    => true,
			'class'       => ['form-row-wide'],
			'clear'       => true,
			'default'     => $full_name, // Pre-fill value
		];

		// Email field
		$fields['billing']['billing_email'] = [
			'label'       => __('Email Address', 'brainforward'),
			'required'    => true,
			'class'       => ['form-row-wide'],
			'clear'       => true,
			'default'     => ( $current_user && $current_user->ID ) ? $current_user->user_email : '',
		];

		// Phone field (optional – add if needed)
		$fields['billing']['billing_phone'] = [
			'label'       => __('Phone Number', 'brainforward'),
			'required'    => true,
			'class'       => ['form-row-wide'],
			'clear'       => true,
			'default'     => ( $current_user && $current_user->ID ) ? get_user_meta( $current_user->ID, 'billing_phone', true ) : '',
		];

		// Remove shipping fields
		$fields['shipping'] = [];

		// Remove order notes
		if ( isset( $fields['order']['order_comments'] ) ) {
			unset( $fields['order']['order_comments'] );
		}

		return $fields;
	}


    public function brainforward_change_woocommerce_review_submit_button( $defaults ) {
        // Only change on WooCommerce product pages
        if ( ! is_product() ) {
            return $defaults;
        }

        $defaults['submit_button'] = '<button name="%1$s" type="submit" id="%2$s" class="%3$s"><span>%4$s</span><i class="ri-send-plane-line"></i></button>';

        return $defaults;
    }


    public function init_function() {

        $options['wishlist'] = get_theme_mod('woocommerce_wishlist_button', 'show');
        $options['badge'] = get_theme_mod('woocommerce_sale_badge', 'show');
        $options['card'] = 'one';


        if( class_exists('Brainforward\Frontend\Wishlist\Wishlist') ){
            $this->setup_wishlist($options);
        }

        $this->modify_woocommerce_hooks($options);
    }

    private function setup_wishlist($options) {
        if (class_exists('Brainforward')) {
            $wishlist = new Brainforward\Frontend\Wishlist\Wishlist();
            if ($options["wishlist"] != 'hide') {
                $actions_position = $this->determine_actions_position($options);
                if ($actions_position) {
                    add_action("woocommerce_before_shop_loop_item_title", [$wishlist, "add_wishlist_button"], 20);
                } else {
                    add_action("woocommerce_after_shop_loop_item", [$wishlist, "add_wishlist_button"], 5);
                }
            }

            add_action("woocommerce_after_add_to_cart_button", [$wishlist, "add_wishlist_button"], 9999);
        }
    }

    private function determine_actions_position($options) {
        return !in_array($options['card'], ["six", "seven", "eight", "nine"]);
    }

    private function modify_woocommerce_hooks($options) {
        // Remove default actions
        remove_action("woocommerce_before_main_content", "woocommerce_breadcrumb", 20);
        remove_action("woocommerce_before_shop_loop_item", "woocommerce_template_loop_product_link_open", 10);
        remove_action("woocommerce_after_shop_loop_item", "woocommerce_template_loop_product_link_close", 5);
        remove_action("woocommerce_before_shop_loop_item_title", "woocommerce_show_product_loop_sale_flash", 10);
        remove_action("woocommerce_shop_loop_item_title", "woocommerce_template_loop_product_title", 10);
        remove_action("woocommerce_before_shop_loop_item_title", "woocommerce_template_loop_product_thumbnail", 10);
        remove_action("woocommerce_before_shop_loop", "woocommerce_output_all_notices", 10);
        remove_action("woocommerce_before_shop_loop", "woocommerce_result_count", 20);
        remove_action("woocommerce_before_shop_loop", "woocommerce_catalog_ordering", 30);
        remove_action("woocommerce_after_shop_loop_item_title", "woocommerce_template_loop_rating", 5);

        // Add custom actions
        add_action("woocommerce_shop_loop_item_title", [$this, "woocommerce_shop_loop_item_title"], 10);
        add_action("woocommerce_before_shop_loop_item_title", [$this, "template_loop_product_thumbnail"], 10);

        if ($options["badge"] != 'hide') {
            add_action("woocommerce_before_shop_loop_item", "woocommerce_show_product_loop_sale_flash", 5);
        }

        $this->add_product_actions_before();
    }


    private function add_product_actions_before() {
        remove_action(
            "woocommerce_after_shop_loop_item",
            "woocommerce_template_loop_add_to_cart",
            10
        );
        add_action(
            "woocommerce_before_shop_loop_item_title",
            "woocommerce_template_loop_add_to_cart",
            20
        );

        add_action(
            "woocommerce_before_shop_loop_item_title",
            function () {
                echo '<div class="product_actions">';
            },
            15
        );
        add_action(
            "woocommerce_before_shop_loop_item_title",
            function () {
                echo "</div>";
            },
            9999
        );
    }
    
    public function woocommerce_shop_loop_item_title() {
        echo '<h4 class="product__title"><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . "</a></h4>";
    }
    
    public function template_loop_product_thumbnail() {
        if (has_post_thumbnail()) {
            echo '<a href="' . esc_url(get_permalink()) . '">';
            the_post_thumbnail('large');
            echo '</a>';
        }
    }


    public function update_mini_cart() {
        // Verify nonce for security
        check_ajax_referer('brainforward_mini_cart_nonce', 'nonce');

        wc_get_template_part('cart/mini-cart');
        wp_die();
    }

    public static function mini_cart() {
        ?>
        <div class="shopping_cart_content">
            <?php woocommerce_mini_cart(); ?>
        </div>
        <?php
    }

    public function shoping_mini_cart_fragment($fragments) {
        $fragments['.cart_toggle .cart_count'] = '<span class="cart_count">' . esc_html(WC()->cart->get_cart_contents_count()) . '</span>';
        ob_start();
        self::mini_cart();
        $fragments['.shopping_cart_content'] = ob_get_clean();
        return $fragments;
    }
}

// Instantiate the class
new WooCommerce_Functions();
