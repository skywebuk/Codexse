<?php
/**
 * Admin Announcements View.
 *
 * @package Bazaar\Admin\Views
 */

defined( 'ABSPATH' ) || exit;

// Handle form submission
if ( isset( $_POST['bazaar_create_announcement'] ) && check_admin_referer( 'bazaar_announcement' ) ) {
    $title = isset( $_POST['announcement_title'] ) ? sanitize_text_field( wp_unslash( $_POST['announcement_title'] ) ) : '';
    $content = isset( $_POST['announcement_content'] ) ? wp_kses_post( wp_unslash( $_POST['announcement_content'] ) ) : '';
    $status = isset( $_POST['announcement_status'] ) ? sanitize_text_field( wp_unslash( $_POST['announcement_status'] ) ) : 'publish';
    $send_email = isset( $_POST['send_email'] ) ? true : false;

    if ( $title && $content ) {
        $announcement_id = wp_insert_post( array(
            'post_title'   => $title,
            'post_content' => $content,
            'post_type'    => 'bazaar_announcement',
            'post_status'  => $status,
            'post_author'  => get_current_user_id(),
        ) );

        if ( $announcement_id && ! is_wp_error( $announcement_id ) ) {
            if ( $send_email ) {
                Bazaar_Notifications::send_announcement_email( $announcement_id );
            }
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Announcement created successfully.', 'bazaar' ) . '</p></div>';
        }
    }
}

// Handle delete
if ( isset( $_GET['action'] ) && 'delete' === $_GET['action'] && isset( $_GET['id'] ) && isset( $_GET['_wpnonce'] ) ) {
    if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'delete_announcement' ) ) {
        wp_delete_post( intval( $_GET['id'] ), true );
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Announcement deleted.', 'bazaar' ) . '</p></div>';
    }
}

// Get announcements
$paged = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
$announcements = get_posts( array(
    'post_type'      => 'bazaar_announcement',
    'posts_per_page' => 20,
    'paged'          => $paged,
    'post_status'    => 'any',
    'orderby'        => 'date',
    'order'          => 'DESC',
) );

$total = wp_count_posts( 'bazaar_announcement' );
$total_count = $total->publish + $total->draft;
$total_pages = ceil( $total_count / 20 );
?>
<div class="wrap bazaar-admin-wrap">
    <div class="bazaar-page-header">
        <h1><?php esc_html_e( 'Announcements', 'bazaar' ); ?></h1>
        <p class="header-subtitle"><?php esc_html_e( 'Send announcements to all vendors', 'bazaar' ); ?></p>
    </div>

    <div class="bazaar-two-column">
        <!-- Left Column - List -->
        <div class="bazaar-column-main">
            <div class="bazaar-table-container">
                <div class="table-header">
                    <h2><?php esc_html_e( 'All Announcements', 'bazaar' ); ?></h2>
                </div>

                <table class="bazaar-admin-table wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th class="column-title"><?php esc_html_e( 'Title', 'bazaar' ); ?></th>
                            <th class="column-author"><?php esc_html_e( 'Author', 'bazaar' ); ?></th>
                            <th class="column-status"><?php esc_html_e( 'Status', 'bazaar' ); ?></th>
                            <th class="column-date"><?php esc_html_e( 'Date', 'bazaar' ); ?></th>
                            <th class="column-actions"><?php esc_html_e( 'Actions', 'bazaar' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( empty( $announcements ) ) : ?>
                            <tr>
                                <td colspan="5" class="no-items">
                                    <div class="empty-state">
                                        <span class="dashicons dashicons-megaphone"></span>
                                        <p><?php esc_html_e( 'No announcements yet.', 'bazaar' ); ?></p>
                                    </div>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ( $announcements as $announcement ) : ?>
                                <?php $author = get_userdata( $announcement->post_author ); ?>
                                <tr>
                                    <td class="column-title">
                                        <strong><?php echo esc_html( $announcement->post_title ); ?></strong>
                                        <p class="excerpt"><?php echo esc_html( wp_trim_words( $announcement->post_content, 15 ) ); ?></p>
                                    </td>
                                    <td class="column-author">
                                        <?php echo $author ? esc_html( $author->display_name ) : '-'; ?>
                                    </td>
                                    <td class="column-status">
                                        <span class="status-badge status-<?php echo esc_attr( $announcement->post_status ); ?>">
                                            <?php echo esc_html( get_post_status_object( $announcement->post_status )->label ); ?>
                                        </span>
                                    </td>
                                    <td class="column-date">
                                        <?php echo esc_html( get_the_date( get_option( 'date_format' ), $announcement ) ); ?>
                                    </td>
                                    <td class="column-actions">
                                        <a href="<?php echo esc_url( get_edit_post_link( $announcement->ID ) ); ?>" class="button button-small">
                                            <?php esc_html_e( 'Edit', 'bazaar' ); ?>
                                        </a>
                                        <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=bazaar-announcements&action=delete&id=' . $announcement->ID ), 'delete_announcement' ) ); ?>" class="button button-small button-link-delete" onclick="return confirm('<?php esc_attr_e( 'Are you sure?', 'bazaar' ); ?>');">
                                            <?php esc_html_e( 'Delete', 'bazaar' ); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ( $total_pages > 1 ) : ?>
                <div class="bazaar-pagination">
                    <?php
                    echo paginate_links( array(
                        'base'      => add_query_arg( 'paged', '%#%' ),
                        'format'    => '',
                        'current'   => $paged,
                        'total'     => $total_pages,
                    ) );
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Column - Create Form -->
        <div class="bazaar-column-sidebar">
            <div class="bazaar-card">
                <div class="card-header">
                    <h3><?php esc_html_e( 'Create Announcement', 'bazaar' ); ?></h3>
                </div>
                <div class="card-body">
                    <form method="post" class="bazaar-form">
                        <?php wp_nonce_field( 'bazaar_announcement' ); ?>

                        <div class="form-group">
                            <label for="announcement_title"><?php esc_html_e( 'Title', 'bazaar' ); ?> <span class="required">*</span></label>
                            <input type="text" name="announcement_title" id="announcement_title" class="regular-text" required>
                        </div>

                        <div class="form-group">
                            <label for="announcement_content"><?php esc_html_e( 'Content', 'bazaar' ); ?> <span class="required">*</span></label>
                            <?php
                            wp_editor( '', 'announcement_content', array(
                                'textarea_name' => 'announcement_content',
                                'textarea_rows' => 8,
                                'media_buttons' => false,
                                'teeny'         => true,
                            ) );
                            ?>
                        </div>

                        <div class="form-group">
                            <label for="announcement_status"><?php esc_html_e( 'Status', 'bazaar' ); ?></label>
                            <select name="announcement_status" id="announcement_status">
                                <option value="publish"><?php esc_html_e( 'Published', 'bazaar' ); ?></option>
                                <option value="draft"><?php esc_html_e( 'Draft', 'bazaar' ); ?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="send_email" value="1">
                                <?php esc_html_e( 'Send email notification to all vendors', 'bazaar' ); ?>
                            </label>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="bazaar_create_announcement" class="button button-primary button-large">
                                <span class="dashicons dashicons-megaphone"></span>
                                <?php esc_html_e( 'Create Announcement', 'bazaar' ); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
