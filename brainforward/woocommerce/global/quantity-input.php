<?php
/**
 * Product quantity inputs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/quantity-input.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 *
 * @var bool   $readonly If the input should be set to readonly mode.
 * @var string $type     The input type attribute.
 */

defined( 'ABSPATH' ) || exit;

// Hide hidden inputs
if ( $type === 'hidden' ) {
    return;
}

/* translators: %s: Quantity. */
$label = ! empty( $args['product_name'] ) 
    ? sprintf( esc_html__( '%s quantity', 'brainforward' ), wp_strip_all_tags( $args['product_name'] ) ) 
    : esc_html__( 'Quantity', 'brainforward' );

?>

<div class="quantity">
    <?php do_action( 'woocommerce_before_quantity_input_field' ); ?>

    <label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>">
        <?php echo esc_html( $label ); ?>
    </label>

    <button type="button" class="minus"><i class="ri-subtract-line"></i></button>

    <input
        type="<?php echo esc_attr( $type ); ?>"
        id="<?php echo esc_attr( $input_id ); ?>"
        class="<?php echo esc_attr( join( ' ', (array) $classes ) ); ?>"
        name="<?php echo esc_attr( $input_name ); ?>"
        value="<?php echo esc_attr( $input_value ? $input_value : 0 ); ?>"
        aria-label="<?php esc_attr_e( 'Product quantity', 'brainforward' ); ?>"
        size="4"
        min="<?php echo esc_attr( $min_value ); ?>"
        max="<?php echo esc_attr( 0 < $max_value ? $max_value : '' ); ?>"
        <?php if ( $readonly ) : ?> readonly="readonly"<?php endif; ?>
        <?php if ( ! $readonly ) : ?>
            step="<?php echo esc_attr( $step ); ?>"
            placeholder="<?php echo esc_attr( $placeholder ); ?>"
            inputmode="<?php echo esc_attr( $inputmode ); ?>"
            autocomplete="<?php echo esc_attr( isset( $autocomplete ) ? $autocomplete : 'on' ); ?>"
        <?php endif; ?>
    />

    <button type="button" class="plus"><i class="ri-add-large-line"></i></button>

    <?php do_action( 'woocommerce_after_quantity_input_field' ); ?>
</div>