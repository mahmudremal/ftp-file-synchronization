<?php
/**
 * Register Menus
 *
 * @package FTPFileSynchronization
 */
namespace FTPFILESYNC_THEME\Inc;
use FTPFILESYNC_THEME\Inc\Traits\Singleton;
class Menus {
	use Singleton;
	protected function __construct() {
		// load class.
		$this->setup_hooks();
	}
	protected function setup_hooks() {
		/**
		 * Actions.
		 */
		// add_action( 'init', [ $this, 'register_menus' ] );
		
    add_filter( 'futurewordpress/project/settings/general', [ $this, 'general' ], 10, 1 );
    add_filter( 'futurewordpress/project/settings/fields', [ $this, 'menus' ], 11, 1 );

		add_filter( 'futurewordpress/project/settings/fields/label', [ $this, 'labelHtml' ], 10, 2 );
		
		add_action( 'in_admin_header', [ $this, 'in_admin_header' ], 100, 0 );
	}
	public function register_menus() {
		register_nav_menus([
			'aquila-header-menu' => esc_html__( 'Header Menu', 'ftp-file-synchronization' ),
			'aquila-footer-menu' => esc_html__( 'Footer Menu', 'ftp-file-synchronization' ),
		]);
	}
	/**
	 * Get the menu id by menu location.
	 *
	 * @param string $location
	 *
	 * @return integer
	 */
	public function get_menu_id( $location ) {
		// Get all locations
		$locations = get_nav_menu_locations();
		// Get object id by location.
		$menu_id = ! empty($locations[$location]) ? $locations[$location] : '';
		return ! empty( $menu_id ) ? $menu_id : '';
	}
	/**
	 * Get all child menus that has given parent menu id.
	 *
	 * @param array   $menu_array Menu array.
	 * @param integer $parent_id Parent menu id.
	 *
	 * @return array Child menu array.
	 */
	public function get_child_menu_items( $menu_array, $parent_id ) {
		$child_menus = [];
		if ( ! empty( $menu_array ) && is_array( $menu_array ) ) {
			foreach ( $menu_array as $menu ) {
				if ( intval( $menu->menu_item_parent ) === $parent_id ) {
					array_push( $child_menus, $menu );
				}
			}
		}
		return $child_menus;
	}
	public function in_admin_header() {
		if( ! isset( $_GET[ 'page' ] ) || $_GET[ 'page' ] != 'crm_dashboard' ) {return;}
		
		remove_all_actions('admin_notices');
		remove_all_actions('all_admin_notices');
		// add_action('admin_notices', function () {echo 'My notice';});
	}
	/**
	 * Supply necessry tags that could be replace on frontend.
	 * 
	 * @return string
	 * @return array
	 */
	public function commontags( $html = false ) {
		$arg = [];$tags = [
			'username', 'sitename', 
		];
		if( $html === false ) {return $tags;}
		foreach( $tags as $tag ) {
			$arg[] = sprintf( "%s{$tag}%s", '<code>{', '}</code>' );
		}
		return implode( ', ', $arg );
	}
	public function contractTags( $tags ) {
		$arg = [];
		foreach( $tags as $tag ) {
			$arg[] = sprintf( "%s{$tag}%s", '<code>{', '}</code>' );
		}
		return implode( ', ', $arg );
	}

  /**
   * WordPress Option page.
   * 
   * @return array
   */
	public function general( $args ) {
		// print_r( $args );wp_die();
		$opt = [
			'page_title'					=> __( 'FTP Configuration.', 'ftp-file-synchronization' ),
			'menu_title'					=> __( 'FTP Config', 'ftp-file-synchronization' ),
			'role'								=> 'manage_options',
			// 'slug'								=> $this->plugin_slug,
			'page_header'					=> __( 'FTP/SFTP Configuration.', 'ftp-file-synchronization' ),
			'page_subheader'			=> __( 'Setup your FTP or SFTP login information and make sure you\'ve inserted all informaton correctly, specially directory path.', 'ftp-file-synchronization' ),
			'no_password'					=> __( 'A password is required.', 'ftp-file-synchronization' ),
		];
		return wp_parse_args( $opt, $args );
	}
	public function menus( $args ) {
    // get_FwpOption( 'key', 'default' ) | apply_filters( 'futurewordpress/project/system/getoption', 'key', 'default' )
		// is_FwpActive( 'key' ) | apply_filters( 'futurewordpress/project/system/isactive', 'key' )
		// $args = [];
		$args['ftp'] 		= [
			'title'							=> __( 'Setup FTP', 'ftp-file-synchronization' ),
			'description'				=> __( 'Setup your SFTP login information and directory information from where file will sync and where to store.', 'ftp-file-synchronization' ),
			'fields'						=> [
				[
					'id' 						=> 'ftp-enable',
					'label'					=> __( 'Enable', 'ftp-file-synchronization' ),
					'description'		=> __( 'Mark to enable all FTP/Sftp functionalities..', 'ftp-file-synchronization' ),
					'type'					=> 'checkbox',
					'default'				=> true
				],
				[
					'id' 						=> 'ftp-server',
					'label'					=> __( 'Server', 'ftp-file-synchronization' ),
					'description'		=> __( 'FTP or SFTP server address. Such as `ftp.example.com`.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'ftp-username',
					'label'					=> __( 'User Name', 'ftp-file-synchronization' ),
					'description'		=> __( 'Ftp/SFtp account username.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'ftp-password',
					'label'					=> __( 'User Password', 'ftp-file-synchronization' ),
					'description'		=> __( 'Insert valid FTP user password.', 'ftp-file-synchronization' ),
					'type'					=> 'password',
					'default'				=> ''
				],
				[
					'id' 						=> 'ftp-remotedir',
					'label'					=> __( 'FTP Directory', 'ftp-file-synchronization' ),
					'description'		=> sprintf( __( 'The directory path from where file will be synced. This directory path should be valid, mistake free and from root directory. Such as %s.', 'ftp-file-synchronization' ), '<code>/home/example/public_html/wp-content/uploads/somefolder</code>' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'ftp-localdir',
					'label'					=> __( 'Local Directory', 'ftp-file-synchronization' ),
					'description'		=> sprintf( __( 'The directory path where file should store. This directory path should be valid, mistake free and from root directory. Such as %s. Leave it blank if you want to use WordPress media library folder. On that case, file will be uploaded on %s.', 'ftp-file-synchronization' ), '<code>/home/example/public_html/wp-content/uploads/somefolder</code>', '<code>' . apply_filters( 'futurewordpress/project/filesystem/uploaddir', false ) . '</code>' ),
					'type'					=> 'text',
					'default'				=> ABSPATH
				],
				[
					'id' 						=> 'ftp-interval',
					'label'					=> __( 'Interval', 'ftp-file-synchronization' ),
					'description'		=> __( 'Select a interval to define scheduled synchronization.', 'ftp-file-synchronization' ),
					'type'					=> 'select',
					// 'default'				=> 'hourly',
					'options'				=> [
						'5mins'									=> __( 'Every 5 Minutes', 'ftp-file-synchronization' ),
						'hourly'								=> __( 'Hourly', 'ftp-file-synchronization' ),
						'2hours'								=> __( 'Every Two Hours', 'ftp-file-synchronization' ),
						'3hours'								=> __( 'Every Three Hours', 'ftp-file-synchronization' ),
						'4hours'								=> __( 'Every Four Hours', 'ftp-file-synchronization' ),
						'daily'									=> __( 'Daily', 'ftp-file-synchronization' ),
						'weekly'								=> __( 'Weekly', 'ftp-file-synchronization' ),
						'fifteendays'						=> __( 'Quarterly (15 days)', 'ftp-file-synchronization' ),
						'monthly'								=> __( 'Monthly', 'ftp-file-synchronization' ),
					]
				],
				[
					'id' 						=> 'ftp-mediadir',
					'label'					=> __( 'Media Directory', 'ftp-file-synchronization' ),
					'description'		=> __( 'The Media directory name, using this name you provide will create a new direcotry on media fiolder and then FTP files will sync on it.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> 'sync'
				],
			]
		];
		return $args;
	}
	public function labelHtml( $html, $field ) {
		return $html;
	}
}