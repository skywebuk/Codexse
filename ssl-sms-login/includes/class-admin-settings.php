<?php
/**
 * Admin Settings Class
 *
 * @package SSL_SMS_Login
 */

if (!defined('ABSPATH')) {
    exit;
}

class SSL_SMS_Admin_Settings {

    /**
     * Settings page slug
     */
    private $page_slug = 'ssl-sms-login-settings';

    /**
     * Option name
     */
    private $option_name = 'ssl_sms_login_settings';

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_filter('plugin_action_links_' . SSL_SMS_PLUGIN_BASENAME, array($this, 'add_settings_link'));
        add_action('wp_ajax_ssl_sms_send_test', array($this, 'ajax_send_test'));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('SSL SMS Login', 'ssl-sms-login'),
            __('SMS Login', 'ssl-sms-login'),
            'manage_options',
            $this->page_slug,
            array($this, 'render_settings_page'),
            'dashicons-smartphone',
            80
        );

        add_submenu_page(
            $this->page_slug,
            __('Settings', 'ssl-sms-login'),
            __('Settings', 'ssl-sms-login'),
            'manage_options',
            $this->page_slug,
            array($this, 'render_settings_page')
        );

        add_submenu_page(
            $this->page_slug,
            __('SMS Logs', 'ssl-sms-login'),
            __('SMS Logs', 'ssl-sms-login'),
            'manage_options',
            'ssl-sms-login-logs',
            array($this, 'render_logs_page')
        );
    }

    /**
     * Add settings link to plugins page
     */
    public function add_settings_link($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=' . $this->page_slug) . '">' . __('Settings', 'ssl-sms-login') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting(
            'ssl_sms_login_settings_group',
            $this->option_name,
            array($this, 'sanitize_settings')
        );
    }

    /**
     * Sanitize settings
     */
    public function sanitize_settings($input) {
        $sanitized = array();

        // Text fields
        $text_fields = array('api_token', 'sender_id', 'redirect_after_login');
        foreach ($text_fields as $field) {
            $sanitized[$field] = isset($input[$field]) ? sanitize_text_field($input[$field]) : '';
        }

        // Number fields
        $number_fields = array(
            'otp_expiry' => array('min' => 1, 'max' => 30, 'default' => 5),
            'max_otp_attempts' => array('min' => 1, 'max' => 10, 'default' => 3),
            'block_duration' => array('min' => 1, 'max' => 72, 'default' => 24),
            'border_radius' => array('min' => 0, 'max' => 50, 'default' => 8),
        );

        foreach ($number_fields as $field => $constraints) {
            $value = isset($input[$field]) ? absint($input[$field]) : $constraints['default'];
            $sanitized[$field] = max($constraints['min'], min($constraints['max'], $value));
        }

        // Checkbox fields
        $checkbox_fields = array('enable_registration', 'enable_login', 'enable_forgot_password');
        foreach ($checkbox_fields as $field) {
            $sanitized[$field] = isset($input[$field]) ? 'yes' : 'no';
        }

        // Textarea fields
        $textarea_fields = array('otp_message_template', 'welcome_message_template', 'password_reset_template');
        foreach ($textarea_fields as $field) {
            $sanitized[$field] = isset($input[$field]) ? sanitize_textarea_field($input[$field]) : '';
        }

        // Color fields
        $color_fields = array('primary_color', 'secondary_color', 'text_color', 'success_color', 'error_color', 'button_text_color');
        foreach ($color_fields as $field) {
            $sanitized[$field] = isset($input[$field]) ? sanitize_hex_color($input[$field]) : '';
        }

        // Form style
        $sanitized['form_style'] = isset($input['form_style']) ? sanitize_text_field($input['form_style']) : 'modern';

        return $sanitized;
    }

    /**
     * Get option with default
     */
    private function get_option($key, $default = '') {
        $options = get_option($this->option_name, array());
        return isset($options[$key]) ? $options[$key] : $default;
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        ?>
        <div class="wrap ssl-sms-wrap">
            <!-- Header -->
            <div class="ssl-sms-header">
                <div class="ssl-sms-header-content">
                    <div class="ssl-sms-logo">
                        <span class="dashicons dashicons-smartphone"></span>
                        <h1><?php esc_html_e('SSL SMS Login', 'ssl-sms-login'); ?></h1>
                    </div>
                    <div class="ssl-sms-version">
                        <?php echo esc_html(sprintf(__('Version %s', 'ssl-sms-login'), SSL_SMS_VERSION)); ?>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="ssl-sms-nav">
                <a href="<?php echo esc_url(add_query_arg('tab', 'general')); ?>"
                   class="ssl-sms-nav-tab <?php echo $active_tab === 'general' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-admin-settings"></span>
                    <?php esc_html_e('General', 'ssl-sms-login'); ?>
                </a>
                <a href="<?php echo esc_url(add_query_arg('tab', 'otp')); ?>"
                   class="ssl-sms-nav-tab <?php echo $active_tab === 'otp' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-lock"></span>
                    <?php esc_html_e('OTP Settings', 'ssl-sms-login'); ?>
                </a>
                <a href="<?php echo esc_url(add_query_arg('tab', 'features')); ?>"
                   class="ssl-sms-nav-tab <?php echo $active_tab === 'features' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-admin-plugins"></span>
                    <?php esc_html_e('Features', 'ssl-sms-login'); ?>
                </a>
                <a href="<?php echo esc_url(add_query_arg('tab', 'messages')); ?>"
                   class="ssl-sms-nav-tab <?php echo $active_tab === 'messages' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-email"></span>
                    <?php esc_html_e('Messages', 'ssl-sms-login'); ?>
                </a>
                <a href="<?php echo esc_url(add_query_arg('tab', 'colors')); ?>"
                   class="ssl-sms-nav-tab <?php echo $active_tab === 'colors' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-art"></span>
                    <?php esc_html_e('Colors', 'ssl-sms-login'); ?>
                </a>
            </div>

            <!-- Content -->
            <div class="ssl-sms-content">
                <form action="options.php" method="post">
                    <?php settings_fields('ssl_sms_login_settings_group'); ?>

                    <?php if ($active_tab === 'general') : ?>
                        <?php $this->render_general_tab(); ?>
                    <?php elseif ($active_tab === 'otp') : ?>
                        <?php $this->render_otp_tab(); ?>
                    <?php elseif ($active_tab === 'features') : ?>
                        <?php $this->render_features_tab(); ?>
                    <?php elseif ($active_tab === 'messages') : ?>
                        <?php $this->render_messages_tab(); ?>
                    <?php elseif ($active_tab === 'colors') : ?>
                        <?php $this->render_colors_tab(); ?>
                    <?php endif; ?>

                    <div class="ssl-sms-submit">
                        <?php submit_button(__('Save Settings', 'ssl-sms-login'), 'primary', 'submit', false); ?>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Render General Tab
     */
    private function render_general_tab() {
        ?>
        <div class="ssl-sms-card">
            <div class="ssl-sms-card-header">
                <h2><span class="dashicons dashicons-cloud"></span> <?php esc_html_e('API Configuration', 'ssl-sms-login'); ?></h2>
                <p><?php esc_html_e('Configure your SSL Wireless SMS API credentials.', 'ssl-sms-login'); ?></p>
            </div>
            <div class="ssl-sms-card-body">
                <div class="ssl-sms-field">
                    <label for="api_token"><?php esc_html_e('API Token', 'ssl-sms-login'); ?></label>
                    <input type="password"
                           id="api_token"
                           name="<?php echo esc_attr($this->option_name); ?>[api_token]"
                           value="<?php echo esc_attr($this->get_option('api_token')); ?>"
                           class="ssl-sms-input"
                           placeholder="<?php esc_attr_e('Enter your API token', 'ssl-sms-login'); ?>" />
                    <span class="ssl-sms-help"><?php esc_html_e('Your SSL Wireless API token for authentication.', 'ssl-sms-login'); ?></span>
                </div>
                <div class="ssl-sms-field">
                    <label for="sender_id"><?php esc_html_e('Sender ID (SID)', 'ssl-sms-login'); ?></label>
                    <input type="text"
                           id="sender_id"
                           name="<?php echo esc_attr($this->option_name); ?>[sender_id]"
                           value="<?php echo esc_attr($this->get_option('sender_id')); ?>"
                           class="ssl-sms-input"
                           placeholder="<?php esc_attr_e('Enter sender ID', 'ssl-sms-login'); ?>" />
                    <span class="ssl-sms-help"><?php esc_html_e('The sender name that appears on SMS messages.', 'ssl-sms-login'); ?></span>
                </div>
            </div>
        </div>

        <div class="ssl-sms-card">
            <div class="ssl-sms-card-header">
                <h2><span class="dashicons dashicons-admin-tools"></span> <?php esc_html_e('Test SMS', 'ssl-sms-login'); ?></h2>
                <p><?php esc_html_e('Send a test SMS to verify your API configuration.', 'ssl-sms-login'); ?></p>
            </div>
            <div class="ssl-sms-card-body">
                <div class="ssl-sms-field ssl-sms-inline-field">
                    <input type="text"
                           id="ssl-test-mobile"
                           class="ssl-sms-input"
                           placeholder="<?php esc_attr_e('Enter mobile number (01XXXXXXXXX)', 'ssl-sms-login'); ?>" />
                    <button type="button" id="ssl-send-test-sms" class="ssl-sms-button ssl-sms-button-secondary">
                        <span class="dashicons dashicons-email-alt"></span>
                        <?php esc_html_e('Send Test SMS', 'ssl-sms-login'); ?>
                    </button>
                </div>
                <div id="ssl-test-result" class="ssl-sms-notice" style="display:none;"></div>
            </div>
        </div>

        <div class="ssl-sms-card">
            <div class="ssl-sms-card-header">
                <h2><span class="dashicons dashicons-shortcode"></span> <?php esc_html_e('Available Shortcodes', 'ssl-sms-login'); ?></h2>
                <p><?php esc_html_e('Use these shortcodes to display login forms on your pages.', 'ssl-sms-login'); ?></p>
            </div>
            <div class="ssl-sms-card-body">
                <div class="ssl-sms-shortcodes">
                    <div class="ssl-sms-shortcode-item">
                        <code>[ssl_sms_login]</code>
                        <span><?php esc_html_e('Complete login/registration form with tabs', 'ssl-sms-login'); ?></span>
                    </div>
                    <div class="ssl-sms-shortcode-item">
                        <code>[ssl_sms_login_form]</code>
                        <span><?php esc_html_e('Login form only', 'ssl-sms-login'); ?></span>
                    </div>
                    <div class="ssl-sms-shortcode-item">
                        <code>[ssl_sms_register_form]</code>
                        <span><?php esc_html_e('Registration form only', 'ssl-sms-login'); ?></span>
                    </div>
                    <div class="ssl-sms-shortcode-item">
                        <code>[ssl_sms_forgot_password]</code>
                        <span><?php esc_html_e('Forgot password form', 'ssl-sms-login'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#ssl-send-test-sms').on('click', function() {
                var mobile = $('#ssl-test-mobile').val();
                if (!mobile) {
                    $('#ssl-test-result').removeClass('ssl-sms-success ssl-sms-error')
                        .addClass('ssl-sms-error')
                        .html('<?php echo esc_js(__('Please enter a mobile number', 'ssl-sms-login')); ?>')
                        .show();
                    return;
                }

                var $btn = $(this);
                var $result = $('#ssl-test-result');

                $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> <?php echo esc_js(__('Sending...', 'ssl-sms-login')); ?>');

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'ssl_sms_send_test',
                        mobile: mobile,
                        nonce: '<?php echo esc_js(wp_create_nonce('ssl_sms_test_nonce')); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $result.removeClass('ssl-sms-error').addClass('ssl-sms-success').html(response.data.message).show();
                        } else {
                            $result.removeClass('ssl-sms-success').addClass('ssl-sms-error').html(response.data.message).show();
                        }
                    },
                    error: function() {
                        $result.removeClass('ssl-sms-success').addClass('ssl-sms-error')
                            .html('<?php echo esc_js(__('Connection error', 'ssl-sms-login')); ?>').show();
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('<span class="dashicons dashicons-email-alt"></span> <?php echo esc_js(__('Send Test SMS', 'ssl-sms-login')); ?>');
                    }
                });
            });
        });
        </script>
        <?php
    }

    /**
     * Render OTP Tab
     */
    private function render_otp_tab() {
        ?>
        <div class="ssl-sms-card">
            <div class="ssl-sms-card-header">
                <h2><span class="dashicons dashicons-lock"></span> <?php esc_html_e('OTP Configuration', 'ssl-sms-login'); ?></h2>
                <p><?php esc_html_e('Configure OTP behavior and security settings.', 'ssl-sms-login'); ?></p>
            </div>
            <div class="ssl-sms-card-body">
                <div class="ssl-sms-field-grid">
                    <div class="ssl-sms-field">
                        <label for="otp_expiry"><?php esc_html_e('OTP Expiry (minutes)', 'ssl-sms-login'); ?></label>
                        <input type="number"
                               id="otp_expiry"
                               name="<?php echo esc_attr($this->option_name); ?>[otp_expiry]"
                               value="<?php echo esc_attr($this->get_option('otp_expiry', 5)); ?>"
                               class="ssl-sms-input ssl-sms-input-sm"
                               min="1"
                               max="30" />
                        <span class="ssl-sms-help"><?php esc_html_e('How long the OTP remains valid (1-30 minutes).', 'ssl-sms-login'); ?></span>
                    </div>
                    <div class="ssl-sms-field">
                        <label for="max_otp_attempts"><?php esc_html_e('Max OTP Attempts', 'ssl-sms-login'); ?></label>
                        <input type="number"
                               id="max_otp_attempts"
                               name="<?php echo esc_attr($this->option_name); ?>[max_otp_attempts]"
                               value="<?php echo esc_attr($this->get_option('max_otp_attempts', 3)); ?>"
                               class="ssl-sms-input ssl-sms-input-sm"
                               min="1"
                               max="10" />
                        <span class="ssl-sms-help"><?php esc_html_e('Maximum OTP send attempts before blocking.', 'ssl-sms-login'); ?></span>
                    </div>
                    <div class="ssl-sms-field">
                        <label for="block_duration"><?php esc_html_e('Block Duration (hours)', 'ssl-sms-login'); ?></label>
                        <input type="number"
                               id="block_duration"
                               name="<?php echo esc_attr($this->option_name); ?>[block_duration]"
                               value="<?php echo esc_attr($this->get_option('block_duration', 24)); ?>"
                               class="ssl-sms-input ssl-sms-input-sm"
                               min="1"
                               max="72" />
                        <span class="ssl-sms-help"><?php esc_html_e('How long to block after max attempts (1-72 hours).', 'ssl-sms-login'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render Features Tab
     */
    private function render_features_tab() {
        ?>
        <div class="ssl-sms-card">
            <div class="ssl-sms-card-header">
                <h2><span class="dashicons dashicons-admin-plugins"></span> <?php esc_html_e('Feature Settings', 'ssl-sms-login'); ?></h2>
                <p><?php esc_html_e('Enable or disable plugin features.', 'ssl-sms-login'); ?></p>
            </div>
            <div class="ssl-sms-card-body">
                <div class="ssl-sms-toggle-group">
                    <div class="ssl-sms-toggle-item">
                        <div class="ssl-sms-toggle-info">
                            <h4><?php esc_html_e('Enable Registration', 'ssl-sms-login'); ?></h4>
                            <p><?php esc_html_e('Allow new users to register using their mobile number.', 'ssl-sms-login'); ?></p>
                        </div>
                        <label class="ssl-sms-switch">
                            <input type="checkbox"
                                   name="<?php echo esc_attr($this->option_name); ?>[enable_registration]"
                                   value="yes"
                                   <?php checked($this->get_option('enable_registration', 'yes'), 'yes'); ?> />
                            <span class="ssl-sms-slider"></span>
                        </label>
                    </div>
                    <div class="ssl-sms-toggle-item">
                        <div class="ssl-sms-toggle-info">
                            <h4><?php esc_html_e('Enable OTP Login', 'ssl-sms-login'); ?></h4>
                            <p><?php esc_html_e('Allow users to login using OTP instead of password.', 'ssl-sms-login'); ?></p>
                        </div>
                        <label class="ssl-sms-switch">
                            <input type="checkbox"
                                   name="<?php echo esc_attr($this->option_name); ?>[enable_login]"
                                   value="yes"
                                   <?php checked($this->get_option('enable_login', 'yes'), 'yes'); ?> />
                            <span class="ssl-sms-slider"></span>
                        </label>
                    </div>
                    <div class="ssl-sms-toggle-item">
                        <div class="ssl-sms-toggle-info">
                            <h4><?php esc_html_e('Enable Forgot Password', 'ssl-sms-login'); ?></h4>
                            <p><?php esc_html_e('Allow users to reset password via SMS OTP.', 'ssl-sms-login'); ?></p>
                        </div>
                        <label class="ssl-sms-switch">
                            <input type="checkbox"
                                   name="<?php echo esc_attr($this->option_name); ?>[enable_forgot_password]"
                                   value="yes"
                                   <?php checked($this->get_option('enable_forgot_password', 'yes'), 'yes'); ?> />
                            <span class="ssl-sms-slider"></span>
                        </label>
                    </div>
                </div>

                <div class="ssl-sms-field" style="margin-top: 30px;">
                    <label for="redirect_after_login"><?php esc_html_e('Redirect After Login', 'ssl-sms-login'); ?></label>
                    <input type="url"
                           id="redirect_after_login"
                           name="<?php echo esc_attr($this->option_name); ?>[redirect_after_login]"
                           value="<?php echo esc_url($this->get_option('redirect_after_login')); ?>"
                           class="ssl-sms-input"
                           placeholder="<?php esc_attr_e('https://example.com/dashboard', 'ssl-sms-login'); ?>" />
                    <span class="ssl-sms-help"><?php esc_html_e('URL to redirect users after successful login. Leave empty for home page.', 'ssl-sms-login'); ?></span>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render Messages Tab
     */
    private function render_messages_tab() {
        ?>
        <div class="ssl-sms-card">
            <div class="ssl-sms-card-header">
                <h2><span class="dashicons dashicons-email"></span> <?php esc_html_e('Message Templates', 'ssl-sms-login'); ?></h2>
                <p><?php esc_html_e('Customize the SMS messages sent to users.', 'ssl-sms-login'); ?></p>
            </div>
            <div class="ssl-sms-card-body">
                <div class="ssl-sms-field">
                    <label for="otp_message_template"><?php esc_html_e('OTP Message', 'ssl-sms-login'); ?></label>
                    <textarea id="otp_message_template"
                              name="<?php echo esc_attr($this->option_name); ?>[otp_message_template]"
                              class="ssl-sms-textarea"
                              rows="3"><?php echo esc_textarea($this->get_option('otp_message_template', __('Your OTP is: {otp}. Valid for {expiry} minutes.', 'ssl-sms-login'))); ?></textarea>
                    <span class="ssl-sms-help">
                        <?php esc_html_e('Available placeholders:', 'ssl-sms-login'); ?>
                        <code>{otp}</code>, <code>{expiry}</code>
                    </span>
                </div>
                <div class="ssl-sms-field">
                    <label for="welcome_message_template"><?php esc_html_e('Welcome Message', 'ssl-sms-login'); ?></label>
                    <textarea id="welcome_message_template"
                              name="<?php echo esc_attr($this->option_name); ?>[welcome_message_template]"
                              class="ssl-sms-textarea"
                              rows="3"><?php echo esc_textarea($this->get_option('welcome_message_template', __('Welcome to {site_name}! Username: {username}, Password: {password}', 'ssl-sms-login'))); ?></textarea>
                    <span class="ssl-sms-help">
                        <?php esc_html_e('Available placeholders:', 'ssl-sms-login'); ?>
                        <code>{username}</code>, <code>{password}</code>, <code>{site_name}</code>
                    </span>
                </div>
                <div class="ssl-sms-field">
                    <label for="password_reset_template"><?php esc_html_e('Password Reset Message', 'ssl-sms-login'); ?></label>
                    <textarea id="password_reset_template"
                              name="<?php echo esc_attr($this->option_name); ?>[password_reset_template]"
                              class="ssl-sms-textarea"
                              rows="3"><?php echo esc_textarea($this->get_option('password_reset_template', __('Your new password for {site_name}: {password}', 'ssl-sms-login'))); ?></textarea>
                    <span class="ssl-sms-help">
                        <?php esc_html_e('Available placeholders:', 'ssl-sms-login'); ?>
                        <code>{username}</code>, <code>{password}</code>, <code>{site_name}</code>
                    </span>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render Colors Tab
     */
    private function render_colors_tab() {
        ?>
        <div class="ssl-sms-card">
            <div class="ssl-sms-card-header">
                <h2><span class="dashicons dashicons-art"></span> <?php esc_html_e('Color Settings', 'ssl-sms-login'); ?></h2>
                <p><?php esc_html_e('Customize the colors of your login forms to match your brand.', 'ssl-sms-login'); ?></p>
            </div>
            <div class="ssl-sms-card-body">
                <div class="ssl-sms-color-grid">
                    <div class="ssl-sms-color-field">
                        <label for="primary_color"><?php esc_html_e('Primary Color', 'ssl-sms-login'); ?></label>
                        <div class="ssl-sms-color-picker-wrap">
                            <input type="text"
                                   id="primary_color"
                                   name="<?php echo esc_attr($this->option_name); ?>[primary_color]"
                                   value="<?php echo esc_attr($this->get_option('primary_color', '#2563eb')); ?>"
                                   class="ssl-sms-color-picker"
                                   data-default-color="#2563eb" />
                        </div>
                        <span class="ssl-sms-help"><?php esc_html_e('Main brand color for buttons and links.', 'ssl-sms-login'); ?></span>
                    </div>
                    <div class="ssl-sms-color-field">
                        <label for="secondary_color"><?php esc_html_e('Secondary Color', 'ssl-sms-login'); ?></label>
                        <div class="ssl-sms-color-picker-wrap">
                            <input type="text"
                                   id="secondary_color"
                                   name="<?php echo esc_attr($this->option_name); ?>[secondary_color]"
                                   value="<?php echo esc_attr($this->get_option('secondary_color', '#64748b')); ?>"
                                   class="ssl-sms-color-picker"
                                   data-default-color="#64748b" />
                        </div>
                        <span class="ssl-sms-help"><?php esc_html_e('Secondary color for secondary buttons.', 'ssl-sms-login'); ?></span>
                    </div>
                    <div class="ssl-sms-color-field">
                        <label for="text_color"><?php esc_html_e('Text Color', 'ssl-sms-login'); ?></label>
                        <div class="ssl-sms-color-picker-wrap">
                            <input type="text"
                                   id="text_color"
                                   name="<?php echo esc_attr($this->option_name); ?>[text_color]"
                                   value="<?php echo esc_attr($this->get_option('text_color', '#1f2937')); ?>"
                                   class="ssl-sms-color-picker"
                                   data-default-color="#1f2937" />
                        </div>
                        <span class="ssl-sms-help"><?php esc_html_e('Main text color for form labels.', 'ssl-sms-login'); ?></span>
                    </div>
                    <div class="ssl-sms-color-field">
                        <label for="button_text_color"><?php esc_html_e('Button Text Color', 'ssl-sms-login'); ?></label>
                        <div class="ssl-sms-color-picker-wrap">
                            <input type="text"
                                   id="button_text_color"
                                   name="<?php echo esc_attr($this->option_name); ?>[button_text_color]"
                                   value="<?php echo esc_attr($this->get_option('button_text_color', '#ffffff')); ?>"
                                   class="ssl-sms-color-picker"
                                   data-default-color="#ffffff" />
                        </div>
                        <span class="ssl-sms-help"><?php esc_html_e('Text color for primary buttons.', 'ssl-sms-login'); ?></span>
                    </div>
                    <div class="ssl-sms-color-field">
                        <label for="success_color"><?php esc_html_e('Success Color', 'ssl-sms-login'); ?></label>
                        <div class="ssl-sms-color-picker-wrap">
                            <input type="text"
                                   id="success_color"
                                   name="<?php echo esc_attr($this->option_name); ?>[success_color]"
                                   value="<?php echo esc_attr($this->get_option('success_color', '#10b981')); ?>"
                                   class="ssl-sms-color-picker"
                                   data-default-color="#10b981" />
                        </div>
                        <span class="ssl-sms-help"><?php esc_html_e('Color for success messages.', 'ssl-sms-login'); ?></span>
                    </div>
                    <div class="ssl-sms-color-field">
                        <label for="error_color"><?php esc_html_e('Error Color', 'ssl-sms-login'); ?></label>
                        <div class="ssl-sms-color-picker-wrap">
                            <input type="text"
                                   id="error_color"
                                   name="<?php echo esc_attr($this->option_name); ?>[error_color]"
                                   value="<?php echo esc_attr($this->get_option('error_color', '#ef4444')); ?>"
                                   class="ssl-sms-color-picker"
                                   data-default-color="#ef4444" />
                        </div>
                        <span class="ssl-sms-help"><?php esc_html_e('Color for error messages.', 'ssl-sms-login'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="ssl-sms-card">
            <div class="ssl-sms-card-header">
                <h2><span class="dashicons dashicons-admin-appearance"></span> <?php esc_html_e('Form Style', 'ssl-sms-login'); ?></h2>
                <p><?php esc_html_e('Choose a style for your login forms.', 'ssl-sms-login'); ?></p>
            </div>
            <div class="ssl-sms-card-body">
                <div class="ssl-sms-style-grid">
                    <?php
                    $styles = array(
                        'modern' => __('Modern', 'ssl-sms-login'),
                        'minimal' => __('Minimal', 'ssl-sms-login'),
                        'rounded' => __('Rounded', 'ssl-sms-login'),
                        'bordered' => __('Bordered', 'ssl-sms-login'),
                        'gradient' => __('Gradient', 'ssl-sms-login'),
                    );
                    $current_style = $this->get_option('form_style', 'modern');
                    foreach ($styles as $value => $label) :
                    ?>
                    <label class="ssl-sms-style-option <?php echo $current_style === $value ? 'active' : ''; ?>">
                        <input type="radio"
                               name="<?php echo esc_attr($this->option_name); ?>[form_style]"
                               value="<?php echo esc_attr($value); ?>"
                               <?php checked($current_style, $value); ?> />
                        <span class="ssl-sms-style-preview ssl-sms-style-<?php echo esc_attr($value); ?>">
                            <span class="ssl-sms-preview-input"></span>
                            <span class="ssl-sms-preview-button"></span>
                        </span>
                        <span class="ssl-sms-style-label"><?php echo esc_html($label); ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>

                <div class="ssl-sms-field" style="margin-top: 30px;">
                    <label for="border_radius"><?php esc_html_e('Border Radius', 'ssl-sms-login'); ?></label>
                    <div class="ssl-sms-range-wrap">
                        <input type="range"
                               id="border_radius"
                               name="<?php echo esc_attr($this->option_name); ?>[border_radius]"
                               value="<?php echo esc_attr($this->get_option('border_radius', 8)); ?>"
                               min="0"
                               max="50"
                               class="ssl-sms-range" />
                        <span class="ssl-sms-range-value"><?php echo esc_html($this->get_option('border_radius', 8)); ?>px</span>
                    </div>
                    <span class="ssl-sms-help"><?php esc_html_e('Corner roundness for form elements (0-50px).', 'ssl-sms-login'); ?></span>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Initialize color pickers
            $('.ssl-sms-color-picker').wpColorPicker();

            // Style selection
            $('.ssl-sms-style-option input').on('change', function() {
                $('.ssl-sms-style-option').removeClass('active');
                $(this).closest('.ssl-sms-style-option').addClass('active');
            });

            // Range slider
            $('#border_radius').on('input', function() {
                $(this).siblings('.ssl-sms-range-value').text($(this).val() + 'px');
            });
        });
        </script>
        <?php
    }

    /**
     * Render logs page
     */
    public function render_logs_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'ssl_sms_logs';

        // Handle delete action
        if (isset($_POST['delete_logs']) && check_admin_referer('ssl_sms_delete_logs')) {
            $wpdb->query("TRUNCATE TABLE $table_name");
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Logs cleared successfully.', 'ssl-sms-login') . '</p></div>';
        }

        // Get logs
        $per_page = 50;
        $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($page - 1) * $per_page;

        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $logs = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d",
                $per_page,
                $offset
            )
        );
        ?>
        <div class="wrap ssl-sms-wrap">
            <div class="ssl-sms-header">
                <div class="ssl-sms-header-content">
                    <div class="ssl-sms-logo">
                        <span class="dashicons dashicons-smartphone"></span>
                        <h1><?php esc_html_e('SMS Logs', 'ssl-sms-login'); ?></h1>
                    </div>
                    <div class="ssl-sms-version">
                        <?php echo esc_html(sprintf(__('Total: %s', 'ssl-sms-login'), number_format_i18n($total_items))); ?>
                    </div>
                </div>
            </div>

            <div class="ssl-sms-content">
                <div class="ssl-sms-card">
                    <div class="ssl-sms-card-header ssl-sms-card-header-flex">
                        <div>
                            <h2><span class="dashicons dashicons-list-view"></span> <?php esc_html_e('SMS Activity Log', 'ssl-sms-login'); ?></h2>
                            <p><?php esc_html_e('View all SMS messages sent by the plugin.', 'ssl-sms-login'); ?></p>
                        </div>
                        <form method="post" class="ssl-sms-delete-form">
                            <?php wp_nonce_field('ssl_sms_delete_logs'); ?>
                            <button type="submit" name="delete_logs" class="ssl-sms-button ssl-sms-button-danger"
                                   onclick="return confirm('<?php echo esc_js(__('Are you sure you want to delete all logs?', 'ssl-sms-login')); ?>');">
                                <span class="dashicons dashicons-trash"></span>
                                <?php esc_html_e('Clear All Logs', 'ssl-sms-login'); ?>
                            </button>
                        </form>
                    </div>
                    <div class="ssl-sms-card-body ssl-sms-card-body-table">
                        <table class="ssl-sms-table">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e('ID', 'ssl-sms-login'); ?></th>
                                    <th><?php esc_html_e('Mobile Number', 'ssl-sms-login'); ?></th>
                                    <th><?php esc_html_e('Type', 'ssl-sms-login'); ?></th>
                                    <th><?php esc_html_e('Status', 'ssl-sms-login'); ?></th>
                                    <th><?php esc_html_e('Date', 'ssl-sms-login'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($logs) : ?>
                                    <?php foreach ($logs as $log) : ?>
                                        <tr>
                                            <td><strong>#<?php echo esc_html($log->id); ?></strong></td>
                                            <td><code><?php echo esc_html($log->mobile_number); ?></code></td>
                                            <td><span class="ssl-sms-badge ssl-sms-badge-info"><?php echo esc_html($log->message_type); ?></span></td>
                                            <td>
                                                <span class="ssl-sms-badge ssl-sms-badge-<?php echo $log->status === 'success' ? 'success' : 'error'; ?>">
                                                    <?php echo esc_html($log->status); ?>
                                                </span>
                                            </td>
                                            <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($log->created_at))); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5" class="ssl-sms-empty"><?php esc_html_e('No logs found.', 'ssl-sms-login'); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    $total_pages = ceil($total_items / $per_page);
                    if ($total_pages > 1) :
                        ?>
                        <div class="ssl-sms-card-footer">
                            <?php
                            $page_links = paginate_links(array(
                                'base' => add_query_arg('paged', '%#%'),
                                'format' => '',
                                'prev_text' => '&laquo; ' . __('Previous', 'ssl-sms-login'),
                                'next_text' => __('Next', 'ssl-sms-login') . ' &raquo;',
                                'total' => $total_pages,
                                'current' => $page,
                                'type' => 'array',
                            ));
                            if ($page_links) :
                                echo '<div class="ssl-sms-pagination">';
                                foreach ($page_links as $link) {
                                    echo wp_kses_post($link);
                                }
                                echo '</div>';
                            endif;
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * AJAX: Send test SMS
     */
    public function ajax_send_test() {
        check_ajax_referer('ssl_sms_test_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'ssl-sms-login')));
        }

        $mobile = isset($_POST['mobile']) ? sanitize_text_field($_POST['mobile']) : '';

        if (empty($mobile)) {
            wp_send_json_error(array('message' => __('Mobile number is required.', 'ssl-sms-login')));
        }

        $sms_gateway = ssl_sms_login()->sms_gateway;
        $result = $sms_gateway->send_sms($mobile, __('This is a test SMS from SSL SMS Login plugin.', 'ssl-sms-login'), 'test');

        if ($result) {
            wp_send_json_success(array('message' => __('Test SMS sent successfully!', 'ssl-sms-login')));
        } else {
            wp_send_json_error(array('message' => __('Failed to send SMS. Please check your API credentials.', 'ssl-sms-login')));
        }
    }
}
