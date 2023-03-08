<?php
/**
 * File Synchronization Using FTP/SFTP
 * 
 * @package FTPFileSynchronization
 */
namespace FTPFILESYNC_THEME\Inc;
use FTPFILESYNC_THEME\Inc\Traits\Singleton;

class Ftp {
	use Singleton;
	private $base;
	private $isSftp;
	private $theTable;
	private $lastError;

	private $ftpConnect;
	private $ftpServer;
	private $ftpUsername;
	private $ftpSettings;
	private $ftpPassword;
	private $ftpDirectory;
	private $localDirectory;
  
	private $theFiletoUpload;
	private $isDevelopmentMode;
	
	protected function __construct() {
		// global $wpdb;$this->theTable = $wpdb->prefix . 'fwp_ftplogs';
    $this->ftpServer = $this->ftpUsername = $this->ftpPassword = $this->ftpDirectory = $this->localDirectory = false;
    $this->isSftp = true;$this->ftpSettings = false;
    $this->isDevelopmentMode = false;
    $this->setup_hooks();

	}
	protected function setup_hooks() {
    add_action( 'synchronization_ftp_files', [ $this, 'syncFiles' ], 10, 0 );
    add_filter( 'futurewordpress/project/filesystem/mediadirectory', [ $this, 'getAllLocalFiles' ], 10, 2 );

		if( $this->isDevelopmentMode ) {add_action( 'init', [ $this, 'syncFiles' ], 10, 0 );}
	}
	public function initialize() {
    $this->lastError        = false;
    $this->ftpConnect       = false;
    $this->isSftp           = ( apply_filters( 'futurewordpress/project/system/isactive', 'ftp-isftp' ) && function_exists( 'ssh2_connect' ) );
    $this->ftpServer        = apply_filters( 'futurewordpress/project/system/getoption', 'ftp-server', false );
    $this->ftpUsername      = apply_filters( 'futurewordpress/project/system/getoption', 'ftp-username', false );
    $this->ftpPassword      = apply_filters( 'futurewordpress/project/system/getoption', 'ftp-password', false );
    $this->ftpDirectory     = apply_filters( 'futurewordpress/project/system/getoption', 'ftp-remotedir', false );
    $this->localDirectory   = apply_filters( 'futurewordpress/project/system/getoption', 'ftp-localdir', false );
    if( $this->localDirectory === false || empty( $this->localDirectory ) ) {
      $this->localDirectory = apply_filters( 'futurewordpress/project/filesystem/uploaddir', false );
    }
	}
  public function establishConnection() {
    if( $this->isSftp ) {
      // Establish SSH connection
      $ssh = \ssh2_connect( $this->ftpServer, 22 );
      if( \ssh2_auth_password( $ssh, $this->ftpUsername, $this->ftpPassword ) ) {
        // 
      }
      // Establish SFTP connection
      $this->ftpConnect = \ssh2_sftp( $ssh );
      if( $this->ftpConnect ) {$this->logEntry( 'Connected to SFTP on server ' . $this->ftpServer );}
      // Change to remote directory
      \ssh2_sftp_chdir( $this->ftpConnect, $this->ftpDirectory );
      if( $this->ftpConnect ) {$this->logEntry( 'Directory set to ' . $this->ftpDirectory );}
      return true;
    } else {
      $this->ftpConnect  = \ftp_connect( $this->ftpServer ) or $this->logEntry( "Could not connect to {$this->ftpServer}", true );
      if( $this->ftpConnect ) {$this->logEntry( 'Connected to FTP on server ' . $this->ftpServer );}
      $login_result = \ftp_login( $this->ftpConnect , $this->ftpUsername, $this->ftpPassword ) or $this->logEntry( "Could not login", true );
      if( $login_result ) {$this->logEntry( 'Successfully Logged in using  ' . $this->ftpPassword . ' for the user ' . $this->ftpUsername );}
      \ftp_pasv( $this->ftpConnect, true );// Enable passive mode
      return true;
    }
  }
  public function syncFiles() {
    if( ! apply_filters( 'futurewordpress/project/system/isactive', 'ftp-enable' ) ) {return;}
    if( ! function_exists( 'ftp_connect' ) ) {$this->logEntry( 'ftp_connect() extension is not installed on your php. Please enable if from Cpanel > Select PHP version > Extension. Then enable "FTP" & "SSH2".' );return;}

    if( $this->ftpServer === false ) {$this->initialize();}
    if( $this->ftpServer === false ) {return;}

    // phpinfo();wp_die();
    if( ! $this->establishConnection() ) {return;}
    // Get raw list of directory contents
    // $raw_list = ftp_rawlist( $this->ftpConnect, $this->ftpDirectory ) or wp_die( "Failed to get directory listing" );
    // print_r( $raw_list );

    $list = $this->scanFtpDirectory( $this->ftpDirectory );
    if( $list && is_array( $list ) && count( $list ) > 0 ) {
      $files = $this->scanLocalDirectory( $this->localDirectory );
      $deleted = $this->deleteFiles( $files, true );
    }
    $copied = $this->copyFtpFiles( $list );

    if( $this->isSftp ) {$this->logEntry( 'SFTP Maked as Closed' );} else {\ftp_close( $this->ftpConnect );$this->logEntry( 'FTP closed successfully.' );}

    if( $this->isDevelopmentMode ) {print_r( $this->lastError );wp_die();}
  }
  public function scanFtpDirectory( $dir ) {
    $file_list = ( $this->isSftp ) ? \ssh2_sftp_nlist( $this->ftpConnect, $dir ) : \ftp_nlist( $this->ftpConnect , $dir );$output = [];
    if( $file_list && count( $file_list ) >= 1 ) {$this->logEntry( 'Scanned FTP files: ' . json_encode( $file_list ) );}
    else {$this->logEntry( ( ! $file_list ) ? 'Scan failed on the dir: ' . $dir : 'Scan file list empty.' );}
    foreach( $file_list as $item ) {
      if( ! in_array( str_replace( $dir, '', $item ), [ '.', '..', '/.', '/..' ] ) ) {
        $output[] = $item;
      }
    }
    return $output;
  }
  public function copyFtpFiles( $files ) {
    $this->lastError = [];
    foreach( $files as $file ) {
      if( $this->isThisFileAllowed( $file ) ) {
        $destination_file = $this->localDirectory . '/' . pathinfo( $file, PATHINFO_BASENAME );
        $copied = ( $this->isSftp ) ? \ssh2_scp_recv( $this->ftpConnect, $file, $destination_file ) : \ftp_get( $this->ftpConnect, $destination_file, $file, FTP_BINARY );
        if( $copied ) {
          $mediaStatus = ( $this->storeOnMediaLibrary( $destination_file ) ) ? 'Updated with Media Library' : 'Failed to add to Media Library';
          $this->logEntry( 'File (' . $file . ') copied successfully & ' . $mediaStatus );
        } else {
          $error = error_get_last();// print_r( $error );
          $this->lastError[] = $error;
          $this->logEntry( 'Failed to copy file: ' . $file );
        }
      } else {
        $this->logEntry( 'This file is not supported to copy: ' . $file );
      }
    }
    return ( count( $this->lastError ) <= 0 );
  }
  public function scanLocalDirectory( $dir ) {
    $listed = [];$scan = scandir( $dir, SCANDIR_SORT_ASCENDING );
    foreach( $scan as $i ) {
      if( ! in_array( $i, [ '.', '..' ] ) && file_exists( $dir . '/' . $i ) && ! is_dir( $dir . '/' . $i ) ) {
        $listed[] = $dir . '/' . $i;
      }
    }
    $this->logEntry( 'Local files scanned successfully' );
    return $listed;
  }
  public function deleteFiles( $files = false ) {
    foreach( $files as $file ) {
      if( file_exists( $file ) && ! is_dir( $file ) ) {
        unlink( $file );
        $this->unStoreOnMediaLibrary( $file );
      }
    }
    $this->logEntry( 'Files deleted successfully.' );
    return true;
  }
  public function getAllLocalFiles( $default, $isUrl ) {
    if( $this->ftpServer === false ) {$this->initialize();}
    $listed=[];$files = $this->scanLocalDirectory( $this->localDirectory );
    if( $isUrl ) {
      $site_url = site_url( '/' );
      foreach( $files as $file ) {
        $listed[] = str_replace( ABSPATH, $site_url, $file );
      }
    } else {
      $listed = $files;
    }
    return $listed;
  }
  public function isThisFileAllowed( $file ) {
    // return true; // for all kind of files
    return ( ! in_array( pathinfo( $file, PATHINFO_EXTENSION ), [ '' ] ) );
    return in_array( pathinfo( $file, PATHINFO_EXTENSION ), [ 'pdf' ] );
  }
  public function storeOnMediaLibrary( $file_path ) {
    $file_url = str_replace( ABSPATH, $site_url, $file_path );
    $file_type = wp_check_filetype( basename( $file_path ), null );
    $attachment = [
      'post_mime_type' => $file_type['type'],
      'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $file_path ) ),
      'post_content' => '',
      'post_status' => 'inherit'
    ];
    $attachment_id = wp_insert_attachment( $attachment, $file_path );
    if( ! is_wp_error( $attachment_id ) ) {
      require_once( ABSPATH . 'wp-admin/includes/image.php' );
      $attachment_data = wp_generate_attachment_metadata( $attachment_id, $file_path );
      wp_update_attachment_metadata( $attachment_id, $attachment_data );
      return true;
    } else {
      return false;
    }
  }
  public function unStoreOnMediaLibrary( $file_path ) {
    global $wpdb;

    $file_path = str_replace( [ WP_CONTENT_DIR . '/uploads/' ], [ '' ], $file_path );

    $attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_wp_attached_file' AND meta_value='%s'", $file_path ) );
    // if( $this->isDevelopmentMode && empty( $file_path ) && isset( $_GET[ 'die_mode' ] ) ) {
    //   // print_r( [  ] );wp_die();
    // }
    if( $attachment_id ) {
      wp_delete_attachment( $attachment_id, true );
      $this->logEntry( 'Attachment Removed from Media Library.' );
    } else {
      $this->logEntry( 'Using (' . $file_path . ') path, Attachment not found on WordPress Media Library.' );
    }
  }
  public function logEntry( $log, $die = false ) {
    if( apply_filters( 'futurewordpress/project/system/isactive', 'log-enable' ) ) {
      if( ! $this->ftpSettings ) {
        $this->ftpSettings = (array) get_option( 'ftp-file-synchronization', false );
        $this->ftpSettings[ 'log-ftp' ] = isset( $this->ftpSettings[ 'log-ftp' ] ) ? $this->ftpSettings[ 'log-ftp' ] : '';
      }
      $this->ftpSettings[ 'log-ftp' ] = '** ' . $log . "\n" . $this->ftpSettings[ 'log-ftp' ];
      update_option( 'ftp-file-synchronization', $this->ftpSettings, true );
    }
    if( $die ) {wp_die( $log );}
  }
}
