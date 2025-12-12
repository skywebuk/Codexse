<?php
/**
 * Elementor Widget Class
 *
 * @package SSL_SMS_Login
 */

if (!defined('ABSPATH')) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class SSL_SMS_Elementor_Widget extends Widget_Base {

    /**
     * Get widget name
     */
    public function get_name() {
        return 'ssl-sms-login';
    }

    /**
     * Get widget title
     */
    public function get_title() {
        return __('SMS Login Form', 'ssl-sms-login');
    }

    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-lock-user';
    }

    /**
     * Get widget categories
     */
    public function get_categories() {
        return array('ssl-sms');
    }

    /**
     * Get widget keywords
     */
    public function get_keywords() {
        return array('login', 'register', 'sms', 'otp', 'mobile', 'phone', 'auth');
    }

    /**
     * Register controls
     */
    protected function register_controls() {
        // Content Section
        $this->start_controls_section(
            'content_section',
            array(
                'label' => __('Content', 'ssl-sms-login'),
                'tab' => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'form_type',
            array(
                'label' => __('Form Type', 'ssl-sms-login'),
                'type' => Controls_Manager::SELECT,
                'default' => 'combined',
                'options' => array(
                    'combined' => __('Login + Register (Tabs)', 'ssl-sms-login'),
                    'login' => __('Login Only', 'ssl-sms-login'),
                    'register' => __('Register Only', 'ssl-sms-login'),
                    'forgot' => __('Forgot Password', 'ssl-sms-login'),
                ),
            )
        );

        $this->add_control(
            'form_style',
            array(
                'label' => __('Form Style', 'ssl-sms-login'),
                'type' => Controls_Manager::SELECT,
                'default' => 'modern',
                'options' => array(
                    'modern' => __('Modern', 'ssl-sms-login'),
                    'minimal' => __('Minimal', 'ssl-sms-login'),
                    'rounded' => __('Rounded', 'ssl-sms-login'),
                    'bordered' => __('Bordered', 'ssl-sms-login'),
                    'gradient' => __('Gradient', 'ssl-sms-login'),
                ),
            )
        );

        $this->add_control(
            'show_title',
            array(
                'label' => __('Show Title', 'ssl-sms-login'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'login_title',
            array(
                'label' => __('Login Title', 'ssl-sms-login'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Login with Mobile', 'ssl-sms-login'),
                'condition' => array(
                    'show_title' => 'yes',
                ),
            )
        );

        $this->add_control(
            'register_title',
            array(
                'label' => __('Register Title', 'ssl-sms-login'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Create Account', 'ssl-sms-login'),
                'condition' => array(
                    'show_title' => 'yes',
                    'form_type' => array('combined', 'register'),
                ),
            )
        );

        $this->add_control(
            'redirect_url',
            array(
                'label' => __('Redirect URL', 'ssl-sms-login'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://your-site.com/dashboard', 'ssl-sms-login'),
                'description' => __('Redirect users after successful login/registration', 'ssl-sms-login'),
            )
        );

        $this->end_controls_section();

        // Style Section - Form Container
        $this->start_controls_section(
            'style_container_section',
            array(
                'label' => __('Form Container', 'ssl-sms-login'),
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'container_background',
            array(
                'label' => __('Background Color', 'ssl-sms-login'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .ssl-sms-form' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'container_padding',
            array(
                'label' => __('Padding', 'ssl-sms-login'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'default' => array(
                    'top' => '30',
                    'right' => '30',
                    'bottom' => '30',
                    'left' => '30',
                    'unit' => 'px',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .ssl-sms-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'container_border',
                'selector' => '{{WRAPPER}} .ssl-sms-form',
            )
        );

        $this->add_control(
            'container_border_radius',
            array(
                'label' => __('Border Radius', 'ssl-sms-login'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .ssl-sms-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name' => 'container_box_shadow',
                'selector' => '{{WRAPPER}} .ssl-sms-form',
            )
        );

        $this->end_controls_section();

        // Style Section - Input Fields
        $this->start_controls_section(
            'style_input_section',
            array(
                'label' => __('Input Fields', 'ssl-sms-login'),
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'input_background',
            array(
                'label' => __('Background Color', 'ssl-sms-login'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .ssl-sms-input' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'input_text_color',
            array(
                'label' => __('Text Color', 'ssl-sms-login'),
                'type' => Controls_Manager::COLOR,
                'default' => '#1f2937',
                'selectors' => array(
                    '{{WRAPPER}} .ssl-sms-input' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'input_border_color',
            array(
                'label' => __('Border Color', 'ssl-sms-login'),
                'type' => Controls_Manager::COLOR,
                'default' => '#d1d5db',
                'selectors' => array(
                    '{{WRAPPER}} .ssl-sms-input' => 'border-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'input_focus_border_color',
            array(
                'label' => __('Focus Border Color', 'ssl-sms-login'),
                'type' => Controls_Manager::COLOR,
                'default' => '#2563eb',
                'selectors' => array(
                    '{{WRAPPER}} .ssl-sms-input:focus' => 'border-color: {{VALUE}}; box-shadow: 0 0 0 3px {{VALUE}}1a;',
                ),
            )
        );

        $this->add_control(
            'input_border_radius',
            array(
                'label' => __('Border Radius', 'ssl-sms-login'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range' => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 50,
                    ),
                ),
                'default' => array(
                    'size' => 8,
                    'unit' => 'px',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .ssl-sms-input' => 'border-radius: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'input_padding',
            array(
                'label' => __('Padding', 'ssl-sms-login'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em'),
                'default' => array(
                    'top' => '12',
                    'right' => '16',
                    'bottom' => '12',
                    'left' => '16',
                    'unit' => 'px',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .ssl-sms-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // Style Section - Button
        $this->start_controls_section(
            'style_button_section',
            array(
                'label' => __('Button', 'ssl-sms-login'),
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );

        $this->start_controls_tabs('button_style_tabs');

        $this->start_controls_tab(
            'button_normal_tab',
            array(
                'label' => __('Normal', 'ssl-sms-login'),
            )
        );

        $this->add_control(
            'button_background',
            array(
                'label' => __('Background Color', 'ssl-sms-login'),
                'type' => Controls_Manager::COLOR,
                'default' => '#2563eb',
                'selectors' => array(
                    '{{WRAPPER}} .ssl-sms-btn-primary' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'button_text_color',
            array(
                'label' => __('Text Color', 'ssl-sms-login'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .ssl-sms-btn-primary' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'button_hover_tab',
            array(
                'label' => __('Hover', 'ssl-sms-login'),
            )
        );

        $this->add_control(
            'button_hover_background',
            array(
                'label' => __('Background Color', 'ssl-sms-login'),
                'type' => Controls_Manager::COLOR,
                'default' => '#1d4ed8',
                'selectors' => array(
                    '{{WRAPPER}} .ssl-sms-btn-primary:hover' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'button_hover_text_color',
            array(
                'label' => __('Text Color', 'ssl-sms-login'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .ssl-sms-btn-primary:hover' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'button_border_radius',
            array(
                'label' => __('Border Radius', 'ssl-sms-login'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range' => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 50,
                    ),
                ),
                'default' => array(
                    'size' => 8,
                    'unit' => 'px',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .ssl-sms-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
                ),
                'separator' => 'before',
            )
        );

        $this->add_responsive_control(
            'button_padding',
            array(
                'label' => __('Padding', 'ssl-sms-login'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em'),
                'default' => array(
                    'top' => '14',
                    'right' => '24',
                    'bottom' => '14',
                    'left' => '24',
                    'unit' => 'px',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .ssl-sms-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .ssl-sms-btn',
            )
        );

        $this->end_controls_section();

        // Style Section - Tabs
        $this->start_controls_section(
            'style_tabs_section',
            array(
                'label' => __('Tabs', 'ssl-sms-login'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'form_type' => 'combined',
                ),
            )
        );

        $this->add_control(
            'tabs_text_color',
            array(
                'label' => __('Text Color', 'ssl-sms-login'),
                'type' => Controls_Manager::COLOR,
                'default' => '#6b7280',
                'selectors' => array(
                    '{{WRAPPER}} .ssl-sms-tab' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'tabs_active_color',
            array(
                'label' => __('Active Color', 'ssl-sms-login'),
                'type' => Controls_Manager::COLOR,
                'default' => '#2563eb',
                'selectors' => array(
                    '{{WRAPPER}} .ssl-sms-tab.active' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ssl-sms-tab.active::after' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Render widget
     */
    protected function render() {
        $settings = $this->get_settings_for_display();

        $form_type = $settings['form_type'];
        $form_style = $settings['form_style'];
        $redirect_url = isset($settings['redirect_url']['url']) ? $settings['redirect_url']['url'] : '';

        $classes = array(
            'ssl-sms-elementor-widget',
            'ssl-sms-style-' . $form_style,
        );

        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <?php
            switch ($form_type) {
                case 'login':
                    echo do_shortcode('[ssl_sms_login_form redirect="' . esc_attr($redirect_url) . '" style="' . esc_attr($form_style) . '"]');
                    break;
                case 'register':
                    echo do_shortcode('[ssl_sms_register_form redirect="' . esc_attr($redirect_url) . '" style="' . esc_attr($form_style) . '"]');
                    break;
                case 'forgot':
                    echo do_shortcode('[ssl_sms_forgot_password redirect="' . esc_attr($redirect_url) . '" style="' . esc_attr($form_style) . '"]');
                    break;
                default:
                    echo do_shortcode('[ssl_sms_login redirect="' . esc_attr($redirect_url) . '" style="' . esc_attr($form_style) . '"]');
            }
            ?>
        </div>
        <?php
    }
}
