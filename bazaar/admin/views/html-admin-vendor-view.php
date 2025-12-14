<?php
/**
 * Admin Vendor View.
 *
 * @package Bazaar\Admin\Views
 */

defined( 'ABSPATH' ) || exit;

if ( ! $vendor ) {
    echo '<div class="wrap"><h1>' . esc_html__( 'Vendor not found.', 'bazaar' ) . '</h1></div>';
    return;
}
?>
<div class="wrap bazaar-admin-wrap bazaar-vendor-view">
    <h1>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-vendors' ) ); ?>">&larr;</a>
        <?php echo esc_html( $vendor['store_name'] ); ?>
        <?php echo Bazaar_Admin_Vendors::get_status_badge( $vendor['status'] ); ?>
    </h1>

    <div class="bazaar-vendor-header">
        <div class="vendor-avatar">
            <?php bazaar_vendor_avatar( $vendor['id'], 120 ); ?>
        </div>
        <div class="vendor-info">
            <h2><?php echo esc_html( $vendor['store_name'] ); ?></h2>
            <p class="vendor-email">
                <span class="dashicons dashicons-email"></span>
                <a href="mailto:<?php echo esc_attr( $vendor['email'] ); ?>"><?php echo esc_html( $vendor['email'] ); ?></a>
            </p>
            <?php if ( $vendor['phone'] ) : ?>
                <p class="vendor-phone">
                    <span class="dashicons dashicons-phone"></span>
                    <?php echo esc_html( $vendor['phone'] ); ?>
                </p>
            <?php endif; ?>
            <p class="vendor-store-url">
                <span class="dashicons dashicons-store"></span>
                <a href="<?php echo esc_url( $vendor['store_url'] ); ?>" target="_blank"><?php echo esc_html( $vendor['store_url'] ); ?></a>
            </p>
        </div>
        <div class="vendor-actions">
            <?php $actions = Bazaar_Admin_Vendors::get_action_links( $vendor['id'] ); ?>
            <?php foreach ( $actions as $action_key => $action ) : ?>
                <a href="<?php echo esc_url( $action['url'] ); ?>" class="button <?php echo 'view' === $action_key ? '' : 'button-primary'; ?> <?php echo esc_attr( $action['class'] ); ?>">
                    <?php echo esc_html( $action['label'] ); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="bazaar-vendor-stats">
        <div class="stat-box">
            <div class="stat-value"><?php echo wc_price( $vendor['balance'] ); ?></div>
            <div class="stat-label"><?php esc_html_e( 'Balance', 'bazaar' ); ?></div>
        </div>
        <div class="stat-box">
            <div class="stat-value"><?php echo wc_price( $vendor['total_sales'] ); ?></div>
            <div class="stat-label"><?php esc_html_e( 'Total Sales', 'bazaar' ); ?></div>
        </div>
        <div class="stat-box">
            <div class="stat-value"><?php echo esc_html( Bazaar_Vendor::get_product_count( $vendor['id'] ) ); ?></div>
            <div class="stat-label"><?php esc_html_e( 'Products', 'bazaar' ); ?></div>
        </div>
        <div class="stat-box">
            <div class="stat-value"><?php echo esc_html( Bazaar_Vendor::get_order_count( $vendor['id'] ) ); ?></div>
            <div class="stat-label"><?php esc_html_e( 'Orders', 'bazaar' ); ?></div>
        </div>
        <div class="stat-box">
            <div class="stat-value">
                <?php if ( $vendor['rating']['count'] > 0 ) : ?>
                    <?php echo esc_html( $vendor['rating']['average'] ); ?>/5
                    <small>(<?php echo esc_html( $vendor['rating']['count'] ); ?>)</small>
                <?php else : ?>
                    -
                <?php endif; ?>
            </div>
            <div class="stat-label"><?php esc_html_e( 'Rating', 'bazaar' ); ?></div>
        </div>
    </div>

    <div class="bazaar-vendor-details">
        <div class="detail-section">
            <h3><?php esc_html_e( 'Store Information', 'bazaar' ); ?></h3>
            <table class="form-table">
                <tr>
                    <th><?php esc_html_e( 'Store Name', 'bazaar' ); ?></th>
                    <td><?php echo esc_html( $vendor['store_name'] ); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Store Slug', 'bazaar' ); ?></th>
                    <td><?php echo esc_html( $vendor['store_slug'] ); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Description', 'bazaar' ); ?></th>
                    <td><?php echo wp_kses_post( $vendor['description'] ); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Registered', 'bazaar' ); ?></th>
                    <td><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $vendor['registered'] ) ) ); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Vacation Mode', 'bazaar' ); ?></th>
                    <td><?php echo 'yes' === $vendor['vacation_mode'] ? esc_html__( 'Enabled', 'bazaar' ) : esc_html__( 'Disabled', 'bazaar' ); ?></td>
                </tr>
            </table>
        </div>

        <div class="detail-section">
            <h3><?php esc_html_e( 'Address', 'bazaar' ); ?></h3>
            <table class="form-table">
                <?php if ( $vendor['address']['street_1'] ) : ?>
                    <tr>
                        <th><?php esc_html_e( 'Street Address', 'bazaar' ); ?></th>
                        <td>
                            <?php echo esc_html( $vendor['address']['street_1'] ); ?>
                            <?php if ( $vendor['address']['street_2'] ) : ?>
                                <br><?php echo esc_html( $vendor['address']['street_2'] ); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if ( $vendor['address']['city'] ) : ?>
                    <tr>
                        <th><?php esc_html_e( 'City', 'bazaar' ); ?></th>
                        <td><?php echo esc_html( $vendor['address']['city'] ); ?></td>
                    </tr>
                <?php endif; ?>
                <?php if ( $vendor['address']['state'] ) : ?>
                    <tr>
                        <th><?php esc_html_e( 'State', 'bazaar' ); ?></th>
                        <td><?php echo esc_html( $vendor['address']['state'] ); ?></td>
                    </tr>
                <?php endif; ?>
                <?php if ( $vendor['address']['postcode'] ) : ?>
                    <tr>
                        <th><?php esc_html_e( 'Postcode', 'bazaar' ); ?></th>
                        <td><?php echo esc_html( $vendor['address']['postcode'] ); ?></td>
                    </tr>
                <?php endif; ?>
                <?php if ( $vendor['address']['country'] ) : ?>
                    <tr>
                        <th><?php esc_html_e( 'Country', 'bazaar' ); ?></th>
                        <td><?php echo esc_html( $vendor['address']['country'] ); ?></td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>

        <div class="detail-section">
            <h3><?php esc_html_e( 'Social Links', 'bazaar' ); ?></h3>
            <table class="form-table">
                <?php foreach ( $vendor['social'] as $platform => $url ) : ?>
                    <?php if ( $url ) : ?>
                        <tr>
                            <th><?php echo esc_html( ucfirst( $platform ) ); ?></th>
                            <td><a href="<?php echo esc_url( $url ); ?>" target="_blank"><?php echo esc_url( $url ); ?></a></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
