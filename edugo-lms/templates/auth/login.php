<?php
/**
 * Login Form Template.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$redirect = $atts['redirect'] ?? '';
if ( empty( $redirect ) ) {
    $redirect = get_permalink();
}

$error = '';
if ( isset( $_GET['login'] ) && $_GET['login'] === 'failed' ) {
    $error = __( 'Invalid username or password.', 'edugo-lms' );
}
?>

<div class="edugo-auth-form edugo-login-form">
    <div class="edugo-auth-header">
        <h2><?php esc_html_e( 'Sign In', 'edugo-lms' ); ?></h2>
        <p><?php esc_html_e( 'Welcome back! Please sign in to continue.', 'edugo-lms' ); ?></p>
    </div>

    <?php if ( $error ) : ?>
        <div class="edugo-notice edugo-notice-error">
            <p><?php echo esc_html( $error ); ?></p>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url( wp_login_url() ); ?>" class="edugo-form">
        <div class="edugo-form-group">
            <label for="user_login"><?php esc_html_e( 'Username or Email', 'edugo-lms' ); ?></label>
            <input type="text" id="user_login" name="log" class="edugo-input" required>
        </div>

        <div class="edugo-form-group">
            <label for="user_pass"><?php esc_html_e( 'Password', 'edugo-lms' ); ?></label>
            <input type="password" id="user_pass" name="pwd" class="edugo-input" required>
        </div>

        <div class="edugo-form-row edugo-form-row-flex">
            <label class="edugo-checkbox">
                <input type="checkbox" name="rememberme" value="forever">
                <span><?php esc_html_e( 'Remember me', 'edugo-lms' ); ?></span>
            </label>

            <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="edugo-link">
                <?php esc_html_e( 'Forgot password?', 'edugo-lms' ); ?>
            </a>
        </div>

        <input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect ); ?>">

        <div class="edugo-form-actions">
            <button type="submit" class="edugo-button edugo-button-primary edugo-button-block">
                <?php esc_html_e( 'Sign In', 'edugo-lms' ); ?>
            </button>
        </div>
    </form>

    <?php if ( get_option( 'users_can_register' ) ) : ?>
        <div class="edugo-auth-footer">
            <p>
                <?php esc_html_e( "Don't have an account?", 'edugo-lms' ); ?>
                <?php
                $register_page = get_option( 'edugo_register_page' );
                $register_url = $register_page ? get_permalink( $register_page ) : wp_registration_url();
                ?>
                <a href="<?php echo esc_url( $register_url ); ?>"><?php esc_html_e( 'Sign up', 'edugo-lms' ); ?></a>
            </p>
        </div>
    <?php endif; ?>
</div>
