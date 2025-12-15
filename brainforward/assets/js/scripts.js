;
(function ($) {
    "use strict";
    $(document).on('ready', function () {

        $('.navbar__search-toggle,.navbar__search-close').on('click',function(){
            $('.navbar__search').slideToggle();
        });

        $('.navbar__sidebar-toggle,.navbar__sidebar-close').on('click',function(){
            $('.navbar__sidebar').toggleClass('active');
        });

        

        
        function setupMobileMenuToggle() {
            const isMobile = window.innerWidth < 992;

            if (isMobile) {
                $('.navbar__menu li .sub__menu-toggle').off('click').on('click', function (e) {
                    e.preventDefault();
                    $(this).siblings('ul').stop(true, true).slideToggle();
                    $(this).toggleClass('active');
                });

                $('.navbar__mobile-toggle').off('click').on('click', function () {
                    $('.main__navbar .navbar__menu').stop(true, true).slideToggle();
                });

            } else {
                // Reset submenu styles and events
                $('.navbar__menu li ul').removeAttr('style');
                $('.navbar__menu li .sub__menu-toggle').removeClass('active').off('click');
                $('.main__navbar .navbar__menu').removeAttr('style');
                $('.navbar__mobile-toggle').off('click');
            }
        }
        // Initial call
        setupMobileMenuToggle();
        // Re-run on window resize
        $(window).on('resize', function () {
            setupMobileMenuToggle();
        });

        
        if (localStorage.getItem('headerAlertClosed') !== 'true') {
            $('.alert__bar').slideDown();
        } else {
            $('.alert__bar').hide();
        }

        $('.alert__close').on('click', function () {
            $(this).closest('.alert__bar').slideUp();
            localStorage.setItem('headerAlertClosed', 'true');
        });


        // Select all links with hashes
        $('.main__navbar .navbar__menu  a[href*="#"]').not('[href="#"]').not('[href="#0"]').on('click', function (event) { if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) { var target = $(this.hash); target = target.length ? target : $('[name=' + this.hash.slice(1) + ']'); if (target.length) { event.preventDefault(); $('html, body').animate({ scrollTop: target.offset().top }, 1000, function () { var $target = $(target); $target.focus(); if ($target.is(":focus")) { return false; } else { $target.attr('tabindex', '-1'); $target.focus(); }; }); } } });
        

        $('.form-submit .submit').addClass('primary_button small-button');

        //Scroll back to top
        var progressPath = document.querySelector('.progress-wrap path');
        if (progressPath) {
            var pathLength = progressPath.getTotalLength();
            progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
            progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
            progressPath.style.strokeDashoffset = pathLength;
            progressPath.getBoundingClientRect();
            progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';
            var updateProgress = function () {
                var scroll = $(window).scrollTop();
                var height = $(document).height() - $(window).height();
                var progress = pathLength - (scroll * pathLength / height);
                progressPath.style.strokeDashoffset = progress;
            }
            updateProgress();
            $(window).scroll(updateProgress);
            var offset = 50;
            var duration = 550;
            jQuery(window).on('scroll', function () {
                if (jQuery(this).scrollTop() > offset) {
                    jQuery('.progress-wrap').addClass('active-progress');
                } else {
                    jQuery('.progress-wrap').removeClass('active-progress');
                }
            });
            jQuery('.progress-wrap').on('click', function (event) {
                event.preventDefault();
                jQuery('html, body').animate({
                    scrollTop: 0
                }, duration);
                return false;
            });
        }

    });

    $(window).on("load", function () {
        $(".preloader").fadeOut(500);
        $('body').removeClass('overflow-hidden');
        $(".post-single").fitVids();
    });

    var lastScrollTop = 0;
    var $navbar = $('.main__navbar');

    $(window).on('scroll', function () {
        var scrollTop = $(this).scrollTop();
        var isStickyEnabled = $navbar.data('sticky') === 'enabled';

        if (isStickyEnabled) {
            if (scrollTop > 100) {
                $navbar.addClass('sticky');

                if (scrollTop < lastScrollTop) {
                    // Scrolling up
                    $navbar.addClass('visible');
                } else {
                    // Scrolling down
                    $navbar.removeClass('visible');
                }
            } else {
                $navbar.removeClass('sticky visible');
            }

            lastScrollTop = scrollTop;
        }
    });


})(jQuery);