<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.


    class folioedgecore_Widgets_Control{ 
        public function __construct(){
            $this->folioedgecore_Widgets_Control();
        }        
        public function folioedgecore_Widgets_Control(){            
            if ( file_exists( __DIR__ . '/widgets/section-title.php' ) ) {
                require_once __DIR__ . '/widgets/section-title.php';
            } 
            
            if ( file_exists( __DIR__ . '/widgets/accordion.php' ) ) {
                require_once __DIR__ . '/widgets/accordion.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/slider-arrow.php' ) ) {
                require_once __DIR__ . '/widgets/slider-arrow.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/progress-cirlce.php' ) ) {
                require_once __DIR__ . '/widgets/progress-cirlce.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/countdown.php' ) ) {
                require_once __DIR__ . '/widgets/countdown.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/button.php' ) ) {
                require_once __DIR__ . '/widgets/button.php';
            }
                        
            if ( file_exists( __DIR__ . '/widgets/feature-box.php' ) ) {
                require_once __DIR__ . '/widgets/feature-box.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/testimonial.php' ) ) {
                require_once __DIR__ . '/widgets/testimonial.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/service.php' ) ) {
                require_once __DIR__ . '/widgets/service.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/flotingeffect.php' ) ) {
                require_once __DIR__ . '/widgets/flotingeffect.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/post-slider.php' ) ) {
                require_once __DIR__ . '/widgets/post-slider.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/team.php' ) ) {
                require_once __DIR__ . '/widgets/team.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/counter.php' ) ) {
                require_once __DIR__ . '/widgets/counter.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/lightbox.php' ) ) {
                require_once __DIR__ . '/widgets/lightbox.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/shortcode.php' ) ) {
                require_once __DIR__ . '/widgets/shortcode.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/tabs.php' ) ) {
                require_once __DIR__ . '/widgets/tabs.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/range-slider.php' ) ) {
                require_once __DIR__ . '/widgets/range-slider.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/price.php' ) ) {
                require_once __DIR__ . '/widgets/price.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/case-studie.php' ) ) {
                require_once __DIR__ . '/widgets/case-studie.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/history.php' ) ) {
                require_once __DIR__ . '/widgets/history.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/slide-box.php' ) ) {
                require_once __DIR__ . '/widgets/slide-box.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/roadmap.php' ) ) {
                require_once __DIR__ . '/widgets/roadmap.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/progress.php' ) ) {
                require_once __DIR__ . '/widgets/progress.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/image-gallery.php' ) ) {
                require_once __DIR__ . '/widgets/image-gallery.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/experiences.php' ) ) {
                require_once __DIR__ . '/widgets/experiences.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/image-carousel.php' ) ) {
                require_once __DIR__ . '/widgets/image-carousel.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/switch.php' ) ) {
                require_once __DIR__ . '/widgets/switch.php';
            }
            
            if ( file_exists( __DIR__ . '/widgets/audio.php' ) ) {
                require_once __DIR__ . '/widgets/audio.php';
            }
        }
        
    }

new folioedgecore_Widgets_Control();