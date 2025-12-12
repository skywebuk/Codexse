<?php
/**
 * SMS Gateway Class
 *
 * Handles communication with SSL Wireless SMS API
 *
 * @package SSL_SMS_Login_Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

class SSL_SMS_Gateway {

    /**
     * API URL
     */
    private $api_url = 'https://smsplus.sslwireless.com/api/v3/send-sms';

    /**
     * API Token
     */
    private $api_token;

    /**
     * Sender ID
     */
    private $sender_id;

    /**
     * Constructor
     */
    public function __construct() {
        $this->api_token = SSL_SMS_Login_Pro::get_option('api_token', '');
        $this->sender_id = SSL_SMS_Login_Pro::get_option('sender_id', '');

        // Register test SMS AJAX handler
        add_action('wp_ajax_ssl_sms_send_test', array($this, 'ajax_send_test_sms'));
    }

    /**
     * Check if API is configured
     */
    public function is_configured() {
        return !empty($this->api_token) && !empty($this->sender_id);
    }

    /**
     * Normalize mobile number
     */
    public function normalize_mobile($mobile) {
        // Remove any non-digit characters
        $mobile = preg_replace('/\D/', '', $mobile);

        // Add Bangladesh country code if not present
        if (strlen($mobile) === 11 && strpos($mobile, '88') !== 0) {
            $mobile = '88' . $mobile;
        }

        // If it starts with 0, replace with 880
        if (strlen($mobile) === 11 && strpos($mobile, '0') === 0) {
            $mobile = '88' . $mobile;
        }

        return $mobile;
    }

    /**
     * Validate mobile number
     */
    public function validate_mobile($mobile) {
        $mobile = $this->normalize_mobile($mobile);

        // Bangladesh mobile number format: 8801XXXXXXXXX (13 digits)
        if (preg_match('/^8801[3-9]\d{8}$/', $mobile)) {
            return $mobile;
        }

        return false;
    }

    /**
     * Send SMS
     */
    public function send_sms($mobile, $message, $type = 'general') {
        // Validate configuration
        if (!$this->is_configured()) {
            return array(
                'success' => false,
                'message' => __('SMS API is not configured.', 'ssl-sms-login-pro'),
                'error_code' => 'api_not_configured'
            );
        }

        // Normalize and validate mobile
        $mobile = $this->normalize_mobile($mobile);
        if (!$this->validate_mobile($mobile)) {
            return array(
                'success' => false,
                'message' => __('Invalid mobile number format.', 'ssl-sms-login-pro'),
                'error_code' => 'invalid_mobile'
            );
        }

        // Generate unique message ID
        $csms_id = $type . '_' . substr(md5(uniqid(wp_rand(), true)), 0, 12);

        // Prepare request body
        $body = array(
            'api_token' => $this->api_token,
            'sid' => $this->sender_id,
            'msisdn' => $mobile,
            'sms' => $message,
            'csms_id' => $csms_id,
        );

        // Send request
        $response = wp_remote_post($this->api_url, array(
            'headers' => array('Content-Type' => 'application/json'),
            'body' => wp_json_encode($body),
            'timeout' => 30,
        ));

        // Handle response
        if (is_wp_error($response)) {
            $this->log_sms($mobile, $type, 'failed', $response->get_error_message());
            return array(
                'success' => false,
                'message' => __('Failed to connect to SMS gateway.', 'ssl-sms-login-pro'),
                'error_code' => 'connection_error',
                'error_details' => $response->get_error_message()
            );
        }

        $response_body = wp_remote_retrieve_body($response);
        $response_data = json_decode($response_body, true);

        // Check response status
        if (isset($response_data['status_code']) && (int) $response_data['status_code'] === 200) {
            $this->log_sms($mobile, $type, 'success', $response_body);
            return array(
                'success' => true,
                'message' => __('SMS sent successfully.', 'ssl-sms-login-pro'),
                'csms_id' => $csms_id
            );
        }

        // Handle API errors
        $error_message = isset($response_data['error_message']) ? $response_data['error_message'] : __('Unknown error', 'ssl-sms-login-pro');
        $this->log_sms($mobile, $type, 'failed', $response_body);

        return array(
            'success' => false,
            'message' => $error_message,
            'error_code' => isset($response_data['status_code']) ? $response_data['status_code'] : 'unknown',
            'api_response' => $response_data
        );
    }

    /**
     * Send OTP
     */
    public function send_otp($mobile, $otp) {
        $expiry = SSL_SMS_Login_Pro::get_option('otp_expiry', 5);
        $template = SSL_SMS_Login_Pro::get_option('otp_message_template', __('Your OTP is: {otp}. Valid for {expiry} minutes.', 'ssl-sms-login-pro'));

        $message = str_replace(
            array('{otp}', '{expiry}'),
            array($otp, $expiry),
            $template
        );

        return $this->send_sms($mobile, $message, 'otp');
    }

    /**
     * Send welcome message
     */
    public function send_welcome($mobile, $username, $password) {
        $template = SSL_SMS_Login_Pro::get_option('welcome_message_template', __('Welcome! Your username: {username}, Password: {password}', 'ssl-sms-login-pro'));

        $message = str_replace(
            array('{username}', '{password}', '{site_name}'),
            array($username, $password, get_bloginfo('name')),
            $template
        );

        return $this->send_sms($mobile, $message, 'welcome');
    }

    /**
     * Send password reset message
     */
    public function send_password_reset($mobile, $username, $new_password) {
        $template = SSL_SMS_Login_Pro::get_option('password_reset_template', __('Your new password is: {password}', 'ssl-sms-login-pro'));

        $message = str_replace(
            array('{username}', '{password}'),
            array($username, $new_password),
            $template
        );

        return $this->send_sms($mobile, $message, 'password_reset');
    }

    /**
     * Log SMS
     */
    private function log_sms($mobile, $type, $status, $response) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ssl_sms_logs';

        $wpdb->insert(
            $table_name,
            array(
                'mobile_number' => $mobile,
                'message_type' => $type,
                'status' => $status,
                'api_response' => is_string($response) ? $response : wp_json_encode($response),
                'created_at' => current_time('mysql'),
            ),
            array('%s', '%s', '%s', '%s', '%s')
        );
    }

    /**
     * AJAX handler for test SMS
     */
    public function ajax_send_test_sms() {
        // Verify nonce
        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'] ?? '')), 'ssl_sms_test_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed.', 'ssl-sms-login-pro')));
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'ssl-sms-login-pro')));
        }

        $mobile = isset($_POST['mobile']) ? sanitize_text_field(wp_unslash($_POST['mobile'])) : '';

        if (empty($mobile)) {
            wp_send_json_error(array('message' => __('Mobile number is required.', 'ssl-sms-login-pro')));
        }

        // Refresh API credentials
        $this->api_token = SSL_SMS_Login_Pro::get_option('api_token', '');
        $this->sender_id = SSL_SMS_Login_Pro::get_option('sender_id', '');

        $test_message = sprintf(
            __('This is a test message from %s. Your SMS configuration is working!', 'ssl-sms-login-pro'),
            get_bloginfo('name')
        );

        $result = $this->send_sms($mobile, $test_message, 'test');

        if ($result['success']) {
            wp_send_json_success(array('message' => __('Test SMS sent successfully!', 'ssl-sms-login-pro')));
        } else {
            wp_send_json_error(array('message' => $result['message']));
        }
    }

    /**
     * Get SMS balance (if supported by API)
     */
    public function get_balance() {
        // SSL Wireless balance check endpoint
        $balance_url = 'https://smsplus.sslwireless.com/api/v3/balance';

        $response = wp_remote_post($balance_url, array(
            'headers' => array('Content-Type' => 'application/json'),
            'body' => wp_json_encode(array('api_token' => $this->api_token)),
            'timeout' => 15,
        ));

        if (is_wp_error($response)) {
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['balance'])) {
            return $body['balance'];
        }

        return false;
    }
}
