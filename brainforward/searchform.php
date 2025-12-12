<?php
add_filter('get_search_form','brainforward_search_form');
if( !function_exists('brainforward_search_form') ){
    function brainforward_search_form(){            
        $data = '<form role="search" method="get" class="search_form" action="'.esc_url(home_url("/")).'">';
        $data .= '<input type="search" name="s" class="form_control" placeholder="'.esc_attr__("Search Here...","brainforward").'" value="'.esc_attr(get_search_query()).'">';
        $data .= '<button type="submit" class="search_submit">';
		$data .= '<i class="ri-search-2-line icon"></i>';
        $data .= '</button>';         
        $data .= '</form>';
        return $data;
   }
}
