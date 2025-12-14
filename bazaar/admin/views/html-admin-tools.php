<?php
/**
 * Admin Tools View.
 *
 * @package Bazaar\Admin\Views
 */

defined( 'ABSPATH' ) || exit;

// Handle tool actions
$message = '';
$message_type = 'success';

if ( isset( $_POST['bazaar_tool_action'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ?? '' ) ), 'bazaar_tools' ) ) {
    $tool = sanitize_text_field( wp_unslash( $_POST['bazaar_tool_action'] ) );
    
    switch ( $tool ) {
        case 'recount_orders':
            Bazaar_Tools::recount_vendor_orders();
            $message = __( 'Vendor orders have been recounted.', 'bazaar' );
            break;
            
        case 'recalculate_commissions':
            $count = Bazaar_Tools::recalculate_commissions();
            $message = sprintf( __( '%d commission records have been recalculated.', 'bazaar' ), $count );
            break;
            
        case 'clear_cache':
            Bazaar_Tools::clear_cache();
            $message = __( 'All marketplace caches have been cleared.', 'bazaar' );
            break;
            
        case 'sync_products':
            $count = Bazaar_Tools::sync_vendor_products();
            $message = sprintf( __( '%d products have been synchronized with their vendors.', 'bazaar' ), $count );
            break;
            
        case 'regenerate_commissions':
            $count = Bazaar_Tools::regenerate_commission_log();
            $message = sprintf( __( 'Commission log regenerated for %d orders.', 'bazaar' ), $count );
            break;
            
        case 'fix_vendor_balance':
            $count = Bazaar_Tools::fix_vendor_balances();
            $message = sprintf( __( 'Balances fixed for %d vendors.', 'bazaar' ), $count );
            break;
            
        case 'export_vendors':
            Bazaar_Tools::export_vendors_csv();
            exit;
            break;
            
        case 'export_commissions':
            Bazaar_Tools::export_commissions_csv();
            exit;
            break;
    }
}
?>
<div class="wrap bazaar-admin-wrap bazaar-tools-page">
    <div class="bazaar-page-header">
        <h1><?php esc_html_e( 'Tools', 'bazaar' ); ?></h1>
        <p class="header-subtitle"><?php esc_html_e( 'Marketplace maintenance and utility tools', 'bazaar' ); ?></p>
    </div>

    <?php if ( $message ) : ?>
        <div class="notice notice-<?php echo esc_attr( $message_type ); ?> is-dismissible">
            <p><?php echo esc_html( $message ); ?></p>
        </div>
    <?php endif; ?>

    <div class="bazaar-tools-grid">
        <!-- Maintenance Tools -->
        <div class="bazaar-tool-section">
            <h2><span class="dashicons dashicons-admin-tools"></span> <?php esc_html_e( 'Maintenance', 'bazaar' ); ?></h2>
            
            <div class="bazaar-tools-list">
                <div class="bazaar-tool-card">
                    <div class="tool-info">
                        <h3><?php esc_html_e( 'Recount Vendor Orders', 'bazaar' ); ?></h3>
                        <p><?php esc_html_e( 'Recalculate order counts for all vendors. Useful if counts seem incorrect.', 'bazaar' ); ?></p>
                    </div>
                    <form method="post" class="tool-action">
                        <?php wp_nonce_field( 'bazaar_tools' ); ?>
                        <button type="submit" name="bazaar_tool_action" value="recount_orders" class="button button-primary">
                            <span class="dashicons dashicons-update"></span>
                            <?php esc_html_e( 'Run', 'bazaar' ); ?>
                        </button>
                    </form>
                </div>

                <div class="bazaar-tool-card">
                    <div class="tool-info">
                        <h3><?php esc_html_e( 'Recalculate Commissions', 'bazaar' ); ?></h3>
                        <p><?php esc_html_e( 'Recalculate commission amounts based on current settings. Does not affect completed transactions.', 'bazaar' ); ?></p>
                    </div>
                    <form method="post" class="tool-action">
                        <?php wp_nonce_field( 'bazaar_tools' ); ?>
                        <button type="submit" name="bazaar_tool_action" value="recalculate_commissions" class="button button-primary">
                            <span class="dashicons dashicons-calculator"></span>
                            <?php esc_html_e( 'Run', 'bazaar' ); ?>
                        </button>
                    </form>
                </div>

                <div class="bazaar-tool-card">
                    <div class="tool-info">
                        <h3><?php esc_html_e( 'Clear Marketplace Cache', 'bazaar' ); ?></h3>
                        <p><?php esc_html_e( 'Clear all cached data including vendor stats, commission calculations, and product counts.', 'bazaar' ); ?></p>
                    </div>
                    <form method="post" class="tool-action">
                        <?php wp_nonce_field( 'bazaar_tools' ); ?>
                        <button type="submit" name="bazaar_tool_action" value="clear_cache" class="button button-primary">
                            <span class="dashicons dashicons-trash"></span>
                            <?php esc_html_e( 'Clear', 'bazaar' ); ?>
                        </button>
                    </form>
                </div>

                <div class="bazaar-tool-card">
                    <div class="tool-info">
                        <h3><?php esc_html_e( 'Sync Vendor Products', 'bazaar' ); ?></h3>
                        <p><?php esc_html_e( 'Synchronize product ownership with vendor accounts. Fixes orphaned products.', 'bazaar' ); ?></p>
                    </div>
                    <form method="post" class="tool-action">
                        <?php wp_nonce_field( 'bazaar_tools' ); ?>
                        <button type="submit" name="bazaar_tool_action" value="sync_products" class="button button-primary">
                            <span class="dashicons dashicons-update"></span>
                            <?php esc_html_e( 'Sync', 'bazaar' ); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Data Recovery -->
        <div class="bazaar-tool-section">
            <h2><span class="dashicons dashicons-database"></span> <?php esc_html_e( 'Data Recovery', 'bazaar' ); ?></h2>
            
            <div class="bazaar-tools-list">
                <div class="bazaar-tool-card">
                    <div class="tool-info">
                        <h3><?php esc_html_e( 'Regenerate Commission Log', 'bazaar' ); ?></h3>
                        <p><?php esc_html_e( 'Regenerate commission log entries from order data. Use if commission history is missing.', 'bazaar' ); ?></p>
                    </div>
                    <form method="post" class="tool-action">
                        <?php wp_nonce_field( 'bazaar_tools' ); ?>
                        <button type="submit" name="bazaar_tool_action" value="regenerate_commissions" class="button button-secondary">
                            <span class="dashicons dashicons-database-add"></span>
                            <?php esc_html_e( 'Regenerate', 'bazaar' ); ?>
                        </button>
                    </form>
                </div>

                <div class="bazaar-tool-card">
                    <div class="tool-info">
                        <h3><?php esc_html_e( 'Fix Vendor Balances', 'bazaar' ); ?></h3>
                        <p><?php esc_html_e( 'Recalculate vendor balances from transaction history. Use if balances are incorrect.', 'bazaar' ); ?></p>
                    </div>
                    <form method="post" class="tool-action">
                        <?php wp_nonce_field( 'bazaar_tools' ); ?>
                        <button type="submit" name="bazaar_tool_action" value="fix_vendor_balance" class="button button-secondary">
                            <span class="dashicons dashicons-money-alt"></span>
                            <?php esc_html_e( 'Fix Balances', 'bazaar' ); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Export Tools -->
        <div class="bazaar-tool-section">
            <h2><span class="dashicons dashicons-download"></span> <?php esc_html_e( 'Export', 'bazaar' ); ?></h2>
            
            <div class="bazaar-tools-list">
                <div class="bazaar-tool-card">
                    <div class="tool-info">
                        <h3><?php esc_html_e( 'Export Vendors', 'bazaar' ); ?></h3>
                        <p><?php esc_html_e( 'Export all vendor data to CSV including store info, earnings, and contact details.', 'bazaar' ); ?></p>
                    </div>
                    <form method="post" class="tool-action">
                        <?php wp_nonce_field( 'bazaar_tools' ); ?>
                        <button type="submit" name="bazaar_tool_action" value="export_vendors" class="button button-secondary">
                            <span class="dashicons dashicons-download"></span>
                            <?php esc_html_e( 'Export CSV', 'bazaar' ); ?>
                        </button>
                    </form>
                </div>

                <div class="bazaar-tool-card">
                    <div class="tool-info">
                        <h3><?php esc_html_e( 'Export Commissions', 'bazaar' ); ?></h3>
                        <p><?php esc_html_e( 'Export commission data including vendor earnings, admin fees, and order details.', 'bazaar' ); ?></p>
                    </div>
                    <form method="post" class="tool-action">
                        <?php wp_nonce_field( 'bazaar_tools' ); ?>
                        <button type="submit" name="bazaar_tool_action" value="export_commissions" class="button button-secondary">
                            <span class="dashicons dashicons-download"></span>
                            <?php esc_html_e( 'Export CSV', 'bazaar' ); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Page Installation -->
        <div class="bazaar-tool-section">
            <h2><span class="dashicons dashicons-admin-page"></span> <?php esc_html_e( 'Page Setup', 'bazaar' ); ?></h2>
            
            <div class="bazaar-page-setup">
                <p><?php esc_html_e( 'Bazaar requires certain pages to function correctly. Click the button below to create missing pages.', 'bazaar' ); ?></p>
                
                <table class="bazaar-pages-table widefat">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Page', 'bazaar' ); ?></th>
                            <th><?php esc_html_e( 'Shortcode', 'bazaar' ); ?></th>
                            <th><?php esc_html_e( 'Status', 'bazaar' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $pages = array(
                            'dashboard'    => array( 'name' => __( 'Vendor Dashboard', 'bazaar' ), 'shortcode' => '[bazaar_dashboard]' ),
                            'store_list'   => array( 'name' => __( 'Store Listing', 'bazaar' ), 'shortcode' => '[bazaar_stores]' ),
                            'registration' => array( 'name' => __( 'Vendor Registration', 'bazaar' ), 'shortcode' => '[bazaar_registration]' ),
                        );
                        
                        foreach ( $pages as $page_key => $page_info ) :
                            $page_id = get_option( 'bazaar_page_' . $page_key );
                            $page_exists = $page_id && get_post( $page_id );
                        ?>
                            <tr>
                                <td><?php echo esc_html( $page_info['name'] ); ?></td>
                                <td><code><?php echo esc_html( $page_info['shortcode'] ); ?></code></td>
                                <td>
                                    <?php if ( $page_exists ) : ?>
                                        <span class="status-ok"><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Installed', 'bazaar' ); ?></span>
                                        <a href="<?php echo esc_url( get_edit_post_link( $page_id ) ); ?>" class="button-link"><?php esc_html_e( 'Edit', 'bazaar' ); ?></a>
                                    <?php else : ?>
                                        <span class="status-missing"><span class="dashicons dashicons-no"></span> <?php esc_html_e( 'Not Found', 'bazaar' ); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <form method="post" class="page-install-form">
                    <?php wp_nonce_field( 'bazaar_tools' ); ?>
                    <button type="submit" name="bazaar_tool_action" value="install_pages" class="button button-primary">
                        <span class="dashicons dashicons-plus"></span>
                        <?php esc_html_e( 'Create Missing Pages', 'bazaar' ); ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- System Status -->
        <div class="bazaar-tool-section full-width">
            <h2><span class="dashicons dashicons-info"></span> <?php esc_html_e( 'System Status', 'bazaar' ); ?></h2>
            
            <table class="bazaar-status-table widefat">
                <tbody>
                    <tr>
                        <td><strong><?php esc_html_e( 'Bazaar Version', 'bazaar' ); ?></strong></td>
                        <td><?php echo esc_html( defined( 'BAZAAR_VERSION' ) ? BAZAAR_VERSION : '1.0.0' ); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e( 'WooCommerce Version', 'bazaar' ); ?></strong></td>
                        <td><?php echo esc_html( defined( 'WC_VERSION' ) ? WC_VERSION : __( 'Not Found', 'bazaar' ) ); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e( 'WordPress Version', 'bazaar' ); ?></strong></td>
                        <td><?php echo esc_html( get_bloginfo( 'version' ) ); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e( 'PHP Version', 'bazaar' ); ?></strong></td>
                        <td><?php echo esc_html( PHP_VERSION ); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e( 'Total Vendors', 'bazaar' ); ?></strong></td>
                        <td><?php echo esc_html( count( get_users( array( 'role' => 'bazaar_vendor' ) ) ) ); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e( 'Active Modules', 'bazaar' ); ?></strong></td>
                        <td><?php echo esc_html( count( Bazaar_Modules::get_active_modules() ) ); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e( 'Database Tables', 'bazaar' ); ?></strong></td>
                        <td>
                            <?php
                            global $wpdb;
                            $tables = array(
                                $wpdb->prefix . 'bazaar_vendor_balance',
                                $wpdb->prefix . 'bazaar_withdrawals',
                                $wpdb->prefix . 'bazaar_orders',
                                $wpdb->prefix . 'bazaar_refunds',
                            );
                            $missing = array();
                            foreach ( $tables as $table ) {
                                if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
                                    $missing[] = $table;
                                }
                            }
                            if ( empty( $missing ) ) {
                                echo '<span class="status-ok"><span class="dashicons dashicons-yes"></span> ' . esc_html__( 'All tables exist', 'bazaar' ) . '</span>';
                            } else {
                                echo '<span class="status-warning"><span class="dashicons dashicons-warning"></span> ' . esc_html__( 'Missing tables:', 'bazaar' ) . ' ' . esc_html( implode( ', ', $missing ) ) . '</span>';
                            }
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
