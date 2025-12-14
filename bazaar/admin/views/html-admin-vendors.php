<?php
/**
 * Admin Vendors List View.
 *
 * @package Bazaar\Admin\Views
 */

defined( 'ABSPATH' ) || exit;

// Handle messages
if ( isset( $_GET['message'] ) ) {
    $messages = array(
        'approved' => __( 'Vendor approved successfully.', 'bazaar' ),
        'rejected' => __( 'Vendor rejected.', 'bazaar' ),
        'disabled' => __( 'Vendor disabled.', 'bazaar' ),
        'enabled'  => __( 'Vendor enabled.', 'bazaar' ),
    );

    if ( isset( $messages[ $_GET['message'] ] ) ) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $messages[ $_GET['message'] ] ) . '</p></div>';
    }
}
?>
<div class="wrap bazaar-admin-wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Vendors', 'bazaar' ); ?></h1>

    <hr class="wp-header-end">

    <div class="bazaar-filters">
        <ul class="subsubsub">
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors' ) ); ?>" class="<?php echo empty( $status ) ? 'current' : ''; ?>">
                    <?php esc_html_e( 'All', 'bazaar' ); ?>
                </a> |
            </li>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors&status=pending' ) ); ?>" class="<?php echo 'pending' === $status ? 'current' : ''; ?>">
                    <?php esc_html_e( 'Pending', 'bazaar' ); ?>
                </a> |
            </li>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors&status=approved' ) ); ?>" class="<?php echo 'approved' === $status ? 'current' : ''; ?>">
                    <?php esc_html_e( 'Approved', 'bazaar' ); ?>
                </a> |
            </li>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors&status=rejected' ) ); ?>" class="<?php echo 'rejected' === $status ? 'current' : ''; ?>">
                    <?php esc_html_e( 'Rejected', 'bazaar' ); ?>
                </a> |
            </li>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors&status=disabled' ) ); ?>" class="<?php echo 'disabled' === $status ? 'current' : ''; ?>">
                    <?php esc_html_e( 'Disabled', 'bazaar' ); ?>
                </a>
            </li>
        </ul>

        <form method="get" class="search-box">
            <input type="hidden" name="page" value="bazaar-vendors" />
            <?php if ( $status ) : ?>
                <input type="hidden" name="status" value="<?php echo esc_attr( $status ); ?>" />
            <?php endif; ?>
            <input type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Search vendors...', 'bazaar' ); ?>" />
            <input type="submit" class="button" value="<?php esc_attr_e( 'Search', 'bazaar' ); ?>" />
        </form>
    </div>

    <table class="wp-list-table widefat fixed striped bazaar-vendors-table">
        <thead>
            <tr>
                <th class="column-avatar"><?php esc_html_e( 'Avatar', 'bazaar' ); ?></th>
                <th class="column-store"><?php esc_html_e( 'Store', 'bazaar' ); ?></th>
                <th class="column-email"><?php esc_html_e( 'Email', 'bazaar' ); ?></th>
                <th class="column-products"><?php esc_html_e( 'Products', 'bazaar' ); ?></th>
                <th class="column-balance"><?php esc_html_e( 'Balance', 'bazaar' ); ?></th>
                <th class="column-status"><?php esc_html_e( 'Status', 'bazaar' ); ?></th>
                <th class="column-registered"><?php esc_html_e( 'Registered', 'bazaar' ); ?></th>
                <th class="column-actions"><?php esc_html_e( 'Actions', 'bazaar' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ( empty( $vendors['vendors'] ) ) : ?>
                <tr>
                    <td colspan="8"><?php esc_html_e( 'No vendors found.', 'bazaar' ); ?></td>
                </tr>
            <?php else : ?>
                <?php foreach ( $vendors['vendors'] as $user ) : ?>
                    <?php
                    $vendor = Bazaar_Vendor::get_vendor( $user->ID );
                    $actions = Bazaar_Admin_Vendors::get_action_links( $user->ID );
                    ?>
                    <tr>
                        <td class="column-avatar">
                            <?php echo get_avatar( $user->ID, 40 ); ?>
                        </td>
                        <td class="column-store">
                            <strong>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors&action=view&vendor=' . $user->ID ) ); ?>">
                                    <?php echo esc_html( $vendor['store_name'] ); ?>
                                </a>
                            </strong>
                            <br>
                            <small><?php echo esc_html( $user->display_name ); ?></small>
                        </td>
                        <td class="column-email">
                            <a href="mailto:<?php echo esc_attr( $user->user_email ); ?>">
                                <?php echo esc_html( $user->user_email ); ?>
                            </a>
                        </td>
                        <td class="column-products">
                            <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=product&author=' . $user->ID ) ); ?>">
                                <?php echo esc_html( Bazaar_Vendor::get_product_count( $user->ID ) ); ?>
                            </a>
                        </td>
                        <td class="column-balance">
                            <?php echo wc_price( $vendor['balance'] ); ?>
                        </td>
                        <td class="column-status">
                            <?php echo Bazaar_Admin_Vendors::get_status_badge( $vendor['status'] ); ?>
                        </td>
                        <td class="column-registered">
                            <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $vendor['registered'] ) ) ); ?>
                        </td>
                        <td class="column-actions">
                            <?php foreach ( $actions as $action_key => $action ) : ?>
                                <a href="<?php echo esc_url( $action['url'] ); ?>" class="button button-small <?php echo esc_attr( $action['class'] ); ?>">
                                    <?php echo esc_html( $action['label'] ); ?>
                                </a>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ( $vendors['pages'] > 1 ) : ?>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <?php
                echo paginate_links(
                    array(
                        'base'      => add_query_arg( 'paged', '%#%' ),
                        'format'    => '',
                        'current'   => $paged,
                        'total'     => $vendors['pages'],
                        'prev_text' => '&laquo;',
                        'next_text' => '&raquo;',
                    )
                );
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>
