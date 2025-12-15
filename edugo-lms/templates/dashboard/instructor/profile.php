<?php
/**
 * Instructor Dashboard - Profile Tab.
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

if ( isset( $_POST['edugo_update_instructor_profile'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'edugo_update_instructor_profile' ) ) {
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

    // Save instructor-specific meta.
    $job_title = sanitize_text_field( wp_unslash( $_POST['job_title'] ?? '' ) );
    $website = esc_url_raw( wp_unslash( $_POST['website'] ?? '' ) );
    $social_facebook = esc_url_raw( wp_unslash( $_POST['social_facebook'] ?? '' ) );
    $social_twitter = esc_url_raw( wp_unslash( $_POST['social_twitter'] ?? '' ) );
    $social_linkedin = esc_url_raw( wp_unslash( $_POST['social_linkedin'] ?? '' ) );

    update_user_meta( $user_id, '_edugo_job_title', $job_title );
    update_user_meta( $user_id, '_edugo_website', $website );
    update_user_meta( $user_id, '_edugo_social_facebook', $social_facebook );
    update_user_meta( $user_id, '_edugo_social_twitter', $social_twitter );
    update_user_meta( $user_id, '_edugo_social_linkedin', $social_linkedin );

    // Refresh user data.
    $user = get_userdata( $user_id );
    $message = __( 'Profile updated successfully.', 'edugo-lms' );
    $message_type = 'success';
}

// Get instructor meta.
$job_title = get_user_meta( $user_id, '_edugo_job_title', true );
$website = get_user_meta( $user_id, '_edugo_website', true );
$social_facebook = get_user_meta( $user_id, '_edugo_social_facebook', true );
$social_twitter = get_user_meta( $user_id, '_edugo_social_twitter', true );
$social_linkedin = get_user_meta( $user_id, '_edugo_social_linkedin', true );
?>

<div class="edugo-dashboard-header">
    <h2><?php esc_html_e( 'Instructor Profile', 'edugo-lms' ); ?></h2>
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
        <?php wp_nonce_field( 'edugo_update_instructor_profile' ); ?>

        <h3><?php esc_html_e( 'Basic Information', 'edugo-lms' ); ?></h3>

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

        <div class="edugo-form-row">
            <div class="edugo-form-group">
                <label for="display_name"><?php esc_html_e( 'Display Name', 'edugo-lms' ); ?></label>
                <input type="text" id="display_name" name="display_name" value="<?php echo esc_attr( $user->display_name ); ?>" class="edugo-input" required>
            </div>

            <div class="edugo-form-group">
                <label for="job_title"><?php esc_html_e( 'Job Title / Expertise', 'edugo-lms' ); ?></label>
                <input type="text" id="job_title" name="job_title" value="<?php echo esc_attr( $job_title ); ?>" class="edugo-input" placeholder="<?php esc_attr_e( 'e.g., Web Developer, Data Scientist', 'edugo-lms' ); ?>">
            </div>
        </div>

        <div class="edugo-form-group">
            <label for="description"><?php esc_html_e( 'Bio', 'edugo-lms' ); ?></label>
            <textarea id="description" name="description" rows="5" class="edugo-textarea"><?php echo esc_textarea( $user->description ); ?></textarea>
            <p class="edugo-form-note"><?php esc_html_e( 'Write a short bio about yourself. This will be displayed on your instructor profile and course pages.', 'edugo-lms' ); ?></p>
        </div>

        <h3><?php esc_html_e( 'Links & Social', 'edugo-lms' ); ?></h3>

        <div class="edugo-form-group">
            <label for="website"><?php esc_html_e( 'Website', 'edugo-lms' ); ?></label>
            <input type="url" id="website" name="website" value="<?php echo esc_attr( $website ); ?>" class="edugo-input" placeholder="https://">
        </div>

        <div class="edugo-form-row">
            <div class="edugo-form-group">
                <label for="social_facebook"><?php esc_html_e( 'Facebook', 'edugo-lms' ); ?></label>
                <input type="url" id="social_facebook" name="social_facebook" value="<?php echo esc_attr( $social_facebook ); ?>" class="edugo-input" placeholder="https://facebook.com/...">
            </div>

            <div class="edugo-form-group">
                <label for="social_twitter"><?php esc_html_e( 'Twitter / X', 'edugo-lms' ); ?></label>
                <input type="url" id="social_twitter" name="social_twitter" value="<?php echo esc_attr( $social_twitter ); ?>" class="edugo-input" placeholder="https://twitter.com/...">
            </div>
        </div>

        <div class="edugo-form-group">
            <label for="social_linkedin"><?php esc_html_e( 'LinkedIn', 'edugo-lms' ); ?></label>
            <input type="url" id="social_linkedin" name="social_linkedin" value="<?php echo esc_attr( $social_linkedin ); ?>" class="edugo-input" placeholder="https://linkedin.com/in/...">
        </div>

        <div class="edugo-form-actions">
            <button type="submit" name="edugo_update_instructor_profile" class="edugo-button edugo-button-primary">
                <?php esc_html_e( 'Update Profile', 'edugo-lms' ); ?>
            </button>
        </div>
    </form>
</div>
