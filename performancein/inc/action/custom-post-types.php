<?php
/**
 * This file contains all custom post types and taxonomies functions.
 *
 * @package performancein
 */

/**
 * This file contains all custom post types and taxonomies functions.
 *
 * @package performancein
 */



function performancein_tag_partner_networks(){
	$tag_labels = array(
		'name' => _x( 'Partner Network Tags', 'taxonomy general name' ),
		'singular_name' => _x( 'Partner Network Tag', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Tags' ),
		'popular_items' => __( 'Popular Tags' ),
		'all_items' => __( 'All Tags' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Tag' ),
		'update_item' => __( 'Update Tag' ),
		'add_new_item' => __( 'Add New Tag' ),
		'new_item_name' => __( 'New Tag Name' ),
		'separate_items_with_commas' => __( 'Separate tags with commas' ),
		'add_or_remove_items' => __( 'Add or remove tags' ),
		'choose_from_most_used' => __( 'Choose from the most used tags' ),
		'menu_name' => __( 'Tags' ),
	);

	register_taxonomy('partner_network_tag','pi_partner_networks',array(
		'hierarchical' => true,
		'has_archive'  => true,
		'labels' => $tag_labels,
		'show_ui' => true,
		'show_in_rest' => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var' => true,
		'rewrite' => array( 'slug' => 'profile-hub/tag','with_front'  => false ),
	));
}

/**
 * Create cat_partner_networks taxonomy for Partner Networks post type.
 *
 * @since 1.0
 */
function performancein_category_partner_networks() {

	$labels = array(
		'name'                       => _x( 'Partner Network Categories', 'Taxonomy General Name', 'performancein' ),
		'singular_name'              => _x( 'Partner Network Category', 'Taxonomy Singular Name', 'performancein' ),
		'menu_name'                  => __( 'Categories', 'performancein' ),
		'all_items'                  => __( 'All Items', 'performancein' ),
		'parent_item'                => __( 'Parent Item', 'performancein' ),
		'parent_item_colon'          => __( 'Parent Item:', 'performancein' ),
		'new_item_name'              => __( 'New Item Name', 'performancein' ),
		'add_new_item'               => __( 'Add New Item', 'performancein' ),
		'edit_item'                  => __( 'Edit Item', 'performancein' ),
		'update_item'                => __( 'Update Item', 'performancein' ),
		'view_item'                  => __( 'View Item', 'performancein' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'performancein' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'performancein' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'performancein' ),
		'popular_items'              => __( 'Popular Items', 'performancein' ),
		'search_items'               => __( 'Search Items', 'performancein' ),
		'not_found'                  => __( 'Not Found', 'performancein' ),
		'no_terms'                   => __( 'No items', 'performancein' ),
		'items_list'                 => __( 'Items list', 'performancein' ),
		'items_list_navigation'      => __( 'Items list navigation', 'performancein' ),
	);
	$args   = array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => false,
	);
	register_taxonomy( 'cat_partner_networks', array( 'pi_partner_networks' ), $args );

}


/**
 * Added the Partner Networks Post Type.
 *
 * @since 1.0
 */
function performancein_partner_networks() {

	$labels  = array(
		'name'                  => _x( 'Partner Network', 'Post Type General Name', 'performancein' ),
		'singular_name'         => _x( 'Partner Network', 'Post Type Singular Name', 'performancein' ),
		'menu_name'             => __( 'Partner Network', 'performancein' ),
		'name_admin_bar'        => __( 'Partner Network', 'performancein' ),
		'archives'              => __( 'Partner Network Archives', 'performancein' ),
		'attributes'            => __( 'Partner Network Attributes', 'performancein' ),
		'parent_item_colon'     => __( 'Parent Partner Post:', 'performancein' ),
		'all_items'             => __( 'All Partners', 'performancein' ),
		'add_new_item'          => __( 'Add New Partner', 'performancein' ),
		'add_new'               => __( 'Add New', 'performancein' ),
		'new_item'              => __( 'New Partner', 'performancein' ),
		'edit_item'             => __( 'Edit Partner', 'performancein' ),
		'update_item'           => __( 'Update Partner', 'performancein' ),
		'view_item'             => __( 'View Partner', 'performancein' ),
		'view_items'            => __( 'View Partners', 'performancein' ),
		'search_items'          => __( 'Search Partner', 'performancein' ),
		'not_found'             => __( 'Not found', 'performancein' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'performancein' ),
		'featured_image'        => __( 'Featured Image', 'performancein' ),
		'set_featured_image'    => __( 'Set featured image', 'performancein' ),
		'remove_featured_image' => __( 'Remove featured image', 'performancein' ),
		'use_featured_image'    => __( 'Use as featured image', 'performancein' ),
		'insert_into_item'      => __( 'Insert into Post', 'performancein' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Post', 'performancein' ),
		'items_list'            => __( 'Partner list', 'performancein' ),
		'items_list_navigation' => __( 'Partner list navigation', 'performancein' ),
		'filter_items_list'     => __( 'Filter Partner list', 'performancein' ),
	);
	$rewrite = array(
		'slug'       => 'profile-hub/company',
		'with_front' => false,
		'pages'      => true,
		'feeds'      => true,
	);
	$args    = array(
		'label'               => __( 'Partner Network', 'performancein' ),
		'labels'              => $labels,
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-groups',
		'supports'            => array(
			'title',
			'revisions',
			'trackbacks',
			'author',
			'excerpt',
			'page-attributes',
			'thumbnail',
			'custom-fields'
		),
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'post',
	);
	register_post_type( 'pi_partner_networks', $args );

}

/**
 * Create cat_partner_networks taxonomy for Job post type.
 *
 * @since 1.0
 */
function performancein_job_post() {

	$labels  = array(
		'name'                  => _x( 'Jobs', 'Post Type General Name', 'performancein' ),
		'singular_name'         => _x( 'Job', 'Post Type Singular Name', 'performancein' ),
		'menu_name'             => __( 'Jobs', 'performancein' ),
		'name_admin_bar'        => __( 'Jobs', 'performancein' ),
		'archives'              => __( 'Jobs Archives', 'performancein' ),
		'attributes'            => __( 'Jobs Attributes', 'performancein' ),
		'parent_item_colon'     => __( 'Parent Jobs:', 'performancein' ),
		'all_items'             => __( 'All Jobs', 'performancein' ),
		'add_new_item'          => __( 'Add New Job', 'performancein' ),
		'add_new'               => __( 'Add Job', 'performancein' ),
		'new_item'              => __( 'New Job', 'performancein' ),
		'edit_item'             => __( 'Edit Job', 'performancein' ),
		'update_item'           => __( 'Update Job', 'performancein' ),
		'view_item'             => __( 'View Job', 'performancein' ),
		'view_items'            => __( 'View Jobs', 'performancein' ),
		'search_items'          => __( 'Search Jobs', 'performancein' ),
		'not_found'             => __( 'Not found', 'performancein' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'performancein' ),
		'featured_image'        => __( 'Featured Image', 'performancein' ),
		'set_featured_image'    => __( 'Set featured image', 'performancein' ),
		'remove_featured_image' => __( 'Remove featured image', 'performancein' ),
		'use_featured_image'    => __( 'Use as featured image', 'performancein' ),
		'insert_into_item'      => __( 'Insert into Post', 'performancein' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Post', 'performancein' ),
		'items_list'            => __( 'Jobs list', 'performancein' ),
		'items_list_navigation' => __( 'Jobs list navigation', 'performancein' ),
		'filter_items_list'     => __( 'Filter Jobs list', 'performancein' ),
	);
	$rewrite = array(
		'slug'       => 'jobs',
		'with_front' => false,
		'pages'      => true,
		'feeds'      => true,
	);
	$args    = array(
		'label'               => __( 'Jobs', 'performancein' ),
		'labels'              => $labels,
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-clipboard',
		'supports'            => array(
			'title',
			'revisions',
			'author',
			'page-attributes',
			'thumbnail',
			'custom-fields'
		),
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'post',
	);
	register_post_type( 'pi_jobs', $args );


	$labels  = array(
		'name'                  => _x( 'Applied jobs', 'Post Type General Name', 'performancein' ),
		'singular_name'         => _x( 'Applied job', 'Post Type Singular Name', 'performancein' ),
		'menu_name'             => __( 'Applied jobs', 'performancein' ),
		'name_admin_bar'        => __( 'Applied jobs', 'performancein' ),
		'archives'              => __( 'Applied jobs Archives', 'performancein' ),
		'attributes'            => __( 'Applied jobs Attributes', 'performancein' ),
		'parent_item_colon'     => __( 'Parent Applied job:', 'performancein' ),
		'all_items'             => __( 'Applied jobs', 'performancein' ),
		'add_new_item'          => __( 'Add New Applied job', 'performancein' ),
		'add_new'               => __( 'Add New', 'performancein' ),
		'new_item'              => __( 'New Applied job', 'performancein' ),
		'edit_item'             => __( 'Edit Applied job', 'performancein' ),
		'update_item'           => __( 'Update Applied job', 'performancein' ),
		'view_item'             => __( 'View Applied job', 'performancein' ),
		'view_items'            => __( 'View Applied jobs', 'performancein' ),
		'search_items'          => __( 'Search Applied job', 'performancein' ),
		'not_found'             => __( 'Not found', 'performancein' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'performancein' ),
		'featured_image'        => __( 'Featured Image', 'performancein' ),
		'set_featured_image'    => __( 'Set featured image', 'performancein' ),
		'remove_featured_image' => __( 'Remove featured image', 'performancein' ),
		'use_featured_image'    => __( 'Use as featured image', 'performancein' ),
		'insert_into_item'      => __( 'Insert into Post', 'performancein' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Post', 'performancein' ),
		'items_list'            => __( 'Applied jobs list', 'performancein' ),
		'items_list_navigation' => __( 'Applied jobs list navigation', 'performancein' ),
		'filter_items_list'     => __( 'Filter Applied jobs list', 'performancein' ),
	);
	$rewrite = array(
		'slug'       => 'applied-jobs',
		'with_front' => false,
		'pages'      => true,
		'feeds'      => true,
	);
	$args    = array(
		'label'               => __( 'Applied jobs', 'performancein' ),
		'show_in_menu'        => 'edit.php?post_type=pi_jobs',
		'labels'              => $labels,
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_rest'        => false,
		'menu_position'       => 5,
		'supports'            => array(
			'title',
			'editor',
			'revisions',
			'author',
			'custom-fields'
		),
		'show_in_admin_bar'   => false,
		'show_in_nav_menus'   => false,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => false,
		'rewrite'             => $rewrite,
		'capability_type'     => 'post',

	);
	register_post_type( 'pi_applied_jobs', $args );

}

/**
 * Create cat_job_post taxonomy for Job post type.
 *
 * @since 1.0
 */
function performancein_category_job_post() {

	$labels = array(
		'name'                       => _x( 'Jobs Categories', 'Taxonomy General Name', 'performancein' ),
		'singular_name'              => _x( 'Jobs Category', 'Taxonomy Singular Name', 'performancein' ),
		'menu_name'                  => __( 'Categories', 'performancein' ),
		'all_items'                  => __( 'All Items', 'performancein' ),
		'parent_item'                => __( 'Parent Item', 'performancein' ),
		'parent_item_colon'          => __( 'Parent Item:', 'performancein' ),
		'new_item_name'              => __( 'New Item Name', 'performancein' ),
		'add_new_item'               => __( 'Add New Item', 'performancein' ),
		'edit_item'                  => __( 'Edit Item', 'performancein' ),
		'update_item'                => __( 'Update Item', 'performancein' ),
		'view_item'                  => __( 'View Item', 'performancein' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'performancein' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'performancein' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'performancein' ),
		'popular_items'              => __( 'Popular Items', 'performancein' ),
		'search_items'               => __( 'Search Items', 'performancein' ),
		'not_found'                  => __( 'Not Found', 'performancein' ),
		'no_terms'                   => __( 'No items', 'performancein' ),
		'items_list'                 => __( 'Items list', 'performancein' ),
		'items_list_navigation'      => __( 'Items list navigation', 'performancein' ),
	);
	$args   = array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => false,
	);
	register_taxonomy( 'pi_cat_jobs', array( 'pi_jobs' ), $args );

}
/**
 * Added the Events Post Type.
 *
 * @since 1.0
 */
function performancein_events() {

	$labels  = array(
		'name'                  => _x( 'Events', 'Post Type General Name', 'performancein' ),
		'singular_name'         => _x( 'Event', 'Post Type Singular Name', 'performancein' ),
		'menu_name'             => __( 'Events', 'performancein' ),
		'name_admin_bar'        => __( 'Events', 'performancein' ),
		'archives'              => __( 'Events Archives', 'performancein' ),
		'attributes'            => __( 'Events Attributes', 'performancein' ),
		'parent_item_colon'     => __( 'Parent Event:', 'performancein' ),
		'all_items'             => __( 'All Events', 'performancein' ),
		'add_new_item'          => __( 'Add New Event', 'performancein' ),
		'add_new'               => __( 'Add New', 'performancein' ),
		'new_item'              => __( 'New Events', 'performancein' ),
		'edit_item'             => __( 'Edit Event', 'performancein' ),
		'update_item'           => __( 'Update Event', 'performancein' ),
		'view_item'             => __( 'View Event', 'performancein' ),
		'view_items'            => __( 'View Events', 'performancein' ),
		'search_items'          => __( 'Search Events', 'performancein' ),
		'not_found'             => __( 'Not found', 'performancein' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'performancein' ),
		'featured_image'        => __( 'Featured Image', 'performancein' ),
		'set_featured_image'    => __( 'Set featured image', 'performancein' ),
		'remove_featured_image' => __( 'Remove featured image', 'performancein' ),
		'use_featured_image'    => __( 'Use as featured image', 'performancein' ),
		'insert_into_item'      => __( 'Insert into Post', 'performancein' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Event', 'performancein' ),
		'items_list'            => __( 'Events list', 'performancein' ),
		'items_list_navigation' => __( 'Events list navigation', 'performancein' ),
		'filter_items_list'     => __( 'Filter Events list', 'performancein' ),
	);
	$rewrite = array(
		'slug'       => 'events',
		'with_front' => false,
		'pages'      => true,
		'feeds'      => true,
	);
	$args    = array(
		'label'               => __( 'Events', 'performancein' ),
		'labels'              => $labels,
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-tickets-alt',
		'supports'            => array(
			'title',
			'revisions',
			'trackbacks',
			'editor',
			'author',
			'excerpt',
			'page-attributes',
			'thumbnail',
			'custom-fields'
		),
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'taxonomies'  => array( 'category' ),
		'capability_type'     => 'post',
	);
	register_post_type( 'pi_events', $args );

}
/**
 * Added the Resources Post Type.
 *
 * @since 1.0
 */
function performancein_resources() {

	$labels  = array(
		'name'                  => _x( 'Resources', 'Post Type General Name', 'performancein' ),
		'singular_name'         => _x( 'Resource', 'Post Type Singular Name', 'performancein' ),
		'menu_name'             => __( 'Resources', 'performancein' ),
		'name_admin_bar'        => __( 'Resources', 'performancein' ),
		'archives'              => __( 'Resources Archives', 'performancein' ),
		'attributes'            => __( 'Resources Attributes', 'performancein' ),
		'parent_item_colon'     => __( 'Parent Resources Post:', 'performancein' ),
		'all_items'             => __( 'All Resources', 'performancein' ),
		'add_new_item'          => __( 'Add New Resource', 'performancein' ),
		'add_new'               => __( 'Add New', 'performancein' ),
		'new_item'              => __( 'New Resources', 'performancein' ),
		'edit_item'             => __( 'Edit Resource', 'performancein' ),
		'update_item'           => __( 'Update Resource', 'performancein' ),
		'view_item'             => __( 'View Resource', 'performancein' ),
		'view_items'            => __( 'View Resources', 'performancein' ),
		'search_items'          => __( 'Search Resources', 'performancein' ),
		'not_found'             => __( 'Not found', 'performancein' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'performancein' ),
		'featured_image'        => __( 'Featured Image', 'performancein' ),
		'set_featured_image'    => __( 'Set featured image', 'performancein' ),
		'remove_featured_image' => __( 'Remove featured image', 'performancein' ),
		'use_featured_image'    => __( 'Use as featured image', 'performancein' ),
		'insert_into_item'      => __( 'Insert into Post', 'performancein' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Post', 'performancein' ),
		'items_list'            => __( 'Resource list', 'performancein' ),
		'items_list_navigation' => __( 'Resources list navigation', 'performancein' ),
		'filter_items_list'     => __( 'Filter Resources list', 'performancein' ),
	);
	$rewrite = array(
		'slug'       => 'resource',
		'with_front' => false,
		'pages'      => true,
		'feeds'      => true,
	);
	$args    = array(
		'label'               => __( 'Resources', 'performancein' ),
		'labels'              => $labels,
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-tickets-alt',
		'supports'            => array(
			'title',
			'revisions',
			'trackbacks',
			'editor',
			'author',
			'excerpt',
			'page-attributes',
			'thumbnail',
			'custom-fields'
		),
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'post',
		'taxonomies'  => array( 'category' ),
	);
	register_post_type( 'pi_resources', $args );

}
