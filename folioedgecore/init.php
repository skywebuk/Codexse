<?php
/*
Plugin Name: Folioedge Core
Description: Creates an interfaces to manage store / business locations on your website. Useful for showing location based information quickly. Includes both a widget and shortcode for ease of use.
Version:     1.0.0
Author:      Ashekur Rahman
Author URI:  https://www.polothemes.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
if( ! defined( 'ABSPATH' ) ) exit(); // Exit if accessed directly


/*-- All-Action-Hooks --*/
add_action( 'plugins_loaded', 'folioedgecore_plugin_loaded' );
add_action( 'wp_enqueue_scripts', 'folioedgecore_enqueue_script' );
add_action( 'admin_enqueue_scripts', 'folioedgecore_admin_scripts' );
add_action( 'widgets_init', 'folioedgecore_widgets_init' );

/*-- Elementor-Widget-Controls --*/
add_action( 'elementor/init','folioedgecore_elementor_init', 10 );
add_action( 'elementor/widgets/widgets_registered', 'folioedgecore_includes_widgets' ); 
add_action( 'elementor/frontend/after_register_scripts', 'folioedgecore_register_fronted_scripts', 10 );
add_action( 'elementor/frontend/after_register_styles', 'folioedgecore_register_frontend_styles', 10 );  
add_action( 'elementor/editor/after_enqueue_styles', 'folioedgecore_admin_scripts', 10 );
add_action( 'elementor/controls/controls_registered', 'folioedgecore_add_fonts_elementor', 10, 1 ); 

function folioedgecore_add_fonts_elementor($controls_registry){
    // retrieve fonts list from Elementor
    $fonts = $controls_registry->get_control( 'font' )->get_settings( 'options' );
    // add your new custom font
    $new_fonts = array_merge( [ 'satoshi' => 'system' ], $fonts );
    $new_fonts = array_merge( [ 'recoleta' => 'system' ], $new_fonts );
    // return the new list of fonts
    $controls_registry->get_control( 'font' )->set_settings( 'options', $new_fonts );
}


function folioedgecore_elementor_init(){
    \Elementor\Plugin::instance()->elements_manager->add_category( 'folioedgecore',[ 'title'  => 'folioedge' ], 1 );
    require_once( dirname(__FILE__) . '/inc/plugin-icon-manager.php');
}

function folioedgecore_includes_widgets(){
    require_once( dirname(__FILE__).'/addons/widgets_control.php' );
}

function folioedgecore_register_frontend_styles(){
    // Add Lity, Used for lightbox popup
    wp_register_style( 'lity', plugins_url( '/assets/css/lity-min.css', __FILE__ ), array(), '2.3.1' );     
}

function folioedgecore_register_fronted_scripts(){
    wp_register_script( 'countdown', plugins_url( '/assets/js/countdown.js', __FILE__ ), array('jquery'), '1.0.0', true );
    wp_register_script( 'isotope', plugins_url( '/assets/js/isotope-min.js', __FILE__ ), array('jquery'), '1.0.0', true );
    wp_register_script( 'addons-active', plugins_url( '/assets/js/addons-active.js', __FILE__ ), array('jquery'), '1.0.0', true );
    // Add Lity, Used for lightbox popup.
    wp_register_script( 'lity', plugins_url( '/assets/js/lity-min.js', __FILE__ ), array('jquery'), '2.3.1', true );     
    wp_register_script( 'bootstrap-js', plugins_url( '/assets/js/bootstrap-min.js', __FILE__ ), array('jquery'), '3.4.1', true );     
    wp_register_script( 'plyr', plugins_url( '/assets/js/plyr.min.js', __FILE__ ), array('jquery'), '3.4.1', true );     
    wp_register_script( 'polyfilled', plugins_url( '/assets/js/plyr.polyfilled.min.js', __FILE__ ), array('jquery'), '3.4.1', true );     
}

function folioedgecore_widgets_init(){
    register_widget( 'folioedge_social_menu' );
    register_widget( 'folioedge_author_info' );
    register_widget( 'folioedge_popular_posts' );
}

function folioedgecore_enqueue_script(){
    $wp_scripts = wp_scripts();
    wp_enqueue_style('folioedge-ui-css', plugins_url( '/assets/css/jquery-ui.css', __FILE__ ), false, '1.13.0', false);
    // Add folioedge Core Style, Used For Stylist Dropdown Select Box
    wp_enqueue_style( 'folioedgecore-audio', plugins_url( '/assets/css/audio.css', __FILE__ ), array(), '1.0.0' );
    wp_enqueue_style( 'folioedgecore-main', plugins_url( '/assets/css/main.css', __FILE__ ), array(), '1.0.0' );
    wp_enqueue_style( 'swiper', plugins_url( '/assets/css/swiper-bundle-min.css', __FILE__ ), array(), '1.0.0' );
            
    wp_register_script( 'jquery-easing', plugins_url( '/assets/js/easing-min.js', __FILE__ ), array('jquery'), '1.3.0', true );
    wp_register_script( 'easypiechart', plugins_url( '/assets/js/easypiechart-min.js', __FILE__ ), array('jquery'), '1.0.0', true );
    wp_register_script( 'anime', plugins_url( '/assets/js/anime.js', __FILE__ ), array('jquery'), '1.0.0', true );
	wp_enqueue_script( 'swiper', plugins_url( '/assets/js/swiper-bundle-min.js', __FILE__ ), array('jquery'), '1.0.0', true );
        
	// folioedgecore-Core-Active-Script
	wp_enqueue_script( 'folioedgecore-active', plugins_url( '/assets/js/plugin-core.js', __FILE__ ), array('jquery'), '1.0.0', true );
}

function folioedgecore_plugin_loaded(){
    load_plugin_textdomain( 'folioedgecore', false, basename(dirname(__FILE__)) . '/language/' );
    require_once( dirname(__FILE__) . '/inc/plugin-functions.php');
    require_once( dirname(__FILE__) . '/inc/metabox.php');
    require_once( dirname(__FILE__) . '/inc/service-post-type.php');
    require_once( dirname(__FILE__) . '/inc/case-studies-post-type.php');
    require_once( dirname(__FILE__) . '/inc/team-post-type.php');
    require_once( dirname(__FILE__) . '/widgets/popular-post.php');
    require_once( dirname(__FILE__) . '/widgets/social-menu.php');
    require_once( dirname(__FILE__) . '/widgets/profile.php');
}

function folioedgecore_admin_scripts(){
    wp_enqueue_style('folioedge_admin_style',plugins_url( '/assets/css/plugin-admin.css', __FILE__ ),array(),'1.0','all');
    wp_enqueue_script('folioedge_admin_script', plugins_url( '/assets/js/plugin-admin.js', __FILE__ ) ,array('jquery'),'1.0',true );
}

function folioedge_add_file_types_to_uploads($file_types){
	$new_filetypes = array();
	$new_filetypes['svg'] = 'image/svg+xml';
	$file_types = array_merge($file_types, $new_filetypes );
	return $file_types;
}
add_action('upload_mimes', 'folioedge_add_file_types_to_uploads');