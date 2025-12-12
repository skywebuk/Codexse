<?php
/**
 * Admin Settings Class
 *
 * @package SSL_SMS_Login_Pro
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
        add_filter('plugin_action_links_' . SSL_SMS_LOGIN_PLUGIN_BASENAME, array($this, 'add_settings_link'));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('SSL SMS Login', 'ssl-sms-login-pro'),
            __('SMS Login', 'ssl-sms-login-pro'),
            'manage_options',
            $this->page_slug,
            array($this, 'render_settings_page'),
            'dashicons-smartphone',
            80
        );

        add_submenu_page(
            $this->page_slug,
            __('Settings', 'ssl-sms-login-pro'),
            __('Settings', 'ssl-sms-login-pro'),
            'manage_options',
            $this->page_slug,
            array($this, 'render_settings_page')
        );

        add_submenu_page(
            $this->page_slug,
            __('SMS Logs', 'ssl-sms-login-pro'),
            __('SMS Logs', 'ssl-sms-login-pro'),
            'manage_options',
            'ssl-sms-login-logs',
            array($this, 'render_logs_page')
        );
    }

    /**
     * Add settings link to plugins page
     */
    public function add_settings_link($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=' . $this->page_slug) . '">' . __('Settings', 'ssl-sms-login-pro') . '</a>';
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

        // API Settings Section
        add_settings_section(
            'ssl_sms_api_section',
            __('API Configuration', 'ssl-sms-login-pro'),
            array($this, 'render_api_section'),
            $this->page_slug
        );

        add_settings_field(
            'api_token',
            __('API Token', 'ssl-sms-login-pro'),
            array($this, 'render_text_field'),
            $this->page_slug,
            'ssl_sms_api_section',
            array(
                'field' => 'api_token',
                'type' => 'password',
                'description' => __('Enter your SSL Wireless API token.', 'ssl-sms-login-pro'),
            )
        );

        add_settings_field(
            'sender_id',
            __('Sender ID (SID)', 'ssl-sms-login-pro'),
            array($this, 'render_text_field'),
            $this->page_slug,
            'ssl_sms_api_section',
            array(
                'field' => 'sender_id',
                'description' => __('Enter your SMS sender ID.', 'ssl-sms-login-pro'),
            )
        );

        // OTP Settings Section
        add_settings_section(
            'ssl_sms_otp_section',
            __('OTP Configuration', 'ssl-sms-login-pro'),
            array($this, 'render_otp_section'),
            $this->page_slug
        );

        add_settings_field(
            'otp_expiry',
            __('OTP Expiry (minutes)', 'ssl-sms-login-pro'),
            array($this, 'render_number_field'),
            $this->page_slug,
            'ssl_sms_otp_section',
            array(
                'field' => 'otp_expiry',
                'min' => 1,
                'max' => 30,
                'default' => 5,
                'description' => __('How long the OTP remains valid.', 'ssl-sms-login-pro'),
            )
        );

        add_settings_field(
            'max_otp_attempts',
            __('Max OTP Attempts', 'ssl-sms-login-pro'),
            array($this, 'render_number_field'),
            $this->page_slug,
            'ssl_sms_otp_section',
            array(
                'field' => 'max_otp_attempts',
                'min' => 1,
                'max' => 10,
                'default' => 3,
                'description' => __('Maximum OTP send attempts before blocking.', 'ssl-sms-login-pro'),
            )
        );

        add_settings_field(
            'block_duration',
            __('Block Duration (hours)', 'ssl-sms-login-pro'),
            array($this, 'render_number_field'),
            $this->page_slug,
            'ssl_sms_otp_section',
            array(
                'field' => 'block_duration',
                'min' => 1,
                'max' => 72,
                'default' => 24,
                'description' => __('How long to block after max attempts.', 'ssl-sms-login-pro'),
            )
        );

        // Features Section
        add_settings_section(
            'ssl_sms_features_section',
            __('Features', 'ssl-sms-login-pro'),
            array($this, 'render_features_section'),
            $this->page_slug
        );

        add_settings_field(
            'enable_registration',
            __('Enable Registration', 'ssl-sms-login-pro'),
            array($this, 'render_checkbox_field'),
            $this->page_slug,
            'ssl_sms_features_section',
            array(
                'field' => 'enable_registration',
                'description' => __('Allow new user registration via SMS.', 'ssl-sms-login-pro'),
            )
        );

        add_settings_field(
            'enable_login',
            __('Enable OTP Login', 'ssl-sms-login-pro'),
            array($this, 'render_checkbox_field'),
            $this->page_slug,
            'ssl_sms_features_section',
            array(
                'field' => 'enable_login',
                'description' => __('Allow users to login via OTP.', 'ssl-sms-login-pro'),
            )
        );

        add_settings_field(
            'enable_forgot_password',
            __('Enable Forgot Password', 'ssl-sms-login-pro'),
            array($this, 'render_checkbox_field'),
            $this->page_slug,
            'ssl_sms_features_section',
            array(
                'field' => 'enable_forgot_password',
                'description' => __('Allow password reset via SMS.', 'ssl-sms-login-pro'),
            )
        );

        add_settings_field(
            'redirect_after_login',
            __('Redirect After Login', 'ssl-sms-login-pro'),
            array($this, 'render_text_field'),
            $this->page_slug,
            'ssl_sms_features_section',
            array(
                'field' => 'redirect_after_login',
                'description' => __('URL to redirect after successful login (leave empty for home page).', 'ssl-sms-login-pro'),
            )
        );

        // Message Templates Section
        add_settings_section(
            'ssl_sms_templates_section',
            __('Message Templates', 'ssl-sms-login-pro'),
            array($this, 'render_templates_section'),
            $this->page_slug
        );

        add_settings_field(
            'otp_message_template',
            __('OTP Message', 'ssl-sms-login-pro'),
            array($this, 'render_textarea_field'),
            $this->page_slug,
            'ssl_sms_templates_section',
            array(
                'field' => 'otp_message_template',
                'description' => __('Available placeholders: {otp}, {expiry}', 'ssl-sms-login-pro'),
            )
        );

        add_settings_field(
            'welcome_message_template',
            __('Welcome Message', 'ssl-sms-login-pro'),
            array($this, 'render_textarea_field'),
            $this->page_slug,
            'ssl_sms_templates_section',
            array(
                'field' => 'welcome_message_template',
                'description' => __('Available placeholders: {username}, {password}, {site_name}', 'ssl-sms-login-pro'),
            )
        );

        add_settings_field(
            'password_reset_template',
            __('Password Reset Message', 'ssl-sms-login-pro'),
            array($this, 'render_textarea_field'),
            $this->page_slug,
            'ssl_sms_templates_section',
            array(
                'field' => 'password_reset_template',
                'description' => __('Available placeholders: {username}, {password}', 'ssl-sms-login-pro'),
            )
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

        return $sanitized;
    }

    /**
     * Render API section
     */
    public function render_api_section() {
        echo '<p>' . esc_html__('Configure your SSL Wireless SMS API credentials.', 'ssl-sms-login-pro') . '</p>';
    }

    /**
     * Render OTP section
     */
    public function render_otp_section() {
        echo '<p>' . esc_html__('Configure OTP behavior and security settings.', 'ssl-sms-login-pro') . '</p>';
    }

    /**
     * Render features section
     */
    public function render_features_section() {
        echo '<p>' . esc_html__('Enable or disable plugin features.', 'ssl-sms-login-pro') . '</p>';
    }

    /**
     * Render templates section
     */
    public function render_templates_section() {
        echo '<p>' . esc_html__('Customize the SMS messages sent to users.', 'ssl-sms-login-pro') . '</p>';
    }

    /**
     * Render text field
     */
    public function render_text_field($args) {
        $options = get_option($this->option_name, array());
        $value = isset($options[$args['field']]) ? $options[$args['field']] : '';
        $type = isset($args['type']) ? $args['type'] : 'text';
        ?>
        <input type="<?php echo esc_attr($type); ?>"
               name="<?php echo esc_attr($this->option_name . '[' . $args['field'] . ']'); ?>"
               value="<?php echo esc_attr($value); ?>"
               class="regular-text" />
        <?php if (isset($args['description'])) : ?>
            <p class="description"><?php echo esc_html($args['description']); ?></p>
        <?php endif;
    }

    /**
     * Render number field
     */
    public function render_number_field($args) {
        $options = get_option($this->option_name, array());
        $value = isset($options[$args['field']]) ? $options[$args['field']] : $args['default'];
        ?>
        <input type="number"
               name="<?php echo esc_attr($this->option_name . '[' . $args['field'] . ']'); ?>"
               value="<?php echo esc_attr($value); ?>"
               min="<?php echo esc_attr($args['min']); ?>"
               max="<?php echo esc_attr($args['max']); ?>"
               class="small-text" />
        <?php if (isset($args['description'])) : ?>
            <p class="description"><?php echo esc_html($args['description']); ?></p>
        <?php endif;
    }

    /**
     * Render checkbox field
     */
    public function render_checkbox_field($args) {
        $options = get_option($this->option_name, array());
        $value = isset($options[$args['field']]) ? $options[$args['field']] : 'yes';
        ?>
        <label>
            <input type="checkbox"
                   name="<?php echo esc_attr($this->option_name . '[' . $args['field'] . ']'); ?>"
                   value="yes"
                   <?php checked($value, 'yes'); ?> />
            <?php if (isset($args['description'])) : ?>
                <?php echo esc_html($args['description']); ?>
            <?php endif; ?>
        </label>
        <?php
    }

    /**
     * Render textarea field
     */
    public function render_textarea_field($args) {
        $options = get_option($this->option_name, array());
        $value = isset($options[$args['field']]) ? $options[$args['field']] : '';
        ?>
        <textarea name="<?php echo esc_attr($this->option_name . '[' . $args['field'] . ']'); ?>"
                  rows="3"
                  class="large-text"><?php echo esc_textarea($value); ?></textarea>
        <?php if (isset($args['description'])) : ?>
            <p class="description"><?php echo esc_html($args['description']); ?></p>
        <?php endif;
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <div class="ssl-sms-admin-header">
                <div class="ssl-sms-shortcodes-info">
                    <h3><?php esc_html_e('Available Shortcodes', 'ssl-sms-login-pro'); ?></h3>
                    <ul>
                        <li><code>[ssl_sms_login]</code> - <?php esc_html_e('Complete login/registration form', 'ssl-sms-login-pro'); ?></li>
                        <li><code>[ssl_sms_login_form]</code> - <?php esc_html_e('Login form only', 'ssl-sms-login-pro'); ?></li>
                        <li><code>[ssl_sms_register_form]</code> - <?php esc_html_e('Registration form only', 'ssl-sms-login-pro'); ?></li>
                        <li><code>[ssl_sms_forgot_password]</code> - <?php esc_html_e('Forgot password form', 'ssl-sms-login-pro'); ?></li>
                    </ul>
                </div>
            </div>

            <form action="options.php" method="post">
                <?php
                settings_fields('ssl_sms_login_settings_group');
                do_settings_sections($this->page_slug);
                submit_button(__('Save Settings', 'ssl-sms-login-pro'));
                ?>
            </form>

            <div class="ssl-sms-test-section">
                <h3><?php esc_html_e('Test SMS', 'ssl-sms-login-pro'); ?></h3>
                <p><?php esc_html_e('Send a test SMS to verify your API configuration.', 'ssl-sms-login-pro'); ?></p>
                <input type="text" id="ssl-test-mobile" placeholder="<?php esc_attr_e('Mobile number', 'ssl-sms-login-pro'); ?>" class="regular-text" />
                <button type="button" id="ssl-send-test-sms" class="button button-secondary">
                    <?php esc_html_e('Send Test SMS', 'ssl-sms-login-pro'); ?>
                </button>
                <span id="ssl-test-result"></span>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#ssl-send-test-sms').on('click', function() {
                var mobile = $('#ssl-test-mobile').val();
                if (!mobile) {
                    alert('<?php echo esc_js(__('Please enter a mobile number', 'ssl-sms-login-pro')); ?>');
                    return;
                }

                var $btn = $(this);
                var $result = $('#ssl-test-result');

                $btn.prop('disabled', true).text('<?php echo esc_js(__('Sending...', 'ssl-sms-login-pro')); ?>');

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
                            $result.html('<span style="color:green;">' + response.data.message + '</span>');
                        } else {
                            $result.html('<span style="color:red;">' + response.data.message + '</span>');
                        }
                    },
                    error: function() {
                        $result.html('<span style="color:red;"><?php echo esc_js(__('Connection error', 'ssl-sms-login-pro')); ?></span>');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('<?php echo esc_js(__('Send Test SMS', 'ssl-sms-login-pro')); ?>');
                    }
                });
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
            echo '<div class="notice notice-success"><p>' . esc_html__('Logs cleared successfully.', 'ssl-sms-login-pro') . '</p></div>';
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
        <div class="wrap">
            <h1><?php esc_html_e('SMS Logs', 'ssl-sms-login-pro'); ?></h1>

            <form method="post" style="margin-bottom: 20px;">
                <?php wp_nonce_field('ssl_sms_delete_logs'); ?>
                <input type="submit" name="delete_logs" class="button button-secondary"
                       value="<?php esc_attr_e('Clear All Logs', 'ssl-sms-login-pro'); ?>"
                       onclick="return confirm('<?php echo esc_js(__('Are you sure you want to delete all logs?', 'ssl-sms-login-pro')); ?>');" />
            </form>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('ID', 'ssl-sms-login-pro'); ?></th>
                        <th><?php esc_html_e('Mobile Number', 'ssl-sms-login-pro'); ?></th>
                        <th><?php esc_html_e('Type', 'ssl-sms-login-pro'); ?></th>
                        <th><?php esc_html_e('Status', 'ssl-sms-login-pro'); ?></th>
                        <th><?php esc_html_e('Date', 'ssl-sms-login-pro'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($logs) : ?>
                        <?php foreach ($logs as $log) : ?>
                            <tr>
                                <td><?php echo esc_html($log->id); ?></td>
                                <td><?php echo esc_html($log->mobile_number); ?></td>
                                <td><?php echo esc_html($log->message_type); ?></td>
                                <td>
                                    <span class="ssl-status-<?php echo esc_attr($log->status); ?>">
                                        <?php echo esc_html($log->status); ?>
                                    </span>
                                </td>
                                <td><?php echo esc_html($log->created_at); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5"><?php esc_html_e('No logs found.', 'ssl-sms-login-pro'); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php
            $total_pages = ceil($total_items / $per_page);
            if ($total_pages > 1) :
                $page_links = paginate_links(array(
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                    'total' => $total_pages,
                    'current' => $page,
                ));
                if ($page_links) :
                    echo '<div class="tablenav"><div class="tablenav-pages">' . wp_kses_post($page_links) . '</div></div>';
                endif;
            endif;
            ?>
        </div>
        <?php
    }
}
