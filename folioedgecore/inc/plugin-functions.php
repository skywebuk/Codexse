<?php

/*
 * HTML Tag list
 * return array
 */
function folioedge_html_tag_lists() {
    $html_tag_list = [
        'h1'   => esc_html__( 'H1', 'eleven-addons' ),
        'h2'   => esc_html__( 'H2', 'eleven-addons' ),
        'h3'   => esc_html__( 'H3', 'eleven-addons' ),
        'h4'   => esc_html__( 'H4', 'eleven-addons' ),
        'h5'   => esc_html__( 'H5', 'eleven-addons' ),
        'h6'   => esc_html__( 'H6', 'eleven-addons' ),
        'p'    => esc_html__( 'p', 'eleven-addons' ),
        'div'  => esc_html__( 'div', 'eleven-addons' ),
        'span' => esc_html__( 'span', 'eleven-addons' ),
    ];
    return $html_tag_list;
}

if( !function_exists('campaign_single_cat') ){
    function campaign_single_cat( $separetor, $type = 'name' ){
        $related_taxs = wp_get_post_terms( get_the_ID(), 'campaign_category' );
        $related_cats = array();
        foreach ( $related_taxs as $related_tax ) {
            $related_cats[] =   $related_tax->$type ;   
        } 
        return implode( $separetor , $related_cats);
    }
}

function folioedge_post_share_social(){
        global $post;
        /* Get current page URL */
        $crunchifyURL = get_permalink();
        $crunchifyImage = get_the_post_thumbnail('full');
        $crunchifyDesc = get_the_content();
        /* Get current page title*/
        $crunchifyTitle = str_replace( ' ', '%20', get_the_title());
        $twitterURL = 'https://twitter.com/intent/tweet?text='.$crunchifyTitle.'&amp;url='.$crunchifyURL.'&amp;via=Crunchify';
        $facebookURL = 'https://www.facebook.com/sharer/sharer.php?u='.$crunchifyURL;
        $linkedinURL = 'https://www.linkedin.com/shareArticle?mini=true&url='.$crunchifyURL;
        $pinterestURL = 'https://pinterest.com/pin/create/button/?url='.$crunchifyURL.'&media='.$crunchifyImage.'&description='.$crunchifyDesc;
        ?>
        <div class="post_share-menu">
            <div class="share-icon"><i class="fal fa-share-alt"></i></div>
            <div class="social-items">
                <a href="<?php echo esc_url($twitterURL); ?>"><i class="fab fa-twitter"></i></a>
                <a href="<?php echo esc_url($facebookURL); ?>"><i class="fab fa-facebook-f"></i></a>
                <a href="<?php echo esc_url($pinterestURL); ?>"><i class="fab fa-pinterest-p"></i></a>
                <a href="<?php echo esc_url($linkedinURL); ?>"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
		<?php
}


function folioedge_get_post_views($postID){
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0 View";
    }
    return $count.' Views';
}

function folioedge_set_post_views($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}


//To keep the count accurate, lets get rid of prefetching
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

/*
 * Get Taxonomy
 * return array
 */
function folioedge_get_taxonomies( $folioedge_texonomy = 'category' ){
    $terms = get_terms( array(
        'taxonomy' => $folioedge_texonomy,
        'hide_empty' => true,
    ));
    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
        foreach ( $terms as $term ) {
            $options[ $term->slug ] = $term->name;
        }
        return $options;
    }
}


function folioedge_get_title( $folioedge_type = 'post' ){
    // query for your post type
    $post_type_query  = new WP_Query(  
        array (  
            'post_type'      => $folioedge_type,  
            'posts_per_page' => -1  
        )  
    );   
    // we need the array of posts
    $posts_array = $post_type_query->posts;   
    // create a list with needed information
    // the key equals the ID, the value is the post_title
    $post_title = wp_list_pluck( $posts_array, 'post_title', 'ID' );
    
    return $post_title;
}

/*
 * All Post Name
 * return array
 */
if( !function_exists('folioedge_post_name') ){
    function folioedge_post_name ( $post_type = 'post', $limit = '-1' ){
        $options = array();
        $options = ['0' => esc_html__( 'None', 'folioedgecore-addons' )];
        $wh_post = array( 'posts_per_page' => $limit, 'post_type'=> $post_type );
        $wh_post_terms = get_posts( $wh_post );
        if ( ! empty( $wh_post_terms ) && ! is_wp_error( $wh_post_terms ) ){
            foreach ( $wh_post_terms as $term ) {
                $options[ $term->ID ] = $term->post_title;
            }
            return $options;
        }
    }
}


/*
 * Get Post Type
 * return array
 */
function folioedge_get_post_types( $args = [] ) {   
    $post_type_args = [
        'show_in_nav_menus' => true,
    ];
    if ( ! empty( $args['post_type'] ) ) {
        $post_type_args['name'] = $args['post_type'];
    }
    $_post_types = get_post_types( $post_type_args , 'objects' );
    $post_types  = [];
    foreach ( $_post_types as $post_type => $object ) {
        $post_types[ $post_type ] = $object->label;
    }
    return $post_types;
}

/*
 * Elementor Templates List
 * return array
 */
function folioedge_elementor_template() {
    $templates = \Elementor\Plugin::instance()->templates_manager->get_source( 'local' )->get_items();
    $types = array();
    if ( empty( $templates ) ) {
        $template_lists = [ '0' => esc_html__( 'Do not Saved Templates.', 'folioedgecore' ) ];
    } else {
        $template_lists = [ '0' => esc_html__( 'Select Template', 'folioedgecore' ) ];
        foreach ( $templates as $template ) {
            $template_lists[ $template['template_id'] ] = $template['title'] . ' (' . $template['type'] . ')';
        }
    }
    return $template_lists;
}


if(!function_exists('folioedge_page_breadcrumb')){
    function folioedge_page_breadcrumb( $home = 'Home', $separator = ' | ' ){
        // Set variables for later use
        $here_text        = esc_html__( 'You are currently here!', '' );
        $home_link        = home_url('/');
        $home_text        = wp_kses_post($home);
        $link_before      = '<span typeof="v:Breadcrumb">';
        $link_after       = '</span>';
        $link_attr        = ' rel="v:url" property="v:title"';
        $link             = $link_before . '<a' . $link_attr . ' href="%1$s">%2$s</a>' . $link_after;
        $delimiter        = '<span class="separator">'.wp_kses_post($separator).'</span>';             // Delimiter between crumbs
        $before           = '<span class="current">'; // Tag before the current crumb
        $after            = '</span>';                // Tag after the current crumb
        $page_addon       = '';                       // Adds the page number if the query is paged
        $breadcrumb_trail = '';
        $category_links   = '';
        /** 
         * Set our own $wp_the_query variable. Do not use the global variable version due to 
         * reliability
         */
        $wp_the_query   = $GLOBALS['wp_the_query'];
        $queried_object = $wp_the_query->get_queried_object();

        // Handle single post requests which includes single pages, posts and attatchments
        if ( is_singular() ) 
        {
            /** 
             * Set our own $post variable. Do not use the global variable version due to 
             * reliability. We will set $post_object variable to $GLOBALS['wp_the_query']
             */
            $post_object = sanitize_post( $queried_object );
            // Set variables 
            $title          = apply_filters( 'the_title', $post_object->post_title );
            $parent         = $post_object->post_parent;
            $post_type      = $post_object->post_type;
            $post_id        = $post_object->ID;
            $post_link      = $before . $title . $after;
            $parent_string  = '';
            $post_type_link = '';
            if ( 'post' === $post_type ) 
            {
                // Get the post categories
                $categories = get_the_category( $post_id );
                if ( $categories ) {
                    // Lets grab the first category
                    $category  = $categories[0];

                    $category_links = get_category_parents( $category, true, $delimiter );
                    $category_links = str_replace( '<a',   $link_before . '<a' . $link_attr, $category_links );
                    $category_links = str_replace( '</a>', '</a>' . $link_after,             $category_links );
                }
            }
            if ( !in_array( $post_type, ['post', 'page', 'attachment'] ) )
            {
                $post_type_object = get_post_type_object( $post_type );
                $archive_link     = esc_url( get_post_type_archive_link( $post_type ) );

                $post_type_link   = sprintf( $link, $archive_link, $post_type_object->labels->singular_name );
            }
            // Get post parents if $parent !== 0
            if ( 0 !== $parent ) 
            {
                $parent_links = [];
                while ( $parent ) {
                    $post_parent = get_post( $parent );
                    $parent_links[] = sprintf( $link, esc_url( get_permalink( $post_parent->ID ) ), get_the_title( $post_parent->ID ) );
                    $parent = $post_parent->post_parent;
                }
                $parent_links = array_reverse( $parent_links );
                $parent_string = implode( $delimiter, $parent_links );
            }
            // Lets build the breadcrumb trail
            if ( $parent_string ) {
                $breadcrumb_trail = $parent_string . $delimiter . $post_link;
            } else {
                $breadcrumb_trail = $post_link;
            }

            if ( $post_type_link )
                $breadcrumb_trail = $post_type_link . $delimiter . $breadcrumb_trail;

            if ( $category_links )
                $breadcrumb_trail = $category_links . $breadcrumb_trail;
        }
        // Handle archives which includes category-, tag-, taxonomy-, date-, custom post type archives and author archives
        if( is_archive() )
        {
            if (    is_category()
                 || is_tag()
                 || is_tax()
            ) {
                // Set the variables for this section
                $term_object        = get_term( $queried_object );
                $taxonomy           = $term_object->taxonomy;
                $term_id            = $term_object->term_id;
                $term_name          = $term_object->name;
                $term_parent        = $term_object->parent;
                $taxonomy_object    = get_taxonomy( $taxonomy );
                $current_term_link  = $before . $taxonomy_object->labels->singular_name . ': ' . $term_name . $after;
                $parent_term_string = '';
                if ( 0 !== $term_parent )
                {
                    // Get all the current term ancestors
                    $parent_term_links = [];
                    while ( $term_parent ) {
                        $term = get_term( $term_parent, $taxonomy );

                        $parent_term_links[] = sprintf( $link, esc_url( get_term_link( $term ) ), $term->name );

                        $term_parent = $term->parent;
                    }
                    $parent_term_links  = array_reverse( $parent_term_links );
                    $parent_term_string = implode( $delimiter, $parent_term_links );
                }
                if ( $parent_term_string ) {
                    $breadcrumb_trail = $parent_term_string . $delimiter . $current_term_link;
                } else {
                    $breadcrumb_trail = $current_term_link;
                }
            } elseif ( is_author() ) {
                $breadcrumb_trail = esc_html__( 'Author archive for ','folioedge') .  $before . $queried_object->data->display_name . $after;
            } elseif ( is_date() ) {
                // Set default variables
                $year     = $wp_the_query->query_vars['year'];
                $monthnum = $wp_the_query->query_vars['monthnum'];
                $day      = $wp_the_query->query_vars['day'];

                // Get the month name if $monthnum has a value
                if ( $monthnum ) {
                    $date_time  = DateTime::createFromFormat( '!m', $monthnum );
                    $month_name = $date_time->format( 'F' );
                }

                if ( is_year() ) {

                    $breadcrumb_trail = $before . $year . $after;

                } elseif( is_month() ) {

                    $year_link        = sprintf( $link, esc_url( get_year_link( $year ) ), $year );

                    $breadcrumb_trail = $year_link . $delimiter . $before . $month_name . $after;

                } elseif( is_day() ) {

                    $year_link        = sprintf( $link, esc_url( get_year_link( $year ) ),             $year       );
                    $month_link       = sprintf( $link, esc_url( get_month_link( $year, $monthnum ) ), $month_name );

                    $breadcrumb_trail = $year_link . $delimiter . $month_link . $delimiter . $before . $day . $after;
                }
            } elseif ( is_post_type_archive() ) {
                $post_type        = $wp_the_query->query_vars['post_type'];
                $post_type_object = get_post_type_object( $post_type );
                $breadcrumb_trail = $before . $post_type_object->labels->singular_name . $after;
            }
        }
        // Handle the search page
        if ( is_search() ) {
            $breadcrumb_trail = esc_html__( 'Search query for: ','folioedge' ) . $before . get_search_query() . $after;
        }
        // Handle 404's
        if ( is_404() ) {
            $breadcrumb_trail = $before . esc_html__( 'Error 404','folioedge' ) . $after;
        }
        // Handle paged pages
        if ( is_paged() ) {
            $current_page = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' );
            $page_addon   = $before . sprintf( esc_html__( ' ( Page %s )','folioedge' ), number_format_i18n( $current_page ) ) . $after;
        }
        $breadcrumb_output_link  = '';
        $breadcrumb_output_link .= '<div class="breadcumbs">';
        if (is_home() or is_front_page() ) {
            // Do not show breadcrumbs on page one of home and frontpage
            if ( is_paged() ) {
                $breadcrumb_output_link .= '<a href="' . $home_link . '">' . $home_text . '</a>';
                $breadcrumb_output_link .= $page_addon;
            }else{
                $breadcrumb_output_link .= $home_text;
                $breadcrumb_output_link .= $page_addon;            
            }
        } else {
            $breadcrumb_output_link .= '<a href="' . $home_link . '" >' . $home_text . '</a>';
            $breadcrumb_output_link .= $delimiter;
            $breadcrumb_output_link .= $breadcrumb_trail;
            $breadcrumb_output_link .= $page_addon;
        }
        $breadcrumb_output_link .= '</div>';

        return $breadcrumb_output_link;
    } 
}


