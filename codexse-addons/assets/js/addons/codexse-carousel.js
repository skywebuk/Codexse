;
(function ($) {
    var carousel_controler = function ($scope, $) {
        var slider_elem = $scope.find('.codexse-slider-active').eq(0);
        if (slider_elem.length > 0) {
            settings = slider_elem.data('settings');
            var slider_pagination = false;
            var slider_effect = settings['slider_effect'];
            var slider_loop = settings['slider_loop'];
            var slider_autoplay = settings['slider_autoplay'];
            var slider_autoplay_delay = settings['slider_autoplay_delay'];
            var slider_speed = settings['slider_speed'];
            var slider_coverflow_rotate = settings['slider_coverflow_rotate'] ? parseInt(settings['slider_coverflow_rotate']) : 0;
            var slider_coverflow_stretch = settings['slider_coverflow_stretch'] ? parseInt(settings['slider_coverflow_stretch']) : 0;
            var slider_coverflow_depth = settings['slider_coverflow_depth'] ? parseInt(settings['slider_coverflow_depth']) : 0;
            var slider_coverflow_shadow = settings['slider_coverflow_shadow'] == 'yes' ? true : false;

            var slider_custom_navigation = settings['slider_custom_navigation'];
            var slider_navigation_target = settings['slider_navigation_target'];
            var slider_custom_pagination = settings['slider_custom_pagination'];
            var slider_pagination_target = settings['slider_pagination_target'];

            var slider_per_view = parseInt(settings['slider_per_view']);
            var slider_position = settings['slider_position'];
            var slider_direction = settings['slider_direction'] ? settings['slider_direction'] : 'horizontal';
            var slider_center_padding = parseInt(settings['slider_center_padding']);

            if (settings['slider_pagination']) {
                if (settings['slider_pagination_type'] == 'progress') {
                    slider_pagination = {
                        el: (slider_custom_pagination == true && slider_pagination_target != null ? '#slider-pagination-' + slider_pagination_target + ' .codexse-pagination' : $scope.find('.swiper-pagination')),
                        clickable: true,
                        type: 'bullets',
                        renderBullet: function (i) {
                            return `<span class="dot swiper-pagination-bullet" ><svg><circle style="animation-duration: ${slider_autoplay_delay / 1000}s;" cx="11" cy="11" r="10"></circle></svg></span>`;
                        }
                    };
                } else if (settings['slider_pagination_type'] == 'number') {
                    slider_pagination = {
                        el: (slider_custom_pagination == true && slider_pagination_target != null ? '#slider-pagination-' + slider_pagination_target + ' .codexse-pagination' : $scope.find('.swiper-pagination')),
                        clickable: true,
                        renderBullet: function (index, className) {
                            var number = (index + 1 < 10) ? '0' + (index + 1) : (index + 1);
                            return '<span class="' + className + '">' + number + '</span>';
                        },
                    };
                } else {
                    slider_pagination = {
                        el: (slider_custom_pagination == true && slider_pagination_target != null ? '#slider-pagination-' + slider_pagination_target + ' .codexse-pagination' : $scope.find('.swiper-pagination')),
                        clickable: true,
                    };
                }
            }

            var slider_laptop_brack = parseInt(settings['slider_laptop_brack']);
            var slider_tablet_brack = parseInt(settings['slider_tablet_brack']);
            var slider_mobile_break = parseInt(settings['slider_mobile_break']);
            var slider_laptop_center_padding = parseInt(settings['slider_laptop_center_padding']);
            var slider_tablet_center_padding = parseInt(settings['slider_tablet_center_padding']);
            var slider_mobile_center_padding = parseInt(settings['slider_mobile_center_padding']);
            var slider_laptop_per_view = parseInt(settings['slider_laptop_per_view']);
            var slider_tablet_per_view = parseInt(settings['slider_tablet_per_view']);
            var slider_mobile_per_view = parseInt(settings['slider_mobile_per_view']);
            

            var swiperOptions = {
                loop: slider_loop,
                speed: slider_speed,
                centeredSlides: slider_position,
                slidesPerView: slider_mobile_per_view,
                spaceBetween: slider_mobile_center_padding,
                direction: slider_direction,
                effect: slider_effect, // More Options: 'slide' | 'fade' | 'cube' | 'coverflow' | 'flip' 
                coverflowEffect: {
                    rotate: slider_coverflow_rotate,
                    stretch: slider_coverflow_stretch,
                    depth: slider_coverflow_depth,
                    modifier: 1,
                    slideShadows: slider_coverflow_shadow,
                },
                cubeEffect: {
                    slideShadows: slider_coverflow_shadow, // Disable shadows
                },
                flipEffect: {
                    slideShadows: slider_coverflow_shadow, // Disable shadows
                },
                autoplay: {
                    enabled: slider_autoplay, // Autoplay is disabled initially
                    delay: slider_autoplay_delay,
                    disableOnInteraction: false,
                },
                navigation: {
                    prevEl: (slider_custom_navigation == true && slider_navigation_target != null ? '#slider-navigation-' + slider_navigation_target + ' .prev-action' : $scope.find('.swiper-navigation .swiper-prev')),
                    nextEl: (slider_custom_navigation == true && slider_navigation_target != null ? '#slider-navigation-' + slider_navigation_target + ' .next-action' : $scope.find('.swiper-navigation .swiper-next')),
                },
                pagination: slider_pagination,
                breakpoints: {
                    [slider_mobile_break]: {
                        slidesPerView: slider_tablet_per_view,
                        spaceBetween: slider_tablet_center_padding,
                    },
                    [slider_tablet_brack]: {
                        slidesPerView: slider_laptop_per_view,
                        spaceBetween: slider_laptop_center_padding,
                    },
                    [slider_laptop_brack]: {
                        slidesPerView: slider_per_view,
                        spaceBetween: slider_center_padding,
                    },
                },
            };


            var swiper = new Swiper(slider_elem, swiperOptions);
        }
    }
    // Run this code under Elementor.
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/codexse_testimonial.default', carousel_controler);
        elementorFrontend.hooks.addAction('frontend/element_ready/codexse_images_slider.default', carousel_controler);
        elementorFrontend.hooks.addAction('frontend/element_ready/codexse_team.default', carousel_controler);
    });
}(jQuery));