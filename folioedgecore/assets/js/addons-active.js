;
(function ($) {

    
    var AudioPlayerJS = function ($scope, $) {

        var nodeList = document.querySelectorAll('.folioedgecore_player.folioedgecore_audio');

        for (var i = 0; i < nodeList.length; i++) {
            var item = nodeList[i];
            var plyrSettings = JSON.parse(item.getAttribute('data-settings'));
            var controls = plyrSettings.controls ? plyrSettings.controls : ['play', 'progress', 'mute', 'volume', 'settings'];
            var muted = plyrSettings.muted == 'true' ? true : false;
            var seekTime = plyrSettings.seek_time ? parseInt(plyrSettings.seek_time) : 100;
            var tooltips_controls = plyrSettings.tooltips_controls == 'true' ? true : false;
            var tooltips_seek = plyrSettings.tooltips_seek == 'false' ? false : true;
            var invertTime = plyrSettings.invertTime == 'false' ? false : true;
            var speed_selected = plyrSettings.speed_selected ? parseFloat(plyrSettings.speed_selected) : 1;
            var debug_mode = plyrSettings.debug_mode == 'true' ? true : false;

            const player = new Plyr(item, {
                debug: debug_mode,
                controls: controls,
                muted: muted,
                seekTime: seekTime,
                invertTime: invertTime,
                tooltips: { controls: tooltips_controls, seek: tooltips_seek },
                speed: { selected: speed_selected, options: [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2] },
            });
        }
    }
    
    // Countdown
    var folioedge_switch = function ($scope, $) {
        var switch_main = $scope.find('.folioedge-switch-area');
        var switch_parent = $scope.find('.folioedge-switches');
        var switch_button = switch_parent.find('.switch-item');
        var switch_toggle = switch_parent.find('.swtich-toggle');

        $(switch_button).on('click',function(){
            var button = $(this).data('switch');            
            if(button == 'left'){
                $(switch_parent).removeClass('right');
                $(switch_parent).addClass('left');
                $(switch_main).find('.switch-contents .right-content').slideUp();
                $(switch_main).find('.switch-contents .left-content').slideDown();
            }else{
                $(switch_parent).removeClass('left');
                $(switch_parent).addClass('right');
                $(switch_main).find('.switch-contents .left-content').slideUp();
                $(switch_main).find('.switch-contents .right-content').slideDown();
            }
        });
        
        $(switch_toggle).on('click',function(){
            $(switch_parent).toggleClass('right');
            $(switch_main).find('.switch-contents .right-content').slideToggle();
            $(switch_main).find('.switch-contents .left-content').slideToggle();
        });

    }
    
    

    // Countdown
    var WidgetCountdownMapHandler = function ($scope, $) {
        var countdown_elem = $scope.find('[data-countdown]').eq(0);
        if ( countdown_elem.length > 0 ) {
            countdown_elem.each(function () {
                var $this = $(this);
                var countdownoptions = $this.data('countdown');
                $this.countdown(countdownoptions.folioedgecoredate, function (event) {

                    var finalTime, daysTime, hours, minutes, second;

                    if( countdownoptions.lavelhide == 'yes' ){
                        daysTime = '<span class="folioedge-count days"><span class="count-inner"><span class="time-count">%-D</span> </span></span>';
                        hours = '<span class="folioedge-count hour"><span class="count-inner"><span class="time-count">%-H</span> </span></span>';
                        minutes = '<span class="folioedge-count minutes"><span class="count-inner"><span class="time-count">%M</span> </span></span>';
                        second = '<span class="folioedge-count second"><span class="count-inner"><span class="time-count">%S</span> </span></span>';
                    }else{
                        daysTime = '<span class="folioedge-count days"><span class="count-inner"><span class="time-count">%-D</span> <div class="count-label">'+countdownoptions.folioedgecoredaytxt+ '</div></span></span>';
                        hours = '<span class="folioedge-count hour"><span class="count-inner"><span class="time-count">%-H</span> <div class="count-label">'+countdownoptions.folioedgecorehourtxt+ '</div></span></span>';
                        minutes = '<span class="folioedge-count minutes"><span class="count-inner"><span class="time-count">%M</span> <div class="count-label">'+countdownoptions.folioedgecoreminutestxt+ '</div></span></span>';
                        second = '<span class="folioedge-count second"><span class="count-inner"><span class="time-count">%S</span> <div class="count-label">'+countdownoptions.folioedgecoresecondstxt+ '</div></span></span>';
                    }

                    // Total default target time
                    finalTime = daysTime + hours + minutes + second;

                    if ( countdownoptions.folioedgecoreday != 'yes' ){
                        finalTime = hours + minutes + second;
                    }
                    if ( countdownoptions.folioedgecorehours != 'yes' ){
                        finalTime = daysTime + minutes + second;
                    }
                    if ( countdownoptions.folioedgecoreminiute != 'yes' ){
                        finalTime = daysTime + hours + second;
                    }
                    if ( countdownoptions.folioedgecoresecond != 'yes' ){
                        finalTime = daysTime + hours + minutes;
                    }

                    //Active Countdown
                    $this.html(event.strftime( finalTime ));

                });
            });
        }
    }
    
    

    var slider_control = function ($scope, $) {
        var slider_elem = $scope.find('.swiper-container').eq(0);
        if (slider_elem.length > 0) {
            settings = slider_elem.data('settings');
            var slloop = settings['slloop'];
            var sleffect = settings['sleffect'];
            var slautolaydelay = settings['slautolaydelay'];
            var slanimation_speed = settings['slanimation_speed'];
            var slcustom_arrow = settings['slcustom_arrow'];
            var slider_target_id = settings['sltarget_id'];
            var sldisplay_columns = parseInt(settings['sldisplay_columns']);
            var slcenter = settings['slcenter'];
            var slcenter_padding = parseInt(settings['slcenter_padding']);

            var laptop_width = parseInt(settings['laptop_width']);
            var tablet_width = parseInt(settings['tablet_width']);
            var mobile_width = parseInt(settings['mobile_width']);
            var laptop_padding = parseInt(settings['laptop_padding']);
            var tablet_padding = parseInt(settings['tablet_padding']);
            var mobile_padding = parseInt(settings['mobile_padding']);
            var laptop_display_columns = parseInt(settings['laptop_display_columns']);
            var tablet_display_columns = parseInt(settings['tablet_display_columns']);
            var mobile_display_columns = parseInt(settings['mobile_display_columns']);

            var swiperOptions = {
                loop: slloop,
                speed: slanimation_speed,
                centeredSlides: slcenter,
                slidesPerView: sldisplay_columns,
                spaceBetween: slcenter_padding,
                effect: sleffect, // More Options: 'slide' | 'fade' | 'cube' | 'coverflow' | 'flip'            
                autoplay: {
                    delay: slautolaydelay,
                    disableOnInteraction: false
                },
                navigation: {
                    prevEl: (slcustom_arrow == true && slider_target_id != null ? '#slider-arrow-' + slider_target_id + ' .prev-action' : $scope.find('.swiper-navigation .swiper-prev')),
                    nextEl: (slcustom_arrow == true && slider_target_id != null ? '#slider-arrow-' + slider_target_id + ' .next-action' : $scope.find('.swiper-navigation .swiper-next')),
                },
                pagination: {
                    el: $scope.find('.swiper-pagination'),
                    clickable: true,
                    type: 'bullets',
                    renderBullet: function (i) {
                        return `<span class="dot swiper-pagination-bullet" ><svg> <circle style="animation-duration: `+slautolaydelay/1000+`s;" cx="11" cy="11" r="10"></circle></svg></span>`;
                    }
                },
                breakpoints: {
                    [mobile_width]: {
                        slidesPerView: mobile_display_columns,
                        spaceBetween: mobile_padding,
                    },
                    [tablet_width]: {
                        slidesPerView: tablet_display_columns,
                        spaceBetween: tablet_padding,
                    },
                    [laptop_width]: {
                        slidesPerView: laptop_display_columns,
                        spaceBetween: laptop_padding,
                    },
                },
            };
            var swiper = new Swiper(slider_elem, swiperOptions);
        }
    }

    var folioedge_accordion = function ($scope, $) {
        var accordion_list = $scope.find('.accordion-list').eq(0);
        if (accordion_list.length > 0) {
            accordion_list.each(function () {
                $(this).find('.accoridon-item .title').on('click', function () {
                    $(this).parent('.accoridon-item').siblings('.accoridon-item').find('.desc').slideUp();
                    $(this).parent('.accoridon-item').siblings('.accoridon-item').find('.title').removeClass('active');
                    $(this).siblings('.desc').slideToggle();
                    $(this).toggleClass('active');
                });
            });
        }
    }

    var range_slider = function ($scope, $) {
        var slider_range = $scope.find('.slider_range').eq(0);
            settings = slider_range.data('settings');
            var suffix = settings['suffix'] == '' ? '' : settings['suffix'];
            var prefix = settings['prefix'] == '' ? '' : settings['prefix'];
            var sliderID = settings['target_id'] == '' ? '' : settings['target_id'];
            var starting_number = parseInt(settings['starting_number']);
            var ending_number = parseInt(settings['ending_number']);
        
        slider_range.find('.slider').each(function(){
            var handle = $(this).find( ".ui-slider-handle span" );        
            $(this).slider({
              value: ending_number / 2,
              min: starting_number,
              max: ending_number,
              orientation: "horizontal",
              range: "min",
              animate: true,
              create: function() {
                handle.text( prefix+$( this ).slider( "value" ).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+suffix );
              },
              slide: function( event, ui ) {
                handle.text( prefix+ui.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+suffix );
                $('.price-box').each(function(){
                    var selector = $(this).find('.price-rate .amount');                    
                    var devide = $(this).data('devide');                    
                    var priceID = $(this).data('id');
                    if(priceID == sliderID){
                        selector.text(Math.trunc(ui.value/devide).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                    }
                });
              }
            });
        });
        //$("#priceRange").val("$" + $("#price-range").slider("values", 0));
    }

    var masonry_lists = function ($scope, $) {
        var masonry_lists = $scope.find('.masonry_lists').eq(0);
        if (masonry_lists.length > 0 && typeof imagesLoaded == 'function') {
            masonry_lists.children().addClass('masonry-item');
            var masonry_item = masonry_lists.find('.masonry-item');
            masonry_item.hide();
            masonry_lists.imagesLoaded(function () {
                masonry_item.fadeIn();
                masonry_lists.masonry({
                    itemSelector: '.masonry-item',
                });
            });
        }
    }

    var circle_progress_bar = function ($scope, $) {
        var circle_progress = $scope.find('.cirlce-progress').eq(0);
        settings = circle_progress.data('settings');
        var barColor = settings['barColor'];
        var trackColor = settings['trackColor'];
        var scaleColor = settings['scaleColor'];
        var scaleLength = settings['scaleLength'];
        var lineWidth = settings['lineWidth'];
        var boxSize = settings['boxSize'];
        var lineCap = settings['lineCap'];
        var animateDuration = settings['animateDuration'];
        circle_progress.appear(function () {
            $(this).easyPieChart({
                animate:{
                    duration:animateDuration,
                    enabled:true
                },
                barColor: barColor,
                trackColor: trackColor,
                scaleColor: scaleColor,
                scaleLength:scaleLength,
                lineWidth: lineWidth,
                size: boxSize,
                lineCap: lineCap,
                easing: 'easeOutBounce',
                onStep: function(from, to, percent) {
                    $(this.el).find('.percent .cont').text(Math.round(percent));
                },
            });            
        });
    }

    // Run this code under Elementor.
    $(window).on('elementor/frontend/init', function () {

        var FloatingFx = elementorModules.frontend.handlers.Base.extend({
            onInit: function () {
                elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
                this.run();
            },

            getTheElement: function () {
                return this.$element.find('.elementor-widget-container')[0];
            },

            resetFx: function () {
                anime.remove(this.getTheElement());
                this.getTheElement() && this.getTheElement().removeAttribute('style');
            },

            onDestroy: function () {
                elementorModules.frontend.handlers.Base.prototype.onDestroy.apply(this, arguments);
                this.resetFx();
            },

            onElementChange: function () {
                this.resetFx();
                this.run();
            },

            run: function () {
                var settings = this.getElementSettings(),
                    fxSettings = {
                        targets: this.getTheElement(),
                        loop: true,
                        direction: 'alternate',
                        easing: 'easeInOutSine'
                    };

                if (settings.ha_floating_fx_translate_toggle) {
                    if (settings.ha_floating_fx_translate_x.size) {
                        fxSettings.translateX = {
                            value: settings.ha_floating_fx_translate_x.size,
                            duration: settings.ha_floating_fx_translate_duration.size,
                            delay: settings.ha_floating_fx_translate_delay.size || 0
                        }
                    }
                    if (settings.ha_floating_fx_translate_y.size) {
                        fxSettings.translateY = {
                            value: settings.ha_floating_fx_translate_y.size,
                            duration: settings.ha_floating_fx_translate_duration.size,
                            delay: settings.ha_floating_fx_translate_delay.size || 0
                        }
                    }
                }

                if (settings.ha_floating_fx_rotate_toggle) {
                    if (settings.ha_floating_fx_rotate_x.size) {
                        fxSettings.rotateX = {
                            value: settings.ha_floating_fx_rotate_x.size,
                            duration: settings.ha_floating_fx_rotate_duration.size,
                            delay: settings.ha_floating_fx_rotate_delay.size || 0
                        }
                    }
                    if (settings.ha_floating_fx_rotate_y.size) {
                        fxSettings.rotateY = {
                            value: settings.ha_floating_fx_rotate_y.size,
                            duration: settings.ha_floating_fx_rotate_duration.size,
                            delay: settings.ha_floating_fx_rotate_delay.size || 0
                        }
                    }
                    if (settings.ha_floating_fx_rotate_z.size) {
                        fxSettings.rotateZ = {
                            value: settings.ha_floating_fx_rotate_z.size,
                            duration: settings.ha_floating_fx_rotate_duration.size,
                            delay: settings.ha_floating_fx_rotate_delay.size || 0
                        }
                    }
                }

                if (settings.ha_floating_fx_scale_toggle) {
                    if (settings.ha_floating_fx_scale_x.size) {
                        fxSettings.scaleX = {
                            value: settings.ha_floating_fx_scale_x.size,
                            duration: settings.ha_floating_fx_scale_duration.size,
                            delay: settings.ha_floating_fx_scale_delay.size || 0
                        }
                    }
                    if (settings.ha_floating_fx_scale_y.size) {
                        fxSettings.scaleY = {
                            value: settings.ha_floating_fx_scale_y.size,
                            duration: settings.ha_floating_fx_scale_duration.size,
                            delay: settings.ha_floating_fx_scale_delay.size || 0
                        }
                    }
                }

                if (settings.ha_floating_fx_translate_toggle || settings.ha_floating_fx_rotate_toggle || settings.ha_floating_fx_scale_toggle) {
                    this.getTheElement() && this.getTheElement().style.setProperty('will-change', 'transform');
                    anime(fxSettings);
                }
            }
        });



        elementorFrontend.hooks.addAction('frontend/element_ready/Folioedge_Audio_Player.default', AudioPlayerJS);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge-switch-addons.default', folioedge_switch);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedgecore-countdown-addons.default', WidgetCountdownMapHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge-accordion.default', folioedge_accordion);
        elementorFrontend.hooks.addAction('frontend/element_ready/cirlce-progress-bar.default', circle_progress_bar);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge-campaign-widget.default', masonry_lists);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge-campaign-widget.default', slider_control);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge-testimonial-widget.default', masonry_lists);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge-testimonial-widget.default', slider_control);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge-history-widget.default', masonry_lists);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge-history-widget.default', slider_control);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge-service-widget.default', masonry_lists);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge-service-widget.default', slider_control);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge-post-widget.default', masonry_lists);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge-post-widget.default', slider_control);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge-donate-widget.default', masonry_lists);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge-donate-widget.default', slider_control);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge-team-widget.default', masonry_lists);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge-team-widget.default', slider_control);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge_case_studie_widget.default', masonry_lists);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge_case_studie_widget.default', slider_control);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge-event-widget.default', masonry_lists);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge-event-widget.default', slider_control);
        elementorFrontend.hooks.addAction('frontend/element_ready/folioedge_range_slider.default', range_slider);
        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            new FloatingFx({
                $element: $scope
            });
        });
    });

}(jQuery));