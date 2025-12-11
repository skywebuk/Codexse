<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Service: Custom Post Types
 *
 *
 */
class folioedgecore_service_Post_Types {
	
	public function __construct()
	{
		$this->register_post_type();
	}

	public function register_post_type()
	{
		$args = array();	

		// service
		$args['service-type'] = array(
			'labels' => array(
				'name' => __( 'Services', 'folioedgecore' ),
				'singular_name' => __( 'Service', 'folioedgecore' ),
				'add_new' => __( 'Add service', 'folioedgecore' ),
				'add_new_item' => __( 'Add service', 'folioedgecore' ),
				'edit_item' => __( 'Edit service', 'folioedgecore' ),
				'new_item' => __( 'New service', 'folioedgecore' ),
				'view_item' => __( 'View service', 'folioedgecore' ),
				'search_items' => __( 'Search Through service', 'folioedgecore' ),
				'not_found' => __( 'No service found', 'folioedgecore' ),
				'not_found_in_trash' => __( 'No service found in Trash', 'folioedgecore' ),
				'parent_item_colon' => __( 'Parent service:', 'folioedgecore' ),
				'menu_name' => __( 'Services', 'folioedgecore' ),				
			),		  
			'hierarchical' => false,
	        'description' => __( 'Add a service item', 'folioedgecore' ),
	        'supports' => array( 'title', 'editor', 'thumbnail'),
	        'menu_icon' =>  'dashicons-clipboard',
	        'public' => true,
	        'publicly_queryable' => true,
	        'exclude_from_search' => false,
	        'query_var' => true,
	        'rewrite' => array( 'slug' => 'service' ),
	        // This is where we add taxonomies to our CPT
        	'taxonomies'          => array( 'service_category' ),
		);	

		// Register post type: name, arguments
		register_post_type('service', $args['service-type']);
	}
}

function folioedgecore_service_types() { new folioedgecore_service_Post_Types(); }

add_action( 'init', 'folioedgecore_service_types' );

/*-----------------------------------------------------------------------------------*/
/*	Creating Custom Category 
/*-----------------------------------------------------------------------------------*/
// hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'folioedgecore_create_service_category', 0 );

// create two category, genres and writers for the post type "book"
function folioedgecore_create_service_category() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Categories', 'taxonomy general name', 'folioedgecore' ),
		'singular_name'     => _x( 'Category', 'taxonomy singular name', 'folioedgecore' ),
		'search_items'      => __( 'Search Categories', 'folioedgecore' ),
		'all_items'         => __( 'Categories', 'folioedgecore' ),
		'parent_item'       => __( 'Parent Category', 'folioedgecore' ),
		'parent_item_colon' => __( 'Parent Category:', 'folioedgecore' ),
		'edit_item'         => __( 'Edit Category', 'folioedgecore' ),
		'update_item'       => __( 'Update Category', 'folioedgecore' ),
		'add_new_item'      => __( 'Add New Category', 'folioedgecore' ),
		'new_item_name'     => __( 'New Category', 'folioedgecore' ),
		'menu_name'         => __( 'Categories', 'folioedgecore' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'service_category' ),
	);

	register_taxonomy( 'service_category', array( 'service' ), $args );
}