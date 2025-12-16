<?php
/**
 * The template for the sidebar containing the main widget area
 *
 * @package Brainforward
 */
if ( is_active_sidebar( 'woocommerce_sidebar' )  ) : 
?>
<aside class="sidebar main-sidebar">
   <?php dynamic_sidebar( 'woocommerce_sidebar' ); ?>
</aside>
<?php endif; ?>