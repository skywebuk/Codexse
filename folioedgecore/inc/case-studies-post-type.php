<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Case Studie: Custom Post Types
 *
 *
 */
class folioedgecore_case_studie_Post_Types {
	
	public function __construct()
	{
		$this->register_post_type();
	}

	public function register_post_type()
	{
		$args = array();	

		// Case Studie
		$args['case-studie-type'] = array(
			'labels' => array(
				'name' => __( 'Case Studies', 'folioedgecore' ),
				'singular_name' => __( 'Case Studie', 'folioedgecore' ),
				'add_new' => __( 'Add Case Studie', 'folioedgecore' ),
				'add_new_item' => __( 'Add Case Studie', 'folioedgecore' ),
				'edit_item' => __( 'Edit Case Studie', 'folioedgecore' ),
				'new_item' => __( 'New Case Studie', 'folioedgecore' ),
				'view_item' => __( 'View Case Studie', 'folioedgecore' ),
				'search_items' => __( 'Search Through Case Studie', 'folioedgecore' ),
				'not_found' => __( 'No Case Studie found', 'folioedgecore' ),
				'not_found_in_trash' => __( 'No Case Studie found in Trash', 'folioedgecore' ),
				'parent_item_colon' => __( 'Parent Case Studie:', 'folioedgecore' ),
				'menu_name' => __( 'Case Studies', 'folioedgecore' ),				
			),		  
			'hierarchical' => false,
	        'description' => __( 'Add a Case Studie item', 'folioedgecore' ),
	        'supports' => array( 'title', 'editor', 'thumbnail'),
	        'menu_icon' =>  'dashicons-analytics',
	        'public' => true,
	        'publicly_queryable' => true,
	        'exclude_from_search' => false,
	        'query_var' => true,
	        'rewrite' => array( 'slug' => 'case-studie' ),
	        // This is where we add taxonomies to our CPT
        	'taxonomies' => array( 'case-studie-category' ),
		);	

		// Register post type: name, arguments
		register_post_type('case-studie', $args['case-studie-type']);
	}
}

function folioedgecore_case_studie_types() { new folioedgecore_case_studie_Post_Types(); }

add_action( 'init', 'folioedgecore_case_studie_types' );

/*-----------------------------------------------------------------------------------*/
/*	Creating Custom Category 
/*-----------------------------------------------------------------------------------*/
// hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'folioedgecore_create_case_studie_category', 0 );

// create two category, genres and writers for the post type "book"
function folioedgecore_create_case_studie_category() {
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
		'rewrite'           => array( 'slug' => 'case-studie-category' ),
	);

	register_taxonomy( 'case-studie-category', array( 'case-studie' ), $args );
}