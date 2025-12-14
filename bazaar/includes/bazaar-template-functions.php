<?php
/**
 * Template Functions.
 *
 * @package Bazaar\Templates
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get template.
 *
 * @param string $template_name Template name.
 * @param array  $args          Template arguments.
 * @param string $template_path Template path.
 * @param string $default_path  Default path.
 */
function bazaar_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
    if ( ! empty( $args ) && is_array( $args ) ) {
        extract( $args );
    }

    $template = bazaar_locate_template( $template_name, $template_path, $default_path );

    if ( ! file_exists( $template ) ) {
        /* translators: %s: template name */
        _doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'bazaar' ), '<code>' . $template . '</code>' ), '1.0.0' );
        return;
    }

    do_action( 'bazaar_before_template_part', $template_name, $template_path, $template, $args );

    include $template;

    do_action( 'bazaar_after_template_part', $template_name, $template_path, $template, $args );
}

/**
 * Get template part.
 *
 * @param string $slug Template slug.
 * @param string $name Template name.
 * @param array  $args Template arguments.
 */
function bazaar_get_template_part( $slug, $name = '', $args = array() ) {
    $template = '';

    if ( $name ) {
        $template = bazaar_locate_template( "{$slug}-{$name}.php" );
    }

    if ( ! $template ) {
        $template = bazaar_locate_template( "{$slug}.php" );
    }

    if ( $template ) {
        if ( ! empty( $args ) && is_array( $args ) ) {
            extract( $args );
        }

        include $template;
    }
}

/**
 * Locate template.
 *
 * @param string $template_name Template name.
 * @param string $template_path Template path.
 * @param string $default_path  Default path.
 * @return string
 */
function bazaar_locate_template( $template_name, $template_path = '', $default_path = '' ) {
    if ( ! $template_path ) {
        $template_path = bazaar()->template_path();
    }

    if ( ! $default_path ) {
        $default_path = BAZAAR_TEMPLATE_PATH;
    }

    // Look within passed path within the theme
    $template = locate_template(
        array(
            trailingslashit( $template_path ) . $template_name,
            $template_name,
        )
    );

    // Get default template
    if ( ! $template ) {
        $template = $default_path . $template_name;
    }

    return apply_filters( 'bazaar_locate_template', $template, $template_name, $template_path );
}

/**
 * Get template HTML.
 *
 * @param string $template_name Template name.
 * @param array  $args          Template arguments.
 * @return string
 */
function bazaar_get_template_html( $template_name, $args = array() ) {
    ob_start();
    bazaar_get_template( $template_name, $args );
    return ob_get_clean();
}

/**
 * Display vendor avatar.
 *
 * @param int $vendor_id Vendor ID.
 * @param int $size      Avatar size.
 */
function bazaar_vendor_avatar( $vendor_id, $size = 96 ) {
    $logo_id = get_user_meta( $vendor_id, '_bazaar_store_logo', true );

    if ( $logo_id ) {
        echo wp_get_attachment_image( $logo_id, array( $size, $size ), false, array( 'class' => 'bazaar-vendor-avatar' ) );
    } else {
        echo get_avatar( $vendor_id, $size, '', '', array( 'class' => 'bazaar-vendor-avatar' ) );
    }
}

/**
 * Display vendor banner.
 *
 * @param int $vendor_id Vendor ID.
 */
function bazaar_vendor_banner( $vendor_id ) {
    $banner_id = get_user_meta( $vendor_id, '_bazaar_store_banner', true );

    if ( $banner_id ) {
        echo wp_get_attachment_image( $banner_id, 'full', false, array( 'class' => 'bazaar-vendor-banner-img' ) );
    } else {
        echo '<div class="bazaar-vendor-banner-placeholder"></div>';
    }
}

/**
 * Display vendor rating.
 *
 * @param int  $vendor_id   Vendor ID.
 * @param bool $show_count  Show review count.
 */
function bazaar_vendor_rating( $vendor_id, $show_count = true ) {
    $rating = Bazaar_Vendor::get_vendor_rating( $vendor_id );

    if ( $rating['count'] === 0 ) {
        echo '<span class="bazaar-no-rating">' . esc_html__( 'No reviews yet', 'bazaar' ) . '</span>';
        return;
    }

    $html = '<div class="bazaar-vendor-rating">';
    $html .= '<span class="bazaar-stars">';

    for ( $i = 1; $i <= 5; $i++ ) {
        if ( $i <= $rating['average'] ) {
            $html .= '<span class="star filled">&#9733;</span>';
        } elseif ( $i - 0.5 <= $rating['average'] ) {
            $html .= '<span class="star half">&#9733;</span>';
        } else {
            $html .= '<span class="star empty">&#9734;</span>';
        }
    }

    $html .= '</span>';
    $html .= '<span class="bazaar-rating-value">' . esc_html( $rating['average'] ) . '</span>';

    if ( $show_count ) {
        $html .= '<span class="bazaar-rating-count">(' . esc_html( $rating['count'] ) . ')</span>';
    }

    $html .= '</div>';

    echo $html;
}

/**
 * Display vendor store info.
 *
 * @param array $vendor Vendor data.
 */
function bazaar_vendor_store_info( $vendor ) {
    ?>
    <div class="bazaar-store-info">
        <div class="bazaar-store-meta">
            <?php if ( ! empty( $vendor['address']['city'] ) || ! empty( $vendor['address']['country'] ) ) : ?>
                <span class="bazaar-store-location">
                    <span class="dashicons dashicons-location"></span>
                    <?php
                    $location_parts = array_filter( array( $vendor['address']['city'], $vendor['address']['country'] ) );
                    echo esc_html( implode( ', ', $location_parts ) );
                    ?>
                </span>
            <?php endif; ?>

            <?php if ( ! empty( $vendor['phone'] ) ) : ?>
                <span class="bazaar-store-phone">
                    <span class="dashicons dashicons-phone"></span>
                    <?php echo esc_html( $vendor['phone'] ); ?>
                </span>
            <?php endif; ?>

            <span class="bazaar-store-products">
                <span class="dashicons dashicons-products"></span>
                <?php
                $product_count = Bazaar_Vendor::get_product_count( $vendor['id'] );
                /* translators: %d: product count */
                printf( esc_html( _n( '%d Product', '%d Products', $product_count, 'bazaar' ) ), $product_count );
                ?>
            </span>
        </div>

        <?php if ( ! empty( array_filter( $vendor['social'] ) ) ) : ?>
            <div class="bazaar-store-social">
                <?php foreach ( $vendor['social'] as $platform => $url ) : ?>
                    <?php if ( ! empty( $url ) ) : ?>
                        <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener" class="bazaar-social-<?php echo esc_attr( $platform ); ?>">
                            <span class="dashicons dashicons-<?php echo esc_attr( $platform ); ?>"></span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Display dashboard navigation.
 *
 * @param string $current_tab Current tab.
 */
function bazaar_dashboard_navigation( $current_tab ) {
    $tabs = bazaar_get_dashboard_tabs();
    ?>
    <nav class="bazaar-dashboard-nav">
        <ul>
            <?php foreach ( $tabs as $tab_slug => $tab ) : ?>
                <li class="<?php echo $current_tab === $tab_slug ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url( bazaar_get_dashboard_tab_url( $tab_slug ) ); ?>">
                        <?php if ( ! empty( $tab['icon'] ) ) : ?>
                            <span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>"></span>
                        <?php endif; ?>
                        <span class="tab-title"><?php echo esc_html( $tab['title'] ); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
            <li>
                <a href="<?php echo esc_url( bazaar_get_vendor_store_url( get_current_user_id() ) ); ?>" target="_blank">
                    <span class="dashicons dashicons-store"></span>
                    <span class="tab-title"><?php esc_html_e( 'View Store', 'bazaar' ); ?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>">
                    <span class="dashicons dashicons-exit"></span>
                    <span class="tab-title"><?php esc_html_e( 'Logout', 'bazaar' ); ?></span>
                </a>
            </li>
        </ul>
    </nav>
    <?php
}

/**
 * Display pagination.
 *
 * @param int $total_pages Total pages.
 * @param int $current     Current page.
 * @param string $base_url Base URL.
 */
function bazaar_pagination( $total_pages, $current = 1, $base_url = '' ) {
    if ( $total_pages <= 1 ) {
        return;
    }

    if ( empty( $base_url ) ) {
        $base_url = remove_query_arg( 'paged' );
    }

    echo '<nav class="bazaar-pagination">';
    echo '<ul>';

    // Previous
    if ( $current > 1 ) {
        echo '<li class="prev"><a href="' . esc_url( add_query_arg( 'paged', $current - 1, $base_url ) ) . '">&laquo;</a></li>';
    }

    // Page numbers
    for ( $i = 1; $i <= $total_pages; $i++ ) {
        if ( $i === $current ) {
            echo '<li class="active"><span>' . esc_html( $i ) . '</span></li>';
        } else {
            echo '<li><a href="' . esc_url( add_query_arg( 'paged', $i, $base_url ) ) . '">' . esc_html( $i ) . '</a></li>';
        }
    }

    // Next
    if ( $current < $total_pages ) {
        echo '<li class="next"><a href="' . esc_url( add_query_arg( 'paged', $current + 1, $base_url ) ) . '">&raquo;</a></li>';
    }

    echo '</ul>';
    echo '</nav>';
}

/**
 * Display notice.
 *
 * @param string $message Message.
 * @param string $type    Notice type.
 */
function bazaar_notice( $message, $type = 'info' ) {
    echo '<div class="bazaar-notice bazaar-notice-' . esc_attr( $type ) . '">';
    echo wp_kses_post( $message );
    echo '</div>';
}

/**
 * Display dashboard stats card.
 *
 * @param string $title Title.
 * @param string $value Value.
 * @param string $icon  Icon class.
 * @param string $link  Link URL.
 */
function bazaar_stats_card( $title, $value, $icon = '', $link = '' ) {
    ?>
    <div class="bazaar-stats-card">
        <?php if ( ! empty( $icon ) ) : ?>
            <div class="stats-icon">
                <span class="dashicons <?php echo esc_attr( $icon ); ?>"></span>
            </div>
        <?php endif; ?>
        <div class="stats-content">
            <div class="stats-value"><?php echo esc_html( $value ); ?></div>
            <div class="stats-title"><?php echo esc_html( $title ); ?></div>
        </div>
        <?php if ( ! empty( $link ) ) : ?>
            <a href="<?php echo esc_url( $link ); ?>" class="stats-link">
                <span class="dashicons dashicons-arrow-right-alt2"></span>
            </a>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Display order status badge.
 *
 * @param string $status Status.
 */
function bazaar_order_status_badge( $status ) {
    $status = str_replace( 'wc-', '', $status );
    $statuses = bazaar_get_order_statuses();
    $label = isset( $statuses[ $status ] ) ? $statuses[ $status ] : $status;

    echo '<span class="bazaar-status-badge status-' . esc_attr( $status ) . '">' . esc_html( $label ) . '</span>';
}

/**
 * Display withdrawal status badge.
 *
 * @param string $status Status.
 */
function bazaar_withdrawal_status_badge( $status ) {
    $label = Bazaar_Withdrawal::get_status_label( $status );

    echo '<span class="bazaar-status-badge withdrawal-' . esc_attr( $status ) . '">' . esc_html( $label ) . '</span>';
}

/**
 * Display loading spinner.
 */
function bazaar_loading_spinner() {
    echo '<div class="bazaar-loading"><span class="bazaar-spinner"></span></div>';
}

/**
 * Get notification icon based on type.
 *
 * @param string $type Notification type.
 * @return string Dashicons class.
 */
function bazaar_get_notification_icon( $type ) {
    $icons = array(
        'order'      => 'dashicons-cart',
        'new_order'  => 'dashicons-cart',
        'review'     => 'dashicons-star-filled',
        'new_review' => 'dashicons-star-filled',
        'product'    => 'dashicons-products',
        'withdrawal' => 'dashicons-money-alt',
        'withdraw'   => 'dashicons-money-alt',
        'earning'    => 'dashicons-chart-line',
        'commission' => 'dashicons-chart-line',
        'refund'     => 'dashicons-undo',
        'coupon'     => 'dashicons-tickets-alt',
        'shipping'   => 'dashicons-car',
        'message'    => 'dashicons-email',
        'alert'      => 'dashicons-warning',
        'system'     => 'dashicons-info',
        'info'       => 'dashicons-info-outline',
        'success'    => 'dashicons-yes-alt',
        'error'      => 'dashicons-dismiss',
    );

    return isset( $icons[ $type ] ) ? $icons[ $type ] : 'dashicons-bell';
}
