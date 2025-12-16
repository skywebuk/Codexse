;
(function ($) {
    "use strict";
    $(document).on('ready', function () {
        $('.button').append('');

        $(document).on('click', '.quantity .plus, .quantity .minus', function() {
            // Get current quantity
            var qty = $(this).closest('.quantity').find('.qty');
            var val = parseFloat(qty.val());
            var step = parseFloat(qty.attr('step'));
            var min = parseFloat(qty.attr('min'));
            var max = parseFloat(qty.attr('max'));        
            // Get button type (plus or minus)
            var button = $(this).is('.plus') ? 'plus' : 'minus';        
            // Calculate new quantity
            if ('plus' === button) {
                if (max && (val >= max)) {
                    qty.val(max);
                } else {
                    qty.val(val + step);
                }
            } else {
                if (min && (val <= min)) {
                    qty.val(min);
                } else if (val > 1) {
                    qty.val(val - step);
                }
            }        
            // Trigger change event
            qty.trigger('change');
        });        
    });

    $(document).on('ajaxComplete', function() {
        $('.added_to_cart').each(function() {
            const $btn = $(this);

            // Avoid duplicate icon and background
            if ($btn.find('.cloud__bg').length === 0) {
                $btn.prepend('<i class="ri-checkbox-circle-line"></i>');
            }

            // Add class if not already added
            if (!$btn.hasClass('button')) {
                $btn.addClass('button');
            }
        });
    });


})(jQuery);