<?php
/**
 * Enqueue theme assets
 *
 * @package FTPFileSynchronization
 */


namespace FTPFILESYNC_THEME\Inc;

use FTPFILESYNC_THEME\Inc\Traits\Singleton;

class Assets {
	use Singleton;

	protected function __construct() {

		// load class.
		$this->setup_hooks();
	}

	protected function setup_hooks() {

		/**
		 * Actions.
		 */
		// add_action( 'wp_enqueue_scripts', [ $this, 'register_styles' ] );
		// add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );
		// add_action( 'wp_enqueue_scripts', [ $this, 'wp_denqueue_scripts' ], 99 );
		/**
		 * The 'enqueue_block_assets' hook includes styles and scripts both in editor and frontend,
		 * except when is_admin() is used to include them conditionally
		 */
		// add_action( 'enqueue_block_assets', [ $this, 'enqueue_editor_assets' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ], 10, 1 );
		// add_action( 'admin_enqueue_scripts', [ $this, 'admin_denqueue_scripts' ], 99 );

		add_filter( 'futurewordpress/project/javascript/siteconfig', [ $this, 'siteConfig' ], 1, 1 );
	}

	public function register_styles() {
		// Register styles.
		wp_register_style( 'bootstrap', FTPFILESYNC_BUILD_LIB_URI . '/css/bootstrap.min.css', [], false, 'all' );
		// wp_register_style( 'slick-css', FTPFILESYNC_BUILD_LIB_URI . '/css/slick.css', [], false, 'all' );
		// wp_register_style( 'slick-theme-css', FTPFILESYNC_BUILD_LIB_URI . '/css/slick-theme.css', ['slick-css'], false, 'all' );
		// wp_register_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css', [], false, 'all' );

		wp_register_style( 'FTPFILESYNC', FTPFILESYNC_BUILD_CSS_URI . '/frontend.css', [], $this->filemtime( FTPFILESYNC_BUILD_CSS_DIR_PATH . '/frontend.css' ), 'all' );
		wp_register_style( 'FTPFILESYNC-library', FTPFILESYNC_BUILD_LIB_URI . '/css/frontend-library.css', [], false, 'all' );

		// Enqueue Styles.
		wp_enqueue_style( 'FTPFILESYNC-library' );
		wp_enqueue_style( 'FTPFILESYNC' );
		// if( $this->allow_enqueue() ) {}

		// wp_enqueue_style( 'bootstrap' );
		// wp_enqueue_style( 'slick-css' );
		// wp_enqueue_style( 'slick-theme-css' );

	}

	public function register_scripts() {
		// Register scripts.
		// wp_register_script( 'slick-js', FTPFILESYNC_BUILD_LIB_URI . '/js/slick.min.js', ['jquery'], false, true );
		wp_register_script( 'FTPFILESYNC', FTPFILESYNC_BUILD_JS_URI . '/frontend.js', ['jquery'], $this->filemtime( FTPFILESYNC_BUILD_JS_DIR_PATH . '/frontend.js' ), true );
		// wp_register_script( 'single-js', FTPFILESYNC_BUILD_JS_URI . '/single.js', ['jquery', 'slick-js'], $this->filemtime( FTPFILESYNC_BUILD_JS_DIR_PATH . '/single.js' ), true );
		// wp_register_script( 'author-js', FTPFILESYNC_BUILD_JS_URI . '/author.js', ['jquery'], $this->filemtime( FTPFILESYNC_BUILD_JS_DIR_PATH . '/author.js' ), true );
		wp_register_script( 'bootstrap', FTPFILESYNC_BUILD_LIB_URI . '/js/bootstrap.min.js', ['jquery'], false, true );
		// wp_register_script( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js', ['jquery'], false, true );
		// wp_register_script( 'prismjs', 'https://preview.keenthemes.com/start/assets/plugins/custom/prismjs/prismjs.bundle.js', ['jquery'], false, true );
		// wp_register_script( 'datatables', 'https://preview.keenthemes.com/start/assets/plugins/custom/datatables/datatables.bundle.js', ['jquery'], false, true );
		wp_register_script( 'popperjs', 'https://unpkg.com/@popperjs/core@2', ['jquery'], false, true );
		wp_register_script( 'plugins-bundle', FTPFILESYNC_BUILD_LIB_URI . '/js/keenthemes.plugins.bundle.js', ['jquery'], false, true );
		wp_register_script( 'scripts-bundle', FTPFILESYNC_BUILD_LIB_URI . '/js/keenthemes.scripts.bundle', ['jquery'], false, true );

		// Enqueue Scripts.
		// Both of is_order_received_page() and is_wc_endpoint_url( 'order-received' ) will work to check if you are on the thankyou page in the frontend.
		// wp_enqueue_script( 'datatables' );
		wp_enqueue_script( 'FTPFILESYNC' );
		// wp_enqueue_script( 'prismjs' );wp_enqueue_script( 'popperjs' )
		;wp_enqueue_script( 'bootstrap' );
		// if( $this->allow_enqueue() ) {}
		
		// wp_enqueue_script( 'bootstrap-js' );
		// wp_enqueue_script( 'slick-js' );

		// If single post page
		// if ( is_single() ) {
		// 	wp_enqueue_script( 'single-js' );
		// }

		// If author archive page
		// if ( is_author() ) {
		// 	wp_enqueue_script( 'author-js' );
		// }
		// 

		wp_localize_script( 'FTPFILESYNC', 'fwpSiteConfig', apply_filters( 'futurewordpress/project/javascript/siteconfig', [
			'videoClips'		=> [],
		] ) );
	}
	private function allow_enqueue() {
		return ( function_exists( 'is_checkout' ) && ( is_checkout() || is_order_received_page() || is_wc_endpoint_url( 'order-received' ) ) );
	}

	/**
	 * Enqueue editor scripts and styles.
	 */
	public function enqueue_editor_assets() {

		$asset_config_file = sprintf( '%s/assets.php', FTPFILESYNC_BUILD_PATH );

		if ( ! file_exists( $asset_config_file ) ) {
			return;
		}

		$asset_config = require_once $asset_config_file;

		if ( empty( $asset_config['js/editor.js'] ) ) {
			return;
		}

		$editor_asset    = $asset_config['js/editor.js'];
		$js_dependencies = ( ! empty( $editor_asset['dependencies'] ) ) ? $editor_asset['dependencies'] : [];
		$version         = ( ! empty( $editor_asset['version'] ) ) ? $editor_asset['version'] : $this->filemtime( $asset_config_file );

		// Theme Gutenberg blocks JS.
		if ( is_admin() ) {
			wp_enqueue_script(
				'aquila-blocks-js',
				FTPFILESYNC_BUILD_JS_URI . '/blocks.js',
				$js_dependencies,
				$version,
				true
			);
		}

		// Theme Gutenberg blocks CSS.
		$css_dependencies = [
			'wp-block-library-theme',
			'wp-block-library',
		];

		wp_enqueue_style(
			'aquila-blocks-css',
			FTPFILESYNC_BUILD_CSS_URI . '/blocks.css',
			$css_dependencies,
			$this->filemtime( FTPFILESYNC_BUILD_CSS_DIR_PATH . '/blocks.css' ),
			'all'
		);

	}
	public function admin_enqueue_scripts( $curr_page ) {
		global $post;
		// if( ! in_array( $curr_page, [ 'edit.php', 'post.php' ] ) || 'shop_order' !== $post->post_type ) {return;}
		wp_register_style( 'FTPFILESYNCBackendCSS', FTPFILESYNC_BUILD_CSS_URI . '/backend.css', [], $this->filemtime( FTPFILESYNC_BUILD_CSS_DIR_PATH . '/backend.css' ), 'all' );
		// wp_register_script( 'FTPFILESYNCBackendJS', FTPFILESYNC_BUILD_JS_URI . '/backend.js', [ 'jquery' ], $this->filemtime( FTPFILESYNC_BUILD_JS_DIR_PATH . '/backend.js' ), true );

		// wp_register_style( 'FTPFILESYNCBackend', 'https://templates.iqonic.design/product/qompac-ui/html/dist/assets/css/qompac-ui.min.css?v=1.0.1', [], false, 'all' );
		// wp_register_style( 'FTPFILESYNCBackend', FTPFILESYNC_BUILD_LIB_URI . '/css/backend-library.css', [], false, 'all' );
		// wp_register_script( 'FTPFILESYNCBackend', FTPFILESYNC_BUILD_JS_URI . '/backend-library.js', [ 'jquery' ], $this->filemtime( FTPFILESYNC_BUILD_JS_DIR_PATH . '/backend-library.js' ), true );
		
		// wp_enqueue_style( 'FTPFILESYNCBackendCSS' );
		// wp_enqueue_script( 'FTPFILESYNCBackendJS' );
		// apply_filters( 'futurewordpress/project/admin/allowedpage', [] )
		if( isset( $_GET[ 'page' ] ) && in_array( $_GET[ 'page' ], [ 'ftp-file-synchronization' ] ) ) {
			wp_enqueue_style( 'FTPFILESYNCBackendCSS' );
		}

		// wp_localize_script( 'FTPFILESYNCBackendJS', 'fwpSiteConfig', apply_filters( 'futurewordpress/project/javascript/siteconfig', [] ) );
	}
	private function filemtime( $file ) {
		return apply_filters( 'futurewordpress/project/filesystem/filemtime', false, $file );
	}
	public function siteConfig( $args ) {
		return wp_parse_args( [
			'ajaxUrl'    		=> admin_url( 'admin-ajax.php' ),
			'ajax_nonce' 		=> wp_create_nonce( 'futurewordpress/project/verify/nonce' ),
			'is_admin' 			=> is_admin(),
			'buildPath'  		=> FTPFILESYNC_BUILD_URI,
			'videoClips'  		=> ( function_exists( 'WC' ) && WC()->session !== null ) ? (array) WC()->session->get( 'uploaded_files_to_archive' ) : [],
			'i18n'					=> [
				'sureToSubmit'							=> __( 'Want to submit it? You can retake.', 'ftp-file-synchronization' ),
				'uploading'									=> __( 'Uploading', 'ftp-file-synchronization' ),
				'click_here'								=> __( 'Click here', 'ftp-file-synchronization' ),
				'video_exceed_dur_limit'		=> __( 'Video exceed it\'s duration limit.', 'ftp-file-synchronization' ),
				'file_exceed_siz_limit'			=> __( 'Filesize exceed it maximum limit 30MB.', 'ftp-file-synchronization' ),
				'audio_exceed_dur_limit'		=> __( 'Audio exceed it\'s duration limit.', 'ftp-file-synchronization' ),
				'invalid_file_formate'			=> __( 'Invalid file formate.', 'ftp-file-synchronization' ),
				'device_error'							=> __( 'Device Error', 'ftp-file-synchronization' ),
				'confirm_cancel_subscribe'	=> __( 'Do you really want to cancel this Subscription?', 'ftp-file-synchronization' ),
				'i_confirm_it'							=> __( 'Yes I confirm it', 'ftp-file-synchronization' ),
				'confirming'								=> __( 'Confirming', 'ftp-file-synchronization' ),
				'successful'								=> __( 'Successful', 'ftp-file-synchronization' ),
				'request_failed'						=> __( 'Request failed', 'ftp-file-synchronization' ),
				'submit'										=> __( 'Submit', 'ftp-file-synchronization' ),
				'cancel'										=> __( 'Cancel', 'ftp-file-synchronization' ),
				'registration_link'					=> __( 'Registration link', 'ftp-file-synchronization' ),
				'password_reset'						=> __( 'Password reset', 'ftp-file-synchronization' ),
				'give_your_old_password'		=> __( 'Give here your old password', 'ftp-file-synchronization' ),
				'you_paused'								=> __( 'Subscription Paused', 'ftp-file-synchronization' ),
				'you_paused_msg'						=> __( 'Your retainer subscription has been successfully paused. We\'ll keep your account on hold until you\'re ready to resume. Thank you!', 'ftp-file-synchronization' ),
				'you_un_paused'							=> __( 'Subscription Resumed', 'ftp-file-synchronization' ),
				'you_un_paused_msg'					=> __( 'Welcome back! Your retainer subscription has been successfully resumed. We\'ll continue to provide you with our services as before. Thank you!', 'ftp-file-synchronization' ),
				'are_u_sure'								=> __( 'Are you sure?', 'ftp-file-synchronization' ),
				'sure_to_delete'						=> __( 'Are you sure about this deletation. Once you permit to delete, this user data will be removed from database forever. This can\'t be Undone', 'ftp-file-synchronization' ),
				'sent_reg_link'							=> __( 'Registration Link sent successfully!', 'ftp-file-synchronization' ),
				'sent_passreset'						=> __( 'Password reset link sent Successfully!', 'ftp-file-synchronization' ),
				'sometextfieldmissing'			=> __( 'Some required field you missed. Pleae fillup them first, then we can proceed.', 'ftp-file-synchronization' ),
				'retainer_zero'							=> __( 'Retainer Amount Zero', 'ftp-file-synchronization' ),
				'retainer_zerowarn'					=> __( 'You must set retainer amount before send a registration email.', 'ftp-file-synchronization' ),
				'selectcontract'						=> __( 'Select Contract', 'ftp-file-synchronization' ),
				'sure2logout'								=> __( 'Are you to Logout?', 'ftp-file-synchronization' ),
				'selectcontractwarn'				=> __( 'Please choose a contract to send the registration link. Once you have selected a contract and updated the form, you will be able to send the registration link.', 'ftp-file-synchronization' ),
				'subscription_toggled'			=> __( 'Thank you for submitting your request. We have reviewed and accepted it, and it is now pending for today. You will have the option to change your decision tomorrow. Thank you for your patience and cooperation.', 'ftp-file-synchronization' ),
				'rusure2unsubscribe'				=> __( 'You can only pause you retainer once every 60 days. Are you sure you want to pause your retainer?', 'ftp-file-synchronization' ),
				'rusure2subscribe'					=> __( 'We are super happy you want to resume your retainer. Are you sure you want to start now?', 'ftp-file-synchronization' ),
				'say2wait2pause'						=> __( 'You\'ve already paused your subscription this month. Please wait until 60 days over to pause again. If you need further assistance, please contact our administrative team.', 'ftp-file-synchronization' ),
			],
			'leadStatus'		=> apply_filters( 'futurewordpress/project/action/statuses', ['no-action' => __( 'No action fetched', 'ftp-file-synchronization' )], false )
		], (array) $args );
	}
	public function wp_denqueue_scripts() {}
	public function admin_denqueue_scripts() {
		if( ! isset( $_GET[ 'page' ] ) ||  $_GET[ 'page' ] !='crm_dashboard' ) {return;}
		wp_dequeue_script( 'qode-tax-js' );
	}

}
