<?php
/**
 * Admin Settings View.
 *
 * @package Bazaar\Admin\Views
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap bazaar-admin-wrap bazaar-settings">
    <h1><?php esc_html_e( 'Bazaar Settings', 'bazaar' ); ?></h1>

    <nav class="nav-tab-wrapper">
        <?php foreach ( $tabs as $tab_slug => $tab_label ) : ?>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bazaar-settings&tab=' . $tab_slug ) ); ?>"
               class="nav-tab <?php echo $tab === $tab_slug ? 'nav-tab-active' : ''; ?>">
                <?php echo esc_html( $tab_label ); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <form method="post" action="">
        <?php wp_nonce_field( 'bazaar_settings' ); ?>

        <table class="form-table">
            <?php foreach ( $settings as $setting ) : ?>
                <tr>
                    <th scope="row">
                        <label for="<?php echo esc_attr( $setting['id'] ); ?>">
                            <?php echo esc_html( $setting['title'] ); ?>
                        </label>
                    </th>
                    <td>
                        <?php Bazaar_Admin_Settings::render_field( $setting ); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <p class="submit">
            <button type="submit" name="bazaar_save_settings" class="button button-primary">
                <?php esc_html_e( 'Save Settings', 'bazaar' ); ?>
            </button>
        </p>
    </form>
</div>
