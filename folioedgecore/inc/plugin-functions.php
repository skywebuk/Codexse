<?php
/**
 * Plugin Helper Functions
 *
 * @package Folioedgecore
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Get HTML Tag options list
 *
 * @return array
 */
function folioedge_html_tag_lists() {
    return array(
        'h1'   => esc_html__( 'H1', 'folioedgecore' ),
        'h2'   => esc_html__( 'H2', 'folioedgecore' ),
        'h3'   => esc_html__( 'H3', 'folioedgecore' ),
        'h4'   => esc_html__( 'H4', 'folioedgecore' ),
        'h5'   => esc_html__( 'H5', 'folioedgecore' ),
        'h6'   => esc_html__( 'H6', 'folioedgecore' ),
        'p'    => esc_html__( 'p', 'folioedgecore' ),
        'div'  => esc_html__( 'div', 'folioedgecore' ),
        'span' => esc_html__( 'span', 'folioedgecore' ),
    );
}

/**
 * Get campaign single category
 *
 * @param string $separator Separator between categories.
 * @param string $type      Type of value to return (name or slug).
 * @return string
 */
if ( ! function_exists( 'campaign_single_cat' ) ) {
    function campaign_single_cat( $separator, $type = 'name' ) {
        $related_taxs = wp_get_post_terms( get_the_ID(), 'campaign_category' );

        if ( is_wp_error( $related_taxs ) || empty( $related_taxs ) ) {
            return '';
        }

        $related_cats = array();
        foreach ( $related_taxs as $related_tax ) {
            $related_cats[] = $related_tax->$type;
        }

        return implode( $separator, $related_cats );
    }
}

/**
 * Display social share buttons
 */
function folioedge_post_share_social() {
    $post_url   = rawurlencode( get_permalink() );
    $post_title = rawurlencode( get_the_title() );
    $post_image = rawurlencode( get_the_post_thumbnail_url( get_the_ID(), 'full' ) );

    $share_links = array(
        'twitter'   => array(
            'url'  => 'https://twitter.com/intent/tweet?text=' . $post_title . '&url=' . $post_url,
            'icon' => 'fab fa-twitter',
            'name' => 'Twitter',
        ),
        'facebook'  => array(
            'url'  => 'https://www.facebook.com/sharer/sharer.php?u=' . $post_url,
            'icon' => 'fab fa-facebook-f',
            'name' => 'Facebook',
        ),
        'pinterest' => array(
            'url'  => 'https://pinterest.com/pin/create/button/?url=' . $post_url . '&media=' . $post_image,
            'icon' => 'fab fa-pinterest-p',
            'name' => 'Pinterest',
        ),
        'linkedin'  => array(
            'url'  => 'https://www.linkedin.com/shareArticle?mini=true&url=' . $post_url,
            'icon' => 'fab fa-linkedin-in',
            'name' => 'LinkedIn',
        ),
    );
    ?>
    <div class="post_share-menu">
        <div class="share-icon">
            <i class="fal fa-share-alt" aria-hidden="true"></i>
            <span class="screen-reader-text"><?php esc_html_e( 'Share', 'folioedgecore' ); ?></span>
        </div>
        <div class="social-items">
            <?php foreach ( $share_links as $network => $link ) : ?>
                <a href="<?php echo esc_url( $link['url'] ); ?>"
                   target="_blank"
                   rel="noopener noreferrer"
                   aria-label="<?php echo esc_attr( sprintf( __( 'Share on %s', 'folioedgecore' ), $link['name'] ) ); ?>">
                    <i class="<?php echo esc_attr( $link['icon'] ); ?>" aria-hidden="true"></i>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/**
 * Get post view count
 *
 * @param int $post_id Post ID.
 * @return string
 */
function folioedge_get_post_views( $post_id ) {
    $count_key = 'post_views_count';
    $count     = get_post_meta( $post_id, $count_key, true );

    if ( empty( $count ) ) {
        delete_post_meta( $post_id, $count_key );
        add_post_meta( $post_id, $count_key, '0' );
        return '0 ' . esc_html__( 'Views', 'folioedgecore' );
    }

    return absint( $count ) . ' ' . esc_html__( 'Views', 'folioedgecore' );
}

/**
 * Increment post view count
 *
 * @param int $post_id Post ID.
 */
function folioedge_set_post_views( $post_id ) {
    // Don't count admin visits
    if ( current_user_can( 'manage_options' ) ) {
        return;
    }

    $count_key = 'post_views_count';
    $count     = get_post_meta( $post_id, $count_key, true );

    if ( empty( $count ) ) {
        delete_post_meta( $post_id, $count_key );
        add_post_meta( $post_id, $count_key, '1' );
    } else {
        $count++;
        update_post_meta( $post_id, $count_key, $count );
    }
}

// Remove prefetching to keep count accurate
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );

/**
 * Get taxonomy terms
 *
 * @param string $taxonomy Taxonomy name.
 * @return array
 */
function folioedge_get_taxonomies( $taxonomy = 'category' ) {
    $terms = get_terms( array(
        'taxonomy'   => $taxonomy,
        'hide_empty' => true,
    ) );

    $options = array();

    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
        foreach ( $terms as $term ) {
            $options[ $term->slug ] = $term->name;
        }
    }

    return $options;
}

/**
 * Get post titles by post type
 *
 * @param string $post_type Post type.
 * @return array
 */
function folioedge_get_title( $post_type = 'post' ) {
    $post_type_query = new WP_Query( array(
        'post_type'      => $post_type,
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ) );

    $post_title = wp_list_pluck( $post_type_query->posts, 'post_title', 'ID' );
    wp_reset_postdata();

    return $post_title;
}

/**
 * Get post names with IDs
 *
 * @param string $post_type Post type.
 * @param int    $limit     Number of posts.
 * @return array
 */
if ( ! function_exists( 'folioedge_post_name' ) ) {
    function folioedge_post_name( $post_type = 'post', $limit = -1 ) {
        $options = array(
            '0' => esc_html__( 'None', 'folioedgecore' ),
        );

        $posts = get_posts( array(
            'posts_per_page' => $limit,
            'post_type'      => $post_type,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ) );

        if ( ! empty( $posts ) && ! is_wp_error( $posts ) ) {
            foreach ( $posts as $post ) {
                $options[ $post->ID ] = $post->post_title;
            }
        }

        return $options;
    }
}

/**
 * Get available post types
 *
 * @param array $args Optional arguments.
 * @return array
 */
function folioedge_get_post_types( $args = array() ) {
    $post_type_args = array(
        'show_in_nav_menus' => true,
    );

    if ( ! empty( $args['post_type'] ) ) {
        $post_type_args['name'] = $args['post_type'];
    }

    $_post_types = get_post_types( $post_type_args, 'objects' );
    $post_types  = array();

    foreach ( $_post_types as $post_type => $object ) {
        $post_types[ $post_type ] = $object->label;
    }

    return $post_types;
}

/**
 * Get Elementor templates list
 *
 * @return array
 */
function folioedge_elementor_template() {
    if ( ! did_action( 'elementor/loaded' ) ) {
        return array( '0' => esc_html__( 'Elementor not loaded.', 'folioedgecore' ) );
    }

    $templates = \Elementor\Plugin::instance()->templates_manager->get_source( 'local' )->get_items();

    if ( empty( $templates ) ) {
        return array( '0' => esc_html__( 'No saved templates.', 'folioedgecore' ) );
    }

    $template_lists = array( '0' => esc_html__( 'Select Template', 'folioedgecore' ) );

    foreach ( $templates as $template ) {
        $template_lists[ $template['template_id'] ] = sprintf(
            '%s (%s)',
            $template['title'],
            $template['type']
        );
    }

    return $template_lists;
}

/**
 * Generate breadcrumb navigation
 *
 * @param string $home      Home link text.
 * @param string $separator Separator between breadcrumb items.
 * @return string
 */
if ( ! function_exists( 'folioedge_page_breadcrumb' ) ) {
    function folioedge_page_breadcrumb( $home = 'Home', $separator = ' | ' ) {
        $home_link        = home_url( '/' );
        $home_text        = wp_kses_post( $home );
        $link_before      = '<span typeof="v:Breadcrumb">';
        $link_after       = '</span>';
        $link_attr        = ' rel="v:url" property="v:title"';
        $link             = $link_before . '<a' . $link_attr . ' href="%1$s">%2$s</a>' . $link_after;
        $delimiter        = '<span class="separator">' . wp_kses_post( $separator ) . '</span>';
        $before           = '<span class="current">';
        $after            = '</span>';
        $page_addon       = '';
        $breadcrumb_trail = '';
        $category_links   = '';

        $wp_the_query   = $GLOBALS['wp_the_query'];
        $queried_object = $wp_the_query->get_queried_object();

        // Handle single posts/pages
        if ( is_singular() ) {
            $post_object = sanitize_post( $queried_object );
            $title       = apply_filters( 'the_title', $post_object->post_title );
            $parent      = $post_object->post_parent;
            $post_type   = $post_object->post_type;
            $post_id     = $post_object->ID;
            $post_link   = $before . esc_html( $title ) . $after;

            $parent_string  = '';
            $post_type_link = '';

            if ( 'post' === $post_type ) {
                $categories = get_the_category( $post_id );
                if ( $categories ) {
                    $category       = $categories[0];
                    $category_links = get_category_parents( $category, true, $delimiter );
                    $category_links = str_replace( '<a', $link_before . '<a' . $link_attr, $category_links );
                    $category_links = str_replace( '</a>', '</a>' . $link_after, $category_links );
                }
            }

            if ( ! in_array( $post_type, array( 'post', 'page', 'attachment' ), true ) ) {
                $post_type_object = get_post_type_object( $post_type );
                $archive_link     = esc_url( get_post_type_archive_link( $post_type ) );
                $post_type_link   = sprintf( $link, $archive_link, esc_html( $post_type_object->labels->singular_name ) );
            }

            if ( 0 !== $parent ) {
                $parent_links = array();
                while ( $parent ) {
                    $post_parent    = get_post( $parent );
                    $parent_links[] = sprintf( $link, esc_url( get_permalink( $post_parent->ID ) ), esc_html( get_the_title( $post_parent->ID ) ) );
                    $parent         = $post_parent->post_parent;
                }
                $parent_links   = array_reverse( $parent_links );
                $parent_string  = implode( $delimiter, $parent_links );
            }

            if ( $parent_string ) {
                $breadcrumb_trail = $parent_string . $delimiter . $post_link;
            } else {
                $breadcrumb_trail = $post_link;
            }

            if ( $post_type_link ) {
                $breadcrumb_trail = $post_type_link . $delimiter . $breadcrumb_trail;
            }

            if ( $category_links ) {
                $breadcrumb_trail = $category_links . $breadcrumb_trail;
            }
        }

        // Handle archives
        if ( is_archive() ) {
            if ( is_category() || is_tag() || is_tax() ) {
                $term_object       = get_term( $queried_object );
                $taxonomy          = $term_object->taxonomy;
                $term_parent       = $term_object->parent;
                $taxonomy_object   = get_taxonomy( $taxonomy );
                $current_term_link = $before . esc_html( $taxonomy_object->labels->singular_name ) . ': ' . esc_html( $term_object->name ) . $after;

                $parent_term_string = '';
                if ( 0 !== $term_parent ) {
                    $parent_term_links = array();
                    while ( $term_parent ) {
                        $term                = get_term( $term_parent, $taxonomy );
                        $parent_term_links[] = sprintf( $link, esc_url( get_term_link( $term ) ), esc_html( $term->name ) );
                        $term_parent         = $term->parent;
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
                $breadcrumb_trail = esc_html__( 'Author archive for ', 'folioedgecore' ) . $before . esc_html( $queried_object->data->display_name ) . $after;
            } elseif ( is_date() ) {
                $year     = $wp_the_query->query_vars['year'];
                $monthnum = $wp_the_query->query_vars['monthnum'];
                $day      = $wp_the_query->query_vars['day'];

                if ( $monthnum ) {
                    $date_time  = DateTime::createFromFormat( '!m', $monthnum );
                    $month_name = $date_time->format( 'F' );
                }

                if ( is_year() ) {
                    $breadcrumb_trail = $before . esc_html( $year ) . $after;
                } elseif ( is_month() ) {
                    $year_link        = sprintf( $link, esc_url( get_year_link( $year ) ), esc_html( $year ) );
                    $breadcrumb_trail = $year_link . $delimiter . $before . esc_html( $month_name ) . $after;
                } elseif ( is_day() ) {
                    $year_link        = sprintf( $link, esc_url( get_year_link( $year ) ), esc_html( $year ) );
                    $month_link       = sprintf( $link, esc_url( get_month_link( $year, $monthnum ) ), esc_html( $month_name ) );
                    $breadcrumb_trail = $year_link . $delimiter . $month_link . $delimiter . $before . esc_html( $day ) . $after;
                }
            } elseif ( is_post_type_archive() ) {
                $post_type        = $wp_the_query->query_vars['post_type'];
                $post_type_object = get_post_type_object( $post_type );
                $breadcrumb_trail = $before . esc_html( $post_type_object->labels->singular_name ) . $after;
            }
        }

        // Handle search
        if ( is_search() ) {
            $breadcrumb_trail = esc_html__( 'Search query for: ', 'folioedgecore' ) . $before . esc_html( get_search_query() ) . $after;
        }

        // Handle 404
        if ( is_404() ) {
            $breadcrumb_trail = $before . esc_html__( 'Error 404', 'folioedgecore' ) . $after;
        }

        // Handle paged
        if ( is_paged() ) {
            $current_page = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' );
            $page_addon   = $before . sprintf(
                /* translators: %s: page number */
                esc_html__( ' ( Page %s )', 'folioedgecore' ),
                number_format_i18n( $current_page )
            ) . $after;
        }

        // Build output
        $breadcrumb_output  = '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'folioedgecore' ) . '">';

        if ( is_home() || is_front_page() ) {
            if ( is_paged() ) {
                $breadcrumb_output .= '<a href="' . esc_url( $home_link ) . '">' . esc_html( $home_text ) . '</a>';
                $breadcrumb_output .= $page_addon;
            } else {
                $breadcrumb_output .= esc_html( $home_text );
            }
        } else {
            $breadcrumb_output .= '<a href="' . esc_url( $home_link ) . '">' . esc_html( $home_text ) . '</a>';
            $breadcrumb_output .= $delimiter;
            $breadcrumb_output .= $breadcrumb_trail;
            $breadcrumb_output .= $page_addon;
        }

        $breadcrumb_output .= '</nav>';

        return $breadcrumb_output;
    }
}
