<?php
/**
 * This plugin ordered by a client and done by Remal Mahmud (fiverr.com/mahmud_remal). Authority dedicated to that cient.
 *
 * @wordpress-plugin
 * Plugin Name:       FTP File Synchronization
 * Plugin URI:        https://github.com/mahmudremal/ftp-file-synchronization/
 * Description:       FTP/SFTP file synchronization custom build WordPress plugin, created by Remal Mahmud.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Remal Mahmud
 * Author URI:        https://github.com/mahmudremal/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ftp-file-synchronization
 * Domain Path:       /languages
 * 
 * @package FTPFileSynchronization
 * @author  Remal Mahmud (https://github.com/mahmudremal)
 * @version 1.0.2
 * @link https://github.com/mahmudremal/ftp-file-synchronization/
 * @category	WooComerce Plugin
 * @copyright	Copyright (c) 2023-25
 * 
 * payment custom link https://mysite.com/checkout/payment/39230/?pay_for_order=true&key=wc_order_UWdhxxxYYYzzz or get link $order->get_checkout_payment_url();
 */

/**
 * Bootstrap the plugin.
 */



defined( 'FTPFILESYNC_PROJECT__FILE__' ) || define( 'FTPFILESYNC_PROJECT__FILE__', untrailingslashit( __FILE__ ) );
defined( 'FTPFILESYNC_DIR_PATH' ) || define( 'FTPFILESYNC_DIR_PATH', untrailingslashit( plugin_dir_path( FTPFILESYNC_PROJECT__FILE__ ) ) );
defined( 'FTPFILESYNC_DIR_URI' ) || define( 'FTPFILESYNC_DIR_URI', untrailingslashit( plugin_dir_url( FTPFILESYNC_PROJECT__FILE__ ) ) );
defined( 'FTPFILESYNC_BUILD_URI' ) || define( 'FTPFILESYNC_BUILD_URI', untrailingslashit( FTPFILESYNC_DIR_URI ) . '/assets/build' );
defined( 'FTPFILESYNC_BUILD_PATH' ) || define( 'FTPFILESYNC_BUILD_PATH', untrailingslashit( FTPFILESYNC_DIR_PATH ) . '/assets/build' );
defined( 'FTPFILESYNC_BUILD_JS_URI' ) || define( 'FTPFILESYNC_BUILD_JS_URI', untrailingslashit( FTPFILESYNC_DIR_URI ) . '/assets/build/js' );
defined( 'FTPFILESYNC_BUILD_JS_DIR_PATH' ) || define( 'FTPFILESYNC_BUILD_JS_DIR_PATH', untrailingslashit( FTPFILESYNC_DIR_PATH ) . '/assets/build/js' );
defined( 'FTPFILESYNC_BUILD_IMG_URI' ) || define( 'FTPFILESYNC_BUILD_IMG_URI', untrailingslashit( FTPFILESYNC_DIR_URI ) . '/assets/build/src/img' );
defined( 'FTPFILESYNC_BUILD_CSS_URI' ) || define( 'FTPFILESYNC_BUILD_CSS_URI', untrailingslashit( FTPFILESYNC_DIR_URI ) . '/assets/build/css' );
defined( 'FTPFILESYNC_BUILD_CSS_DIR_PATH' ) || define( 'FTPFILESYNC_BUILD_CSS_DIR_PATH', untrailingslashit( FTPFILESYNC_DIR_PATH ) . '/assets/build/css' );
defined( 'FTPFILESYNC_BUILD_LIB_URI' ) || define( 'FTPFILESYNC_BUILD_LIB_URI', untrailingslashit( FTPFILESYNC_DIR_URI ) . '/assets/build/library' );
defined( 'FTPFILESYNC_ARCHIVE_POST_PER_PAGE' ) || define( 'FTPFILESYNC_ARCHIVE_POST_PER_PAGE', 9 );
defined( 'FTPFILESYNC_SEARCH_RESULTS_POST_PER_PAGE' ) || define( 'FTPFILESYNC_SEARCH_RESULTS_POST_PER_PAGE', 9 );
defined( 'FUTUREWORDPRESS_PROJECT_OPTIONS' ) || define( 'FUTUREWORDPRESS_PROJECT_OPTIONS', get_option( 'ftp-file-synchronization' ) );

require_once FTPFILESYNC_DIR_PATH . '/inc/helpers/autoloader.php';
// require_once FTPFILESYNC_DIR_PATH . '/inc/helpers/template-tags.php';

if( ! function_exists( 'futurewordpress_ftpsync_get_theme_instance' ) ) {
	function futurewordpress_ftpsync_get_theme_instance() {\FTPFILESYNC_THEME\Inc\Project::get_instance();}
}
futurewordpress_ftpsync_get_theme_instance();



