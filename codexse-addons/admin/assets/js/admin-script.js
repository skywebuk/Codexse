jQuery(document).ready(function($) {
    // Handle form submission
    $('.codexse-option-form').on('submit', function(e) {
        e.preventDefault();

        let widgets = {};
        let features = {};
        // Collect widgets from checkboxes
        $('.codexse-widgets input[type="checkbox"]').each(function() {
            widgets[$(this).attr('id')] = $(this).is(':checked') ? 'on' : 'off';
        });

        $('.codexse-features input[type="checkbox"]').each(function() {
            features[$(this).attr('id')] = $(this).is(':checked') ? 'on' : 'off';
        });

        // AJAX request to save widgets
        $.ajax({
            url: codexse_setting.ajax_url,
            method: 'POST',
            data: {
                action: 'codexse_save_setting',
                nonce: codexse_setting.nonce,
                widgets: widgets,
                features: features
            },
            success: function(response) {
                let messageText = response.success ? response.data : 'Error: ' + response.data;
                let messageColor = response.success ? 'green' : 'red';
                displayTemporaryMessage(messageText, messageColor);
            },
            error: function(xhr, status, error) {
                displayTemporaryMessage('AJAX Error: ' + error, 'red');
            }
        });
    });

    // Toggle all checkboxes
    $('#toggleAll').on('change', function() {
        let isChecked = $(this).is(':checked');
        $('.codexse-checkboxs input[type="checkbox"]').prop('checked', isChecked);
    });

    // Function to display message for 10 seconds
    function displayTemporaryMessage(text, color) {
        // Create the message div
        let messageDiv = $('<div>', {
            id: 'codexse-message',
            text: text,
            css: {
                display: 'none',
                color: color,
                position: 'fixed',
                bottom: '15px',
                right: '15px',
                backgroundColor: '#fff',
                padding: '18px 20px',
                borderLeft: '4px solid',
                borderColor: color,
                zIndex: 1000,
                fontSize: '16px'
            }
        });

        // Append the div to the body and show it
        $('body').append(messageDiv);
        messageDiv.fadeIn();

        // Remove the div after 10 seconds
        setTimeout(function() {
            messageDiv.fadeOut(function() {
                $(this).remove();
            });
        }, 1000); // 10000ms = 10 seconds
    }
});
