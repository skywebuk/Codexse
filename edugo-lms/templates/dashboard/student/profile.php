<?php
/**
 * Student Dashboard - Profile Tab.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$user_id = get_current_user_id();
$user = get_userdata( $user_id );

// Handle form submission.
$message = '';
$message_type = '';

if ( isset( $_POST['edugo_update_profile'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'edugo_update_profile' ) ) {
    $first_name = sanitize_text_field( wp_unslash( $_POST['first_name'] ?? '' ) );
    $last_name = sanitize_text_field( wp_unslash( $_POST['last_name'] ?? '' ) );
    $display_name = sanitize_text_field( wp_unslash( $_POST['display_name'] ?? '' ) );
    $description = sanitize_textarea_field( wp_unslash( $_POST['description'] ?? '' ) );

    wp_update_user( array(
        'ID'           => $user_id,
        'first_name'   => $first_name,
        'last_name'    => $last_name,
        'display_name' => $display_name,
        'description'  => $description,
    ) );

    // Refresh user data.
    $user = get_userdata( $user_id );
    $message = __( 'Profile updated successfully.', 'edugo-lms' );
    $message_type = 'success';
}
?>

<div class="edugo-dashboard-header">
    <h2><?php esc_html_e( 'My Profile', 'edugo-lms' ); ?></h2>
</div>

<?php if ( $message ) : ?>
    <div class="edugo-notice edugo-notice-<?php echo esc_attr( $message_type ); ?>">
        <p><?php echo esc_html( $message ); ?></p>
    </div>
<?php endif; ?>

<div class="edugo-profile-section">
    <div class="edugo-profile-avatar">
        <?php echo get_avatar( $user_id, 120 ); ?>
        <p class="edugo-avatar-note">
            <?php
            printf(
                /* translators: %s: Gravatar URL */
                esc_html__( 'Profile picture is managed via %s', 'edugo-lms' ),
                '<a href="https://gravatar.com" target="_blank">Gravatar</a>'
            );
            ?>
        </p>
    </div>

    <form method="post" class="edugo-profile-form">
        <?php wp_nonce_field( 'edugo_update_profile' ); ?>

        <div class="edugo-form-row">
            <div class="edugo-form-group">
                <label for="first_name"><?php esc_html_e( 'First Name', 'edugo-lms' ); ?></label>
                <input type="text" id="first_name" name="first_name" value="<?php echo esc_attr( $user->first_name ); ?>" class="edugo-input">
            </div>

            <div class="edugo-form-group">
                <label for="last_name"><?php esc_html_e( 'Last Name', 'edugo-lms' ); ?></label>
                <input type="text" id="last_name" name="last_name" value="<?php echo esc_attr( $user->last_name ); ?>" class="edugo-input">
            </div>
        </div>

        <div class="edugo-form-group">
            <label for="display_name"><?php esc_html_e( 'Display Name', 'edugo-lms' ); ?></label>
            <input type="text" id="display_name" name="display_name" value="<?php echo esc_attr( $user->display_name ); ?>" class="edugo-input" required>
        </div>

        <div class="edugo-form-group">
            <label for="user_email"><?php esc_html_e( 'Email Address', 'edugo-lms' ); ?></label>
            <input type="email" id="user_email" value="<?php echo esc_attr( $user->user_email ); ?>" class="edugo-input" disabled>
            <p class="edugo-form-note"><?php esc_html_e( 'Email cannot be changed from here.', 'edugo-lms' ); ?></p>
        </div>

        <div class="edugo-form-group">
            <label for="description"><?php esc_html_e( 'Bio', 'edugo-lms' ); ?></label>
            <textarea id="description" name="description" rows="4" class="edugo-textarea"><?php echo esc_textarea( $user->description ); ?></textarea>
        </div>

        <div class="edugo-form-actions">
            <button type="submit" name="edugo_update_profile" class="edugo-button edugo-button-primary">
                <?php esc_html_e( 'Update Profile', 'edugo-lms' ); ?>
            </button>
        </div>
    </form>
</div>

<div class="edugo-profile-section">
    <h3><?php esc_html_e( 'Account Settings', 'edugo-lms' ); ?></h3>

    <div class="edugo-account-links">
        <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="edugo-link">
            <span class="dashicons dashicons-lock"></span>
            <?php esc_html_e( 'Change Password', 'edugo-lms' ); ?>
        </a>

        <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="edugo-link edugo-link-danger">
            <span class="dashicons dashicons-exit"></span>
            <?php esc_html_e( 'Log Out', 'edugo-lms' ); ?>
        </a>
    </div>
</div>
