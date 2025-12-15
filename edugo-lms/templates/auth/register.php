<?php
/**
 * Registration Form Template.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$role = $atts['role'] ?? 'edugo_student';
$redirect = $atts['redirect'] ?? '';

$errors = array();
$success = false;

// Handle form submission.
if ( isset( $_POST['edugo_register'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'edugo_register' ) ) {
    $username = sanitize_user( wp_unslash( $_POST['username'] ?? '' ) );
    $email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $first_name = sanitize_text_field( wp_unslash( $_POST['first_name'] ?? '' ) );
    $last_name = sanitize_text_field( wp_unslash( $_POST['last_name'] ?? '' ) );

    // Validate.
    if ( empty( $username ) ) {
        $errors[] = __( 'Please enter a username.', 'edugo-lms' );
    } elseif ( username_exists( $username ) ) {
        $errors[] = __( 'This username is already taken.', 'edugo-lms' );
    }

    if ( empty( $email ) || ! is_email( $email ) ) {
        $errors[] = __( 'Please enter a valid email address.', 'edugo-lms' );
    } elseif ( email_exists( $email ) ) {
        $errors[] = __( 'This email is already registered.', 'edugo-lms' );
    }

    if ( empty( $password ) ) {
        $errors[] = __( 'Please enter a password.', 'edugo-lms' );
    } elseif ( strlen( $password ) < 6 ) {
        $errors[] = __( 'Password must be at least 6 characters.', 'edugo-lms' );
    } elseif ( $password !== $password_confirm ) {
        $errors[] = __( 'Passwords do not match.', 'edugo-lms' );
    }

    if ( empty( $errors ) ) {
        $user_id = wp_create_user( $username, $password, $email );

        if ( is_wp_error( $user_id ) ) {
            $errors[] = $user_id->get_error_message();
        } else {
            // Update user meta.
            wp_update_user( array(
                'ID'         => $user_id,
                'first_name' => $first_name,
                'last_name'  => $last_name,
            ) );

            // Set role.
            $user = new \WP_User( $user_id );
            $user->set_role( $role );

            // Auto-login.
            wp_set_current_user( $user_id );
            wp_set_auth_cookie( $user_id );

            /**
             * Fires after a new user registers through Edugo LMS.
             *
             * @since 1.0.0
             * @param int    $user_id The new user ID.
             * @param string $role    The user role.
             */
            do_action( 'edugo_user_registered', $user_id, $role );

            $success = true;

            // Redirect.
            if ( $redirect ) {
                wp_safe_redirect( $redirect );
                exit;
            }
        }
    }
}
?>

<div class="edugo-auth-form edugo-register-form">
    <div class="edugo-auth-header">
        <h2><?php esc_html_e( 'Create Account', 'edugo-lms' ); ?></h2>
        <p><?php esc_html_e( 'Join our learning community today.', 'edugo-lms' ); ?></p>
    </div>

    <?php if ( $success ) : ?>
        <div class="edugo-notice edugo-notice-success">
            <p><?php esc_html_e( 'Registration successful! Welcome to our platform.', 'edugo-lms' ); ?></p>
            <?php
            $dashboard_page = get_option( 'edugo_student_dashboard_page' );
            if ( $dashboard_page ) :
                ?>
                <a href="<?php echo esc_url( get_permalink( $dashboard_page ) ); ?>" class="edugo-button edugo-button-primary">
                    <?php esc_html_e( 'Go to Dashboard', 'edugo-lms' ); ?>
                </a>
            <?php endif; ?>
        </div>
    <?php else : ?>
        <?php if ( ! empty( $errors ) ) : ?>
            <div class="edugo-notice edugo-notice-error">
                <?php foreach ( $errors as $error ) : ?>
                    <p><?php echo esc_html( $error ); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="edugo-form">
            <?php wp_nonce_field( 'edugo_register' ); ?>

            <div class="edugo-form-row">
                <div class="edugo-form-group">
                    <label for="first_name"><?php esc_html_e( 'First Name', 'edugo-lms' ); ?></label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo esc_attr( $_POST['first_name'] ?? '' ); ?>" class="edugo-input">
                </div>

                <div class="edugo-form-group">
                    <label for="last_name"><?php esc_html_e( 'Last Name', 'edugo-lms' ); ?></label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo esc_attr( $_POST['last_name'] ?? '' ); ?>" class="edugo-input">
                </div>
            </div>

            <div class="edugo-form-group">
                <label for="username"><?php esc_html_e( 'Username', 'edugo-lms' ); ?> <span class="required">*</span></label>
                <input type="text" id="username" name="username" value="<?php echo esc_attr( $_POST['username'] ?? '' ); ?>" class="edugo-input" required>
            </div>

            <div class="edugo-form-group">
                <label for="email"><?php esc_html_e( 'Email Address', 'edugo-lms' ); ?> <span class="required">*</span></label>
                <input type="email" id="email" name="email" value="<?php echo esc_attr( $_POST['email'] ?? '' ); ?>" class="edugo-input" required>
            </div>

            <div class="edugo-form-group">
                <label for="password"><?php esc_html_e( 'Password', 'edugo-lms' ); ?> <span class="required">*</span></label>
                <input type="password" id="password" name="password" class="edugo-input" required>
            </div>

            <div class="edugo-form-group">
                <label for="password_confirm"><?php esc_html_e( 'Confirm Password', 'edugo-lms' ); ?> <span class="required">*</span></label>
                <input type="password" id="password_confirm" name="password_confirm" class="edugo-input" required>
            </div>

            <div class="edugo-form-group">
                <label class="edugo-checkbox">
                    <input type="checkbox" name="terms" required>
                    <span>
                        <?php
                        printf(
                            /* translators: %s: Terms and conditions link */
                            esc_html__( 'I agree to the %s', 'edugo-lms' ),
                            '<a href="#">' . esc_html__( 'Terms and Conditions', 'edugo-lms' ) . '</a>'
                        );
                        ?>
                    </span>
                </label>
            </div>

            <div class="edugo-form-actions">
                <button type="submit" name="edugo_register" class="edugo-button edugo-button-primary edugo-button-block">
                    <?php esc_html_e( 'Create Account', 'edugo-lms' ); ?>
                </button>
            </div>
        </form>

        <div class="edugo-auth-footer">
            <p>
                <?php esc_html_e( 'Already have an account?', 'edugo-lms' ); ?>
                <?php
                $login_page = get_option( 'edugo_login_page' );
                $login_url = $login_page ? get_permalink( $login_page ) : wp_login_url();
                ?>
                <a href="<?php echo esc_url( $login_url ); ?>"><?php esc_html_e( 'Sign in', 'edugo-lms' ); ?></a>
            </p>
        </div>
    <?php endif; ?>
</div>
