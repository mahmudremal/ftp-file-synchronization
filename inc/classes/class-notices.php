<?php
/**
 * LoadmorePosts
 *
 * @package FTPFileSynchronization
 */

namespace FTPFILESYNC_THEME\Inc;

use FTPFILESYNC_THEME\Inc\Traits\Singleton;
use \WP_Query;

class Notices {

	use Singleton;

	protected function __construct() {

		// load class.
		$this->setup_hooks();
	}

	protected function setup_hooks() {
		add_action( 'admin_notices', [ $this, 'admin_notices' ], 10, 0 );
	}
	public function admin_notices() {
		// $this->notice();
	}
	private function notice() {
		$args = [
			// 'confirm'	=> __( 'Are you sure you want to disapear this message?', 'ftp-file-synchronization' ),
			// 'request'	=> []
		];
		?>
		<div class="notice fwp-notice fwp-notice--dismissible fwp-notice--extended">
			<i class="fwp-notice__dismiss" role="button" aria-label="Dismiss" tabindex="0" data-delay="500" data-events="<?php echo esc_attr( json_encode( $args ) ); ?>"></i>
			<div class="fwp-notice__aside">
				<div class="fwp-notice__icon-wrapper">
					<i class="eicon-elementor" aria-hidden="true"></i>
				</div>
			</div>
			<div class="fwp-notice__content">
				<h3><?php esc_html_e('Congratulations! Love using this Plugin?.', 'ftp-file-synchronization'); ?></h3>
				<p><?php esc_html_e('Become a super contributor by opting in to share non-sensitive plugin data and to receive periodic email updates from us.', 'ftp-file-synchronization') ?> <a href="#!" target="_blank"><?php esc_html_e('Learn more.', 'ftp-file-synchronization') ?></a>
			</p>
			<div class="fwp-notice__actions">
				<a href="#!" class="fwp-button">
					<span><?php esc_html_e('Sure! I\'d love to help', 'ftp-file-synchronization') ?></span>
				</a>
				<a href="#!" class="fwp-button fwp-button__outline fwp-notice__cancel">
					<span><?php esc_html_e('No thanks', 'ftp-file-synchronization') ?></span>
				</a>
			</div>
			</div>
		</div>
		<?php
	}
}
