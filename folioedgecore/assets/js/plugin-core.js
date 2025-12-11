(function($) {

    "use strict";
    /*----------------------
    Document-Reader-Function
    -----------------------*/
    $(document).on('ready', function(){
        /*====== Services-Slider-Active ======*/
        var blog__slider = new Swiper(".photo_slider", {
            loop: true,
            speed: 800,
            spaceBetween: 0,
            slidesPerView: 1,
            watchSlidesVisibility: true,
            watchSlidesProgress: true,
            navigation: {
                nextEl: ".post_slider_arrow .arrow_next",
                prevEl: ".post_slider_arrow .arrow_prev",
            },
            pagination: {
                el: '.photo_slider .swiper-pagination',
                clickable: true,
            },
        });
        
        $('.post-gird-row.style3 .post-column:nth-child(4n-3)').addClass('default-box');
        $('.post-gird-row.style2 .post-column:nth-child(5n-2)').addClass('default-box');


    });
    /*------------------------
    Window-Load-Function
    -------------------------*/
    $(window).on("load", function () {
        $('.video-post').each(function () {
            var video_src = '';            
            $(this).find(".video-play-bttn").on("click", function (ev) {
                $('.video-post').find("iframe").attr("src","");                
                video_src = $(this).parent('.videoPoster').siblings('iframe').data("src");                
                $(this).parent('.videoPoster').siblings('iframe').attr('src',video_src );
                $(this).parent('.videoPoster').fadeOut(300);
                return false;
            });
        });
    });
    
    
})(jQuery);