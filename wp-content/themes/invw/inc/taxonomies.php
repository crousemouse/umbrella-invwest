<?php
	
function invw_custom_taxonomies() {
	register_taxonomy(
		'partners',
		'post',
		array(
			'hierarchical' => true,
			'labels' => array(
				'name' => _x( 'Partners', 'taxonomy general name' ),
				'singular_name' => _x( 'Partner', 'taxonomy singular name' ),
				'search_items' => __( 'Search Partners' ),
				'all_items' => __( 'All Partners' ),
				'parent_item' => __( 'Parent Partner' ),
				'parent_item_colon' => __( 'Parent Partner:' ),
				'edit_item' => __( 'Edit Partner' ),
				'view_item' => __( 'View Partner' ),
				'update_item' => __( 'Update Partner' ),
				'add_new_item' => __( 'Add New Partner' ),
				'new_item_name' => __( 'New Partners Name' ),
				'menu_name' => __( 'Partners' ),
			),
			'query_var' => true,
			'show_admin_column' => true,
			'rewrite' => true,
		)
	);
}
add_action( 'init', 'invw_custom_taxonomies' );