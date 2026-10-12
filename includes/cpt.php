<?php
/**
 * Menu item post type and menu categories.
 *
 * @package WP_QR_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function qrm_register_cpt() {
	register_post_type(
		'qrm_item',
		array(
			'labels'       => array(
				'name'               => __( 'Menu Items', 'wp-qr-menu' ),
				'singular_name'      => __( 'Menu Item', 'wp-qr-menu' ),
				'add_new'            => __( 'Add Item', 'wp-qr-menu' ),
				'add_new_item'       => __( 'Add Menu Item', 'wp-qr-menu' ),
				'edit_item'          => __( 'Edit Menu Item', 'wp-qr-menu' ),
				'new_item'           => __( 'New Menu Item', 'wp-qr-menu' ),
				'view_item'          => __( 'View Menu Item', 'wp-qr-menu' ),
				'search_items'       => __( 'Search Menu Items', 'wp-qr-menu' ),
				'not_found'          => __( 'No menu items yet.', 'wp-qr-menu' ),
				'not_found_in_trash' => __( 'No menu items in trash.', 'wp-qr-menu' ),
				'menu_name'          => __( 'QR Menu', 'wp-qr-menu' ),
			),
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-food',
			'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
			'has_archive'  => false,
			'rewrite'      => array( 'slug' => 'menu-item' ),
		)
	);

	register_taxonomy(
		'qrm_category',
		'qrm_item',
		array(
			'labels'            => array(
				'name'          => __( 'Menu Categories', 'wp-qr-menu' ),
				'singular_name' => __( 'Menu Category', 'wp-qr-menu' ),
				'menu_name'     => __( 'Categories', 'wp-qr-menu' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'menu-category' ),
		)
	);
}
add_action( 'init', 'qrm_register_cpt' );
