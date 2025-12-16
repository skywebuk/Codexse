;(function($){
    $(document).ready(function(){

        /** ─────────────────────────────
         * TAB SWITCHER
         * ───────────────────────────── */
        $('.bf-login-tabs .bf-tab-button').on('click', function(){
            var tab = $(this).data('tab');
            $('.bf-tab-button').removeClass('active');
            $(this).addClass('active');
            $('.bf-login-tab-content').removeClass('active');
            $('#' + tab).addClass('active');
        });


        $('#bf-password-toggle').on('click', function(){
            var $input = $('#bf_mobile_password');

            if($input.attr('type') === 'password'){
                $input.attr('type', 'text');
                $(this).removeClass('ri-eye-line').addClass('ri-eye-off-line'); // icon পরিবর্তন
            } else {
                $input.attr('type', 'password');
                $(this).removeClass('ri-eye-off-line').addClass('ri-eye-line'); // icon পরিবর্তন
            }
        });


        /** ─────────────────────────────
         * MOBILE NUMBER INPUT FILTER (max 14 digits)
         * ───────────────────────────── */
        $('#bf_mobile_number').on('input', function() { let v = this.value.replace(/[^0-9+]/g, ''); if (v.indexOf('+') > 0) v = v.replace(/\+/g, ''); v = v.startsWith('+') ? '+' + v.slice(1).replace(/\D/g, '').slice(0, 14) : v.replace(/\D/g, '').slice(0, 14); this.value = v; });


        $('#bf-number-section').on('submit', function(e){
            e.preventDefault();

            var $form = $(this);
            var $btn = $form.find('button[type="submit"]'); // submit button
            var $mobileInput = $('#bf_mobile_number');
            var mobile = $mobileInput.val().trim();

            // Remove previous error messages
            $mobileInput.next('.text-danger').remove();
            $('#bf-message').remove(); // Clear previous global messages

            // Bangladesh mobile number regex: starts with 01 and total 11 digits
            var bdMobileRegex = /^01[0-9]{9}$/;

            if(!mobile){
                $mobileInput.after('<span class="text-danger">আপনার মোবাইল নম্বরটি দিন</span>');
                return;
            }

            if(!bdMobileRegex.test(mobile)){
                $mobileInput.after('<span class="text-danger">আপনার দেওয়া মোবাইল নম্বরটি ভুল হয়েছে</span>');
                return;
            }

            $.ajax({
                url: brainfwd_login_obj.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'brainfwd_send_otp',
                    nonce: brainfwd_login_obj.nonce,
                    mobile_no: mobile
                },
                beforeSend: function(){
                    $btn.prop('disabled', true).addClass('loading');
                },
                success: function(res){
                    $btn.prop('disabled', false).removeClass('loading');
                    
                    var message = $('<div id="bf-message" class="mt-4"></div>');

                    if(res.success && res.data.exists){
                        $('#bf-message').remove();
                        $('#bf-number-section').slideUp();
                        $('#bf-password-section').slideDown();
                        message.addClass('text-info').text(res.data.message || 'এই মোবাইল নম্বরটি ইতিমধ্যে রেজিস্ট্রেশন হয়েছে। অনুগ্রহ করে পাসওয়ার্ড দিয়ে লগইন করুন।');
                        $('#bf-password-section').append(message);
                    } else if(res.success){
                        $('#bf-message').remove();
                        message.addClass('text-success').text(res.data.message || 'ওটিপি সফলভাবে পাঠানো হয়েছে!');
                        $('#bf-number-section').slideUp();
                        $('#bf-otp-section').slideDown();
                        $('#bf-otp-section').after(message);
                        var countdown = 30;
                        var countdownMessage = $('<div id="bf-countdown" class="mt-4 text-info">পুনরায় OTP পাঠাতে পারবেন ' + countdown + ' সেকেন্ড পরে</div>');
                        $('#bf-otp-section').after(countdownMessage);
                        var interval = setInterval(function(){
                            countdown--;
                            countdownMessage.text('পুনরায় OTP পাঠাতে পারবেন ' + countdown + ' সেকেন্ড পরে');
                            if(countdown <= 0){
                                clearInterval(interval);
                                countdownMessage.remove();
                                $('#bf-otp-section').slideUp();
                                $('#bf-number-section').slideDown();
                                $('#bf-message').remove();
                            }
                        }, 1000);
                    } else {
                        $('#bf-message').remove();
                        message.addClass('text-danger').text(res.data.message || 'ওটিপি পাঠানো ব্যর্থ হয়েছে। অনুগ্রহ করে পুনরায় চেষ্টা করুন।');
                        $form.append(message);
                    }
                },
                error: function(){
                    $('#bf-message').remove();
                    $btn.prop('disabled', false).removeClass('loading');
                    var message = $('<div id="bf-message" class="text-danger mt-4">অনুরোধ ব্যর্থ হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।</div>');
                    $form.append(message);
                }
            });
        });

        $('#bf-forgot-button').on('click', function(e){
            e.preventDefault();

            var $btn = $('#bf-password-button');
            var $mobileInput = $('#bf_mobile_number');
            var mobile = $mobileInput.val().trim();

            // Clear previous messages
            $mobileInput.next('.text-danger').remove();
            $('#bf-message').remove();

            var bdMobileRegex = /^01[0-9]{9}$/;
            if (!mobile) {
                $mobileInput.after('<span class="text-danger">আপনার মোবাইল নম্বরটি দিন</span>');
                return;
            }
            if (!bdMobileRegex.test(mobile)) {
                $mobileInput.after('<span class="text-danger">আপনার দেওয়া মোবাইল নম্বরটি ভুল হয়েছে</span>');
                return;
            }
            $.ajax({
                url: brainfwd_login_obj.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'brainfwd_forgot_password',
                    nonce: brainfwd_login_obj.nonce,
                    mobile_no: mobile
                },
                beforeSend: function(){
                    $btn.prop('disabled', true).addClass('loading');
                },
                success: function(res){
                    $btn.prop('disabled', false).removeClass('loading');
                    $('#bf-message').remove();
                    var message = $('<div id="bf-message" class="mt-4"></div>');

                    if (res.success) {
                        $('#bf-password-section').slideUp();
                        $('#bf-otp-section').slideDown();
                        // Countdown timer
                        var countdown = 30;
                        var countdownMessage = $('<div id="bf-countdown" class="mt-4 text-info">পুনরায় OTP পাঠাতে পারবেন ' + countdown + ' সেকেন্ড পরে</div>');
                        $('#bf-otp-section').after(countdownMessage);

                        var interval = setInterval(function(){
                            countdown--;
                            countdownMessage.text('পুনরায় OTP পাঠাতে পারবেন ' + countdown + ' সেকেন্ড পরে');
                            if(countdown <= 0){
                                clearInterval(interval);
                                countdownMessage.remove();
                                $('#bf-otp-section').slideUp();
                                $('#bf-number-section').slideDown();
                                $('#bf-message').remove();
                            }
                        }, 1000);

                    } else {
                        if (res.data.user_registered === false) {
                            $('#bf-otp-section').slideUp();
                            $('#bf-password-section').slideUp();
                            $('#bf-number-section').slideDown();
                            $('#bf-message').remove();
                        } else {
                            message.addClass('text-danger').text(res.data.message || 'ওটিপি পাঠানো ব্যর্থ হয়েছে। অনুগ্রহ করে পুনরায় চেষ্টা করুন।');
                            $btn.after(message);
                        }
                    }
                },
                error: function(xhr, status, error){
                    $btn.prop('disabled', false).removeClass('loading');
                    $('#bf-message').remove();
                    var message = $('<div id="bf-message" class="text-danger mt-4">অনুরোধ ব্যর্থ হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।</div>');
                    $btn.after(message);
                }
            });

        });


        /** ─────────────────────────────
         * VERIFY OTP & LOGIN (FORM SUBMIT)
         * ───────────────────────────── */
        $('#bf-otp-section').on('submit', function(e){
            e.preventDefault();

            var $form  = $(this);

            var $btn   = $form.find('button[type="submit"]');
            var mobile = $('#bf_mobile_number').val();
            var otp    = $('#bf_otp_code').val();

            // Remove previous messages
            $('#bf-message').remove();

            if(!otp){
                $('#bf-otp_code').after('<span id="bf-message" class="text-danger">অনুগ্রহ করে OTP লিখুন।</span>');
                return;
            }

            $.ajax({
                url: brainfwd_login_obj.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'brainfwd_verify_otp',
                    nonce: brainfwd_login_obj.nonce,
                    mobile: mobile,
                    otp: otp
                },
                beforeSend: function(){
                    $btn.prop('disabled', true).addClass('loading');
                },
                success: function(res){
                    $('#bf-message').remove();
                    $btn.prop('disabled', false).removeClass('loading');

                    var message = $('<div id="bf-message" class="mt-4"></div>');

                    if(res.success){
                        message.addClass('text-success').text(res.data.message || 'সফলভাবে লগইন হয়েছে।');
                        $btn.after(message);
                        setTimeout(function(){
                            window.location.reload();
                        }, 1000);
                    } else {
                        message.addClass('text-danger').text(res.data.message || 'ভুল OTP। অনুগ্রহ করে আবার চেষ্টা করুন।');
                        $('#bf_otp_code').after(message);
                    }
                },
                error: function(){
                    $('#bf-message').remove();
                    $btn.prop('disabled', false).removeClass('loading');
                    var message = $('<div id="bf-message" class="text-danger mt-4">অনুরোধ ব্যর্থ হয়েছে। অনুগ্রহ করে পুনরায় চেষ্টা করুন।</div>');
                    $('#bf_otp_code').after(message);
                }
            });
        });

        /** ─────────────────────────────
         * PASSWORD LOGIN FORM SUBMIT
         * ───────────────────────────── */
        $('#bf-password-section').on('submit', function(e){
            e.preventDefault();

            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');
            var mobile = $('#bf_mobile_number').val();
            var password = $('#bf_mobile_password').val();

            // Remove previous messages
            $('#bf-message').remove();

            if(!password){
                $('#bf_mobile_password').after('<span id="bf-message" class="text-danger">অনুগ্রহ করে পাসওয়ার্ড লিখুন।</span>');
                return;
            }

            $.ajax({
                url: brainfwd_login_obj.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'brainfwd_password_login',
                    nonce: brainfwd_login_obj.nonce,
                    mobile: mobile,
                    password: password
                },
                beforeSend: function(){
                    $btn.prop('disabled', true).addClass('loading');
                },
                success: function(res){
                    $btn.prop('disabled', false).removeClass('loading');

                    $('#bf-message').remove();
                    var message = $('<div id="bf-message" class="mt-4"></div>');

                    if(res.success){
                        message.addClass('text-success').text(res.data.message || 'সফলভাবে লগইন হয়েছে।');
                        $btn.after(message);
                        setTimeout(function(){
                            window.location.reload();
                        }, 1000);
                    } else {
                        message.addClass('text-danger').text(res.data.message || 'ভুল পাসওয়ার্ড। অনুগ্রহ করে আবার চেষ্টা করুন।');
                        $btn.after(message);
                    }
                },
                error: function(){
                    $('#bf-message').remove();
                    $btn.prop('disabled', false).removeClass('loading');
                    var message = $('<div id="bf-message" class="text-danger mt-4">অনুরোধ ব্যর্থ হয়েছে। অনুগ্রহ করে পুনরায় চেষ্টা করুন।</div>');
                    $btn.after(message);
                }
            });
        });



    });
})(jQuery);
