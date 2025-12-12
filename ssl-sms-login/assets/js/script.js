/**
 * SSL SMS Login - Frontend JavaScript
 *
 * @package SSL_SMS_Login
 */

(function($) {
    'use strict';

    // Timer instance
    let otpTimer = null;

    /**
     * Initialize
     */
    function init() {
        bindEvents();
    }

    /**
     * Bind all events
     */
    function bindEvents() {
        // Tab switching
        $(document).on('click', '.ssl-sms-tab', handleTabSwitch);

        // Login method toggle
        $(document).on('change', 'input[name="login_method"]', handleLoginMethodChange);

        // Send OTP
        $(document).on('click', '.ssl-sms-send-otp', handleSendOtp);

        // Verify OTP (Login)
        $(document).on('click', '.ssl-sms-verify-otp', handleVerifyOtp);

        // Verify Forgot Password OTP
        $(document).on('click', '.ssl-sms-verify-forgot-otp', handleForgotPasswordVerify);

        // Password login
        $(document).on('click', '.ssl-sms-password-login', handlePasswordLogin);

        // Complete registration
        $(document).on('click', '.ssl-sms-complete-register', handleCompleteRegister);

        // Resend OTP
        $(document).on('click', '.ssl-sms-resend-otp', handleResendOtp);

        // Back link
        $(document).on('click', '.ssl-sms-back-link', handleBackLink);

        // Forgot password link
        $(document).on('click', '.ssl-sms-forgot-link', handleForgotLink);

        // Back to login
        $(document).on('click', '.ssl-sms-back-to-login', handleBackToLogin);

        // OTP input auto-focus next
        $(document).on('input', '.ssl-sms-otp-input', handleOtpInput);

        // Enter key submit
        $(document).on('keypress', '.ssl-sms-input', handleEnterKey);
    }

    /**
     * Handle tab switch
     */
    function handleTabSwitch(e) {
        e.preventDefault();
        const $tab = $(this);
        const tabName = $tab.data('tab');
        const $wrapper = $tab.closest('.ssl-sms-form-wrapper');

        // Update tabs
        $wrapper.find('.ssl-sms-tab').removeClass('active');
        $tab.addClass('active');

        // Update content
        $wrapper.find('.ssl-sms-tab-content').hide();
        $wrapper.find('#ssl-sms-' + tabName + '-tab').show();

        // Reset forms
        resetForm($wrapper);
    }

    /**
     * Handle login method change
     */
    function handleLoginMethodChange() {
        const method = $(this).val();
        const $form = $(this).closest('.ssl-sms-form');

        if (method === 'password') {
            $form.find('.ssl-sms-password-field').show();
            $form.find('.ssl-sms-send-otp').hide();
            $form.find('.ssl-sms-password-login').show();
        } else {
            $form.find('.ssl-sms-password-field').hide();
            $form.find('.ssl-sms-send-otp').show();
            $form.find('.ssl-sms-password-login').hide();
        }
    }

    /**
     * Handle send OTP
     */
    function handleSendOtp(e) {
        e.preventDefault();
        const $btn = $(this);
        const $form = $btn.closest('.ssl-sms-form');
        const purpose = $btn.data('purpose') || 'login';

        // Get mobile number
        let mobile = '';
        if (purpose === 'forgot_password') {
            mobile = $form.find('#ssl-forgot-mobile').val();
        } else if (purpose === 'register') {
            mobile = $form.find('#ssl-register-mobile').val();
        } else {
            mobile = $form.find('#ssl-login-mobile').val();
        }

        if (!mobile) {
            showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'error', sslSmsLogin.i18n.error);
            return;
        }

        // Set loading state
        setLoading($btn, true);

        // Send AJAX request
        $.ajax({
            url: sslSmsLogin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'ssl_sms_send_otp',
                nonce: sslSmsLogin.nonce,
                mobile: mobile,
                purpose: purpose
            },
            success: function(response) {
                setLoading($btn, false);

                if (response.success) {
                    // Store mobile for later use
                    $form.data('mobile', mobile);

                    // Show success message
                    showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'success', response.data.message);

                    // Move to OTP step
                    setTimeout(function() {
                        if (purpose === 'forgot_password') {
                            goToStep($form, 'forgot-otp');
                        } else {
                            goToStep($form, 2);
                        }

                        // Update OTP info
                        updateOtpInfo($form, mobile, response.data.expiry);

                        // Start timer
                        startResendTimer($form, 60);
                    }, 500);
                } else {
                    showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'error', response.data.message);
                }
            },
            error: function() {
                setLoading($btn, false);
                showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'error', sslSmsLogin.i18n.error);
            }
        });
    }

    /**
     * Handle verify OTP
     */
    function handleVerifyOtp(e) {
        e.preventDefault();
        const $btn = $(this);
        const $form = $btn.closest('.ssl-sms-form');
        const purpose = $btn.data('purpose') || 'login';
        const mobile = $form.data('mobile');
        const otp = $form.find('.ssl-sms-otp-input').val();

        if (!otp || otp.length !== 6) {
            showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'error', 'Please enter a valid 6-digit OTP');
            return;
        }

        setLoading($btn, true);

        $.ajax({
            url: sslSmsLogin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'ssl_sms_verify_otp',
                nonce: sslSmsLogin.nonce,
                mobile: mobile,
                otp: otp,
                purpose: purpose
            },
            success: function(response) {
                setLoading($btn, false);

                if (response.success) {
                    showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'success', response.data.message);

                    if (purpose === 'login') {
                        // Redirect after login
                        setTimeout(function() {
                            const redirect = $form.data('redirect') || response.data.redirect || window.location.href;
                            window.location.href = redirect;
                        }, 500);
                    } else if (purpose === 'register') {
                        // Move to details step
                        setTimeout(function() {
                            goToStep($form, 3);
                        }, 500);
                    }
                } else {
                    showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'error', response.data.message);
                }
            },
            error: function() {
                setLoading($btn, false);
                showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'error', sslSmsLogin.i18n.error);
            }
        });
    }

    /**
     * Handle forgot password verify
     */
    function handleForgotPasswordVerify(e) {
        e.preventDefault();
        const $btn = $(this);
        const $form = $btn.closest('.ssl-sms-form');
        const mobile = $form.data('mobile') || $form.find('#ssl-forgot-mobile').val();
        const otp = $form.find('.ssl-sms-step-forgot-otp .ssl-sms-otp-input, #ssl-forgot-otp').val();

        if (!otp || otp.length !== 6) {
            showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'error', 'Please enter a valid 6-digit OTP');
            return;
        }

        setLoading($btn, true);

        $.ajax({
            url: sslSmsLogin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'ssl_sms_forgot_password',
                nonce: sslSmsLogin.nonce,
                mobile: mobile,
                otp: otp
            },
            success: function(response) {
                setLoading($btn, false);

                if (response.success) {
                    showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'success', response.data.message);

                    // Return to login after a delay
                    setTimeout(function() {
                        resetForm($form.closest('.ssl-sms-form-wrapper'));
                        goToStep($form, 1);
                    }, 3000);
                } else {
                    showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'error', response.data.message);
                }
            },
            error: function() {
                setLoading($btn, false);
                showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'error', sslSmsLogin.i18n.error);
            }
        });
    }

    /**
     * Handle password login
     */
    function handlePasswordLogin(e) {
        e.preventDefault();
        const $btn = $(this);
        const $form = $btn.closest('.ssl-sms-form');
        const mobile = $form.find('#ssl-login-mobile').val();
        const password = $form.find('#ssl-login-password').val();

        if (!mobile || !password) {
            showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'error', 'Mobile and password are required');
            return;
        }

        setLoading($btn, true);

        $.ajax({
            url: sslSmsLogin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'ssl_sms_password_login',
                nonce: sslSmsLogin.nonce,
                mobile: mobile,
                password: password
            },
            success: function(response) {
                setLoading($btn, false);

                if (response.success) {
                    showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'success', response.data.message);

                    setTimeout(function() {
                        const redirect = $form.data('redirect') || response.data.redirect || window.location.href;
                        window.location.href = redirect;
                    }, 500);
                } else {
                    showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'error', response.data.message);
                }
            },
            error: function() {
                setLoading($btn, false);
                showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'error', sslSmsLogin.i18n.error);
            }
        });
    }

    /**
     * Handle complete registration
     */
    function handleCompleteRegister(e) {
        e.preventDefault();
        const $btn = $(this);
        const $form = $btn.closest('.ssl-sms-form');
        const mobile = $form.data('mobile');
        const name = $form.find('#ssl-register-name').val();
        const email = $form.find('#ssl-register-email').val();
        const password = $form.find('#ssl-register-password').val();

        if (!name || !password) {
            showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'error', 'Name and password are required');
            return;
        }

        setLoading($btn, true);

        $.ajax({
            url: sslSmsLogin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'ssl_sms_register',
                nonce: sslSmsLogin.nonce,
                mobile: mobile,
                name: name,
                email: email,
                password: password
            },
            success: function(response) {
                setLoading($btn, false);

                if (response.success) {
                    showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'success', response.data.message);

                    setTimeout(function() {
                        const redirect = $form.data('redirect') || response.data.redirect || window.location.href;
                        window.location.href = redirect;
                    }, 500);
                } else {
                    showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'error', response.data.message);
                }
            },
            error: function() {
                setLoading($btn, false);
                showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'error', sslSmsLogin.i18n.error);
            }
        });
    }

    /**
     * Handle resend OTP
     */
    function handleResendOtp(e) {
        e.preventDefault();
        const $btn = $(this);
        const $form = $btn.closest('.ssl-sms-form');
        const purpose = $btn.data('purpose') || $form.find('.ssl-sms-send-otp').data('purpose') || 'login';
        const mobile = $form.data('mobile');

        if (!mobile) {
            return;
        }

        setLoading($btn, true);

        $.ajax({
            url: sslSmsLogin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'ssl_sms_send_otp',
                nonce: sslSmsLogin.nonce,
                mobile: mobile,
                purpose: purpose
            },
            success: function(response) {
                setLoading($btn, false);

                if (response.success) {
                    showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'success', sslSmsLogin.i18n.otpSent);
                    startResendTimer($form, 60);
                } else {
                    showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'error', response.data.message);
                }
            },
            error: function() {
                setLoading($btn, false);
                showMessage($form.find('.ssl-sms-form-step.active .ssl-sms-message'), 'error', sslSmsLogin.i18n.error);
            }
        });
    }

    /**
     * Handle back link
     */
    function handleBackLink(e) {
        e.preventDefault();
        const $form = $(this).closest('.ssl-sms-form');
        goToStep($form, 1);
    }

    /**
     * Handle forgot password link
     */
    function handleForgotLink(e) {
        e.preventDefault();
        const $form = $(this).closest('.ssl-sms-form');
        goToStep($form, 'forgot');
    }

    /**
     * Handle back to login
     */
    function handleBackToLogin(e) {
        e.preventDefault();
        const $form = $(this).closest('.ssl-sms-form');
        goToStep($form, 1);
    }

    /**
     * Handle OTP input
     */
    function handleOtpInput(e) {
        const $input = $(this);
        let value = $input.val().replace(/\D/g, '');

        if (value.length > 6) {
            value = value.substring(0, 6);
        }

        $input.val(value);
    }

    /**
     * Handle enter key
     */
    function handleEnterKey(e) {
        if (e.which === 13) {
            e.preventDefault();
            const $form = $(this).closest('.ssl-sms-form');
            const $activeStep = $form.find('.ssl-sms-form-step.active');
            const $btn = $activeStep.find('.ssl-sms-btn-primary:visible:first');

            if ($btn.length) {
                $btn.trigger('click');
            }
        }
    }

    /**
     * Go to step
     */
    function goToStep($form, step) {
        $form.find('.ssl-sms-form-step').removeClass('active');

        if (typeof step === 'number') {
            $form.find('.ssl-sms-form-step[data-step="' + step + '"]').addClass('active');
        } else {
            $form.find('.ssl-sms-step-' + step).addClass('active');
        }

        // Clear messages
        $form.find('.ssl-sms-message').removeClass('show').empty();
    }

    /**
     * Show message
     */
    function showMessage($element, type, message) {
        $element.removeClass('success error info show')
            .addClass(type + ' show')
            .text(message);
    }

    /**
     * Set loading state
     */
    function setLoading($btn, loading) {
        if (loading) {
            $btn.addClass('loading').prop('disabled', true);
        } else {
            $btn.removeClass('loading').prop('disabled', false);
        }
    }

    /**
     * Update OTP info
     */
    function updateOtpInfo($form, mobile, expiry) {
        const maskedMobile = mobile.replace(/(\d{3})(\d{4})(\d+)/, '$1****$3');
        const message = 'OTP sent to ' + maskedMobile + '. Valid for ' + expiry + ' minutes.';
        $form.find('.ssl-sms-otp-info').text(message);
    }

    /**
     * Start resend timer
     */
    function startResendTimer($form, seconds) {
        const $btn = $form.find('.ssl-sms-resend-otp');
        const $timer = $form.find('.ssl-sms-timer');

        // Clear existing timer
        if (otpTimer) {
            clearInterval(otpTimer);
        }

        $btn.prop('disabled', true);
        $timer.text('(' + sslSmsLogin.i18n.resendIn + ' ' + seconds + ' ' + sslSmsLogin.i18n.seconds + ')');

        otpTimer = setInterval(function() {
            seconds--;

            if (seconds <= 0) {
                clearInterval(otpTimer);
                $btn.prop('disabled', false);
                $timer.text('');
            } else {
                $timer.text('(' + sslSmsLogin.i18n.resendIn + ' ' + seconds + ' ' + sslSmsLogin.i18n.seconds + ')');
            }
        }, 1000);
    }

    /**
     * Reset form
     */
    function resetForm($wrapper) {
        $wrapper.find('input').val('');
        $wrapper.find('.ssl-sms-message').removeClass('show').empty();
        $wrapper.find('.ssl-sms-form-step').removeClass('active');
        $wrapper.find('.ssl-sms-form-step[data-step="1"]').addClass('active');
        $wrapper.find('.ssl-sms-password-field').hide();
        $wrapper.find('.ssl-sms-send-otp').show();
        $wrapper.find('.ssl-sms-password-login').hide();
        $wrapper.find('input[name="login_method"][value="otp"]').prop('checked', true);

        if (otpTimer) {
            clearInterval(otpTimer);
        }
    }

    // Initialize on document ready
    $(document).ready(init);

})(jQuery);
