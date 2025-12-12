<?php
/**
 * Login Forms Class
 *
 * Handles shortcodes, forms, and AJAX handlers for login/registration
 *
 * @package SSL_SMS_Login
 */

if (!defined('ABSPATH')) {
    exit;
}

class SSL_SMS_Login_Forms {

    /**
     * OTP Handler instance
     */
    private $otp_handler;

    /**
     * Constructor
     */
    public function __construct($otp_handler) {
        $this->otp_handler = $otp_handler;

        // Register shortcodes
        add_shortcode('ssl_sms_login', array($this, 'shortcode_combined_form'));
        add_shortcode('ssl_sms_login_form', array($this, 'shortcode_login_form'));
        add_shortcode('ssl_sms_register_form', array($this, 'shortcode_register_form'));
        add_shortcode('ssl_sms_forgot_password', array($this, 'shortcode_forgot_password'));

        // Register AJAX handlers
        add_action('wp_ajax_ssl_sms_send_otp', array($this, 'ajax_send_otp'));
        add_action('wp_ajax_nopriv_ssl_sms_send_otp', array($this, 'ajax_send_otp'));

        add_action('wp_ajax_ssl_sms_verify_otp', array($this, 'ajax_verify_otp'));
        add_action('wp_ajax_nopriv_ssl_sms_verify_otp', array($this, 'ajax_verify_otp'));

        add_action('wp_ajax_ssl_sms_login', array($this, 'ajax_login'));
        add_action('wp_ajax_nopriv_ssl_sms_login', array($this, 'ajax_login'));

        add_action('wp_ajax_ssl_sms_register', array($this, 'ajax_register'));
        add_action('wp_ajax_nopriv_ssl_sms_register', array($this, 'ajax_register'));

        add_action('wp_ajax_ssl_sms_forgot_password', array($this, 'ajax_forgot_password'));
        add_action('wp_ajax_nopriv_ssl_sms_forgot_password', array($this, 'ajax_forgot_password'));

        add_action('wp_ajax_ssl_sms_password_login', array($this, 'ajax_password_login'));
        add_action('wp_ajax_nopriv_ssl_sms_password_login', array($this, 'ajax_password_login'));
    }

    /**
     * Combined login/register form shortcode
     */
    public function shortcode_combined_form($atts) {
        if (is_user_logged_in()) {
            return $this->get_logged_in_message();
        }

        $atts = shortcode_atts(array(
            'redirect' => '',
            'show_tabs' => 'yes',
        ), $atts);

        ob_start();
        $this->render_combined_form($atts);
        return ob_get_clean();
    }

    /**
     * Login form shortcode
     */
    public function shortcode_login_form($atts) {
        if (is_user_logged_in()) {
            return $this->get_logged_in_message();
        }

        $atts = shortcode_atts(array(
            'redirect' => '',
        ), $atts);

        ob_start();
        $this->render_login_form($atts);
        return ob_get_clean();
    }

    /**
     * Register form shortcode
     */
    public function shortcode_register_form($atts) {
        if (is_user_logged_in()) {
            return $this->get_logged_in_message();
        }

        if (SSL_SMS_Login::get_option('enable_registration', 'yes') !== 'yes') {
            return '<p class="ssl-sms-notice">' . esc_html__('Registration is currently disabled.', 'ssl-sms-login') . '</p>';
        }

        $atts = shortcode_atts(array(
            'redirect' => '',
        ), $atts);

        ob_start();
        $this->render_register_form($atts);
        return ob_get_clean();
    }

    /**
     * Forgot password form shortcode
     */
    public function shortcode_forgot_password($atts) {
        if (SSL_SMS_Login::get_option('enable_forgot_password', 'yes') !== 'yes') {
            return '<p class="ssl-sms-notice">' . esc_html__('Password reset via SMS is currently disabled.', 'ssl-sms-login') . '</p>';
        }

        $atts = shortcode_atts(array(
            'redirect' => '',
        ), $atts);

        ob_start();
        $this->render_forgot_password_form($atts);
        return ob_get_clean();
    }

    /**
     * Get logged in message
     */
    private function get_logged_in_message() {
        $current_user = wp_get_current_user();
        return sprintf(
            '<div class="ssl-sms-logged-in">
                <p>%s <strong>%s</strong></p>
                <a href="%s" class="ssl-sms-btn ssl-sms-btn-secondary">%s</a>
            </div>',
            esc_html__('You are logged in as', 'ssl-sms-login'),
            esc_html($current_user->display_name),
            esc_url(wp_logout_url(home_url())),
            esc_html__('Logout', 'ssl-sms-login')
        );
    }

    /**
     * Render combined form
     */
    private function render_combined_form($atts) {
        $enable_registration = SSL_SMS_Login::get_option('enable_registration', 'yes') === 'yes';
        ?>
        <div class="ssl-sms-form-wrapper" data-redirect="<?php echo esc_attr($atts['redirect']); ?>">
            <?php if ($atts['show_tabs'] === 'yes' && $enable_registration) : ?>
            <div class="ssl-sms-tabs">
                <button type="button" class="ssl-sms-tab active" data-tab="login">
                    <?php esc_html_e('Login', 'ssl-sms-login'); ?>
                </button>
                <button type="button" class="ssl-sms-tab" data-tab="register">
                    <?php esc_html_e('Register', 'ssl-sms-login'); ?>
                </button>
            </div>
            <?php endif; ?>

            <div class="ssl-sms-tab-content" id="ssl-sms-login-tab">
                <?php $this->render_login_form($atts); ?>
            </div>

            <?php if ($enable_registration) : ?>
            <div class="ssl-sms-tab-content" id="ssl-sms-register-tab" style="display:none;">
                <?php $this->render_register_form($atts); ?>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render login form
     */
    private function render_login_form($atts) {
        ?>
        <div class="ssl-sms-form ssl-sms-login-form" data-redirect="<?php echo esc_attr($atts['redirect']); ?>">
            <div class="ssl-sms-form-step ssl-sms-step-mobile active" data-step="1">
                <h3><?php esc_html_e('Login with Mobile', 'ssl-sms-login'); ?></h3>
                <div class="ssl-sms-form-group">
                    <label for="ssl-login-mobile"><?php esc_html_e('Mobile Number', 'ssl-sms-login'); ?></label>
                    <input type="tel" id="ssl-login-mobile" class="ssl-sms-input"
                           placeholder="<?php esc_attr_e('01XXXXXXXXX', 'ssl-sms-login'); ?>"
                           required />
                </div>

                <div class="ssl-sms-login-method">
                    <label class="ssl-sms-radio">
                        <input type="radio" name="login_method" value="otp" checked />
                        <span class="ssl-sms-radio-mark"></span>
                        <span class="ssl-sms-radio-label"><?php esc_html_e('Login with OTP', 'ssl-sms-login'); ?></span>
                    </label>
                    <label class="ssl-sms-radio">
                        <input type="radio" name="login_method" value="password" />
                        <span class="ssl-sms-radio-mark"></span>
                        <span class="ssl-sms-radio-label"><?php esc_html_e('Login with Password', 'ssl-sms-login'); ?></span>
                    </label>
                </div>

                <div class="ssl-sms-password-field" style="display:none;">
                    <div class="ssl-sms-form-group">
                        <label for="ssl-login-password"><?php esc_html_e('Password', 'ssl-sms-login'); ?></label>
                        <input type="password" id="ssl-login-password" class="ssl-sms-input" />
                    </div>
                </div>

                <div class="ssl-sms-message"></div>

                <button type="button" class="ssl-sms-btn ssl-sms-btn-primary ssl-sms-send-otp" data-purpose="login">
                    <?php esc_html_e('Send OTP', 'ssl-sms-login'); ?>
                </button>
                <button type="button" class="ssl-sms-btn ssl-sms-btn-primary ssl-sms-password-login" style="display:none;">
                    <?php esc_html_e('Login', 'ssl-sms-login'); ?>
                </button>

                <?php if (SSL_SMS_Login::get_option('enable_forgot_password', 'yes') === 'yes') : ?>
                <p class="ssl-sms-links">
                    <a href="#" class="ssl-sms-forgot-link"><?php esc_html_e('Forgot Password?', 'ssl-sms-login'); ?></a>
                </p>
                <?php endif; ?>
            </div>

            <div class="ssl-sms-form-step ssl-sms-step-otp" data-step="2">
                <h3><?php esc_html_e('Enter OTP', 'ssl-sms-login'); ?></h3>
                <p class="ssl-sms-otp-info"></p>
                <div class="ssl-sms-form-group">
                    <label for="ssl-login-otp"><?php esc_html_e('OTP Code', 'ssl-sms-login'); ?></label>
                    <input type="text" id="ssl-login-otp" class="ssl-sms-input ssl-sms-otp-input"
                           maxlength="6" pattern="[0-9]*" inputmode="numeric"
                           placeholder="______" required />
                </div>

                <div class="ssl-sms-message"></div>

                <button type="button" class="ssl-sms-btn ssl-sms-btn-primary ssl-sms-verify-otp" data-purpose="login">
                    <?php esc_html_e('Verify & Login', 'ssl-sms-login'); ?>
                </button>

                <p class="ssl-sms-links">
                    <button type="button" class="ssl-sms-link-btn ssl-sms-resend-otp" disabled>
                        <?php esc_html_e('Resend OTP', 'ssl-sms-login'); ?>
                    </button>
                    <span class="ssl-sms-timer"></span>
                </p>
                <p class="ssl-sms-links">
                    <a href="#" class="ssl-sms-back-link"><?php esc_html_e('&larr; Back', 'ssl-sms-login'); ?></a>
                </p>
            </div>

            <!-- Forgot Password Section -->
            <div class="ssl-sms-form-step ssl-sms-step-forgot" data-step="forgot">
                <h3><?php esc_html_e('Forgot Password', 'ssl-sms-login'); ?></h3>
                <div class="ssl-sms-form-group">
                    <label for="ssl-forgot-mobile"><?php esc_html_e('Mobile Number', 'ssl-sms-login'); ?></label>
                    <input type="tel" id="ssl-forgot-mobile" class="ssl-sms-input"
                           placeholder="<?php esc_attr_e('01XXXXXXXXX', 'ssl-sms-login'); ?>"
                           required />
                </div>

                <div class="ssl-sms-message"></div>

                <button type="button" class="ssl-sms-btn ssl-sms-btn-primary ssl-sms-send-otp" data-purpose="forgot_password">
                    <?php esc_html_e('Send OTP', 'ssl-sms-login'); ?>
                </button>

                <p class="ssl-sms-links">
                    <a href="#" class="ssl-sms-back-to-login"><?php esc_html_e('&larr; Back to Login', 'ssl-sms-login'); ?></a>
                </p>
            </div>

            <div class="ssl-sms-form-step ssl-sms-step-forgot-otp" data-step="forgot-otp">
                <h3><?php esc_html_e('Verify OTP', 'ssl-sms-login'); ?></h3>
                <p class="ssl-sms-otp-info"></p>
                <div class="ssl-sms-form-group">
                    <label for="ssl-forgot-otp"><?php esc_html_e('OTP Code', 'ssl-sms-login'); ?></label>
                    <input type="text" id="ssl-forgot-otp" class="ssl-sms-input ssl-sms-otp-input"
                           maxlength="6" pattern="[0-9]*" inputmode="numeric"
                           placeholder="______" required />
                </div>

                <div class="ssl-sms-message"></div>

                <button type="button" class="ssl-sms-btn ssl-sms-btn-primary ssl-sms-verify-forgot-otp">
                    <?php esc_html_e('Reset Password', 'ssl-sms-login'); ?>
                </button>

                <p class="ssl-sms-links">
                    <button type="button" class="ssl-sms-link-btn ssl-sms-resend-otp" data-purpose="forgot_password" disabled>
                        <?php esc_html_e('Resend OTP', 'ssl-sms-login'); ?>
                    </button>
                    <span class="ssl-sms-timer"></span>
                </p>
            </div>
        </div>
        <?php
    }

    /**
     * Render register form
     */
    private function render_register_form($atts) {
        ?>
        <div class="ssl-sms-form ssl-sms-register-form" data-redirect="<?php echo esc_attr($atts['redirect']); ?>">
            <div class="ssl-sms-form-step ssl-sms-step-mobile active" data-step="1">
                <h3><?php esc_html_e('Register with Mobile', 'ssl-sms-login'); ?></h3>
                <div class="ssl-sms-form-group">
                    <label for="ssl-register-mobile"><?php esc_html_e('Mobile Number', 'ssl-sms-login'); ?></label>
                    <input type="tel" id="ssl-register-mobile" class="ssl-sms-input"
                           placeholder="<?php esc_attr_e('01XXXXXXXXX', 'ssl-sms-login'); ?>"
                           required />
                </div>

                <div class="ssl-sms-message"></div>

                <button type="button" class="ssl-sms-btn ssl-sms-btn-primary ssl-sms-send-otp" data-purpose="register">
                    <?php esc_html_e('Send OTP', 'ssl-sms-login'); ?>
                </button>
            </div>

            <div class="ssl-sms-form-step ssl-sms-step-otp" data-step="2">
                <h3><?php esc_html_e('Verify Mobile', 'ssl-sms-login'); ?></h3>
                <p class="ssl-sms-otp-info"></p>
                <div class="ssl-sms-form-group">
                    <label for="ssl-register-otp"><?php esc_html_e('OTP Code', 'ssl-sms-login'); ?></label>
                    <input type="text" id="ssl-register-otp" class="ssl-sms-input ssl-sms-otp-input"
                           maxlength="6" pattern="[0-9]*" inputmode="numeric"
                           placeholder="______" required />
                </div>

                <div class="ssl-sms-message"></div>

                <button type="button" class="ssl-sms-btn ssl-sms-btn-primary ssl-sms-verify-otp" data-purpose="register">
                    <?php esc_html_e('Verify OTP', 'ssl-sms-login'); ?>
                </button>

                <p class="ssl-sms-links">
                    <button type="button" class="ssl-sms-link-btn ssl-sms-resend-otp" disabled>
                        <?php esc_html_e('Resend OTP', 'ssl-sms-login'); ?>
                    </button>
                    <span class="ssl-sms-timer"></span>
                </p>
                <p class="ssl-sms-links">
                    <a href="#" class="ssl-sms-back-link"><?php esc_html_e('&larr; Back', 'ssl-sms-login'); ?></a>
                </p>
            </div>

            <div class="ssl-sms-form-step ssl-sms-step-details" data-step="3">
                <h3><?php esc_html_e('Complete Registration', 'ssl-sms-login'); ?></h3>
                <div class="ssl-sms-form-group">
                    <label for="ssl-register-name"><?php esc_html_e('Full Name', 'ssl-sms-login'); ?></label>
                    <input type="text" id="ssl-register-name" class="ssl-sms-input" required />
                </div>
                <div class="ssl-sms-form-group">
                    <label for="ssl-register-email"><?php esc_html_e('Email (Optional)', 'ssl-sms-login'); ?></label>
                    <input type="email" id="ssl-register-email" class="ssl-sms-input" />
                </div>
                <div class="ssl-sms-form-group">
                    <label for="ssl-register-password"><?php esc_html_e('Password', 'ssl-sms-login'); ?></label>
                    <input type="password" id="ssl-register-password" class="ssl-sms-input" required />
                </div>

                <div class="ssl-sms-message"></div>

                <button type="button" class="ssl-sms-btn ssl-sms-btn-primary ssl-sms-complete-register">
                    <?php esc_html_e('Complete Registration', 'ssl-sms-login'); ?>
                </button>
            </div>
        </div>
        <?php
    }

    /**
     * Render forgot password form (standalone)
     */
    private function render_forgot_password_form($atts) {
        ?>
        <div class="ssl-sms-form ssl-sms-forgot-form" data-redirect="<?php echo esc_attr($atts['redirect']); ?>">
            <div class="ssl-sms-form-step active" data-step="1">
                <h3><?php esc_html_e('Reset Password', 'ssl-sms-login'); ?></h3>
                <div class="ssl-sms-form-group">
                    <label for="ssl-forgot-mobile"><?php esc_html_e('Mobile Number', 'ssl-sms-login'); ?></label>
                    <input type="tel" id="ssl-forgot-mobile" class="ssl-sms-input"
                           placeholder="<?php esc_attr_e('01XXXXXXXXX', 'ssl-sms-login'); ?>"
                           required />
                </div>

                <div class="ssl-sms-message"></div>

                <button type="button" class="ssl-sms-btn ssl-sms-btn-primary ssl-sms-send-otp" data-purpose="forgot_password">
                    <?php esc_html_e('Send OTP', 'ssl-sms-login'); ?>
                </button>
            </div>

            <div class="ssl-sms-form-step" data-step="2">
                <h3><?php esc_html_e('Enter OTP', 'ssl-sms-login'); ?></h3>
                <p class="ssl-sms-otp-info"></p>
                <div class="ssl-sms-form-group">
                    <label for="ssl-forgot-otp"><?php esc_html_e('OTP Code', 'ssl-sms-login'); ?></label>
                    <input type="text" id="ssl-forgot-otp" class="ssl-sms-input ssl-sms-otp-input"
                           maxlength="6" pattern="[0-9]*" inputmode="numeric"
                           placeholder="______" required />
                </div>

                <div class="ssl-sms-message"></div>

                <button type="button" class="ssl-sms-btn ssl-sms-btn-primary ssl-sms-verify-forgot-otp">
                    <?php esc_html_e('Reset Password', 'ssl-sms-login'); ?>
                </button>

                <p class="ssl-sms-links">
                    <button type="button" class="ssl-sms-link-btn ssl-sms-resend-otp" data-purpose="forgot_password" disabled>
                        <?php esc_html_e('Resend OTP', 'ssl-sms-login'); ?>
                    </button>
                    <span class="ssl-sms-timer"></span>
                </p>
            </div>
        </div>
        <?php
    }

    /**
     * AJAX: Send OTP
     */
    public function ajax_send_otp() {
        check_ajax_referer('ssl_sms_login_nonce', 'nonce');

        $mobile = isset($_POST['mobile']) ? sanitize_text_field(wp_unslash($_POST['mobile'])) : '';
        $purpose = isset($_POST['purpose']) ? sanitize_text_field(wp_unslash($_POST['purpose'])) : 'login';

        if (empty($mobile)) {
            wp_send_json_error(array('message' => __('Mobile number is required.', 'ssl-sms-login')));
        }

        // For registration, check if mobile already exists
        if ($purpose === 'register') {
            $existing_user = $this->get_user_by_mobile($mobile);
            if ($existing_user) {
                wp_send_json_error(array('message' => __('This mobile number is already registered.', 'ssl-sms-login')));
            }
        }

        // For login/forgot password, check if mobile exists
        if ($purpose === 'login' || $purpose === 'forgot_password') {
            $existing_user = $this->get_user_by_mobile($mobile);
            if (!$existing_user) {
                wp_send_json_error(array('message' => __('No account found with this mobile number.', 'ssl-sms-login')));
            }
        }

        $result = $this->otp_handler->send_otp($mobile, $purpose);

        if ($result['success']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }

    /**
     * AJAX: Verify OTP
     */
    public function ajax_verify_otp() {
        check_ajax_referer('ssl_sms_login_nonce', 'nonce');

        $mobile = isset($_POST['mobile']) ? sanitize_text_field(wp_unslash($_POST['mobile'])) : '';
        $otp = isset($_POST['otp']) ? sanitize_text_field(wp_unslash($_POST['otp'])) : '';
        $purpose = isset($_POST['purpose']) ? sanitize_text_field(wp_unslash($_POST['purpose'])) : 'login';

        if (empty($mobile) || empty($otp)) {
            wp_send_json_error(array('message' => __('Mobile and OTP are required.', 'ssl-sms-login')));
        }

        $result = $this->otp_handler->verify_otp($mobile, $otp, $purpose);

        if ($result['success']) {
            // Mark as verified for multi-step processes
            $this->otp_handler->mark_verified($result['mobile'], $purpose);

            // For login, actually log the user in
            if ($purpose === 'login') {
                $user = $this->get_user_by_mobile($result['mobile']);
                if ($user) {
                    $this->login_user($user);
                    $redirect = SSL_SMS_Login::get_option('redirect_after_login', '');
                    $result['redirect'] = !empty($redirect) ? $redirect : home_url();
                }
            }

            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }

    /**
     * AJAX: Complete registration
     */
    public function ajax_register() {
        check_ajax_referer('ssl_sms_login_nonce', 'nonce');

        $mobile = isset($_POST['mobile']) ? sanitize_text_field(wp_unslash($_POST['mobile'])) : '';
        $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
        $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
        $password = isset($_POST['password']) ? wp_unslash($_POST['password']) : '';

        // Validate
        if (empty($mobile) || empty($name) || empty($password)) {
            wp_send_json_error(array('message' => __('All required fields must be filled.', 'ssl-sms-login')));
        }

        // Check if OTP was verified
        if (!$this->otp_handler->is_otp_verified($mobile, 'register')) {
            wp_send_json_error(array('message' => __('Please verify your mobile number first.', 'ssl-sms-login')));
        }

        // Check if mobile already registered
        if ($this->get_user_by_mobile($mobile)) {
            wp_send_json_error(array('message' => __('This mobile number is already registered.', 'ssl-sms-login')));
        }

        // Generate username from mobile
        $sms_gateway = ssl_sms_login()->sms_gateway;
        $normalized_mobile = $sms_gateway->normalize_mobile($mobile);
        $username = 'user_' . substr($normalized_mobile, -8);

        // Make username unique
        $original_username = $username;
        $counter = 1;
        while (username_exists($username)) {
            $username = $original_username . '_' . $counter;
            $counter++;
        }

        // Create user
        $user_data = array(
            'user_login' => $username,
            'user_pass' => $password,
            'user_email' => $email,
            'display_name' => $name,
            'first_name' => $name,
            'role' => 'subscriber',
        );

        $user_id = wp_insert_user($user_data);

        if (is_wp_error($user_id)) {
            wp_send_json_error(array('message' => $user_id->get_error_message()));
        }

        // Store mobile number
        update_user_meta($user_id, 'mobile_number', $normalized_mobile);
        update_user_meta($user_id, 'mobile_verified', true);

        // Clear verified status
        $this->otp_handler->clear_verified($mobile);

        // Log user in
        $user = get_user_by('ID', $user_id);
        $this->login_user($user);

        // Send welcome SMS
        $sms_gateway->send_welcome($normalized_mobile, $username, $password);

        $redirect = SSL_SMS_Login::get_option('redirect_after_login', '');

        wp_send_json_success(array(
            'message' => __('Registration successful!', 'ssl-sms-login'),
            'redirect' => !empty($redirect) ? $redirect : home_url(),
        ));
    }

    /**
     * AJAX: Password login
     */
    public function ajax_password_login() {
        check_ajax_referer('ssl_sms_login_nonce', 'nonce');

        $mobile = isset($_POST['mobile']) ? sanitize_text_field(wp_unslash($_POST['mobile'])) : '';
        $password = isset($_POST['password']) ? wp_unslash($_POST['password']) : '';

        if (empty($mobile) || empty($password)) {
            wp_send_json_error(array('message' => __('Mobile and password are required.', 'ssl-sms-login')));
        }

        $user = $this->get_user_by_mobile($mobile);
        if (!$user) {
            wp_send_json_error(array('message' => __('No account found with this mobile number.', 'ssl-sms-login')));
        }

        // Verify password
        if (!wp_check_password($password, $user->user_pass, $user->ID)) {
            wp_send_json_error(array('message' => __('Incorrect password.', 'ssl-sms-login')));
        }

        // Log user in
        $this->login_user($user);

        $redirect = SSL_SMS_Login::get_option('redirect_after_login', '');

        wp_send_json_success(array(
            'message' => __('Login successful!', 'ssl-sms-login'),
            'redirect' => !empty($redirect) ? $redirect : home_url(),
        ));
    }

    /**
     * AJAX: Forgot password
     */
    public function ajax_forgot_password() {
        check_ajax_referer('ssl_sms_login_nonce', 'nonce');

        $mobile = isset($_POST['mobile']) ? sanitize_text_field(wp_unslash($_POST['mobile'])) : '';
        $otp = isset($_POST['otp']) ? sanitize_text_field(wp_unslash($_POST['otp'])) : '';

        if (empty($mobile) || empty($otp)) {
            wp_send_json_error(array('message' => __('Mobile and OTP are required.', 'ssl-sms-login')));
        }

        // Verify OTP
        $result = $this->otp_handler->verify_otp($mobile, $otp, 'forgot_password');

        if (!$result['success']) {
            wp_send_json_error($result);
        }

        // Get user
        $user = $this->get_user_by_mobile($result['mobile']);
        if (!$user) {
            wp_send_json_error(array('message' => __('No account found with this mobile number.', 'ssl-sms-login')));
        }

        // Generate new password
        $new_password = $this->generate_password();

        // Update password
        wp_set_password($new_password, $user->ID);

        // Send password via SMS
        $sms_gateway = ssl_sms_login()->sms_gateway;
        $sms_gateway->send_password_reset($result['mobile'], $user->user_login, $new_password);

        wp_send_json_success(array(
            'message' => __('New password has been sent to your mobile number.', 'ssl-sms-login'),
        ));
    }

    /**
     * Get user by mobile number
     */
    private function get_user_by_mobile($mobile) {
        $sms_gateway = ssl_sms_login()->sms_gateway;
        $normalized_mobile = $sms_gateway->normalize_mobile($mobile);

        $users = get_users(array(
            'meta_key' => 'mobile_number',
            'meta_value' => $normalized_mobile,
            'number' => 1,
        ));

        if (!empty($users)) {
            return $users[0];
        }

        // Also check without country code
        $short_mobile = substr($normalized_mobile, 2); // Remove '88' prefix
        $users = get_users(array(
            'meta_key' => 'mobile_number',
            'meta_value' => $short_mobile,
            'number' => 1,
        ));

        if (!empty($users)) {
            return $users[0];
        }

        return null;
    }

    /**
     * Log user in
     */
    private function login_user($user) {
        wp_clear_auth_cookie();
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, true);
        do_action('wp_login', $user->user_login, $user);
    }

    /**
     * Generate random password
     */
    private function generate_password($length = 8) {
        $words = array(
            'Happy', 'Lucky', 'Smart', 'Quick', 'Brave', 'Shiny', 'Jolly', 'Merry',
            'Swift', 'Bold', 'Cool', 'Fresh', 'Bright', 'Clear', 'Great', 'Super'
        );

        $word = $words[array_rand($words)];
        $number = wp_rand(10, 99);
        $special = array('@', '#', '$', '!')[wp_rand(0, 3)];

        return $word . $special . $number;
    }
}
