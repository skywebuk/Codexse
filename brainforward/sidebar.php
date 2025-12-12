<?php
/**
 * The template for the sidebar containing the main widget area
 *
 * @package Brainforward
 */
if ( is_active_sidebar( 'main_sidebar' )  ) : 
?>
<aside class="sidebar main-sidebar">
   <?php dynamic_sidebar( 'main_sidebar' ); ?>
</aside>
<?php endif; ?>