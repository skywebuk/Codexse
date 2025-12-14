<?php
/**
 * Vendor Store Template.
 *
 * @package Bazaar\Templates
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: bazaar_before_store.
 */
do_action( 'bazaar_before_store', $vendor );
?>

<div id="bazaar-store" class="bazaar-store-page">
    <?php
    /**
     * Hook: bazaar_store_header.
     *
     * @hooked bazaar_template_store_header - 10
     */
    do_action( 'bazaar_store_header', $vendor );
    ?>

    <div class="bazaar-store-content">
        <?php
        /**
         * Hook: bazaar_store_sidebar.
         *
         * @hooked bazaar_template_store_sidebar - 10
         */
        do_action( 'bazaar_store_sidebar', $vendor );
        ?>

        <div class="bazaar-store-main">
            <?php
            /**
             * Hook: bazaar_store_content.
             *
             * @hooked bazaar_template_store_tabs - 10
             * @hooked bazaar_template_store_products - 20
             */
            do_action( 'bazaar_store_content', $vendor );
            ?>
        </div>
    </div>
</div>

<?php
/**
 * Hook: bazaar_after_store.
 */
do_action( 'bazaar_after_store', $vendor );

get_footer( 'shop' );
