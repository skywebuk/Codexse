<?php
/**
 * OTP Handler Class
 *
 * Handles OTP generation, storage, verification, and rate limiting
 *
 * @package SSL_SMS_Login_Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

class SSL_SMS_OTP_Handler {

    /**
     * SMS Gateway instance
     */
    private $sms_gateway;

    /**
     * Transient prefix
     */
    private $transient_prefix = 'ssl_sms_';

    /**
     * Constructor
     */
    public function __construct($sms_gateway) {
        $this->sms_gateway = $sms_gateway;
    }

    /**
     * Generate OTP
     */
    public function generate_otp($length = 6) {
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        return wp_rand($min, $max);
    }

    /**
     * Get mobile hash for transient keys
     */
    private function get_mobile_hash($mobile) {
        return md5($this->sms_gateway->normalize_mobile($mobile));
    }

    /**
     * Check if mobile is blocked
     */
    public function is_blocked($mobile) {
        $mobile_hash = $this->get_mobile_hash($mobile);
        $blocked = get_transient($this->transient_prefix . 'blocked_' . $mobile_hash);
        return !empty($blocked);
    }

    /**
     * Get remaining attempts
     */
    public function get_remaining_attempts($mobile) {
        $mobile_hash = $this->get_mobile_hash($mobile);
        $max_attempts = SSL_SMS_Login_Pro::get_option('max_otp_attempts', 3);
        $current_attempts = get_transient($this->transient_prefix . 'attempts_' . $mobile_hash);

        if ($current_attempts === false) {
            return $max_attempts;
        }

        return max(0, $max_attempts - intval($current_attempts));
    }

    /**
     * Increment attempts counter
     */
    private function increment_attempts($mobile) {
        $mobile_hash = $this->get_mobile_hash($mobile);
        $max_attempts = SSL_SMS_Login_Pro::get_option('max_otp_attempts', 3);
        $block_duration = SSL_SMS_Login_Pro::get_option('block_duration', 24);
        $current_attempts = get_transient($this->transient_prefix . 'attempts_' . $mobile_hash);

        if ($current_attempts === false) {
            $current_attempts = 0;
        }

        $current_attempts = intval($current_attempts) + 1;

        // Store attempts for 24 hours
        set_transient(
            $this->transient_prefix . 'attempts_' . $mobile_hash,
            $current_attempts,
            $block_duration * HOUR_IN_SECONDS
        );

        // Block if max attempts reached
        if ($current_attempts >= $max_attempts) {
            set_transient(
                $this->transient_prefix . 'blocked_' . $mobile_hash,
                true,
                $block_duration * HOUR_IN_SECONDS
            );
            return false;
        }

        return true;
    }

    /**
     * Clear attempts
     */
    public function clear_attempts($mobile) {
        $mobile_hash = $this->get_mobile_hash($mobile);
        delete_transient($this->transient_prefix . 'attempts_' . $mobile_hash);
        delete_transient($this->transient_prefix . 'blocked_' . $mobile_hash);
    }

    /**
     * Store OTP
     */
    private function store_otp($mobile, $otp, $purpose = 'login') {
        $mobile_hash = $this->get_mobile_hash($mobile);
        $expiry = SSL_SMS_Login_Pro::get_option('otp_expiry', 5);

        $otp_data = array(
            'otp' => $otp,
            'purpose' => $purpose,
            'created_at' => time(),
            'verified' => false,
        );

        set_transient(
            $this->transient_prefix . 'otp_' . $mobile_hash,
            $otp_data,
            $expiry * MINUTE_IN_SECONDS
        );
    }

    /**
     * Get stored OTP
     */
    private function get_stored_otp($mobile) {
        $mobile_hash = $this->get_mobile_hash($mobile);
        return get_transient($this->transient_prefix . 'otp_' . $mobile_hash);
    }

    /**
     * Delete stored OTP
     */
    private function delete_otp($mobile) {
        $mobile_hash = $this->get_mobile_hash($mobile);
        delete_transient($this->transient_prefix . 'otp_' . $mobile_hash);
    }

    /**
     * Send OTP
     */
    public function send_otp($mobile, $purpose = 'login') {
        // Validate mobile
        $normalized_mobile = $this->sms_gateway->validate_mobile($mobile);
        if (!$normalized_mobile) {
            return array(
                'success' => false,
                'message' => __('Invalid mobile number format.', 'ssl-sms-login'),
                'error_code' => 'invalid_mobile'
            );
        }

        // Check if blocked
        if ($this->is_blocked($normalized_mobile)) {
            $block_duration = SSL_SMS_Login_Pro::get_option('block_duration', 24);
            return array(
                'success' => false,
                'message' => sprintf(
                    __('Too many attempts. Please try again after %d hours.', 'ssl-sms-login'),
                    $block_duration
                ),
                'error_code' => 'blocked'
            );
        }

        // Check remaining attempts
        $remaining = $this->get_remaining_attempts($normalized_mobile);
        if ($remaining <= 0) {
            return array(
                'success' => false,
                'message' => __('Maximum OTP attempts exceeded.', 'ssl-sms-login'),
                'error_code' => 'max_attempts'
            );
        }

        // Check cooldown (60 seconds between OTPs)
        $otp_data = $this->get_stored_otp($normalized_mobile);
        if ($otp_data && isset($otp_data['created_at'])) {
            $time_passed = time() - $otp_data['created_at'];
            if ($time_passed < 60) {
                $wait_time = 60 - $time_passed;
                return array(
                    'success' => false,
                    'message' => sprintf(
                        __('Please wait %d seconds before requesting a new OTP.', 'ssl-sms-login'),
                        $wait_time
                    ),
                    'error_code' => 'cooldown',
                    'wait_time' => $wait_time
                );
            }
        }

        // Generate and send OTP
        $otp = $this->generate_otp();
        $result = $this->sms_gateway->send_otp($normalized_mobile, $otp);

        if ($result['success']) {
            // Store OTP
            $this->store_otp($normalized_mobile, $otp, $purpose);

            // Increment attempts
            $this->increment_attempts($normalized_mobile);

            return array(
                'success' => true,
                'message' => __('OTP sent successfully.', 'ssl-sms-login'),
                'remaining_attempts' => $this->get_remaining_attempts($normalized_mobile),
                'expiry' => SSL_SMS_Login_Pro::get_option('otp_expiry', 5)
            );
        }

        return $result;
    }

    /**
     * Verify OTP
     */
    public function verify_otp($mobile, $otp, $purpose = 'login') {
        // Validate mobile
        $normalized_mobile = $this->sms_gateway->validate_mobile($mobile);
        if (!$normalized_mobile) {
            return array(
                'success' => false,
                'message' => __('Invalid mobile number.', 'ssl-sms-login'),
                'error_code' => 'invalid_mobile'
            );
        }

        // Get stored OTP
        $otp_data = $this->get_stored_otp($normalized_mobile);

        if (!$otp_data) {
            return array(
                'success' => false,
                'message' => __('OTP expired or not found. Please request a new one.', 'ssl-sms-login'),
                'error_code' => 'otp_expired'
            );
        }

        // Check purpose
        if ($otp_data['purpose'] !== $purpose) {
            return array(
                'success' => false,
                'message' => __('Invalid OTP for this operation.', 'ssl-sms-login'),
                'error_code' => 'wrong_purpose'
            );
        }

        // Verify OTP
        if (intval($otp_data['otp']) !== intval($otp)) {
            return array(
                'success' => false,
                'message' => __('Invalid OTP. Please try again.', 'ssl-sms-login'),
                'error_code' => 'invalid_otp'
            );
        }

        // Mark as verified and delete
        $this->delete_otp($normalized_mobile);

        // Clear attempts on successful verification
        $this->clear_attempts($normalized_mobile);

        return array(
            'success' => true,
            'message' => __('OTP verified successfully.', 'ssl-sms-login'),
            'mobile' => $normalized_mobile
        );
    }

    /**
     * Check if OTP is verified (for multi-step forms)
     */
    public function is_otp_verified($mobile, $purpose = 'login') {
        $mobile_hash = $this->get_mobile_hash($mobile);
        $verified = get_transient($this->transient_prefix . 'verified_' . $mobile_hash);

        if ($verified && isset($verified['purpose']) && $verified['purpose'] === $purpose) {
            return true;
        }

        return false;
    }

    /**
     * Mark mobile as verified (for multi-step processes)
     */
    public function mark_verified($mobile, $purpose = 'login') {
        $mobile_hash = $this->get_mobile_hash($mobile);

        set_transient(
            $this->transient_prefix . 'verified_' . $mobile_hash,
            array(
                'purpose' => $purpose,
                'verified_at' => time(),
            ),
            10 * MINUTE_IN_SECONDS // Valid for 10 minutes
        );
    }

    /**
     * Clear verified status
     */
    public function clear_verified($mobile) {
        $mobile_hash = $this->get_mobile_hash($mobile);
        delete_transient($this->transient_prefix . 'verified_' . $mobile_hash);
    }
}
