<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * team: Custom Post Types
 *
 *
 */
class folioedgecore_team_Post_Types {
	
	public function __construct()
	{
		$this->register_post_type();
	}

	public function register_post_type()
	{
		$args = array();	

		// team
		$args['team-type'] = array(
			'labels' => array(
				'name' => __( 'Team Members', 'folioedgecore' ),
				'singular_name' => __( 'Team Members', 'folioedgecore' ),
				'add_new' => __( 'Add team', 'folioedgecore' ),
				'add_new_item' => __( 'Add team', 'folioedgecore' ),
				'edit_item' => __( 'Edit team', 'folioedgecore' ),
				'new_item' => __( 'New team', 'folioedgecore' ),
				'view_item' => __( 'View team', 'folioedgecore' ),
				'search_items' => __( 'Search Through team', 'folioedgecore' ),
				'not_found' => __( 'No team found', 'folioedgecore' ),
				'not_found_in_trash' => __( 'No team found in Trash', 'folioedgecore' ),
				'parent_item_colon' => __( 'Parent team:', 'folioedgecore' ),
				'menu_name' => __( 'Team Members', 'folioedgecore' ),				
			),		  
			'hierarchical' => false,
	        'description' => __( 'Add a team Members', 'folioedgecore' ),
	        'supports' => array( 'title', 'editor', 'thumbnail'),
	        'menu_icon' =>  'dashicons-businessperson',
	        'public' => true,
	        'publicly_queryable' => true,
	        'exclude_from_search' => false,
	        'query_var' => true,
	        'rewrite' => array( 'slug' => 'team' ),
	        // This is where we add taxonomies to our CPT
        	'taxonomies'          => array( 'team_category' ),
		);	

		// Register post type: name, arguments
		register_post_type('team', $args['team-type']);
	}
}

function folioedgecore_team_types() { new folioedgecore_team_Post_Types(); }

add_action( 'init', 'folioedgecore_team_types' );

/*-----------------------------------------------------------------------------------*/
/*	Creating Custom Category 
/*-----------------------------------------------------------------------------------*/
// hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'folioedgecore_create_team_category', 0 );

// create two category, genres and writers for the post type "book"
function folioedgecore_create_team_category() {
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
		'rewrite'           => array( 'slug' => 'team_category' ),
	);

	register_taxonomy( 'team_category', array( 'team' ), $args );
}