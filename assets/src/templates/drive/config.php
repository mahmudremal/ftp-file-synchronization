<?php
/**
CREATE TABLE `drive_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `google_drive_file_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
*/


// Database configuration    
define('DB_HOST', 'localhost');
define('DB_NAME', 'wemakeco_WPATK');
define('DB_USERNAME', 'wemakeco_WPATK');
define('DB_PASSWORD', 'VA?-RYrNng1LKQcOd');
 
// Google API configuration 
define('GOOGLE_CLIENT_ID', '719552948296-d14739tknbd33tv16b932pb859kcvums.apps.googleusercontent.com'); 
define('GOOGLE_CLIENT_SECRET', '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'); 
define('GOOGLE_OAUTH_SCOPE', 'https://www.googleapis.com/auth/drive'); 
define('REDIRECT_URI', 'https://wemakecontent.net/auth/google/capture');
// https://wemakecontent.net/wp-content/plugins/we-make-content-crm/assets/drive/google_drive_sync.php
 
// Start session 
if(!session_id()) session_start(); 
 
// Google OAuth URL 
$googleOauthURL = 'https://accounts.google.com/o/oauth2/auth?scope=' . urlencode(GOOGLE_OAUTH_SCOPE) . '&redirect_uri=' . REDIRECT_URI . '&response_type=code&client_id=' . GOOGLE_CLIENT_ID . '&access_type=online'; 
 
?>