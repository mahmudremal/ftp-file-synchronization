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
    add_filter( 'futurewordpress/project/settings/fields', [ $this, 'menus' ], 10, 1 );
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
		return $args;
	}
	public function menus( $args ) {
    // get_FwpOption( 'key', 'default' ) | apply_filters( 'futurewordpress/project/system/getoption', 'key', 'default' )
		// is_FwpActive( 'key' ) | apply_filters( 'futurewordpress/project/system/isactive', 'key' )
		$args = [];
		$args['standard'] 		= [
			'title'							=> __( 'General', 'ftp-file-synchronization' ),
			'description'				=> __( 'Generel fields comst commonly used to changed.', 'ftp-file-synchronization' ),
			'fields'						=> [
				[
					'id' 						=> 'general-enable',
					'label'					=> __( 'Enable', 'ftp-file-synchronization' ),
					'description'		=> __( 'Mark to enable function of this Plugin.', 'ftp-file-synchronization' ),
					'type'					=> 'checkbox',
					'default'				=> true
				],
				[
					'id' 						=> 'general-address',
					'label'					=> __( 'Address', 'ftp-file-synchronization' ),
					'description'		=> __( 'Company address, that might be used on invoice and any public place if needed.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'general-archivedelete',
					'label'					=> __( 'Archive delete', 'ftp-file-synchronization' ),
					'description'		=> __( 'Enable archive delete permission on frontend, so that user can delete archive files and data from their profile.', 'ftp-file-synchronization' ),
					'type'					=> 'checkbox',
					'default'				=> ''
				],
				[
					'id' 						=> 'general-leaddelete',
					'label'					=> __( 'Delete User', 'ftp-file-synchronization' ),
					'description'		=> __( 'Enable this option to apear a possibility to delete user/lead with one click. If it\'s disabled, then user delete option on list and single user details page will gone until turn it on.', 'ftp-file-synchronization' ),
					'type'					=> 'checkbox',
					'default'				=> false
				],
			]
		];
		$args['permalink'] 		= [
			'title'						=> __( 'Permalink', 'ftp-file-synchronization' ),
			'description'			=> __( 'Setup some permalink like dashboard and like this kind of things.', 'ftp-file-synchronization' ),
			'fields'					=> [
				[
					'id' 							=> 'permalink-dashboard',
					'label'						=> __( 'Dashboard Slug', 'ftp-file-synchronization' ),
					'description'			=> __( 'Enable dashboard parent Slug. By default it is "/dashboard". Each time you change this field you\'ve to re-save permalink settings.', 'ftp-file-synchronization' ),
					'type'						=> 'text',
					'default'					=> 'dashboard'
				],
				[
					'id' 						=> 'permalink-userby',
					'label'					=> __( 'Dashboard Slug', 'ftp-file-synchronization' ),
					'description'		=> __( 'Enable dashboard parent Slug. By default it is "/dashboard".', 'ftp-file-synchronization' ),
					'type'					=> 'radio',
					'default'				=> 'id',
					'options'				=> [ 'id' => __( 'User ID', 'ftp-file-synchronization' ), 'slug' => __( 'User Unique Name', 'ftp-file-synchronization' ) ]
				],
			]
		];
		$args['dashboard'] 		= [
			'title'							=> __( 'Dashboard', 'ftp-file-synchronization' ),
			'description'				=> __( 'Dashboard necessery fields, text and settings can configure here. Some tags on usable fields can be replace from here.', 'ftp-file-synchronization' ) . $this->commontags( true ),
			'fields'						=> [
				[
					'id' 						=> 'dashboard-disablemyaccount',
					'label'					=> __( 'Disable My Account', 'ftp-file-synchronization' ),
					'description'		=> __( 'Disable WooCommerce My Account dashboard and form redirect user to new dashboard. If you enable it, it\'ll apply. But be aware, WooCommerce orders and paid downloads are listed on My Account page.', 'ftp-file-synchronization' ),
					'type'					=> 'checkbox',
					'default'				=> false
				],
				[
					'id' 						=> 'dashboard-title',
					'label'					=> __( 'Dashboard title', 'ftp-file-synchronization' ),
					'description'		=> __( 'The title on dahsboard page. make sure you user tags properly.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> sprintf( __( 'Client Dashoard | %s | %s', 'ftp-file-synchronization' ), '{username}', '{sitename}' )
				],
				[
					'id' 						=> 'dashboard-yearstart',
					'label'					=> __( 'Year Starts', 'ftp-file-synchronization' ),
					'description'		=> __( 'The Year range on dashboard starts from.', 'ftp-file-synchronization' ),
					'type'					=> 'number',
					'default'				=> date( 'Y' )
				],
				[
					'id' 						=> 'dashboard-yearend',
					'label'					=> __( 'Yeah Ends with', 'ftp-file-synchronization' ),
					'description'		=> __( 'The Year range on dashboard ends on.', 'ftp-file-synchronization' ),
					'type'					=> 'number',
					'default'				=> ( date( 'Y' ) + 3 )
				],
				[
					'id' 						=> 'dashboard-headerbg',
					'label'					=> __( 'Header Background', 'ftp-file-synchronization' ),
					'description'		=> __( 'Dashboard header background image url.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> ''
				],
			]
		];
		$args['links'] 		= [
			'title'							=> __( 'Links', 'ftp-file-synchronization' ),
			'description'				=> __( 'Documentation feature and their links can be change from here. If you leave blank anything then these "Learn More" never display.', 'ftp-file-synchronization' ) . $this->commontags( true ),
			'fields'						=> [
				[
					'id' 						=> 'docs-monthlyretainer',
					'label'					=> __( 'Monthly Retainer', 'ftp-file-synchronization' ),
					'description'		=> __( 'Your Monthly retainer that could be chaged anytime. Once you\'ve changed this amount, will be sync with your stripe account.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-monthlyretainerurl',
					'label'					=> __( 'Learn more', 'ftp-file-synchronization' ),
					'description'		=> __( 'The URL to place on Learn more.', 'ftp-file-synchronization' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-contentcalendly',
					'label'					=> __( 'Content Calendar', 'ftp-file-synchronization' ),
					'description'		=> __( 'See your content calendar on Calendly.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-contentcalendlyurl',
					'label'					=> __( 'Learn more', 'ftp-file-synchronization' ),
					'description'		=> __( 'The URL to place on Learn more.', 'ftp-file-synchronization' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-contentlibrary',
					'label'					=> __( 'Content Library', 'ftp-file-synchronization' ),
					'description'		=> __( 'Open content library from here.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-contentlibraryurl',
					'label'					=> __( 'Learn more', 'ftp-file-synchronization' ),
					'description'		=> __( 'The URL to place on Learn more.', 'ftp-file-synchronization' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-clientrowvideos',
					'label'					=> __( 'Client Raw Video Archive', 'ftp-file-synchronization' ),
					'description'		=> __( 'All of the video files are here. Click on the buton to open all archive list.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-clientrowvideosurl',
					'label'					=> __( 'Learn more', 'ftp-file-synchronization' ),
					'description'		=> __( 'The URL to place on Learn more.', 'ftp-file-synchronization' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-manageretainer',
					'label'					=> __( 'Manage your Retainer', 'ftp-file-synchronization' ),
					'description'		=> __( 'Manage your retainer from here. You can pause or cancel it from here.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-manageretainerurl',
					'label'					=> __( 'Learn more', 'ftp-file-synchronization' ),
					'description'		=> __( 'The URL to place on Learn more.', 'ftp-file-synchronization' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-paymenthistory',
					'label'					=> __( 'Payment History', 'ftp-file-synchronization' ),
					'description'		=> __( 'Payment history is synced form your stripe account since you started subscription.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-paymenthistoryurl',
					'label'					=> __( 'Learn more', 'ftp-file-synchronization' ),
					'description'		=> __( 'The URL to place on Learn more.', 'ftp-file-synchronization' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-changepassword',
					'label'					=> __( 'Payment History', 'ftp-file-synchronization' ),
					'description'		=> __( 'Change your password from here. This won\'t store on our database. Only encrypted password we store and make sure you\'ve saved your password on a safe place.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-changepasswordurl',
					'label'					=> __( 'Learn more', 'ftp-file-synchronization' ),
					'description'		=> __( 'The URL to place on Learn more.', 'ftp-file-synchronization' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-emailaddress',
					'label'					=> __( 'Email Address', 'ftp-file-synchronization' ),
					'description'		=> __( 'Email address required. Don\'t worry, we won\'t sent spam.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-emailaddressurl',
					'label'					=> __( 'Learn more', 'ftp-file-synchronization' ),
					'description'		=> __( 'The URL to place on Learn more.', 'ftp-file-synchronization' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-contactnumber',
					'label'					=> __( 'Contact Number', 'ftp-file-synchronization' ),
					'description'		=> __( 'Your conatct number is necessery in case if you need to communicate with you.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-contactnumberurl',
					'label'					=> __( 'Learn more', 'ftp-file-synchronization' ),
					'description'		=> __( 'The URL to place on Learn more.', 'ftp-file-synchronization' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-website',
					'label'					=> __( 'Website URL', 'ftp-file-synchronization' ),
					'description'		=> __( 'Give here you websute url if you have. Some case we might need to get idea about your and your company information.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-websiteurl',
					'label'					=> __( 'Learn more', 'ftp-file-synchronization' ),
					'description'		=> __( 'The URL to place on Learn more.', 'ftp-file-synchronization' ),
					'type'					=> 'url',
					'default'				=> ''
				],
			]
		];
		$args['rest'] 		= [
			'title'							=> __( 'Rest API', 'ftp-file-synchronization' ),
			'description'				=> __( 'Setup what happened when a rest api request fired on this site.', 'ftp-file-synchronization' ),
			'fields'						=> [
				[
					'id' 						=> 'rest-createprofile',
					'label'					=> __( 'Create profile', 'ftp-file-synchronization' ),
					'description'		=> __( 'When a request email doesn\'t match any account, so will it create a new user account?.', 'ftp-file-synchronization' ),
					'type'					=> 'checkbox',
					'default'				=> true
				],
				[
					'id' 						=> 'rest-updateprofile',
					'label'					=> __( 'Update profile', 'ftp-file-synchronization' ),
					'description'		=> __( 'When a request email detected an account, so will it update profile with requested information?.', 'ftp-file-synchronization' ),
					'type'					=> 'checkbox',
					'default'				=> false
				],
				[
					'id' 						=> 'rest-preventemail',
					'label'					=> __( 'Prevent Email', 'ftp-file-synchronization' ),
					'description'		=> __( 'Creating an account will send an email by default. Would you like to prevent sending email from rest request operation?', 'ftp-file-synchronization' ),
					'type'					=> 'checkbox',
					'default'				=> true
				],
				[
					'id' 						=> 'rest-defaultpass',
					'label'					=> __( 'Default Password', 'ftp-file-synchronization' ),
					'description'		=> __( 'The default password will be applied if any request contains emoty password or doesn\'t. Default value is random number.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> ''
				],
			]
		];
		$args['auth'] 		= [
			'title'							=> __( 'Social Auth', 'ftp-file-synchronization' ),
			'description'				=> __( 'Social anuthentication requeired provider API keys and some essential information. Claim them and setup here. Every API has an expiry date. So further if you face any problem with social authentication, make sure if api validity expired.', 'ftp-file-synchronization' ),
			'fields'						=> [
				[
					'id' 						=> 'auth-enable',
					'label'					=> __( 'Enable Social Authetication', 'ftp-file-synchronization' ),
					'description'		=> __( 'Mark this field to run social authentication. Once you disable from here, social authentication will be disabled from everywhere.', 'ftp-file-synchronization' ),
					'type'					=> 'checkbox',
					'default'				=> true
				],
				[
					'id' 						=> 'auth-google',
					'label'					=> __( 'Enable Google Authetication', 'ftp-file-synchronization' ),
					'description'		=> __( 'If you don\'t want to enable google authentication, you can disable this function from here.', 'ftp-file-synchronization' ),
					'type'					=> 'checkbox',
					'default'				=> true
				],
				[
					'id' 						=> 'auth-connectdrive',
					'label'					=> __( 'Connect with Google Drive?', 'ftp-file-synchronization' ),
					'description'		=> sprintf( __( 'Click on this %slink%s and allow access to connect with it.', 'ftp-file-synchronization' ), '<a href="'. site_url( '/auth/drive/redirect/' ) . '" target="_blank">', '</a>' ),
					'type'					=> 'textcontent'
				],
				[
					'id' 						=> 'auth-googleclientid',
					'label'					=> __( 'Google Client ID', 'ftp-file-synchronization' ),
					'description'		=> __( 'Your Google client or App ID, that you created for Authenticate.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'auth-googleclientsecret',
					'label'					=> __( 'Google Client Secret', 'ftp-file-synchronization' ),
					'description'		=> __( 'Your Google client or App Secret. Is required here.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'auth-googledrivefolder',
					'label'					=> __( 'Storage Folder ID', 'ftp-file-synchronization' ),
					'description'		=> __( 'ID of that specific folder where you want to sync files.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'auth-googleclientredirect',
					'label'					=> __( 'Google App Redirect', 'ftp-file-synchronization' ),
					'description'		=> __( 'Place this link on Google Auth Callback or Redirect field on your Google App.', 'ftp-file-synchronization' ) . '<code>' . apply_filters( 'futurewordpress/project/socialauth/redirect', '/handle/google', 'google' ) . '</code>',
					'type'					=> 'textcontent'
				],
				[
					'id' 						=> 'auth-googleauthlink',
					'label'					=> __( 'Google Auth Link', 'ftp-file-synchronization' ),
					'description'		=> __( 'Use this link on your "Login with Google" button.', 'ftp-file-synchronization' ) . '<code>' . apply_filters( 'futurewordpress/project/socialauth/link', '/auth/google', 'google' ) . '</code>',
					'type'					=> 'textcontent'
				],
			]
		];
		$args['social'] 		= [
			'title'							=> __( 'Social', 'ftp-file-synchronization' ),
			'description'				=> __( 'Setup your social links her for client dashboard only. Only people who loggedin, can access these social links.', 'ftp-file-synchronization' ),
			'fields'						=> [
				[
					'id' 						=> 'social-contact',
					'label'					=> __( 'Enable Contact', 'ftp-file-synchronization' ),
					'description'		=> __( 'Enable contact now tab on client dashboard.', 'ftp-file-synchronization' ),
					'type'					=> 'checkbox',
					'default'				=> true
				],
				[
					'id' 						=> 'social-telegram',
					'label'					=> __( 'Telegram', 'ftp-file-synchronization' ),
					'description'		=> __( 'Provide Telegram messanger link here.', 'ftp-file-synchronization' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'social-whatsapp',
					'label'					=> __( 'WhatsApp', 'ftp-file-synchronization' ),
					'description'		=> __( 'Provide WhatsApp messanger link here.', 'ftp-file-synchronization' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'social-email',
					'label'					=> __( 'Email', 'ftp-file-synchronization' ),
					'description'		=> __( 'Email address for instant support.', 'ftp-file-synchronization' ),
					'type'					=> 'email',
					'default'				=> ''
				],
				[
					'id' 						=> 'social-contactus',
					'label'					=> __( 'Contact Us', 'ftp-file-synchronization' ),
					'description'		=> __( 'Place the Contact Us page link here.', 'ftp-file-synchronization' ),
					'type'					=> 'url',
					'default'				=> ''
				],
			]
		];
		$args['signature'] 		= [
			'title'							=> __( 'E-Signature', 'ftp-file-synchronization' ),
			'description'				=> __( 'Setup e-signature plugin some customize settings from here. Four tags for Contract is given below.', 'ftp-file-synchronization' ) . $this->contractTags( ['{client_name}','{client_address}','{todays_date}','{retainer_amount}'] ),
			'fields'						=> [
				[
					'id' 						=> 'signature-addressplaceholder',
					'label'					=> __( 'Address Placeholder', 'ftp-file-synchronization' ),
					'description'		=> __( 'What shouldbe replace if address1 & address2 both are empty. If you leave it blank, then it\'ll be blank.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> 'N/A'
				],
				[
					'id' 						=> 'signature-dateformat',
					'label'					=> __( 'Date formate', 'ftp-file-synchronization' ),
					'description'		=> __( 'The date format which will apply on {{todays_date}} place.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> get_option('date_format')
				],
				[
					'id' 						=> 'signature-emptyrrtainer',
					'label'					=> __( 'Empty Retainer amount', 'ftp-file-synchronization' ),
					'description'		=> __( 'if anytime we found empty retainer amount, so what will be replace there?', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> 'N/A'
				],
				[
					'id' 						=> 'signature-defaultcontract',
					'label'					=> __( 'Default contract form', 'ftp-file-synchronization' ),
					'description'		=> __( 'When admin doesn\'t select a registration from before sending it to client, user is taken to this contract. It should be a page where a simple wp-form will apear with client name, service type, retainer amount if necessery.', 'ftp-file-synchronization' ),
					'type'					=> 'url',
					'default'				=> ''
				],
			]
		];
		$args['email'] 		= [
			'title'							=> __( 'E-Mail', 'ftp-file-synchronization' ),
			'description'				=> __( 'Setup email configuration here', 'ftp-file-synchronization' ) . $this->contractTags( ['{client_name}','{client_address}','{todays_date}','{retainer_amount}', '{registration_link}', '{{site_name}}', '{{passwordreset_link}}' ] ),
			'fields'						=> [
				// [
				// 	'id' 						=> 'email-registationlink',
				// 	'label'					=> __( 'Registration Link', 'ftp-file-synchronization' ),
				// 	'description'		=> __( 'Registration link that contains WP-Form registration form.', 'ftp-file-synchronization' ),
				// 	'type'					=> 'text',
				// 	'default'				=> "https://wemakecontent.net/test-page/"
				// ],
				[
					'id' 						=> 'email-registationsubject',
					'label'					=> __( 'Subject', 'ftp-file-synchronization' ),
					'description'		=> __( 'The Subject, used on registration link sending mail.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> "Invitation to Register for [Event/Service/Product]"
				],
				[
					'id' 						=> 'email-sendername',
					'label'					=> __( 'Sender name', 'ftp-file-synchronization' ),
					'description'		=> __( 'Sender name that should be on mail metadata..', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> "Invitation to Register for [Event/Service/Product]"
				],
				[
					'id' 						=> 'email-registationbody',
					'label'					=> __( 'Registration link Template', 'ftp-file-synchronization' ),
					'description'		=> __( 'The template, used on registration link sending mail.', 'ftp-file-synchronization' ),
					'type'					=> 'textarea',
					'default'				=> "Dear [Name],\nWe are delighted to invite you to join us for [Event/Service/Product], a [brief description of event/service/product].\n[Event/Service/Product] offers [brief summary of benefits or features]. As a valued member of our community, we would like to extend a special invitation for you to be part of this exciting opportunity.\nTo register, simply click on the link below:\n[Registration link]\nShould you have any questions or require additional information, please do not hesitate to contact us at [contact information].\nWe look forward to seeing you at [Event/Service/Product].\nBest regards,\n[Your Name/Company Name]",
					'attr'					=> [ 'data-a-tinymce' => true ]
				],
				[
					'id' 						=> 'email-passresetsubject',
					'label'					=> __( 'Password Reset Subject', 'ftp-file-synchronization' ),
					'description'		=> __( 'The email subject on password reset mail.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> __( 'Password Reset Request',   'ftp-file-synchronization' )
				],
				[
					'id' 						=> 'email-passresetbody',
					'label'					=> __( 'Password Reset Template', 'ftp-file-synchronization' ),
					'description'		=> __( 'The template, used on password reset link sending mail.', 'ftp-file-synchronization' ),
					'type'					=> 'textarea',
					'default'				=> "Dear {{client_name}},\n\nYou recently requested to reset your password for your {{site_name}} account. Please follow the link below to reset your password:\n\n{{passwordreset_link}}\n\nIf you did not make this request, you can safely ignore this email.\n\nBest regards,\n{{site_name}} Team"
				],
			]
		];
		$args['stripe'] 		= [
			'title'							=> __( 'Stripe', 'ftp-file-synchronization' ),
			'description'				=> __( 'Stripe payment system configuration process should be do carefully. Here some field is importent to work with no inturrupt. Such as API key or secret key, if it\'s expired on your stripe id, it won\'t work here. New user could face problem fo that reason.', 'ftp-file-synchronization' ),
			'fields'						=> [
				[
					'id' 						=> 'stripe-cancelsubscription',
					'label'					=> __( 'Cancellation', 'ftp-file-synchronization' ),
					'description'		=> __( 'Enable it to make a possibility to user to cancel subscription from client dashboard.', 'ftp-file-synchronization' ),
					'type'					=> 'checkbox',
					'default'				=> false
				],
				[
					'id' 						=> 'stripe-publishablekey',
					'label'					=> __( 'Publishable Key', 'ftp-file-synchronization' ),
					'description'		=> __( 'The key which is secure, could import into JS, and is safe evenif any thirdparty got those code. Note that, secret key is not a publishable key.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'stripe-secretkey',
					'label'					=> __( 'Secret Key', 'ftp-file-synchronization' ),
					'description'		=> __( 'The secret key that never share with any kind of frontend functionalities and is ofr backend purpose. Is required.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'stripe-currency',
					'label'					=> __( 'Currency', 'ftp-file-synchronization' ),
					'description'		=> __( 'Default currency which will use to create payment link.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> 'usd'
				],
				[
					'id' 						=> 'stripe-productname',
					'label'					=> __( 'Product name text', 'ftp-file-synchronization' ),
					'description'		=> __( 'A text to show on product name place on checkout sanbox.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> __( 'Subscription',   'ftp-file-synchronization' )
				],
				[
					'id' 						=> 'stripe-productdesc',
					'label'					=> __( 'Product Description', 'ftp-file-synchronization' ),
					'description'		=> __( 'Some text to show on product description field.', 'ftp-file-synchronization' ),
					'type'					=> 'text',
					'default'				=> __( 'Payment for',   'ftp-file-synchronization' ) . ' ' . get_option( 'blogname', 'FTP File Synchronization' )
				],
				[
					'id' 						=> 'stripe-productimg',
					'label'					=> __( 'Product Image', 'ftp-file-synchronization' ),
					'description'		=> __( 'A valid image url for product. If image url are wrong or image doesn\'t detect by stripe, process will fail.', 'ftp-file-synchronization' ),
					'type'					=> 'url',
					'default'				=> esc_url( FTPFILESYNC_BUILD_URI . '/icons/Online payment_Flatline.svg' )
				],
				[
					'id' 						=> 'stripe-paymentmethod',
					'label'					=> __( 'Payment Method', 'ftp-file-synchronization' ),
					'description'		=> __( 'Select which payment method you will love to get payment.', 'ftp-file-synchronization' ),
					'type'					=> 'select',
					'default'				=> 'card',
					'options'				=> apply_filters( 'futurewordpress/project/payment/stripe/payment_methods', [] )
				],
			]
		];
		$args['regis'] 		= [
			'title'							=> __( 'Registrations', 'ftp-file-synchronization' ),
			'description'				=> __( 'Setup registration link and WP-forms information here.', 'ftp-file-synchronization' ),
			'fields'						=> [
				[
					'id' 						=> 'regis-rows',
					'label'					=> __( 'Rows', 'ftp-file-synchronization' ),
					'description'		=> __( 'How many registration links do you have.', 'ftp-file-synchronization' ),
					'type'					=> 'number',
					'default'				=> 2
				],
			]
		];
		for( $i = 1;$i <= apply_filters( 'futurewordpress/project/system/getoption', 'regis-rows', 3 ); $i++ ) {
			$args['regis'][ 'fields' ][] = [
				'id' 						=> 'regis-link-title-' . $i,
				'label'					=> __( 'Link title #' . $i, 'ftp-file-synchronization' ),
				'description'		=> '',
				'type'					=> 'text',
				'default'				=> 'Link #' . $i
			];
			$args['regis'][ 'fields' ][] = [
				'id' 						=> 'regis-link-url-' . $i,
				'label'					=> __( 'Link URL #' . $i, 'ftp-file-synchronization' ),
				'description'		=> '',
				'type'					=> 'url',
				'default'				=> ''
			];
			$args['regis'][ 'fields' ][] = [
				'id' 						=> 'regis-link-pageid-' . $i,
				'label'					=> __( 'Page ID#' . $i, 'ftp-file-synchronization' ),
				'description'		=> __( 'Registration Page ID, leave it blank if you don\'t want to disable it without invitation.', 'ftp-file-synchronization' ),
				'type'					=> 'text',
				'default'				=> ''
			];
		}
		$args['docs'] 		= [
			'title'							=> __( 'Documentations', 'ftp-file-synchronization' ),
			'description'				=> __( 'The workprocess is tring to explain here.', 'ftp-file-synchronization' ),
			'fields'						=> [
				[
					'id' 						=> 'auth-brifing',
					'label'					=> __( 'How to setup thank you page?', 'ftp-file-synchronization' ),
					'description'		=> sprintf( __( 'first go to %sthis link%s Create or Edit an "Stand Alone" document. Give your thankyou custom page link here %s', 'ftp-file-synchronization' ), '<a href="'. admin_url( 'admin.php?page=esign-docs&document_status=stand_alone' ) . '" target="_blank">', '</a>', '<img src="' . FTPFILESYNC_DIR_URI . '/docs/Stand-alone-esign-metabox.PNG' . '" alt="" />' ),
					'type'					=> 'textcontent'
				],
			]
		];
		return $args;
	}
}

/**
 * {{client_name}}, {{client_address}}, {{todays_date}}, {{retainer_amount}}
 */
