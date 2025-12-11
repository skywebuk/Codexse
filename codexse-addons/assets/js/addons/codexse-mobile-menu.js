jQuery(window).on('elementor/frontend/init', function () {
    const codexse_mobile_menu = function($scope, $) {
        const $openButton = $scope.find('.open-button');
        const $closeButton = $scope.find('.close-button');
        const $menu = $scope.find('.codexse-mobile-menu');

        // Show menu and toggle buttons
        $openButton.on('click', function () {
            $menu.toggleClass('menu-open'); // Show menu
        });

        // Hide menu and toggle buttons
        $closeButton.on('click', function () {
            $menu.toggleClass('menu-open'); // Show menu
        });

        // Submenu toggle functionality
        $scope.find('.collapse-arrow').on('click', function(e) {
            e.preventDefault();
            const $submenu = $(this).siblings('.sub-menu');

            // Toggle submenu visibility
            $submenu.slideToggle(300);

            // Toggle icon between '+' and '-' for open/close state
            const $icon = $(this).find('i');
            if ($icon.hasClass('cx-plus')) {
                $icon.removeClass('cx-plus').addClass('cx-minus');
            } else {
                $icon.removeClass('cx-minus').addClass('cx-plus');
            }
        });
    };

    // Hook into Elementor's frontend ready event for the 'codexse_mobile_menu' widget
    elementorFrontend.hooks.addAction('frontend/element_ready/codexse_mobile_menu.default', codexse_mobile_menu);
});
