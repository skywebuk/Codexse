"use strict";
(function ($) {
    $(window).on("elementor/frontend/init", function () {
        // Function to handle sticky sections using position: fixed
        $(window).on('scroll', function () {
            $(".elementor-element[data-sticky]").each(function () {
                var $element = $(this);
                var stickyOption = $element.data('sticky'); // Get the sticky option ('top' or 'bottom')
                var stickyOffset = parseInt($element.data('sticky-offset'), 10) || 0; // Get the offset value
                var scrollTop = $(window).scrollTop(); // Current scroll position

                if (stickyOption === "top") {
                    // Apply sticky-top class if scroll position exceeds offset
                    if (scrollTop > stickyOffset) {
                        $element.addClass('sticky-top');
                    } else {
                        $element.removeClass('sticky-top');
                    }
                } else if (stickyOption === "bottom") {
                    // Apply sticky-bottom class if scroll position exceeds offset
                    if (scrollTop > stickyOffset) {
                        $element.addClass('sticky-bottom');
                    } else {
                        $element.removeClass('sticky-bottom');
                    }
                }
            });
        });
    });
})(jQuery);
