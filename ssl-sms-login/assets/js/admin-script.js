/**
 * SSL SMS Login - Admin JavaScript
 *
 * @package SSL_SMS_Login
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize color pickers
        $('.ssl-color-picker').wpColorPicker();

        // Form style preview
        $('#form_style').on('change', function() {
            var style = $(this).val();
            $('.ssl-form-preview').removeClass('modern minimal rounded bordered gradient').addClass(style);
        });

        // Tab navigation
        $('.ssl-admin-tab').on('click', function(e) {
            e.preventDefault();
            var tabId = $(this).data('tab');

            $('.ssl-admin-tab').removeClass('active');
            $(this).addClass('active');

            $('.ssl-admin-tab-content').hide();
            $('#' + tabId).show();
        });
    });

})(jQuery);
