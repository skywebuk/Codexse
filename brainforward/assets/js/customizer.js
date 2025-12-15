(function ($) {
    "use strict";

    // Update site title in real-time when changed in Theme Customizer.
    wp.customize('blogname', function (value) {
        value.bind(function (to) {
            $('.navbar__logo a').text(to);
        });
    });

    // Update site description in real-time when changed in Theme Customizer.
    wp.customize('blogdescription', function (value) {
        value.bind(function (to) {
            $('.site-header__description').text(to);
        });
    });


})(jQuery);