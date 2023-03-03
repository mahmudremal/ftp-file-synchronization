<?php
/**
 * Register Post Types
 *
 * @package FTPFileSynchronization
 */

namespace FTPFILESYNC_THEME\Inc;

use FTPFILESYNC_THEME\Inc\Traits\Singleton;
 
class PostTypes {
	use Singleton;

	protected function __construct() {

		// load class.
		$this->setup_hooks();
	}

	protected function setup_hooks() {

		/**
		 * Actions.
		 */
		add_action( 'init', [ $this, 'create_movie_cpt' ], 0 );

	}

	// Register Custom Post Type Movie
	public function create_movie_cpt() {

		$labels = [
			'name'                  => _x( 'Movies', 'Post Type General Name', 'ftp-file-synchronization' ),
			'singular_name'         => _x( 'Movie', 'Post Type Singular Name', 'ftp-file-synchronization' ),
			'menu_name'             => _x( 'Movies', 'Admin Menu text', 'ftp-file-synchronization' ),
			'name_admin_bar'        => _x( 'Movie', 'Add New on Toolbar', 'ftp-file-synchronization' ),
			'archives'              => __( 'Movie Archives', 'ftp-file-synchronization' ),
			'attributes'            => __( 'Movie Attributes', 'ftp-file-synchronization' ),
			'parent_item_colon'     => __( 'Parent Movie:', 'ftp-file-synchronization' ),
			'all_items'             => __( 'All Movies', 'ftp-file-synchronization' ),
			'add_new_item'          => __( 'Add New Movie', 'ftp-file-synchronization' ),
			'add_new'               => __( 'Add New', 'ftp-file-synchronization' ),
			'new_item'              => __( 'New Movie', 'ftp-file-synchronization' ),
			'edit_item'             => __( 'Edit Movie', 'ftp-file-synchronization' ),
			'update_item'           => __( 'Update Movie', 'ftp-file-synchronization' ),
			'view_item'             => __( 'View Movie', 'ftp-file-synchronization' ),
			'view_items'            => __( 'View Movies', 'ftp-file-synchronization' ),
			'search_items'          => __( 'Search Movie', 'ftp-file-synchronization' ),
			'not_found'             => __( 'Not found', 'ftp-file-synchronization' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'ftp-file-synchronization' ),
			'featured_image'        => __( 'Featured Image', 'ftp-file-synchronization' ),
			'set_featured_image'    => __( 'Set featured image', 'ftp-file-synchronization' ),
			'remove_featured_image' => __( 'Remove featured image', 'ftp-file-synchronization' ),
			'use_featured_image'    => __( 'Use as featured image', 'ftp-file-synchronization' ),
			'insert_into_item'      => __( 'Insert into Movie', 'ftp-file-synchronization' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Movie', 'ftp-file-synchronization' ),
			'items_list'            => __( 'Movies list', 'ftp-file-synchronization' ),
			'items_list_navigation' => __( 'Movies list navigation', 'ftp-file-synchronization' ),
			'filter_items_list'     => __( 'Filter Movies list', 'ftp-file-synchronization' ),
		];
		$args   = [
			'label'               => __( 'Movie', 'ftp-file-synchronization' ),
			'description'         => __( 'The movies', 'ftp-file-synchronization' ),
			'labels'              => $labels,
			'menu_icon'           => 'dashicons-video-alt',
			'supports'            => [
				'title',
				'editor',
				'excerpt',
				'thumbnail',
				'revisions',
				'author',
				'comments',
				'trackbacks',
				'page-attributes',
				'custom-fields',
			],
			'taxonomies'          => [],
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'hierarchical'        => false,
			'exclude_from_search' => false,
			'show_in_rest'        => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		];

		register_post_type( 'movies', $args );

	}


}
