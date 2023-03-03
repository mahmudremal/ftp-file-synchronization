<?php
/**
 * Blocks
 *
 * @package WeMakeContentCMS
 */
namespace WEMAKECONTENTCMS_THEME\Inc;
use WEMAKECONTENTCMS_THEME\Inc\Traits\Singleton;
class GoogleDrive {
	use Singleton;
	private $clientId;
	private $clientSecret;
	private $refreshToken;
	protected function __construct() {
		$this->clientId = "<CLIENT_ID>";
		$this->clientSecret = "<CLIENT_SECRET>";
		$this->refreshToken = "<REFRESH_TOKEN>";
		$this->setup_hooks();
	}
	protected function setup_hooks() {
		/**
		 * Actions.
		 */
		// add_filter( 'block_categories_all', [ $this, 'add_block_categories' ] );
	}
	public function init() {
		$this->redirectURL = apply_filters( 'futurewordpress/project/socialauth/redirect', '/handle/google', 'google' );
		
		if( isset( $_GET[ 'googletest' ] ) ) {
			print_r( "https://accounts.google.com/o/oauth2/auth?response_type=code&client_id={$this->clientId}&redirect_uri={$this->redirectURL}&scope=" . urlencode( 'https://www.googleapis.com/auth/drive' ) . "&access_type=offline\r\n" );
		// $googleOauthURL = 'https://accounts.google.com/o/oauth2/auth?scope=' . urlencode( 'https://www.googleapis.com/auth/drive' ) . '&redirect_uri=' . $this->redirectURL . '&response_type=code&client_id=' . $this->clientId . '&access_type=online';

			$file_path = $this->theFiletoUpload;
			$access_token = '4/0AWtgzh6W8uSwT4fjuZHcEMLpHOhC1t-pJoHvqcby-6czNrmaFrB64Xu2eOZ4srHqNSTU7Q';
			$parent_folder_id = $this->parentDirectory;

			// $client = new \IGD\Authorization();
			// folder id maybe eyJpZCI6IjFLR3BEc0ZYNmJEMUFvcFRWYUZRemw4UG1RWjI2UDhQdiIsImFjY291bnRJZCI6IjA1MzY1Mzk1NTQ4OTQxNDc0MTk2IiwibmFtZSI6IkZvbGRlciJ9
			$response = \IGD\App::instance()->delete( ['1CJHILybYLv0nssI8sR5_Gy7kf0SfL4cn'] ); // \IGDGoogle_Client::getAccessToken();
			print_r( $response );

			
			$response = get_option( 'igd_tokens', [] );
			// $response = $this->upload_to_google_drive($file_path, $access_token, $parent_folder_id);
			foreach( $response as $i => $row ) {
				if( ! empty( $row ) ) {
					$row = json_decode( $row, true );
					if( isset( $row[ 'access_token' ] ) ) {
						$this->accessToken = $row[ 'access_token' ];
					}
				}
			}
			$response = $this->upload_to_google_drive($file_path, $access_token, $parent_folder_id);
			print_r( [$file_path, $this->accessToken, $parent_folder_id, $response] );
			// print_r( urldecode( 'https://wemakecontent.net/auth/google/capture?code=4%2F0AWtgzh6W8uSwT4fjuZHcEMLpHOhC1t-pJoHvqcby-6czNrmaFrB64Xu2eOZ4srHqNSTU7Q&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fdrive' ) );
			wp_die();
		}
	}
	/**
	 * Authenticate Google Drive and get access token
	 * @return string
	 */
	private function authenticate() {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://oauth2.googleapis.com/token");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "client_id={$this->clientId}&client_secret={$this->clientSecret}&refresh_token={$this->refreshToken}&grant_type=refresh_token");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

		$response = json_decode($response);
		$accessToken = $response->access_token;
		$this->refreshToken = $accessToken;
		return $accessToken;
	}
	/**
	 * Upload a file using CURL.
	 */
	private function saveAFile( $file = false ) {
		if( ! $file ) {return;}
		$fileName = "example_file.txt";
		$parentFolderId = "<PARENT_FOLDER_ID>";
		$mimeType = "text/plain";

		$file = new CURLFile($fileName, $mimeType, $fileName);

		$postData = [
			'name' => $fileName,
			'parents' => [$parentFolderId],
			'mimeType' => $mimeType
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Bearer $this->accessToken",
			"Content-Type: multipart/related"
		]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, [
			"metadata" => json_encode($postData),
			"file" => $file
		]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($responseCode == 200) {
			echo "File successfully uploaded.";
		} else {
			echo "File upload failed with response code: $responseCode.";
		}
	}
	private function retrieveTheFileMetadata() {
		$fileId = "<FILE_ID>";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/files/$fileId");
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Bearer $accessToken"
		]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

		$response = json_decode($response);
		$fileName = $response->name;
		$fileMimeType = $response->mimeType;
	}
	private function tempDownloadLink() {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/files/$fileId?alt=media");
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Bearer $accessToken",
			"Content-Type: $fileMimeType"
		]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

		header("Content-Type: $fileMimeType");
		header("Content-Disposition: attachment; filename=\"$fileName\"");
		echo $response;
	}
	private function searchOnDrive( $fileName = false ) {
		if( ! $fileName ) {return;}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/files?q=name='$fileName'");
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Bearer $accessToken"
		]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($response);
		$file = $response->files[0];
		$fileId = $file->id;
	}



	
	public function upload_to_google_drive($file_path, $access_token, $parent_folder_id) {
    // Set API endpoint
    $url = 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart';

    // Set file metadata
    $file_name = basename($file_path);
    $mime_type = mime_content_type($file_path);
    $metadata = [
        'name' => $file_name,
        'parents' => [$parent_folder_id]
    ];
    $json_metadata = json_encode($metadata);

    // Set file contents
    $file_contents = file_get_contents($file_path);

    // Set boundary string
    $boundary = '-------------------------' . mt_rand();

    // Set headers
    $headers = [
        'Content-Type: multipart/related; boundary=' . $boundary,
        'Authorization: Bearer ' . $access_token
    ];

    // Set request body
    $request_body = "--$boundary\r\n";
    $request_body .= "Content-Type: application/json; charset=UTF-8\r\n\r\n";
    $request_body .= "$json_metadata\r\n";
    $request_body .= "--$boundary\r\n";
    $request_body .= "Content-Type: $mime_type\r\n\r\n";
    $request_body .= "$file_contents\r\n";
    $request_body .= "--$boundary--";

    // Create curl request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Send curl request and get response
    $response = curl_exec($ch);
    curl_close($ch);

    // Return response
    return $response;
	}


	public function googleDrive() {
		$access_token = 'your_access_token_here';
		$file_path = $this->theFiletoUpload;
		$file_name = pathinfo( $this->theFiletoUpload, PATHINFO_BASENAME );
		$folder_id = $this->parentDirectory; //if you want to upload the file to a specific folder

		$url = 'https://www.googleapis.com/upload/drive/v3/files?uploadType=resumable';

		$headers = array(
			'Authorization: Bearer '.$access_token,
			'Content-Type: application/json; charset=UTF-8',
		);

		$data = array(
			'name' => $file_name,
			'parents' => array($folder_id)
		);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

		$response = curl_exec($ch);
		print_r( $response );

		if (curl_errno($ch)) {
			echo 'Error: ' . curl_error($ch);
			exit;
		}

		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($http_code == 200) {
			$upload_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		} else {
			echo 'Error: Failed to get upload URL';
			exit;
		}

		curl_close($ch);

		$ch = curl_init();

		$headers = array(
			'Content-Type: application/octet-stream',
			'Authorization: Bearer '.$access_token,
		);

		curl_setopt($ch, CURLOPT_URL, $upload_url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($file_path));

		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			echo 'Error: ' . curl_error($ch);
			exit;
		}

		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($http_code == 200) {
			echo 'File uploaded successfully';
		} else {
			echo 'Error: Failed to upload file';
		}

		curl_close($ch);

	}




	public function upload_file() {
		// Prepare the file
		$file = 'example.txt';
		$access_token = '';
		$file_size = filesize($file);
		$file_data = file_get_contents($file);
		$file_name = 'example.txt';
		$boundary = uniqid();
		// Prepare the API request
		$url = 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart';
		$headers = array(
			'Authorization: Bearer ' . $access_token,
			'Content-Type: multipart/related; boundary="' . $boundary . '"'
		);
		$post_data = '--' . $boundary . "\r\n";
		$post_data .= 'Content-Type: application/json; charset=UTF-8' . "\r\n\r\n";
		$post_data .= json_encode(array(
			'name' => $file_name
		)) . "\r\n";
		$post_data .= '--' . $boundary . "\r\n";
		$post_data .= 'Content-Type: ' . mime_content_type($file) . "\r\n";
		$post_data .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
		$post_data .= base64_encode($file_data) . "\r\n";
		$post_data .= '--' . $boundary . '--' . "\r\n";

		// Send the API request
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

		// Check for errors
		if (!$response) {
			die('Error: API request failed');
		}

		// Decode the API response
		$response_data = json_decode($response, true);

		// Check for errors
		if (isset($response_data['error'])) {
			die('Error: ' . $response_data['error']['message']);
		}

		// Print the file ID
		echo 'File ID: ' . $response_data['id'];
	}
	private function searchOnDrive( $fileName = false ) {
		if( ! $fileName ) {return;}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/files?q=name='$fileName'");
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Bearer {$this->clientSecret}"
		]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($response);
		$file = $response->files[0];
		$fileId = $file->id;

		print_r( [ $this->clientSecret, $response ] );wp_die();
	}
	private function uploadToGoogleDrive( $file_path, $access_token, $parent_folder_id = false ) {
		$parent_folder_id = ( $parent_folder_id ) ? $parent_folder_id : $this->parentDirectory;
		$url = "https://www.googleapis.com/upload/drive/v3/files?uploadType=resumable";
		$headers = [
			"Authorization: Bearer " . $access_token,
			"Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
			"X-Upload-Content-Type: application/zip",
			"X-Upload-Content-Length: " . filesize( $file_path )
		];
		$data = [
			"name" => basename( $file_path ),
			"parents" => [ $parent_folder_id ]
		];
		$json_data = json_encode($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
		curl_setopt($ch, CURLOPT_HEADER, true);
		$response = curl_exec($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		curl_close($ch);

		print_r( $response );
		$location = "";
		if (preg_match('/location:\s*(\S+)/i', $response, $match)) {
			$location = trim($match[1]);
		}
		if( ! empty( $location ) ) {
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $location );
			curl_setopt( $ch, CURLOPT_PUT, true );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_HEADER, false );
			$fh = fopen( $file_path, 'r' );
			curl_setopt( $ch, CURLOPT_INFILE, $fh );
			curl_setopt( $ch, CURLOPT_INFILESIZE, filesize( $file_path) );
			$result = curl_exec( $ch );
			fclose( $fh );
			curl_close( $ch );
			print_r( $result );
			return $result;
		}

		return false;
	}
	public function serverToDriveUpload() {
		$file_path = $this->theFiletoUpload;
		$access_token = $this->get_access_token( '' );
		$parent_folder_id = $this->parentDirectory;
		$result = $this->uploadToGoogleDrive( $file_path, $access_token, $parent_folder_id );

		if( $result !== false ) {
			echo "File uploaded successfully!";
		} else {
			echo "Failed to upload file.";
		}

	}
	


	private function upload_to_google_drive_1($file, $fileName) {
		$filePath = realpath($file);
		$mimeType = mime_content_type($filePath);
		// Set up the cURL request
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => array('file' => curl_file_create($filePath, $mimeType, $fileName)),
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer {$this->accessToken}",
				"Content-Type: multipart/related"
			),
		));
		// Send the request and get the response
		$response = curl_exec($curl);
		$err = curl_error($curl);
		// Close the cURL session
		curl_close($curl);
		// Return the response or error
		if ($err) {
			return "cURL Error #:" . $err;
		} else {
			return $response;
		}
	}
	private function get_access_token() {
		// Set up the cURL request
		$body = [
			'code'					=> '4/0AWtgzh6sAkiOikwI0NBvmHErOHE4bmzC9KIVvJeNo6VxMaDU7eVrh53SuoqZnnLvoQD1sg', // $this->authorizationCode,
			'client_id'			=> $this->clientId,
			'client_secret'	=> $this->clientSecret,
			'redirect_uri'	=> $this->redirectURL,
			'grant_type'		=> 'authorization_code'
		];
		$curl = curl_init();
		curl_setopt_array( $curl, array(
			CURLOPT_URL => "https://oauth2.googleapis.com/token",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => http_build_query( $body ),
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/x-www-form-urlencoded"
			),
		) );
		// Send the request and get the response
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		print_r( [ $response, $body ] );
		// Check for errors
		if( $err ) {
			return "cURL Error #:" . $err;
		} else {
			// Parse the response and return the access token
			$response = json_decode( $response, true );
			if( isset( $response[ 'access_token' ] ) ) {
				$this->accessToken = $response[ 'access_token' ];
			}
			return $response[ 'access_token' ];
		}
	}


	private function getAccessToken() {
		$body = [
			'code' 							=> $this->authorizationCode,
			'client_id' 				=> $this->clientId,
			'client_secret'			=> $this->clientSecret,
			'redirect_uri'			=> $this->redirectURL,
			'grant_type'				=> 'authorization_code',
			'refresh_token'			=> $this->refreshToken,
			// 'grant_type'				=> 'refresh_token'
		];
		print_r( $body );
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( $body ) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		print_r( $response );wp_die();
		$accessToken = json_decode($response)->access_token;
		return $accessToken;
	}
	public function uploadZipFile( $zipFilePath ) {
    $ch = curl_init();
    $postData = array(
			'file' => new \CURLFile($zipFilePath)
		);
		$headers = array(
				'Authorization: Bearer ' . $this->getAccessToken(),
				'Content-Type: multipart/related; boundary=foo_bar_baz'
		);
		curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart' );
		curl_setopt($ch, CURLOPT_POST, 1 );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		$response = curl_exec($ch);
    if (curl_errno($ch)) {
			$error_msg = curl_error($ch);
			curl_close($ch);
			return "cURL Error: " . $error_msg;
    }
    curl_close($ch);
    return $response;
	}

	public function updateTokens() {
		$clientId				= $this->clientId;
		$clientSecret		= $this->clientSecret;
		$redirectUri		= $this->redirectURL;
		$code						= $this->authorizationCode;

		$ch = curl_init('https://accounts.google.com/o/oauth2/token');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
			'code' => $code,
			'client_id' => $clientId,
			'client_secret' => $clientSecret,
			'redirect_uri' => $redirectUri,
			'grant_type' => 'authorization_code',
		)));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		curl_close($ch);

		if (!$response) {
			die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
		}

		$responseData = json_decode($response, true);

		print_r( $responseData );

		$this->accessToken = $responseData['access_token'];
		$this->refreshToken = $responseData['refresh_token'];
	}
	public function upload_to_google_drive_full() {
		// Define variables
		$client_id				= $this->clientId;
		$client_secret		= $this->clientSecret;
		$refresh_token		= $this->refreshToken;
		$folder_id				= $this->parentDirectory;
		$file_path				= $this->theFiletoUpload;
		$access_token			= $this->get_access_token();
		print_r( [ $access_token ] );return false;

		if( ! $access_token ) {
			wp_die('Could not get access token');
		}

		// Upload the file to Google Drive
		$url = 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart';

		// Set the file metadata
		$file_name = basename($file_path);
		$file_mime_type = mime_content_type($file_path);
		$file_metadata = array(
				'name' => $file_name,
				'parents' => array($folder_id)
		);

		$data = array(
				'data' => json_encode($file_metadata),
				'content' => file_get_contents($file_path),
				'mimeType' => $file_mime_type,
		);

		$headers = array(
				'Authorization: Bearer ' . $access_token,
				'Content-Type: multipart/related; boundary=foo_bar_baz',
				'Content-Length: ' . strlen(json_encode($file_metadata)) + filesize($file_path) + 101
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

		if (!$response) {
			wp_die('Error uploading file');
		}

		$response_data = json_decode($response, true);

		// Print the file ID of the uploaded file
		echo 'File ID: ' . $response_data['id'];

	}
}
