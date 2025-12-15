<?php

if ( ! class_exists( 'Brainforward_Functions' ) ) {
    class Brainforward_Functions {

        public function __construct() {
            add_filter('wp_kses_allowed_html', [$this, 'kses_svg_allowed'], 10, 2);
            add_filter('body_class', [$this, 'body_classes']);
            add_action('wp_head', [$this, 'pingback_header']);
            add_action('wp_footer', [$this, 'footer_contents']);
            add_filter('comment_form_fields', [$this, 'move_comment_field_to_bottom']);
            add_filter('wp_list_categories', [$this, 'cat_count_span']);
            add_filter('get_archives_link', [$this, 'archive_count_span']);
            add_action('init', [$this, 'register_block_styles']);
            add_action('init', [$this, 'register_block_patterns']);

            add_action( 'wp_ajax_brainforward_send_otp', [ $this, 'send_otp' ] );
            add_action( 'wp_ajax_nopriv_brainforward_send_otp', [ $this, 'send_otp' ] );
            add_action( 'wp_ajax_brainforward_verify_otp', [ $this, 'verify_otp' ] );
            add_action( 'wp_ajax_nopriv_brainforward_verify_otp', [ $this, 'verify_otp' ] );
            add_action( 'wp_ajax_brainforward_password_login', [ $this, 'password_login' ] );
            add_action( 'wp_ajax_nopriv_brainforward_password_login', [ $this, 'password_login' ] );
            add_action( 'wp_ajax_brainforward_forgot_password', [ $this, 'forgot_password' ] );
            add_action( 'wp_ajax_nopriv_brainforward_forgot_password', [ $this, 'forgot_password' ] );

            add_action('woocommerce_before_checkout_process', [$this, 'create_or_login_user_before_checkout']);

            add_action('woocommerce_after_checkout_billing_form', [$this, 'after_checkout_billing_form'] );
            
            add_filter('wc_add_to_cart_message_html', '__return_false');

        }

        public function after_checkout_billing_form($checkout) {
            if (is_user_logged_in()) return;
            $message = __('Please enter your correct phone number. We will send your Username and Password to this phone number.', 'brainforward');
            echo '<p class="woocommerce-info" style="margin-top:10px;">' . esc_html($message) . '</p>';
        }
        
        
        public function create_or_login_user_before_checkout() {
            if (is_user_logged_in()) return;

            $phone = isset($_POST['billing_phone']) ? sanitize_text_field($_POST['billing_phone']) : '';
            $email = isset($_POST['billing_email']) ? sanitize_email($_POST['billing_email']) : '';
            $fname = isset($_POST['billing_first_name']) ? sanitize_text_field($_POST['billing_first_name']) : '';
            $lname = isset($_POST['billing_last_name']) ? sanitize_text_field($_POST['billing_last_name']) : '';

            if (empty($phone)) return;

            // Normalize phone
            if (strlen($phone) === 11 && strpos($phone, '88') !== 0) {
                $phone = '88' . $phone;
            }

            // Generate fallback email if missing
            if (empty($email)) {
                $email = preg_replace('/\D/', '', $phone) . '@brainforward.com.bd';
            }

            if (email_exists($email)) {
                // Email exists, log in user
                $user = get_user_by('email', $email);
                if ($user) {
                    wp_set_current_user($user->ID);
                    wp_set_auth_cookie($user->ID);
                }
            } else {
                // Create new user
                $username = sanitize_user(current(explode('@', $email)));
                $password = wp_generate_password(12, false);
                $user_id = wp_create_user($username, $password, $email);

                if (!is_wp_error($user_id)) {
                    update_user_meta($user_id, 'phone_number', $phone);
                    update_user_meta($user_id, 'billing_phone', $phone);
                    update_user_meta($user_id, 'billing_first_name', $fname);
                    update_user_meta($user_id, 'billing_last_name', $lname);

                    // Send SMS with credentials
                    $message = "BrainForward-এ আপনাকে স্বাগতম!\nইউজারনেম: {$username}\nপাসওয়ার্ড: {$password}";
                    $this->send_sms($phone, $message);

                    // Log in the new user
                    wp_set_current_user($user_id);
                    wp_set_auth_cookie($user_id);
                }
            }
        }



        public function forgot_password() {
            // AJAX nonce যাচাই
            check_ajax_referer('brainforward_login_nonce', 'nonce');

            $mobile = sanitize_text_field($_POST['mobile_no'] ?? '');

            if (empty($mobile)) {
                wp_send_json_error([
                    'message' => 'অনুগ্রহ করে আপনার মোবাইল নম্বর লিখুন।',
                    'number_exists' => false
                ]);
            }

            // মোবাইল normalize
            if (strlen($mobile) === 11 && strpos($mobile, '88') !== 0) {
                $mobile = '88' . $mobile;
            }

            $mobile_hash = md5($mobile);

            // ❌ ব্লক চেক (স্ট্যান্ডার্ড টাইম UTC)
            $blocked_until = get_transient('bf_forgot_block_' . $mobile_hash);
            $current_time = time();
            if ($blocked_until && $current_time < $blocked_until) {
                $remaining = human_time_diff($current_time, $blocked_until);
                wp_send_json_error([
                    'message' => "আপনি OTP অনেকবার চেষ্টা করেছেন। অনুগ্রহ করে {$remaining} পরে পুনরায় চেষ্টা করুন।"
                ]);
            }

            // ✅ ব্যবহারকারী চেক
            $existing_users = get_users([
                'meta_key'   => 'phone_number',
                'meta_value' => $mobile,
                'number'     => 1,
                'fields'     => 'ID'
            ]);

            if (empty($existing_users)) {
                wp_send_json_error([
                    'message' => 'এই মোবাইল নম্বরটি রেজিস্ট্রেশন করা হয়নি। অনুগ্রহ করে রেজিস্ট্রেশন করে নিন।',
                    'user_registered' => false
                ]);
            }

            // 🔢 OTP send attempts check
            $attempts = (int) get_transient('bf_forgot_count_' . $mobile_hash);
            if ($attempts >= 3) {
                // ⚠️ ২৪ ঘণ্টার জন্য ব্লক
                $block_time = $current_time + 24 * HOUR_IN_SECONDS;
                set_transient('bf_forgot_block_' . $mobile_hash, $block_time, 24 * HOUR_IN_SECONDS);
                delete_transient('bf_forgot_count_' . $mobile_hash);

                wp_send_json_error([
                    'message' => 'আপনি সর্বোচ্চ ৩ বার OTP পাঠিয়েছেন। আপনার মোবাইল ২৪ ঘণ্টার জন্য ব্লক করা হয়েছে।'
                ]);
            }

            // 🔧 OTP তৈরি এবং ৫ মিনিটের জন্য save
            $otp = wp_rand(100000, 999999);
            set_transient('bf_otp_' . $mobile_hash, $otp, 5 * MINUTE_IN_SECONDS);

            $api_url   = 'https://smsplus.sslwireless.com/api/v3/send-sms';
            $api_token = get_theme_mod('sms_api_token', '');
            $sid       = get_theme_mod('sms_sender_id', '');

            if (empty($api_token) || empty($sid)) {
                wp_send_json_error(['message' => 'SMS API কনফিগার করা হয়নি। অনুগ্রহ করে অ্যাডমিনের সাথে যোগাযোগ করুন।']);
            }

            $csms_id   = 'otp_' . substr(md5(uniqid(wp_rand(), true)), 0, 12);
            $message   = "আপনার BrainForward OTP হলো: {$otp}";

            $body = [
                'api_token' => $api_token,
                'sid'       => $sid,
                'msisdn'    => $mobile,
                'sms'       => $message,
                'csms_id'   => $csms_id,
            ];

            $response = wp_remote_post($api_url, [
                'headers' => ['Content-Type' => 'application/json'],
                'body'    => wp_json_encode($body),
                'timeout' => 20,
            ]);

            if (is_wp_error($response)) {
                wp_send_json_error(['message' => 'SMS গেটওয়ে সংযোগ করা যায়নি। অনুগ্রহ করে আবার চেষ্টা করুন।']);
            }

            $data = json_decode(wp_remote_retrieve_body($response), true);

            if (!empty($data['status_code']) && (int)$data['status_code'] === 200) {
                // ✅ send attempts increment
                set_transient('bf_forgot_count_' . $mobile_hash, $attempts + 1, 24 * HOUR_IN_SECONDS);

                wp_send_json_success(['message' => 'ওটিপি সফলভাবে পাঠানো হয়েছে!']);
            } else {
                wp_send_json_error([
                    'message' => 'ওটিপি পাঠানো ব্যর্থ হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।',
                    'api_response' => $data
                ]);
            }
        }

        /**
         * SSL Wireless এর মাধ্যমে OTP পাঠানো (সর্বোচ্চ ৩ বার, পরে ২৪ ঘন্টার ব্লক)
         */
        public function send_otp() {
            check_ajax_referer('brainforward_login_nonce', 'nonce');

            $mobile = sanitize_text_field($_POST['mobile_no'] ?? '');

            if (empty($mobile)) {
                wp_send_json_error(['message' => 'অনুগ্রহ করে আপনার মোবাইল নম্বর লিখুন।']);
            }

            // মোবাইল normalize
            if (strlen($mobile) === 11) {
                $mobile = '88' . $mobile;
            }

            // ✅ চেক যদি মোবাইল নম্বর ইতিমধ্যেই ব্যবহারকারীর মধ্যে থাকে
            $existing_users = get_users([
                'meta_key'   => 'phone_number',
                'meta_value' => $mobile,
                'number'     => 1,
                'fields'     => 'ID'
            ]);

            if (!empty($existing_users)) {
                wp_send_json_success([
                    'exists'  => true,
                    'message' => 'এই মোবাইল নম্বরটি ইতিমধ্যে রেজিস্ট্রেশন হয়েছে। অনুগ্রহ করে পাসওয়ার্ড দিয়ে লগইন করুন।'
                ]);
            }
            
            $mobile_hash = md5($mobile);

            // ❌ ব্লক চেক (স্ট্যান্ডার্ড টাইম UTC)
            $blocked_until = get_transient('bf_otp_block_' . $mobile_hash);
            $current_time = time(); // server time (Unix timestamp)
            if ($blocked_until && $current_time < $blocked_until) {
                $remaining = human_time_diff($current_time, $blocked_until);
                wp_send_json_error([
                    'message' => "আপনি OTP অনেকবার চেষ্টা করেছেন। অনুগ্রহ করে {$remaining} পরে পুনরায় চেষ্টা করুন।"
                ]);
            }

            // 🔢 OTP send attempts check
            $attempts = (int) get_transient('bf_otp_count_' . $mobile_hash);
            if ($attempts >= 3) {
                // ⚠️ ব্লক ২৪ ঘণ্টার জন্য (UTC time)
                $block_time = $current_time + 24 * HOUR_IN_SECONDS;
                set_transient('bf_otp_block_' . $mobile_hash, $block_time, 24 * HOUR_IN_SECONDS);
                delete_transient('bf_otp_count_' . $mobile_hash);

                wp_send_json_error([
                    'message' => 'আপনি সর্বোচ্চ ৩ বার OTP পাঠিয়েছেন। আপনার মোবাইল ২৪ ঘণ্টার জন্য ব্লক করা হয়েছে।'
                ]);
            }

            // 🔧 OTP তৈরি এবং ৫ মিনিটের জন্য save
            $otp = wp_rand(100000, 999999);
            set_transient('bf_otp_' . $mobile_hash, $otp, 5 * MINUTE_IN_SECONDS);

            // 🔧 SSL Wireless SMS পাঠানো
            $api_url   = 'https://smsplus.sslwireless.com/api/v3/send-sms';
            $api_token = get_theme_mod('sms_api_token', '');
            $sid       = get_theme_mod('sms_sender_id', '');

            if (empty($api_token) || empty($sid)) {
                wp_send_json_error(['message' => 'SMS API কনফিগার করা হয়নি। অনুগ্রহ করে অ্যাডমিনের সাথে যোগাযোগ করুন।']);
            }

            $csms_id   = 'otp_' . substr(md5(uniqid(wp_rand(), true)), 0, 12);
            $message   = "আপনার BrainForward OTP হলো: {$otp}";

            $body = [
                'api_token' => $api_token,
                'sid'       => $sid,
                'msisdn'    => $mobile,
                'sms'       => $message,
                'csms_id'   => $csms_id,
            ];

            $response = wp_remote_post($api_url, [
                'headers' => ['Content-Type' => 'application/json'],
                'body'    => wp_json_encode($body),
                'timeout' => 30,
            ]);

            if (is_wp_error($response)) {
                wp_send_json_error(['message' => 'SMS গেটওয়ে সংযোগ করা যায়নি। অনুগ্রহ করে আবার চেষ্টা করুন।']);
            }

            $data = json_decode(wp_remote_retrieve_body($response), true);

            if (isset($data['status_code']) && $data['status_code'] == 200) {
                // ✅ send attempts increment
                set_transient('bf_otp_count_' . $mobile_hash, $attempts + 1, 24 * HOUR_IN_SECONDS);

                wp_send_json_success(['message' => 'OTP সফলভাবে পাঠানো হয়েছে।']);
            } else {
                wp_send_json_error(['message' => 'OTP পাঠানো ব্যর্থ হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।']);
            }
        }


        public function password_login() {
            // Check nonce
            if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'brainforward_login_nonce' ) ) {
                wp_send_json_error( [ 'message' => 'Invalid request.' ] );
            }

            // Sanitize input
            $mobile   = sanitize_text_field( $_POST['mobile'] ?? '' );
            $password = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : '';

            if ( empty( $mobile ) || empty( $password ) ) {
                wp_send_json_error( [ 'message' => 'মোবাইল নম্বর এবং পাসওয়ার্ড উভয়ই প্রয়োজন।' ] );
            }


            // মোবাইল নম্বর normalize করা (যদি 88 না থাকে)
            if (strlen($mobile) === 11) {
                $mobile = '88' . $mobile;
            }

            // ✅ চেক যদি মোবাইল নম্বর ইতিমধ্যেই ব্যবহারকারীর মধ্যে থাকে
            $existing_users = get_users([
                'meta_key'   => 'phone_number',
                'meta_value' => $mobile,
                'number'     => 1,
                'fields'     => 'ID'
            ]);

            if ( empty( $existing_users ) ) {
                wp_send_json_error( [ 'message' => 'এই মোবাইল নম্বরের কোন ব্যবহারকারী পাওয়া যায়নি।' ] );
            }

            $user_id = $existing_users[0];
            $user = get_userdata( $user_id );

            // Verify password
            if ( ! wp_check_password( $password, $user->user_pass, $user->ID ) ) {
                wp_send_json_error( [ 'message' => 'পাসওয়ার্ড ভুল হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।' ] );
            }

            // Login the user
            wp_set_current_user( $user->ID );
            wp_set_auth_cookie( $user->ID, true );

            wp_send_json_success( [ 'message' => 'ধন্যবাদ, আপনার পাসওয়ার্ড সঠিক হয়েছে। দয়া করে অপেক্ষা করুন।' ] );
        }

        /**
         * OTP যাচাই ও লগইন/রেজিস্টার
         */
        public function verify_otp() {
            check_ajax_referer('brainforward_login_nonce', 'nonce');

            $mobile = sanitize_text_field($_POST['mobile'] ?? '');
            $otp    = sanitize_text_field($_POST['otp'] ?? '');

            if (empty($mobile) || empty($otp)) {
                wp_send_json_error(['message' => 'মোবাইল নম্বর বা OTP অনুপস্থিত।']);
            }

            if (strlen($mobile) === 11 && strpos($mobile, '88') !== 0) {
                $mobile = '88' . $mobile;
            }

            $mobile_hash = md5($mobile);
            $current_time = time();

            // ❌ ব্লক চেক
            $blocked_until = get_transient('bf_otp_block_' . $mobile_hash);
            if ($blocked_until && $current_time < $blocked_until) {
                $remaining = human_time_diff($current_time, $blocked_until);
                wp_send_json_error([
                    'message' => "আপনি অনেকবার OTP ভুল দিয়েছেন। অনুগ্রহ করে {$remaining} পরে চেষ্টা করুন।"
                ]);
            }

            $stored_otp = get_transient('bf_otp_' . $mobile_hash);

            if (!$stored_otp) {
                wp_send_json_error(['message' => 'OTP মেয়াদ শেষ হয়েছে বা পাওয়া যায়নি।']);
            }

            if ((string) $otp !== (string) $stored_otp) {
                // ❌ ভুল OTP হলে ট্রাই ইনক্রিমেন্ট
                $attempts = (int) get_transient('bf_otp_attempts_' . $mobile_hash);
                $attempts++;
                set_transient('bf_otp_attempts_' . $mobile_hash, $attempts, 24 * HOUR_IN_SECONDS);

                if ($attempts >= 3) {
                    // ⚠️ ২৪ ঘণ্টা ব্লক
                    set_transient('bf_otp_block_' . $mobile_hash, $current_time + 24 * HOUR_IN_SECONDS, 24 * HOUR_IN_SECONDS);
                    delete_transient('bf_otp_attempts_' . $mobile_hash);
                    wp_send_json_error([
                        'message' => '৩ বার ভুল OTP দেওয়ার কারণে আপনার মোবাইল ২৪ ঘণ্টার জন্য ব্লক করা হয়েছে।'
                    ]);
                }

                wp_send_json_error(['message' => 'ভুল OTP। অনুগ্রহ করে আবার চেষ্টা করুন।']);
            }

            // ✅ সঠিক OTP হলে remove করা
            delete_transient('bf_otp_' . $mobile_hash);
            delete_transient('bf_otp_attempts_' . $mobile_hash);

            // ইউজার খোঁজা
            $existing_user = get_users([
                'meta_key'   => 'phone_number',
                'meta_value' => $mobile,
                'number'     => 1,
            ]);

            $is_new_user = false;
            $username = '';
            $password = '';

            $password = $this->generate_random_password();


            if (!empty($existing_user)) {
                // পুরাতন ইউজার
                $user_id = $existing_user[0]->ID;
                wp_set_password($password, $user_id);
                $username = $existing_user[0]->user_login;

                $message = "BrainForward-এ আপনাকে স্বাগতম! আপনার নতুন পাসওয়ার্ড: {$password}\nঅনুগ্রহ করে এটি সংরক্ষণ করে রাখুন।";
                $this->send_sms($mobile, $message);

            } else {
                // নতুন ইউজার
                $username = preg_replace('/\D/', '', $mobile);
                $email    = $username . '@brainforward.com.bd';
                $user_id = wp_create_user($username, $password, $email);

                if (is_wp_error($user_id)) {
                    wp_send_json_error(['message' => 'ইউজার অ্যাকাউন্ট তৈরি করতে সমস্যা হয়েছে।']);
                }

                update_user_meta($user_id, 'phone_number', $mobile);
                $is_new_user = true;

                $welcome_message = "BrainForward-এ আপনাকে স্বাগতম! \nআপনার ইউজারনেম: {$username}\nপাসওয়ার্ড: {$password}\nঅনুগ্রহ করে সংরক্ষণ করে রাখুন।";
                $this->send_sms($mobile, $welcome_message);
            }

            // ইউজার লগইন করানো
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id, true);

            wp_send_json_success([
                'message' => $is_new_user ? 'নিবন্ধন সফল হয়েছে।' : 'পাসওয়ার্ড পুনঃনির্মাণ সফল হয়েছে।'
            ]);
        }

        public function generate_random_password() {
            $words = [
                'Happy', 'Smart', 'Quick', 'Bright', 'Fast', 'Strong', 'Sharp', 'Brave', 'Cool', 'Fresh',
                'Kind', 'Bold', 'Sunny', 'Lucky', 'Calm', 'Wise', 'Neat', 'Clean', 'Nice', 'Jolly',
                'Lively', 'Gentle', 'Chill', 'Zesty', 'Spicy', 'Witty', 'Nifty', 'Zany', 'Perky', 'Cheer',
                'Snappy', 'Peppy', 'Breezy', 'Quirky', 'Perky', 'Wavy', 'Jazzy', 'Funky', 'Dandy', 'Sassy',
                'Noble', 'Glowy', 'Shiny', 'Smiley', 'Bouncy', 'Trusty', 'Zippy', 'Cranky', 'Feisty', 'Plucky',
                'Crafty', 'Grippy', 'Fizzy', 'Wiggly', 'Tidy', 'Silly', 'Goofy', 'Zappy', 'Loopy', 'Dreamy',
                'Swanky', 'Snug', 'Mellow', 'Giddy', 'Twisty', 'Chirpy', 'Spry', 'Chubby', 'Rusty', 'Cheeky',
                'Snoozy', 'Zippy', 'Clumsy', 'Spunky', 'Bubbly', 'Jumpy', 'Nerdy', 'Quaint', 'Slick', 'Perky',
                'Swifty', 'Crafty', 'Cranky', 'Witty', 'Speedy', 'Zesty', 'Tasty', 'Brainy', 'Cheery', 'Picky',
                'Greedy', 'Fluffy', 'Sparky', 'Slinky', 'Weird', 'Clever', 'Dizzy', 'Jazzy', 'Fuzzy', 'Noisy'
            ];

            $randomWord = $words[array_rand($words)];
            $number     = wp_rand(10, 99);

            return $randomWord . 'Brain@' . $number;
        }

        /**
         * সাধারণ SMS পাঠানোর ফাংশন (SSL Wireless)
         */
        private function send_sms($mobile, $message) {
            $api_url   = 'https://smsplus.sslwireless.com/api/v3/send-sms';
            $api_token = get_theme_mod('sms_api_token', '');
            $sid       = get_theme_mod('sms_sender_id', '');

            if (empty($api_token) || empty($sid)) {
                return false;
            }

            $csms_id = 'msg_' . substr(md5(uniqid(wp_rand(), true)), 0, 12);

            $body = [
                'api_token' => $api_token,
                'sid'       => $sid,
                'msisdn'    => $mobile,
                'sms'       => $message,
                'csms_id'   => $csms_id,
            ];

            $response = wp_remote_post($api_url, [
                'headers' => ['Content-Type' => 'application/json'],
                'body'    => wp_json_encode($body),
                'timeout' => 30,
            ]);

            if (is_wp_error($response)) {
                return false;
            }

            return true;
        }
        
        public function footer_contents() {
            echo '<div style="display: none;"><svg width="0" height="0"><filter id="filter"> <feTurbulence type="fractalNoise" baseFrequency=".01" numOctaves="6"></feTurbulence><feDisplacementMap in="SourceGraphic" scale="100"></feDisplacementMap></filter></svg></div>';
            $scrollup = get_theme_mod('scroll_up_settings', 'show');
            if ($scrollup !== 'hide') {
                echo '
                <div class="progress-wrap">
                    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102" aria-hidden="true">
                        <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
                    </svg>
                    <span class="icon"><i class="ri-arrow-up-long-line"></i></span>
                </div>';
            }
        }


        /*----------------------
        Get-Popular-Google-Fonts
        ----------------------*/
        public static function get_popular_google_fonts() {
            return array(
               'default' => 'Default',
               'Bebas Neue' => 'Bebas Neue',
               'Urbanist' => 'Urbanist',
               'Roboto' => 'Roboto',
               'Open Sans' => 'Open Sans',
               'Lato' => 'Lato',
               'Montserrat' => 'Montserrat',
               'Poppins' => 'Poppins',
               'Oswald' => 'Oswald',
               'Raleway' => 'Raleway',
               'Slabo 27px' => 'Slabo 27px',
               'Noto Sans' => 'Noto Sans',
               'Noto Serif' => 'Noto Serif',
               'Source Sans Pro' => 'Source Sans Pro',
               'Source Serif Pro' => 'Source Serif Pro',
               'Merriweather' => 'Merriweather',
               'Roboto Condensed' => 'Roboto Condensed',
               'Ubuntu' => 'Ubuntu',
               'Playfair Display' => 'Playfair Display',
               'Dancing Script' => 'Dancing Script',
               'Lora' => 'Lora',
               'Archivo' => 'Archivo',
               'Josefin Sans' => 'Josefin Sans',
               'Quicksand' => 'Quicksand',
               'Cabin' => 'Cabin',
               'Titillium Web' => 'Titillium Web',
               'Exo 2' => 'Exo 2',
               'Kumbh Sans' => 'Kumbh Sans',
               'Lexend Deca' => 'Lexend Deca',
               'Barlow' => 'Barlow',
               'Asap' => 'Asap',
               'Muli' => 'Muli',
               'Cormorant' => 'Cormorant',
               'Zilla Slab' => 'Zilla Slab',
               'Rock Salt' => 'Rock Salt',
               'Cinzel' => 'Cinzel',
               'Varela Round' => 'Varela Round',
               'Fjalla One' => 'Fjalla One',
               'Spectral' => 'Spectral',
               'Work Sans' => 'Work Sans',
               'Pangram' => 'Pangram',
               'Sanchez' => 'Sanchez',
               'Baskerville' => 'Baskerville',
               'Righteous' => 'Righteous',
               'Oxygen' => 'Oxygen',
               'Droid Sans' => 'Droid Sans',
               'PT Sans' => 'PT Sans',
               'PT Serif' => 'PT Serif',
               'Play' => 'Play',
               'Sacramento' => 'Sacramento',
               'Robo' => 'Robo',
               'Satisfy' => 'Satisfy',
               'Allerta' => 'Allerta',
               'Abel' => 'Abel',
               'Bitter' => 'Bitter',
               'Sacramento' => 'Sacramento',
               'Anton' => 'Anton',
               'Lobster' => 'Lobster',
               'Amatic SC' => 'Amatic SC',
               'Caveat' => 'Caveat',
               'Neuton' => 'Neuton',
               'Gloock' => 'Gloock',
               'Balsamiq Sans' => 'Balsamiq Sans',
               'Fira Sans' => 'Fira Sans',
               'Sanchez' => 'Sanchez',
               'Almendra' => 'Almendra',
               'Acme' => 'Acme',
               'Arimo' => 'Arimo',
               'Lato' => 'Lato',
               'Nanum Gothic' => 'Nanum Gothic',
               'Raleway Dots' => 'Raleway Dots',
               'Alfa Slab One' => 'Alfa Slab One',
               'Berkshire Swash' => 'Berkshire Swash',
               'Cinzel Decorative' => 'Cinzel Decorative',
               'Gloock' => 'Gloock',
               'Julius Sans One' => 'Julius Sans One',
               'Karla' => 'Karla',
               'Playfair Display SC' => 'Playfair Display SC',
               'Rufina' => 'Rufina',
               'Russo One' => 'Russo One',
               'Rufina' => 'Rufina',
               'Gentium Basic' => 'Gentium Basic',
               'Gloock' => 'Gloock',
            );
         }

        /*----------------------
        WP_Kses-SVG-Tags-Allowed
        -----------------------*/
        public function kses_svg_allowed($tags) {
            $args = array(
                'svg'   => array(
                    'class'           => true,
                    'aria-hidden'     => true,
                    'aria-labelledby' => true,
                    'role'            => true,
                    'xmlns'           => true,
                    'width'           => true,
                    'height'          => true,
                    'viewbox'         => true
                ),
                'g'     => array('fill' => true),
                'title' => array('title' => true),
                'path'  => array(
                    'd'    => true,
                    'fill' => true
                ),
                'use'   => array(
                    'xlink:href' => true,
                )
            );
            return array_merge($tags, $args);
        }

        /*----------------
        Add-Body-Class
        -----------------*/
        public function body_classes($classes) {
            $menu_format_globe = get_theme_mod('transparent_menu_setting', 'static');

            if($menu_format_globe == 'light'){
                $classes[] = 'transparent_menu light';
            }elseif($menu_format_globe == 'dark'){
                $classes[] = 'transparent_menu dark';
            }

            if (!is_singular()) {
                $classes[] = 'hfeed';
            }
            return $classes;
        }

        /*-------------------------------------------------------------------------------
        * Add a pingback url auto-discovery header for singularly identifiable articles.
        --------------------------------------------------------------------------------*/
        public function pingback_header() {
            if (is_singular() && pings_open()) {
                echo '<link rel="pingback" href="' . esc_url(get_bloginfo('pingback_url')) . '">';
            }
        }

        /*------------------------------------------
        Comment-Form-Field-Position-Change-Function 
        -------------------------------------------*/
        public function move_comment_field_to_bottom($fields) {
            $comment_field = $fields['comment'];
            unset($fields['comment']);
            $fields['comment'] = $comment_field;
            return $fields;
        }

        // Page-Title-Generated
        public static function page_title() {
            $blog_title = get_theme_mod('blog_list_page_title', __('Blog List', 'brainforward'));
            $data = '';

            if (is_home()) {
                $data .= esc_html($blog_title);
            } elseif (is_single() && 'post' == get_post_type()) {
                $data .= get_the_title();
            } elseif (is_single() && 'event' == get_post_type()) {
                $data .= esc_html__('Event', 'brainforward');
            } elseif (is_single()) {
                $data .= get_the_title();
            } elseif (is_search()) {
                $data .= __('Search', 'brainforward') . ' : <span class="search_select">' . esc_html(get_search_query()) . '</span>';
            } elseif (is_archive()) {
                if (class_exists('WooCommerce') && is_shop()) {
                    $data .= woocommerce_page_title(false);
                } elseif ('courses' == get_post_type()) {
                    $data .= esc_html__('Courses', 'brainforward');
                } else {
                    $data .= get_the_archive_title('', '');
                }
            } elseif (class_exists('WooCommerce') && is_woocommerce()) {
                if (is_shop()) {
                    $data .= esc_html__('Shop Page', 'brainforward');
                } else {
                    $data .= woocommerce_page_title(false);
                }
            } elseif (is_404()) {
                $data .= esc_html__('Error Page', 'brainforward');
            } else {
                $data .= single_post_title('', false);
            }

            return empty($data) ? false : wp_kses($data, wp_kses_allowed_html('post'));
        }

        /*----- Post_Thumbnail_Function -----*/
        public static function post_thumbnail($thumb_size = 'large') {
            if (post_password_required() || is_attachment() || !has_post_thumbnail()) {
                return;
            }
            if (is_single()) {
                printf('<figure class="post-media">%1$s</figure>', get_the_post_thumbnail('', $thumb_size));
            } else {
                printf('<figure class="post-media"><a href="%1$s" aria-hidden="true">%2$s</a></figure>', get_the_permalink(), get_the_post_thumbnail('', $thumb_size));
            }
        }

        /*----- Post_Date_Function_Modify -----*/
        public static function get_post_date($format = 'j F, Y') {
            $time_string = sprintf(
                wp_kses('<time class="entry-date published updated" datetime="%1$s">%2$s</time>', wp_kses_allowed_html('post')),
                esc_attr(get_the_date('c')),
                esc_html(get_the_date($format))
            );
            if (get_the_date('Y/m/d')) {
                return '<a href="' . esc_url(get_day_link(get_the_date('Y'), get_the_date('m'), get_the_date('d'))) . '">' . $time_string . '</a>';
            }
            return false;
        }

        /*----- Post_Comments_Function_Modify -----*/
        public static function get_comment_count() {
            if (!post_password_required() && (comments_open() || get_comments_number()) && get_comments_number() > 0) {
                return get_comments_number_text(
                    esc_html__('No comment', 'brainforward'),
                    esc_html__('1 Comment', 'brainforward'),
                    /* translators: %s: number of comments */
                    sprintf(esc_html__('%s Comments', 'brainforward'), '%')
                );
            }
            return false;
        }

        // Post title array
        public static function get_post_title_array($postType = 'post') {
                
            $post_type_query = new WP_Query(array('post_type' => $postType, 'posts_per_page' => -1));

            $post_title_array = array();

            if ($post_type_query->have_posts()) {
                $posts_array = $post_type_query->posts;
                $post_title_array = wp_list_pluck($posts_array, 'post_title', 'ID');
            }

            // Add "Default" as the first option
            $post_title_array = array('default' => esc_html__('Default', 'brainforward')) + $post_title_array;

            return $post_title_array;
            
        }

        /**
         * Filter the categories archive widget to add a span around post count
         */
        public function cat_count_span($links) {
            $links = str_replace('</a> (', '<span class="post-count">(', $links);
            $links = str_replace(')', ')</span></a>', $links);
            return $links;
        }

        /**
         * Filter the archives widget to add a span around post count
         */
        public function archive_count_span($links) {
            $links = str_replace('</a>&nbsp;(', '<span class="post-count">(', $links);
            $links = str_replace(')', ')</span></a>', $links);
            return $links;
        }

        public function register_block_styles() {
            register_block_style('core/paragraph', array(
                'name'  => 'highlighted',
                'label' => __('Highlighted', 'brainforward'),
            ));
            register_block_style('core/button', array(
                'name'  => 'outline',
                'label' => __('Outline', 'brainforward'),
            ));
        }

        public function register_block_patterns() {
            register_block_pattern('brainforward/hero-section', array(
                'title'   => __('Hero Section', 'brainforward'),
                'content' => '<!-- wp:group --><div class="wp-block-group"><!-- wp:heading --> <h2>' . __('Your Product Title', 'brainforward') . '</h2><!-- /wp:heading --></div><!-- /wp:group -->',
            ));
        }
    }
}

new Brainforward_Functions();


function sanitize_multiple_select($value) {
    if (is_array($value)) {
        return implode(';', array_map('sanitize_text_field', $value)); // Convert array to comma-separated string
    }
    return sanitize_text_field($value);
}