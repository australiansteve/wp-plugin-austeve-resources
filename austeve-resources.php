<?php
/**
 * Plugin Name: Resources - Sustainable Saint John
 * Plugin URI: https://github.com/australiansteve/wp-plugin-austeve-resources
 * Description: Add, edit & display Resources
 * Version: 1.0.0
 * Author: AustralianSteve
 * Author URI: http://AustralianSteve.com
 * License: GPL2
 */

class AUSteve_Resources_CPT {

	function __construct() {

		add_action( 'init', array($this, 'register_post_type') );

		add_action( 'init', array($this, 'register_category_taxonomy') );

		add_action( 'template_redirect', array($this, 'redirect_singular_posts') );

		add_filter('manage_austeve-resources_posts_columns', array($this, 'alter_admin_columns_head') );

		add_action('manage_austeve-resources_posts_custom_column', array($this, 'alter_admin_columns_content'), 10, 2 );

		add_action('pre_get_posts', array($this, 'always_get_all_resources') );
	}

	function register_post_type() {

		// Set UI labels for Custom Post Type
		$labels = array(
			'name'                => _x( 'Resources', 'Post Type General Name', 'austeve-resources' ),
			'singular_name'       => _x( 'Resource', 'Post Type Singular Name', 'austeve-resources' ),
			'menu_name'           => __( 'Resources', 'austeve-resources' ),
			'all_items'           => __( 'All Resources', 'austeve-resources' ),
			'view_item'           => __( 'View Resource', 'austeve-resources' ),
			'add_new_item'        => __( 'Add New Resource', 'austeve-resources' ),
			'add_new'             => __( 'Add New', 'austeve-resources' ),
			'edit_item'           => __( 'Edit Resource', 'austeve-resources' ),
			'update_item'         => __( 'Update Resource', 'austeve-resources' ),
			'search_items'        => __( 'Search Resources', 'austeve-resources' ),
			'not_found'           => __( 'Not Found', 'austeve-resources' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'austeve-resources' ),
		);
		
		// Set other options for Custom Post Type		
		$args = array(
			'label'               => __( 'Resources', 'austeve-resources' ),
			'description'         => __( 'Resources', 'austeve-resources' ),
			'labels'              => $labels,
			// Features this CPT supports in Post Editor
			'supports'            => array( 'title', 'editor', 'author', 'revisions', ),
			// You can associate this CPT with a taxonomy or custom taxonomy. 
			'taxonomies'          => array( '' ),
			/* A hierarchical CPT is like Pages and can have
			* Parent and child items. A non-hierarchical CPT
			* is like Posts.
			*/	
			'hierarchical'        => false,
			'rewrite'           => array( 'slug' => 'sustainability-resources' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'menu_icon'           => 'dashicons-hammer',
		);
		
		// Registering your Custom Post Type
		register_post_type( 'austeve-resources', $args );
	}

	function register_category_taxonomy() {

		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => _x( 'Categories', 'taxonomy general name', 'austeve-resources' ),
			'singular_name'     => _x( 'Category', 'taxonomy singular name', 'austeve-resources' ),
			'search_items'      => __( 'Search Categories', 'austeve-resources' ),
			'all_items'         => __( 'All Categories', 'austeve-resources' ),
			'parent_item'       => __( 'Parent Category', 'austeve-resources' ),
			'parent_item_colon' => __( 'Parent Category:', 'austeve-resources' ),
			'edit_item'         => __( 'Edit Category', 'austeve-resources' ),
			'update_item'       => __( 'Update Category', 'austeve-resources' ),
			'add_new_item'      => __( 'Add New Category', 'austeve-resources' ),
			'new_item_name'     => __( 'New Category Name', 'austeve-resources' ),
			'menu_name'         => __( 'Categories', 'austeve-resources' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'resource-category' ),
		);

		register_taxonomy( 'resource-category', array( 'austeve-resources' ), $args );

	}

    function redirect_singular_posts() {
      if ( is_singular('austeve-resources') ) {
      	$terms = wp_get_post_terms( get_the_ID(), 'resource-category' );
      	error_log("Single resource! ".print_r($terms, true));

      	if (isset($terms[0]))
      	{
        	wp_redirect( home_url('resource-category/'.$terms[0]->slug), 302 );
      	}
      	else
      	{
        	wp_redirect( home_url('resources'), 302 );
      	}

        exit;
      }
    }

    function always_get_all_resources($query) {
    	//if querying a resource category, get all resources
    	if (!is_admin() && $query->get('resource-category') != null)
    	{
    		$query->set('posts_per_page', -1);
    		$query->set('orderby', 'title');//<-- Our custom ordering!
    		$query->set('order', 'ASC');
    		//error_log('PGP altered: '.print_r($query, true));
    	}
    	return $query;
    }

	function alter_admin_columns_head($defaults) {
		$res = array_slice($defaults, 0, 2, true) +
		    array("resource-category" => "Category") +
		    array_slice($defaults, 2, count($defaults) - 1, true) ;
		$defaults = $res;
		//Remove the old date column
		unset($defaults['categories']);
	    return $defaults;
	}
	 
	function alter_admin_columns_content($column_name, $post_ID) {
	    if ($column_name == 'resource-category') {
			$term_args = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'names');
	    	$term_list = wp_get_post_terms($post_ID, 'resource-category', $term_args);
	    	if (count($term_list) > 1)
				echo implode(", ", $term_list);
			else if (count($term_list) > 0)
				echo $term_list[0];
	    }
	}




}

$austeveResources = new AUSteve_Resources_CPT();

