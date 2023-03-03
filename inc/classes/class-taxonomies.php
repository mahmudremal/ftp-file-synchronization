<?php
/**
 * Register Custom Taxonomies
 *
 * @package FTPFileSynchronization
 */

namespace FTPFILESYNC_THEME\Inc;

use FTPFILESYNC_THEME\Inc\Traits\Singleton;

class Taxonomies {
	use Singleton;

	protected function __construct() {

		// load class.
		$this->setup_hooks();
	}

	protected function setup_hooks() {

		/**
		 * Actions.
		 */
		add_action( 'init', [ $this, 'create_genre_taxonomy' ] );
		add_action( 'init', [ $this, 'create_year_taxonomy' ] );

	}

	// Register Taxonomy Genre
	public function create_genre_taxonomy() {

		$labels = [
			'name'              => _x( 'Genres', 'taxonomy general name', 'ftp-file-synchronization' ),
			'singular_name'     => _x( 'Genre', 'taxonomy singular name', 'ftp-file-synchronization' ),
			'search_items'      => __( 'Search Genres', 'ftp-file-synchronization' ),
			'all_items'         => __( 'All Genres', 'ftp-file-synchronization' ),
			'parent_item'       => __( 'Parent Genre', 'ftp-file-synchronization' ),
			'parent_item_colon' => __( 'Parent Genre:', 'ftp-file-synchronization' ),
			'edit_item'         => __( 'Edit Genre', 'ftp-file-synchronization' ),
			'update_item'       => __( 'Update Genre', 'ftp-file-synchronization' ),
			'add_new_item'      => __( 'Add New Genre', 'ftp-file-synchronization' ),
			'new_item_name'     => __( 'New Genre Name', 'ftp-file-synchronization' ),
			'menu_name'         => __( 'Genre', 'ftp-file-synchronization' ),
		];
		$args   = [
			'labels'             => $labels,
			'description'        => __( 'Movie Genre', 'ftp-file-synchronization' ),
			'hierarchical'       => true,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_tagcloud'      => true,
			'show_in_quick_edit' => true,
			'show_admin_column'  => true,
			'show_in_rest'       => true,
		];

		register_taxonomy( 'genre', [ 'movies' ], $args );

	}

	// Register Taxonomy Year
	public function create_year_taxonomy() {

		$labels = [
			'name'              => _x( 'Years', 'taxonomy general name', 'ftp-file-synchronization' ),
			'singular_name'     => _x( 'Year', 'taxonomy singular name', 'ftp-file-synchronization' ),
			'search_items'      => __( 'Search Years', 'ftp-file-synchronization' ),
			'all_items'         => __( 'All Years', 'ftp-file-synchronization' ),
			'parent_item'       => __( 'Parent Year', 'ftp-file-synchronization' ),
			'parent_item_colon' => __( 'Parent Year:', 'ftp-file-synchronization' ),
			'edit_item'         => __( 'Edit Year', 'ftp-file-synchronization' ),
			'update_item'       => __( 'Update Year', 'ftp-file-synchronization' ),
			'add_new_item'      => __( 'Add New Year', 'ftp-file-synchronization' ),
			'new_item_name'     => __( 'New Year Name', 'ftp-file-synchronization' ),
			'menu_name'         => __( 'Year', 'ftp-file-synchronization' ),
		];
		$args   = [
			'labels'             => $labels,
			'description'        => __( 'Movie Release Year', 'ftp-file-synchronization' ),
			'hierarchical'       => false,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_tagcloud'      => true,
			'show_in_quick_edit' => true,
			'show_admin_column'  => true,
			'show_in_rest'       => true,
		];
		register_taxonomy( 'movie-year', [ 'movies' ], $args );

	}

}
